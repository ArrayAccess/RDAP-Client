<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Services;

use ArrayAccess\RdapClient\Exceptions\FileNotFoundException;
use ArrayAccess\RdapClient\Exceptions\InvalidServiceDefinitionException;
use ArrayAccess\RdapClient\Interfaces\RdapRequestInterface;
use ArrayAccess\RdapClient\Interfaces\RdapServiceInterface;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use function array_keys;
use function array_merge;
use function array_search;
use function array_unique;
use function array_unshift;
use function array_values;
use function dirname;
use function error_clear_last;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function filemtime;
use function is_array;
use function is_dir;
use function is_file;
use function is_readable;
use function is_string;
use function is_writable;
use function json_decode;
use function md5;
use function mkdir;
use function preg_match;
use function restore_error_handler;
use function set_error_handler;
use function sprintf;
use function str_starts_with;
use function stream_context_create;
use function sys_get_temp_dir;
use function unlink;

abstract class AbstractRdapService implements RdapServiceInterface
{
    protected DateTimeInterface $publication;

    protected string $version;

    protected string $description;

    protected array $services = [];

    private static ?string $tempDir = null;

    /**
     * @throws Exception
     */
    public function __construct(
        string $version,
        string $description,
        string|DateTimeInterface $publication,
        array $services
    ) {
        $this->setVersion($version);
        $this->setDescription($description);
        $this->setPublication(
            is_string($publication)
            ? new DateTimeImmutable($publication)
            : DateTimeImmutable::createFromInterface($publication)
        );
        $this->setServices($services);
    }

    public function setServices(array $services): void
    {
        $serviceArray = [];
        foreach ($services as $key => $service) {
            if (count($service) !== 2
                || !is_array($service[0]??null)
                || !is_array($service[1]??null)
            ) {
                throw new InvalidServiceDefinitionException(
                    sprintf(
                        'Service definition is invalid in offset %s',
                        $key
                    )
                );
            }

            foreach ($service[1] as $item) {
                if (!str_starts_with($item, 'http://')
                    && !str_starts_with($item, 'https://')
                ) {
                    throw new InvalidServiceDefinitionException(
                        sprintf(
                            'Service definition is invalid in offset %s',
                            $key
                        )
                    );
                }
            }
            $serviceArray[] = $service;
        }
        $this->services = $serviceArray;
    }

    public function getServices(): array
    {
        return $this->services;
    }

    private function createTargets(string ...$target): array
    {
        $targets = [];
        foreach ($target as $item) {
            if (isset($targets[$item])) {
                continue;
            }
            $item = $this->normalizeSource($item);
            $targets[$item] = true;
        }
        return array_keys($targets);
    }

    public function throwInvalidTarget(string $target) : never
    {
        throw new InvalidServiceDefinitionException(
            sprintf('Target %s is not valid', $target)
        );
    }

    /**
     * @param string $target
     * @return string
     * @throws InvalidServiceDefinitionException
     */
    abstract protected function normalizeSource(string $target): string;

    protected function getOffset(string $rdapURL) : ?int
    {
        $offset = null;
        $trimmedRdap = rtrim($rdapURL, '/');
        foreach ($this->services as $key => $value) {
            foreach ($value[1] as $u) {
                if ($trimmedRdap === rtrim($u, '/')) {
                    $offset = $key;
                    break 2;
                }
            }
        }
        return $offset;
    }

    public function prepend(string $rdapURL, string ...$target): void
    {
        $offset = $this->getOffset($rdapURL);
        $targets = $this->createTargets(...$target);
        if (empty($targets)) {
            return;
        }
        if ($offset !== null && isset($this->services[$offset])) {
            $this->services[$offset][0] = array_merge($targets, $this->services[$offset][0]);
            $this->services[$offset][0] = array_values(array_unique($this->services[$offset][0]));
            return;
        }
        array_unshift($this->services, [$targets, [$rdapURL]]);
    }

    public function append(string $rdapURL, string ...$target): void
    {
        $offset = $this->getOffset($rdapURL);
        $targets = $this->createTargets(...$target);
        if (empty($targets)) {
            return;
        }
        if ($offset !== null && isset($this->services[$offset])) {
            $this->services[$offset][0] = array_merge($this->services[$offset][0], $targets);
            $this->services[$offset][0] = array_values(array_unique($this->services[$offset][0]));
            return;
        }
        $this->services[] = [$targets, [$rdapURL]];
    }

    public function remove(string $definition): void
    {
        foreach ($this->services as $key => $service) {
            $offset = array_search($definition, $service[0]);
            if ($offset === false) {
                continue;
            }
            unset($this->services[$key][$offset]);
            $this->services[$key] = array_values($this->services[$key]);
        }
    }

    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setPublication(DateTimeInterface $publication): void
    {
        if (!$publication instanceof DateTimeImmutable) {
            $publication = DateTimeImmutable::createFromInterface($publication)
                ->setTimezone(new DateTimeZone('Z'));
        }
        $this->publication = $publication;
    }

    public function getPublication(): DateTimeInterface
    {
        return $this->publication;
    }

    /**
     * @throws Exception
     */
    public static function fromURL(string $url) : static
    {
        $fileCache = null;
        $isUseHttp = preg_match('~https?://~i', $url);
        if ($isUseHttp) {
            self::$tempDir ??= sys_get_temp_dir();
            if (is_dir(self::$tempDir) && is_writable(self::$tempDir)) {
                $rdapDir = self::$tempDir .'/rdap-php';
                if (!file_exists($rdapDir)) {
                    set_error_handler(static function () {
                        error_clear_last();
                    });
                    mkdir($rdapDir, 0755, true);
                    restore_error_handler();
                }
                $fileCache = $rdapDir . '/rdap-'. md5($url).'.json';
                if (is_file($fileCache)
                    && is_readable($fileCache)
                    && (filemtime($fileCache) + 3600) > time()
                ) {
                    $data = file_get_contents($fileCache);
                    $data = is_string($data)
                        ? json_decode($data, true)
                        : null;
                    if (is_array($data)
                        && is_string($data['version']??null)
                        && is_string($data['description']??null)
                        && is_string($data['publication']??null)
                        && is_array($data['services']??null)
                    ) {
                        return new static(
                            $data['version'],
                            $data['description'],
                            $data['publication'],
                            $data['services']
                        );
                    }
                    $isWritable = is_writable($fileCache);
                    if ($isWritable) {
                        unlink($fileCache);
                    }
                }
                $fileCache = ($isWritable??true) === true
                    && is_writable(dirname($fileCache)) ? $fileCache : null;
            }
        }

        set_error_handler(static fn () => error_clear_last());
        $content = file_get_contents(
            $url,
            false,
            $isUseHttp ? stream_context_create(
                RdapRequestInterface::DEFAULT_STREAM_CONTEXT
            ) : null
        );
        restore_error_handler();

        $data = is_string($content) ? json_decode($content, true) : null;
        if (!is_array($data)
            || !is_string($data['version']??null)
            || !is_string($data['description']??null)
            || !is_string($data['publication']??null)
            || !is_array($data['services']??null)
        ) {
            throw new InvalidServiceDefinitionException(
                sprintf('Protocol service URL "%s" return invalid data', $url)
            );
        }
        if ($fileCache) {
            file_put_contents($fileCache, $content);
        }
        return new static(
            $data['version'],
            $data['description'],
            $data['publication'],
            $data['services']
        );
    }

    /**
     * @throws Exception
     */
    public static function fromFile(string $file) : static
    {
        if (!is_file($file)) {
            throw new FileNotFoundException($file);
        }
        return self::fromURL($file);
    }
}

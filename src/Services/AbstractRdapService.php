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
use function array_key_exists;
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
use function min;
use function mkdir;
use function preg_match;
use function restore_error_handler;
use function rtrim;
use function set_error_handler;
use function sprintf;
use function str_starts_with;
use function stream_context_create;
use function sys_get_temp_dir;
use function time;
use function unlink;

abstract class AbstractRdapService implements RdapServiceInterface
{
    /**
     * @var DateTimeInterface $publication The publication date
     */
    protected DateTimeInterface $publication;

    /**
     * @var string $version The version of the service
     */
    protected string $version;

    /**
     * @var string $description The description of the service
     */
    protected string $description;

    /**
     * @var array<int, array<array-key, array<array-key, string>>> $services The services
     */
    protected array $services = [];

    /**
     * @var string|null $tempDir The temporary directory
     */
    private static ?string $tempDir = null;

    /**
     * @var int<60, 604800> $cacheExpirations The cache expiration time
     */
    private static int $cacheExpirations = 3600;

    /**
     * The maximum expiration time default in 7 days
     */
    public const MAX_EXPIRES = 604800;

    /**
     * Constructor
     *
     * @param string $version
     * @param string $description
     * @param string|DateTimeInterface $publication
     * @param array<array-key, string[][]> $services
     * @throws \Exception
     * @noinspection PhpFullyQualifiedNameUsageInspection
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

    /**
     * @return int<60, 604800>
     */
    public static function cacheExpirations(): int
    {
        return self::$cacheExpirations;
    }

    /**
     * Set the expiration time
     *
     * @param int<60, 604800> $expires
     * @return void
     */
    public function setCacheExpirations(int $expires): void
    {
        self::$cacheExpirations = max(min($expires, self::MAX_EXPIRES), 60);
    }

    /**
     * Set the services
     *
     * @param array<string[][]> $services
     * @throws InvalidServiceDefinitionException
     */
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

    /**
     * @inheritDoc
     */
    public function getServices(): array
    {
        return $this->services;
    }

    /**
     * Create targets
     *
     * @param string ...$target
     * @return array<array-key, string>
     */
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


    /**
     * @inheritDoc
     */
    public function throwInvalidTarget(string $target) : never
    {
        throw new InvalidServiceDefinitionException(
            sprintf('Target %s is not valid', $target)
        );
    }

    /**
     * Normalize the source
     *
     * @param string $target
     * @return string
     * @throws InvalidServiceDefinitionException
     */
    abstract protected function normalizeSource(string $target): string;

    /**
     * Get the offset
     *
     * @param string $rdapURL
     * @return int|null
     */
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

    /**
     * @inheritDoc
     */
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

    /**
     * @inheritDoc
     */
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

    /**
     * @inheritDoc
     */
    public function remove(string $definition): void
    {
        foreach ($this->services as $key => $service) {
            $offset = array_search($definition, $service[0]);
            if ($offset === false) {
                continue;
            }
            if (array_key_exists($offset, $this->services[$key])) {
                unset($this->services[$key][$offset]);
            }
            if (empty($this->services[$key])) {
                unset($this->services[$key]);
                continue;
            }
            $this->services[$key] = array_values($this->services[$key]);
        }
    }

    /**
     * @inheritDoc
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @inheritDoc
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @inheritDoc
     */
    public function setPublication(DateTimeInterface $publication): void
    {
        if (!$publication instanceof DateTimeImmutable) {
            $publication = DateTimeImmutable::createFromInterface($publication)
                ->setTimezone(new DateTimeZone('Z'));
        }
        $this->publication = $publication;
    }

    /**
     * @inheritDoc
     */
    public function getPublication(): DateTimeInterface
    {
        return $this->publication;
    }

    /**
     * @throws \Exception
     * @noinspection PhpFullyQualifiedNameUsageInspection
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
                    set_error_handler(static function (int $code, string $message, string $file, int $line) : bool {
                        error_clear_last();
                        return true;
                    });
                    mkdir($rdapDir, 0755, true);
                    restore_error_handler();
                }
                $fileCache = $rdapDir . '/rdap-'. md5($url).'.json';
                if (is_file($fileCache)
                    && is_readable($fileCache)
                    && (filemtime($fileCache) + self::cacheExpirations()) > time()
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
                        // @phpstan-ignore-next-line
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
        set_error_handler(static function (int $code, string $message, string $file, int $line) : bool {
            error_clear_last();
            return true;
        });
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
        // @phpstan-ignore-next-line
        return new static(
            $data['version'],
            $data['description'],
            $data['publication'],
            $data['services']
        );
    }

    /**
     * @throws \Exception
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public static function fromFile(string $file) : static
    {
        if (!is_file($file)) {
            throw new FileNotFoundException($file);
        }
        return self::fromURL($file);
    }
}

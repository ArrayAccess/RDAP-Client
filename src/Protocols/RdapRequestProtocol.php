<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Protocols;

use ArrayAccess\RdapClient\Exceptions\MismatchProtocolBehaviorException;
use ArrayAccess\RdapClient\Exceptions\RdapRemoteRequestException;
use ArrayAccess\RdapClient\Interfaces\RdapProtocolInterface;
use ArrayAccess\RdapClient\Interfaces\RdapRequestInterface;
use ArrayAccess\RdapClient\Interfaces\RdapResponseInterface;
use RuntimeException;
use function array_pop;
use function explode;
use function file_get_contents;
use function restore_error_handler;
use function set_error_handler;
use function sprintf;
use function str_contains;
use function str_ends_with;
use function stream_context_create;

class RdapRequestProtocol implements RdapRequestInterface
{
    /**
     * @var string $target The target
     */
    protected string $target;

    /**
     * @var RdapProtocolInterface $protocol The RDAP protocol
     */
    protected RdapProtocolInterface $protocol;

    /**
     * @var RdapResponseInterface|null $response The RDAP response
     */
    protected ?RdapResponseInterface $response = null;

    /**
     * @var int $errorCode The error code
     */
    private int $errorCode = 0;

    /**
     * @var ?string $errorMessage The error message
     */
    private ?string $errorMessage = null;

    /**
     * @var string|null $rdapSearchURL The RDAP search URL
     */
    private ?string $rdapSearchURL = null;

    /**
     * @inheritDoc
     */
    public function __construct(string $target, RdapProtocolInterface $protocol)
    {
        $this->target = $target;
        $this->protocol = $protocol;
    }

    /**
     * @inheritDoc
     */
    public function getProtocol(): RdapProtocolInterface
    {
        return $this->protocol;
    }

    /**
     * @inheritDoc
     */
    public function getTarget(): string
    {
        return $this->target;
    }

    /**
     * @inheritDoc
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    /**
     * @return string|null The RDAP search URL
     */
    public function getRdapSearchURL(): ?string
    {
        return $this->rdapSearchURL;
    }

    /**
     * @inheritDoc
     */
    public function withRdapSearchURL(string $url) : static
    {
        $obj = clone $this;
        $obj->errorCode = 0;
        $obj->errorMessage = '';
        $obj->rdapSearchURL = $url;
        $obj->response = null;
        $targetUrl = $url;
        if (str_contains($targetUrl, '?')) {
            [$targetUrl] = explode('?', $targetUrl, 2);
        }
        if (str_contains($targetUrl, '#')) {
            [$targetUrl] = explode('#', $targetUrl, 2);
        }
        $targetUrl = rtrim($targetUrl, '/');
        $targetUrl = explode('/', $targetUrl);
        // change the target
        $obj->target = (string) array_pop($targetUrl);

        // validate target
        $path = array_pop($targetUrl);
        if (!$path) {
            throw new RuntimeException(
                'Could not get RDAP search path from URL'
            );
        }
        $searchPath = rtrim($this->getProtocol()->getSearchPath(), '/');
        if (!str_ends_with($searchPath, $path)) {
            throw new MismatchProtocolBehaviorException(
                'Target RDAP search path is mismatch'
            );
        }

        return $obj;
    }

    /**
     * @inheritDoc
     * @return RdapResponseInterface
     */
    public function getResponse(): RdapResponseInterface
    {
        if ($this->response) {
            return $this->response;
        }

        $this->rdapSearchURL ??= $this->getProtocol()->getFindURL($this->getTarget());
        if (!$this->rdapSearchURL) {
            throw new RuntimeException(
                sprintf('Could not get Rdap URL for %s', $this->getTarget())
            );
        }
        if ($this->errorCode !== 0 || $this->errorMessage) {
            throw new RdapRemoteRequestException(
                $this->errorMessage??'',
                $this->errorCode
            );
        }
        set_error_handler(function (int $code, string $message, string $file, int $line) : bool {
            $this->errorCode = $code;
            $this->errorMessage = $message;
            return true;
        });
        $context = RdapRequestInterface::DEFAULT_STREAM_CONTEXT;
        $content = file_get_contents(
            $this->rdapSearchURL,
            false,
            stream_context_create(
                $context
            )
        );
        restore_error_handler();
        if (!$content) {
            throw new RdapRemoteRequestException(
                $this->errorMessage??'Could not get RDAP response',
                $this->errorCode
            );
        }

        return $this->response = $this->protocol->createResponse($content, $this);
    }
}

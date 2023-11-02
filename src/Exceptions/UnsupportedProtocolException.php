<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Exceptions;

use InvalidArgumentException;
use Throwable;
use function sprintf;

class UnsupportedProtocolException extends InvalidArgumentException
{
    public function __construct(
        protected string $protocolType,
        string $message = "",
        int $code = 0,
        ?Throwable $previous = null
    ) {
        if ($message === '') {
            $message = sprintf('Protocol type "%s" is not supported.', $this->getProtocolType());
        }
        parent::__construct($message, $code, $previous);
    }

    public function getProtocolType(): string
    {
        return $this->protocolType;
    }
}

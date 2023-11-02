<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Exceptions;

use InvalidArgumentException;
use Throwable;
use function sprintf;

class FileNotFoundException extends InvalidArgumentException
{
    public function __construct(
        protected string $fileName,
        string $message = "",
        int $code = 0,
        ?Throwable $previous = null
    ) {
        if ($message === '') {
            $message = sprintf('File "%s" has not found.', $this->getFileName());
        }
        parent::__construct($message, $code, $previous);
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }
}

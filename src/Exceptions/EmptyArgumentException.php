<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Exceptions;

use InvalidArgumentException;
use Throwable;

class EmptyArgumentException extends InvalidArgumentException
{
    public function __construct(
        string $message = "",
        int $code = 0,
        ?Throwable $previous = null
    ) {
        if ($message === '') {
            $message = 'Argument could not be empty';
        }
        parent::__construct($message, $code, $previous);
    }
}

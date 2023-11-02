<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Protocols;

abstract class AbstractIPProtocol extends AbstractRdapProtocol
{
    /**
     * @return string
     * @link https://datatracker.ietf.org/doc/html/rfc7482#section-3.1.1
     */
    public function getSearchPath(): string
    {
        return '/ip';
    }
}

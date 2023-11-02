<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data;

use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractRdapResponseDataString;
use Stringable;

class ContactURI extends AbstractRdapResponseDataString
{
    protected string $name = 'contact_URI';

    public function __construct(string|Stringable $data)
    {
        $this->values = (string) $data;
    }
}

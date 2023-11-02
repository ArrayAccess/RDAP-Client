<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data;

use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataObjectDataClassNameInterface;
use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractRdapResponseDataString;
use Stringable;

/**
 * @template T of string|Stringable
 */
class ObjectClassName extends AbstractRdapResponseDataString implements RdapResponseDataObjectDataClassNameInterface
{
    protected string $name = 'objectClassName';

    protected bool $nullable = false;

    /**
     * @param string<T>|Stringable<T> $data
     */
    public function __construct(string|Stringable $data)
    {
        $this->values = (string) $data;
    }

    /**
     * @return string<T>
     */
    public function getValues(): string
    {
        return $this->values;
    }
}

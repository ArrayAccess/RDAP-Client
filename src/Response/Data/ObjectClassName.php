<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data;

use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataObjectDataClassNameInterface;
use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractRdapResponseDataString;
use Stringable;

/**
 * @template T of string
 */
class ObjectClassName extends AbstractRdapResponseDataString implements RdapResponseDataObjectDataClassNameInterface
{
    protected string $name = 'objectClassName';

    /**
     * @var bool $nullable
     */
    protected bool $nullable = false;

    /**
     * @param string|Stringable $data
     * @phpstan-param T $data
     */
    public function __construct(string|Stringable $data)
    {
        $this->values = (string) $data;
    }

    /**
     * @return string
     * @phpstan-return T
     */
    public function getValues(): string
    {
        $tReturn = $this->values;
        /**
         * @var T $tReturn
         */
        return (string) $tReturn;
    }
}

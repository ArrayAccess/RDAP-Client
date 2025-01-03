<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data\Definitions;

use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractRdapResponseDataRecursiveArrayEmptyName;
use ArrayAccess\RdapClient\Response\Data\NonStandards\EmptyObject;
use IteratorAggregate;
use Stringable;

/**
 * @template-implements IteratorAggregate<array{
 *     0: string,
 *     1: NamedRecursiveObjectData|EmptyObject,
 *     2: string,
 *     3: string|array<array-key, mixed>,
 *     4: string|array<array-key, mixed>
 * }>
 */
class VCardDefinition extends AbstractRdapResponseDataRecursiveArrayEmptyName implements IteratorAggregate
{
    /**
     * @var array<array-key, string|null> $allowedKeys
     */
    protected array $allowedKeys = [
        'type',
        'label',
        'identifier',
        null
    ];

    /**
     * @param string|Stringable $name
     * @param NamedRecursiveObjectData|EmptyObject $attribute
     * @param string|Stringable $typeValue
     * @param string|Stringable|array<array-key, mixed> $value
     * @param string|Stringable|array<array-key, mixed> $values
     */
    public function __construct(
        string|Stringable $name,
        NamedRecursiveObjectData|EmptyObject $attribute,
        string|Stringable $typeValue,
        string|Stringable|array $value,
        string|Stringable|array ...$values
    ) {
        $this->values = [(string) $name];
        $this->values[] = $attribute;
        $this->values[] = (string) $typeValue;
        $this->values[] = $value instanceof Stringable ? (string) $value : $value;
        foreach ($values as $value) {
            $this->values[] = $value instanceof Stringable ? (string) $value : $value;
        }
    }
}

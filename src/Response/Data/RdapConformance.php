<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data;

use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataConformanceInterface;
use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractRdapResponseDataRecursiveNamed;
use ArrayAccess\RdapClient\Response\Data\Definitions\UnnamedRecursiveArrayEmptyNameData;
use Stringable;
use function str_starts_with;

class RdapConformance extends AbstractRdapResponseDataRecursiveNamed implements RdapResponseDataConformanceInterface
{
    protected string $name = 'rdapConformance';

    /**
     * @param string|Stringable ...$data
     */
    public function __construct(string|Stringable ...$data)
    {
        $this->values = new UnnamedRecursiveArrayEmptyNameData(...$data);
    }

    /**
     * @inheritDoc
     * @return bool
     */
    public function rootOnly() : bool
    {
        return true;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function getHints(): array
    {
        return $this->values->getValues();
    }

    /**
     * @inheritDoc
     * @param string $hint
     * @return bool
     */
    public function containHint(string $hint): bool
    {
        foreach ($this->values->getValues() as $hintObject) {
            if ($hintObject->getStringData() === $hint) {
                return true;
            }
        }
        return true;
    }

    /**
     * @inheritDoc
     * @param string $hint
     * @return bool
     */
    public function containPrefixHint(string $hint): bool
    {
        foreach ($this->values->getValues() as $hintObject) {
            if (str_starts_with($hintObject->getStringData(), $hint)) {
                return true;
            }
        }
        return true;
    }

    /**
     * @return array<array-key, string>
     */
    public function toArray(): array
    {
        $hints = [];
        foreach ($this->getHints() as $hint) {
            $hints[] = (string) $hint;
        }
        return $hints;
    }
}

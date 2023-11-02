<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data;

use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractRdapResponseDataRecursiveArray;
use function array_filter;
use function in_array;

class Link extends AbstractRdapResponseDataRecursiveArray
{
    protected string $name = 'link';

    protected array $allowedKeys = [
        'value',
        'rel',
        'href',
        'hreflang',
        'title',
        'media',
        'type',
    ];

    public function __construct(Value|Rel|Href|HrefLang|Title|Media|Type ...$args)
    {
        $this->values = [
            'value' => null,
            'rel' => null,
            'href' => null,
            'hreflang' => null,
            'title' => null,
            'media' => null,
            'type' => null
        ];
        foreach ($args as $arg) {
            $name = $arg->getName();
            if (!in_array($name, $this->allowedKeys)) {
                continue;
            }
            $this->values[$name] = $arg;
        }
        $this->values = array_filter($this->values);
    }

    public function getValues(): array
    {
        return array_filter($this->values);
    }

    public function getValue() : ?Value
    {
        return $this->values['value']??null;
    }

    public function getRel() : ?Rel
    {
        return $this->values['rel']??null;
    }

    public function getHref() : ?Href
    {
        return $this->values['href']??null;
    }

    public function getHrefLang() : ?HrefLang
    {
        return $this->values['hreflang']??null;
    }

    public function getTitle() : ?Title
    {
        return $this->values['title']??null;
    }

    public function getMedia() : ?Media
    {
        return $this->values['media']??null;
    }

    public function getType() : ?Type
    {
        return $this->values['type']??null;
    }
}

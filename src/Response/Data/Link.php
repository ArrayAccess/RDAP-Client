<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data;

use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractRdapResponseDataRecursiveArray;
use IteratorAggregate;
use function array_filter;

/**
 * @template-implements IteratorAggregate<string, Value|Rel|Href|HrefLang|Title|Media|Type>
 */
class Link extends AbstractRdapResponseDataRecursiveArray implements IteratorAggregate
{
    /**
     * @var string $name The name of the object
     */
    protected string $name = 'link';

    /**
     * @var array<"value"|"rel"|"href"|"hreflang"|"title"|"media"|"type">
     */
    protected array $allowedKeys = [
        'value',
        'rel',
        'href',
        'hreflang',
        'title',
        'media',
        'type',
    ];

    /**
     * @var array{
     *     value?: Value,
     *     rel?: Rel,
     *     href?: Href,
     *     hreflang?: HrefLang,
     *     title?: Title,
     *     media?: Media,
     *     type?: Type,
     * } $values
     */
    protected array $values = [];

    /**
     * @param Value|Rel|Href|HrefLang|Title|Media|Type ...$args
     */
    public function __construct(Value|Rel|Href|HrefLang|Title|Media|Type ...$args)
    {
        $this->values = [];
        foreach ($args as $arg) {
            if ($arg instanceof Rel) {
                $this->values['rel'] = $arg;
                continue;
            }
            if ($arg instanceof HrefLang) {
                $this->values['hreflang'] = $arg;
                continue;
            }
            if ($arg instanceof Title) {
                $this->values['title'] = $arg;
                continue;
            }
            if ($arg instanceof Media) {
                $this->values['media'] = $arg;
                continue;
            }
            if ($arg instanceof Type) {
                $this->values['type'] = $arg;
                continue;
            }
            if ($arg instanceof Href) {
                $this->values['href'] = $arg;
                continue;
            }
            $this->values['value'] = $arg;
        }
    }

    /**
     * @return array<string, Value|Rel|Href|HrefLang|Title|Media|Type>
     */
    public function getValues(): array
    {
        return array_filter($this->values);
    }

    /**
     * Get the value
     * @return Value|null
     */
    public function getValue() : ?Value
    {
        return $this->values['value']??null;
    }

    /**
     * Get the rel
     * @return Rel|null
     */
    public function getRel() : ?Rel
    {
        return $this->values['rel']??null;
    }

    /**
     * Get the href
     * @return Href|null
     */
    public function getHref() : ?Href
    {
        return $this->values['href']??null;
    }

    /**
     * Get the hreflang
     * @return HrefLang|null
     */
    public function getHrefLang() : ?HrefLang
    {
        return $this->values['hreflang']??null;
    }

    /**
     * Get the title
     * @return Title|null
     */
    public function getTitle() : ?Title
    {
        return $this->values['title']??null;
    }

    /**
     * Get the media
     * @return Media|null
     */
    public function getMedia() : ?Media
    {
        return $this->values['media']??null;
    }

    /**
     * Get the type
     * @return Type|null
     */
    public function getType() : ?Type
    {
        return $this->values['type']??null;
    }
}

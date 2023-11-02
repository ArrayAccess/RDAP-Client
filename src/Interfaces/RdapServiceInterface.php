<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Interfaces;

use DateTimeInterface;

interface RdapServiceInterface
{
    const DATE_FORMAT = 'Y-m-d\TH:i:s.v\Z';

    public function throwInvalidTarget(string $target) : never;

    public function setVersion(string $version);

    public function getVersion() : string;

    public function setDescription(string $description);

    public function getDescription() : string;

    public function setPublication(DateTimeInterface $publication);

    public function getPublication() : DateTimeInterface;

    public static function fromURL(string $url) : static;

    public static function fromFile(string $file) : static;

    /**
     * @return array<string[], string[]>
     */
    public function getServices(): array;

    public function prepend(string $rdapURL, string ...$target);

    public function append(string $rdapURL, string ...$target);

    public function remove(string $definition);

    public function getRdapURL(string $target) : ?string;

    public function normalize(string $target) : ?string;
}

<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Interfaces;

use DateTimeInterface;

interface RdapServiceInterface
{
    public const DATE_FORMAT = 'Y-m-d\TH:i:s.v\Z';

    /**
     * Throw an invalid target exception
     *
     * @param string $target
     * @return never
     */
    public function throwInvalidTarget(string $target) : never;

    /**
     * Set the name
     *
     * @param string $version
     */
    public function setVersion(string $version); // @phpstan-ignore-line

    /**
     * Get the version
     *
     * @return string
     */
    public function getVersion() : string;

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription(string $description) : void;

    /**
     * Get the description
     *
     * @return string
     */
    public function getDescription() : string;

    /**
     * Set the publication date
     *
     * @param DateTimeInterface $publication
     */
    public function setPublication(DateTimeInterface $publication); // @phpstan-ignore-line

    /**
     * Get the publication date
     *
     * @return DateTimeInterface
     */
    public function getPublication() : DateTimeInterface;

    /**
     * Create a new instance from the given URL
     *
     * @param string $url
     * @return static
     */
    public static function fromURL(string $url) : static;

    /**
     * Create a new instance from the given file
     *
     * @param string $file
     * @return static
     */
    public static function fromFile(string $file) : static;

    /**
     * Get the services
     *
     * @return array<int, array<array-key, array<array-key, string>>> $services The services
     */
    public function getServices(): array;

    /**
     * Prepend the given definition
     *
     * @param string $rdapURL
     * @param string ...$target
     */
    public function prepend(string $rdapURL, string ...$target); // @phpstan-ignore-line

    /**
     * Append the given definition
     *
     * @param string $rdapURL
     * @param string ...$target
     */
    public function append(string $rdapURL, string ...$target); // @phpstan-ignore-line

    /**
     * Remove the given definition
     *
     * @param string $definition
     */
    public function remove(string $definition); // @phpstan-ignore-line

    /**
     * Get RDAP URL for the given target
     *
     * @param string $target
     * @return string|null
     */
    public function getRdapURL(string $target) : ?string;

    /**
     * Normalize the given target
     *
     * @param string $target
     * @return string|null
     */
    public function normalize(string $target) : ?string;
}

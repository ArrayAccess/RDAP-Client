<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Interfaces;

use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataConformanceDataInterface;
use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataConformanceInterface;
use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataErrorCodeInterface;
use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataObjectDataClassNameInterface;
use ArrayAccess\RdapClient\Response\Data\Description;
use ArrayAccess\RdapClient\Response\Data\Entities;
use ArrayAccess\RdapClient\Response\Data\Events;
use ArrayAccess\RdapClient\Response\Data\Handle;
use ArrayAccess\RdapClient\Response\Data\Lang;
use ArrayAccess\RdapClient\Response\Data\Links;
use ArrayAccess\RdapClient\Response\Data\Name;
use ArrayAccess\RdapClient\Response\Data\Notices;
use ArrayAccess\RdapClient\Response\Data\Status;
use ArrayAccess\RdapClient\Response\Data\Title;
use JsonSerializable;
use Stringable;

interface RdapResponseDefinitionInterface extends Stringable, JsonSerializable
{
    /**
     * RDAP Response Definition Constructor
     *
     * @param RdapResponseInterface $rdapResponseObject
     */
    public function __construct(RdapResponseInterface $rdapResponseObject);

    /**
     * Get the RDAP Response Object
     * @return RdapResponseInterface
     */
    public function getRdapResponseObject(): RdapResponseInterface;

    /**
     * Get the RDAP Conformance
     * @return RdapResponseDataConformanceInterface|null
     */
    public function getRdapConformance(): ?RdapResponseDataConformanceInterface;

    /**
     * Get the RDAP Conformance Data
     * @return RdapResponseDataConformanceDataInterface<array-key, mixed>|null
     */
    public function getRdapConformanceData(): ?RdapResponseDataConformanceDataInterface;

    /**
     * Get the Object Class Name
     * @return RdapResponseDataObjectDataClassNameInterface|null
     */
    public function getObjectClassName(): ?RdapResponseDataObjectDataClassNameInterface;

    /**
     * Get the Error Code
     * @return RdapResponseDataErrorCodeInterface|null
     */
    public function getErrorCode(): ?RdapResponseDataErrorCodeInterface;

    /**
     * Get the Related Request
     * @return RdapRequestInterface|null
     */
    public function getRelatedRequest(): ?RdapRequestInterface;

    /**
     * Get the Title
     * @return Title|null
     */
    public function getTitle(): ?Title;

    /**
     * Get the Status
     * @return Status|null
     */
    public function getStatus(): ?Status;

    /**
     * Get the description
     * @return Description|null
     */
    public function getDescription(): ?Description;

    /**
     * Get the language
     * @return Lang|null
     */
    public function getLang(): ?Lang;

    /**
     * Get the notices
     * @return Notices|null
     */
    public function getNotices(): ?Notices;

    /**
     * Get the links
     *
     * @return Links|null
     */
    public function getLinks(): ?Links;

    /**
     * Get the handle
     * @return Handle|null
     */
    public function getHandle(): ?Handle;

    /**
     * Get the events
     * @return Events|null
     */
    public function getEvents(): ?Events;

    /**
     * Get the entities
     * @return Entities|null
     */
    public function getEntities(): ?Entities;

    /**
     * Get the name
     * @return Name|null
     */
    public function getName(): ?Name;

    /**
     * Is error
     *
     * @return bool
     */
    public function isError(): bool;
}

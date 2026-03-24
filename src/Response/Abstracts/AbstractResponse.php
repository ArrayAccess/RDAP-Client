<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Abstracts;

use ArrayAccess\RdapClient\Exceptions\InvalidDataTypeException;
use ArrayAccess\RdapClient\Exceptions\MismatchProtocolBehaviorException;
use ArrayAccess\RdapClient\Exceptions\RdapResponseException;
use ArrayAccess\RdapClient\Interfaces\RdapProtocolInterface;
use ArrayAccess\RdapClient\Interfaces\RdapRequestInterface;
use ArrayAccess\RdapClient\Interfaces\RdapResponseInterface;
use ArrayAccess\RdapClient\Response\Traits\AllowedKeyDataTraits;
use function get_class;
use function is_array;
use function json_decode;
use function sprintf;

abstract class AbstractResponse implements RdapResponseInterface
{
    use AllowedKeyDataTraits;

    /**
     * @var string original response JSON
     */
    protected string $responseJson;

    /**
     * @var array<array-key, mixed> decoded json
     */
    protected array $responseArray = [];

    /**
     * @var RdapRequestInterface request object
     */
    protected RdapRequestInterface $request;

    /**
     * @var RdapProtocolInterface protocol object
     */
    protected RdapProtocolInterface $protocol;

    /**
     * Constructor
     * @param string $responseJson
     * @param RdapRequestInterface $request
     * @param RdapProtocolInterface $protocol
     */
    public function __construct(
        string $responseJson,
        RdapRequestInterface $request,
        RdapProtocolInterface $protocol
    ) {
        if ($request->getProtocol() !== $protocol) {
            throw new MismatchProtocolBehaviorException(
                sprintf(
                    'Protocol object "%s" from request is mismatch with protocol object "%s"',
                    get_class($request->getProtocol()),
                    $protocol::class
                )
            );
        }

        $this->assertResponse($responseJson);
        $this->responseJson = $responseJson;
        $this->request = $request;
        $this->protocol = $protocol;
    }

    /**
     * Assert that the raw JSON response is valid and populate internal
     * array representation.
     *
     * @param string $responseJson
     * @return void
     *
     * @throws InvalidDataTypeException when the content cannot be decoded or
     *         does not contain any of the required fields: objectClassName,
     *         rdapConformance, or errorCode.
     * @throws RdapResponseException  when the payload represents an error
     *         response (``errorCode`` field present).  The returned exception
     *         may be inspected via {@see RdapResponseException::getResponse()}
     *         for the full payload; its message and code are derived from the
     *         `title`/`description`/`errorCode` fields.
     */
    private function assertResponse(string $responseJson): void
    {
        $decoded = json_decode($responseJson, true);

        if (!is_array($decoded) ||
            (!isset($decoded['objectClassName']) &&
                !isset($decoded['rdapConformance']) &&
                !isset($decoded['errorCode']))
        ) {
            throw new InvalidDataTypeException(
                'Response is not valid json content'
            );
        }

        if (isset($decoded['errorCode'])) {
            // convert immediately into an exception; no need to validate
            // required fields for error payloads.
            throw RdapResponseException::fromResponse($decoded);
        }

        $this->responseArray = $decoded;
    }

    /**
     * @inheritDoc
     */
    public function getResponseJson(): string
    {
        return $this->responseJson;
    }

    /**
     * @inheritDoc
     */
    public function getResponseArray(): array
    {
        return $this->responseArray;
    }

    /**
     * @inheritDoc
     */
    public function getRequest(): RdapRequestInterface
    {
        return $this->request;
    }

    /**
     * @inheritDoc
     */
    public function getProtocol(): RdapProtocolInterface
    {
        return $this->protocol;
    }

    /**
     * @inheritDoc
     * @return array<array-key, mixed>
     */
    public function jsonSerialize() : array
    {
        return $this->responseArray;
    }
}

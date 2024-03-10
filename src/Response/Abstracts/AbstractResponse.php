<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Abstracts;

use ArrayAccess\RdapClient\Exceptions\InvalidDataTypeException;
use ArrayAccess\RdapClient\Exceptions\MismatchProtocolBehaviorException;
use ArrayAccess\RdapClient\Interfaces\RdapProtocolInterface;
use ArrayAccess\RdapClient\Interfaces\RdapRequestInterface;
use ArrayAccess\RdapClient\Interfaces\RdapResponseInterface;
use ArrayAccess\RdapClient\Response\Traits\AllowedKeyDataTraits;
use function get_class;
use function is_array;
use function is_string;
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
     * @var array decoded json
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

    private function assertResponse(string $responseJson): void
    {
        $responseJson = json_decode($responseJson, true);
        if (!is_array($responseJson) || !is_string($responseJson['objectClassName']??null)) {
            throw new InvalidDataTypeException(
                'Response is not valid json content'
            );
        }
        $this->responseArray = $responseJson;
    }

    public function getResponseJson(): string
    {
        return $this->responseJson;
    }

    public function getResponseArray(): array
    {
        return $this->responseArray;
    }

    public function getRequest(): RdapRequestInterface
    {
        return $this->request;
    }

    public function getProtocol(): RdapProtocolInterface
    {
        return $this->protocol;
    }

    public function jsonSerialize() : array
    {
        return $this->responseArray;
    }
}

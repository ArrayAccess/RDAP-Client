<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Exceptions;

use RuntimeException;
use function array_filter;
use function implode;
use function is_array;
use function is_string;

/**
 * Thrown when the remote RDAP server returns an error response payload.
 *
 * The exception message is assembled from the `title` and `description`
 * properties supplied by the server.  The exception code contains the
 * numeric `errorCode` value.  The full decoded response is available via
 * {@see getResponse()}.
 */
class RdapResponseException extends RuntimeException
{
    /**
     * Full decoded payload returned by the server.
     *
     * Keeping the entire array allows callers to inspect additional
     * properties without having to parse/reconstruct them from the message
     * or code.
     *
     * @var array<string, mixed>
     */
    private array $response;

    /**
     * Construct from a previously prepared message.
     *
     * Use the named factory if possible.
     *
     * @param string                  $message  human readable description
     * @param int                     $code     errorCode from the payload
     * @param array<string, mixed>    $response original decoded response
     * @param \Throwable|null         $previous previous exception
     */
    public function __construct(
        string $message = "",
        int $code = 0,
        array $response = [],
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->response = $response;
    }

    /**
     * Create an exception instance from a decoded RDAP error response.
     *
     * @param array<string, mixed> $response
     * @return self
     */
    public static function fromResponse(array $response): self
    {
        $code = isset($response['errorCode']) ? (int) $response['errorCode'] : 0;
        $title = is_string($response['title'] ?? null) ? $response['title'] : '';
        $description = '';
        if (isset($response['description'])) {
            if (is_string($response['description'])) {
                $description = $response['description'];
            } elseif (is_array($response['description'])) {
                $description = implode(' ', $response['description']);
            }
        }

        $parts = array_filter([$title, $description]);
        $message = $parts ? implode(' - ', $parts) : 'RDAP error response';

        return new self($message, $code, $response);
    }

    /**
     * Get the raw response array that triggered the exception.
     *
     * @return array<string, mixed>
     */
    public function getResponse(): array
    {
        return $this->response;
    }
}

<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Traits;

use ArrayAccess\RdapClient\Exceptions\InvalidDataTypeException;
use ArrayAccess\RdapClient\Exceptions\MismatchDataBehaviorException;
use function gettype;
use function is_a;
use function is_array;
use function is_bool;
use function is_integer;
use function is_numeric;
use function is_object;
use function is_string;
use function sprintf;

trait AssertionTrait
{
    private function determineAssertionType(mixed $item) : string
    {
        return is_object($item) ? $item::class : gettype($item);
    }

    private function assertArray(mixed $item, ?string $key = null): void
    {
        if (!is_array($item)) {
            throw new InvalidDataTypeException(
                $key !== null ? sprintf(
                    'Key value of "%s" must be as an array. Type of "%s" given.',
                    $key,
                    $this->determineAssertionType($item)
                ) : sprintf(
                    'Argument must be as an array. Type of "%s" given.',
                    $this->determineAssertionType($item)
                )
            );
        }
    }

    private function assertString(mixed $item, ?string $key = null): void
    {
        if (!is_string($item)) {
            throw new InvalidDataTypeException(
                $key !== null ? sprintf(
                    'Key value of "%s" must be as a string. Type of "%s" given.',
                    $key,
                    $this->determineAssertionType($item)
                ) : sprintf(
                    'Argument must be as a string. Type of "%s" given.',
                    $this->determineAssertionType($item)
                )
            );
        }
    }

    private function assertNumeric(mixed $item, ?string $key = null): void
    {
        if (!is_numeric($item)) {
            throw new InvalidDataTypeException(
                $key !== null ? sprintf(
                    'Key value of "%s" must be as a numeric. Type of "%s" given.',
                    $key,
                    $this->determineAssertionType($item)
                ) : sprintf(
                    'Argument must be as a numeric. Type of "%s" given.',
                    $this->determineAssertionType($item)
                )
            );
        }
    }

    private function assertInteger(mixed $item, ?string $key = null): void
    {
        if (!is_integer($item)) {
            throw new InvalidDataTypeException(
                $key !== null ? sprintf(
                    'Key value of "%s" must be as an integer. Type of "%s" given.',
                    $key,
                    $this->determineAssertionType($item)
                ) : sprintf(
                    'Argument must be as an integer. Type of "%s" given.',
                    $this->determineAssertionType($item)
                )
            );
        }
    }

    private function assertStringOrArray(mixed $item, ?string $key = null): void
    {
        if (is_string($item) || is_array($item)) {
            return;
        }
        throw new InvalidDataTypeException(
            $key !== null ? sprintf(
                'Key value of "%s" must be as a string or array. Type of "%s" given.',
                $key,
                $this->determineAssertionType($item)
            ) : sprintf(
                'Argument must be as a string or array. Type of "%s" given.',
                $this->determineAssertionType($item)
            )
        );
    }

    /**
     * @param array<array-key, mixed> $item
     * @param string|null $key
     * @return void
     */
    private function assertArrayStringValue(array $item, ?string $key = null): void
    {
        foreach ($item as $i) {
            if (is_string($i)) {
                continue;
            }
            throw new InvalidDataTypeException(
                $key !== null ? sprintf(
                    'Key value of "%s" must be as an array string. Type of "%s" given.',
                    $key,
                    $this->determineAssertionType($i)
                ) : sprintf(
                    'Argument must be as an array string. Type of "%s" given.',
                    $this->determineAssertionType($i)
                )
            );
        }
    }

    /**
     * @param array<array-key, mixed> $item
     * @param string|null $key
     * @return void
     */
    private function assertArrayBooleanValue(array $item, ?string $key = null): void
    {
        foreach ($item as $i) {
            if (is_bool($i)) {
                continue;
            }
            throw new InvalidDataTypeException(
                $key !== null ? sprintf(
                    'Key value of "%s" must be as an array boolean. Type of "%s" given.',
                    $key,
                    $this->determineAssertionType($i)
                ) : sprintf(
                    'Argument must be as an array boolean. Type of "%s" given.',
                    $this->determineAssertionType($i)
                )
            );
        }
    }

    /**
     * @param array<array-key, mixed> $item
     * @param string|null $key
     * @return void
     */
    private function assertArrayStringKey(array $item, ?string $key = null): void
    {
        foreach ($item as $i => $value) {
            if (is_string($i)) {
                continue;
            }
            throw new InvalidDataTypeException(
                $key !== null ? sprintf(
                    'Key offset value of "%s" must be as a string. Type of "%s" given.',
                    $key,
                    $this->determineAssertionType($i)
                ) : sprintf(
                    'Argument key offset in array must be as a string. Type of "%s" given.',
                    $this->determineAssertionType($i)
                )
            );
        }
    }

    /**
     * @param mixed $item
     * @param string|null $key
     * @return void
     */
    private function assertBoolean(mixed $item, ?string $key = null): void
    {
        if (!is_bool($item)) {
            throw new InvalidDataTypeException(
                $key !== null ? sprintf(
                    'Key value of "%s" must be as a boolean. Type of "%s" given.',
                    $key,
                    $this->determineAssertionType($item)
                ) : sprintf(
                    'Argument must be as a boolean. Type of "%s" given.',
                    $this->determineAssertionType($item)
                )
            );
        }
    }

    /**
     * @param mixed $item
     * @param string|object $instance
     * @param string|null $key
     * @return void
     */
    private function assertInstanceOf(mixed $item, string|object $instance, ?string $key = null): void
    {
        $instance = is_object($instance) ? $instance::class : $instance;
        if (!is_a($item, $instance)) {
            throw new InvalidDataTypeException(
                $key !== null ? sprintf(
                    'Key values of "%s" must be instance of "%s". "%s" given.',
                    $key,
                    $instance,
                    $this->determineAssertionType($item)
                ) : sprintf(
                    'Argument must be instance of "%s". "%s" given.',
                    $instance,
                    $this->determineAssertionType($item)
                )
            );
        }
    }

    /**
     * @param mixed $item
     * @param mixed $expected
     * @param string|null $key
     * @return void
     */
    private function assertEqual(mixed $item, mixed $expected, ?string $key = null): void
    {
        if ($item !== $expected) {
            throw new MismatchDataBehaviorException(
                sprintf(
                    'Data is not equal with given value%s',
                    $key !== null
                        ? sprintf(' for key %s', $key)
                        : ''
                )
            );
        }
    }

    /**
     * @param array<array-key, mixed> $item
     * @param int $expected
     * @param string|null $key
     * @return void
     */
    private function assertCount(array $item, int $expected, ?string $key = null) : void
    {
        if (($count = count($item)) !== $expected) {
            throw new MismatchDataBehaviorException(
                sprintf(
                    'Array data length must be "%d" and only contain "%d" length%s',
                    $expected,
                    $count,
                    $key !== null
                        ? sprintf(' for key %s', $key)
                        : ''
                )
            );
        }
    }

    /**
     * @param array<array-key, mixed> $item
     * @param int $expected
     * @param string|null $key
     * @return void
     */
    private function assertCountGreaterThan(array $item, int $expected, ?string $key = null) : void
    {
        if (($count = count($item)) > $expected) {
            return;
        }
        throw new MismatchDataBehaviorException(
            sprintf(
                'Array data length must be greater than "%d" and only contain "%d" length%s',
                $expected,
                $count,
                $key !== null
                    ? sprintf(' for key %s', $key)
                    : ''
            )
        );
    }

    /**
     * @param array<array-key, mixed> $item
     * @param int $expected
     * @param string|null $key
     * @return void
     */
    private function assertCountGreaterOrEqual(array $item, int $expected, ?string $key = null) : void
    {
        if (($count = count($item)) >= $expected) {
            return;
        }
        throw new MismatchDataBehaviorException(
            sprintf(
                'Array data length must be greater or equal "%d" and only contain "%d" length%s',
                $expected,
                $count,
                $key !== null
                    ? sprintf(' for key %s', $key)
                    : ''
            )
        );
    }
}

<?php
/*
* File:     Attribute.php
* Category: -
* Author:   M. Goldenbaum
* Created:  01.01.21 20:17
* Updated:  -
*
* Description:
*  -
*/

namespace Webklex\PHPIMAP;

use ArrayAccess;
use Carbon\Carbon;

/**
 * Class Attribute
 *
 * @package Webklex\PHPIMAP
 */
class Attribute implements ArrayAccess {

    /** @var string $name */
    protected string $name;

    /**
     * Value holder
     *
     * @var array $values
     */
    protected array $values = [];

    /**
     * Attribute constructor.
     * @param string $name
     * @param mixed|null $value
     */
    public function __construct(string $name,  $value = null) {
        $this->setName($name);
        $this->add($value);
    }

    /**
     * Handle class invocation calls
     *
     * @return array|string
     */
    public function __invoke() {
        if ($this->count() > 1) {
            return $this->toArray();
        }
        return $this->toString();
    }

    /**
     * Return the serialized address
     *
     * @return array
     */
    public function __serialize(){
        return $this->values;
    }

    /**
     * Return the stringified attribute
     *
     * @return string
     */
    public function __toString() {
        return implode(", ", $this->values);
    }

    /**
     * Return the stringified attribute
     *
     * @return string
     */
    public function toString(): string {
        return $this->__toString();
    }

    /**
     * Convert instance to array
     *
     * @return array
     */
    public function toArray(): array {
        return $this->__serialize();
    }

    /**
     * Convert first value to a date object
     *
     * @return Carbon
     */
    public function toDate(): Carbon {
        $date = $this->first();
        if ($date instanceof Carbon) return $date;

        return Carbon::parse($date);
    }

    /**
     * Determine if a value exists at a given key.
     *
     * @param int|string $key
     * @return bool
     */
    public function has( $key = 0): bool {
        return array_key_exists($key, $this->values);
    }

    /**
     * Determine if a value exists at a given key.
     *
     * @param int|string $key
     * @return bool
     */
    public function exist( $key = 0): bool {
        return $this->has($key);
    }

    /**
     * Check if the attribute contains the given value
     * @param mixed $value
     *
     * @return bool
     */
    public function contains( $value): bool {
        return in_array($value, $this->values, true);
    }

    /**
     * Get a value by a given key.
     *
     * @param int|string $key
     * @return mixed
     */
    public function get($key = 0) {
        return $this->values[$key] ?? null;
    }

    /**
     * Set the value by a given key.
     *
     * @param mixed $key
     * @param mixed $value
     * @return Attribute
     */
    public function set( $value,  $key = 0): Attribute {
        if (is_null($key)) {
            $this->values[] = $value;
        } else {
            $this->values[$key] = $value;
        }
        return $this;
    }

    /**
     * Unset a value by a given key.
     *
     * @param int|string $key
     * @return Attribute
     */
    public function remove($key = 0): Attribute {
        if (isset($this->values[$key])) {
            unset($this->values[$key]);
        }
        return $this;
    }

    /**
     * Add one or more values to the attribute
     * @param array|mixed $value
     * @param boolean $strict
     *
     * @return Attribute
     */
    public function add( $value, bool $strict = false): Attribute {
        if (is_array($value)) {
            return $this->merge($value, $strict);
        }elseif ($value !== null) {
            $this->attach($value, $strict);
        }

        return $this;
    }

    /**
     * Merge a given array of values with the current values array
     * @param array $values
     * @param boolean $strict
     *
     * @return Attribute
     */
    public function merge(array $values, bool $strict = false): Attribute {
        foreach ($values as $value) {
            $this->attach($value, $strict);
        }

        return $this;
    }

    /**
     * Attach a given value to the current value array
     * @param $value
     * @param bool $strict
     * @return Attribute
     */
    public function attach($value, bool $strict = false): Attribute {
        if ($strict === true) {
            if ($this->contains($value) === false) {
                $this->values[] = $value;
            }
        }else{
            $this->values[] = $value;
        }
        return $this;
    }

    /**
     * Set the attribute name
     * @param $name
     *
     * @return Attribute
     */
    public function setName($name): Attribute {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the attribute name
     *
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Get all values
     *
     * @return array
     */
    public function all(): array {
        reset($this->values);
        return $this->values;
    }

    /**
     * Get the first value if possible
     *
     * @return mixed|null
     */
    public function first() {
        return reset($this->values);
    }

    /**
     * Get the last value if possible
     *
     * @return mixed|null
     */
    public function last() {
        return end($this->values);
    }

    /**
     * Get the number of values
     *
     * @return int
     */
    public function count(): int {
        return count($this->values);
    }

    /**
     * @see  ArrayAccess::offsetExists
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists( $offset): bool {
        return $this->has($offset);
    }

    /**
     * @see  ArrayAccess::offsetGet
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet( $offset) {
        return $this->get($offset);
    }

    /**
     * @see  ArrayAccess::offsetSet
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet( $offset,  $value): void {
        $this->set($value, $offset);
    }

    /**
     * @see  ArrayAccess::offsetUnset
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset( $offset): void {
        $this->remove($offset);
    }

    /**
     * @param callable $callback
     * @return array
     */
    public function map(callable $callback): array {
        return array_map($callback, $this->values);
    }
}
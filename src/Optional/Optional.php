<?php

namespace Tadgola\Optional;

use Tadgola\Exception\IllegalStateException;
use Tadgola\Preconditions\Preconditions;

/**
 * Class Optional
 *
 * A wrapper around a value that represents whether the item exists or not
 *
 * @package Tadgola\Optional
 */
class Optional
{
	private $value;
	private $present;

	/**
	 * Optional constructor.
	 *
	 * @param boolean $present
	 * @param mixed|null $value
	 */
	private function __construct($present, $value = null)
	{
		$this->value = $value;
		$this->present = $present;
	}

	/**
	 * Gets an instance of Optional wrapping the given value
	 *
	 * @param mixed $reference
	 * @return Optional
	 */
	public static function of($reference)
	{
		return new Optional(true, Preconditions::checkNotNull($reference));
	}

	/**
	 * Gets an instance of Optional wrapping the given value, if a null
	 * is passed in Optional.absent() is returned
	 *
	 * @param $reference
	 * @return Optional
	 */
	public static function fromNullable($reference)
	{
		return $reference === null ? static::absent() : static::of($reference);
	}

	/**
	 * Returns an instance with no wrapped value.
	 *
	 * @return Optional
	 */
	public static function absent()
	{
		return new Optional(false);
	}

	/**
	 * Checks if the wrapped value is present
	 * @return bool
	 */
	public function isPresent()
	{
		return $this->present;
	}

	/**
	 * Gets the wrapped value
	 *
	 * @return mixed
	 */
	public function get()
	{
		if ($this->present) {
			return $this->value;
		}

		throw new IllegalStateException('Get called on absent Optional');
	}

	/**
	 * Gets the wrapped value or null if absent
	 *
	 * @return mixed|null
	 */
	public function getOrNull()
	{
		if ($this->present) {
			return $this->value;
		}
		return null;
	}

	/**
	 * Gets the wrapped value of the default one passed in if wrapped value is absent
	 *
	 * @param $defaultValue
	 * @return mixed
	 */
	public function getOrElse($defaultValue)
	{
		Preconditions::checkNotNull($defaultValue);
		if ($this->present) {
			return $this->value;
		}
		return $defaultValue;
	}


}
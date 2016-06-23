<?php

namespace Tadgola\Preconditions;

use Tadgola\Exception\IllegalArgumentException;
use Tadgola\Exception\IllegalStateException;
use Tadgola\Exception\NullPointerException;

/**
 * Class Preconditions
 *
 * Utility class for checking preconditions of a method
 *
 * @package Tadgola\Preconditions
 */
abstract class Preconditions
{
	/**
	 * Check that the argument is true
	 *
	 * @param boolean $expression
	 * @param string|null $errorMessage
	 * @throws IllegalArgumentException
	 */
	public static function checkArgument($expression, $errorMessage = null)
	{
		$errorMessage = self::orDefaultMessage($errorMessage, 'Illegal argument received');

		if ($expression === false) {
			throw new IllegalArgumentException($errorMessage);
		}
	}

	/**
	 * Check that the given state is true
	 *
	 * @param mixed $reference
	 * @param string|null $errorMessage
	 * @return mixed
	 * @throws NullPointerException
	 */
	public static function checkNotNull($reference, $errorMessage = null)
	{
		$errorMessage = self::orDefaultMessage($errorMessage, 'Null reference given');

		if (is_null($reference)) {
			throw new NullPointerException($errorMessage);
		}

		return $reference;
	}

	/**
	 * Check that the state is true
	 *
	 * @param boolean $expression
	 * @param string|null $errorMessage
	 * @throws IllegalStateException
	 */
	public static function checkState($expression, $errorMessage = null)
	{
		$errorMessage = self::orDefaultMessage($errorMessage, 'Illegal argument received');

		if (!$expression) {
			throw new IllegalStateException($errorMessage);
		}
	}

	/**
	 * Returns the message if not null, or the default message
	 *
	 * @param string $errorMessage
	 * @param string $defaultMessage
	 * @return string
	 */
	private static function orDefaultMessage($errorMessage, $defaultMessage)
	{
		return is_null($errorMessage) ? $defaultMessage : $errorMessage;
	}
}
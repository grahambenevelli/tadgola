<?php

namespace Tadgola\Util;

use Tadgola\Exception\NullPointerException;

/**
 * Class Objects
 *
 * Utility class for handling objects
 *
 * @package Tadgola\Util
 */
class Objects
{
	/**
	 * Get the first non null argument passed in
	 * @return mixed
	 */
	public static function firstNonNull()
	{
		$args = func_get_args();

		foreach ($args as $arg) {
			if (!is_null($arg)) {
				return $arg;
			}
		}

		throw new NullPointerException('No non null references passed in');
	}

}
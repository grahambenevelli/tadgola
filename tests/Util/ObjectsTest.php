<?php

namespace Tadgola\Util;

use PHPUnit_Framework_TestCase;
use Tadgola\Exception\NullPointerException;

class ObjectsTest extends PHPUnit_Framework_TestCase
{
	public function testFirstNonNull()
	{
		$actual = Objects::firstNonNull(1, 2);
		$this->assertEquals(1, $actual);

		$actual = Objects::firstNonNull('1', '2');
		$this->assertEquals('1', $actual);

		$actual = Objects::firstNonNull(null, null, null, null, null, '1', '2');
		$this->assertEquals('1', $actual);

		try {
			Objects::firstNonNull(null, null, null, null, null, null, null, null, null);
			$this->fail('NullPointerException was thrown');
		} catch (NullPointerException $e) {}

	}
}

<?php

namespace Tadgola\Optional;

use PHPUnit_Framework_TestCase;
use Tadgola\Exception\IllegalStateException;
use Tadgola\Exception\NullPointerException;

class OptionalTest extends PHPUnit_Framework_TestCase
{
	public function testOf()
	{
		$reference = 1;
		$opt = Optional::of($reference);
		$this->assertTrue($opt->isPresent());
		$this->assertEquals($reference, $opt->get());

		$reference = true;
		$opt = Optional::of($reference);
		$this->assertTrue($opt->isPresent());
		$this->assertEquals($reference, $opt->get());

		$reference = 'foo';
		$opt = Optional::of($reference);
		$this->assertTrue($opt->isPresent());
		$this->assertEquals($reference, $opt->get());

		$reference = 1.1;
		$opt = Optional::of($reference);
		$this->assertTrue($opt->isPresent());
		$this->assertEquals($reference, $opt->get());

		$reference = null;
		try {
			Optional::of($reference);
			$this->fail('NullPointerException should have been thrown');
		} catch (NullPointerException $e) {}
	}

	public function testAbsent()
	{
		$opt = Optional::absent();
		$this->assertTrue(!$opt->isPresent());

		try {
			$opt->get();
			$this->fail('IllegalStateException should have been thrown');
		} catch (IllegalStateException $e) {}
	}

	public function testFromNullable()
	{
		$opt = Optional::fromNullable('foo');

		$this->assertTrue($opt->isPresent());
		$this->assertEquals('foo', $opt->get());

		$opt = Optional::fromNullable(null);

		$this->assertTrue(!$opt->isPresent());
	}

	public function testGetOrNull()
	{
		$this->assertNotNull(Optional::of('foo')->getOrNull());
		$this->assertNull(Optional::absent()->getOrNull());
	}

	public function testGetOrElse()
	{
		$this->assertEquals('foo', Optional::of('foo')->getOrElse('not foo'));
		$this->assertEquals('foo', Optional::absent()->getOrElse('foo'));
	}
}
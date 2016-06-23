<?php

namespace Tadgola;

use PHPUnit_Framework_TestCase;
use Tadgola\Exception\IllegalArgumentException;
use Tadgola\Exception\IllegalStateException;
use Tadgola\Exception\NullPointerException;
use Tadgola\Preconditions\Preconditions;

class PreconditionsTest extends PHPUnit_Framework_TestCase
{

	public function testCheckArgumentPasses()
	{
		Preconditions::checkArgument(true);
		Preconditions::checkArgument(true, 'Message that won\'t be used');
	}

	public function testCheckArgumentFails()
	{
		try {
			Preconditions::checkArgument(false);
		} catch (IllegalArgumentException $e) {
			$this->assertEquals($e->getMessage(), 'Illegal argument received');
		}

		try {
			Preconditions::checkArgument(false, 'FooBar');
		} catch (IllegalArgumentException $e) {
			$this->assertEquals($e->getMessage(), 'FooBar');
		}
	}

	public function testCheckStatePasses()
	{
		Preconditions::checkState(true);
		Preconditions::checkState(true, 'Message that won\'t be used');
	}

	public function testCheckStateFails()
	{
		try {
			Preconditions::checkState(false);
		} catch (IllegalStateException $e) {
			$this->assertEquals($e->getMessage(), 'Illegal argument received');
		}

		try {
			Preconditions::checkState(false, 'FooBar');
		} catch (IllegalStateException $e) {
			$this->assertEquals($e->getMessage(), 'FooBar');
		}
	}

	public function testCheckNotNullPasses()
	{
		Preconditions::checkNotNull(0);
		Preconditions::checkNotNull(0, 'Foo');
		Preconditions::checkNotNull(false);
		Preconditions::checkNotNull(false, 'Foo');
		Preconditions::checkNotNull('');
		Preconditions::checkNotNull('', 'Foo');
		Preconditions::checkNotNull(new NullPointerException());
		Preconditions::checkNotNull(new NullPointerException(), 'Foo');
	}

	public function testCheckNotNullFails()
	{
		try {
			Preconditions::checkNotNull(null);
		} catch (NullPointerException $e) {
			$this->assertEquals($e->getMessage(), 'Null reference given');
		}

		try {
			Preconditions::checkNotNull(null, 'FooBar');
		} catch (NullPointerException $e) {
			$this->assertEquals($e->getMessage(), 'FooBar');
		}
	}
}
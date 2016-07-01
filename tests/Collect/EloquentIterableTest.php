<?php

namespace Tadgola\Optional;

use PHPUnit_Framework_TestCase;
use Tadgola\Collect\EloquentIterable;
use Tadgola\Exception\IllegalStateException;
use Tadgola\Exception\NullPointerException;

class EloquentIterableTest extends PHPUnit_Framework_TestCase
{

	public function testIterable()
	{
		$iter = EloquentIterable::wrap([1, 2, 3]);
		$counter = 0;
		foreach($iter as $value) {
			$counter++;
			$this->assertEquals($counter, $value);
		}
		$this->assertEquals(3, $counter);
	}

	public function testIterableWithKeys()
	{
		$iter = EloquentIterable::wrap([1, 2, 3]);
		$counter = 0;
		foreach($iter as $key => $value) {
			$counter++;
			$this->assertEquals($counter, $value);
			$this->assertEquals($counter-1, $key);
		}
		$this->assertEquals(3, $counter);
	}

	public function testIterableWithKeysSpecified()
	{
		$iter = EloquentIterable::wrap([2 => 1, 3 => 2, 4 => 3]);
		$counter = 0;
		foreach($iter as $key => $value) {
			$counter++;
			$this->assertEquals($counter, $value);
			$this->assertEquals($counter+1, $key);
		}
		$this->assertEquals(3, $counter);
	}

	public function testAllMatch()
	{
		$this->assertTrue(EloquentIterable::wrap([])
			->allMatch(function($value) {
				return $value > 0;
			}));

		$this->assertTrue(EloquentIterable::wrap([1, 2, 3])
			->allMatch(function($value) {
				return $value > 0;
			}));

		$this->assertFalse(EloquentIterable::wrap([1, 2, 3, 0])
			->allMatch(function($value) {
				return $value > 0;
			}));

		$this->assertFalse(EloquentIterable::wrap([0, 1, 2, 3])
			->allMatch(function($value) {
				return $value > 0;
			}));
	}

	public function testAnyMatch()
	{
		$this->assertFalse(EloquentIterable::wrap([])
			->anyMatch(function($value) {
				return $value < 0;
			}));

		$this->assertFalse(EloquentIterable::wrap([1, 2, 3])
			->anyMatch(function($value) {
				return $value < 0;
			}));

		$this->assertTrue(EloquentIterable::wrap([-1, 1, 2, 3])
			->anyMatch(function($value) {
				return $value < 0;
			}));

		$this->assertTrue(EloquentIterable::wrap([1, 2, 3, -1])
			->anyMatch(function($value) {
				return $value < 0;
			}));
	}

	public function testAppendIterator()
	{
		$first = [1, 2, 3];
		$second = [4, 5];

		$iter = EloquentIterable::wrap($first)
			->appendIterator($second);

		$counter = 0;
		foreach($iter as $value) {
			$counter++;
			$this->assertEquals($counter, $value);
		}
		$this->assertEquals(5, $counter);
	}

	public function testAppendIteratorWithKeys()
	{
		$first = [1, 2, 3];
		$second = [4, 5];

		$iter = EloquentIterable::wrap($first)
			->appendIterator($second);

		$counter = 0;
		foreach($iter as $key => $value) {
			$counter++;
			$this->assertEquals($counter, $value);
			$this->assertEquals(($counter-1) % 3, $key);
		}
		$this->assertEquals(5, $counter);
	}

	public function testAppendIteratorWithKeysSpecified()
	{
		$first = ['a' => 1, 'b' => 2, 'c' => 3];
		$second = ['d' => 4, 'e' => 5];

		$iter = EloquentIterable::wrap($first)
			->appendIterator($second);

		$counter = 0;
		foreach($iter as $key => $value) {
			$counter++;
			$this->assertEquals($counter, $value);
			$this->assertEquals(chr(ord('a') + ($counter-1)), $key);
		}
		$this->assertEquals(5, $counter);
	}

	public function testAppendIteratorEmptyFirst()
	{
		$first = [];
		$second = [4, 5];

		$iter = EloquentIterable::wrap($first)
			->appendIterator($second);

		$counter = 0;
		foreach($iter as $value) {
			$counter++;
			$this->assertEquals($counter + 3, $value);
		}
		$this->assertEquals(2, $counter);
	}

	public function testAppendIteratorEmptySecond()
	{
		$first = [1, 2, 3];
		$second = [];

		$iter = EloquentIterable::wrap($first)
			->appendIterator($second);

		$counter = 0;
		foreach($iter as $value) {
			$counter++;
			$this->assertEquals($counter, $value);
		}
		$this->assertEquals(3, $counter);
	}

	public function testAppendIteratorBothEmpty()
	{
		$first = [];
		$second = [];

		$iter = EloquentIterable::wrap($first)
			->appendIterator($second);

		$counter = 0;
		foreach($iter as $value) {
			$counter++;
		}
		$this->assertEquals(0, $counter);
	}

	public function testAppendElements()
	{
		$iter = EloquentIterable::wrap([1, 2, 3])
			->appendElements(4, 5);

		$counter = 0;
		foreach($iter as $key => $value) {
			$counter++;
			$this->assertEquals($counter, $value);
			$this->assertEquals(($counter-1) % 3, $key);
		}
		$this->assertEquals(5, $counter);
	}

	public function testCycle()
	{
		$iter = EloquentIterable::wrap([1, 2, 3])
			->cycle();

		$counter = 0;
		foreach($iter as $key => $value) {
			$counter++;
			if ($counter >= 5) {
				break;
			}
		}
		$this->assertEquals(5, $counter);
	}

	public function testFilter()
	{
		$iter = EloquentIterable::wrap([1, 0, 2, 3])
			->filter(function($value) {
				return $value > 0;
			});

		$counter = 0;
		foreach($iter as $value) {
			$counter++;
			$this->assertEquals($counter, $value);
		}
		$this->assertEquals(3, $counter);
	}

	public function testFilterEmpty()
	{
		$iter = EloquentIterable::wrap([])
			->filter(function ($annotation) {
				return $annotation->getAnnotation() == '@var';
			});

		$this->assertEquals([], $iter->toArray());
	}

	public function testFilterLotsOfInvalid()
	{
		$iter = EloquentIterable::wrap([0, -1, -2, -3, 0, 0, 1, 0, 2, -1, -3, -1, 3, 0])
			->filter(function($value) {
				return $value > 0;
			});

		$counter = 0;
		foreach($iter as $value) {
			$counter++;
			$this->assertEquals($counter, $value);
		}
		$this->assertEquals(3, $counter);
	}

	public function testFilterAllInvalid()
	{
		$iter = EloquentIterable::wrap([0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0])
			->filter(function($value) {
				return $value > 0;
			});

		foreach($iter as $value) {
			$this->fail("No item should be returned");
		}
	}

	public function testFirst()
	{
		$first = EloquentIterable::wrap([1, 2, 3])
			->first();

		$this->assertTrue($first->isPresent());
		$this->assertEquals(1, $first->get());
	}

	public function testFirstEmpty()
	{
		$first = EloquentIterable::wrap([])
			->first();

		$this->assertFalse($first->isPresent());
	}

	public function testFirstMatch()
	{
		$first = EloquentIterable::wrap([-2, -1, 0, 1, 2, 3])
			->firstMatch(function($value) {
				return $value > 0;
			});

		$this->assertTrue($first->isPresent());
		$this->assertEquals(1, $first->get());
	}

	public function testFirstMatchEmpty()
	{
		$first = EloquentIterable::wrap([])
			->firstMatch(function($value) {
				return $value > 0;
			});

		$this->assertFalse($first->isPresent());
	}

	public function testFirstMatchAllInvalid()
	{
		$first = EloquentIterable::wrap([0, -1, -2, -3, -4, -5, -6])
			->firstMatch(function($value) {
				return $value > 0;
			});

		$this->assertFalse($first->isPresent());
	}

	public function testEmptyOnFull()
	{
		$isEmpty = EloquentIterable::wrap([1, 2, 3, 4])
			->isEmpty();

		$this->assertFalse($isEmpty);
	}

	public function testEmptyOnEmpty()
	{
		$isEmpty = EloquentIterable::wrap([])
			->isEmpty();

		$this->assertTrue($isEmpty);
	}

	public function testEmptyOnEmptyAfterFilter()
	{
		$isEmpty = EloquentIterable::wrap([0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0])
			->filter(function($value) {
				return $value > 0;
			})
			->isEmpty();

		$this->assertTrue($isEmpty);
	}

	public function testLast()
	{
		$last = EloquentIterable::wrap([1, 2, 3])
			->last();

		$this->assertTrue($last->isPresent());
		$this->assertEquals(3, $last->get());
	}

	public function testLastEmpty()
	{
		$last = EloquentIterable::wrap([])
			->last();

		$this->assertFalse($last->isPresent());
	}

	public function testSize()
	{
		$this->assertEquals(0, EloquentIterable::wrap([])->size());
		$this->assertEquals(3, EloquentIterable::wrap([1, 2, 3])->size());
		$predicate = function ($value) {
			return $value < 0;
		};
		$this->assertEquals(0, EloquentIterable::wrap([1, 2, 3])
			->filter($predicate)
			->size());
	}

	public function testToArray()
	{
		$actual = EloquentIterable::wrap([1, 2, 3])
			->toArray();

		$this->assertEquals([1, 2, 3], $actual);

		$actual = EloquentIterable::wrap([])
			->toArray();

		$this->assertEquals([], $actual);

		$actual = EloquentIterable::wrap([1, 2, 3])
			->appendElements(4, 5)
			->toArray();

		$this->assertEquals([1, 2, 3, 4, 5], $actual);

		$actual = EloquentIterable::wrap([1, 2, 3])
			->appendElements([4, 5])
			->toArray();

		$this->assertEquals([1, 2, 3, [4, 5]], $actual);

		$actual = EloquentIterable::wrap([1, 2, 3])
			->appendIterator([4, 5])
			->toArray();

		$this->assertEquals([1, 2, 3, 4, 5], $actual);
	}

	public function testToMap()
	{
		$actual = EloquentIterable::wrap([1, 2, 3])
			->toMap(function($value) {
				return $value * 2 + 1;
			});

		$this->assertEquals([
			1 => 3,
			2 => 5,
			3 => 7
		], $actual);
	}

	public function testTransform()
	{
		$func = function($value) {
			return $value + 1;
		};

		$iter = EloquentIterable::wrap([1, 2, 3])
			->transform($func);

		$this->assertEquals([2, 3, 4], $iter->toArray());
	}
}
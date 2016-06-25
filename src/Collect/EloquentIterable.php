<?php

namespace Tadgola\Collect;


use ArrayIterator;
use Closure;
use Iterator;
use Tadgola\Collect\Impl\AppendedEloquentIterable;
use Tadgola\Collect\Impl\BasicEloquentIterable;
use Tadgola\Collect\Impl\CyclingEloquentIterable;
use Tadgola\Collect\Impl\FilteredEloquentIterable;
use Tadgola\Collect\Impl\LimitedEloquentIterator;
use Tadgola\Collect\Impl\TransformEloquentIterable;
use Tadgola\Optional\Optional;
use Tadgola\Preconditions\Preconditions;

abstract class EloquentIterable implements Iterator
{
	/**
	 * Create new instance of a EloquentIterable
	 * wrapping the passed in iterable
	 *
	 * @param $iterable
	 * @return BasicEloquentIterable
	 */
	public static function wrap($iterable)
	{
		if (is_array($iterable)) {
			$iterable = new ArrayIterator($iterable);
		}
		return new BasicEloquentIterable($iterable);
	}

	/**
	 * Return the current element
	 *
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 * @since 5.0.0
	 */
	public abstract function current();

	/**
	 * Move forward to next element
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public abstract function next();

	/**
	 * Return the key of the current element
	 * @link http://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 * @since 5.0.0
	 */
	public abstract function key();

	/**
	 * Checks if all elements match the given predicate
	 *
	 * @param Closure $predicate
	 * @return bool
	 */
	public function allMatch(Closure $predicate)
	{
		Preconditions::checkNotNull($predicate);
		foreach ($this as $value) {
			if (!$predicate($value)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Checks if any elements matches the given predicate
	 *
	 * @param Closure $predicate
	 * @return bool
	 */
	public function anyMatch(Closure $predicate)
	{
		Preconditions::checkNotNull($predicate);
		foreach ($this as $value) {
			if ($predicate($value)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns a eloquent iterable whose iterators traverse first the elements of this eloquent iterable, followed by the elements passed in.
	 *
	 * @return EloquentIterable
	 */
	public function appendElements()
	{
		$args = func_get_args();
		return $this->appendIterator($args);
	}

	/**
	 * Returns a eloquent iterable whose iterators traverse first the elements of this eloquent iterable, followed by those of other.
	 *
	 * @param Iterator|array $iter
	 * @return EloquentIterable
	 */
	public function appendIterator($iter)
	{
		if (is_array($iter)) {
			$iter = new ArrayIterator($iter);
		}
		return new AppendedEloquentIterable($this, $iter);
	}

	/**
	 * Returns true if this eloquent iterable contains any object for which equals(target) is true.
	 *
	 * @param $other
	 * @return bool
	 */
	public function contains($other)
	{
		Preconditions::checkNotNull($other);
		foreach ($this as $value) {
			if (!$other == $value) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Returns a eloquent iterable who cycles indefinitely over the elements of this eloquent iterable.
	 */
	public function cycle()
	{
		return new CyclingEloquentIterable($this);
	}

	/**
	 * Returns an Optional containing the first element in this eloquent iterable that satisfies the given predicate, if such an element exists.
	 *
	 * @param Closure $predicate
	 * @return Optional
	 */
	public function firstMatch(Closure $predicate)
	{
		return $this->filter($predicate)->first();
	}

	/**
	 * Returns an Optional containing the first element in this eloquent iterable.
	 *
	 * @return Optional
	 */
	public function first()
	{
		foreach ($this as $value) {
			return Optional::of($value);
		}
		return Optional::absent();
	}

	/**
	 * Returns the elements from this eloquent iterable that satisfy a predicate.
	 *
	 * @param Closure $predicate
	 * @return EloquentIterable
	 */
	public function filter(Closure $predicate)
	{
		return new FilteredEloquentIterable($this, $predicate);
	}

	/**
	 * Determines whether this eloquent iterable is empty.
	 */
	public function isEmpty()
	{
		$this->rewind();
		return !$this->valid();
	}

	/**
	 * Rewind the Iterator to the first element
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public abstract function rewind();

	/**
	 * Checks if current position is valid
	 * @link http://php.net/manual/en/iterator.valid.php
	 * @return boolean The return value will be casted to boolean and then evaluated.
	 * Returns true on success or false on failure.
	 * @since 5.0.0
	 */
	public abstract function valid();

	/**
	 * Returns an Optional containing the last element in this eloquent iterable.
	 */
	public function last()
	{
		$result = Optional::absent();
		foreach ($this as $value) {
			$result = Optional::of($value);
		}
		return $result;
	}

	/**
	 * Returns an Optional containing the last element in this eloquent iterable.
	 *
	 * @param $maxSize
	 * @return EloquentIterable
	 */
	public function limit($maxSize)
	{
		return new LimitedEloquentIterator($this, $maxSize);
	}

	/**
	 * Returns the number of elements in this eloquent iterable.
	 */
	public function size()
	{
		$size = 0;
		foreach ($this as $value) {
			$size++;
		}
		return $size;
	}

	/**
	 * Returns a map whose keys are the distinct elements of this FluentIterable and whose value for each key was computed by valueFunction.
	 *
	 * @param Closure $valueFunction
	 * @return array
	 */
	public function toMap(Closure $valueFunction)
	{
		$result = [];
		foreach ($this as $value) {
			$result[$value] = $valueFunction($value);
		}
		return $result;
	}

	/**
	 * Returns an array containing all of the elements from this eloquent iterable with duplicates removed.
	 */
	public function toSet()
	{
		$result = [];
		foreach ($this as $key => $value) {
			$result[$value] = 1;
		}
		return array_keys($result);
	}

	/**
	 * Returns an array containing all of the elements from this FluentIterable in order
	 */
	public function toSortedArray()
	{
		return sort($this->toArray());
	}

	/**
	 * Returns an array containing all of the elements from this eloquent iterable in iteration order.
	 * This does not preserve keys
	 */
	public function toArray()
	{
		$result = [];
		foreach ($this as $key => $value) {
			$result[] = $value;
		}
		return $result;
	}

	/**
	 * Returns a eloquent iterable that applies the given closure to each element of this eloquent iterable.
	 *
	 * @param Closure $transformation
	 * @return EloquentIterable
	 */
	public function transform(Closure $transformation)
	{
		return new TransformEloquentIterable($this, $transformation);
	}
}
<?php

namespace Tadgola\Collect\Impl;


use Tadgola\Collect\EloquentIterable;
use Iterator;
use Closure;

/**
 * An iterable that filters out invalid values
 */
class FilteredEloquentIterable extends EloquentIterable
{

	/**
	 * @var Iterator
	 */
	private $iter;

	/**
	 * @var Closure
	 */
	private $predicate;

	/**
	 * FilteredEloquentIterable constructor.
	 * @param Iterator $iter
	 * @param Closure $predicate
	 */
	public function __construct(Iterator $iter, Closure $predicate)
	{
		$this->iter = $iter;
		$this->predicate = $predicate;

		$predicate = $this->predicate;
		$current = $this->iter->current();
		if (!$predicate($current)) {
			$this->next();
		}
	}

	/**
	 * Return the current element
	 *
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 * @since 5.0.0
	 */
	public function current()
	{
		return $this->iter->current();
	}

	/**
	 * Move forward to next element
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function next()
	{
		$predicate = $this->predicate;
		while ($this->iter->valid()) {
			$this->iter->next();
			$match = $predicate($this->iter->current());
			if ($match) {
				break;
			}
		}
	}

	/**
	 * Return the key of the current element
	 * @link http://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 * @since 5.0.0
	 */
	public function key()
	{
		return $this->iter->key();
	}

	/**
	 * Checks if current position is valid
	 * @link http://php.net/manual/en/iterator.valid.php
	 * @return boolean The return value will be casted to boolean and then evaluated.
	 * Returns true on success or false on failure.
	 * @since 5.0.0
	 */
	public function valid()
	{
		return $this->iter->valid();
	}

	/**
	 * Rewind the Iterator to the first element
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function rewind()
	{
		$this->iter->rewind();
		$predicate = $this->predicate;
		$current = $this->iter->current();
		if (!$predicate($current)) {
			$this->next();
		}
	}
}
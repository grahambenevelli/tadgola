<?php

namespace Tadgola\Collect\Impl;

use Iterator;
use Tadgola\Collect\EloquentIterable;

/**
 * Iterable that iterators over two iterators
 */
class AppendedEloquentIterable extends EloquentIterable
{

	/**
	 * @var Iterator
	 */
	private $first;

	/**
	 * @var Iterator
	 */
	private $second;

	/**
	 * @var boolean
	 */
	private $onFirst;

	/**
	 * BasicEloquentIterable constructor.
	 * @param Iterator $first
	 * @param Iterator $second
	 */
	public function __construct(Iterator $first, Iterator $second)
	{
		$this->first = $first;
		$this->second = $second;
		$this->onFirst = true;
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
		if ($this->onFirst) {
			return $this->first->current();
		} else {
			return $this->second->current();
		}
	}

	/**
	 * Move forward to next element
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function next()
	{
		if ($this->onFirst) {
			$this->first->next();
		} else {
			$this->second->next();
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
		if ($this->onFirst) {
			return $this->first->key();
		} else {
			return $this->second->key();
		}
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
		if ($this->onFirst) {
			if ($this->first->valid()) {
				return true;
			} else {
				$this->onFirst = false;
				return $this->second->valid();
			}
		} else {
			return $this->second->valid();
		}
	}

	/**
	 * Rewind the Iterator to the first element
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function rewind()
	{
		$this->first->rewind();
		$this->second->rewind();
		$this->onFirst = true;
	}
}
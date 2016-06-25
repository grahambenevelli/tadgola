<?php

namespace Tadgola\Collect\Impl;


use Iterator;
use Closure;
use Tadgola\Collect\EloquentIterable;

/**
 * Iterable that transforms the under lying iterator
 */
class TransformEloquentIterable extends EloquentIterable
{

	private $iter;
	private $tranformation;

	/**
	 * TransformEloquentIterable constructor.
	 *
	 * @param Iterator $iter
	 * @param Closure $transformation
	 */
	public function __construct(Iterator $iter, Closure $transformation)
	{
		$this->iter = $iter;
		$this->tranformation = $transformation;
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
		$transformation = $this->tranformation;
		return $transformation($this->iter->current());
	}

	/**
	 * Move forward to next element
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function next()
	{
		$this->iter->next();
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
	}
}
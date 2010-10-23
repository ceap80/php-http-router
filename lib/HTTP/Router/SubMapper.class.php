<?php
namespace HTTP\Router;

require_once 'HTTP/Router.class.php';

class SubMapper {

	/*
	 *
	 */
	private $parent = null;

	/*
	 *
	 */
	private $pattern = null;

	/*
	 *
	 */
	private $destination = null;

	/*
	 *
	 */
	private $options = null;
	/*
	 *
	 */
	public function __construct(\HTTP\Router &$parent, $pattern = array(), $destination = array(), $options = array())
	{
		$this->parent = $parent;
		$this->pattern = $pattern;
		$this->destination = $destination;
		$this->options = $options;
	}

	/*
	 *
	 */
	public function connect($pattern, $destination = array(), $options = array())
	{
		$this->parent->connectWithName(
			null,
			empty($this->pattern) ? $pattern : $this->pattern.$pattern,
			array_merge($this->destination, (array)$destination),
			array_merge($this->options, (array)$options)
		);

		return $this;
	}

	/*
	 *
	 */
	public function connectWithName($name, $pattern, $destination = array(), $options = array())
	{
		$this->parent->connectWithName(
			$name,
			empty($this->pattern) ? $pattern : $this->pattern.$pattern,
			array_merge($this->destination, (array)$destination),
			array_merge($this->options, (array)$options)
		);

		return $this;
	}

	/*
	 *
	 */
	public function getParent()
	{
		return $this->parent;
	}

	/*
	 *
	 */
	public function __toString()
	{
		return $this->parent->__toString();
	}
}


<?php
namespace HTTP\Router;

class SubMapper {

	/*
	 *
	 */
	function __construct()
	{
	}

	/*
	 *
	 */
	function connect($pattern, $destination = array(), $options = array())
	{
		if ($this->pattern) {
			$pattern = $this->pattern.$pattern;
		}

		$this->parent->connect(
			$pattern,
			array_merge($this->destination, $destination),
			array_merge($this->options, $options),
		);

		return $this;
	}
}


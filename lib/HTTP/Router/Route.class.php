<?php
namespace HTTP\Router;

class Route {

	/*
	 *
	 */
	private $data = array();

	/*
	 *
	 */
	private $name = null;

	/*
	 *
	 */
	private $pattern = null;

	/*
	 *
	 */
	private $destination = array();

	/*
	 *
	 */
	private $options = array();

	/*
	 *
	 */
	function __construct()
	{
		$args = func_get_args();
		$data = array();

		if (count($args) < 4) {
			array_unshift($args, null);
		}

		list($name, $pattern) = $args;

		$destination = array();
		if (!empty($args[2])) {
			$destination = $args[2];
		}

		$options = array();
		if (!empty($args[3])) {
			$options = $args[3];
		}

		$on_match = false;
		if (isset($options['on_match']) && is_callable($options['on_match'])) {
			$data['on_match'] = $options['on_match'];
		}

		if (isset($optoons['method'])) {
			$method = $options['method'];
			$data['method'] = is_array($method) ? $method : array($method);
			$data['method_re'] = sprintf('@^(?:%s)$@', implode('|', array_values($data['method'])));
		}

		if (isset($options['host'])) {
			$data['host'] = $options['host'];
			$data['host_re'] = sprintf('@^%s$@', preg_quote($data['host'], '@'));
		}

		$data['pattern'] = $pattern;
		
		$captures = array();

		$pattern_re = preg_replace_callback(
			'@
			\{((?:\{[0-9,]+\}|[^{}]+)+)\} | 
			:([A-Za-z0-9_]+)              |
			(\*)                          |
			([^{:*]+)
			@mx',
			function ($matches) use (&$captures) {
				if (!empty($matches[1])) {
					$pair = explode(':', $matches[1]);

					array_push($captures, $pair[0]);

					if (!empty($pair[1])) {
						return sprintf('(%s)', $pair[1]);
					}

					return '([^/]+)';
				} elseif (!empty($matches[2])) {
					array_push($captures, $matches[2]);

					return '([^/]+)';
				} elseif (!empty($matches[3])) {
					array_push($captures, '__SPLAT__');

					return '(.+)';
				}

				return preg_quote($matches[4], '@');
			},
			$pattern
		);

		$data['pattern_re'] = sprintf('@^%s$@', $pattern_re);

		$this->data = $data;
		$this->captures = $captures;

		$this->name = $name;
		$this->pattern = $pattern;
		$this->destination = $destination;
		$this->options = $options;
	}

	/*
	 *
	 */
	function match($env)
	{
		if (!empty($this->data['host_re'])) {
			if (!isset($env['HTTP_HOST']) || !preg_match($this->data['host_re'], $env['HTTP_HOST'])) {
				return false;
			}
		}

		if (!empty($this->data['method_re'])) {
			if (!isset($env['REQUEST_METHOD']) || !preg_match($this->data['method_re'], $env['REQUEST_METHOD'])) {
				return false;
			}
		}

		$captured = array();
		preg_match($this->data['pattern_re'], $env['PATH_INFO'], $captured);
		if (!empty($captured)) {
			$args = array();
			$splat = array();

			array_shift($captured);

			for ($index = 0; $index < count($this->captures); $index++) {
				if ($this->captures[$index] == '__SPLAT__') {
					$splat[] = $captured[$index];
				} else {
					$args[$this->captures[$index]] = $captured[$index];
				}
			}

			$match = array_merge((array)$this->destination, $args);

			if (!empty($splat)) {
				$match['splat'] = $splat;
			}

			if (isset($this->data['on_match']) && is_callable($this->data['on_match'])) {
				$result = $this->data['on_match']($env, $match);

				if (empty($result)) {
					return false;
				}
			}

			return $match;
		}

		return false;
	}

}


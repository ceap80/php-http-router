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

		if (isset($options['method'])) {
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

		if ($this->regexpUses($pattern)) {
			$data['regexp_uses'] = true;
			$data['pattern_re'] = $pattern;
		} else {
			$data['regexp_uses'] = false;
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
		}


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

			if ($this->data['regexp_uses']) {
				$splat = $captured;

				foreach ($splat as $key => $value) {
					if (!is_integer($key)) {
						$args[$key] = $value;
						unset($splat[$key]);
					}
				}
			} else {
				for ($index = 0; $index < count($this->captures); $index++) {
					if ($this->captures[$index] == '__SPLAT__') {
						$splat[] = $captured[$index];
					} else {
						$args[$this->captures[$index]] = $captured[$index];
					}
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

	/*
	 *
	 */
	public function regexpUses($pattern)
	{
		// See: http://php.net/manual/reference.pcre.pattern.modifiers.php
		// See: http://php.net/manual/regexp.reference.delimiters.php
		if (preg_match('@^([^\s\\\\a-z0-9])(?:.*?)\1(?:[imsxeADSUXJu]*)?$@mD', $pattern)) return true;
		if (preg_match('@^\{(?:.*?)\}(?:[imsxeADSUXJu]*)?$@mD', $pattern)) return true;
		if (preg_match('@^\[(?:.*?)\](?:[imsxeADSUXJu]*)?$@mD', $pattern)) return true;
		if (preg_match('@^\<(?:.*?)\>(?:[imsxeADSUXJu]*)?$@mD', $pattern)) return true;
		if (preg_match('@^\((?:.*?)\)(?:[imsxeADSUXJu]*)?$@mD', $pattern)) return true;
		return false;
	}

	/*
	 *
	 */
	public function getName()
	{
		if (!empty($this->name)) {
			return $this->name;
		}

		return null;
	}

	/*
	 *
	 */
	public function getPattern()
	{
		if (!empty($this->pattern)) {
			return $this->pattern;
		}

		return null;
	}

	/*
	 *
	 */
	public function getData($key = null)
	{
		if (empty($key)) {
			return array_keys($this->data);
		}

		if (!empty($this->data[$key])) {
			return $this->data[$key];
		}

		return null;
	}
}


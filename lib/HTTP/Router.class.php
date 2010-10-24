<?php
namespace HTTP;

require_once 'HTTP/Router/Route.class.php';
require_once 'HTTP/Router/SubMapper.class.php';

class Router {

	/*
	 *
	 */
	private $routes = array();

	/*
	 *
	 */
	public function __construct()
	{
		$this->routes = array();
	}

	/*
	 *
	 */
	public function connect($pattern, $destination = array(), $options = array())
	{
		$this->routes[] =& new \HTTP\Router\Route($pattern, $destination, $options);
		return $this;
	}

	/*
	 *
	 */
	public function connectWithName($name, $pattern, $destination = array(), $options = array())
	{
		$this->routes[] =& new \HTTP\Router\Route($name, $pattern, $destination, $options);
		return $this;
	}

	/*
	 *
	 */
	public function getSubMapper($pattern, $destination = array(), $options = array())
	{
		$submapper =& new \HTTP\Router\SubMapper($this, $pattern, $destination, $options);
		return $submapper;
	}

	/*
	 *
	 */
	public function match($request)
	{
		list($match) = $this->process($request);
		return $match;
	}

	/*
	 *
	 */
	public function routematch($request)
	{
		return $this->process($request);
	}

	/*
	 *
	 */
	private function process($request)
	{
		$env = $_SERVER + $_ENV;

		if ( is_array($request) ) {
			$env = array_merge($env, $request);
		} elseif (!empty($request)) {
			$env = array_merge($env, array('PATH_INFO' => $request));
		}

		foreach ($this->routes as $route) {
			if ( ($result = $route->match($env)) !== false ) {
				return array($result, $route);
			}
		}

		return array(false, false);
	}

	public function __toString()
	{
		if (empty($this->routes)) {
			return "There are no routes to connect.\n";
		}

		$nm = max(array_map(
			function ($route) {
				if ( ($name = $route->getName()) ) {
					return strlen($name);
				}
				return 0;
			},
			$this->routes
		));

		$mm = max(array_map(
			function ($route) {
				if ( ($methods = $route->getData('method')) ) {
					return strlen(implode('|', $methods));
				}
				return 0;
			},
			$this->routes
		));

		$pm = max(array_map(
			function ($route) {
				if ( ($pattern = $route->getPattern()) ) {
					return strlen($pattern);
				}
				return 0;
			},
			$this->routes
		));

		return implode("\n", array_map(
			function ($route) use ($nm, $mm, $pm) {
				return sprintf(
					"%-{$nm}s %-{$mm}s %-{$pm}s %s",
					$route->getName(),
					$route->getData('method') ? implode('|', $route->getData('method')) : null,
					$route->getPattern(),
					$route->getData('host')
				);
			},
			$this->routes
		)) . "\n";
	}

}


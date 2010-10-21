<?php
namespace HTTP;

require_once 'HTTP/Router/Route.class.php';

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

	public function match($request)
	{
		list($match) = $this->process($request);
		return $match;
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

		return false;
	}

}


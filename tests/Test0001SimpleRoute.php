<?php

set_include_path(implode(':', array(
	dirname(dirname(__FILE__)).'/lib',
	get_include_path()
)));

require_once 'PHPUnit/Framework.php';
require_once 'HTTP/Router.class.php';

class Test0001SimpleRoute extends PHPUnit_Framework_TestCase {

	private $router = null;

	public function setup()
	{
		$this->router = new \HTTP\Router();
	}

	public function test_simple_route_01()
	{
		$this->router->connect(
			'/',
			array('controller' => 'root', 'action' => 'show'),
			array('method' => 'GET', 'host' => 'localhost')
		);

		$match = $this->router->match(array(
			'REQUEST_METHOD' => 'GET',
			'PATH_INFO' => '/',
			'HTTP_HOST' => 'localhost',
		));

		$this->assertEquals($match['controller'], 'root');
		$this->assertEquals($match['action'], 'show');
	}

	public function test_simple_route_02()
	{
		$this->router->connect(
			'/blog/:year/{month}',
			array('controller' => 'blog', 'action' => 'monthly'),
			array('method' => 'GET', 'host' => 'localhost')
		);

		$match = $this->router->match(array(
			'REQUEST_METHOD' => 'GET',
			'PATH_INFO' => '/blog/2010/03',
			'HTTP_HOST' => 'localhost',
		));

		$this->assertEquals($match['controller'], 'blog');
		$this->assertEquals($match['action'], 'monthly');
		$this->assertEquals($match['year'], 2010);
		$this->assertEquals($match['month'], '03');
	}

	public function test_simple_route_03()
	{
		$this->router->connect(
			'/blog/{year:[0-9]{1,4}}/{month:\d{2}}/{day:\d\d}',
			array('controller' => 'blog', 'action' => 'daily'),
			array('method' => 'GET')
		);

		$match = $this->router->match(array(
			'REQUEST_METHOD' => 'GET',
			'PATH_INFO' => '/blog/2010/03/04',
		));

		$this->assertEquals($match['controller'], 'blog');
		$this->assertEquals($match['action'], 'daily');
		$this->assertEquals($match['year'], 2010);
		$this->assertEquals($match['month'], '03');
		$this->assertEquals($match['day'], '04');
	}

	public function test_simple_route_04()
	{
		$this->router->connect(
			'/comment',
			array('controller' => 'comment', 'action' => 'create'),
			array('method' => 'POST')
		);

		$match = $this->router->match(array(
			'REQUEST_METHOD' => 'GET',
			'PATH_INFO' => '/comment',
			'HTTP_HOST' => 'localhost',
		));

		$this->assertEquals($match['controller'], 'comment');
		$this->assertEquals($match['action'], 'create');
	}

	public function test_simple_route_05()
	{
		$this->router->connect(
			'/',
			array('controller' => 'root', 'action' => 'show_sub'),
			array('host' => 'sub.localhost')
		);

		$match = $this->router->match(array(
			'PATH_INFO' => '/',
			'HTTP_HOST' => 'sub.localhost',
		));

		$this->assertEquals($match['controller'], 'root');
		$this->assertEquals($match['action'], 'show_sub');
	}

	public function tearDown()
	{
		unset($this->router);
	}

}




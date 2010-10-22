<?php

set_include_path(implode(':', array(
	dirname(dirname(__FILE__)).'/lib',
	get_include_path()
)));

require_once 'PHPUnit/Framework.php';
require_once 'HTTP/Router.class.php';

class Test0001MultipleRoute extends PHPUnit_Framework_TestCase {

	public function setup()
	{
	}

	public function test_multiple_route_01()
	{
		$router = new \HTTP\Router();

		$router
			->connect('/', array('controller' => 'root', 'action' => 'show'), array('method' => 'GET', 'host' => 'localhost'))
			->connect('/blog/:year/{month}', array('controller' => 'blog', 'action' => 'monthly'), array('method' => 'GET', 'host' => 'localhost'))
			->connect('/blog/{year:[0-9]{1,4}}/{month:\d{2}}/{day:\d\d}', array('controller' => 'blog', 'action' => 'daily'), array('method' => 'GET'))
			->connect('/comment', array('controller' => 'comment', 'action' => 'create'), array('method' => 'POST'))
			->connect('/', array('controller' => 'root', 'action' => 'show_sub'), array('host' => 'sub.localhost'));

		$match01 = $router->match(array('REQUEST_METHOD' => 'GET', 'PATH_INFO' => '/', 'HTTP_HOST' => 'localhost',));
		$this->assertEquals($match01['controller'], 'root');
		$this->assertEquals($match01['action'], 'show');

		$match02 = $router->match(array('REQUEST_METHOD' => 'GET', 'PATH_INFO' => '/blog/2010/03', 'HTTP_HOST' => 'localhost',)); 
		$this->assertEquals($match02['controller'], 'blog');
		$this->assertEquals($match02['action'], 'monthly');
		$this->assertEquals($match02['year'], 2010);
		$this->assertEquals($match02['month'], '03');

		$match03 = $router->match(array('REQUEST_METHOD' => 'GET', 'PATH_INFO' => '/blog/2010/03/04',)); 
		$this->assertEquals($match03['controller'], 'blog');
		$this->assertEquals($match03['action'], 'daily');
		$this->assertEquals($match03['year'], 2010);
		$this->assertEquals($match03['month'], '03');
		$this->assertEquals($match03['day'], '04');

		$match04 = $router->match(array('REQUEST_METHOD' => 'GET', 'PATH_INFO' => '/comment', 'HTTP_HOST' => 'localhost',)); 
		$this->assertEquals($match04['controller'], 'comment');
		$this->assertEquals($match04['action'], 'create');

		$match05 = $router->match(array('PATH_INFO' => '/', 'HTTP_HOST' => 'sub.localhost',)); 
		$this->assertEquals($match05['controller'], 'root');
		$this->assertEquals($match05['action'], 'show_sub');

	}

	public function tearDown()
	{
	}

}




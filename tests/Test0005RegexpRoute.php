<?php

set_include_path(implode(':', array(
	dirname(dirname(__FILE__)).'/lib',
	get_include_path()
)));

require_once 'PHPUnit/Framework.php';
require_once 'HTTP/Router.class.php';

class Test0005RegexpRoute extends PHPUnit_Framework_TestCase {

	public function setup()
	{
	}

	public function test_regexp_route_01()
	{
		$router = new \HTTP\Router();

		$router
			->connect('/', array('controller' => 'root', 'action' => 'show'), array('method' => 'GET', 'host' => 'localhost'))
			->connect('@^/blog/(?<year>\d+)/(?<month>\d+)$@', array('controller' => 'blog', 'action' => 'monthly'), array('method' => 'GET', 'host' => 'localhost'))
			->connect('@^/blog/(?<year>[0-9]{1,4})/(?<month>\d{2})/(?<day>\d\d)$@', array('controller' => 'blog', 'action' => 'daily'), array('method' => 'GET'));

		$match01 = $router->match(array('REQUEST_METHOD' => 'GET', 'PATH_INFO' => '/', 'HTTP_HOST' => 'localhost',));
		$this->assertEquals($match01['controller'], 'root');
		$this->assertEquals($match01['action'], 'show');

		$match02 = $router->match(array('REQUEST_METHOD' => 'GET', 'PATH_INFO' => '/blog/2010/03', 'HTTP_HOST' => 'localhost',)); 
		$this->assertEquals($match02['controller'], 'blog');
		$this->assertEquals($match02['action'], 'monthly');
		$this->assertEquals($match02['splat']['year'], 2010);
		$this->assertEquals($match02['splat']['month'], '03');

		$match03 = $router->match(array('REQUEST_METHOD' => 'GET', 'PATH_INFO' => '/blog/2010/03/04',)); 
		$this->assertEquals($match03['controller'], 'blog');
		$this->assertEquals($match03['action'], 'daily');
		$this->assertEquals($match03['splat']['year'], 2010);
		$this->assertEquals($match03['splat']['month'], '03');
		$this->assertEquals($match03['splat']['day'], '04');
	}

	public function tearDown()
	{
	}

}




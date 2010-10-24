<?php

set_include_path(implode(':', array(
	dirname(dirname(__FILE__)).'/lib',
	get_include_path()
)));

require_once 'PHPUnit/Framework.php';
require_once 'HTTP/Router.class.php';

class Test0006RouteSplat extends PHPUnit_Framework_TestCase {

	public function setup()
	{
	}

	public function test_route_splat_01()
	{
		$router = new \HTTP\Router();

		$router
			->connect('/say/*/to/*', array('controller' => 'person', 'action' => 'say'))
			->connect('/download/*.*', array('controller' => 'file', 'action' => 'download'));

		$match01 = $router->match(array('PATH_INFO' => '/say/foo/to/bar',));
		$this->assertEquals($match01['controller'], 'person');
		$this->assertEquals($match01['action'], 'say');
		$this->assertEquals($match01['splat'][0], 'foo');
		$this->assertEquals($match01['splat'][1], 'bar');

		$match02 = $router->match(array('PATH_INFO' => '/download/path/to/file.ext',));
		$this->assertEquals($match02['controller'], 'file');
		$this->assertEquals($match02['action'], 'download');
		$this->assertEquals($match02['splat'][0], 'path/to/file');
		$this->assertEquals($match02['splat'][1], 'ext');
	}

	public function tearDown()
	{
	}

}




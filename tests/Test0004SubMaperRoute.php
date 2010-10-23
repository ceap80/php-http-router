<?php

set_include_path(implode(':', array(
	dirname(dirname(__FILE__)).'/lib',
	get_include_path()
)));

require_once 'PHPUnit/Framework.php';
require_once 'HTTP/Router.class.php';

class Test0004SubMapperRoute extends PHPUnit_Framework_TestCase {

	public function setup()
	{
	}

	public function test_submapper_route_01()
	{
		$router = new \HTTP\Router();

		$submapper = $router->getSubMapper('/entry/{id}', array('controller' => 'entry'));
		$submapper
			->connect('/edit', array('action' => 'edit'), array('method' => 'POST'))
			->connect('/show', array('action' => 'show'));

		$match01 = $router->match(array('PATH_INFO' => '/entry/2/edit', 'REQUEST_METHOD' => 'POST'));
		$this->assertEquals($match01['controller'], 'entry');
		$this->assertEquals($match01['id'], 2);
		$this->assertEquals($match01['action'], 'edit');

		$match02 = $router->match(array('PATH_INFO' => '/entry/2/show'));
		$this->assertEquals($match02['controller'], 'entry');
		$this->assertEquals($match02['id'], 2);
		$this->assertEquals($match02['action'], 'show');
	}

	public function tearDown()
	{
	}

}




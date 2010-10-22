<?php

set_include_path(implode(':', array(
	dirname(dirname(__FILE__)).'/lib',
	get_include_path()
)));

require_once 'PHPUnit/Framework.php';
require_once 'HTTP/Router.class.php';

class Test0003RouterDump extends PHPUnit_Framework_TestCase {

	public function setup()
	{
	}

	public function test_router_dump_01()
	{
		$router = new \HTTP\Router();

		$router
			->connect('/', array('controller' => 'root', 'action' => 'show'), array('method' => 'GET', 'host' => 'localhost'))
			->connect('/blog/:year/{month}', array('controller' => 'blog', 'action' => 'monthly'), array('method' => 'GET', 'host' => 'localhost'))
			->connect('/blog/{year:[0-9]{1,4}}/{month:\d{2}}/{day:\d\d}', array('controller' => 'blog', 'action' => 'daily'), array('method' => 'GET'))
			->connect('/comment', array('controller' => 'comment', 'action' => 'create'), array('method' => 'POST'))
			->connect('/', array('controller' => 'root', 'action' => 'show_sub'), array('host' => 'sub.localhost'));

		print "{$router}";

		$this->assertContains("\n", "{$router}");
	}

	public function test_router_dump_02()
	{
		$router = new \HTTP\Router();
		$this->assertEquals("{$router}", "There are no routes to connect.\n");
	}

	public function tearDown()
	{
	}

}




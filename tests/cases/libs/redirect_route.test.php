<?php
App::import('Lib', 'RouteEnhancements.RouteClasses');
class TestRedirectRoute extends RedirectRoute {
	public $headers = array();
	public $exited = false;
	protected function header($header) {
		$this->headers[] = $header;
	}
	protected function _stop() {
		$this->exited = true;
	}
}
class RedirectRouteTestCase extends CakeTestCase {
	private $_GET = array();

	public function startTest() {
		$this->_GET = $_GET;
	}

	public function testParsingWithoutRedirect() {
		$route = current(Router::connect('/beer/:action', array('controller' => 'drinks'), array('routeClass' => 'TestRedirectRoute')));

		$expectation = array('controller' => 'drinks', 'action' => 'show', 'named' => array(), 'pass' => array(), 'plugin' => null);
		$result = Router::parse('/drinks/show');

		$this->assertEqual($result, $expectation);
		$this->assertTrue(empty($route->headers));
		$this->assertFalse($route->exited);
	}

	public function testParsingWithRedirect() {
		$route = current(Router::connect('/beer/:action', array('controller' => 'drinks'), array('routeClass' => 'TestRedirectRoute')));
		$_GET = array();

		Router::parse('/beer/show');

		$expectation = array(
			'HTTP/1.1 301 Moved Permanently',
			'Location: /drinks/show'
		);

		$this->assertEqual($route->headers, $expectation);
		$this->assertTrue($route->exited);
	}

	public function testParsingWithTemporaryRedirect() {
		$route = current(Router::connect('/beer/:action', array('controller' => 'drinks'), array('routeClass' => 'TestRedirectRoute', 'permanent' => false)));
		$_GET = array();

		Router::parse('/beer/show');

		$expectation = array(
			'HTTP/1.1 302 Found',
			'Location: /drinks/show'
		);

		$this->assertEqual($route->headers, $expectation);
		$this->assertTrue($route->exited);
	}

	public function testRedirectParameters() {
		$route = current(Router::connect('/beer/:action/*', array('controller' => 'drinks'), array('routeClass' => 'TestRedirectRoute')));
		$_GET = array();

		Router::parse('/beer/show/54/324/34223/beer:yes');

		$expectation = array(
			'HTTP/1.1 301 Moved Permanently',
			'Location: /drinks/show/54/324/34223/beer:yes'
		);

		$this->assertEqual($route->headers, $expectation);
		$this->assertTrue($route->exited);
	}

	public function testPassingQueryString() {
		$route = current(Router::connect('/beer/:action/*', array('controller' => 'drinks'), array('routeClass' => 'TestRedirectRoute', 'pass' => array('type'))));
		$_GET = array('type' => 'water');

		Router::parse('/beer/show/54/324/34223/beer:yes?type=water');

		$expectation = array(
			'HTTP/1.1 301 Moved Permanently',
			'Location: /drinks/show/54/324/34223/water/beer:yes'
		);

		$this->assertEqual($route->headers, $expectation);
		$this->assertTrue($route->exited);
	}

	public function endTest() {
		$_GET = $this->_GET;
		Router::reload();
	}
}
?>
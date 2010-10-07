<?php
App::import('Lib', 'RouteEnhancements.RouteClasses');
class QueryStringParseRouteTestCase extends CakeTestCase {
	private $_GET = array();

	public function beginTest() {
		$this->_GET = $_GET;
	}

	public function testParsingWithGetParameters() {
		Router::connect('/file.php', array('controller' => 'pages', 'action' => 'display'), array('routeClass' => 'QueryStringParseRoute', 'pass' => array('page')));
		$expectation = array(
			'named' => array(
				'section' => 'details'
			),
			'pass' => array(
				'about'
			),
			'controller' => 'pages',
			'action' => 'display',
			'plugin' => null
		);
		$_GET = array(
			'url' => '/file.php?page=about&section=details',
			'page' => 'about',
			'section' => 'details'
		);
		$result = Router::parse('/file.php?page=about&section=details');
		$this->assertEqual($result, $expectation);
	}
	
	public function testParsingWithGetParametersAsNamed() {
		Router::connect('/file.php', array('controller' => 'pages', 'action' => 'display'), array('routeClass' => 'QueryStringParseRoute'));
		$expectation = array(
			'named' => array(
				'page' => 'about',
				'section' => 'details'
			),
			'pass' => array(
			),
			'controller' => 'pages',
			'action' => 'display',
			'plugin' => null
		);
		$_GET = array(
			'url' => '/file.php?page=about&section=details',
			'page' => 'about',
			'section' => 'details'
		);
		$result = Router::parse('/file.php?page=about&section=details');
		$this->assertEqual($result, $expectation);
	}

	public function testParsingWithGetParametersAsNamedWithAliases() {
		Router::connect('/file.php', array('controller' => 'pages', 'action' => 'display'), array('routeClass' => 'QueryStringParseRoute', 'aliases' => array('page' => 'chapter')));
		$expectation = array(
			'named' => array(
				'chapter' => 'about',
				'section' => 'details'
			),
			'pass' => array(
			),
			'controller' => 'pages',
			'action' => 'display',
			'plugin' => null
		);
		$_GET = array(
			'url' => '/file.php?page=about&section=details',
			'page' => 'about',
			'section' => 'details'
		);
		$result = Router::parse('/file.php?page=about&section=details');
		$this->assertEqual($result, $expectation);
	}

	public function testParsingWithGetParametersAsPass() {
		Router::connect('/file.php', array('controller' => 'pages', 'action' => 'display'), array('routeClass' => 'QueryStringParseRoute', 'pass' => array('section', 'page')));
		$expectation = array(
			'named' => array(
			),
			'pass' => array(
				'details',
				'about'
			),
			'controller' => 'pages',
			'action' => 'display',
			'plugin' => null
		);
		$_GET = array(
			'url' => '/file.php?page=about&section=details',
			'page' => 'about',
			'section' => 'details'
		);
		$result = Router::parse('/file.php?page=about&section=details');
		$this->assertEqual($result, $expectation);
	}
	
	public function testParsingWithGetParameterIgnored() {
		Router::connect('/file.php', array('controller' => 'pages', 'action' => 'display'), array('routeClass' => 'QueryStringParseRoute', 'ignore' => array('section')));
		$expectation = array(
			'named' => array(
				'page' => 'about',
			),
			'pass' => array(
			),
			'controller' => 'pages',
			'action' => 'display',
			'plugin' => null
		);
		$_GET = array(
			'url' => '/file.php?page=about&section=details',
			'page' => 'about',
			'section' => 'details'
		);
		$result = Router::parse('/file.php?page=about&section=details');
		$this->assertEqual($result, $expectation);
	}

	public function testParsingWithGetParametersAndConditions() {
		Router::connect('/file.php', array('controller' => 'pages', 'action' => 'display'), array('routeClass' => 'QueryStringParseRoute', 'page' => 'contact', 'pass' => array('page')));
		$expectation = array(
			'controller' => 'file.php',
			'named' => array(
			),
			'pass' => array(
			),
			'action' => 'index',
			'plugin' => null
		);
		$_GET = array(
			'url' => '/file.php?page=about&section=details',
			'page' => 'about',
			'section' => 'details'
		);
		$result = Router::parse('/file.php?page=about&section=details');
		$this->assertEqual($result, $expectation);
	}

	public function testParsingWithGetParametersAndRegexConditions() {
		Router::connect('/file.php', array('controller' => 'pages', 'action' => 'display'), array('routeClass' => 'QueryStringParseRoute', 'page' => 'contact|about', 'pass' => array('page')));
		$expectation = array(
			'named' => array(
				'section' => 'details'
			),
			'pass' => array(
				'about'
			),
			'controller' => 'pages',
			'action' => 'display',
			'plugin' => null
		);
		$_GET = array(
			'url' => '/file.php?page=about&section=details',
			'page' => 'about',
			'section' => 'details'
		);
		$result = Router::parse('/file.php?page=about&section=details');
		$this->assertEqual($result, $expectation);
	}

	public function testParsingWithGetParametersAndMoreConditions() {
		Router::connect('/file.php', array('controller' => 'pages', 'action' => 'display'), array('routeClass' => 'QueryStringParseRoute', 'page' => '[0-9]+', 'pass' => array('page')));
		$expectation = array(
			'named' => array(
			),
			'pass' => array(
				'14'
			),
			'controller' => 'pages',
			'action' => 'display',
			'plugin' => null
		);
		$_GET = array(
			'url' => '/file.php?page=14',
			'page' => '14',
		);
		$result = Router::parse('/file.php?page=14');
		$this->assertEqual($result, $expectation);
	}
	
	public function testParsingWithGetParametersAndConditionsOnNamedParameter() {
		Router::connect('/file.php', array('controller' => 'pages', 'action' => 'display'), array('routeClass' => 'QueryStringParseRoute', 'page' => '[0-9]+'));
		$expectation = array(
			'named' => array(
				'page' => '14'
			),
			'pass' => array(
			),
			'controller' => 'pages',
			'action' => 'display',
			'plugin' => null
		);
		$_GET = array(
			'url' => '/file.php?page=14',
			'page' => '14',
		);
		$result = Router::parse('/file.php?page=14');
		$this->assertEqual($result, $expectation);
	}

	public function testParsingWithGetParametersAndMoreConditionsOnNamedParameter() {
		Router::connect('/file.php', array('controller' => 'pages', 'action' => 'display'), array('routeClass' => 'QueryStringParseRoute', 'page' => '[0-9]+'));
		$expectation = array(
			'controller' => 'file.php',
			'named' => array(
			),
			'pass' => array(
			),
			'action' => 'index',
			'plugin' => null
		);
		$_GET = array(
			'url' => '/file.php?page=phally',
			'page' => 'phally',
		);
		$result = Router::parse('/file.php?page=phally');
		$this->assertEqual($result, $expectation);
	}

	public function testParsingGetRouting() {
		Router::connect('/file.php', array('controller' => 'pages', 'action' => ':page'), array('routeClass' => 'QueryStringParseRoute'));
		$expectation = array(
			'named' => array(
				'section' => 'details'
			),
			'pass' => array(
			),
			'controller' => 'pages',
			'action' => 'about',
			'plugin' => null
		);
		$_GET = array(
			'url' => '/file.php?page=about&section=details',
			'page' => 'about',
			'section' => 'details'
		);
		$result = Router::parse('/file.php?page=about&section=details');
		$this->assertEqual($result, $expectation);
	}

	public function testParsingGetRoutingWithMatchingConditions() {
		Router::connect('/file.php', array('controller' => 'pages', 'action' => ':page'), array('routeClass' => 'QueryStringParseRoute', 'page' => 'contact|about'));
		$expectation = array(
			'named' => array(
				'section' => 'details'
			),
			'pass' => array(
			),
			'controller' => 'pages',
			'action' => 'about',
			'plugin' => null
		);
		$_GET = array(
			'url' => '/file.php?page=about&section=details',
			'page' => 'about',
			'section' => 'details'
		);
		$result = Router::parse('/file.php?page=about&section=details');
		$this->assertEqual($result, $expectation);
	}

	public function testParsingGetRoutingWithFailingConditions() {
		Router::connect('/file.php', array('controller' => 'pages', 'action' => ':page'), array('routeClass' => 'QueryStringParseRoute', 'page' => 'contact|about'));
		$expectation = array(
			'controller' => 'file.php',
			'named' => array(
			),
			'pass' => array(
			),
			'action' => 'index',
			'plugin' => null
		);
		$_GET = array(
			'url' => '/file.php?page=portfolio&section=details',
			'page' => 'portfolio',
			'section' => 'details'
		);
		$result = Router::parse('/file.php?page=portfolio&section=details');
		$this->assertEqual($result, $expectation);
	}

	public function testParsingGetRoutingWithMissingGetParameter() {
		Router::connect('/file.php', array('controller' => 'pages', 'action' => ':page'), array('routeClass' => 'QueryStringParseRoute'));
		$expectation = array(
			'controller' => 'file.php',
			'named' => array(
			),
			'pass' => array(
			),
			'action' => 'index',
			'plugin' => null
		);
		$_GET = array(
			'url' => '/file.php?section=details',
			'section' => 'details'
		);
		$result = Router::parse('/file.php?section=details');
		$this->assertEqual($result, $expectation);
	}

	public function testAdvancedParsingGetRouting() {
		Router::connect('/file.php', array('controller' => 'pages', 'action' => ':page{/about\.([a-z]+)/}'), array('routeClass' => 'QueryStringParseRoute'));
		$expectation = array(
			'named' => array(
			),
			'pass' => array(
			),
			'controller' => 'pages',
			'action' => 'details',
			'plugin' => null
		);
		$_GET = array(
			'url' => '/file.php?page=about.details',
			'page' => 'about.details'
		);
		$result = Router::parse('/file.php?page=about.details');
		$this->assertEqual($result, $expectation);
	}

	public function testAdvancedParsingGetRoutingWithFailingFilter() {
		Router::connect('/file.php', array('controller' => 'pages', 'action' => ':page{/about\.([0-9]+)/}'), array('routeClass' => 'QueryStringParseRoute'));
		$expectation = array(
			'controller' => 'file.php',
			'named' => array(
			),
			'pass' => array(
			),
			'action' => 'index',
			'plugin' => null
		);
		$_GET = array(
			'url' => '/file.php?page=about.details',
			'page' => 'about.details'
		);
		$result = Router::parse('/file.php?page=about.details');
		$this->assertEqual($result, $expectation);
	}

	public function testExtendedAdvancedParsingGetRouting() {
		Router::connect('/file.php', array('controller' => 'pages', 'action' => ':page{/about\.([a-z]+)\.contact/}'), array('routeClass' => 'QueryStringParseRoute'));
		$expectation = array(
			'named' => array(
			),
			'pass' => array(
			),
			'controller' => 'pages',
			'action' => 'details',
			'plugin' => null
		);
		$_GET = array(
			'url' => '/file.php?page=about.details.contact',
			'page' => 'about.details.contact'
		);
		$result = Router::parse('/file.php?page=about.details.contact');
		$this->assertEqual($result, $expectation);
	}

	public function testExtendedAdvancedParsingGetRoutingWithMultipleMatches() {
		Router::connect('/file.php', array('controller' => 'pages', 'action' => ':page{/[a-z]+\.([a-z]+)\.[a-z]+/}'), array('routeClass' => 'QueryStringParseRoute'));
		$expectation = array(
			'named' => array(
			),
			'pass' => array(
			),
			'controller' => 'pages',
			'action' => 'details',
			'plugin' => null
		);
		$_GET = array(
			'url' => '/file.php?page=about.details.contact',
			'page' => 'about.details.contact'
		);
		$result = Router::parse('/file.php?page=about.details.contact');
		$this->assertEqual($result, $expectation);
	}

	public function testExtendedAdvancedParsingGetRoutingWithMultipleMatchesAndSingleParameters() {
		Router::connect('/file.php', array('controller' => ':page{/([a-z]+)\.[a-z]+/}', 'action' => ':page{/[a-z]+\.([a-z]+)/}'), array('routeClass' => 'QueryStringParseRoute'));
		$expectation = array(
			'named' => array(
			),
			'pass' => array(
			),
			'controller' => 'about',
			'action' => 'details',
			'plugin' => null
		);
		$_GET = array(
			'url' => '/file.php?page=about.details',
			'page' => 'about.details'
		);
		$result = Router::parse('/file.php?page=about.details');
		$this->assertEqual($result, $expectation);
	}

	public function testExtendedAdvancedParsingGetRoutingWithMultipleMatchesAndMultipleParameters() {
		Router::connect('/file.php', array('controller' => ':background{/color\-([a-z]+)/}', 'action' => ':page{/[a-z]+\.([a-z]+)\.[a-z]+/}'), array('routeClass' => 'QueryStringParseRoute'));
		$expectation = array(
			'named' => array(
			),
			'pass' => array(
			),
			'controller' => 'green',
			'action' => 'details',
			'plugin' => null
		);
		$_GET = array(
			'url' => '/file.php?page=about.details.contact&background=color-green',
			'page' => 'about.details.contact',
			'background' => 'color-green'
		);
		$result = Router::parse('/file.php?page=about.details.contact&background=color-green');
		$this->assertEqual($result, $expectation);
	}

	public function testLengthParsingGetRoutingWithMultipleMatches() {
		Router::connect('/file.php', array('controller' => 'pages', 'action' => ':page{/[a-z]{5}\.([a-z]{7})\.[a-z]{7}/}'), array('routeClass' => 'QueryStringParseRoute'));
		$expectation = array(
			'named' => array(
			),
			'pass' => array(
			),
			'controller' => 'pages',
			'action' => 'details',
			'plugin' => null
		);
		$_GET = array(
			'url' => '/file.php?page=about.details.contact',
			'page' => 'about.details.contact'
		);
		$result = Router::parse('/file.php?page=about.details.contact');
		$this->assertEqual($result, $expectation);
	}

	public function endTest() {
		$_GET = $this->_GET;
		Router::reload();
	}
}
?>
<?php
/**
 * These classes implement querystring parsing and route redirecting for CakePHP 1.3.
 *
 * Great when dealing with legacy urls!
 *
 * Installation is done like any other custom route class. Clone this plugin to your
 * plugins directory in your application (or clone as a git submodule). Then import
 * this file in your APP/config/routes.php like this:
 *
 * <?php
 * App::import('Lib', 'RouteEnhancements.RouteClasses');
 * ?>
 *
 * Inspiration from:
 * - http://cakephp.lighthouseapp.com/projects/42648/tickets/1151-redirection-in-routesphp
 *
 * @author Frank de Graaf (Phally)
 * @link http://www.frankdegraaf.net/
 * @link http://github.com/phally/route_enhancements/wiki
 * @license MIT
 */
class QueryStringParseRoute extends CakeRoute {
	/**
	 * Parses GET parameters.
	 *
	 * @param string $url The called url.
	 * @return mixed False if the GET parameters weren't matched else an array of parameters.
	 * @access public
	 */
	public function parse($url) {
		if ($params = parent::parse($url)) {
			if (isset($_GET)) {

				// Merge in the defaults:
				$this->options += array(
					'aliases' => array(),
					'ignore' => array(),
					'pass' => array()
				);

				// GET routing:
				foreach($this->defaults as $default => $value) {
					if (strpos($value, ':') == 0 && isset($_GET[$value = substr($value, 1)])) {
						if ($this->fails($value, $_GET[$value])) {
							return false;
						}
						$params[$default] = $_GET[$value];
						$this->options['ignore'][] = $value;
					}
				}

				// Ignore list:
				$get = array_diff_key($_GET, array_flip(array_merge($this->options['ignore'], array('url'))));

				// Pass parameters:
				foreach($this->options['pass'] as $param) {
					if (isset($get[$param]) && !$this->fails($param, $get[$param])) {
						$params['pass'][] = $get[$param];
						unset($get[$param]);
					} else {
						return false;
					}
				}

				// Named parameters:
				foreach ($get as $param => $value) {
					if ($this->fails($param, $value)) {
						return false;
					}
					$param = isset($this->options['aliases'][$param]) ? $this->options['aliases'][$param] : $param;
					$params['named'][$param] = $value;
				}
			}
			return $params;
		}
		return false;
	}

	/**
	 * Overrides CakeRoute::match() so these ugly urls will never reach the app.
	 *
	 * @return boolean Always false.
	 * @access public
	 */
	public function match() {
		return false;
	}

	/**
	 * Checks if a condition fails on a certain parameter.
	 *
	 * @param string $param Name of the parameter to check.
	 * @param string $value Value of the parameter to check.
	 * @return boolean True if the condition fails, else false.
	 * @access protected
	 */
	protected function fails($param, $value) {
		return isset($this->options[$param]) && !preg_match('/' . $this->options[$param] . '/', $value);
	}
}

class RedirectRoute extends QueryStringParseRoute {

	/**
	 * Checks if the routes needs to be redirected and redirects.
	 *
	 * @param string $url The called url.
	 * @return boolean Only false if there is no redirect.
	 * @access public
	 */
	public function parse($url) {
		if ($params = parent::parse($url)) {
			$this->options += array(
				'permanent' => true,
			);
			$this->redirect($params);
		}
		return false;
	}

	/**
	 * Redirects to a certain location based on the resulting params.
	 *
	 * @param array $params An array with parameters which result from CakeRoute::parse().
	 * @return void
	 * @access protected
	 */
	protected function redirect($params) {
		$url = Router::reverse($this->prepare($params));
		if ($this->options['permanent']) {
			$code = 301;
			$status = 'Moved Permanently';
		} else {
			$code = 302;
			$status = 'Found';
		}
		$this->header('HTTP/1.1 ' . $code . ' ' . $status);
		$this->header('Location: ' . $url);
		$this->_stop();
	}

	/**
	 * Converts params from CakeRoute::parse() to an array url.
	 *
	 * @param array $params Array of parameters from CakeRoute::parse().
	 * @return array Converted array url.
	 * @access protected
	 */
	protected function prepare($params) {
		if (isset($params['_args_'])) {
			$params = array_merge_recursive(Router::getArgs($params['_args_']), $params);
			unset($params['_args_']);
		}
		return $params + array('url' => array());
	}

	/**
	 * Simple wrapper to override for test purposes.
	 *
	 * @param string $header The header to send.
	 * @return void
	 * @access protected
	 */
	protected function header($header) {
		header($header);
	}
}
?>
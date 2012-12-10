<?php
if (! defined ( 'cw_inc' )) {
	echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
	exit ( 1 );
}
/**
 * CobWeb Routing Class:
 * Uses Routes stored in /cw.routes.php to determine which Controller and Action is called
 * @todo Blacklisting
 * @todo Route Caching
 * @todo Route Settings (e.g. Redirect)
 */
class CobWeb_Router {
	private $routes = array ();
	/**
	 * @return unknown
	 */
	public function getRoutes() {
		return $this->routes;
	}
	/**
	 * @param unknown_type $routes
	 */
	public function setRoutes($routes) {
		$this->routes = $routes;
	}
	public function connect($route, $array_controller) {
		if (array_key_exists ( 'controller', $array_controller ) != true) {
			$array_controller ['controller'] = false;
		}
		if (array_key_exists ( 'action', $array_controller ) != true) {
			$array_controller ['action'] = false;
		}
		if ($route && strpos ( $route, '/' ) !== 0) {
			$route = '/' . $route;
		}
		if (strpos ( $route, '?' ) !== false) {
			$route = substr ( $route, 0, strpos ( $route, '?' ) );
		}
		$this->routes [$route] = $array_controller;
	}
	/**
	 * Sanatizes the $uri Route to be processible by further Routing Functions
	 *
	 * @param string $uri
	 * @return string 
	 */
	private function sanatizeRoute($uri) {
		if ($uri && strpos ( $uri, '/' ) != false) {
			$uri = '/' . $uri;
		}
		if (substr ( $uri, - 1 ) != '/') {
			$uri = $uri . '/';
		}
		if (strpos ( $uri, '?' ) != false) {
			$uri = substr ( $uri, 0, strpos ( $uri, '?' ) );
		}
		while ( strpos ( $uri, '//' ) !== false ) {
			$uri = str_replace ( '//', '/', $uri );
		}
		return $uri;
	}
	/**
	 * Gets the Routing of a URI:
	 *
	 * @param string $req_uri
	 * @param array $additional_params
	 * @return array
	 */
	public function getRouting($req_uri, $additional_params = null) {
		$req_uri = $this->sanatizeRoute ( $req_uri );
		$lastmatch = false;
		//Before we parse, lets check whether we have a static Route:
		if (array_key_exists ( $req_uri, $this->routes )) {
			return array ('route' => $req_uri, 'params' => ($this->routes [$req_uri] ['params'])?$this->routes [$req_uri] ['params']:array(), 'controller' => $this->routes [$req_uri] ['controller'], 'action' => $this->routes [$req_uri] ['action'], 'static' => true );
		} else {
			//Parse req_uri:
			$uri_chunks = explode ( '/', $req_uri );
			foreach ( array_reverse ( $this->routes ) as $route => $settings ) {
				$route_chunks = explode ( '/', $route );
				$cp = - 1;
				$not_found = false;
				$controller = null;
				$action = null;
				if (count ( $uri_chunks ) != count ( $route_chunks ) && strpos ( $route, '*' ) === false) {
					continue;
				} else {
					$chunks = count ( $route_chunks ) - 1;
					foreach ( $route_chunks as $pattern ) {
						$cp ++;
						if (strpos ( $pattern, '##controller##' ) !== false || strpos ( $pattern, '##action##' ) !== false || strpos ( $pattern, '*' ) !== false) {
							if ($cp == $chunks && count ( $uri_chunks ) != count ( $route_chunks )) { //Last Chunk; n!=p
								$lastchar = substr ( $pattern, - 1 );
								if ($lastchar != '*') {
									//Length mismatch
									$not_found = true;
									break;
								} else {
									$params = array_slice ( $uri_chunks, count ( $route_chunks ) - 1, (count ( $uri_chunks ) - count ( $route_chunks )) );
									break;
								}
							}
							$pattern = preg_quote ( $pattern );
							$pattern = str_replace ( '\*', '*', $pattern );
							if (strpos ( $pattern, '##controller##' ) !== false) {
								//Check whether the Routechunk has a specified controller or not:
								if ($settings ['controller'] != false) {
									$controller = $settings ['controller'];
								} else {
									//We need to match the Controller via Regex:
									$c_pattern = '/' . str_replace ( '##controller##', '(.+)', $pattern ) . '/';
									$c_match = array ();
									if (preg_match ( $c_pattern, $uri_chunks [$cp], $c_match )) {
										$controller = $c_match [1];
									} else {
										$not_found = true;
										break;
									}
								}
							}
							if (strpos ( $pattern, '##action##' ) !== false) {
								//Check whether the Routechunk has a specified action or not:
								if ($settings ['action'] != false) {
									$controller = $settings ['action'];
								} else {
									//We need to match the Action via Regex:
									$a_pattern = '/' . str_replace ( '##action##', '(.+)', $pattern ) . '/';
									$a_match = array ();
									if (preg_match ( $a_pattern, $uri_chunks [$cp], $a_match )) {
										$action = $a_match [1];
									} else {
										$not_found = true;
										break;
									}
								}
							}
							$w_pattern = $pattern;
							$w_pattern = '/' . str_replace ( array ('##controller##', '##action##', '*' ), array (preg_quote ( $controller ), preg_quote ( $action ), '(.*)' ), $w_pattern ) . '/';
							if (preg_match ( $w_pattern, $uri_chunks [$cp] )) {
								continue;
							} else {
								$not_found = true;
								break;
							}
						} else {
							if ($uri_chunks [$cp] == $pattern) {
								continue;
							} else {
								$not_found = true;
								break;
							}
						}
					}
					if ($not_found) {
						continue;
					} else {
						$lastmatch = $settings;
						if ($controller != '') {
							$lastmatch ['controller'] = $controller;
						}
						if ($action != '') {
							$lastmatch ['action'] = $action;
						}
						if (! is_array ( $additional_params )) {
							$additional_params = array ();
						}
						if (! isset ( $params )) {
							$params = null;
						}
						if (! is_array ( $params )) {
							$params = array ();
						}
						$lastmatch ['route'] = $route;
						$lastmatch ['static'] = false;
						$lastmatch ['params'] = array_merge ( $params, $additional_params );
					}
				}
			}
		}
		if (! is_array ( $lastmatch ))
			return false;
		else
			return $lastmatch;
	}
	/**
	 * Shortcut Alias for CobWeb_Router::createRoute([...]);
	 *  
	 * @param unknown_type $arrayRoute
	 */
	public function createRouteByArray($arrayRoute){
	    if(!isset($arrayRoute['action'])) $arrayRoute['action'] = null;
	    if(!isset($arrayRoute['params'])) $arrayRoute['params'] = null;
	    return $this->createRoute($arrayRoute['controller'],$arrayRoute['action'],$arrayRoute['params']);
	}
	
	/**
	 * Creates A Route to the given Parameters on the Basis of the declared Routes
	 *
	 * @param unknown_type $controller
	 * @param unknown_type $action
	 * @param unknown_type $params
	 */
	public function createRoute($controller, $action = null, $params = null) {
		//First of lets filter the Array for our Controller / Action
		$routes = array ();
		foreach ( $this->routes as $route => $settings ) {
			//Check the Route for occurrence of ##controller## 
			if (strpos ( $route, '##controller##' ) !== false) {
				if ($action !== null) {
				    if (strpos ( $route, '##action##' ) !== false) {
						if ($params !== null) {
							if (strpos ( $route, '*' ) !== false) {
								$routes [] = $route;
								continue; //Its obviously not static so pass that
							}
						} else {
							$routes [] = $route;
							continue; //Its obviously not static so pass that
						}
					}
				} else {
					$routes [] = $route;
					continue; //Its obviously not static so pass that
				}
			}
			//Do we have a static Route?
			if (is_array ( $settings ))
				if (array_key_exists ( 'controller', $settings )) {
					if ($settings ['controller'] == $controller) {
						if ($action != null && array_key_exists ( 'action', $settings ) == true) {
							if ($settings ['action'] == $action) {
								$routes [] = $route;
							}
						} else {
							$routes [] = $route;
						}
					}
				}
		
		}
		//$routes now contains an array of routes to the controller
		if ($action !== null)
			foreach ( $routes as $route ) {
				if (! (strpos ( $route, '##action##' ) !== false || (isset ( $this->routes [$route] ['action'] ) == true && $this->routes [$route] ['action'] == $action))) {
					unset ( $routes [$route] );
				}
			}
		reset ( $routes );
		//Create Param Appendix:
		$appendix = '';
		if (is_array ( $params ))
			foreach ( $params as $identifier => $value ) {
				if(!is_numeric($identifier)){
					$appendix .= urlencode ( $identifier ) . '/' . urlencode ( $value ).'/';
				}else{
					$appendix .= urlencode ( $value ).'/';
				}
				
			}
		if (count ( $routes ) >= 1) {
			reset ( $routes );
			$bestRoute = current ( $routes );
			foreach ( $routes as $key => $route ) {
				if (strlen ( $routes [$key] ) < strlen ( $bestRoute )) {
					if ($action != null) {
						if (strpos ( $routes [$key], '##action##' ) === false) {
							continue;
						}
					}
					if ($params != null) {
						if (strpos ( $routes [$key], '*' ) === false) {
							continue;
						}
					}
					$bestRoute = $routes [$key];
				}
			}

			$bestRoute = str_replace ( '##controller##', $controller, $bestRoute );
			$bestRoute = str_replace ( '##action##', $action, $bestRoute );
			$bestRoute = str_replace ( '*', $appendix, $bestRoute );
			$bestRoute = cw_webroot.$bestRoute;
			$bestRoute = $this->sanatizeRoute ( $bestRoute );
			
			return $bestRoute;
		} else {
			return false;
		}
	}
	
	public function getSelfLink(){
	    return CobWeb_Router::createRouteByArray(CobWeb::getSetting('CurrentRoute'));
	}
	
	public function getCustomRoute($params,$route = false){
	    if($route == false){
            $newRoute = CobWeb::getSetting('CurrentRoute');
	    }else{
	        $newRoute = $route;
	    }
	    
	    if(is_array($params)){
	        foreach($params as $identifier => $value){
	            $newRoute['params'][$identifier] = $value;
	        }
	    }
	    return CobWeb_Router::createRouteByArray($newRoute);
	}
}
?>
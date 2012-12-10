<?php
if (! defined ( 'cw_inc' )) {
	echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
	exit ( 1 );
}
/**
 * CobWeb URL Dispatcher:
 * Everything in our Application is concetrated in the index.php file, which redirects the request to this
 * Dispatcher, which uses CW_Router to select the appropriate Controller,Action and Params:
 * 
 **/
class CobWeb_Dispatcher {
	public function __construct() {
		CobWeb::checkDep ( 'Router' );
	}
	public function dispatchRequest($requested_route = false) {
		global $cw;
		if ($requested_route == false) {
			$requested_route = str_replace ( cw_webroot, '', $_SERVER ['REQUEST_URI'] );
		}
		$Route = $cw->Router->getRouting ( $requested_route );
		if ($Route !== false) {
			//Extract Params
			$numParams = count ( $Route ['params'] );
			if ($numParams >= 1) {
				if (($numParams % 2) == 0) {
					$keys = $values = array ();
					for($i = 0; $i < $numParams; $i ++) {
						if ($i % 2 != 0) {
							$values [$i] = $Route ['params'] [$i];
						} else {
							$keys [$i] = $Route ['params'] [$i];
							
						}
					}
					$Params = array_combine ( $keys, $values );
				} elseif ($numParams == 1) {
					$Params = array ('single' => $Route ['params'] [0] );
				} else {
					foreach ( $Route ['params'] as $value ) {
						$Params [] = $value;
					}
				}
			
			} else {
				$Params = null;
			}
			$Route ['params'] = $Params;
			return $Route;
		} else {
			return false;
		}
	}
	private static function getControllerFile($controllerName) {
		return cw_controller_dir . strtolower ( $controllerName ) . '/' . strtolower ( $controllerName ) . '.controller.' . cw_phpex;
	}
	public static function checkController($controllerName, $action = null) {
		//Check whether the Controller File exists:
		$controllerFile = self::getControllerFile ( $controllerName );
		if (file_exists ( $controllerFile )) {
			require_once $controllerFile;
			if (in_array ( strtolower ( $controllerName ) . 'Controller', get_declared_classes () )) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	public function loadController($controllerName, $action = null, $params = null) {
		//Load Controller File:
		$controllerFile = $this->getControllerFile ( $controllerName );
		if ($this->checkController ( $controllerName, $action )) {
			$controllerClassName = $controllerName . 'Controller';
			$Controller = new $controllerClassName ( );
			if ($action != null && $action != false) {
				$Controller->setRequestAction ( $action );
			}
			if ($params != null && $params != false) {
				$Controller->setParams ( $params );
			}
			$Controller->doAction ();
			return $Controller;
		} else {
			CobWeb::o ( 'Console' )->warning ( 'Unable to Load Controller: "' . $controllerName . '"' );
			if (! class_exists ( 'errorController', false )) {
				require_once cw_controller_dir . 'error/error.controller.' . cw_phpex;
			}
			$errorController = new errorController ( );
			$errorController->setRequestAction ( 'missingController' );
			$errorController->doAction ();
			return false;
		}
	}
	public static function redirect($route){
	    header('LOCATION: http://'.cw_urlroot.$route);
	}
}
?>
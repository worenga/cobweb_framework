<?php
/**
 * ************************************************************************************************
 *   _____      _ __          __  _         ______     RAPID WEB APPLICATION FRAMEWORK       _    
 *  / ____|    | |\ \        / / | |       |  ____|                                         | |   
 * | |     ___ | |_\ \  /\  / /__| |__     | |__ _ __ __ _ _ __ ___   _____      _____  _ __| | __
 * | |    / _ \| '_ \ \/  \/ / _ \ '_ \    |  __| '__/ _` | '_ ` _ \ / _ \ \ /\ / / _ \| '__| |/ /
 * | |___| (_) | |_) \  /\  /  __/ |_) |   | |  | | | (_| | | | | | |  __/\ V  V / (_) | |  |   < 
 *  \_____\___/|_.__/ \/  \/ \___|_.__/    |_|  |_|  \__,_|_| |_| |_|\___| \_/\_/ \___/|_|  |_|\_\
 * ************************************************************************************************ 
 * Filename: cobweb.formelement.abstract.php
 * Author: Benedikt Wolters (mightyuhu@php.net)
 * License:  MIT License
 * Version: $id;
 * ************************************************************************************************
 * Description:
 * 
 * ************************************************************************************************
 */
if (! defined('cw_inc')) {
    echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
    exit(1);
}
abstract class CobWeb_FormElement {
	protected $name = null;
	protected $validators = array ();
	protected $label = null;
	protected $jsValidation = null;
	protected $required = false;
	protected $settings;
	protected $failedChecks = null;
	protected $defaultValidationError = false;
	protected $validateWith = null;
	protected $validationResult = null;
	protected $lastValidated = null;
	protected $canContainDefault = true;
	protected $errorMessages = array ();

	public function hasErros() {
		if (count ( $this->errorMessages ) > 0) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * @param $defaultValidationError the $defaultValidationError to set
	 */
	public function setDefaultValidationError($defaultValidationError) {
		$this->defaultValidationError = $defaultValidationError;
	}

	/**
	 * @return the $defaultValidationError
	 */
	public function getDefaultValidationError() {
		return $this->defaultValidationError;
	}

	public function getErrorMessages() {
		return $this->errorMessages;
	}
	public function getCanContainDefault() {
		return $this->canContainDefault;
	}
	public function setCanContainDefault($canContainDefault) {
		$this->canContainDefault = $canContainDefault;
	}
	/**
	 * @return unknown
	 */
	public function getLabel() {
		return $this->label;
	}
	/**
	 * @return unknown
	 */
	public function getName() {
		return $this->name;
	}
	/**
	 * @return unknown
	 */
	public function getRequired() {
		return $this->required;
	}
	/**
	 * @return unknown
	 */
	public function getSettings() {
		return $this->settings;
	}
	/**
	 * @param unknown_type $label
	 */
	public function setLabel($label) {
		$this->label = $label;
	}
	/**
	 * @param unknown_type $name
	 */
	public function setName($name) {
		$this->name = $name;
	}
	/**
	 * @param unknown_type $settings
	 */
	public function setSettings($settings) {
		$this->settings = $settings;
	}
	function __construct($name, $settings = false) {
		$this->name = $name;
		$this->settings = $settings;
	}
	public function setRequired($bool) {
		$this->required = $bool;
		if ($bool == TRUE) {
			$this->addValidator ( 'isEmpty' ,array('breakOnError' => true) );
		}
	}
	public function addLabel($string) {
		$this->label = $string;
	}
	public abstract function isDefault($mixed); //to be implemented
	public abstract function setDefault($mixed);
	public abstract function setJsValidation($bool);
	public abstract function setAjaxControl($bool);
	public abstract function render($validation = false);
	public function __destruct() {}
	public function validate($validateWith) {
		CobWeb::checkDep ( 'Validator' );
		if (! isset ( $validateWith [$this->getName ()] ))
			$validateWith [$this->getName ()] = null;
		$this->lastValidated = $validateWith [$this->getName ()];
		if ($this->required == false) { //If not required lets check whether the user has used this field or not
			if (CobWeb::o ( 'Validator' )->isEmpty ( $validateWith [$this->getName ()] )) {
				return true;
			}
		}
		$failedChecks = array ();
		$errorOccurred = false;
		$this->validateWith = $validateWith;
		if ($this->canContainDefault == false) {
			if ($this->isDefault ( $this->lastValidated )) {
				$failedChecks [] = 'containsDefault';
				$errorOccurred = true;
			}
		}
		foreach ( $this->validators as $validatorName => $validatorSettings ) {
			$validatorError = null;
			if (isset ( $validatorSettings ['onError'] )) {
				$validatorError = $validatorSettings ['onError'];
				unset ( $validatorSettings ['onError'] );
			} else {
			    if($this->defaultValidationError == false){
				$validatorError = CobWeb::o('Translator')->forms_defaultValidationError;
			    }else{
			        $validatorError = $this->defaultValidationError;
			    }
			}
			if (isset ( $validatorSettings ['breakOnError'] )) {
				$breakOnError = $validatorSettings ['breakOnError'];
				unset ( $validatorSettings ['breakOnError'] );
			} else {
				$breakOnError = false;
			}
			if (is_array ( $validatorSettings )) {
				$params = array_merge ( array ($validateWith [$this->getName ()] ), $validatorSettings );
			} else {
				$params = array ($validateWith [$this->getName ()] );
			}
			if (method_exists ( $this, $validatorName )) {
				$validationResult = call_user_func_array ( array ($this, $validatorName ), $params );
			} elseif (method_exists ( CobWeb::o ( 'Validator' ), $validatorName )) {
				$validationResult = call_user_func_array ( array (CobWeb::o ( 'Validator' ), $validatorName ), $params );
			} elseif (function_exists ( $validatorName )) {
				$validationResult = call_user_func_array ( $validatorName, $params );
			} else {
				$validationResult = false;
				CobWeb::o ( 'Console' )->warning ( 'CobWeb_FormElement##validate: Cannot find Validation Method: ' . $validatorName . '!' );
			}
			if ($validationResult == false) {
				$failedChecks [] = $validatorName;
				$errorOccurred = true;
				$this->errorMessages [] = $validatorError;
				if ($breakOnError == true) {
					break;
				}
			}
		}
		$this->validationResult = $errorOccurred;
		$this->failedChecks = $failedChecks;
		if ($errorOccurred == false) {
			return true;
		} else {
			return $failedChecks;
		}
	}
	/**
	 * Standard Settings:
	 * onError - Message if Validator fails
	 * breakOnError true|false - Breaks the Validation Chain if this Validator fails
	 * @param unknown_type $validator
	 * @param unknown_type $settings
	 */
	public function addValidator($validator, $settings = null) {
		$this->validators [$validator] = $settings;
	}
	public function renderErrorMessage($context = true) {
		if ($this->hasErros ()) {
			if ($context) {
				$DivWrapper = new CobWeb_NodeBuilder ( 'div' );
				$DivWrapper->addClass ( 'cw_validation_error' );
				$UlWrapper = new CobWeb_NodeBuilder ( 'ul' );
				$UlWrapper->addClass('cw_validatation_error_list');
			}
			foreach ( $this->errorMessages as $errorMessage ) {
				$Li = new CobWeb_NodeBuilder ( 'li' );
				$Li->addContent ( $errorMessage );
				if ($context) {
					$UlWrapper->addChild ( $Li );
				} else {
					$out .= $Li->render ();
				}
			}
			if ($context) {
				$DivWrapper->addChild ( $UlWrapper );
				$out = $DivWrapper->render ();
			}
			return $out;
		} else {
			return null;
		}
	}
	protected function getWrapperElement() {
		$Wrapper = new CobWeb_NodeBuilder ( 'div' );
		$Wrapper->addClass ( 'cw_form_element_wrapper' );
		if (isset ( $this->settings ['wrapperclass'] )) {
			$Wrapper->addClass ( $this->settings ['wrapperclass'] );
		}
		if ($this->validationResult != true) {
			//Add Error Class
			$Wrapper->addClass ( 'cw_field_error' );
		}
		
		if (isset ( $this->settings ['wrapperid'] )) {
        	$Wrapper->setId($this->settings['wrapperid']);
			;
		}
		return $Wrapper;
	}
} 
?>
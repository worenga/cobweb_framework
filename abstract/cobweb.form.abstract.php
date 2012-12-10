<?php
/**
 * ************************************************************************************************
 * _____      _ __          __  _         ______                                           _    
 * / ____|    | |\ \        / / | |       |  ____|         Rapid Web Application           | |   
 * | |     ___ | |_\ \  /\  / /__| |__     | |__ _ __ __ _ _ __ ___   _____      _____  _ __| | __
 * | |    / _ \| '_ \ \/  \/ / _ \ '_ \    |  __| '__/ _` | '_ ` _ \ / _ \ \ /\ / / _ \| '__| |/ /
 * | |___| (_) | |_) \  /\  /  __/ |_) |   | |  | | | (_| | | | | | |  __/\ V  V / (_) | |  |   < 
 * \_____\___/|_.__/ \/  \/ \___|_.__/    |_|  |_|  \__,_|_| |_| |_|\___| \_/\_/ \___/|_|  |_|\_\
 * ************************************************************************************************ 
 * Filename: cobweb.form.abstract.php
 * Author: Benedikt Wolters (mightyuhu@php.net)
 * License:  MIT License
 * Version: $Id: cobweb.form.abstract.php 19 2009-09-12 13:40:15Z mightyuhu $;
 * ************************************************************************************************
 * Description:
 * CobWeb Abstract Form Class
 * All Forms are inherited from this parent class.
 * The CobWeb_Form class provides general Methods used in the context of the orm generation and 
 * Form validation process
 * ************************************************************************************************
 */
if (! defined ( 'cw_inc' )) {
	echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
	exit ( 1 );
}
function CobWeb_Form_loadDependencies() {
	CobWeb::o ( 'Translator' )->loadLib ( 'forms' );
	//CobWeb_FormElement is covered by __autoload()
	require_once cw_framework_dir . 'formhelper/cobweb.form.text.element.' . cw_phpex;
	require_once cw_framework_dir . 'formhelper/cobweb.form.select.element.' . cw_phpex;
	require_once cw_framework_dir . 'formhelper/cobweb.form.radio.element.' . cw_phpex;
	require_once cw_framework_dir . 'formhelper/cobweb.form.checkbox.element.' . cw_phpex;
	require_once cw_framework_dir . 'formhelper/cobweb.form.fieldset.element.' . cw_phpex;
	require_once cw_framework_dir . 'formhelper/cobweb.form.reset.element.' . cw_phpex;
	require_once cw_framework_dir . 'formhelper/cobweb.form.textarea.element.' . cw_phpex;
	require_once cw_framework_dir . 'formhelper/cobweb.form.submit.element.' . cw_phpex;
	require_once cw_framework_dir . 'formhelper/cobweb.form.hidden.element.' . cw_phpex;
	require_once cw_framework_dir . 'formhelper/cobweb.form.captcha.element.' . cw_phpex;
	require_once cw_framework_dir . 'formhelper/cobweb.form.password.element.' . cw_phpex;
}
abstract class CobWeb_Form {
	protected $elements = null;
	protected $formHash = null;
	protected $formHashSessionCheck = false;
	protected $namePrefix = null;
	//<form>
	protected $enctype = null; // application/x-www-form-urlencoded || multipart/form-data
	protected $action = null;
	protected $method = null; //POST || GET
	protected $validate = null;
	protected $cache = null;
	public function getAction() {
		return $this->action;
	}
	public function getElements() {
		return $this->elements;
	}
	public function getEnctype() {
		return $this->enctype;
	}
	public function getFormHash() {
		return $this->formHash;
	}
	public function getFormHashSessionCheck() {
		return $this->formHashSessionCheck;
	}
	public function getMethod() {
		return $this->method;
	}
	public function getNamePrefix() {
		return $this->namePrefix;
	}
	public function setAction($action) {
		$this->action = $action;
	}
	public function setElements($elements) {
		$this->elements = $elements;
	}
	public function setEnctype($enctype) {
		$this->enctype = $enctype;
	}
	public function setFormHash($formHash) {
		$this->formHash = $formHash;
	}
	public function setFormHashSessionCheck($formHashSessionCheck) {
		$this->formHashSessionCheck = $formHashSessionCheck;
	}
	public function setMethod($method) {
		$this->method = $method;
	}
	public function setNamePrefix($namePrefix) {
		$this->namePrefix = $namePrefix;
	}
	public function __construct() {
		$this->elements = array ();
		$this->formHash = CobWeb::o ( 'Session' )->getNonce ();
		$this->formHashSessionCheck = false;
		$this->referrerCheck = false;
		$this->namePrefix = null;
		$this->enctype = 'application/x-www-form-urlencoded';
		$this->action = '';
		$this->method = 'POST';
		$CW_FId = new CobWeb_FormHiddenElement ( 'CW_FId' );
		$CW_FId->setDefault ( $this->getFormHash () );
		$CW_FFTime = new CobWeb_FormHiddenElement ( 'CW_FFTime' );
		$CW_FFTime->setDefault ( time () );
		$this->addElement ( $CW_FId );
		$this->addElement ( $CW_FFTime );
	}
	public function __destruct() {
	}
	public abstract function init(); //Function has to be implemented!
	

	//renders the form for individual usage
	public function prototype() {
		$this->init ();
		$this->display ();
	}
	//Validation:
	public function isValid(&$validateWith) {
		$globalFailedChecks = array ();
		$errorOccured = false;
		if ($this->formHashSessionCheck) {
			if ($validateWith ['CW_FId'] != $this->getFormHash ()) {
				$globalFailedChecks ['formHashSessionCheck'] = true; #
				$errorOccured = true;
			}
		}
		if ($this->referrerCheck != false) {
			//TODO do Referrer Check
			//$globalFailedChecks['ReferrerCheck'] = true;
		}
		foreach ( $this->elements as $element ) {
			if ($element->getName () != 'CW_FId' && $element->getName () != 'CW_FFTime' && !($element instanceof CobWeb_FormSubmitElement) && !($element instanceof CobWeb_FormResetElement)) {
			    if ( isset ( $validateWith [$element->getName ()] ) == false && $element instanceof CobWeb_FormCheckboxElement){
				    $validateWith [$element->getName ()] = 0;
				}
				if (! isset ( $validateWith [$element->getName ()] ))
					$validateWith [$element->getName ()] = null;
					
				$validationResult = $element->validate ( $validateWith );
				if (is_array($validationResult)) {
					$errorOccured = true;
					$globalFailedChecks [$element->getName ()] = $validationResult;
				}
			}
		}
		if ($errorOccured == true) {
			$this->validate = false;
			return false;
		} else {
			$this->validate = true;
			return true;
		}
	}
	//Generation
	public function addElement(CobWeb_FormElement $objectFormElement) {
		$failure = false;
		$name = $objectFormElement->getName ();
		foreach ( $this->elements as $element ) {
			if ($element->getName () == $name) {
				CobWeb::o ( 'Console' )->warning ( 'Cannot add Element ' . get_class ( $objectFormElement ) . '/' . $name . ': Name has already been chosen!' );
				$failure = true;
			}
		}
		//Text, Password, Selectbox, Option, File, submit, textarea, reset, hidden, checkbox, Catcha
		if (! $failure)
			$this->elements [] = &$objectFormElement;
		return ! $failure;
	}
	//Show
	public function preDisplay() {
	}
	public function postDisplay() {
	}
	public function preElement(CobWeb_FormElement $element) {
	}
	public function postElement(CobWeb_FormElement $element) {
	}
	public function display($showLastValidationValues = false) {
		$output = '';
		$this->preDisplay ();
		$Form = new CobWeb_NodeBuilder ( 'form' );
		$Form->addClass ( 'cw_form' );
		$Form->setAttrib ( 'method', $this->method );
		$Form->setAttrib ( 'action', $this->action );
		$Form->setAttrib ( 'enctype', $this->enctype );
		if (is_array ( $this->elements ))
			foreach ( $this->elements as $element ) {
				$output = $this->preElement ( $element );
				$output .= $element->renderErrorMessage ();
				if ($element->getName () != 'CW_FId' && $element->getName () != 'CW_FFTime') {
					$output .= $element->render ( $showLastValidationValues );
				} else {
					$output .= $element->render ( false );
				}
				$output .= $this->postElement ( $element );
				$Form->addContent ( $output, false );
			}
		$this->postDisplay ();
		$output = $Form->render ();
		$this->cache = $output;
		return $output;
	}
	public function __toString() {
		$this->display ();
	}
}
?>
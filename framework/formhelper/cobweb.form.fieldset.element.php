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
 * Filename: cobweb.form.fieldset.element.php
 * Author: Benedikt Wolters (mightyuhu@php.net)
 * License:  MIT License
 * Version: $Id: cobweb.form.fieldset.element.php 19 2009-09-12 13:40:15Z mightyuhu $;
 * ************************************************************************************************
 * Description:
 * 
 * ************************************************************************************************
 */
if (! defined ( 'cw_inc' )) {
	echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
	exit ( 1 );
}
if (! defined ( 'cw_inc' )) {
	echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
	exit ( 1 );
}
class CobWeb_FormFieldsetElement extends CobWeb_FormElement {
	private $elementContainer;
	public function __construct($name, $settings = false) {
		parent::__construct ( $name, $settings );
		$this->settings = $settings;
		$this->name = $name;
		$this->elementContainer = array ();
	}
	public function render($validation = false) {
		//Determine if there is a Elementid:
		if (isset ( $this->settings ['id'] ))
			$elementid = $this->settings ['id'];
		else
			$elementid = $this->name;
		
		$Fieldset = new CobWeb_NodeBuilder ( 'fieldset', $elementid );
		if ($this->label != '' || $this->settings ['label'] != '') {
			if ($this->settings ['label'] != '') {
				$labelText = $this->settings ['label'];
			}
			if ($this->label != '') {
				$labelText = $this->label;
			}
			$Legend = new CobWeb_NodeBuilder ( 'legend' );
			$Legend->addContent ( $labelText );
			$Fieldset->addChild ( $Legend );
		}
		$out = '';
		foreach ( $this->elementContainer as $element ) {
			$out .= $this->preElement ( $element );
			$out .= $element->renderErrorMessage ();
			$out .= $element->render ( $validation );
			$out .= $this->postElement ( $element );
		}
		$Fieldset->addContent ( $out, FALSE );
		return $Fieldset->render ();
	}
	public function preElement(CobWeb_FormElement $element) {
	}
	public function postElement(CobWeb_FormElement $element) {
	}
	public function setRequired($bool) {
		//Redirect to Elements
		foreach ( $this->elementContainer as $element ) {
			$element->setRequired ( $bool );
		}
	}
	public function addElement(CobWeb_FormElement $element) {
		$this->elementContainer [$element->getName ()] = &$element;
	}
	public function setAjaxControl($bool) {
		//Redirect to Elements
		foreach ( $this->elementContainer as $element ) {
			$element->setAjaxControl ( $bool );
		}
	}
	public function setDefault($mixed) {
		//Redirect to Elements
		foreach ( $this->elementContainer as $element ) {
			$element->setDefault ( $mixed );
		}
	}
	public function isDefault($mixed) {
		return false;
	}
	public function setJsValidation($bool) {
		foreach ( $this->elementContainer as $element ) {
			$element->setJsValidation ( $bool );
		}
	}
	public function hasElements() {
		return (count ( $this->elementContainer ) >= 1) ? true : false;
	}
	public function validate($validateWith) {
		
		if ($this->hasElements ()) {
			CobWeb::checkDep ( 'Validator' );
			$failedChecks = array ();
			$errorOccured = false;
			foreach ( $this->elementContainer as $elementName => $element ) {
				if (! ($element instanceof CobWeb_FormSubmitElement) && ! ($element instanceof CobWeb_FormResetElement)) {
					if (isset ( $validateWith [$elementName] ) == false && $element instanceof CobWeb_FormCheckboxElement) {
						$validateWith [$elementName] = 0;
					} else if (! isset ( $validateWith [$elementName] ))
						$validateWith [$elementName] = null;
						
					$validationResult = $element->validate ( $validateWith );
					if (is_array($validationResult) ==true) {
						$errorOccured = true;
						$failedChecks [$element->getName ()] = $validationResult;
					}
				}
			}
			if ($errorOccured == true) { 
				return  $failedChecks;
			} else {
				return true;
			}
		} else {
			return true;
		}
	}
	public function addValidator($validator, $settings = null) {
		CobWeb::o ( 'Console' )->notice ( 'CobWeb FieldsetElement cannot have a Validator function' );
	}
}
?>
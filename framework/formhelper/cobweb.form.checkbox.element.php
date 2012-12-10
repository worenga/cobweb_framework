<?php
/**
 * ************************************************************************************************
 * _____      _ __          __  _         ______     RAPID WEB APPLICATION FRAMEWORK       _    
 * / ____|    | |\ \        / / | |       |  ____|                                         | |   
 * | |     ___ | |_\ \  /\  / /__| |__     | |__ _ __ __ _ _ __ ___   _____      _____  _ __| | __
 * | |    / _ \| '_ \ \/  \/ / _ \ '_ \    |  __| '__/ _` | '_ ` _ \ / _ \ \ /\ / / _ \| '__| |/ /
 * | |___| (_) | |_) \  /\  /  __/ |_) |   | |  | | | (_| | | | | | |  __/\ V  V / (_) | |  |   < 
 * \_____\___/|_.__/ \/  \/ \___|_.__/    |_|  |_|  \__,_|_| |_| |_|\___| \_/\_/ \___/|_|  |_|\_\
 * ************************************************************************************************ 
 * Filename: cobweb.form.checkbox.element.php
 * Author: Benedikt Wolters (mightyuhu@php.net)
 * License:  MIT License
 * Version: $Id: cobweb.form.checkbox.element.php 19 2009-09-12 13:40:15Z mightyuhu $;
 * ************************************************************************************************
 * Description:
 * Helper Class, CobWeb_FormChechboxElement. All Functions of the CheckboxElement are included here. 
 * ************************************************************************************************
 */
if (! defined ( 'cw_inc' )) {
	echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
	exit ( 1 );
}
class CobWeb_FormCheckboxElement extends CobWeb_FormElement {
	private $defaultValue = 1;
	private $checkedState = false;
	
	//Settings keys: wrapperclass, readonly, maxlenght, id
	public function __construct($name, $settings = false) {
		$this->settings = $settings;
		$this->name = $name;
	}


	public function render($validation = false) {
		//Determine if there is a Elementid:
		if (isset ( $this->settings ['id'] ))
			$elementId = $this->settings ['id'];
		else
			$elementId = $this->name;
			
		//BEGIN WRAPPER ELEMENT START
		$Wrapper = $this->getWrapperElement ();
		$Wrapper->addClass ( 'cw_checkbox' );
		//END WRAPPER ELEMENT START
		//If there is a label lets display it:
		if ($this->label != '' || $this->settings ['label'] != '') {
			if ($this->settings ['label'] != '') {
				$labelText = $this->settings ['label'];
			}
			if ($this->label != '') {
				$labelText = $this->label;
			}
			$Label = new CobWeb_NodeBuilder ( 'label' );
			$Label->setAttrib ( 'for', $elementId );
			$Label->addContent ( $labelText );
			$Wrapper->addChild ( $Label );
		}
		//Our actual Form Element:
		$checkboxElement = new CobWeb_NodeBuilder ( 'input' );
		$checkboxElement->setAttrib ( 'type', 'checkbox' );
		$checkboxElement->setId ( $elementId );
		$checkboxElement->setAttrib ( 'name', $this->name );
		$checkboxElement->setAttrib ( 'value', $this->defaultValue );
		
		if ($this->settings ['readonly'] == true) {
			$checkboxElement->setAttrib ( 'disabled', 'disabled' );
		}
		if ($validation == true) {
		   
			//Compare Hash values:
			if ($this->lastValidated == $this->defaultValue) {
				//Was selected:
				$checkboxElement->setAttrib ( 'checked', 'checked' );
			}
		}
		if ($this->defaultValue != null) {
			$checkboxElement->setAttrib ( 'value', $this->defaultValue );
		}
		
		$Wrapper->addChild ( $checkboxElement );
		return $Wrapper->render ();
	}
	public function setJsValidation($bool) {
		$this->jsValidation = $bool;
	}
	public function setAjaxControl($bool) { //...
	}
	public function isDefault($value) {
		if ($value == $this->defaultValue) {
			return true;
		} else {
			return false;
		}
	}
	public function setDefault($string) {
		return ($this->defaultValue = $string);
	}
}
?>
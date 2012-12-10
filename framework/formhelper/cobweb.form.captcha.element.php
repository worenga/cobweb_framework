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
 * Filename: cobweb.form.captcha.element.php
 * Author: Benedikt Wolters (mightyuhu@php.net)
 * License:  MIT License
 * Version: $Id: cobweb.form.captcha.element.php 19 2009-09-12 13:40:15Z mightyuhu $;
 * ************************************************************************************************
 * Description:
 * Helper Class, CobWeb_FormCaptchaElement. All Functions of the CaptchaElement are included here. 
 * ************************************************************************************************
 */
if (! defined('cw_inc')) {
    echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
    exit(1);
}
class CobWeb_FormCaptchaElement extends CobWeb_FormElement {
	private $defaultValue = null;
	private $hash = null;
	
	/**
	 * @return unknown
	 */
	public function getHash() {
		return $this->hash;
	}
	/**
	 * @param unknown_type $hash
	 */
	public function setHash($hash) {
		$this->hash = $hash;
	}
	//Settings keys: wrapperclass, readonly, maxlenght, id
	public function __construct($name, $settings = false) {
		$this->settings = $settings;
		$this->name = $name;
		$this->setRequired ( true ); //Captchas are always required
		$this->addValidator ( 'captchaCheck', array('breakOnError' => true));
	}
	
	public function captchaCheck($validateWith) {
		$hash = $this->validateWith ['cwcph_' . $this->getName ()];
		$shadowCaptcha = new CobWeb_CaptchaImage ( $hash );
		if ($shadowCaptcha->checkCaptcha ( $validateWith )) {
			return true;
		} else {
			return false;
		}
	}
	
	public function render($validation = false) {
		$element = '';
		//Determine if there is a Elementid:
		if (isset ( $this->settings ['id'] ))
			$elementid = $this->settings ['id'];
		else
			$elementid = $this->name;
			//BEGIN WRAPPER ELEMENT START
		$Wrapper = $this->getWrapperElement ();
		$Wrapper->addClass ( 'cw_captcha' );
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
			$Label->setAttrib ( 'for', $elementid );
			$Label->addContent ( $labelText );
			$Wrapper->addChild ( $Label );
		}
		if ($this->hash == null) {
			$Hash = CobWeb::o ( 'Security' )->generateHashCode ();
		}
		
		$CaptchaWrapper = new CobWeb_NodeBuilder ( 'div' );
		$CaptchaWrapper->addClass ( 'cw_captchaimage' );
		
		$CaptchaImage = new CobWeb_NodeBuilder ( 'img' );
		$CaptchaImage->setId('cw_captchaimg_'.$elementid);
		$CaptchaImage->setAttrib ( 'alt', '' );
		$CaptchaImage->setAttrib ( 'src', CobWeb::o ( 'Router' )->createRoute ( 'captcha', 'display', array ('hash' => $Hash ) ) );
		$CaptchaWrapper->addChild ( $CaptchaImage );
		
		if (!isset ( $this->settings ['noregen'] )){
			$RegenLink = new CobWeb_NodeBuilder('a');
			$RegenLink->setId('cw_regenerate_'.$elementid);
			$RegenLink->addClass('cw_captcha_regenerate');
			$RegenLink->setAttrib('name','cw_regenerate_'.$elementid);
			$RegenLink->setAttrib('href','#cw_regenerate_'.$elementid);
			$RegenLink->addContent(CobWeb::o('Translator')->forms_regenerateCaptchaLabel); //Todo Translation 
			$CaptchaWrapper->addChild ( $RegenLink );
			
			$WaitText = new CobWeb_NodeBuilder('span');
			$WaitText->setId('cw_regenerate_wait_'.$elementid);
			$WaitText->addClass('cw_captcha_regenerate_wait');
			$WaitText->addContent(CobWeb::o('Translator')->forms_regeneralteCaptchaWait);
			$CaptchaWrapper->addChild ( $WaitText );
			
			//Notify View
			CobWeb::setSetting('view##jquery_captcha_regeneration',true);
		}
		
		
		//Our actual Form Element:
		$textElement = new CobWeb_NodeBuilder ( 'input' );
		$textElement->setAttrib ( 'type', 'text' );
		$textElement->setId ( $elementid );
		$textElement->setAttrib ( 'name', $this->name );
		if ($this->settings ['maxlength'] != '') {
			$textElement->setAttrib ( 'maxlength', $settings ['maxlength'] );
		}
		if ($this->settings ['readonly'] == true) {
			$textElement->setAttrib ( 'readonly', 'readonly' );
		}
		$CaptchaWrapper->addChild ( $textElement );
		$Wrapper->addChild ( $CaptchaWrapper );
		$brclearfix = new CobWeb_NodeBuilder('br');
		$brclearfix->addClass('cl');
		$Wrapper->addChild ( $brclearfix );
		
		$HashValue = new CobWeb_NodeBuilder ( 'input' );
		$HashValue->setAttrib ( 'type', 'hidden' );
		$HashValue->setAttrib ( 'value', $Hash );
		$HashValue->setAttrib ( 'name', 'cwcph_' . $this->name );
		
		$Wrapper->addChild ( $HashValue );
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
		$this->defaultValue = $string;
	}
}
?>
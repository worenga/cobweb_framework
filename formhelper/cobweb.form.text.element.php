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
 * Filename: cobweb.form.text.element.php
 * Author: Benedikt Wolters (mightyuhu@php.net)
 * License:  MIT License
 * Version: $Id: cobweb.form.text.element.php 19 2009-09-12 13:40:15Z mightyuhu $;
 * ************************************************************************************************
 * Description:
 * Helper Class, CobWeb_FormTextElement. All Functions of the TextElement are included here. 
 * ************************************************************************************************
 */
if (! defined('cw_inc')) {
    echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
    exit(1);
}
class CobWeb_FormTextElement extends CobWeb_FormElement
{
    private $defaultValue = null;
    //Settings keys: wrapperclass, readonly, maxlenght, id
    public function __construct ($name, $settings = false)
    {
        $this->settings = $settings;
        $this->name = $name;
    }
    public function render ($validation = false)
    {
        //Determine if there is a Elementid:
        if (isset($this->settings['id']))
            $elementid = $this->settings['id'];
        else
            $elementid = $this->name;
        
        //BEGIN WRAPPER ELEMENT START
        $Wrapper = $this->getWrapperElement();
		$Wrapper->addClass('cw_text');
        //END WRAPPER ELEMENT START
        //If there is a label lets display it:
        if ($this->label != '' || $this->settings['label'] != '') {
            if ($this->settings['label'] != '') {
                $labelText = $this->settings['label'];
            }
            if ($this->label != '') {
                $labelText = $this->label;
            }
            $Label = new CobWeb_NodeBuilder('label');
            $Label->setAttrib('for',$elementid);
            $Label->addContent($labelText);
            $Wrapper->addChild($Label);
        }
        //Our actual Form Element:
        $textElement = new CobWeb_NodeBuilder('input');
        $textElement->setAttrib('type','text');
        $textElement->setId($elementid);
        $textElement->setAttrib('name',$this->name);
        if ($this->settings['maxlength'] != '') {
			$textElement->setAttrib('maxlength',$settings['maxlength']);
        }
        if ($this->settings['readonly'] == true) {
			$textElement->setAttrib('readonly','readonly');
        }
        if ($validation == true) {
        	$textElement->setAttrib('value',$this->lastValidated);
        } else {
            if ($this->defaultValue != null) {
            	$textElement->setAttrib('value',$this->defaultValue);
            }
        }
        $Wrapper->addChild($textElement);
        return $Wrapper->render();
    }
    public function setJsValidation ($bool)
    {
        $this->jsValidation = $bool;
    }
    public function setAjaxControl ($bool)
    {    //...
    }
    public function isDefault ($value)
    {    
        if($value == $this->defaultValue){
            return true;
        }else{
            return false;
        }
    }
    public function setDefault ($string)
    {
        $this->defaultValue = $string;
    }
}
?>
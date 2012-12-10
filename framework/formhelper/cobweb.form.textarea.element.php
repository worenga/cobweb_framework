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
 * Filename: cobweb.form.validator.php
 * Author: Benedikt Wolters (mightyuhu@php.net)
 * License:  MIT License
 * Version: $Id: cobweb.form.textarea.element.php 19 2009-09-12 13:40:15Z mightyuhu $;
 * ************************************************************************************************
 * Description:
 * Helper Class, CobWeb_FormTextareaElement. All Functions of the TextareaElement are included here. 
 * ************************************************************************************************
 */
if (! defined('cw_inc')) {
    echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
    exit(1);
}
class CobWeb_FormTextareaElement extends CobWeb_FormElement
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

        $Wrapper = $this->getWrapperElement();
		$Wrapper->addClass('cw_textarea');
        
		
		
		//If there is a label lets display it:
        if ($this->label != '' || $this->settings['label'] != '') {
            $Label = new CobWeb_NodeBuilder('label');
            
        	if ($this->settings['label'] != '') {
                $labelText = $this->settings['label'];
            }
            if ($this->label != '') {
                $labelText = $this->label;
            }
            $Label->addContent($labelText);
            $Label->setAttrib('for',$elementid);
            $Wrapper->addChild($Label);
        }
        //Our actual Form Element:
        $Textarea = new CobWeb_NodeBuilder('textarea');
        $Textarea->setId($elementid);
        $Textarea->setAttrib('name',$this->name);
        if ($this->settings['maxlength'] != '') {
        	$Textarea->setAttrib('maxlength',$settings['maxlength']);
        }
        if ($this->settings['readonly'] == true) {
        	$Textarea->setAttrib('readonly','readonly');
        }
        if ($validation == true) {
            $Textarea->addContent($this->lastValidated,false); //TODO?
        } else {
            if ($this->defaultValue != null) {
            	$Textarea->addContent($this->defaultValue);
            }
        }
        $Wrapper->addChild($Textarea);
        $element = $Wrapper->render();
        return $element;
    }
    public function setJsValidation ($bool)
    {
        $this->jsValidation = $bool;
    }
    public function setAjaxControl ($bool)
    {
        
    }
    public function setDefault ($string)
    {
        $this->defaultValue = stripslashes($string);
    }
    public function isDefault ($value)
    {
        if ($value == $this->defaultValue) {
            return true;
        } else {
            return false;
        }
    }
}
?>
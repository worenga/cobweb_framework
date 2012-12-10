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
 * Version: $Id: cobweb.form.hidden.element.php 19 2009-09-12 13:40:15Z mightyuhu $;
 * ************************************************************************************************
 * Description:
 * Helper Class, CobWeb_FormTextElement. All Functions of the TextElement are included here. 
 * ************************************************************************************************
 */
if (! defined('cw_inc')) {
    echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
    exit(1);
}
class CobWeb_FormHiddenElement extends CobWeb_FormElement
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
        $HiddenField = new CobWeb_NodeBuilder('input');
        $HiddenField->setAttrib('type','hidden');
        $HiddenField->setId($elementid);
        $HiddenField->setAttrib('name',$this->name);
        if ($validation == true) {
        	$HiddenField->setAttrib('value',$this->lastValidated);
        } else {
            if ($this->defaultValue != null) {
            	$HiddenField->setAttrib('value',$this->defaultValue);
            }
        }
        return $HiddenField->render();
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
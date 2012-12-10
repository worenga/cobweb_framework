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
 * Filename: cobweb.form.submit.element.php
 * Author: Benedikt Wolters (mightyuhu@php.net)
 * License:  MIT License
 * Version: $Id: cobweb.form.submit.element.php 19 2009-09-12 13:40:15Z mightyuhu $;
 * ************************************************************************************************
 * Description:
 * Helper Class, CobWeb_FormSubmitElement. All Functions of the SubmitElement are included here. 
 * ************************************************************************************************
 */
if (! defined('cw_inc')) {
    echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
    exit(1);
}
class CobWeb_FormSubmitElement extends CobWeb_FormElement
{
    public function __construct ($name, $settings = false)
    {
        $this->settings = $settings;
        $this->name = $name;
        $this->label = CobWeb::o('Translator')->forms_defaultSubmitLabel;
    }
    public function render ($validation = false)
    {
        
        //Determine if there is a Elementid:
        if (isset($this->settings['id']))
            $elementid = $this->settings['id'];
        else
            $elementid = $this->name;
            
        $SubmitElement = new CobWeb_NodeBuilder('input');
        $SubmitElement->addClass('cw_form_submit');
        $SubmitElement->setAttrib('type','submit');
        $SubmitElement->setAttrib('value',$this->label);
        $SubmitElement->setAttrib('name',$this->name);
        $SubmitElement->setId($elementid);
        return $SubmitElement->render(); 
    }
    public function setAjaxControl ($bool)
    {
        //TODO Create Callback Button here instead??
        return false;
    }
    public function setDefault ($mixed)
    {
        $this->label = (string) $mixed;
    }
    public function isDefault($mixed){
        if($this->label == $mixed){
            return true;
        }else{
            return false;
        }
    }
    public function setJsValidation ($bool)
    {
        return false;
    }
}
?>
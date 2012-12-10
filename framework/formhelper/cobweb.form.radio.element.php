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
 * Filename: cobweb.form.radio.element.php
 * Author: Benedikt Wolters (mightyuhu@php.net)
 * License:  MIT License
 * Version: $Id: cobweb.form.radio.element.php 19 2009-09-12 13:40:15Z mightyuhu $;
 * ************************************************************************************************
 * Description:
 * Helper Class, CobWeb_FormRadioElement. All Functions of the SelectElement are included here. 
 * ************************************************************************************************
 */
if (! defined('cw_inc')) {
    echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
    exit(1);
}
class CobWeb_FormRadioElement extends CobWeb_FormElement
{
    private $defaultValue = null;
    private $defaultKeySelected;
    //Settings keys: wrapperclass, readonly, id, allowForeignInput
    public function __construct ($name, $settings = false)
    {
        $this->settings = $settings;
        $this->name = $name;
        $this->defaultKeySelected = null;
        if(isset($this->settings['allowForeignInput']) ==false || $this->settings['allowForeignInput'] == false){
            $this->addValidator('isNoForeignInput',array('breakOnError' => true));
        }
    }
    /**
     * @public
     * @param Key Value selected
     */
    public function setSelected($keyContents){
        if(array_key_exists($keyContents,$this->defaultValue)){
            return ($this->defaultKeySelected = $keyContents);    
        }else{
            CobWeb::o('Console')->warning('CobWeb_FormSelectElement##'.$this->name.': Selected key not found.');
            return false;
        } 
        
    }
    /**
     * 
     * @param $validateWith
     * @return unknown_type
     */
    protected function isNoForeignInput($validateWith){
        if(!is_array($validateWith)){
            if(in_array($validateWith,$this->defaultValue,true)){
                return true;
            }else{
                return false;
            }
        }else{
            foreach ($validateWith as $value){
                if (!array_key_exists($value,$this->defaultValue)){
                    return false;
                }
            }
            return true;
        }
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
		$Wrapper->addClass('cw_selectbox');
        //END WRAPPER ELEMENT START
        //If there is a label lets display it:
        if ($this->label != '' || (isset($this->settings['label']) && $this->settings['label'] != '') ) {
            
            if (isset($this->settings['label']) &&$this->settings['label'] != '') {
                $labelText = $this->settings['label'];
            }
            if ($this->label != '') {
                $labelText = $this->label;
            }
            $Label = new CobWeb_NodeBuilder('label');
            $Label->addContent($labelText);
            $Wrapper->addChild($Label);
        }
        
        
        //Our actual Form Element:
        $selectWrapper = new CobWeb_NodeBuilder('optgroup',$elementid);
        $selectWrapper->setId($elementid);
        
        if (isset($this->settings['readonly']) && $this->settings['readonly'] == true) {
			$selectWrapper->setAttrib('disabled','disabled');
        }
        
       
        //put default values as option-tags
        foreach ($this->defaultValue as $id => $optLabel) {
            $radioElement = new CobWeb_NodeBuilder('input',$this->name.'_'.$id);
            $radioElement->setAttrib('type','radio');
            $radioElement->setAttrib('value',$id);
            $radioElement->setAttrib('name',$this->name);
            if ($validation != true) {
                   if($id == $this->defaultKeySelected){
                    $radioElement->setAttrib('checked','checked');
                   }
               
            }else{
                    if($id == $this->lastValidated){
                        $radioElement->setAttrib('checked','checked');
                    }
            }
            $labelElement = new CobWeb_NodeBuilder('label',$this->name.'_lbl_'.$id);
            $labelElement->setAttrib('for',$this->name.'_'.$id);
            $labelElement->addContent($optLabel);
            
            $selectWrapper->addChild($radioElement);
            $selectWrapper->addChild($labelElement);
            unset($radioElement);
            unset($labelElement);
        }
        
         
       //todo validation here
        if ($validation == true) {
              //todo determine the validated option box and do render
              
            //check 
              
        } else {
            if ($this->defaultValue != null) {
            	$selectWrapper->setAttrib('value',$this->defaultValue);
            }
        }
        $Wrapper->addChild($selectWrapper);
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
    public function setDefault ($values)
    {
        $this->defaultValue = $values;
    }
}
?>
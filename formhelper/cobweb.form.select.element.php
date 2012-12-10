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
 * Filename: cobweb.form.select.element.php
 * Author: Benedikt Wolters (mightyuhu@php.net)
 * License:  MIT License
 * Version: $Id: cobweb.form.select.element.php 19 2009-09-12 13:40:15Z mightyuhu $;
 * ************************************************************************************************
 * Description:
 * Helper Class, CobWeb_FormSelectElement. All Functions of the SelectElement are included here. 
 * ************************************************************************************************
 */
if (! defined('cw_inc')) {
    echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
    exit(1);
}
class CobWeb_FormSelectElement extends CobWeb_FormElement
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
            $this->addValidator('isNoForeignInput',array('breakOnError' => true,'onError'=>'Invalid foreign Input')); //TODO
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
    
    protected function isNoForeignInput($validateWith){

        if(!is_array($validateWith)){
            if(array_key_exists($validateWith,$this->defaultValue)){
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
            $Label->setAttrib('for',$elementid);
            $Label->addContent($labelText);
            $Wrapper->addChild($Label);
        }
        
        
        //Our actual Form Element:
        $selectWrapper = new CobWeb_NodeBuilder('select',$elementid);
        $selectWrapper->setAttrib('name',$this->name);
        $selectWrapper->setId($elementid);
        
        if (isset($this->settings['readonly']) && $this->settings['readonly'] == true) {
			$selectWrapper->setAttrib('disabled','disabled');
        }
        
        
        if(isset($this->settings['multiple']) && $this->settings['multiple'] == true) {
            $selectWrapper->setAttrib('name',$this->name.'[]');
            $selectWrapper->setAttrib('multiple','multiple');
            $selectWrapper->setAttrib('size',count($this->defaultValue));
        } 
        
        //put default values as option-tags
        foreach ($this->defaultValue as $id => $optLabel) {
            $option = new CobWeb_NodeBuilder('option',$this->name.'_'.$id);
            $option->addContent($optLabel);
            $option->setAttrib('value',$id);
            if ($validation != true) {
                   if($id == $this->defaultKeySelected){
                    $option->setAttrib('selected','selected');
                   }
               
            }else{
                if(isset($this->settings['multiple']) && $this->settings['multiple'] == true) {
                    if (in_array($id,$this->lastValidated,true)){
                        $option->setAttrib('selected','selected');
                    }
                }else{
                    if($id == $this->lastValidated){
                        $option->setAttrib('selected','selected');
                    }
                }
            }
            $selectWrapper->addChild($option);
            unset($option);
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
<?php
if (! defined('cw_inc')) {
    echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
    exit(1);
}
class CobWeb_FormResetElement extends CobWeb_FormElement
{
    public function __construct ($name, $settings = false)
    {
        $this->settings = $settings;
        $this->name = $name;
        $this->label = CobWeb::o('Translator')->forms_defaultResetLabel;
    }
    public function render ($validation = false)
    {
        //Determine if there is a Elementid:
        if (isset($this->settings['id']))
            $elementid = $this->settings['id'];
        else
            $elementid = $this->name;
        
        $ResetElement = new CobWeb_NodeBuilder('input');
        $ResetElement->addClass('cw_form_reset');
        $ResetElement->setAttrib('type','reset');
        $ResetElement->setAttrib('value',$this->label);
        $ResetElement->setAttrib('name',$this->name);
        $ResetElement->setId($elementid);
        return $ResetElement->render(); 
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
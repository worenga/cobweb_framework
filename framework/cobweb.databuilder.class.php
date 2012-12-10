<?php

class CobWeb_DataBuilder extends CobWeb_Builder
{
    private $value = null;
    public function __construct($value,$escape = true)
    {
        if($escape){
            $this->value = CobWeb::o('Security')->maskxHtml($value);
        }else{
            $this->value = $value;        
        }
        return $this;
    }
    public function render()
    {
        return $this->value;
    }
    
}
?>
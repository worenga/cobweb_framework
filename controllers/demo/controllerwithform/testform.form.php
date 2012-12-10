<?php
class TestForm extends CobWeb_Form{
    public function init(){
        
        $area = new CobWeb_FormFieldsetElement('Fieldset');
        $area->addLabel('MeinFieldset1');
                
        $textfeld1 = new CobWeb_FormTextElement('TextfeldName1');
        $textfeld1->addLabel('MeinTextfeld');
        $textfeld1->setDefault('Defaultwert');
        $textfeld1->setRequired(true);
        //$textfeld1->addValidator('isNumber',array('onError' => 'Bitte numerisch!'));
        $textfeld1->addValidator('isWithinRange',array(0,2,'onError' => 'nich in range!'));
        $textfeld1->setCanContainDefault(false);
        //CobWeb_Validator::isWithinRange()
        $area->addElement($textfeld1);
        
        $textarea1 = new CobWeb_FormTextareaElement('Textarea1');
        $textarea1->addLabel('Textarea:');
        $textarea1->setDefault('Defaultwert');
        $textarea1->setRequired(true);
        
        $area->addElement($textarea1);
        $this->addElement($area);
        
        $outerText = new CobWeb_FormTextElement('Textfeld2');
        $outerText->addLabel('MeinTextfeld2');
        $outerText->setDefault('Defaultwert');
        $outerText->setRequired(true);#
        $outerText->addValidator('isNumber',array('onError' => 'Bitte numerisch!'));
        
        $captcha = new CobWeb_FormCaptchaElement('Captcha');
        $captcha->addLabel('Captcha Element');
        $this->addElement($captcha);
        
        $testselect1 = new CobWeb_FormSelectElement('testselect1');
        $testselect1->addLabel('Selectbox Label');
        $testselect1->setDefault(array('key1'=>'testlabel1','key2'=>'testlabel2','key3'=>'testlabel3'));
        $testselect1->setRequired(true);
        $testselect1->setSelected('key2');
        $this->addElement($testselect1);
        
        $testradio1 = new CobWeb_FormRadioElement('testradio');
        $testradio1->addLabel('Selectbox Label');
        $testradio1->setDefault(array('key1'=>'testlabel1','key2'=>'testlabel2','key3'=>'testlabel3'));
        $testradio1->setRequired(true);
        $testradio1->setSelected('key2');
        $this->addElement($testradio1);
        
        $Passwordfield1 = new CobWeb_FormPasswordElement('anothrPwd');
        $Passwordfield1->addLabel('Irgendein Passwort');
        $Passwordfield1->setRequired(true);
        $Passwordfield1->addValidator('equalsTo',array('onError' => 'Passwörter stimmen nicht überein!', 'password'));
        $this->addElement($Passwordfield1);
        
        $Passwordfield2 = new CobWeb_FormPasswordElement('password');
        $Passwordfield2->addLabel('Wiederholung!!!');
        $Passwordfield2->setRequired(true);
        //$Passwordfield2->addValidator('equalsTo','anothrPwd');
        $this->addElement($Passwordfield2);
        
        $TestCheckbox1 = new CobWeb_FormCheckboxElement('chk1_test');
        $TestCheckbox1->addLabel('Meine kuhle Chäckbox');
        $TestCheckbox1->setDefaultValidationError('Gib mal was anständiges ein!!!');
        $TestCheckbox1->setRequired(true);
        $this->addElement($TestCheckbox1);
        
        
        $Submit = new CobWeb_FormSubmitElement('pre_submit');
        $Reset = new CobWeb_FormResetElement('pre_reset');
        $this->addElement($outerText);
        $this->addElement($Submit);
        $this->addElement($Reset);
    }
}
?>
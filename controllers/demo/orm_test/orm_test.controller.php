<?php
class orm_testController extends CobWeb_Controller
{
    public function __construct ()
    {
        $this->ControllerName = 'orm_testController';
        $this->setDefaultAction('test');
        $this->setControllerCaching(false);
        parent::__construct();
    }
    
    public function testAction()
    {

    }    
    
}
?>
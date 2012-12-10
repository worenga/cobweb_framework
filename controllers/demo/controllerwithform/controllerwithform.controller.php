<?php
class controllerwithformController extends CobWeb_WebPageController
{
    /**
     * 
     */
    public function __construct ()
    {
        $this->ControllerName = 'controllerwithformController';
        $this->setDefaultAction('showForm');
        parent::__construct();
    }
    public function logAction(){
        CobWeb::o('Console')->warning("test!!!");
    }
    
    public function showFormAction ()
    {
     CobWeb::o('Console')->warning("test!!!");
        $this->loadForm('testform');
        $testform = new TestForm();
        $testform->init();
        if($this->isPost()){
            if($testform->isValid($_POST)){
                $_out = "Congrats, Validated!";
                $form = null;
            }else{
                $_out = "Yo Noob! Theres an error!";
                $form = $testform->display(true);
            }
        }else{
            //echo CobWeb_Compressor::out(CobWeb_Compressor::compressxHTML($testform->display()));
            $_out = null;
        	$form =  $testform->display();    
        }
        $data = array('form' => $form,'notice' => $_out,'pageTitle' => 'Hello Bastard');
        Cobweb::o('View')->view('defaultPage/standard',$data);
        
    }
    public function RoutingTestAction ()
    {
        var_dump(CobWeb::o('Router')->createRoute('controllername'));
    }
    /**
     * 
     */
    public function __destruct ()
    {}
}
?>
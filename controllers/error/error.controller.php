<?php
class errorController extends CobWeb_Controller
{
    /**
     * 
     */
    public function __construct ()
    {
        //Set Default Action:
        $this->DefaultAction = 'undefinedError';
        $this->ControllerName = 'errorController';
        //Enable Caching for the Error Controller
        $this->ControllerCaching = true;
        
        parent::__construct();
    }
    /**
     * 
     */
    
    public function missingControllerAction(){
        $data['message'] = 'missingController';
        Cobweb::o('View')->view('error/notfound',$data);
    }
    
    public function accessDeniedAction(){
        $data['message'] = '';
        Cobweb::o('View')->view('error/accessdenied',$data);
    }
    
    function __destruct ()
    {
        
        parent::__destruct();
    }
}
?>
<?php
class contentController extends CobWeb_Controller
{
    
    /**
     * 
     */
    public function __construct ()
    {
        $this->ControllerName = 'contentController';
        $this->setDefaultAction('view');
        $this->setControllerCaching(true);
        parent::__construct();
        
       
    }
    
    
    public function viewAction(){
                $usr = new User();
        $usr->foo();
        $this->saveToCache('view','viewAction-ToCache');
        echo "No Cache DAMN!s";
    }
    public function viewActionCache(){
        echo 'Cache Controller, Cache is ';
        echo $this->cacheAge;
        echo ' Secounds old!';
    }
    
    /**
     * 
     */
    public function __destruct ()
    {}
}
?>
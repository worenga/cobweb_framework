<?php
class welcomeController extends CobWeb_Controller
{
    

    public function __construct ()
    {
        $this->ControllerName = 'welcomeController';
        $this->setDefaultAction('view');
        $this->setControllerCaching(true);
        $this->setCacheLifetime(10);
        parent::__construct();
    }
    
    
    public function viewAction(){
        $data['diagnostics'] = CobWeb::getSetting('Diagnostics');
        $out = CobWeb_Compressor::compressxHTML(Cobweb::o('View')->view('welcome/intro',$data,true));
        $this->saveToCache('view',$out);
        CobWeb_Compressor::out($out);

    }
    public function viewActionCache(){
        echo $this->cacheData;
        
    }
    
    /**
     * 
     */
    public function __destruct ()
    {}
}
?>
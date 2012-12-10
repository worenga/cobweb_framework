<?php
class CobWeb_Datagrid
{
    /**
	 * Route Parameters
	 * - p = Current Page
	 * - o = Order By Field
	 * - seq = DESC / ASC 
	 * - l = Limit
	 * @param unknown_type $params
	 */
    
    
    private $maxDataSets = 20;
    private $currentPage = 1;
    private $Data = null; //Data
    private $OrmDefaultCondition = null; //Default Condition for the ORM Object
    private $OrmClass = null;
    private $OrmOrderBy = null;
    private $Headers = array(); //Header Captions
    private $Limiter = array(5 , 10 , 15 , 20 , 25 , 50 , 75 , 100 , 150 , 200 , 500);
    private $Fields = array();
    private $id = null;
    private $class = null;
    private $allowSort = true; 
    private $repeatHeader = 50; //Repeat Header ever x Rows.. or false;
    private $gridUrlParams = array();
    private $optionRoutes = array();

    public function getAllowSort() {
		return $this->allowSort;
	}

	/**
	 * @param $allowSort the $allowSort to set
	 */
	public function setAllowSort($allowSort) {
		$this->allowSort = $allowSort;
	}

	public function getBaseRoute() {
		if($this->BaseRoute === null){
		    //Base Route by current Routing
	        return CobWeb::getSetting('CurrentRoute');
		}else{
		    return $this->BaseRoute;
		}
	    
	}

	/**
	 * @param $BaseRoute the $BaseRoute to set
	 */
	public function setBaseRoute($BaseRoute) {
		$this->BaseRoute = $BaseRoute;
	}
	public function getDefaultOrderBy ()
    {
        return $this->OrmOrderBy;
    }
    public function setField ($fieldname, $type = 'varchar', $displayName = false)
    {
        //TODO: validate fieldname
        $this->Fields[$fieldname] = array('id' => $fieldname , 'type' => $type , 'displayName' => $displayName);
    }
    
    public function setOptionRoute($Route,$referenceParams,$displayText)
    {
        static $countRoutes = 0;
        $countRoutes++;
        $this->optionRoutes[$countRoutes] = array('route' => $Route , 'referenceParams' => $referenceParams , 'displayText' => $displayText);
    }  
    
    public function setDefaultOrderBy ($OrmOrderBy)
    {
        $this->OrmOrderBy = $OrmOrderBy;
    }
    public function __construct ($OrmClass, $OrmDefaultCondition = null)
    {
        $this->OrmDefaultCondition = $OrmDefaultCondition;
        if (class_exists($OrmClass)) {
            $this->OrmClass = $OrmClass;
        } else {
            CobWeb::o('Console')->warning('CobWeb_Datagrid##__construct Orm Class does not exist');
            return false;
        }
    }
    public function setGridId ($value)
    {
        $this->id = $value;
    }
    public function setGridClass ($value)
    {
        $this->class = $value;
    }

    public function setGridParams ($arrayParams)
    {
        $this->gridUrlParams = $arrayParams;
    }
    
    
    //Allow Helper Functions / Views for Datafields
    public function render ($DatagridView = false)
    {
        
        //Current Limit?
        if(isset($this->gridUrlParams['l']) == true && in_array($this->gridUrlParams['l'],$this->Limiter) == true){
            $this->maxDataSets = $this->gridUrlParams['l'];
        }
        
        //Count all Rows
        $Sum = call_user_func_array(array($this->OrmClass , 'GetAllRows'), array($this->generateGridCondition()));
        
        //Calc Pages
        $Pages = ceil($Sum / $this->maxDataSets);

        //Check if Current Page is valid and set Current Page
        CobWeb::checkDep('Validator');
        if(isset($this->gridUrlParams['p'])){
            if(CobWeb::o('Validator')->isWithinRange($this->gridUrlParams['p'],1,$Pages)){
               $this->currentPage = $this->gridUrlParams['p'];
            }else{
                CobWeb::o('Console')->warning('CobWeb_Datagrid##render, Page is not in Range');
            }
        }
        
        
        //Calculate Limiter Starts
        $ViewFrom = $this->currentPage * $this->maxDataSets - $this->maxDataSets;
        
        list($OrderField,$OrderMethod) = preg_split('/ /',$this->generateOrderby());
        
        //Call Load here
        $Data = call_user_func_array(array($this->OrmClass , 'Load'), array($this->generateGridCondition() , $this->generateOrderby() , $ViewFrom.','.$this->maxDataSets));
        if($Data === false){
            $countData = 0;
        }else{
            $countData = count($Data);
        }
        
        //Pass Data to View
        $ViewData = array(	'GridData' => $Data , 
        					'GridSettings' => array('Class' => $this->OrmClass ,
        											'Fields' => $this->Fields ,
                                                    'OptionRoutes' => $this->optionRoutes,
        											'HtmlClass' => $this->class ,
        											'HtmlId' => $this->id,
                                                    'Limiter' => $this->Limiter,
                                                    'CurrentLimit' => $this->maxDataSets,
        
                                                    'Page' => $this->currentPage,
                                                    'Pages' => $Pages,
                                                    
                                                    'RepeatHeader' => $this->repeatHeader,
                                                    'ViewingFrom' => $ViewFrom,
                                                    'ViewingTo' => $this->currentPage * $this->maxDataSets - $this->maxDataSets + $countData,
                                                    'Sum' => $Sum,
                                                    'OrderField' => $OrderField,
                                                    'OrderMethod' => $OrderMethod
                                                    )
                            );
        //Enable Datagrid Javascripts:
            CobWeb::setSetting('view##jquery_datagrid',true);
            
        //Dataview:
        if ($DatagridView == false) {
            return CobWeb::o('View')->view('datagrid/default', $ViewData, true);
        } else {
            return CobWeb::o('View')->view('datagrid/default', $ViewData, false);
        }
    }
    private function generateGridCondition ()
    {
        //Return Modified Condition
        //Check Default Condition
        if ($this->OrmDefaultCondition instanceof CobWeb_OrmCondition) {
            return $this->OrmDefaultCondition;
        } else {
            return new CobWeb_OrmCondition();
        }
    }
    private function generateOrderby ()
    {
        //Check wheter we have a custom OrderBy
        
        if(isset($this->gridUrlParams['o']) || isset($this->gridUrlParams['seq'])){
            if(isset($this->gridUrlParams['o'])){
                if(isset($this->Fields[$this->gridUrlParams['o']])) 
                {
                    $orderByStr = $this->Fields[$this->gridUrlParams['o']]['id'];
                }else{
                    CobWeb::o('Console')->warning('CobWeb_Datagrid##generateOrderby: Field not Found');
                    $orderByStr = 'id';
                }
            }else{
                $orderByStr = 'id';
            }
            if(isset($this->gridUrlParams['seq'])){
                if(strtolower($this->gridUrlParams['seq'] == 'desc' || strtolower($this->gridUrlParams['seq']) == 'asc')){
                    $orderByStr .= ' '.strtoupper($this->gridUrlParams['seq']);
                }else{
                    CobWeb::o('Console')->warning('CobWeb_Datagrid##generateOrderby: Sequence not Supported');
                    $orderByStr = ' ASC';
                }
            }else{
                $orderByStr = ' ASC';
            }
            return $orderByStr;
        }else{
	        if ($this->getDefaultOrderBy() == null) {
	            //Rerturn Common Default Condition:
	            return 'id ASC';
	        } else {
	            return $this->getDefaultOrderBy();
	        }
        }
    }
    public function __destruct ()
    {//Unload
}
}
?>
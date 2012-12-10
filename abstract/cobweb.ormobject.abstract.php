<?php
function stripslashes_deep ($value)
{
    $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
    return $value;
}
abstract class CobWeb_OrmObject
{
    protected static $instance;
    //Default Values for every Object
    protected $data = array('id' => null , 'created' => null , 'updated' => null);
    protected $wasModified = false;
    private $affectedRows = 0;
    //Private empty Constructor to ensure class is initiated by CobWeb_OrmObject::CREATE or CobWeb_OrmObject::LOAD or CobWeb_OrmObject::LOADBYID($id)
    function __construct ()
    {
        return null;
    }
    public function __set ($member, $value)
    {
        //lowercase by default, we dont care about case sensitivity
        $member = strtolower($member);
        // The ID of the dataset is read-only by default
        if ($member == "id") {
            CobWeb::o('Console')->warning('CobWeb_OrmObject##ID is read-only by default!');
            return;
        }
        if ($member == "created") {
            CobWeb::o('Console')->warning('CobWeb_OrmObject##Created is read-only by default!');
            return;
        }
        if (! array_key_exists($member, $this->data) & isset($this->$member)) {
            CobWeb::o('Console')->warning('CobWeb_OrmObject## Undefined property ' . $member);
            return;
        }
        //Check wheter our child-class has a validation method for this property:
        if (method_exists(self::$instance, '_' . $member . 'Set')) {
            //Delegate to Class Setter Function
            return call_user_func_array(array(self::$instance , '_' . $member . 'Set'), array($value));
        } else {
            //direct set,if exists
            if (isset($this->data[$member])) {
                $this->wasModified = true;
                return ($this->data[$member] = $value);
            } elseif (isset($this->$member)) {
                $this->wasModified = true;
                return ($this->$member = $value);
            }
        }
    }
    public function __get ($member)
    {
        //lowercase by default, we dont care about case sensitivity
        $member = strtolower($member);
        if (! array_key_exists($member, $this->data) & isset($this->$member)) {
            CobWeb::o('Console')->warning('CobWeb_OrmObject## Undefined property ' . $member);
            return;
        }
        //Check wheter our child-class has a validation method for this property:
        if (method_exists(self::$instance, '_' . $member . 'Get')) {
            //Delegate to Class Setter Function
            return call_user_func_array(array(self::$instance , '_' . $member . 'Get'),array());
        } else {
            return $this->data[$member];
        }
    }
    //Create a new ORM-Object with default values
    static public function Create ()
    {
        $obj = get_called_class();
        self::$instance = new $obj();
        self::$instance->data['created'] = time();
        if (method_exists(self::$instance, 'onCreate')) {
            call_user_func_array(array(self::$instance , 'onCreate'), array());
        }
        return self::$instance;
    }
    //Delete an ORM Object from the Database
    public function delete ()
    {
        if ($this->isNew()) {
            CobWeb::o('Console')->warning('CobWeb_OrmObject##delete: Cannot delete a newly created Object. Use unset to remove it from memory.');
            return false;
        } else {
            $onDeleteResult = false;
            if (method_exists($this, 'onDelete')) {
                $onDeleteResult = call_user_func_array(array($this , 'onDelete'), array());
            }
            if ($onDeleteResult == false) {
                $DeletionCondition = new CobWeb_OrmCondition();
                $DeletionCondition->add('id', $this->data['id']);
                return CobWeb::o('Database')->deleteRecords($this->table, $DeletionCondition->toSQL());
            } else {
                return true;
            }
        }
    }
    /**
     * Loads an ORM Object using a CobWeb_OrmCondition Object
     * @param CobWeb_OrmCondition | bool $condition
     * @return unknown_type
     * @abstract , but not because of notice
     */
    public static function Load (CobWeb_OrmCondition $condition, $orderby = '', $limit = -1)
    {
        self::Create();
        if (method_exists(self::$instance, 'onLoad')) {
            $onLoadResult = call_user_func_array(array(self::$instance , 'onLoad'), array());
        } else {
            $onLoadResult = true;
        }
        if ($onLoadResult) {
            $loadResult = self::$instance->_loadCond($condition, $orderby, $limit);
            if ($loadResult === true) {
                if (method_exists(self::$instance, 'onAfterLoad')) {
                	call_user_func_array(array(self::$instance , 'onAfterLoad'), array());
                }
                return self::$instance;
            } elseif (is_array($loadResult)) {
            	$dataObjects = array();
                //Refill;
                foreach ($loadResult as $dataResult) {
                    self::$instance = null;
                    self::Create();
                    if (method_exists(self::$instance, 'onLoad')) {
                        call_user_func_array(array(self::$instance , 'onLoad'), array());
                    }
                    //Merge DB Data with Object Data (add ORM-Layer Fields)
                    self::$instance->data = array_replace($dataResult,self::$instance->data);
                    //Strip Database Slashes
                    self::$instance->data = stripslashes_deep(self::$instance->data);
                    if (method_exists(self::$instance, 'onAfterLoad')) {
                        call_user_func_array(array(self::$instance , 'onAfterLoad'), array());
                    }
                    $dataObjects[] = self::$instance;
                }
                return $dataObjects;
            } else {
                return false;
            }
        }
    }
    /* Will flatten ::Load Results to be always an array even with one Object in it */
    public static function ObjectsInArray($unknowResults){
        if(is_array($unknowResults)){
            return $unknowResults;
        }elseif(is_object($unknowResults)){
            return array(0 => $unknowResults);
        }
    }
    /**
     * 
     * @param $id
     * @return unknown_type
     */
    public static function LoadById ($id)
    {
        $DefaultIdCondition = new CobWeb_OrmCondition();
        $DefaultIdCondition->add('id', $id);
        return self::Load($DefaultIdCondition);
    }
    /**
     * 
     * @param CobWeb_OrmCondition $condition
     * @param $orderby
     * @param $limit
     * @return unknown_type
     */
    protected function _loadCond (CobWeb_OrmCondition $condition = null, $orderby = null, $limit = -1)
    {
        $result = CobWeb::o('Database')->selectRecords($this->table, array_keys($this->data), $condition->toSQL(), $orderby, $limit);
        //Evaluate whether there is one, more or zero Objects
        if (is_null($result)) {
        	$this->data = null;
            return false;
        } elseif (is_array(current($result))) {
            //Strip Database Slashes
            $result = stripslashes_deep($result);
            return $result;
        } elseif (is_array($result)) {
            //Strip Database Slashes
            $result = stripslashes_deep($result);
            $this->data = $result;
            return true;
        }
    }
    /**
     * 
     * @return unknown_type
     */
    public function save ()
    {
        
        //Update the 'updated'-Timestamp
        $this->data['updated'] = time();
        if ($this->isNew()) {
            //Insert data record:
            //Remove due to auto_increment:
            unset($this->data['id']);
            $onSaveResult = false;
            if (method_exists(self::$instance, 'onSave')) {
                $onSaveResult = call_user_func_array(array($this , 'onSave'), array());
            }
            if ($onSaveResult == false) {
                return CobWeb::o('Database')->insertRecord($this->table, $this->data);
            } else {
                return true;
            }
            return CobWeb::o('Database')->insert($this->table, $this->data);
        } else {
            $UpdateCondition = new CobWeb_OrmCondition();
            $UpdateCondition->add('id', $this->data['id']);
            //Update
            $onSaveResult = false;
            if (method_exists(self::$instance, 'onSave')) {
                $onSaveResult = call_user_func_array(array($this , 'onSave'), array());
            }
            if ($onSaveResult == false) {
                return CobWeb::o('Database')->updateRecords($this->table, $this->data, $UpdateCondition->toSQL());
            } else {
                return true;
            }
        }
        //Reset Modified
        $this->wasModified = false;
    }
    /**
     * 
     * @param $id
     * @return unknown_type
     */
    protected function saveAs ($id)
    {
        $UpdateCondition = new CobWeb_OrmCondition();
        $UpdateCondition->add('id', $id);
        $dataCopy = $this->data;
        $dataCopy['id'] = $id;
        //Update
        $onSaveResult = false;
        if (method_exists(self::$instance, 'onSave')) {
            $onSaveResult = call_user_func_array(array($this , 'onSave'), array());
        }
        if ($onSaveResult == false) {
            return CobWeb::o('Database')->updateRecords($this->table, $UpdateCondition->toSQL());
        } else {
            return true;
        }
    }
    /**
     * 
     * @return unknown_type
     */
    public function getDataKeys ()
    {
        return array_keys($this->data);
    }
    public function mergeData ($arrayValues)
    {
        if (is_array($arrayValues)) {
            foreach ($arrayValues as $key => $value) {
                    $this->{$key} = $value;
                
            }
            $this->wasModified = true;
            return null;
        }
    }
    //Returns whether the Object has been modified while loaded
    public function isModified ()
    {
        return $this->wasModified;
    }
    //Returns whether the Object is new
    public function isNew ()
    {
        if ($this->data['id'] == null) {
            return true;
        } else {
            return false;
        }
    }
    public function getMax ($field)
    {
        $result = CobWeb::o('Database')->result('SELECT MAX(' . $field . ') as maxVal FROM ' . cw_dbmysql_tblprefix . $this->table);
        if ($result) {
            return $result['maxVal'];
        } else {
            return false;
        }
    }
    public function getMin ($field)
    {
        $result = CobWeb::o('Database')->result('SELECT  MIN(' . $field . ') as minVal FROM ' . cw_dbmysql_tblprefix . $this->table);
        if ($result) {
            return $result['minVal'];
        } else {
            return false;
        }
    }
    public function getAvg ($field)
    {
        $result = CobWeb::o('Database')->result('SELECT AVG(' . $field . ') as avgVal FROM ' . cw_dbmysql_tblprefix . $this->table);
        if ($result) {
            return $result['avgVal'];
        } else {
            return false;
        }
    }
    //Returns a Number of Affected Rows, important for Datagrid
    public static function GetAllRows (CobWeb_OrmCondition $condition = null)
    {
    	self::Create();
        $loadResult = self::$instance->_GetAllRows($condition);
        return $loadResult;
    }
    protected function _GetAllRows (CobWeb_OrmCondition $condition = null)
    {
        if ($condition->toSQL() != '')
            $condition = ' WHERE ' . $condition->toSQL();
        else
            $condition = '';
        return CobWeb::o('Database')->count('SELECT id FROM ' . cw_dbmysql_tblprefix . $this->table . $condition);
    }
    public function __toArray(){
        return $this->data;
    }
}
?>
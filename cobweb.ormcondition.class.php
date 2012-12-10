<?php
//TODO Draw connection between ORM Condition and ORM Object
class CobWeb_OrmCondition
{
    public $arrayConditions = array();
    
    const CW_AND = 'AND';
    const CW_OR = 'OR';
    const CW_EQUAL = '=';
    const CW_UNEQUAL = '<>';
    const CW_LIKE = 'LIKE';
    const CW_GT = '>';
    const CW_LT = '<';
    const CW_GTE = '<=';
    const CW_LTE = '>=';
    
    public function add ($member, $value, $operator =CobWeb_OrmCondition::CW_EQUAL, $link = CobWeb_OrmCondition::CW_AND )
    {
        if($operator == null){
            $operator = CW_EQUAL;
        }
        if($operator == null){
            $operator = CW_EQUAL;
        }
        $this->arrayConditions[] = array('condition' => array($member , $value , $operator) , 'link' => $link);
    }
    public function addCondition (CobWeb_OrmCondition $condition, $link = CobWeb_OrmCondition::CW_AND)
    {
        $this->arrayConditions[] = array('condition' => $condition , 'link' => $link);
    }
    public function hasConditions(){
        return empty($this->arrayConditions);
    } 
    public function toSQL ()
    {
        $SQLString = null;
        $CondCounter = 0;
        foreach ($this->arrayConditions as $condition) {
            $CondCounter ++;
            if ($CondCounter > 1) {
                $SQLString .= $condition['link'].' ';
            }
            if (is_object($condition['condition'])) {
                $SQLString .= '(' . $condition->toSQL() . ')';
            }else{
                $SQLString .= ' ' . $condition['condition'][0] . ' '.$condition['condition'][2]." '".$condition['condition'][1]."' ";
            }
        }
        return $SQLString;
    }
}
?>
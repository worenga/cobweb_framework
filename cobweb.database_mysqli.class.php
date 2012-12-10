<?php
if (! defined('cw_inc')) {
    echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
    exit(1);
}
class CobWeb_database_mysqli implements CobWeb_DBMS
{
    private $mysqli;
    private static $querycount = 0;
    private $transactionmode = false;
    private $transactionerror = false;
    /**
     * @return Numbers of Querys executed
     */
    public static function getQuerycount ()
    {
        return $this->querycount;
    }
    public function __construct ()
    {
        $this->mysqli = new mysqli(cw_dbmysql_host, cw_dbmysql_user, cw_dbmysql_pw, cw_dbmysql_db);
        if (mysqli_connect_error()) {
            $Console = CobWeb::getObject('Console');
            $Console->criticalError('Cannot Connect to MySQL Server');
        }
    }
    public function __destruct ()
    {
        @$this->mysqli->close();
    }
    public function query ($qry)
    {
        $this->querycount ++;
        $result = @$this->mysqli->query($qry);
        if ($result == false && $this->transactionmode == false) {
            $errormessage = 'Cobweb Database(mysqli): Error Occurred in Query No.' . $this->querycount . cw_system_crlf . ' Mysql sais: ' . $this->mysqli->error . cw_system_crlf;
            if (cw_debugmode) {
                $errormessage .= 'Query was:  ' . $qry . cw_system_crlf;
            }
            trigger_error($errormessage, E_USER_ERROR);
        } elseif ($result == false && $this->transactonmode == true) {
            $this->transactionerror = true;
        } else {
            return $result;
        }
    }
    public function count ($qry)
    {
        $result = $this->query($qry);
        $count = $result->num_rows;
        $result->close();
        return $count;
    }
    public function result ($qry)
    {
        $resultset = array();
        $result = $this->query($qry);
        if ($result instanceof MySQLi_Result) {
            $results = 0;
            while ($row = $result->fetch_assoc()) {
                $results ++;
                $resultset[] = $row;
            }
            if ($results > 1) {
                return $resultset;
            } elseif ($results == 1) {
                return $resultset[0];
            }
            $result->close();
        } else {
            return false;
        }
    }
    public function getLastId ()
    {
        return $this->mysqli->insert_id;
    }
    public function affectedRows ()
    {
        return $this->mysqli->affected_rows;
    }
    public function beginTransaction ()
    {
        $this->transactionmode = true;
        $this->mysqli->autocommit(FALSE);
    }
    public function performTransaction ()
    {
        if ($this->transactionerror == true) {
            trigger_error('Coweb Database(mysqli): Cannot perform transaction quenue, because errors occurred in statements');
            return false;
        } else {
            $this->mysqli->commit();
            $this->mysqli->autocommit(TRUE);
        }
    }
    public function rollbackTransaction ()
    {
        $this->mysqli->rollback();
        $this->mysqli->autocommit(TRUE);
        $this->transactionmode = false;
    }
    public function truncate ($table)
    {
        $querystr = 'TRUNCATE TABLE ' . cw_dbmysql_tblprefix . $table;
        $statement = $this->mysqli->prepare($querystr);
        $statement->execute();
        $affected = $statement->affected_rows;
        $statement->close();
        return $affected;
    }
    public function selectRecords ($table, $fields, $condition = "", $orderby = "" , $limit = -1)
    {
        $querystr = 'SELECT ';
        @$sanatizedFields = array_walk($fields, 'CobWeb_Paramedic::sql');
        $querystr .= join(',', $fields);
        $querystr .= ' FROM ' . cw_dbmysql_tblprefix . CobWeb_Paramedic::sql($table);
        if ($condition != '') {
            $querystr .= ' WHERE ' . $condition;
        }
        if($orderby != ''){
            $querystr .= ' ORDER BY '.$orderby;
        }
        if ($limit != -1) {
            $querystr .= ' LIMIT ' . $limit;
        }
        return $this->result($querystr);
    }
    public function updateRecords ($table, $fields, $condition = '', $limit = -1)
    {
        
        $querystr = 'UPDATE ' . cw_dbmysql_tblprefix . $table . ' SET' . cw_system_crlf;
        $typestr = '';
        foreach ($fields as $name => $value) {
            $querystr .= "`$name` = ?, ";
            $values[] = $value;
            if (is_integer($value)) {
                $typestr .= 'i';
            } elseif (is_double($value)) {
                $typestr .= 'd';
            } elseif (is_string($value)) {
                $typestr .= 's';
            } else {
                $typestr .= 's';
            }
        }
        $querystr = substr($querystr, 0, - 2);
        if ($condition != '') {
            $querystr .= ' WHERE ' . $condition;
        }
        if ($limit != - 1) {
            $querystr .= ' LIMIT ' . $limit;
        }
        $statement = $this->mysqli->prepare($querystr);
        $params = array_merge(array($typestr), $values);
        $tmp = array();
        foreach($params as $key => $value) $tmp[$key] = &$params[$key];
        call_user_func_array(array($statement, 'bind_param'), $tmp);
        $statement->execute();
        $affected = $statement->affected_rows;
        $statement->close();
        return $affected;
    }
    public function deleteRecords ($table, $condition = '', $limit = -1)
    {
        $querystr = 'DELETE FROM ' . cw_dbmysql_tblprefix . $table;
        if ($condition != '') {
            $querystr .= ' WHERE ' . $condition;
        }
        if ($limit != - 1) {
            $querystr .= ' LIMIT ' . $limit;
        }
        $this->query($querystr);
        return $this->mysqli->affected_rows;
    }
    public function insertRecord ($table, $fields)
    {
        $querystr = 'INSERT INTO ' . cw_dbmysql_tblprefix . $table . cw_system_crlf;
        $querystr_n = '( ';
        $querystr_v = ' ) VALUES ( ';
        $typestr = '';
        foreach ($fields as $name => $value) {
            $value = $this->mysqli->real_escape_string($value);
            $querystr_n .= "`$name`,";
            $querystr_v .= '?,';
            $values[] = $value;
            
            if (is_integer($value)) {
                $typestr .= 'i';
            } elseif (is_double($value)) {
                $typestr .= 'd';
            } elseif (is_string($value)) {
                $typestr .= 's';
            } else {
                $typestr .= 's';
            }
        }
        $querystr_n = substr($querystr_n, 0, - 1);
        $querystr_v = substr($querystr_v, 0, - 1);
        $querystr .= $querystr_n . $querystr_v . ')';
        $statement = $this->mysqli->prepare($querystr);
        
        $params = array_merge(array($typestr), $values);
        $tmp = array();
        foreach($params as $key => $value) $tmp[$key] = &$params[$key];
        call_user_func_array(array($statement, 'bind_param'), $tmp);
        
        $statement->execute();
        $affected = $statement->affected_rows;
        $statement->close();
        return $affected;
    }
}
?>
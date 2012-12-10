<?php
if (! defined('cw_inc')) {
    echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
    exit(1);
}
define('CW_Interfaces_Loaded', true);
/**
 * Interface File for Cobweb 2.0
 * @delegate_class
 */
interface CobWeb_DBMS
{
    public function __construct ();
    public function __destruct ();
    public function query ($qry);
    public function count ($qry);
    public function result ($qry);
    public function getLastId ();
    public function affectedRows ();
    public function beginTransaction ();
    public function performTransaction ();
    public function rollbackTransaction ();
    public function truncate($table);
    public function updateRecords ($table, $fields, $condition = '', $limit = -1);
    public function deleteRecords ($table, $condition = '', $limit = -1);
    public function insertRecord ($table, $fields);
}
?>
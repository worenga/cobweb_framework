<?php
/**
 * ************************************************************************************************
 *   _____      _ __          __  _         ______                                           _    
 *  / ____|    | |\ \        / / | |       |  ____|         Rapid Web Application           | |   
 * | |     ___ | |_\ \  /\  / /__| |__     | |__ _ __ __ _ _ __ ___   _____      _____  _ __| | __
 * | |    / _ \| '_ \ \/  \/ / _ \ '_ \    |  __| '__/ _` | '_ ` _ \ / _ \ \ /\ / / _ \| '__| |/ /
 * | |___| (_) | |_) \  /\  /  __/ |_) |   | |  | | | (_| | | | | | |  __/\ V  V / (_) | |  |   < 
 *  \_____\___/|_.__/ \/  \/ \___|_.__/    |_|  |_|  \__,_|_| |_| |_|\___| \_/\_/ \___/|_|  |_|\_\
 * ************************************************************************************************ 
 * Filename: cobweb.compatibility.lib.php
 * Author: Benedikt Wolters (mightyuhu@php.net)
 * License:  MIT License
 * Version: $Id;
 * ************************************************************************************************
 * Compatibility Library
 * This file hacks or fixes php combability issues
 * ************************************************************************************************
 */ 

if(!defined('cw_inc')){
    echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
    exit(1);
}

/**
 * Cobweb Database Management System
 * Delegates SQL-Querys to the DBMS
 * @delegate_class
 */

/* ========================================
 * CODEPART: DBMS Preloader
 * ========================================
 */	

function Database_loadDependencies() {
    if(!defined('CW_Interfaces_Loaded')){
        //Load Interface
    	@require_once (cw_framework_dir . 'cobweb.interfaces.' . cw_phpex);
    }
	
	//Load selected DBMS-Class
	@require_once (cw_framework_dir . 'cobweb.database_' . cw_dbms . '.class.' . cw_phpex);
}

/* ========================================
 * CODEPART: Class Cobweb_Database
 * ========================================
 */	
class CobWeb_Database implements CobWeb_DBMS {
	private $dbms_handler;
	private $caching;
	private $query_cache;
	private $transaction_mode;
	
	public function __construct() {
		switch (cw_dbms) {
			case 'mysqli' :
				$this->dbms_handler = new CobWeb_database_mysqli ( );
				break;
		}
	}
	public function __destruct() {
		unset ( $this->dbms_handler );
	}
	
	/* ========================================
	 * CODEPART:Core Database Functions
	 * ========================================
	 */	
	//Querys Database
	public function query($qry) {
		return $this->dbms_handler->query ( $qry );
	}
	
	//Count Data
	public function count($qry) {
		return $this->dbms_handler->count ( $qry );
	}
	
	//Assoc Array
	public function result($qry) {
		return $this->dbms_handler->result ( $qry );
	}
	
	public function getQuerycount(){
	    return $this->dbms_handler->getQuerycount();
	}
	
	public function getLastId() {
		return $this->dbms_handler->getLastId ();
	}
	
	public function affectedRows() {
		return $this->dbms_handler->affectedRows ();
	}
	
	public function beginTransaction() {
		return $this->dbms_handler->beginTransaction ();
	}
	
	public function performTransaction() {
		return $this->dbms_handler->performTransaction ();
	}
	
	public function rollbackTransaction() {
		return $this->dbms_handler->rollbackTransaction ();
	}
	/* ========================================
	 * CODEPART: Database abstraction Functions
	 * ========================================
	 */	
	
	public function truncate($table){
	    return $this->dbms_handler->truncate($table);
	}
	public function selectRecords($table, $fields, $condition = '', $orderby = '', $limit = '') {
		return $this->dbms_handler->selectRecords ( $table, $fields, $condition, $orderby, $limit );
	} //Return Rows affected
	
	public function updateRecords($table, $fields, $condition = '', $limit = -1) {
		return $this->dbms_handler->updateRecords ( $table, $fields, $condition, $limit );
	} //Return Rows affected
		public function deleteRecords($table, $condition = '', $limit = -1) {
		return $this->dbms_handler->deleteRecords ( $table, $condition, $limit );
	} //Return Rows affected
	public function insertRecord($table, $fields) {
		return $this->dbms_handler->insertRecord ( $table, $fields );
	}
	
	public function update($table, $fields, $condition = '', $limit = -1) {
		return $this->updateRecords ( $table, $fields, $condition, $limit );
	}
	public function delete($table, $condition = '', $limit = -1) {
		return $this->deleteRecords ( $table, $condition, $limit );
	}
	public function insert($table, $fields) {
		return $this->insertRecord ( $table, $fields );
	}

	/* ========================================
	 * CODEPART: Method Aliases
	 * ========================================
	 */	
	public function f($qry) {
		return $this->result ( $qry );
	}
	public function q($qry) {
		return $this->query ( $qry );
	}
}
?>
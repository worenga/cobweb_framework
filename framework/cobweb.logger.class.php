<?php
class CobWeb_Logger {
	private $src;
	private $logid;
	
	public function __construct($logId, $logType) {
		if ($logType == cw_log_db || $logType == cw_log_file) {
			$this->type = $logType;
			if ($logType == cw_log_file && file_exists ( $logId ) && is_writeable ( $logId )) {
				$this->type = $logType;
			} else {
				CobWeb::o ( 'Console' )->warning ( 'CobWeb_Logger: Unknown Resource LogType' );
			}
		}
	}
	public function event($text) {
	
	}
	
	private function write($message) {
		if ($this->src == cw_log_db) {
			CobWeb::o ( 'Database' )->insertRecord ( $this->logid, array ('timestamp' => time (), 'message' => $message ) );
		} elseif (file_exists ( $this->src )) {
			if (is_writeable ( $this->src )) {
				$msg = '<event><time>' . date ( 'r', time () ) . '</time><description>' . $message . '</description></event>';
				@touch($this->src);
				file_put_contents ( $this->logid, $msg, FILE_APPEND );
			} else {
				CobWeb::o ( 'Console' )->warning ( 'CobWeb_Logger: ' . $this->src . ' is not writeable' );
			}
		} else {
			CobWeb::o ( 'Console' )->warning ( 'CobWeb_Logger: ' . $this->src . ' was not found' );
		}
	}
	/**
	 * 
	 */
	function __destruct() {
	//TODO - Insert your code here
	}
}

?>
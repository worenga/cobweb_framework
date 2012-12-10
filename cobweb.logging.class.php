<?php
/**
 * 
 */
/**
 * @Todo: Auto Delete Function, Priorities, User, Notifier
 */
class CobWeb_Logging
{
    private $src;
    private $type;
    private $entrys = array();
    public function __construct ($logId, $logType)
    {
        $this->src = $logId;
        if ($logType == cw_log_db || $logType == cw_log_file) {
            $this->type = $logType;
            if ($logType == cw_log_file && file_exists($logId) && is_writeable($logId)) {
                $this->type = $logType;
            } else {
                CobWeb::o('Console')->warning('CobWeb_Logging##__construct: Unknown Resource LogType');
            }
        }
    }
    /**
     * Currently used as delegate for later uses
     * @param $text
     * @return unknown_type
     */
    public function event ($text) 
    {
        $this->entrys[] = $text;
    }
    public function clearLog ()
    {
        if ($this->src == cw_log_db) {
            CobWeb::o('Database')->truncate($this->src);
        } elseif (file_exists($this->src)) {
            //Truncate the complete Log
            $fh = fopen($this->src, 'w');
            fclose($fh);
        }
    }
    public function write ()
    {
        if (! empty($this->entrys)) {
            if ($this->src == cw_log_db) {
                foreach ($this->entrys as $message) {
                    CobWeb::o('Database')->insertRecord($this->src, array('timestamp' => time() , 'message' => $message));
                }
            } elseif (file_exists($this->src)) {
                if (is_writeable($this->src)) {
                    $msg = '';
                    foreach ($this->entrys as $message) {
                        $msg .= '<event><time>' . date('r', time()) . '</time><description>' . $message . '</description></event>'.cw_system_crlf;
                    }
                    @touch($this->src);
                    file_put_contents($this->src, $msg, FILE_APPEND);
                } else {
                    CobWeb::o('Console')->warning('CobWeb_Logging: ' . $this->src . ' is not writeable');
                }
            } else {
                CobWeb::o('Console')->warning('CobWeb_Logger: ' . $this->src . ' was not found');
            }
        }
    }
}
?>
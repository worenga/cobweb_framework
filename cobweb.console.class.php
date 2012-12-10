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
 * Filename: cobweb.console.class.php
 * Author: Benedikt Wolters (mightyuhu@php.net)
 * License:  MIT License
 * Version: $Id;
 * ************************************************************************************************
 * CobWeb Console
 * Console is the Pipe for framework messages to the Debugger (User / Writer)
 * Console displaying and logging can be turned off or on in the configuration
 * ************************************************************************************************
 */ 

if (! defined('cw_inc')) {
    echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
    exit(1);
}
class CobWeb_Console
{
    const cw_log_event = 0;
    const cw_log_notice = 1;
    const cw_log_warning = 2;
    const cw_log_criticalerror = 3;
    const cw_log_mt_stamp = 4;
    private $console;
    private $instanced;
    public function __construct ()
    {
        $this->instanced = microtime(true);
    }
    public function __destruct ()
    {
        /*
         * @todo Console Logger
         */
        if (CobWeb::getDebugMode() == true && count($this->console) > 0) {
            if (cw_debugmode_showconsole)
                $this->display();
        }
    }
    private function _log ($message, $mode)
    {
        //Log Some Event
        $this->console[] = array('message' => $message , 'mode' => $mode , 'time' => microtime(true));
    }
    public function get ()
    {
        return $this->console;
    }
    public function setTimestamp ()
    {
        $this->_log(microtime(true), self::cw_log_mt_stamp);
    }
    //System Event
    public function log ($message)
    {
        $this->_log($message, self::cw_log_event);
    }
    //System Message for Debugging purposes
    public function notice ($message)
    {
        $this->_log($message, self::cw_log_notice);
    }
    //System Warning
    public function warning ($message)
    {
        $this->_log($message, self::cw_log_warning);
    }
    //Critical Error
    public function criticalError ($message)
    {
        $this->_log($message, self::cw_log_criticalerror);
    }
    public function display ()
    {
        $lasttimestamp = null;
        $lastlogtimestamp = null;
        $cCopy = array_reverse($this->console);
        echo '<h1 style="width:600px;padding:2px;border-bottom:2px solid #EFAD3B; font-size:12px;color:#EFAD3B;font-weight:bold;font-family:Verdana,Arial,Helvetica,sans-serif;line-height:20px;">Cobweb Console Log</h1>';
        echo '<ul style="list-style-type:none;list-style-image:none;padding:5px;background-color:#000;margin:0;padding-top:3px;padding-bottom:3px;font-size:10px;">';
        if (is_array($this->console)) {
            $i = count($this->console) + 1;
            foreach ($cCopy as $entry) {
                $i --;
                if ($entry['mode'] != self::cw_log_mt_stamp) {
                    if ($lastlogtimestamp == null) {
                        $lastlogtimestamp = $this->instanced;
                    }
                    $modeColor = '';
                    $modeType = 'undefined';
                    echo '<li style="color:#fff;font-family:Consolas,"Andale Mono WT","Andale Mono","Lucida Console",Monaco,"Courier New",Courier,monospace;font-size:12px;color:#fff;background-color:#000;">';
                    switch ($entry['mode']) {
                        case self::cw_log_event:
                            $modeColor = '#207CAF';
                            $modeType = 'Application Event';
                            break;
                        case self::cw_log_notice:
                            $modeColor = '#077F2E';
                            $modeType = 'Application Notice';
                            break;
                        case self::cw_log_warning:
                            $modeColor = '#CF4040';
                            $modeType = 'Warning';
                            break;
                        case self::cw_log_criticalerror:
                            $modeColor = '#FF3F6B';
                            $modeType = 'Critical Error';
                            break;
                        default:
                            $modeColor = '#fff';
                            break;
                    }
                    $ts_diff1 = round($entry['time'] - $lastlogtimestamp, 3);
                    $ts_diff2 = round($entry['time'] - $this->instanced, 3);
                    $lastlogtimestamp = $entry['time'];
                    echo '<span style="font-weight:bold;width:150px;display:block;float:left;color:' . $modeColor . '">' . $i . '. ' . $modeType . ': ' . '</span> ' . $entry['message'] . cw_system_crlf . '<span style="color:#AFAFAF">- Timestamp: <span style="color:#EFB34A">+' . $ts_diff1 . ' (+' . $ts_diff2 . ')</span></span>';
                    echo '</li>';
                } else {
                    if ($lasttimestamp == null) {
                        $lasttimestamp = $this->instanced;
                    }
                    $ts_diff1 = round($entry['time'] - $lasttimestamp, 3);
                    $ts_diff2 = round($entry['time'] - $this->instanced, 3);
                    $lasttimestamp = $entry['time'];
                    echo '<li style="color:#fff;font-family:Consolas,"Andale Mono WT","Andale Mono","Lucida Console",Monaco,"Courier New",Courier,monospace;font-size:12px;color:#fff;background-color:#000;">';
                    echo '<span style="font-weight:bold;width:150px;display:block;float:left;color:' . $modeColor . '">' . $i . '. Console Timestamp : ' . '</span><span style="color:#AFAFAF">- Timestamp: <span style="color:#EFB34A">+' . $ts_diff1 . '</span>(+' . $ts_diff2 . ')</span>';
                    echo '</li>';
                }
            }
        } else {
            echo '<li style="font-size:12px;color:#000;font-weight:bold;font-family:Verdana,Arial,Helvetica,sans-serif;line-height:20px;">Console is empty</li>';
        }
        echo '</ul>';
    }
    /**
     * @todo pass to logger
     */
    public function getConsoleEntrys ()
    {
        if (is_array($this->console))
            return array_reverse($this->console);
        else
            return array();
    }
    public function clean ()
    {
        $this->console = array();
    }
}
?>

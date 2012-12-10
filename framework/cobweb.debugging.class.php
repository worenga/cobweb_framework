<?php
//TODO: User Node Builder Here
if (! defined('cw_inc')) {
    echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
    exit(1);
}
function CW_Error_Handler ($errcode, $errmsg, $errfile, $errline)
{
    global $cw;
    if($errcode != E_STRICT){
    static $firstcall = true;
    if (cw_debugmode == true) {
        switch ($errcode) {
            case E_ERROR:
                $errorbgcolor = '#FFCFCF';
                $errorbordercolor = '#AF1515';
                $errtype = "Error";
                break;
            case E_WARNING:
                $errtype = "Warning";
                $errorbgcolor = '#AFF9FF';
                $errorbordercolor = '#232C8F';
                break;
            case E_PARSE:
                $errtype = "Parse Error";
                $errorbgcolor = '#FFFBCF';
                $errorbordercolor = '#FFB93F';
                break;
            case E_NOTICE:
                $errtype = "Notice";
                $errorbgcolor = '#FFFBCF';
                $errorbordercolor = '#FFB93F';
                break;
            case E_CORE_ERROR:
                $errorbgcolor = '#FFCFCF';
                $errorbordercolor = '#AF1515';
                $errtype = "Core Error";
                break;
            case E_CORE_WARNING:
                $errorbgcolor = '#FFCFCF';
                $errorbordercolor = '#AF1515';
                $errtype = "Core Warning";
                break;
            case E_COMPILE_ERROR:
                $errorbgcolor = '#FFCFCF';
                $errorbordercolor = '#AF1515';
                $errtype = "Compile Error";
                break;
            case E_COMPILE_WARNING:
                $errorbgcolor = '#FFCFCF';
                $errorbordercolor = '#AF1515';
                $errtype = "Compile Warning";
                break;
            case E_USER_ERROR:
                $errtype = "User Error";
                $errorbgcolor = '#FFFBCF';
                $errorbordercolor = '#FFB93F';
                break;
            case E_USER_WARNING:
                $errorbgcolor = '#FFFBCF';
                $errorbordercolor = '#FFB93F';
                $errtype = "User Warning";
                break;
            case E_USER_NOTICE:
                $errorbgcolor = '#FFFBCF';
                $errorbordercolor = '#FFB93F';
                $errtype = "User Notice";
                break;
            case E_STRICT:
                $errorbgcolor = '#FFFBCF';
                $errorbordercolor = '#FFB93F';
                $errtype = "Strict Notice";
                break;
            case E_RECOVERABLE_ERROR:
                $errorbgcolor = '#FFCFCF';
                $errorbordercolor = '#AF1515';
                $errtype = "Recoverable Error";
                break;
            default:
                $errorbgcolor = '#FFCFCF';
                $errorbordercolor = '#AF1515';
                $errtype = "Unknown error ($errno)";
                break;
        }
        if ($firstcall) {
            echo '<h1 style="width:600px;padding:5px;border-bottom:2px solid #5BBF3B; font-size:14px;color:#5BBF3B;font-weight:bold;font-family:Verdana,Arial,Helvetica,sans-serif;line-height:20px;">CobWeb Framework has encountered script errors:</h1>';
            echo '<div class="cw_error" style="color:#fff;background-color:#000;font-size:10px;font-family:\'Consolas\',\'Andale Mono WT\',\'Andale Mono\',\'Lucida Console\',Monaco,\'Courier New\',Courier,monospace;border:1px solid #DDDF37;padding:5px;">';
            CobWeb_Debugging::backtrace();
            echo '</div>';
            echo '<h1 style="width:600px;padding:5px;border-bottom:2px solid #5BBF3B; font-size:14px;color:#5BBF3B;font-weight:bold;font-family:Verdana,Arial,Helvetica,sans-serif;line-height:20px;">Errors:</h1>';
            $firstcall = false;
        }
        echo '<div class="cw_error" style="font-size:12px;font-family:Verdana,Arial,Helvetica,sans-serif;border:1px solid ' . $errorbordercolor . ';background-color:' . $errorbgcolor . ';padding:5px;">';
        echo '<span style="font-weight:bold;">' . $errfile . ' @ Line ' . $errline . '</span><br/><span style="font-weight:bold;">' . $errtype . '(' . $errcode . ')</span> ' . $errmsg . cw_system_crlf . '<br/>';
        echo '</div>';
    }
    }
    return true;
}
class CobWeb_Debugging
{
    public function dump ($mixed_var)
    {
        echo '<div style="background-color:white;color:#000;text-align:left;">';
        echo '<pre style="font-size:10px;font-family:Consolas,\'Andale Mono WT\',\'Andale Mono\',\'Lucida Console\',Monaco,\'Courier New\',Courier,monospace;">' . cw_system_crlf;
        var_dump($mixed_var);
        echo '</pre>';
        echo '</div>';
    }
    private function line ($str)
    {
        echo '<div style="background-color:white;color:#000;text-align:left;">';
        echo "<br/>----- $str ------ <br/>";
        echo '</div>';
    }
    public function backtrace ()
    {
    	$raw = debug_backtrace();
        $raw = array_slice(($raw),2); //Strip last 2 Functions because they are not interesting!        foreach ($raw as $entry) {
        foreach ($raw as $entry) {
            $output .= '<span style="color:rgb(207, 64, 64);">File:</span> <span style="color:#F1EDED;">' . dirname($entry['file']).'\\</span><span style="color:#EF9595">' . basename($entry['file']).'</span>' . ' (Line: <span style="color:#6FD9FF">' . $entry['line'] . "</span>) " . cw_system_crlf;
            if ($entry['class']) {
                $output .= '<span style="color:#6FD9FF;">Class:</span> ' . $entry['class'] . cw_system_crlf;
            }
            $output .= '<span style="color:#EFCA68;">Function:</span> ' . $entry['function'] . cw_system_crlf;
            if (is_array($entry['args'])) {
                $output .= '<span style="color:#5BBF3B;">Args:</span>' . var_export($entry['args'],true) . cw_system_crlf;
            }
            $output .=  cw_system_crlf;
        }
        echo '<div style="font-size:10px;font-family:Consolas,\'Andale Mono WT\',\'Andale Mono\',\'Lucida Console\',Monaco,\'Courier New\',Courier,monospace;">' . cw_system_crlf;
        echo nl2br($output);
        echo '</div>';
    }
    public function gpcdump ()
    {
        self::line('$_GET:');
        self::dump($_GET);
        self::line('$_POST:');
        self::dump($_POST);
        self::line('$_COOKIE:');
        self::dump($_COOKIE);
    }
    public function __construct ()
    {
        set_error_handler("CW_Error_Handler");
        if (cw_debugmode == true) {
            //	error_reporting ( E_ALL ^ E_NOTICE );
        }
    }
}
?>

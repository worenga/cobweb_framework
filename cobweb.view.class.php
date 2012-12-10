<?php
if (! defined('cw_inc')) {
    echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
    exit(1);
}
class CobWeb_View
{
    private $data = null;
    /**
     * 
     */
    public function __construct ()
    {}
    /**
     * Basic Private View Handling Functions:
     */
    private function getViewFile ($viewName)
    {
        return cw_view_dir . strtolower($viewName) . '.' . cw_phpex;
    }
    private function evalView ($viewName)
    {
        $viewContent = file_get_contents($this->getViewFile($viewName));
        if (eval("?>" . $viewContent) !== false) {
            return true;
        } else {
            CobWeb::o('Console')->warning('CobWeb_View##evalView: View "' . $viewName . '"(' . $this->getViewFile($viewName) . ') has Syntax Errors.');
            return false;
        }
    }
    public function view ($viewName, $viewData = null, $buffer = false)
    {
        if(is_array($this->data) && is_array($viewData))
            $this->data = array_merge($this->data,$viewData);
        elseif($this->data == null)
            $this->data = $viewData;
        else
            CobWeb::o('Console')->warning('CobWeb_View##view Type mismatch in viewData');
            
        ob_start(); //Output Buffering for Speed, And Postprocessing
        if ($this->checkView($viewName)) {
            $this->evalView($viewName);
            $viewReturn = ob_get_contents();
            ob_end_clean();
            
            
            if ($buffer == true) {
                return $viewReturn;
            } else {
                echo $viewReturn;
                return true;
            }
        } else {
            CobWeb::o('Console')->warning('Unable to Load View: "' . $viewName . '"(' . $this->getViewFile($viewName) . ')');
            @ob_end_clean();
            return false;
        }
    }
    public function setCommonViewData($data){
        if(!is_array($data)){
            CobWeb::o('Console')->warning('CobWeb_View##setCommonViewData: $data has to be an array!');
        }else{
            $this->data = $data;
        }
    }
    
    private function checkView ($viewName)
    {
        if (file_exists($this->getViewFile($viewName)) == true && is_readable($this->getViewFile($viewName))) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * View Helper Functions:
     */
    private function xmlHead ()
    {}
    private function htmlOpen ()
    {
        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . cw_system_crlf;
        echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">' . cw_system_crlf;
    }
    private function htmlBody ()
    {
        echo '</head>' . cw_system_crlf;
        echo '<body>' . cw_system_crlf;
    }
    private function htmlClose ()
    {
        echo '</body>' . cw_system_crlf;
        echo '</html>' . cw_system_crlf;
    }
    private function htmlHead ($title, $encoding = null)
    {
        if ($encoding == null) {
            $encoding = cw_app_encoding;
        }
        echo '<head>' . cw_system_crlf;
        echo '<title>' . $title . '</title>';
        echo '<meta http-equiv="content-type" content="text/html; charset=' . $encoding . '" />' . cw_system_crlf;
    }
    private function htmlIncludeJs ($filename)
    {
        if (file_exists(cw_js_dir . $filename)) {
            echo '<script type="text/javascript" src="' . "/" . cw_webroot . cw_js_dir . $filename . '"></script>'. cw_system_crlf;
        } else {
            CobWeb::o('Console')->warning('CobWeb_View##htmlIncludeJs: Unable to find: ' . "/" . cw_webroot . cw_js_dir . $filename);
        }
    }
    private function htmlIncludeCss ($filename)
    {
        if (file_exists(cw_css_dir . $filename)) {
            echo '<link rel="stylesheet" href="' . "/" . cw_webroot . cw_css_dir . $filename . '" type="text/css"/>'. cw_system_crlf; 
        } else {
            CobWeb::o('Console')->warning('CobWeb_View##htmlIncludeCss: Unable to find: ' . "/" . cw_webroot . cw_css_dir . $filename);
        }
    }
    private function htmlIncludeJquery (){
        $this->htmlIncludeJs(cw_jquery_path);
    }
    private function htmlIncludeRequiredScripts(){
        $settings = CobWeb::getAllSettings();
        if(isset($settings['view##jquery_captcha_regeneration']) && $settings['view##jquery_captcha_regeneration'] == true){
            $this->htmlIncludeJs('cw.form.behavior.js');
        }
        if(isset($settings['view##jquery_datagrid'])){
            $this->htmlIncludeJs('cw.datagrid.behavior.js');
        }
    }
    private function rssHead ($channelTitle, $channellDesc, $channelLang, $channelLink, $encoding = null)
    {
        if ($encoding == null) {
            $encoding = cw_app_encoding;
        }
        header("Content-Type: application/xml; charset=$encoding");
        echo '<?xml version="1.0" encoding="' . $encoding . '"?>' . cw_system_crlf;
        echo '    <rss version="2.0">' . cw_system_crlf;
        echo '<channel>' . cw_system_crlf;
        echo '<title>' . $channelTitle . '</title>' . cw_system_crlf;
        echo '<description>' . $channellDesc . '</description>' . cw_system_crlf;
        echo '<language>' . $channelLang . '</language>' . cw_system_crlf;
        echo '<link>' . $channelLink . '</link>' . cw_system_crlf;
        echo '<lastBuildDate>' . date('r') . '</lastBuildDate>' . cw_system_crlf;
    }
    private function rssClose ()
    {
        echo '</rss>';
    }
    private function rssItem ($title, $desc, $link, $timestamp = null)
    {
        if ($timestamp === null) {
            $timestamp = time();
        }
        echo '<item>' . cw_system_crlf;
        echo '<title>' . $title . '</title>' . cw_system_crlf;
        echo '<link>' . $link . '</link>' . cw_system_crlf;
        echo '<description><![CDATA[' . $desc . ']]></description>' . cw_system_crlf;
        echo '<pubDate>' . date("r", $timestamp) . '</pubDate>' . cw_system_crlf;
        echo '</item>' . cw_system_crlf;
    }
    private function toJSON ($mixedData)
    {
        echo json_encode($mixedData);
    }
    private function toLI ($arrayData)
    {
        foreach ($arrayData as $key => $value) {
            echo '<li>';
            echo $key . '-' . $value;
            echo '</li>' . cw_system_crlf;
        }
    }
    private function helper ($helperName)
    {
        if ($this->checkView($helperName)) {
            $this->evalView($helperName);
        } else {
            CobWeb::o('Console')->warning('CobWeb_View##helper: Unable to Load Helper: "' . $helperName . '"(' . $this->getViewFile($helperName) . ')');
        }
    }
    //Alias for CobWeb::o('Translator')
    private function t($strIdentifier){
        echo $this->tr($strIdentifier);
    }
    private function tr($strIdentifier){
        return CobWeb::o('Translator')->getBit($strIdentifier);
    }
    
    private function convertTimestamp ($timestamp)
    {
        echo date(cw_datestr, $timestamp);
    }
    public function __destruct ()
    {}
}
?>
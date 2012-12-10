<?php
/**
 * ************************************************************************************************
 *   _____      _ __          __  _         ______     RAPID WEB APPLICATION FRAMEWORK       _    
 *  / ____|    | |\ \        / / | |       |  ____|                                         | |   
 * | |     ___ | |_\ \  /\  / /__| |__     | |__ _ __ __ _ _ __ ___   _____      _____  _ __| | __
 * | |    / _ \| '_ \ \/  \/ / _ \ '_ \    |  __| '__/ _` | '_ ` _ \ / _ \ \ /\ / / _ \| '__| |/ /
 * | |___| (_) | |_) \  /\  /  __/ |_) |   | |  | | | (_| | | | | | |  __/\ V  V / (_) | |  |   < 
 *  \_____\___/|_.__/ \/  \/ \___|_.__/    |_|  |_|  \__,_|_| |_| |_|\___| \_/\_/ \___/|_|  |_|\_\
 * ************************************************************************************************ 
 * Filename: cobweb.diagnostic.class.php
 * Author: Benedikt Wolters (mightyuhu@php.net)
 * License:  MIT License
 * Version: $Id;
 * ************************************************************************************************
 * Description:
 * CobWeb Self Diagnostic Class, 
 * Checks:
 * 1. File Integrity of .htaccess Files
 * 2. PHP Security Settings
 * 3. Checks whether logs, cache are writeable
 * ************************************************************************************************
 */
if (! defined('cw_inc')) {
    echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
    exit(1);
}
class CobWeb_Diagnostics{
    private $checks = array();
    public function __construct(){
        //Self Diagnose
        if(!($this->diagSecurity() && $this->diagIntegrity() && $this->diagFileSystemPermissions())) {
            CobWeb::setSetting('Diagnostics',$this->checks);
            CobWeb::o('Console')->notice('CobWeb_Diagnostics: There are Problems with your CobWeb Installation!');
        }
        
    }
    
    private function diagSecurity(){
        $diagSecurity = array();
        if( ini_get('register_globals') ){
         $diagSecurity[] = 'register_globals';   
        }
        if(!ini_get('magic_quotes_gpc')){
         $diagSecurity[] = 'magic_quotes_gpc';   
        }
        if(!empty($diagSecurity)){
            $this->checks['security'] = $diagSecurity;
            return false;
        }else{
            return true;
        }
    }
    
    private function diagIntegrity(){
        $diagIntegrity = array();
        if(!file_exists('.htaccess')){
            $diagIntegrity[] = '.htaccess';
        }
        if(!file_exists(cw_framework_dir.'.htaccess')){
            $diagIntegrity[] = cw_framework_dir.'.htaccess';
        }
        if(!file_exists(cw_controller_dir. '.htaccess')){
            $diagIntegrity[] = cw_controller_dir.'.htaccess';
        }
        if(!file_exists(cw_language_dir. '.htaccess')){
            $diagIntegrity[] = cw_language_dir.'.htaccess';
        }
        if(!file_exists(cw_view_dir. '.htaccess')){
            $diagIntegrity[] = cw_view_dir.'.htaccess';
        }
        if(!file_exists(cw_cache_dir. '.htaccess')){
            $diagIntegrity[] = cw_cache_dir.'.htaccess';
        }
        if(!file_exists(cw_appclasses_dir. '.htaccess')){
            $diagIntegrity[] = cw_appclasses_dir.'.htaccess';
        }
        if(!file_exists(cw_log_dir. '.htaccess')){
            $diagIntegrity[] = cw_log_dir.'.htaccess';
        }
        if(!empty($diagIntegrity)){
            $this->checks['integrity'] = $diagIntegrity;
            return false;
        }else{
            return true;
        }
        
        
    }
    
    private function diagFileSystemPermissions(){
       $diagFileSystemPermissions = array();
       if(!is_writeable(cw_log_dir)){
           $diagFileSystemPermissions[] = 'log_dir';
       }
       if(!is_writeable(cw_cache_dir)){
           $diagFileSystemPermissions[] = 'cache_dir';
       }
       if(is_writeable('cw.configuration.'.cw_phpex)){
           $diagFileSystemPermissions[] = 'config';
       }

       if(!empty($diagFileSystemPermissions)){
           $this->checks['permission'] = $diagFileSystemPermissions;
           return false;
       }else{
            return true;
       }
    }
    
}
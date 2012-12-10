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
 * Filename: cw.configuration.php
 * Author: Benedikt Wolters (mightyuhu@php.net)
 * License:  MIT License
 * Version: $Id: cw.configuration.php 19 2009-09-12 13:40:15Z mightyuhu $;
 * ************************************************************************************************
 * WARNING: This file has to be read-only by your default filesystem
 * ************************************************************************************************
 * Application and Framework Settings are defined here:
 * 
 * cw_version - current CobWeb version
 * cw_app_encoding - encoding used throughout the application
 * cw_language - application default language
 * cw_datestr - application default date-format string for date()
 * cw_debugmode - triggers CobWeb's debugging mode
 * cw_debugmode_showconsole - show console log in Debug Mode
 * cw_error_reporting - error reporting
 * cw_offline - disables the whole application immediately
 * cw_space_root - path to the CobWeb installation
 * cw_phpex - extension of the php files^
 * cw_urlroot - website root directory (usually /)
 * cw_framework_dir - framework classes directory
 * cw_webroot - url path to CobWeb installation
 * cw_controller_dir - directory containing CobWeb controllers
 * cw_view_dir - directory containing CobWeb views
 * cw_appclasses_dir - directory containing the appclasses
 * cw_cache_dir - cache directory
 * cw_language_dir - translation directory
 * cw_log_dir - logfile dir
 * cw_css_dir - css files dir, used by view functions
 * cw_js_dir - javascript dir, used by view functions
 * cw_jquery_path - jQuery Path
 * cw_jquery_version - jQuery Version number
 * cw_system_crlf - system carriage return settings(new line)
 * cw_config_loaded - constant to check whether the configuration got loaded
 * cw_dbms - default dbms
 * cw_dbmsyql_* - mysql(i) connection details
 * cw_config_loaded - triggers if configuration has been fully loaded
 * cw_session_expires - session lifetime
 * cw_session_regenexpires - lifetime of old sessions after regeneration
 * cw_session_nounce_secret - nounce passphrase
 * cw_captcha_lifetime - auto delete time for temporary captcha files
 * cw_log_db - CobWeb_Logger default database
 * cw_log_file - CobWeb_Logger logfile
 * ************************************************************************************************
 */
if (! defined('cw_inc')) {
    echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
    exit(1);
}
/**
 * Cobweb Framework Configuration File
 */
//General Constants
define('cw_version', '0.23', true);
define('cw_app_encoding', 'utf-8');
//Locale:
define('cw_language', 'de', true);
define('cw_datestr', 'H:m:s m.d.Y', true);
date_default_timezone_set('Europe/Berlin'); // Default Timezone
//Runtime Options
define('cw_debugmode', true, true); //Debugging Mode?
define('cw_debugmode_showconsole', true, true); //Show Console Log in Debugging Mode?
define('cw_logconsole',true,true);
define('cw_error_reporting', E_ALL, true); //http://www.php.net/manual/en/errorfunc.configuration.php#ini.error-reporting
define('cw_offline', false, true); //Application offline?
//Paths & Files
define('cw_space_root', '/var/www/vhosts/example.com/httpdocs/', true);
define('cw_phpex', 'php', true);
define('cw_urlroot', 'www.example.com',true);
//Dirnames:
define('cw_framework_dir', 'framework/', true);
define('cw_webroot', '', true); //Usually /
define('cw_controller_dir', 'controllers/', true);
define('cw_view_dir', 'views/', true);
define('cw_appclasses_dir', 'appclasses/', true);
define('cw_cache_dir', 'cache/', true);
define('cw_language_dir', 'language/', true);
define('cw_log_dir', 'logs/', true);
define('cw_css_dir','css_files/',true);
define('cw_js_dir','javascript/',true);
//JQuery
define('cw_jquery_path','jquery-1.3.2.min.js'); //Has to be within cw_js_dir
define('cw_jquery_version','1.3.2');
//OS Constants
define('cw_system_crlf', "\n", true);
ini_set("auto_detect_line_endings", 1);
//Database
define('cw_dbms', 'mysqli', true);
define('cw_dbmysql_user', '', true);
define('cw_dbmysql_pw', '', true);
define('cw_dbmysql_host', '', true);
define('cw_dbmysql_db', '', true);
define('cw_dbmysql_tblprefix', 'cw_');
//configuration has been loaded:
define('cw_config_loaded', true, true);
//Session Handler
define('cw_session_expires',45); //Minutes
define('cw_session_regenexpires',20); //Secounds
define('cw_session_nounce_secret','cVz5Br%6(EmÖ-O:Xn5r5}%vd4ÖÄk++R,'); //(CHANGE THIS!!!)
//Password Management(CHANGE THIS!!!):
define('cw_password_salt_pre','4d>&W;w:=Kts+wS,ÖNl[u,0R=O@T,syf',true); //(CHANGE THIS!!!)
define('cw_password_salt_post','WVa+Nä+l_hKg)&N<Rd-#B{CrO%}R@B{E',true); //(CHANGE THIS!!!)

//Captcha's
define('cw_captcha_lifetime',900);
//Compression Level(1-9):
@ini_set('zlib.output_compression_level', 3);
//CobWeb Constants
define('cw_log_db',1);
define('cw_log_file',2);
//Login System:
define('cw_login_sessionlength',2700); //Session Length in Secs 2700 = 45 min
define('cw_login_maxtries',8); //Maximum Login Tries til Session-Lock
define('cw_login_bantime',600) //Time the User has to Wait until he can try to login again (after cw_login_maxtries tries)
?>
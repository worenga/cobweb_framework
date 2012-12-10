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
 * Filename: index.php
 * Author: Benedikt Wolters (mightyuhu@php.net)
 * License:  MIT License
 * Version: $Id;
 * ************************************************************************************************
 * Description:
 * Application entry and end point.
 * Handles:
 * 1. loads configuration
 * 2. creation of Main CW Instance
 * 3. class autoloader
 * 4. dispatching, controller loading
 * 5. unloading, quitting
 * ************************************************************************************************
 */
define("cw_app_path", dirname(__FILE__) . "/", true);
define("cw_inc", true, true); //All cw-files will check this constant. 
//Check for Config file or "die":
(@require_once ('cw.configuration.php')) or trigger_error('Cobweb: Cannot find configuration file. Exit');
if (cw_config_loaded) {
    /**
     * Cobweb Autoloader
     *
     * @param string Classname $class
     */
    function classPreload ($class)
    {
        //Has the Class its own Autoloader?
        if (function_exists($class . '_loadDependencies'))
            call_user_func($class . '_loadDependencies');
    }
    function __autoload ($class)
    {
        //Check wheter the class has already been loaded manually e.g. by cobweb's autoloader
        if (! class_exists($class, false)) {
            //@todo make $class abituary
            //Lets try to find the class in the appclasses Dir
            //Appclass?
            if (file_exists(dirname(__FILE__) . '/' . cw_appclasses_dir . str_replace('cobweb_', '', strtolower($class)) . '.class.' . cw_phpex)) {
                require_once dirname(__FILE__) . '/' . cw_appclasses_dir . str_replace('cobweb_', '', strtolower($class)) . '.class.' . cw_phpex;#
                
                classPreload($class);
                return true;
            }
            //CW_Helper?
            if (file_exists(dirname(__FILE__) . '/' . cw_framework_dir . 'cobweb.' . str_replace('cobweb_', '', strtolower($class)) . '.helper.' . cw_phpex)) {
                require_once dirname(__FILE__) . '/' . cw_framework_dir . 'cobweb.' . str_replace('cobweb_', '', strtolower($class)) . '.helper.' . cw_phpex;
                classPreload($class);
                return true;
            }
            //CW_Class?
            if (file_exists(dirname(__FILE__) . '/' . cw_framework_dir . 'cobweb.' . str_replace('cobweb_', '', strtolower($class)) . '.class.' . cw_phpex)) {
                require_once dirname(__FILE__) . '/' . cw_framework_dir . 'cobweb.' . str_replace('cobweb_', '', strtolower($class)) . '.class.' . cw_phpex;
                classPreload($class);
                return true;
            }
            //CW Abstract?
            if (file_exists(dirname(__FILE__) . '/' . cw_framework_dir . 'abstract/cobweb.' . str_replace('cobweb_', '', strtolower($class)) . '.abstract.' . cw_phpex)) {
                require_once dirname(__FILE__) . '/' . cw_framework_dir . 'abstract/cobweb.' . str_replace('cobweb_', '', strtolower($class)) . '.abstract.' . cw_phpex;
                classPreload($class);
                return true;
            }
            if (! class_exists($class, false)) {
                trigger_error('Cobweb: Unable to autoload class ' . $class);
            }
        }
    }
    //Bootstrap:
    require_once (cw_framework_dir . 'cobweb.' . cw_phpex);
    $cw = CobWeb::init(); //Init CobWeb Framework
    $cw->loadDefaults(); //Load Default Classes and Libs
    require_once 'cw.aliase.' . cw_phpex; //Class Aliases
    //Application Framework is up and ready. Now lets Dispatch the URI:
    require_once 'cw.routes.' . cw_phpex;
    $Dispatcher = new CobWeb_Dispatcher();
    $Route = $Dispatcher->dispatchRequest();
    //Save Current Route for later (all kinds of use, link generation etc.)
    CobWeb::setSetting('CurrentRoute',$Route);
    $Dispatcher->loadController($Route['controller'], $Route['action'], $Route['params']);
    //Bootstrap finished, Controller has been (hopefully) been loaded!
    
    
    //If we're done:
    if (cw_logconsole) {
        //Save Console to Log
        $ConsoleEntrys = CobWeb::o('Console')->getConsoleEntrys();
        if (! empty($ConsoleEntrys)) {
            $ConsoleLog = new CobWeb_Logging(cw_log_dir . 'console.log', cw_log_file);
            foreach ($ConsoleEntrys as $entry) {
                $ConsoleLog->event($entry['message']);
            }
            $ConsoleLog->write();
        }
        unset($ConsoleLog);
    }
    unset($cw); //Kills all objects and database connections
}
exit(); //Exit Application
?>
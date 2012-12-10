<?php
/**
 * ************************************************************************************************
 *   _____      _ __          __  _         ______                                           _    
 *  / ____|    | |\ \        / / | |       |  ____|         RAPID WEB APPLICATION           | |   
 * | |     ___ | |_\ \  /\  / /__| |__     | |__ _ __ __ _ _ __ ___   _____      _____  _ __| | __
 * | |    / _ \| '_ \ \/  \/ / _ \ '_ \    |  __| '__/ _` | '_ ` _ \ / _ \ \ /\ / / _ \| '__| |/ /
 * | |___| (_) | |_) \  /\  /  __/ |_) |   | |  | | | (_| | | | | | |  __/\ V  V / (_) | |  |   < 
 *  \_____\___/|_.__/ \/  \/ \___|_.__/    |_|  |_|  \__,_|_| |_| |_|\___| \_/\_/ \___/|_|  |_|\_\
 * ************************************************************************************************ 
 * Filename: cobweb.php
 * Author: Benedikt Wolters (mightyuhu@php.net)
 * License:  MIT License
 * Version: $Id: cobweb.php 19 2009-09-12 13:40:15Z mightyuhu $;
 * ************************************************************************************************
 * Static Cobweb Registry
 * Stores Global Objects and Settings
 * ************************************************************************************************
 **/
if (! defined('cw_inc')) {
    echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
    exit(1);
}
//Load Cobweb Interfaces:
require_once cw_framework_dir . 'cobweb.interfaces.php';
final class CobWeb
{
    //Static, so we do only have one Instance
    private static $instance;
    //Object Container
    private static $objects = array();
    //Setting Container
    private static $settings = array();
    private static $debugstate = null;
    /**
     * @return unknown
     */
    public static function getDebugMode ()
    {
        return self::$debugstate;
    }
    /**
     * @param unknown_type $debugstate
     */
    public static function setDebugMode ($debugstate)
    {
        $debugstate = (bool) $debugstate;
        self::$debugstate = $debugstate;
    }
    private function __construct ()
    { //Private Constructor avoids Creating an Intance of Cobweb
        self::$debugstate = cw_debugmode;
    }
    public function __clone ()
    {
        trigger_error('Cobweb: You cannot clone Cobweb.', E_USER_ERROR);
        return false;
    }
    public function __toString ()
    {
        return var_export(self::$objects, true);
    }
    //Alias for getObject by property-calls
    public function __get ($key)
    {
        if (! empty($key) && array_key_exists($key, self::$objects)) {
            return self::getObject($key);
        }
        if (! empty($key) && array_key_exists($key, self::$settings)) {
            return self::getSetting($key);
        }
    }
    /**
     * Checks Dependencies
     *
     * @param unknown_type $class
     * @param unknown_type $key
     */
    public static function checkDep ($class, $key = false)
    {
        if (! array_key_exists($class, self::$objects)) {
            if ($key == false) {
                self::l($class, $class);
            } else {
                self::l($class, $key);
            }
        }
        if ($key !== false) {
            if (! array_key_exists($key, self::$objects)) {
                self::alias($key, $class);
            }
        }
    }
    //Unload all Objects
    public function __destruct ()
    {
        foreach (self::$objects as $key => $object) {
            unset(self::$objects[$key]);
        }
    }
    public static function init ()
    {
        if (! isset(self::$instance)) {
            $obj = __CLASS__;
            self::$instance = new $obj();
        }
        return self::$instance;
    }
    public static function loadDefaults ()
    {
        //Load Compatability Fixes for older PHP Versions
        self::ll('compatability');
        
        //Load Default classes into CW:
        self::l('Router');
        self::l('Console');
        self::l('Debugging');
        self::l('Database');
        self::l('Security');
        self::l('View');
        self::l('Session');
        self::l('Diagnostics');
        self::l('Translator');
        
    }
    //Creates an alias reference to another Object
    public static function alias ($alias, $key)
    {
        if (! array_key_exists($alias, self::$objects)) {
            self::$objects["$alias"] = &self::$objects["$key"];
        } else {
            trigger_error('Cobweb: Alias:' . $alias . ' already exists', E_USER_ERROR);
        }
    }
    //Alias for loadObject()
    public static function l ($class, $key = null)
    {
        if ($key == '') {
            $key = & $class;
        }
        return self::$instance->loadObject($class, $key);
    }
    public static function loadObject ($class, $key = null)
    {
        $object = 'CobWeb_' . $class;
        if ($key == '') {
            $key = & $class;
        }
        require_once cw_framework_dir . 'cobweb.' . strtolower($class) . '.class.' . cw_phpex;
        if (function_exists($class . '_loadDependencies') == true) {
            call_user_func($class . '_loadDependencies');
        }
        self::$objects[$key] = new $object();
    }
    public static function killObject($key){
        if(isset(self::$objects[$key])){
            unset(self::$objects[$key]);
        }
    }
    public static function getAllObjects(){
        return self::$objects;
    }
    //Alias for loadLib();
    public static function ll ($lib)
    {
        return self::loadLib($lib);
    }
    public static function loadLib ($lib)
    {
        $lib = str_ireplace('cw_', '', $lib);
        require cw_framework_dir . 'cobweb.' . strtolower($lib) . '.lib.' . cw_phpex;
        if (function_exists($lib . '_loadDependencies'))
            call_user_func($lib . '_loadDependencies');
    }
    //Alias for getObject();
    public static function o ($key)
    {
        return self::getObject($key);
    }
    public static function getObject ($key)
    {
        return self::$objects[$key];
    }
    //Set Preloaded Object:
    public static function setObject ($key, $obj)
    {
        if (is_object($obj))
            self::$objects[$key] &= $obj;
    }
    public static function getSetting ($key)
    {
        if(isset(self::$settings[$key]))
            return self::$settings[$key];
        else
            return false;
    } 
    public static function setSetting ($key, $value)
    {
        self::$settings[$key] = $value;
        return true;
    }
    public static function getAllSettings ()
    {
        return self::$settings;
    }
}
?>
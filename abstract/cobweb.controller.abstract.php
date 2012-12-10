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
 * Filename: cobweb.controller.abstract.php
 * Author: Benedikt Wolters (mightyuhu@php.net)
 * License:  MIT License
 * Version: $Id: cobweb.controller.abstract.php 19 2009-09-12 13:40:15Z mightyuhu $;
 * ************************************************************************************************
 * Description:
 *                              CobWeb Abstract Controller Class
 *                  All Controllers are inherited from this parent class.
 * This Class provides basic Controller Methods such as Caching, ActionDispatching and 
 * Parameter Handling
 * ************************************************************************************************
 */
if (! defined('cw_inc')) {
    echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
    exit(1);
}
abstract class CobWeb_Controller
{
    protected $ControllerName = null;
    protected $ControllerCaching = false;
    protected $RequestAction = null;
    protected $DefaultAction = null;
    protected $Params = array();
    protected $cacheData = null; // Mixed cached Data
    protected $cacheExpires = null; //Calculated death of the cached version
    protected $cacheAge = null; //time() - (cacheExpires - cacheLifetime) 
    protected $cacheLifetime = 900; // 15 Minutes in Seconds
    protected $cacheExists = false; //True, if there is no spechial Cache Controller
    public function getCacheLifetime ()
    {
        return $this->cacheLifetime;
    }
    public function setCacheLifetime ($cacheLifetime)
    {
        $this->cacheLifetime = $cacheLifetime;
    }
    public function getParams ()
    {
        return $this->Params;
    }
    public function setParams ($Params)
    {
        $this->Params = $Params;
    }
    public function getDefaultAction ()
    {
        return $this->DefaultAction;
    }
    public function setDefaultAction ($DefaultAction)
    {
        $this->DefaultAction = $DefaultAction;
    }
    public function setRequestAction ($RequestAction)
    {
        $this->RequestAction = $RequestAction;
    }
    public function getControllerCaching ()
    {
        return $this->ControllerCaching;
    }
    public function setControllerCaching ($ControllerCaching)
    {
        $this->ControllerCaching = $ControllerCaching;
    }
    public function setControllerName ($ControllerName)
    {
        $this->ControllerName = $ControllerName;
    }
    public function getControllerName ()
    {
        return $this->ControllerName;
    }
    public function __construct ()
    {
        if ($this->ControllerName == null) {
            CobWeb::o('Console')->notice('No Controller Name specified!');
            $this->ControllerName = get_class($this);
        }
        $controllerClassName = &$this->ControllerName;
        if (substr($controllerClassName, - 10) != 'Controller') { // Last Word = "Controller"?
            CobWeb::o('Console')->warning('Controller Classname not well formed!');
        }
    }
    public function loadForm ($formName)
    {
        $controllerDir = cw_controller_dir . str_replace('Controller', '', $this->getControllerName()) . '/';
        if (file_exists($controllerDir . strtolower($formName) . '.form.' . cw_phpex)) {
            require_once $controllerDir . strtolower($formName) . '.form.' . cw_phpex;
            if (class_exists($formName, false)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function postAction ()
    {}
    public function doAction ()
    {
        
        $this->preAction();
        if ($this->RequestAction == null) {
            $this->RequestAction = $this->DefaultAction;
            //DISPATCHING PARTs
            //In this special Case we need to Update The CobWeb Settings-Registry to specify the default Route:
            //Important for later Link generation
            $OldRoute = CobWeb::getSetting('CurrentRoute');
            $OldRoute['action'] = $this->DefaultAction;
            CobWeb::setSetting('CurrentRoute',$OldRoute);
            
        }
        if (method_exists($this, $this->RequestAction . "Action")) {
            if ($this->ControllerCaching == true && $this->cacheExists($this->RequestAction)) {
                //Ok there is a cached Version available, lets check whether the is a spechial cacheController
                if (method_exists($this, $this->RequestAction . "ActionCache")) {
                    call_user_func_array(array($this , $this->RequestAction . "ActionCache"), $this->Params);
                    //Otherwise we're going to call the usual action
                } else {
                    $this->cacheExists = true;
                    call_user_func_array(array($this , $this->RequestAction . "Action"), $this->Params);
                }
            } else {
                call_user_func_array(array($this , $this->RequestAction . "Action"), $this->Params);
            }
        } else {
            if (method_exists($this, "unknownAction")) {
                call_user_func_array(array($this , "unknownAction"), $this->Params);
            } else {
                CobWeb::o('Console')->notice('Could not load Action: "' . $this->RequestAction . '"');
            }
        }
        $this->postAction();
    }
    public function preAction ()
    {}
    private function cacheExists ($action)
    {
        $cacheFile = cw_cache_dir . 'controllers/' . strtolower($this->ControllerName) . '.' . strtolower($action) . '.' . cw_phpex;
        //Check if there is a cached version available
        if (file_exists($cacheFile)) {
            //Read Data from Cached Version and check wheter the cached Version is up-to-date:
            $cacheData = null;
            $cHandle = @fopen($cacheFile, 'r');
            if (is_resource($cHandle)) {
                flock($cHandle, LOCK_SH);
                while (! feof($cHandle)) {
                    $cacheData .= fread($cHandle, 8192);
                }
                fclose($cHandle);
                $cObj = @unserialize($cacheData);
                if (! $cObj or (time() > $cObj[0])) {
                    //Cached Version is older or there was a problem Parsing:
                    return false;
                } else {
                    //We have a cached Version:
                    $this->preCache();
                    $this->cacheExpires = $cObj[0];
                    $this->cacheData = $cObj[1];
                    $this->postCache();
                    return true;
                }
            } else {
                trigger_error('CobWeb: Cannot read Cachefile:"' . $cacheFile . '".', E_USER_ERROR);
                return false;
            }
        } else {
        }
    }
    public function saveToCache ($action, $result)
    {
        $cacheFile = cw_cache_dir . 'controllers/' . strtolower($this->ControllerName) . '.' . strtolower($action) . '.' . cw_phpex;
        $cHandle = @fopen($cacheFile, 'w');
        if (is_resource($cHandle)) {
            $cache = @serialize(array(0 => time() + $this->cacheLifetime , 1 => $result));
            flock($cHandle, LOCK_EX);
            ftruncate($cHandle, 0);
            if (@fwrite($cHandle, $cache) === false) {
                fclose($cHandle);
                return false;
            } else {
                fclose($cHandle);
                return true;
            }
        } else {
            return false;
        }
    }
    public function preCache ()
    {}
    public function postCache ()
    {
        //Calculate cache age in Secs:
        $this->cacheAge = time() - ($this->cacheExpires - $this->cacheLifetime);
    }
    public function selfAddress ($action = null, $params = null)
    {
        if ($action == NULL and $params == null)
            return str_replace(cw_webroot, '', $_SERVER['REQUEST_URI']);
        else
            return $this->route(str_replace('Controller', '', $this->ControllerName));
    }
    //Alias for CobWeb_Router::createRoute
    public function route ($controller, $action = null, $params = null)
    {
        return CobWeb::o('Router')->createRoute($controller,$action,$params);
    }
    public function __destruct ()
    {}
}
?>
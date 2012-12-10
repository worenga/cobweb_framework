<?php
/**
 * ************************************************************************************************
 * _____      _ __          __  _         ______                                           _    
 * / ____|    | |\ \        / / | |       |  ____|         Rapid Web Application           | |   
 * | |     ___ | |_\ \  /\  / /__| |__     | |__ _ __ __ _ _ __ ___   _____      _____  _ __| | __
 * | |    / _ \| '_ \ \/  \/ / _ \ '_ \    |  __| '__/ _` | '_ ` _ \ / _ \ \ /\ / / _ \| '__| |/ /
 * | |___| (_) | |_) \  /\  /  __/ |_) |   | |  | | | (_| | | | | | |  __/\ V  V / (_) | |  |   < 
 * \_____\___/|_.__/ \/  \/ \___|_.__/    |_|  |_|  \__,_|_| |_| |_|\___| \_/\_/ \___/|_|  |_|\_\
 * ************************************************************************************************ 
 * Filename: cobweb.compatibility.lib.php
 * Author: Benedikt Wolters (mightyuhu@php.net)
 * License:  MIT License
 * Version: $Id;
 * ************************************************************************************************
 * Compatibility Library
 * This file hacks or fixes php combability issues
 * ************************************************************************************************
 */
if (! defined('cw_inc')) {
    echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
    exit(1);
}
/* 
 * get_called_class only available in 5.3
 * Thanks to progman at centrum dot sk @ http://de.php.net/manual/en/function.get-called-class.php
 */
if (! function_exists('get_called_class')) {
    function get_called_class ($bt = false, $l = 1)
    {
        if (! $bt)
            $bt = debug_backtrace();
        if (! isset($bt[$l]))
            throw new Exception(
            "Cannot find called class -> stack level too deep.");
        if (! isset($bt[$l]['type'])) {
            if(isset($bt[$l]['function'])){
            	if($bt[$l]['function'] == 'call_user_func_array'){
            		return $bt[$l]['args'][0][0];
            	}
           }
        } else {
            switch ($bt[$l]['type']) {
                case '::':
                	if (! isset($bt[$l]['file'])) {
						return get_called_class($bt,$l+1);
                    	
                    }
                    $lines = file($bt[$l]['file']);
                    $i = 0;
                    $callerLine = '';
                    do {
                        $i ++;
                        $callerLine = $lines[$bt[$l]['line'] -
                         $i] .
                         $callerLine;
                    } while (stripos(
                    $callerLine, 
                    $bt[$l]['function']) ===
                     false);
                    preg_match(
                    '/([a-zA-Z0-9\_]+)::' .
                     $bt[$l]['function'] .
                     '/', 
                    $callerLine, 
                    $matches);
                    if (! isset(
                    $matches[1])) {
                        // must be an edge case.
                        throw new Exception(
                        "Could not find caller class: originating method call is obscured.");
                    }
                    switch ($matches[1]) {
                        case 'self':
                        case 'parent':
                            return get_called_class(
                            $bt, 
                            $l +
                             1);
                        default:
                            return $matches[1];
                    }
                // won't get here.
                case '->':
                    switch ($bt[$l]['function']) {
                        case '__get':
                            // edge case -> get class of calling object
                            if (! is_object(
                            $bt[$l]['object']))
                                throw new Exception(
                                "Edge case fail. __get called on non object.");
                            return get_class(
                            $bt[$l]['object']);
                        default:
                            return $bt[$l]['class'];
                    }
                default:
                    throw new Exception(
                    "Unknown backtrace method type");
            }
        }
    }
}
if (! function_exists('array_replace')) {
    function array_replace (array &$array, array &$array1, 
    $filterEmpty = false)
    {
        $args = func_get_args();
        $count = func_num_args() - 1;
        for ($i = 0; $i < $count; ++ $i) {
            if (is_array(
            $args[$i])) {
                foreach ($args[$i] as $key => $val) {
                    if ($filterEmpty &&
                     empty(
                    $val))
                        continue;
                    $array[$key] = $val;
                }
            } else {
                trigger_error(
                __FUNCTION__ .
                 '(): Argument #' .
                 ($i +
                 1) .
                 ' is not an array', 
                E_USER_WARNING);
                return NULL;
            }
        }
        return $array;
    }
}
?>
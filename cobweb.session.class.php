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
 * Filename: cobweb.session.class.php
 * Author: Benedikt Wolters (mightyuhu@php.net)
 * License:  MIT License
 * Version: $Id: cobweb.session.class.php 19 2009-09-12 13:40:15Z mightyuhu $;
 * ************************************************************************************************
 * Session Handler Class
 * ************************************************************************************************
 */
if (! defined('cw_inc')) {
    echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
    exit(1);
}
final class CobWeb_Session
{
    /**
     * Cortesy of tedivm at tedivm dot com
     * @see http://de3.php.net/manual/de/function.session-regenerate-id.php
     * Modified
     */
    function regenerateSession ($reload = false)
    {
        if (! isset($_SESSION['REGENERATE_COUNT']) || $reload)
            $_SESSION['REGENERATE_COUNT'] = 0;
        // This token is used by forms to prevent cross site forgery attempts
        if (! isset($_SESSION['nonce']) || $reload)
            $_SESSION['nonce'] = md5(microtime(true) . cw_session_nounce_secret);
        if (! isset($_SESSION['IPaddress']) || $reload)
            $_SESSION['IPaddress'] = $_SERVER['REMOTE_ADDR'];
        if (! isset($_SESSION['userAgent']) || $reload)
            $_SESSION['userAgent'] = $_SERVER['HTTP_USER_AGENT'];
        $_SESSION['OBSOLETE'] = true;
        $_SESSION['REGENERATE_COUNT']++;
        $_SESSION['EXPIRES'] = time() + cw_session_regenexpires;
        // Create new session without destroying the old one
        session_regenerate_id(false);
        // Grab current session ID and close both sessions to allow other scripts to use them
        $newSession = session_id();
        session_write_close();
        // Set session ID to the new one, and start it back up again
        session_id($newSession);
        $this->init();
        // Don't want this one to expire
        unset($_SESSION['OBSOLETE']);
        unset($_SESSION['EXPIRES']);
        if (! isset($_SESSION['sandbox'])) {
            $_SESSION['sandbox'] = array();
        }
    }
    function checkSessionIntegrity ()
    {
        try {
            if (isset($_SESSION['OBSOLETE']) && ($_SESSION['EXPIRES'] < time()))
                throw new Exception('Attempt to use expired session.');
            if ($_SESSION['IPaddress'] != $_SERVER['REMOTE_ADDR'])
                throw new Exception('IP Address mixmatch (possible session hijacking attempt).');
            
            if ($_SESSION['userAgent'] != $_SERVER['HTTP_USER_AGENT'])
                throw new Exception('Useragent mixmatch (possible session hijacking attempt).');
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    function __construct ()
    {
        $this->init();
        $this->regenerateSession();
    }
    function __destruct ()
    {
        session_write_close();
    }
    function init ()
    {
        session_start();
        session_cache_expire(cw_session_expires);
    }
    function get ($identifier)
    {
        if ($this->checkSessionIntegrity() == true && isset($_SESSION['sandbox'][$identifier]) == true)
            return $_SESSION['sandbox'][$identifier]; 
        else
            return false;
    }
    function set ($identifier, $mixedValue)
    {
        if ($this->checkSessionIntegrity()) {
            return ($_SESSION['sandbox'][$identifier] = $mixedValue);
        } else
            return false;
    }
    function getNonce ()
    {
        if ($this->checkSessionIntegrity())
            return $_SESSION['nonce'];
        else
            return false;
    }
    function killsandbox ()
    {
        if ($this->checkSessionIntegrity())
            unset($_SESSION['sandbox']);
        else
            return false;
    }
    function kill ()
    {
        unset($_SESSION);
        session_destroy();
    }
}
?>
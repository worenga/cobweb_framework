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
 * Filename: cobweb.webpagecontroller.abstract.php
 * Author: Benedikt Wolters (mightyuhu@php.net)
 * License:  MIT License
 * Version: $Id: cobweb.webpagecontroller.abstract.php 19 2009-09-12 13:40:15Z mightyuhu $;
 * ************************************************************************************************
 * Description:
 *                                  CobWeb Abstract Web Controller Class
 * This controller is a spechial variant for handling WebPages, You might want to modify it for 
 * your application.
 * ************************************************************************************************
 */
if (! defined('cw_inc')) {
    echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
    exit(1);
}
class CobWeb_WebPageController extends CobWeb_Controller
{
    //Implement POST / GET Routing
    public function isGet() {
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            return true;
        }else{
            return false;
        }
    }
    public function isPost() {
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            return true;
        }else{
            return false;
        }
    }
}
?>
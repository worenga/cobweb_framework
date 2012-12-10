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
 * Filename: cobweb.ajaxcontroller.php
 * Author: Benedikt Wolters (mightyuhu@php.net)
 * License:  MIT License
 * Version: $Id: cobweb.ajaxcontroller.abstract.php 19 2009-09-12 13:40:15Z mightyuhu $;
 * @todo ?? What is this??
 * ************************************************************************************************
 * Description:
 * 
 * ************************************************************************************************
 */
if (! defined('cw_inc')) {
    echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
    exit(1);
}
class CobWeb_AjaxController extends CobWeb_Controller
{
    protected function toJSON ()
    {}
    protected function toList ()
    {}
    protected function toLi ()
    {}
}
?>
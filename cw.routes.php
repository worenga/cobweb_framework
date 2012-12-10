<?php
/* ************************************************************************************************
 *   _____      _ __          __  _         ______     RAPID WEB APPLICATION FRAMEWORK       _    
 *  / ____|    | |\ \        / / | |       |  ____|                                         | |   
 * | |     ___ | |_\ \  /\  / /__| |__     | |__ _ __ __ _ _ __ ___   _____      _____  _ __| | __
 * | |    / _ \| '_ \ \/  \/ / _ \ '_ \    |  __| '__/ _` | '_ ` _ \ / _ \ \ /\ / / _ \| '__| |/ /
 * | |___| (_) | |_) \  /\  /  __/ |_) |   | |  | | | (_| | | | | | |  __/\ V  V / (_) | |  |   < 
 *  \_____\___/|_.__/ \/  \/ \___|_.__/    |_|  |_|  \__,_|_| |_| |_|\___| \_/\_/ \___/|_|  |_|\_\
 * ************************************************************************************************ 
 * Filename: cw.routes.php
 * Author: Benedikt Wolters (mightyuhu@php.net)
 * License:  MIT License
 * Version: $Id;
 * ************************************************************************************************
 * Define custom routes to controllers and actions 
 * i.e.:  

 * Custom Routes:
 * $Router->connect('/newspage/*',array('controller' => 'news','action'=>'show',''));

 * Default Routes:
 * $Router &= CobWeb::getObject('Router');
 */

//Default Routes with /app/ Prefix
CobWeb::o('Router')->connect('/app/##controller##/',array());
CobWeb::o('Router')->connect('/app/##controller##/##action##/',array());
CobWeb::o('Router')->connect('/app/##controller##/##action##/*',array());

//Default Routes with no prefix
//CobWeb::o('Router')->connect('/##controller##/',array());
//CobWeb::o('Router')->connect('/##controller##/##action##/',array());
//CobWeb::o('Router')->connect('/##controller##/##action##/*',array());

//Define Custom Routes here (higher = important):
//..
//Have received old Link:
CobWeb::o('Router')->connect('/have_received/confirmation_bot/',array('controller'=>'have_received','action'=>'confirmation_bot','params'=>array()));
CobWeb::o('Router')->connect('/have_received/read/*',array('controller'=>'have_received','action'=>'read'));

//Content Wrapper
CobWeb::o('Router')->connect('/content/##action##/',array('controller'=>'content'));
CobWeb::o('Router')->connect('/content/##action##/*',array('controller'=>'content'));
//Front Page
CobWeb::o('Router')->connect('/',array('controller'=>'content','action'=>'view','params' => array('home')));
//Basic Url Content Route:
CobWeb::o('Router')->connect('/*',array('controller'=>'content','action'=>'view'));

//Logout redirects to login/logout but should appear as a seperate Controller:
CobWeb::o('Router')->connect('/logout/',array('controller' => 'login', 'action'=> 'logout'));
?>
<?php
/* ************************************************************************************************
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
 * CobWeb class aliases 
 * 
 * Syntax: CobWeb::o(alias)
 */

//Aliase for Default classes
CobWeb::alias('db', 'Database');
CobWeb::alias('debg', 'Debugging');
CobWeb::alias('sec', 'Security');
CobWeb::alias('console', 'Console');
CobWeb::alias('cons', 'Console');
CobWeb::alias('c', 'Console');
CobWeb::alias('C', 'Console');
CobWeb::alias('Routing', 'Router');
CobWeb::alias('tlr', 'Translator');
//Debugging Wrapper Function, yes we're lazy tards:
function dmp($mixedVar){CobWeb::o('Debugging')->dump($mixedVar);}
?>
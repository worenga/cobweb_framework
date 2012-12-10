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
 * Filename: cobweb.security.class.php
 * Author: Benedikt Wolters (mightyuhu@php.net)
 * License:  MIT License
 * Version: $Id: cobweb.security.class.php 19 2009-09-12 13:40:15Z mightyuhu $;
 * ************************************************************************************************
 * Description:
 * Helper Class, CobWeb_FormChechboxElement. All Functions of the CheckboxElement are included here. 
 * ************************************************************************************************
 */
if (! defined ( 'cw_inc' )) {
	echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
	exit ( 1 );
}
class CobWeb_Security {
	public function stripRepeat($str) { //Thanks to jette at nerdgirl dot dk @ phpmanual 
		//Do not allow repeated whitespace
		$str = preg_replace ( "/(\s){2,}/", '$1', $str );
		//Result: aaaaaaaaaaabbccccccccaaaaad d d d d d d ddde''''''''''''
		//Do not allow more than 3 identical characters separated by any whitespace 
		$str = preg_replace ( '{( ?.)\1{4,}}', '$1$1$1', $str );
		//Final result: aaabbcccaaad d d ddde'''
		return $str;
	}
	
	public function string_to_filename($word) {
		$tmp = preg_replace ( '/^\W+|\W+$/', '', $word ); // remove all non-alphanumeric chars at begin & end of string
		$tmp = preg_replace ( '/\s+/', '_', $tmp ); // compress internal whitespace and replace with _
		return strtolower ( preg_replace ( '/\W-/', '', $tmp ) ); // remove all non-alphanumeric chars except _ and -
	}
	
	public function generateHashCode() {
		if(!function_exists('make_Seed')){
		function make_seed() {
			list ( $usec, $sec ) = explode ( ' ', microtime () );
			return ( float ) $sec + (( float ) $usec * 100000);
		}}
		srand ( make_seed () );
		return md5 ( uniqid ( rand ( 1, 200 ) ) );
	}
	public function encrypt($str) {
		//$iv = mcrypt_create_iv(CW_AES_IV, MCRYPT_RAND);
		//return str_replace("/","%2f%2f%2f",base64_encode(mcrypt_encrypt(CW_ENCRYPTWITH, CW_AES_ENCKEY, $str, MCRYPT_MODE_ECB,$iv)));
		return str_replace ( "/", "%2f%2f%2f", base64_encode ( base64_encode ( $str ) ) );
	
	}
	public function decrypt($str) {
		//$iv = mcrypt_create_iv(CW_AES_IV, MCRYPT_RAND);
		$str = str_replace ( "%2f%2f%2f", "/", $str );
		$str = base64_decode ( base64_decode ( $str ) );
		//return trim(mcrypt_decrypt(CW_ENCRYPTWITH,CW_AES_ENCKEY,($str),MCRYPT_MODE_ECB,$iv));
		return trim ( $str );
	}
	public function escape($str) {
		return addslashes ( $str );
	}
	public function unescape($str) {
		return stripslashes ( $str );
	}
	public function maskxHtml($string) {
		return htmlentities ( $string, ENT_QUOTES );
	}
	
	public function secureGPC() {
		$_POST = @array_map ( "mysql_real_escape_string", $_POST );
		$_GET = @array_map ( "mysql_real_escape_string", $_GET );
		$_FILES = @array_map ( "mysql_real_escape_string", $_FILES );
		$_SESSION = @array_map ( "mysql_real_escape_string", $_SESSION );
		$_COOKIE = @array_map ( "mysql_real_escape_string", $_COOKIE );
	}
	
	function ajaxDecode($arr_data) {
		$arr_data = array_walk_recursive ( &$arr_data, 'utf8_decode' );
		return $arr_data;
	}
}
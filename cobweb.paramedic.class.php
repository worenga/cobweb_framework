<?php
if (! defined('cw_inc')) {
    echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
    exit(1);
}
class CobWeb_Paramedic {
	public function sql($value){
		//Spechial SQL
		$strip = array(
		'/"/',
		'/´/',
		'/`/',
		"/'/",
		'/UNION/i',
		'/SELECT/i',
		'/DELETE/i',
		'/TRUNCATE/i',
		'/DESCRIBE/i',
		'/DROP/i',
		'/1=1/',
		'/--/',
		'/\/\*/',
		'/\*\//',
		'/{/',
		'/}/'
		);
		$cleaned = preg_replace($strip,'',$value);
		return $cleaned;
	}
	public function html($value){
		$strip = array(
		'/</',
		'/>/',
		'/j(\s)*a(\s)*v(\s)*a(\s)*s(\s)*c(\s)*r(\s)*i(\s)*p(\s)*t/i',
		'/&quot;/',
		'/"/',
		"/'/",
		'/f(\s)*r(\s)*o(\s)*m(\s)*c(\s)*h(\s)*a(\s)*r(\s)*c(\s)*o(\s)*d(\s)*e(\s)*/i');
		$cleaned = preg_replace($strip,'',$value);
		return $cleaned;
	}
	public function alphaNum($value,$stripWhitespace = false){
		$str = ($stripWhitespace) ? ' ' : '';  
		$cleaned = preg_replace('/([^a-zA-Z0-9'.$str.']+)/','',$value);
		return $cleaned;
	}
	public function alphaNumUml($value,$stripWhitespace = false){
		$str = ($stripWhitespace) ? ' ' : '';  
		$cleaned = preg_replace('/([^\w'.$str.']+)/','',$value);
		return $cleaned;
	}
}
?>
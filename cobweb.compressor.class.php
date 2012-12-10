<?php
if (! defined('cw_inc')) {
    echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
    exit(1);
}
class CobWeb_Compressor {
	private static function prepare($source){
		$cleaned = $source;
		//Remove whitespace at the end and in front:
		$cleaned = trim($cleaned);
		//Remove all Linebreaks:
		$cleaned = preg_replace("/\n|\r\n|\r$/", "", $cleaned);
		return $cleaned;
	}
	public static function compressxHTML($source){
		$cleaned = CobWeb_Compressor::prepare($source);
		//Whitespace Fix:
		$cleaned = preg_replace('/\s\s+/', ' ', $cleaned);
		//Make <tag > to <tag>; <tag /> to <tag/>; </tag > to </tag> and <tag> </tag> to <tag></tag>
		$cleaned = preg_replace('/>\s</im','><',$cleaned);
		$cleaned = preg_replace('/\s>/im','>',$cleaned);
		$cleaned = preg_replace('/\s\/>/im','/>',$cleaned);
		return $cleaned;
	}
	public static function compressCSS($source){
		$cleaned = CobWeb_Compressor::prepare($source);
		//Whitespace Fix:
		$cleaned = preg_replace('/\s\s+/', ' ', $cleaned);
		return $cleaned;
	}
	public static function compressxJS($source){
		$cleaned = CobWeb_Compressor::prepare($source);
		return $cleaned;
	}
	
	public static function out($content){
		//See cw.configuration.php for compression level
		ob_start("ob_gzhandler");
		echo $content;
		ob_end_flush();
	}
}
?>
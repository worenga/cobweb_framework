<?php
if(!defined('cw_inc')){
    echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
    exit(1);
}

class CobWeb_FileFunctions {
	
	public function __construct() {
		trigger_error ( 'Cobweb: Cobweb_FileFunctions is a library!', E_USER_ERROR );
	}
	
	function byteConvert($bytes) {
		if ($bytes <= 0)
			return '0 Byte';
		$convention = 1000; //[1000->10^x|1024->2^x]
		$s = array (

		'B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB' );
		$e = floor ( log ( $bytes, $convention ) );
		return round ( $bytes / pow ( $convention, $e ), 2 ) . ' ' . $s [$e];
	}
	
	public function GetExtension($filename) {
		$pos = strrpos ( $filename, "." );
		if ($pos === FALSE) {
			return '';
		} else {
			return strtolower ( end ( explode ( '.', $filename ) ) );
		}
	
	}
	
	public function ConvertExtension($filename) {
		$ext = GetExtension ( $filename );
		$pos = strrpos ( $filename, "." );
		if ($pos === FALSE) {
			return $filename . ".$ext";
		} else {
			return substr ( $filename, 0, $pos ) . ".$ext";
		}
	}
	
	public function FixExtension($filename, $rightExt) {
		$rightExt = strtolower ( $rightExt );
		if (GetExtension ( $filename ) != $rightExt) {
			$pos = strrpos ( $filename, "." );
			if ($pos === FALSE) {
				return $filename . ".$rightExt";
			} else {
				return substr ( $filename, 0, $pos ) . ".$rightExt";
			}
		} else {
			return $filename;
		}
	}
	
	public function GetMimeStr($filename) {
		$ext = GetExtension ( $filename );
		switch ($ext) {
			case 'pdf' :
				return 'application/pdf';
				break;
			case 'jpg' :
			case 'jpeg' :
				return "image/jpg";
				break;
			case 'png' :
			case 'gif' :
				return "image/" . $ext;
				break;
		}
	}
	
	public function rmDir($path, $recursive = true) {
		$path = rtrim ( $path, '/' ) . '/';
		if (is_readable ( $path )) {
			$handle = opendir ( $path );
			for(; false !== ($file = readdir ( $handle ));)
				if ($file != "." and $file != "..") {
					$fullpath = $path . $file;
					if (is_dir ( $fullpath ) == true && $recursive == true) {
						self::rmDir ( $fullpath );
					} else {
						if (@unlink ( $fullpath )) {
							trigger_error ( 'Cobweb: Unable to delete File:' . $fullpath, E_USER_ERROR );
						}
					}
				}
			closedir ( $handle );
			rmdir ( $path );
		} else {
			trigger_error ( 'Cobweb: Unable to read dir:' . $path, E_USER_ERROR );
		}
	}
	
	private function checkfile($filename, $maxsize) {
		if (is_uploaded_file ( $filename )) {
			//Filesize:
			$size = filesize ( $filename );
			if ($size <= 0) {
				//We dont want Zero-Byte Files
				return false;
			} elseif ($size > $maxsize) {
				//File is too big
				return false;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}
	
	public function upload($identifier, $dir, $allowedtypes, $maxsize, $muli = false, $rename = true) {
		$upload_error = false;
		$files = array ();
		if ($multi == true) {
			foreach ( $_FILES [$identifier] ['error'] as $key => $error ) {
				if ($error != UPLOAD_ERR_OK) {
					$upload_error = true;
				}
			}
			if ($upload_error == true) {
				trigger_error ( 'Cobweb: Internal Error uploading Files', E_USER_ERROR );
				return false;
			} else {
				foreach ( $_FILES [$identifier] ['tmp_name'] as $key => $tmp_name ) {
					if (self::checkfile ( $tmp_name, $maxsize ) == false) {
						trigger_error ( 'Cobweb: Invalid File', E_USER_ERROR );
						return false;
					} else {
						if ($rename == true) {
							do {
								$filename = md5 ( uniqid ( rand ( 1, 200 ) ) );
							} while ( file_exists ( strtolower ( $dir . $filename . self::GetExtension ( $_FILES [$identifier] ['name'] [$key] ) ) ) );
						
						} else {
							$filename = $_FILES [$identifier] ['name'] [$key];
						}
						//Check wheter the filename has a correct Name and Sanatize
						$filename = preg_replace ( "/[^a-z0-9-]/", "-", strtolower ( $filename ) );
						
						//Check if there is an extension:
						if (self::GetExtension ( $filename ) != '') {
							//Fix Extension: (.Jpeg becomes .jpeg)
							$filename = self::FixExtension ( $filename, self::GetExtension ( $filename ) );
							
							$umask_old = umask ( 0 );
							if (@move_uploaded_file ( $_FILES [$identifier] ['tmp_name'] ['key'], $dir . $filename )) {
								@chmod ( $dir . $filename, 0755 );
								$files [] = array ('name' => $filename, 'size' => filesize ( $dir . $filename ) );
							} else {
								trigger_error ( 'Cobweb: Unable to move uploaded File', E_USER_ERROR );
							}
						} else {
							trigger_error ( 'Cobweb: Unexpected File Extension' );
						}
						
						umask ( $umask_old );
					}
				}
			}
		} else {
			//Just one File to upload:
			if ($_FILES [$identifier] ['error'] != UPLOAD_ERR_OK) {
				trigger_error ( 'Cobweb: Internal Error uploading Files', E_USER_ERROR );
				return false;
			} else {
				if (self::checkfile ( $_FILES [$identifier] ['tmp_name'], $maxsize ) == false) {
					trigger_error ( 'Cobweb: Invalid File', E_USER_ERROR );
					return false;
				} else {
					if ($rename == true) {
						do {
							$filename = md5 ( uniqid ( rand ( 1, 200 ) ) );
						} while ( file_exists ( strtolower ( $dir . $filename . self::GetExtension ( $_FILES [$identifier] ['name'] ) ) ) );
					} else {
						$filename = $_FILES [$identifier] ['name'];
					}
					//Check wheter the filename has a correct Name and Sanatize
					$filename = preg_replace ( "/[^a-z0-9-]/", "-", strtolower ( $filename ) );
					
					//Check if there is an extension:
					if (self::GetExtension ( $filename ) != '') {
						//Fix Extension: (.Jpeg becomes .jpeg)
						$filename = self::FixExtension ( $filename, self::GetExtension ( $filename ) );
						
						$umask_old = umask ( 0 );
						if (@move_uploaded_file ( $_FILES [$identifier] ['tmp_name'], $dir . $filename )) {
							@chmod ( $dir . $filename, 0755 );
							$files [] = array ('name' => $filename, 'size' => filesize ( $dir . $filename ) );
						} else {
							trigger_error ( 'Cobweb: Unable to move uploaded File', E_USER_ERROR );
						}
					} else {
						trigger_error ( 'Cobweb: Unexpected File Extension' );
					}
					umask ( $umask_old );
				}
			}
		}
		return $files;
	}
}
?>
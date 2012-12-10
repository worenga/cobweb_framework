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
 * Filename: cobweb.captchaimage.class.php
 * Author: Benedikt Wolters (mightyuhu@php.net)
 * License:  MIT License
 * Version: $Id;
 * ************************************************************************************************
 * CobWeb Captcha Image Class
 * Creates and stores a Captcha Image to verify that the User is an human being
 * ************************************************************************************************
 * @notice: No Include check here: Standalone is allowed.
 **/
class CobWeb_CaptchaImage
{
    private $secretPassphrase = null;
    private $captchaCacheDir = null;
    private $captchaFontDir = null;
    private $img = null;
    private $hash = null;
    function __construct ($hash = null)
    {
        $this->captchaCacheDir = cw_cache_dir . 'captchas/';
        $this->captchaFontDir = cw_framework_dir . 'meta/captchafonts/';
        $this->hash = $hash;
    }
    private function createPassphrase ()
    {
    	srand((double) microtime() * 1000000);
        $chars = array();
        $passphrasesize = rand(5, 5); //5-7 Chars
        for ($i = 0; $i < $passphrasesize; $i ++) {
        	srand((double) microtime() * 1000000);
            //Flip a coin:
            if (rand(0, 1) == 0) {
                do {
                srand((double) microtime() * 1000000);
                $chars[$i] = chr(rand(65, 90)); //Letter
                }while ($chars[$i] == "I" || $chars[$i] == "O"); 
                 
               
            } else {
                $chars[$i] = rand(2, 9); //Number no 0 and 1 because this might causes confusion
            }
        }
        return $chars;
    }
    public function clearCaptchas ()
    {
        $DIR = $this->captchaCacheDir;
        if ($dir_handle = opendir($DIR)) {
            while (false !== ($file = readdir($dir_handle))) {
                if (($file != ".") && ($file != "..")) {
                    $time = time() - filemtime($DIR . "/" . $file);
                    if ($time > cw_captcha_lifetime) {
                        if($file != '.svn')
                        unlink($DIR . "/" . $file); 
                    }
                }
            }
            closedir($dir_handle);
        }
    }
    
    public function checkCaptcha($passphrase){
    	$possibleFile = $this->captchaCacheDir.$this->hash.strtoupper($passphrase);
    	if(file_exists($possibleFile)){
    		unlink($possibleFile);
    		return true;
    	}else{
    		return false;
    	}
    }
    
    public function createCaptchaImage ()
    {
        $DIR = $this->captchaCacheDir;
        $FONTS = $this->captchaFontDir;
        $keys = $this->createPassphrase();
        $this->secretPassphrase = implode('',$keys);
        $this->img = imagecreatetruecolor(155, 40);
        srand((double) microtime() * 1000000);
        $bgColor = ImageColorAllocate($this->img, 255,250, 366);
        ImageFilledRectangle($this->img, 0, 0, 155, 40, $bgColor);
        //Basic Black
        $color = imagecolorallocate($this->img, 0, 0, 0);
        


        // Font Color
        $colors = array(
        imagecolorallocate($this->img, 35, 191, 170),
         imagecolorallocate($this->img, 239, 29, 37) ,
         imagecolorallocate($this->img, 204, 112, 1) ,
          imagecolorallocate($this->img, 255, 0, 226) ,
           imagecolorallocate($this->img, 0, 69, 255));
       shuffle($colors);
        
       if(function_exists('imageantialias'))
        	imageantialias($this->img, true);

        
        for ($i = 0; $i < 5; $i ++) {
            $linecolor = imagecolorallocate($this->img, rand(1, 255), rand(1, 255), rand(1, 255));
            imageline($this->img, 0, rand(1, 40), 156, rand(1, 40), $linecolor);
        }
        for ($i = 0; $i < 5; $i ++) {
            $linecolor = imagecolorallocate($this->img, rand(1, 255), rand(1, 255), rand(1, 255));
            imageline($this->img, rand(0, 155), 40, rand(0, 155), 0, $linecolor);
        }
        for ($i = 0; $i < 4; $i ++) {
            $linecolor = imagecolorallocate($this->img, rand(1, 255), rand(1, 255), rand(1, 255));
            imagearc($this->img, rand(1, 155), rand(1, 40), rand(10, 100), rand(10, 100), rand(10, 100), rand(10, 100), $linecolor);
        }
        
        function doangle ()
        {
            srand((double) microtime() * 1000000);
            $angle = rand(0, 20);
            if (rand(0, 1) == 1) {
                $angle = 360 - $angle;
            }
            return $angle;
        }
        // Zahlen auf das Bild zeichnen, Position etwas variieren, zufällig eine Schriftart auswählen (1.ttf-7.ttf)
        $it = 0;
        foreach ($keys as $key){
            $it++;
            $size = rand(20, 22);
            imagettftext($this->img,$size,doangle(),5+($it*25),25 + rand(0, 10),$colors[rand(0,4)],$FONTS .  rand(1, 7) . '.ttf',$key);    
        }
        
        //Finally annoy with a random amount of dots
        $dots = rand(30,150);
        while($i <= $dots){
            imagesetpixel($this->img,rand(0,155),rand(0,40),$colors[rand(0,count($colors)-1)]);
            $i++;
        }
        
        if(function_exists('imageantialias'))
        	imageantialias($this->img, true);
    }
    public function display ()
    {
        header("Content-type: image/jpeg");
        imagejpeg($this->img);
    }
    public function save ()
    {
        imagejpeg($this->img, $this->captchaCacheDir . $this->hash . $this->secretPassphrase, 90);
    }
    public function __destruct ()
    {
    	if($this->img != null)
        imagedestroy($this->img);
    }
}
?>
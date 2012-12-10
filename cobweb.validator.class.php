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
 * Filename: cobweb.form.validator.php
 * Author: Benedikt Wolters (mightyuhu@php.net)
 * License:  MIT License
 * Version: $id;
 * ************************************************************************************************
 * Description:
 * This File contains several validation functions used by CobWeb_Form, CobWeb_FormElement and 
 * can be used within your application to validate values. 
 * ************************************************************************************************
 */
if (! defined('cw_inc')) {
    echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
    exit(1);
}
class CobWeb_Validator
{
    public function isEmpty ($value)
    { //Also considers whitespace as empty!
        $value = (string) $value;
        if (trim($value) == '') {
            return false;
        } else {
            return true;
        }
    }
    public function isChar ()
    {
        if (strlen(trim($value)) == 1) {
            return true;
        } else {
            return false;
        }
    }
    public function isNumber ($value)
    {
        if (is_numeric($value) === true && intval($value) == $value) {
            return true;
        } else {
            return false;
        }
    }
    public function isString ($value)
    {
        return is_string($value);
    }
    public function isAlphanum ($value)
    {
        return preg_match('/^[a-zA-Z]+$/', $value);
    }
    public function isInteger ($value)
    {
        if (intval($value) == $value) {
            return true;
        } else {
            return false;
        }
    }
    public function isFloat ($value)
    {
        return is_float($value);
    }
    public function isUrl ($value)
    {
        if ($url == NULL)
            return false;
        $protocol = '(http://|https://)';
        $allowed = '([a-z0-9]([-a-z0-9]*[a-z0-9]+)?)';
        $regex = "^" . $protocol . // must include the protocol
'(' . $allowed . '{1,63}\.)+' . // 1 or several sub domains with a max of 63 chars
'[a-z]' . '{2,6}'; // followed by a TLD
        if (eregi($regex, $url) == true)
            return true;
        else
            return false;
    }
public function isEmail($email)
{
   $isValid = true;
   $atIndex = strrpos($email, "@");
   if (is_bool($atIndex) && !$atIndex)
   {
      $isValid = false;
   }
   else
   {
      $domain = substr($email, $atIndex+1);
      $local = substr($email, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);
      if ($localLen < 1 || $localLen > 64)
      {
         // local part length exceeded
         $isValid = false;
      }
      else if ($domainLen < 1 || $domainLen > 255)
      {
         // domain part length exceeded
         $isValid = false;
      }
      else if ($local[0] == '.' || $local[$localLen-1] == '.')
      {
         // local part starts or ends with '.'
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $local))
      {
         // local part has two consecutive dots
         $isValid = false;
      }
      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
      {
         // character not valid in domain part
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $domain))
      {
         // domain part has two consecutive dots
         $isValid = false;
      }
      else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                 str_replace("\\\\","",$local)))
      {
         // character not valid in local part unless 
         // local part is quoted
         if (!preg_match('/^"(\\\\"|[^"])+"$/',
             str_replace("\\\\","",$local)))
         {
            $isValid = false;
         }
      }
      if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
      {
         // domain not found in DNS
         $isValid = false;
      }
   }
   return $isValid;
}			
    public function isWithinRange ($value, $min, $max)
    {
        $value = trim($value);
        if (strlen($value) >= $min || $min < 0) {
            if (strlen($value) <= $max || $max < 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function isDate ($value)
    {
        $parseResult = strtotime(trim($value));
        if ($parseResult != true) {
            return false;
        } else {
            return checkdate(date('n', $parseResult), date('j', $parseResult), date('Y', $parseResult));
        }
    }
    public function matches ($value, $regex)
    {
        return preg_match($regex, $value);
    }
    public function isOldEnough ($value, $ageLimit = null)
    {
        if ($ageLimit == null)
            $ageLimit = 441504000; // 14*365*24*60*60
        $parseResult = strtotime(trim($value));
        if ($parseResult != true) {
            return false;
        } else {
            if ($parseResult <= time() - $ageLimit) {
                return true;
            } else {
                return false;
            }
        }
    }
}
?>
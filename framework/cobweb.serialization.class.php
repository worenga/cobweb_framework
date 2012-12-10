<?php
/**
  * Also Serializes HTML and "'; values
 */
class CobWeb_Serialization {
    
    public static function serialize ( $var = array(), $recur = FALSE )
    {
        if ( $recur ) {
            foreach ( $var as $k => $v )
            {
                if ( is_array($v) ) {
                    $var[$k] = self::serialize($v, 1);
                } else {
                    $var[$k] = base64_encode($v);
                }
            }
            return $var;
        } else {
            return serialize(self::serialize($var, 1));
        }
    }
       
    public static function unserialize ( $var = FALSE, $recur = FALSE )
    {
        if ( $recur ) {
            foreach ( $var as $k => $v )
            {
                if ( is_array($v) ) {
                    $var[$k] = self::unserialize($v, 1);
                } else {
                    $var[$k] = base64_decode($v);
                }
            }
            return $var;
        } else {
            return self::unserialize(unserialize($var), 1);
        }
    }
    
    
}


?>
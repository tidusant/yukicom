<?php
/** 
* @link http://code.google.com/p/php-lzw/
* @author Jakub Vrana, http://php.vrana.cz/
* @copyright 2009 Jakub Vrana
* @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
*/

/** LZW compression
* @param string data to compress
* @return string binary data
*/
class lzw {
	public static function compress($unc) {
        $i;$c;$wc;
        $w = "";
        $dictionary = array();
        $result = array();
        $dictSize = 256;
        for ($i = 0; $i < 256; $i += 1) {
            $dictionary[chr($i)] = $i;
        }
        for ($i = 0; $i < strlen($unc); $i++) {
            $c = $unc[$i];
            $wc = $w.$c;
            if (array_key_exists($w.$c, $dictionary)) {
                $w = $w.$c;
            } else {
                array_push($result,$dictionary[$w]);
                $dictionary[$wc] = $dictSize++;
                $w = (string)$c;
            }
        }
        if ($w !== "") {
            array_push($result,$dictionary[$w]);
        }
        array_shift($result);
		
		//return implode(",",$result);
		$str='';
		
		foreach($result as $k=>$v){
			$str.=\lzw::unichr($v); 
			
		}
        return $str;
    }
	
	public static function unichr($u) {
		return mb_convert_encoding('&#' . intval($u) . ';', 'UTF-8', 'HTML-ENTITIES');
	}
	


 
    public static function decompress($com) {
        $com = explode(",",$com);
        $i;$w;$k;$result;
        $dictionary = array();
        $entry = "";
        $dictSize = 256;
        for ($i = 0; $i < 256; $i++) {
            $dictionary[$i] = chr($i);
        }
        $w = chr($com[0]);
        $result = $w;
        for ($i = 1; $i < count($com);$i++) {
            $k = $com[$i];
            if ($dictionary[$k]) {
                $entry = $dictionary[$k];
            } else {
                if ($k === $dictSize) {
                    $entry = $w.$w[0];
                } else {
                    return null;
                }
            }
            $result .= $entry;
            $dictionary[$dictSize++] = $w + $entry[0];
            $w = $entry;
        }
        return $result;
    }
	
	public function ords_to_unistr($ords, $encoding = 'UTF-8'){
	
    // Turns an array of ordinal values into a string of unicode characters
    $str = '';
    for($i = 0; $i < sizeof($ords); $i++){
        // Pack this number into a 4-byte string
        // (Or multiple one-byte strings, depending on context.)                
        $v = $ords[$i];
        $str .= pack("N",$v);
    }
	
    $str = mb_convert_encoding($str,$encoding,"UCS-4BE");
    return($str);            
}

public static function unistr_to_ords($str, $encoding = 'UTF-8'){        
    // Turns a string of unicode characters into an array of ordinal values,
    // Even if some of those characters are multibyte.
    $str = mb_convert_encoding($str,"UCS-4BE",$encoding);
    $ords = array();
    
    // Visit each unicode character
    for($i = 0; $i < mb_strlen($str,"UCS-4BE"); $i++){        
        // Now we have 4 bytes. Find their total
        // numeric value.
        $s2 = mb_substr($str,$i,1,"UCS-4BE");                    
        $val = unpack("N",$s2);            
        $ords[] = $val[1];                
    }        
    return($ords);
}

}
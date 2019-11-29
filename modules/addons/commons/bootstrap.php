<?php

$pathName=explode("\\",__DIR__);
if(count($pathName)<=1)$pathName=explode('/',__DIR__);
$moduleName=$pathName[count($pathName)-1];
// API for calling from outsite module.
$this->module($moduleName)->extend([
    "getdb"=>function(){
        $mongo = new \MongoClient("mongodb://duyhf.com",["db" => "colis","username" => "colis", "password" => "siloc@1234.P"]);
        //$mongo = new \MongoClient("mongodb://localhost",["db" => "colis","username" => "colisshop", "password" => "colis@1234"]);
        $db=$mongo->colis;
        return $db;
    },
    "createslug"=>function($text="",$special="-"){

        $finds=array('á','à','ả','ã','ạ','ă','ắ','ằ','ẳ','ẵ','ặ','â','ấ','ầ','ẩ','ẫ','ậ');
        $text=str_replace($finds, 'a', $text);
        $finds=array('Á','À','Ả','Ã','Ạ','Ă','Ắ','Ằ','Ẳ','Ẵ','Ặ','Â','Ấ','Ầ','Ẩ','Ẫ','Ậ');
        $text=str_replace($finds, 'A', $text);
        $finds=array('ó','ò','ỏ','õ','ọ','ơ','ớ','ờ','ở','ỡ','ợ','ô','ố','ồ','ổ','ỗ','ộ');
        $text=str_replace($finds, 'o', $text);
        $finds=array('Ó','Ò','Ỏ','Õ','Ọ','Ơ','Ớ','Ờ','Ở','Ỡ','Ợ','Ô','Ố','Ồ','Ổ','Ỗ','Ộ');
        $text=str_replace($finds, 'O', $text);      
        $finds=array('é','è','ẻ','ẽ','ẹ','ê','ế','ề','ể','ễ','ệ');      
        $text=str_replace($finds, 'e', $text);
        $finds=array('É','È','Ẻ','Ẽ','Ẹ','Ê','Ế','Ề','Ể','Ễ','Ệ');      
        $text=str_replace($finds, 'E', $text);
        $finds=array('ú','ù','ủ','ũ','ụ','ư','ứ','ừ','ử','ữ','ự');      
        $text=str_replace($finds, 'u', $text);
        $finds=array('Ú','Ù','Ủ','Ũ','Ụ','Ư','Ứ','Ừ','Ử','Ữ','Ự');      
        $text=str_replace($finds, 'U', $text);
        $finds=array('í','ì','ỉ','ĩ','ị');      
        $text=str_replace($finds, 'i', $text);
        $finds=array('Í','Ì','Ỉ','Ĩ','Ị');      
        $text=str_replace($finds, 'I', $text);
        $finds=array('ý','ỳ','ỷ','ỹ','ỵ');      
        $text=str_replace($finds, 'y', $text);
        $finds=array('Ý','Ỳ','Ỷ','Ỹ','Ỵ');      
        $text=str_replace($finds, 'Y', $text);
        $finds=array('đ');      
        $text=str_replace($finds, 'd', $text);
        $finds=array('Đ');      
        $text=str_replace($finds, 'D', $text);      

        $text= preg_replace('/[^a-zA-Z0-9]/i', $special, $text);
        $text= preg_replace('/-+/i', $special, $text);
        $text=trim($text,$special);
        return strtolower($text);
    },
    'create_random_string'=>function($num,$won=0){
      //Tao du lieu cho hinh ngau nhien
        //without number
      if($won)
            $chars = array( 'a', 'A', 'b', 'B', 'c', 'C', 'd', 'D', 'e', 'E', 'f', 'F', 'g', 'G', 'h', 'H', 'i', 'I', 'j', 'J',  'k', 'K', 'l', 'L', 'm', 'M', 'n', 'N', 'o', 'p', 'P', 'q', 'Q', 'r', 'R', 's', 'S', 't', 'T',  'u', 'U', 'v', 'V', 'w', 'W', 'x', 'X', 'y', 'Y', 'z', 'Z');
        else
            $chars = array( 'a', 'A', 'b', 'B', 'c', 'C', 'd', 'D', 'e', 'E', 'f', 'F', 'g', 'G', 'h', 'H', 'i', 'I', 'j', 'J',  'k', 'K', 'l', 'L', 'm', 'M', 'n', 'N', 'o', 'p', 'P', 'q', 'Q', 'r', 'R', 's', 'S', 't', 'T',  'u', 'U', 'v', 'V', 'w', 'W', 'x', 'X', 'y', 'Y', 'z', 'Z', '1', '2', '3', '4', '5', '6', '7', '8', '9');
      $max_chars = count($chars) - 1;
      for($i = 0; $i < $num; $i++){
        $code = ( $i == 0 ) ? $chars[rand(0, $max_chars)] : $code . $chars[rand(0, $max_chars)];
      }
      return $code;
    },
    "numberToString"=>function($num){
        $rt='';
        $strarr=['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','x','y','z','w'];
        foreach ($num as $key => $value) {
            $rt.=$strarr[$value];
        }
        return $rt;
    },
    "recursiveRemove"=>function ($dir) {
        $structure = glob(rtrim($dir, "/").'/*');
        if (is_array($structure)) {
            foreach($structure as $file) {
                if (is_dir($file)) $this->app->module('commons')->recursiveRemove($file);
                elseif (is_file($file)) @unlink($file);
            }
        }
        @rmdir($dir);
    },
    "ftp_rdel"=>function($handle, $path) {

      if (@ftp_delete ($handle, $path) === false) {
        if ($children = @ftp_nlist ($handle, $path)) {
            //print_r($children);
            foreach ($children as $p){
                if($p!='.' && $p!='..')
                    $this->app->module('commons')->ftp_rdel ($handle,  $path.'/'.$p);
            }
        }
        @ftp_rmdir ($handle, $path);
      }
}

]);



//dashboard widget


// ADMIN
if(COCKPIT_ADMIN) {
    //register acl    
    $app->on("admin.init", function() use($app,$moduleName){       
        // bind api
        $app->bindClass("$moduleName\\Controller\\Api", "api/$moduleName");
        
       
        
    });
    
}


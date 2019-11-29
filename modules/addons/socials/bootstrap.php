<?php

//API

$pathName=explode("\\",__DIR__);
if(count($pathName)<=1)$pathName=explode('/',__DIR__);
$moduleName=$pathName[count($pathName)-1];
$this->module($moduleName)->extend([
    'accept_result_code'=>function(){
      return ['200','201'];
    },
    'create_random_string'=>function($num){
      //Tao du lieu cho hinh ngau nhien
      $chars = array( 'a', 'A', 'b', 'B', 'c', 'C', 'd', 'D', 'e', 'E', 'f', 'F', 'g', 'G', 'h', 'H', 'i', 'I', 'j', 'J',  'k', 'K', 'l', 'L', 'm', 'M', 'n', 'N', 'o', 'p', 'P', 'q', 'Q', 'r', 'R', 's', 'S', 't', 'T',  'u', 'U', 'v', 'V', 'w', 'W', 'x', 'X', 'y', 'Y', 'z', 'Z', '1', '2', '3', '4', '5', '6', '7', '8', '9');
      $max_chars = count($chars) - 1;
      for($i = 0; $i < $num; $i++){
        $code = ( $i == 0 ) ? $chars[rand(0, $max_chars)] : $code . $chars[rand(0, $max_chars)];
      }
      return $code;
    },
    'curl_file_create'=>function($filename, $mimetype = '', $postname = '') { 
      print_r("@$filename;filename=" . ($postname ? $postname: basename($filename)) . ($mimetype ? ";type=$mimetype" : ''));exit;
      return "@$filename;filename=" . ($postname ? $postname: basename($filename)) . ($mimetype ? ";type=$mimetype" : '');
    },
    'createFile'=>function($imgURL) {
      $remImgURL = urldecode($imgURL); $urlParced = pathinfo($remImgURL); $remImgURLFilename = $urlParced['basename'];

      $imgData = $this->app->module('socials')->request2($remImgURL, array('timeout' => 45,'isVerifySSL'=>1)); 
      //print_r($imgData);exit;
      //if (is_wp_error($imgData)) { $badOut['Error'] = print_r($imgData, true)." - ERROR"; return $badOut; }
      if (isset($imgData['content-type'])) $cType = $imgData['content-type']; $imgData = $imgData['body'];
      $tmp=array_search('uri', @array_flip(stream_get_meta_data($GLOBALS[mt_rand()]=tmpfile())));
      if (!is_writable($tmp))  { $badOut['Error'] = "Your temporary folder or file (file - ".$tmp.") is not witable. Can't upload images to Flickr"; return $badOut; }
      copy($tmp, $tmp.='.png'); 
      register_shutdown_function(create_function('', "unlink('{$tmp}');"));
      file_put_contents($tmp, $imgData); if (!$tmp) { $badOut['Error'] = 'You must specify a path to a file'; return $badOut; }
      if (!file_exists($tmp)) { $badOut['Error'] = 'File path specified does not exist'; return $badOut; }
      if (!is_readable($tmp)) { $badOut['Error'] = 'File path specified is not readable'; return $badOut; }
      
      $cfile = curl_file_create($tmp,$cType,'nxstmp'); 
      //print_r($cfile);exit;
      return $cfile;
    },
    'maybe_unserialize'=>function( $original ) {
      if ( $this->app->module('socials')->is_serialized( $original ) ) // don't attempt to unserialize data that wasn't serialized going in
        return @unserialize( $original );
      return $original;
    },
    'is_serialized'=>function( $data, $strict = true ) {
    // if it isn't a string, it isn't serialized
    if ( ! is_string( $data ) ) {
      return false;
    }
    $data = trim( $data );
    if ( 'N;' == $data ) {
      return true;
    }
    if ( strlen( $data ) < 4 ) {
      return false;
    }
    if ( ':' !== $data[1] ) {
      return false;
    }
    if ( $strict ) {
      $lastc = substr( $data, -1 );
      if ( ';' !== $lastc && '}' !== $lastc ) {
        return false;
      }
    } else {
      $semicolon = strpos( $data, ';' );
      $brace     = strpos( $data, '}' );
      // Either ; or } must exist.
      if ( false === $semicolon && false === $brace )
        return false;
      // But neither must be in the first X characters.
      if ( false !== $semicolon && $semicolon < 3 )
        return false;
      if ( false !== $brace && $brace < 4 )
        return false;
    }
    $token = $data[0];
    switch ( $token ) {
      case 's' :
        if ( $strict ) {
          if ( '"' !== substr( $data, -2, 1 ) ) {
            return false;
          }
        } elseif ( false === strpos( $data, '"' ) ) {
          return false;
        }
        // or else fall through
      case 'a' :
      case 'O' :
        return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
      case 'b' :
      case 'i' :
      case 'd' :
        $end = $strict ? '$' : '';
        return (bool) preg_match( "/^{$token}:[0-9.E-]+;$end/", $data );
    }
    return false;
  },
    'request'=>function($ch,$url,$headers,$method='GET',$params=null){
        $ckfile =tempnam ("/tmp", "CURLCOOKIE");
        $headerdata=[];
        foreach($headers as $k=>$v){
          $headerdata[]=$k.': '.$v;
        }
        //curl init        
        
        curl_setopt($ch, CURLOPT_URL,$url);
        //curl_setopt($ch, CURLOPT_PROXY, '127.0.0.1:8888');
        curl_setopt ($ch, CURLOPT_COOKIEJAR, $ckfile);
        curl_setopt ($ch, CURLOPT_COOKIEFILE, $ckfile);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt ( $ch , CURLOPT_VERBOSE , 1 );
        curl_setopt ( $ch , CURLOPT_HEADER , 1 );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerdata);
        if(isset($method) && $method=='POST'){

          curl_setopt($ch, CURLOPT_POST, 1);
          if(!empty($params)){
            $paramsstr="";
            if(is_array($params)){
              foreach($params as $k=>$v){
                $paramsstr.='&'.$k.'='.urlencode($v);
              }
              $paramsstr=substr($paramsstr, 1);
            }
            else{
              $paramsstr=$params;
            }

            curl_setopt($ch, CURLOPT_POSTFIELDS,$paramsstr);
          }
        }
        else{
          curl_setopt($ch, CURLOPT_POST, 0);
        }
        $response = curl_exec ($ch);
        $header_size = curl_getinfo($ch,CURLINFO_HEADER_SIZE);
        $result['header'] = substr($response, 0, $header_size);
        foreach(explode("\r\n", $result['header']) as $k=>$v){
          //skip empty row
          if(empty($v))continue;
          //get code respone:
          if(strpos($v, 'HTTP/1.1')!==false){
            $result['code']=explode(' ', $v)[1];
          }
          else{
            $tmp=explode(':',$v);
            if(count($tmp)>1){
              $result[strtolower(trim($tmp[0]))]=trim($tmp[1]);
            }
          }
        }
        $result['body'] = substr( $response, $header_size );
        //gzdecode:
        if(strpos($result['header'], 'Content-Encoding: gzip')!==false) $result['body'] =gzdecode( $result['body'] );
        return $result;
    },
    'request2'=>function($url,$option){
        //$ckfile =tempnam ("/tmp", "CURLCOOKIE");
      $ckfile =isset($option['cookies'])?$option['cookies']:tempnam ("/tmp", "CURLCOOKIE");
      
      
      $headers['X-Requested-With'] = 'XMLHttpRequest';
      $headers['Connection'] = 'keep-alive';
      $headers['User-Agent'] = 'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:28.0) Gecko/20100101 Firefox/28.0';
      if(isset($option['method']) && $option['method']=='POST'){
        $headers['Content-Type'] = 'application/x-www-form-urlencoded';            
      }
      $headers['Accept-Encoding'] = 'gzip, deflate';
      $headers['Accept-Language'] = 'en-US,en;q=0.5';
      $headers['Cache-Control'] = 'no-cache';   
      $headers['Accept'] = 'application/json, text/javascript, */*; q=0.01';
    if(isset($option['headers'])){
        $headers=array_merge($headers,$option['headers']);  
      }
        $headerdata=[];
        if(isset($option['headers'])){
          foreach($option['headers'] as $k=>$v){
            $headerdata[]=$k.': '.$v;
          }
        }
        //curl init        
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        //curl_setopt($ch, CURLOPT_PROXY, '127.0.0.1:8888');

        curl_setopt ($ch, CURLOPT_COOKIEJAR, $ckfile);
        curl_setopt ($ch, CURLOPT_COOKIEFILE, $ckfile);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,TRUE);
        if(isset($option['redirection'])){
          curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        }
        
        if(isset($option['isVerifySSL']) && $option['isVerifySSL']){
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        }
        else{
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }
        curl_setopt ( $ch , CURLOPT_VERBOSE , 1 );
        curl_setopt ( $ch , CURLOPT_HEADER , 1 );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerdata);
        if(isset($option['method']) && $option['method']=='POST'){

          curl_setopt($ch, CURLOPT_POST, 1);
          if(isset($option['params'])){
            $params="";
            if(is_array($option['params'])){
              foreach($option['params'] as $k=>$v){
                $params.='&'.$k.'='.urlencode($v);
              }
              $params=substr($params, 1);
            }
            else{
              $params=$option['params'];
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS,$params);
          }
        }
        else{
          curl_setopt($ch, CURLOPT_POST, 0);
        }
        $response = curl_exec ($ch);
        $header_size = curl_getinfo($ch,CURLINFO_HEADER_SIZE);
        curl_close($ch);
        $result['header'] = substr($response, 0, $header_size);

        //get header content
        //print_r(explode("\r\n", $result['header']));exit;
        foreach(explode("\r\n", $result['header']) as $k=>$v){
          //skip empty row
          if(empty($v))continue;
          //get code respone:
          if(strpos($v, 'HTTP/1.1')!==false){
            $result['code']=explode(' ', $v)[1];
          }
          else{
            $tmp=explode(':',$v);
            if(count($tmp)>1){
              $result[strtolower(trim($tmp[0]))]=trim($tmp[1]);
            }
          }
        }
        $result['body'] = substr( $response, $header_size );
        //gzdecode:
        if(strpos($result['header'], 'Content-Encoding: gzip')!==false) $result['body'] =gzdecode( $result['body'] );
        
        return $result;
    },
    'fixBigNumber'=>function($item,$isSaveData=0){
        
        if($item['name']=='facebook' || $item['name']=='blogger'){
            if($isSaveData){
                if(isset($item["field1"]))$item["field1"] = 'fb'.$item["field1"];
                if(isset($item["field2"]))$item["field2"] = 'fb'.$item["field2"];
                if(isset($item["field3"]))$item["field3"] = 'fb'.$item["field3"];
                if(isset($item["field4"]))$item["field4"] = 'fb'.$item["field4"];
                if(isset($item["field5"]))$item["field5"] = 'fb'.$item["field5"];
            }
            else{
                if(isset($item["field1"]))$item["field1"] = str_replace('fb','',$item["field1"]);
                if(isset($item["field2"]))$item["field2"] = str_replace('fb','',$item["field2"]);
                if(isset($item["field3"]))$item["field3"] = str_replace('fb','',$item["field3"]);
                if(isset($item["field4"]))$item["field4"] = str_replace('fb','',$item["field4"]);
                if(isset($item["field5"]))$item["field5"] = str_replace('fb','',$item["field5"]);
            }
        }
        return $item;
    },
    'CutFromTo'=>function($string, $from, $to){
      $fstart = stripos($string, $from); $tmp = substr($string,$fstart+strlen($from)); $flen = stripos($tmp, $to);  return substr($tmp,0, $flen);
    },
    'postStumbleupon'=>function($data){
        $item=$this->app->db->findOne("social/accs", ['name'=>'stumbleupon']);

        if ( isset($item) ) {
             $headers = array();
            $headers['X-Requested-With'] = 'XMLHttpRequest';
            $headers['Connection'] = 'keep-alive';
            $headers['Referer'] = 'https://www.stumbleupon.com';
            $headers['User-Agent'] = 'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:28.0) Gecko/20100101 Firefox/28.0';
            $headers['Content-Type'] = 'application/x-www-form-urlencoded';
            $headers['Accept'] = 'application/json, text/javascript, */*; q=0.01';
            //$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
            $headers['Origin'] = 'https://www.stumbleupon.com';
            $headers['Accept-Encoding'] = 'gzip, deflate';
            $headers['Accept-Language'] = 'en-US,en;q=0.5';
            $headers['Cache-Control'] = 'no-cache';
            $ch = curl_init();
            $result=$this->app->module('socials')->request($ch,'https://www.stumbleupon.com/login',$headers);
            

            //do login
            $frmTxt = $this->app->module('socials')->CutFromTo($result['body'], '<form id="login-form"','</form>'); 
            $md = array(); $flds  = array();  $mids = '';
            while (stripos($frmTxt, '<input')!==false){ 
                    $inpField = trim($this->app->module('socials')->CutFromTo($frmTxt,'<input', '>')); 
                    $name = trim($this->app->module('socials')->CutFromTo($inpField,'name="', '"'));
                    if ( stripos($inpField, '"hidden"')!==false && $name!='' && !in_array($name, $md)) 
                    { 
                        $md[] = $name; $val = trim($this->app->module('socials')->CutFromTo($inpField,'value="', '"')); 
                        $vars[$name]= $val; $mids .= "&".$name."=".$val;
                    }
                    $frmTxt = substr($frmTxt, stripos($frmTxt, '<input')+8);
                  }
                  
            $vars['user']=$item['username'];
            $vars['pass']=$item['password'];
            $vars['remember']='true';
            $vars['nativeSubmit']='0';
            $vars['_method']='create';
            $vars['_output']='Json';
            
            

            $result=$this->app->module('socials')->request($ch,'https://www.stumbleupon.com/login?_nospa=true',$headers,'POST',$vars);
            
            //print_r($vars);exit;
            $datars=json_decode($result['body']);
            
            if($datars->_success=='true'){
                //do post
                $headers['Referer'] = 'https://www.stumbleupon.com/submit';
                $result=$this->app->module('socials')->request($ch,'https://www.stumbleupon.com/submit',$headers);

                $frmTxt = $this->app->module('socials')->CutFromTo($result['body'], '<form method="post" id="submit-form"', '</form>');
                $md = array();
                $flds = array();
                $mids = ''; // prr($contents);
                while (stripos($frmTxt, '<input') !== false) {
                    $inpField = trim($this->app->module('socials')->CutFromTo($frmTxt, '<input', '>'));
                    $name = trim($this->app->module('socials')->CutFromTo($inpField, 'name="', '"'));
                    if (stripos($inpField, '"hidden"') !== false && $name != '' && !in_array($name, $md)) {
                        $md[] = $name;
                        $val = trim($this->app->module('socials')->CutFromTo($inpField, 'value="', '"'));
                        $flds[$name] = $val;
                        $mids .= "&".$name."=".$val;
                    }
                    $frmTxt = substr($frmTxt, stripos($frmTxt, '<input') + 8);
                }
                $flds['url'] = $data['link'];
                $flds['review'] = $data['message'];
                $flds['tags'] = $item['field1'];
                $flds['nsfw'] = false;
                $flds['user-tags'] = $data['tags'];
                $flds['_output'] = 'Json';
                $flds['_method'] = 'create';
                $flds['language'] = 'EN';

                //print_r($flds);exit;
                $result=$this->app->module('socials')->request($ch,'https://www.stumbleupon.com/submit',$headers,'POST',$flds);
                curl_close($ch);
                print_r($result);exit;
            }
            else{
                return json_encode(['error'=>implode(',',$datars->_reason)]);
            }

      }   
    },
    'postDiigo'=>function($data){
        $item=$this->app->db->findOne("social/accs", ['name'=>'diigo']);

        if ( isset($item) ) {
             $headers = array();
            $headers['X-Requested-With'] = 'XMLHttpRequest';
            $headers['Connection'] = 'keep-alive';
            $headers['Referer'] = 'https://secure.diigo.com/api/v2/bookmarks';
            $headers['User-Agent'] = 'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:28.0) Gecko/20100101 Firefox/28.0';
            $headers['Content-Type'] = 'application/x-www-form-urlencoded';
            $headers['Accept'] = 'application/json, text/javascript, */*; q=0.01';
            //$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
            $headers['Origin'] = 'https://secure.diigo.com/api/v2/bookmarks';
            $headers['Accept-Encoding'] = 'gzip, deflate';
            $headers['Accept-Language'] = 'en-US,en;q=0.5';
            $headers['Cache-Control'] = 'no-cache';
            $headers['Authorization'] =  'Basic '.base64_encode($item['username'].':'.$item['password']);
            
            $ch = curl_init();
          
            $flds['key']=$item['field1']; 
            $flds['url']=$data['url']; 
            $flds['title']=$this->app->module('posts')->cutstring($data['title'], 250); 
            $flds['desc']=$this->app->module('posts')->cutstring($data['description'], 250); 
            $flds['tags']=$data['tags']; 
            $flds['shared']='yes';
            

            $result=$this->app->module('socials')->request($ch,'https://secure.diigo.com/api/v2/bookmarks',$headers,'POST',$flds);
            
           curl_close($ch);
            $datars=json_decode($result['body'],true);
            
            if(isset($datars['code']) && $datars['code']=='1'){
                return json_encode(['success'=>1,'url'=>$item['field5']]);               
                
            }
            else{
                return json_encode(['error'=>$datars->message]);
            }

      }   
    },
    'postFlickr'=>function($data){
        $item=$this->app->db->findOne("social/accs", ['name'=>'flickr']);

        if ( isset($item) ) {
            if(!isset($item['userURL']) || empty($item['userURL']))return json_encode(['error'=>'not verify']);
            //print_r(COCKPIT_DIR);exit;
            require_once(COCKPIT_DIR.'/vendor/tumblroauth/scOAuth.php');   
            $tum_oauth = new \wpScoopITOAuth($item['field1'], $item['field2'],$item['accessToken'], $item['accessTokenSec']);
            $tum_oauth->cockapp=$this->app;
            $tum_oauth->baseURL = 'https://www.flickr.com/services'; $tum_oauth->request_token_path = '/oauth/request_token'; $tum_oauth->access_token_path = '/oauth/access_token';
      //return json_encode(['error'=>json_encode($data)]);
            $postArr = array('title'=>$data['title'], 'description'=>$data['description'], 'tags'=>$data['tags'], 'is_public'=>1, 'safety_level'=>1, 'content_type'=>1, 'hidden'=>1);
            $imgFile = $this->createFile($data['imgURL']);  
            if (empty($imgFile) || is_array($imgFile)) { $badOut['Error'] = 'Image Error - '.print_r($imgFile, true); return json_encode(['error'=>$badOut]); }
            
            $phiID = $tum_oauth->flUploadPhoto($imgFile, $postArr);
            //print_r($phiID);exit;
            if(strpos($phiID, 'Problem:')!==false){
                
                return json_encode(['error'=>$phiID]);
                
            }
            else{
                return json_encode(['success'=>1,'url'=>str_ireplace('people', 'photos', $item['userURL']).$phiID]);
            }

      }   
    },
    'postScoopit'=>function($data){
        $item=$this->app->db->findOne("social/accs", ['name'=>'scoopit']);

        if ( isset($item) ) {
            if(!isset($item['accessToken']) || empty($item['accessToken']))return json_encode(['error'=>'not verify']);
            //print_r(COCKPIT_DIR);exit;
            require_once(COCKPIT_DIR.'/vendor/tumblroauth/scOAuth.php'); 
            //print_r($item);exit;  
            $tum_oauth = new \wpScoopITOAuth($item['field1'], $item['field2'],$item['accessToken'], $item['accessTokenSec']);
            $tum_oauth->cockapp=$this->app;
            //check topicurl:
            //print_r($data['imgURL']);exit;
            $tiID = $tum_oauth->makeReq('http://www.scoop.it/api/1/topic', array('urlName'=>$item['field3'])); 
            
            if (!empty($tiID) && is_array($tiID) && !empty($tiID['topic']) && !empty($tiID['topic']['id'])) 
              {
                $topicID = $tiID['topic']['id'];
                $creatorID=$tiID['topic']['creator']['id'];
            }
            else
              return json_encode(['error'=>$tiID]);

            $postArr = array('action'=>'create', 'title'=>$data['title'], 'content'=>$data['content'], 'url'=>$data['url'], 'imageUrl'=>$data['imgURL'], 'topicId'=>$topicID);  
            
            $postinfo = $tum_oauth->makeReq('http://www.scoop.it/api/1/post', $postArr,'POST');
            
            if (is_array($postinfo) && isset($postinfo['post'])) { $apNewPostID = $postinfo['post']['id']; $apNewPostURL = $postinfo['post']['scoopUrl']; 
              if (isset($data['tags'])) { $postArr = array('action'=>'edit', 'tag'=>$data['tags'], 'id'=>$apNewPostID);  
                $postinfo = $tum_oauth->makeReq('http://www.scoop.it/api/1/post', $postArr, 'POST'); 
              }
                    
            } 
            $code = $tum_oauth->http_code;

            if($code==200){
                
                return json_encode(['error'=>$postinfo]);
                
            }
            else{
                return json_encode(['success'=>1,'url'=>$postinfo['post']['scoopUrl']]);
            }

      }   
    },
    'postPlurk'=>function($data){
        $item=$this->app->db->findOne("social/accs", ['name'=>'plurk']);

        if ( isset($item) ) {
            if(!isset($item['pkAccessTocken']) || empty($item['pkAccessTocken']))return json_encode(['error'=>'not verify']);
            require_once(COCKPIT_DIR.'/vendor/tumblroauth/plurkOAuth.php');            
            $tum_oauth = new \wpPlurkOAuth($item['field2'], $item['field3'],$item['pkAccessTocken'], $item['pkAccessTockenSec']);
            $tum_oauth->cockapp=$this->app;
            $pkURL = trim(str_ireplace('http://', '', $item['field1'])); 
            if (substr($pkURL,-1)=='/') $pkURL = substr($pkURL,0,-1);     
            //if ($options['pkCat']=='') $options['pkCat'] = ':';                
          
            $postArr = array('content'=>$this->app->module('posts')->cutstring($data['message'],300), 'qualifier'=>':');
            $postinfo = $tum_oauth->makeReq('http://www.plurk.com/APP/Timeline/plurkAdd', $postArr);
            //print_r($postinfo);exit;
            if(isset($postinfo['plurk_id'])){
                $alphabet = str_split("0123456789abcdefghijklmnopqrstuvwxyz"); $shorten = ''; $plurk_id = $postinfo['plurk_id'];
                while ($plurk_id != 0){ $i = $plurk_id % 36; $plurk_id = intval($plurk_id / 36); $shorten = $alphabet[$i].$shorten;}  
                $link = 'http://www.plurk.com/p/'.$shorten;
                 return json_encode(['success'=>1,'url'=>$link]);
            }
            else{
               return json_encode(['error'=>$postinfo['error_text']]);
            }

      }   
    },
    'postKippt'=>function($data){
        $item=$this->app->db->findOne("social/accs", ['name'=>'kippt']);

        if ( isset($item) ) {
            $headers = array();
            $headers['X-Requested-With'] = 'XMLHttpRequest';
            $headers['Connection'] = 'keep-alive';
            $headers['Referer'] = 'http://kippt.com';
            $headers['User-Agent'] = 'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:28.0) Gecko/20100101 Firefox/28.0';
            $headers['Content-Type'] = 'application/x-www-form-urlencoded';
            $headers['Accept'] = 'application/json, text/javascript, */*; q=0.01';
            //$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
            $headers['Origin'] = 'http://kippt.com';
            $headers['Accept-Encoding'] = 'gzip, deflate';
            $headers['Accept-Language'] = 'en-US,en;q=0.5';
            $headers['Cache-Control'] = 'no-cache';            
            $headers['X-Kippt-Username'] =  $item['username'];
            $headers['X-Kippt-API-Token'] =  $item['field1'];
            $ch = curl_init();
            $flds = array();  
            $flds['url']=$data['url']; 
            $flds['notes']=$data['message']; 
            $flds['title']=$data['title']; 
            $flds['list']=$item['listID'];//listid
            $flds = json_encode($flds);

            $result=$this->app->module('socials')->request($ch,'https://kippt.com/api/clips/',$headers,'POST',$flds);
            curl_close($ch);
           
            $datars=json_decode($result['body'],true);
            
            
            if(in_array($result['code'],$this->app->module('socials')->accept_result_code())){
                return json_encode(['success'=>1,'url'=>'https://kippt.com'.$datars['app_url']]);
            }
            else{
                return json_encode(['error'=>$result['body']]);
            }

      }   
    },
    'postDelicious'=>function($data){
        $item=$this->app->db->findOne("social/accs", ['name'=>'delicious']);

        if ( isset($item) ) {
             $headers = array();
            $headers = array();
            $headers['X-Requested-With'] = 'XMLHttpRequest';
            $headers['Connection'] = 'keep-alive';
            $headers['Referer'] = 'https://delicious.com';
            $headers['User-Agent'] = 'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:28.0) Gecko/20100101 Firefox/28.0';
            $headers['Content-Type'] = 'application/x-www-form-urlencoded';
            $headers['Accept'] = 'application/json, text/javascript, */*; q=0.01';
            //$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
            $headers['Origin'] = 'https://delicious.com';
            $headers['Accept-Encoding'] = 'gzip, deflate';
            $headers['Accept-Language'] = 'en-US,en;q=0.5';
            $headers['Cache-Control'] = 'no-cache';            
            
            $flds = array('username'=>$item['username'], 'password'=>base64_encode(strrev($item['password'])));
            $ch = curl_init();            
            $result=$this->app->module('socials')->request($ch,'https://avosapi.delicious.com/api/v1/account/login',$headers,'POST',$flds);
            if($result['code']=='200' ){
              $datars=json_decode($result['body']);
              $flds = array('url'=>$data['url'], 
                'description'=>$data['description'], 
                'tags'=>$data['tags'], 
                'note'=>$data['note'], 
                'replace'=>'true', 
                'private'=>'false', 
                'share'=>'true');

              $result=$this->app->module('socials')->request($ch,'https://avosapi.delicious.com/api/v1/posts/addoredit',$headers,'POST',$flds);
              curl_close($ch);
              $datars=json_decode($result['body']);
              //print_r($result);exit;
              if($result['code']=='200' ){
                return json_encode(['success'=>1,'url'=>$item['field5']]);
              }
              else{
                return json_encode(['error'=>$result]);
              }
            }
            else{
                curl_close($ch);
                return json_encode(['error'=>$result]);
            }

      }   
    },
    'postTumblr'=>function($data){
        $item=$this->app->db->findOne("social/accs", ['name'=>'tumblr']);

        if ( isset($item) && isset( $item['oauth_token'] ) ) {
            $post_URI = 'http://api.tumblr.com/v2/blog/'.$item['field3'].'/post';

            $tum_oauth = new \tumblroauth\TumblrOAuth($item['field1'],$item['field2'],$item['oauth_token'],$item['oauth_token_secret']);

            // // Make an API call with the TumblrOAuth instance. For text Post, pass parameters of type, title, and body
            // $parameters = array();
            // $parameters['type'] = "text";
            // $parameters['title'] = "title text";
            // $parameters['body'] = "body text";
            $data['date']=date('Y-m-d h:s:i',time()).' GMT';
            
            $post = $tum_oauth->post($post_URI,$data);
            
            if($tum_oauth->http_code==201){
                return json_encode(['success'=>1,'url'=>'http://'.$item['field3'].'/post/'.$post->response->id]);
            }
            else{
                return json_encode(['error'=>$post->meta->msg]);
              
            }  

      }   
    },

    'postLivejournal'=>function($data){
        $item=$this->app->db->findOne("social/accs", ['name'=>'livejournal']);

        if ( isset($item)) {
            require_once (COCKPIT_DIR.'/vendor/tumblroauth/xmlrpc-client.php');
                     
            //$result=$this->app->module('socials')->request2('https://api.linkedin.com/v1/people/~',['headers'=>$headers]);
            $nxsToLJclient = new \NXS_XMLRPC_Client('http://www.livejournal.com/interface/xmlrpc'); 
            $nxsToLJclient->debug = false;   

            $date = time(); 
            $year = date("Y", $date); 
            $mon = date("m", $date); 
            $day = date("d", $date); 
            $hour = date("G", $date); 
            $min = date("i", $date);
            $nxsToLJContent = array( "username" => $item['username'], "password" => $item['password'], "event" => $data['message'], "subject" =>  $data['title'], "lineendings" => "unix", "year" => $year, "mon" => $mon, "day" => $day, "hour" => $hour, "min" => $min, "ver" => 2);      
            $nxsToLJContent["usejournal"] = $item['field1'];  //blogID
            $nxsToLJContent['props'] = array('taglist' => $data['tags']);
            if(!$nxsToLJclient->query('LJ.XMLRPC.postevent', $nxsToLJContent)){
                return json_encode(['error'=>'Something went wrong - '.$nxsToLJclient->getErrorCode().' : '.$nxsToLJclient->getErrorMessage()]);
            }
            $result = $nxsToLJclient->getResponse();
            
            if(isset($result['url'])){
                return json_encode(['success'=>1,'url'=>$result['url']]);
            }
            else{
                return json_encode(['error'=>'Something went wrong']);
            } 
      }   
    },
    'postLinkedin'=>function($data){
        $item=$this->app->db->findOne("social/accs", ['name'=>'linkedin']);

        if ( isset($item)) {
            
      $headers['Referer'] = 'https://api.linkedin.com';
      $headers['Content-Type'] = 'application/json';
            $headers['Origin'] = 'https://api.linkedin.com';
      $headers['x-li-format'] = 'json';
      
      $datapost=[];
      $datapost['comment']=$data['title'];
      $datapost['content']=[];
      $datapost['content']['title']='';
      $datapost['content']['description']=$data['description'];
      $datapost['content']['submitted-url']=$data['url'];
      $datapost['content']['submitted-image-url']=$data['imgURL'];
      $datapost['visibility']=[];
      $datapost['visibility']['code']='anyone';
      
      
            $result=$this->app->module('socials')->request2('https://api.linkedin.com/v1/people/~/shares?format=json&oauth2_access_token='.$item['access_token'],['headers'=>$headers,'params'=>json_encode($datapost),'method'=>'POST']);
           
            
            if($result['code']==200 || $result['code']==201 ){
                return json_encode(['success'=>1,'url'=>json_decode($result['body'],true)['updateUrl']]);
            }
            else{
                return json_encode(['error'=>'Something went wrong']);
            } 
      }   
    },
    'postBlogger'=>function($data){
      $item=$this->app->db->findOne("social/accs", ['name'=>'blogger']);

      if ( isset($item)  && isset($item['access_token'])) {
        $item=$this->app->module('socials')->fixBigNumber($item);
        require_once(COCKPIT_DIR.'/vendor/Google/autoload.php');  
        $client = new \Google_Client();
        $client->setClientId($item['field1']);
        $client->setClientSecret($item['field2']);        
        $client->setAccessToken($item['access_token']);
        //refresh token
        $client->refreshToken($client->getRefreshToken());
        $ac=$client->getAccessToken();
        $client->setAccessToken($ac);

        if($client->isAccessTokenExpired()){
            return json_encode(['error'=>'Access token expired']);
        }

        $blogger = new \Google_Service_Blogger($client);        
        $dataPost=new \Google_Service_Blogger_Post();
        $dataPost->setTitle($data['title']);
        //$dataPost->setTitleLink($data['url']);
        $dataPost->setEtag($data['tags']);
        $dataPost->setContent($data['description']);

        //$dataPost=['title'=>$data['title'],'image'=>[$data['imgURL']],'url'=>$data['url'],'content'=>$data['description'],'etag'=>$data['tags']];
        $blog=$blogger->posts->insert($item['field4'],$dataPost);
        if(isset($blog->url)){
          return json_encode(['success'=>1,'url'=>$blog->url]);
        }
        else{
          return json_encode(['error'=>$blog]);
        }
        
        
      }  
      else{
        return json_encode(['error'=>'not have access token']);
      } 
    },
    'postWordpress'=>function($dataPost){
        $item=$this->app->db->findOne("social/accs", ['name'=>'wordpress']);

        if ( isset($item)) {
            require_once (COCKPIT_DIR.'/vendor/tumblroauth/xmlrpc-client.php');
                     
            //$result=$this->app->module('socials')->request2('https://api.linkedin.com/v1/people/~',['headers'=>$headers]);
            $nxsToWPclient = new \NXS_XMLRPC_Client($item['field1']); 
            $nxsToWPclient->debug = false;  
            //check image
            $imgURL=$dataPost['imgURL'];
            
            if ($imgURL!=='' && stripos($imgURL, 'http')!==false) {      
              // $handle = fopen($imgURL, "rb"); $filedata = ''; while (!feof($handle)) {$filedata .= fread($handle, 8192);} fclose($handle);
              $filedata = $this->app->module('socials')->request2($imgURL,[]); 
              $filedata = $filedata['body']; // echo "AWC?";
              $orID=time();
              $data = array('name'  => 'image-'.$orID.'.jpg', 'type'  => 'image/jpg', 'bits'  => new NXS_XMLRPC_Base64($filedata), true); 
              $status = $nxsToWPclient->query('metaWeblog.newMediaObject', $orID, $item['username'], $item['password'], $data);  
              $imgResp = $nxsToWPclient->getResponse();  $gid = $imgResp['id'];
            } else $gid = '';


            //post
            $ext = substr($dataPost['description'], 0, 1000);  
            $nxsToWPContent = array('title'=>$dataPost['title'], 'description'=>$dataPost['description'], 'post_status'=>'draft', 'mt_excerpt'=>$ext, 'mt_allow_comments'=>1, 'mt_allow_pings'=>1, 'post_type'=>'post', 'mt_keywords'=>$dataPost['tags'], 'categories'=>'', 'custom_fields' =>  '');
            $params = array(0, $item['username'], $item['password'], $nxsToWPContent, true);
            if (!$nxsToWPclient->query('metaWeblog.newPost', $params)) { $ret = 'Something went wrong - '.$nxsToWPclient->getErrorCode().' : '.$nxsToWPclient->getErrorMessage();} else $ret = 'OK';
            $pid = $nxsToWPclient->getResponse();  
         
            if ($gid!='') {      
              $nxsToWPContent = array('post_thumbnail'=>$gid);  $params = array(0, $item['username'], $item['password'], $pid, $nxsToWPContent, true);      
              if (!$nxsToWPclient->query('wp.editPost', $params)) { $ret = 'Something went wrong - '.$nxsToWPclient->getErrorCode().' : '.$nxsToWPclient->getErrorMessage();} else $ret = 'OK';
            }
            $nxsToWPContent = array('post_status'=>'publish');  $params = array(0, $item['username'], $item['password'], $pid, $nxsToWPContent, true);      
            if (!$nxsToWPclient->query('wp.editPost', $params)) { $ret = 'Something went wrong - '.$nxsToWPclient->getErrorCode().' : '.$nxsToWPclient->getErrorMessage();} else $ret = 'OK';

            if ($ret!='OK') {
              return json_encode(['error'=>$ret]);
            } else { 
              $wpURL = str_ireplace('/xmlrpc.php','',$item['field1']); 
              if(substr($wpURL, -1)=='/') $wpURL=substr($wpURL, 0, -1); 
              $wpURL .= '/?p='.$pid; 
              return json_encode(['success'=>1,'postID'=>$pid, 'isPosted'=>1, 'url'=>$wpURL, 'pDate'=>date('Y-m-d H:i:s')]);              
            }

           
      }   
    },

    'postFB'=>function($data){
        $item=$this->app->db->findOne("social/accs", ['name'=>'facebook']);

        if ( isset($item) && isset( $item['token'] ) ) {

            $item=$this->app->module('socials')->fixBigNumber($item);

            \Facebook\FacebookSession::setDefaultApplication($item['field1'], $item['field2']);
              // create new session from saved access_token
              $session = new \Facebook\FacebookSession( $item['token'] );
              
              // validate the access_token to make sure it's still valid
              try {
                if ( !$session->validate() ) {
                  $session = null;

                }
              } catch ( Exception $e ) {
                // catch any exceptions

               
                $session = null;
              }

              if ( isset( $session ) ) {  
 
              
                  // $data['name'] = 'Facebook API: Posting As A Page using Graph API v2.x and PHP SDK 4.0.x';
                  //       $data['link'] = 'http://dayxam.com/khoa-phun-theu-tham-my-khuyen-mai-lon-2015/';
                  //       $data['caption'] = 'The Facebook API lets you post to Pages you administrate via the API. This tutorial shows you how to achieve this using the Facebook PHP SDK v4.0.x and Graph API 2.x.';
                  //       $data['message'] = 'Check out my new blog post!';
                unset($data['url']);
                unset($data['tags']);
                unset($data['imgURL']);
                unset($data['title']);
                $page_post = (new \Facebook\FacebookRequest( $session, 'POST', '/me/feed', $data ))->execute()->getResponse();//->getGraphObject()->asArray();
                
                if(isset($page_post->id)){
                    return json_encode(['success'=>1,'url'=>'https://www.facebook.com/'.$page_post->id]);
                }
                else{
                    $raw=array_keys($page_post)[0];
                    $raw=json_decode(substr($raw,0,strlen($raw)-1),true);
                    
                    return json_encode(['error'=>$raw]);
                }
                
              }
               
            }  

        return 0;
    },
    'postTwitter'=>function($data){
        $item=$this->app->db->findOne("social/accs", ['name'=>'twitter']);
        if ( isset($item)) {
          $item=$this->app->module('socials')->fixBigNumber($item);
            require_once(COCKPIT_DIR.'/vendor/tumblroauth/tmhOAuth.php');               
            $hdrsArr['User-Agent']='Mozilla/5.0 (Windows NT 6.1; WOW64; rv:32.0) Gecko/20100101 Firefox/32.0'; 
            $advSet=array('headers'=>$hdrsArr,'httpversion'=>'1.1','timeout'=>45,'sslverify'=>false);
            $tmhOAuth = new \NXS_tmhOAuth(array( 'consumer_key' => $item['field1'], 'consumer_secret' => $item['field2'], 'user_token' =>$item['field3'], 'user_secret' =>$item['field4']));  
            $isMedia=false;
            $params_array =array('status' => $data['message']); 
            if(isset($data['imgURL']) && $data['imgURL']!=''){
              $imgURL = str_replace(' ', '%20', $data['imgURL']);
              $imgRS=$this->app->module('socials')->request2($imgURL,$advSet);
              $img='';
              if( isset($imgRS['body']))$img=$imgRS['body'];
              if($img!=''){
                $params_array['media[]'] = $img;
                $isMedia=true;
              }
                         
            }
            if($isMedia){
              $code = $tmhOAuth -> request('POST', 'https://api.twitter.com/1.1/statuses/update_with_media.json', $params_array, true, true);    
            }
            else{
              $code = $tmhOAuth->request('POST', $tmhOAuth->url('1.1/statuses/update'), $params_array);
            }

            //check response with media
            if ( $code=='403' && stripos($tmhOAuth->response['response'], 'User is over daily photo limit')!==false && $isMedia!=false) { 
              unset($params_array['media[]']);
              $code = $tmhOAuth->request('POST', $tmhOAuth->url('1.1/statuses/update'), $params_array);
            }

            if ($code == 200){
               $twResp = json_decode($tmhOAuth->response['response'], true);  if (is_array($twResp) && isset($twResp['id_str'])) $twNewPostID = $twResp['id_str'];  
               if (is_array($twResp) && isset($twResp['user'])) $twPageID = $twResp['user']['screen_name'];
               return json_encode(['success'=>1,'url'=>'https://twitter.com/'.$twPageID.'/status/'.$twNewPostID]);
               
            } else { 
              $badOut= "Resp: ".print_r($tmhOAuth->response['response'], true)."| Error: ".print_r($tmhOAuth->response['error'], true); 
              return json_encode(['error'=>$badOut]);
              
            }

        } 

        return 0;
    },
    'postVk'=>function($data){
        $item=$this->app->db->findOne("social/accs", ['name'=>'vk']);

        if ( isset($item) ) {
            if(!isset($item['field2']) )return json_encode(['error'=>'not verify']);
            //print_r($data);exit;
            //post image
            //print_r($GLOBALS);exit;
            $imgUpld=false;
            if(isset($data['imgURL']) && $data['imgURL']!=''){
              $imgURL=$data['imgURL'];
              $postUrl = 'https://api.vkontakte.ru/method/photos.getWallUploadServer?gid='.(str_replace('-','',$item['pgIntID'])).'&access_token='.$item['vkAppAuthToken'];
              $response = $this->app->module('socials')->request2($postUrl,[]); 
              $thumbUploadUrl = $response['body'];    
              if (!empty($thumbUploadUrl)) { $thumbUploadUrlObj = json_decode($thumbUploadUrl); $VKuploadUrl = $thumbUploadUrlObj->response->upload_url; }   // prr($thumbUploadUrlObj); echo "UURL=====-----";
              if (!empty($VKuploadUrl)) {    
                // if (stripos($VKuploadUrl, '//pu.vkontakte.ru/c')!==false) { $c = 'c'.CutFromTo($VKuploadUrl, '.ru/c', '/'); $VKuploadUrl = str_ireplace('/pu.','/'.$c.'.',str_ireplace($c.'/','',$VKuploadUrl)); }
                $remImgURL = urldecode($imgURL); $urlParced = pathinfo($remImgURL); $remImgURLFilename = $urlParced['basename']; $imgData = $this->app->module('socials')->request2($remImgURL,[]); $imgData = $imgData['body'];        
                $tmp=array_search('uri', @array_flip(stream_get_meta_data($GLOBALS[mt_rand()]=tmpfile())));  
                if (!is_writable($tmp)) return "Your temporary folder or file (file - ".$tmp.") is not writable. Can't upload image to VK";
                copy($tmp, $tmp.='.png'); register_shutdown_function(create_function('', "unlink('{$tmp}');"));       
                file_put_contents($tmp, $imgData); 
              
                $ch = curl_init(); curl_setopt($ch, CURLOPT_URL, $VKuploadUrl); curl_setopt($ch, CURLOPT_POST, 1); curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                
                if (function_exists('curl_file_create')) { $file  = curl_file_create($tmp); curl_setopt($ch, CURLOPT_POSTFIELDS, array('photo' => $file)); } 
                  else curl_setopt($ch, CURLOPT_POSTFIELDS, array('photo' => '@' . $tmp));

                $response = curl_exec($ch); $errmsg = curl_error($ch); curl_close($ch); //prr($response);
                
                $uploadResultObj = json_decode($response); // prr($response); //prr($uploadResultObj);
                
                if (!empty($uploadResultObj->server) && !empty($uploadResultObj->photo) && !empty($uploadResultObj->hash)) {
                  $postUrl = 'https://api.vkontakte.ru/method/photos.saveWallPhoto?server='.$uploadResultObj->server.'&photo='.$uploadResultObj->photo.'&hash='.$uploadResultObj->hash.'&gid='.(str_replace('-','',$item['pgIntID'])).'&access_token='.$item['vkAppAuthToken'];
                  $response = $this->app->module('socials')->request2($postUrl,[]);            
                  $resultObject = json_decode($response['body']); //prr($resultObject);
                  
                  if (isset($resultObject) && isset($resultObject->response[0]->id)) { $imgUpld= $resultObject->response[0]; } 
                }
              }
            }
            $atts='';
            if (is_object($imgUpld)){
              $atts = $imgUpld->id.',';
            }
            $atts.=$data['url'];
            $postUrl = 'https://api.vkontakte.ru/method/wall.post';
            $postArr = array('owner_id'=>$item['pgIntID'], 'access_token'=>$item['vkAppAuthToken'], 'from_group'=>'1', 'message'=>$data['message'], 'attachment'=>$atts);
            
            $response = $this->app->module('socials')->request2($postUrl, array('params' => $postArr,'method'=>'POST')); 
            if((is_object($response) && (isset($response->errors))) || (is_array($response) && stripos($response['body'],'"error":')!==false )) { 
               return json_encode(['error'=>print_r($response, true)]);

            } else { 
              $respJ = json_decode($response['body'], true);  
              $ret = $item['pgIntID'].'_'.$respJ['response']['post_id'];                 
              return json_encode(['success'=>1,'url'=>$item['field3'].'?w=wall'.$ret]);
            }
           

      }   
    }
]);


// ADMIN
if(COCKPIT_ADMIN) {
    //register acl
    $this("acl")->addResource("$moduleName", ['manage.index', 'manage.edit']);
    $app->on("admin.init", function() use($app,$moduleName){
        if (!$this->module('auth')->hasaccess($moduleName, ['manage.index', 'manage.edit'])) return;
        // bind routes
        
        $app->bindClass("$moduleName\\Controller\\Main", $moduleName);
        
        // bind api
        $app->bindClass("$moduleName\\Controller\\Api", "api/$moduleName");
        
        $user=$app->module("auth")->getUser();
        if($user['group']=='admin'){
             $app("admin")->menu("top", [
                "url"    => $app->routeUrl("/$moduleName"),
                "label"  => '<i class="uk-icon-users"></i>',
                "title"  => $app("i18n")->get("Socials")
            ],1);
        }
    });
    
}
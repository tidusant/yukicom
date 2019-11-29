<?php

$pathName=explode("\\",__DIR__);
if(count($pathName)<=1)$pathName=explode('/',__DIR__);
$moduleName=$pathName[count($pathName)-1];
// API for calling from outsite module.
$this->module($moduleName)->config=[];
$this->module($moduleName)->extend([
    
    "sitebaseurl"=>function(){
        return 'http://'.$_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT']=='80'?'':':'.$_SERVER['SERVER_PORT']);
    },  
    "siteurl"=>function(){
        return $this->app->module("builds")->sitebaseurl().COCKPIT_BASE_URL.'/output/';
    },  
   "t"=>function ($data,$exit=1) {
        echo '<pre style="text-align:left;">';
        print_r($data);
        echo '</pre>';
        if($exit)exit;
    },
    "output"=>function ($slug,$content,$uploadFiles,$oddnum=3) {

       $config=$this->app->module('builds')->getConfig();
        if($config['debug'])print_r('slug: '.$slug);
        if($config['debug'])print_r("\r\n");   
        $cachename=preg_replace('/[\/\.:\-]/is', '', $slug);
        if($config['debug'])print_r('replace /.: '.$cachename);
        if($config['debug'])print_r("\r\n");        
        $cachename=base64_encode($cachename);
        $cachename=preg_replace('/[=]/is', '', $cachename);
        if($config['debug'])print_r('base64 replace: '.$cachename);
        if($config['debug'])print_r("\r\n");
        $tmp=str_split ($cachename);
        $cachename1='';
        $cachename2='';
        for( $i=(count($tmp)-1);$i>=0;$i--)
            if($i%$oddnum==0)$cachename1.=$tmp[$i];
            else $cachename2.=$tmp[$i];
        $cachename=$cachename1.$cachename2;
        //$cachename=substr($cachename, 0,12);  
        if($config['debug'])print_r('oddnum '.$oddnum.': '.$cachename);
        if($config['debug'])print_r("\r\n");
        if($config['debug'])print_r("\r\n");
        $cachename=strtolower(substr($cachename, 0,1)).substr($cachename,1);
        $cachefolder=substr($cachename, 0,1);

        $this->app->module('builds')->createdir('output/'.$cachefolder);
        $cachedatafile=COCKPIT_DIR.'/output/'.$cachefolder.'/'.$cachename;            
        file_put_contents($cachedatafile, $content);
        $uploadFiles[]= './'.$cachefolder.'/'.$cachename; 
        return $uploadFiles;
    },
    "eb64"=>function($str,$oddnum=2){
        $str=preg_replace('/=/is', '', $str);
        $tmp=str_split ($str);
        $cachename1='';
        $cachename2='';
        for( $i=(count($tmp)-1);$i>=0;$i--)
            if($i%$oddnum==0)$cachename1.=$tmp[$i];
            else $cachename2.=$tmp[$i];
        $cachename=$cachename1.$cachename2;
        return $cachename;
    },
    "deb64"=>function($str64,$oddnum=2){
        $oddstr=substr($str64, 0,ceil(strlen($str64)/$oddnum));        
        $ukey=str_replace($oddstr, '', $str64);   
        $base64='';
        
        for($i=strlen($oddstr)-1;$i>=0;$i--){
            $base64.=substr($oddstr, strlen($oddstr)-1);
            $oddstr=substr($oddstr,0,strlen($oddstr)-1);
            
            if(strlen($ukey)-$oddnum+1>0)
                $base64.=strrev(substr($ukey, strlen($ukey)-$oddnum+1));
            else
                $base64.=strrev($ukey);
            
            $ukey=substr($ukey,0, strlen($ukey)-$oddnum+1);
            
        }
        return $base64;
    },
    "createdir"=>function($dir){

        //print_r($dir);
        //print_r("\r\n");
        $arrPaths=explode("/", $dir);
        $dir='.';
        foreach ($arrPaths as $key => $path) {
            if($path==".")continue;
            $dir.='/'.$path;
            // print_r($dir);
            // print_r("\r\n");
            if (!file_exists($dir))
                mkdir(strtolower($dir));
        }  
    },
    "removedir"=>function($dirname){
        array_map('unlink', glob("$dirname/*.*"));
        rmdir($dirname);

    },
    "getConfig"=>function(){
        if(!empty($this->module("builds")->config))return $this->module("builds")->config;
        else
        //prepare config
        $config=[];
        $isbuilddata=1;
        $dev=1;
        $host=$this->app->db->findOne('addons/hosts');
        if(!$host)return;
        $isbuilddata=$host['builddata'];
        $dev=$host['dev'];
        $disable=$host['disable'];

        $localurl='http://localhost/cockcms/';
        $sitetitle=$this->app->module("builds")->sitetitle();
        $live=$host['live'];
        $hostname=$host['hostname'];
        $username=$host['username'];
        $password=$host['password'];
        if(isset($host['sitetitle'])){
                $sitetitle=$host['sitetitle'];
        }
        if(isset($host['localurl'])){
                $localurl=$host['localurl'];
        }
        if(isset($host['siteurl'])){
                $siteurl=$host['siteurl'];
        }
        //get array of image size:
        $arrSize=$this->app->module("builds")->getArrSize();
        if(isset($host['arrimgsize'])){
            $arrSize=json_decode($host['arrimgsize'],true);
        }
        //get data in home:
        $noitemhome=0;       
        if(isset($host['noitemhome'])){
            $noitemhome=intval($host['noitemhome']);
        }
        $noitemcathome=0;       
        if(isset($host['noitemcathome'])){
            $noitemcathome=intval($host['noitemcathome']);
        }
        //cat show on home
        $catonhome=[];
        if(isset($host['catonhome'])){
            $catonhome=explode(',',$host['catonhome']);
        }
        $norelateitem=0;
        if(isset($host['norelateitem'])){
            $norelateitem=$host['norelateitem'];
        }
        $noitempercat=0;
        if(isset($host['noitempercat'])){
            $noitempercat=$host['noitempercat'];
        }
        $conf= compact('live',
            'hostname',
            'username',
            'password',
            'dev',
            'debug',
            'isbuilddata',
            'sitetitle',
            'sitedescription',
            'siteurl',
            'apiurl',
            'siteid',
            'localurl',
            'arrSize',
            'noitemhome',
            'noitemcathome',
            'catonhome',
            'disable',
            'norelateitem',
            'noitempercat',
            'custom_async_script',
            'custom_event',
            'theme','shortcode');
        if(!isset($host['theme']) || empty($host['theme']))$host['theme']='default';
        $conf=array_merge($conf,$host);

        //short code
        $conf['shortcode']=json_decode($host['shortcode'],true);

        //print_r($host);exit;
        //fix big number:
        $conf['FBId']=str_replace('fb', '', $conf['FBId']);

        return $conf;
    },
    "getArrSize"=>function(){
        return ['main'=>[0=>750,//default            
            480=>330,
            600=>550,
            768=>470,
            930=>617,
            1200=>750]];
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
    "numberToString"=>function($num){
        $rt='';
        $strarr=['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','x','y','z','w'];
        foreach ($num as $key => $value) {
            $rt.=$strarr[$value];
        }
        return $rt;
    },
    "pushfile"=>function($arrFiles){
        $config=$this->app->module('builds')->getConfig();
        //connect to host       
        //print_r($config);exit;
        if($config['live']!=true)return;

        // set up basic connection
        $conn_id = ftp_connect($config['hostname']);
        if($conn_id){
            
            // login with username and password
            if(!ftp_login($conn_id, $config['username'], $config['password'])){
                return '{"error":"cannot login to host '.$config['hostname'].' with username='.$config['username'].'"}';
            }
            
            //print_r('connected');exit;
            ftp_pasv($conn_id, true) ;
        }
        else{
            print_r('not connected');exit;
            return '{"error":"cannot connect to host '.$config['hostname'].'"}';                
        }
        
        foreach ($arrFiles as $key => $file) {
            $dirs=explode('/', str_replace('./', '', $file));
            $i=0;
            $chdir='.';

            for(;$i<count($dirs)-1;$i++){
                $chdir.='/'.$dirs[$i]; 
                   
                @ftp_mkdir($conn_id,  $chdir);                
            }
            $chdir.='/'.$dirs[$i];

            $remote_file ='ftp://'.$config['username'].':'.$config['password'].'@'.$config['hostname'].'/';
            $stream_options = array('ftp' => array('overwrite' => true));
            $stream_context = stream_context_create($stream_options);
            //print_r($remote_file.$chdir);
            if ($fh = fopen($remote_file.$chdir, 'w', 0, $stream_context))
            {
                //print_r($remote_file.$chdir);exit;
                // Writes contents to the file

                fputs($fh, file_get_contents('./output/'.$file));
                
                // Closes the file handle
                fclose($fh);
            }
            //print_r($chdir);exit;
            //goto root
            
        }
    },
    
    "JSencrypt"=>function($content,$slug='home',$type='js')
    {
        $config=$this->app->module('builds')->getConfig();

        if($type=='html')$content=\Minify\lib\Minify\HTML::minify($content);
        else if($type=='js')$content=$this->app->module('builds')->JSminify($content);

        $content=str_replace("\r\n", '', $content);
        $content=str_replace("\n", '', $content);
        $content=str_replace("\\", '{{s}}', $content);
        if($config['dev']){            
            return $content;
        }

        //if($slug=='') print_r($content);
        $oldcontent=$content;
        $content=\lzw::compress($slug.$content);
        //print_r(substr($content,strlen($slug)-1));exit;
        if($slug==''){
            //print_r($content."\r\n");
            $content=substr($oldcontent, 0,1).$content;
            //print_r($content."\r\n");
        }
        else{
            $slugcompress=\lzw::compress($slug);
            $content=substr($content, strlen($slugcompress));
        }
        //fix lost first char when compress js:
        //print_r($content."\r\n");
        
        //$scriptadd=\lzw::decompress($scriptadd);
        //print_r($jscontent);exit;
        return $content;
    },
    "CSSminify"=>function($content)
    {
       //https://github.com/promatik/PHP-JS-CSS-Minifier
        // $url = 'http://cssminifier.com/raw';                
        // $postdata = array('http' => array(
        //     'method'  => 'POST',
        //     'header'  => 'Content-type: application/x-www-form-urlencoded',
        //     'content' => http_build_query( array('input' => $content) ) ) );
        // $content= file_get_contents($url, false, stream_context_create($postdata)) ;


        // Remove comments
        $content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content);
        // Remove space after colons
        $content = str_replace(': ', ':', $content);
        // Remove whitespace
        $content = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $content);
        return $content;
    },
    "JSminify"=>function($jscontent)
    {
       $config=$this->app->module("builds")->getConfig();
       // if($config['dev']){
       //      //print_r($config);exit;
        
       //      $jscontent=preg_replace('#\s*//.*#', "", $jscontent); 
       //      $jscontent=preg_replace('#\s*/\*.*?\*/\s*#i', "", $jscontent);  
       //      $jscontent=str_replace("\r\n", '', $jscontent);
       //      $jscontent=str_replace("\n", '', $jscontent);
       //      return $jscontent;
       // }
        $url = 'http://localhost:81/api/Minify/Js';                
        $postdata = array('http' => array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => http_build_query( array('data' => $jscontent) ) ) );
        
        $jscontent= file_get_contents($url, false, stream_context_create($postdata)) ;   
//print_r('--2--<br/>');exit;

        return $jscontent;

        // $url = 'https://javascript-minifier.com/raw';                
        // $postdata = array('http' => array(
        //     'method'  => 'POST',
        //     'header'  => 'Content-type: application/x-www-form-urlencoded',
        //     'content' => http_build_query( array('input' => $jscontent) ) ) );
        // $jscontent= file_get_contents($url, false, stream_context_create($postdata)) ;        
        // return $jscontent;

        

        
        


        // $jz = new \JSqueeze();       
        // $jscontent = $jz->squeeze(
        //     $jscontent,
        //     true,   // $singleLine
        //     false,   // $keepImportantComments
        //     false   // $specialVarRx
        // );
        // return $jscontent;
    },
    "JPGoptimiser"=>function($imgstr,$filename='default.jpg', &$error = '')
    {   

        
        $eol = "\r\n"; //default line-break for mime type
        $BOUNDARY = md5(time()); //random boundaryid, is a separator for each param on my post curl function
        $BODY=""; //init my curl body
        // $BODY.= '--'.$BOUNDARY. $eol; //start param header
        // $BODY .= 'Content-Disposition: form-data; name="sometext"' . $eol . $eol; // last Content with 2 $eol, in this case is only 1 content.
        // $BODY .= "Some Data" . $eol;//param data in this case is a simple post data and 1 $eol for the end of the data
        $BODY.= '--'.$BOUNDARY. $eol; // start 2nd param,
        $BODY .= 'Content-Disposition: form-data; name="input";filename="'.$filename.'"' . $eol;
        $BODY.= 'Content-Type: image/jpeg' . $eol. $eol; //Same before row
        $BODY .= $imgstr . $eol;//param data in this case is a simple post data and 1 $eol for the end of the data
        $BODY.= '--'.$BOUNDARY .'--' . $eol. $eol; // we close the param and the post width "--" and 2 $eol at the end of our boundary header.

        $headers = array();
        $headers['X-Requested-With'] = 'XMLHttpRequest';
        $headers['Connection'] = 'keep-alive';
        $headers['Referer'] = 'http://jpgoptimiser.com/optimise';
        $headers['User-Agent'] = 'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:28.0) Gecko/20100101 Firefox/28.0';
        //$headers['Content-Type'] = 'application/x-www-form-urlencoded';
        $headers['Accept'] = 'application/json, text/javascript, */*; q=0.01';
        //$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        $headers['Origin'] = 'http://jpgoptimiser.com/optimise';
        $headers['Accept-Encoding'] = 'gzip, deflate';
        $headers['Accept-Language'] = 'en-US,en;q=0.5';
        $headers['Content-Type'] = 'multipart/form-data; boundary='.$BOUNDARY;
       foreach($headers as $k=>$v){
          $headerdata[]=$k.': '.$v;
        }


        $ch = curl_init('http://jpgoptimiser.com/optimise');
       //curl_setopt($ch, CURLOPT_PROXY, '127.0.0.1:8888');
       curl_setopt($ch, CURLOPT_TIMEOUT, 10);
       curl_setopt($ch, CURLOPT_FAILONERROR, 1);
       curl_setopt($ch, CURLOPT_POST, 1);
       curl_setopt($ch, CURLOPT_POSTFIELDS, $BODY);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt ( $ch , CURLOPT_HEADER , 1 );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerdata);
       $response = curl_exec($ch);
       $header_size = curl_getinfo($ch,CURLINFO_HEADER_SIZE);
        $result['header'] = substr($response, 0, $header_size);
        $result['body'] = substr( $response, $header_size );
       
       $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
       if ($status !== 200) {
          $error = 'jpgoptimiser.com request failed: HTTP code ' . $status;
          
       }
       $curl_error = curl_error($ch);
       if (!empty($curl_error)) {
          $error = 'jpgoptimiser.com request failed: CURL error ' . $curl_error;          
       }
       if($result['body']){
            $result['body']=json_decode($result['body'],true);
            if(isset($result['body']['ok']) && $result['body']['ok']){
                $newimgurl='http://jpgoptimiser.com'.$result['body']['output'];
                $imgstr=$this->app->module("builds")->getImageStr($newimgurl); 
            }
       }
       return $imgstr;
    },
    "PNGOpti"=>function($path_to_png_file,$color=256)
    {
       
        if (!file_exists($path_to_png_file)) {
            throw new Exception("File does not exist: $path_to_png_file");
        }


        

        // '-' makes it use stdout, required to save to $compressed_png_content variable
        // '<' makes it read from the given file path
        // escapeshellarg() makes this safe to use with any path
        //print_r("C:\truepng.exe --quality=$min_quality-$max_quality --output $path_to_png_file - < ".escapeshellarg(    $path_tmp));exit;
       //shell_exec("C:\\truepng.exe ".escapeshellarg($path_to_png_file)." /cq c=$color /o max");

       //create tmp image
        // $path_tmp=$path_to_png_file.'tmp';
        // @copy($path_to_png_file,$path_tmp);        
        // @unlink($path_to_png_file);
        

        
        //compress quality 256 color
        shell_exec("C:\\pngquant\pngquant.exe --force --verbose $color ".escapeshellarg($path_to_png_file));
        $arrfilename=explode('.',substr($path_to_png_file,strrpos($path_to_png_file, '/')+1));        
        $filename=$arrfilename[count($arrfilename)-2];              
        $type=strtolower('.'.$arrfilename[count($arrfilename)-1]);
        //lossless compress
        // guarantee that quality won't be worse than that.
        $max_quality = 60;
        $min_quality = 60;
        $path_tmp=str_replace($filename.$type, $filename.'-fs8'.$type, $path_to_png_file);
        @unlink($path_to_png_file);
        //print_r("C:\\pngquant\pngquant.exe --quality=$min_quality-$max_quality --output $path_to_png_file - < ".escapeshellarg($path_tmp));exit;
        shell_exec("C:\\pngquant\pngquant.exe --quality=$min_quality-$max_quality --output $path_to_png_file - < ".escapeshellarg($path_tmp));
        @unlink($path_tmp);
        // print_r($path_tmp);exit;
       
    },
    "PNGcrush"=>function($imgstr,$filename='default.png', &$error = '')
    {
       
       $eol = "\r\n"; //default line-break for mime type
        $BOUNDARY = md5(time()); //random boundaryid, is a separator for each param on my post curl function
        $BODY=""; //init my curl body
        // $BODY.= '--'.$BOUNDARY. $eol; //start param header
        // $BODY .= 'Content-Disposition: form-data; name="sometext"' . $eol . $eol; // last Content with 2 $eol, in this case is only 1 content.
        // $BODY .= "Some Data" . $eol;//param data in this case is a simple post data and 1 $eol for the end of the data
        $BODY.= '--'.$BOUNDARY. $eol; // start 2nd param,
        $BODY .= 'Content-Disposition: form-data; name="input";filename="'.$filename.'"' . $eol;
        $BODY.= 'Content-Type: image/png' . $eol. $eol; //Same before row
        $BODY .= $imgstr . $eol;//param data in this case is a simple post data and 1 $eol for the end of the data
        $BODY.= '--'.$BOUNDARY .'--' . $eol. $eol; // we close the param and the post width "--" and 2 $eol at the end of our boundary header.

        $headers = array();
        $headers['X-Requested-With'] = 'XMLHttpRequest';
        $headers['Connection'] = 'keep-alive';
        $headers['Referer'] = 'http://pngcrush.com/crush';
        $headers['User-Agent'] = 'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:28.0) Gecko/20100101 Firefox/28.0';
        //$headers['Content-Type'] = 'application/x-www-form-urlencoded';
        $headers['Accept'] = 'application/json, text/javascript, */*; q=0.01';
        //$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        $headers['Origin'] = 'http://pngcrush.com/crush';
        $headers['Accept-Encoding'] = 'gzip, deflate';
        $headers['Accept-Language'] = 'en-US,en;q=0.5';
        $headers['Content-Type'] = 'multipart/form-data; boundary='.$BOUNDARY;
       foreach($headers as $k=>$v){
          $headerdata[]=$k.': '.$v;
        }


        $ch = curl_init('http://pngcrush.com/crush');
       
       curl_setopt($ch, CURLOPT_TIMEOUT, 10);
       curl_setopt($ch, CURLOPT_FAILONERROR, 1);
       curl_setopt($ch, CURLOPT_POST, 1);
       curl_setopt($ch, CURLOPT_POSTFIELDS, $BODY);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt ( $ch , CURLOPT_HEADER , 1 );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerdata);
       $response = curl_exec($ch);
       $header_size = curl_getinfo($ch,CURLINFO_HEADER_SIZE);
        $result['header'] = substr($response, 0, $header_size);
        $result['body'] = substr( $response, $header_size );

       
       $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
       if ($status !== 200) {
          $error = 'request failed: HTTP code ' . $status;
          
       }
       $curl_error = curl_error($ch);
       if (!empty($curl_error)) {
          $error = 'request failed: CURL error ' . $curl_error;          
       }
       if($result['body']){
            $result['body']=json_decode($result['body'],true);
            if(isset($result['body']['ok']) && $result['body']['ok']){
                $newimgurl='http://pngcrush.com'.$result['body']['output'];
                $imgstr=$this->app->module("builds")->getImageStr($newimgurl); 
            }
       }
       return $imgstr;
    },
    "getImageStr"=>function($img)
    {
       //get image
        $curl = curl_init($img);
        //curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ( $curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 (.NET CLR 3.5.30729)' );
        curl_setopt ( $curl, CURLOPT_CONNECTTIMEOUT, 5 );
        curl_setopt ( $curl, CURLOPT_TIMEOUT, 15 );
        $imgstr=curl_exec($curl);
        curl_close($curl); 
        return $imgstr;
    },
    "getImageResize"=>function($imgstr, $width,$filename='default',$filetype='jpg')
    {
       
       $eol = "\r\n"; //default line-break for mime type
        $BOUNDARY = md5(time()); //random boundaryid, is a separator for each param on my post curl function
        $BODY=""; //init my curl body
        $BODY.= '--'.$BOUNDARY. $eol; //start param header
        $BODY .= 'Content-Disposition: form-data; name="op"' . $eol . $eol; // last Content with 2 $eol, in this case is only 1 content.
        $BODY .= 'fixedWidth'. $eol;//param data in this case is a simple post data and 1 $eol for the end of the data
        $BODY.= '--'.$BOUNDARY. $eol; // start 2nd param,
        $BODY .= 'Content-Disposition: form-data; name="width"' . $eol . $eol; // last Content with 2 $eol, in this case is only 1 content.
        $BODY .= $width. $eol;//param data in this case is a simple post data and 1 $eol for the end of the data
        $BODY.= '--'.$BOUNDARY. $eol; 
        $BODY .= 'Content-Disposition: form-data; name="input";filename="'.$filename.'.'.$filetype.'"' . $eol;
        $BODY.= 'Content-Type: image/'.(($filetype=='jpg')?'jpeg':$filetype) . $eol. $eol; //Same before row
        $BODY .= $imgstr . $eol;//param data in this case is a simple post data and 1 $eol for the end of the data
        $BODY.= '--'.$BOUNDARY .'--' . $eol. $eol; // we close the param and the post width "--" and 2 $eol at the end of our boundary header.

        $headers = array();
        $headers['X-Requested-With'] = 'XMLHttpRequest';
        $headers['Connection'] = 'keep-alive';
        $headers['Referer'] = 'http://pngcrush.com/crush';
        $headers['User-Agent'] = 'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:28.0) Gecko/20100101 Firefox/28.0';
        //$headers['Content-Type'] = 'application/x-www-form-urlencoded';
        $headers['Accept'] = 'application/json, text/javascript, */*; q=0.01';
        //$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        $headers['Origin'] = 'http://pngcrush.com/crush';
        $headers['Accept-Encoding'] = 'gzip, deflate';
        $headers['Accept-Language'] = 'en-US,en;q=0.5';
        $headers['Content-Type'] = 'multipart/form-data; boundary='.$BOUNDARY;
       foreach($headers as $k=>$v){
          $headerdata[]=$k.': '.$v;
        }


        $ch = curl_init('http://img-resize.com/resize');
       //curl_setopt($ch, CURLOPT_PROXY, '127.0.0.1:8888');
       curl_setopt($ch, CURLOPT_TIMEOUT, 10);
       curl_setopt($ch, CURLOPT_FAILONERROR, 1);
       curl_setopt($ch, CURLOPT_POST, 1);
       curl_setopt($ch, CURLOPT_POSTFIELDS, $BODY);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt ( $ch , CURLOPT_HEADER , 1 );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerdata);
       $response = curl_exec($ch);
       $header_size = curl_getinfo($ch,CURLINFO_HEADER_SIZE);
        $result['header'] = substr($response, 0, $header_size);
        $result['body'] = substr( $response, $header_size );

       
       $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
       if ($status !== 200) {
          $error = 'request failed: HTTP code ' . $status;
          
       }
       $curl_error = curl_error($ch);
       if (!empty($curl_error)) {
          $error = 'request failed: CURL error ' . $curl_error;          
       }
       if($result['body']){
            $result['body']=json_decode($result['body'],true);
            if(isset($result['body']['ok']) && $result['body']['ok']){
                $newimgurl='http://img-resize.com'.$result['body']['download'];
                $imgstr=$this->app->module("builds")->getImageStr($newimgurl); 
            }
       }
       return $imgstr;
    },
    "getImageResize3"=>function($fullfilename,$filename,$arrSize,$destination)
    {
       
        $url = 'http://localhost:81/api/ImageProcess/resize2';                
        $postdata = array('http' => array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => http_build_query( [
                'fullfilename' => $fullfilename,
                'filename' => $filename,
                'arrSize' => implode(",", array_unique($arrSize)),
                'destination' =>$destination,
                ] ) ) );
        $jscontent= file_get_contents($url, false, stream_context_create($postdata)) ;   
        

        return $jscontent;

        // $url = 'https://javascript-minifier.com/raw';                
        // $postdata = array('http' => array(
        //     'method'  => 'POST',
        //     'header'  => 'Content-type: application/x-www-form-urlencoded',
        //     'content' => http_build_query( array('input' => $jscontent) ) ) );
        // $jscontent= file_get_contents($url, false, stream_context_create($postdata)) ;        
        // return $jscontent;

        

        
        


        // $jz = new \JSqueeze();       
        // $jscontent = $jz->squeeze(
        //     $jscontent,
        //     true,   // $singleLine
        //     false,   // $keepImportantComments
        //     false   // $specialVarRx
        // );
        // return $jscontent;
    },
    "getImageResize2"=>function($imgstr, $width,$filename='default',$filetype='jpg')
    {
       
       $eol = "\r\n"; //default line-break for mime type
        $BOUNDARY = md5(time()); //random boundaryid, is a separator for each param on my post curl function
        $BODY=""; //init my curl body
        $BODY.= '--'.$BOUNDARY. $eol; //start param header
        // $BODY .= 'Content-Disposition: form-data; name="op"' . $eol . $eol; // last Content with 2 $eol, in this case is only 1 content.
        // $BODY .= 'fixedWidth'. $eol;//param data in this case is a simple post data and 1 $eol for the end of the data
        // $BODY.= '--'.$BOUNDARY. $eol; // start 2nd param,
        // $BODY .= 'Content-Disposition: form-data; name="width"' . $eol . $eol; // last Content with 2 $eol, in this case is only 1 content.
        // $BODY .= $width. $eol;//param data in this case is a simple post data and 1 $eol for the end of the data
        // $BODY.= '--'.$BOUNDARY. $eol; 
        $BODY .= 'Content-Disposition: form-data; name="input";filename="'.$filename.'.'.$filetype.'"' . $eol;
        $BODY.= 'Content-Type: image/'.(($filetype=='jpg')?'jpeg':$filetype) . $eol. $eol; //Same before row
        $BODY .= $imgstr . $eol;//param data in this case is a simple post data and 1 $eol for the end of the data
        $BODY.= '--'.$BOUNDARY .'--' . $eol. $eol; // we close the param and the post width "--" and 2 $eol at the end of our boundary header.

        $headers = array();
        $headers['X-Requested-With'] = 'XMLHttpRequest';
        $headers['Connection'] = 'keep-alive';
        $headers['Referer'] = 'http://localhost:81';
        $headers['User-Agent'] = 'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:28.0) Gecko/20100101 Firefox/28.0';
        //$headers['Content-Type'] = 'application/x-www-form-urlencoded';
        $headers['Accept'] = 'application/json, text/javascript, */*; q=0.01';
        //$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        $headers['Origin'] = 'http://localhost:81';
        $headers['Accept-Encoding'] = 'gzip, deflate';
        $headers['Accept-Language'] = 'en-US,en;q=0.5';
        $headers['Content-Type'] = 'multipart/form-data; boundary='.$BOUNDARY;
       foreach($headers as $k=>$v){
          $headerdata[]=$k.': '.$v;
        }


        $ch = curl_init('http://localhost:81/api/ImageProcess/resize');
       //curl_setopt($ch, CURLOPT_PROXY, '127.0.0.1:8888');
       curl_setopt($ch, CURLOPT_TIMEOUT, 10);
       curl_setopt($ch, CURLOPT_FAILONERROR, 1);
       curl_setopt($ch, CURLOPT_POST, 1);
       curl_setopt($ch, CURLOPT_POSTFIELDS, $BODY);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt ( $ch , CURLOPT_HEADER , 1 );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerdata);
       $response = curl_exec($ch);
       $header_size = curl_getinfo($ch,CURLINFO_HEADER_SIZE);
        $result['header'] = substr($response, 0, $header_size);
        $result['body'] = substr( $response, $header_size );
        //print_r($result['body']);exit;
       
       $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
       if ($status !== 200) {
          $error = 'request failed: HTTP code ' . $status;
          
       }
       $curl_error = curl_error($ch);
       if (!empty($curl_error)) {
          $error = 'request failed: CURL error ' . $curl_error;          
       }
       if($result['body']){

            $imgstr=$result['body'];
       }
       return $imgstr;
    },
    "getCacheImage"=>function($img,$slug,$arrSize=null,$index=0,$overwrite=0){
        //remove -300x200 size
        
        
        $fullfilename=$img;
        //$fullfilename=preg_replace('/-(\d+)x(\d+)\.(\w+)$/i','.${3}',$fullfilename);
        $arrfilename=explode('.',substr($fullfilename,strrpos($fullfilename, '/')+1));
        $arrFiles=[];
        $filename=$slug.($index>0?$index:'');
        $type=strtolower('.'.$arrfilename[count($arrfilename)-1]);
        $typewodot= strtolower($arrfilename[count($arrfilename)-1]);
        //print_r($arrSize);exit;
        $firstchar=substr($slug, 0,1);    
        if(!empty($arrSize) && count($arrSize)>0){
            //check folder and image
            $cachePath='./output/'.$firstchar.'/'.$slug.'/';
            //check folder exist:
            if (!file_exists($cachePath)) {
                 if (!file_exists('./output/'.$firstchar)) 
                    @mkdir("./output/" .$firstchar);
                @mkdir("./output/" .$firstchar.'/'. $slug);
            }
            //check file exit
            $newfilename=$filename.$arrSize[0].$type;            
            foreach ($arrSize as $key => $cachesize) {
                $newfilename=$filename.$cachesize.$type;
                if(file_exists($cachePath.$newfilename) && !$overwrite)continue;
                
                //add file to push
                $arrFiles[]='./'.$firstchar.'/'.$slug.'/'.$newfilename;

            }  
            //print_r($arrFiles);exit;
            //request resize server
            //print_r($fullfilename);exit;
            if(count($arrFiles)>0){
                $resizeimgstr = $this->app->module("builds")->getImageResize3($fullfilename,$filename,$arrSize,COCKPIT_DIR.'/output/'.$firstchar.'/'.$slug.'/');
            }
                
        }
        return ['filename'=>$filename,'type'=>$type,'arrFiles'=>$arrFiles];
    },
    "replaceSpecialChar"=>function($p,$convert=0){

        $replaceSpecialChars=[
            "[[s]]"=> " ",
            "[[and]]"=> "&&",
            "[[or]]"=> "||",
            "[[gte]]"=> ">=",
            "{{lte}}"=> "[lte]",
            "[[gt]]"=> ">",
            "{{lt}}"=> "[lt]",
            "[[e]]"=> "==",
            "[[ne]]"=> "!=",
            "[[x]]"=> "*",
            "[[div]]"=> "/",
            "[[p]]"=> "+",
            "[[m]]"=> "-"
        ];
        foreach($replaceSpecialChars as $k=>$v){
            if($convert){
                $p=str_replace($k, $v, $p);
            }
            else{
                $p=str_replace($v, $k, $p);
            }
        }
        return $p;
    },
    "parseJsOperator"=>function($op,$vars,$loop=0){
        //parse true false
        // if($loop>=111)print_r($op."\r\n");
        // if(strpos($op, 'siteurl+data.slug+\'<<m>>\'+data.nextpage+\'<<div>>\'')!==false){
        //     $loop=111;
        //     print_r($op."\r\n");
        //     //exit;
        // }

        //parse function:
        preg_match_all('/(\w+)\((.*?)\)/i', $op, $matches);
        if(count($matches[0])>0){
            //print_r($matches);
            foreach ($matches[0] as $k => $v) {                
                $rt=$this->app->module('builds')->parseJsOperator($matches[2][$k],$vars,$loop+1);                
                $rt=$this->app->module("builds")->{$matches[1][$k]}($rt);// {$matches[1]}($rt);
                $op=str_replace($matches[0][$k], $rt, $op);
            }
            //print_r($op);
            
            
        }


        $index = strpos($op,"&&");
        if ($index >= 1)
        {
            $left = substr($op, 0, $index);
            $right = substr($op,$index + 2);
            return ($this->app->module('builds')->parseJsOperator($left, $vars,$loop+1)) && ($this->app->module('builds')->parseJsOperator($right, $vars,$loop+1));
        }

        $index = strpos($op,"||");
        if ($index >= 1)
        {
            $left = substr($op, 0, $index);
            $right = substr($op,$index + 2);
            return ($this->app->module('builds')->parseJsOperator($left, $vars,$loop+1)) || ($this->app->module('builds')->parseJsOperator($right, $vars,$loop+1));
        }

        $index = strpos($op,">");
        if ($index >= 1)
        {
            $left = substr($op, 0, $index);
            $right = substr($op,$index + 1);
            return ($this->app->module('builds')->parseJsOperator($left, $vars,$loop+1)) > ($this->app->module('builds')->parseJsOperator($right, $vars,$loop+1));
        }

        $index = strpos($op,"[lt]");
        if ($index >= 1)
        {
            $left = substr($op, 0, $index);
            $right = substr($op,$index + 4);
            return ($this->app->module('builds')->parseJsOperator($left, $vars,$loop+1)) < ($this->app->module('builds')->parseJsOperator($right, $vars,$loop+1));
        }

        $index = strpos($op,">=");
        if ($index >= 1)
        {
            $left = substr($op, 0, $index);
            $right = substr($op,$index + 2);

            return ($this->app->module('builds')->parseJsOperator($left, $vars,$loop+1)) >= ($this->app->module('builds')->parseJsOperator($right, $vars,$loop+1));
        }

        $index = strpos($op,"[lte]");
        if ($index >= 1)
        {
            $left = substr($op, 0, $index);
            $right = substr($op,$index + 5);
            return ($this->app->module('builds')->parseJsOperator($left, $vars,$loop+1)) <= ($this->app->module('builds')->parseJsOperator($right, $vars,$loop+1));
        }

        $index = strpos($op,"==");
        if ($index >= 1)
        {
            $left = substr($op, 0, $index);
            $right = substr($op,$index + 2);
            //if($loop>=11){print_r($this->app->module('builds')->parseJsOperator($right, $vars,$loop+1));exit;}
            return ($this->app->module('builds')->parseJsOperator($left, $vars,$loop+1)) == ($this->app->module('builds')->parseJsOperator($right, $vars,$loop+1));
        }

        $index = strpos($op,"!=");
        if ($index >= 1)
        {
            $left = substr($op, 0, $index);
            $right = substr($op,$index + 2);
            return ($this->app->module('builds')->parseJsOperator($left, $vars,$loop+1)) != ($this->app->module('builds')->parseJsOperator($right, $vars,$loop+1));
        }

        $index = strpos($op,"+");
        if ($index >= 1)
        {
            $left = substr($op, 0, $index);
            $right = substr($op,$index + 1);  
            $rtleft=$this->app->module('builds')->parseJsOperator($left, $vars,$loop+1);
            $rtright=  $this->app->module('builds')->parseJsOperator($right, $vars,$loop+1);        
            if(is_numeric($rtleft)&&is_numeric($rtright))return $rtleft + $rtright;
            return $rtleft . $rtright;
        }

        $index = strpos($op,"-");
        if ($index >= 1)
        {
            $left = substr($op, 0, $index);
            $right = substr($op,$index + 2);
            return ($this->app->module('builds')->parseJsOperator($left, $vars,$loop+1)) - ($this->app->module('builds')->parseJsOperator($right, $vars,$loop+1));
        }

        $index = strpos($op,"*");
        if ($index >= 1)
        {
            $left = substr($op, 0, $index);
            $right = substr($op,$index + 1);
            // print_r($op);
            // print_r("\r\n");
            // print_r('left:'.$left);
            // print_r("\r\n");
            // print_r('right:'.$right);
            // print_r("\r\n");
            return ($this->app->module('builds')->parseJsOperator($left, $vars,$loop+1)) * ($this->app->module('builds')->parseJsOperator($right, $vars,$loop+1));
        }

        $index = strpos($op,"/");
        if ($index >= 1)
        {
            $left = substr($op, 0, $index);
            $right = substr($op,$index + 1);
            return ($this->app->module('builds')->parseJsOperator($left, $vars,$loop+1)) / ($this->app->module('builds')->parseJsOperator($right, $vars,$loop+1));
        }
        
        return $this->app->module('builds')->parseData($op, $vars);
    },

    "parseJsSyntax"=>function($str,$vars,$loop=0){
        //$str='siteurl + post.slug + "/test{}" + "asdf"';
        //find string and replace special char:
        $str=trim($str,'{{');
        $str=trim($str,'}}');



        //get operator
        preg_match_all('/[\'"](.*?)[\'"]/i', $str, $matches);
        if(count($matches[0])>0){
            foreach ($matches[0] as $key => $value) {
                $str=str_replace($value, $this->app->module('builds')->replaceSpecialChar($value), $str);
            }
            
        }
        
        //parse js operation
        $str=str_replace(' ', '', $str);
        // print_r($str);
        // print_r("\r\n");
        $str=$this->app->module('builds')->parseJsOperator($str,$vars,$loop);
        
        $str=$this->app->module('builds')->replaceSpecialChar($str,1);

        return $str;
    },
    "parseData"=>function($p,$vars){
        $rt='';
        if(is_numeric($p))return $p;
        if (strpos($p,'"')!==false || strpos($p,"'")!==false)
        {
            
            return str_replace('"', '', str_replace("'", "", $p));
        }
        //check length
        // print_r($p);
        // print_r("\r\n");
        if(substr($p, strlen($p)-7)=='.length'){
            $p=str_replace('.length','',$p);
            return count(isset($vars[$p])?$vars[$p]:$p);
        }
        // print_r($rt);
        // print_r("\r\n");
        $rt= isset($vars[$p])?$vars[$p]:$p;
        
        //$rt=str_replace('<', '&#60;', $rt);
        //$rt=str_replace('>', '&#62;', $rt);
        // print_r($rt);
        // print_r("\r\n");
        return $rt;
    },
    "getTemplate"=>function($lang){
        $config=$this->app->module('builds')->getConfig();
        $siteurl=$config['live']?$config['siteurl']:$config['localurl'];
        $langFolder='./input/Layouts/'.$config['theme'].'/'.$lang;        
        if(!file_exists($langFolder))$langFolder='./input/Layouts/'.$config['theme'].'/default';
        $files = scandir($langFolder);
        foreach ($files as $key => $value) {
            if(is_dir($langFolder.'/'.$value) || (substr($value, -5)!=".html") || $value=="index.html")continue;
            $viewname=str_replace('.html', '', $value);
            $html=$this->app->module('builds')->getTemplateFile($lang,'/'.$value);
            $html=str_replace('{{siteurl}}', $siteurl, $html);            
            $tmpl[$viewname]=\Minify\lib\Minify\HTML::minify($html,['jsMinifier'=>['\Minify\lib\JSMin', 'minify']]);          
        }
        return $tmpl;
    },
    "getGATemplate"=>function(){
        // $gacodes = $this->app->db->find("ads/gacodes");
        // //print_r($gacodes->toArray());
        // foreach($gacodes as $k=>$v){
        //     if($v['publish'])
        //         $tmpl[$v['name']]=\Minify\lib\Minify\HTML::minify($v['code'],['jsMinifier'=>['\Minify\lib\JSMin', 'minify']]);
        //     else
        //         $tmpl[$v['name']]='';
        // }
        // //print_r($html);exit;
        // return $tmpl;
        return '';
    },
    "buildscript"=>function(){
        $config=$this->app->module('builds')->getConfig();
        $siteurl=$config['live']?$config['siteurl']:$config['localurl'];
        $tmpl=[];
        $uploadFiles=[];
        $indexHtml='';

        $langs=[$config['defaultlang']];
        if($config['showlang'])
            if(count($config['langs'])>0)
                $langs=$config['langs'];

        
        foreach ($langs as $key => $lang) {            
            $prefix=$lang;            
            $indexHtml=$this->app->module('builds')->getTemplateFile($lang,'/index.html');           
            //get langslink
            // $langslink=[];
            // foreach ($config['langs'] as $l) {
            //     if($l==$lang)continue;                
            //     $langslink[$l]=$siteurl.$l.'/';
            //     if($config['defaultlang']==$l)
            //         $langslink[$l]=$siteurl;
            // }
            // $indexHtml=$this->app->module('builds')->addLangs($indexHtml,$lang,$langslink);

            //get css file link and minify:  
            $files=$this->app->module('builds')->getStyles($indexHtml);
            $cssFiles=$files['cssFiles'];
            //print_r($cssFiles);exit;
            $csscontent='';        
            if($cssFiles){
                foreach ($cssFiles as $key => $src) {
                    if(strpos($src, 'logo.ico')>0)continue;
                    if(substr($src,0, 4)=="http")$csscontent.=file_get_contents($src);
                    else
                        $csscontent.=$this->app->module('builds')->getTemplateFile($lang,$src);                         
                }
            }

            //build responsive
            //print_r($this->app->module('builds')->getTemplateFolder($lang));exit;
            // $langFolder=$this->app->module('builds')->getTemplateFolder($lang).'/css/responsive/';
            // $files = scandir($langFolder);
            // foreach ($files as $key => $value) {
            //     $path=$langFolder.$value;
            //     $tmp=explode('.', $value);                
            //     if(is_dir($path) || count($tmp)<2 || $tmp[1]!="css")continue;
            //     $csscontent.=file_get_contents($path);
            //     //print_r($csscontent);exit;

            //     if(!$config['dev'])$csscontent=$this->app->module('builds')->CSSminify($csscontent); //\Minify\lib\Minify\CSS::minify($csscontent);            
            //     $csscontent = str_replace("\r\n", "", $csscontent);
            //     $csscontent = str_replace("\n", "", $csscontent);
            //     $csscontent = str_replace("{{siteurl}}", $siteurl, $csscontent);
                
                
            //     //write to output css file                
            //     $slug=$siteurl.'style'.$prefix.$tmp[0].'.js';
            //     $slug=str_replace('/', '', $slug);
            //     $slug=str_replace(".", '', $slug);
            //     $slug=str_replace(":", '', $slug);
            //     $csscontent=$this->app->module('builds')->JSencrypt($csscontent,$slug,'html',$config);
            //     $uploadFiles=$this->app->module('builds')->output($slug,$csscontent,$uploadFiles);
            // }

            if(!$config['dev'])$csscontent=$this->app->module('builds')->CSSminify($csscontent); //\Minify\lib\Minify\CSS::minify($csscontent);            
            $csscontent = str_replace("\r\n", "", $csscontent);
            $csscontent = str_replace("\n", "", $csscontent);
            $csscontent = str_replace("{{siteurl}}", $siteurl, $csscontent);
            
            
            //write to output css file                
            $slug=$siteurl.'style'.$prefix.'.js';
            $slug=str_replace('/', '', $slug);
            $slug=str_replace(".", '', $slug);
            $slug=str_replace(":", '', $slug);
            $csscontent=$this->app->module('builds')->JSencrypt($csscontent,$slug,'html',$config);
            $uploadFiles=$this->app->module('builds')->output($slug,$csscontent,$uploadFiles);
           

            

            

            $files=$this->app->module('builds')->getScripts($indexHtml);             
            $jsFiles=$files['jsFiles'];
            $jscontent='';   
            $jscontent.=$this->app->module('builds')->getTemplateFile($lang,'/js/jquery.mycustom.js');
            $jscontent.=$this->app->module('builds')->getTemplateFile($lang,'/js/plugin.js');     
            if($jsFiles){
                foreach ($jsFiles as $key => $src) {
                    //if dev then remove disable                
                    if(!$config['disable'] && substr($src, -11)=="disabled.js" )continue;
                    $jscontent.=file_get_contents('./input'.$src);   
                    
                }
                $jscontent=str_replace('{{debug}}', $config['debug'], $jscontent);   
                $jscontent=str_replace('{{apiurl}}', $config['apiurl'], $jscontent);                  
                $jscontent=str_replace('{{FBId}}', $config['FBId'], $jscontent);                  
                
                //trick to use utf8
                $data=['baseScript'=>$config['custom_event']];
                $baseScript=json_encode($data);
                $random=$this->app->module('commons')->create_random_string(5);
                $baseScript='eval(JSON.parse(JXG.decompress("'.$random.base64_encode(gzcompress($baseScript,9)).'".replace("'.$random.'",""))).baseScript);';
                $jscontent=str_replace('{{custom_event}}', $baseScript, $jscontent);                  
                //add predata:                      
                        
                $predata=['tmpl'=>$this->app->module('builds')->getTemplate($lang)];
                //get all ga code
                $predata['gatmpl']=$this->app->module('builds')->getGATemplate();
                $predata=str_replace('==','',base64_encode('home')).base64_encode(gzcompress(json_encode($predata),9));
                $scriptadd='var pred="'.$predata.'";';
                $jscontent=$scriptadd.$jscontent;
               
            }        
            //write to output
            
            
            $slug=$siteurl.'script'.$prefix.'.js';
            $slug=str_replace('/', '', $slug);
            $slug=str_replace(".", '', $slug);
            $slug=str_replace(":", '', $slug);

            $jscontent=$this->app->module('builds')->JSencrypt($jscontent,$slug,'js',$config);
           
            $uploadFiles=$this->app->module('builds')->output($slug,$jscontent,$uploadFiles);

            
        }
        
        //for custom jquery.js with other script
        $jscontent=file_get_contents('./input/js/jquery.min.js');


        //$jscontent.=file_get_contents('./input/js/jquery.mycustom.js');        
        $slug=$siteurl.'jquery.js';
        $slug=str_replace('/', '', $slug);
        $slug=str_replace(".", '', $slug);
        $slug=str_replace(":", '', $slug);
        //$jscontent=$this->app->module('builds')->JSencrypt($jscontent,$slug,'js',$config);
        $uploadFiles=$this->app->module('builds')->output($slug,$jscontent,$uploadFiles);
        
        
        
        // print_r(base64_decode("aHR0cDpsb2NhbGhvc3Rua2NtMm91dHB1dHN0eWxlLmpz"));
        // print_r("\r\n");
        // print_r(base64_decode("aHR0cGxvY2FsaG9zdG5rY20yb3V0cHV0c3R5bGVqcw"));exit;
        // $cachename=$siteurl.'style.js';
        
        // print_r($cachename);
        // print_r("\r\n");
        // $cachename=base64_encode($cachename);

       
        // $cachename=substr($cachename, 0,strlen($cachename)-2);            
        // $last=substr($cachename, strlen($cachename)-8);
        //  print_r($last);
        // print_r("\r\n");
        // $tmp=str_split ($cachename);        
        // $cachename='';    
        // for( $i=(count($tmp)-1);$i>=0;$i--)
        //     if($i%3==0)$cachename.=$tmp[$i];  
        // $cachename=$last.$cachename;
        // $cachename=substr($cachename, 0,12);  
        //  print_r($cachename);
        // print_r("\r\n");
        // $cachefolder=substr($cachename, 0,1);
        // $this->app->module('builds')->createdir('output/'.$cachefolder);
        // $cachedatafile=COCKPIT_DIR.'/output/'.$cachefolder.'/'.$cachename;            
        // file_put_contents($cachedatafile, $csscontent);
        // $uploadFiles[]= './'.$cachefolder.'/'.$cachename; 

       
        

        //get js file link and minify:   
        



       

        //antilocal view
        if(!$config['dev']){
            $filename=str_replace(['=','/'], '', base64_encode('antilocal')).'.js';
            $antibaseScript='
            function tb (s) {
                "use strict";                            
                var dict = {};
                var data = s.split("");                
                var currChar = data[0];
                var oldPhrase = currChar;
                var out = [currChar];
                var code = 256;
                var phrase;
                for (var i=1; i<data.length; i++) {
                    var currCode = data[i].charCodeAt(0);
                    if (currCode < 256) {
                        phrase = data[i];
                    }
                    else {
                       phrase = dict[currCode] ? dict[currCode] : (oldPhrase + currChar);
                    }
                    out.push(phrase);
                    currChar = phrase.charAt(0);
                    dict[code] = oldPhrase + currChar;
                    code++;
                    oldPhrase = phrase;
                }
                return out.join("");
            }';
            //$antibaseScript=$this->app->module('builds')->JSminify($antibaseScript);
            $antiScript=file_get_contents('./input/js/antilocal.js');
            //$antiScript='';
            //print_r($antiScript);exit;
            $antiScript=$this->app->module('builds')->JSencrypt($antiScript,'','js',$config);
            //$antiScript=$this->app->module('builds')->strCompress($antiScript);
            //write to file
            $antiScript=$antibaseScript.'var s=\''.$antiScript.'\';window.onload =function(){ eval(tb(s));}';
            //$antiScript=$this->app->module('builds')->JSminify($antiScript);
            //print_r($antiScript);exit;
            $filepath=COCKPIT_DIR.'/output/'.$filename;
            file_put_contents($filepath,$antiScript);
            $uploadFiles[]= './'.$filename;
        }

        //web.data file
        // refresh version string
        $jscontent=$this->app->module("builds")->create_random_string(90);
        $slug=$siteurl.'web.data';
        $uploadFiles=$this->app->module('builds')->output($slug,$jscontent,$uploadFiles);

        $this->app->module("builds")->pushfile($uploadFiles,$config);
        return true;
    },
    "getStyles"=>function($html){
        $cssFiles=[];
        $replaceFiles=[];
        preg_match_all('/<link[^>]*\/>/is',$html, $matches);
        if($matches[0]){
            foreach ($matches[0] as $key => $value) {
                preg_match('/href=[\'"](.*?)[\'"]/is',$value, $matches2);                        
                if($matches2 && substr($matches2[1], -4)==".css"){
                    $cssFiles[]=$matches2[1];
                    $replaceFiles[]=$value;
                }
            }
        }
        return ['cssFiles'=>$cssFiles,'replaceFiles'=>$replaceFiles];
    },
    "getScripts"=>function($html){
        $jsFiles=[];
        preg_match_all('/<script[^>]*><\/script>/is',$html, $matches);
        $replaceFiles=[];
        if($matches[0]){
            foreach ($matches[0] as $key => $value) {                
                preg_match('/src=[\'"](.*?)[\'"]/is',$value, $matches2);                
                if(!$matches2 || substr($matches2[1],0, 4)=="http" )continue;
                $jsFiles[]=$matches2[1];
                $replaceFiles[]=$value;
            }
        }
        return ['jsFiles'=>$jsFiles,'replaceFiles'=>$replaceFiles];
    },
    // "getTemplateFile"=>function($lang,$filePath){
    //     $config=$this->app->module('builds')->getConfig();
    //     $langFolderDefault='./input/Layouts/'.$config['theme'].'/default';
    //     $langFolder='./input/Layouts/'.$config['theme'].'/'.$lang;
    //     if(file_exists($langFolder.$filePath)) return file_get_contents($langFolder.$filePath);
    //     else if(file_exists($langFolderDefault.$filePath)) return file_get_contents($langFolderDefault.$filePath);
    //     else return '';
            
    // },
    "getTemplateFile"=>function($lang,$filePath,$defaultFilePath=''){
        $config=$this->app->module('builds')->getConfig();
        $langFolderDefault='./input/Layouts/'.$config['theme'].'/default';
        $langFolder='./input/Layouts/'.$config['theme'].'/'.$lang;        
        if(file_exists($langFolder.$filePath)) return file_get_contents($langFolder.$filePath);
        else if(!empty($defaultFilePath) && file_exists($langFolder.$defaultFilePath))return file_get_contents($langFolder.$defaultFilePath);
        else if(file_exists($langFolderDefault.$filePath)) return file_get_contents($langFolderDefault.$filePath);
        else if(!empty($defaultFilePath) && file_exists($langFolderDefault.$defaultFilePath)) return file_get_contents($langFolderDefault.$defaultFilePath);
        else return '';
            
    },
    "getTemplateFolder"=>function($lang){
        $config=$this->app->module('builds')->getConfig();
        $langFolderDefault='./input/Layouts/'.$config['theme'].'/default';
        $langFolder='./input/Layouts/'.$config['theme'].'/'.$lang;
        if(file_exists($langFolder)) return $langFolder;
        else if(file_exists($langFolderDefault)) return $langFolderDefault;
        else return '';
            
    },
    "buildHtml"=>function($isamp=0){
        $ampfolder='';
        if($isamp)$ampfolder='/amp';        
        $config=$this->app->module('builds')->getConfig();
        $siteurl=$config['live']?$config['siteurl']:$config['localurl'];
        
        $tmpl=[];
        $uploadFiles=[];
        
        $htmlLangs=[];
        $bodyHtmlLangs=[];

        $langs=[$config['defaultlang']];
        if($config['showlang'])
            if(count($config['langs'])>0)
                $langs=$config['langs'];
        
        $html=[];

        foreach ($langs as $key => $lang) {    
            $html[$lang]=[];
            $indexHtml=$this->app->module('builds')->getTemplateFile($lang,$ampfolder.'/index.html');
            
            //get langslink
            $langslink=[];
            foreach ($config['langs'] as $l) {
                if($l==$lang)continue;
                $langslink[$l]=$siteurl.$l.'/';
                if($config['defaultlang']==$l)
                    $langslink[$l]=$siteurl;
            }
            //$indexHtml=$this->app->module('builds')->addLangs($indexHtml,$lang,$langslink);
            //print_r($indexHtml);exit;
            //get css file link and minify:                
            $files=$this->app->module('builds')->getStyles($indexHtml);  
            $replaceFiles=$files['replaceFiles'];         
           
            //remove script
            
            $indexHtml=str_replace($replaceFiles, '', $indexHtml);


            //get js file link and minify:                
            $files=$this->app->module('builds')->getScripts($indexHtml);  

            $replaceFiles=$files['replaceFiles'];         
            //remove script link        
            $indexHtml=str_replace($replaceFiles, '', $indexHtml);   

            //jquery script
            //$jquerylink='<script type="text/javascript" src="'.$siteurl.'jquery.min.js"></script>';
            //$jquerylink.='<script type="text/javascript" src="'.$siteurl.'jwplayer.js"></script>';
            //$indexHtml=str_replace('<script />', $jquerylink, $indexHtml);           
            
            //extract body content
            preg_match("/<body[^>]*>(.*?)<\/body>/si", $indexHtml,$matches);         

            $bodyHtml=$matches[1];

            //get header & footer
            $HeaderHtml=$this->app->module('builds')->getTemplateFile($lang,$ampfolder.'/Header.html');
            $FooterHtml=$this->app->module('builds')->getTemplateFile($lang,$ampfolder.'/Footer.html');
            if($isamp){
                $HeaderHtml=preg_replace("/{{siteurl}}/si", $siteurl, $HeaderHtml);
                $FooterHtml=preg_replace("/{{siteurl}}/si", $siteurl, $FooterHtml);
            }
            else{                
                $parsedHtml=['html'=>$HeaderHtml];                
                $parsedHtml['var']=['siteurl'=>$siteurl];
                $HeaderHtml=$this->app->module('builds')->parseAngularHtml($parsedHtml); 

                $parsedHtml=['html'=>$FooterHtml];                  
                $parsedHtml['var']=['siteurl'=>$siteurl];
                $FooterHtml=$this->app->module('builds')->parseAngularHtml($parsedHtml);
                
            }

            $bodyHtml=str_replace("<div id='HeaderController'></div>", $HeaderHtml, $bodyHtml);
            $bodyHtml=str_replace("<div id='FooterController'></div>", $FooterHtml, $bodyHtml);
            //print_r($bodyHtml);exit;


            

            
            //remove bodycontent
            $replacement="<body$1></body>";
            if(!$config['disable'])$replacement="<body></body>";
            $indexHtml=preg_replace("/<body([^>]*)>.*<\/body>/si", $replacement, $indexHtml);

            $html[$lang]['html']=$indexHtml;
            $html[$lang]['body']=$bodyHtml;
        }
        return $html;

    },
    "addLangs"=>function($html,$lang,$langslink,$amplink='',$isamp=0){
        
        $config=$this->app->module('builds')->getConfig();
        $langshtml="";
        $siteurl=($config['live']?$config['siteurl']:$config['localurl']);
        if(empty($lang))$lang=$config['defaultlang'];
        //print_r($lang);exit;
        $locale=\MyCommon\Common::listLocale(); 
        $flags=\MyCommon\Common::listCountryFlag();
        $countryName=\MyCommon\Common::listCountry();
        $langshtml.='<meta property="og:locale" content="'.$locale[$lang].'" />';
        $html=str_replace('{{lang}}',$lang,$html);
        $html=str_replace('{{langiso}}',$locale[$lang],$html);
        $html=str_replace('{{siteurl}}',$siteurl,$html);
        if(count($langslink)>0 && $config['showlang']){
            foreach($langslink as $k=>$link){
                $langurl=$link;
                if(strpos($link, 'http://')===false && strpos($link, 'https://')===false )$langurl=$siteurl.$link;                
                $langshtml.='<link rel="alternate" langname="'.$countryName[$k].'" lang-iso="'.$flags[$k].'" hreflang="'.$k.'" href="'.$langurl.'"  />';
            }                
        }

        if($amplink){
            $langshtml.='<link rel="amphtml" href="'.$amplink.'"  />';

        }
        $html=str_replace('<languages />',$langshtml,$html);
        

        if($isamp){
            //process data
            preg_match_all("/<!-- languages-container -->(.*?)<!-- languages-container-end -->/si", $html,$matches);
            
            if(count($matches)>0){
                foreach ($matches[0] as $key => $match) {
                    $amplanghtml='';
                    foreach($langslink as $k=>$v){
                        $amplangcontent='';

                        $amplangcontent=str_replace("{{flagurl}}", $locale[$k], $matches[1][$key]);
                        $amplangcontent=str_replace("{{url}}", $siteurl.$v, $amplangcontent);
                        $amplangcontent=str_replace("{{flag}}", $flags[$k], $amplangcontent);
                        $amplangcontent=str_replace("{{flagalt}}", $countryName[$k], $amplangcontent);
                        $amplangcontent=str_replace("{{urltitle}}", $countryName[$k], $amplangcontent);
                        $amplangcontent=str_replace("{{current}}", $lang==$k?'current':'', $amplangcontent);
                        $amplanghtml.=$amplangcontent; 
                                              
                    }                                                                    
                    $html=str_replace($match,$amplanghtml,$html);
                }           
                           
            }
        }

        
        return $html;
    },
    "addMetadata"=>function($html,$metadata=null){
        $config=$this->app->module('builds')->getConfig();
        $siteurl=($config['live']?$config['siteurl']:$config['localurl']);
        //metadata
        $metadataStr='';        
        if($metadata){
            foreach($metadata as $k=>$meta){
                if($meta['meta']=='og:authorlink')$metadataStr.='<link rel="author" href="'.$meta['content'].'" />';
                else $metadataStr.='<meta property="'.$meta['meta'].'" content="'.$meta['content'].'" />';
                if($meta['meta']=='og:title')$metadataStr.='<title>'.$meta['content'].'</title>';
                if($meta['meta']=='og:url')$metadataStr.='<link rel="canonical" href="'.$meta['content'].'" />';

            }
        }
        $html=str_replace('<meta />',$metadataStr,$html);



        
        return $html;
    },
    "addFirstScript"=>function($html,$title='',$slug='home'){
        
        $html=str_replace('<?xml version="1.0"?>', '', $html);
        

        
        $html=$this->app->module('builds')->JSencrypt($html,$slug,'html',$config);
        

        return $html;
    },
    "getBottomScriptAndMinify"=>function($indexHtml,$slug='',$morescript=''){
        $config=$this->app->module('builds')->getConfig();
        
        if(empty($slug))$slug='home';
        $sitesize='var cursitesize=document.body.clientWidth;';        
        $sitesize.='var sitesizeStr="{";';
        $i=0;
        foreach ( $config['arrSize'] as $k => $type) {
            $sitesize.='sitesizeStr+="\"'.$k.'\":";';
            foreach($type as $key=>$value){
                if($key==0)continue;
                $sitesize.='if(cursitesize<='.$key.'){                  
                    sitesizeStr+="\"'.$value.'\"";
                }else ';
            }
            $sitesize.='sitesizeStr+="\"'. $type[0].'\"";';
            if(++$i<count($config['arrSize']))
                $sitesize.='sitesizeStr+=",";';
        }
        $sitesize.='sitesizeStr+="}";
        var debug='.$config['debug'].';
        var sitesizet=JSON.parse(sitesizeStr);
        var sitesize=sitesizet.home;';
        $siteurl=($config['live']?$config['siteurl']:$config['localurl']);
        $sitesize.='var localurl="'.$config['localurl'].'";
        var siteurl="'.$siteurl.'";
        var apiurl="'.$config['apiurl'].'";
        var siteid="'.$config['siteid'].'";
        var myFBId="'.$config['FBId'].'";    
        var isLoadGacode=0;
        var sitetitle=document.getElementsByTagName("title")[0].innerHTML;';
        //antilocal view
        if(!$config['dev']){
            $sitesize.='
            function tb (s) {
                    "use strict";                            
                    var dict = {};
                    var data = s.split("");                
                    var currChar = data[0];
                    var oldPhrase = currChar;
                    var out = [currChar];
                    var code = 256;
                    var phrase;
                    for (var i=1; i<data.length; i++) {
                        var currCode = data[i].charCodeAt(0);
                        if (currCode < 256) {
                            phrase = data[i];
                        }
                        else {
                           phrase = dict[currCode] ? dict[currCode] : (oldPhrase + currChar);
                        }
                        out.push(phrase);
                        currChar = phrase.charAt(0);
                        dict[code] = oldPhrase + currChar;
                        code++;
                        oldPhrase = phrase;
                    }
                    return out.join("");
                }var s=\'if(window.location.hostname==""){Ćcuęnt.body.ĄnerHTMLĜ";ġģeĥĒeadĬnĮİĲĴĝ};\';window.onload =function(){ eval(tb(s));}

            ';
        }
        $comebineScript='        
        function u(s) {
            "use strict";
            // Build the dictionary.
            '.($config['dev']?'return s;':'').'
            var i,
                dictionary = {},
                c,
                wc,
                w = "",
                result = [],
                dictSize = 256;
            for (i = 0; i < 256; i += 1) {
                dictionary[String.fromCharCode(i)] = i;
            }
     
            for (i = 0; i < s.length; i += 1) {
                c = s.charAt(i);
                wc = w + c;
                //Do not use dictionary[wc] because javascript arrays 
                //will return values for array[\'pop\'], array[\'push\'] etc
               // if (dictionary[wc]) {
                if (dictionary.hasOwnProperty(wc)) {
                    w = wc;
                } else {
                    result.push(String.fromCharCode(dictionary[w]));
                    // Add wc to the dictionary.
                    dictionary[wc] = dictSize++;
                    w = String(c);
                }
            }
     
            // Output the code for w.
            if (w !== "") {
                result.push(String.fromCharCode(dictionary[w]));
            }
            return result.join("");
        }        
        function t (s,d,h) {
                "use strict";
                
                // Build the dictionary.               
                '.($config['dev']?'
                return s.replace(/\{\{s\}\}/g,"\\\\").replace(/\{\{qq\}\}/g,\'"\').replace(/\{\{q\}\}/g,"\'");
                    ':'').'
                var par=window.location.href.split("?");
                if(d==undefined)d=par[0].replace("'.$siteurl.'","");
                
                if(d.length==0)d="home";
                var dor=d;
                d=u(d);
                var dict = {};
                var data = (d+s).split("");  
                              
                var currChar = data[0];
                var oldPhrase = currChar;
                var out = [currChar];
                var code = 256;
                var phrase;
                for (var i=1; i<data.length; i++) {
                    var currCode = data[i].charCodeAt(0);
                    if (currCode < 256) {
                        phrase = data[i];
                    }
                    else {
                       phrase = dict[currCode] ? dict[currCode] : (oldPhrase + currChar);
                    }
                    out.push(phrase);
                    currChar = phrase.charAt(0);
                    dict[code] = oldPhrase + currChar;
                    code++;
                    oldPhrase = phrase;
                }
                var rt= out.join("").substr(dor.length).replace(/\{\{s\}\}/g,"\\\\").replace(/\{\{qq\}\}/g,\'"\').replace(/\{\{q\}\}/g,"\'");
                
                
                return (h==undefined||!h)?rt:decodeURIComponent(escape(rt));
            }
        ;';
        
        $baseScript=file_get_contents('./input/js/basescript.js');

        //custom_async_script
        $baseScript=str_replace('{{custom_async_script}}', $config['custom_async_script'], $baseScript);
        
        $baseScript=str_replace('{{apiurl}}', $config['apiurl'], $baseScript);
        //more script
        //$morescript=str_replace('"', '\"', $morescript);
        //$morescript=str_replace("'", "\'", $morescript);
        //$baseScript.='setTimeout(function(){eval(utf8_decode(b("'.$morescript.'")));},300);';
        $baseScript=str_replace('{{morescript}}',$morescript, $baseScript);
        //$baseScript=str_replace('{{morescript}}',$morescript, $baseScript);
        
        
        $noMinScript="";
        //call ajax:
        
        // $baseScript=$this->app->module('builds')->JSencrypt($sitesize.$baseScript,$slug,'',$config);
        // $baseScript=str_replace('"', '\"', $baseScript);
        // $baseScript=str_replace("'", "\'", $baseScript);
        //$scriptadd='<script type="text/javascript">'.$comebineScript.'eval(t(\''.$baseScript.'\'));</script>';
        
        if(!$config['dev'])$baseScript=$this->app->module('builds')->JSminify($comebineScript.$sitesize.$baseScript);        
        $data=['baseScript'=>$baseScript];
        $baseScript=json_encode($data);
        $random=$this->app->module('commons')->create_random_string(5);
        $baseScript='eval(JSON.parse(JXG.decompress("'.$random.base64_encode(gzcompress($baseScript,9)).'".replace("'.$random.'",""))).baseScript);';

        //$baseScript=str_replace('"', '\"', $baseScript);
        //print_r(\lzw::compress($baseScript));
        $jsx=file_get_contents('./input/js/jsxcompressor.min.js');
        $scriptadd='<script type="text/javascript">'.$jsx.$baseScript.'</script>';
       
        
        $indexHtml=str_replace('</body>', $scriptadd.'</body>', $indexHtml);
        
        
        //minify

        if($config['dev']){
            
            $indexHtml=str_replace('oncontextmenu="return false" onselectstart="return false"', '', $indexHtml);
            
        }
        else{
            //$scriptadd.=$extScript;
            
            
            $indexHtml=\Minify\lib\Minify\HTML::minify($indexHtml);
        }
        return $indexHtml;
    },
    /* 
    "ProccessParsedData"=>function($parsedHtml,$loop=0){
        if(!isset($parsedHtml['html']) || empty($parsedHtml['html']))return '';
        
        $html=$parsedHtml['html'];
        //process repeat content
      
        if(isset($parsedHtml['repeatContents']) && count($parsedHtml['repeatContents'])>0){
            foreach($parsedHtml['repeatContents'] as $repeatContent){
                //print_r($parsedHtml['var'][$repeatContent['varname']]);exit;
                $repeatHtml='';
                //process repeat data
                if(isset($repeatContent['varname']) && isset($parsedHtml['var'][$repeatContent['varname']]) && count($parsedHtml['var'][$repeatContent['varname']])>0){
                    
                    foreach($parsedHtml['var'][$repeatContent['varname']] as $repeatData){
                        $repeatContent['var']=$parsedHtml['var'];

                        //unset repeat data
                        unset($repeatContent['var'][$repeatContent['varname']]);
                        foreach($repeatData as $k=>$v){
                            $repeatContent['var'][$repeatContent['prefix'].'.'.$k]=$v;
                        }
                        
                        $repeatHtml.=$this->app->module("builds")->ProccessParsedData($repeatContent,1);
                        //print_r($html);exit;
                    }
                }

                //$repeatContent['var']=$parsedHtml['var'];
                //$repeatContent['repeatvar']=$parsedHtml['repeatvar'];
                print_r($html);
                print_r($repeatHtml);
                print_r($repeatContent);exit;
                $html=str_replace($repeatContent['name'], $repeatHtml,  $html) ;
                //print_r($html);exit;
            }
        }
        


        
        //process show content
        if(isset($parsedHtml['showContents']) && count($parsedHtml['showContents'])>0){
             //if($loop==11){print_r($parsedHtml);exit;}
            foreach($parsedHtml['showContents'] as $k=>$showContent){

                //check data to show content
                if(!empty($this->app->module("builds")->parseJsSyntax($showContent['varname'],$parsedHtml['var'],$loop))){
                    $showContent['var']=$parsedHtml['var'];  
                    //$showContent['html']=$parsedHtml['html'];
                    //if($loop==11){print_r($k);exit;}
                    $html=str_replace($showContent['name'], $this->app->module("builds")->ProccessParsedData($showContent,$loop+1),  $html) ;    
                    
                }
                else{
                    $html=str_replace($showContent['name'],'',$html);
                }
                
            }
        }
        //process data
        preg_match_all("/{{(.*?)}}/si", $html,$matches);
        
        if(isset($matches[0]) && count($matches[0])>0){
            foreach ($matches[0] as $key => $match) {
                $html=str_replace($match, $this->app->module("builds")->parseJsSyntax($match,$parsedHtml['var']), $html);
            }           

        }
        //print_r($html);exit;
        return $html;
    },  */
    "parseAngularHtml"=>function($parsedHtml,$loop=0){
        $html=isset($parsedHtml['html'])?$parsedHtml['html']:'';
        if(empty($html))return $html;
        //print_r($loop."\r\n");
        $rt=[];
        //replace vars
        $html = preg_replace("/ng-(src|href|title|alt|style|class)=\"(.*?)\"/i", '$1="{{$2}}"', $html);
        $html = preg_replace("/ ng-bind=\"(.*?)\"(.*?)>.*?</i", '$2>{{$1}}<', $html);
        //get repeat content
                     
        
        
        $simplexml = simplexml_load_string(html_entity_decode($html)); 
        $repeatNodes = $simplexml->xpath('//*[@ng-repeat]');

         //convert to the xml format:
        $html=$simplexml->saveXML();
        $html=trim(str_replace('<?xml version="1.0"?>','', $html));
        $repeatHtml=[];
        foreach ($repeatNodes as $key => $repeatNode) {            
            $att=$repeatNode->attributes()['ng-repeat'];
            //remove attr:
            $repeatContentOuterXML=$repeatNode->saveXML();
            $repeatContentOuterXML = simplexml_load_string($repeatContentOuterXML)->saveXML(); 
            $repeatContentOuterXML=trim(str_replace('<?xml version="1.0"?>','', $repeatContentOuterXML));
            $repeatContent=str_replace('ng-repeat="'.$att.'"', '', $repeatContentOuterXML);
            $tmp=explode(' ', $att);
            $arrayName=$tmp[2];
            $itemName=$tmp[0];
            $repeatContent=['html'=>$repeatContent];
            $repeatContent['var']=$parsedHtml['var'];
            unset($repeatContent['var'][$arrayName]);
            $repeat='';
            foreach($parsedHtml['var'][$arrayName] as $repeatData){                
                foreach($repeatData as $k=>$v){
                    $repeatContent['var'][$itemName.'.'.$k]=$v;
                }
                $repeat.=$this->app->module("builds")->parseAngularHtml($repeatContent,$loop+1);
                //print_r($html);exit;
            }
            $repeatHtml[]=$repeat;
            $html=str_replace($repeatContentOuterXML, '[[[repeat'.$key.']]]', $html);   

        }
        //get show content
        
        
        $simplexml = simplexml_load_string(html_entity_decode($html));
        //print_r($html); 
        $showNodes = $simplexml->xpath('//*[@ng-show]');
        
        $i=0;
        foreach ($showNodes as $key => $showNode) {
            $att=$showNode->attributes()['ng-show'];    
            $att=str_replace(' ', '',$att);    
            //remove attr:           
            $showContentOuterXML=simplexml_load_string($showNode->saveXML())->saveXML();
            $showContentOuterXML=trim(str_replace('<?xml version="1.0"?>','', $showContentOuterXML));
            $showContent=str_replace('ng-show="'.htmlentities($att).'"', '', $showContentOuterXML);
            $showHtml='';
            
            
            if(!empty($this->app->module("builds")->parseJsSyntax($att,$parsedHtml['var'],$loop))){
                $showContent=['html'=>$showContent];
                $showContent['var']=$parsedHtml['var'];
                $showHtml=$this->app->module("builds")->parseAngularHtml($showContent,$loop+1);
            }
            // print_r($showContentOuterXML);
            // print_r($showHtml);
            // print_r($html);
            $html=str_replace($showContentOuterXML, $showHtml, $html);
        }
        
        //replace repeat content:
       
        if(count($repeatHtml)>0){
            foreach ($repeatHtml as $key => $repeat) {
                $html=str_replace('[[[repeat'.$key.']]]', $repeat, $html);
            }
        }
      
        
        //process data
        preg_match_all("/{{(.*?)}}/si", $html,$matches);        
        if(isset($matches[0]) && count($matches[0])>0){
            foreach ($matches[0] as $key => $match) {
                $html=str_replace($match, $this->app->module("builds")->parseJsSyntax($match,$parsedHtml['var']), $html);
            }           

        }
        return $html;
    },
    "buildHome"=>function($html){
        //prepare config
        $config=$this->app->module('builds')->getConfig();
        $siteurl=$config['live']?$config['siteurl']:$config['localurl'];     
        $uploadFiles=[];
        $langs=[$config['defaultlang']];

        if($config['showlang'])
            if(count($config['langs'])>0)
                $langs=$config['langs'];
        
        //get langslink
        $langslink=[];
        foreach ($langs as $l) {                    
            $langslink[$l]=$siteurl.$l.'/';
            if($config['defaultlang']==$l)
                $langslink[$l]=$siteurl;
        }
        
        
        foreach ($langs as $key => $lang) {
            //add lang href
            $tmplangslink=$langslink;
            unset($tmplangslink[$lang]);
            $html[$lang]=$this->app->module('builds')->addLangs($html[$lang],$lang,$tmplangslink);

            $mainHtml=$this->app->module('builds')->getTemplateFile($lang,'/Main.html');
            

            //$mainHtml=$this->app->module("builds")->replaceNgHref($mainHtml);

           
            
            //print_r($indexHtml);exit;
            //metadata
            $metadataStr='';
            $metadata=[];            
            $metadata[]=['meta'=>'og:title','content'=>$config['sitetitle']];
            $metadata[]=['meta'=>'og:description','content'=>$config['sitedescription']];
            $metadata[]=['meta'=>'og:url','content'=>$siteurl];
            $metadata[]=['meta'=>'og:image','content'=>$siteurl.'images/logo.jpg'];
            $indexHtml=$this->app->module("builds")->addMetadata($html[$lang]['html'],$metadata,null,$config);
            

            
            //get schema content    
            $microdata=[];
           
            $microdata['title']=$config['sitetitle'];
            $microdata['description']=$config['sitedescription'];           
            $microdata['dateModified']=date("Y-m-d",time());
            $schemaHtml=$this->app->module("builds")->getTemplateFile($lang,'/schema/Main.html');
            $parsedSchemaHtml=['html'=>$schemaHtml];
            
            $parsedSchemaHtml['var']=['siteurl'=>$siteurl];
            foreach($microdata as $k=>$v){
                $parsedSchemaHtml['var']['data.'.$k]=$v;    
            }               
            //print_r($parsedSchemaHtml);exit;
            $schemaHtml=$this->app->module('builds')->parseAngularHtml($parsedSchemaHtml); 
            //================
            if(isset($schemaHtml))
                $indexHtml=preg_replace('/<body(.*?)>/','<body$1>'.$schemaHtml,$indexHtml); 

            
            //write to file index.html 
            $langFolder='';
            $langslug='';
            if($lang!=$config['defaultlang']){
                $langslug=$lang.'/';
                $f=substr($lang, 0,1);
                $langFolder=$f.'/'.$lang.'/';
                $this->app->module("builds")->createdir('output/'.$f.'/'.$lang);
            }
            $indexHtml=$this->app->module("builds")->getBottomScriptAndMinify($indexHtml,$langslug);
            file_put_contents('./output/'.$langFolder.'index.html', $indexHtml);
            $uploadFiles[]='./'.$langFolder.'index.html';

            //write to file cache.js for index.html 
            $slugfilename=$siteurl.$langFolder.'cache.js';            
            //proccess parsed data
            $parsedHtml=['html'=>$mainHtml];
            //get var
            $parsedHtml['var']=['siteurl'=>$siteurl];
            $parsedHtml['var']['data.description']=$config['sitedescription'];
            $mainHtml=$this->app->module("builds")->parseAngularHtml($parsedHtml);
            //print_r($mainHtml);exit;
            //replace parse html from angularjs 

            $mainHtml=str_replace("id='MainController'></div>", "id='MainController'>".$mainHtml."</div>", $html[$lang]['body']);
            $mainHtml=str_replace('class="menuhome"','class="menuhome active"', $mainHtml);   
            //print_r($mainHtml);exit;

            //write cache data            
            $slugfilename=str_replace('/', '', $slugfilename);
            $slugfilename=str_replace(".", '', $slugfilename);
            $slugfilename=str_replace(":", '', $slugfilename);
            $cachedata=$this->app->module('builds')->JSencrypt($mainHtml,$slugfilename,'html',$config);
            $uploadFiles=$this->app->module('builds')->output($slugfilename,$cachedata,$uploadFiles);


            //write cache data for ajaxload
            $slugfilename=$siteurl.$langFolder.'cache';
            $rt=[];
            $rt['controllerName']='Main';
            $rt['model']='Home';
            $rt['title']=$config['sitetitle'];
            $rt['description']=$config['sitedescription'];
            $slugfilename=str_replace('/', '', $slugfilename);
            $slugfilename=str_replace(".", '', $slugfilename);
            $slugfilename=str_replace(":", '', $slugfilename);              
            $cachedata=str_replace(['==','='],'',base64_encode($slugfilename)).base64_encode(gzcompress(json_encode($rt),9));


            $uploadFiles=$this->app->module('builds')->output($slugfilename,$cachedata,$uploadFiles,2);
        }
        
         
        $this->app->module("builds")->pushfile($uploadFiles,$config);
        return true;
    },
    "sendMsg"=>function($id, $msg,$processText='',$color='',$isNotBreakLine=0) {
        if(empty($color))$color='#000';
        $message='<span style="color:'.$color.'">'.$msg.'</span>'.($isNotBreakLine?'':'<br />') ;
        if($id=='DATA'){
            $message=$msg;
        }

         $d = array('processText'=>$processText,'message' => $message);
          echo "id: $id" . PHP_EOL;
          echo "data: ".json_encode($d)  . PHP_EOL;
          echo PHP_EOL;
          ob_flush();
          flush();
    },
    
    "getMenuData"=>function(){
        $rt=[];
        $catnames=['day-xam-2','dich-vu','cham-soc-da'];
        $groups=$this->app->db->find('addons/groups',['filter'=>['slug'=>['$in'=>$catnames]]]);
        foreach ($groups as $key => $g) {
            $group=[];
            $group['name']=$g['name'];
                $curP=$this->app->db->find('addons/posts',['filter'=>['gid'=>$g['_id'],'publish'=>1],'limit'=>4]);
                $posts=[];
                foreach ($curP as $key => $post) { 
                    $p['title']=$post['title'];
                    $p['slug']=$post['slug'];
                    
                    $posts[$post['_id']]=$p;

                }
                $group['posts']=$posts;                
            
            $rt[$g['_id']]=$group;
        }
        return $rt;
    },
    "buildMenu"=>function($config){
        
        $uploadFiles=[];
        
        //get menu data
        $rt=[];
        
        $groups=$this->app->db->find('addons/groups',['filter'=>['slug'=>['$in'=>$catnames]]]);
        foreach ($groups as $key => $g) {
            $group=[];
            $group['name']=$g['name'];
                $curP=$this->app->db->find('addons/posts',['filter'=>['gid'=>$g['_id'],'publish'=>1],'limit'=>4]);
                $posts=[];
                foreach ($curP as $key => $post) { 
                    $p['title']='test';
                    $p['slug']=$post['slug'];
                    
                    if(isset($post['featureimage'])){
                        $feature_image=$this->app->module("builds")->sitebaseurl().str_replace("site:",$this->app->pathToUrl('site:'),$post['featureimage']);                               
                        $files=$this->app->module("builds")->getCacheImage($feature_image, $post['slug'],$config['arrSize']['menu']);
                        $p['featureimage']=$files['filename'];  
                        $p['featureimagetype']=$files['type'];
                        $uploadFiles=array_merge($uploadFiles,$files['arrFiles']);                        
                    }
                    $posts[$post['_id']]=$p;

                }
                $group['posts']=$posts;                
            
            $rt[$g['_id']]=$group;
        }


        $filename=str_replace(['=','/'], '', base64_encode('menudata'));
        $cachedatafile=COCKPIT_DIR.'/output/'.$filename;
        $uploadFiles[]='./'.$filename;
        $myfile = fopen($cachedatafile, "w") or die("Unable to open file!");   
        //print_r($rt);                 
        fwrite($myfile,base64_encode(gzcompress(json_encode($rt),9)));
        fclose($myfile); 
        $this->app->module("builds")->pushfile($uploadFiles,$config);
        return true;
    },
    'injectGACode'=>function($html,$exceptions=[]){
        $gacodes = $this->app->db->find("ads/gacodes");
        //print_r($gacodes->toArray());
        foreach($gacodes as $k=>$v){
            if( !in_array($v['name'],$exceptions) && $v['publish']==1)
                $html=str_replace('[['.$v['name'].']]', $v['code'], $html);
            else
                $html=str_replace('[['.$v['name'].']]', '', $html);
        }
        //print_r($html);exit;
        return $html;
    },
    "getUrlContent"=>function($url,$customheader=[],$cookies=''){
        $rt='';
        $headers = array();
        $headers['Cookie'] = $cookies;
        //$headers['Connection'] = 'keep-alive';
        $headers['Referer'] = 'http://google.com';
        $headers['User-Agent'] = 'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:28.0) Gecko/20100101 Firefox/28.0';
        //$headers['Content-Type'] = 'application/x-www-form-urlencoded';
        $headers['Accept'] = 'application/json, text/javascript, */*; q=0.01';
        //$headers['Cookie'] = $cookies;
        //$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        $headers['Origin'] = 'http://google.com';
        $headers['Accept-Encoding'] = 'gzip, deflate, sdch';
        $headers['Accept-Language'] = 'en-US,en;q=0.8,vi;q=0.6';
        
        //overwrite headers
        if(count($customheader)>0){
            foreach ($customheader as $key => $value) {
                $headers[$key]=$value;
            }
        }
       foreach($headers as $k=>$v){
          $headerdata[]=$k.': '.$v;
        }


        $ch = curl_init($url);
       //curl_setopt($ch, CURLOPT_PROXY, '127.0.0.1:8888');
       curl_setopt($ch, CURLOPT_TIMEOUT, 10);
       curl_setopt($ch, CURLOPT_FAILONERROR, 1);
       if(empty($cookies)){
            curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
            curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt'); 
       }
       
       //curl_setopt($ch, CURLOPT_POST, 1);
       //curl_setopt($ch, CURLOPT_POSTFIELDS, $BODY);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt ( $ch , CURLOPT_HEADER , 1 );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerdata);
        curl_setopt($ch, CURLOPT_ENCODING , '');
       $response = curl_exec($ch);
       $header_size = curl_getinfo($ch,CURLINFO_HEADER_SIZE);
        $result['header'] = substr($response, 0, $header_size);
        $result['body'] = substr( $response, $header_size );

       //  print_r($url);print_r("\r\n");
       
       $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
       curl_close($ch);

       $detail=$result['body'];
       //if($gz)$detail=gzdecode($detail);
        //print_r($detail);
       
        //get cookie
        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $response, $matches);
        $cookies = array();
        foreach($matches[1] as $item) {
            parse_str($item, $cookie);
            $cookies = array_merge($cookies, $cookie);
        }    

        $header=[];
        //list($header, $body) = explode("\r\n\r\n", $result['header'], 2);
        $result['header']=explode("\r\n",$result['header']);
        foreach($result['header'] as $k=>$v){
            if(empty(trim($v)))continue;
            if($k==0)$header['status']=$v;
            else{
                $header[trim(substr($v,0,strpos($v,':')))]=trim(substr($v,strpos($v,':')+1));                
            }
        }
        //print_r($result['header']);exit;


        return ['html'=>$detail,'cookies'=>$cookies,'header'=>$header];
    },
    /**
     * GZIPs a file on disk (appending .gz to the name)
     *
     * From http://stackoverflow.com/questions/6073397/how-do-you-create-a-gz-file-using-php
     * Based on function by Kioob at:
     * http://www.php.net/manual/en/function.gzwrite.php#34955
     * 
     * @param string $source Path to file that should be compressed
     * @param integer $level GZIP compression level (default: 9)
     * @return string New filename (with .gz appended) if success, or false if operation fails
     */
    "gzCompressFile"=>function($source, $level = 9){ 
        $dest = $source . '.gz'; 
        $mode = 'wb' . $level; 
        $error = false; 
        if ($fp_out = gzopen($dest, $mode)) { 
            if ($fp_in = fopen($source,'rb')) { 
                while (!feof($fp_in)) 
                    gzwrite($fp_out, fread($fp_in, 1024 * 512)); 
                fclose($fp_in); 
            } else {
                $error = true; 
            }
            gzclose($fp_out); 
        } else {
            $error = true; 
        }
        if ($error)
            return false; 
        else
            return $dest; 
    },
    "camelize"=>function($scored,$isCapital=0) {
        $scored=str_replace('-', '_', $scored);
        $rt= lcfirst(
          implode(
            '',
            array_map(
              'ucfirst',
              array_map(
                'strtolower',
                explode(
                  '_', $scored)))));
        if($isCapital){
            $rt=strtoupper(substr($rt, 0,1)).substr($rt,1);
        }
        return $rt;
    },
        /**
        * Transforms a camelCasedString to an under_scored_one
        */
    "underscore"=>function($cameled) {
        return implode(
          '_',
          array_map(
            'strtolower',
            preg_split('/([A-Z]{1}[^A-Z]*)/', $cameled, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY)));
    },
    "numberWithCommas"=>function($value) {
        return number_format($value,0,',','.');
    }
]);
// ADMIN
if(COCKPIT_ADMIN) {
    //register acl
    $this("acl")->addResource($moduleName, ['manage.index', 'manage.edit']);
    $app->on("admin.init", function() use($app,$moduleName){
        if (!$this->module('auth')->hasaccess($moduleName, ['manage.index', 'manage.edit'])) return;
        // bind routes
        $app->bindClass("$moduleName\\Controller\\Main", $moduleName);
        
        // bind api
        $app->bindClass("$moduleName\\Controller\\Api", "api/$moduleName");
        
        // menu item
        $user=$app->module("auth")->getUser();
        //if($user['group']=='admin'){
            $app("admin")->menu("top", [
                "url"    => $app->routeUrl("/$moduleName/index"),
                "label"  => '<i class="uk-icon-building"></i>',
                "title"  => $app("i18n")->get("Build Websites")
            ], 0);
        //}
        
    });
    
}



<?php

namespace socials\Controller;

class Main extends \Cockpit\Controller {
	private $moduleName='';

	public function __construct($app) {
        parent::__construct($app);
        //duy.ha: get path to detect module name
        $pathName=explode("\\",__DIR__);
        if(count($pathName)<=1)$pathName=explode("/",__DIR__);        
        $moduleName=$pathName[count($pathName)-2];
        $this->moduleName=$moduleName;
    }

    public function index() {
       $viewdata=['action'=>__FUNCTION__,
                    'moduleName'=>$this->moduleName];
        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.index')) {  
            return false;
        }
        
        // $curS=$this->app->db->find("addons/socialaccs");
        // foreach($curS as $k=>$v){
        //   //print_r($v);exit;
        //   unset($v['_id']);
        //   $this->app->db->save("social/accs",$v);
        // }
        //  $t=$this->app->db->find("social/accs")->toArray();
        

        //$viewdata['buildModules']=$buildModules;
        return $this->render("$this->moduleName:views/". $viewdata["action"].".php",$viewdata);
    }

    public function index2() {
       $viewdata=['action'=>__FUNCTION__,
                    'moduleName'=>$this->moduleName];
        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.index')) {  
            return false;
        }        
      
        return $this->render("$this->moduleName:views/". $viewdata["action"].".php",$viewdata);
    }

    public function accountlist() {
       $viewdata=['action'=>__FUNCTION__,
                    'moduleName'=>$this->moduleName];
        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.index')) {  
            return false;
        }
       
        //$viewdata['buildModules']=$buildModules;
        return $this->render("$this->moduleName:views/". $viewdata["action"].".php",$viewdata);
    }

    public function account($id = null) {
        $viewdata=['action'=>__FUNCTION__,
                    'moduleName'=>$this->moduleName]; 
        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.edit')) {  
            return false;
        }
        @session_start();
        $item=$this->app->db->findOne("social/accs", ['_id'=>$id]);
        //verify fb account
        if(isset($_GET['state']) && isset($item['name']) && $item['name']=='facebook'){
            //get item
            
            $item=$this->app->module('socials')->fixBigNumber($item);
        	\Facebook\FacebookSession::setDefaultApplication($item['field1'], $item['field2']);
            
        	$helper = new \Facebook\FacebookRedirectLoginHelper('http://'.$_SERVER['HTTP_HOST'].$this->app->routeUrl('/socials/account/'.$id));
            try {
              $session = $helper->getSessionFromRedirect();
 //print_r($session);exit;
              
             // $me = (new \Facebook\FacebookRequest(
             //      $session, 'GET', '/'.$item['field3'].'?fields=access_token'
             //    ))->execute()->getGraphObject(\Facebook\GraphUser::className());
             //  print_r($me->getProperty('access_token'));
             //print_r($me->backingData['access_token']);exit;

              $request = new \Facebook\FacebookRequest( $session,'GET', '/'.$item['field3'].'?fields=access_token' );
                $response = $request->execute();
                $graphObject = $response->getGraphObject()->asArray(); 
                
            $token=$graphObject['access_token'];
            
              $item['token']=$token;
              $item=$this->app->module('socials')->fixBigNumber($item,1);
              $this->app->db->save("social/accs", $item);
            
              //save into db

              


            } catch(\Facebook\FacebookRequestException $ex) {
                //print_r($ex);exit;
            
              // When Facebook returns an error
            } catch(\Exception $ex) {        
            //print_r($ex);exit;    

              // When validation fails or other local issues
            }
        }

        //verify tumblr
        if(isset($_GET['oauth_token']) && isset($item['name']) && $item['name']=='tumblr'){
          $tum_oauth = new \tumblroauth\TumblrOAuth($item['field1'], $item['field2'], $_SESSION['request_token'], $_SESSION['request_token_secret']);

          // Ok, let's get an Access Token. We'll need to pass along our oauth_verifier which was given to us in the URL. 
          $access_token = $tum_oauth->getAccessToken($_REQUEST['oauth_verifier']);

          // We're done with the Request Token and Secret so let's remove those.
          unset($_SESSION['request_token']);
          unset($_SESSION['request_token_secret']);

          // Make sure nothing went wrong.
          if (200 == $tum_oauth->http_code) {
            // good to go
          } else {
            die('Unable to authenticate');
          }
           $item['oauth_token']=$access_token['oauth_token'];
           $item['oauth_token_secret']=$access_token['oauth_token_secret'];
            
            $this->app->db->save("social/accs", $item);
          

        }

        //verify flickr
        if(isset($_GET['oauth_verifier']) && isset($item['name']) && $item['name']=='flickr'){
          require_once(COCKPIT_DIR.'/vendor/tumblroauth/scOAuth.php');
          
          $tum_oauth = new \wpScoopITOAuth($item['field1'], $item['field2'], $item['oauth_token'], $item['oauth_token_secret']);
           $tum_oauth->cockapp=$this->app;
           $tum_oauth->baseURL = 'https://www.flickr.com/services'; 
           $tum_oauth->request_token_path = '/oauth/request_token'; 
           $tum_oauth->access_token_path = '/oauth/access_token';
           $access_token = $tum_oauth->getAccToken($_GET['oauth_verifier']);
           if(isset($access_token['oauth_token'])){
            $item['accessToken']=$access_token['oauth_token'];  
            $item['accessTokenSec'] = $access_token['oauth_token_secret'];
            $this->app->db->save("social/accs", $item);
           }
          
        }

        //verify scoopit
        if(isset($_GET['oauth_verifier']) && isset($item['name']) && $item['name']=='scoopit'){
          require_once(COCKPIT_DIR.'/vendor/tumblroauth/scOAuth.php');
          
          $tum_oauth = new \wpScoopITOAuth($item['field1'], $item['field2'], $item['oAuthToken'], $item['oAuthTokenSecret']);
           $tum_oauth->cockapp=$this->app;
           
           $access_token = $tum_oauth->getAccToken($_GET['oauth_verifier']);
           if(isset($access_token['oauth_token'])){
            $item['accessToken']=$access_token['oauth_token'];  
            $item['accessTokenSec'] = $access_token['oauth_token_secret'];
            $this->app->db->save("social/accs", $item);
           }
          
        }

        //verify plurk
        if(isset($_GET['oauth_verifier']) && isset($item['name']) && $item['name']=='plurk'){
          require_once(COCKPIT_DIR.'/vendor/tumblroauth/plurkOAuth.php');
          $tum_oauth = new \wpPlurkOAuth($item['field2'], $item['field3'], $item['pkOAuthToken'], $item['pkOAuthTokenSecret']); //prr($tum_oauth);
          $tum_oauth->cockapp=$this->app;
          $access_token = $tum_oauth->getAccToken($_GET['oauth_verifier']);

           if(isset($access_token['oauth_token'])){
            $item['pkAccessTocken']=$access_token['oauth_token'];  
            $item['pkAccessTockenSec'] = $access_token['oauth_token_secret'];
            $this->app->db->save("social/accs", $item);
           }
          
        }

        //verify linkedin
        if(isset($_GET['code']) && isset($item['name']) && $item['name']=='linkedin'){
          $callback_url ='http://'.$_SERVER['HTTP_HOST'].$this->app->routeUrl('/socials/account/'.$item['_id']);
          //request accessToken:
          $headers = array();
            $headers['X-Requested-With'] = 'XMLHttpRequest';
            $headers['Connection'] = 'keep-alive';
            $headers['Referer'] = 'www.linkedin.com';
            $headers['User-Agent'] = 'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:28.0) Gecko/20100101 Firefox/28.0';
            $headers['Content-Type'] = 'application/x-www-form-urlencoded';
            $headers['Accept'] = 'application/json, text/javascript, */*; q=0.01';
            //$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
            $headers['Origin'] = 'www.linkedin.com';
            $headers['Accept-Encoding'] = 'gzip, deflate';
            $headers['Accept-Language'] = 'en-US,en;q=0.5';
            $headers['Cache-Control'] = 'no-cache';
            $fields=[];
            $fields['grant_type']='authorization_code';
            $fields['code']=$_GET['code'];
            $fields['redirect_uri']=$callback_url;
            $fields['client_id']=$item['field1'];
            $fields['client_secret']=$item['field2'];
            $requestUrl='https://www.linkedin.com/uas/oauth2/accessToken';
            $response = $this->app->module('socials')->request2($requestUrl, array('timeout' => 45,'method'=>'POST','headers'=>$headers,'params'=>$fields)); 
            //print_r($response);exit;
            if($response['code']==200){
              $data=json_decode($response['body'],true);
              $item['access_token']=$data['access_token'];
              $this->app->db->save("social/accs", $item);
            }
        }

        //verify blogger
        if(isset($_GET['code']) && isset($item['name']) && $item['name']=='blogger'){
          $item=$this->app->module('socials')->fixBigNumber($item);
          $callback_url ='http://'.$_SERVER['HTTP_HOST'].$this->app->routeUrl('/socials/account/'.$item['_id']);
          require_once(COCKPIT_DIR.'/vendor/Google/autoload.php');  
          $client = new \Google_Client();
          $client->setClientId($item['field1']);
          $client->setClientSecret($item['field2']);
          $client->setRedirectUri($callback_url);
          
          $client->authenticate($_GET['code']);
          
          $item['access_token']=$client->getAccessToken();
          //$item['access_token']=json_decode($item['access_token'],true)['access_token'];
          $item=$this->app->module('socials')->fixBigNumber($item,1);
          $this->app->db->save("social/accs", $item);
        }
        
        $viewdata['id']=$id;
        return $this->render("$this->moduleName:views/". $viewdata["action"].".php",$viewdata);
    }

    
}


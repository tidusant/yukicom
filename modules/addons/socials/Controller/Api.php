<?php

namespace socials\Controller;
// require COCKPIT_DIR . '/vendor/FBSDK/autoload.php';
//         use Facebook\FacebookSession;
class Api extends \Cockpit\Controller {
    private $moduleName='';    
    private $sitebaseurl='';
    public function __construct($app) {
        parent::__construct($app);
        $this->sitebaseurl=$this->module("builds")->sitebaseurl();
        //duy.ha: get path to detect module name
        $pathName=explode("\\",__DIR__);
        if(count($pathName)<=1)$pathName=explode('/',__DIR__);
        $moduleName=$pathName[count($pathName)-2];
        $this->moduleName=$moduleName; 

    }

    public function testpost(){
        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.edit'))return false;
        $accname = $this->param("accname", null);
        if($accname=="facebook"){
            $data=[];
            $data['name'] = 'Facebook API: Posting As A Page using Graph API v2.x and PHP SDK 4.0.x';
            $data['link'] = 'http://dayxam.com/khoa-phun-theu-tham-my-khuyen-mai-lon-2015/';
            $data['caption'] = 'The Facebook API lets you post to Pages you administrate via the API. This tutorial shows you how to achieve this using the Facebook PHP SDK v4.0.x and Graph API 2.x.';
            $data['message'] = 'Check out my new blog post!';
            return $this->app->module('socials')->postFB($data);
           
        }
        else if($accname=="tumblr"){
            $data=[];
            $data = array();
            $data['type'] = "text";
            $data['tags'] = "test";
            //$data['link'] = 'http://nhatkychame.vn/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh560.jpg';
            //$data['source'] = 'http://nhatkychame.vn/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh/';
            $data['title'] = "titlec text";
            $data['body'] = "body text <img src='http://nhatkychame.vn/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh560.jpg' /> <br /> xem them http://nhatkychame.vn/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh/";
           return $this->app->module('socials')->postTumblr($data);

        }
        else if($accname=="stumbleupon"){
           $data = array();
            $data['message'] = "<p> adsfasd fsaftest</p>";
            $data['tags'] = "test";
            
            $data['link']="http://dayxam.com/khoa-phun-theu-tham-my-khuyen-mai-lon-2015/";

            return $this->app->module('socials')->postStumbleupon($data);
        }
        else if($accname=="diigo"){
            $data = array();
            $data['url'] = "http://dayxam.com/khoa-phun-theu-tham-my-khuyen-mai-lon-2015/";
            $data['title'] = "title text";
            $data['description'] = "body text";
            $data['tags'] = "body text";            

            return $this->app->module('socials')->postDiigo($data);
        }
        else if($accname=="delicious"){
            $data = array();
            $data['url'] = "http://dayxam.com/khoa-phun-theu-tham-my-khuyen-mai-lon-2015/";
            $data['note'] = "note text";
            $data['description'] = "body text";
            $data['tags'] = "body text";            

            return $this->app->module('socials')->postDelicious($data);
        }
        else if($accname=="kippt"){
            $data = array();
            $data['url'] = "http://colisshop.com/dam-thun-cat-sieu-re-60k-ma-van-de-thuong/";
            $data['title'] = "note text";
            $data['message'] = "body text";
            return $this->app->module('socials')->postKippt($data);
        }
        else if($accname=="flickr"){
            $data = array();
            $data['imgURL'] = "http://nhatkychame.vn/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh560.jpg";
            $data['title'] = "note text";
            $data['description'] = "body text http://colisshop.com/dam-thun-cat-sieu-re-60k-ma-van-de-thuong/";
            $data['tags'] = "body tags";    
            //print_r($data);exit;
            return $this->app->module('socials')->postFlickr($data);
        }
        else if($accname=="livejournal"){
            $data = array();
            
            $data['title'] = "note text";
            $data['message'] = "body text <img src='http://nhatkychame.vn/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh560.jpg' />  <br /> xem thêm <a href='http://colisshop.com/dam-thun-cat-sieu-re-60k-ma-van-de-thuong/' /> test</a>";
            $data['tags'] = "body tags";    
            return $this->app->module('socials')->postLivejournal($data);
        }
         else if($accname=="plurk"){
            $data = array();
            $data['message'] = "aaaaaaaaaaaaaa http://nhatkychame.vn/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh560.jpg  <br /> xem thêm http://colisshop.com/dam-thun-cat-sieu-re-60k-ma-van-de-thuong/";           
            return $this->app->module('socials')->postPlurk($data);
        }
        else if($accname=="scoopit"){
            $data = array();
            $data['imgURL'] = "http://nhatkychame.vn/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh560.jpg";
            $data['title'] = "Test Post - Title";
            $data['content'] = "body text Test Post, Description";
            $data['url'] = "http://nhatkychame.vn/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh/";    
            return $this->app->module('socials')->postScoopit($data);
        }
        else if($accname=="twitter"){
            $data = array();
            $data['message'] = "aaaaaaaaaaaaaa http://nhatkychame.vn/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh/"; 
            //$data['imgURL'] = "http://nhatkychame.vn/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh560.jpg";
            return $this->app->module('socials')->postTwitter($data);
        }
        else if($accname=="vk"){
            $data = array();
            $data['url'] = "http://nhatkychame.vn/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh/";                
            $data['message'] = "aaaaaaaaaaaaaa http://nhatkychame.vn/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh/"; 
            $data['imgURL'] = "http://nhatkychame.vn/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh560.jpg";
            return $this->app->module('socials')->postVk($data);
        }
        else if($accname=="linkedin"){
            $data = array();
            $data['url'] = "http://nhatkychame.vn/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh/";                
            $data['title']='test title';
            $data['description'] = "aaaaaaaaaaaaaa "; 
            $data['imgURL'] = "http://nhatkychame.vn/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh560.jpg";
            $data['tags']='test,title';
            return $this->app->module('socials')->postLinkedin($data);
        }
        else if($accname=="wordpress"){
            $data = array();
            //$data['url'] = "http://nhatkychame.vn/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh/";                
            $data['title']='test title';
            $data['description'] = "aaaaaaaaaaaaaa http://nhatkychame.vn/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh/"; 
            $data['imgURL'] = "http://nhatkychame.vn/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh560.jpg";
            $data['tags']='test,title';
            return $this->app->module('socials')->postWordpress($data);
        }
        else if($accname=="blogger"){
            $data = array();
            
            $data['url'] = "http://nhatkychame.vn/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh/";                             
            $data['title']='test title';
            $data['description'] = "aaaaaaaaaaaaaa <img src='http://nhatkychame.vn/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh560.jpg' />"; 
            $data['tags']='test,title';
            return $this->app->module('socials')->postBlogger($data);
        }
        else{
            return 0;
        }
    }

    public function verifyaccount(){
        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.edit'))return false;
        $accname = $this->param("accname", null);
        if($accname=="facebook"){
           return $this->verifyfbaccount();
        }
        else if($accname=="tumblr"){
           return $this->verifytumblraccount();
        }
        else if($accname=="flickr"){
           return $this->verifyflickraccount();
        }
        else if($accname=="linkedin"){
           return $this->verifylinkedinaccount();
        }
        else if($accname=="plurk"){
           return $this->verifyplurkaccount();
        }
        else if($accname=="scoopit"){
           return $this->verifyscoopitaccount();
        }
        else if($accname=="vk"){
           return $this->verifyvkaccount();
        }
         else if($accname=="blogger"){
           return $this->verifybloggeraccount();
        }
        else{
            return 0;
        }
    }

     public function checkverify(){
        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.edit'))return false;
        $accname = $this->param("accname", null);
        if($accname=="facebook"){
           return $this->checkverifyfb();
        }
        if($accname=="blogger"){
           return $this->checkverifyblogger();
        }
        else if($accname=="tumblr"){
           return $this->checkverifytumblr();
        }
        else if($accname=="stumbleupon"){
           return $this->checkverifystumbleupon();
        }
        else if($accname=="diigo"){
           return $this->checkverifydiigo();
        }
        else if($accname=="delicious"){
           return $this->checkverifydelicious();
        }
        else if($accname=="kippt"){
           return $this->checkverifykippt();
        }
        else if($accname=="flickr"){
           return $this->checkverifyflickr();
        }
        else if($accname=="linkedin"){
           return $this->checkverifylinkedin();
        }
        else if($accname=="livejournal"){
           return $this->checkverifylivejournal();
        }
        else if($accname=="plurk"){
           return $this->checkverifyplurk();
        }
        else if($accname=="scoopit"){
           return $this->checkverifyscoopit();
        }
        else if($accname=="twitter"){
           return $this->checkverifytwitter();
        }
        else if($accname=="vk"){
           return $this->checkverifyvk();
        }
        else if($accname=="wordpress"){
           return $this->checkverifywordpress();
        }
        else {
            return json_encode(['error'=>'nnote verify']);
        }
    }

    public function testfbpost(){
        $data['message']="test";
        //$data['link']="http://dayxam.com/khoa-phun-theu-tham-my-khuyen-mai-lon-2015/";

        return $this->app->module('socials')->postFB($data);
        
    }

    public function testtumblrpost(){
        $data = array();
            $data['type'] = "text";
            $data['title'] = "title text";
            $data['body'] = "body text";
        //$data['link']="http://dayxam.com/khoa-phun-theu-tham-my-khuyen-mai-lon-2015/";

        return $this->app->module('socials')->postTumblr($data);
        
    }

    public function teststumbleuponpost(){
        
        
    }

    private function verifyfbaccount(){

        
        $item = $this->param("item", null);
        if($item) {
            $item=$this->app->module('socials')->fixBigNumber($item);

            \Facebook\FacebookSession::setDefaultApplication($item['field1'], $item['field2']);
            // // Add `use Facebook\FacebookRedirectLoginHelper;` to top of file
            $helper = new \Facebook\FacebookRedirectLoginHelper('http://'.$_SERVER['HTTP_HOST'].$this->app->routeUrl('/socials/account/'.$item['_id']));
            //$loginUrl = $helper->getLoginUrl(array( 'email', 'publish_actions' ));
            $loginUrl = $helper->getLoginUrl(['manage_pages,publish_pages']);
            return json_encode(['fblogin'=>$loginUrl]);
            
        }
    }

     private function verifytumblraccount(){

        
        $item = $this->param("item", null);
        if($item) {
            
            $callback_url ='http://'.$_SERVER['HTTP_HOST'].$this->app->routeUrl('/socials/account/'.$item['_id']);
            $tum_oauth = new \tumblroauth\TumblrOAuth($item['field1'], $item['field2']);
            $request_token = $tum_oauth->getRequestToken($callback_url);
            @session_start();
            // Store the request token and Request Token Secret as out callback.php script will need this
            $_SESSION['request_token'] = $token = $request_token['oauth_token'];
            $_SESSION['request_token_secret'] = $request_token['oauth_token_secret'];

            // Check the HTTP Code.  It should be a 200 (OK), if it's anything else then something didn't work.
            switch ($tum_oauth->http_code) {
              case 200:
                // Ask Tumblr to give us a special address to their login page
                $url = $tum_oauth->getAuthorizeURL($token);
                
                // Redirect the user to the login URL given to us by Tumblr
                return json_encode(['fblogin'=>$url]);
                
                // That's it for our side.  The user is sent to a Tumblr Login page and
                // asked to authroize our app.  After that, Tumblr sends the user back to
                // our Callback URL (callback.php) along with some information we need to get
                // an access token.
                
                break;
            default:
                // Give an error message
                echo 'Could not connect to Tumblr. Refresh the page or try again later.';
            }
            exit();
        }
    }

    private function verifyflickraccount(){

        
        $item = $this->param("item", null);
        if($item) {
            require_once(COCKPIT_DIR.'/vendor/tumblroauth/scOAuth.php');   
            $tum_oauth = new \wpScoopITOAuth($item['field1'], $item['field2']);
            $tum_oauth->cockapp=$this->app;
            $tum_oauth->baseURL = 'https://www.flickr.com/services';
            $tum_oauth->request_token_path = '/oauth/request_token'; $tum_oauth->access_token_path = '/oauth/access_token';
            $request_token = $tum_oauth->getReqToken('http://'.$_SERVER['HTTP_HOST'].$this->app->routeUrl('/socials/account/'.$item['_id']));
            //save oAuth
            $item['oauth_token']=$request_token['oauth_token'];
            $item['oauth_token_secret']=$request_token['oauth_token_secret'];
            $this->app->db->save("social/accs", $item);
            $url = 'https://www.flickr.com/services/oauth/authorize?oauth_token='.$request_token['oauth_token'];
            
            
            return json_encode(['fblogin'=>$url]);
        }
    }

    private function verifyscoopitaccount(){

        
        $item = $this->param("item", null);
        if($item) {
            require_once(COCKPIT_DIR.'/vendor/tumblroauth/scOAuth.php');   
            $tum_oauth = new \wpScoopITOAuth($item['field1'], $item['field2']);
            $tum_oauth->cockapp=$this->app;
            $callback_url='http://'.$_SERVER['HTTP_HOST'].$this->app->routeUrl('/socials/account/'.$item['_id']);
            $request_token = $tum_oauth->getReqToken($callback_url);
            //save oAuth
            $item['oAuthToken']=$request_token['oauth_token'];
            $item['oAuthTokenSecret']=$request_token['oauth_token_secret'];
            $this->app->db->save("social/accs", $item);
            $url = 'http://www.scoop.it/oauth/authorize?oauth_token='.$request_token['oauth_token'];
            
            
            return json_encode(['fblogin'=>$url]);
        }
    }

    private function verifyplurkaccount(){

        
        $item = $this->param("item", null);
        if($item) {
            require_once(COCKPIT_DIR.'/vendor/tumblroauth/plurkOAuth.php');   
            $callback_url ='http://'.$_SERVER['HTTP_HOST'].$this->app->routeUrl('/socials/account/'.$item['_id']);           
            $tum_oauth = new \wpPlurkOAuth($item['field2'], $item['field3']);
            $tum_oauth->cockapp=$this->app;
            $request_token = $tum_oauth->getReqToken($callback_url); 
            
              $item['pkOAuthToken'] = $request_token['oauth_token'];
              $item['pkOAuthTokenSecret'] = $request_token['oauth_token_secret'];
              $url = 'http://www.plurk.com/OAuth/authorize?oauth_token='.$item['pkOAuthToken'];             
            $this->app->db->save("social/accs", $item);                       
            
            return json_encode(['fblogin'=>$url]);
        }
    }

    private function verifylinkedinaccount(){

        
        $item = $this->param("item", null);
        if($item) {            
            $callback_url ='http://'.$_SERVER['HTTP_HOST'].$this->app->routeUrl('/socials/account/'.$item['_id']);           
            $url='https://www.linkedin.com/uas/oauth2/authorization?response_type=code';
            $url.='&client_id='.$item['field1'];
            $url.='&redirect_uri='.urlencode($callback_url);
            $url.='&state='.$this->app->module('socials')->create_random_string(8);
            $url.='&scope=r_basicprofile%20w_share';
            return json_encode(['fblogin'=>$url]);
        }
        return 0;
    }

    private function verifyvkaccount(){        
        $item = $this->param("item", null);
        if($item) {            
            //$callback_url ='http://'.$_SERVER['HTTP_HOST'].$this->app->routeUrl('/socials/account/'.$item['_id']);           
            $url='http://api.vkontakte.ru/oauth/authorize?client_id='.$item['field1'].'&scope=offline,wall,photos,pages&redirect_uri=http://api.vkontakte.ru/blank.html&display=page&response_type=token';
            //$result=$this->app->module('socials')->request2($url,[]);
            //print_r($result);exit;
            return json_encode(['fblogin'=>$url]);
        }
        return 0;
    }

    public function verifybloggeraccount(){

        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.edit'))return false;
        $item = $this->param("item", null);
        $item=$this->app->db->findOne("social/accs", ['_id'=>$item['_id']]);

        if($item) {
            $item=$this->app->module('socials')->fixBigNumber($item);
            $callback_url ='http://'.$_SERVER['HTTP_HOST'].$this->app->routeUrl('/socials/account/'.$item['_id']);
            require_once(COCKPIT_DIR.'/vendor/Google/autoload.php');  
            $client = new \Google_Client();
            $client->setClientId($item['field1']);
            $client->setClientSecret($item['field2']);
            $client->setRedirectUri($callback_url);
            $client->setScopes(["https://www.googleapis.com/auth/plus.login","https://www.googleapis.com/auth/blogger"]);
            $client->setAccessType('offline');
            $client->setApprovalPrompt('force');
            $authUrl = $client->createAuthUrl();

            return json_encode(['fblogin'=>$authUrl]);           
            
            
        }
        return 0;
    }

    public function checkverifyblogger(){

        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.edit'))return false;
        $item = $this->param("item", null);
        $item=$this->app->db->findOne("social/accs", ['_id'=>$item['_id']]);
        if($item && isset($item['access_token'])) {
            $item=$this->app->module('socials')->fixBigNumber($item);
            require_once(COCKPIT_DIR.'/vendor/Google/autoload.php');  
            $client = new \Google_Client();
            $client->setClientId($item['field1']);
            $client->setClientSecret($item['field2']);
            $client->setAccessToken($item['access_token']);
            $client->refreshToken($client->getRefreshToken());
            $ac=$client->getAccessToken();
            $client->setAccessToken($ac);
            
            if($client->isAccessTokenExpired()){

                return json_encode(['error'=>'Access token expired']);
            }
            return json_encode(['success'=>1]);
            
            
        }
        return json_encode(['error'=>'not have access token']);
    }

    public function checkverifyfb(){

        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.edit'))return false;
        $item = $this->param("item", null);
        $item=$this->app->db->findOne("social/accs", ['_id'=>$item['_id']]);
        if($item) {

            $item=$this->app->module('socials')->fixBigNumber($item);

            \Facebook\FacebookSession::setDefaultApplication($item['field1'], $item['field2']);
             try {
                if ( isset( $item['token'] ) ) {

                  // create new session from saved access_token
                  $session = new \Facebook\FacebookSession( $item['token'] );
                  
                  // validate the access_token to make sure it's still valid
                 
                    if ( !$session->validate() ) {
                      $session = null;
                    }
                    
                }

              } 
              catch(\Facebook\FacebookSDKException $e){
                print_r($e);
                $session = null;
              }
              catch ( Exception $e ) {
                // catch any exceptions
                print_r($e);
                $session = null;
              }
               

            if ( isset( $session ) ) {   
    
                $request = new \Facebook\FacebookRequest( $session,'GET', '/me/feed' );
                $response = $request->execute();                
                // get response
                $graphObject = $response->getGraphObject()->asArray(); 
                
                //print_r($graphObject);exit;
                if(isset($graphObject['data']) && isset($graphObject['data'][0]) && isset($graphObject['data'][0]->id)){
                    return json_encode(['success'=>1]);
                }
                else{
                    return json_encode(['error'=>'access token expired']);
                }
             
              return $graphObject;
              
            }else {
                return json_encode(['error'=>'not have access token']);
            }   
            
            
        }
    }

    public function checkverifytumblr(){

        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.edit'))return false;
        $item = $this->param("item", null);
        $item=$this->app->db->findOne("social/accs", ['_id'=>$item['_id']]);
        if($item) {

            $post_URI = 'http://api.tumblr.com/v2/blog/'.$item['field3'].'/followers';

            $tum_oauth = new \tumblroauth\TumblrOAuth($item['field1'],$item['field2'],$item['oauth_token'],$item['oauth_token_secret']);

            // // Make an API call with the TumblrOAuth instance. For text Post, pass parameters of type, title, and body
            // $parameters = array();
            // $parameters['type'] = "text";
            // $parameters['title'] = "title text";
            // $parameters['body'] = "body text";

            $post = $tum_oauth->get($post_URI);
            if($tum_oauth->http_code==200){
                return json_encode(['success'=>1]);
            }
            else{
                return json_encode(['error'=>$post->meta->msg]);
            }
        }
    }

    public function checkverifydiigo(){

        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.edit'))return false;
        $item = $this->param("item", null);
        $item=$this->app->db->findOne("social/accs", ['_id'=>$item['_id']]);
        if($item) {
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
            $result=$this->app->module('socials')->request($ch,'https://secure.diigo.com/api/v2/bookmarks?user='.$item['username'].'&key='.$item['field1'],$headers);
            
           $data=json_decode($result['body']);
            if( $result['code']==200){
                return json_encode(['success'=>1]);
            }
            else{
                return json_encode(['error'=>$result]);
            }
        }
    }

    public function checkverifystumbleupon(){

        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.edit'))return false;
        $item = $this->param("item", null);
        $item=$this->app->db->findOne("social/accs", ['_id'=>$item['_id']]);
        if($item) {
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
            
            //print_r($result);exit;
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
            curl_close($ch);
            //print_r($result);exit;
            $data=json_decode($result['body'],true);
            
            if(isset($data['_success']) && $data['_success']=='true'){
                return json_encode(['success'=>1]);
            }
            else{
                return json_encode(['error'=>$result]);
            }

           
            
            
        }
    }

    public function checkverifydelicious(){

        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.edit'))return false;
        $item = $this->param("item", null);
        $item=$this->app->db->findOne("social/accs", ['_id'=>$item['_id']]);
        if($item) {
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
            
           curl_close($ch);
            $data=json_decode($result['body']);
            
            if(isset($data->status) && $data->status=='success' ){
                return json_encode(['success'=>1]);
            }
            else{
                return json_encode(['error'=>$data->status]);
            }
        }
    }

    public function checkverifykippt(){

        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.edit'))return false;
        $item = $this->param("item", null);
        $item=$this->app->db->findOne("social/accs", ['_id'=>$item['_id']]);
        if($item) {
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
            $result=$this->app->module('socials')->request($ch,'https://kippt.com/api/lists/',$headers);
            
            curl_close($ch);
            
            $data=json_decode($result['body'], true);
            //print_r($data);exit;
            if(isset($data['objects']) && is_array($data['objects'])){
                //get listid
                foreach ($data['objects'] as $list) 
                    if ($list['slug'] == $item['field2']) 
                        $listID = $list['resource_uri']; 
                    if (empty($listID))  
                        $listID = ''; 
                //save listID
                $item['listID']=$listID;
                $this->app->db->save("social/accs", $item);
                return json_encode(['success'=>1]);
            }
            else{
                return json_encode(['error'=>$data['message']]);
            }
        }
    }

    public function checkverifyflickr(){

        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.edit'))return false;
        $item = $this->param("item", null);
        $item=$this->app->db->findOne("social/accs", ['_id'=>$item['_id']]);
        if($item) {
            if(!isset($item['accessToken']) || empty($item['accessToken']))return json_encode(['error'=>'not verify']);
            //print_r(COCKPIT_DIR);exit;
            require_once(COCKPIT_DIR.'/vendor/tumblroauth/scOAuth.php');   
            $tum_oauth = new \wpScoopITOAuth($item['field1'], $item['field2'],$item['accessToken'], $item['accessTokenSec']);
            $tum_oauth->cockapp=$this->app;
            $params = array ('format' => 'php_serial', 'method'=>'flickr.urls.getUserProfile');
           
           $uinfo = $tum_oauth->makeReq('https://api.flickr.com/services/rest/',$params);
           //print_r($uinfo);exit;
           
            if(isset($uinfo['user']) && isset($uinfo['user']['nsid'])){
                $item['userURL']=$uinfo['user']['url'];
                $this->app->db->save("social/accs", $item);
                return json_encode(['success'=>1]);
            }
            else{
                return json_encode(['error'=>$data['message']]);
            }            
        }
    }

    public function checkverifyscoopit(){

        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.edit'))return false;
        $item = $this->param("item", null);
        $item=$this->app->db->findOne("social/accs", ['_id'=>$item['_id']]);
        if($item) {
            if(!isset($item['accessToken']) || empty($item['accessToken']))return json_encode(['error'=>'not verify']);
            //print_r(COCKPIT_DIR);exit;
            require_once(COCKPIT_DIR.'/vendor/tumblroauth/scOAuth.php');   
            $tum_oauth = new \wpScoopITOAuth($item['field1'], $item['field2'],$item['accessToken'], $item['accessTokenSec']);
            $tum_oauth->cockapp=$this->app;
            
           $uinfo = $tum_oauth->makeReq('http://www.scoop.it/api/1/profile','');
           //print_r($uinfo);exit;
           
            if(isset($uinfo['user']) && isset($uinfo['user']['name'])){
                return json_encode(['success'=>1]);
            }
            else{
                return json_encode(['error'=>$uinfo]);
            }            
        }
    }

    public function checkverifylinkedin(){

        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.edit'))return false;
        $item = $this->param("item", null);
        $item=$this->app->db->findOne("social/accs", ['_id'=>$item['_id']]);
        if($item && isset($item['access_token'])) {
            $headers = array();
            
            $headers['Referer'] = 'https://api.linkedin.com';
            
            
            //$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
            $headers['Origin'] = 'https://api.linkedin.com';
            $headers['Authorization'] = 'Bearer '.$item['access_token'];
                     
            $result=$this->app->module('socials')->request2('https://api.linkedin.com/v1/people/~',['headers'=>$headers]);
           
            
            if($result['code']==200 ){
                return json_encode(['success'=>1]);
            }
            else{
                return json_encode(['error'=>'invalid access token']);
            }
        }
        else
        {
            return json_encode(['error'=>'not have access token']);
        }
    }

    public function checkverifylivejournal(){

        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.edit'))return false;
        $item = $this->param("item", null);
        $item=$this->app->db->findOne("social/accs", ['_id'=>$item['_id']]);
        if($item) {
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
            $nxsToLJContent = array( "username" => $item['username'], "password" => $item['password'], "event" => '$msg', "subject" => '$msgT', "lineendings" => "unix", "year" => $year, "mon" => $mon, "day" => $day, "hour" => $hour, "min" => $min, "ver" => 2);      
            $nxsToLJContent["usejournal"] = $item['field1'];  //blogID
            if(!$nxsToLJclient->query('LJ.XMLRPC.getfriends', $nxsToLJContent)){
                return json_encode(['error'=>'Something went wrong - '.$nxsToLJclient->getErrorCode().' : '.$nxsToLJclient->getErrorMessage()]);
            }
            $result = $nxsToLJclient->getResponse();
            
            if(isset($result['friends']) && is_array($result['friends'])){
                return json_encode(['success'=>1]);
            }
            else{
                return json_encode(['error'=>'Something went wrong']);
            }
        }
        else
        {
            return json_encode(['error'=>'not have access token']);
        }
    }

    public function checkverifywordpress(){

        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.edit'))return false;
        $item = $this->param("item", null);
        $item=$this->app->db->findOne("social/accs", ['_id'=>$item['_id']]);
        if($item) {
            require_once (COCKPIT_DIR.'/vendor/tumblroauth/xmlrpc-client.php');
                     
            //$result=$this->app->module('socials')->request2('https://api.linkedin.com/v1/people/~',['headers'=>$headers]);
            $nxsToWPclient = new \NXS_XMLRPC_Client($item['field1']); 
            $nxsToWPclient->debug = false;   
            $params = array($item['username'], $item['password']); 
            $status = $nxsToWPclient->query('wp.getUsersBlogs',$params);
            if($status){
                $result=$nxsToWPclient->getResponse();
                if(is_array($result) && isset($result[0]) && isset($result[0]['blogid'])){
                    return json_encode(['success'=>$result]);
                }
                else{
                    return json_encode(['error'=>'Something went wrong']);
                }
            }
          
            else{
                return json_encode(['error'=>'Something went wrong']);
            }
        }
        else
        {
            return json_encode(['error'=>'not have access token']);
        }
    }

    public function checkverifyplurk(){

        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.edit'))return false;
        $item = $this->param("item", null);
        $item=$this->app->db->findOne("social/accs", ['_id'=>$item['_id']]);
        if($item) {
            require_once(COCKPIT_DIR.'/vendor/tumblroauth/plurkOAuth.php');   
            
            $tum_oauth = new \wpPlurkOAuth($item['field2'], $item['field3'],$item['pkAccessTocken'], $item['pkAccessTockenSec']);
            $tum_oauth->cockapp=$this->app;
            $params=[];
            $result = $tum_oauth->makeReq('http://www.plurk.com/APP/Profile/getOwnProfile', $params); 
            
            if(isset($result['plurks_users']) && is_array($result['plurks_users'])){
                return json_encode(['success'=>1]);
            }
            else{
                return json_encode(['error'=>'Something went wrong']);
            }
        }
        else
        {
            return json_encode(['error'=>'not have access token']);
        }
    }

    public function checkverifytwitter(){

        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.edit'))return false;
        $item = $this->param("item", null);
        $item=$this->app->db->findOne("social/accs", ['_id'=>$item['_id']]);
        if($item) {
            require_once(COCKPIT_DIR.'/vendor/tumblroauth/tmhOAuth.php');               
            $hdrsArr['User-Agent']='Mozilla/5.0 (Windows NT 6.1; WOW64; rv:32.0) Gecko/20100101 Firefox/32.0'; 
            $advSet=array('headers'=>$hdrsArr,'httpversion'=>'1.1','timeout'=>45,'sslverify'=>false);
            $tmhOAuth = new \NXS_tmhOAuth(array( 'consumer_key' => $item['field1'], 'consumer_secret' => $item['field2'], 'user_token' =>$item['field3'], 'user_secret' =>$item['field4']));  
             $code=$tmhOAuth->request('GET', $tmhOAuth->url('1.1/statuses/home_timeline'), '',true);
            // print_r($tmhOAuth->response);exit;

            if($code==200){
                return json_encode(['success'=>1]);
            }
            else{
                return json_encode(['error'=>json_decode($tmhOAuth->response['response'])]);
            }
        }
        else
        {
            return json_encode(['error'=>'not have access token']);
        }
    }

    public function checkverifyvk(){

        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.edit'))return false;
        $item = $this->param("item", null);
        $item=$this->app->db->findOne("social/accs", ['_id'=>$item['_id']]);
        if($item) {
             //return json_encode(['error'=>'not have access token']);

            if(isset($item['field2'])){
                $item['vkAppAuthToken'] = trim( $this->app->module("socials")->CutFromTo($item['field2'].'&', 'access_token=','&')); 
                $item['vkAppAuthUser'] = trim( $this->app->module("socials")->CutFromTo($item['field2']."&", 'user_id=','&')); 
                $hdrsArr = array(); 
                 $hdrsArr['Cache-Control']='no-cache'; 
                 $hdrsArr['Connection']='keep-alive'; 
                 $hdrsArr['Referer']=$item['field3'];
                 $hdrsArr['User-Agent']='Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.45 Safari/537.17';
                 
                 $hdrsArr['X-Requested-With']='XMLHttpRequest'; 
                 $hdrsArr['Accept']='text/html, application/xhtml+xml, */*'; 
                 $hdrsArr['DNT']='1';
                 $hdrsArr['Accept-Encoding']='gzip,deflate'; 
                 $hdrsArr['Accept-Language']='en-US,en;q=0.8'; 
                 $hdrsArr['Accept-Charset']='ISO-8859-1,utf-8;q=0.7,*;q=0.3'; 

                $response = $this->app->module("socials")->request2($item['field3'], array( 'method' => 'GET', 'timeout' => 45, 'redirection' => 0,'headers'=>$hdrsArr));
                $contents=$response['body'];
                if (stripos($contents, '"group_id":')!==false) { $item['pgIntID'] =  '-'.$this->app->module("socials")->CutFromTo($contents, '"group_id":', ','); }  
                if (stripos($contents, '"public_id":')!==false) { $item['pgIntID'] =  '-'.$this->app->module("socials")->CutFromTo($contents, '"public_id":', ',');  }  
                if (stripos($contents, '"user_id":')!==false) {   $item['pgIntID'] =  $this->app->module("socials")->CutFromTo($contents, '"user_id":', ','); } 

                $this->app->db->save("social/accs", $item);
                return json_encode(['success'=>1]);
            }
            else{
                return json_encode(['error'=>'not have access token']);
            }
        }
        else
        {
            return json_encode(['error'=>'not have access token']);
        }
    }

    

    public function find(){
        //check acl and role
        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.index'))return '{"error":"not permission"}';
        $page = $this->param("page", 1);
        $search = $this->param("search", null);
        
        $itemperpage=10;
        if(!isset($_SESSION[$this->moduleName]))$_SESSION[$this->moduleName]=[];
        $moduleInfo=$_SESSION[$this->moduleName];
        if(!isset($moduleInfo['currentpage']))$moduleInfo['currentpage']=1;
        if(!isset($moduleInfo['noi']))$moduleInfo['noi']=$itemperpage;
        if(!isset($moduleInfo['filter']))$moduleInfo['filter']=[];
        $moduleInfo['filter']['publish']=1;
        $notgroup=$this->app->db->findOne("addons/groups",['slug'=>'pages']);
        $moduleInfo['filter']['gid']=['$nin'=>[$notgroup['_id'],0]];
        //update info from query
        if($page)$moduleInfo['currentpage']=$page;

        if($search){            
            $moduleInfo['searchterms']=$search;
            $moduleInfo['filter']['slug']=['$regex'=>$this->app->module("posts")->createslug($search),'$options'=>'i'];
            $moduleInfo['currentpage']=1;
        }
        else if($search!==null){
            unset( $moduleInfo['filter']['slug']);
            unset( $moduleInfo['searchterms']);
        }
        $moduleInfo['itemcount']= $this->app->db->count("addons/posts",$moduleInfo['filter']);
        if($moduleInfo['itemcount']==0)return json_encode(['items'=>[],'moduleInfo'=>$moduleInfo]);
        $moduleInfo['totalpage']=ceil($moduleInfo['itemcount']/$moduleInfo['noi']);
        if($moduleInfo['currentpage']>$moduleInfo['totalpage'])$moduleInfo['currentpage']=$moduleInfo['totalpage'];
        $_SESSION[$this->moduleName]=$moduleInfo;

        //get post        
        //$posts = $this->app->db->find("addons/posts",['filter'=>['publish'=>1,'gid'=>['$nin'=>[$notgroup['_id'],0]]],'sort'=>['_id'=>-1],'fields'=>['_id'=>1,'title'=>1,'slug'=>1],'skip'=>($page-1)*$itemperpage,'limit'=>$itemperpage])->toArray();
        $posts = $this->app->db->find("addons/posts",['filter'=>$moduleInfo['filter'],'sort'=>['_id'=>-1],'fields'=>['_id'=>1,'title'=>1,'slug'=>1],'skip'=>($page-1)*$itemperpage,'limit'=>$itemperpage])->toArray();
        //print_r($posts);exit;
        $arrPostId=array_column($posts,'_id');
        //get item count if page =1
        // $pagecount=0;
        // if($page==1){
        //     $itemcount=$this->app->db->find("addons/posts",['filter'=>['publish'=>1,'gid'=>['$nin'=>[$notgroup['_id'],0]]]])->count();
        //     $pagecount=ceil($itemcount/$itemperpage);
        // }

        

        //get socialacc
        $socialaccounts=$this->app->db->find("social/accs",['filter'=>['publish'=>true],'sort'=>['_id'=>-1]])->toArray();
        //print_r($socialaccounts);exit;
        //get saveresult
        $savedResultsData=$this->app->db->find("social/post_results",['filter'=>['pid'=>['$in'=>$arrPostId]],'sort'=>['_id'=>-1]])->toArray();
        //regroup saved data:
        $savedResults=[];
        foreach($savedResultsData as $k=>$v){
            if(!isset( $savedResults[$v['accid']])) $savedResults[$v['accid']]=[];
            $savedResults[$v['accid']][$v['pid']]=$v;
        }
        //print_r($savedResults);exit;
       //get autopost data
        $data=[];
        foreach($socialaccounts as $key=>$acc){
            $socialdata=['name'=>$acc['name'],'accid'=>$acc['_id']];                        
            $data[]=$socialdata;
        }
        return json_encode(['posts'=>$posts,'acc'=>$data,'resultPost'=>$savedResults,'pagecount'=>$moduleInfo['totalpage']]);
    }

    public function find2(){
        //check acl and role
        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.index'))return '{"error":"not permission"}';
        $page = $this->param("page", 1);
        $config=$this->app->module('builds')->getConfig();
        $itemperpage=10;
        $notgroup=$this->app->db->findOne("addons/groups",['slug'=>'pages']);
        //get all post that had posted today
        $filter=[];        
        $arrPostedId=[];
        $postdate=strtotime(date('Y-m-d',time()));        
        $filter['posteddate']=['$gt'=>$postdate];
        $savepost=$this->app->db->find("social/post_results",['filter'=>$filter])->toArray();
        
        if(count($savepost)>0){
            foreach($savepost as $k=>$v)
                $arrPostedId[$v['pid']]=$v['pid'];
        }
        
        //get post
        $postdata=[];
        $posts = $this->app->db->find("addons/posts",['filter'=>['publish'=>1,'gid'=>['$nin'=>[$notgroup['_id'],0]]],'sort'=>['_id'=>-1],'fields'=>['_id'=>1,'title'=>1,'slug'=>1],'skip'=>($page-1)*$itemperpage,'limit'=>$itemperpage])->toArray();
        foreach($posts as $key=>$post){
            $postdata[$post['_id']]=['_id'=>$post['_id'],'title'=>$post['title'],'slug'=>$post['slug']];
        }
        $arrPostId=array_column($posts,'_id');
        //get item count if page =1
        $pagecount=0;
        if($page==1){
            $itemcount=$this->app->db->find("addons/posts",['filter'=>['publish'=>1,'gid'=>['$nin'=>[$notgroup['_id'],0]]]])->count();
            $pagecount=ceil($itemcount/$itemperpage);
        }
        //get socialacc
        //get socialacc
        // $socialaccountspublished=$this->app->db->find("social/accs",['filter'=>['publish'=>true],'sort'=>['_id'=>-1]])->toArray();
        // $socialaccountNot=[];
        // foreach($socialaccountspublished as $k=>$acc){
        //     $socialaccountNot[]=new \MongoId($acc['_id']);
        // }
        
        $socialaccounts=$this->app->db->find("social/accs",['filter'=>['$or'=>[['publish'=>false],['publish'=>['$exists'=>false]]]],'sort'=>['_id'=>-1]])->toArray();
        //get saveresult
        $savedResultsData=$this->app->db->find("social/post_results",['filter'=>['pid'=>['$in'=>$arrPostId]],'sort'=>['_id'=>-1]])->toArray();
        //regroup saved data:
        $savedResults=[];
        foreach($savedResultsData as $k=>$v){
            if(!isset( $savedResults[$v['accid']])) $savedResults[$v['accid']]=[];
            $savedResults[$v['accid']][$v['pid']]=$v;
        }
        //print_r($savedResults);exit;
       //get autopost data
        $data=[];
        foreach($socialaccounts as $key=>$acc){
            $socialdata=['name'=>$acc['name'],'accid'=>$acc['_id']];
            $data[$acc['_id']]=$acc;
            //print_r($data);exit;
        }
        return json_encode([
            'posts'=>$postdata,
            'acc'=>$data,
            'resultPost'=>$savedResults,
            'pagecount'=>$pagecount,
            'arrPostedId'=>$arrPostedId,
            'siteurl'=>$config['siteurl']
            ]);
    }

    private function savePostResult($post,$acc,$result){
        $savedPost=[];
        $savedPost['pid']=$post['_id'];
        $savedPost['accid']=$acc['_id'];
        $savedPost['posteddate']=time();
        if(isset($result['success'])){
            $savedPost['success']=1;
            $savedPost['content']=$result['url'];
        }
        else{
            $savedPost['success']=0;
            $savedPost['content']=$result['error'];
        }
        $this->app->db->save("social/post_results", $savedPost);
    }

    public function savePostResult2(){
        
        $savedPost=[];
        $savedPost=$this->app->db->findOne("social/post_results", ['accid'=>$_POST['accid'],'pid'=>$_POST['pid']]);
        $savedPost['pid']=$_POST['pid'];
        $savedPost['accid']=$_POST['accid'];
        $savedPost['posteddate']=time();        
        $savedPost['success']=1;
        $savedPost['content']=$_POST['content'];        
        $this->app->db->save("social/post_results", $savedPost);
        return json_encode($savedPost);
    }

    public function repost(){
        set_time_limit(36000);
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        $config=$this->module('builds')->getConfig();

        // $this->app->db->remove("social/post_results");
        // $ps=$this->app->db->find("social/post_results")->toArray();
        // print_r($ps);exit;
        //get all post that had posted today
        $accid=$_GET['accid'];
        $pid=$_GET['pid'];
        $acc=$this->app->db->findOne("social/accs",['_id'=>new \MongoId($accid)]);
        $post=$this->app->db->findOne("addons/posts",['_id'=>$pid]);
        $this->app->module('builds')->sendMsg('MESSAGE','Repost to '.$acc['name'].' ... '); 
        $result=$this->postToSocial($post,$acc['name']); 
        //save to db
        $savedPost=$this->app->db->findOne("social/post_results", ['accid'=>$accid,'pid'=>$pid]);
        
        $savedPost['posteddate']=time();
        $savedPost['pid']=$post['_id'];
        $savedPost['accid']=$acc['_id'];
        if(isset($result['success'])){
            $savedPost['success']=1;
            $savedPost['content']=$result['url'];
        }
        else{
            $savedPost['success']=0;
            $savedPost['content']=$result['error'];
        }
        $this->app->db->save("social/post_results", $savedPost);
        $this->app->module('builds')->sendMsg('DATA',json_encode($savedPost)); 
        if(isset($result['success'])){
            $message='<b style="color:blue">Success</b> <a target="_blank" href="'.$result['url'].'" >url</a>';
            $arrPostedId[]=$post['_id'];
        }
        else $message='<b style="color:red">Error :</b> '.$result['error'];
        $this->app->module('builds')->sendMsg('MESSAGE',$message.'<br />'); 
        //break;
        
        $this->app->module('builds')->sendMsg('STOP','<br />Repost done');
        exit;   
    }

    public function autopost(){
        set_time_limit(36000);
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        
        // /$this->app->db->remove("social/post_results");
        // $ps=$this->app->db->find("social/post_results")->toArray();
        // print_r($ps);exit;
        //get all post that had posted today
        $filter=[];        
        $postdate=strtotime(date('Y-m-d',time()-60*60*2));        
        $filter['posteddate']=['$gt'=>$postdate];
        $savepost=$this->app->db->find("social/post_results",['filter'=>$filter])->toArray();
        $arrPostedId=array_column($savepost,'pid');
        
        
        $arrAccIdPosted=array_column($savepost,'accid');      
        //get all post that had already posted by accname
        $curPostedPost=$this->app->db->find("social/post_results");
        //regroup saved data:
        $postedPosts=[];
        foreach($curPostedPost as $k=>$v){
            if(!isset( $postedPosts[$v['accid']])) $postedPosts[$v['accid']]=[];
            $postedPosts[$v['accid']][]=$v['pid'];
        }
        
        //get all socials except already posted today:

        $this->app->module('builds')->sendMsg('MESSAGE','Getting all socials ... ','','',1); 
        //print_r($arrAccIdPosted);
        $socialaccounts=$this->app->db->find("social/accs",['filter'=>['publish'=>true],'sort'=>['_id'=>1]])->toArray();
        //remove already post acc
        //print_r($socialaccounts);
        foreach($socialaccounts as $k=>$v)        {
            //if(in_array($v['_id'], $arrAccIdPosted))unset($socialaccounts[$k]);
        }
        //print_r($socialaccounts);exit;
        $count=count($socialaccounts);
        //get all post that not yet post
        $notgroup=$this->app->db->findOne("addons/groups",['slug'=>'pages']);
        //print_r($groups);exit;
        
        //print_r($posts);exit;    
        
        $this->app->module('builds')->sendMsg('MESSAGE',$count.' founds <br />','0/'.$count); 
        
        
        if($count>0)
        {
            //get post
            $posts = $this->app->db->find("addons/posts",['filter'=>['gid'=>['$ne'=>$notgroup['_id']],'slug'=>['$ne'=>['contact','about']],'publish'=>1],'sort'=>['_id'=>1]])->toArray();
            foreach($socialaccounts as $k=>$social){
                //not post id
                //if($social['name']!='flickr')continue;
                $notPostedId=$arrPostedId;
                if(isset($postedPosts[$social['_id']]))$notPostedId=array_merge($notPostedId,$postedPosts[$social['_id']]);
                
                //remove notposted id
                print_r($notPostedId);
                foreach($posts as $key=>$v)        {
                    if(in_array($v['_id'], $notPostedId))unset($posts[$key]);
                }

                print_r(count($posts));
                if(empty($posts) || count($posts)==0){
                    $this->app->module('builds')->sendMsg('MESSAGE','<b style="color:red">Post not found. Skip</b><br />',($k+1).'/'.$count); 
                    continue;
                }
                $post=array_values($posts)[0];
                $this->app->module('builds')->sendMsg('MESSAGE','Autopost to '.$social['name'].' ...',$k.'/'.$count); 
                $result=$this->postToSocial($post,$social['name']);  
                //save to db
                $this->savePostResult($post,$social,$result); 
                $arrPostedId[]=$post['_id'];
                if(isset($result['success'])){
                    $message='<b style="color:blue">Success</b> <a target="_blank" href="'.$result['url'].'" >url</a>';
                    $arrPostedId[]=$post['_id'];
                }
                else $message='<b style="color:red">Error: </b> '.$result['error'];
                $this->app->module('builds')->sendMsg('MESSAGE',$message.'<br />',($k+1).'/'.$count); 
                //break;
            }
        }
        $this->app->module('builds')->sendMsg('STOP','<br />Autopost done');
        exit; 
    }

    private function postToSocial($post,$accname){
        $config=$this->module('builds')->getConfig();
        $result=[];
        $data=[];
        $data['imgURL']='';
        $data['url'] = $config['siteurl'].$post['slug'].'/';
        $data['title'] = $post['title']; 
        if(isset($post['featureimage'])){
            //get image:
            $image=substr($post['featureimage'],strrpos($post['featureimage'],'/')+1);
            $imagename=substr($image,0,strrpos($image,'.'));
            $imagetype=substr($image,strrpos($image,'.'));
            $data['imgURL'] = $data['url'].$post['slug'].$config['arrSize']['home'][0].$imagetype;
        }
        $data['tags'] = "";
        //print_r($data);exit;
        if(isset($post['tag']))$data['tags'] = $post['tag'];
        if($accname=="facebook"){
            $data['name'] =$post['title']; 
            $data['link'] =$data['url'] ;
            $data['caption'] = $post['description'];
            //$data['message'] = 'Check out my new blog post!';
            $result=$this->app->module('socials')->postFB($data);  
            
        }
        else if($accname=="tumblr"){
           
            
            $data['type'] = "text";
            $data['body'] = "<a href='".$data['url']."' ><img style='float:left;margin-right:10px;margin-bottom:10px;' src='".$data['imgURL']."' /></a> ".$post['description']." <br /> <br /> Xem thêm ".$data['url'];
            $result=$this->app->module('socials')->postTumblr($data);
             

        }
        
        else if($accname=="diigo"){
                           
            $data['description'] = $post['description'];
            $result=$this->app->module('socials')->postDiigo($data);
              
        }
        else if($accname=="delicious"){
                           
            $data['note'] = $data['title']; 
            $data['description'] = $post['description']; 
            $result=$this->app->module('socials')->postDelicious($data);

            
        }
        else if($accname=="kippt"){
                            
            $data['message'] = $post['description']; 
            $result=$this->app->module('socials')->postKippt($data);
            
        }
        else if($accname=="flickr"){
                            
            $data['description'] = $post['description'].". Chi tiết: ".$data['url'];                 
            $result=$this->app->module('socials')->postFlickr($data);
            
        }
        else if($accname=="linkedin"){
           
            $result=$this->app->module('socials')->postLinkedin($data);
        }
        else if($accname=="livejournal"){
            
            $data['message'] = "<a href='".$data['url']."'><img style='float:left;margin-right:10px;margin-bottom:10px;' src='".$data['imgURL']."' /></a> ".$post['description']." <br /> xem thêm <a href='".$data['url']."'>tại đây</a>";
            $result=$this->app->module('socials')->postLivejournal($data);
            
        }
         else if($accname=="plurk"){
            
            $data['message'] = $data['imgURL']." ".$data['url']." ".$post['description'];           
            $result=$this->app->module('socials')->postPlurk($data);
            
        }
        else if($accname=="scoopit"){
                           
            $data['content'] = $post['description'].' '.$data['url'];
            $result=$this->app->module('socials')->postScoopit($data);
            
        }
        else if($accname=="twitter"){
            
            $data['message'] = $data['title']." ".$data['url']; 
            
            $result=$this->app->module('socials')->postTwitter($data);
            
            
        }
        else if($accname=="vk"){
            
           
            $data['message'] = $data['title']." ".$data['url']; 
           
            $result=$this->app->module('socials')->postVk($data);
            
        }
        else if($accname=="wordpress"){
            
            //$data['url'] = "http://nhatkychame.vn/danh-ba-cac-bac-si-nhi-gioi-tai-tp-ho-chi-minh/";                
            
            $data['description'] = $post['description']." ".$data['url']; 
            
            
            $result=$this->app->module('socials')->postWordpress($data);
             
        }
        else if($accname=="blogger"){            
            $data['description'] = "<a href='".$data['url']."'><img style='float:left;margin-right:10px;margin-bottom:10px;' src='".$data['imgURL']."' /></a> ".$post['description']." <br /> xem thêm <a href='".$data['url']."'>tại đây</a>";            
            $result=$this->app->module('socials')->postBlogger($data);             
        }

            
        $result=json_decode($result,true);
        return $result;
    }

    public function saveaccount(){
        //check acl and role
        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.edit'))return false;
        $item = $this->param("item", null);

        if($item) {

            $item["modified"] = time();
            $item["uid"]     = $this->user["_id"];

            if(!isset($item["_id"])){
                $item["created"] = $item["modified"];
            }
           
           $item=$this->app->module("socials")->fixBigNumber($item,1);

            $this->app->db->save("social/accs", $item);

        }

        return $item ? json_encode($item) : '{}';
    }

    public function accountfindOne(){
        //check acl and role
        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.index'))return false;        
        $item = $this->app->db->findOne("social/accs", $this->param("filter", []));
        $item=$this->app->module("socials")->fixBigNumber($item,0);
        //print_r(bigintval($doc['field1']));exit;
        return $item ? json_encode($item) : '{}';
    }
    
    public function removeaccount() {
        //check acl and role
        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.edit'))return false;
        $id = $this->param("id", null);
        if($id) {
            $this->app->db->remove("social/accs", ["_id" => $id]);
        }

        return $id ? '{"success":true}' : '{"success":false}';
    }

    public function findaccount(){

        $datas = $this->app->db->find("social/accs");
       
        return json_encode($datas->toArray());
    }
}
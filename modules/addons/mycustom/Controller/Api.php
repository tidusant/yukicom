<?php

namespace mycustom\Controller;

class Api extends \Cockpit\Controller {
    private $moduleName='';
     private $sitebaseurl='http://localhost:8080';
    private $siteurl=COCKPIT_BASE_URL.'/output/';
    public function __construct($app) {
        parent::__construct($app);
        //duy.ha: get path to detect module name
        $this->siteurl=$this->sitebaseurl.$this->siteurl;
        //duy.ha: get path to detect module name
        $pathName=explode("\\",__DIR__);
        if(count($pathName)<=1)$pathName=explode('/',__DIR__);
        $moduleName=$pathName[count($pathName)-2];
        $this->moduleName=$moduleName; 

    }


   
    public function submit(){
        //check acl and role        
        $item = $this->param("item", null);
        if(!$this->app->module("auth")->hasaccess($this->moduleName,'manage.edit')) return '{"error":"permission denied"}';
        $item='./modules/addons/common/taskman.php';
        
        if(file_exists($item)) {
            include $item;
            //create connection:
            $link = mysql_connect($dpimport['host'], $dpimport['login'], $dpimport['password']);
            if (!$link) {
                die('Could not connect: ' . mysql_error());
            }
            mysql_set_charset('utf8',$link);
            //select db:            
            $db_selected = mysql_select_db($dpimport['database'], $link);
            if (!$db_selected) {
                die ('Can\'t select db '.$dpimport['database'].' : ' . mysql_error());
            }
            //=============begin convert
            $this->app->db->remove('addons/posts');
            $this->app->db->remove('addons/groups');
            
            $result = mysql_query("SELECT * FROM wp_posts where post_type='post' and post_status='publish'");
             while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                $convertdata=[];
                //default data
                $convertdata['uid']=$this->user['_id'];
                $convertdata['publish']=1;
                $convertdata['home']=0;
                $convertdata['isdraft']=0;
                //convert data
                $convertdata['title']=$row['post_title'];
                $convertdata['slug']=$row['post_name'];
                $convertdata['created']=strtotime($row['post_date']);
                $convertdata['post_date']=strtotime($row['post_date']);
                $convertdata['publishdate']=date('Y-m-d',$convertdata['post_date']);
                $convertdata['publishtime']=date('h:i',$convertdata['post_date']);
                $convertdata['modified']=strtotime($row['post_modified']);
                $convertdata['description']=$row['post_excerpt'];
                $convertdata['content']=$row['post_content'];
                $convertdata['content']=str_replace('http://localhost:8080/dayxam/wp-content/uploads/', $this->sitebaseurl.'/cockcms/uploaded/',$convertdata['content']);
                $convertdata['content']=str_replace('http://dayxam.com/wp-content/uploads/', $this->sitebaseurl.'/cockcms/uploaded/',$convertdata['content']);
                //get image:
                $sql="SELECT * FROM wp_posts where ID =(select meta_value from wp_postmeta where meta_key='_thumbnail_id' and post_id=".$row['ID'].")";

                $imageresult = mysql_query($sql);
                if ($imagerow = mysql_fetch_array($imageresult, MYSQL_ASSOC)) {
                    $convertdata['featureimage']='site:cockcms/uploaded/'.str_replace('http://localhost:8080/dayxam/wp-content/uploads/', '',$imagerow['guid']);
                    $convertdata['featureimage']=str_replace('http://dayxam.com/wp-content/uploads/', '',$convertdata['featureimage']);
                }
                //get group:
                $sql="SELECT * FROM wp_terms where term_id =(select tr.term_taxonomy_id from wp_term_relationships tr inner join wp_term_taxonomy tt on tr.term_taxonomy_id=tt.term_taxonomy_id where tr.object_id=".$row['ID']." and tt.taxonomy='category')";
                
                $groupresult = mysql_query($sql);
                if ($grouprow = mysql_fetch_array($groupresult, MYSQL_ASSOC)) {
                    //get group
                    
                    $group=$this->app->db->findOne('addons/groups',['slug'=>$grouprow['slug']]);

                    if(!$group){
                        //insert group
                        $group['name']=$grouprow['name'];
                        $group['slug']=$grouprow['slug'];             
                        $group['uid']=$this->user['_id'];
                        $group['modified']=time();
                        $group['created']=$group['modified'];

                        $this->app->db->save('addons/groups',$group);
                        
                    }
                    $convertdata['gid']=$group['_id'];
                }

                
                $this->app->db->save('addons/posts',$convertdata);
                
             }
            //print_r( $this->app->db->find('addons/posts')->toArray());
            mysql_close($link);
           
            
        }

        return $item ? json_encode($item) : '{}';
    }

    public function convertToMongo(){
        set_time_limit(100000);   
        //ini_set('memory_limit', '256M'); 
        ini_set('memory_limit', '512M'); 
        //check acl and role        
        $item = $this->param("item", null);
        if(!$this->app->module("auth")->hasaccess($this->moduleName,'manage.edit')) return '{"error":"permission denied"}';
        
        //connect to mongo        
        $option=Array ( 'db' => 'hagt_test', 'username' => 'hagt_test', 'password' => 'hagt123456@' );
        $connect = new \MongoClient('mongodb://duyhf.com:27017',$option);
        $db = $connect->selectDB($option['db']);

 
       


        //convert table users
        $accIds=[];
        $items=$this->app->db->find('cockpit/accounts');
        $collection = $db->selectCollection("cockpit_accounts");
        foreach ($items as $key => $item) {
            $document = [];            
            foreach ($item as $k => $v) {
                if($k!='_id'){
                    $document[$k]=$v;
                }
            }
            $collection->insert($document);
            $accIds[$item['_id']]=(string)$document['_id'];
            
        }

        //convert table group
        $groupIds=[];
        $items=$this->app->db->find('addons/groups');
        $collection = $db->selectCollection("addons_groups");
        foreach ($items as $key => $item) {
            $document = [];
            
            foreach ($item as $k => $v) {
                if($k!='_id'){                   
                    $document[$k]=$v;
                   
                }
            }
            $collection->insert($document);
            $groupIds[$item['_id']]=(string)$document['_id'];
        }

        //convert table post
        $postIds=[];
        $items=$this->app->db->find('addons/posts');
        $collection = $db->selectCollection("addons_posts");
        foreach ($items as $key => $item) {
            $document = [];
            
            foreach ($item as $k => $v) {
                if($k!='_id'){
                    if($k=='uid'){
                        $document[$k]=$accIds[$v];
                    }
                    else if($k=='gid'){
                        
                        $document[$k]=isset($groupIds[$v])?$groupIds[$v]:$groupIds[array_keys($groupIds)[0]];
                    }
                    else{
                        $document[$k]=$v;
                    }
                }
            }
            $collection->insert($document);
            $postIds[$item['_id']]=(string)$document['_id'];
        }

        //convert table addon host
        $postIds=[];
        $items=$this->app->db->find('addons/hosts');
        $collection = $db->selectCollection("addons_hosts");
        foreach ($items as $key => $item) {
            $document = [];
            
            foreach ($item as $k => $v) {
                if($k!='_id'){
                   
                        $document[$k]=$v;
                   
                }
            }
            $collection->insert($document);
            
        }
       

        //convert table social
        $socialIds=[];
        $items=$this->app->db->find('social/accs');
        $collection = $db->selectCollection("socail_accs");
        foreach ($items as $key => $item) {
            $document = [];
            
            foreach ($item as $k => $v) {
                if($k!='_id'){
                   $document[$k]=$v;                   
                }
            }
            $collection->insert($document);
            $socialIds[$item['_id']]=(string)$document['_id'];
        }

        //convert table post_results
        
        $items=$this->app->db->find('social/post_results');
        $collection = $db->selectCollection("socail_post_results");
        foreach ($items as $key => $item) {
            $document = [];
            
            foreach ($item as $k => $v) {
                if($k!='_id'){
                   $document[$k]=$v;                   
                }
            }
            $collection->insert($document);
            
        }

         //convert table gacode
        
        $items=$this->app->db->find('ads/gacodes');
        $collection = $db->selectCollection("ads_gacodes");
        foreach ($items as $key => $item) {
            $document = [];
            
            foreach ($item as $k => $v) {
                if($k!='_id'){
                   $document[$k]=$v;                   
                }
            }
            $collection->insert($document);
            
        }
        

        return $item ? json_encode($item) : '{}';
    }


    
}
<?php

namespace gas\Controller;

class Api extends \Cockpit\Controller {
    private $moduleName='';
     private $sitebaseurl='http://localhost';
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
        $item='./modules/addons/sql2cps/taskman.php';
        
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
                $convertdata['content']=str_replace('http://localhost/dayxam/wp-content/uploads/', $this->sitebaseurl.'/cockcms/uploaded/',$convertdata['content']);
                $convertdata['content']=str_replace('http://dayxam.com/wp-content/uploads/', $this->sitebaseurl.'/cockcms/uploaded/',$convertdata['content']);
                //get image:
                $sql="SELECT * FROM wp_posts where ID =(select meta_value from wp_postmeta where meta_key='_thumbnail_id' and post_id=".$row['ID'].")";

                $imageresult = mysql_query($sql);
                if ($imagerow = mysql_fetch_array($imageresult, MYSQL_ASSOC)) {
                    $convertdata['featureimage']='site:cockcms/uploaded/'.str_replace('http://localhost/dayxam/wp-content/uploads/', '',$imagerow['guid']);
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
           
           

            $this->app->db->save("ads/gacodes", $item);

        }

        return $item ? json_encode($item) : '{}';
    }

    public function accountfindOne(){
        //check acl and role
        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.index'))return false;        
        $item = $this->app->db->findOne("ads/gacodes", $this->param("filter", []));
        
        //print_r(bigintval($doc['field1']));exit;
        return $item ? json_encode($item) : '{}';
    }
    
    public function removeaccount() {
        //check acl and role
        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.edit'))return false;
        $id = $this->param("id", null);
        if($id) {
            $this->app->db->remove("ads/gacodes", ["_id" => $id]);
        }

        return $id ? '{"success":true}' : '{"success":false}';
    }

    public function findaccount(){

        $datas = $this->app->db->find("ads/gacodes",['sort'=>['_id'=>-1]]);
       
        return json_encode($datas->toArray());
    }

    
}
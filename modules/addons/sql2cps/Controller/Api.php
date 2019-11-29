<?php

namespace sql2cps\Controller;

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

            //insert group
            $group['name']='Series';
            $group['slug']='series';
            $group['lang']='vi';
            $group['uid']=$this->user['_id'];
            $group['modified']=time();            

            $this->app->db->save('addons/groups',$group);
            
            $result = mysql_query("SELECT * FROM wp_posts where post_type='post' and post_status='publish'");
            //print_r($result);exit;
             while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                $convertdata=[];
                //default data
                $convertdata['uid']=$this->user['_id'];
                $convertdata['publish']=1;
                $convertdata['home']=0;
                $convertdata['isdraft']=0;
                $convertdata['lang']='vi';
                //convert data
                $convertdata['title']=$row['post_title'];
                $convertdata['slug']=$row['post_title'];
                $convertdata['created']=strtotime($row['post_date']);
                $convertdata['post_date']=strtotime($row['post_date']);
                $convertdata['publishdate']=date('Y-m-d',$convertdata['post_date']);
                $convertdata['publishtime']=date('h:i',$convertdata['post_date']);
                $convertdata['modified']=strtotime($row['post_modified']);
                $convertdata['description']=$row['post_excerpt'];
                $content='<table width="100%" border="0" cellspacing="0" cellpadding="0"> <tbody> <tr><th>Tag ID:</th> <td><h2 style="font-size:1em">{{seri}}</h2></td> </tr> <tr><th width="100px" valign="top">Type:</th> <td>Arowana<br>Yuki super king red</td> </tr> <tr><th>Location:</th> <td>West Kalimantan INDONESIA</td> </tr> <tr><th>Distributor:</th> <td>Surabaya Red</td> </tr> <tr><th>Day of:</th> <td>{{dayof}}</td> </tr> <tr><th>Place to Sold:</th> <td>Huy Thao Arowana (Vietnam)</td> </tr> </tbody> </table>';
               

                //get series                
                $sql="select meta_value from wp_postmeta where meta_key='seri' and post_id=".$row['ID']."";
                $rs = mysql_query($sql);
                if ($rsw = mysql_fetch_array($rs, MYSQL_ASSOC)) {                    
                    $content=str_replace('{{seri}}', $rsw['meta_value'], $content);
                }
                //get dayof
                $sql="select meta_value from wp_postmeta where meta_key='dayof' and post_id=".$row['ID']."";
                $rs = mysql_query($sql);
                if ($rsw = mysql_fetch_array($rs, MYSQL_ASSOC)) {                    
                    $content=str_replace('{{dayof}}', $rsw['meta_value'], $content);
                }




                //get image:
                $sql="SELECT * FROM wp_posts where ID =(select meta_value from wp_postmeta where meta_key='_thumbnail_id' and post_id=".$row['ID'].")";

                $imageresult = mysql_query($sql);
                if ($imagerow = mysql_fetch_array($imageresult, MYSQL_ASSOC)) {
                    $convertdata['featureimage']='site:nkcm2/uploaded/'.str_replace('http://localhost:8080/nkcm/wp-content/uploads/', '',$imagerow['guid']);
                    $convertdata['featureimage']=str_replace('http://nhatkychame.vn/wp-content/uploads/', '',$convertdata['featureimage']);
                }
                $convertdata['gid']=$group['_id'];
                $convertdata['content']=$content;
                //print_r($convertdata);exit;
                
                $this->app->db->save('addons/posts',$convertdata);
                
             }
            //print_r( $this->app->db->find('addons/posts')->toArray());
            mysql_close($link);
           
            
        }

        return $item ? json_encode($item) : '{}';
    }

    public function submit2(){
        set_time_limit(36000); 
        $mongo = new \MongoClient("mongodb://104.155.216.230",["db" => "nkcm","username" => "nkcm", "password" => "nkcm141421"]);
        $db=$mongo->nkcm;




        

        
        //import accounts
        $userids=[];
        $collection=$db->cockpit_accounts;
        $collection->remove();
        $accounts=$this->app->db->find('cockpit/accounts');
        
        foreach($accounts as $acc){
            $data=$acc;
            unset($data['_id']);
            unset($data['assigned']);
            $collection->insert($data);            
            $userids[$acc['_id']]= (string)$data['_id'];
        }


        //import group
        $groupids=[];
        $collection=$db->addons_groups;
        $collection->remove();
        $groups=$this->app->db->find('addons/groups');
        
        foreach($groups as $group){
            $data=$group;
            unset($data['_id']);
            $collection->insert($data);            
            $groupids[$group['_id']]= (string)$data['_id'];
        }

        //print_r($groupids);exit;

        //import post
        $postids=[];
        $collection=$db->addons_posts;
        $collection->remove();
        $posts=$this->app->db->find('addons/posts');
        foreach($posts as $post){            
            $data=$post;      
              
            
            unset($data['_id']);
            $data['gid']=isset($groupids[$post['gid']])?$groupids[$post['gid']]:0;
            $data['uid']=isset($post['uid'])?$userids[$post['uid']]:$userids[array_keys($userids)[0]];
            if(isset($data['group']))   {
                $data['group']['uid']=isset($post['group']['uid'])?$userids[$post['group']['uid']]:$userids[array_keys($userids)[0]];
                $data['group']['gid']=isset($groupids[$post['group']['gid']])?$groupids[$post['group']['gid']]:0;
                $data['group']['_id']=isset($groupids[$post['group']['_id']])?$groupids[$post['group']['_id']]:0;
            }
            $collection->insert($data);
            $postids[$post['_id']]= (string)$data['_id'];                        
            
        }

        //import social
        $accids=[];
        $collection=$db->social_accs;
        $collection->remove();
        $items=$this->app->db->find('social/accs');
        
        foreach($items as $item){
            $data=$item;  
            unset($data['_id']);
            $data['uid']=isset($item['uid'])?$userids[$item['uid']]:$userids[array_keys($userids)[0]];               
            $collection->insert($data);  
            $accids[$item['_id']]= (string)$data['_id'];        
        }

        //import post result
        
        $collection=$db->social_post_results;
        $collection->remove();
        $items=$this->app->db->find('social/post_results');
        //print('<pre>');
        //print_r($items[0]);exit;
        foreach($items as $item){
            $data=$item;  
            unset($data['_id']);
            $data['pid']=$postids[$item['pid']];
            $data['accid']=$accids[$item['accid']];
            $collection->insert($data);          
        }

        //import config
        
        $collection=$db->addons_hosts;
        $collection->remove();
        $hosts=$this->app->db->find('addons/hosts');
        
        foreach($hosts as $config){
            $data=$config;  
            unset($data['_id']);
            unset($data['tid']);
            unset($data['uid']);
            unset($data['pid']);
            unset($data['name']);
            unset($data['mapto']);
            unset($data['mapfrom']);
            $collection->insert($data);          
        }

        return 1;
    }

    public function submit3(){
        set_time_limit(36000); 
        $mongo = new \MongoClient("mongodb://localhost",["db" => "nkcm","username" => "nkcm", "password" => "nkcm141421"]);
        $db=$mongo->nkcm;




        

        //import post
        $postids=[];
        $collection=$db->addons_posts;
        $posts=$this->app->db->find('addons/posts');
        foreach($posts as $post){            
            $data=$post;      
            // if(strpos($post['content'],'nhatkychame.vn')){
            //     print_r($data['content']);
            //     exit;
            // }
            $data['_id']=new \MongoId($data['_id']);
            
            $data['content']=str_replace('Nhatkychame.com', 'nhatkychame.vn', $post['content']);            
            $collection->update(['_id'=>$data['_id']],$data);           
            
        }


        return 1;
    }
}
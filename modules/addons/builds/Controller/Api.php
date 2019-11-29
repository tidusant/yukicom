<?php

namespace builds\Controller;

class Api extends \Cockpit\Controller {
    private $moduleName='builds';
    private $sitebaseurl='';
    

    public function __construct($app) {
        parent::__construct($app);
        //duy.ha: get path to detect module name
        $this->sitebaseurl=$this->module("builds")->sitebaseurl();
        
        $pathName=explode("\\",__DIR__);
        if(count($pathName)<=1)$pathName=explode('/',__DIR__);
        $moduleName=$pathName[count($pathName)-2];
        $this->moduleName=$moduleName;        
    }

    public function saveHost(){        
        //read data to build output:
        $host = $this->param("host", null);

        if($host){
            $host['FBId']='fb'.$host['FBId'];
            if(!isset($host['custom_async_script']))$host['custom_async_script']='';
            if(!isset($host['custom_event']) || empty($host['custom_event']))$host['custom_event']='function custom_event(){}';
            if(!isset($host['defaultlang'])){
                if(!isset($host['langs']) || count($host['langs'])==0)$host['defaultlang']='';
                else $host['defaultlang']=$host['langs'][0];
            }
            //unset($host['custome_async_script']);
            $this->app->db->save('addons/hosts',$host);
        }
        return $host ? json_encode($host) : '{}';
    }

    public function getHost(){        
        //read data to build output:
        $data=[];
        $host=$this->app->db->findOne('addons/hosts');
        $host['FBId']=str_replace('fb', '', $host['FBId']);
        $data['host']=$host;
        //get all country flags:
        $flags=\MyCommon\Common::listCountryFlag();
        $listCountry=\MyCommon\Common::listCountry();
        $data['listCountry']=$listCountry;
        $data['listCountryFlag']=$flags;
        return $data ? json_encode($data) : '{}';
    }

    public function buildSiteMap(){
        $config=$this->module('builds')->getConfig();
        
        $uploadFiles=[];
        //get all group
        $siteurl=$config['siteurl'];
        $time=time();
        $xml='<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
    xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<url>
<loc>'.$siteurl.'</loc>
<lastmod>'.date('Y-m-d',$time).'T'.date('H:s:i',$time).'+07:00</lastmod>
<changefreq>weekly</changefreq>
<priority>1</priority>
</url>';
    
        //get size:
        $arrSize=$config['arrSize']['home'];
        $size=array_values($arrSize)[1];
        //get all post
        $curP=$this->app->db->find('addons/posts',['filter'=>['publish'=>1]]);
        foreach ($curP as $key => $post) {
            if(!isset($post['modified']))$post['modified']=time();
            $postxml='<url>
<loc>'.$siteurl.$post['slug'].'/</loc>
<lastmod>'.date('Y-m-d',$post['modified']).'T'.date('H:s:i',$post['modified']).'+07:00</lastmod>
<changefreq>weekly</changefreq>
<priority>0.6</priority>';
            //get feature image:
            if(isset($post['featureimage'])){
                $feature_image=$this->module("builds")->sitebaseurl().str_replace("site:",$this->app->pathToUrl('site:'),$post['featureimage']);
                $arrfilename=explode('.',substr($feature_image,strrpos($feature_image, '/')+1));
                $name=$post['slug'];              
                $type='.'.$arrfilename[1];  
                $postxml.='<image:image>
        <image:loc>'.$siteurl.$post['slug'].'/'.$name.$size.$type.'</image:loc>
        <image:caption>
            <![CDATA['.$name.']]>
        </image:caption>
    </image:image>';
            }
            //get image in content
            preg_match_all('/<img.*?(src=["\'](.*?)["\']).*?>/is', $post['content'], $matches);
            if(isset($matches[2])){
                foreach($matches[2] as $k=>$img){
                    //sync with editor:
                    //remove -300x200 size
                    $img=preg_replace('/-(\d+)x(\d+)\.(\w+)$/i','.${3}',$img);
                    $arrfilename=explode('.',substr($img,strrpos($img, '/')+1));
                    $name=$post['slug'].($k+1);              
                    $type='.'.$arrfilename[1];  
                    $postxml.='<image:image>
    <image:loc>'.$siteurl.$post['slug'].'/'.urlencode($name).$size.$type.'</image:loc>
    <image:caption>
        <![CDATA['.$name.']]>
    </image:caption>
</image:image>';
                }
            }

            $postxml.='</url>';
            $xml.=$postxml;
        }

        //sitemapfor cat:
        $curG=$this->app->db->find('addons/groups');
        foreach ($curG as $key => $group) {
            if(!isset($group['modified']))$group['modified']=time();
            $catxml='<url>
<loc>'.$siteurl.$group['slug'].'/</loc>
<lastmod>'.date('Y-m-d',$group['modified']).'T'.date('H:s:i',$group['modified']).'+07:00</lastmod>
<changefreq>weekly</changefreq>
<priority>0.6</priority></url>';
            $xml.=$catxml;
        }

        $xml.='</urlset>';
        new \Minify\App();
        $xml=\Minify\lib\Minify\HTML::minify($xml);
        $xml=str_replace("> <", "><", $xml);
        //print_r($xml);
        $filename=base64_encode(str_replace('http://', '', $config['siteurl']));
        $filename=substr($filename, 0,10);
        file_put_contents('./output/'.$filename.'.xml', $xml);
        $this->module("builds")->gzCompressFile('./output/'.$filename.'.xml');
        $uploadFiles[]='./'.$filename.'.xml.gz';

        if(!$config['dev'])$this->module("builds")->pushfile($uploadFiles,$config);
        return true;
    }

    public function build(){
        set_time_limit(36000);
        //read data to build output:
        $result=true;        
        $isbuilddata = $this->app->param("isbuilddata", null);                
        $module=$this->param("module", null);
        $config=$this->module('builds')->getConfig();

        //print_r($module);exit;
        $html=$this->module("builds")->buildHtml();
        
        if($module=='buildscript'){
            $result=$result && $this->module("mycustom")->buildscript();
        }
        else if($module=='buildhome'){
            // $imgstr = $this->app->module("builds")->getImageStr('http://localhost/nkcm2/output/h/hinh-anh-tung-giai-doan-phat-trien-cua-thai-nhi/qua-trinh-tinh-trung-thu-thai-voi-trung560.jpg');            
            // $resizeimgstr = $this->app->module("builds")->getImageResize2($imgstr,10,'test','jpg');                          
            //print_r(COCKPIT_DIR.'/uploaded/2014/09/meo-hay-giup-cham-soc-tre-so-sinh560.png');exit;
            //$imgstr = $this->app->module("builds")->PNGOpti(COCKPIT_DIR.'/uploaded/2014/09/meo-hay-giup-cham-soc-tre-so-sinh-720x340.png');
            //file_put_contents(COCKPIT_DIR.'/uploaded/test.png', $imgstr);
            //$newimgstr=$this->app->module("builds")->getImageStr('http://localhost/nkcm2/uploaded/test.png'); 
            //$newimgstr=$this->app->module("builds")->getImageStr('http://localhost/nkcm2/uploaded/2014/09/muoi-hot-gung-giup-eo-thon-sau-sinh.jpg'); 
            //print_r($newimgstr);exit;
            
            //print_r($newimgstr);exit;

            $result=$this->app->module("mycustom")->buildHome($html);
        }
        else if($module=='buildmenu'){
            $result=$result && $this->module("builds")->buildMenu();
        }
        else if($module=='buildgroup'){
            $cats=$this->app->db->find('addons/groups')->toArray();
            $result=$result && $this->module("builds")->buildCat($cats,$html,$config);  
        }
        else if($module=='buildsitemap'){
            $result=$result && $this->buildSiteMap();  
        }
        else if($module=='posts'){
            $result=$result && $this->module("posts")->build($html,$config);
        }
        else if($module=='createamplink'){
            $posts=$this->app->db->find('addons/posts',['filter'=>['publish'=>1]]);
            foreach ($posts as $key => $post) {
                if(!isset($post['amp'])||empty($post['amp'])){
                    $post["amp"]=$this->app->module('builds')->create_random_string(5);
                    $this->app->db->save("addons/posts", $post);
                }
            }
            $result=1;
        }
        
        
            
        return $result;
        //return json_encode($datas->toArray());
    }

    public function stopbuild(){
        $buildname = $this->app->param("buildname", null); 
        if(empty($buildname))return 0;
        $procStatus=$this->app->db->findOne('status/buildprocess',['procName'=>$buildname]);
        if(empty($procStatus)){
            return 1;
        }
        $procStatus['isstopbyuser']=1;
        $procStatus['isBuilding']=0;
        $this->app->db->update("status/buildprocess",['_id'=>$procStatus['_id']],$procStatus);
        session_write_close(); 
        return 1;
    }
    public function resetbuild(){
        $buildname = $this->app->param("buildname", null);    
        //$this->app->db->remove('status/buildprocess');
        if(empty($buildname))return 0;
        $procStatus=$this->app->db->findOne('status/buildprocess',['procName'=>$buildname]);  
        if($procStatus){     
            $procStatus['isstopbyuser']=0;
            $procStatus['currentBuildAt']=0;
            $procStatus['isBuilding']=0;
            $this->app->db->update("status/buildprocess",['_id'=>$procStatus['_id']],$procStatus);
        }
        session_write_close();
        return 1;
    }
    public function buildpost(){
        
        set_time_limit(36000);
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        $procStatus=[];
        $buildname = $this->app->param("buildname", null);    
        if(empty($buildname)){
            $this->app->module('builds')->sendMsg('STOP','Unknown buildname ','','red'); 
            exit;  
        }
        //check current build status:
        $procStatus=$this->app->db->findOne('status/buildprocess',['procName'=>$buildname]);      
            
        if(empty($procStatus)){
            $procStatus=['procName'=>$buildname,'isstopbyuser'=>0,'currentBuildAt'=>0,'isBuilding'=>0];
            $this->app->db->save("status/buildprocess",$procStatus);
        }
        else{
            //check is running status:
            if($procStatus['isBuilding']==1){
                $this->app->module('builds')->sendMsg('STOP','Another building is running at '.($procStatus['currentBuildAt']+1).'. Cannot start.','','red'); 
                exit;    
            }
        }
        $i=0;

        if($procStatus['currentBuildAt']>0){
            $i=$procStatus['currentBuildAt'];
            $this->app->module('builds')->sendMsg('MESSAGE','Restart from last build item: '.$i,'','blue');     
        }

        //read data to build output:
        $result=true;        
       
        $config=$this->module('builds')->getConfig();
        //print_r($module);exit;
        $html=$this->module("builds")->buildHtml();        
        $ampHtml=$this->module("builds")->buildHtml(1);
        if($buildname=='Build Post'){
            $this->buildPostStatus($html,$ampHtml,$config,$i);
        }
        else if($buildname=='Build Post Overwrite'){
            $this->buildPostStatus($html,$ampHtml,$config,$i,1);
        }
        else if($buildname=='Build Group'){
            $this->buildCatStatus($html,$config,$i);
        }

        else if($buildname=='Build Group Overwrite'){
            $this->buildCatStatus($html,$config,$i,1);
        }

        else if($buildname=='Build Plugin'){
            $this->buildPluginStatus($html,$config,$i);
        }
        else if($buildname=='Build Plugin Overwrite'){
            $this->buildPluginStatus($html,$config,$i,1);
        }
        
        

        
        
       //reset status
        @session_start();
        $procStatus['currentBuildAt']=0;
        $procStatus['isstopbyuser']=0;
        $procStatus['isBuilding']=0;
        $this->app->db->update("status/buildprocess",['_id'=>$procStatus['_id']],$procStatus);
        session_write_close(); 
        $this->app->module('builds')->sendMsg('STOP', '<b>All done</b>','','blue');
        exit;
        
            
        //return json_encode($datas->toArray());
    }

    public function buildPostStatus($html,$ampHtml,$config,$i=0,$overwrite=0){
        $this->app->module('builds')->sendMsg('MESSAGE','Getting all post...','','',1);    
        
        $groupsById=[];
        $curG=$this->app->db->find('addons/groups');        
        foreach ($curG as $key => $group) {
            $groupsById[$group['_id']]=$group;
        }
        

        //get all post
        $posts=$this->app->db->find('addons/posts',['filter'=>['publish'=>1,'gid'=>['$ne'=>0]],'sort'=>['_id'=>1],'skip'=>$i]);
        
        $count=count($posts)+$i;
        $this->app->module('builds')->sendMsg('MESSAGE',' Done','','blue');
        $this->app->module('builds')->sendMsg('MESSAGE',' Post count: <b style="color:#f00">'.$count.'</b>');
        //exit;
        if($posts){
            foreach($posts as $k=>$post){  
                //save status:
                //save status:
                @session_start();
                $procStatus=$this->app->db->findOne('status/buildprocess',['procName'=>'Build Post']);                
                
                if($procStatus['isstopbyuser']==1){
                    $procStatus['isstopbyuser']=0;
                    $procStatus['isBuilding']=0;
                    $this->app->db->update("status/buildprocess",['_id'=>$procStatus['_id']],$procStatus);
                    $this->app->module('builds')->sendMsg('STOP','Stopped by user!',($k+$i).'/'.$count,'red');                    
                    exit;
                } 
                $procStatus['isBuilding']=1;               
                $procStatus['currentBuildAt']=$k+$i;
                $this->app->db->update("status/buildprocess",['_id'=>$procStatus['_id']],$procStatus);
                session_write_close(); 

                $this->app->module('builds')->sendMsg('MESSAGE','Building <b>'.$post['title'].'</b>...',$k+$i.'/'.$count,'',1);
                $post['gname']= isset($post['gid']) && isset($groupsById[$post['gid']])?$groupsById[$post['gid']]['slug']:'';                
                $result=$this->app->module("mycustom")->builddatabyposts($html,$ampHtml,[$post['_id']],$overwrite); 
                $this->app->module('builds')->sendMsg('MESSAGE',' <b>Done</b>',($k+$i+1).'/'.$count);
                  // print_r($post['slug']);        
                //exit;
            }
        }
    }

     public function buildCatStatus($html,$config,$i=0,$overwrite=0){
        if($config['noitempercat']==0){
            $this->app->module('builds')->sendMsg('MESSAGE','Building Group is disable','','',1);  
            $this->app->module('builds')->sendMsg('MESSAGE','<b> done.</b>');
            return 1;
        }
        $this->app->module('builds')->sendMsg('MESSAGE','Getting all cat...','','',1);  

        $uploadFiles=[];   
        $curG=$this->app->db->find('addons/groups',['sort'=>['_id'=>1],'skip'=>$i]);  
        $count=count($curG)+$i;
        $this->app->module('builds')->sendMsg('MESSAGE',' Done','','blue');
        $this->app->module('builds')->sendMsg('MESSAGE',' Group count: <b style="color:#f00">'.$count.'</b>');  
        if($count==0)return 1;    
        foreach ($curG as $k => $group) { 

             //save status:
            @session_start();
            $procStatus=$this->app->db->findOne('status/buildprocess',['procName'=>'Build Group']);                
            
            if($procStatus['isstopbyuser']==1){
                $procStatus['isstopbyuser']=0;
                $procStatus['isBuilding']=0;
                $this->app->db->update("status/buildprocess",['_id'=>$procStatus['_id']],$procStatus);
                $this->app->module('builds')->sendMsg('STOP','Stopped by user!',($k+$i).'/'.$count,'red');                    
                exit;
            } 
            $procStatus['isBuilding']=1;               
            $procStatus['currentBuildAt']=$k+$i;
            $this->app->db->update("status/buildprocess",['_id'=>$procStatus['_id']],$procStatus);
            session_write_close(); 
            $this->app->module('builds')->sendMsg('MESSAGE','Building <b>'.$group['name'].'</b>...',$k+$i.'/'.$count,'');

            $result=$this->app->module("mycustom")->builddatabygroups($html,[$group['_id']],$overwrite); 


            
            $this->app->module("builds")->pushfile($uploadFiles,$config);
            $this->app->module('builds')->sendMsg('MESSAGE','<b> done.</b>',($k+$i+1).'/'.$count);
            $this->app->module('builds')->sendMsg('MESSAGE','Building <b>'.$group['name'].' done.</b><br />');
           
        }
    }

   

    
}
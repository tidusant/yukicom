<?php

//API

$pathName=explode("\\",__DIR__);
if(count($pathName)<=1)$pathName=explode('/',__DIR__);
$moduleName=$pathName[count($pathName)-1];
$this->module($moduleName)->extend([
    "getdb"=>function(){
        $mongo = new \MongoClient("mongodb://localhost",["db" => "socman","username" => "socman", "password" => "Password2@"]);
        $db=$mongo->socman;
        return $db;
    },
    "builddatabyposts"=>function($html,$ampHtml,$builditemids,$overwrite=0){
       if(count($builditemids)<=0)return 1;
        $config=$this->app->module('builds')->getConfig();
        $or=[];
        foreach ($builditemids as $key => $id) {
            $or[]=['_id'=>new \MongoId($id)];
        }
        $curP=$this->app->db->find('addons/posts',['filter'=>['$or'=>$or],'limit'=>1184467440737]);  
        $count=count($curP);
        if($count==0)return 1;
        $items=[];
        
        foreach ($curP as $key => $item) {

            if(!isset($item['code'])){
                $item["code"] =$this->app->module('commons')->create_random_string(3,1);
                //check item code
                //$item["code"]='abc';
                do{
                    $itemdup=null;                    
                    $itemdup=$this->app->db->findOne("addons/$this->moduleName", ['code'=>$item["code"]]);
                    if($itemdup){
                        $item["code"] =$this->app->module('commons')->create_random_string(3,1);
                    }                
                }while(($itemdup!=null && !empty($itemdup)) || $item["code"]=='all');
                $this->app->db->save("addons/posts", $item);
            }

            //replace shortcode
            preg_match_all('/\[\[\[(.*?)\]\]\]/is', $item['content'], $matches);                
            if(isset($matches[0])){          
               foreach($matches[1] as $v){
                    $params=explode(':::', $v);
                    if(isset($config['shortcode'][$params[0]]))
                    {
                        //get shortcode token
                        $value=$config['shortcode'][$params[0]];
                        preg_match_all('/\[\[\[(.*?)\]\]\]/is', $value, $matches2);                
                        if(isset($matches2[0])){          
                           foreach($matches2[1] as $p){
                            $value=str_replace('[[['.$p.']]]',isset($params[$p])?$params[$p]:'',$value);
                           }
                        }
                        $item['content']=str_replace('[[['.$v.']]]',$value,$item['content']);
                    }
                    else{
                        if($params[0]=='price'){
                            $item['content']=str_replace('[[['.$v.']]]','<div class="detailpriceinfo"><div class="pricetitle"> <span class="title"> '.$item['title'].' </span></div><div class="pricecontent" id="prod'.$item['code'].'" pvalue="'.$item['price'].'"> '.$this->app->module('builds')->numberWithCommas($item['price']*1000).'đ </div><div class="buttoncontent"> <button class="button buttonG btnAddCart" id="btn2prod'.$item['code'].'">BỎ VÀO GIỎ HÀNG</button> <button class="button buttonG btnBuyNow" id="btn3prod'.$item['code'].'" >MUA NGAY</button></div></div>',$item['content']);
                        }
                    }
               }                   
            }
            

            $additionscript='';
            //get all prod info 
            if($item['slug']=='order'){
                $curs=$this->app->db->find('addons/posts',['filter'=>['publish'=>1,'microdata'=>'Product'],'limit'=>1184467440737]);  
                $additionscript='var prods={';
                foreach($curs as $k=>$v){
                    
                    $additionscript.='"'.$v['code'].'":{"name":"'.$v['title'].'","price":'.(isset($v['price'])?$v['price']:0).',"slug":"'.$v['slug'].'"},';
                }
                $additionscript=substr($additionscript,0, strlen($additionscript)-1);
                $additionscript.='};';
            }

            //print_r($additionscript);
            $item['best']=isset($item['best'])?$item['best']:0;
            //addition fields be used in cache data         
            $item['data']=[];
            $item['data']['script']=$additionscript.(isset($item['script'])?$item['script']:'');
            
            $item['data']['controllerName']='Detail';
            $item['data']['best']=isset($item['best'])?$item['best']:0;
            $item['data']['alternateControllerName']=$item['data']['controllerName'];
            if(isset($groupslugs[$item['gid']]))
                $item['data']['controllerName']=$this->app->module('builds')->camelize('Detail_'.$groupslugs[$item['gid']],1);
            $item['data']['title']=str_replace("\"", "", $item['title']);
            $item['data']['description']=str_replace("\"", "", $item['description']);
           
            $item['data']['content']=$item['content'];
            $item['data']['slug']=$item['slug'];        
            $item['data']['gid']=$item['gid'];
            $item['data']['lang']=$item['lang'];
            if($config['showlang']){
                $item['data']['langslink']=isset($item['langslink'])?$item['langslink']:[];
                $item['data']['langsiso']=isset($item['langsiso'])?$item['langsiso']:[];
                $item['data']['langsname']=isset($item['langsname'])?$item['langsname']:[];
            }
            $items[]=$item;
        }
        
        

        $this->app->module('posts')->builddatabyposts($html,$ampHtml,$items,$overwrite);
    },
    "builddatabygroups"=>function($html,$buildcatids,$overwrite=0){
        
        $config=$this->app->module('builds')->getConfig();

        if($config['noitempercat']==0)return 1; //disabled build group
        if(count($buildcatids)==0)return 1;
        
        $or=[];
        foreach ($buildcatids as $key => $id) {
            $or[]=['_id'=>new \MongoId($id)];
        }

        $curG=$this->app->db->find('addons/groups',['filter'=>['$or'=>$or],'limit'=>1184467440737]);  
        $count=count($curG);

        if($count==0)return 1;
        $groups=[];
        foreach ($curG as $key => $item) {
            //additional script
            $script='';
            //more fields (be used in function)
           
            $item['script']=isset($item['script'])?$item['script']:'';

            //addition fields be used in cache data         
            $item['data']=[];
            
            $item['data']['controllerName']=$this->app->module('builds')->camelize('Category_'.$item['slug'],1);
            $item['data']['alternateControllerName']='Category';
            $item['data']['script']=isset($item['script'])?$item['script']:'';
            $item['data']['slug']=$item['slug'];        
            $item['data']['gid']=$item['gid'];
            $item['data']['code']=isset($item['code'])?$item['code']:'';
            $item['data']['lang']=$item['lang'];
            if($config['showlang']){
                $item['data']['langslink']=isset($item['langslink'])?$item['langslink']:[];
                $item['data']['langsiso']=isset($item['langsiso'])?$item['langsiso']:[];
                $item['data']['langsname']=isset($item['langsname'])?$item['langsname']:[];
            }
            $groups[]=$item;
        }
        
        $this->app->module('groups')->builddatabygroups($html,$groups,$overwrite);
    },
    "buildHome"=>function($html){


        $this->app->module('builds')->buildHome($html);
        return 1;
    },
    "buildscript"=>function(){

        $this->app->module('builds')->buildscript();
        return 1;
    }

   
]);


// ADMIN
if(COCKPIT_ADMIN) {
    //register acl
    $this("acl")->addResource("$moduleName", ['manage.index', 'manage.edit']);
    $app->on("admin.init", function() use($app,$moduleName){
        
        // bind routes
        
        $app->bindClass("$moduleName\\Controller\\Main", "$moduleName");
        
        // bind api
        $app->bindClass("$moduleName\\Controller\\Api", "api/$moduleName");
       
        
    });
    
}
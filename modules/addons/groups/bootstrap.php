<?php

$pathName=explode("\\",__DIR__);
if(count($pathName)<=1)$pathName=explode('/',__DIR__);
$moduleName=$pathName[count($pathName)-1];
// API for calling from outsite module.
$this->module($moduleName)->extend([
    "getTree"=>function($currentItems=null,$default='- No group -'){
        $groups=[];
        $config=$this->app->module("builds")->getConfig();
        foreach ($config['langs'] as $key => $value) {
            $items = $this->app->db->find("addons/groups", ['filter'=>['lang'=>$value,'gid'=>0]])->toArray();
            
            $tree=[];
            if(!empty($default))
                $tree[]=["_id"=>0,"name"=>"- No group -"];
            $currentId=0;
            if(isset($currentItems[$value]))$currentId=$currentItems[$value]['_id'];
            foreach($items as $k=>$v){                
                $tree=$this->app->module('groups')->loopTree($v,$tree,$currentId);
            }
            $groups[$value]=$tree;    
        }
        return $groups;
    },
    "loopTree"=>function($item,$tree,$currentId=0,$disabled=0,$level=0){
        if($level>0){
            for($i=0;$i<$level;$i++){
                if($i==0) $item['name']=' ┗ '.$item['name'];
                else $item['name']='   '.$item['name'];
            }
        }
        if($item['_id']==$currentId)$disabled=1;
        $item['disabled']=$disabled;
        $tree[]=$item;
        //get child
        $items = $this->app->db->find("addons/groups", ['filter'=>['gid'=>$item['_id']]]);
        //print_r($items);exit;
        if(count($items)>0){
            foreach ($items as $key => $value) {
                $tree=$this->app->module('groups')->loopTree($value,$tree,$currentId,$disabled,$level+1);
            }
        }
        return $tree;
    },
    "builddatabygroups"=>function($html,$groups,$overwrite=0){
        set_time_limit(36000);
        $config=$this->app->module('builds')->getConfig();
        $siteurl=($config['live']?$config['siteurl']:$config['localurl']);
        $uploadFiles=[];   
          
        foreach ($groups as $k => $group) {     
            $firstchar=substr($group['slug'],0,1);
            
            //remove width height in content
            $group['content']=preg_replace('/<img(.*?)(width=[\'"].*?[\'"])(.*?)>/si','<img$1$3>', $group['content']);
            $group['content']=preg_replace('/<img(.*?)(height=[\'"].*?[\'"])(.*?)>/si', '<img$1$3>', $group['content']);
            $group['content']=preg_replace('/<img(.*?)>/si', '<img class="thumbnail"$1>', $group['content']);      
            //check featureimage change:
            if(isset($group['featureimage'])){
                $feature_image =$group['featureimage'];
                $feature_image=str_replace("site:",$this->app->pathToUrl('site:'),$feature_image);                
                $feature_image_path=COCKPIT_DIR.str_replace(COCKPIT_APP_PATH.'/', '', $feature_image);
                $files=$this->app->module("builds")->getCacheImage($feature_image_path, $group['slug'],$config['arrSize']['detail'],0,$overwrite);
               
                $uploadFiles=array_merge($uploadFiles,$files['arrFiles']); 
                $group['featureimage']=$files['filename'];
                $group['featureimagetype']=$files['type'];
            }  

            if($config['isbuilddata']){
                //get all image in content and replace with cache image
                
                preg_match_all('/<img.*?(src=["\'](.*?)["\']).*?>/is', $group['content'], $matches);
                //print_r($matches);
                if(isset($matches[2])){          
                    $imagesReplace=[];                    
                    foreach($matches[2] as $k=>$img){
                        //sync with editor:
                        $syncimg=str_replace($this->app->module("builds")->sitebaseurl().COCKPIT_BASE_URL,'../..',$img);
                        $image_path=COCKPIT_DIR.str_replace('../..', '', $syncimg);
                        
                        //generate cache image
                        $files=$this->app->module("builds")->getCacheImage($image_path, $group['slug'],$config['arrSize']['detail'],$k+1,$overwrite);
                        if(empty($group['featureimage'])){
                            $group['featureimage']=$files['filename'];
                            $group['featureimagetype']=$files['type'];
                        }
                        //print_r($isnewimage);

                        $uploadFiles=array_merge($uploadFiles,$files['arrFiles']);   

                        $imagesReplace[$matches[1][$k]]='src="{{siteurl}}'.$group['slug'].'/'.$files['filename'].'[[sitesize.detail]]'.$files['type'].'"';
                    }
                    //print_r($imagesReplace);exit;
                    $content2=str_replace(array_keys($imagesReplace),$imagesReplace,$group['content']);
                    $group['content']=$content2;
                }

                //replace shortcode
                preg_match_all('/\[\[\[(.*?)\]\]\]/is', $group['content'], $matches);                
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

                           
                            $group['content']=str_replace('[[['.$v.']]]',$value,$post['content']);
                        }
                   }                   
                }
            


                $posts=[];
                //get group child:
                $groupchilds=$this->app->db->find('addons/groups',['filter'=>['gid'=>$group['_id'],'publish'=>1],'sort'=>['_id'=>-1]])->toArray(); 
                if(count($groupchilds)>0){
                    $groupchildIds=[];
                    foreach ($groupchilds as $key => $groupchild) {
                        //check if have publish item:
                        $curP1=$this->app->db->find('addons/posts',['filter'=>['gid'=>$groupchild['_id'],'publish'=>1]]);                                             
                        if(count($curP1)>0){                            
                            $groupchildIds[]=$groupchild['_id'];
                            $p=[];
                            $p['_id']=$groupchild['_id'];
                            $p['slug']=$groupchild['slug'];
                            $p['title']=$groupchild['name'];    
                            $p['description']=$groupchild['description'];         
                            $p['post_date']=$groupchild['created'];               
                            $p['featureimage']='';
                            $posts[]=$p;                        
                        }                   

                    }
                    $this->app->module('mycustom')->builddatabygroups($html,$groupchildIds,$overwrite);
                }

                //get child
                $curP=$this->app->db->find('addons/posts',['filter'=>['gid'=>$group['_id'],'publish'=>1],'sort'=>['_id'=>-1]])->toArray();                             
                $postcount=count($curP);                  
                $pagecount=ceil($postcount/$config['noitempercat']);
                
                //extract Html
                $fileCatName=$this->app->module('builds')->camelize('Category_'.$group['slug'],1);

                $mainHtml=$this->app->module("builds")->getTemplateFile($group['lang'],'/'.$fileCatName.'.html','/Category.html');
                //print_r($mainHtml);
                //$mainHtml=$this->app->module('builds')->parseNgData($mainHtml,['post'=>$p,'siteurl'=>$this->app->module('builds')->siteurl(),'sitesize'=>$this->app->module('builds')->getArrSize()[0]]);
                
                //get loop html
                $parsedHtml=['html'=>$mainHtml];
                $parsedHtml['var']=['siteurl'=>$siteurl];
                $parsedHtml['var']['sitesize']=array_values($config['arrSize']['detail'])[0];
                //print_r($parsedHtml);exit;
                foreach ($curP as $key => $post) {     
                    
                    $p=[];
                    $p['_id']=$post['_id'];
                    $p['slug']=$post['slug'];
                    $p['title']=$post['title'];    
                    $p['description']=$post['description'];         
                    $p['post_date']=$post['created'];        
                    $p['best']=isset($post['best'])?$post['best']:0;
                    $p['price']=isset($post['price'])?$post['price']:0;
                    $p['code']=isset($post['code'])?$post['code']:0;
                    if(isset($post['featureimage'])){
                        //print_r($config['arrSize']);exit;
                        $feature_image=$this->app->module("builds")->sitebaseurl().str_replace("site:",$this->app->pathToUrl('site:'),$post['featureimage']);                               
                        $files=$this->app->module("builds")->getCacheImage($feature_image,$post['slug'],$config['arrSize']['cat'],0,$overwrite);
                        $p['featureimage']=$files['filename'];  
                        $p['featureimagetype']=$files['type'];                       
                        
                        $uploadFiles=array_merge($uploadFiles,$files['arrFiles']);

                       
                    }
                    else $p['featureimage']='';
                    $posts[]=$p;
                    //$this->app->module('builds')->sendMsg('MESSAGE','<b> done.</b>',$grouppercent.'+'.($key+1).'/'.$postcount.'/'.$count);                  
                }

                $ps=[];
                //print_r($posts);
                foreach ($posts as $key => $post) {        
                    
                    $ps[]=$post;
                    //paging for cat data
                    if(($key+1)%$config['noitempercat']==0 || $key==$postcount-1){
                        $page=ceil(($key+1)/$config['noitempercat']);
                        $pagename=$group['slug'].($page>1?'-'.$page:'');
                        $this->app->module('builds')->createdir('./output/'.$firstchar.'/'.$pagename);

                        $pagetitle=$group['slug'].($page>1?' - page '.$page:'');
                        //render page:
                        //$this->app->module('builds')->sendMsg('MESSAGE','Start render page '.$page.'...','','',1);
                        $parsedHtml['var']['data.posts']=$ps;
                        $parsedHtml['var']['data.title']=$group['name'];
                        $parsedHtml['var']['data.description']=$group['description'];
                        $parsedHtml['var']['data.content']=$group['content'];
                        $parsedHtml['var']['data.page']=$page;
                        $parsedHtml['var']['data.nextpage']=$page+1;
                        $parsedHtml['var']['data.prevpage']=$page-1;
                        $parsedHtml['var']['data.groupslug']=$group['slug'];
                        $parsedHtml['var']['data.slug']=$pagename;
                        $parsedHtml['var']['data.totalpage']=$pagecount;                        
                        $parsedHtml['var']['data.pagetype']='category';
                        //print_r($parsedHtml);exit;
                        $mainHtml=$this->app->module("builds")->parseAngularHtml($parsedHtml,10);
                        
                        if(strpos($html[$group['lang']]['body'], "id='".$fileCatName."Controller'>")!==false)             
                            $mainHtml=preg_replace("/id='".$fileCatName."Controller'>.*?</si", "id='".$fileCatName."Controller'>".$mainHtml."<", $html[$group['lang']]['body']);
                        else 
                            $mainHtml=preg_replace("/id='CategoryController'>.*?</si", "id='CategoryController'>".$mainHtml."<", $html[$group['lang']]['body']);
                        $mainHtml=str_replace('class="menu'.$group['slug'].'"','class="menu'.$group['slug'].' active"', $mainHtml);
                        

                        //write to file
                        //cache.js for index.html
                        $slug=$pagename.'cachejs';
                        $mainHtml=$this->app->module('builds')->JSencrypt($mainHtml,$slug,'html',$config);
                        $uploadFiles=$this->app->module('builds')->output($slug,$mainHtml,$uploadFiles);
                       
                             
                        //cache data for ajax:
                        
                        $group['data']['siteurl']=$siteurl;
                        $group['data']['title']=$group['name'];
                        $group['data']['description']=$group['description'];
                        $group['data']['content']=$group['content'];
                        $group['data']['slug']=$pagename;
                        $group['data']['groupslug']=$group['slug'];
                        $group['data']['page']=$page;
                        $group['data']['nextpage']=$page+1;
                        $group['data']['prevpage']=$page-1;
                        $group['data']['totalpage']=$pagecount;
                        $group['data']['posts']=$ps;
                        $group['data']['pagetype']='category';
                        $group['data']['featureimage']=isset($group['featureimage'])?$group['featureimage']:'';
                        $group['data']['featureimagetype']=isset($group['featureimagetype'])?$group['featureimagetype']:'';
                        //reset post
                        
                        $cachedata=base64_encode(gzcompress(json_encode($group['data']),9));
                        $uploadFiles=$this->app->module('builds')->output($pagename,$cachedata,$uploadFiles,2);
                        
                      
                        //get schema content    
                        $microdata=[];
                       
                        $microdata['title']=$group['name'];
                        $microdata['description']=isset($group['description'])?$group['description']:'';
                        $microdata['slug']=$group['slug'];
                        $microdata['datePublished']=date("Y-m-d",$group['created']);
                        $microdata['dateModified']=date("Y-m-d",$group['modified']);

                        $microdata['type']='Article';
                        $microdata['authorname']=isset($group['authorname'])?$group['authorname']:'';
                        $microdata['publisher']=$microdata['authorname'];
                        $microdata['imagewidth']=array_values($config['arrSize']['detail'])[0];
                        $microdata['imageheight']=373;
                        $microdata['familyName']=substr($microdata['authorname'], 0,strpos($microdata['authorname'], " "));
                        $microdata['givenName']=substr($microdata['authorname'],strpos($microdata['authorname'], " ")+1);
                    
                       
                        $microdata['posts']=$ps;
                        
                                    
                        // $filename='./input/'.$group['lang'].'/schema/'.$fileCatName.'.html';
                        // if(!file_exists($filename)){
                        //     $filename='./input/'.$group['lang'].'/schema/Category.html';
                        //     if(!file_exists($filename)){
                        //         $filename='./input/default/schema/'.$fileCatName.'.html';
                        //         if(!file_exists($filename)){
                        //             $filename='./input/default/schema/Category.html';
                        //         }
                        //     }
                        // }
                        $schemaHtml=$this->app->module("builds")->getTemplateFile($group['lang'],'/schema/'.$fileCatName.'.html','/schema/Category.html');
                        $parsedSchemaHtml=['html'=>$schemaHtml];                        
                        $parsedSchemaHtml['var']=['siteurl'=>$siteurl];
                        foreach($microdata as $k=>$v){
                            $parsedSchemaHtml['var']['data.'.$k]=$v;    
                        }               
                        //print_r($parsedSchemaHtml);
                        $schemaHtml=$this->app->module('builds')->parseAngularHtml($parsedSchemaHtml); 
                        //================
                        
                        //metadata
                        $metadata=[];
                        $metadata[]=['meta'=>'og:type','content'=>strtolower(isset($group['microtype'])?$group['microtype']:'article')];
                        $metadata[]=['meta'=>'og:title','content'=>str_replace("\"", "", $group['name'])];        
                        $metadata[]=['meta'=>'og:url','content'=>$siteurl.$group['slug'].'/'];
                        if(isset($group['featureimage'])){
                            $metadata[]=['meta'=>'og:image','content'=>$siteurl.$group['slug'].'/'.$group['featureimage'].array_values($config['arrSize']['detail'])[0].$group['featureimagetype']];
                        }
                        $metadesc=str_replace(["\n\r","\n","\""], "", $group['description']);
                        $metadata[]=['meta'=>'og:description','content'=>$metadesc];
                        $metadata[]=['meta'=>'article:published_time','content'=>date("Y-m-d",$group['created']).'T'.date("H:i:s+00:00",$group['created'])];
                        $metadata[]=['meta'=>'article:modified_time','content'=>date("Y-m-d",$group['modified']).'T'.date("H:i:s+00:00",$group['modified'])];                        
                        if(!empty($group['authorlink']))
                            $metadata[]=['meta'=>'og:authorlink','content'=>$group['authorlink']];
                        if(isset($group['fbauthorlink']))
                            $metadata[]=['meta'=>'article:author','content'=>$group['fbauthorlink']];
                        $metadata[]=['meta'=>'fb:app_id','content'=>$config['FBId']];



                        $htmlcat=$this->app->module("builds")->addMetadata($html[$group['lang']]['html'],$metadata);
                        if(isset($schemaHtml))
                            $htmlcat=preg_replace('/<body(.*?)>/','<body$1>'.$schemaHtml,$htmlcat);   
                        $htmlcat=$this->app->module("builds")->addLangs($htmlcat,$group['lang'],isset($group['langslink'])?$group['langslink']:[]);


                        //$this->app->module('builds')->sendMsg('MESSAGE','Uploading file ...','','',1);
                        //bottom script and minify

                        $htmlcat=$this->app->module("builds")->getBottomScriptAndMinify($htmlcat,$pagename.'/',$group['data']['script']);

                        
                        //write html
                        
                        $filename=$pagename.'/index.html';                       
                        $cachedatafile=COCKPIT_DIR.'/output/'.$firstchar.'/'.$filename;

                        file_put_contents($cachedatafile, $htmlcat);            
                        $uploadFiles[]= './'.$firstchar.'/'.$filename;
                        $ps=[];
                        //$this->app->module('builds')->sendMsg('MESSAGE','<b> done.</b>');

                    }

                }                 
                
            }
            
            $this->app->module("builds")->pushfile($uploadFiles,$config);            
        }
    },
    
]);



//dashboard widget


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
        
       
        $app("admin")->menu("top", [
            "url"    => $app->routeUrl("/$moduleName"),
            "label"  => '<i class="uk-icon-book"></i>',
            "title"  => $app("i18n")->get("Groups")
        ], 1);
        
        
    });
    
}


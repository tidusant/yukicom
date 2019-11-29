<?php

$pathName=explode("\\",__DIR__);
if(count($pathName)<=1)$pathName=explode('/',__DIR__);
$moduleName=$pathName[count($pathName)-1];
// API for calling from outsite module.
$this->module($moduleName)->extend([

    "isBuildable"=>function(){
        return true;
    },
    "removedatapost"=>function($post){
        $config=$this->app->module('builds')->getConfig();
        $firstchar=substr($post['slug'],0,1);            
        //connect to host       
        //print_r($config);exit;
        if($config['live']==true){
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
            //remove on ftp
            if($conn_id){
                $path='./'.$firstchar.'/'.$post['slug']; 
                $this->app->module('commons')->ftp_rdel($conn_id,$path);
                $path.='-'.$post['amp']; 
                $this->app->module('commons')->ftp_rdel($conn_id,$path);
            }
        }

        //remove local
        
        $path='./output/'.$firstchar.'/'.$post['slug']; 
        $this->app->module('commons')->recursiveRemove($path);
        

        $path.='-'.$post['amp']; 
        $this->app->module('commons')->recursiveRemove($path);

    },
    "builddatabyposts"=>function($html,$ampHtml,$posts,$overwrite=0){
        
        $config=$this->app->module('builds')->getConfig();
        $siteurl=($config['live']?$config['siteurl']:$config['localurl']);
        $uploadFiles=[];
        
        foreach ($posts as $key => $post) {
            if(!$post['publish']){
                $this->app->module('posts')->removedatapost($post);                
            }
            else{
                //check folder exist:
                $firstchar=substr($post['slug'],0,1);            
                $this->app->module('builds')->createdir('./output/'.$firstchar.'/'.$post['slug']);            
                //remove width height in content
                $post['content']=preg_replace('/<img(.*?)(class=[\'"].*?noresize.*?[\'"])(.*?)>/si','{{img$1$2$3}}', $post['content']);
                $post['content']=preg_replace('/<img(.*?)(width=[\'"].*?[\'"])(.*?)>/si','<img$1$3>', $post['content']);
                $post['content']=preg_replace('/<img(.*?)(height=[\'"].*?[\'"])(.*?)>/si', '<img$1$3>', $post['content']);
                
                $best='';
                if(isset($post['best']) && $post['best'])$best='<div class="best"></div>';
                $post['content']=preg_replace('/<img(.*?)>/si', '<div  class="thumbnail">'.$best.'<img $1></div>', $post['content']);

                
                //gname:
                if($post['gid']!=0)
                    $group=$this->app->db->findOne('addons/groups',['_id'=>new \MongoId($post['gid'])]);
                $gname='unknown';
                $gnameslug='unknown';
                if(isset($group) && $group){
                    $gname=$group['name'];
                    $gnameslug=$group['slug'];
                }
                $post['gname']=$gname;            
                $post['gnameslug']=$gnameslug;            
                $post['pagetype']='detail';
                //check code for product
                if(isset($post['microdata']) && strtolower($post['microdata'])=='product'){
                    if(!isset($post['code']))$post['code']=$this->app->module('builds')->create_random_string(10);
                }
                //check featureimage change:
                if(isset($post['featureimage'])){
                    $feature_image =$post['featureimage'];
                    $feature_image=str_replace("site:",$this->app->pathToUrl('site:'),$feature_image);                
                    $feature_image_path=COCKPIT_DIR.str_replace(COCKPIT_APP_PATH.'/', '', $feature_image);
                    $this->app->module("builds")->getCacheImage($feature_image_path, $post['slug'],$config['arrSize']['home'],0,$overwrite);   
                    $this->app->module("builds")->getCacheImage($feature_image_path, $post['slug'],$config['arrSize']['cat'],0,$overwrite);   
                    
                    $files=$this->app->module("builds")->getCacheImage($feature_image_path, $post['slug'],$config['arrSize']['detail'],0,$overwrite);                              
                    $uploadFiles=array_merge($uploadFiles,$files['arrFiles']); 
                    $post['featureimage']=$files['filename'];
                    $post['featureimagetype']=$files['type'];
                }
                
                
                

                //build data
                $isLoadGacode=0;
                if($config['isbuilddata']){
                    //check content is change?
                    if(1){
                        
                        

                        //get all image in content and replace with cache image
                        preg_match_all('/<img.*?(src=["\'](.*?)["\']).*?>/is', $post['content'], $matches);
                        //print_r($matches);
                        if(isset($matches[2])){          
                            $imagesReplace=[];                    
                            foreach($matches[2] as $k=>$img){
                                //sync with editor:
                                $syncimg=str_replace($this->app->module("builds")->sitebaseurl().COCKPIT_BASE_URL,'../..',$img);
                                $image_path=COCKPIT_DIR.str_replace('../..', '', $syncimg);
                                //generate cache image
                                $files=$this->app->module("builds")->getCacheImage($image_path, $post['slug'],$config['arrSize']['detail'],$k+1,$overwrite);
                                if(empty($post['featureimage'])){
                                    $post['featureimage']=$files['filename'];
                                    $post['featureimagetype']=$files['type'];
                                }
                                //print_r($isnewimage);

                                $uploadFiles=array_merge($uploadFiles,$files['arrFiles']);   

                                $imagesReplace[$matches[1][$k]]='src="{{siteurl}}'.$post['slug'].'/'.$files['filename'].'[[sitesize.detail]]'.$files['type'].'?v='.$this->app->module('commons')->create_random_string(3).'"';
                            }
                            //print_r($imagesReplace);exit;
                            $content2=str_replace(array_keys($imagesReplace),$imagesReplace,$post['content']);
                            $post['content']=$content2;
                        }
                        $post['content']=preg_replace('/{{img(.*?)}}/si','<img$1>', $post['content']);
                       // print_r($post['content']);
                    }
                    



                    //extract Html
                    $fileTemplateName=$this->app->module('builds')->camelize('Detail_'.$post['gnameslug'],1);
                    // $filename='./input/'.$post['lang'].'/'.$fileTemplateName.'.html';
                    // if(!file_exists($filename)){
                    //     $filename='./input/'.$post['lang'].'/Detail.html';
                    //     if(!file_exists($filename)){
                    //         $filename='./input/default/'.$fileTemplateName.'.html';
                    //         if(!file_exists($filename)){
                    //             $filename='./input/default/Detail.html';
                    //         }
                    //     }
                    // }
                    $mainHtml=$this->app->module("builds")->getTemplateFile($post['lang'],'/'.$fileTemplateName.'.html','/Detail.html');

                    //relate post
                    $relateposts=[];
                    if(isset($post['relatepost']) && count($post['relatepost'])>0){
                        //print_r($post['relatepost']);
                        $cursrelate=$this->app->db->find('addons/posts',['filter'=>['slug'=>['$in'=>$post['relatepost']],'publish'=>1],'fields'=>['_id'=>0,'code'=>1,'price'=>1,'title'=>1,'slug'=>1]]);
                        $relateposts=$cursrelate->toArray();
                        //print_r($relateposts);
                    }


                    //print_r($mainHtml);exit;
                    $parsedHtml=['html'=>$mainHtml];
                    //print_r($parsedHtml);exit;
                    $parsedHtml['var']=['siteurl'=>$siteurl];
                    $parsedHtml['var']['sitesize']=array_values($config['arrSize']['detail'])[0];
                    foreach($post as $k=>$v){
                        $parsedHtml['var']['data.'.$k]=$v;    
                    }
                    $parsedHtml['var']['data.relateposts']=$relateposts; 
                   
                    
                   $mainHtml=$this->app->module('builds')->parseAngularHtml($parsedHtml);
                   

                    if(strpos($html[$post['lang']]['body'], "id='".$fileTemplateName."Controller'")!==false)             
                        $mainHtml=str_replace("<div id='".$fileTemplateName."Controller'></div>", "<div id='".$fileTemplateName."Controller'>".$mainHtml."<", $html[$post['lang']]['body']);
                    else 
                        $mainHtml=str_replace("<div id='DetailController'></div>", "<div id='DetailController'>".$mainHtml."</div>", $html[$post['lang']]['body']);
                    //=====================


                    //get schema content    
                    $microdata=[];
                    if(!empty($post['microdata'])){
                        $microdata['type']=$post['microdata'];
                        $post['microtype']=$post['microdata'];
                        $microdata['title']=str_replace("\"", "", $post['title']);
                        $microdata['description']=$post['description'];
                        $microdata['slug']=$post['slug'];
                        if(isset($post['featureimage'])){
                            $microdata['image']=$siteurl.$post['slug'].'/'.$post['featureimage'].array_values($config['arrSize']['detail'])[0].$post['featureimagetype'];
                            $microdata['thumb']=$siteurl.$post['slug'].'/'.$post['featureimage'].array_values($config['arrSize']['detail'])[1].$post['featureimagetype'];
                        }
                        $microdata['datePublished']=date("Y-m-d",$post['created']);
                        $microdata['dateModified']=date("Y-m-d",$post['modified']);
                        
                        
                        if($microdata['type']=='Article'){
                            $microdata['authorname']=isset($post['authorname'])?$post['authorname']:'';
                            $microdata['publisher']=$microdata['authorname'];

                            

                            $microdata['imagewidth']=array_values($config['arrSize']['detail'])[0];
                            $microdata['imageheight']=373;

                            $microdata['familyName']=substr($microdata['authorname'], 0,strpos($microdata['authorname'], " "));
                            $microdata['givenName']=substr($microdata['authorname'],strpos($microdata['authorname'], " ")+1);
                        }
                        else if($microdata['type']=='Video'){
                            
                            $microdata['embedURL']=isset($post['embedUrl'])?$post['embedUrl']:'';               
                            $microdata['contentURL']=$microdata['embedURL'];
                            $microdata['duration']=isset($post['duration'])?$post['duration']:'';
                        }
                        else if($microdata['type']=='Product'){
                            
                            $microdata['price']=isset($post['price'])?$post['price']:'';               
                            $post['data']['price']=isset($post['price'])?$post['price']:'';      
                        }
                        
                    }
                                
                    // $filename='./input/'.$post['lang'].'/schema/'.$fileTemplateName.'.html';
                    // if(!file_exists($filename)){
                    //     $filename='./input/'.$post['lang'].'/schema/Detail.html';
                    //     if(!file_exists($filename)){
                    //         $filename='./input/default/schema/'.$fileTemplateName.'.html';
                    //         if(!file_exists($filename)){
                    //             $filename='./input/default/schema/Detail.html';
                    //         }
                    //     }
                    // }
                    $schemaHtml=$this->app->module("builds")->getTemplateFile($post['lang'],'/schema/'.$fileTemplateName.'.html','/schema/Detail.html');
                    $parsedSchemaHtml=['html'=>$schemaHtml];                
                    $parsedSchemaHtml['var']=['siteurl'=>$siteurl];
                    
                    foreach($microdata as $k=>$v){
                        $parsedSchemaHtml['var']['data.'.$k]=$v;    
                    }               
                    
                    $schemaHtml=$this->app->module('builds')->parseAngularHtml($parsedSchemaHtml); 
                    //================
                    
                    
                    //ga code
                    //$mainHtml=$this->app->module("builds")->injectGACode($mainHtml,isset($post['not_show_code'])?$post['not_show_code']:[]);
                    
                    //print_r($mainHtml);exit;
                    //$this->app->module('builds')->t($mainHtml);
                    
                    //$cachedata=$this->app->module("builds")->addFirstScript($mainHtml,$post['title'],$post['slug'].'/');

                    //write to file           
                    //cache.js

                    $slug=$post['slug'].'cachejs';
                    $cachedata=$this->app->module('builds')->JSencrypt($mainHtml,$slug,'html',$config);
                    $uploadFiles=$this->app->module('builds')->output($slug,$cachedata,$uploadFiles);
                    

                    //output cache data         
                     
                    $post['data']['content']=$post['content'];
                    $post['data']['featureimage']=isset($post['featureimage'])?$post['featureimage']:'';
                    $post['data']['featureimagetype']=isset($post['featureimagetype'])?$post['featureimagetype']:'';
                    $post['data']['gname']=$post['gname'];
                    $post['data']['best']=$post['best'];
                    $post['data']['gnameslug']=$post['gnameslug'];
                    $post['data']['pagetype']='detail';
                    $post['data']['code']=isset($post['code'])?$post['code']:'';
                    $post['data']['relateposts']=$relateposts; 
                    $slug=$post['slug'];
                    $cachedata=base64_encode(gzcompress(json_encode($post['data']),9));
                    $uploadFiles=$this->app->module('builds')->output($slug,$cachedata,$uploadFiles,2);

                  
                }
                //write to file    
                
                
               

                $metadata=[];
                $metadata[]=['meta'=>'og:type','content'=>strtolower(isset($post['microtype'])?$post['microtype']:'article')];
                $metadata[]=['meta'=>'og:title','content'=>str_replace("\"", "", $post['title'])];        
                $metadata[]=['meta'=>'og:url','content'=>$siteurl.$post['slug'].'/'];
                if(isset($post['featureimage'])){
                    $metadata[]=['meta'=>'og:image','content'=>$siteurl.$post['slug'].'/'.$post['featureimage'].array_values($config['arrSize']['detail'])[0].$post['featureimagetype']];
                }
                $metadesc=str_replace(["\n\r","\n","\""], "", $post['description']);
                $metadata[]=['meta'=>'og:description','content'=>$metadesc];
                $metadata[]=['meta'=>'article:published_time','content'=>date("Y-m-d",$post['created']).'T'.date("H:i:s+00:00",$post['created'])];
                $metadata[]=['meta'=>'article:modified_time','content'=>date("Y-m-d",$post['modified']).'T'.date("H:i:s+00:00",$post['modified'])];
                // if(!empty($post['authorname']))
                //     $metadata[]=['meta'=>'og:author','content'=>$post['authorname']];
                if(!empty($post['authorlink']))
                    $metadata[]=['meta'=>'og:authorlink','content'=>$post['authorlink']];
                if(isset($post['fbauthorlink']))
                    $metadata[]=['meta'=>'article:author','content'=>$post['fbauthorlink']];
                $metadata[]=['meta'=>'fb:app_id','content'=>$config['FBId']];

                $indexHtml=$this->app->module("builds")->addMetadata($html[$post['lang']]['html'],$metadata);
                if(isset($schemaHtml))
                    $indexHtml=preg_replace('/<body(.*?)>/','<body$1>'.$schemaHtml,$indexHtml);   

                //add language href and amplink
                $indexHtml=$this->app->module("builds")->addLangs($indexHtml,$post['lang'],isset($post['langslink'])?$post['langslink']:[],$siteurl.$post['slug'].'-'.$post['amp'].'/');
                
                
                $indexHtml=$this->app->module("builds")->getBottomScriptAndMinify($indexHtml,$post['slug'].'/',$post['data']['script']);
                
                //print_r($post);exit;
                //write html file           
                $filename=$firstchar.'/'.$post['slug'].'/index.html';
                $cachedatafile=COCKPIT_DIR.'/output/'.$filename;
                file_put_contents($cachedatafile, $indexHtml);            
                $uploadFiles[]= './'.$filename;


                //=============================================amp version
                //=============================================
                //=============================================
                //=============================================            
                if(isset($post["amp"]) && !empty($post["amp"])){
                    $this->app->module('builds')->createdir('./output/'.$firstchar.'/'.$post['slug'].'-'.$post['amp']);

                     
                    
                    //get detail html                
                    // $filename='./input/'.$post['lang'].'/amp/'.$fileTemplateName.'.html';
                    // if(!file_exists($filename)){
                    //     $filename='./input/'.$post['lang'].'/amp/Detail.html';
                    //     if(!file_exists($filename)){
                    //         $filename='./input/default/amp/'.$fileTemplateName.'.html';
                    //         if(!file_exists($filename)){
                    //             $filename='./input/default/amp/Detail.html';
                    //         }
                    //     }
                    // }
                    // $detailAmpHtml=file_get_contents($filename); 
                    $detailAmpHtml=$this->app->module("builds")->getTemplateFile($post['lang'],'/amp/'.$fileTemplateName.'.html','/amp/Detail.html');
                    //get data and render
                    $parsedAmpHtml=['html'=>$detailAmpHtml];
                    $parsedAmpHtml['var']=['siteurl'=>$siteurl];                
                    foreach($post['data'] as $k=>$v){
                        if($k=='content'){
                            //content progress for amp
                            //remove inline style
                            //$v=preg_replace( "~<(?!a\s)([a-z][a-z0-9]*)[^>]*?(/?)>~i",'<$1$2>', $v ); 
                                              
                            $v=preg_replace( "/\sstyle=\".*?\"/",'', $v );

                            //$v=preg_replace( "/<(.+)\s(.*?)>/is",'<$1>', $v );
                            //$v=strip_tags($v,'<a><p>');
                            

                            
                            //replace internal link with amp link
                            $linkSrc=[];
                            $replaceSrc=[];
                            preg_match_all('/<a[^>]*>/is',$v, $matches);  
                            
                            if($matches[0]){
                                foreach ($matches[0] as $key => $value) {
                                    preg_match('/href=[\'"](.*?)[\'"]/is',$value, $matches2);                        
                                    if($matches2 && strpos($matches2[1], $config['siteurl'])==0){
                                        
                                        //find by slug and replace amplink
                                        $linkslug=str_replace($config['siteurl'], "", $matches2[1]);
                                        $linkslug=str_replace("/", "", $linkslug);
                                        $linkpost=$this->app->db->findOne('addons/posts',['slug'=>$linkslug]);
                                        if(!empty($linkpost)){
                                            $linkSrc[]=$matches2[1];    
                                            $replaceSrc[]=$siteurl.$linkpost['slug'].'-'.$linkpost['amp'].'/';
                                        }
                                    }
                                }
                                $v=str_replace($linkSrc, $replaceSrc, $v);
                            }

                            //replace img tag with amp-img  
                                              
                            preg_match_all('/<img[^>]*>/is',$v, $matches);  
                            
                            if($matches[0]){
                                foreach ($matches[0] as $key => $value) {
                                    
                                    preg_match('/src=[\'"](.*?)[\'"]/is',$value, $matches2);   
                                    if($matches2 ){                                    
                                        //find by slug and replace amplink
                                        $imglink=str_replace("{{siteurl}}", $siteurl, $matches2[1]);
                                        $imglink=str_replace("[[sitesize.detail]]", array_values($config['arrSize']['detail'])[0], $imglink);
                                        //get height and width
                                        $localimg=str_replace("{{siteurl}}", $config['localurl'], $matches2[1]);
                                        $localimg=str_replace("[[sitesize.detail]]", array_values($config['arrSize']['detail'])[0], $localimg);
                                        $size=getimagesize ($localimg);         
                                        $v=str_replace( $value,'<amp-img src="'.$imglink.'" layout="responsive" width="'.$size[0].'" height="'.$size[1].'">', $v );    
                                    }
                                    
                                }
                                
                            }

                            //replace youtube iframe                        
                            preg_match_all('/<iframe[^>]*><\/iframe>/is',$v, $matches);  
                            if($matches[0]){
                                
                                foreach ($matches[0] as $key => $value) {
                                    
                                    preg_match('/src=[\'"](.*?)[\'"]/is',$value, $matches2);   
                                    if($matches2  && (strpos($matches2[1],"https://www.youtube.com/embed/")==0)){                                    
                                        //find by slug and replace amplink
                                        $ytid=str_replace("https://www.youtube.com/embed/", "", $matches2[1]);
                                        $ytinfo=file_get_contents("https://www.youtube.com/oembed?url=http%3A//www.youtube.com/watch?v=BDY-MF2raSc&format=json");
                                        $ytinfo=json_decode($ytinfo,true);
                                        $v=str_replace( $value,'<amp-youtube data-videoid="'.$ytid.'" layout="responsive" width="'.$ytinfo["width"].'" height="'.$ytinfo["height"].'"></amp-youtube>', $v );    
                                    }
                                    
                                }
                                
                            }


                        }
                        $parsedAmpHtml['var']['data.'.$k]=$v;    
                    }


                                  
                    $parsedAmpHtml['var']['data.relateposts']=$relateposts; 

                    //get feature image:
                    $parsedAmpHtml['var']['data.image']=isset($microdata['image'])?$microdata['image']:'';
                    $parsedAmpHtml['var']['data.imagewidth']=isset($microdata['imagewidth'])?$microdata['imagewidth']:'';
                    $parsedAmpHtml['var']['data.imageheight']=isset($microdata['imageheight'])?$microdata['imageheight']:'';
                    $parsedAmpHtml['var']['data.dateModified']=isset($microdata['dateModified'])?$microdata['dateModified']:'';
                    $parsedAmpHtml['var']['data.datePublished']=isset($microdata['datePublished'])?$microdata['datePublished']:'';

                    //print_R($parsedAmpHtml['var'])  ;
                    //$relateposts=$this->app->db->findOne('addons/posts',['gid'=>$post['gid']]);

                    $detailAmpHtml=$this->app->module('builds')->parseAngularHtml($parsedAmpHtml); 

                    //render                
                    if(strpos($ampHtml[$post['lang']]['body'], "id='".$fileTemplateName."Controller'>")!==false)             
                        $detailAmpHtml=str_replace("<div id='".$fileTemplateName."Controller'></div>", $detailAmpHtml, $ampHtml[$post['lang']]['body']);
                    else 
                        $detailAmpHtml=str_replace("<div id='DetailController'></div>", $detailAmpHtml, $ampHtml[$post['lang']]['body']);
                    $detailAmpHtml=preg_replace('/<body(.*?)>/','<body$1>'.$detailAmpHtml,$ampHtml[$post['lang']]['html']);

                    //add amp style                   
                    if(strpos($detailAmpHtml, '<style amp-custom />')){
                        $csscontent=$this->app->module('builds')->getTemplateFile($post['lang'],'/css/amp.css');
                        $csscontent=$this->app->module('builds')->CSSminify($csscontent);
                        $detailAmpHtml=str_replace('<style amp-custom />','<style amp-custom>'.$csscontent.'</style>',$detailAmpHtml);

                    }

                    //add amp meta
                    $detailAmpHtml=$this->app->module("builds")->addMetadata($detailAmpHtml,$metadata); 

                    //add language ref
                    $detailAmpHtml=$this->app->module("builds")->addLangs($detailAmpHtml,$post['lang'],isset($post['langslinkamp'])?$post['langslinkamp']:[],'',1);

                    //replace sitesize                
                    foreach ($config['arrSize'] as $key => $sizepage) {
                        $detailAmpHtml=preg_replace('/\[\[sitesize.'.$key.'\]\]/',array_values($config['arrSize']['detail'])[3],$detailAmpHtml);  
                    }

                    //write html file           
                    $filename=$firstchar.'/'.$post['slug'].'-'.$post['amp'].'/index.html';
                    $cachedatafile=COCKPIT_DIR.'/output/'.$filename;
                    file_put_contents($cachedatafile, $detailAmpHtml);            
                    $uploadFiles[]= './'.$filename;
                }
                $this->app->module("builds")->pushfile($uploadFiles);
            }

        }
        
        
        return true;
        
    },
    
    "send_message"=>function($id, $message, $progress){
        $d = array('message' => $message , 'progress' => $progress);
        echo "id: $id".PHP_EOL;
        echo "data: " . json_encode($d) . PHP_EOL;
        echo PHP_EOL;
        ob_flush();
        flush();
    },
    
    "cutstring"=>function($string, $limit = 100){
        //print_r($string);exit;
        // Return early if the string is already shorter than the limit
        if(strlen($string) < $limit) {return $string;}

        $regex = "/(.{1,$limit})\b/si";
        preg_match($regex, $string, $matches);

        return $matches[1].'...';
    }
]);

// extend lexy parser
$app->renderer->extend(function($content) use($moduleName){
    $content = preg_replace('/(\s*)@cutstring\?\((.+?)\)/', '$1<?php if ($app->module("'.$moduleName.'")->cutstring($2)) { ?>', $content);
    return $content;
});

//dashboard widget
$app->on("admin.dashboard.main", function() use($moduleName) {
    $action='dashboard';
    $this->renderView("$moduleName:views/dashboard.php",compact('moduleName','action'));
}, 6);

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
        $app("admin")->menu("top", [
            "url"    => $app->routeUrl("/"),
            "label"  => '<i class="uk-icon-home"></i>',
            "title"  => $app("i18n")->get("home")
        ]);

      
        $app("admin")->menu("top", [
            "url"    => $app->routeUrl("/$moduleName/item"),
            "label"  => '<i class="uk-icon-plus"></i>',
            "title"  => $app("i18n")->get("create post")
        ], 1);
        
        
    });
    
}


<?php

namespace plugins\Controller;

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

    public function build(){

        set_time_limit(36000);
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        $procStatus=[];
        $buildname = $this->app->param("buildname", null);  
        $overwrite=0; 
        if($buildname=="Build Plugin Overwrite")$overwrite=1; 
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
        $result=true;        
       
        $config=$this->module('builds')->getConfig();
        //print_r($module);exit;
        $html=$this->module("builds")->buildHtml();

       
            $this->buildPluginStatus($html,$config,$i,$overwrite);
        

        

        
        
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
   
    public function buildPluginStatus($html,$config,$i=0,$overwrite=0){
        //plugin count
        $count=5;
        $plugin=[];
        $lang='vi';

       
     //    //build plugin  - relateCat
        $pluginname='relateCat';        
        $pluginhtml=$this->app->module('builds')->getTemplateFile($lang,'/pluginHtml/'.$pluginname.'.html');  
        $i=0;      
       
            //check if not found template then skip next plugin
            if(empty($pluginhtml)){
                $this->app->module('builds')->sendMsg('MESSAGE','Plugin <b>'.$pluginname.'</b> not found');
                $this->app->module('builds')->sendMsg('MESSAGE','<b>Skip '.$pluginname.'</b><br />',($i+1).'/'.$count,'red');
            }
            else{
                $percent=$i.'/'.$count;
                $this->app->module('builds')->sendMsg('MESSAGE','Bulding plugin <b>'.$pluginname.'</b>:');


                $uploadFiles=[];
                $rt=[];
                
                //print_r($catnames);exit;
                $this->app->module('builds')->sendMsg('MESSAGE','Get all groups ... ','','',1);
                $groups=$this->app->db->find('addons/groups');
                $groupcount=count($groups);
                
                $this->app->module('builds')->sendMsg('MESSAGE','<b>'.$groupcount.' groups</b>');
                foreach ($groups as $key => $g) {
                    $grouppercent=$percent.'+'.$key.'/'.$groupcount.'/'.$count;
                    //check status:
                    @session_start();
                    $procStatus=$this->app->db->findOne('status/buildprocess',['procName'=>'Build Plugin']);                
                    if($procStatus['isstopbyuser']==1){
                        $procStatus['isstopbyuser']=0;
                        $procStatus['isBuilding']=0;
                        $this->app->db->update("status/buildprocess",['_id'=>$procStatus['_id']],$procStatus);
                        $this->app->module('builds')->sendMsg('STOP','Stopped by user!','','red');                                        
                        exit;
                    }                    
                    session_write_close();

                    $this->app->module('builds')->sendMsg('MESSAGE','Getting posts in group <b>'.$g['name'].'</b>...','','',1);
                    $group=[];
                    $group['name']=$g['name'];
                    $curP=$this->app->db->find('addons/posts',['filter'=>['gid'=>$g['_id'],'publish'=>1]]);
                    $posts=[];
					//hardcode focus post -- hinh-anh-tung-giai-doan-phat-trien-cua-thai-nhi
					// $hardPost=$this->app->db->findOne('addons/posts',['slug'=>'hinh-anh-tung-giai-doan-phat-trien-cua-thai-nhi']);
					// $p=[];
					// $p['title']=$hardPost['title'];
					// $p['slug']=$hardPost['slug'];
					
					// if(isset($hardPost['featureimage'])){
					// 	$feature_image=$this->app->module("builds")->sitebaseurl().str_replace("site:",$this->app->pathToUrl('site:'),$hardPost['featureimage']);                               
					// 	$files=$this->app->module("builds")->getCacheImage($feature_image, $hardPost['slug'],$config['arrSize']['relate']);
					// 	$p['featureimage']=$files['filename'];  
					// 	$p['featureimagetype']=$files['type'];
					// 	$uploadFiles=array_merge($uploadFiles,$files['arrFiles']);                        
					// }
					// $posts[]=$p;                 
					
					
                    $postcount=count($curP);
                    $this->app->module('builds')->sendMsg('MESSAGE',' <b>Done</b>');
                    foreach ($curP as $key2 => $post) { 
                        $postPercent=$grouppercent.'+'.($key2+1).'/'.$postcount.'/'.$groupcount.'/'.$count;
                        //check status:
                        @session_start();
                        $procStatus=$this->app->db->findOne('status/buildprocess',['procName'=>'Build Plugin']);                
                        if($procStatus['isstopbyuser']==1){
                            $procStatus['isstopbyuser']=0;
                            $procStatus['isBuilding']=0;
                            $this->app->db->update("status/buildprocess",['_id'=>$procStatus['_id']],$procStatus);
                            $this->app->module('builds')->sendMsg('STOP','Stopped by user!','','red');                                        
                            exit;
                        }                    
                        session_write_close();

                        $this->app->module('builds')->sendMsg('MESSAGE','Building posts <b>'.$post['title'].'</b>...','','',1);

                        $p['title']=$post['title'];
                        $p['description']=$post['description'];
                        $p['slug']=$post['slug'];
                        
                        if(isset($post['featureimage'])){
                            $feature_image=str_replace("site:",$this->app->pathToUrl('site:'),$post['featureimage']);                               
                            $feature_image_path=COCKPIT_DIR.str_replace(COCKPIT_APP_PATH, '', $feature_image);                            
                            $files=$this->app->module("builds")->getCacheImage($feature_image_path, $post['slug'],$config['arrSize']['relate'],0,$overwrite);
                            $p['featureimage']=$files['filename'];  
                            $p['featureimagetype']=$files['type'];
                            $uploadFiles=array_merge($uploadFiles,$files['arrFiles']);                        
                        }
                        $posts[]=$p;                        
                        $this->app->module('builds')->sendMsg('MESSAGE',' <b>Done</b>',$postPercent);
                        
                    }
                    $plugin['name']=$pluginname;
                    $plugin['limit']=$config['norelateitem'];

                    $pluginCatFile='./input/pluginHtml/'.$this->app->module('builds')->camelize('relate_'.$g['slug']).'Cat.html';                     
                    if(file_exists($pluginCatFile)){
                        $this->app->module('builds')->sendMsg('MESSAGE',' Render template '.$pluginCatFile,'','blue');
                        $plugin['html']=file_get_contents($pluginCatFile);
                    }
                    else{
                        $this->app->module('builds')->sendMsg('MESSAGE',' Render template relateCat.html','','blue');
                        $plugin['html']=$pluginhtml;
                    }
                    $plugin['content']=[['data'=>['posts'=>$posts],'name'=>'data']];
                    //write to file
                    
                    $filename=str_replace(['=','/'], '', base64_encode($pluginname.$g['_id']));
                    $foldername=str_replace(['=','/'], '', base64_encode('plugins'));
                    //check folder exist:
                    if (!file_exists('./output/'.$foldername)) {
                        mkdir("./output/" . $foldername);
                    }
                    $cachedatafile=COCKPIT_DIR.'/output/'.$foldername.'/'.$filename;

                    $uploadFiles[]='./'.$foldername.'/'.$filename;
                    $myfile = fopen($cachedatafile, "w") or die("Unable to open file!");   
                    //print_r($rt);                 
                    fwrite($myfile,base64_encode(gzcompress(json_encode($plugin),9)));
                    fclose($myfile);

                    $this->app->module('builds')->sendMsg('MESSAGE','Building group <b>'.$g['name'].'</b> done<br />');
                    
                }
               
                $this->app->module('builds')->sendMsg('MESSAGE','Uploading files ...','','',1);
                 
                $this->app->module("builds")->pushfile($uploadFiles,$config);
                $this->app->module('builds')->sendMsg('MESSAGE',' <b>Done</b>');
                $this->app->module('builds')->sendMsg('MESSAGE','<b>Building Plugin '.$pluginname.' DONE</b><br />',($i+1).'/'.$count);  
            }
        $i++;   
            
       

        //build plugin  - homeData
        $pluginname='homeData';        
        $pluginhtml=$this->app->module('builds')->getTemplateFile($lang,'/pluginHtml/'.$pluginname.'.html');         
                
        
        //check if not found template then skip next plugin
        if(empty($pluginhtml)){
            $this->app->module('builds')->sendMsg('MESSAGE','Plugin <b>'.$pluginname.'</b> not found');
            $this->app->module('builds')->sendMsg('MESSAGE','<b>Skip '.$pluginname.'</b><br />',($i+1).'/'.$count,'red');
        }
        else{
            $plugin=[];
            $uploadFiles=[];
            $percent=$i.'/'.$count;
            $this->app->module('builds')->sendMsg('MESSAGE','Bulding plugin <b>'.$pluginname.'</b>:');


            $curP=$this->app->db->find('addons/posts',['filter'=>['home'=>1,'publish'=>1],'limit'=>$config['noitemhome']])->toArray();
            
            $posts=[];
            $postcount=count($curP);
            $this->app->module('builds')->sendMsg('MESSAGE',' <b>Done</b>');
            foreach ($curP as $key2 => $post) { 
                $postPercent=$percent.'+'.$key2.'/'.$postcount.'/'.$count;
                //check status:
                @session_start();
                $procStatus=$this->app->db->findOne('status/buildprocess',['procName'=>'Build Plugin']);                
                if($procStatus['isstopbyuser']==1){
                    $procStatus['isstopbyuser']=0;
                    $procStatus['isBuilding']=0;
                    $this->app->db->update("status/buildprocess",['_id'=>$procStatus['_id']],$procStatus);
                    $this->app->module('builds')->sendMsg('STOP','Stopped by user!','','red');                                        
                    exit;
                }                    
                session_write_close();

                $this->app->module('builds')->sendMsg('MESSAGE','Building posts <b>'.$post['title'].'</b>...','','',1);

                $p['title']=$post['title'];
                $p['description']=$post['description'];
                $p['slug']=$post['slug'];
                $p['price']=isset($post['price'])?$post['price']:0;
                $p['code']=isset($post['code'])?$post['code']:'';
                
                if(isset($post['featureimage'])){
                    $feature_image=str_replace("site:",$this->app->pathToUrl('site:'),$post['featureimage']);                               
                    $feature_image_path=COCKPIT_DIR.str_replace(COCKPIT_APP_PATH, '', $feature_image);                            
                    $files=$this->app->module("builds")->getCacheImage($feature_image_path, $post['slug'],$config['arrSize']['home'],0,$overwrite);
                    $p['featureimage']=$files['filename'];  
                    $p['featureimagetype']=$files['type'];
                    $uploadFiles=array_merge($uploadFiles,$files['arrFiles']);                        
                }
                $posts[]=$p;                        
                $this->app->module('builds')->sendMsg('MESSAGE',' <b>Done</b>',$postPercent);
                
            }



            $plugin['name']=$pluginname;
            //$plugin['limit']=$config['norelateitem'];
            $plugin['html']=$pluginhtml;
            $plugin['content']=[['data'=>['posts'=>$posts],'name'=>'data']];
            //write to file

            $filename=str_replace(['=','/'], '', base64_encode($pluginname.$lang));
            $foldername=str_replace(['=','/'], '', base64_encode('plugins'));
            //check folder exist:
            if (!file_exists('./output/'.$foldername)) {
                mkdir("./output/" . $foldername);
            }                
            $cachedatafile=COCKPIT_DIR.'/output/'.$foldername.'/'.$filename;

            $uploadFiles[]='./'.$foldername.'/'.$filename;
            $myfile = fopen($cachedatafile, "w") or die("Unable to open file!");   
            //print_r($rt);                 
            fwrite($myfile,base64_encode(gzcompress(json_encode($plugin),9)));
            fclose($myfile);

           
            $this->app->module('builds')->sendMsg('MESSAGE','Uploading files ...','','',1);
             
            $this->app->module("builds")->pushfile($uploadFiles,$config);
            $this->app->module('builds')->sendMsg('MESSAGE',' <b>Done</b>');
            $this->app->module('builds')->sendMsg('MESSAGE','<b>Building Plugin '.$pluginname.' DONE</b><br />',($i+1).'/'.$count);  
        }
        $i++;

           
            
       

       //build plugin  - homeData
        $pluginname='bestData';        
        $pluginhtml=$this->app->module('builds')->getTemplateFile($lang,'/pluginHtml/homeData.html');         
               
        
        //check if not found template then skip next plugin
        if(empty($pluginhtml)){
            $this->app->module('builds')->sendMsg('MESSAGE','Plugin <b>'.$pluginname.'</b> not found');
            $this->app->module('builds')->sendMsg('MESSAGE','<b>Skip '.$pluginname.'</b><br />',($i+1).'/'.$count,'red');
        }
        else{
            $plugin=[];
            $uploadFiles=[];
            $percent=$i.'/'.$count;
            $this->app->module('builds')->sendMsg('MESSAGE','Bulding plugin <b>'.$pluginname.'</b>:');


            $curP=$this->app->db->find('addons/posts',['filter'=>['best'=>1,'publish'=>1],'limit'=>$config['noitemhome']])->toArray();
            
            $posts=[];
            $postcount=count($curP);
            $this->app->module('builds')->sendMsg('MESSAGE',' <b>Done</b>');
            foreach ($curP as $key2 => $post) { 
                $postPercent=$percent.'+'.$key2.'/'.$postcount.'/'.$count;
                //check status:
                @session_start();
                $procStatus=$this->app->db->findOne('status/buildprocess',['procName'=>'Build Plugin']);                
                if($procStatus['isstopbyuser']==1){
                    $procStatus['isstopbyuser']=0;
                    $procStatus['isBuilding']=0;
                    $this->app->db->update("status/buildprocess",['_id'=>$procStatus['_id']],$procStatus);
                    $this->app->module('builds')->sendMsg('STOP','Stopped by user!','','red');                                        
                    exit;
                }                    
                session_write_close();

                $this->app->module('builds')->sendMsg('MESSAGE','Building posts <b>'.$post['title'].'</b>...','','',1);

                $p['title']=$post['title'];
                $p['description']=$post['description'];
                $p['slug']=$post['slug'];
                $p['price']=isset($post['price'])?$post['price']:0;
                $p['code']=isset($post['code'])?$post['code']:'';
                
                if(isset($post['featureimage'])){
                    $feature_image=str_replace("site:",$this->app->pathToUrl('site:'),$post['featureimage']);                               
                    $feature_image_path=COCKPIT_DIR.str_replace(COCKPIT_APP_PATH, '', $feature_image);                            
                    $files=$this->app->module("builds")->getCacheImage($feature_image_path, $post['slug'],$config['arrSize']['relate'],0,$overwrite);
                    $p['featureimage']=$files['filename'];  
                    $p['featureimagetype']=$files['type'];
                    $uploadFiles=array_merge($uploadFiles,$files['arrFiles']);                        
                }
                $posts[]=$p;                        
                $this->app->module('builds')->sendMsg('MESSAGE',' <b>Done</b>',$postPercent);
                
            }



            $plugin['name']=$pluginname;
            //$plugin['limit']=$config['norelateitem'];
            $plugin['html']=$pluginhtml;
            $plugin['content']=[['data'=>['posts'=>$posts],'name'=>'data']];
            //write to file

            $filename=str_replace(['=','/'], '', base64_encode($pluginname.$lang));
            $foldername=str_replace(['=','/'], '', base64_encode('plugins'));
            //check folder exist:
            if (!file_exists('./output/'.$foldername)) {
                mkdir("./output/" . $foldername);
            }                
            $cachedatafile=COCKPIT_DIR.'/output/'.$foldername.'/'.$filename;

            $uploadFiles[]='./'.$foldername.'/'.$filename;
            $myfile = fopen($cachedatafile, "w") or die("Unable to open file!");   
            //print_r($rt);                 
            fwrite($myfile,base64_encode(gzcompress(json_encode($plugin),9)));
            fclose($myfile);

           
            $this->app->module('builds')->sendMsg('MESSAGE','Uploading files ...','','',1);
             
            $this->app->module("builds")->pushfile($uploadFiles,$config);
            $this->app->module('builds')->sendMsg('MESSAGE',' <b>Done</b>');
            $this->app->module('builds')->sendMsg('MESSAGE','<b>Building Plugin '.$pluginname.' DONE</b><br />',($i+1).'/'.$count);  
        }
        $i++;



        //build plugin  - menuCat
        $pluginname='menuCat';        
        $pluginhtml=$this->app->module('builds')->getTemplateFile($lang,'/pluginHtml/menuCat.html');         
               
        
        //check if not found template then skip next plugin
        if(empty($pluginhtml)){
            $this->app->module('builds')->sendMsg('MESSAGE','Plugin <b>'.$pluginname.'</b> not found');
            $this->app->module('builds')->sendMsg('MESSAGE','<b>Skip '.$pluginname.'</b><br />',($i+1).'/'.$count,'red');
        }
        else{
            $plugin=[];
            $uploadFiles=[];
            $percent=$i.'/'.$count;
            $this->app->module('builds')->sendMsg('MESSAGE','Bulding plugin <b>'.$pluginname.'</b>:');

            $rt=[];
            
            //print_r($catnames);exit;
            $groups=$this->app->db->find('addons/groups',['filter'=>['publish'=>1]]);
            $groupcount=count($groups);
            
            foreach ($groups as $key => $g) {
                $grouppercent=$percent.'+'.$key.'/'.$groupcount.'/'.$count;
                //check status:
                @session_start();
                $procStatus=$this->app->db->findOne('status/buildprocess',['procName'=>'Build Plugin']);                
                if($procStatus['isstopbyuser']==1){
                    $procStatus['isstopbyuser']=0;
                    $procStatus['isBuilding']=0;
                    $this->app->db->update("status/buildprocess",['_id'=>$procStatus['_id']],$procStatus);
                    $this->app->module('builds')->sendMsg('STOP','Stopped by user!','','red');                                        
                    exit;
                }                    
                session_write_close();

                
                $group=[];
                $group['name']=$g['name'];                
                $group['slug']=$g['slug'];
                $group['content']=$g['content'];
                if(isset($g['featureimage'])){
                    $feature_image=str_replace("site:",$this->app->pathToUrl('site:'),$g['featureimage']);                               
                    $feature_image_path=COCKPIT_DIR.str_replace(COCKPIT_APP_PATH, '', $feature_image);                            
                    $files=$this->app->module("builds")->getCacheImage($feature_image_path, $g['slug'],$config['arrSize']['home'],0,$overwrite);
                    $group['featureimage']=$files['filename'];  
                    $group['featureimagetype']=$files['type'];
                    $uploadFiles=array_merge($uploadFiles,$files['arrFiles']);                        
                }

                $rt[]=$group;
                $grouppercent=$percent.'+'.($key+1).'/'.$groupcount.'/'.$percent;
                $this->app->module('builds')->sendMsg('MESSAGE','Building group <b>'.$g['name'].'</b> done',$grouppercent);
            }
            $plugin['name']=$pluginname;
            $plugin['html']=$pluginhtml;
            $plugin['content']=[['data'=>['cats'=>$rt],'name'=>'data']];
            $this->app->module('builds')->sendMsg('MESSAGE','Uploading files ...','','',1);
            $filename=str_replace(['=','/'], '', base64_encode($pluginname.$lang));
            $foldername=str_replace(['=','/'], '', base64_encode('plugins'));
            //check folder exist:
            if (!file_exists('./output/'.$foldername)) {
                mkdir("./output/" . $foldername);
            }
            $cachedatafile=COCKPIT_DIR.'/output/'.$foldername.'/'.$filename;
            $uploadFiles[]='./'.$foldername.'/'.$filename;
            $myfile = fopen($cachedatafile, "w") or die("Unable to open file!");   
            //print_r($rt);                 
            fwrite($myfile,base64_encode(gzcompress(json_encode($plugin),9)));
            fclose($myfile); 
            $this->app->module("builds")->pushfile($uploadFiles,$config);
            $this->app->module('builds')->sendMsg('MESSAGE',' <b>Done</b>',$postPercent);
            $this->app->module('builds')->sendMsg('MESSAGE','<b>Building Plugin '.$pluginname.' DONE</b><br /><br />',($i+1).'/'.$count);  
        }
        $i++;



        //build plugin  - DATA FOR CART
        $pluginname='dataCart';        
        $pluginhtml=$this->app->module('builds')->getTemplateFile($lang,'/pluginHtml/dataCart.html');         
               
        
        //check if not found template then skip next plugin
        if(empty($pluginhtml)){
            $this->app->module('builds')->sendMsg('MESSAGE','Plugin <b>'.$pluginname.'</b> not found');
            $this->app->module('builds')->sendMsg('MESSAGE','<b>Skip '.$pluginname.'</b><br />',($i+1).'/'.$count,'red');
        }
        else{
        
            $plugin=[];
            $uploadFiles=[];
            $percent=$i.'/'.$count;
            $this->app->module('builds')->sendMsg('MESSAGE','Bulding plugin <b>'.$pluginname.'</b>:');


            $curP=$this->app->db->find('addons/posts',['filter'=>['publish'=>1,'microdata'=>'Product']])->toArray();
            
            $posts=[];
            $postindex=[];
            $postcount=count($curP);
            $this->app->module('builds')->sendMsg('MESSAGE',' <b>Done</b>');
            foreach ($curP as $key2 => $post) { 
                if(!isset($post['code']))continue;
                $p=[];
                $postPercent=$percent.'+'.$key2.'/'.$postcount.'/'.$count;
                //check status:
                @session_start();
                $procStatus=$this->app->db->findOne('status/buildprocess',['procName'=>'Build Plugin']);                
                if($procStatus['isstopbyuser']==1){
                    $procStatus['isstopbyuser']=0;
                    $procStatus['isBuilding']=0;
                    $this->app->db->update("status/buildprocess",['_id'=>$procStatus['_id']],$procStatus);
                    $this->app->module('builds')->sendMsg('STOP','Stopped by user!','','red');                                        
                    exit;
                }                    
                session_write_close();

                $this->app->module('builds')->sendMsg('MESSAGE','Building posts <b>'.$post['title'].'</b>...','','',1);

                $p['name']=$post['title'];                
                $p['slug']=$post['slug'];
                $p['price']=isset($post['price'])?$post['price']:0;

                //check featureimage change:
                if(isset($post['featureimage'])){
                    $feature_image =$post['featureimage'];
                    $feature_image=str_replace("site:",$this->app->pathToUrl('site:'),$feature_image);                
                    $feature_image_path=COCKPIT_DIR.str_replace(COCKPIT_APP_PATH.'/', '', $feature_image);
                    
                    $files=$this->app->module("builds")->getCacheImage($feature_image_path, $post['slug'],$config['arrSize']['cart'],0,$overwrite);
                    $uploadFiles=array_merge($uploadFiles,$files['arrFiles']); 
                    
                }
                
                $posts[$post['code']]=$p;  
                $postindex[]=$post['code'];
                $this->app->module('builds')->sendMsg('MESSAGE',' <b>Done</b>',$postPercent);
                
            }



            $plugin['name']=$pluginname;
            $plugin['html']=$pluginhtml;
            //$plugin['limit']=$config['norelateitem'];            
            $plugin['content']=[['data'=>['posts'=>$posts,'postindex'=>$postindex],'name'=>'data']];
            //write to file

            $filename=str_replace(['=','/'], '', base64_encode($pluginname.$lang));
            $foldername=str_replace(['=','/'], '', base64_encode('plugins'));
            //check folder exist:
            if (!file_exists('./output/'.$foldername)) {
                mkdir("./output/" . $foldername);
            }                
            $cachedatafile=COCKPIT_DIR.'/output/'.$foldername.'/'.$filename;

            $uploadFiles[]='./'.$foldername.'/'.$filename;
            $myfile = fopen($cachedatafile, "w") or die("Unable to open file!");   
            //print_r($rt);                 
            fwrite($myfile,base64_encode(gzcompress(json_encode($plugin),9)));
            fclose($myfile);

           
            $this->app->module('builds')->sendMsg('MESSAGE','Uploading files ...','','',1);
            $this->app->module("builds")->pushfile($uploadFiles,$config);
            $this->app->module('builds')->sendMsg('MESSAGE',' <b>Done</b>',$postPercent);
            $this->app->module('builds')->sendMsg('MESSAGE','<b>Building Plugin '.$pluginname.' DONE</b><br /><br />',($i+1).'/'.$count);  
        }
        $i++;

        
    }

    
}
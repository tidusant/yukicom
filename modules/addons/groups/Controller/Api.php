<?php

namespace groups\Controller;

class Api extends \Cockpit\Controller {
    private $moduleName='';
    public function __construct($app) {
        parent::__construct($app);
        //duy.ha: get path to detect module name
        $pathName=explode("\\",__DIR__);
        if(count($pathName)<=1)$pathName=explode('/',__DIR__);
        $moduleName=$pathName[count($pathName)-2];
        $this->moduleName=$moduleName;        
    }


    public function find(){
        $rt=[];
        
        //check acl and role
        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.index'))return '{"error":"not permission"}';
       
        
        $filter = $this->param("filter", null);
        $config=$this->app->module("builds")->getConfig();

        //check acl and role
        $items=[];
        
       
        //get groups
       $items= $this->app->module('groups')->getTree(null,null);
       //remove first
       
        
        

        

        
        //get all gacode
        //print_r($doc);
        
        //print_r($gacodes->toArray());
        $flags=\MyCommon\Common::listCountryFlag();       
        $data['listCountryFlag']=$flags;
        return json_encode(['items'=>$items,'showlang'=>$config['showlang'],
                                    'defaultlang'=>$config['defaultlang'], 
                                    'listCountryFlag'=>$flags,                                   
                                    'langs'=>$config['langs'],
                                    ]);
    
    }


    public function getdata(){
        
        $filter = $this->param("filter", null);
        $config=$this->app->module("builds")->getConfig();

        //check acl and role
        $items=[];
        $currentlang=$config['defaultlang'];
        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.edit'))return '{"error":"not permission"}';
        if(isset($filter['_id']) && \MongoId::isValid($filter['_id']) ){
            $doc = $this->app->db->findOne("addons/$this->moduleName", $filter);        
            if(!empty($doc)){
                //get group
                $doc['group']=$this->app->db->findOne("addons/$this->moduleName", ['_id'=>$doc['gid']]);
                if(!isset($doc['lang']))$doc['lang']=$config['defaultlang'];
                $currentlang=$doc['lang'];
                $items[$doc['lang']]=$doc;    
                //get other data lang
                if(isset($doc['langs'])  && $config['showlang']){
                    foreach ($doc['langs'] as $key => $value) {
                        $doclang = $this->app->db->findOne("addons/$this->moduleName", ['_id'=>$value]);
                        if(!empty($doclang)){
                            $doclang['group']=$this->app->db->findOne("addons/$this->moduleName", ['_id'=>$doclang['gid']]);

                            $items[$doclang['lang']]=$doclang;    
                        }
                    }
                }
            }
        }

        //get groups
       $groups= $this->app->module('groups')->getTree($items);
        
        //get all gacode
        //print_r($doc);
        
        //print_r($gacodes->toArray());
        $flags=\MyCommon\Common::listCountryFlag();       
        $data['listCountryFlag']=$flags;
        
        return json_encode(['items'=>$items,'groups'=>$groups,'showlang'=>$config['showlang'],
                                    'defaultlang'=>$config['defaultlang'],
                                    'listCountryFlag'=>$flags,
                                    'currentlang'=>$currentlang,
                                    'langs'=>$config['langs'],
                                    ]);
    }
    
       


    public function save() {
        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.edit'))return '{"error":"not permission"}';
        $config=$this->app->module("builds")->getConfig();
        $data = $this->param("data", null);
        $id = $this->param("_id", null);
        $overwrite = $this->param("overwrite", 0); 
        $currentlang = $this->param("currentlang", null);
        $langs=[];
        $langslink=[];
        $langsiso=[];
        $langsname=[];
        //print_r($data);exit;
        $items=[];
        $builditemids=[];
        //print_r($data);exit;
        $listLocale=\MyCommon\Common::listLocale();    
        $listCountryCode=array_keys($listLocale);
        $listCountry=\MyCommon\Common::listCountry();
        foreach ($data as $item) {

            if(!empty($item['name'])){
                $olditem=null;
                if(isset($item['_id']))$olditem=$this->app->db->findOne("addons/$this->moduleName", ['_id'=>$item['_id']]);

                //check slug
                if(empty($item['slug']))$item['slug']=$this->app->module("commons")->createslug($item['name']);
                $i=0; 
                
                $slug=$item['slug'];           
                do{
                    $itemdup=null;                    
                    $itemdup=$this->app->db->findOne("addons/$this->moduleName", ['slug'=>$slug]);
                    if($itemdup){
                        if(isset($item['_id']) && $itemdup['_id']==$item['_id'] && !in_array($slug, $listCountryCode))break;
                        $slug=$item['slug'].(++$i);
                        
                    }                
                }while($itemdup!=null && !empty($itemdup));
                $item['slug']=$slug;
                //fix image ulr
                if(isset($item['_id']))$item['_id']=new \MongoId($item['_id']);
                $item['content']=str_replace('src="../uploaded', 'src="../../uploaded', $item['content']);
                
                //update
                if(!isset($item['uid']))$item['uid']=$this->user['_id'];
                if(!isset($item['created']))$item['created']=time();
                $item['modified']=time();
                //remove group object
                if(isset($item['group'])){
                    $item['gid']=$item['group']['_id'];                    
                    unset($item['group']);
                }
                //check if new item
                if(!isset($item['_id'])){
                    if($item['gid']!=0)$builditemids[]=$item['gid'];
                }
                else{
                    if($olditem['publish']!=$item['publish'])
                        //check parent
                        if($item['gid']!=0)$builditemids[]=$item['gid'];
                }

                //find and replace image in content
                $content= $item['content'];

                //get all image in content and save to host if is new
                preg_match_all('/<img.*?(src=["\'](.*?)["\']).*?>/is', $content, $matches);

                //print_r($matches);
                if(isset($matches[2])){            

                    foreach($matches[2] as $k=>$img){

                        //sync with editor:
                        $syncimg=str_replace($this->app->module("builds")->sitebaseurl().COCKPIT_BASE_URL,'../..',$img);
                        //print_r($syncimg);exit;
                        $isnewimage=1;                    
                        if(strpos($syncimg, '://')>5 || strpos($syncimg, '://')===false){                        
                            $syncimg=$this->app->module("builds")->sitebaseurl().COCKPIT_BASE_URL.'/'.str_replace('../../','',$syncimg);                           

                            $isnewimage=0;
                        }
                        // print_r($isnewimage);
                        // print_r($syncimg);exit;
                        
                        if($isnewimage){
                        //save image into uploaded
                            
                            $year=date("Y");
                            $month=date("m");
                            @mkdir('./uploaded/'.$year);
                            @mkdir('./uploaded/'.$year.'/'.$month);

                            $fullfilename=preg_replace('/-(\d+)x(\d+)\.(\w+)$/i','.${3}',$syncimg);
                            $fullfilename=preg_replace('/https?:\/\//i','',$fullfilename);
                            $domainname=substr($fullfilename,0,strpos($fullfilename, '/'));
                            $arrfilename=explode('.',substr($fullfilename,strrpos($fullfilename, '/')+1));
                            
                            $filename=$this->app->module('commons')->createslug($arrfilename[count($arrfilename)-2]);
                            $type=strtolower('.'.$arrfilename[count($arrfilename)-1]);
                           
                            $newfilename=$domainname.'-'.$filename.$type;
                            $file_name='./uploaded/'.$year.'/'.$month.'/'.$newfilename; 
                            //check file is exist
                            $i=1;
                            while(file_exists($file_name)){
                                $newfilename=$domainname.'-'.$filename.$i.$type;
                                $i++;
                                $file_name='./uploaded/'.$year.'/'.$month.'/'.$newfilename;
                            }   
                            file_put_contents(COCKPIT_DIR.'/'.$file_name,file_get_contents($syncimg));                      
                            $content=str_replace($syncimg, '../.'.$file_name, $content);                        
                        }
                    }
                    $item['content']=$content;                    
                }

                //amp code
                if(!isset($item["amp"]) || empty($item["amp"])){
                    $item["amp"]=$this->app->module('builds')->create_random_string(5);
                }

                //default lang
                if(!isset($item["lang"]) || empty($item["lang"])){
                    $item["lang"]=$config['defaultlang'];
                }





                
                //print_r($item);exit;
               $this->app->db->save('addons/'.$this->moduleName,$item);   
               $items[$item['lang']]=$item;
               $langs[$item['lang']]=$item['_id'];
               if($item['publish']){                    
                    $builditemids[]=$item['_id'];                    
                    $langslink[$item['lang']]=$item['slug'].'/';
                    $langsiso[$item['lang']]=$listLocale[$item['lang']];
                    $langsname[$item['lang']]=$listCountry[$item['lang']];
                }
                


            }
        }

        //update langs link id reference
        foreach ($items as $key => $item) {
            $tmplangs=$langs;
            unset($tmplangs[$item['lang']]);
            $item['langs']=$tmplangs;

            $tmplangsiso=$langsiso;
            unset($tmplangsiso[$item['lang']]);
            $item['langsiso']=$tmplangsiso;

            $tmplangsname=$langsname;
            unset($tmplangsname[$item['lang']]);
            $item['langsname']=$tmplangsname;

            $tmplangslink=$langslink;
            unset($tmplangslink[$item['lang']]);
            $item['langslink']=$tmplangslink;
            $this->app->db->save("addons/$this->moduleName",$item);  
            $items[$key]=$item;
            if($item['lang']==$currentlang)$id=$item['_id'];
        }

        if($config['isbuilddata']){
            $html=$this->module("builds")->buildHtml();  
            //print_r($builditemids); 
            if(count($builditemids)>0)$this->module('mycustom')->builddatabygroups($html,$builditemids,$overwrite);
        }

        

        return json_encode(['message'=>'Item\'s saved',
                            'result'=>'success',
                            '_id'=>$id
            ]);
    }

    public function remove() {
        $id = $this->param("id", null);
        $config=$this->app->module("builds")->getConfig();
        // $this->app->db->update("addons/$this->moduleName", ['gid'=>'578f454a50ad1f441800002b'],['gid'=>'1']);
        $builditemids=[];
        // exit;
        //check acl and role
        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.edit'))return '{"error":"not permission"}';
        $item=$this->app->db->findOne("addons/$this->moduleName", ["_id" => $id]);
        if($item) {           
            //get parent id:
            $gid=isset($item['gid'])?$item['gid']:0;
            if($gid!=0)$builditemids[]=$gid;
            //remove
            $this->app->db->remove("addons/$this->moduleName", ["_id" => $item['_id']]);
            //update child
            $childs=$this->app->db->find("addons/$this->moduleName", ["filter"=>['gid'=>$item['_id']]])->toArray();
            if($childs && count($childs)>0)
                foreach ($childs as $key => $child) {
                    $child['gid']=$gid;
                    $this->app->db->save("addons/$this->moduleName", $child);
                }
            //update post
            $childs=$this->app->db->find("addons/posts", ["filter"=>['gid'=>$item['_id']]])->toArray();
            if($childs && count($childs)>0)
                foreach ($childs as $key => $child) {
                    $child['gid']=$gid;
                    $this->app->db->save("addons/posts", $child);
                }
            
            //remove other lang
            if(isset($item['langs']) && count($item['langs'])>0){
                foreach ($item['langs'] as $key => $value) {
                    $item=$this->app->db->findOne("addons/$this->moduleName", ["_id" => $value]);
                    if($item) {           
                        //get parent id:
                        $gid=isset($item['gid'])?$item['gid']:0;
                        if($gid!=0)$builditemids[]=$gid;
                        //remove
                        $this->app->db->remove("addons/$this->moduleName", ["_id" => $item['_id']]);
                        //update child
                        $childs=$this->app->db->find("addons/$this->moduleName", ["filter"=>['gid'=>$item['_id']]])->toArray();
                        if($childs && count($childs)>0)
                            foreach ($childs as $key => $child) {
                                $child['gid']=$gid;
                                $this->app->db->save("addons/$this->moduleName", $child);
                            }
                    }
                }
            }

            if($config['isbuilddata']){
                $html=$this->module("builds")->buildHtml(); 
                if(count($builditemids)>0)$this->module('mycustom')->builddatabygroups($html,$builditemids);
            }
            
        }

        return $item ? '{"success":true}' : '{"success":false}';
    }
  

   
}
<?php

namespace posts\Controller;

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


    public function getAllProds(){
        $config=$this->app->module("builds")->getConfig();
        //check acl and role
        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.index'))return '{"error":"not permission"}';       
        $currentlang = $this->param("currentlang", $config['defaultlang']);
        if(empty($currentlang))$currentlang=$config['defaultlang'];
        $curs = $this->app->db->find("addons/$this->moduleName",['filter'=>['lang'=>$currentlang,'publish'=>1],'fields'=>['slug'=>1,'code'=>1,'title'=>1,'featureimage'=>1],'sort'=>['_id'=>-1]]);
        $rt=[];
        $rtindex=[];
        foreach ($curs as $key => $value) {
            $rt[]=$value;
            $rtindex[]=$value['slug'];
        }
        return json_encode(['items'=>$rt,'itemsindex'=>$rtindex]);
    }

    public function find(){
        $rt=[];
        
        $config=$this->app->module("builds")->getConfig();
        //check acl and role
        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.index'))return '{"error":"not permission"}';
        $page = $this->param("page", null);
        $search = $this->param("search", null);
        $groupid = $this->param("groupid", null);
        $currentlang = $this->param("currentlang", $config['defaultlang']);
        if(empty($currentlang))$currentlang=$config['defaultlang'];
        
        $isshowpublish = $this->param("isshowpublish", 1);
        $isshowunpublish = $this->param("isshowunpublish", 0);
        $isshowhome = $this->param("isshowhome", 0);
        
        //get page information:
        //unset($_SESSION[$this->moduleName]);
        if(!isset($_SESSION[$this->moduleName]))$_SESSION[$this->moduleName]=[];
        $moduleInfo=$_SESSION[$this->moduleName];
        if(!isset($moduleInfo['currentpage']))$moduleInfo['currentpage']=1;
        if(!isset($moduleInfo['noi']))$moduleInfo['noi']=50;
        if(!isset($moduleInfo['filter']))$moduleInfo['filter']=[];
        //set lang
        if(!empty($currentlang) && in_array($currentlang, $config['langs'])){
            $moduleInfo['filter']['lang']=$currentlang;
        }

        //update info from query
        if($page)$moduleInfo['currentpage']=$page;
        //check show home
        if($isshowhome){ 
            $moduleInfo['filter']['home']=1;
        }
        else{
            unset($moduleInfo['filter']['home']);
        }
        //check show publish
        if($isshowpublish && $isshowunpublish){ 
            unset($moduleInfo['filter']['publish']);
        }
        else if($isshowpublish){ 
           
             $moduleInfo['filter']['publish']=1;
        }
        else if($isshowunpublish){
            $moduleInfo['filter']['publish']=0;   
        }
        //check group id
        if($groupid>0){            
            $moduleInfo['filter']['gid']=$groupid;
            $moduleInfo['currentpage']=1;
        }
        else if($groupid==0){
            unset( $moduleInfo['filter']['gid']);
        }
        else if($groupid==-1){
            $moduleInfo['filter']['gid']=0;
            $moduleInfo['currentpage']=1;
        }

        if($search){            
            $moduleInfo['searchterms']=$search;
            $moduleInfo['filter']['slug']=['$regex'=>$this->app->module("commons")->createslug($search),'$options'=>'i'];

            $moduleInfo['currentpage']=1;
        }
        else if($search!==null){
            unset( $moduleInfo['filter']['slug']);
            unset( $moduleInfo['searchterms']);
        }
        //print_r($moduleInfo);exit;
        //$moduleInfo['currentpage']=1;
        

       

        $moduleInfo['itemcount']= $this->app->db->count("addons/$this->moduleName",$moduleInfo['filter']);
        if($moduleInfo['itemcount']>0){
            $moduleInfo['totalpage']=ceil($moduleInfo['itemcount']/$moduleInfo['noi']);
            if($moduleInfo['currentpage']>$moduleInfo['totalpage'])$moduleInfo['currentpage']=$moduleInfo['totalpage'];
             //save page information:
            //print_r($moduleInfo);exit;
            $_SESSION[$this->moduleName]=$moduleInfo;
            



            $rt = $this->app->db->find("addons/$this->moduleName",['filter'=>$moduleInfo['filter'],'fields'=>['content'=>0],'sort'=>['_id'=>-1],'skip'=>$moduleInfo['noi']*($moduleInfo['currentpage']-1),'limit'=>$moduleInfo['noi']])->toArray();
            //print_r($rt);
            //remove project not relate to user     
            // if is admin then skip

        }
        //get only field username of all user 
        $users=[];
        $curU = $this->app->db->find("cockpit/accounts",['fields'=>['user'=>1]]);   
        foreach ($curU as $key => $user) {
            $users[$user['_id']]=$user;
        } 

        //get group
        $groups= $this->app->module('groups')->getTree(null,null);
        $groups=$groups[$currentlang];

        $flags=\MyCommon\Common::listCountryFlag();       
        $data['listCountryFlag']=$flags;    
        return json_encode(['items'=>$rt,'groups'=>$groups,'showlang'=>$config['showlang'],
                                    'defaultlang'=>$config['defaultlang'],
                                    'currentlang'=>$currentlang,
                                    'listCountryFlag'=>$flags,
                                    'langs'=>$config['langs'],
                                    'users'=>$users,
                                    'moduleInfo'=>$moduleInfo
                                    ]);

        
    }

    public function getdata(){
        
        $filter = $this->param("filter", null);
        $config=$this->app->module("builds")->getConfig();
        //print_r($filter);exit;
        //check acl and role
        $items=[];
        $currentlang=$config['defaultlang'];
        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.edit'))return '{"error":"not permission"}';
        if(isset($filter['_id']) && \MongoId::isValid($filter['_id']) ){
            $doc = $this->app->db->findOne("addons/$this->moduleName", $filter);        
            if(!empty($doc)){
                //get group
                $doc['group']=$this->app->db->findOne("addons/groups", ['_id'=>$doc['gid']]);
                if(!isset($doc['lang']))$doc['lang']=$config['defaultlang'];
                $currentlang=$doc['lang'];
                $items[$doc['lang']]=$doc;    
                //get other data lang
                if(isset($doc['langs']) && $config['showlang']){
                    foreach ($doc['langs'] as $key => $value) {
                        $doclang = $this->app->db->findOne("addons/$this->moduleName", ['_id'=>$value]);
                        if( $doclang){
                            $doclang['group']=$this->app->db->findOne("addons/groups", ['_id'=>$doclang['gid']]);
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
                                    'currentlang'=>$currentlang,
                                    'listCountryFlag'=>$flags,
                                    'langs'=>$config['langs'],
                                    ]);
    }

    public function savePrice(){     
        set_time_limit(1000);   

        
        $item = $this->param("item", null);
        $currentlang = $this->param("currentlang", null);

        $overwrite = $this->param("overwrite", 0);  
        $config=$this->module('builds')->getConfig();
        $olditem=$this->app->db->findOne("addons/posts",["_id"=>$item["_id"]]);
        if(isset($item["noi"]))$olditem["noi"]=$item["noi"];
        if(isset($item["price"]))$olditem["price"]=$item["price"];
        if(isset($item["baseprice"]))$olditem["baseprice"]=$item["baseprice"];
        $this->app->db->save("addons/posts",$olditem);
        $html=$this->module("builds")->buildHtml();   
        $ampHtml=$this->module("builds")->buildHtml(1);   
        $this->module('mycustom')->builddatabyposts($html,$ampHtml,[$olditem["_id"]],$overwrite);
        //rebuild home data and push
        if(isset($olditem["best"]) || isset($olditem["home"]))
            $this->module('mycustom')->buildHome($html);
        // //rebuild cat data
        $cat=$this->app->db->findOne("addons/groups",["slug"=>$olditem["gid"]]);    
        if($cat)$this->module('mycustom')->builddatabygroups($html,[$cat["_id"]]);
        return 1;
    }
    
    public function save(){     
        set_time_limit(1000);   

        $data = $this->param("data", null);
        $id = $this->param("_id", null);
        $currentlang = $this->param("currentlang", null);

        $overwrite = $this->param("overwrite", 0);  

        $isbuildhome=0;
        $isbuildmenu=0;
        
        $buildcatids=[];
        $builditemids=[];
        $config=$this->module('builds')->getConfig();
        //get group
        $groupsById=[];
        $curG=$this->app->db->find('addons/groups');
        foreach ($curG as $key => $group) {
            $groupsById[$group['_id']]=$group;
        }
        //print_r($item);exit;
        $langs=[];
        $langslink=[];
        $langslinkamp=[];
        $langsiso=[];
        $langsname=[];
        //print_r($data);exit;
        $items=[];
        $listLocale=\MyCommon\Common::listLocale();    
        $listCountryCode=array_keys($listLocale);
        $listCountry=\MyCommon\Common::listCountry();
        foreach ($data as $item) {

            if(!empty($item['title'])){
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
                
                if(!isset($item['home']))$item['home']=0;
                if(isset($item["group"]['_id']) ){
                    $item["gid"] = $item["group"]['_id'];         
                    unset($item["group"]);
                }
                
                if(!isset($item['publishdate'])){
                    $item['post_date']=0;
                }
                else{
                    
                    $item['post_date']=strtotime($item['publishdate'].' '.(isset($item['publishtime'])?$item['publishtime']:''));   
                }
               
                
                //print_r($item);exit;
                //check is build data
                $olditem=null;
                if(isset($item['_id'])){            
                    $olditem=$this->app->db->findOne("addons/$this->moduleName", ['_id'=>$item['_id']]);
                    if(!isset($olditem['home']))$olditem['home']=0;
                    //print_r($olditem['content']!=$item['content']);exit;                
                    if($item['gid']!==$olditem['gid']){
                        $item['gname']=$groupsById[$item['gid']]['slug'];
                        if(!in_array($item['gid'], $buildcatids))
                            $buildcatids[]=$item['gid'];
                        if(!in_array($olditem['gid'], $buildcatids))
                            $buildcatids[]=$olditem['gid'];                        
                    }                
                    if($item['home'] || $item['home']!=$olditem['home'])$isbuildhome=1;                
                    if(!isset($item["created"])){
                        $item["created"] =time();
                    }
                }
                else{        
                       
                    $item['uid']=$this->user['_id'];  
                    $item["created"] =time();
                    $item["modified"] =time();              
                    

                    if($item['home'])$isbuildhome=1;
                    $isbuildmenu   =true;                
                    if(isset($item['gid']) && isset($groupsById[$item['gid']])){                    
                        $buildcats[]=$item['gid'];
                    }
                }

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
                }
                
                
                
                $item["modified"] = time();
                
                if(!isset($item["_id"])){
                    $item["created"] = $item["modified"];
                }

                $item["post_date"] =strtotime($item["startdate"].' '.$item["starttime"]);

                //set language
                if(!isset($item['lang'])){
                    $item['lang']=$config['defaultlang'];
                }

                //find and replace image in content
                $content= $item['content'];
                //get all image in content and save to host if is new
                preg_match_all('/<img.*?(src=["\'](.*?)["\']).*?>/is', $content, $matches);

                //print_r($matches);
                if(isset($matches[2])){            

                    foreach($matches[2] as $k=>$img){
                        //check image host
                        $imageinfo=parse_url($img);
                        if(isset($imageinfo['host']) && ($imageinfo['host']==$config['siteurl'] || $imageinfo['host']==$config['localurl'] ));continue;
                        //sync with editor:
                        $syncimg=str_replace($this->app->module("builds")->sitebaseurl().COCKPIT_BASE_URL,'../..',$img);
                        //print_r($syncimg);exit;
                        $isnewimage=1;                    
                        if(strpos($syncimg, '://')>5 || strpos($syncimg, '://')===false){                        
                            $syncimg=$this->app->module("builds")->sitebaseurl().COCKPIT_BASE_URL.'/'.str_replace('../../','',$syncimg);                           

                            $isnewimage=0;
                        }
                        //print_r($syncimg);exit;
                        
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
                $this->app->db->save("addons/$this->moduleName", $item);
                
                $items[$item['lang']]=$item;
                $langs[$item['lang']]=$item['_id'];                
               
                //if($item['publish']){
                    $builditemids[]=$item['_id'];
                    $langslink[$item['lang']]=$item['slug'].'/';
                    $langslinkamp[$item['lang']]=$item['slug'].'-'.$item['amp'].'/';
                    $langsiso[$item['lang']]=$listLocale[$item['lang']];
                    $langsname[$item['lang']]=$listCountry[$item['lang']];
                //}
                
                //remove autosave:
                $pid=0;   
                if(isset($item["_id"])){
                    $pid=$item["_id"];
                }  
                $autoitem["uid"]     = new \MongoId ($this->user["_id"]);
                $autoitem["pid"]     =  $pid;

                
                $this->app->db->remove("addons/autosaves", $autoitem);
            }
            
        }
        
        //update langs link id reference
        
        foreach ($items as $key => $item) {
            $tmplangs=$langs;
            //unset($tmplangs[$item['lang']]);
            $item['langs']=$tmplangs;

            $tmplangsiso=$langsiso;
            //unset($tmplangsiso[$item['lang']]);
            $item['langsiso']=$tmplangsiso;

            $tmplangsname=$langsname;
            //unset($tmplangsname[$item['lang']]);
            $item['langsname']=$tmplangsname;

            $tmplangslink=$langslink;
            //unset($tmplangslink[$item['lang']]);
            $item['langslink']=$tmplangslink;

            $tmplangslinkamp=$langslinkamp;
            //unset($tmplangslinkamp[$item['lang']]);
            $item['langslinkamp']=$tmplangslinkamp;
            
            $this->app->db->save("addons/$this->moduleName",$item);  
            $items[$key]=$item;
            if($item['lang']==$currentlang){
                $id=$item['_id'];
            }
        }

        
       
       //print_r($isbuildhome);exit;
        if($config['isbuilddata'] ){
            $html=$this->module("builds")->buildHtml();   
            $ampHtml=$this->module("builds")->buildHtml(1);   
            $this->module('mycustom')->builddatabyposts($html,$ampHtml,$builditemids,$overwrite);
            
            
            //rebuild home data and push
            if($isbuildhome)$this->module('mycustom')->buildHome($html);
            // //rebuild cat data

            if(count($buildcatids)>0)$this->module('mycustom')->builddatabygroups($html,$buildcatids);
        }
        
        // //rebuild menu data
        //$this->module('builds')->buildMenu();
        
        return json_encode(['message'=>'Item\'s saved',
                            'result'=>'success',
                            '_id'=>$id
            ]);
    }    

    

    public function autosave(){   

        $item = $this->param("item", null);
        //print_r($item);exit;
        //get old last autosave:
        $pid=0;
        if(isset($item["_id"])){
            $pid=$item["_id"];
        }
        $autoitem=[];
        $autoitem["uid"]     = new \MongoId($this->user["_id"]);
        $autoitem["pid"]     =  $pid;
        $old=$this->app->db->findOne("addons/autosaves", $autoitem);
        if($old) {
            $autoitem=$old;
        }
        $autoitem["modified"] = time();
        if(isset($item["group"]) && isset($item["group"]['_id'])){
            $item["gid"]= $item["group"]['_id'];
            unset($item['group']);
        }
        $autoitem["item"]=$item;

        $this->app->db->save("addons/autosaves", $autoitem);
        //print_r($autoitem);
        return $autoitem ? json_encode($autoitem) : '{}';
    }

    public function getAutoSave(){        
        $pid = $this->param("pid", null);

        //get old last autosave:
        $old=$this->app->db->findOne("addons/autosaves", ['pid'=>$pid,'uid'=>new \MongoId ($this->user['_id'])]);
        if(isset($old['item'])){
            $old['item']['group']=$this->app->db->findOne("addons/groups", ['_id'=>$old['item']['gid']]);
        }
        return $old ? json_encode($old) : '{}';
    }

    public function revertAutoSave(){        
        $id = $this->param("id", null);
        //get old last autosave:

        $old=$this->app->db->findOne("addons/autosaves", ['_id'=>$id]);
        if(isset($old['item'])){
            $old['item']['group']=$this->app->db->findOne("addons/groups", ['_id'=>$old['item']['gid']]);
        }
        return $old ? json_encode($old) : '{}';
    }

     public function duplicate(){
 
        $id = $this->param("id", null);
        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.edit'))return '{"error":"not permission"}';

        if ($id) {

            $item = $this->app->db->findOneById("addons/$this->moduleName", $id);

            if ($item) {

                unset($item['_id']);
                $item["modified"] = time();
                $item["_uid"]     = @$this->user["_id"];
                $item["created"] = $item["modified"];
                if(!isset($item["title"]))$item["title"]='n/a';
                $item["title"] .= ' (copy)';

                $this->app->db->save("addons/$this->moduleName", $item);

                return json_encode($item);
            }
        }

        return false;
    }
    
    public function remove() {
        $id = $this->param("id", null);
        //check acl and role
        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.edit'))return '{"error":"not permission"}';
        $config=$this->app->module("builds")->getConfig();
        if($id) {
            $post=$this->app->db->findOne("addons/$this->moduleName", ["_id" => $id]);
            if($post){
                //delete folder
                //local
                $f=substr($post['slug'], 0,1);
                $this->app->module('builds')->removedir('./output/'.$f.'/'.$post['slug'].'/');
                if($post['amp'])
                    $this->app->module('builds')->removedir('./output/'.$f.'/'.$post['slug'].'-'.$post['amp'].'/');
                
            }
            //remove project
            $this->app->db->remove("addons/$this->moduleName", ["_id" => $id]);

        }

        return $id ? '{"success":true}' : '{"success":false}';
    }

    public function removeGroup(){
        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.edit'))return '{"error":"not permission"}';
        $criteria = $this->param("criteria", false);
        $data     = $this->param("data", false);
        //print_r($data);
        
        if ($criteria && $data) {
            //check posts
            $gid=new \MongoId($criteria['group']['_id']);
            $posts=$this->app->db->find('addons/posts',['filter'=>['gid'=>$criteria['group']['_id']],'fields'=>['_id'=>1]]);
            
            //$this->app->db->remove("addons/groups", ['_id'=>$gid]);
        }

        return '{"success":true}';
    }

    public function addGroups() {
        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.edit'))return '{"error":"not permission"}';
        $name = $this->param("name", null);

        if ($name) {
            $group['uid']=new \MongoId ($this->user['_id']);
            $group['name']=$name;
            $group['slug']=$this->app->module("commons")->createslug($name);
            $this->app->db->save("addons/groups", $group);

            return json_encode($group);
        }

        return false;
    }
    public function editGroups() {
        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.edit'))return '{"error":"not permission"}';
        $group = $this->param("group", null);

        $group['modified']=time();
        if(!$group['_id'])$group['created']=$group['modified'];
        if ($group) {
            $group['uid']=new \MongoId ($this->user['_id']);
            $group['created']=time();
            $group['slug']=$this->app->module("commons")->createslug($group['name']);
            $this->app->db->save("addons/groups", $group);

            return json_encode($group);
        }

        return false;
    }

    public function getGroups() {
    if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.index'))return '{"error":"not permission"}';
        $groups = $this->app->db->find("addons/groups", [])->toArray();
        
        return json_encode($groups);
    }
}
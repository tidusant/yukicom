<?php

namespace builds\Controller;

class Main extends \Cockpit\Controller {
	private $moduleName='';
    
	public function __construct($app) {
        parent::__construct($app);
        //duy.ha: get path to detect module name
        $pathName=explode("\\",__DIR__);
        if(count($pathName)<=1)$pathName=explode('/',__DIR__);
        $moduleName=$pathName[count($pathName)-2];
        $this->moduleName=$moduleName;        
    }

    

    public function index() {
        // $posts=$this->app->db->find('addons/posts',['filter'=>['publish'=>1],'sort'=>['_id'=>-1],'skip'=>$i,'limit'=>1184467440737]);
        
        // print_r(count($posts));
        // exit;

        $viewdata=['action'=>__FUNCTION__,
                    'moduleName'=>$this->moduleName];
        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.index')) {  
            return false;
        }
        $buildModules=[];
        foreach (new \DirectoryIterator($this->app->path('modules:addons')) as $addon) {
            if ($addon->isFile() || $addon->isDot()) continue;
            $name = $addon->getFilename();
            if($this->module($name)->isBuildable()){
                $buildModules[]=$name;
            }
        }
        // print('<pre>');

        // print_r($_SERVER);
        //  print('</pre>');
        $viewdata['buildModules']=$buildModules;
        
        
        return $this->render("$this->moduleName:views/". $viewdata["action"].".php",$viewdata);
    }

  
    
}


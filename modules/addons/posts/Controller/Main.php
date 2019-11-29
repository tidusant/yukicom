<?php

namespace posts\Controller;

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
        $viewdata=['action'=>__FUNCTION__,
                    'moduleName'=>$this->moduleName];
        if (!$this->app->module("auth")->hasaccess($this->moduleName, 'manage.index')) {  
            return false;
        }
        return $this->render("$this->moduleName:views/". $viewdata["action"].".php",$viewdata);
    }

    public function item($id = null) {
        // echo '<pre>';
        // print_r($_SERVER);exit;
        $viewdata=['action'=>__FUNCTION__,
                    'moduleName'=>$this->moduleName]; 
        $viewdata['id']=$id;
        $config=$this->app->module("builds")->getConfig();
        $siteurl=($config['live']?$config['siteurl']:$config['localurl']);
        $host=$this->app->db->findOne('addons/hosts');        
        if($host['live'])
            $siteurl=$host['siteurl'];
        $viewdata['siteurl']=$siteurl;
        return $this->render("$this->moduleName:views/". $viewdata["action"].".php",$viewdata);
    }
    
}


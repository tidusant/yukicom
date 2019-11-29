<?php

namespace sql2cps\Controller;

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

    public function edit($pid = null,$id=null) {
        $viewdata=['action'=>__FUNCTION__,
                    'moduleName'=>$this->moduleName,
                    'proj'=>$this->app->module("projects")->checkroleAndAcl($pid,'manage.edit','leader')];
        //check role: if has role then return project info 

        if(!$viewdata['proj'])
            return false;
        //end 
        $viewdata['id']=$id;
        return $this->render("$this->moduleName:views/". $viewdata["action"].".php",$viewdata);
    }

    public function index() {

        $viewdata=['action'=>__FUNCTION__,
                    'moduleName'=>$this->moduleName];
        //check role: if has join role then return project info  
        if(!$this->app->module("auth")->hasaccess($this->moduleName,'manager.index'))
            return false;
        //end 
        
        return $this->render("$this->moduleName:views/". $viewdata["action"].".php",$viewdata);
    }
    
}


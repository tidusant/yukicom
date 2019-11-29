<?php

namespace commons\Controller;

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

    public function getLangs(){        
      
        $config=$this->app->module("builds")->getConfig();  
        
        //get all gacode
        //print_r($doc);
        
        //print_r($gacodes->toArray());
        
        return  json_encode(['showlang'=>$config['showlang'],
                                    'defaultlang'=>$config['defaultlang'],                                    
                                    'langs'=>$config['langs'],
                                    ]);
    }
  
}
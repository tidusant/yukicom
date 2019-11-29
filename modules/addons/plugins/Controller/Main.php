<?php

namespace plugins\Controller;

class Main extends \Cockpit\Controller {
	private $moduleName='';

	public function __construct($app) {
        parent::__construct($app);
        //duy.ha: get path to detect module name
        $pathName=explode("\\\\",__DIR__);
        if(count($pathName)<=1)$pathName=explode('/',__DIR__);
        $moduleName=$pathName[count($pathName)-2];
        $this->moduleName=$moduleName;
    }

    

    
}


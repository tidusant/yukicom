<?php

namespace themes\Controller;

class Api extends \Cockpit\Controller {
    private $moduleName='';
     private $sitebaseurl='http://localhost';
    private $siteurl=COCKPIT_BASE_URL.'/output/';
    public function __construct($app) {
        parent::__construct($app);
        //duy.ha: get path to detect module name
        $this->siteurl=$this->sitebaseurl.$this->siteurl;
        //duy.ha: get path to detect module name
        $pathName=explode("\\",__DIR__);
        if(count($pathName)<=1)$pathName=explode('/',__DIR__);
        $moduleName=$pathName[count($pathName)-2];
        $this->moduleName=$moduleName; 

    }

     public function setTheme(){
        $name = $this->param("name", null);
        $host=$this->app->db->findOne('addons/hosts');
        $host['theme']=$name;
        $this->app->db->save("addons/hosts", $host);
        return 1;
     }
    public function getThemes(){

        //get all layout
        $themes=[];
        $host=$this->app->db->findOne('addons/hosts');
        foreach (new \DirectoryIterator('./input/Layouts') as $theme) {

            if ($theme->isFile() || $theme->isDot()) continue;

            $name = $theme->getFilename();

            $info = [
                "name"        => $name,
                "version"     => null,
                "description" => null,
                "homepage"    => null,
                "check_url"   => null,
                "repo"        => null
            ];

            if ($meta = "./input/Layouts/{$name}/info.json") {

                $meta = json_decode(file_get_contents($meta), true);

                if (!is_null($meta)) {
                    $info = array_merge($info, $meta);
                }
            }

            $info["path"] = "./input/Layouts/{$name}";
            $info["screenshot"] = "./input/Layouts/{$name}/screenshot.jpg";            
            $themes[] = $info;
        }
        
       
        return json_encode(['themes'=>$themes,'currentTheme'=>$host['theme']]);
    }

    
}
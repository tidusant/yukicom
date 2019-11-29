<?php

//API

$pathName=explode("\\",__DIR__);
if(count($pathName)<=1)$pathName=explode('/',__DIR__);
$moduleName=$pathName[count($pathName)-1];
$this->module($moduleName)->extend([
]);


// ADMIN
if(COCKPIT_ADMIN) {
    //register acl
    $this("acl")->addResource("$moduleName", ['manage.index', 'manage.edit']);
    $app->on("admin.init", function() use($app,$moduleName){
        
        // bind routes
        
        $app->bindClass("$moduleName\\Controller\\Main", "$moduleName");
        
        // bind api
        $app->bindClass("$moduleName\\Controller\\Api", "api/$moduleName");
        
         $app("admin")->menu("top", [
            "url"    => $app->routeUrl("/$moduleName"),
            "label"  => '<i class="uk-icon-database"></i>',
            "title"  => $app("i18n")->get("Sql2cp")
        ],0);
    });
    
}
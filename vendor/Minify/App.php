<?php
/**
 * Front controller for default Minify implementation
 * 
 * DO NOT EDIT! Configure this utility via config.php and groupsConfig.php
 * 
 * @package Minify
 */
namespace Minify;
class App {
	public function __construct() {
        require dirname(__FILE__) . '/lib'."/Minify/Loader.php";
		\Minify_Loader::register();
    }
	

}
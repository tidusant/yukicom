<?php
/**
 * Class Minify_Loader
 * @package Minify
 */

/**
 * Class autoloader
 *
 * @package Minify
 * @author Stephen Clay <steve@mrclay.org>
 */

class Minify_Loader {
    public function loadClass($class)
    {
        $file = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR;
        $file .= strtr($class, "\\_", DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR) . '.php';
        //print_r($file);ob_flush();exit;
        if (is_readable($file)) {
            require $file;
        }
    }

    static public function register()
    {
        //print_r('aaa');ob_flush();
        $inst = new self();
        spl_autoload_register(array($inst, 'loadClass'));
    }
}

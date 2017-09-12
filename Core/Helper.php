<?php
/**
 * Created by PhpStorm.
 * User: alonex
 * Date: 17/9/12
 * Time: 13:53
 */
namespace Core;

class Helpers{
    public function get_class_all_methods($class){
        $methods = get_class_methods($class);
        return $methods;
    }
}
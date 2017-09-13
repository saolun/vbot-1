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

    /**
     * @name 输出日志到控制
     * @param $text
     * @param string $status
     * @throws Exception
     */
    public function console_out_log($text, $status='SUC',$isdie=false) {
        $out = "";
        switch($status) {
            case "SUC":
                $out = "[32m"; //Green
                break;
            case "FAIL":
                $out = "[31m"; //Red
                break;
            case "WARN":
                $out = "[33m"; //Yellow
                break;
            case "NOTE":
                $out = "[34m"; //Blue
                break;
            default:
                throw new Exception("Invalid status: " . $status);
        }
        $NoticeMsg =  chr(27) . "$out" . "$text" . chr(27) . "[0m";
        print_r($NoticeMsg);
        if($isdie){
            die(0);
        }
    }
}
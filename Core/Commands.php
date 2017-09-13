<?php
namespace Core;
/**
 * Class Commands
 * author:ALonexy<961610358@qq.com>
 */
use Services\Run;
use Core\Helpers;

class Commands{
    private $argv;
    private $run;
    private $console;

    public function __construct($argv)
    {
        $this->argv = $argv;
        $this->run  = new Run();
        $this->handle();
    }
    private function handle(){
        if($this->checkOption($this->argv)){
            foreach($this->checkOption($this->argv) as $vals)
            {
                $argv_opts = each($vals);
                if($argv_opts['key'] == '--func')
                {
                    if(isset($argv_opts['value'])){
                        //匹配public 的方法
                        list($status,$funName) = $this->isExitsClassMethods($argv_opts['value']);
                        if($status){
                            $this->run->$funName();
                            $this->run->vbot->console->log('--func:'.$argv_opts['value'].'->>>执行SUC');
                        }else{
                            $this->run->vbot->console->log('--func:无匹配Function','ERROR');
                            die(0);
                        }
                    }else{
                        $this->run->vbot->console->log('--func:参数为空','ERROR');
                        die(0);
                    }
                    $this->run->start();
                }
            }
        }else{
            $this->run->vbot->console->log('执行 php server --help 查看帮助信息');
            die(0);
        }
    }
    //判断启动方法是否在类中
    private function isExitsClassMethods($func)
    {
        $RetStatus = false;
        $RetFunName = '';
        $HelpFun = new Helpers();
        $methods = $HelpFun->get_class_all_methods($this->run);
        array_walk($methods,function(&$v,$k){$v = strtolower($v);});
        if(is_array($methods)&&in_array(strtolower($func),$methods)){
            $key = array_search(strtolower($func),$methods);
            $RetStatus = true;
            $RetFunName = $HelpFun->get_class_all_methods($this->run)[$key];
        }
        return [$RetStatus,$RetFunName];
    }
    //获取 所有可执行Public Function
    private function getAllClassMethods(){
        $HelpFun = new Helpers();
        $methods = $HelpFun->get_class_all_methods($this->run);
        array_walk($methods,function(&$v,$k){$v = strtolower($v);});
        return $methods;
    }
    private function colorize($text, $status='SUC') {
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
        return chr(27) . "$out" . "$text" . chr(27) . "[0m";
    }
    //匹配 --
    private function checkOption($argv){
        if(is_array($argv)){
            $CommandOptions = [];
            foreach($argv as $val){
                $rule ='/\-\-\w+/';
                if(preg_match($rule,$val,$result)){
                    $cs = isset(explode('=',$val)[1])?explode('=',$val)[1]:false;
                    if($cs && isset($result[0])){
                        array_push($CommandOptions,[$result[0]=>$cs]);
                    }
                }
                $rule_help = '/\-\-help/';
                if(preg_match($rule_help,$val,$result_help)){
                    list($allFuncs,$allFunDocs) = $this->getClassDetail();
                    $consolMsg = "<Alonexy@961610358@qq.com>:\n 使用格式：\t php server --session=[*] --func=[function不区分大小写]\n";
                    $funStrlenMax = 0;
                    foreach($allFuncs as $funV){
                        if(strlen($funV) >$funStrlenMax){
                            $funStrlenMax = strlen($funV);
                        }
                    }
                    foreach($allFuncs as $allFk=>$allFv){
                        if($allFv == '__construct'){
                        }else{
                            $gaps = '      ';
                            if($funStrlenMax - strlen($allFv)){
                                for($i=0;$i<($funStrlenMax - strlen($allFv));$i++){
                                    $gaps.=' ';
                                }
                            }
                            $consolMsg.="\n ".$allFv.$gaps.(isset($allFunDocs[$allFk])?$allFunDocs[$allFk]:'')."\t";
                        }
                    }
                    print_r($this->colorize($consolMsg));
                    die(0);
                }
            }
            if(count($CommandOptions) >0){
                return $CommandOptions;
            }else{
                return false;
            }
        }
        return false;
    }
    //php反射API
    public function getClassDetail()
    {
        $class = new \ReflectionClass($this->run);
        /**
         *   默认情况下，ReflectionClass会取所有的属性，private 和protected的也可以
        如果只想获取到private属性，就要额外传个参数
        可用参数列表:
        $private_properties = $class->getProperties(ReflectionProperty::IS_PRIVATE);
        可用参数列表
        ReflectionProperty::IS_STATIC
        ReflectionProperty::IS_PUBLIC
        ReflectionProperty::IS_PROVATE
        ReflectionProperty::IS_PROECTED
        如果要同时获取public 和private 属性，就这样写：ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED。
         */
        //方法=>getMethods  属性=>getProperties  注释=>getDocComment
        $properties = $class->getMethods(\ReflectionProperty::IS_PUBLIC);
        //获取注释
        $funcs = [];
        $funs_docs = [];
        foreach($properties as &$property)
        {
            array_push($funcs,$property->name);
            if($property->isPublic())
            {
                $docblock = $property->getDocComment();
                preg_match_all('/@([namedesc])+\s[^\x00-\x80]*/',$docblock, $matches);
                $docMsg = isset(explode(' ',$matches[0][0])[1])?explode(' ',$matches[0][0])[1]:'NotMsg';
                array_push($funs_docs,$docMsg);
            }
        }
        return [$funcs,$funs_docs];

    }
}
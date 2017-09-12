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
                    $allFuncs = $this->getAllClassMethods();
                    $consolMsg = '';

                    foreach($allFuncs as $allFv){
                        if($allFv == '__construct'){
                        }else{
                            $consolMsg.="\n可执行Funs=>".$allFv."\n";
                        }
                    }
                    $this->run->vbot->console->log($consolMsg);
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

}
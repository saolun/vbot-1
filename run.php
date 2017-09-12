<?php
require_once __DIR__.'/vendor/autoload.php';

date_default_timezone_set("PRC");
use Hanson\Vbot\Foundation\Vbot;
use Illuminate\Support\Collection;
use Hanson\Vbot\Message\Text;
use Models\ContactsModel;

class Run{
    private $vbot;
    private $observer;
    private $messageHandler;
    public  $ContactsModel;
    public function __construct()
    {
        $this->vbot = new Vbot($this->getConfigs());
        // 获取消息处理器实例
        $this->messageHandler = $this->vbot->messageHandler;
        // 获取监听器实例
        $this->observer = $this->vbot->observer;
        // 联系人model
        $this->ContactsModel = new ContactsModel();
    }
    public function start()
    {
        $this->vbot->console->log('---脚本启动中---');
        //监听错误
        $this->observer->setNeedActivateObserver(function($err){
            $this->vbot->console->log($err);
        });
        $this->vbot->server->serve();
    }
    public function MsgReturn()
    {
        // 收到消息时触发
        $this->messageHandler->setHandler(function(Collection $message){
            $this->vbot->console->log('---收到消息-回复消息---');
            Text::send($message['from']['UserName'], 'Hi! 现在时间：'.date('Y-m-d H:i:s',time()));
        });
    }
    public function TimeTask(){
        // 一直触发
        $this->messageHandler->setCustomHandler(function(){
            $this->ContactsModel->getAllFirends()->each(function($info,$userid){
                $this->vbot->console->log($userid);
                Text::send($userid, 'Hi! '.$info['NickName'].'现在时间：'.date('Y-m-d H:i:s',time()).'--Powered By:Alonexy');
            });
            sleep(60*30);
        });
    }
    //获取配置文件
    protected function getConfigs()
    {
        $path = __DIR__.'/tmp/';
        return [
            'path'     => $path,
            /*
             * swoole 配置项（执行主动发消息命令必须要开启，且必须安装 swoole 插件）
             */
            'swoole'  => [
                'status' => false,
                'ip'     => '127.0.0.1',
                'port'   => '8866',
            ],
            /*
             * 下载配置项
             */
            'download' => [
                'image'         => true,
                'voice'         => true,
                'video'         => true,
                'emoticon'      => true,
                'file'          => true,
                'emoticon_path' => $path.'emoticons', // 表情库路径（PS：表情库为过滤后不重复的表情文件夹）
            ],
            /*
             * 输出配置项
             */
            'console' => [
                'output'  => true, // 是否输出
                'message' => true, // 是否输出接收消息 （若上面为 false 此处无效）
            ],
            /*
             * 日志配置项
             */
            'log'      => [
                'level'         => 'debug',
                'permission'    => 0777,
                'system'        => $path.'log', // 系统报错日志
                'message'       => $path.'log', // 消息日志
            ],
            /*
             * 缓存配置项
             */
            'cache' => [
                'default' => 'file', // 缓存设置 （支持 redis 或 file）
                'stores'  => [
                    'file' => [
                        'driver' => 'file',
                        'path'   => $path.'cache',
                    ],
                    'redis' => [
                        'driver'     => 'redis',
                        'connection' => 'default',
                    ],
                ],
            ],
            /*
             * 拓展配置
             * ==============================
             * 如果加载拓展则必须加载此配置项
             */
            'extension' => [
                // 管理员配置（必选），优先加载 remark_name
                'admin' => [
                    'remark'   => 'test_vbot',
                    'nickname' => 'alonexy',
                ],
            ],
        ];
    }
}

function checkOption($argv){
    if(is_array($argv)){
        $CommandOptions = [];
        foreach($argv as $val){
            $rule ='/\-\-\w+/';
            if(preg_match($rule,$val,$result)){
                array_push($CommandOptions,[$result[0]=>explode('=',$val)[1]]);
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

if(checkOption($argv)){
    print_r(checkOption($argv));
    exit;
}else{
    echo '没有可执行参数';
    die(0);
}
$run  = new Run();
$run->MsgReturn();
$run->start();

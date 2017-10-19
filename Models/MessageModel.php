<?php
namespace Models;
use Hanson\Vbot\Message\Emoticon;
use Hanson\Vbot\Message\Text;

/**
 * @name 消息类型分发
 * Class MessageModel
 * @package Models
 */
class MessageModel extends BaseModel{
    private $message;

    public function __construct($msg){
        parent::__construct();
        $this->message = $msg;
        if(isset($this->message['fromType'])){
            $fromTypes_CN = isset($this->FromTypes()[$this->message['fromType']])?$this->FromTypes()[$this->message['fromType']]:'位置消息';
            vbot('console')->log("消息发送者类型>>>{$fromTypes_CN}",'MESSAGE');
        }
        if(isset($this->message['type'])){
            vbot('console')->log("消息类型>>>{$this->message['type']}",'MESSAGE');
            $MsgType = ucfirst(strtolower($this->message['type']));
            $HandleFunName = $MsgType.'_Handle';
            if(!method_exists($this,$HandleFunName)){
                $this->CommonFun->console_out_log($HandleFunName."不存在,无法调用\n",'FAIL',true);
                $this->Default_Handle();
            }else{
                vbot('console')->log($HandleFunName.">>>开始处理消息",'INFO');
                $this->$HandleFunName();
            }
        }else{
            $this->CommonFun->console_out_log("MsgHadle>>>无法区分消息类型\n",'FAIL',true);
        }
    }

    /**
     * 消息发送者类型
     * @return array
     */
    private function FromTypes()
    {
        return [
            'System'  =>'系统消息',
            'Self'    =>'自己发送的消息',
            'Group'   =>'群组消息',
            'Contact' =>'联系人消息',
            'Friend'  =>'好友消息',
            'Official'=>'公众号消息',
            'Special' =>'特殊账号消息',
            'Unknown' =>'未知消息'
        ];
    }
    /**
     * @name 默认处理
     */
    public function Default_Handle()
    {
        Text::send($this->message['from']['UserName'], 'Hi! 现在时间：'.date('Y-m-d H:i:s',time()));
    }
    /**
     * @name text类型处理
     */
    public function Text_Handle()
    {
        Text::send($this->message['from']['UserName'], 'Hi! 现在时间：'.date('Y-m-d H:i:s',time()));
    }

    /**
     * @name 语音消息
     */
    public function Voice_Handle()
    {

    }

    /**
     * @name 图片消息
     */
    public function Image_Handle()
    {

    }

    /**
     * @name 表情消息
     */
    public function Emoticon_Handle()
    {
        // 下载表情至默认路径
        Emoticon::download($this->message);
        // 下载表情到自定义路径或自定义处理
//        Emoticon::download($this->message, function ($resource) {
//            file_put_contents(__DIR__.'/test.gif', $resource);
//        });
        // 直接传 $message 便能直接发送该 message 的表情
        Emoticon::send($this->message['from']['UserName'], $this->message);
        // 也可以选择一个表情路径
        //Emoticon::send($this->message['from']['UserName'], __DIR__.'/test.gif');
        // 或者从表情库随机抽取一个进行发送
        //Emoticon::sendRandom($this->message['from']['UserName']);
    }

}
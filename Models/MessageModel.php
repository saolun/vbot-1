<?php
namespace Models;
/**
 * @name 消息类型分发
 * Class MessageModel
 * @package Models
 */
class MessageModel extends BaseModel{
    private $message;
    public function __construct($msg){
        $this->message = $msg;
    }


}
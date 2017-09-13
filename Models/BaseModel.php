<?php
namespace Models;
use Core\Helpers;
class BaseModel{

    public $CommonFun;

    public function __construct(){
        $this->CommonFun = new Helpers();
    }
}
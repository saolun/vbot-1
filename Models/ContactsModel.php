<?php
/**
 * Class Contacts
 * 联系人Models
 */
namespace Models;

class ContactsModel extends BaseModel{

    public function __construct()
    {
        parent::__construct();
    }
    //获取所有好友
    public function getAllFirends(){
        $friends = vbot('friends');
        return $friends;
    }
}
<?php
namespace App\Model;

use App\Model\Db;

class User extends Db
{
    protected $table = 'user';  
    
    public function getUsers()
    {
        return Db::Find('Select * from users',[]);
    }

}

?>

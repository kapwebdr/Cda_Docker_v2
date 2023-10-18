<?php
namespace Project\Coop\Controller;

use Project\Coop\Model\Produit;

use App\Controller\Main;
use App\Controller\View;
use App\Model\User;

class Home extends Main
{
    public function Index()
    {
        $User           = new User();
        $Users          = $User->Find('Select * from user order by nom',[]);
        
        View::Init('smarty');
        View::Set('title','Titre de la page');
        View::Set('h1','Bonjour le monde !!');
        View::Set('users',$Users);
        View::Display('Home');
    }
    public function Test($params=[])
    {
        echo (isset($params['id']))?'Bonjour : '.$params['id']:'Bonjour invité';
    }
}
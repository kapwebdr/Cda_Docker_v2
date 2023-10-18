<?php
namespace Project\Coop\Controller;

use Project\Coop\Model\Produit as ProduitModel;
use App\Controller\View;
use App\Controller\Main;

class Produit extends Main
{
    public function getProduit($params=[]) //$params = ['id'=>2]
    {
        $model      = new ProduitModel();
        $produit    = $model->getProduit($params['id']);

        View::Init('smarty');
        View::Set('h1','Coucou');
        
        
        View::Set('produit',$produit);
        View::Display('Produit');
    }
}

?>
<?php
namespace Projects\Coop\Controller;

use \Projects\Model\Produit;
use \Projects\Model\Categorie;

use App\Controller\Main;
use App\Controller\View;

class Home extends Main
{
    public function Index()
    {
        $names = ['Apple','Tesla','Microsoft'];
        // Insert
        echo "Insertion en Bdd :  ";
        $ProduitInsert                    = new Produit();
        $ProduitInsert->name              = $names[rand(0,count($names))].microtime();
        $ProduitInsert->description       = "Ipod";
        $ProduitInsert->categorie         = rand(1,2);
        $ProduitInsert->Save();
        echo $ProduitInsert->idProduit." <br/>";
        
        echo "Get produit id : ".$ProduitInsert->idProduit." :  <br/>";
        // Récupération d'un Produit : 
        $Produit                    = new Produit();
        $Result = $Produit
            ->With('categorie', Categorie::class, 'categorie', 'categorie_id')
            //->Join(['type'=>'INNER','table'=>'categorie','condition'=>'categorie.categorie_id=produit.categorie'])
            ->Where(['idProduit'=>$ProduitInsert->idProduit])
            ->FindOne();
        /*
        $result = $user->Where([['age', '<', 18], 'OR', ['age', '>', 60]])->Find();
        $user = new User();
        $result = $user->Where([
            ['name', '=', 'John'],
            'AND',
            [['age', '<', 18], 'OR', ['age', '>', 60]]
        ])->Find();
        $result = $user->Where(['age', 'BETWEEN', [18, 60]])->Find();
        */    
        echo $Result->name." <br/>";
        echo '<pre>';
        var_dump($Result->toArray());
        var_dump($Result->toJson());
        echo '</pre>';
        if($ProduitInsert->idProduit%2)
        {
            echo "Update produit id : ".$ProduitInsert->idProduit." :  <br/>";
            $Produit->name              = $names[rand(0,count($names))].microtime();
            $Produit->description       = "Ipod New";
            $Produit->Save();
        }
        echo "Delete produit id : ".$ProduitInsert->idProduit." :  <br/>";
        //$Produit->Delete();
        
        $Produits = [];
        $ProduitList                    = new Produit();
        $Results                        = $ProduitList->Find();
        echo '<pre>';
        var_dump($Results->toArray());
        var_dump($Results->toJson());
        echo '</pre>';
        $Produits = $Results->toArray();

        // Récupération d'un Produit avec Clause Where Plus Complexe: 
        $Produit                    = new Produit();
        $Results = $Produit->Where(['column' => 'description', 'operator' => '=', 'value' => 'Ipod'])->Where(['column' => 'name', 'operator' => 'LIKE', 'value' => '%app%'])->Find();
        echo '<pre>';
        var_dump($Results->toArray());
        echo '</pre>';
        
        View::Init('smarty');
        View::Set('title','Titre de la page');
        View::Set('h1','Bonjour le monde !!');
        View::Set('Products',$Produits);
        View::Display('Home');
    }
    public function Test($params=[])
    {
        echo (isset($params['id']))?'Bonjour : '.$params['id']:'Bonjour invité';
    }
}
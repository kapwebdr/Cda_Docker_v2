<?php
namespace Projects\Coop\Model;

use App\Model\Db;

class Produit extends Db
{
    protected string $table = 'produit';
    protected array $primaryKeys = ['idProduit'];
    
    public int $idProduit;
    public string $name;
    public string $description;
    public int $categorie;
    
}
?>
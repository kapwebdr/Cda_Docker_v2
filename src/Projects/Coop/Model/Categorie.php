<?php
namespace Projects\Coop\Model;

use App\Model\Db;

class Categorie extends Db
{
    protected string $table = 'categorie';
    protected array $primaryKeys = ['categorie_id'];
    
    public int $categorie_id;
    public string $categiorie_name;
}
?>
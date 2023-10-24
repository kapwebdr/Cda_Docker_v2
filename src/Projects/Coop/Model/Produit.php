<?php
namespace Projects\Coop\Model;

class Produit extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'produit';
    protected $primaryKey = 'idProduit';
    public $incrementing    = true;
    protected $fillable = [
        'name', 'description', 'categorie'
    ];
}
?>
{include file="./header.tpl"}

{$h1}
{$produit['IdProduit']}

{foreach key=k item=produit from=$produits}
    <tr>
        <td>{$k} - {$produit['IdProduit']}</td>
    </tr>
{/foreach}

{include file="./footer.tpl"}
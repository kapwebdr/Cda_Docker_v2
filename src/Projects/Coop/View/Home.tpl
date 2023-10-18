{include file="./header.tpl"}
<h1>Smarty =::= {$h1}</h1>

<table border="1" width="100%">
    <tr style="background:gray;color:white;font-weight:bold;">
        <td>Nom</td>
        <td>Prénom</td>
    </tr>
{foreach key=k item=user from=$users}
    <tr>
        <td>{$k} - {$user['nom']}</td>
        <td>{$user['prenom']}</td>
    </tr>
{/foreach}
</table>
{include file="./footer.tpl"}
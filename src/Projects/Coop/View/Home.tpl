{include file="./header.tpl"}
<h1>Smarty =::= {$h1}</h1>

<table border="1" width="100%">
    <tr style="background:gray;color:white;font-weight:bold;">
        <td>Nom</td>
        <td>Prénom</td>
    </tr>
{foreach key=k item=Product from=$Products}
    <tr>
        <td>{$k} - {$Product['name']}</td>
        <td>{$Product['description']}</td>
    </tr>
{/foreach}
</table>

<img src="/img/pinguoin.jpg"/>
{include file="./footer.tpl"}
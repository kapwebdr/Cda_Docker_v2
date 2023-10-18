<?php
$routes = [
    '/' => [
        'method'=>['GET'],
        'controller'=>['Project\Coop\Controller\Home','Index']
    ],
    '/produit/{id:\d+}' => [
        'method'=>['GET'],
        'controller'=>['Project\Coop\Controller\Produit','getProduit']
    ]
];
?>
<?php

define('DIR_VIEW',DIR_PROJECT_VIEW);
define('DIR_PRIVATE',DIR_PROJECT_PRIVATE);

define('DB_HOST',$_ENV['DB_HOST']);
define('DB_PORT',$_ENV['DB_PORT']);
define('DB_NAME',$_ENV['DB_NAME']);
define('DB_USER',$_ENV['DB_USER']);
define('DB_PASSWORD',$_ENV['DB_PASSWORD']);

if(file_exists(DIR_PROJECT_ROOT.'vendor/autoload.php'))
{
    require_once(DIR_PROJECT_ROOT.'vendor/autoload.php');
}

require_once DIR_PROJECT_CONFIG.'routes.php';

require_once(DIR_PROJECT_CONTROLLER.'Home.php');
require_once(DIR_PROJECT_MODEL.'Produit.php');
require_once(DIR_PROJECT_MODEL.'Categorie.php');
?>
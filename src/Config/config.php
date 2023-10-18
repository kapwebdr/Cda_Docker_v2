<?php
header('Content-Type: text/html; charset=utf-8'); 
header('Vary: User-Agent');
date_default_timezone_set("Europe/Paris");
ini_set('memory_limit', '1G');

define('DIR_BASE',__DIR__.'/../');
define('DIR_APP',DIR_BASE.'App/');
define('DIR_APP_CONFIG',DIR_BASE.'Config/');
define('DIR_APP_CONTROLLER',DIR_APP.'Controller/');
define('DIR_APP_MODEL',DIR_APP.'Model/');
define('DIR_PUBLIC',DIR_BASE.'Public/');
define('DIR_PROJECT',DIR_BASE.'Projects/');

require_once(DIR_APP_CONFIG.'projects.php');
require_once(DIR_BASE.'vendor/autoload.php');

ini_set( "display_errors", "off" );
error_reporting( E_ALL );

set_error_handler(['\App\Controller\ErrorHandler', 'PhpErrors'], E_ALL);
register_shutdown_function(['\App\Controller\ErrorHandler', 'PhpFatalErrors']);

$project 	= (isset($allowed[$_SERVER['HTTP_HOST']]))?$allowed[$_SERVER['HTTP_HOST']]:null;

if(is_null($project))
{
	exit('Domain name not allowed :: '.$_SERVER['HTTP_HOST']);
}

if(is_array($project))
{
	define('PROJECT',$project['name']);
	define('DIR_PROJECT_PRIVATE',DIR_PROJECT.PROJECT.'/Private/');
	define('DIR_PROJECT_CONFIG',DIR_PROJECT.PROJECT.'/Config/');
	define('DIR_PROJECT_VIEW',DIR_PROJECT.PROJECT.'/View/');
	define('DIR_PROJECT_MODEL',DIR_PROJECT.PROJECT.'/Model/');
	define('DIR_PROJECT_CONTROLLER',DIR_PROJECT.PROJECT.'/Controller/');
	
	$dotenv = Dotenv\Dotenv::createImmutable(DIR_PROJECT_CONFIG.$project['env'].'/');
	$dotenv->safeLoad();
}

if(!is_array($project) || count($project)<1)
{
	exit('Domain name not allowed');
}

if(!isset($project['config']['show_errors']))
{
	ini_set('display_errors',false);
}
else
{
	error_reporting($project['config']['show_errors']);
	ini_set('display_errors',true);	
}
if(file_exists(DIR_PROJECT_CONFIG.'/init.php'))
{
	require_once(DIR_PROJECT_CONFIG.'/init.php');
}
?>
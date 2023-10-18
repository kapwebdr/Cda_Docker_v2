<?php
namespace App\Controller;

use App\Controller\Session;
class ErrorHandler extends \Exception
{
	static private $userError = [];

	static function setUserError($error,$level='warning')
	{
		self::$userError[] = ['error'=>$error,'level'=>$level,'date'=>time()];
	}
	static function getUserError()
	{
		return self::$userError;
	}

	static function saveUserError()
	{
		Session::Set('UserError',self::$userError);
	}
    public function __construct(string $message='',int $code=0,$previous=null)
    {
        parent::__construct($message, $code, $previous);
    }
    static function PhpFatalErrors()
	{
		$lastError = error_get_last();
		if(!is_null($lastError))
        static::PhpErrors($lastError['type'],$lastError['message'],$lastError['file'],$lastError['line']);
    }
	/* It's a function that will be called when a PHP error occurs. */
	static function PhpErrors($errno,$errstr,$errfile,$errline)
	{
        $Action = [];
        $Action['msg'] 		='';
		$Action['exit'] 	=true;
		$Action['display']	=false;
		$Action['notify']	=false;
		
		switch ($errno)
		{
			case E_USER_NOTICE :
			case E_NOTICE :
			{
			    $Action['exit'] 	= false;
			    $Action['display'] 	= true;
			    $Action['notify'] 	= true;
			    $Action['type'] 	= "Notification (".$errno.")";
				break;
			}
			case E_COMPILE_WARNING :
			case E_CORE_WARNING :
			case E_USER_WARNING :
			case E_WARNING :
			case E_DEPRECATED :
			case E_USER_DEPRECATED :
			{
			    $Action['exit'] 	= false;
			    $Action['display'] 	= true;
			    $Action['notify'] 	= false;
			    $Action['type'] 	= "Avertissement (".$errno.")";
			    break;
			}
			case E_PARSE :
			{
			   	$Action['exit'] 	= true;
			    $Action['display'] 	= true;
			    $Action['notify'] 	= true;
			    $Action['type'] 	= "Syntaxe (".$errno.")";
			    break;
			}
			case E_COMPILE_ERROR :
			case E_CORE_ERROR :
			case E_USER_ERROR :
			case E_ERROR :
			{
			   	$Action['exit'] 	= true;
			    $Action['display'] 	= true;
			    $Action['notify'] 	= true;
			    $Action['type'] 	= "Erreur Fatale (".$errno.")";
			    break;
			}
			default :
			{
			    $Action['exit'] 	= false;
			    $Action['display'] 	= true;
			    $Action['notify'] 	= true;
			    $Action['type'] 	= "Erreur inconnue (".$errno.")";
			    break;
			}
	    }
	    
	    if($Action['display'] === true)
	    {
		    echo $Action['type'].'['.$errno.' : '.$errstr.' : '.$errfile.' :: '.$errline.']<br/><br/>';
		}
		if($Action['notify'] === true)
	    {
			// Exemple, envoie de mail etc...
		}
	    if($Action['exit'] === true)
	    {
		    exit();
		}
	}
	static function RouteErrors($errno,$errstr,$errurl)
	{
		// A modifier par vous même.
		die('Error N ' . $errno);
	}
}
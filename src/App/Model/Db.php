<?php
namespace App\Model;
use Illuminate\Database\Capsule\Manager as Capsule;
// use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
        
class Db
{
    static $db=null;
    
    static function Connect()
    {
        if(is_null(self::$db))
        {
            self::$db = new Capsule;

            self::$db->addConnection([
                'driver' => 'mysql',
                'host' => DB_HOST,
                'database' => DB_NAME,
                'username' => DB_USER,
                'password' => DB_PASSWORD,
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix' => '',
            ]);

            // Set the event dispatcher used by Eloquent models... (optional)
            //self::$db->setEventDispatcher(new Dispatcher(new Container));
            // Make this Capsule instance available globally via static methods... (optional)
            self::$db->setAsGlobal();
            // Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
            self::$db->bootEloquent();
        }
    }
}

<?php
namespace App\Model;

use PDO;
class Db
{
    static $db=null;
    private array $datas;
    
    static function Connect()
    {
        if(is_null(self::$db))
        {
            $dsn    = 'mysql:host='.DB_HOST.';dbname='.DB_NAME;
            try {
                self::$db =  new PDO($dsn,DB_USER,DB_PASSWORD);
                self::$db->exec("SET NAMES 'UTF8'");
                self::$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
    }
    public function __construct()
    {
        self::Connect();
    }
    public function __set($var,$value)
    {
        $this->datas[$var] = $value;
    }

    public function __get($var)
    {
        return $this->datas[$var];
    }

    public function Save()
    {
        $columns = array_keys($this->datas);

        $sql    = 'insert into '.$this->table.' set ';

        foreach($columns as $key=>$column)
        {
            $sql   .= $column.'=:'.$column;
            if($key < (count($columns)-1))
            $sql   .= ',';
        }
        
        $rq = self::$db->prepare($sql);
        $rq->execute($this->datas);
        return self::$db->lastInsertId();
    }

    static function Update()
    {

    }

    static function Delete()
    {

    }

    static function Find($sql,$datas)
    {
        $rq = self::$db->prepare($sql);
        $rq->execute($datas);
        return $rq->fetchAll();
    }

    static function FindOne($sql,$datas)
    {
        $rq = self::$db->prepare($sql);
        $rq->execute($datas);
        return $rq->fetch();
    }

}

?>
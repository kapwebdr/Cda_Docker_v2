<?php
namespace App\Model;

use PDO;
class Db
{
    static $db=null;
    protected string $table;
    protected array $primaryKeys = [];
    protected array $whereConditions = [];
    protected array $joinConditions = [];
    
    protected array $relations = [];


    private $_result = [];

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
        if (empty($this->primaryKeys)) {
            // Par défaut, on utilise 'id' comme clé primaire si $primaryKeys n'est pas défini.
            $this->primaryKeys = ['id'];
        }
    }

    private function getProperties()
    {
        $properties = get_object_vars($this);
        unset($properties['table'], $properties['primaryKeys'], $properties['whereConditions'], $properties['joinConditions'],$properties['_result'], $properties['relations'],);
        return $properties;
    }

    public function Join(array $join): static
    {
        $this->joinConditions[] = $join;
        return $this;
    }

    public function With(string $relation, string $modelClass, string $foreignKey, string $localKey): static
    {
        $this->relations[$relation] = [
            'model' => $modelClass,
            'foreignKey' => $foreignKey,
            'localKey' => $localKey
        ];
        return $this;
    }

    private function affectResult($object,$data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($object, $key)) {
                $object->$key = $value;
            }
        }
        foreach ($this->relations as $relation => $config) {
            $relatedModel = new $config['model']();
            $relatedModel->Where([$config['foreignKey'] => $object->{$config['localKey']}]);
            $object->$relation = $relatedModel->FindOne();
        }
    }
    public function Find()
    {
        $whereSql = $this->generateWhere();
        $joinSql = $this->generateJoin();
        $sql = "SELECT * FROM {$this->table}{$joinSql}{$whereSql}";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute($this->getWhereValues());
        $dataAll = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->_result = $dataAll;
        if ($dataAll) {
            foreach ($dataAll as $data) {
                $item = clone $this;
                $this->affectResult($item,$data);
            }
        }
        
        return $this;
    }

    public function FindOne()
    {
        $whereSql = $this->generateWhere();
        $joinSql = $this->generateJoin();
        $sql = "SELECT * FROM {$this->table}{$joinSql}{$whereSql}";
        $stmt = self::$db->prepare($sql);
        $stmt->execute($this->getWhereValues());
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->_result = $data;
        if ($data) {
            $this->affectResult($this,$data);
        }
        return $this;
    }
  
    public function Save():object
    {
        $properties = $this->getProperties();
        
        $isUpdate = true;
        foreach ($this->primaryKeys as $primaryKey) {
            if (empty($properties[$primaryKey])) {
                $isUpdate = false;
                break;
            }
        }
        
        if ($isUpdate) {
            return $this->Update();
        } else {
            return $this->Insert();
        }
    }
    
    public function Insert()
    {
        $properties = $this->getProperties();
        
        $columns = array_keys($properties);
        $placeholders = array_map(fn($prop) => ":$prop", $columns);
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = self::$db->prepare($sql);
        
        foreach ($properties as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->execute();
        foreach ($this->primaryKeys as $primaryKey) {
            if (property_exists($this, $primaryKey)) {
                $lastInsertId = self::$db->lastInsertId($primaryKey);
                if ($lastInsertId) {
                    $this->$primaryKey = $lastInsertId;
                }
            }
        }
        // Ajouter le lastInsertId dans l'Id de la propriété 
        return $this;
    }
    public function Update()
    {
        $properties = $this->getProperties();
         
        $setClause = implode(', ', array_map(fn($prop) => "$prop = :$prop", array_keys($properties)));
        $whereClauseParts = [];
        foreach ($this->primaryKeys as $primaryKey) {
            if (array_key_exists($primaryKey, $properties)) {
                $whereClauseParts[] = "$primaryKey = :$primaryKey";
            } else {
                throw new \Exception("La clé primaire $primaryKey doit être présente pour effectuer une mise à jour");
            }
        }
        $whereClause = implode(' AND ', $whereClauseParts);
        
        $sql = "UPDATE {$this->table} SET $setClause WHERE $whereClause";
        $stmt = self::$db->prepare($sql);
        
        foreach ($properties as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        $stmt->execute();
        return $this;
    }

    public function Delete()
    {
        $whereSql = $this->generateWhere();
        $sql = "DELETE FROM {$this->table}{$whereSql}";
        $stmt = self::$db->prepare($sql);
        $stmt->execute($this->whereConditions);
        return $this;
    }

    public function Where($conditions, $operator = 'AND'): static
    {
        if (!is_array($conditions)) {
            throw new \InvalidArgumentException('Conditions must be an array');
        }

        if (!empty($this->whereConditions)) {
            $this->whereConditions[] = $operator;
        }
        
        $this->whereConditions[] = $conditions;
        
        return $this;
    }

    protected function generateWhere(): string
    {
        if (empty($this->whereConditions)) {
            return '';
        }
        $conditions = $this->buildWhereConditions($this->whereConditions);
        return " WHERE $conditions";
    }

    protected function buildWhereConditions($conditions): string
    {
        $parts = [];

        foreach ($conditions as $key => $value) {
            if (is_string($key) && !is_array($value)) {
                // Pour les conditions simples telles que ['colonne' => 'valeur']
                $parts[] = "$key = :$key";
            } 
            elseif (is_array($value)) {
                if (isset($value['column'], $value['operator'], $value['value'])) {
                    // Pour des conditions personnalisées telles que ['column' => 'age', 'operator' => '>', 'value' => 21]
                    $parts[] = "{$value['column']} {$value['operator']} :{$value['column']}";
                } elseif (count($value) === 3 && isset($value[0], $value[1], $value[2])) {
                    // Pour des conditions personnalisées sous forme de tableau ['age', '>', 21]
                    $parts[] = "{$value[0]} {$value[1]} :{$value[0]}";
                } else {
                    // Pour des conditions groupées, récursivement
                    $parts[] = '(' . $this->buildWhereConditions($value) . ')';
                }
            } elseif (is_string($value) && in_array(strtoupper($value), ['AND', 'OR'])) {
                // Pour les opérateurs logiques
                $parts[] = $value;
            }
        }

        return implode(' ', $parts);
    }
    protected function getWhereValues($conditions = null): array
    {
        $values = [];
        if ($conditions === null) {
            $conditions = $this->whereConditions;
        }

        foreach ($conditions as $key => $value) {
            if (is_string($key) && !is_array($value)) {
                // Pour les conditions simples telles que ['colonne' => 'valeur']
                $values[$key] = $value;
            } elseif (is_array($value)) {
                if (isset($value['column'], $value['value']) && !is_array($value['value'])) {
                    // Pour des conditions personnalisées telles que ['column' => 'age', 'operator' => '>', 'value' => 21]
                    $values[$value['column']] = $value['value'];
                } elseif (count($value) === 3 && isset($value[0], $value[2]) && !is_array($value[2])) {
                    // Pour des conditions personnalisées sous forme de tableau ['age', '>', 21]
                    $values[$value[0]] = $value[2];
                } else {
                    // Pour des conditions groupées, récursivement
                    $values = array_merge($values, $this->getWhereValues($value));
                }
            }
        }

        return $values;
    }
    protected function generateJoin(): string
    {
        if (empty($this->joinConditions)) {
            return '';
        }

        $joins = array_map(fn($join) => "{$join['type']} JOIN {$join['table']} ON {$join['condition']}", $this->joinConditions);
        return ' ' . implode(' ', $joins);
    }

    static function Sql($sql,$datas)
    {
        $rq = self::$db->prepare($sql);
        $rq->execute($datas);
        return $rq;
    }


    public function toArray(): array
    {
        return $this->_result;  
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

}

?>
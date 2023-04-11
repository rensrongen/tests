<?php

class Entity
{
    private static $InsertSQL = "INSERT INTO entity (name, title, notes, score) VALUES(:name, :title, :notes, :score)";
    private static $UpdateSQL = "UPDATE entity SET name=:name, title=:title, notes=:notes, score=:score WHERE id=:id OR name=:name";
    public $id;
    public $name;
    public $title;
    public $notes;
    public $score;
    function __construct($name, $title = '', $notes = '', $score = 0, $id = null) {
        $this->id = $id;
        $this->name = $name;
        $this->title = $title;
        $this->notes = $notes;
        $this->score = $score;
    }

    public function serialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'title' => $this->title,
            'notes' => $this->notes,
            'score' => $this->score
        ];
    }

    public static function store(Entity $entity) {
        $pdo = Entity::ConnectPDO();
        if ($pdo === false) {
            return false;
        }

        $statement = $pdo->prepare(self::$InsertSQL);
        $data = $entity->serialize();
        $statement->execute($data);

        $entity->id = $pdo->lastInsertId();

        Entity::DisconnectPDO($pdo);
    }
    
    public static function update(Entity $entity) {
        $pdo = Entity::ConnectPDO();
        if ($pdo === false) {
            return false;
        }

        $statement = $pdo->prepare(self::$UpdateSQL);
        $data = $entity->serialize();
        $statement->execute($data);

        Entity::DisconnectPDO($pdo);
    }

    // Batch insert many rows
    public static function storeMultiple($entities, $batchSize = 500)
    {
        $mysqli = Entity::ConnectMySQLI();
        if ($mysqli === false) {
            return false;
        }
        $count = count($entities);
        $batch = $count >= $batchSize ? $batchSize : $count;
        $batchCount = ceil($count / $batch);
        $index = 0;
        for ($n = 0; $n < $batchCount; $n++) {
            $query = "INSERT INTO entity (name, title, notes, score) VALUES ";
            $nextBatchSize = $index + $batch >= $count ? $count - $index : $batch;
            for ($i = 0; $i < $nextBatchSize; $i++) {
                $entity = $entities[$index + $i];
                $query .= "('$entity->name', '$entity->title', '$entity->notes', '$entity->score'),";
            }
            $query[strlen($query) - 1] = ' ';
            $mysqli->query($query);
            $index += $batch;
        }
        Entity::DisconnectMySQLI($mysqli);
    }

    private static function ConnectPDO() {
        $HOST = 'localhost';
        $DB = 'tripolis';
        $USER = 'root';
        $PASSWORD = '';
        $DSN = "mysql:host={$HOST};dbname=$DB";
        try {
            $connection = new PDO($DSN, $USER, $PASSWORD, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
        return $connection;
    }

    private static function DisconnectPDO(&$connection) {
        $connection = null;
    }

    private static function ConnectMySQLI() {
        $HOST = 'localhost';
        $DB = 'tripolis';
        $USER = 'root';
        $PASSWORD = '';
        try {
            $connection = mysqli_connect($HOST, $USER, $PASSWORD, $DB);
        } catch (mysqli_sql_exception $e) {
            echo $e->getMessage();
            return false;
        }
        return $connection;
    }

    private static function DisconnectMySQLI(&$connection) {
        $connection->close();
    }

    public static function generateTestEntities($num)
    {
        $entities = [];
        for ($i = 0; $i < $num; $i++) {
            $entity = new Entity('N:'.uniqid(), 'T:'.uniqid(), 'Notes... \n'.uniqid().'..', random_int(0, 1e5));
            $entities[] = $entity;
        }
        return $entities;
    }
}

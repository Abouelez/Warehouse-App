<?php

namespace App\Models;

use Config\Database;
use Exception;
use PDO;

class Model
{
    protected static $connection;
    protected  $table = null;
    protected $id;
    protected $attributes = [];
    protected $data = [];

    public function __construct()
    {
        self::$connection = Database::get_instance()->get_connection();
        if (!$this->table) {
            $class = explode('\\', get_called_class()); //it will return => App\Models\items, we need also 'items'
            $this->table = strtolower($class[array_key_last($class)]) . "s";     //User class will be users ;)
        }
    }

    //Get value of specific attribute(Magic method)
    // public function __get($name)
    // {
    //     if (in_array($name, $this->attributes)) {
    //         return $this->data[$name] ?? null;
    //     }
    //     throw new Exception("Attribute $name does not exist in " . get_called_class());
    // }

    // //Set value of specific attribute(Magic method)
    // public function __set($name, $value)
    // {
    //     if (in_array($name, $this->attributes)) {
    //         $this->data[$name] = $value;
    //     } else {
    //         throw new Exception("Attribute $name does not exist in " . get_called_class());
    //     }
    // }

    // Static method to get all records
    public static function all()
    {
        $instance = new static();

        $query = "SELECT * FROM " . $instance->table;
        $stmt = self::$connection->query($query);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find($search_key, $search_column = 'id')
    {

        $instance = new static();
        $query = "SELECT * FROM {$instance->table} WHERE $search_column = :$search_column";

        $stmt = self::$connection->prepare($query);

        $stmt->execute([$search_column => $search_key]);
        $record = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($record) {
            return $record;
        }

        return false;
    }

    public static function insert($data)
    {
        $instance = new static();
        //Validate that all keys exist in attributes
        foreach ($data as $key => $value) {
            if (!in_array($key, $instance->attributes))
                throw new Exception("Attribute $key does not exist in " . get_called_class());
        }

        $cols = implode(',', array_keys($data)); //Get columns names
        $placeholders = implode(',', array_fill(0, count($data), '?')); // Generate placeholders

        //Prepare SQL statement
        $query = "INSERT INTO {$instance->table} ($cols) VAlUES ($placeholders)";
        $stmt = self::$connection->prepare($query);

        $values = array_values($data);

        if ($stmt->execute($values)) {

            return self::find(self::$connection->lastInsertId());
        }
        return false;
    }

    public function update($id, $data)
    {

        // Validate that all keys exist in attributes
        foreach ($data as $key => $value) {
            if (!in_array($key, $this->attributes)) {
                throw new Exception("Attribute $key does not exist in " . get_called_class());
            }
        }

        //Generate the SET part of query
        $set_clause = implode(',', array_map(fn($key) => "$key = ?", array_keys($data)));

        $query = "UPDATE {$this->table} SET {$set_clause} WHERE id = ?";
        $stmt = self::$connection->prepare($query);

        $values = array_values($data);
        $values[] = $id;
        if ($stmt->execute($values)) {

            return self::find($id);
        }
        return false;
    }

    public function delete($id)
    {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = self::$connection->prepare($query);

        return $stmt->execute([$id]);
    }
}

<?php

namespace App\Models;

use Config\Database;
use Exception;
use PDO;

class Model
{
    protected $connection;
    protected $table = null;
    protected $id;
    protected $attributes = [];
    protected $data = [];

    public function __construct()
    {
        $this->connection = Database::get_instance()->get_connection();
        if (!$this->table)
            $this->table = strtolower(get_called_class() . 's');  //User class will be users ;)
    }

    //Get value of specific attribute(Magic method)
    public function __get($name)
    {
        if (in_array($name, $this->attributes)) {
            return $this->data[$name] ?? null;
        }
        throw new Exception("Attribute $name does not exist in " . get_called_class());
    }

    //Set value of specific attribute(Magic method)
    public function __set($name, $value)
    {
        if (in_array($name, $this->attributes)) {
            $this->data[$name] = $value;
        } else {
            throw new Exception("Attribute $name does not exist in " . get_called_class());
        }
    }

    // Static method to get all records
    public static function all()
    {
        $instance = new static(); // Create an instance of the class
        $query = "SELECT * FROM " . $instance->table;
        $stmt = $instance->connection->query($query);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find($id)
    {
        $instance = new static();
        $query = "SELECT * FROM {$instance->table} WHERE id = ?";
        $stmt = $instance->connection->prepare($query);
        $stmt->execute([$id]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($record) {
            $instance->id = $id;
            foreach ($record as $key => $value) {
                $instance->data[$key] = $value;
            }
            return $instance;
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

        $cols = implode(',', array_values($instance->attributes)); //Get columns names
        $placeholders = implode(',', array_fill(0, count($data), '?')); // Generate placeholders

        //Prepare SQL statement
        $query = "INSERT INTO {$instance->table} ($cols) VAlUES ($placeholders)";
        $stmt = $instance->connection->prepare($query);

        $values = array_values($data);

        if ($stmt->execute($values)) {
            $instance->id = $instance->connection->lastInsertId();

            foreach ($data as $key => $value) {
                $instance->$key = $value;
            }
            return $instance;
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
        $set_clause = implode(',', array_map(fn($key) => "$key = ?", $this->attributes));

        $query = "UPDATE {$this->table} SET {$set_clause} WHERE id = ?";
        $stmt = $this->connection->prepare($query);

        $values = array_values($data);
        $values[] = $id;
        if ($stmt->execute($values)) {
            $this->id = $id;
            foreach ($data as $key => $value) {
                $this->$key = $value;
            }
            return $this;
        }
        return false;
    }

    public function delete($id)
    {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->connection->prepare($query);

        return $stmt->execute([$id]);
    }
}

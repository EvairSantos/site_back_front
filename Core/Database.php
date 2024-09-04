<?php

namespace Core;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        $host = 'localhost';
        $dbname = 'mercadinho';
        $user = 'root';
        $password = '';

        try {
            $this->connection = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Falha na conexão com o banco de dados: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __clone() { }

    public function __wakeup() { }

    public function getConnection()
    {
        return $this->connection;
    }

    public function select($table, $conditions = [])
    {
        $sql = "SELECT * FROM $table";

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', array_map(function ($key) {
                return "$key = :$key";
            }, array_keys($conditions)));
        }

        $stmt = $this->connection->prepare($sql);

        if (!empty($conditions)) {
            foreach ($conditions as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($table, $data)
    {
        $columns = implode(',', array_keys($data));
        $placeholders = implode(',', array_map(function ($key) {
            return ":$key";
        }, array_keys($data)));

        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";

        $stmt = $this->connection->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        return $stmt->execute();
    }

    public function update($table, $data, $conditions)
    {
        $setClause = implode(',', array_map(function ($key) {
            return "$key = :$key";
        }, array_keys($data)));

        $conditionClause = implode(' AND ', array_map(function ($key) {
            return "$key = :$key";
        }, array_keys($conditions)));

        $sql = "UPDATE $table SET $setClause WHERE $conditionClause";

        $stmt = $this->connection->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        foreach ($conditions as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        return $stmt->execute();
    }

    public function delete($table, $conditions)
    {
        $conditionClause = implode(' AND ', array_map(function ($key) {
            return "$key = :$key";
        }, array_keys($conditions)));

        $sql = "DELETE FROM $table WHERE $conditionClause";

        $stmt = $this->connection->prepare($sql);

        foreach ($conditions as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        return $stmt->execute();
    }
}

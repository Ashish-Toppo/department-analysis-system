<?php

class Database {

    private $host = HOST;
    private $user = USER;
    private $database = DATABASE;
    private $password = PASSWORD;

    private $con;
    private $obj;
    private $table;
    private $params = [];

    // Constructor with optional table name parameter
    public function __construct($table = null) {
        $this->table = $table; // Allow table name to be set during instantiation
        try {
            $this->con = new PDO('mysql:host='.$this->host.';dbname='.$this->database, $this->user, $this->password);
        } catch (PDOException $e) {
            showDBConnectionError($e);
        }
    }

    // Method to dynamically set table name
    public function setTable($table) {
        $this->table = $table;
        return $this;
    }

    // Method to clear current query object
    public function clearQuery() {
        $this->obj = "";
        return $this;
    }

    // Method to dynamically query the database
    public function query($qry, $params = []) {
        try {
            $result = $this->con->prepare($qry);
            if (empty($params)) {
                $result->execute();
            } else {
                $result->execute($params);
            }
            return $result->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // Fetch specific fields with chaining
    public function fetch($fields = '*') {
        if (is_array($fields)) {
            $fields = implode('`, `', $fields);
            $this->obj = "SELECT `$fields` FROM `$this->table` WHERE 1";
        } else {
            $this->obj = "SELECT $fields FROM `$this->table` WHERE 1";
        }

        return $this; // Allows chaining with ->where(), etc.
    }

    // Set the ORDER BY clause
    public function orderBy($column, $direction = 'ASC') {
        $this->obj .= " ORDER BY `$column` $direction";
        return $this;
    }

    // Where clause with chaining (only sets the condition)
    public function where($condition, $params = []) {
        // Check if the query already contains a WHERE clause
        if (strpos($this->obj, 'WHERE') === false) {
            $this->obj .= " WHERE $condition";
        } else {
            $this->obj .= " AND $condition";
        }

        // Store parameters for the query
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    // Run the query and execute with the parameters
    public function run() {
        try {
            $prepared = $this->con->prepare($this->obj);
            $prepared->execute($this->params);

            // Clear query and parameters after execution
            $this->resetQuery();

            // Fetch and return the result set
            return $prepared->fetchAll();
        } catch (PDOException $e) {
            // Handle exception if needed
            echo "Error: " . $e->getMessage();
        }
    }

    // Reset the query and params after execution to reuse the object
    public function resetQuery() {
        $this->obj = '';
        $this->params = [];
    }

    // Fetch all records with optional conditions
    public function fetchAll() {
        $query = "SELECT * FROM `$this->table` WHERE 1";
        return $this->con->query($query)->fetchAll();
    }

    // Insert into database
    public function insert($data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO `$this->table` ($columns) VALUES ($placeholders)";
        $prepared = $this->con->prepare($sql);
        return $prepared->execute(array_values($data));
    }

    // Update records
    public function update($data, $condition, $params = []) {
        $set = implode(', ', array_map(fn($col) => "`$col` = ?", array_keys($data)));
        $sql = "UPDATE `$this->table` SET $set WHERE $condition";
        $prepared = $this->con->prepare($sql);
        return $prepared->execute(array_merge(array_values($data), $params));
    }

    // Delete records
    public function delete($condition, $params = []) {
        $sql = "DELETE FROM `$this->table` WHERE $condition";
        $prepared = $this->con->prepare($sql);
        return $prepared->execute($params);
    }

    // Count records
    public function count($condition = '1', $params = []) {
        $sql = "SELECT COUNT(*) FROM `$this->table` WHERE $condition";
        $prepared = $this->con->prepare($sql);
        $prepared->execute($params);
        return $prepared->fetchColumn();
    }

    // Check if a record exists
    public function exists($condition, $params = []) {
        $sql = "SELECT 1 FROM `$this->table` WHERE $condition LIMIT 1";
        $prepared = $this->con->prepare($sql);
        $prepared->execute($params);
        return (bool) $prepared->fetch();
    }

    // Last Insert ID
    public function getLastInsertId() {
        return $this->con->lastInsertId();
    }

    // Transaction methods
    public function beginTransaction() {
        return $this->con->beginTransaction();
    }

    public function commit() {
        return $this->con->commit();
    }

    public function rollBack() {
        return $this->con->rollBack();
    }

    // Join tables
    public function join($table, $on, $type = 'INNER') {
        $this->obj .= " $type JOIN `$table` ON $on ";
        return $this;
    }

    

    // Limit records
    public function limit($limit, $offset = 0) {
        $this->obj .= " LIMIT $offset, $limit";
        return $this;
    }

    // Pagination helper
    public function paginate($perPage, $currentPage = 1) {
        $offset = ($currentPage - 1) * $perPage;
        return $this->limit($perPage, $offset);
    }

}

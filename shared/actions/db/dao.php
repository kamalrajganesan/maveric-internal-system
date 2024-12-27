<?php

require_once("../shared/php/connect.php");

class sqlHelper
{
    private $conn, $f, $query;

    public function __construct()
    {
        if (!session_id()) {
            session_start();
        }
        $this->f = "sql_error.log";
        $this->conn = createConn();        
    }

    public function __destruct() {
        $this->conn->close();
    }

    public function prepareStatement($sql) {

        $this->query = $this->conn->prepare($sql);
    }

    public function setParameters($params, $type) {
        
        $this->query->bind_param($type, ...$params);
    }

    public function execPreparedStatement() {
     
        try {
            $this->query->execute();
        } catch (Exception $e) {
            $this->logError($e->getMessage(), [ 'exception' => $e ]);
        }
    }

    public function getResultSet() {
        
        return $this->query->get_result();
    }


    private function logError($param1, $param2) {
        
        file_put_contents($this->f, PHP_EOL, FILE_APPEND | LOCK_EX);
        file_put_contents($this->f, print_r("Error --------------------".PHP_EOL, true), FILE_APPEND | LOCK_EX);
        file_put_contents($this->f, print_r($param1, true), FILE_APPEND | LOCK_EX);
        file_put_contents($this->f, print_r("Cause ====================".PHP_EOL, true), FILE_APPEND | LOCK_EX);
        file_put_contents($this->f, print_r($param2, true), FILE_APPEND | LOCK_EX);
        file_put_contents($this->f, PHP_EOL, FILE_APPEND | LOCK_EX);
    }

}

?>
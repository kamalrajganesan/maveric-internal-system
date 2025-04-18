<?php

require_once("../shared/php/connect.php");

class sqlHelper
{
    private $conn, $f, $query;

    public function __construct()
    {
        date_default_timezone_set('Asia/Kolkata');
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
     
        $resp["success"] = false;
        $resp["message"] = "";
        try {
            $this->query->execute();
            $resp["success"] = true;
            $resp["message"] = "Query ran successfully...!";
            return $resp;
        } catch (Exception $e) {
            $resp["message"] = $e->getMessage();
            $this->logError($e->getMessage(), [ 'exception' => $e ]);
            return $resp;
        }
    }

    public function getResultSet() {
        
        return $this->query->get_result();
    }


    public function logError($param1, $param2) {
        
        file_put_contents($this->f, PHP_EOL, FILE_APPEND | LOCK_EX);
        file_put_contents($this->f, print_r("Error --------------------".PHP_EOL, true), FILE_APPEND | LOCK_EX);
        file_put_contents($this->f, print_r($param1, true), FILE_APPEND | LOCK_EX);
        file_put_contents($this->f, PHP_EOL, FILE_APPEND | LOCK_EX);
        file_put_contents($this->f, print_r("Cause ====================".PHP_EOL, true), FILE_APPEND | LOCK_EX);
        file_put_contents($this->f, print_r($param2, true), FILE_APPEND | LOCK_EX);
        file_put_contents($this->f, PHP_EOL, FILE_APPEND | LOCK_EX);
    }

}

?>
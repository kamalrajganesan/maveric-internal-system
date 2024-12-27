<?php

require_once("../shared/actions/db/dao.php");

if(isset($_POST['tId'])) {
    
    $db = new sqlHelper();
    $transacFetchSingleSQL = "SELECT * FROM ticket WHERE is_deleted = 0 and uniq_id = ?";

    $db->prepareStatement($transacFetchSingleSQL);
    $db->setParameters([$_POST['tId']], 's');
    $db->execPreparedStatement();
    $transacFetchSingleSQLResultSet = $db->getResultSet();

    if ($transacFetchSingleSQLResultSet->num_rows > 0) {
        $data = array();
        while ($row = $transacFetchSingleSQLResultSet->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode(array("success" => true, "data" => $data, "message" => "Data found."));
    } else {
        echo json_encode(array("success" => false, "data" => [], "message" => "No data found."));
    }
}

?>
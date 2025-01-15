<?php

require_once("../shared/actions/db/dao.php");
date_default_timezone_set('Asia/Kolkata');

$valid = array('success' => false, 'message' => "");


if(isset($_POST['tId'])) {
    

    try {
        $db = new sqlHelper();
        $transacFetchSingleSQL = "SELECT * FROM ticket WHERE is_deleted = 0 and uniq_id = ?";

        $db->prepareStatement($transacFetchSingleSQL);
        $db->setParameters([$_POST['tId']], 's');
        $resp = $db->execPreparedStatement();
        $transacFetchSingleSQLResultSet = $db->getResultSet();

        if( $resp['success'] ) {
            $data = array();
            if ($transacFetchSingleSQLResultSet->num_rows > 0) {
                while ($row = $transacFetchSingleSQLResultSet->fetch_assoc()) {
                    $data[] = $row;
                }
                $valid['success'] = true;
                $valid['data'] = $data;
                $valid['message'] = "Data found.";
            } else {
                $valid['success'] = false;
                $valid['message'] = "Default";
            }
        } else {
            $valid['success'] = false;
            $valid['message'] = "Default";
        }
    } catch (Exception $e) {
            
        $valid['success'] = false;
        $valid["message"] = 'Exception';
        $valid["detailed"] = $e->getMessage();
    }
}

?>
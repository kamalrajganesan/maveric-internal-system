<?php

require_once("../shared/actions/db/dao.php");
date_default_timezone_set('Asia/Kolkata');

$valid = array('success' => false, 'message' => "");

function getCustomerDetails($dbConn, $param) {

    $assocCustomer = "SELECT * from cust_mstr WHERE is_deleted = 0 and id = ?";

    $dbConn->prepareStatement($assocCustomer);
    $dbConn->setParameters([$param], 'i');

    $resp = $dbConn->execPreparedStatement();
    if($resp['success'] == TRUE) {
        
        $cSQLResultSet = $dbConn->getResultSet();
        $cValid = array('success' => false, 'message' => "");
        if ($cSQLResultSet->num_rows > 0) {
            
            while ($cRow = $cSQLResultSet->fetch_assoc()) {
                $cData[] = $cRow;
            }
            $cValid['success'] = true;
            $cValid['data'] = $cData;
            $cValid['message'] = "Data found.";
        } else {
            
            $cValid['success'] = false;
            $cValid['message'] = "Default";
        }
    } else {

    }
    return $cValid;
}

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

                $data = [];
                while ($row = $transacFetchSingleSQLResultSet->fetch_assoc()) {
                    $data[] = $row;
                }
                $valid['success'] = true;
                $valid['data'] = $data;
                $valid['message'] = "Data found.";
                $valid['cData'] = getCustomerDetails($db, $valid['data'][0]['customer_id']);
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

echo json_encode($valid);

?>
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

            $formatted_updated_on = new DateTime($cData[0]["updated_on"]);
            $cData[0]["updated_on"] = $formatted_updated_on->format('d-m-Y h:i:s A');

            $formatted_created_on = new DateTime($cData[0]["created_on"]);
            $cData[0]["created_on"] = $formatted_created_on->format('d-m-Y h:i:s A');

            $fieldsToFormat = ['amc_st_date', 'amc_end_date', 'tally_st_date', 'tally_end_date', 'cloud_st_date', 'cloud_end_date'];
            foreach ($fieldsToFormat as $field) {
                if (!empty($cData[0][$field])) {
                    $formattedDate = new DateTime($cData[0][$field]);
                    $cData[0][$field] = $formattedDate->format('d-m-Y');
                }
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

                $formatted_updated_on = new DateTime($data[0]["updated_on"]);
                $data[0]["updated_on"] = $formatted_updated_on->format('d-m-Y h:i:s A');

                $formatted_updated_on = new DateTime($data[0]["created_on"]);
                $data[0]["created_on"] = $formatted_updated_on->format('d-m-Y h:i:s A');

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
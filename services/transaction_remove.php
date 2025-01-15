<?php

require_once("../shared/actions/db/dao.php");
date_default_timezone_set('Asia/Kolkata');

$valid = array('success' => false, 'message' => "");

// Check if the form data is posted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $tId = $_POST['ticketId'];
    if($tId) {

        $db = new sqlHelper();
        $query = "UPDATE ticket set is_deleted = 1 WHERE uniq_id = ?";

        try {
            
            // Prepare the statement
            $stmt = $db->prepareStatement($query);

            // Parameters for binding
            $params = [$tId];
            $types = 's';
            $db->setParameters($params, $types);

            // Execute the statement
            $resp = $db->execPreparedStatement();
            
            if( $resp['success'] ) {
                $valid['success'] = true;
                $valid['message'] = "Successfully removed the Transaction";
            } else {
                $valid['success'] = false;
                $valid['message'] = "Default";
            }
        } catch (Exception $e) {
            
            $valid['success'] = false;
            $valid["message"] = 'Exception';
            $valid["detailed"] = $e->getMessage();
        }

        echo json_encode($valid);
    }
}

?>
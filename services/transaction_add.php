<?php

// Include the database connection
require_once("../shared/actions/db/dao.php");
date_default_timezone_set('Asia/Kolkata');

// Initialize response variable
$valid = array('success' => false, 'message' => "", 'detail' => "");

// Check if the form data is posted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!session_id()) {
        session_start();
    }

    // print_r($_POST);

    // mandatory fields
    $customerId = isset($_POST['customerId']) ? htmlspecialchars($_POST['customerId']) : 0;
    $problemStmt = isset($_POST['problemStmt']) ? htmlspecialchars($_POST['problemStmt']) : '';
    $serviceType = isset($_POST['serviceType']) ? htmlspecialchars($_POST['serviceType']) : '';
    $serviceThru = isset($_POST['serviceThrough']) ? htmlspecialchars($_POST['serviceThrough']) : '';
    $comments = isset($_POST['comments']) ? htmlspecialchars($_POST['comments']) : '';
    $status = isset($_POST['status']) ? htmlspecialchars($_POST['status']) : '';;

    // nullable fields
    $problemDesc = isset($_POST['problemDesc']) ? htmlspecialchars($_POST['problemDesc']) : '';
    $notes = isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : '';


    function generateSecureNumericUniqueId()
    {
        $uniqueId = substr(abs(crc32(uniqid())), 0, 5); // Generate unique hash and take the first 5 digits
        return "TCK_" . $uniqueId;
    }

    // Capture form data
    $uniqId = generateSecureNumericUniqueId();
    $createdBy = $_SESSION['user']['id'];
    $assignedAgentId = $createdBy;
    
    $commentsArr = array(
        "date" => date("Y-m-d H:i:s"),
        "message" => $comments,
        "status" => $status,
        "commentBy" => $_SESSION['user']["nm"]
    );
    $notesArr = array(
        "date" => date("Y-m-d H:i:s"),
        "message" => $notes,
        "noteBy" => $_SESSION['user']["nm"]
    );

    // Make sure all the required fields are filled
    if (empty($uniqId) || empty($createdBy) || empty($assignedAgentId) || empty($customerId)) {
        
        $valid['message'] = 'Missing fields';
        $valid['detail'] = 'Some mandatory fields are missing..!.';
    } else {
        
        $db = new sqlHelper();
        
        // Prepare the SQL insert query
        $query = "INSERT INTO ticket (
                uniq_id, assignd_agent_id, created_by, customer_id, 
                comments, problem_desc, status, problem_stmt, 
                service_typ, notes, is_active, is_deleted, 
                service_thru, updated_by
            ) VALUES (
                ?, ?, ?, ?, 
                ?, ?, ?, ?, 
                ?, ?, 1, 0, 
                ?, ?
            )";

        try {
            // Prepare the statement
            $stmt = $db->prepareStatement($query);

            // Parameters for binding
            $params = [
                $uniqId, $assignedAgentId, $createdBy, $customerId,
                json_encode($commentsArr), $problemDesc, $status, $problemStmt,
                $serviceType, json_encode($notesArr),
                $serviceThru, $createdBy
            ];

            $types = 'sisisssssssi'; 
            $db->setParameters($params, $types);

            // Execute the statement
            $resp = $db->execPreparedStatement();
            if($resp['success']){ 
                
                $valid['success'] = true;
                $valid["message"] = 'Transaction created Successfully';
            } else {

                $valid['success'] = false;
                if(str_contains($resp["message"], 'Duplicate entry') && str_contains($resp["message"], 'customer_uniq_code')) {
                    $valid["message"] = 'Duplicate entry - Customer Serial Number';
                } else {
                    $valid["message"] = 'Default';
                }
            }
        } catch (Exception $e) {
            $valid["message"] = 'Exception';
            $valid["detail"] = $e->getMessage();
        }
    }
} else {
    $valid["message"] = 'Invalid Request';
}

// Return the response as JSON
echo json_encode($valid);

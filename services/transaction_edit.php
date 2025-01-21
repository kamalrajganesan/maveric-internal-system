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
    $tId = isset($_POST['tId']) ? htmlspecialchars($_POST['tId']) : ""; 
    $serviceThru = isset($_POST['serviceThrough']) ? htmlspecialchars($_POST['serviceThrough']) : "";
    $newComment = isset($_POST['comments']) ? htmlspecialchars($_POST['comments']) : "";
    $newStatus = isset($_POST['status']) ? htmlspecialchars($_POST['status']) : "";

    // nullable fields
    $newNotes = isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : "";

    // auto generating fields
    $newCommentArr = array(
        "date" => date("Y-m-d H:i:s"),
        "message" => $newComment,
        "status" => $newStatus,
        "commentBy" => $_SESSION['user']['nm']
    );
    $newNotesArr = array(
        "date" => date("Y-m-d H:i:s"),
        "message" => $newNotes,
        "noteBy" => $_SESSION['user']['nm']
    );

    $validationFlag = true;


    // Make sure all the mandatory fields are filled
    if (empty($tId) || empty($serviceThru) || empty($newComment) || empty($newStatus)) {
        
        $validationFlag = false;
        $valid["message"] = "Mandatory";
    } 

    if($validationFlag) {

        try {
            $db = new sqlHelper();
            
            $sql = "UPDATE ticket SET 
                comments = CONCAT(comments, ?), 
                service_thru = ?,
                notes = CONCAT(notes, ?),
                ticket.status = ?";
                
            if($newStatus == "Closed") {
                $newClosedDt = date("Y-m-d H:i:s");
                $solvedBy = $_SESSION['user']['id'];
                $sql .= ", closed_date = ?, solved_by = ?";
            }
                
            $sql .= " WHERE uniq_id = ?";

            $db->prepareStatement($sql);

            if($newStatus == "Closed") {
                $db->setParameters(
                    [
                        ','.json_encode($newCommentArr), 
                        $serviceThru, 
                        ','.json_encode($newNotesArr),
                        $newStatus, 
                        $newClosedDt, 
                        $solvedBy, 
                        $tId
                    ], 
                    "sssssss");
            } else {
                $db->setParameters(
                    [
                        ','.json_encode($newCommentArr), 
                        $serviceThru, 
                        ','.json_encode($newNotesArr),
                        $newStatus, 
                        $tId
                    ], 
                    "sssss");
            }
            
            $resp = $db->execPreparedStatement();

            if($resp['success']) {
                $valid['success'] = true;
                $valid['message'] = "Successfully updated Transaction";
            } else {
                $valid['success'] = false;
                $valid["message"] = 'Default';
            }
        } catch (Exception $e) {
                
            $valid['success'] = false;
            $valid["message"] = 'Exception';
            $valid["detailed"] = $e->getMessage();
        }
    }
        
    echo json_encode($valid);
}

?>
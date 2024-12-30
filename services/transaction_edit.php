<?php

require_once("../shared/actions/db/dao.php");
date_default_timezone_set('Asia/Kolkata');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if(!session_id()) {
        session_start();
    }

    $tId = $_POST['tId']; 
    $serviceThru = $_POST['serviceThrough'];
    $newComment = $_POST['comments'];
    $newStatus = $_POST['status'];
    $newNotes = $_POST['notes'];

    $newCommentArr = array(
        "timestamp" => date("Y-m-d H:i:s"),
        "message" => $newComment,
    );
    $newNotesArr = array(
        "timestamp" => date("Y-m-d H:i:s"),
        "message" => $newNotes,
    );
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
    
    if($db->execPreparedStatement()) {
        $valid['success'] = true;
        $valid['message'] = "Successfully updated Transaction";
    } else {
        $valid['success'] = false;
        $valid['message'] = "Error while updating the transaction data ".$sql;
    }

    echo json_encode($valid);

}
?>
<?php

require_once("../shared/php/connect.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if(!session_id()) {
        session_start();
    }

    $tId = $_POST['tId']; 
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


    $sql = "UPDATE ticket SET 
        comments = CONCAT(comments, ',". json_encode($newCommentArr) ."'), 
        notes = CONCAT(notes, ',". json_encode($newNotesArr) ."'),
        ticket.status = '". $newStatus ."'";

    if($newStatus == "Closed") {
        $newClosedDt = date("Y-m-d H:i:s");
        $solvedBy = $_SESSION['user']['id'];

        $sql .= ", closed_date = '". $newClosedDt ."', solved_by = ". $solvedBy;
    } else {
        $newClosedDate = null;
    }
    
    $sql .= " WHERE uniq_id = '". $tId ."'";

    $connect = createConn();
    if($connect->query($sql) === TRUE) {
        $valid['success'] = true;
        $valid['message'] = "Successfully updated customer";
    } else {
        $valid['success'] = false;
        $valid['message'] = "Error while updating the data ".$sql;
    }

    $connect->close();
    echo json_encode($valid);

}
?>
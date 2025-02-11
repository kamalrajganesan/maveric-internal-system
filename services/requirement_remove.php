<?php

require_once("../shared/php/connect.php");

$connect = createConn();

$valid = array('success' => false, 'message' => "");

$lId = $_POST['requirementId'];

if($lId) {

    $sql = "UPDATE req_tracker set is_deleted = 1 WHERE id = '".$lId."'";

    if( $connect-> query($sql) === TRUE) {
 	    $valid['success'] = true;
	    $valid['message'] = "Successfully removed a Lead";
    } else {
 	    $valid['success'] = false;
 	    $valid['message'] = "Error while removing the Lead data";
    }

    $connect->close();
    echo json_encode($valid);
}

?>
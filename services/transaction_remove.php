<?php

require_once("../shared/php/connect.php");

$connect = createConn();

$valid = array('success' => false, 'message' => "");

$tId = $_POST['ticketId'];

if($tId) {

    $sql = "UPDATE ticket set is_deleted = 1 WHERE id = '".$tId."'";

    if( $connect-> query($sql) === TRUE) {
 	    $valid['success'] = true;
	    $valid['message'] = "Successfully removed Customer";
    } else {
 	    $valid['success'] = false;
 	    $valid['message'] = "Error while removing the Customer data";
    }

    $connect->close();
    echo json_encode($valid);
}

?>
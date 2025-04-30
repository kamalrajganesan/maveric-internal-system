<?php

require_once("../shared/php/connect.php");

$connect = createConn();

$valid = array('success' => false, 'message' => "");

$aId = isset($_POST['aId']) ? htmlspecialchars($_POST['aId']) : "";

if($aId) {

    $sql = "UPDATE agent set is_deleted = 1 WHERE id = '".$aId."'";

    if( $connect-> query($sql) === TRUE) {
 	    $valid['success'] = true;
	    $valid['message'] = "Successfully removed agent";
    } else {
 	    $valid['success'] = false;
 	    $valid['message'] = "Error while removing the agent data";
    }

    $connect->close();
    echo json_encode($valid);
}

?>
<?php

require_once("../shared/php/connect.php");

$connect = createConn();

$valid = array('success' => false, 'message' => "");

$cId = $_POST['customerId'];

if($cId) {

    $sql = "UPDATE cust_mstr set is_deleted = 1 WHERE customer_uniq_code = '".$cId."'";

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
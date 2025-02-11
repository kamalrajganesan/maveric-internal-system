<?php

require_once("../shared/php/connect.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!session_id()) {
        session_start();
    }

    $requirementId = $_POST['rId']; 
    $requirementName = $_POST['nm'];
    $brief = $_POST['brief'];
    $detailed = $_POST['detailed'];
    $custId = $_POST['cust_id'];
    $phone = $_POST['phone'];
    $requirementStatus = $_POST['requirementStatus'];

    // Create the update query
    $sql = "UPDATE req_tracker SET 
        nm = '". $requirementName ."', 
        brief = '". $brief ."',
        detailed = '". $detailed ."',
        cust_id = '". $custId ."',
        phone = '". $phone ."',
        requirement_status = '". $requirementStatus ."',
        updated_on = NOW()
        WHERE id = ". $requirementId;

    // Establish connection to the database
    $connect = createConn();

    if ($connect->query($sql) === TRUE) {
        $valid['success'] = true;
        $valid['message'] = "Successfully updated requirement details.";
    } else {
        $valid['success'] = false;
        $valid['message'] = "Error while updating the requirement details: " . $sql;
    }

    $connect->close();
    echo json_encode($valid);
}
?>

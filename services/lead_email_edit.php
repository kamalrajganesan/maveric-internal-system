<?php

require_once("../shared/php/connect.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!session_id()) {
        session_start();
    }

    $leadId = $_POST['lId']; 
    $leadName = $_POST['leadNm'];
    $email = $_POST['email'];
    $companyName = $_POST['companyNm'];
    $contact = $_POST['contact'];
    $requirement = $_POST['requirement'];
    $description = $_POST['description'];
    $notes = $_POST['notes'];
    $addressLine = $_POST['addressLn'];
    $area = $_POST['area'];
    $city = $_POST['city'];
    $pincode = $_POST['pincode'];
    $followUpDate = $_POST['followUpDt'];
    $leadStatus = $_POST['leadStatus'];

    // Create the update query
    $sql = "UPDATE lead_email_tracker SET 
        lead_nm = '". $leadName ."', 
        email = '". $email ."',
        company_nm = '". $companyName ."',
        contact = '". $contact ."',
        requirement = '". $requirement ."',
        description = '". $description ."',
        notes = '". $notes ."',
        address_ln = '". $addressLine ."',
        area = '". $area ."',
        city = '". $city ."',
        pincode = '". $pincode ."',
        follow_up_dt = '". $followUpDate ."',
        lead_status = '". $leadStatus ."',
        updated_on = NOW()
        WHERE id = ". $leadId;

    // Establish connection to the database
    $connect = createConn();

    if ($connect->query($sql) === TRUE) {
        $valid['success'] = true;
        $valid['message'] = "Successfully updated lead details.";
    } else {
        $valid['success'] = false;
        $valid['message'] = "Error while updating the lead details: " . $sql;
    }

    $connect->close();
    echo json_encode($valid);
}
?>

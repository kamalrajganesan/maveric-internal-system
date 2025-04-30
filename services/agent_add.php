<?php

require_once("../shared/actions/db/dao.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if(!session_id()) {
        session_start();
    }

    $valid['success'] = false;
    $valid['message'] = "";

    // print_r($_POST);

    // mandatory fields
    $agentName = isset($_POST['agentName']) ? htmlspecialchars($_POST['agentName']) : "";
    $agentEmail = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : "";
    $agentPhone1 = isset($_POST['contact1']) ? htmlspecialchars($_POST['contact1']) : "";
    $agentPincode = isset($_POST['pincode']) ? htmlspecialchars($_POST['pincode']) : "";
    $agentCity = isset($_POST['city']) ? htmlspecialchars($_POST['city']) : "";
    $agentPasscode = isset($_POST['password']) ? htmlspecialchars($_POST['password']) : "";
    
    // nullable fields
    $agentPhone2 = isset($_POST['contact2']) ? htmlspecialchars($_POST['contact2']) : "";
    $agentAddress = isset($_POST['address']) ? htmlspecialchars($_POST['address']) : "";
    $agentArea = isset($_POST['area']) ? htmlspecialchars($_POST['area']) : "";
    $agentActiveStatus = isset($_POST["activeStatus"]) ? htmlspecialchars($_POST["activeStatus"]) : "";

    // auto generating fields
    $updatedBy = $_SESSION['user']['id'];
    $createdBy = $_SESSION['user']['nm'];


    $validationFlag = true;



    // Make sure all the mandatory fields are filled
    if (empty($agentName) || empty($agentEmail) || empty($agentPhone1) || empty($agentPincode) || 
            empty($agentCity) || empty($agentPasscode)) {

        $validationFlag = false;
        $valid["message"] = "Mandatory";
    } 

    if($validationFlag) {
        
        $db = new sqlHelper();

        $query = "
            INSERT INTO agent(
                agent_nm, address_ln, primary_contact, secondary_contact, 
                email, pincode, city, area, 
                pass_code, is_active, updated_by, created_by, 
                created_on, is_deleted) 
            VALUES (
                ?, ?, ?, ?, 
                ?, ?, ?, ?, 
                ?, ?, ?, ?, 
                now(), 0
            )
        ";

        try {
            // Prepare the statement
            $stmt = $db->prepareStatement($query);

            // Parameters for binding
            $params = [
                $agentName, $agentAddress, $agentPhone1, $agentPhone2, 
                $agentEmail, $agentPincode, $agentCity, $agentArea, 
                $agentPasscode, $agentActiveStatus, $updatedBy, $createdBy
            ];
            $types = 'sssssssssiis'; 
            $db->setParameters($params, $types);

            // Execute the statement
            $db->execPreparedStatement();
            $valid['success'] = true;
            $valid["message"] = 'Agent created successfully!';
            $valid["detailed"] = "";

        } catch (Exception $e) {
            $valid['success'] = false;
            $valid["message"] = 'Error in creating Agent details';
            $valid["detailed"] = $e->getMessage();
        }

    } 
    
    echo json_encode($valid);
}

?>
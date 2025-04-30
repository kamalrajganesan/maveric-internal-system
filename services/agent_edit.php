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
    $aId = isset($_POST['aId']) ? htmlspecialchars($_POST['aId']) : ""; 
    
    $agentName = isset($_POST['agentName']) ? htmlspecialchars($_POST['agentName']) : "";
    $agentEmail = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : "";
    $agentPhone1 = isset($_POST['contact1']) ? htmlspecialchars($_POST['contact1']) : "";
    $agentPhone2 = isset($_POST['contact2']) ? htmlspecialchars($_POST['contact2']) : "";
    $agentPincode = isset($_POST['pincode']) ? htmlspecialchars($_POST['pincode']) : "";
    $agentCity = isset($_POST['city']) ? htmlspecialchars($_POST['city']) : "";
    $agentPasscode = isset($_POST['password']) ? htmlspecialchars($_POST['password']) : "";
    
    // nullable fields
    $agentAddress = isset($_POST['address']) ? htmlspecialchars($_POST['address']) : "";
    $agentArea = isset($_POST['area']) ? htmlspecialchars($_POST['area']) : "";
    $agentActiveStatus = isset($_POST["activeStatus"]) ? htmlspecialchars($_POST["activeStatus"]) : "";

    // auto generating fields
    $updatedBy = $_SESSION['user']['id'];


    $validationFlag = true;



    // Make sure all the mandatory fields are filled
    if (empty($aId) || empty($agentName) || empty($agentEmail) || empty($agentPhone1) || 
        empty($agentPhone2) || empty($agentPincode) || empty($agentCity) || empty($agentPasscode)) {

        $validationFlag = false;
        $valid["message"] = "Mandatory";
    } 

    if($validationFlag) {
        
        $db = new sqlHelper();

        $query = "
            UPDATE agent SET 
                agent_nm = ?, address_ln = ?, primary_contact = ?, secondary_contact = ?, 
                email = ?, pincode = ?, city = ?, area = ?, 
                pass_code = ?, is_active = ?, updated_by = ?
            WHERE id = ?
        ";

        try {
            // Prepare the statement
            $stmt = $db->prepareStatement($query);

            // Parameters for binding
            $params = [
                $agentName, $agentAddress, $agentPhone1, $agentPhone2, 
                $agentEmail, $agentPincode, $agentCity, $agentArea, 
                $agentPasscode, $agentActiveStatus, $updatedBy,
                $aId
            ];
            $types = 'sssssssssiii'; 
            $db->setParameters($params, $types);

            // Execute the statement
            $db->execPreparedStatement();
            $valid['success'] = true;
            $valid["message"] = 'Agent updated successfully!';
            $valid["detailed"] = "";

        } catch (Exception $e) {
            $valid['success'] = false;
            $valid["message"] = 'Error in updating Agent details';
            $valid["detailed"] = $e->getMessage();
        }

    } 
    
    echo json_encode($valid);
}

?>
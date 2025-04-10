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
    $followUpDate = isset($_POST['followUpDt']) ? htmlspecialchars($_POST['followUpDt']) : "";
    $leadStatus = isset($_POST['leadStatus']) ? htmlspecialchars($_POST['leadStatus']) : "";
    $leads = isset($_POST['leads'])? $_POST['leads'] : [];

    // auto generating fields
    $updatedBy = $_SESSION['user']['id'];

    $validationFlag = true;


    // Make sure all the mandatory fields are filled
    if (empty($followUpDate) || empty($leadStatus) || empty($leads)) {

        $validationFlag = false;
        $valid["message"] = "Mandatory";
    } 

    if($validationFlag) {

        $leadIds = [];
        foreach ($leads as $key => $value) {
            // print_r($value);
            array_push($leadIds, $value[0]);
        }
        
        $db = new sqlHelper();

        $placeholders = implode(',', array_fill(0, count($leadIds), '?'));
        $query = 
            "UPDATE lead_email_tracker SET 
                follow_up_dt = ?,
                lead_status = ?,
                updated_on = NOW(),
                updated_by = ?
            WHERE id IN ($placeholders)";

        try {

            // Prepare the statement
            $stmt = $db->prepareStatement($query);

            // Parameters for binding
            $params = [
                $followUpDate, 
                $leadStatus,
                $updatedBy
            ];
            $params = array_merge($params, $leadIds);

            $types = 'sss';
            $types .= str_repeat('i', count($leadIds));

            $db->setParameters($params, $types);

            // Execute the statement
            $db->execPreparedStatement();
            $valid['success'] = true;
            $valid["message"] = 'Leads updated successfully!';
            $valid["detailed"] = "";

        } catch (Exception $e) {
            $valid['success'] = false;
            $valid["message"] = 'Error in updating Lead details';
            $valid["detailed"] = $e->getMessage();
        }

    } 
    
    echo json_encode($valid);
}

?>
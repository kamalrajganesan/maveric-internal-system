<?php
// Include the database connection
require_once("../shared/actions/db/dao.php");

// Initialize response variable
$response = '';
$db = new sqlHelper();

// Check if the form data is posted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    if (!session_id()) {
        session_start();
    }
    
    $valid['success'] = false;
    $valid['message'] = "";

    // print_r($_POST);

    // Capture form data
    $leadId = isset($_POST['lId']) ? htmlspecialchars($_POST['lId']) : '';
    $leadNm = isset($_POST['leadNm']) ? htmlspecialchars($_POST['leadNm']) : '';
    $leadStatus = isset($_POST['leadStatus']) ? htmlspecialchars($_POST['leadStatus']) : '';
    $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
    $companyNm = isset($_POST['companyNm']) ? htmlspecialchars($_POST['companyNm']) : '';
    $contact = isset($_POST['contact']) ? htmlspecialchars($_POST['contact']) : '';
    $requirement = isset($_POST['requirement']) ? htmlspecialchars($_POST['requirement']) : '';
    $description = isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '';
    $notes = isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : '';
    $addressLn = isset($_POST['addressLn']) ? htmlspecialchars($_POST['addressLn']) : '';
    $area = isset($_POST['area']) ? htmlspecialchars($_POST['area']) : '';
    $city = isset($_POST['city']) ? htmlspecialchars($_POST['city']) : '';
    $pincode = isset($_POST['pincode']) ? htmlspecialchars($_POST['pincode']) : '';
    $followUpDt = isset($_POST['followUpDt']) ? htmlspecialchars($_POST['followUpDt']) : '';
    

    // Make sure all the required fields are filled
    if (empty($leadStatus) || empty($leadId) || empty($leadNm)) {
        
        $valid["message"] = "Mandatory";
        $valid['detail'] = 'Some mandatory fields are missing..!.';
    } else {
        
        $createdBy =  $_SESSION['user']['id'];
        $commentsArr = array(
            "date" => date("Y-m-d H:i:s"),
            "message" => $notes,
            "status" => $leadStatus,
            "commentBy" => $_SESSION['user']["nm"]
        );

        // Prepare the SQL insert query
        $query = "UPDATE lead_call_tracker SET
                     lead_nm = ?, contact = ?, company_nm = ?, requirement = ?, notes = ?, 
                     description = ?, address_ln = ?, pincode = ?, city = ?, area = ?, 
                     email = ?, follow_up_dt = ?, lead_status = ?, log = CONCAT(log, ?), created_by = ?, 
                     updated_by = ?
                WHERE id = ?
                ";

        try {
            // Prepare the statement
            $stmt = $db->prepareStatement($query);

            // Parameters for binding
            $params = [
                $leadNm, $contact, $companyNm, $requirement, $notes, 
                $description, $addressLn, $pincode, $city, $area, 
                $email, $followUpDt, $leadStatus, json_encode($commentsArr), $createdBy, 
                $createdBy, $leadId
            ];

            // Bind the parameters (types: 's' for string, 'i' for integer)
            $types = 'ssssssssssssssiii'; // Adjust types if necessary
            $db->setParameters($params, $types);

            // Execute the statement
            $resp = $db->execPreparedStatement();

            if($resp['success']){ 
                
                $valid['success'] = true;
                $valid["message"] = 'Lead updated successfully!';
            } else {

                $valid['success'] = false;
                if(str_contains($resp["message"], 'Duplicate entry') && str_contains($resp["message"], 'contact')) {
                    $valid["message"] = 'Duplicate';
                    $valid["detailed"] = 'Duplicate entry - Lead contact number';
                } else {
                    $valid["message"] = 'Default';
                }
            }

        } catch (Exception $e) {
            
            $valid['success'] = false;
            $valid["message"] = 'Exception';
            $valid["detailed"] = $e->getMessage();
        }
    }

    // Return the response as JSON
    echo json_encode($valid);
    
}

?>

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
    $leadNm = isset($_POST['leadNm']) ? htmlspecialchars($_POST['leadNm']) : '';
    $contact = isset($_POST['contact']) ? htmlspecialchars($_POST['contact']) : '';
    $companyNm = isset($_POST['companyNm']) ? htmlspecialchars($_POST['companyNm']) : '';
    $requirement = isset($_POST['requirement']) ? htmlspecialchars($_POST['requirement']) : '';
    $notes = isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : '';
    $description = isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '';
    $addressLn = isset($_POST['addressLn']) ? htmlspecialchars($_POST['addressLn']) : '';
    $pincode = isset($_POST['pincode']) ? htmlspecialchars($_POST['pincode']) : '';
    $city = isset($_POST['city']) ? htmlspecialchars($_POST['city']) : '';
    $area = isset($_POST['area']) ? htmlspecialchars($_POST['area']) : '';
    $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
    $followUpDt = isset($_POST['followUpDt']) ? htmlspecialchars($_POST['followUpDt']) : '';
    $leadStatus = isset($_POST['leadStatus']) ? htmlspecialchars($_POST['leadStatus']) : '';

    // Make sure all the required fields are filled
    if (empty($contact) || empty($leadStatus)) {
        
        $valid["message"] = "Mandatory";
    } else {
        // Get the current user ID dynamically (Example, replace with actual user session handling)
        $createdBy =  1; // Use session or other method to get user ID

        // Prepare the SQL insert query
        $query = "INSERT INTO lead_call_tracker (
                lead_nm, contact, company_nm, requirement, notes, description, 
                address_ln, pincode, city, area, email, follow_up_dt, lead_status, 
                created_by, updated_by, is_active, is_deleted
            ) VALUES (
                ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?, 
                ?, ?, 1, 0
            )";

        try {
            // Prepare the statement
            $stmt = $db->prepareStatement($query);

            // Parameters for binding
            $params = [
                $leadNm,
                $contact,
                $companyNm,
                $requirement,
                $notes,
                $description,
                $addressLn,
                $pincode,
                $city,
                $area,
                $email,
                $followUpDt,
                $leadStatus,
                $createdBy,
                $createdBy
            ];

            // Bind the parameters (types: 's' for string, 'i' for integer)
            $types = 'sssssssssssssii'; // Adjust types if necessary
            $db->setParameters($params, $types);

            // Execute the statement
            $resp = $db->execPreparedStatement();

            if($resp['success']){ 
                
                $valid['success'] = true;
                $valid["message"] = 'Lead created successfully!';
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
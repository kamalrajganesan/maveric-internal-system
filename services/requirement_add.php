<?php
// Include the database connection
require_once("../shared/actions/db/dao.php");

// Initialize response variable
$response = '';
$db = new sqlHelper();
$currentDateTime = date('Y-m-d H:i:s');

// Check if the form data is posted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    if (!session_id()) {
        session_start();
    }


    // Capture form data
    $nm = isset($_POST['nm']) ? htmlspecialchars($_POST['nm']) : '';
    $brief = isset($_POST['brief']) ? htmlspecialchars($_POST['brief']) : '';
    $detailed = isset($_POST['detailed']) ? htmlspecialchars($_POST['detailed']) : '';
    $cust_id = isset($_POST['cust_id']) ? htmlspecialchars($_POST['cust_id']) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '';
    $requirementStatus = isset($_POST['requirementStatus']) ? htmlspecialchars($_POST['requirementStatus']) : '';
    $updated_on = isset($_POST['updated_on']) ? htmlspecialchars($_POST['updated_on']) : '';
    $updated_by = isset($_POST['updated_by']) ? htmlspecialchars($_POST['updated_by']) : '';
    $created_on = isset($_POST['created_on']) ? htmlspecialchars($_POST['created_on']) : '';
    $created_by = isset($_POST['created_by']) ? htmlspecialchars($_POST['created_by']) : '';

    // Make sure all the required fields are filled
    if (empty($nm) || empty($brief) || empty($detailed) || empty($requirementStatus)) {
        $response = 'All fields are required.';
    } else {
        // Get the current user ID dynamically (Example, replace with actual user session handling)
        $createdBy =  1; // Use session or other method to get user ID

        // Prepare the SQL insert query
        $query = "INSERT INTO req_tracker (
                brief, detailed, cust_id, phone, nm, requirement_status, 
                updated_on, updated_by, created_on, created_by, is_active, is_deleted
            ) VALUES (
                ?, ?, ?, ?, ?, ?, 
                ?, ?, ?, ?, 1, 0
            )";

        try {
            // Prepare the statement
            $stmt = $db->prepareStatement($query);

            // Parameters for binding
            $params = [
                $brief,
                $detailed,
                $cust_id,
                $phone,
                $nm,
                $requirementStatus,
                $updated_on,
                $updated_by,
                $currentDateTime,
                $createdBy
            ];

            $types = 'ssissssssi'; 
            $db->setParameters($params, $types);

            $db->execPreparedStatement();
            $response = array("success" => true, "message" => 'Requirement added successfully!');
        } catch (Exception $e) {
            $response = 'Error: ' . $e->getMessage();
        }
    }
} else {
    $response = 'Invalid request method';
}

// Return the response as JSON
echo json_encode($response);
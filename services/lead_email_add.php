<?php
require_once("../shared/actions/db/dao.php");

// Check if the form data is posted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!session_id()) {
        session_start();
    }

    $db = new sqlHelper();
    $valid = [
        'success' => false,
        'message' => "",
        'detailed' => ""
    ];

    // Capture and sanitize form data
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
    $leadStatus = isset($_POST['leadStatus']) ? htmlspecialchars($_POST['leadStatus']) : '';
    $assignee = isset($_POST['assignee']) ? htmlspecialchars($_POST['assignee']) : $_SESSION['user']['id'];
    $createdBy = $_SESSION['user']['id'];

    // Handle follow-up date
    $followUpDt = null;
    if (!empty($_POST['followUpDt'])) {
        $dateTime = DateTime::createFromFormat('Y-m-d', $_POST['followUpDt']);
        if ($dateTime === false) {
            $valid['success'] = false;
            $valid['message'] = "Invalid";
            $valid['detailed'] = "Invalid follow-up date format";
            echo json_encode($valid);
            exit();
        }
        $followUpDt = $dateTime->format('Y-m-d H:i:s');
    }

    // Validate mandatory fields
    if (empty($email) || empty($leadStatus)) {
        $valid["message"] = "Mandatory";
        $valid['detailed'] = 'Email and Lead Status are required fields';
        echo json_encode($valid);
        exit();
    }

    // Prepare the SQL insert query
    $query = "INSERT INTO lead_email_tracker (
            lead_nm, contact, company_nm, requirement, notes, description, 
            address_ln, pincode, city, area, email, follow_up_dt, lead_status, 
            assignee, created_by, is_active, is_deleted
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
            $leadNm, $contact, $companyNm, $requirement, $notes, $description,
            $addressLn, $pincode, $city, $area, $email, $followUpDt, $leadStatus,
            $assignee, $createdBy
        ];

        // Bind the parameters (types: 's' for string, 'i' for integer)
        $types = 'ssssssssssssssi'; 
        $db->setParameters($params, $types);

        // Execute the statement
        $resp = $db->execPreparedStatement();

        if($resp['success']){ 
            $valid['success'] = true;
            $valid["message"] = 'Success';
            $valid["detailed"] = 'Lead created successfully!';
        } else {
            $valid['success'] = false;
            if(str_contains($resp["message"], 'Duplicate entry') && str_contains($resp["message"], 'email')) {
                $valid["message"] = 'Duplicate';
                $valid["detailed"] = 'This email already exists in the system';
            } else {
                $valid["message"] = 'Error';
                $valid["detailed"] = $resp["message"];
            }
        }
    } catch (Exception $e) {
        $valid['success'] = false;
        $valid["message"] = 'Exception';
        $valid["detailed"] = $e->getMessage();
    }

    // Return the response as JSON
    echo json_encode($valid);
    exit();
}
?>
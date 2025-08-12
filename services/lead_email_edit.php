<?php
require_once("../shared/php/connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!session_id()) {
        session_start();
    }

    $valid = ['success' => false, 'message' => ''];

    // Check login
     if (!isset($_SESSION['user']['id'])) {
        $valid['success'] = false;
        $valid['message'] = "User not logged in or session expired.";
        echo json_encode($valid);
        exit();
    }
    $currentUserId   = $_SESSION['user']['id'];
    $currentUserRole = $_SESSION['user']['role'];

    // Sanitize POST inputs
    $leadId       = $_POST['lId'] ?? '';
    $leadName     = $_POST['leadNm'] ?? '';
    $email        = $_POST['email'] ?? '';
    $companyName  = $_POST['companyNm'] ?? '';
    $contact      = $_POST['contact'] ?? '';
    $requirement  = $_POST['requirement'] ?? '';
    $description  = $_POST['description'] ?? '';
    $notes        = $_POST['notes'] ?? '';
    $addressLine  = $_POST['addressLn'] ?? '';
    $area         = $_POST['area'] ?? '';
    $city         = $_POST['city'] ?? '';
    $pincode      = $_POST['pincode'] ?? '';
    $leadStatus   = $_POST['leadStatus'] ?? '';
    $assignee     = $_POST['assignee'] ?? '';
 date_default_timezone_set('Asia/Kolkata');
    // Handle follow-up date validation
    $followUpDate = null;
    if (!empty($_POST['followUpDt'])) {
        $dateTime = DateTime::createFromFormat('Y-m-d', $_POST['followUpDt']);
        if ($dateTime === false) {
            $valid['message'] = "Invalid follow-up date format.";
            echo json_encode($valid);
            exit();
        }
        $followUpDate = $dateTime->format('Y-m-d H:i:s');
    }

    $connect = createConn();

    // Check if lead exists
    $checkSql = "SELECT assignee FROM lead_email_tracker WHERE id = " . $connect->real_escape_string($leadId);
    $result   = $connect->query($checkSql);

    if ($result && $result->num_rows > 0) {
        $row              = $result->fetch_assoc();
        $currentAssignee  = $row['assignee'];
        $shouldAssign     = false;

        // Assignee handling based on role
        if ($currentUserRole === 'agent' && (empty($currentAssignee) || $currentAssignee == 0)) {
            $assignee     = $currentUserId;
            $shouldAssign = true;
        } elseif ($currentUserRole === 'admin') {
            $assignee = ($assignee === '') ? 0 : $connect->real_escape_string($assignee);
        } else {
            $assignee = $currentAssignee;
        }

        // Build update query
        $sql = "UPDATE lead_email_tracker SET
            lead_nm      = '" . $connect->real_escape_string($leadName) . "',
            email        = '" . $connect->real_escape_string($email) . "',
            company_nm   = '" . $connect->real_escape_string($companyName) . "',
            contact      = '" . $connect->real_escape_string($contact) . "',
            requirement  = '" . $connect->real_escape_string($requirement) . "',
            description  = '" . $connect->real_escape_string($description) . "',
            notes        = '" . $connect->real_escape_string($notes) . "',
            address_ln   = '" . $connect->real_escape_string($addressLine) . "',
            area         = '" . $connect->real_escape_string($area) . "',
            city         = '" . $connect->real_escape_string($city) . "',
            pincode      = '" . $connect->real_escape_string($pincode) . "',
            follow_up_dt = " . ($followUpDate ? "'" . $connect->real_escape_string($followUpDate) . "'" : "NULL") . ",
            lead_status  = '" . $connect->real_escape_string($leadStatus) . "',
            updated_on   = NOW(),
            updated_by   = '" . $connect->real_escape_string($currentUserId) . "',
            assignee     = '" . $assignee . "'
            WHERE id     = " . $connect->real_escape_string($leadId);

        if ($connect->query($sql) === TRUE) {
            $valid['success'] = true;
            $valid['message'] = "Successfully updated lead details.";
            if ($shouldAssign) {
                $valid['message'] .= " Lead has been assigned to you.";
            }
        } else {
            $valid['message'] = "Error updating lead details: " . $connect->error;
        }
    } else {
        $valid['message'] = "Lead not found.";
    }

    $connect->close();
    echo json_encode($valid);
}
?>

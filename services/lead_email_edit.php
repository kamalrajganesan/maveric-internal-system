<?php
require_once("../shared/php/connect.php");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!session_id()) {
        session_start();
    }
    // Check if user is logged in and has an ID
    if (!isset($_SESSION['user']['id'])) {
        $valid['success'] = false;
        $valid['message'] = "User not logged in or session expired.";
        echo json_encode($valid);
        exit();
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
    $assignee = $_POST['assignee'] ?? ''; // Get assignee from form
    $currentUserId = $_SESSION['user']['id'];
    $currentUserRole = $_SESSION['user']['role']; // Get user role
    
    // First, check if the lead exists
    $connect = createConn();
   
    // Get current assignment status
    $checkSql = "SELECT assignee FROM lead_email_tracker WHERE id = " . $connect->real_escape_string($leadId);
    $result = $connect->query($checkSql);
   
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $currentAssignee = $row['assignee'];
       
        // Determine if we should update assignee
        $shouldAssign = false;
        if ($currentUserRole === 'agent' && (empty($currentAssignee) || $currentAssignee == 0 || $currentAssignee == '')) {
            // Auto-assign to agent if lead is unassigned
            $assignee = $currentUserId;
            $shouldAssign = true;
        } elseif ($currentUserRole === 'admin') {
            // Admin can set any assignee (including unassigned)
            $assignee = $assignee === '' ? 0 : $connect->real_escape_string($assignee);
        } else {
            // Non-admin, non-agent users can't change assignee
            $assignee = $currentAssignee;
        }
       
        // Create the update query
        $sql = "UPDATE lead_email_tracker SET
            lead_nm = '" . $connect->real_escape_string($leadName) . "',
            email = '" . $connect->real_escape_string($email) . "',
            company_nm = '" . $connect->real_escape_string($companyName) . "',
            contact = '" . $connect->real_escape_string($contact) . "',
            requirement = '" . $connect->real_escape_string($requirement) . "',
            description = '" . $connect->real_escape_string($description) . "',
            notes = '" . $connect->real_escape_string($notes) . "',
            address_ln = '" . $connect->real_escape_string($addressLine) . "',
            area = '" . $connect->real_escape_string($area) . "',
            city = '" . $connect->real_escape_string($city) . "',
            pincode = '" . $connect->real_escape_string($pincode) . "',
            follow_up_dt = '" . $connect->real_escape_string($followUpDate) . "',
            lead_status = '" . $connect->real_escape_string($leadStatus) . "',
            updated_on = NOW(),
            updated_by = '" . $connect->real_escape_string($currentUserId) . "',
            assignee = '" . $assignee . "'
            WHERE id = " . $connect->real_escape_string($leadId);
        
        if ($connect->query($sql) === TRUE) {
            $valid['success'] = true;
            $message = "Successfully updated lead details.";
            if ($shouldAssign) {
                $message .= " Lead has been assigned to you.";
            }
            $valid['message'] = $message;
        } else {
            $valid['success'] = false;
            $valid['message'] = "Error while updating the lead details: " . $connect->error;
        }
    } else {
        $valid['success'] = false;
        $valid['message'] = "Lead not found.";
    }
    
    $connect->close();
    echo json_encode($valid);
}
?>
<?php
require_once("../shared/php/connect.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!session_id()) {
        session_start();
    }

    // Verify user is logged in
    if (!isset($_SESSION['userType']) || !isset($_SESSION['user']['id'])) {
        echo json_encode(["success" => false, "message" => "Authentication required"]);
        exit();
    }

    $leadId = isset($_POST['lId']) ? intval($_POST['lId']) : 0;
    $leadName = $_POST['leadNm'] ?? '';
    $email = $_POST['email'] ?? '';
    $companyName = $_POST['companyNm'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $requirement = $_POST['requirement'] ?? '';
    $description = $_POST['description'] ?? '';
    $notes = $_POST['notes'] ?? '';
    $addressLine = $_POST['addressLn'] ?? '';
    $area = $_POST['area'] ?? '';
    $city = $_POST['city'] ?? '';
    $pincode = $_POST['pincode'] ?? '';
    $followUpDate = $_POST['followUpDt'] ?? '';
    $leadStatus = $_POST['leadStatus'] ?? '';
    
    // Get current user ID (agent editing the lead)
    $currentUserId = intval($_SESSION['user']['id']);
    $isAdmin = ($_SESSION['userType'] ?? '') === 'admin';
    
    // Get current assignee
    $currentAssignee = null;
    $connect = createConn();
    $checkSql = "SELECT assignee FROM lead_call_tracker WHERE id = $leadId AND is_deleted = 0";
    if (!$isAdmin) {
        $checkSql .= " AND (assignee = $currentUserId OR assignee IS NULL OR assignee = 0)";
    }
    $result = $connect->query($checkSql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $currentAssignee = $row['assignee'];
    } else {
        $connect->close();
        echo json_encode(["success" => false, "message" => "Lead not found or access denied"]);
        exit();
    }
    
    // Auto-assign logic: If lead is unassigned (assignee is NULL or 0), assign to current user
    $assigneeUpdate = '';
    if (!$isAdmin && (!$currentAssignee || $currentAssignee == 0)) {
        $assigneeUpdate = ", assignee = $currentUserId";
    }

    // Create the update query
    $sql = "UPDATE lead_call_tracker SET 
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
        lead_status = '" . $connect->real_escape_string($leadStatus) . "'
        $assigneeUpdate,
        updated_by = $currentUserId,
        updated_on = NOW()
        WHERE id = $leadId";

    if ($connect->query($sql) === TRUE) {
        $valid['success'] = true;
        $valid['message'] = "Successfully updated lead details.";
    } else {
        $valid['success'] = false;
        $valid['message'] = "Error while updating the lead details: " . $connect->error;
    }

    $connect->close();
    echo json_encode($valid);
}
?>
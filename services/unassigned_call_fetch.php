<?php
require_once("../shared/actions/db/dao.php");

if (!session_id()) {
    session_start();
}

// Verify user is logged in
if (!isset($_SESSION['userType'])) {
    echo json_encode(["success" => false, "message" => "Authentication required"]);
    exit();
}

$page = isset($_POST['param']) ? htmlspecialchars($_POST['param']) : '';
$currentUserId = $_SESSION['user']['id'] ?? 0;
$isAdmin = ($_SESSION['userType'] ?? '') === 'admin';

$db = new sqlHelper();

// Base query: Fetch unassigned leads (NULL, 0, or empty string)
$FetchAllSQL = "SELECT * FROM lead_call_tracker WHERE is_deleted = 0";
$FetchAllSQL .= " AND (assignee IS NULL OR assignee = 0 OR assignee = '')";

// For non-admin users, only show unassigned leads in New status
if (!$isAdmin) {
    $FetchAllSQL .= " AND lead_status = 'New'";
}

// Apply additional status filter if specified (only for admin)
$allowedStatuses = ['New', 'Contacted', 'Converted', 'Following', 'Lost'];
if ($isAdmin && in_array($page, $allowedStatuses)) {
    $FetchAllSQL .= " AND lead_status = '" . $db->escapeString($page) . "'";
}

$db->prepareStatement($FetchAllSQL);
$db->execPreparedStatement();
$FetchAllSQLResultSet = $db->getResultSet();

if ($FetchAllSQLResultSet->num_rows > 0) {
    $data = [];
    $siVar = 1;
    
    while ($row = $FetchAllSQLResultSet->fetch_assoc()) {
        $btn = '
        <div class="btn-group">
            <button type="button" class="btn btn-inverse-primary btn-fw" data-toggle="modal" data-target="#viewLeadModal" onclick="viewLead(' . (int)$row['id'] . ')">
                <i class="fa fa-2x fa-ellipsis-v"></i>
            </button>';
        
        // Only show edit button for admin or if lead is New
        if ($isAdmin || $row['lead_status'] === 'New') {
            $btn .= '
            <button type="button" class="btn btn-inverse-secondary btn-fw" data-toggle="modal" data-target="#editLeadModal" onclick="editLead(' . (int)$row['id'] . ')">
                <i class="fa fa-2x fa-pencil-square-o"></i>
            </button>';
        }
        
        $btn .= '
            <button type="button" class="btn btn-inverse-dark btn-fw" data-toggle="modal" data-target="#removeLeadModal" onclick="removeLead(' . (int)$row['id'] . ')">
                <i class="fa fa-2x fa-trash-o"></i>
            </button>
        </div>';
        
        $data[] = [
            $siVar++,
            htmlspecialchars($row['lead_nm'] ?? ''),
            htmlspecialchars($row['company_nm'] ?? ''),
            htmlspecialchars($row['contact'] ?? ''),
            htmlspecialchars($row['email'] ?? ''),
            htmlspecialchars($row['lead_status'] ?? ''),
            date('d-m-Y h:i A', strtotime($row['follow_up_dt'] ?? 'now')),
            $btn
        ];
    }
    
    echo json_encode(["success" => true, "data" => $data, "message" => "Unassigned leads found"]);
} else {
    echo json_encode(["success" => false, "data" => [], "message" => "No unassigned leads found"]);
}
?>
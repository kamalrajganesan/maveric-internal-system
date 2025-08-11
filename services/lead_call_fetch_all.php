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
$FetchAllSQL = "SELECT * FROM lead_call_tracker WHERE is_deleted = 0";

// Apply assignee filtering to fetch only assigned leads
$FetchAllSQL .= " AND assignee IS NOT NULL AND assignee != 0"; // Only fetch assigned leads
if (!$isAdmin) {
    // Agents see only their own assigned leads
    $FetchAllSQL .= " AND assignee = " . intval($currentUserId);
}

// Apply status filters if specified
switch ($page) {
    case 'New':
        $FetchAllSQL .= " AND lead_status = 'New'";
        break;
    case 'Contacted':
        $FetchAllSQL .= " AND lead_status = 'Contacted'";
        break;
    case 'Converted':
        $FetchAllSQL .= " AND lead_status = 'Converted'";
        break;
    case 'Following':
        $FetchAllSQL .= " AND lead_status = 'Following'";
        break;
    case 'Lost':
        $FetchAllSQL .= " AND lead_status = 'Lost'";
        break;
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
                <button type="button" class="btn btn-inverse-primary btn-fw" data-toggle="modal" data-target="#viewLeadModal" id="viewLeadModalBtn" onclick="viewLead(' . $row['id'] . ')">
                    <i class="fa fa-2x fa-ellipsis-v"></i>
                </button>
                <button type="button" class="btn btn-inverse-secondary btn-fw" data-toggle="modal" data-target="#editLeadModal" id="editLeadModalBtn" onclick="editLead(' . $row['id'] . ')">
                    <i class="fa fa-2x fa-pencil-square-o"></i>
                </button>
                <button type="button" class="btn btn-inverse-dark btn-fw" data-toggle="modal" data-target="#removeLeadModal" id="removeLeadModalBtn" onclick="removeLead(' . $row['id'] . ')">
                    <i class="fa fa-2x fa-trash-o"></i>
                </button>
            </div>
        ';

        $data[] = [
            $siVar++,
            htmlspecialchars($row['lead_nm']),
            htmlspecialchars($row['company_nm']),
            htmlspecialchars($row['contact']),
            htmlspecialchars($row['email']),
            $row['lead_status'],
            date('d-m-Y h:i A', strtotime($row['follow_up_dt'])),
            $btn
        ];
    }
    
    echo json_encode([
        "success" => true,
        "data" => $data,
        "message" => "Assigned call leads found"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "data" => [],
        "message" => "No assigned call leads found"
    ]);
}
?>
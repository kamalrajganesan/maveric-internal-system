<?php
require_once("../shared/actions/db/dao.php");

$page = "";
if (isset($_POST['param'])) {
    $page = htmlspecialchars($_POST['param']);
}

$db = new sqlHelper();

// Base query for unassigned leads only
$FetchAllSQL = "SELECT id, email, contact, company_nm, lead_status, follow_up_dt 
                FROM lead_email_tracker 
                WHERE is_deleted = 0 
                AND (assignee IS NULL OR assignee = 0 OR assignee = '')";

// Role-based filtering for unassigned leads
if (isset($_SESSION['user']['role']) && isset($_SESSION['user']['id'])) {
    switch ($_SESSION['user']['role']) {
        case 'admin':
        case 'manager':
        case 'agent':
            // Admins, managers, and agents can see all unassigned leads (no additional filter needed)
            break;
        default:
            // Default to most restrictive view
            echo json_encode(["success" => false, "data" => [], "message" => "Unauthorized access."]);
            exit();
    }
} else {
    // If no role or user ID is set, return empty result to prevent unauthorized access
    echo json_encode(["success" => false, "data" => [], "message" => "User not authenticated."]);
    exit();
}

$db->prepareStatement($FetchAllSQL);
$db->execPreparedStatement();
$FetchAllSQLResultSet = $db->getResultSet();

if ($FetchAllSQLResultSet->num_rows > 0) {
    $data = [];
    
    while ($row = $FetchAllSQLResultSet->fetch_assoc()) {
        $btn = '
        <div class="btn-group">
            <button type="button" class="btn btn-inverse-primary btn-fw" data-toggle="modal" data-target="#viewLeadModal" onclick="viewLead(' . $row['id'] . ')">
                <i class="fa fa-2x fa-ellipsis-v"></i>
            </button>
            <button type="button" class="btn btn-inverse-secondary btn-fw" data-toggle="modal" data-target="#editLeadModal" onclick="editLead(' . $row['id'] . ')">
                <i class="fa fa-2x fa-pencil-square-o"></i>
            </button>
            <button type="button" class="btn btn-inverse-dark btn-fw" data-toggle="modal" data-target="#removeLeadModal" onclick="removeLead(' . $row['id'] . ')">
                <i class="fa fa-2x fa-trash-o"></i>
            </button>
        </div>
        ';
        
        $data[] = [
            $row['email'],
            $row['contact'],
            $row['company_nm'],
            $row['lead_status'],
            date('d-m-Y h:i:s A', strtotime($row['follow_up_dt'])),
            $btn
        ];
    }
    
    echo json_encode(["success" => true, "data" => $data, "message" => "Unassigned leads found."]);
} else {
    echo json_encode(["success" => false, "data" => [], "message" => "No unassigned leads found."]);
}
?>
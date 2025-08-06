<?php
require_once("../shared/actions/db/dao.php");

$page = "";
if (isset($_POST['param'])) {
    $page = htmlspecialchars($_POST['param']);
}

$db = new sqlHelper();

// Base query for assigned leads only
$FetchAllSQL = "SELECT id, email, contact, company_nm, lead_status, follow_up_dt 
                FROM lead_email_tracker 
                WHERE is_deleted = 0 
                AND assignee IS NOT NULL 
                AND assignee != 0 
                AND assignee != ''";

// Optional status filtering for assigned leads
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
    default:
        // Show all assigned leads regardless of status
        break;
}

// Role-based filtering for assigned leads
if (isset($_SESSION['user']['role'])) {
    switch ($_SESSION['user']['role']) {
        case 'admin':
        case 'manager':
            // Admins and managers can see all assigned leads
            break;
        case 'agent':
            // Agents can only see leads assigned to them
            $currentUserId = $_SESSION['user']['id'];
            $FetchAllSQL .= " AND assignee = " . intval($currentUserId); // Use intval for security
            break;
        default:
            // Default to most restrictive view if role isn't recognized
            $currentUserId = isset($_SESSION['user']['id']) ? intval($_SESSION['user']['id']) : 0;
            $FetchAllSQL .= " AND assignee = " . intval($currentUserId);
            break;
    }
} else {
    // If no role is set, assume most restrictive view
    $currentUserId = isset($_SESSION['user']['id']) ? intval($_SESSION['user']['id']) : 0;
    $FetchAllSQL .= " AND assignee = " . intval($currentUserId);
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
    
    echo json_encode(["success" => true, "data" => $data, "message" => "Assigned leads found."]);
} else {
    echo json_encode(["success" => false, "data" => [], "message" => "No assigned leads found."]);
}
?>
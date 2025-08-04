<?php
require_once("../shared/actions/db/dao.php");
$page = "";
if (isset($_POST['param'])) {
    $page = htmlspecialchars($_POST['param']);
} else {
    // echo "param not found";
}
$db = new sqlHelper();
$FetchAllSQL = "SELECT * FROM lead_email_tracker WHERE is_deleted = 0";
switch ($page) {
    case 'New':
        $FetchAllSQL .= " and lead_status = 'New'";
        break;
    case 'Contacted':
        $FetchAllSQL .= " and lead_status = 'Contacted'";
        break;
    case 'Converted':
        $FetchAllSQL .= " and lead_status = 'Converted'";
        break;
    case 'Following';
        $FetchAllSQL .= " and lead_status = 'Following'";
        break;
    case 'Lost':
        $FetchAllSQL .= " and lead_status = 'Lost'";
        break;
    default:
        // echo "Invalid Parm...";
        break;
}

// Custom filtering based on role
if (isset($_SESSION['user']['role'])) {
    switch ($_SESSION['user']['role']) {
        case 'admin':
        case 'manager':
            // Admins and managers can see all leads - no additional filter needed
            break;
        case 'agent':
            // Agents can see:
            // 1. Unassigned leads (assignee IS NULL or assignee = 0 or assignee = '')
            // 2. Leads assigned to them specifically
            $currentUserId = $_SESSION['user']['id'];
            $FetchAllSQL .= " AND (assignee IS NULL OR assignee = 0 OR assignee = '' OR assignee = '$currentUserId')";
            break;
        default:
            // Default to most restrictive view if role isn't recognized
            $currentUserId = $_SESSION['user']['id'];
            $FetchAllSQL .= " AND (assignee = '$currentUserId')";
            break;
    }
} else {
    // If no role is set at all, assume most restrictive view
    $currentUserId = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;
    $FetchAllSQL .= " AND (assignee = '$currentUserId')";
}

$db->prepareStatement($FetchAllSQL);
$db->execPreparedStatement();
$FetchAllSQLResultSet = $db->getResultSet();
if ($FetchAllSQLResultSet->num_rows > 0) {
    $data = array();
    // $siVar = 1;
    $checkBox = '<input type="checkbox" class="row-checkbox">';
   
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
        $data[] = array(
            $row['email'],
            $row['contact'],
            $row['company_nm'],
            $row['lead_status'],
            date('d-m-Y h:i:s A', strtotime($row['follow_up_dt'])),
            $btn
        );
        // $siVar++;
    }
    echo json_encode(array("success" => true, "data" => $data, "message" => "Data found."));
} else {
    echo json_encode(array("success" => false, "data" => [], "message" => "No data found."));
}
?>
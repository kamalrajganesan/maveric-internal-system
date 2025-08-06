<?php
require_once("../shared/actions/db/dao.php");

if (!session_id()) {
    session_start();
}

ob_start(); // Prevent stray output
header('Content-Type: application/json'); // Set JSON response type

$db = new sqlHelper();

// Check for unassignedOnly parameter
$unassignedOnly = isset($_POST['unassignedOnly']) && $_POST['unassignedOnly'] === 'true';
$userType = isset($_POST['userType']) ? htmlspecialchars($_POST['userType']) : '';

// Base query
$FetchAllSQL = "SELECT * FROM lead_call_tracker WHERE is_deleted = 0";

// Filter by unassigned leads for agents
if ($unassignedOnly && $userType === 'agent') {
    $FetchAllSQL .= " AND (assignee IS NULL OR assignee = 0 OR assignee = '')";
} elseif ($userType === 'agent') {
    // For agents: show only their assigned leads and unassigned leads (as per original logic)
    $FetchAllSQL .= " AND (assignee = " . (isset($_SESSION['user']['id']) ? intval($_SESSION['user']['id']) : 0) . " OR assignee IS NULL OR assignee = 0)";
} else {
    // For admins: show all leads (no additional filter needed)
}

// Filter by status if specified
$page = isset($_POST['param']) ? htmlspecialchars($_POST['param']) : '';
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
        break;
}

$db->prepareStatement($FetchAllSQL);
$db->execPreparedStatement();
$FetchAllSQLResultSet = $db->getResultSet();

if ($FetchAllSQLResultSet && $FetchAllSQLResultSet->num_rows > 0) {
    $data = array();
    $siVar = 1;
    while ($row = $FetchAllSQLResultSet->fetch_assoc()) {
        $btn = '
        <div class="btn-group">
            <button type="button" class="btn btn-inverse-primary btn-fw" data-toggle="modal" data-target="#viewLeadModal" id="viewLeadModalBtn" onclick="viewLead(' . $row['id'] . ')">
                <i class="fa fa-2x fa-ellipsis-v"></i>
            </button>';
        
        // Only show edit button if admin or if agent is assigned to this lead
        if (isset($_SESSION['userType']) && ($_SESSION['userType'] === 'admin' || $row['assignee'] == $_SESSION['user']['id'])) {
            $btn .= '
            <button type="button" class="btn btn-inverse-secondary btn-fw" data-toggle="modal" data-target="#editLeadModal" id="editLeadModalBtn" onclick="editLead(' . $row['id'] . ')">
                <i class="fa fa-2x fa-pencil-square-o"></i>
            </button>';
        }
        
        $btn .= '
            <button type="button" class="btn btn-inverse-dark btn-fw" data-toggle="modal" data-target="#removeLeadModal" id="removeLeadModalBtn" onclick="removeLead(' . $row['id'] . ')">
                <i class="fa fa-2x fa-trash-o"></i>
            </button>
        </div>
        ';

        $data[] = array(
            $siVar,
            $row['lead_nm'],
            $row['company_nm'],
            $row['contact'],
            $row['email'],
            $row['lead_status'],
            date('d-m-Y h:i:s A', strtotime($row['follow_up_dt'])),
            $btn
        );
        $siVar++;
    }
    ob_end_clean();
    echo json_encode(array("success" => true, "data" => $data, "message" => "Unassigned call leads found."));
} else {
    ob_end_clean();
    echo json_encode(array("success" => false, "data" => [], "message" => "No unassigned call leads found."));
}
?>
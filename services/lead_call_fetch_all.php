<?php
require_once("../shared/actions/db/dao.php");

if (!session_id()) {
    session_start();
}

$page = "";
if (isset($_POST['param'])) {
    $page = htmlspecialchars($_POST['param']);
} else {
    // echo "param not found";
}

$db = new sqlHelper();
$FetchAllSQL = "SELECT * FROM lead_call_tracker WHERE is_deleted = 0";

// Filter by status if specified
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
        // No status filter
        break;
}   

// Filter by assignee based on user type
if ($_SESSION['userType'] === 'agent') {
    // For agents: show only their assigned leads (excluding NULL/0 assignments)
    $FetchAllSQL .= " AND assignee = " . $_SESSION['user']['id'];
} else {
    // For admins: show all assigned leads (excluding NULL/0 assignments)
    $FetchAllSQL .= " AND assignee IS NOT NULL AND assignee != 0";
}

$db->prepareStatement($FetchAllSQL);
$db->execPreparedStatement();
$FetchAllSQLResultSet = $db->getResultSet();

if ($FetchAllSQLResultSet->num_rows > 0) {
    $data = array();
    $siVar = 1;
    while ($row = $FetchAllSQLResultSet->fetch_assoc()) {
        $btn = '
        <div class="btn-group">
            <button type="button" class="btn btn-inverse-primary btn-fw" data-toggle="modal" data-target="#viewLeadModal" id="viewLeadModalBtn" onclick="viewLead(' . $row['id'] . ')">
                <i class="fa fa-2x fa-ellipsis-v"></i>
            </button>';
        
        // Only show edit button if admin or if agent is assigned to this lead
        if ($_SESSION['userType'] === 'admin' || $row['assignee'] == $_SESSION['user']['id']) {
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
    echo json_encode(array("success" => true, "data" => $data, "message" => "Data found."));
} else {
    echo json_encode(array("success" => false, "data" => [], "message" => "No data found."));
}
?>
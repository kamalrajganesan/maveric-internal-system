<?php
require_once("../shared/actions/db/dao.php");

if (!session_id()) {
    session_start();
}

// Check authentication
if (!isset($_SESSION['user']['role']) || !isset($_SESSION['user']['id'])) {
    echo json_encode(["success" => false, "data" => [], "message" => "User not authenticated."]);
    exit();
}

// Allow only admins
if ($_SESSION['user']['role'] !== 'admin') {
    echo json_encode(["success" => false, "data" => [], "message" => "You are not authorized to view Lost leads."]);
    exit();
}

$db = new sqlHelper();

// Query: fetch ALL Lost leads (assigned or not)
$FetchAllSQL = "SELECT id, email, contact, company_nm, lead_status, follow_up_dt 
                FROM lead_email_tracker 
                WHERE is_deleted = 0 
                AND lead_status = 'Lost'
                ORDER BY follow_up_dt DESC";

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
            !empty($row['follow_up_dt']) ? date('d-m-Y h:i:s A', strtotime($row['follow_up_dt'])) : "-",
            $btn
        ];
    }
    
    echo json_encode([
        "success" => true,
        "data" => $data,
        "message" => "Lost leads found."
    ]);
} else {
    echo json_encode([
        "success" => false,
        "data" => [],
        "message" => "No Lost leads found."
    ]);
}
?>
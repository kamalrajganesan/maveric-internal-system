<?php
require_once("../shared/actions/db/dao.php");

if (!session_id()) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['userType'])) {
    echo json_encode(["success" => false, "message" => "Authentication required"]);
    exit();
}

$db = new sqlHelper();

// Base query: Only Lost leads
$FetchAllSQL = "SELECT * FROM lead_call_tracker WHERE is_deleted = 0 AND lead_status = 'Lost'";

// Allow only admin
if ($_SESSION['userType'] !== 'admin') {
    echo json_encode([
        "success" => false,
        "data" => [],
        "message" => "You are not authorized to view Lost leads."
    ]);
    exit();
}

// Order by creation date (newest first)
$FetchAllSQL .= " ORDER BY created_on DESC";

$db->prepareStatement($FetchAllSQL);
$db->execPreparedStatement();
$FetchAllSQLResultSet = $db->getResultSet();

if ($FetchAllSQLResultSet->num_rows > 0) {
    $data = [];
    $siVar = 1;

    while ($row = $FetchAllSQLResultSet->fetch_assoc()) {
        // Action buttons
        $btn = '
        <div class="btn-group">
            <button type="button" class="btn btn-inverse-primary btn-fw" onclick="viewLead(' . $row['id'] . ')">
                <i class="fa fa-2x fa-ellipsis-v"></i>
            </button>
            <button type="button" class="btn btn-inverse-secondary btn-fw" onclick="editLead(' . $row['id'] . ')">
                <i class="fa fa-pencil-square-o"></i>
            </button>
            <button type="button" class="btn btn-inverse-dark btn-fw" onclick="removeLead(' . $row['id'] . ')">
                <i class="fa fa-trash-o"></i>
            </button>
        </div>';

        // Format follow-up date
        $followUpDate = '';
        if (!empty($row['follow_up_dt'])) {
            $followUpDate = date('d-m-Y h:i A', strtotime($row['follow_up_dt']));
        }

        $data[] = [
            $siVar,
            htmlspecialchars($row['lead_nm'] ?? ''),
            htmlspecialchars($row['company_nm'] ?? ''),
            htmlspecialchars($row['contact'] ?? ''),
            htmlspecialchars($row['email'] ?? ''),
            htmlspecialchars($row['lead_status'] ?? ''),
            $followUpDate,
            $btn
        ];
        $siVar++;
    }

    echo json_encode([
        "success" => true,
        "data" => $data,
        "message" => "Lost leads found.",
        "total" => count($data)
    ]);
} else {
    echo json_encode([
        "success" => false,
        "data" => [],
        "message" => "No lost leads found."
    ]);
}
?>
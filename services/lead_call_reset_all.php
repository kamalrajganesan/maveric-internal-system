<?php
require_once("../shared/actions/db/dao.php");

if (!session_id()) session_start();

// Only allow admins
if (!isset($_SESSION['userType']) || $_SESSION['userType'] !== 'admin') {
    echo json_encode(["success" => false, "message" => "You are not authorized to perform this action."]);
    exit();
}

$db = new sqlHelper();

// Reset all Lost leads to New and move them to unassigned (assignee = NULL)
$updateSQL = "UPDATE lead_call_tracker 
              SET lead_status = 'New', assignee = NULL 
              WHERE lead_status = 'Lost' AND is_deleted = 0";

$db->prepareStatement($updateSQL);
$result = $db->execPreparedStatement();

if ($result) {
    echo json_encode(["success" => true, "message" => "All Lost leads have been reset to New and moved to unassigned."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to reset leads."]);
}
?>

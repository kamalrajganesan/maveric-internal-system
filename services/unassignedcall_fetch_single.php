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

$currentUserId = (int)$_SESSION['user']['id'];
$isAdmin = $_SESSION['userType'] === 'admin';

if (isset($_POST['leadId'])) {
    $db = new sqlHelper();
    
    $leadFetchSingleSQL = "
        SELECT 
            l.id, 
            l.lead_nm AS lead_name, 
            l.email, 
            l.company_nm AS company_name, 
            l.contact, 
            l.requirement, 
            l.description, 
            l.notes, 
            l.address_ln AS address_line, 
            l.pincode, 
            l.city, 
            l.area, 
            l.follow_up_dt AS follow_up_date, 
            l.lead_status, 
            l.is_active, 
            l.assignee,
            l.log,
            l.created_by,
            l.updated_by,
            l.created_at
        FROM 
            lead_call_tracker l
        WHERE 
            l.is_deleted = 0 
            AND l.id = ?
            AND (l.assignee IS NULL OR l.assignee = 0 OR l.assignee = '')
    ";

    // For agents, no additional restriction needed since we're fetching unassigned leads
    $db->prepareStatement($leadFetchSingleSQL);
    $db->setParameters([$_POST['leadId']], 'i');

    $db->execPreparedStatement();
    $leadFetchSingleResultSet = $db->getResultSet();

    if ($leadFetchSingleResultSet->num_rows > 0) {
        $data = [];
        while ($row = $leadFetchSingleResultSet->fetch_assoc()) {
            // Ensure log is a valid JSON array
            $log = $row['log'] ? $row['log'] : '[]';
            try {
                json_decode($log);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $log = '[]'; // Fallback to empty array if invalid JSON
                }
            } catch (Exception $e) {
                $log = '[]'; // Fallback to empty array on error
            }

            $data[] = [
                'id' => (int)$row['id'],
                'lead_name' => $row['lead_name'] ?: '',
                'email' => $row['email'] ?: '',
                'company_name' => $row['company_name'] ?: '',
                'contact' => $row['contact'] ?: '',
                'requirement' => $row['requirement'] ?: '',
                'description' => $row['description'] ?: '',
                'notes' => $row['notes'] ?: '',
                'address_line' => $row['address_line'] ?: '',
                'pincode' => $row['pincode'] ?: '',
                'city' => $row['city'] ?: '',
                'area' => $row['area'] ?: '',
                'follow_up_date' => $row['follow_up_date'] ? date('Y-m-d\TH:i', strtotime($row['follow_up_date'])) : '',
                'lead_status' => $row['lead_status'] ?: '',
                'is_active' => (int)$row['is_active'],
                'assignee' => $row['assignee'] !== null ? (int)$row['assignee'] : 0,
                'log' => $log,
                'created_by' => $row['created_by'] !== null ? (int)$row['created_by'] : 0,
                'updated_by' => $row['updated_by'] !== null ? (int)$row['updated_by'] : 0,
                'created_at' => $row['created_at'] ? date('Y-m-d H:i:s', strtotime($row['created_at'])) : ''
            ];
        }
        echo json_encode(["success" => true, "data" => $data, "message" => "Unassigned lead found"]);
    } else {
        echo json_encode(["success" => false, "data" => [], "message" => "No unassigned lead found"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}
?>
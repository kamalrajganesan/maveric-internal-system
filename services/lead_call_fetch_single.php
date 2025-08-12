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
            l.assignee AS assignee_id,
            l.log,
            l.created_by AS created_by_id,
            l.updated_by AS updated_by_id,
            l.created_on,
            l.updated_on
        FROM
            lead_call_tracker l
        WHERE
            l.is_deleted = 0
            AND l.id = ?
    ";
   
    // For non-admin users, restrict to their own assigned leads OR unassigned leads
    if (!$isAdmin) {
        $leadFetchSingleSQL .= " AND (l.assignee = ? OR l.assignee IS NULL OR l.assignee = 0)";
        $db->prepareStatement($leadFetchSingleSQL);
        $db->setParameters([$_POST['leadId'], $currentUserId], 'ii');
    } else {
        $db->prepareStatement($leadFetchSingleSQL);
        $db->setParameters([$_POST['leadId']], 'i');
    }
   
    $db->execPreparedStatement();
    $leadFetchSingleResultSet = $db->getResultSet();
   
    if ($leadFetchSingleResultSet->num_rows > 0) {
        $data = [];
        while ($row = $leadFetchSingleResultSet->fetch_assoc()) {
            // Parse history from log field
            $history = [];
            if (!empty($row['log'])) {
                try {
                    $decodedHistory = json_decode($row['log'], true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decodedHistory)) {
                        $history = $decodedHistory;
                    }
                } catch (Exception $e) {
                    // Keep empty array if parsing fails
                }
            }
            
            // If no proper history exists, create one for backward compatibility
            if (empty($history)) {
                $history = [
                    [
                        "action" => "created",
                        "changed_by" => getUserNameForHistory($row['created_by_id']),
                        "date" => $row['created_on'] ?? date("Y-m-d H:i:s"),
                        "changes" => [],
                        "status_change" => [
                            "from" => "",
                            "to" => $row['lead_status']
                        ]
                    ]
                ];
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
                'follow_up_date' => $row['follow_up_date'] ?: '', // Send raw datetime
                'lead_status' => $row['lead_status'] ?: '',
                'is_active' => (int)$row['is_active'],
                'assignee' => $row['assignee_id'] !== null ? (int)$row['assignee_id'] : 0,
                'created_by' => $row['created_by_id'] !== null ? (int)$row['created_by_id'] : 0,
                'updated_by' => $row['updated_by_id'] !== null ? (int)$row['updated_by_id'] : 0,
                'created_at' => $row['created_on'],
                'updated_at' => $row['updated_on'],
                'log' => json_encode($history),
                'history' => $history
            ];
        }
        echo json_encode(["success" => true, "data" => $data, "message" => "Data found"]);
    } else {
        echo json_encode(["success" => false, "data" => [], "message" => "No assigned lead found"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}

function getUserNameForHistory($userId) {
    if (!$userId || $userId == 0) {
        return 'Admin';
    }
    
    $db = new sqlHelper();
    $sql = "SELECT agent_nm FROM agent WHERE id = ?";
    $db->prepareStatement($sql);
    $db->setParameters([$userId], 'i');
    $db->execPreparedStatement();
    $result = $db->getResultSet();
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['agent_nm'] ?? 'Unknown User';
    }
    
    return 'Unknown User';
}
?>
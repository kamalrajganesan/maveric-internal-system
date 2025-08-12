<?php
// lead_call_edit.php - Updated with fixed assignee logic
require_once("../shared/actions/db/dao.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!session_id()) {
        session_start();
    }
    
    // Verify user is logged in
    if (!isset($_SESSION['userType']) || !isset($_SESSION['user']['id'])) {
        echo json_encode(["success" => false, "message" => "Authentication required"]);
        exit();
    }
    
    $leadId = isset($_POST['lId']) ? intval($_POST['lId']) : 0;
    if ($leadId <= 0) {
        echo json_encode(["success" => false, "message" => "Invalid Lead ID"]);
        exit();
    }
    
    // Get form data
    $newData = [
        'lead_nm' => $_POST['leadNm'] ?? '',
        'email' => $_POST['email'] ?? '',
        'company_nm' => $_POST['companyNm'] ?? '',
        'contact' => $_POST['contact'] ?? '',
        'requirement' => $_POST['requirement'] ?? '',
        'description' => $_POST['description'] ?? '',
        'notes' => $_POST['notes'] ?? '',
        'address_ln' => $_POST['addressLn'] ?? '',
        'area' => $_POST['area'] ?? '',
        'city' => $_POST['city'] ?? '',
        'pincode' => $_POST['pincode'] ?? '',
        'follow_up_dt' => $_POST['followUpDt'] ?? '',
        'lead_status' => $_POST['leadStatus'] ?? '',
        'assignee' => isset($_POST['assignee']) ? intval($_POST['assignee']) : null
    ];
    
    $currentUserId = intval($_SESSION['user']['id']);
    $currentUserName = $_SESSION['user']['nm'] ?? 'Unknown User';
    $isAdmin = ($_SESSION['userType'] ?? '') === 'admin';
    
    $db = new sqlHelper();
    
    // Get current lead data for comparison
    $currentDataSql = "SELECT * FROM lead_call_tracker WHERE id = ? AND is_deleted = 0";
    if (!$isAdmin) {
        $currentDataSql .= " AND (assignee = ? OR assignee IS NULL OR assignee = 0)";
        $db->prepareStatement($currentDataSql);
        $db->setParameters([$leadId, $currentUserId], 'ii');
    } else {
        $db->prepareStatement($currentDataSql);
        $db->setParameters([$leadId], 'i');
    }
    
    $db->execPreparedStatement();
    $result = $db->getResultSet();
    
    if ($result->num_rows == 0) {
        echo json_encode(["success" => false, "message" => "Lead not found or access denied"]);
        exit();
    }
    
    $currentData = $result->fetch_assoc();
    
    // Compare data and track changes
    $changes = [];
    $statusChange = null;
    
    $fieldMappings = [
        'lead_nm' => 'Lead Name',
        'email' => 'Email',
        'company_nm' => 'Company Name',
        'contact' => 'Contact',
        'requirement' => 'Requirement',
        'description' => 'Description',
        'notes' => 'Notes',
        'address_ln' => 'Address Line',
        'area' => 'Area',
        'city' => 'City',
        'pincode' => 'Pincode',
        'follow_up_dt' => 'Follow-up Date'
    ];
    
    foreach ($fieldMappings as $dbField => $displayName) {
        $oldValue = $currentData[$dbField] ?? '';
        $newValue = $newData[$dbField] ?? '';
        
        if ($oldValue != $newValue) {
            $changes[] = [
                'field' => $displayName,
                'old_value' => $oldValue,
                'new_value' => $newValue
            ];
        }
    }
    
    // Check for status change
    if ($currentData['lead_status'] != $newData['lead_status']) {
        $statusChange = [
            'from' => $currentData['lead_status'],
            'to' => $newData['lead_status']
        ];
    }
    
    // Handle assignee logic
    $originalAssignee = $currentData['assignee'];
    $requestedAssignee = $newData['assignee'];
    
    // For agents: Auto-assign if unassigned, but don't allow changing existing assignment
    if (!$isAdmin) {
        if (!$originalAssignee || $originalAssignee == 0) {
            // Auto-assign to current agent if unassigned
            $newData['assignee'] = $currentUserId;
            $changes[] = [
                'field' => 'Assignee',
                'old_value' => 'Unassigned',
                'new_value' => $currentUserName
            ];
        } else {
            // Keep original assignee - agents cannot change assignments
            $newData['assignee'] = $originalAssignee;
        }
    } else {
        // For admins: Allow assignee changes
        if ($originalAssignee != $requestedAssignee) {
            $oldAssigneeName = getUserNameForHistory($originalAssignee);
            $newAssigneeName = getUserNameForHistory($requestedAssignee);
            
            $changes[] = [
                'field' => 'Assignee',
                'old_value' => $oldAssigneeName,
                'new_value' => $newAssigneeName
            ];
        }
    }
    
    // Only proceed if there are actual changes
    if (empty($changes) && !$statusChange) {
        echo json_encode(["success" => true, "message" => "No changes detected"]);
        exit();
    }
    
    // Parse existing history
    $existingHistory = [];
    if (!empty($currentData['log'])) {
        $existingHistory = json_decode($currentData['log'], true) ?? [];
        // Handle old format conversion if needed
        if (!empty($existingHistory) && !isset($existingHistory[0]['action'])) {
            $existingHistory = convertOldHistoryFormat($existingHistory, $currentData);
        }
    }
    
    // Add new history entry
    $newHistoryEntry = [
        'action' => 'updated',
        'changed_by' => $currentUserName,
        'date' => date('Y-m-d H:i:s'),
        'changes' => $changes,
        'status_change' => $statusChange
    ];
    
    $existingHistory[] = $newHistoryEntry;
    $updatedHistoryJson = json_encode($existingHistory);
    
    // Build update query
    $updateFields = [];
    $updateParams = [];
    $updateTypes = '';
    
    foreach ($newData as $field => $value) {
        if ($field === 'assignee') {
            $updateFields[] = "$field = " . ($value ? intval($value) : 'NULL');
        } else {
            $updateFields[] = "$field = ?";
            $updateParams[] = $value;
            $updateTypes .= 's';
        }
    }
    
    $updateFields[] = "log = ?";
    $updateFields[] = "updated_by = ?";
    $updateFields[] = "updated_on = NOW()";
    
    $updateParams[] = $updatedHistoryJson;
    $updateParams[] = $currentUserId;
    $updateTypes .= 'si';
    
    $sql = "UPDATE lead_call_tracker SET " . implode(', ', $updateFields) . " WHERE id = ?";
    $updateParams[] = $leadId;
    $updateTypes .= 'i';
    
    $db->prepareStatement($sql);
    $db->setParameters($updateParams, $updateTypes);
    $resp = $db->execPreparedStatement();
    
    if ($resp['success']) {
        $valid['success'] = true;
        $valid['message'] = "Successfully updated lead details.";
    } else {
        $valid['success'] = false;
        $valid['message'] = "Error while updating the lead details: " . ($resp['message'] ?? 'Unknown error');
    }
    
    echo json_encode($valid);
}

function getUserNameForHistory($userId) {
    if (!$userId || $userId == 0) {
        return 'Unassigned';
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

function convertOldHistoryFormat($oldHistory, $currentData) {
    $newHistory = [];
    
    // If it's an array of old-style comments
    if (is_array($oldHistory)) {
        foreach ($oldHistory as $comment) {
            if (isset($comment['message']) || isset($comment['status'])) {
                $newHistory[] = [
                    'action' => strpos($comment['message'] ?? '', 'Created') !== false ? 'created' : 'updated',
                    'changed_by' => $comment['commentBy'] ?? 'Unknown User',
                    'date' => $comment['date'] ?? $currentData['created_on'] ?? date('Y-m-d H:i:s'),
                    'changes' => [],
                    'status_change' => [
                        'from' => '',
                        'to' => $comment['status'] ?? 'Unknown'
                    ]
                ];
            }
        }
    }
    
    // If no valid history found, create a default creation entry
    if (empty($newHistory)) {
        $creatorName = getUserNameForHistory($currentData['created_by'] ?? 0);
        $newHistory[] = [
            'action' => 'created',
            'changed_by' => $creatorName,
            'date' => $currentData['created_on'] ?? date('Y-m-d H:i:s'),
            'changes' => [],
            'status_change' => [
                'from' => '',
                'to' => $currentData['lead_status'] ?? 'Unknown'
            ]
        ];
    }
    
    return $newHistory;
}
?>
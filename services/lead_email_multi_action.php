<?php
require_once("../shared/actions/db/dao.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $response = ['success' => false, 'message' => ''];

    // Get all input values
    $leadStatus = $_POST['leadStatus'] ?? null;
    $followUpDate = $_POST['followUpDt'] ?? null;
    $leads = $_POST['leads'] ?? [];
    $updatedBy = $_SESSION['user']['id'] ?? null;

    // Validation
    if (empty($leads) || !$updatedBy) {
        $response['message'] = 'Missing required fields.';
        echo json_encode($response);
        exit;
    }

    // Prepare lead IDs
    $leadIds = array_map(fn($lead) => $lead[0], $leads);
    $placeholders = implode(',', array_fill(0, count($leadIds), '?'));

    // Build dynamic SET clauses
    $setParts = [];
    $params = [];
    $types = '';

    // Only add status if explicitly provided (not empty)
    if ($leadStatus !== null && $leadStatus !== '') {
        $setParts[] = "lead_status = ?";
        $params[] = $leadStatus;
        $types .= 's';
    }

    // Only add follow-up date if explicitly provided (not empty)
    if ($followUpDate !== null && $followUpDate !== '') {
        $setParts[] = "follow_up_dt = ?";
        $params[] = htmlspecialchars($followUpDate);
        $types .= 's';
    }

    // If nothing to update (except metadata)
    if (empty($setParts)) {
        $response['message'] = 'Nothing to update.';
        echo json_encode($response);
        exit;
    }

    // Always update modified metadata
    $setParts[] = "updated_on = NOW()";
    $setParts[] = "updated_by = ?";
    $params[] = $updatedBy;
    $types .= 'i';

    // Build final query
    $query = "UPDATE lead_email_tracker SET " . implode(', ', $setParts) . " WHERE id IN ($placeholders)";
    $params = array_merge($params, $leadIds);
    $types .= str_repeat('i', count($leadIds));

    try {
        $db = new sqlHelper();
        $stmt = $db->prepareStatement($query);
        $db->setParameters($params, $types);
        $db->execPreparedStatement();

        $response['success'] = true;
        $response['message'] = 'Leads updated successfully.';

    } catch (Exception $e) {
        $response['message'] = 'Update failed.';
        $response['detailed'] = $e->getMessage();
    }

    echo json_encode($response);
}
?>
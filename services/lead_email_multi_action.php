<?php
require_once("../shared/actions/db/dao.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Start session if not already started
    if (!session_id()) {
        session_start();
    }

    // Initialize response array
    $response = [
        'success' => false,
        'message' => '',
        'detailed' => ''
    ];

    // Validate and sanitize input
    $followUpDate = !empty($_POST['followUpDt']) ? htmlspecialchars($_POST['followUpDt']) : null;
    $leadStatus = !empty($_POST['leadStatus']) ? htmlspecialchars($_POST['leadStatus']) : null;
    $leads = !empty($_POST['leads']) ? $_POST['leads'] : [];

    // Validate mandatory fields
    if (empty($leadStatus) || empty($leads)) {
        $response['message'] = "Mandatory fields missing";
        $response['detailed'] = "Lead status and leads selection are required";
        echo json_encode($response);
        exit();
    }
 date_default_timezone_set('Asia/Kolkata');
    // Validate and format follow-up date
    if ($followUpDate) {
        $dateTime = DateTime::createFromFormat('Y-m-d', $followUpDate);
        if ($dateTime === false) {
            $response['message'] = "Invalid date format";
            $response['detailed'] = "Follow-up date must be in YYYY-MM-DD format";
            echo json_encode($response);
            exit();
        }
        $followUpDate = $dateTime->format('Y-m-d H:i:s'); // Add current time
    }

    // Extract lead IDs
    $leadIds = array_column($leads, 0);
    $updatedBy = $_SESSION['user']['id'];

    try {
        $db = new sqlHelper();
        
        // Prepare the query with placeholders
        $placeholders = implode(',', array_fill(0, count($leadIds), '?'));
        $query = "UPDATE lead_email_tracker SET 
                    follow_up_dt = ?,
                    lead_status = ?,
                    updated_on = NOW(),
                    updated_by = ?
                  WHERE id IN ($placeholders)";

        // Prepare the statement
        $stmt = $db->prepareStatement($query);

        // Bind parameters
        $params = [$followUpDate, $leadStatus, $updatedBy];
        $params = array_merge($params, $leadIds);

        // Set parameter types (s=string, i=integer)
        $types = 'sss' . str_repeat('i', count($leadIds));
        $db->setParameters($params, $types);

        // Execute the statement
        $result = $db->execPreparedStatement();

        if ($result['success']) {
            $response['success'] = true;
            $response['message'] = 'Successfully updated ' . count($leadIds) . ' leads';
            $response['updated_count'] = count($leadIds);
        } else {
            $response['message'] = 'Database error';
            $response['detailed'] = $result['message'];
        }

    } catch (Exception $e) {
        $response['message'] = 'System error';
        $response['detailed'] = $e->getMessage();
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>
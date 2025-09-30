<?php
// Enable error reporting during development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Always return JSON
header('Content-Type: application/json');

// Include DB connection
require_once('../config/db.php'); // adjust path if needed

try {
    // Read POST params
    $dateRange  = isset($_POST['dateRange']) ? trim($_POST['dateRange']) : '';
    $singleDate = isset($_POST['singleDate']) ? trim($_POST['singleDate']) : '';
    $leadType   = isset($_POST['leadType']) ? trim($_POST['leadType']) : '';

    // Default conditions
    $dateWhereCall  = "1=1";
    $dateWhereEmail = "1=1";

    // Handle date filters
    if (!empty($dateRange) && strpos($dateRange, ' to ') !== false) {
        $dates = explode(' to ', $dateRange);
        $startDate = date('Y-m-d', strtotime(str_replace('/', '-', $dates[0])));
        $endDate   = date('Y-m-d', strtotime(str_replace('/', '-', $dates[1])));

        $dateWhereCall  = "DATE(lct.created_on) BETWEEN '$startDate' AND '$endDate'";
        $dateWhereEmail = "DATE(let.created_on) BETWEEN '$startDate' AND '$endDate'";
    } elseif (!empty($singleDate)) {
        $date = date('Y-m-d', strtotime(str_replace('/', '-', $singleDate)));
        $dateWhereCall  = "DATE(lct.created_on) = '$date'";
        $dateWhereEmail = "DATE(let.created_on) = '$date'";
    }

    // Base query
    $query = "
        SELECT 
            a.id as agent_id,
            a.agent_nm,

            -- Total leads handled
            (
                SELECT COUNT(DISTINCT lct.id) 
                FROM lead_call_tracker lct 
                WHERE lct.assignee = a.id 
                AND lct.is_deleted = 0
                AND $dateWhereCall
            ) + 
            (
                SELECT COUNT(DISTINCT let.id) 
                FROM lead_email_tracker let 
                WHERE let.created_by = a.id 
                AND let.is_deleted = 0
                AND $dateWhereEmail
            ) as leads_handled,

            -- Leads in hand
            (
                SELECT COUNT(DISTINCT lct.id) 
                FROM lead_call_tracker lct 
                WHERE lct.assignee = a.id 
                AND lct.is_deleted = 0
                AND lct.lead_status IN ('New', 'Contacted/Pending', 'Following Up')
                AND $dateWhereCall
            ) + 
            (
                SELECT COUNT(DISTINCT let.id) 
                FROM lead_email_tracker let 
                WHERE let.created_by = a.id 
                AND let.is_deleted = 0
                AND let.lead_status IN ('New', 'Contacted/Pending', 'Following Up')
                AND $dateWhereEmail
            ) as leads_in_hand,

            -- Leads converted
            (
                SELECT COUNT(DISTINCT lct.id) 
                FROM lead_call_tracker lct 
                WHERE lct.assignee = a.id 
                AND lct.is_deleted = 0
                AND lct.lead_status = 'Closed'
                AND $dateWhereCall
            ) + 
            (
                SELECT COUNT(DISTINCT let.id) 
                FROM lead_email_tracker let 
                WHERE let.created_by = a.id 
                AND let.is_deleted = 0
                AND let.lead_status = 'Closed'
                AND $dateWhereEmail
            ) as leads_converted,

            -- Leads lost
            (
                SELECT COUNT(DISTINCT lct.id) 
                FROM lead_call_tracker lct 
                WHERE lct.assignee = a.id 
                AND lct.is_deleted = 0
                AND lct.lead_status = 'Lost'
                AND $dateWhereCall
            ) + 
            (
                SELECT COUNT(DISTINCT let.id) 
                FROM lead_email_tracker let 
                WHERE let.created_by = a.id 
                AND let.is_deleted = 0
                AND let.lead_status = 'Lost'
                AND $dateWhereEmail
            ) as leads_lost

        FROM agent a
        WHERE a.is_deleted = 0 
        AND a.is_active = 1
    ";

    // Lead type filter
    if (!empty($leadType)) {
        if ($leadType === 'Phone Call') {
            $query .= " AND EXISTS (
                SELECT 1 FROM lead_call_tracker lct 
                WHERE lct.assignee = a.id AND lct.is_deleted = 0 AND $dateWhereCall
            )";
        } elseif ($leadType === 'Email') {
            $query .= " AND EXISTS (
                SELECT 1 FROM lead_email_tracker let 
                WHERE let.created_by = a.id AND let.is_deleted = 0 AND $dateWhereEmail
            )";
        }
    }

    $query .= " GROUP BY a.id, a.agent_nm ORDER BY leads_handled DESC";

    // Run query
    $data = [];
    $counter = 1;

    if (isset($conn)) {
        $result = mysqli_query($conn, $query);
        if (!$result) {
            throw new Exception("MySQL error: " . mysqli_error($conn));
        }
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = [
                'sno'             => $counter++,
                'agent_name'      => htmlspecialchars($row['agent_nm']),
                'leads_handled'   => (int)$row['leads_handled'],
                'leads_in_hand'   => (int)$row['leads_in_hand'],
                'leads_converted' => (int)$row['leads_converted'],
                'leads_lost'      => (int)$row['leads_lost']
            ];
        }
    } else {
        throw new Exception("DB connection not found");
    }

    // ✅ Clean output buffer before returning JSON
    if (ob_get_length()) ob_clean();

    echo json_encode([
        'success' => true,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);
    exit;

} catch (Exception $e) {
    if (ob_get_length()) ob_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'data' => []
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

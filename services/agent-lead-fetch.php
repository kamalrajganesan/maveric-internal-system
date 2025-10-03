<?php
// Enable error reporting during development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Always return JSON
header('Content-Type: application/json');

// Include DB connection
require_once('../shared/php/connect.php');

$conn = createConn();
if (!$conn) {
    throw new Exception("Database connection failed");
}

try {
    // Read POST params
    $dateRange  = isset($_POST['dateRange']) ? trim($_POST['dateRange']) : '';
    $singleDate = isset($_POST['singleDate']) ? trim($_POST['singleDate']) : '';
    $leadType   = isset($_POST['leadType']) ? trim($_POST['leadType']) : '';
    $leadStatus = isset($_POST['leadStatus']) ? trim($_POST['leadStatus']) : '';

    // Prepare date filters with proper conversion
    $dateWhereCall  = "1=1";
    $dateWhereEmail = "1=1";

    if (!empty($dateRange) && strpos($dateRange, ' to ') !== false) {
        list($start, $end) = explode(' to ', $dateRange);
        
        // Convert DD/MM/YYYY to YYYY-MM-DD
        $startParts = explode('/', trim($start));
        $endParts = explode('/', trim($end));
        
        if (count($startParts) === 3 && count($endParts) === 3) {
            $startDate = $startParts[2] . '-' . str_pad($startParts[1], 2, '0', STR_PAD_LEFT) . '-' . str_pad($startParts[0], 2, '0', STR_PAD_LEFT);
            $endDate = $endParts[2] . '-' . str_pad($endParts[1], 2, '0', STR_PAD_LEFT) . '-' . str_pad($endParts[0], 2, '0', STR_PAD_LEFT);
            
            $startDateEsc = mysqli_real_escape_string($conn, $startDate);
            $endDateEsc = mysqli_real_escape_string($conn, $endDate);
            
            $dateWhereCall  = "DATE(lct.created_on) BETWEEN '$startDateEsc' AND '$endDateEsc'";
            $dateWhereEmail = "DATE(let.created_on) BETWEEN '$startDateEsc' AND '$endDateEsc'";
        }
    } elseif (!empty($singleDate)) {
        // Convert DD/MM/YYYY to YYYY-MM-DD
        $dateParts = explode('/', trim($singleDate));
        
        if (count($dateParts) === 3) {
            $date = $dateParts[2] . '-' . str_pad($dateParts[1], 2, '0', STR_PAD_LEFT) . '-' . str_pad($dateParts[0], 2, '0', STR_PAD_LEFT);
            $dateEsc = mysqli_real_escape_string($conn, $date);
            
            $dateWhereCall  = "DATE(lct.created_on) = '$dateEsc'";
            $dateWhereEmail = "DATE(let.created_on) = '$dateEsc'";
        }
    }

    // Handle lead status mapping
    $callStatusFilter = "";
    $emailStatusFilter = "";
    
    if (!empty($leadStatus)) {
        $leadStatusEsc = mysqli_real_escape_string($conn, $leadStatus);
        
        // Map status values for call leads
        $callStatusMap = [
            'New' => 'New',
            'Contacted' => 'Contacted',
            'Following' => 'Following',
            'Converted' => 'Converted',
            'Lost' => 'Lost'
        ];
        
        // Map status values for email leads
        $emailStatusMap = [
            'New' => 'New',
            'Contacted' => 'Contacted',
            'Following' => 'Following',
            'Converted' => 'Converted',
            'Lost' => 'Lost',
            'Emailed and Waiting for reply' => 'Emailed and Waiting for reply'
        ];
        
        if (isset($callStatusMap[$leadStatus])) {
            $callStatusFilter = " AND lct.lead_status = '$leadStatusEsc'";
        }
        
        if (isset($emailStatusMap[$leadStatus])) {
            $emailStatusFilter = " AND let.lead_status = '$leadStatusEsc'";
        }
    }

    $leadTypeEsc = mysqli_real_escape_string($conn, $leadType);

    // Base query
    $query = "
        SELECT 
            a.id as agent_id,
            a.agent_nm,

            -- Leads Handled: ALL leads (no date or status filtering, just count everything)
            (
                SELECT COUNT(DISTINCT lct.id) 
                FROM lead_call_tracker lct 
                WHERE lct.assignee = a.id 
                AND lct.is_deleted = 0
            ) + 
            (
                SELECT COUNT(DISTINCT let.id) 
                FROM lead_email_tracker let 
                WHERE let.assignee = a.id 
                AND let.is_deleted = 0
            ) as leads_handled,

            -- Leads In Hand: Apply date, lead type, and status filters
            CASE 
                WHEN '$leadTypeEsc' = 'Phone Call' THEN
                    (
                        SELECT COUNT(DISTINCT lct.id) 
                        FROM lead_call_tracker lct 
                        WHERE lct.assignee = a.id 
                        AND lct.is_deleted = 0
                        AND lct.lead_status IN ('New', 'Contacted', 'Following')
                        AND $dateWhereCall
                        $callStatusFilter
                    )
                WHEN '$leadTypeEsc' = 'Email' THEN
                    (
                        SELECT COUNT(DISTINCT let.id) 
                        FROM lead_email_tracker let 
                        WHERE let.assignee = a.id 
                        AND let.is_deleted = 0
                        AND let.lead_status IN ('New', 'Contacted', 'Following', 'Emailed and Waiting for reply')
                        AND $dateWhereEmail
                        $emailStatusFilter
                    )
                ELSE
                    (
                        SELECT COUNT(DISTINCT lct.id) 
                        FROM lead_call_tracker lct 
                        WHERE lct.assignee = a.id 
                        AND lct.is_deleted = 0
                        AND lct.lead_status IN ('New', 'Contacted', 'Following')
                        AND $dateWhereCall
                        $callStatusFilter
                    ) + 
                    (
                        SELECT COUNT(DISTINCT let.id) 
                        FROM lead_email_tracker let 
                        WHERE let.assignee = a.id 
                        AND let.is_deleted = 0
                        AND let.lead_status IN ('New', 'Contacted', 'Following', 'Emailed and Waiting for reply')
                        AND $dateWhereEmail
                        $emailStatusFilter
                    )
            END as leads_in_hand,

            -- Leads Converted: Apply date and lead type filters
            CASE 
                WHEN '$leadTypeEsc' = 'Phone Call' THEN
                    (
                        SELECT COUNT(DISTINCT lct.id) 
                        FROM lead_call_tracker lct 
                        WHERE lct.assignee = a.id 
                        AND lct.is_deleted = 0
                        AND lct.lead_status = 'Converted'
                        AND $dateWhereCall
                    )
                WHEN '$leadTypeEsc' = 'Email' THEN
                    (
                        SELECT COUNT(DISTINCT let.id) 
                        FROM lead_email_tracker let 
                        WHERE let.assignee = a.id 
                        AND let.is_deleted = 0
                        AND let.lead_status = 'Converted'
                        AND $dateWhereEmail
                    )
                ELSE
                    (
                        SELECT COUNT(DISTINCT lct.id) 
                        FROM lead_call_tracker lct 
                        WHERE lct.assignee = a.id 
                        AND lct.is_deleted = 0
                        AND lct.lead_status = 'Converted'
                        AND $dateWhereCall
                    ) + 
                    (
                        SELECT COUNT(DISTINCT let.id) 
                        FROM lead_email_tracker let 
                        WHERE let.assignee = a.id 
                        AND let.is_deleted = 0
                        AND let.lead_status = 'Converted'
                        AND $dateWhereEmail
                    )
            END as leads_converted,

            -- Leads Lost: Apply date and lead type filters
            CASE 
                WHEN '$leadTypeEsc' = 'Phone Call' THEN
                    (
                        SELECT COUNT(DISTINCT lct.id) 
                        FROM lead_call_tracker lct 
                        WHERE lct.assignee = a.id 
                        AND lct.is_deleted = 0
                        AND lct.lead_status = 'Lost'
                        AND $dateWhereCall
                    )
                WHEN '$leadTypeEsc' = 'Email' THEN
                    (
                        SELECT COUNT(DISTINCT let.id) 
                        FROM lead_email_tracker let 
                        WHERE let.assignee = a.id 
                        AND let.is_deleted = 0
                        AND let.lead_status = 'Lost'
                        AND $dateWhereEmail
                    )
                ELSE
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
                        WHERE let.assignee = a.id 
                        AND let.is_deleted = 0
                        AND let.lead_status = 'Lost'
                        AND $dateWhereEmail
                    )
            END as leads_lost

        FROM agent a
        WHERE a.is_deleted = 0 
        AND a.is_active = 1
        GROUP BY a.id, a.agent_nm 
        ORDER BY leads_handled DESC
    ";

    // Execute query
    $data = [];
    $counter = 1;

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

    // Return JSON
    echo json_encode([
        'success' => true,
        'data' => $data,
        'totalRecords' => count($data),
        'filters' => [
            'dateRange' => $dateRange,
            'singleDate' => $singleDate,
            'leadType' => $leadType,
            'leadStatus' => $leadStatus
        ]
    ], JSON_UNESCAPED_UNICODE);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'data' => []
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
?>
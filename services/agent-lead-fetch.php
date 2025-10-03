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
    
    $startParts = explode('/', trim($start));
    $endParts   = explode('/', trim($end));
    
    if (count($startParts) === 3 && count($endParts) === 3) {
        $startDate = $startParts[2] . '-' . str_pad($startParts[1], 2, '0', STR_PAD_LEFT) . '-' . str_pad($startParts[0], 2, '0', STR_PAD_LEFT);
        $endDate   = $endParts[2]   . '-' . str_pad($endParts[1],   2, '0', STR_PAD_LEFT) . '-' . str_pad($endParts[0],   2, '0', STR_PAD_LEFT);

        $startDateEsc = mysqli_real_escape_string($conn, $startDate);
        $endDateEsc   = mysqli_real_escape_string($conn, $endDate);

        // ✅ Check both created_on & updated_on
        $dateWhereCall  = "(DATE(lct.created_on) BETWEEN '$startDateEsc' AND '$endDateEsc' 
                         OR DATE(lct.updated_on) BETWEEN '$startDateEsc' AND '$endDateEsc')";
        $dateWhereEmail = "(DATE(let.created_on) BETWEEN '$startDateEsc' AND '$endDateEsc' 
                         OR DATE(let.updated_on) BETWEEN '$startDateEsc' AND '$endDateEsc')";
    }
} elseif (!empty($singleDate)) {
    $dateParts = explode('/', trim($singleDate));
    if (count($dateParts) === 3) {
        $date = $dateParts[2] . '-' . str_pad($dateParts[1], 2, '0', STR_PAD_LEFT) . '-' . str_pad($dateParts[0], 2, '0', STR_PAD_LEFT);
        $dateEsc = mysqli_real_escape_string($conn, $date);

        // ✅ Check both created_on & updated_on
        $dateWhereCall  = "(DATE(lct.created_on) = '$dateEsc' OR DATE(lct.updated_on) = '$dateEsc')";
        $dateWhereEmail = "(DATE(let.created_on) = '$dateEsc' OR DATE(let.updated_on) = '$dateEsc')";
    }
}

    // Handle lead status filter
    $statusFilterCall = "";
    $statusFilterEmail = "";
    
    if (!empty($leadStatus)) {
        $leadStatusEsc = mysqli_real_escape_string($conn, $leadStatus);
        $statusFilterCall = " AND lct.lead_status = '$leadStatusEsc'";
        
        // Handle email status specially for "Emailed and Waiting for reply"
        if ($leadStatus === 'Emailed and Waiting for reply') {
            $statusFilterEmail = " AND let.lead_status = 'Emailed and Waiting for reply'";
        } else {
            $statusFilterEmail = " AND let.lead_status = '$leadStatusEsc'";
        }
    }

    $query = "
SELECT 
    a.id as agent_id,
    a.agent_nm,

    -- New
    (
        SELECT COUNT(*) FROM lead_call_tracker lct
        WHERE lct.assignee = a.id 
        AND lct.is_deleted = 0
        AND lct.lead_status = 'New'
        " . ($leadType === 'Phone Call' || $leadType === '' ? "AND $dateWhereCall" : "AND 1=0") . "
        " . ($leadType === 'Phone Call' || $leadType === '' ? $statusFilterCall : "") . "
    ) +
    (
        SELECT COUNT(*) FROM lead_email_tracker let
        WHERE let.assignee = a.id 
        AND let.is_deleted = 0
        AND let.lead_status = 'New'
        " . ($leadType === 'Email' || $leadType === '' ? "AND $dateWhereEmail" : "AND 1=0") . "
        " . ($leadType === 'Email' || $leadType === '' ? $statusFilterEmail : "") . "
    ) as new_leads,

    -- Following Up
    (
        SELECT COUNT(*) FROM lead_call_tracker lct
        WHERE lct.assignee = a.id 
        AND lct.is_deleted = 0
        AND lct.lead_status = 'Following'
        " . ($leadType === 'Phone Call' || $leadType === '' ? "AND $dateWhereCall" : "AND 1=0") . "
        " . ($leadType === 'Phone Call' || $leadType === '' ? $statusFilterCall : "") . "
    ) +
    (
        SELECT COUNT(*) FROM lead_email_tracker let
        WHERE let.assignee = a.id 
        AND let.is_deleted = 0
        AND let.lead_status = 'Following'
        " . ($leadType === 'Email' || $leadType === '' ? "AND $dateWhereEmail" : "AND 1=0") . "
        " . ($leadType === 'Email' || $leadType === '' ? $statusFilterEmail : "") . "
    ) as following_up,

    -- Converted
    (
        SELECT COUNT(*) FROM lead_call_tracker lct
        WHERE lct.assignee = a.id 
        AND lct.is_deleted = 0
        AND lct.lead_status = 'Converted'
        " . ($leadType === 'Phone Call' || $leadType === '' ? "AND $dateWhereCall" : "AND 1=0") . "
        " . ($leadType === 'Phone Call' || $leadType === '' ? $statusFilterCall : "") . "
    ) +
    (
        SELECT COUNT(*) FROM lead_email_tracker let
        WHERE let.assignee = a.id 
        AND let.is_deleted = 0
        AND let.lead_status = 'Converted'
        " . ($leadType === 'Email' || $leadType === '' ? "AND $dateWhereEmail" : "AND 1=0") . "
        " . ($leadType === 'Email' || $leadType === '' ? $statusFilterEmail : "") . "
    ) as converted,

    -- Contacted / Emailed
    (
        SELECT COUNT(*) FROM lead_call_tracker lct
        WHERE lct.assignee = a.id 
        AND lct.is_deleted = 0
        AND lct.lead_status = 'Contacted'
        " . ($leadType === 'Phone Call' || $leadType === '' ? "AND $dateWhereCall" : "AND 1=0") . "
        " . ($leadType === 'Phone Call' || $leadType === '' ? $statusFilterCall : "") . "
    ) +
    (
        SELECT COUNT(*) FROM lead_email_tracker let
        WHERE let.assignee = a.id 
        AND let.is_deleted = 0
        AND let.lead_status IN ('Contacted', 'Emailed and Waiting for reply')
        " . ($leadType === 'Email' || $leadType === '' ? "AND $dateWhereEmail" : "AND 1=0") . "
        " . ($leadType === 'Email' || $leadType === '' ? $statusFilterEmail : "") . "
    ) as contacted_emailed,

    -- Lost
    (
        SELECT COUNT(*) FROM lead_call_tracker lct
        WHERE lct.assignee = a.id 
        AND lct.is_deleted = 0
        AND lct.lead_status = 'Lost'
        " . ($leadType === 'Phone Call' || $leadType === '' ? "AND $dateWhereCall" : "AND 1=0") . "
        " . ($leadType === 'Phone Call' || $leadType === '' ? $statusFilterCall : "") . "
    ) +
    (
        SELECT COUNT(*) FROM lead_email_tracker let
        WHERE let.assignee = a.id 
        AND let.is_deleted = 0
        AND let.lead_status = 'Lost'
        " . ($leadType === 'Email' || $leadType === '' ? "AND $dateWhereEmail" : "AND 1=0") . "
        " . ($leadType === 'Email' || $leadType === '' ? $statusFilterEmail : "") . "
    ) as lost,

    -- Total Leads Handled
    (
        SELECT COUNT(*) FROM lead_call_tracker lct 
        WHERE lct.assignee = a.id 
        AND lct.is_deleted = 0
        " . ($leadType === 'Phone Call' ? "AND $dateWhereCall" : "") . "
    ) +
    (
        SELECT COUNT(*) FROM lead_email_tracker let 
        WHERE let.assignee = a.id 
        AND let.is_deleted = 0
        " . ($leadType === 'Email' ? "AND $dateWhereEmail" : "") . "
    ) as leads_handled

FROM agent a
WHERE a.is_deleted = 0 AND a.is_active = 1
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
            'sno'                => $counter++,
            'agent_name'         => htmlspecialchars($row['agent_nm']),
            'new_leads'          => (int)$row['new_leads'],
            'following_up'       => (int)$row['following_up'],
            'converted'          => (int)$row['converted'],
            'contacted_emailed'  => (int)$row['contacted_emailed'],
            'lost'               => (int)$row['lost'],
            'leads_handled'      => (int)$row['leads_handled']
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
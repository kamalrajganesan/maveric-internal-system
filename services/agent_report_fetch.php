<?php
require_once("../shared/actions/db/dao.php");
$db = new sqlHelper();

if (!session_id()) session_start();

// --- Debug: Log all POST data ---
error_log("=== AGENT REPORT DEBUG ===");
error_log("POST data: " . print_r($_POST, true));
error_log("=== END DEBUG ===");

// --- Filters ---
$serviceType = trim($_POST['serviceType'] ?? '');
$dateRange   = trim($_POST['transactionDateRange'] ?? '');
$singleDate  = trim($_POST['transactionDate'] ?? '');

// Debug individual fields
error_log("serviceType: '$serviceType'");
error_log("dateRange: '$dateRange'");
error_log("singleDate: '$singleDate'");

// --- Prepare date range ---
$startDate = '1970-01-01';
$endDate   = '2099-12-31';

// Priority: Single date over date range
if ($singleDate !== '') {
    // Single date filter - filter for that specific day
    $parsedDate = date('Y-m-d', strtotime(str_replace('/', '-', trim($singleDate))));
    error_log("Processing single date: '$singleDate' -> '$parsedDate'");
    
    if ($parsedDate !== '1970-01-01') { // Check if date parsing was successful
        $startDate = $parsedDate . ' 00:00:00';
        $endDate = $parsedDate . ' 23:59:59';
    }
} elseif ($dateRange !== '') {
    // Date range filter
    error_log("Processing date range: '$dateRange'");
    
    if (strpos($dateRange, ' to ') !== false) {
        $dates = explode(' to ', $dateRange);
        if (count($dates) == 2) {
            $startDate = date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', trim($dates[0]))));
            $endDate = date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', trim($dates[1]))));
        }
    } else {
        // If it's a single value but in dateRange field, treat as single date
        $parsedDate = date('Y-m-d', strtotime(str_replace('/', '-', trim($dateRange))));
        if ($parsedDate !== '1970-01-01') {
            $startDate = $parsedDate . ' 00:00:00';
            $endDate = $parsedDate . ' 23:59:59';
        }
    }
}

error_log("Final date range: $startDate to $endDate");

// Simple SQL approach
$sql = "
SELECT 
    a.id AS agent_id,
    a.agent_nm,
    COUNT(t.id) AS total_transactions,
    MAX(t.created_on) AS last_transaction
FROM agent a
LEFT JOIN ticket t ON t.assignd_agent_id = a.id 
    AND t.created_on BETWEEN ? AND ? 
    AND t.is_deleted = 0
";

$params = [$startDate, $endDate];
$types = 'ss';

// Add service type filter to JOIN
if ($serviceType !== '' && $serviceType !== 'all') {
    $sql .= " AND t.service_thru = ?";
    $params[] = $serviceType;
    $types .= 's';
}

$sql .= " WHERE a.is_deleted = 0 GROUP BY a.id ORDER BY a.agent_nm ASC";

error_log("Final SQL: $sql");
error_log("Parameters: " . print_r($params, true));

$db->prepareStatement($sql);
$db->setParameters($params, $types);
$resp = $db->execPreparedStatement();

$resultData = [];
if ($resp['success']) {
    $rs = $db->getResultSet();
    $rowCount = 0;
    while ($row = $rs->fetch_assoc()) {
        $rowCount++;
        $lastTransaction = $row['last_transaction'] 
            ? date('d-m-Y H:i', strtotime($row['last_transaction']))
            : 'Never';

        $transactionCount = $serviceType !== '' && $serviceType !== 'all' 
            ? $row['total_transactions'] . ' ' . $serviceType
            : $row['total_transactions'] . ' transactions';

        $resultData[] = [
            'agent_id' => $row['agent_id'],
            'agent_name' => htmlspecialchars($row['agent_nm']),
            'total_transactions' => $transactionCount,
            'last_transaction' => $lastTransaction
        ];
    }
    
    error_log("Rows returned: $rowCount");

    echo json_encode(['success' => true, 'data' => $resultData]);
} else {
    error_log("Query failed");
    echo json_encode(['success' => false, 'data' => [], 'message' => 'Query failed']);
}
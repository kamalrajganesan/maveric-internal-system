<?php
require_once("../shared/actions/db/dao.php");
$db = new sqlHelper();

if (!session_id()) session_start();

// --- Filters ---
$serviceType = trim($_POST['serviceType'] ?? '');
$dateRange   = trim($_POST['transactionDateRange'] ?? '');

// Debug
error_log("Date Range: " . $dateRange);
error_log("Service Type: " . $serviceType);

// --- Prepare date range ---
$startDate = '1970-01-01';
$endDate   = '2099-12-31';
if ($dateRange !== '') {
    $dates = explode(' to ', $dateRange);
    if (count($dates) == 2) {
        $startDate = date('Y-m-d', strtotime(str_replace('/', '-', trim($dates[0]))));
        $endDate = date('Y-m-d', strtotime(str_replace('/', '-', trim($dates[1]))));
    }
}

// Debug converted dates
error_log("Start Date: " . $startDate);
error_log("End Date: " . $endDate);

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

// Debug final SQL
error_log("SQL: " . $sql);

$db->prepareStatement($sql);
$db->setParameters($params, $types);
$resp = $db->execPreparedStatement();

$resultData = [];
if ($resp['success']) {
    $rs = $db->getResultSet();
    while ($row = $rs->fetch_assoc()) {
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

    echo json_encode(['success' => true, 'data' => $resultData]);
} else {
    echo json_encode(['success' => false, 'data' => [], 'message' => 'Query failed']);
}
?>
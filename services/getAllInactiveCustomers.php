<?php
require_once("../shared/actions/db/dao.php");
$db = new sqlHelper();

if (!session_id()) session_start();

// --- Get filters ---
$serviceThru = trim($_POST['serviceThrough'] ?? '');
$serviceType = trim($_POST['serviceType'] ?? '');
$dateRange   = trim($_POST['dateRange'] ?? '');
$singleDate  = trim($_POST['singleDate'] ?? '');
$pincode     = trim($_POST['pincode'] ?? '');

// ----------------------
// Build filter conditions for tickets
// ----------------------
$ticketFilterSql = "tk.is_deleted = 0";
$ticketParams = [];
$ticketTypes = '';

// Single date filter
if ($singleDate !== '') {
    $parts = explode('/', $singleDate);
    if (count($parts) === 3) {
        $ticketFilterSql .= " AND DATE(tk.created_on) = ?";
        $ticketParams[] = $parts[2] . '-' . str_pad($parts[1],2,'0',STR_PAD_LEFT) . '-' . str_pad($parts[0],2,'0',STR_PAD_LEFT);
        $ticketTypes .= 's';
    }
}
// Date range filter
elseif ($dateRange !== '' && strpos($dateRange, ' to ') !== false) {
    $dates = explode(' to ', $dateRange);
    if (count($dates) === 2) {
        $start = explode('/', $dates[0]);
        $end   = explode('/', $dates[1]);
        if (count($start) === 3 && count($end) === 3) {
            $ticketFilterSql .= " AND DATE(tk.created_on) BETWEEN ? AND ?";
            $ticketParams[] = $start[2] . '-' . str_pad($start[1],2,'0',STR_PAD_LEFT) . '-' . str_pad($start[0],2,'0',STR_PAD_LEFT);
            $ticketParams[] = $end[2]   . '-' . str_pad($end[1],2,'0',STR_PAD_LEFT)   . '-' . str_pad($end[0],2,'0',STR_PAD_LEFT);
            $ticketTypes .= 'ss';
        }
    }
}

// Service Type filter (AMC, Tally Subscription, Cloud, One Time)
if ($serviceType !== '' && strtolower($serviceType) !== 'all') {
    $ticketFilterSql .= " AND tk.service_typ = ?";
    $ticketParams[] = $serviceType;
    $ticketTypes .= 's';
}

// Service Through filter (Remote, Phone Call, Physical Visit)
if ($serviceThru !== '' && strtolower($serviceThru) !== 'all') {
    $ticketFilterSql .= " AND tk.service_thru = ?";
    $ticketParams[] = $serviceThru;
    $ticketTypes .= 's';
}

// ----------------------
// Determine the label based on service_thru filter
// ----------------------
$serviceLabel = 'Transactions';
if ($serviceThru !== '' && strtolower($serviceThru) !== 'all') {
    $serviceLabel = $serviceThru;
}

// ----------------------
// Main query - Always show customer's actual services
// ----------------------
$sql = "
SELECT
    c.id AS customer_id,
    c.customer_uniq_code,
    c.company_nm,
    COALESCE(c.service_type, 'No Service') AS services,
    COALESCE(CONCAT(t.total_count, ' $serviceLabel'), '0 $serviceLabel') AS total_services,
    t.last_service_date
FROM cust_mstr c
LEFT JOIN (
    SELECT 
        tk.customer_id,
        COUNT(*) AS total_count,
        MAX(tk.created_on) AS last_service_date
    FROM ticket tk
    WHERE $ticketFilterSql
    GROUP BY tk.customer_id
) t ON c.id = t.customer_id
WHERE c.is_active = 1 AND c.is_deleted = 0
";

// Customer-level filters
$customerParams = [];
$customerTypes  = '';

// Pincode filter
if ($pincode !== '') {
    $sql .= " AND c.pincode LIKE ?";
    $customerParams[] = "%$pincode%";
    $customerTypes .= 's';
}

// Service type filter at customer level
if ($serviceType !== '' && strtolower($serviceType) !== 'all') {
    $sql .= " AND c.service_type LIKE ?";
    $customerParams[] = "%$serviceType%";
    $customerTypes .= 's';
}

// ----------------------
// Bind parameters
// ----------------------
$allParams = array_merge($ticketParams, $customerParams);
$allTypes  = $ticketTypes . $customerTypes;

// ----------------------
// Execute
// ----------------------
$db->prepareStatement($sql);
if (!empty($allParams)) {
    $db->setParameters($allParams, $allTypes);
}
$resp = $db->execPreparedStatement();

// ----------------------
// Process results
// ----------------------
$resultData = [];
if ($resp['success']) {
    $rs = $db->getResultSet();
    if ($rs->num_rows > 0) {
        $i = 1;
        while ($row = $rs->fetch_assoc()) {
            $customerBtn = '<button type="button" class="btn btn-link p-0 view-btn" '
                .'data-bs-toggle="modal" data-bs-target="#viewCustomerModal" '
                .'data-customer="'.htmlspecialchars($row['customer_uniq_code']).'">'
                .htmlspecialchars($row['company_nm']).'</button>';

            // Clean up service display - remove brackets and quotes if present
            $services = $row['services'];
            $services = str_replace(['"', '[', ']'], '', $services);
            if (empty(trim($services))) {
                $services = 'No Service';
            }

            $totalServices = $row['total_services'];

            $lastServiceDate = $row['last_service_date']
                ? date('d-m-Y H:i', strtotime($row['last_service_date']))
                : "Never";

            $daysSince = $row['last_service_date']
                ? (new DateTime())->diff(new DateTime($row['last_service_date']))->days
                : "Never";

            $resultData[] = [
                $i,
                $customerBtn,
                $services,
                $totalServices,
                $lastServiceDate,
                $daysSince
            ];
            $i++;
        }
        echo json_encode(['success'=>true,'data'=>$resultData]);
    } else {
        echo json_encode(['success'=>false,'data'=>[],'message'=>'No data found']);
    }
} else {
    echo json_encode(['success'=>false,'data'=>[],'message'=>'Query execution failed']);
}
?>
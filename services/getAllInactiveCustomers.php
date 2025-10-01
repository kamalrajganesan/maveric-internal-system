<?php
require_once("../shared/actions/db/dao.php");
$db = new sqlHelper();

if (!session_id()) session_start();

// --- Get filters ---
$serviceThru = trim($_POST['serviceThrough'] ?? '');
$serviceType = trim($_POST['serviceType'] ?? '');
$dateRange   = trim($_POST['dateRange'] ?? '');
$singleDate  = trim($_POST['singleDate'] ?? '');

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
// Determine the label based on filters
// ----------------------
$hasTicketFilters = ($singleDate !== '' || $dateRange !== '' || 
                     ($serviceThru !== '' && strtolower($serviceThru) !== 'all') ||
                     ($serviceType !== '' && strtolower($serviceType) !== 'all'));

// ----------------------
// Main query
// ----------------------
$sql = "
SELECT
    c.id AS customer_id,
    c.customer_uniq_code,
    c.company_nm,
    c.pincode,
    COALESCE(c.service_type, 'No Service') AS services,
    COALESCE(total_tickets.total_count, 0) AS total_count,
    COALESCE(filtered_tickets.filtered_count, 0) AS filtered_count,
    filtered_tickets.last_service_date
FROM cust_mstr c
LEFT JOIN (
    SELECT 
        customer_id,
        COUNT(*) AS total_count
    FROM ticket
    WHERE is_deleted = 0
    GROUP BY customer_id
) total_tickets ON c.id = total_tickets.customer_id
LEFT JOIN (
    SELECT 
        tk.customer_id,
        COUNT(*) AS filtered_count,
        MAX(tk.created_on) AS last_service_date
    FROM ticket tk
    WHERE $ticketFilterSql
    GROUP BY tk.customer_id
) filtered_tickets ON c.id = filtered_tickets.customer_id
WHERE c.is_active = 1 AND c.is_deleted = 0
";

// Customer-level filters
$customerParams = [];
$customerTypes  = '';

// Service type filter at customer level
if ($serviceType !== '' && strtolower($serviceType) !== 'all') {
    $sql .= " AND c.service_type LIKE ?";
    $customerParams[] = "%$serviceType%";
    $customerTypes .= 's';
}

$sql .= " ORDER BY c.company_nm ASC";

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

            // Clean up service display
            $services = $row['services'];
            $services = str_replace(['"', '[', ']'], '', $services);
            if (empty(trim($services))) {
                $services = 'No Service';
            }

            // Calculate counts
            $totalCount = (int)($row['total_count'] ?? 0);
            $filteredCount = (int)($row['filtered_count'] ?? 0);

            // Determine label for Total Services Consumed
            if ($serviceThru !== '' && strtolower($serviceThru) !== 'all') {
                $totalServices = $filteredCount . ' ' . $serviceThru;
            } elseif ($hasTicketFilters) {
                $totalServices = $filteredCount . ' Transactions';
            } else {
                $totalServices = $totalCount . ' Transactions';
            }

            $lastServiceDate = $row['last_service_date']
                ? date('d-m-Y H:i', strtotime($row['last_service_date']))
                : "Never";

            $daysSince = $row['last_service_date']
                ? (new DateTime())->diff(new DateTime($row['last_service_date']))->days
                : "Never";

            $resultData[] = [
                $i,                         // Column 0: S.No
                $customerBtn,               // Column 1: Company Name
                $services,                  // Column 2: Service Consumed
                $row['pincode'],            // Column 3: Pincode (hidden but searchable)
                $totalServices,             // Column 4: Total Services Consumed
                $lastServiceDate,           // Column 5: Last Service Date
                $daysSince                  // Column 6: Days Since Last Service
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
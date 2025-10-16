<?php
// services/getAllInactiveCustomers.php
require_once("../shared/actions/db/dao.php");
$db = new sqlHelper();

if (!session_id()) session_start();

// --- Get filters from POST ---
$dateRange   = trim($_POST['dateRange'] ?? ''); // format: "DD/MM/YYYY to DD/MM/YYYY" from flatpickr range
$pincode     = trim($_POST['pincode'] ?? '');
$serviceThru = trim($_POST['serviceThrough'] ?? '');
$serviceType = trim($_POST['serviceType'] ?? '');

// ----------------------
// Build ticket-level filter conditions
// ----------------------
$ticketFilterSql = "tk.is_deleted = 0";
$ticketParams = [];
$ticketTypes = '';

// Date range filter on tickets
if ($dateRange !== '' && strpos($dateRange, ' to ') !== false) {
    $dates = explode(' to ', $dateRange);
    if (count($dates) === 2) {
        $start = explode('/', trim($dates[0]));
        $end   = explode('/', trim($dates[1]));
        if (count($start) === 3 && count($end) === 3) {
            $ticketFilterSql .= " AND DATE(tk.created_on) BETWEEN ? AND ?";
            $ticketParams[] = $start[2] . '-' . str_pad($start[1],2,'0',STR_PAD_LEFT) . '-' . str_pad($start[0],2,'0',STR_PAD_LEFT);
            $ticketParams[] = $end[2]   . '-' . str_pad($end[1],2,'0',STR_PAD_LEFT)   . '-' . str_pad($end[0],2,'0',STR_PAD_LEFT);
            $ticketTypes .= 'ss';
        }
    }
}

// Service Type filter (ticket-level)
if ($serviceType !== '' && strtolower($serviceType) !== 'all') {
    $ticketFilterSql .= " AND tk.service_typ = ?";
    $ticketParams[] = $serviceType;
    $ticketTypes .= 's';
}

// Service Through filter (ticket-level)
if ($serviceThru !== '' && strtolower($serviceThru) !== 'all') {
    $ticketFilterSql .= " AND tk.service_thru = ?";
    $ticketParams[] = $serviceThru;
    $ticketTypes .= 's';
}

// ----------------------
// Determine if ticket filters exist (for label text decisions)
// ----------------------
$hasTicketFilters = ($dateRange !== '' ||
                     ($serviceThru !== '' && strtolower($serviceThru) !== 'all') ||
                     ($serviceType !== '' && strtolower($serviceType) !== 'all'));

// ----------------------
// Main query - include pincode and area columns
// ----------------------
$sql = "
SELECT
    c.id AS customer_id,
    c.customer_uniq_code,
    c.company_nm,
    c.pincode,
    c.area,
    COALESCE(c.service_type, '') AS services,
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

// ----------------------
// Customer-level filters (pincode & service_type)
$customerParams = [];
$customerTypes  = '';

// Pincode filter (customer level) - partial match for quick search.
// For exact match change LIKE to '=' and remove % wrappers below.
if ($pincode !== '') {
    $sql .= " AND c.pincode LIKE ?";
    $customerParams[] = "%$pincode%";
    $customerTypes .= 's';
}

// Service type filter at customer level (optional)
if ($serviceType !== '' && strtolower($serviceType) !== 'all') {
    // Keep this to filter customers who have that service type set
    $sql .= " AND c.service_type LIKE ?";
    $customerParams[] = "%$serviceType%";
    $customerTypes .= 's';
}

$sql .= " ORDER BY c.company_nm ASC";

// ----------------------
// Bind params (ticket params first as they are used in the subquery)
$allParams = array_merge($ticketParams, $customerParams);
$allTypes  = $ticketTypes . $customerTypes;

// ----------------------
// Prepare and execute
$db->prepareStatement($sql);
if (!empty($allParams)) {
    $db->setParameters($allParams, $allTypes);
}
$resp = $db->execPreparedStatement();

// ----------------------
// Process results and respond JSON
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

            // Counts
            $totalCount = (int)($row['total_count'] ?? 0);
            $filteredCount = (int)($row['filtered_count'] ?? 0);

            // Label for total services consumed column
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

            $pincodeOut = htmlspecialchars($row['pincode'] ?? '');
            $areaOut = htmlspecialchars($row['area'] ?? '');

            // Arrange data in the desired order:
            // 1. S.No, 2. Company Name, 3. Service Consumed, 4. Total Services Consumed, 
            // 5. Last Service Date, 6. Days Since Last Service, 7. Pincode, 8. Area
            $resultData[] = [
                $i,                          // S.No
                $customerBtn,                // Company Name
                
                $totalServices,              // Total Services Consumed
                $lastServiceDate,            // Last Service Date
                $daysSince,                  // Days Since Last Service
                $services,                   // Service Consumed
                $pincodeOut ,                // Pincode
                 $areaOut                     // Area
               
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
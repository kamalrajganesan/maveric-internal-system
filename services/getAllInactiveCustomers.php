<?php
// services/getAllInactiveCustomers.php
require_once("../shared/actions/db/dao.php");
$db = new sqlHelper();

if (!session_id()) session_start();

// Handle area dropdown request
if (isset($_POST['getAreas']) && $_POST['getAreas'] === 'true') {
    $areaSql = "SELECT DISTINCT TRIM(area) as area
                FROM cust_mstr 
                WHERE is_deleted = 0 
                AND area IS NOT NULL 
                AND TRIM(area) != '' 
                ORDER BY area ASC";
    
    $db->prepareStatement($areaSql);
    $areaResp = $db->execPreparedStatement();
    
    $areas = [];
    
    if ($areaResp['success']) {
        $rs = $db->getResultSet();
        if ($rs->num_rows > 0) {
            while ($row = $rs->fetch_assoc()) {
                $area = trim($row['area']);
                if ($area !== '') {
                    $areas[] = $area;
                }
            }
        }
    }
    
    // NEW: Extract base area name (first word/phrase before comma or special chars)
    $uniqueAreas = [];
    $seenBaseNames = [];
    
    foreach ($areas as $area) {
        // Extract the primary area name (everything before comma, dash with space, or just trim)
        $baseName = $area;
        
        // Split by comma and take first part
        if (strpos($baseName, ',') !== false) {
            $baseName = explode(',', $baseName)[0];
        }
        
        // Split by " - " and take first part
        if (strpos($baseName, ' - ') !== false) {
            $baseName = explode(' - ', $baseName)[0];
        }
        
        // Remove trailing punctuation and trim
        $baseName = rtrim(trim($baseName), '.,;:-');
        $baseNameLower = strtolower($baseName);
        
        // Only add if we haven't seen this base name before
        if ($baseName !== '' && !in_array($baseNameLower, $seenBaseNames)) {
            $uniqueAreas[] = strtoupper($baseName); // Normalize to uppercase
            $seenBaseNames[] = $baseNameLower;
        }
    }
    
    // Sort alphabetically
    sort($uniqueAreas);
    
    echo json_encode(['success' => true, 'data' => $uniqueAreas]);
    exit;
}

// --- Get filters from POST ---
$dateRange   = trim($_POST['dateRange'] ?? ''); // format: "DD/MM/YYYY to DD/MM/YYYY" from flatpickr range
$pincode     = trim($_POST['pincode'] ?? '');
$area        = trim($_POST['area'] ?? '');
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
    $ticketFilterSql .= " AND tk.service_typ LIKE ?";
    $ticketParams[] = "%$serviceType%";
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
    c.contact,
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
// Customer-level filters (pincode, area & service_type)
$customerParams = [];
$customerTypes  = '';

// Pincode filter (customer level) - partial match for quick search.
// For exact match change LIKE to '=' and remove % wrappers below.
if ($pincode !== '') {
    $sql .= " AND c.pincode LIKE ?";
    $customerParams[] = "%$pincode%";
    $customerTypes .= 's';
}

// MODIFIED: Area filter (customer level) - PATTERN MATCH to catch all variations
if ($area !== '' && strtolower($area) !== 'all') {
    // This will match: AMBATTUR, AMBATTUR., AMBATTUR,, AMBATTUR INDUSTRIAL ESTATE, etc.
    $sql .= " AND (c.area LIKE ? OR c.area LIKE ?)";
    $customerParams[] = $area; // Exact match
    $customerParams[] = $area . "%"; // Starts with (covers punctuation and extensions)
    $customerTypes .= 'ss';
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
            $contactNo = htmlspecialchars($row['contact'] ?? '');

            // Arrange data in the desired order:
            // 1. S.No, 2. Company Name, 3. Contact 4. Service Consumed, 5. Total Services Consumed, 
            // 6. Last Service Date, 7. Days Since Last Service, 8. Pincode, 9. Area
            $resultData[] = [
                $i,                          // S.No
                $customerBtn,                // Company Name
                $contactNo,                  // Contact
                $totalServices,              // Total Services Consumed
                $lastServiceDate,            // Last Service Date
                $daysSince,                  // Days Since Last Service
                $services,                   // Service Consumed
                $pincodeOut,                 // Pincode
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
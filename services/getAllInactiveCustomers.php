<?php
require_once("../shared/actions/db/dao.php");
$db = new sqlHelper();

if (!session_id()) session_start();

// Get filter parameters from POST request
$dateRange    = $_POST['dateRange'] ?? '';
$serviceType  = $_POST['serviceType'] ?? '';
$singleDate   = $_POST['singleDate'] ?? '';
$pincode      = $_POST['pincode'] ?? '';

// Base SQL query
$sql = "SELECT c.id,
        c.customer_uniq_code,
        c.customer_nm,
        c.company_nm,
        c.service_type,
        c.contact,
        c.email,
        c.city,
        c.area,
        c.pincode,
        c.created_on,
        MAX(t.created_on) AS last_service_date
    FROM cust_mstr c
    LEFT JOIN ticket t ON c.id = t.customer_id
    WHERE c.is_active = 1 AND c.is_deleted = 0";

// Build WHERE conditions dynamically
$additionalConditions = [];

if (!empty($serviceType)) {
    $additionalConditions[] = "c.service_type LIKE '%$serviceType%'";
}

// Prefer singleDate over dateRange
if (!empty($singleDate)) {
    $parts = explode('/', $singleDate);
    if (count($parts) === 3) {
        $formattedDate = $parts[2] . '-' . str_pad($parts[1], 2, '0', STR_PAD_LEFT) . '-' . str_pad($parts[0], 2, '0', STR_PAD_LEFT);
        $additionalConditions[] = "DATE(c.created_on) = '$formattedDate'";
    }
} elseif (!empty($dateRange) && strpos($dateRange, ' to ') !== false) {
    $dates = explode(' to ', $dateRange);
    if (count($dates) === 2) {
        $start = explode('/', trim($dates[0]));
        $end   = explode('/', trim($dates[1]));
        if (count($start) === 3 && count($end) === 3) {
            $startDate = $start[2] . '-' . str_pad($start[1], 2, '0', STR_PAD_LEFT) . '-' . str_pad($start[0], 2, '0', STR_PAD_LEFT);
            $endDate   = $end[2] . '-' . str_pad($end[1], 2, '0', STR_PAD_LEFT) . '-' . str_pad($end[0], 2, '0', STR_PAD_LEFT);
            $additionalConditions[] = "DATE(c.created_on) BETWEEN '$startDate' AND '$endDate'";
        }
    }
}

if (!empty($pincode)) {
    $additionalConditions[] = "c.pincode LIKE '%$pincode%'";
}

if (!empty($additionalConditions)) {
    $sql .= " AND " . implode(" AND ", $additionalConditions);
}

// Group and filter inactive customers
$sql .= " GROUP BY c.id, c.customer_nm, c.company_nm, c.contact, c.email, c.city, c.area, c.pincode
          HAVING (last_service_date IS NULL OR last_service_date < NOW() - INTERVAL 1 MONTH)";

// Execute query
$db->prepareStatement($sql);
$db->execPreparedStatement();
$result = $db->getResultSet();

$data = [];
if ($result->num_rows > 0) {
    $i = 1;
    while ($row = $result->fetch_assoc()) {
        // Last serviced date display
        if ($row['last_service_date']) {
            $cDate = new DateTime($row['last_service_date']);
            $daysAgo = (new DateTime())->diff($cDate)->days;
            $lastServiced = '<div style="text-align:right;">
                                <span>' . date('d-m-Y h:i A', strtotime($row['last_service_date'])) . '</span><br>
                                <small>' . $daysAgo . ' days ago</small>
                              </div>';
        } else {
            $lastServiced = '<div style="text-align:right;">
                                <span>No service record</span><br>
                                <small>Never serviced</small>
                             </div>';
        }

        // Make Customer Name clickable
        $customerNameBtn = '<button type="button" class="btn btn-link p-0 view-btn" 
                                data-bs-toggle="modal" data-bs-target="#viewCustomerModal" 
                                data-customer="' . $row['customer_uniq_code'] . '">'
                                . htmlspecialchars($row['customer_nm']) .
                            '</button>';

        // View button
        $btn = '<div class="btn-group">
                    <button type="button" class="btn btn-inverse-primary btn-fw view-btn" 
                        data-bs-toggle="modal" data-bs-target="#viewCustomerModal" 
                        data-customer="' . $row['customer_uniq_code'] . '">
                        <i class="fa fa-ellipsis-v"></i>
                    </button>
                </div>';

        // S. No. first, Customer Name second
        $data[] = [
            $i,               // S. No.
            $customerNameBtn, // Customer Name
            $row['company_nm'],
            $row['contact'],
            $row['service_type'],
            $row['pincode'],
            $lastServiced,
            $btn
        ];
        $i++;
    }
    echo json_encode(["success" => true, "data" => $data]);
} else {
    echo json_encode(["success" => false, "data" => [], "message" => "No data found"]);
}
?>

<?php
// services/expiring_services_fetch.php
require_once("../shared/actions/db/dao.php");
$db = new sqlHelper();
if (!session_id()) session_start();

$dateRange       = isset($_POST['dateRange']) ? trim($_POST['dateRange']) : '';
$selectedService = isset($_POST['service']) ? trim($_POST['service']) : '';
$today           = date('Y-m-d');

$sql = "
    SELECT
        c.customer_uniq_code,
        c.company_nm,
        a.agent_nm AS agent_name,
        c.contact,
        COALESCE(c.service_type, '') AS services_offered,
        c.amc_end_date,
        c.tally_end_date,
        c.cloud_end_date
    FROM cust_mstr c
    LEFT JOIN agent a ON a.id = c.created_by
    WHERE c.is_active = 1 AND c.is_deleted = 0
";

$params = [];
$types  = '';

// SERVICE FILTER — "One Time" is completely ignored
if (!empty($selectedService) && $selectedService !== '' && $selectedService !== 'One Time') {
    $map = [
        'AMC'               => 'AMC',
        'Tally Subscription'=> 'Tally',
        'Cloud'             => 'Cloud'
        // 'One Time' is intentionally excluded
    ];
    $dbValue = $map[$selectedService] ?? $selectedService;
    $sql .= " AND c.service_type LIKE ?";
    $params[] = "%{$dbValue}%";
    $types .= 's';
}

// DATE RANGE PARSING
$hasDateRange = false;
$startDate = $endDate = null;

if (!empty($dateRange) && strpos($dateRange, ' to ') !== false) {
    $parts = explode(' to ', $dateRange);
    if (count($parts) === 2) {
        $from = trim($parts[0]);
        $to   = trim($parts[1]);
        $f = explode('/', $from);
        $t = explode('/', $to);
        if (count($f) === 3 && count($t) === 3) {
            $startDate = $f[2] . '-' . str_pad($f[1],2,'0',STR_PAD_LEFT) . '-' . str_pad($f[0],2,'0',STR_PAD_LEFT);
            $endDate   = $t[2] . '-' . str_pad($t[1],2,'0',STR_PAD_LEFT) . '-' . str_pad($t[0],2,'0',STR_PAD_LEFT);
            $hasDateRange = true;
        }
    }
}

// APPLY DATE FILTER
if ($hasDateRange) {
    if (!empty($selectedService) && $selectedService !== '' && $selectedService !== 'One Time') {
        // Single service selected (AMC / Tally / Cloud only)
        $field = '';
        if ($selectedService === 'AMC')               $field = 'c.amc_end_date';
        elseif ($selectedService === 'Tally Subscription') $field = 'c.tally_end_date';
        elseif ($selectedService === 'Cloud')         $field = 'c.cloud_end_date';

        if ($field) {
            $sql .= " AND $field >= ? AND $field <= ? AND $field != '0000-00-00' AND $field IS NOT NULL";
            $params[] = $startDate;
            $params[] = $endDate;
            $types .= 'ss';
        }
    } else {
        // ALL SERVICES + DATE RANGE — Only AMC, Tally, Cloud (One Time ignored)
        $sql .= " AND (
            (c.amc_end_date >= ? AND c.amc_end_date <= ? AND c.amc_end_date != '0000-00-00' AND c.amc_end_date IS NOT NULL) OR
            (c.tally_end_date >= ? AND c.tally_end_date <= ? AND c.tally_end_date != '0000-00-00' AND c.tally_end_date IS NOT NULL) OR
            (c.cloud_end_date >= ? AND c.cloud_end_date <= ? AND c.cloud_end_date != '0000-00-00' AND c.cloud_end_date IS NOT NULL)
        )";
        $params = array_merge($params, [$startDate,$endDate,$startDate,$endDate,$startDate,$endDate]);
        $types .= 'ssssss';
    }
} else {
    // NO DATE RANGE → Show only future AMC/Tally/Cloud (One Time never appears)
    $sql .= " AND (
        c.amc_end_date >= ? OR
        c.tally_end_date >= ? OR
        c.cloud_end_date >= ?
    )";
    $params[] = $today; $params[] = $today; $params[] = $today;
    $types .= 'sss';
}

$sql .= " ORDER BY c.company_nm ASC";

$db->prepareStatement($sql);
if (!empty($params)) $db->setParameters($params, $types);
$resp = $db->execPreparedStatement();

$resultData = [];
if ($resp['success']) {
    $rs = $db->getResultSet();
    $i = 1;
    while ($row = $rs->fetch_assoc()) {
        $companyName = htmlspecialchars($row['company_nm'] ?? 'N/A');
        $agentName   = htmlspecialchars($row['agent_name'] ?? 'N/A');
        $contact     = htmlspecialchars($row['contact'] ?? 'N/A');

        // Services Offered — "One Time" never shown
        $servicesOffered = 'No Service';
        if (!empty($selectedService) && $selectedService !== '' && $selectedService !== 'One Time') {
            $displayMap = [
                'AMC'               => 'AMC',
                'Tally Subscription'=> 'Tally Subscription',
                'Cloud'             => 'Cloud'
            ];
            $servicesOffered = htmlspecialchars($displayMap[$selectedService] ?? $selectedService);
        } else {
            $services = trim(str_replace(['"', '[', ']'], '', $row['services_offered'] ?? ''));
            // Remove "One Time" from display if present
            $services = str_replace(['One Time', ',One Time', 'One Time,'], '', $services);
            $services = trim(str_replace(',,', ',', $services), ', ');
            $servicesOffered = htmlspecialchars(empty($services) ? 'No Service' : $services);
        }

        // Expiry Date — Only AMC, Tally, Cloud considered
        $expiryDate = null;
        if ($hasDateRange) {
            $candidates = [];
            if (!empty($row['amc_end_date']) && $row['amc_end_date'] >= $startDate && $row['amc_end_date'] <= $endDate && $row['amc_end_date'] !== '0000-00-00')
                $candidates[] = $row['amc_end_date'];
            if (!empty($row['tally_end_date']) && $row['tally_end_date'] >= $startDate && $row['tally_end_date'] <= $endDate && $row['tally_end_date'] !== '0000-00-00')
                $candidates[] = $row['tally_end_date'];
            if (!empty($row['cloud_end_date']) && $row['cloud_end_date'] >= $startDate && $row['cloud_end_date'] <= $endDate && $row['cloud_end_date'] !== '0000-00-00')
                $candidates[] = $row['cloud_end_date'];
            if (!empty($candidates)) {
                sort($candidates);
                $expiryDate = $candidates[0];
            }
        } else {
            $dates = [];
            if (!empty($row['amc_end_date']) && $row['amc_end_date'] >= $today && $row['amc_end_date'] !== '0000-00-00') $dates[] = $row['amc_end_date'];
            if (!empty($row['tally_end_date']) && $row['tally_end_date'] >= $today && $row['tally_end_date'] !== '0000-00-00') $dates[] = $row['tally_end_date'];
            if (!empty($row['cloud_end_date']) && $row['cloud_end_date'] >= $today && $row['cloud_end_date'] !== '0000-00-00') $dates[] = $row['cloud_end_date'];
            if (!empty($dates)) {
                sort($dates);
                $expiryDate = $dates[0];
            }
        }

        $expiryDisplay = 'No Expiry';
        if ($expiryDate && $expiryDate !== '0000-00-00') {
            $diff = (new DateTime($today))->diff(new DateTime($expiryDate));
            $days = (int)$diff->format('%r%a');
            $text = $days > 0 ? "$days days left" : ($days == 0 ? "Expires Today" : abs($days)." days ago");
            $expiryDisplay = '<strong>'.date('d/m/Y', strtotime($expiryDate)).'</strong><br><small class="text-muted">'.$text.'</small>';
        }

        $actionBtn = '<button type="button" class="btn-view-perfect view-btn"
                         data-bs-toggle="modal" data-bs-target="#viewCustomerModal"
                         data-customer="'.htmlspecialchars($row['customer_uniq_code']).'">
                         <i class="fa fa-eye"></i> View
                      </button>';

        $resultData[] = [$i++, $companyName, $agentName, $contact, $servicesOffered, $expiryDisplay, $actionBtn];
    }
}

echo json_encode(['success' => true, 'data' => $resultData]);
?>
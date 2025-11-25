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
        $field = '';
        if ($selectedService === 'AMC') $field = 'c.amc_end_date';
        elseif ($selectedService === 'Tally Subscription') $field = 'c.tally_end_date';
        elseif ($selectedService === 'Cloud') $field = 'c.cloud_end_date';

        if ($field) {
            $sql .= " AND $field >= ? AND $field <= ? AND $field != '0000-00-00' AND $field IS NOT NULL";
            $params[] = $startDate;
            $params[] = $endDate;
            $types .= 'ss';
        }
    } else {
        $sql .= " AND (
            (c.amc_end_date >= ? AND c.amc_end_date <= ? AND c.amc_end_date != '0000-00-00' AND c.amc_end_date IS NOT NULL) OR
            (c.tally_end_date >= ? AND c.tally_end_date <= ? AND c.tally_end_date != '0000-00-00' AND c.tally_end_date IS NOT NULL) OR
            (c.cloud_end_date >= ? AND c.cloud_end_date <= ? AND c.cloud_end_date != '0000-00-00' AND c.cloud_end_date IS NOT NULL)
        )";
        $params = array_merge($params, [$startDate,$endDate,$startDate,$endDate,$startDate,$endDate]);
        $types .= 'ssssss';
    }
} else {
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

        // Get raw services
        $rawServices = $row['services_offered'] ?? '';
        $rawServices = str_replace(['"', '[', ']'], '', $rawServices);
        $rawServices = trim($rawServices);

        // Determine expiring service
        $expiringService = '';
        $expiryDate = null;

        if (!empty($selectedService) && $selectedService !== '' && $selectedService !== 'One Time') {

            if ($selectedService === 'AMC') {
                $expiringService = 'AMC';
                if (!empty($row['amc_end_date']) && $row['amc_end_date'] !== '0000-00-00') {
                    $expiryDate = $row['amc_end_date'];
                }
            } elseif ($selectedService === 'Tally Subscription') {
                $expiringService = 'Tally';
                if (!empty($row['tally_end_date']) && $row['tally_end_date'] !== '0000-00-00') {
                    $expiryDate = $row['tally_end_date'];
                }
            } elseif ($selectedService === 'Cloud') {
                $expiringService = 'Cloud';
                if (!empty($row['cloud_end_date']) && $row['cloud_end_date'] !== '0000-00-00') {
                    $expiryDate = $row['cloud_end_date'];
                }
            }

        } else {

            if ($hasDateRange) {
                $candidates = [];
                if (!empty($row['amc_end_date']) && $row['amc_end_date'] >= $startDate && $row['amc_end_date'] <= $endDate) {
                    $candidates[] = ['date' => $row['amc_end_date'], 'service' => 'AMC'];
                }
                if (!empty($row['tally_end_date']) && $row['tally_end_date'] >= $startDate && $row['tally_end_date'] <= $endDate) {
                    $candidates[] = ['date' => $row['tally_end_date'], 'service' => 'Tally'];
                }
                if (!empty($row['cloud_end_date']) && $row['cloud_end_date'] >= $startDate && $row['cloud_end_date'] <= $endDate) {
                    $candidates[] = ['date' => $row['cloud_end_date'], 'service' => 'Cloud'];
                }
                if (!empty($candidates)) {
                    usort($candidates, fn($a,$b)=>strcmp($a['date'],$b['date']));
                    $expiryDate = $candidates[0]['date'];
                    $expiringService = $candidates[0]['service'];
                }
            } else {
                $dates = [];
                if (!empty($row['amc_end_date']) && $row['amc_end_date'] >= $today) {
                    $dates[] = ['date' => $row['amc_end_date'], 'service' => 'AMC'];
                }
                if (!empty($row['tally_end_date']) && $row['tally_end_date'] >= $today) {
                    $dates[] = ['date' => $row['tally_end_date'], 'service' => 'Tally'];
                }
                if (!empty($row['cloud_end_date']) && $row['cloud_end_date'] >= $today) {
                    $dates[] = ['date' => $row['cloud_end_date'], 'service' => 'Cloud'];
                }
                if (!empty($dates)) {
                    usort($dates, fn($a,$b)=>strcmp($a['date'],$b['date']));
                    $expiryDate = $dates[0]['date'];
                    $expiringService = $dates[0]['service'];
                }
            }
        }

        // DISPLAY ALL SERVICES — WITH PROPER HIGHLIGHTING
        $servicesOffered = 'No Service';
        if (!empty($rawServices)) {
            $serviceArray = explode(',', $rawServices);
            $displayServices = [];

            foreach ($serviceArray as $svc) {
                $svc = trim($svc);
                if (empty($svc)) continue;
                if ($svc === 'One Time') continue;

                // NORMALIZE names for comparison
                $normalized = $svc;
                if ($svc === 'Tally Subscription') $normalized = 'Tally';

                // Should highlight?
                $shouldHighlight = ($normalized === $expiringService);

                // Display name mapping
                if ($normalized === 'Tally') {
                    $displayName = 'Tally Subscription';
                } else {
                    $displayName = htmlspecialchars($svc);
                }

                // Apply highlight
                if ($shouldHighlight) {
                    $displayServices[] = "<strong>$displayName</strong>";
                } else {
                    $displayServices[] = $displayName;
                }
            }

            if (!empty($displayServices)) {
                $servicesOffered = implode(', ', $displayServices);
            }
        }

        // EXPIRY DISPLAY
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

        $resultData[] = [
            $i++,
            $companyName,
            $contact,
            $servicesOffered,
            $expiryDisplay,
            $agentName,
            $actionBtn
        ];
    }
}

echo json_encode(['success' => true, 'data' => $resultData]);
?>

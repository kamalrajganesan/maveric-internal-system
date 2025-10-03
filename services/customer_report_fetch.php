<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'C:/xampp/logs/php_errors.log');
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');

require_once("../shared/actions/db/dao.php");

try {
    $db = new sqlHelper();
    $conn = $db->getConnection();
    if (!$conn) throw new Exception("Failed to connect to DB.");

    if (!session_id()) session_start();

    $response = [
        "draw" => isset($_POST['draw']) ? (int)$_POST['draw'] : 0,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "success" => false,
        "data" => [],
        "message" => ""
    ];

    function dbEscape($value, $conn) {
        return mysqli_real_escape_string($conn, $value);
    }

    function parseDate($dateStr) {
        if (empty($dateStr)) return '';
        $formats = ['Y-m-d','d/m/Y','d-m-Y'];
        foreach ($formats as $fmt) {
            $date = DateTime::createFromFormat($fmt, $dateStr);
            if ($date) return $date->format('Y-m-d');
        }
        return '';
    }

    $transac_range       = $_POST['transac_range'] ?? '';
    $transac_single_date = $_POST['transac_single_date'] ?? '';
    $serviceType         = $_POST['serviceType'] ?? '';
    $search_value        = $_POST['search']['value'] ?? '';
    $start               = isset($_POST['start']) ? (int)$_POST['start'] : 0;
    $length              = isset($_POST['length']) ? (int)$_POST['length'] : 10;

    $customerIdForPopup = intval($_POST['customerIdForPopup'] ?? 0);

    // --- Popup fetch ---
    if ($customerIdForPopup > 0) {
        $custIdEsc = intval($customerIdForPopup);
        $custRes = $conn->query("SELECT customer_nm, company_nm, contact 
                                 FROM cust_mstr 
                                 WHERE id=$custIdEsc AND is_deleted=0 LIMIT 1");
        $customer = $custRes ? $custRes->fetch_assoc() : null;

        if (!$customer) {
            echo json_encode(["success" => false, "message" => "Customer not found"]);
            exit;
        }

        $ticketSql = "
            SELECT 
                id,
                created_on,
                service_thru,
                service_typ,
                comments,
                notes,
                problem_stmt,
                problem_desc
            FROM ticket
            WHERE customer_id = $custIdEsc
              AND is_deleted = 0
            ORDER BY created_on DESC, id DESC
        ";
        $tRes = $conn->query($ticketSql);
        $transactions = [];

        if ($tRes) {
            while ($r = $tRes->fetch_assoc()) {
                $serviceUsed = trim($r['service_thru'] ?? $r['service_typ'] ?? "Not Specified");

           // --- Comment Parsing ---
// --- Comment Parsing ---
$comment = '-';
if (!empty($r['comments'])) {
    $decoded = json_decode($r['comments'], true);

    if (json_last_error() === JSON_ERROR_NONE) {
        // Case 1: Array of comment objects
        if (is_array($decoded) && isset($decoded[0])) {
            $messages = [];
            foreach ($decoded as $entry) {
                if (is_array($entry) && isset($entry['message'])) {
                    $messages[] = trim($entry['message']);
                }
            }
            $comment = !empty($messages) ? implode(" | ", $messages) : '-';
        }
        // Case 2: Single object
        elseif (isset($decoded['message']) || isset($decoded['msg'])) {
            $comment = trim($decoded['message'] ?? $decoded['msg']);
        }
    } else {
        // Case 3: Stored as JSON string but not valid array
        // Try to clean and extract text between "message":"..."
        if (preg_match_all('/"message"\s*:\s*"([^"]+)"/', $r['comments'], $matches)) {
            $comment = implode(" | ", array_map('trim', $matches[1]));
        } else {
            // Otherwise plain text
            $comment = trim($r['comments']);
        }
    }
} elseif (!empty($r['notes'])) {
    $comment = trim($r['notes']);
} elseif (!empty($r['problem_stmt'])) {
    $comment = trim($r['problem_stmt']);
} elseif (!empty($r['problem_desc'])) {
    $comment = trim($r['problem_desc']);
}




                $transactions[] = [
                    'ticket_id'    => (int)$r['id'],
                    'created_on'   => !empty($r['created_on']) ? date("d-m-Y", strtotime($r['created_on'])) : '',
                    'service_type' => $serviceUsed,
                    'comments'     => $comment
                ];
            }
        }

        echo json_encode([
            "success" => true,
            "popupCustomer" => [
                "customer_name"   => !empty($customer['company_nm']) ? $customer['company_nm'] : $customer['customer_nm'],
                "contact_number"  => $customer['contact'] ?? '',
                "transactions"    => $transactions
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // --- Normal report fetch ---
    $where_customer = ["c.is_deleted=0"];
    $where_ticket = ["t.is_deleted=0"];

    if (!empty($transac_range)) {
        $dates = explode(" to ", $transac_range);
        if (count($dates) == 2) {
            $from = parseDate(trim($dates[0]));
            $to   = parseDate(trim($dates[1]));
            if ($from && $to) $where_ticket[] = "DATE(t.created_on) BETWEEN '".dbEscape($from, $conn)."' AND '".dbEscape($to, $conn)."'";
        }
    }

    if (!empty($transac_single_date)) {
        $singleDate = parseDate($transac_single_date);
        if ($singleDate) $where_ticket[] = "DATE(t.created_on)='".dbEscape($singleDate, $conn)."'";
    }

    if (!empty($serviceType)) {
        $serviceTypeEscaped = dbEscape($serviceType, $conn);
        $where_ticket[] = "(t.service_typ='$serviceTypeEscaped' OR t.service_thru='$serviceTypeEscaped')";
    }

    if (!empty($search_value)) {
        $search_value = dbEscape($search_value, $conn);
        $where_customer[] = "(c.customer_nm LIKE '%$search_value%' OR c.company_nm LIKE '%$search_value%' OR c.contact LIKE '%$search_value%')";
    }

    $customerWhereStr = implode(" AND ", $where_customer);
    $ticketWhereStr   = implode(" AND ", $where_ticket);

    $sql = "SELECT 
        c.id AS customer_id,
        c.customer_nm,
        c.company_nm,
        c.contact,
        MAX(a.agent_nm) AS contact_person,
        MAX(t.created_on) AS last_serviced_date,
        COUNT(t.id) AS total_trans,
        SUM(CASE WHEN t.service_thru='Phone Call' THEN 1 ELSE 0 END) AS phone_call_count,
        SUM(CASE WHEN t.service_thru='Remote' THEN 1 ELSE 0 END) AS remote_count,
        SUM(CASE WHEN t.service_thru='Physical Visit' THEN 1 ELSE 0 END) AS physical_visit_count
    FROM cust_mstr c
    LEFT JOIN ticket t ON t.customer_id=c.id AND $ticketWhereStr
    LEFT JOIN agent a ON a.id=t.assignd_agent_id AND a.is_deleted=0
    WHERE $customerWhereStr
    GROUP BY c.id, c.customer_nm, c.company_nm, c.contact
    ORDER BY last_serviced_date DESC
    LIMIT $start, $length";

    $result = $conn->query($sql);
    if ($result === false) throw new Exception($conn->error);

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $transactions_text = $row['total_trans'] . " transactions";
        if (!empty($serviceType)) {
            if ($serviceType == "Phone Call") $transactions_text = $row['phone_call_count'] . " Phone Call";
            if ($serviceType == "Remote") $transactions_text = $row['remote_count'] . " Remote";
            if ($serviceType == "Physical Visit") $transactions_text = $row['physical_visit_count'] . " Physical Visit";
        }

        $data[] = [
            "id" => $row['customer_id'],
            "customer_name" => !empty($row['company_nm']) ? $row['company_nm'] : $row['customer_nm'],
            "contact_number" => $row['contact'] ?? '',
            "transactions_count" => $transactions_text,
            "contact_person" => $row['contact_person'] ?? 'N/A',
            "last_serviced_date" => !empty($row['last_serviced_date']) ? date("d-m-Y H:i", strtotime($row['last_serviced_date'])) : "Never"
        ];
    }

    $total_result = $conn->query("SELECT COUNT(*) AS total FROM cust_mstr WHERE is_deleted=0");
    $response['recordsTotal'] = $total_result->fetch_assoc()['total'];

    $filtered_result = $conn->query("SELECT COUNT(DISTINCT c.id) AS filtered 
                                     FROM cust_mstr c
                                     LEFT JOIN ticket t ON t.customer_id=c.id AND $ticketWhereStr
                                     WHERE $customerWhereStr");
    $response['recordsFiltered'] = $filtered_result->fetch_assoc()['filtered'];

    $response['success'] = true;
    $response['data'] = $data;

} catch (Exception $e) {
    $response['message'] = "Database error: " . $e->getMessage();
    error_log("Customer Report Error: " . $e->getMessage());
    http_response_code(500);
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;

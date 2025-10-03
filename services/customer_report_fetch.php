<?php
// Error handling (log errors, do not display in JSON)
ini_set('display_errors', 0); // Disable error display for production
ini_set('log_errors', 1);
ini_set('error_log', 'C:/xampp/logs/php_errors.log'); // Adjust to your error log path
error_reporting(E_ALL);
header('Content-Type: application/json; chars et=utf-8');

require_once("../shared/actions/db/dao.php");

try {
    // Initialize database connection
    $db = new sqlHelper();
    $conn = $db->getConnection(); // Ensure this returns a valid mysqli object
    if (!$conn) {
        throw new Exception("Failed to establish database connection.");
    }

    if (!session_id()) session_start();

    // Initialize response
    $response = [
        "draw" => isset($_POST['draw']) ? (int)$_POST['draw'] : 0,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "success" => false,
        "data" => [],
        "message" => ""
    ];

    // Escape function
    function dbEscape($value, $conn) {
        return mysqli_real_escape_string($conn, $value);
    }

    // POST filters
    $transac_range = $_POST['transac_range'] ?? '';
    $transac_single_date = $_POST['transac_single_date'] ?? '';
    $serviceType = $_POST['serviceType'] ?? '';
    $search_value = $_POST['search']['value'] ?? ''; // DataTables search
    $start = isset($_POST['start']) ? (int)$_POST['start'] : 0;
    $length = isset($_POST['length']) ? (int)$_POST['length'] : 10;

    $where = ["c.is_deleted = 0", "t.is_deleted = 0"];

    // Date range filter
    if (!empty($transac_range)) {
        $dates = explode(" to ", $transac_range);
        if (count($dates) === 2) {
            $from = date('Y-m-d', strtotime(trim($dates[0])));
            $to = date('Y-m-d', strtotime(trim($dates[1])));
            $where[] = "DATE(t.created_on) BETWEEN '" . dbEscape($from, $conn) . "' AND '" . dbEscape($to, $conn) . "'";
        }
    }

    // Single date filter
    if (!empty($transac_single_date)) {
        $singleDate = date('Y-m-d', strtotime($transac_single_date));
        $where[] = "DATE(t.created_on) = '" . dbEscape($singleDate, $conn) . "'";
    }

    // Service type filter
    if (!empty($serviceType)) {
        $where[] = "t.service_typ = '" . dbEscape($serviceType, $conn) . "'";
    }

    // Search filter for DataTables
    if (!empty($search_value)) {
        $search_value = dbEscape($search_value, $conn);
        $where[] = "(c.customer_nm LIKE '%$search_value%' OR c.company_nm LIKE '%$search_value%' OR c.contact LIKE '%$search_value%' OR a.agent_nm LIKE '%$search_value%')";
    }

    // Base SQL query
    $sql = "
        SELECT 
            c.id AS customer_id,
            c.customer_nm,
            c.company_nm,
            c.contact,
            COUNT(t.id) AS transactions_count,
            MAX(t.created_on) AS last_serviced_date,
            a.agent_nm AS contact_person
        FROM cust_mstr c
        LEFT JOIN ticket t ON t.customer_id = c.id AND t.is_deleted = 0
        LEFT JOIN agent a ON a.id = t.assignd_agent_id AND a.is_deleted = 0
    ";

    // Add WHERE clause
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    // Group and order
    $sql .= " GROUP BY c.id, c.customer_nm, c.company_nm, c.contact, a.agent_nm";
    $sql .= " ORDER BY last_serviced_date DESC";

    // Get total records (before filtering)
    $total_sql = "SELECT COUNT(DISTINCT c.id) AS total FROM cust_mstr c";
    $total_result = $conn->query($total_sql);
    if ($total_result === false) {
        throw new Exception("Failed to fetch total records: " . $conn->error);
    }
    $response['recordsTotal'] = $total_result->fetch_assoc()['total'];

    // Get filtered records count
    $filtered_sql = "SELECT COUNT(DISTINCT c.id) AS filtered FROM cust_mstr c
        LEFT JOIN ticket t ON t.customer_id = c.id AND t.is_deleted = 0
        LEFT JOIN agent a ON a.id = t.assignd_agent_id AND a.is_deleted = 0";
    if (!empty($where)) {
        $filtered_sql .= " WHERE " . implode(" AND ", $where);
    }
    $filtered_result = $conn->query($filtered_sql);
    if ($filtered_result === false) {
        throw new Exception("Failed to fetch filtered records: " . $conn->error);
    }
    $response['recordsFiltered'] = $filtered_result->fetch_assoc()['filtered'];

    // Add pagination
    $sql .= " LIMIT $start, $length";

    // Execute query
    // Try executeQuery() first, fall back to direct mysqli query if needed
    if (method_exists($db, 'executeQuery')) {
        $rows = $db->executeQuery($sql);
    } else {
        $result = $conn->query($sql);
        if ($result === false) {
            throw new Exception("Query execution failed: " . $conn->error);
        }
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }

    // Prepare data for DataTables
    $data = [];
    foreach ($rows as $row) {
        $data[] = [
            "id" => $row['customer_id'],
            "customer_name" => !empty($row['company_nm']) ? $row['company_nm'] : $row['customer_nm'],
            "contact_number" => $row['contact'] ?? '',
            "transactions_count" => $row['transactions_count'] ?? 0,
            "contact_person" => $row['contact_person'] ?? 'N/A',
            "last_serviced_date" => !empty($row['last_serviced_date'])
                ? date("d-m-Y H:i", strtotime($row['last_serviced_date']))
                : "Never"
        ];
    }

    $response['success'] = true;
    $response['data'] = $data;

} catch (Exception $e) {
    $response['message'] = "Database error: " . $e->getMessage();
    error_log("Customer Report Error: " . $e->getMessage() . " in " . __FILE__ . " on line " . __LINE__);
    http_response_code(500); // Ensure 500 status is returned
}

// Return JSON
echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
?>
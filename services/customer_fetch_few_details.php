<?php

require_once("../shared/actions/db/dao.php");

$db = new sqlHelper();

// Fetch customer details sql
$sql = "SELECT id, customer_nm, company_nm FROM cust_mstr WHERE is_deleted = 0";
$db->prepareStatement($sql);
$db->execPreparedStatement();
$result = $db->getResultSet();

$customers = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $customer['name'] = $row['company_nm'];
        $customer['id'] = $row['id'];
        $customers[] = $customer;
    }
} else {
    echo "no results";
}

$response = array(
    'success' => true,
    'data' => $customers,
    'message' => 'Customer details fetched successfully'
);

echo json_encode($response);
?>
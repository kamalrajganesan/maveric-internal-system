<?php

require_once("../shared/actions/db/dao.php");

if(isset($_POST['customerId'])) {
    
    $db = new sqlHelper();
    $customerFetchSingleSQL = "SELECT c.*, (SELECT agent_nm FROM agent a WHERE a.id = c.updated_by) AS updated_by, (SELECT agent_nm FROM agent a WHERE a.id = c.created_by) AS created_by FROM cust_mstr c WHERE is_deleted = 0 and customer_uniq_code = ?";

    $db->prepareStatement($customerFetchSingleSQL);
    $db->setParameters([$_POST['customerId']], 's');
    $db->execPreparedStatement();
    $custFetchSingleSQLResultSet = $db->getResultSet();

    if ($custFetchSingleSQLResultSet->num_rows > 0) {
        $data = array();
        while ($row = $custFetchSingleSQLResultSet->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode(array("success" => true, "data" => $data, "message" => "Data found."));
    } else {
        echo json_encode(array("success" => false, "data" => [], "message" => "No data found."));
    }
}

?>
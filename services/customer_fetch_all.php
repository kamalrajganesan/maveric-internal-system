<?php

require_once("../shared/actions/db/dao.php");

$db = new sqlHelper();
$customerFetchAllSQL = "SELECT * FROM cust_mstr WHERE is_deleted = 0";

$db->prepareStatement($customerFetchAllSQL);
$db->execPreparedStatement();
$custFetchAllSQLResultSet = $db->getResultSet();

if ($custFetchAllSQLResultSet->num_rows > 0) {
    $data = array();
    while ($row = $custFetchAllSQLResultSet->fetch_assoc()) {

        $btn = '
        <div class="btn-group">
            <button type="button" class="btn btn-inverse-primary btn-fw" data-toggle="modal" data-target="#viewCustomerModal" id="viewCustomerModalBtn" onclick="viewCustomer(' . $row['customer_uniq_code'] . ')">
                <i class="fa fa-2x fa-ellipsis-v"></i>
            </button>
            <button type="button" class="btn btn-inverse-secondary btn-fw" data-toggle="modal" data-target="#editCustomerModal" id="editCustomerModalBtn" onclick="editCustomer(' . $row['customer_uniq_code'] . ')">
                <i class="fa fa-2x fa-pencil-square-o"></i>
            </button>
            <button type="button" class="btn btn-inverse-dark btn-fw" data-toggle="modal" data-target="#removeCustomerModal" id="removeCustomerModalBtn" onclick="removeCustomer(' . $row['customer_uniq_code'] . ')">
                <i class="fa fa-2x fa-trash-o"></i>
            </button>
        </div>
        ';

        $link = '<a href="single-customer.php?customer_uniq_code=' . $row['customer_uniq_code'] . '">' . $row['customer_uniq_code'] . '</a>';

        $data[] = array(
            $link,
            $row['customer_nm'],
            $row['company_nm'],
            $row['city'] . " / " . $row['pincode'],
            $row['contact'],
            $row['service_type'],
            $row['email'],
            $btn
        );

        // $data[] = $row;
    }
    echo json_encode(array("success" => true, "data" => $data, "message" => "Data found."));
} else {
    echo json_encode(array("success" => false, "data" => [], "message" => "No data found."));
}

?>
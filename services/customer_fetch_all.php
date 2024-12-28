<?php

require_once("../shared/actions/db/dao.php");

$page = "";
if (isset($_POST['param'])) {
    $page = htmlspecialchars($_POST['param']);
} else {
    echo "param not found";
}


$db = new sqlHelper();
$fetchAllSQL = "SELECT * FROM cust_mstr WHERE is_deleted = 0";


switch ($page) {

    case 'All':
        $fetchAllSQL .= " ;";
        break;
    case 'Tally':
        $fetchAllSQL .= " and service_type Like '%Tally%';";
        break;
    case 'AMC':
        $fetchAllSQL .= " and service_type Like '%AMC%';";
        break;
    case 'OnCall':
        $fetchAllSQL .= " and service_type Like '%On Call%';";
        break;
    case 'OneTime':
        $fetchAllSQL .= " and service_type Like '%One Time%';";
        break;
    case 'Active':
        $fetchAllSQL .= " and is_active = true;";
        break;
    case 'InActive':
        $fetchAllSQL .= " and is_active = false;";
        break;
    case 'Endangered':
        $fetchAllSQL .= " and is_active = false;";
        break;
    default:
        echo "Invalid Parm...";
        break;
}

$db->prepareStatement($fetchAllSQL);
$db->execPreparedStatement();
$custFetchAllSQLResultSet = $db->getResultSet();

if ($custFetchAllSQLResultSet->num_rows > 0) {
    $data = array();
    while ($row = $custFetchAllSQLResultSet->fetch_assoc()) {

        $btn = '
        <div class="btn-group">
            <button type="button" class="btn btn-inverse-primary btn-fw" data-toggle="modal" data-target="#viewCustomerModal" id="viewCustomerModalBtn" onclick="viewCustomer(\'' . $row['customer_uniq_code'] . '\')">
                <i class="fa fa-2x fa-ellipsis-v"></i>
            </button>
            <button type="button" class="btn btn-inverse-secondary btn-fw" data-toggle="modal" data-target="#editCustomerModal" id="editCustomerModalBtn" onclick="editCustomer(\'' . $row['customer_uniq_code'] . '\')">
                <i class="fa fa-2x fa-pencil-square-o"></i>
            </button>
            <button type="button" class="btn btn-inverse-dark btn-fw" data-toggle="modal" data-target="#removeCustomerModal" id="removeCustomerModalBtn" onclick="removeCustomer(\'' . $row['customer_uniq_code'] . '\')">
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

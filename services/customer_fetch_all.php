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
    case 'AMC':
        $fetchAllSQL .= " and service_type Like '%AMC%';";
        break;
    case 'Tally':
        $fetchAllSQL .= " and service_type Like '%Tally%';";
        break;
    case 'Cloud':
        $fetchAllSQL .= " and service_type Like '%Cloud%';";
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
    $siVar = 1;
    while ($row = $custFetchAllSQLResultSet->fetch_assoc()) {
        
        $btn = '
            <div class="btn-group">
                <button type="button" class="btn btn-inverse-primary btn-fw" data-toggle="modal" data-target="#viewCustomerModal" id="viewCustomerModalBtn" onclick="viewCustomer(\'' . $row['customer_uniq_code'] . '\')">
                    <i class="fa fa-ellipsis-v"></i>
                </button>
                <button type="button" class="btn btn-inverse-secondary btn-fw" data-toggle="modal" data-target="#addTransactionModal" id="addTransactionModalBtn" onclick="addTransactionOfACustomer(\'' . $row['customer_uniq_code'] . '\')">
                    <i class="fa fa-th-list"></i>
                </button>
                <button type="button" class="btn btn-inverse-secondary btn-fw" data-toggle="modal" data-target="#editCustomerModal" id="editCustomerModalBtn" onclick="editCustomer(\'' . $row['customer_uniq_code'] . '\')">
                    <i class="fa fa-pencil-square-o"></i>
                </button>
                <button type="button" class="btn btn-inverse-dark btn-fw" data-toggle="modal" data-target="#removeCustomerModal" id="removeCustomerModalBtn" onclick="removeCustomer(\'' . $row['customer_uniq_code'] . '\')">
                    <i class="fa fa-trash-o"></i>
                </button>
            </div>
        ';
            $data[] = array(
                $siVar,
                $row['customer_uniq_code'],
                $row['company_nm'],
                $row['service_type'],
                $row['contact'],
                $row['city'] . " / " . $row['pincode'],
                $row['email'],
                $btn
            );
            $siVar++;
        // $data[] = $row;
    }
    echo json_encode(array("success" => true, "data" => $data, "message" => "Data found."));
} else {
    echo json_encode(array("success" => false, "data" => [], "message" => "No data found."));
}

<?php

require_once("../shared/actions/db/dao.php");

$db = new sqlHelper();
$sql = 
    "SELECT c.id,
        c.customer_uniq_code,
        c.company_nm,
        c.service_type,
        c.contact,
        c.created_on,
        MAX(t.created_on) AS last_service_date
    FROM cust_mstr c
    LEFT JOIN ticket t ON c.id = t.customer_id
    WHERE c.is_active = 1 AND c.is_deleted = 0
    GROUP BY c.id, c.customer_nm, c.company_nm, c.contact, c.email, c.city, c.area
    HAVING (last_service_date IS NULL OR last_service_date < NOW() - INTERVAL 1 MONTH)
";

$db->prepareStatement($sql);

$db->execPreparedStatement();
$custFetchAllInactiveCustomerResultSet = $db->getResultSet();

if ($custFetchAllInactiveCustomerResultSet->num_rows > 0) {
    
    $iVar = 1;
    $data = array();
    while ($row = $custFetchAllInactiveCustomerResultSet->fetch_assoc()) {
        
        $btn = '
            <div class="btn-group">
                <button type="button" class="btn btn-inverse-primary btn-fw" data-toggle="modal" data-target="#viewCustomerModal" id="viewCustomerModalBtn" onclick="viewCustomer(\'' . $row['customer_uniq_code'] . '\')">
                    <i class="fa fa-ellipsis-v"></i>
                </button>

            </div>
        ';

        $cDate = new DateTime($row['created_on']);
        $last_serviced_date = '
            <div style="text-align: right">
                <span>' . date('d-m-Y h:i:s A', strtotime($row["last_service_date"])) . '</span>
                <br>
                <small> ' . $cDate->diff(new DateTime())->days . ' days ago</small>
            </div>
        '; 
        $data[] = array(
            $iVar,
            $row['company_nm'],
            $row['contact'],
            $row['service_type'],
            $last_serviced_date,
            $btn
        );
        $iVar++;
    }

    echo json_encode(array("success" => true, "data" => $data, "message" => "Data found."));
} else {
    echo json_encode(array("success" => false, "data" => [], "message" => "No data found."));
}


?>
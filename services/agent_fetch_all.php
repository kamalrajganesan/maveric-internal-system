<?php

require_once("../shared/actions/db/dao.php");

$page = "";
if (isset($_POST['param'])) {
    $page = htmlspecialchars($_POST['param']);
} else {
    // echo "";
}

$db = new sqlHelper();
$FetchAllSQL = "SELECT * FROM agent WHERE is_deleted = 0";

$db->prepareStatement($FetchAllSQL);
$db->execPreparedStatement();
$FetchAllSQLResultSet = $db->getResultSet();

if ($FetchAllSQLResultSet->num_rows > 0) {
    $data = array();
    $siVar = 1;
    while ($row = $FetchAllSQLResultSet->fetch_assoc()) {

        $btn = '
        <div class="btn-group">
            <button type="button" class="btn btn-inverse-primary btn-fw" data-toggle="modal" data-target="#viewAgentModal" id="viewAgentModalBtn" onclick="viewAgent(' . $row['id'] . ')">
                <i class="fa fa-2x fa-ellipsis-v"></i>
            </button>
            <button type="button" class="btn btn-inverse-secondary btn-fw" data-toggle="modal" data-target="#editAgentModal" id="editAgentModalBtn" onclick="editAgent(' . $row['id'] . ')">
                <i class="fa fa-2x fa-pencil-square-o"></i>
            </button>
            <button type="button" class="btn btn-inverse-dark btn-fw" data-toggle="modal" data-target="#removeAgentModal" id="removeAgentModalBtn" onclick="removeAgent(' . $row['id'] . ')">
                <i class="fa fa-2x fa-trash-o"></i>
            </button>
        </div>
        ';

        if($row['is_active'] == 1){
            $active_status = '<button type="button" class="btn btn-inverse-success btn-fw" style="padding: 12px 18px;"> 
                <span class="mdi mdi-check-all"></span> Active
            </button>';
        } else {
            $active_status = '<button type="button" class="btn btn-inverse-danger btn-fw" style="padding: 12px 18px;"> 
                <span class="mdi mdi-close-box-multiple"></span> Inactive
            </button>';
        }

        $data[] = array(
            $siVar,
            $row['agent_nm'],
            $row['email'],
            $row['primary_contact'],
            $active_status,
            $btn
        );
        $siVar++;
        $active_status = "";
    }
    echo json_encode(array("success" => true, "data" => $data, "message" => "Data found."));
} else {
    echo json_encode(array("success" => false, "data" => [], "message" => "No data found."));
}

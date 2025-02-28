<?php

require_once("../shared/actions/db/dao.php");

$page = "";
if (isset($_POST['param'])) {
    $page = htmlspecialchars($_POST['param']);
} else {
    echo "param not found";
}

$db = new sqlHelper();
$FetchAllSQL = "SELECT * FROM lead_tracker WHERE is_deleted = 0";

switch ($page) {

    case 'New':
        $FetchAllSQL .= " and lead_status = 'New';";
        break;
    case 'Contacted':
        $FetchAllSQL .= " and lead_status = 'Contacted';";
        break;
    case 'Converted':
        $FetchAllSQL .= " and lead_status = 'Converted';";
        break;
    case 'Following';
        $FetchAllSQL .= " and lead_status = 'Following';";
        break;
    case 'Lost':
        $FetchAllSQL .= " and lead_status = 'Lost';";
        break;
    default:
        echo "Invalid Parm...";
        break;
}

$db->prepareStatement($FetchAllSQL);
$db->execPreparedStatement();
$FetchAllSQLResultSet = $db->getResultSet();

if ($FetchAllSQLResultSet->num_rows > 0) {
    $data = array();
    $siVar = 1;
    while ($row = $FetchAllSQLResultSet->fetch_assoc()) {
        $btn = '
        <div class="btn-group">
            <button type="button" class="btn btn-inverse-primary btn-fw" data-toggle="modal" data-target="#viewLeadModal" id="viewLeadModalBtn" onclick="viewLead(' . $row['id'] . ')">
                <i class="fa fa-2x fa-ellipsis-v"></i>
            </button>
            <button type="button" class="btn btn-inverse-secondary btn-fw" data-toggle="modal" data-target="#editLeadModal" id="editLeadModalBtn" onclick="editLead(' . $row['id'] . ')">
                <i class="fa fa-2x fa-pencil-square-o"></i>
            </button>
            <button type="button" class="btn btn-inverse-dark btn-fw" data-toggle="modal" data-target="#removeLeadModal" id="removeLeadModalBtn" onclick="removeLead(' . $row['id'] . ')">
                <i class="fa fa-2x fa-trash-o"></i>
            </button>
        </div>
        ';

        $data[] = array(
            $siVar,
            $row['lead_nm'],
            $row['contact'],
            $row['company_nm'],
            $row['requirement'],
            $row['description'],
            $row['city'] . " / " . $row['pincode'],
            $row['lead_status'],
            date('d-m-Y h:i:s A', strtotime($row['follow_up_dt'])),
            $btn
        );
        $siVar++;
    }
    echo json_encode(array("success" => true, "data" => $data, "message" => "Data found."));
} else {
    echo json_encode(array("success" => false, "data" => [], "message" => "No data found."));
}

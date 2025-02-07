<?php

require_once("../shared/actions/db/dao.php");

$page = "";
if (isset($_POST['param'])) {
    $page = htmlspecialchars($_POST['param']);
} else {
    echo "param not found";
}

$db = new sqlHelper();
$FetchAllSQL = "SELECT * FROM req_tracker WHERE is_deleted = 0";

switch ($page) {

    case 'New':
        $FetchAllSQL .= " and requirement_status = 'New';";
        break;
    case 'requirement':
        $FetchAllSQL .= " and requirement_status = 'Pending';";
        break;
    case 'Closed':
        $FetchAllSQL .= " and requirement_status = 'Closed';";
        break;
    case 'Lost':
        $FetchAllSQL .= " and requirement_status = 'Lost';";
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
            <button type="button" class="btn btn-inverse-primary btn-fw" data-toggle="modal" data-target="#viewRequirementModal" id="viewRequirementModalBtn" onclick="viewRequirement(' . $row['id'] . ')">
                <i class="fa fa-2x fa-ellipsis-v"></i>
            </button>
            <button type="button" class="btn btn-inverse-secondary btn-fw" data-toggle="modal" data-target="#editRequirementModal" id="editRequirementModalBtn" onclick="editRequirement(' . $row['id'] . ')">
                <i class="fa fa-2x fa-pencil-square-o"></i>
            </button>
            <button type="button" class="btn btn-inverse-dark btn-fw" data-toggle="modal" data-target="#removeRequirementModal" id="removeRequirementModalBtn" onclick="removeRequirement(' . $row['id'] . ')">
                <i class="fa fa-2x fa-trash-o"></i>
            </button>
        </div>
        ';

        $data[] = array(
            $siVar,
            $row['nm'],
            $row['brief'],
            $row['detailed'],
            $row['cust_id'],
            $row['phone'],
            $row['updated_on'],
            $row['requirement_status'],
            $btn
        );
        $siVar++;
    }
    echo json_encode(array("success" => true, "data" => $data, "message" => "Data found."));
} else {
    echo json_encode(array("success" => false, "data" => [], "message" => "No data found."));
}
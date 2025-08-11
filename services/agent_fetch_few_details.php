<?php
require_once("../shared/actions/db/dao.php");

$db = new sqlHelper();
$agentFetchSQL = "SELECT id, agent_nm AS name FROM agent WHERE is_deleted = 0";
$db->prepareStatement($agentFetchSQL);
$db->execPreparedStatement();
$agentFetchResultSet = $db->getResultSet();

if ($agentFetchResultSet->num_rows > 0) {
    $data = [];
    while ($row = $agentFetchResultSet->fetch_assoc()) {
        $data[] = [
            'id' => (int)$row['id'],
            'name' => $row['name'] ?: 'Unknown Agent'
        ];
    }
    echo json_encode(["success" => true, "data" => $data]);
} else {
    echo json_encode(["success" => false, "data" => [], "message" => "No agents found"]);
}
?>
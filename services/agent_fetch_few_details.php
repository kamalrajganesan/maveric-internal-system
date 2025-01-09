<?php

require_once("../shared/actions/db/dao.php");

$db = new sqlHelper();

// Fetch agents details sql
$sql = "SELECT id as code, agent_nm as names FROM agent WHERE is_deleted = 0";

$db->prepareStatement($sql);
$db->execPreparedStatement();
$result = $db->getResultSet();

$agents = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $agent['name'] = $row['names'];
        $agent['id'] = $row['code'];
        $agents[] = $agent;
    }
} else {
    echo "no results";
}

$response = array(
    'success' => true,
    'data' => $agents,
    'message' => 'Agent details fetched successfully'
);

echo json_encode($response);
?>
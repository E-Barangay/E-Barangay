<?php
header('Content-Type: application/json; charset=utf-8');
include_once __DIR__ . "/../../../sharedAssets/connect.php";

$demographicsQuery = "
    SELECT 
        ui.userInfoID AS id,
        ui.firstName,
        ui.lastName,
        ui.age,
        ui.gender
    FROM userinfo ui
    JOIN users u ON ui.userID = u.userID
    WHERE u.role = 'user' 
      AND ui.firstName IS NOT NULL
";

$demographicsQueryResults = $conn->query($demographicsQuery);

$demographicsData = [];
if ($demographicsQueryResults) {
    while ($row = $demographicsQueryResults->fetch_assoc()) {
        $demographicsData[] = $row;
    }
}

echo json_encode($demographicsData);
exit;

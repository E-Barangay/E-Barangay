<?php
include_once __DIR__ . "/../../../sharedAssets/connect.php";

$demographicsQuery = "
     SELECT 
    ui.userInfoID AS id,
    ui.firstName,
    ui.middleName,
    ui.lastName,
    ui.suffix,
    ui.gender,
    ui.birthDate,
    ui.age,
    ui.birthPlace,
    ui.bloodType,
    ui.civilStatus,
    ui.citizenship,
    ui.occupation,
    ui.lengthOfStay,
    ui.residencyType,
    ui.remarks,
    CONCAT(
        a.streetName, ', ',
        a.blockLotNo, ', ',
        a.phase, ', ',
        a.subdivisionName, ', ',
        a.purok, ', ',
        a.barangayName, ', ',
        a.cityName, ', ',
        a.provinceName
    ) AS address
FROM userinfo ui
LEFT JOIN addresses a ON ui.userInfoID = a.userInfoID
JOIN users u ON ui.userID = u.userID
WHERE u.role = 'user'
  AND ui.firstName IS NOT NULL;
  
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

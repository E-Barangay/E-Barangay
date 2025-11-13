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
            COALESCE(a.streetName, ''), 
            IF(a.streetName IS NOT NULL AND a.streetName != '', ', ', ''),
            COALESCE(a.blockLotNo, ''),
            IF(a.blockLotNo IS NOT NULL AND a.blockLotNo != '', ', ', ''),
            COALESCE(a.phase, ''),
            IF(a.phase IS NOT NULL AND a.phase != '', ', ', ''),
            COALESCE(a.subdivisionName, ''),
            IF(a.subdivisionName IS NOT NULL AND a.subdivisionName != '', ', ', ''),
            COALESCE(a.purok, ''),
            IF(a.purok IS NOT NULL AND a.purok != '', ', ', ''),
            COALESCE(a.barangayName, ''),
            IF(a.barangayName IS NOT NULL AND a.barangayName != '', ', ', ''),
            COALESCE(a.cityName, ''),
            IF(a.cityName IS NOT NULL AND a.cityName != '', ', ', ''),
            COALESCE(a.provinceName, '')
        ) AS address
    FROM userinfo ui
    LEFT JOIN addresses a ON ui.userInfoID = a.userInfoID
    WHERE ui.firstName IS NOT NULL 
       OR ui.lastName IS NOT NULL
    ORDER BY ui.lastName ASC, ui.firstName ASC
";

$demographicsQueryResults = $conn->query($demographicsQuery);

$demographicsData = [];
if ($demographicsQueryResults) {
    while ($row = $demographicsQueryResults->fetch_assoc()) {
        $address = $row['address'];
        $address = preg_replace('/,\s*,+/', ',', $address);
        $address = preg_replace('/^,\s*/', '', $address);
        $address = preg_replace('/,\s*$/', '', $address);
        $address = trim($address);
        $row['address'] = $address;
        
        $demographicsData[] = $row;
    }
}

echo json_encode($demographicsData);
exit;
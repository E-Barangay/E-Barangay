<?php
session_start();
require_once __DIR__ . '/../../sharedAssets/connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {

}

$docId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($docId <= 0) {
    die("Invalid document ID.");
}


$sql = "
    SELECT
        d.documentID,
        d.userID,
        d.documentTypeID,
        d.purpose,
        d.documentStatus,
        d.requestDate,
        d.approvalDate,
        dt.documentName AS documentName,

        -- users
        u.email AS userEmail,
        u.role AS userRole,
        u.phoneNumber,

        -- userinfo
        ui.userInfoID,
        ui.firstName,
        ui.middleName,
        ui.lastName,
        ui.suffix,
        ui.gender,
        ui.birthDate,
        ui.birthPlace,
        ui.civilStatus,
        ui.citizenship,
        ui.occupation,
        ui.lengthOfStay,
        ui.residencyType,
        ui.remarks,

        -- addresses (current)
        a.cityName AS cityName,
        a.provinceName AS provinceName,
        a.barangayName AS barangayName,
        a.streetName AS streetName,
        a.blockLotNo AS blockLotNo,
        a.phase AS phase,
        a.subdivisionName AS subdivisionName,
        a.purok AS purok,

        -- permanent addresses
        pa.permanentCityName,
        pa.permanentProvinceName,
        pa.permanentBarangayName,
        pa.permanentStreetName,
        pa.permanentBlockLotNo,
        pa.permanentPhase,
        pa.permanentSubdivisionName,
        pa.permanentPurok

    FROM documents d
    LEFT JOIN documenttypes dt ON d.documentTypeID = dt.documentTypeID
    LEFT JOIN users u ON d.userID = u.userID
    LEFT JOIN userinfo ui ON ui.userID = u.userID
    LEFT JOIN addresses a ON a.userInfoID = ui.userInfoID
    LEFT JOIN permanentaddresses pa ON pa.userInfoID = ui.userInfoID
    WHERE d.documentID = ?
    LIMIT 1
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $docId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Document not found.");
}

$row = $result->fetch_assoc();

$documentName = isset($row['documentName']) && $row['documentName'] !== '' ? $row['documentName'] : 'Unknown Document';

$firstName = $row['firstName'] ?? '';
$middleName = $row['middleName'] ?? '';
$lastName = $row['lastName'] ?? '';
$suffix = $row['suffix'] ?? '';
$fullName = trim(implode(' ', array_filter([$firstName, $middleName, $lastName, $suffix])));

$purpose = $row['purpose'] ?? '';
$documentStatus = $row['documentStatus'] ?? '';
$requestDate = $row['requestDate'] ?? '';
$approvalDate = $row['approvalDate'] ?? '';

$cityName = $row['cityName'] ?? '';
$provinceName = $row['provinceName'] ?? '';
$barangayName = $row['barangayName'] ?? '';
$streetName = $row['streetName'] ?? '';
$blockLotNo = $row['blockLotNo'] ?? '';
$phase = $row['phase'] ?? '';
$subdivisionName = $row['subdivisionName'] ?? '';
$purok = $row['purok'] ?? '';

$permanentCityName = $row['permanentCityName'] ?? '';
$permanentProvinceName = $row['permanentProvinceName'] ?? '';
$permanentBarangayName = $row['permanentBarangayName'] ?? '';
$permanentStreetName = $row['permanentStreetName'] ?? '';
$permanentBlockLotNo = $row['permanentBlockLotNo'] ?? '';
$permanentPhase = $row['permanentPhase'] ?? '';
$permanentSubdivisionName = $row['permanentSubdivisionName'] ?? '';
$permanentPurok = $row['permanentPurok'] ?? '';

$gender = $row['gender'] ?? '';
$birthDate = $row['birthDate'] ?? '';
$birthPlace = $row['birthPlace'] ?? '';
$civilStatus = $row['civilStatus'] ?? '';
$citizenship = $row['citizenship'] ?? '';
$occupation = $row['occupation'] ?? '';
$lengthOfStay = $row['lengthOfStay'] ?? '';
$residencyType = $row['residencyType'] ?? '';
$remarks = $row['remarks'] ?? '';
$userEmail = $row['userEmail'] ?? '';
$userRole = $row['userRole'] ?? '';

function docNameToFile($docName) {
    $name = preg_replace('/[^a-zA-Z0-9\s]/', '', $docName);
    $name = strtolower(trim($name));
    $name = preg_replace('/\s+/', '_', $name);
    $map = [
        'barangay_clearance' => 'barangayClearance.php', 
        'good_moral' => 'goodMoral.php',
    ];
    if (isset($map[$name])) return $map[$name];
    return $name . '.php';
}

$filename = docNameToFile($documentName);
$docPath = realpath(__DIR__ . "/../../documents/documentTypes/" . $filename);

if (!$docPath) {
    $alt = str_replace('_', '', str_replace(' ', '', ucwords(str_replace('_',' ',$filename))));
    $camel = preg_replace_callback('/_([a-z])/', function($m){ return strtoupper($m[1]); }, $filename);
    $tryPaths = [
        __DIR__ . "/../../documents/documentTypes/" . $camel,
        __DIR__ . "/../../documents/documentTypes/" . ucfirst($camel),
        __DIR__ . "/../../documents/documentTypes/" . $filename, // original
    ];
    foreach ($tryPaths as $p) {
        if (file_exists($p)) {
            $docPath = realpath($p);
            break;
        }
    }
}

if (!$docPath || !file_exists($docPath)) {
    $docPath = realpath(__DIR__ . "/../../documents/documentTypes/defaultLayout.php");
}


?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Print - <?= htmlspecialchars($documentName) ?></title>
    <style>
        body { font-family: "Times New Roman", serif; margin: 30px; color: #000; background: #fff; }
        .admin-watermark { position: fixed; top: 35%; left: 15%; transform: rotate(-30deg); font-size: 6rem; opacity: 0.08; pointer-events: none; user-select: none; }
        .no-print { text-align: right; margin-bottom: 18px; }
        .print-btn { padding: 8px 12px; background:#4CAF50; color:#fff; border:0; border-radius:4px; cursor:pointer;}
        @media print { .no-print { display:none } .admin-watermark{opacity:0.06;} }
    </style>
</head>
<body>
    <div class="admin-watermark">ADMIN COPY</div>

    <div class="no-print">
        <button class="print-btn" onclick="window.print()">🖨 Print</button>
    </div>

    <?php
    if ($docPath && file_exists($docPath)) {
        include $docPath;
    } else {
        echo "<h2>" . htmlspecialchars($documentName) . " (Preview)</h2>";
        echo "<p><strong>Name:</strong> " . htmlspecialchars($fullName) . "</p>";
        echo "<p><strong>Purpose:</strong> " . htmlspecialchars($purpose) . "</p>";
    }
    ?>

    <script>
        setTimeout(()=> window.print(), 800);
    </script>
</body>
</html>
<?php
$stmt->close();
$conn->close();

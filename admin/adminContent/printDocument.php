<?php
session_start();
include_once __DIR__ . '/../../sharedAssets/connect.php';

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

if (!isset($_GET['documentID'])) {
    header("Location: ../adminContent/document.php");
    exit();
}

$documentID = mysqli_real_escape_string($conn, $_GET['documentID']);

$userQuery = "
    SELECT 
        users.userID,
        users.email,
        users.phoneNumber,
        userInfo.firstName,
        userInfo.middleName,
        userInfo.lastName,
        userInfo.suffix,
        userInfo.birthDate,
        userInfo.birthPlace,
        userInfo.gender,
        userInfo.civilStatus,
        userInfo.citizenship,
        userInfo.lengthOfStay,
        userInfo.residencyType,
        userInfo.remarks,
        userInfo.profilePicture,
        
        addresses.blockLotNo,
        addresses.phase,
        addresses.subdivisionName,
        addresses.purok,
        addresses.streetName,
        addresses.barangayName,
        addresses.cityName,
        addresses.provinceName,

        permanentAddresses.permanentBlockLotNo,
        permanentAddresses.permanentPhase,
        permanentAddresses.permanentSubdivisionName,
        permanentAddresses.permanentPurok,
        permanentAddresses.permanentStreetName,
        permanentAddresses.permanentBarangayName,
        permanentAddresses.permanentCityName,
        permanentAddresses.permanentProvinceName,

        documents.documentID,
        documents.documentTypeID,
        documents.purpose,
        documents.businessName,
        documents.businessAddress,
        documents.businessNature,
        documents.controlNo,
        documents.ownership,
        documents.spouseName,
        documents.marriageYear,
        documents.childNo,
        documents.soloParentSinceDate,
        documents.requestDate,
        documents.documentStatus,
        documentTypes.documentName

    FROM documents
    LEFT JOIN users ON documents.userID = users.userID
    LEFT JOIN userInfo ON users.userID = userInfo.userID
    LEFT JOIN addresses ON userInfo.userID = addresses.userInfoID
    LEFT JOIN permanentAddresses ON userInfo.userInfoID = permanentAddresses.userInfoID
    LEFT JOIN documentTypes ON documents.documentTypeID = documentTypes.documentTypeID
    WHERE documents.documentID = '$documentID' AND documents.documentStatus = 'approved'
";

$userResult = mysqli_query($conn, $userQuery);

if (!$userResult || mysqli_num_rows($userResult) === 0) {
    echo "<script>alert('Document not found or not yet approved!'); window.location.href='../adminContent/document.php';</script>";
    exit();
}

$userRow = mysqli_fetch_assoc($userResult);

$firstName = $userRow['firstName'];
$middleName = $userRow['middleName'];
$lastName = $userRow['lastName'];
$suffix = $userRow['suffix'];
$fullName = trim("$firstName " . ($middleName ? $middleName[0] . ". " : "") . "$lastName $suffix");

$birthDate = $userRow['birthDate'];
$age = $birthDate ? date_diff(date_create($birthDate), date_create('now'))->y : '';
$birthPlace = $userRow['birthPlace'];
$gender = $userRow['gender'];
$profilePicture = $userRow['profilePicture'];
$citizenship = $userRow['citizenship'];
$civilStatus = $userRow['civilStatus'];
$lengthOfStay = $userRow['lengthOfStay'];
$currentYear = date('Y');
$residingYear = $currentYear - (int)$lengthOfStay;
$residencyType = $userRow['residencyType'];
$remarks = $userRow['remarks'];

$startYear = (int)$lengthOfStay;
$currentYear = date("Y");
$yearsOfStay = $currentYear - $startYear;

function formatAddress($value) {
    return ucwords(strtolower($value ?? ''));
}

$blockLotNo = formatAddress($userRow['blockLotNo']);
$phase = formatAddress($userRow['phase']); 
$subdivisionName = formatAddress($userRow['subdivisionName']);
$purok = formatAddress($userRow['purok']);
$streetName = formatAddress($userRow['streetName']);
$barangayName = formatAddress($userRow['barangayName']);
$cityName = formatAddress($userRow['cityName']);
$provinceName = formatAddress($userRow['provinceName']);

$addressParts = array_filter([
    $blockLotNo, $phase, $subdivisionName, $streetName,
    $purok ? "Purok $purok" : '',
    $barangayName ? "Brgy. $barangayName" : '',
    $cityName, $provinceName
]);
$fullAddress = implode(', ', $addressParts);

$permanentBlockLotNo = formatAddress($userRow['permanentBlockLotNo']);
$permanentPhase = formatAddress($userRow['permanentPhase']); 
$permanentSubdivisionName = formatAddress($userRow['permanentSubdivisionName']);
$permanentPurok = formatAddress($userRow['permanentPurok']);
$permanentStreetName = formatAddress($userRow['permanentStreetName']);
$permanentBarangayName = formatAddress($userRow['permanentBarangayName']);
$permanentCityName = formatAddress($userRow['permanentCityName']);
$permanentProvinceName = formatAddress($userRow['permanentProvinceName']);

$permanentAddressParts = array_filter([
    $permanentBlockLotNo, $permanentPhase, $permanentSubdivisionName, $permanentStreetName,
    $permanentPurok ? "Purok $permanentPurok" : '',
    $permanentBarangayName ? "Brgy. $permanentBarangayName" : '',
    $permanentCityName, $permanentProvinceName
]);
$permanentFullAddress = implode(', ', $permanentAddressParts);

$documentName = $userRow['documentName'];
$purpose = $userRow['purpose'];
$businessName = $userRow['businessName'];
$businessAddress = $userRow['businessAddress'];
$businessNature = $userRow['businessNature'];
$controlNo = $userRow['controlNo'];
$ownership = $userRow['ownership'];
$spouseName = $userRow['spouseName'];
$marriageYear = $userRow['marriageYear'];
$childNo = $userRow['childNo'];
$soloParentSinceDate = $userRow['soloParentSinceDate'];

// paths
$sharedPath = __DIR__ . '/documents/sharedAssets/';
$layoutPath = __DIR__ . '/documents/documentTypes/';

$documentTypeID = $userRow['documentTypeID'];
switch ($documentTypeID) {
    case 1: $layoutFile = 'barangayClearance.php'; break;
    case 2: $layoutFile = 'businessClearance.php'; break;
    case 3: $layoutFile = 'constructionClearance.php'; break;
    case 4: $layoutFile = 'firstTimeJobSeeker.php'; break;
    case 5: $layoutFile = 'goodHealth.php'; break;
    case 6: $layoutFile = 'goodMoral.php'; break;
    case 7: $layoutFile = 'jointCohabitation.php'; break;
    case 9: $layoutFile = 'residency.php'; break;
    case 10: $layoutFile = 'soloParent.php'; break;
    default: $layoutFile = null;
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Print <?= htmlspecialchars($documentName); ?></title>
    <link rel="icon" href="../../assets/images/logoSanAntonio.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    @page {
        size: A4;
        margin: 1in;
    }

    @media print {
        body {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            margin: 0;
            background: white;
        }

        .no-print {
            display: none !important;
        }

        @page {
            margin: 0;
        }

        .print-container {
            margin: 0;
            padding: 1in;
            width: 210mm;
            height: 297mm;
            box-sizing: border-box;
            page-break-after: always;
        }

        html, body {
            width: 210mm;
            height: 297mm;
        }
    }

    body {
        font-family: "Times New Roman", serif;
        background-color: #fff;
    }

    .print-container {
        width: 210mm;
        min-height: 297mm;
        margin: auto;
        padding: 1in;
        background: white;
        box-shadow: 0 0 5px rgba(0,0,0,0.2);
    }

    .buttons {
        text-align: center;
        margin: 20px 0;
    }

    .btn {
        padding: 10px 25px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 15px;
    }

    .btn-success
    .btn-danger
    .btn:hover { opacity: 0.8; }

    .print-container p {
    text-align: justify;
    line-height: 1.5;
    font-size: 12pt;
}

</style>

</head>
<body onload="window.print()">

<div class="buttons no-print">
    <button class="btn btn-success" onclick="window.print()">🖨️ Print Document</button>
    <button class="btn btn-danger" onclick="window.close()">← Back</button>
</div>

<?php if ($documentTypeID == 4) { ?>
    
    <div class="print-container d-flex flex-column px-5 py-5">
        <?php include $sharedPath . 'header.php'; ?>

        <?php
        if ($layoutFile && file_exists($layoutPath . $layoutFile)) {
            include $layoutPath . $layoutFile;
        } else {
            echo "<p style='text-align:center; margin-top:100px;'>Layout not found for this document type.</p>";
        }
        ?>

        <?php include $sharedPath . 'footer.php'; ?>
    </div>

    <div class="print-container d-flex flex-column px-5 py-5">
        <?php 
            $customTitle = "Oath Of Undertaking"; 
            include $sharedPath . 'header.php';
        ?>

        <?php include ("documents/documentTypes/oathOfUndertaking.php") ?>

        <?php include $sharedPath . 'footer.php'; ?>
    </div>

<?php } else { ?>

    <div class="print-container d-flex flex-column px-5 py-5">
        <?php include $sharedPath . 'header.php'; ?>

        <?php
            if ($layoutFile && file_exists($layoutPath . $layoutFile)) {
                include $layoutPath . $layoutFile;
            } else {
                echo "<p style='text-align:center; margin-top:100px;'>Layout not found for this document type.</p>";
            }
        ?>

        <?php include $sharedPath . 'footer.php'; ?>
    </div>

<?php } ?>

</body>

</html>
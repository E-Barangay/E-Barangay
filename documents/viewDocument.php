<?php

include("../sharedAssets/connect.php");

session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
} else {
    $userID = $_SESSION['userID'];
}

$document = '';

$documentTypeID = isset($_GET['documentTypeID']) ? $_GET['documentTypeID'] : '';

if (isset($_GET['documentTypeID'])) {
    $document = $_GET['documentTypeID'];
    switch ($document) {
        case "1":
            $document = "barangayClearance";
            break;
        case "2":
            $document = "businessClearance";
            break;
        case "3":
            $document = "constructionClearance";
            break;
        case "4":
            $document = "firstTimeJobSeeker";
            break;
        case "5":
            $document = "goodHealth";
            break;
        case "6":
            $document = "goodMoral";
            break;
        case "7":
            $document = "jointCohabitation";
            break;
        case "8":
            $document = "residency";
            break;
        case "9":
            $document = "soloParent";
            break;
        default:
            header("Location: ../documents.php");
            break;
    }
}

$userQuery = "SELECT * FROM users 
            LEFT JOIN userinfo ON users.userID = userinfo.userID 
            LEFT JOIN documents ON users.userID = documents.userID
            LEFT JOIN addresses ON userinfo.userInfoID = addresses.userInfoID  
            LEFT JOIN permanentaddresses ON userinfo.userInfoID = permanentaddresses.userInfoID
            WHERE users.userID = $userID";
$userResult = executeQuery($userQuery);

$userRow = mysqli_fetch_assoc($userResult);

$firstName = $userRow['firstName'];
$middleName = $userRow['middleName'];
$lastName = $userRow['lastName'];
$suffix = $userRow['suffix'];
$fullName = $firstName . " " . ($middleName ? $middleName[0] . ". " : "") . $lastName . " " . $suffix;
$birthDate = $userRow['birthDate'];
$age = date_diff(date_create($birthDate), date_create('now'))->y;
$birthPlace = $userRow['birthPlace'];
$gender = $userRow['gender'];
$profilePicture = $userRow['profilePicture'];
$citizenship = $userRow['citizenship'];
$civilStatus = $userRow['civilStatus'];
$lengthOfStay = $userRow['lengthOfStay'];
$currentYear = date('Y');
$residingYear = $currentYear - $lengthOfStay;
$residencyType = $userRow['residencyType'];
$remarks = $userRow['remarks'];

$startYear = $lengthOfStay;
$currentYear = date("Y");
$yearsOfStay = $currentYear - $startYear;

function formatAddress($value) {
    return ucwords(strtolower($value));
}

$blockLotNo = $userRow['blockLotNo'];
$phase = $userRow['phase']; 
$subdivisionName = $userRow['subdivisionName'];
$purok = $userRow['purok'];
$streetName = $userRow['streetName'];
$barangayName = formatAddress($userRow['barangayName']);
$cityName = formatAddress($userRow['cityName']);
$provinceName = formatAddress($userRow['provinceName']);

$permanentBlockLotNo = $userRow['permanentBlockLotNo'];
$permanentPhase = $userRow['permanentPhase']; 
$permanentSubdivisionName = $userRow['permanentSubdivisionName'];
$permanentPurok = $userRow['permanentPurok'];
$permanentStreetName = $userRow['permanentStreetName'];
$permanentBarangayName = formatAddress($userRow['permanentBarangayName']);
$permanentCityName = formatAddress($userRow['permanentCityName']);
$permanentProvinceName = formatAddress($userRow['permanentProvinceName']);

$documentQuery = "SELECT * FROM documenttypes WHERE documentTypeID = $documentTypeID";
$documentResult = executeQuery($documentQuery);

$documentRow = mysqli_fetch_assoc($documentResult);

$documentName = $documentRow['documentName'];

$purpose = $_SESSION['purpose'] ?? '';
$businessName = $_SESSION['businessName'] ?? '';
$businessAddress = $_SESSION['businessAddress'] ?? '';
$businessNature = $_SESSION['businessNature'] ?? '';
$controlNo = $_SESSION['controlNo'] ?? '';
$spouseName = $_SESSION['spouseName'] ?? '';
$marriageYear = $_SESSION['marriageYear'] ?? '';
$ownership = $_SESSION['ownership'] ?? '';
$educationStatus = $_SESSION['educationStatus'] ?? '';
$childNo = $_SESSION['childNo'] ?? '';
$soloParentSinceDate = $_SESSION['soloParentSinceDate'] ?? '';

if (isset($_POST['yes'])) {
    
    if ($documentTypeID == 2) {
        $documentRequestQuery = "INSERT INTO documents (documentTypeID, userID, purpose, businessName, businessAddress, businessNature, controlNo, ownership, requestDate, approvalDate, cancelledDate, deniedDate, archiveDate) VALUES ($documentTypeID, $userID, '$purpose', '$businessName', '$businessAddress', '$businessNature', $controlNo, '$ownership', NOW(), NULL, NULL, NULL, NULL)";
    } elseif ($documentTypeID == 1 || $documentTypeID == 3|| $documentTypeID == 5 || $documentTypeID == 8) {
        $documentRequestQuery = "INSERT INTO documents (documentTypeID, userID, purpose, requestDate, approvalDate, cancelledDate, deniedDate, archiveDate) VALUES ($documentTypeID, $userID, '$purpose', NOW(), NULL, NULL, NULL, NULL)";
    } elseif ($documentTypeID == 4) {
        $documentRequestQuery = "INSERT INTO documents (documentTypeID, userID, purpose, educationStatus, requestDate, approvalDate, cancelledDate, deniedDate, archiveDate) VALUES ($documentTypeID, $userID, 'General Request', '$educationStatus', NOW(), NULL, NULL, NULL, NULL)";
    } elseif ($documentTypeID == 7) {
        $documentRequestQuery = "INSERT INTO documents (documentTypeID, userID, purpose, spouseName, marriageYear, requestDate, approvalDate, cancelledDate, deniedDate, archiveDate) VALUES ($documentTypeID, $userID, 'General Request', '$spouseName', $marriageYear, NOW(), NULL, NULL, NULL, NULL)";
    } elseif ($documentTypeID == 9) {
        $documentRequestQuery = "INSERT INTO documents (documentTypeID, userID, purpose, childNo, soloParentSinceDate, requestDate, approvalDate, cancelledDate, deniedDate, archiveDate) VALUES ($documentTypeID, $userID, 'General Request', $childNo, $soloParentSinceDate, NOW(), NULL, NULL, NULL, NULL)";
    } else {
        $documentRequestQuery = "INSERT INTO documents (documentTypeID, userID, purpose, requestDate, approvalDate, cancelledDate, deniedDate, archiveDate) VALUES ($documentTypeID, $userID, 'General Request', NOW(), NULL, NULL, NULL, NULL)";
    }

    $documentRequestResult = executeQuery($documentRequestQuery);

    if ($educationStatus == "Not Studying") {
        $updateUserInfoQuery = "UPDATE userinfo SET isOSY = 'Yes' WHERE userID = $userID";
    } else {
        $updateUserInfoQuery = "UPDATE userinfo SET isOSY = 'No' WHERE userID = $userID";
    }

    executeQuery($updateUserInfoQuery);

    unset(
        $_SESSION['purpose'],
        $_SESSION['businessName'],
        $_SESSION['businessAddress'],
        $_SESSION['businessNature'],
        $_SESSION['controlNo'],
        $_SESSION['ownership'],
        $_SESSION['educationStatus'],
        $_SESSION['spouseName'],
        $_SESSION['marriageYear'],
        $_SESSION['childNo']
    );

    $_SESSION['documentName'] = $documentRow['documentName'];
    $_SESSION['success'] = 'requestConfirmed';
    header("Location: ../documents.php?content=documentRequest");
    exit();
    
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Barangay | <?php echo $documentName ?></title>

    <!-- Icon -->
    <link rel="icon" href="../assets/images/logoSanAntonio.png">

    <!-- Style Sheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/documents/style.css">
</head>

<body data-bs-theme="light">

    <?php include("../sharedAssets/loadingIndicator.php"); ?>
    
    <form method="POST">
        <div class="container d-flex justify-content-center align-items-center py-4" style="min-height:100vh;">
            <div class="row">
                <div class="col my-auto">
                    
                    <?php include("sharedAssets/header.php") ?>

                    <div class="row my-4 d-flex flex-row justify-content-center">

                        <?php include("documentTypes/" . $document . ".php"); ?>

                    </div>

                    <?php include("sharedAssets/footer.php") ?>

                </div>
            </div>
        </div>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>

    <script src="assets/js/loadingIndicator/script.js"></script>

    <script>

        var residency = document.getElementById("residency");

        residency.addEventListener("change", function () {

            var value = residency.value;

            switch (value) {
                case "Migrant":
                    document.body.style.backgroundColor = "#FFF3CD";
                    break;
                case "Transient":
                    document.body.style.backgroundColor = "#CCE5FF";
                    break;
                case "Foreign":
                    document.body.style.backgroundColor = "#F8D7DA";
                    break;
                default:
                    document.body.style.backgroundColor = "#CCD7E9";
            }

        });

    </script>

</body>

</html>
<?php

include("../sharedAssets/connect.php");

session_start();

$userID = $_SESSION['userID'];

$document = '';

$documentTypeID = isset($_GET['documentTypeID']) ? $_GET['documentTypeID'] : '';

if (isset($_GET['documentTypeID'])) {
    $document = $_GET['documentTypeID'];
    switch ($document) {
        case "1":
            $document = "businessClearance";
            break;
        case "2":
            $document = "barangayClearance";
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
        case "9":
            $document = "residency";
            break;
        case "10":
            $document = "soloParent";
            break;
        default:
            header("Location: ../documents.php");
            break;
    }
}

$userQuery = "SELECT * FROM users 
            LEFT JOIN userInfo ON users.userID = userInfo.userID 
            LEFT JOIN documents ON users.userID = documents.userID
            LEFT JOIN addresses ON userInfo.userID = addresses.userInfoID  
            LEFT JOIN permanentAddresses ON userInfo.userInfoID = permanentAddresses.userInfoID
            WHERE users.userID = $userID";
$userResult = executeQuery($userQuery);

$userRow = mysqli_fetch_assoc($userResult);

$firstName = $userRow['firstName'];
$middleName = $userRow['middleName'];
$lastName = $userRow['lastName'];
$fullName = $firstName . " " . ($middleName ? $middleName[0] . ". " : "") . $lastName;
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

$documentQuery = "SELECT * FROM documentTypes WHERE documentTypeID = $documentTypeID";
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
$childNo = $_SESSION['childNo'] ?? '';

if (isset($_POST['confirmButton'])) {
    
    if ($documentTypeID == 1) {
        $documentRequestQuery = "INSERT INTO documents (documentTypeID, userID, purpose, businessName, businessAddress, businessNature, controlNo, ownership, requestDate) VALUES ($documentTypeID, $userID, '$purpose', '$businessName', '$businessAddress', '$businessNature', $controlNo, '$ownership', NOW())";
    } elseif ($documentTypeID == 2 || $documentTypeID == 5 || $documentTypeID == 9) {
        $documentRequestQuery = "INSERT INTO documents (documentTypeID, userID, purpose, requestDate) VALUES ($documentTypeID, $userID, '$purpose', NOW())";
    } elseif ($documentTypeID == 7) {
        $documentRequestQuery = "INSERT INTO documents (documentTypeID, userID, purpose, spouseName, marriageYear, requestDate) VALUES ($documentTypeID, $userID, '$purpose', '$spouseName', $marriageYear, NOW())";
    } elseif ($documentTypeID == 10) {
        $documentRequestQuery = "INSERT INTO documents (documentTypeID, userID, childNo, requestDate) VALUES ($documentTypeID, $userID, $childNo, NOW())";
    } else {
        $documentRequestQuery = "INSERT INTO documents (documentTypeID, userID, requestDate) VALUES ($documentTypeID, $userID, NOW())";
    }

    $documentRequestResult = executeQuery($documentRequestQuery);

    unset(
        $_SESSION['purpose'],
        $_SESSION['businessName'],
        $_SESSION['businessAddress'],
        $_SESSION['businessNature'],
        $_SESSION['controlNo'],
        $_SESSION['ownership'],
        $_SESSION['spouseName'],
        $_SESSION['marriageYear'],
        $_SESSION['childNo']
    );

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
    <style>
        * {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }
        
        .purposeSelect {
            height: 30px;
            align-items: center;
        }

        .confirmButton {
            background-color: #19AFA5;
            border: none;
        }
    </style>
</head>

<body data-bs-theme="light">
    
    <form method="POST">
        <div class="container py-4">
            <div class="row">
                <div class="col">
                    
                    <?php include("sharedAssets/header.php") ?>

                    <div class="row mt-3 justify-content-center">

                        <?php include("documentTypes/" . $document . ".php"); ?>

                    </div>

                    <?php include("sharedAssets/footer.php") ?>
                    
                    <div class="d-flex justify-content-end mt-3">
                        <a href="../documents.php">
                            <button class="btn btn-secondary cancelButton me-2" type="button">Cancel</button>
                        </a>
                        <button class="btn btn-primary confirmButton" id="confirmButton" type="submit" name="confirmButton">Confirm Request</button>
                    </div>

                </div>
            </div>
        </div>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>

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
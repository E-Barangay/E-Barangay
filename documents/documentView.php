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
            $document = "registration";
            break;
        case "2":
            $document = "barangayClearance";
            break;
        case "3":
            $document = "newBusinessClearance";
            break;
        case "4":
            $document = "newConstructionClearance";
            break;
        case "5":
            $document = "newIndigencyRecord";
            break;
        case "6":
            $document = "firstTimeJobSeeker";
            break;
        case "7":
            $document = "jointCohabitation";
            break;
        case "8":
            $document = "constructionClearance";
            break;
        case "9":
            $document = "goodMoral";
            break;
        case "10":
            $document = "goodHealthRecord";
            break;
        case "11":
            $document = "pendingResidentRegistration";
            break;
        case "12":
            $document = "migrantList";
            break;
        case "13":
            $document = "transientList";
            break;
        case "14":
            $document = "foreignList";
            break;
        default:
            header("Location: ../documents.php");
            break;
    }
}

$userQuery = "SELECT * FROM users 
              LEFT JOIN userInfo ON users.userID = userInfo.userID 
              LEFT JOIN addresses ON userInfo.userInfoID = addresses.userInfoID 
              LEFT JOIN streets ON addresses.streetID = streets.streetID
              LEFT JOIN barangays ON streets.barangayID = barangays.barangayID
              LEFT JOIN cities ON barangays.cityID = cities.cityID
              LEFT JOIN provinces ON cities.provinceID = provinces.provinceID
              WHERE users.userID = $userID";
$userResult = executeQuery($userQuery);

$userRow = mysqli_fetch_assoc($userResult);

$firstName = $userRow['firstName'];
$middleName = $userRow['middleName'];
$lastName = $userRow['lastName'];
$birthDate = $userRow['birthDate'];
$gender = $userRow['gender'];
$profilePicture = $userRow['profilePicture'];
$residencyType = $userRow['residencyType'];
$streetName = $userRow['streetName'];
$barangayName = $userRow['barangayName'];
$cityName = $userRow['cityName'];
$provinceName = $userRow['provinceName'];

$documentQuery = "SELECT * FROM documentTypes WHERE documentTypeID = $documentTypeID";
$documentResult = executeQuery($documentQuery);

$documentRow = mysqli_fetch_assoc($documentResult);

$documentName = $documentRow['documentName'];

if (isset($_POST['submit'])) {
    $purpose = $_POST['purpose'];

    $submitQuery = "INSERT INTO documents (documentTypeID, userID, purpose, requestDate) VALUES ($documentTypeID, $userID, '$purpose', NOW())";
    executeQuery($submitQuery);

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

        .submitButton {
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
                        <button class="btn btn-primary submitButton" id="submitButton" type="submit" name="submit">Submit</button>
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
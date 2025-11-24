<?php
include("sharedAssets/connect.php");

session_start();

$page = "complaintSection";

$userID = $_SESSION['userID'];

if (isset($_GET['page'])) {
    $page = $_GET['page'];
    switch ($page) {
        case "complaintSection":
            if (!isset($_SESSION['userID'])) {
                header("Location: login.php");
            }
            break;
        case "makeComplaint":
            $userQuery = "SELECT * FROM users 
                        LEFT JOIN userinfo ON users.userID = userinfo.userID 
                        LEFT JOIN addresses ON userinfo.userInfoID = addresses.userInfoID  
                        LEFT JOIN permanentaddresses ON userinfo.userInfoID = permanentaddresses.userInfoID
                        WHERE users.userID = $userID";
            $userResult = executeQuery($userQuery);

            $userDataRow = mysqli_fetch_assoc($userResult);

            $isProfileComplete = !(
                empty($userDataRow['firstName'])
                || empty($userDataRow['lastName'])
                || empty($userDataRow['profilePicture'])
                || empty($userDataRow['gender'])
                || empty($userDataRow['birthDate'])
                || empty($userDataRow['birthPlace'])
                || empty($userDataRow['civilStatus'])
                || empty($userDataRow['citizenship'])
                || empty($userDataRow['lengthOfStay'])
                || empty($userDataRow['residencyType'])
                || empty($userDataRow['phoneNumber'])
                || empty($userDataRow['email'])
                || empty($userDataRow['purok'])
                || empty($userDataRow['barangayName'])
                || empty($userDataRow['cityName'])
                || empty($userDataRow['provinceName'])
                || empty($userDataRow['permanentPurok'])
                || empty($userDataRow['permanentBarangayName'])
                || empty($userDataRow['permanentCityName'])
                || empty($userDataRow['permanentProvinceName'])
            );

            if (!$isProfileComplete) {
                $_SESSION['warning'] = 'incompleteInformation2';
                header("Location: profile.php");
            } else {
                $page = "makeComplaint";
                break;
            }
        case "submittedComplaints":
            $page = "submittedComplaints";
            break;
        default:
            header("Location: ?page=complaintSection");
            break;
    }
} else {
    header("Location: ?page=complaintSection");
}


?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Barangay | Complaints</title>

    <!-- Icon -->
    <link rel="icon" href="assets/images/logoSanAntonio.png">

    <!-- Style Sheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/navbar/style.css">
    <link rel="stylesheet" href="assets/css/complaints/style.css">
    <link rel="stylesheet" href="assets/css/footer/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>


</head>

<body data-bs-theme="light">

    <?php

    if (isset($_SESSION['userID'])) {
        include("sharedAssets/navbarLoggedIn.php");
    } else {
        include("sharedAssets/navbar.php");
    }

    ?>

    <div class="container pt-3">
        <div class="row">
            <div class="col-12 col-lg-3 p-0">
                
                <div class="filterCard card m-1 p-3">
                    <div class="row">
                        <div class="col d-flex flex-column align-items-center">
                            <a href="?page=complaintSection"
                                class="btn btn-primary filterButton m-2 w-100 <?php echo ($page == 'complaintSection') ? 'active' : ''; ?>">Home</a>
                            <a href="?page=makeComplaint"
                                class="btn btn-primary filterButton m-2 w-100 <?php echo ($page == 'makeComplaint') ? 'active' : ''; ?>">File
                                a Complaint</a>
                            <a href="?page=submittedComplaints"
                                class="btn btn-primary filterButton m-2 w-100 <?php echo ($page == 'submittedComplaints') ? 'active' : ''; ?>">Submitted
                                Complaints</a>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-12 col-lg-9 p-0">
                <div class="contentCard card m-1 p-2">
                    <div class="row  px-3 py-2" id="scrollable" style="max-height: 100vh; overflow-y: auto;">

                        <?php include("contents/complaintContent/" . $page . ".php"); ?>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include("sharedAssets/footer.php") ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
    <script src="assets/js/signUp/report.js"></script>
    
</body>
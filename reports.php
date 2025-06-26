<?php
include("sharedAssets/connect.php");

session_start();

$page = "complaintSection";

if (isset($_GET['page'])) {
    $page = $_GET['page'];
    switch ($page) {
        case "makeComplaint":
            $page = "makeComplaint";
            break;
        case "complaintSection":
            if (!isset($_SESSION['userID'])) {
                header("Location: signIn.php");
            }
            break;
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
    <title>E-Barangay | Reports</title>

    <!-- Icon -->
    <link rel="icon" href="assets/images/logoSanAntonio.png">

    <!-- Style Sheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/navbar/style.css">
    <link rel="stylesheet" href="assets/css/reports/style.css">
    <link rel="stylesheet" href="assets/css/footer/style.css">


</head>

<body data-bs-theme="dark">

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
                <div class="filterCard card m-1 p-2 d-none d-md-block">
                    <div class="row mt-2">
                        <div class="col d-flex flex-column align-items-center">
                            <a href="?page=complaintSection" class="btn btn-primary filterButton m-2 <?php echo ($page == 'complaintSection') ? 'active' : ''; ?>">Home</a>
                            <a href="?page=makeComplaint" class="btn btn-primary filterButton m-2 <?php echo ($page == 'makeComplaint') ? 'active' : ''; ?>">File a Complaint</a>
                            <a href="?page=submittedComplaints" class="btn btn-primary filterButton m-2 <?php echo ($page == 'submittedComplaints') ? 'active' : ''; ?>">Submitted Reports</a>
                        </div>
                    </div>
                </div>

                <div class="row mb-2 px-5 d-sm-none d-flex justify-content-center">
                    <div class="col-4 d-flex justify-content-center">
                        <a href="?page=complaintSection" class="btn filterButton p-2 w-100 text-center">
                            <i class="bi bi-house-door-fill"></i>
                        </a>
                    </div>
                    <div class="col-4 d-flex justify-content-center">
                        <a href="?page=makeComplaint" class="btn filterButton p-2 w-100 text-center">
                            <i class="bi bi-file-earmark-text-fill"></i>
                        </a>
                    </div>
                    <div class="col-4 d-flex justify-content-center">
                        <a href="?page=submittedComplaints" class="btn filterButton p-2 w-100 text-center">
                            <i class="bi bi-send-fill"></i>
                        </a>
                    </div>
                </div>
            </div>


            <div class="col-12 col-lg-9 ">
                <div class="card cardBorder m-1 p-0 p-md-2">
                    <?php include("reportContent/" . $page . ".php"); ?>
                </div>
            </div>
        </div>
    </div>
    </div>

    <?php include("sharedAssets/footer.php") ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>


</body>
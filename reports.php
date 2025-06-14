<?php

$page = "complaintSection";

if (isset($_GET['page'])) {
    $page = $_GET['page'];
    switch ($page) {
        case "makeComplaint":
            $page = "makeComplaint";
            break;
        case "complaintSection":
            $page = "complaintSection";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/navbar/style.css">
    <link rel="stylesheet" href="assets/css/reports/style.css">
    <link rel="stylesheet" href="assets/css/footer/style.css">

    <style>
        .content {
            min-height: 75vh;
            max-height: 75vh;
            overflow-y: auto;
        }

        .sidebar {
            min-height: 25vh;
        }

        .btn-width {
            width: 200px;
        }
    </style>
</head>

<body data-bs-theme="dark">

    <?php include("sharedAssets/navbar.php") ?>

    <div class="container pt-3">
        <div class="row">
            <div class="col-12 col-lg-3 p-0">
                <div class="filterCard card m-1 p-2 d-none d-sm-block">
                    <div class="row mt-4">
                        <div class="col d-flex flex-column align-items-center">
                            <a href="?page=complaintSection" class="btn btn-primary btn-width mb-2">Complaint
                                Section</a>
                            <a href="?page=makeComplaint" class="btn btn-primary btn-width mb-2">Make A Complaint</a>
                            <a href="?page=submittedComplaints" class="btn btn-primary btn-width mb-2">Submitted
                                Complaints</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-9 ">
                <div class="card m-1 p-2">
                    <?php include("reportContent/" . $page . ".php"); ?>
                </div>
            </div>
        </div>
    </div>

    <?php include("sharedAssets/footer.php") ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>


</body>
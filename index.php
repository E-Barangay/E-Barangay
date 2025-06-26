<?php

include("sharedAssets/connect.php");
session_start();

?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Barangay | Home</title>

    <!-- Icon -->
    <link rel="icon" href="assets/images/logoSanAntonio.png">

    <!-- Style Sheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/navbar/style.css">
    <link rel="stylesheet" href="assets/css/index/style.css">
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

    <div class="container">
        <div class="col">
            <div class="row">
                <div class="card mt-4 border-0">
                    <div class="h1 text-center m-3 fw-bold">
                        Barangay San Antonio E-Services
                    </div>
                    <div class="h6 text-center m-3">
                        "Makabagong Putol, Makikinabang All"
                    </div>
                    <div class="d-flex justify-content-center m-4">
                        <button class="btn btn-primary viewButton p-2"
                            onclick="window.location.href='documents.php'">View Our Services</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col">
                <div class="card border-0">
                    <div id="carouselExample" class="carousel slide">
                        <div class="carousel-inner rounded-4" style="height: 600px;">
                            <?php
                            $query = "SELECT image FROM announcements ORDER BY dateTime DESC";
                            $result = mysqli_query($conn, $query);
                            $isFirst = true;
                            while ($row = mysqli_fetch_assoc($result)) {
                                $image = htmlspecialchars($row['image']);
                                $imagePath = "assets/images/announcements/" . $image;
                                ?>
                                <div class="carousel-item <?= $isFirst ? 'active' : '' ?>">
                                    <img src="<?= $imagePath ?>" class="d-block w-100 h-100 object-fit-cover"
                                        alt="Announcement Image">
                                </div>
                                <?php
                                $isFirst = false;
                            }
                            ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row mb-3">
            <div class="col">
                <div class="d-flex align-items-center">
                    <h1 class="me-2 mb-0">Announcements</h1>
                    <span class="badge rounded-pill text-bg-danger">New</span>
                </div>
            </div>
        </div>
        <div class="row">
            <?php
            $query = "SELECT * FROM announcements WHERE isImportant = 'TRUE' ORDER BY dateTime DESC";
            $result = mysqli_query($conn, $query);

            while ($row = mysqli_fetch_assoc($result)) {
                $title = htmlspecialchars($row['title']);
                $description = htmlspecialchars($row['description']);
                $image = htmlspecialchars($row['image']);
                $imagePath = "assets/images/announcements/" . $image;
                ?>
                <div class="col-md-4">
                    <div class="card mt-3" style="width: 100%;">
                        <img src="<?= $imagePath ?>" class="card-img-top" alt="Announcement Image">
                        <div class="card-body">
                            <h5 class="card-title custom-title"><?= $title ?></h5>
                            <p class="card-text"><?= $description ?></p>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>

    <?php include("sharedAssets/footer.php") ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
</body>

</html>
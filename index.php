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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/navbar/style.css">
    <link rel="stylesheet" href="assets/css/index/style.css">
    <link rel="stylesheet" href="assets/css/footer/style.css">
</head>

<body data-bs-theme="light">

    <?php
        if (isset($_SESSION['userID'])) {
            include("sharedAssets/navbarLoggedIn.php");
        } else {
            include("sharedAssets/navbar.php");
        }
    ?>

    <div class="container-fluid p-0 pt-4 overflow-hidden">
        <div class="row">
            <div class="col p-0 d-flex justify-content-center">
                <div class="card bannerCard p-0">
                    <img src="assets/images/banner.jpeg" class="bannerImage" alt="Banner Image">
                    <div class="bannerContent">
                        <div class="header">
                            Barangay San Antonio <br> E-Services
                        </div>
                        <div class="tagline">
                            Makabagong Putol, Makikinabang All
                        </div>
                        <div class="d-flex justify-content-center m-4">
                            <a class="btn btn-primary viewButton p-2" href='documents.php'>View Our Services</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container pt-5">

        <div class="row">
            <div class="col">
                <div class="announcements">
                    Announcements
                </div>
            </div>
        </div>

        <div class="row pt-4">
            <div class="col">
                <div class="whatIsNew">
                    What's New
                </div>
            </div>
        </div>

        <div class="row pt-3">

            <div class="col-lg-4 col-md-6 col-12 pb-4">
                <div class="card newCard">
                    <div class="row">
                        <div class="col d-flex flex-row align-items-center px-4 py-3">
                            <img src="assets/images/logoSanAntonio.png" class="logo" alt="Logo">
                            <div class="d-flex flex-column justify-content-center ps-2">
                                <span class="barangay">Barangay San Antonio</span>
                                <span class="date">
                                    September 7, 2025
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <img src="assets/images/announcements/announcement1.jpg" class="newAnnouncementImage" alt="New Announcement Image">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col px-4 pt-3">
                            <span class="title">Lorem ipsum dolor sit amet consectetur adipisicing elit</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col px-4 pt-2 pb-3">
                            <p class="description m-0">
                                Lorem ipsum dolor, sit amet consectetur adipisicing elit. Perspiciatis beatae velit ratione dignissimos quisquam adipisci maxime cum culpa aliquid unde. Nisi iste voluptatum, ducimus delectus consectetur voluptates quos laborum molestias.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12 pb-4">
                <div class="card newCard">
                    <div class="row">
                        <div class="col d-flex flex-row align-items-center px-4 py-3">
                            <img src="assets/images/logoSanAntonio.png" class="logo" alt="Logo">
                            <div class="d-flex flex-column justify-content-center ps-2">
                                <span class="barangay">Barangay San Antonio</span>
                                <span class="date">
                                    September 7, 2025
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <img src="assets/images/announcements/announcement2.jpg" class="newAnnouncementImage" alt="New Announcement Image">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col px-4 pt-3">
                            <span class="title">Lorem ipsum dolor sit amet consectetur adipisicing elit</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col px-4 pt-2 pb-3">
                            <p class="description m-0">
                                Lorem ipsum dolor, sit amet consectetur adipisicing elit. Perspiciatis beatae velit ratione dignissimos quisquam adipisci maxime cum culpa aliquid unde. Nisi iste voluptatum, ducimus delectus consectetur voluptates quos laborum molestias.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12 pb-4">
                <div class="card newCard">
                    <div class="row">
                        <div class="col d-flex flex-row align-items-center px-4 py-3">
                            <img src="assets/images/logoSanAntonio.png" class="logo" alt="Logo">
                            <div class="d-flex flex-column justify-content-center ps-2">
                                <span class="barangay">Barangay San Antonio</span>
                                <span class="date">
                                    September 7, 2025
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <img src="assets/images/announcements/announcement3.jpg" class="newAnnouncementImage" alt="New Announcement Image">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col px-4 pt-3">
                            <span class="title">Lorem ipsum dolor sit amet consectetur adipisicing elit</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col px-4 pt-2 pb-3">
                            <p class="description m-0">
                                Lorem ipsum dolor, sit amet consectetur adipisicing elit. Perspiciatis beatae velit ratione dignissimos quisquam adipisci maxime cum culpa aliquid unde. Nisi iste voluptatum, ducimus delectus consectetur voluptates quos laborum molestias.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row pt-1">
            <div class="col">
                <div class="recents">
                    Recents
                </div>
            </div>
        </div>

        <div class="row pt-3">
            
            <div class="col-lg-4 col-md-6 col-12 pb-4">
                <div class="card recentCard">
                    <div class="row">
                        <div class="col">
                            <img src="assets/images/announcements/announcement1.jpg" class="recentAnnouncementImage" alt="Recent Announcement Image">
                        </div>
                    </div>
                    <div class="row px-3 pt-3">
                        <div class="col d-flex flex-row align-items-center">
                            <img src="assets/images/logoSanAntonio.png" class="logo" alt="Logo">
                            <div class="d-flex flex-column ps-2">
                                <span class="barangay">Barangay San Antonio</span>
                                <span class="date text-start">
                                    September 07, 2025
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row px-3 py-3">
                        <div class="col">
                            <span class="title">Lorem ipsum dolor sit, amet consectetur adipisicing elit</span>
                        </div>
                    </div>
                    <div class="row px-3 pb-3">
                        <div class="col">
                            <button class="btn btn-primary viewDetailsButton" type="button" data-bs-toggle="modal" data-bs-target="#announcement">View More Details</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12 pb-4">
                <div class="card recentCard">
                    <div class="row">
                        <div class="col">
                            <img src="assets/images/announcements/announcement2.jpg" class="recentAnnouncementImage" alt="Recent Announcement Image">
                        </div>
                    </div>
                    <div class="row px-3 pt-3">
                        <div class="col d-flex flex-row align-items-center">
                            <img src="assets/images/logoSanAntonio.png" class="logo" alt="Logo">
                            <div class="d-flex flex-column ps-2">
                                <span class="barangay">Barangay San Antonio</span>
                                <span class="date text-start">
                                    September 07, 2025
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row px-3 py-3">
                        <div class="col">
                            <span class="title">Lorem ipsum dolor sit, amet consectetur adipisicing elit</span>
                        </div>
                    </div>
                    <div class="row px-3 pb-3">
                        <div class="col">
                            <button class="btn btn-primary viewDetailsButton" type="button" data-bs-toggle="modal" data-bs-target="#announcement">View More Details</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12 pb-4">
                <div class="card recentCard">
                    <div class="row">
                        <div class="col">
                            <img src="assets/images/announcements/announcement3.jpg" class="recentAnnouncementImage" alt="Recent Announcement Image">
                        </div>
                    </div>
                    <div class="row px-3 pt-3">
                        <div class="col d-flex flex-row align-items-center">
                            <img src="assets/images/logoSanAntonio.png" class="logo" alt="Logo">
                            <div class="d-flex flex-column ps-2">
                                <span class="barangay">Barangay San Antonio</span>
                                <span class="date text-start">
                                    September 07, 2025
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row px-3 py-3">
                        <div class="col">
                            <span class="title">Lorem ipsum dolor sit, amet consectetur adipisicing elit</span>
                        </div>
                    </div>
                    <div class="row px-3 pb-3">
                        <div class="col">
                            <button class="btn btn-primary viewDetailsButton" type="button" data-bs-toggle="modal" data-bs-target="#announcement">View More Details</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal fade" id="announcement" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="postModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body p-0"> 
                            <div class="row g-0">
                                <div class="col-lg-7 col-12 d-flex align-items-center justify-content-center">
                                    <img src="assets/images/announcements/announcement1.jpg" class="modalRecentAnnouncementImage" alt="Recent Announcement Image">
                                </div>
                                <div class="col-lg-5 col-12 d-flex flex-column">
                                    <div class="row px-3 pt-3">
                                        <div class="col d-flex flex-row align-items-center">
                                            <img src="assets/images/logoSanAntonio.png" class="modalLogo" alt="Logo">
                                            <div class="d-flex flex-column ps-2">
                                                <span class="modalBarangay">Barangay San Antonio</span>
                                                <span class="modalDate text-start">
                                                    September 07, 2025
                                                </span>
                                            </div>
                                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                    </div>
                                    <div class="row px-3 py-3">
                                        <div class="col">
                                            <span class="modalTitle">Lorem ipsum dolor sit amet consectetur adipisicing elit</span>
                                        </div>
                                    </div>
                                    <div class="row px-3 pb-3">
                                        <div class="col">
                                            <p class="modalDescription">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Voluptates dolores quod magni reprehenderit optio repellendus deserunt exercitationem quisquam incidunt repellat dolorum eligendi perspiciatis facilis fuga, doloribus iusto, eaque nisi quos?</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include("sharedAssets/footer.php") ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>

</body>

</html>
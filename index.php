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

    <div class="container pt-3 mx-auto">

        <div class="row">
            <div class="col">
                <div class="card p-5 border-0 rounded-4" style="background-color: #19AFA5;">
                    <div class="h1 text-center m-3 fw-bold text-white">
                        Barangay San Antonio E-Services
                    </div>
                    <div class="h5 text-center m-3 text-white">
                        "Makabagong Putol, Makikinabang All"
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        <a href="documents.php">
                            <button class="btn btn-primary viewButton p-2">View Our Services</button>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row pt-4">
            <div class="col">
                <div id="carouselExampleCaptions" class="carousel slide shadow rounded-4 overflow-hidden">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active"
                            aria-current="true" aria-label="Slide 1"></button>
                        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1"
                            aria-label="Slide 2"></button>
                        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2"
                            aria-label="Slide 3"></button>
                    </div>
                    <div class="carousel-inner ratio ratio-16x9">
                        <div class="carousel-item active">
                            <img src="assets/images/announcements/announcement3.jpg" class="d-block w-100 h-100" style="object-fit: cover; object-position: center;" alt="...">
                        </div>
                        <div class="carousel-item">
                            <img src="assets/images/announcements/announcement2.jpg" class="d-block w-100 h-100" style="object-fit: cover;" alt="...">
                        </div>
                        <div class="carousel-item">
                            <img src="assets/images/announcements/announcement1.jpg" class="d-block w-100 h-100" style="object-fit: cover;" alt="...">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>


        <div class="row pt-5 p-0">

            <div class="d-flex align-items-center">
                <h3 class="me-2 mb-0">Announcements</h3>
                <span class="badge rounded-pill text-bg-danger">New</span>
            </div>

            <div class="col-lg-4 col-12 d-flex justify-content-center">
                <div class="card mt-3">
                    <img src="assets/images/announcements/announcement1.jpg" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title custom-title">Announcement 1</h5>
                        <p class="card-text">Lorem ipsum dolor sit amet consectetur adipisicing elit. Laborum magni fuga voluptatibus! Labore, similique! Sit qui, adipisci blanditiis, hic molestias illum molestiae maxime ab quia quod exercitationem? Quia, voluptas nihil.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-12 d-flex justify-content-center">
                <div class="card mt-3">
                    <img src="assets/images/announcements/announcement1.jpg" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title custom-title">Announcement 2</h5>
                        <p class="card-text">Lorem ipsum dolor sit amet consectetur adipisicing elit. Laborum magni fuga voluptatibus! Labore, similique! Sit qui, adipisci blanditiis, hic molestias illum molestiae maxime ab quia quod exercitationem? Quia, voluptas nihil.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-12 d-flex justify-content-center">
                <div class="card mt-3">
                    <img src="assets/images/announcements/announcement1.jpg" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title custom-title">Announcement 3</h5>
                        <p class="card-text">Lorem ipsum dolor sit amet consectetur adipisicing elit. Laborum magni fuga voluptatibus! Labore, similique! Sit qui, adipisci blanditiis, hic molestias illum molestiae maxime ab quia quod exercitationem? Quia, voluptas nihil.
                        </p>
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

</html>
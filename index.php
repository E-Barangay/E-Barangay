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
    <?php include("sharedAssets/navbar.php") ?>

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
                        <button class="btn btn-primary viewButton p-2">View Our Services</button>
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
                            <div class="carousel-item active">
                                <img src="assets/images/announcements/image.png"
                                    class=" w-100 h-100 object-fit-cover" alt="...">
                            </div>
                            <div class="carousel-item">
                                <img src="assets/images/announcements/image1.png"
                                    class="d-block w-100 h-100 object-fit-cover" alt="...">
                            </div>
                            <div class="carousel-item">
                                <img src="assets/images/announcements/image2.png"
                                    class="d-block w-100 h-100 object-fit-cover" alt="...">
                            </div>
                            <div class="carousel-item">
                                <img src="assets/images/announcements/image3.png"
                                    class="d-block w-100 h-100 object-fit-cover" alt="...">
                            </div>
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
            <div class="col-md-4">
                <div class="card mt-3" style="width: 100%;">
                    <img src="assets/images/announcements/image2.png" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title custom-title">Balik Eskwela</h5>
                        <p class="card-text">Balik Eskwela is an annual initiative that marks the beginning of a new
                            academic year, encouraging students to return to school with enthusiasm and readiness. It
                            aims to promote awareness, preparedness, and community involvement in ensuring a smooth and
                            safe school opening. This campaign also highlights the importance of education as a shared
                            responsibility among students, parents, teachers, and local stakeholders.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mt-3" style="width: 100%;">
                    <img src="assets/images/announcements/image2.png" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title custom-title">Balik Eskwela</h5>
                        <p class="card-text">Balik Eskwela is an annual initiative that marks the beginning of a new
                            academic year, encouraging students to return to school with enthusiasm and readiness. It
                            aims to promote awareness, preparedness, and community involvement in ensuring a smooth and
                            safe school opening. This campaign also highlights the importance of education as a shared
                            responsibility among students, parents, teachers, and local stakeholders.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mt-3" style="width: 100%;">
                    <img src="assets/images/announcements/image2.png" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title custom-title">Balik Eskwela</h5>
                        <p class="card-text">Balik Eskwela is an annual initiative that marks the beginning of a new
                            academic year, encouraging students to return to school with enthusiasm and readiness. It
                            aims to promote awareness, preparedness, and community involvement in ensuring a smooth and
                            safe school opening. This campaign also highlights the importance of education as a shared
                            responsibility among students, parents, teachers, and local stakeholders.
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
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Landing Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
</head>

<body data-bs-theme="dark">
    <nav class="navbar navbar-expand-lg bg-body-tertiary p-0">
        <div class="container-fluid shadow">
            <a class="navbar-brand d-flex align-items-center gap-3" href="navbar.html">
                <img src="assets/images/logoSanAntonio.png" alt="San Antonio Logo" width="100" height="125">
                <img src="assets/images/logoSantoTomas.png" alt="Santo Tomas Logo" width="100" height="100">
                <span class="fw-bold">Barangay San Antonio</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse " id="navbarNav">
                <ul class="navbar-nav ms-auto d-flex align-items-center">
                    <li class="nav-item">
                        <a class="nav-link active1" href="documents.html">Documents</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active2" href="reports.html">Reports</a>
                    </li>
                    <li class="nav-item">
                        <div class="d-flex flex-row align-items-center">
                            <a class="nav-link active3" href="signIn.html">Sign In/Sign Up</a>
                            <div class="bg-secondary" style="width: 75px; height: 75px; border-radius: 50%;">
                                <img src="assets/images/defaultProfile.png"
                                    style="object-fit: cover; width: 100%; height: 100%;" alt="Profile">
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <div class="col">
            <div class="row">
                <div class="card mt-3">
                    <div class="h1 text-center m-3">
                        Barangay San Antonio E-Services
                    </div>
                    <div class="h6 text-center m-3">
                        "Makabagong Putol, Makikinabang All"
                    </div>
                    <div class="d-flex justify-content-center m-4">
                        <button type="button" class="btn btn-primary">View Our Services</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5 mb-5">
        <div class="col">
            <div class="row">
                <div id="carouselExample" class="carousel slide">
                    <div class="carousel-inner" style="height: 800px;">
                        <div class="carousel-item active">
                            <img src="assets/images/announcements/image.png" class="d-block w-100"
                                style="height: 100%; object-fit: cover;" alt="...">
                        </div>
                        <div class="carousel-item">
                            <img src="assets/images/announcements/image1.png" class="d-block w-100"
                                style="height: 100%; object-fit: cover;" alt="...">
                        </div>
                        <div class="carousel-item">
                            <img src="assets/images/announcements/image2.png" class="d-block w-100"
                                style="height: 100%; object-fit: cover;" alt="...">
                        </div>
                        <div class="carousel-item">
                            <img src="assets/images/announcements/image3.png" class="d-block w-100"
                                style="height: 100%; object-fit: cover;" alt="...">
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
                        <h5 class="card-title">Card title</h5>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk of
                            the
                            card’s content.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mt-3" style="width: 100%;">
                    <img src="assets/images/announcements/image2.png" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title">Card title</h5>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk of
                            the
                            card’s content.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mt-3" style="width: 100%;">
                    <img src="assets/images/announcements/image2.png" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title">Card title</h5>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk of
                            the
                            card’s content.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="h1 text-center">
        FOOTER
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
</body>

</html>
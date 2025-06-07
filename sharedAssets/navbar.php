<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Navbar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <style>

        .active1, .active2, .active3 {
            font-weight: bold;
            color: white;
        }

        .active1:hover, .active2:hover, .active3:hover  {
            color: #8CD998;
        }
        
    </style>
</head>

<body data-bs-theme="dark">
    <nav class="navbar navbar-expand-lg bg-body-tertiary p-0">
        <div class="container-fluid shadow">
            <a class="navbar-brand d-flex align-items-center gap-3" href="../navbar.php">
                <img src="../assets/images/logoSanAntonio.png" alt="San Antonio Logo" width="100" height="125">
                <img src="../assets/images/logoSantoTomas.png" alt="Santo Tomas Logo" width="100" height="100">
                <span class="fw-bold">Barangay San Antonio</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse " id="navbarNav">
                <ul class="navbar-nav ms-auto d-flex align-items-center">
                    <li class="nav-item">
                        <a class="nav-link active1" href="../documents.php">Documents</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active2" href="../reports.php">Reports</a>
                    </li>
                    <li class="nav-item">
                        <div class="d-flex flex-row align-items-center">
                            <a class="nav-link active3" href="../signIn.php">Sign In/Sign Up</a>
                            <div class="bg-secondary" style="width: 75px; height: 75px; border-radius: 50%;">
                                <img src="../assets/images/defaultProfile.png" style="object-fit: cover; width: 100%; height: 100%;" alt="Profile">
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>

</html>
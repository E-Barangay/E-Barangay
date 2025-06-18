<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/signin/style.css">

    <style>

    </style>
</head>

<body data-bs-theme="light">

    <div class="container-fluid">
        <div class="row">
            <!-- Left Side Image -->
            <div class="col-md-7 d-none d-md-block p-0">
                <img src="assets/images/bg.png" class="left-img position-relative" alt="Barangay Background">
            </div>

            <!-- Right Side Sign In -->
            <div class="col-md-5 col-12 d-flex align-items-start justify-content-center"
                style="height: 100vh; padding-top: 100px;">

                <!-- <div class="card rounded-5 w-100 shadow sign-in-card p-5"> -->
                <div class="w-100">

                    <div class="text-center mb-2">
                        <div class="d-flex flex-row justify-content-center align-items-center gap-3">
                            <img src="assets/images/logoSanAntonio.png" class="img-fluid" style="max-width: 60px;"
                                alt="Logo">
                            <img src="assets/images/logoSantoTomas.png" class="img-fluid" style="max-width: 60px;"
                                alt="Logo">
                        </div>

                        <h2 class="mb-0">Brgy. San Antonio</h2>
                        <div class="fs-6">Sto. Tomas, Batangas</div>

                        <p class="fs-6 fst-italic text-center">
                            Serving the vibrant community of Barangay San Antonio, Santo Tomas, Batangas — where
                            tradition meets progress.
                        </p>

                    </div>

                    <div class="mb-4 mt-3 d-flex flex-column align-items-center">
                        <label class="form-label w-75">Email/Phone Number</label>
                        <input type="email" placeholder="Email" class="form-control w-75">

                        <label class="form-label mt-3 w-75">Password</label>
                        <input type="password" placeholder="Password" class="form-control w-75"
                            aria-describedby="passwordHelpBlock">

                        <div class="text-end mt-2 w-75">
                            <a href="#">Forgot Password?</a>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center mb-4">
                        <button type="button" class="btn btn-custom rounded-5 fs-5 px-5">Log in</button>
                    </div>

                    <div class="fs-6 text-center">
                        Need an Account? <a href="signup.php">Sign Up</a>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
</body>

</html>
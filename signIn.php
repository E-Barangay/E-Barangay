<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/navbar/style.css">
</head>

<body data-bs-theme="dark">
    <?php include("sharedAssets/navbar.php") ?>
    <div class="container p-3 p-md-5">
        <div class="card p-3" style="min-height: 75vh">
            <div class="row">
                <div class="col-md-3 col-0"></div>
                <div class="col-md-6 col-12 d-flex flex-column">
                    <div class="text-center mt-5 mb-5">
                        <h2>SIGN IN</h2>
                    </div>
                    <div class="mb-5 mt-3">
                        <label class="form-label text-start">Email/Phone Number</label>
                        <input type="email" class="form-control w-100 h-25">

                        <label class="form-label mt-3">Password</label>
                        <input type="password" class="form-control w-100 h-25" aria-describedby="passwordHelpBlock">
                        <div class="text-end mt-2">Forgot Password</div>
                    </div>

                    <div class="d-flex flex-row justify-content-center mb-md-5 m-0">
                        <button type="button" class="rounded-5 btn btn-primary fs-5" style="width: 200px">Log
                            in</button>
                    </div>



                    <div class="fs-6 mt-md-5 mt-2 mb-3 text-center">
                        Need an Account? <a href="signup.php">Sign Up</a>
                    </div>


                </div>
                <div class="col-md-3 col-0"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
</body>

</html>
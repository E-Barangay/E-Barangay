<?php
include("sharedAssets/connect.php");

session_start();

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $email = str_replace('\'', '', $email);
    $password = str_replace('\'', '', $password);


    $loginQuery = "SELECT * FROM users WHERE (email = '$email') AND password = '$password'";
    $loginResult = executeQuery($loginQuery);

    if (mysqli_num_rows($loginResult) > 0) {
        $user = mysqli_fetch_assoc($loginResult);
        $_SESSION['userID'] = $user['userID'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        if ($user['role'] === 'admin') {
            header("Location: admin/index.php"); 
        } else {
            header("Location: index.php");      
        }
    } else {
        $_SESSION['login_error'] = true; // Set error flag
        header("Location: signin.php");   // Redirect to same page
        exit();
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/signin/style.css">
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
                style="height: 100vh; padding-top: 50px;">
                <div class="w-100">
                    <div class="text-center mb-5">
                        <div class="d-flex flex-row justify-content-center align-items-center gap-3">
                            <img src="assets/images/logoSanAntonio.png" class="img-fluid" style="max-width: 60px;"
                                alt="Logo">
                            <img src="assets/images/logoSantoTomas.png" class="img-fluid" style="max-width: 60px;"
                                alt="Logo">
                        </div>
                        <h2 class="mb-0">Barangay San Antonio</h2>
                        <div class="fs-6">Sto. Tomas, Batangas</div>
                        <p class="fs-6 fst-italic text-center">
                            Serving the vibrant community of Barangay San Antonio, Santo Tomas, Batangas — where
                            tradition meets progress.
                        </p>
                    </div>

                    <form action="" method="POST">
                        <div class="mb-4 mt-3 d-flex flex-column align-items-center">
                            <label class="form-label w-75">Email</label>
                            <input type="email" placeholder="Email" name="email" class="form-control w-75" required>

                            <label class="form-label mt-3 w-75">Password</label>
                            <input type="password" placeholder="Password" name="password" class="form-control w-75"
                                required>

                            <div class="text-end mt-2 w-75">
                                <a href="#">Forgot Password?</a>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center mb-4">
                            <button type="submit" name="submit" class="btn btn-custom rounded-5 fs-5 px-5">Log
                                in</button>
                        </div>
                    </form>

                    <div class="fs-6 text-center">
                        Need an Account? <a href="signup.php">Sign Up</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Login Error Modal -->
    <div class="modal fade" id="loginErrorModal" tabindex="-1" aria-labelledby="loginErrorModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="loginErrorModalLabel">Login Failed</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Invalid email or password. Please try again.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>

    <?php if (isset($_SESSION['login_error'])): ?>
        <script>
            new bootstrap.Modal(document.getElementById('loginErrorModal')).show();
        </script>
        <?php unset($_SESSION['login_error']); ?>
    <?php endif; ?>
</body>

</html>
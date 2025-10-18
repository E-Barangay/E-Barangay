<?php

include("sharedAssets/connect.php");

session_start();

$loginStep = 'email';

if (isset($_POST['next'])) {
    $email = $_POST['email'];

    $email = str_replace('\'', '', $email);

    $emailCheckQuery = "SELECT * FROM users WHERE email = '$email'";
    $emailCheckResult = executeQuery($emailCheckQuery);

    if (mysqli_num_rows($emailCheckResult) > 0) {
        $user = mysqli_fetch_assoc($emailCheckResult);
        $_SESSION['email'] = $email;

        if (empty($user['password'])) {
            $loginStep = "notExistingPassword";
        } else {
            $loginStep = "existingPassword";
        }
    } else {
        $loginStep = 'email';
        $_SESSION['warning'] = 'notFoundEmail';
    }
}

if (isset($_POST['submit'])) {
    $email = $_SESSION['email'];
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    $checkUserQuery = "SELECT * FROM users WHERE email = '$email'";
    $checkUserResult = executeQuery($checkUserQuery);
    $userRow = mysqli_fetch_assoc($checkUserResult);

    if ($userRow && !empty($userRow['password'])) {
        if ($password === $userRow['password']) {
            $_SESSION['userID'] = $userRow['userID'];
            $_SESSION['role'] = $userRow['role'];

            if ($userRow['role'] === 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: index.php");
            }
        } else {
            $_SESSION['alert'] = 'invalidPassword';
            $loginStep = 'existingPassword';
        }

    } else {
        if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*\W).{8,}$/', $password)) {
            $_SESSION['alert'] = 'weakPassword';
            $loginStep = 'notExistingPassword';
        } elseif ($password !== $confirmPassword) {
            $_SESSION['alert'] = 'mismatchPassword';
            $loginStep = 'notExistingPassword';
        } else {
            $password = str_replace("'", "", $password);

            $updatePasswordQuery = "UPDATE users SET password = '$password', isNew = 'No' WHERE email = '$email'";
            executeQuery($updatePasswordQuery);

            $loginQuery = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
            $loginResult = executeQuery($loginQuery);

            if (mysqli_num_rows($loginResult) > 0) {
                $user = mysqli_fetch_assoc($loginResult);
                $_SESSION['userID'] = $user['userID'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    header("Location: admin/index.php");
                } else {
                    header("Location: index.php");
                }
            }
        }
    }
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Barangay | Login</title>

    <!-- Icon -->
    <link rel="icon" href="assets/images/logoSanAntonio.png">

    <!-- Style Sheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/login/style.css">
</head>

<body data-bs-theme="light">

    <div class="container-fluid">
        <div class="row min-vh-100">
            
            <div class="col-lg-7 d-none d-lg-block p-0">
                <img src="assets/images/bgHall.jpeg" class="leftBackgroundImage" alt="Barangay Background">
            </div>

            <div class="col-lg-5 col-12 d-flex flex-column justify-content-center px-3 px-sm-5">
                
                <div class="row">
                    <div class="col d-flex justify-content-center">
                        <img src="assets/images/logoSanAntonio.png" class="logoSanAntonio me-2" alt="Logo San Antonio">
                        <img src="assets/images/logoSantoTomas.png" class="logoSantoTomas" alt="Logo Santo Tomas">
                    </div>
                </div>
                <div class="row mt-1">
                    <div class="col">
                        <div class="d-flex flex-column">
                            <span class="barangay mt-2">Barangay San Antonio</span>
                            <span class="city">Santo Tomas City, Batangas</span>
                        </div>
                    </div>
                </div>

                <form method="POST">
                    <div class="row my-4">
                        
                        <div class="col-12">
                            <?php if (isset($_SESSION['warning']) && $_SESSION['warning'] === 'notFoundEmail'): ?>
                                <div class="alert alert-warning">Email not found. Please sign up to create an account.</div>
                                <?php unset($_SESSION['warning']); ?>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['alert']) && $_SESSION['alert'] === 'weakPassword'): ?>
                                <div class="alert alert-danger">Oops! Password must be 8+ characters with uppercase, lowercase, number, and symbol.</div>
                                <?php unset($_SESSION['alert']); ?>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['alert']) && $_SESSION['alert'] === 'mismatchPassword'): ?>
                                <div class="alert alert-danger">Passwords do not match.</div>
                                <?php unset($_SESSION['alert']); ?>
                            <?php endif; ?>

                            <?php if (isset($_SESSION['alert']) && $_SESSION['alert'] === 'invalidPassword'): ?>
                                <div class="alert alert-danger">Invalid Password.</div>
                                <?php unset($_SESSION['alert']); ?>
                            <?php endif; ?>
                        </div>


                        <?php if ($loginStep == "email") { ?>

                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="email" class="form-control" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" id="emailInput" name="email" placeholder="Email address/Phone Number" required>
                                    <label for="emailInput">Email address/Phone Number</label>
                                </div>
                            </div>

                        <?php } elseif ($loginStep == "existingPassword") { ?>

                            <div class="col">
                                <div class="form-floating">
                                    <input type="password" class="form-control" value="<?php echo isset($_POST['password']) ? htmlspecialchars($_POST['password']) : ''; ?>" id="passwordInput" name="password" placeholder="Password" required>
                                    <label for="passwordInput">Password</label>
                                    <i class="fa-regular fa-eye" onclick="togglePassword('passwordInput', this)" style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer; color: #6c757d;"></i>
                                </div>
                            </div>

                        <?php } elseif ($loginStep == "notExistingPassword") { ?>

                            <div class="col-12 mb-3">
                                <div class="form-floating">
                                    <input type="password" class="form-control" value="<?php echo isset($_POST['password']) ? htmlspecialchars($_POST['password']) : ''; ?>" id="passwordInput" name="password" placeholder="Password" required>
                                    <label for="passwordInput">Password</label>
                                    <i class="fa-regular fa-eye" onclick="togglePassword('passwordInput', this)" style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer; color: #6c757d;"></i>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="password" class="form-control" value="<?php echo isset($_POST['confirmPassword']) ? htmlspecialchars($_POST['confirmPassword']) : ''; ?>" id="confirmPasswordInput" name="confirmPassword" placeholder="Confirm Password" required>
                                    <label for="confirmPasswordInput">Confirm Password</label>
                                    <i class="fa-regular fa-eye" onclick="togglePassword('confirmPasswordInput', this)" style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer; color: #6c757d;"></i>
                                </div>
                            </div>

                        <?php } ?>
                        
                    </div>

                    <?php if ($loginStep == "email") { ?>

                        <div class="row">
                            <div class="col text-center">
                                <button class="btn btn-primary nextButton mb-4 mt-2" type="submit" name="next">Next</button>
                                <span class="pt-2" style="color: black;">Need an account?</span> <a href="signUp.php" style="color: #19AFA5;">Sign Up</a>
                            </div>
                        </div>

                    <?php } elseif ($loginStep == "existingPassword" || $loginStep == "notExistingPassword") { ?>

                        <div class="row">
                            <div class="col text-center">
                                <button class="btn btn-primary signUpButton mb-4 mt-2" type="submit" name="submit">Login</button>
                                <span class="pt-2" style="color: black;">Need an account?</span> <a href="signUp.php" style="color: #19AFA5;">Sign Up</a>
                            </div>
                        </div>

                    <?php } ?>
                    
                </form>
                
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, icon) {
            var input = document.getElementById(inputId);
            
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>

</body>

</html>
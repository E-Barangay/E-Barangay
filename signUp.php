<?php

session_start();

include('sharedAssets/connect.php');

if (isset($_POST["signUp"])) {
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phoneNumber'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    $checkEmailQuery = "SELECT * FROM users WHERE email = '$email'";
    $checkEmailResult = executeQuery($checkEmailQuery);
    
    if (mysqli_num_rows($checkEmailResult) > 0) {
        $_SESSION['warning'] = 'emailExists';
    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*\W).{8,}$/', $password)) {
        $_SESSION['alert'] = 'weakPassword';
    } elseif ($password !== $confirmPassword) {
        $_SESSION['alert'] = 'mismatchPassword';
    } else {
        $insertAccountQuery = "INSERT INTO users (email, phoneNumber, password) VALUES('$email', '$phoneNumber', '$password')";
        $insertAccountResult = executeQuery($insertAccountQuery);

        if ($insertAccountResult) {
            $userID = mysqli_insert_id($conn);

            $insertUserQuery = "INSERT INTO userInfo (userID, firstName, middleName, lastName) VALUES('$userID', '$firstName', '$middleName', '$lastName')";
            $insertUserResult = executeQuery($insertUserQuery);

            $_SESSION['userID'] = $userID;

            header("Location: index.php");
        }
        
    }
}

?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Barangay | Sign Up</title>

    <!-- Icon -->
    <link rel="icon" href="assets/images/logoSanAntonio.png">

    <!-- Style Sheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/signup/style.css">
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
                            <?php if (isset($_SESSION['warning']) && $_SESSION['warning'] === 'emailExists'): ?>
                                <div class="alert alert-warning">This email is already registered. Please use a different one or log in.</div>
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
                        </div>

                        <div class="col-lg-4 col-md-4 col-6">
                            <div class="form-floating mb-3">
                                <input type="text" name="firstName" value="<?php echo isset($_POST['firstName']) ? htmlspecialchars($_POST['firstName']) : ''; ?>" class="form-control" id="firstNameInput" placeholder="First Name" required>
                                <label for="firstNameInput">First Name</label>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-4 col-6 p-lg-0 p-md-0">
                            <div class="form-floating mb-3">
                                <input type="text" name="middleName" value="<?php echo isset($_POST['middleName']) ? htmlspecialchars($_POST['middleName']) : ''; ?>" class="form-control" id="middleNameInput" placeholder="Middle Name" required>
                                <label for="middleNameInput">Middle Name</label>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-4 col-12">
                            <div class="form-floating mb-3">
                                <input type="text" name="lastName" value="<?php echo isset($_POST['lastName']) ? htmlspecialchars($_POST['lastName']) : ''; ?>" class="form-control" id="lastNameInput" placeholder="Last Name" required>
                                <label for="lastNameInput">Last Name</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <input type="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" class="form-control" id="emailInput" placeholder="Email address" required>
                                <label for="emailInput">Email address</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <input type="tel" name="phoneNumber" value="<?php echo isset($_POST['phoneNumber']) ? htmlspecialchars($_POST['phoneNumber']) : ''; ?>" class="form-control" id="phoneNumberInput" placeholder="Phone Number" pattern="^09\d{9}$" maxlength="11" title="Phone number must start with 09 and be exactly 11 digits (e.g., 09123456789)" required> 
                                <label for="phoneNumberInput">Phone Number</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-floating mb-3 position-relative">
                                <input type="password" name="password" value="<?php echo isset($_POST['password']) ? htmlspecialchars($_POST['password']) : ''; ?>" class="form-control" id="passwordInput" placeholder="Password" required>
                                <label for="passwordInput">Password</label>
                                <i class="fa-regular fa-eye" onclick="togglePassword('passwordInput', this)" style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer; color: #6c757d;"></i>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-floating position-relative">
                                <input type="password" name="confirmPassword" value="<?php echo isset($_POST['confirmPassword']) ? htmlspecialchars($_POST['confirmPassword']) : ''; ?>" class="form-control" id="confirmPasswordInput" placeholder="Confirm Password" required>
                                <label for="confirmPasswordInput">Confirm Password</label>
                                <i class="fa-regular fa-eye" onclick="togglePassword('confirmPasswordInput', this)" style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer; color: #6c757d;"></i>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col text-center">
                            <button class="btn btn-primary signUpButton mb-3 mt-2" name="signUp">Sign Up</button>
                            <span class="pt-2" style="color: black;">Already have an account?</span> <a href="login.php" style="color: #19AFA5;">Login</a>
                        </div>
                    </div>
                    
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
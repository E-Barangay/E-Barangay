<?php

include('sharedAssets/connect.php');

session_start();

$signUpStep = 'data';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'assets/phpmailer/src/Exception.php';
require 'assets/phpmailer/src/PHPMailer.php';    
require 'assets/phpmailer/src/SMTP.php';

if (isset($_POST["register"])) {
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phoneNumber'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    $isWeak = !preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^\w\s]|_).{8,}$/', $password);

    $checkEmailQuery = "SELECT * FROM users WHERE email = '$email'";
    $checkEmailResult = executeQuery($checkEmailQuery);
    
    if (mysqli_num_rows($checkEmailResult) > 0) {
        $_SESSION['warning'] = 'emailExists';
    } elseif ($isWeak) {
        $_SESSION['alert'] = 'weakPassword';
    } elseif ($password !== $confirmPassword) {
        $_SESSION['alert'] = 'mismatchPassword';
    } else {
        $_SESSION['firstName'] = $firstName;
        $_SESSION['middleName'] = $middleName;
        $_SESSION['lastName'] = $lastName;
        $_SESSION['email'] = $email;
        $_SESSION['phoneNumber'] = $phoneNumber;
        $_SESSION['password'] = $password;

        $_SESSION['success'] = 'emailSent';
        $signUpStep = 'verification';

        $verificationCode = random_int(100000, 999999);
        $_SESSION['verificationCode'] = $verificationCode;
        $verificationCodeExpiry = date('Y-m-d H:i:s', time() + (5 * 60));
        $_SESSION['verificationCodeExpiry'] = $verificationCodeExpiry;

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'brgysanantonioputol@gmail.com';
            $mail->Password   = 'jcal kski idji qghl';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;
            $mail->AddEmbeddedImage('assets/images/logoSanAntonio.webp', 'logoSanAntonio');
            $mail->AddEmbeddedImage('assets/images/logoSantoTomas.webp', 'logoSantoTomas');

            $mail->setFrom('brgysanantonioputol@gmail.com', 'San Antonio e-Desk');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = "Your San Antonio e-Desk Verification Code";
            $mail->Body = '<div style="font-family: Arial, sans-serif; background-color:#f4f6f7; padding: 0; margin: 0;">
                    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f7; padding: 40px 0;">
                        <tr>
                            <td align="center">
                                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                                    <tr style="background-color: #19AFA5;">
                                        <td align="center" style="padding: 20px;">
                                            <img src="cid:logoSanAntonio" alt="San Antonio Logo" style="height:80px;">
                                            <img src="cid:logoSantoTomas" alt="Santo Tomas Logo" style="height:80px; margin-left:10px;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 30px;">
                                            <h2 style="text-align:center; color:#19AFA5; margin-top:0;">Barangay San Antonio</h2>
                                            <p style="text-align:center; margin:0; color:#555;">Santo Tomas City, Batangas</p>
                                            <p style="text-align:center; margin:0; color:#555;">Office of the Barangay Chairman</p>
                                            <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

                                            <p style="font-size:15px; color:#333;">Hi <strong>user</strong>,</p>

                                            <p style="font-size:15px; color:#333;">
                                                Welcome to <strong>San Antonio e-Desk</strong>! We are excited to have you on board.
                                                Before you can access your account, please verify your email address by entering the code below.
                                            </p>

                                            <p style="font-size:15px; color:#333;">Your One-Time Password (OTP) is:</p>

                                            <h2 style="text-align:center; letter-spacing:5px; font-size:32px; color:#19AFA5; margin:20px 0;">' . $verificationCode . '</h2>

                                            <p style="font-size:15px; color:#333;">
                                                Enter this code on the verification page to complete your registration. 
                                                For your security, this code will expire in <strong>5 minutes</strong>.
                                            </p>

                                            <p style="font-size:15px; color:#333;">
                                                If you didn’t create an account with San Antonio e-Desk, please ignore this message.
                                            </p>

                                            <p style="font-size:15px; color:#333;">Thank you for joining us!</p>

                                            <p style="margin-top:30px; color:#333;">
                                                Warm regards,<br>
                                                <strong>San Antonio e-Desk</strong><br>
                                                Barangay San Antonio, Santo Tomas City, Batangas
                                            </p>

                                            <div style="text-align:center; font-size:13px; color:#888; margin-top:20px;">
                                                Telefax: (043) 784-3812 | sanantonioputol@gmail.com
                                            </div>
                                        </td>
                                    </tr>
                                    <tr style="background-color:#19AFA5;">
                                        <td align="center" style="padding:15px; color:white; font-size:13px;">
                                            © 2025 San Antonio e-Desk. All Rights Reserved.
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>';
            $mail->send();
        } catch (Exception $e) {
            echo "Email failed. Error: {$mail->ErrorInfo}";
        }
    }
}

if (isset($_POST['resend'])) {
    $firstName = $_SESSION['firstName'];
    $middleName = $_SESSION['middleName'];
    $lastName = $_SESSION['lastName'];
    $email = $_SESSION['email'];
    $phoneNumber = $_SESSION['phoneNumber'];
    $password = $_SESSION['password'];

    $verificationCode = random_int(100000, 999999);
    $_SESSION['verificationCode'] = $verificationCode;
    $verificationCodeExpiry = date('Y-m-d H:i:s', time() + (5 * 60));
    $_SESSION['verificationCodeExpiry'] = $verificationCodeExpiry;

    $_SESSION['success'] = 'codeResent';
    $signUpStep = "verification";

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'brgysanantonioputol@gmail.com';
        $mail->Password   = 'jcal kski idji qghl';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->AddEmbeddedImage('assets/images/logoSanAntonio.webp', 'logoSanAntonio');
        $mail->AddEmbeddedImage('assets/images/logoSantoTomas.webp', 'logoSantoTomas');

        $mail->setFrom('brgysanantonioputol@gmail.com', 'San Antonio e-Desk');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = "Your San Antonio e-Desk Verification Code";
        $mail->Body = '<div style="font-family: Arial, sans-serif; background-color:#f4f6f7; padding: 0; margin: 0;">
                <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f7; padding: 40px 0;">
                    <tr>
                        <td align="center">
                            <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                                <tr style="background-color: #19AFA5;">
                                    <td align="center" style="padding: 20px;">
                                        <img src="cid:logoSanAntonio" alt="San Antonio Logo" style="height:80px;">
                                        <img src="cid:logoSantoTomas" alt="Santo Tomas Logo" style="height:80px; margin-left:10px;">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 30px;">
                                        <h2 style="text-align:center; color:#19AFA5; margin-top:0;">Barangay San Antonio</h2>
                                        <p style="text-align:center; margin:0; color:#555;">Santo Tomas City, Batangas</p>
                                        <p style="text-align:center; margin:0; color:#555;">Office of the Barangay Chairman</p>
                                        <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

                                        <p style="font-size:15px; color:#333;">Hi <strong>user</strong>,</p>

                                        <p style="font-size:15px; color:#333;">
                                            You requested a new One-Time Password (OTP) to verify your account for <strong>San Antonio e-Desk</strong>.
                                        </p>

                                        <p style="font-size:15px; color:#333;">Your new OTP is:</p>

                                        <h2 style="text-align:center; letter-spacing:5px; font-size:32px; color:#19AFA5; margin:20px 0;">' . $verificationCode . '</h2>

                                        <p style="font-size:15px; color:#333;">
                                            Please enter this new code on the verification page to continue. 
                                            Your previous code is no longer valid. This code will expire in <strong>5 minutes</strong> for your security.
                                        </p>

                                        <p style="font-size:15px; color:#333;">
                                            If you did not request this new code, please ignore this email or contact our support team immediately.
                                        </p>

                                        <p style="font-size:15px; color:#333;">Thank you for keeping your account secure!</p>


                                        <p style="margin-top:30px; color:#333;">
                                            Warm regards,<br>
                                            <strong>San Antonio e-Desk</strong><br>
                                            Barangay San Antonio, Santo Tomas City, Batangas
                                        </p>

                                        <div style="text-align:center; font-size:13px; color:#888; margin-top:20px;">
                                            Telefax: (043) 784-3812 | sanantonioputol@gmail.com
                                        </div>
                                    </td>
                                </tr>
                                <tr style="background-color:#19AFA5;">
                                    <td align="center" style="padding:15px; color:white; font-size:13px;">
                                        © 2025 San Antonio e-Desk. All Rights Reserved.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>';
        $mail->send();
    } catch (Exception $e) {
        echo "Email failed. Error: {$mail->ErrorInfo}";
    }
}

if (isset($_POST['verify'])) {
    $firstName = $_SESSION['firstName'];
    $middleName = $_SESSION['middleName'];
    $lastName = $_SESSION['lastName'];
    $email = $_SESSION['email'];
    $phoneNumber = $_SESSION['phoneNumber'];
    $password = $_SESSION['password'];
    $verificationCode = $_POST['verificationCode'];

    if (trim((string)$verificationCode) === trim((string)$_SESSION['verificationCode'])) {
        $currentTime = date('Y-m-d H:i:s');
        if ($currentTime <= $_SESSION['verificationCodeExpiry']) {

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insertAccountQuery = "INSERT INTO users (email, phoneNumber, password) VALUES('$email', '$phoneNumber', '$hashedPassword')";
            $insertAccountResult = executeQuery($insertAccountQuery);

            if ($insertAccountResult) {
                $userID = mysqli_insert_id($conn);

                $insertUserQuery = "INSERT INTO userinfo (userID, firstName, middleName, lastName) VALUES('$userID', '$firstName', '$middleName', '$lastName')";
                $insertUserResult = executeQuery($insertUserQuery);

                if ($insertUserResult) {
                    $userInfoID = mysqli_insert_id($conn);

                    $insertUserAddressQuery = "INSERT INTO addresses (userInfoID, blockLotNo, phase, subdivisionName, purok, streetName, barangayName, cityName, provinceName) VALUES('$userInfoID', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL)";
                    $insertUserAddressResult = executeQuery($insertUserAddressQuery);

                    $insertUserPermanentAddressQuery = "INSERT INTO permanentaddresses (userInfoID, permanentBlockLotNo, permanentPhase, permanentSubdivisionName, permanentPurok, permanentStreetName, permanentBarangayName, permanentCityName, permanentProvinceName, foreignPermanentAddress) VALUES('$userInfoID', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL)";
                    $insertUserPermanentAddressResult = executeQuery($insertUserPermanentAddressQuery);
                }

                $_SESSION['userID'] = $userID;

                $_SESSION['success'] = 'newUser';

                unset($_SESSION['verificationCode'], $_SESSION['verificationCodeExpiry']);

                header("Location: profile.php");
            }
        } else {
            $_SESSION['alert'] = 'verificationCodeExpired';
            $signUpStep = 'verification';
        }
    } else {
        $_SESSION['alert'] = 'invalidVerificationCode';
        $signUpStep = 'verification';
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
    <link rel="stylesheet" href="assets/css/signUp/style.css">
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
                        <img src="assets/images/logoSanAntonio.webp" class="logoSanAntonio me-2" alt="Logo San Antonio">
                        <img src="assets/images/logoSantoTomas.webp" class="logoSantoTomas" alt="Logo Santo Tomas">
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
                                <div class="alert alert-warning" style="font-size: 14px; line-height: 1.4;"><i class="fa-solid fa-triangle-exclamation" style="margin-right:8px;"></i>This email is already registered. Please use a different one.</div>
                                <?php unset($_SESSION['warning']); ?>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['alert']) && $_SESSION['alert'] === 'weakPassword'): ?>
                                <div class="alert alert-danger" style="font-size: 14px; line-height: 1.4;"><i class="fa-solid fa-circle-exclamation" style="margin-right:8px;"></i>Oops! Password must be 8+ characters with uppercase, lowercase, number, and symbol.</div>
                                <?php unset($_SESSION['alert']); ?>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['alert']) && $_SESSION['alert'] === 'mismatchPassword'): ?>
                                <div class="alert alert-danger" style="font-size: 14px; line-height: 1.4;"><i class="fa-solid fa-circle-exclamation" style="margin-right:8px;"></i>Passwords do not match.</div>
                                <?php unset($_SESSION['alert']); ?>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['alert']) && $_SESSION['alert'] === 'verificationCodeExpired'): ?>
                                <div class="alert alert-danger" style="font-size: 14px; line-height: 1.4;"><i class="fa-solid fa-circle-exclamation" style="margin-right:8px;"></i>Your verification code has expired. Please resend a new code.</div>
                                <?php unset($_SESSION['alert']); ?>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['alert']) && $_SESSION['alert'] === 'invalidVerificationCode'): ?>
                                <div class="alert alert-danger" style="font-size: 14px; line-height: 1.4;"><i class="fa-solid fa-circle-exclamation" style="margin-right:8px;"></i>Invalid verification code.</div>
                                <?php unset($_SESSION['alert']); ?>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['success']) && $_SESSION['success'] === 'emailSent'): ?>
                                <div class="alert alert-success" style="font-size: 14px; line-height: 1.4;"><i class="fa-solid fa-circle-check" style="margin-right:8px;"></i>Verification code sent successfully! Please check your email to continue.</div>
                                <?php unset($_SESSION['success']); ?>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['success']) && $_SESSION['success'] === 'codeResent'): ?>
                                <div class="alert alert-success" style="font-size: 14px; line-height: 1.4;"><i class="fa-solid fa-circle-check" style="margin-right:8px;"></i>A new verification code has been sent to your email.</div>
                                <?php unset($_SESSION['success']); ?>
                            <?php endif; ?>
                        </div>

                        <?php if ($signUpStep == "data") { ?>

                            <div class="col-lg-4 col-md-4 col-6">
                                <div class="form-floating mb-3">
                                    <input type="text" name="firstName" value="<?php echo isset($_POST['firstName']) ? htmlspecialchars($_POST['firstName']) : ''; ?>" class="form-control" id="firstNameInput" placeholder="First Name" pattern="[A-Za-z\s]+" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')" required>
                                    <label for="firstNameInput">First Name</label>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-6 p-lg-0 p-md-0">
                                <div class="form-floating mb-3">
                                    <input type="text" name="middleName" value="<?php echo isset($_POST['middleName']) ? htmlspecialchars($_POST['middleName']) : ''; ?>" class="form-control" id="middleNameInput" placeholder="Middle Name" pattern="[A-Za-z\s]+" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')" required>
                                    <label for="middleNameInput">Middle Name</label>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-12">
                                <div class="form-floating mb-3">
                                    <input type="text" name="lastName" value="<?php echo isset($_POST['lastName']) ? htmlspecialchars($_POST['lastName']) : ''; ?>" class="form-control" id="lastNameInput" placeholder="Last Name" pattern="[A-Za-z\s]+" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')" required>
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

                        <?php } elseif ($signUpStep == "verification") { ?>

                            <div class="col">
                                <div class="form-floating">
                                    <input type="number" class="form-control" value="<?php echo isset($_POST['verificationCode']) ? htmlspecialchars($_POST['verificationCode']) : ''; ?>" id="verificationInput" name="verificationCode" placeholder="Enter 6-digit verification code" inputmode="numeric" pattern="[0-9]*" oninput="if(this.value.length > 6) this.value = this.value.slice(0, 6);" min="0" onkeydown="return !['e','E','-','+','.',','].includes(event.key)" required>
                                    <label for="verificationInput">Enter 6-digit verification code</label>
                                </div>
                            </div>

                        <?php } ?>

                    </div>

                    <?php if ($signUpStep == "data") { ?>
                    
                        <div class="row">

                            <div class="col-12 mb-3 text-start">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="termsCheck" required>
                                    <label class="form-check-label" for="termsCheck">
                                        I agree to the <a data-bs-toggle="modal" data-bs-target="#termsModal" style="color: #19AFA5; text-decoration: none;">Terms and Conditions</a>.
                                    </label>
                                </div>
                            </div>

                            <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                                    <div class="modal-content border-0 shadow-lg">
                                    
                                        <div class="modal-header border-0" style="background-color: #19AFA5; color: white;">
                                            <div class="d-flex align-items-center">
                                                <img src="assets/images/logoSantoTomas.png" alt="Barangay Logo" style="width: 40px; height: 40px; margin-right: 10px;">
                                                <h5 class="modal-title mb-0" id="termsModalLabel">Terms and Conditions</h5>
                                            </div>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                    
                                        <div class="modal-body px-4 py-3" style="background-color: #f8f9fa; color: #333; font-size: 15px; line-height: 1.7;">
                                            <h5 class="fw-semibold text-center mb-3">Welcome to <span style="color: #19AFA5; white-space: nowrap;">San Antonio e-Desk</span></h5>
                                            <p class="text-center">Please review the following terms before proceeding with your registration.</p>
                                            <hr>
                                            <ul style="list-style-type: disc; padding-left: 20px;">
                                                <li>Your personal information will be used solely for official barangay transactions and records.</li>
                                                <li>Do not share your account credentials with anyone to protect your privacy.</li>
                                                <li>Any unauthorized or fraudulent use of the platform is prohibited and may lead to account suspension.</li>
                                                <li>The barangay reserves the right to modify these terms as necessary, with notice to users.</li>
                                                <li>For any inquiries, contact us at <a href="mailto:sanantonioputol@gmail.com" style="color: #19AFA5; text-decoration: none;">sanantonioputol@gmail.com</a>.</li>
                                            </ul>
                                            <hr>
                                            <p class="text-center fw-semibold mb-0">By continuing registration, you confirm that you have read and agree to these terms.</p>
                                        </div>

                                        <div class="modal-footer border-0 justify-content-center" style="background-color: #f8f9fa;">
                                            <button type="button" class="btn px-4" style="background-color: #19AFA5; color: white; border-radius: 12px;" data-bs-dismiss="modal">I Understand</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col text-center">
                                <button class="btn btn-primary registerButton mb-3" type="submit" name="register">Register</button>
                                <span class="pt-2" style="color: black;">Already have an account?</span> <a href="login.php" style="color: #19AFA5;">Login</a>
                            </div>

                        </div>

                    <?php } elseif ($signUpStep == "verification") { ?>
                    
                        <div class="row">
                            <div class="col text-center">
                                <button class="btn btn-primary verifyButton mb-3" type="submit" name="verify">Verify & Login</button>
                                <span style="color: black;">Didn’t get the code?</span>
                                <button type="submit" name="resend" formnovalidate class="btn btn-link p-0" style="color: #19AFA5; text-decoration: none;">Resend Code</button>
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
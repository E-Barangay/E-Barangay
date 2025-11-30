<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include("sharedAssets/connect.php");

session_start();

$loginStep = 'email';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'assets/phpmailer/src/Exception.php';
require 'assets/phpmailer/src/PHPMailer.php';    
require 'assets/phpmailer/src/SMTP.php';

if (isset($_POST['next'])) {
    $email = $_POST['email'];

    $email = str_replace('\'', '', $email);

    $emailCheckQuery = "SELECT * FROM users WHERE email = '$email'";
    $emailCheckResult = executeQuery($emailCheckQuery);

    if (mysqli_num_rows($emailCheckResult) > 0) {
        $user = mysqli_fetch_assoc($emailCheckResult);
        
        $restrictionEnd = $user['restrictionEnd'];
        $restrictionReason = $user['restrictionReason'];

        if ($user['isRestricted'] === 'Yes' && !empty($restrictionEnd)) {
            
            date_default_timezone_set('Asia/Manila');
            $currentTime = date('Y-m-d H:i:s');

            $restrictionEndTime = new DateTime($restrictionEnd, new DateTimeZone('Asia/Manila'));
            $currentDatetime = new DateTime($currentTime, new DateTimeZone('Asia/Manila'));

            if ($restrictionEndTime <= $currentDatetime) {
                $unrestrictQuery = "UPDATE users SET isRestricted = 'No', restrictionStart = NULL, restrictionEnd = NULL, restrictionReason = NULL WHERE email = '$email'";
                executeQuery($unrestrictQuery);
                
                $_SESSION['restriction_lifted'] = true;
                $_SESSION['email'] = $email;
                $loginStep = "existingPassword";
            } else {
                $_SESSION['warning'] = 'userRestricted';
                $loginStep = "email";
            }
        } elseif ($user['isNew'] === 'Yes') {
            $_SESSION['email'] = $email;

            $verificationCode = random_int(100000, 999999);
            $_SESSION['verificationCode'] = $verificationCode;
            $verificationCodeExpiry = date('Y-m-d H:i:s', time() + (5 * 60));
            $_SESSION['verificationCodeExpiry'] = $verificationCodeExpiry;

            $_SESSION['success'] = 'emailSent';
            $loginStep = "verification";

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
                                                    We received a request to verify your account for <strong>San Antonio e-Desk</strong>.
                                                </p>

                                                <p style="font-size:15px; color:#333;">Your One-Time Password (OTP) is:</p>

                                                <h2 style="text-align:center; letter-spacing:5px; font-size:32px; color:#19AFA5; margin:20px 0;">' . $verificationCode . '</h2>

                                                <p style="font-size:15px; color:#333;">
                                                    Please enter this code on the verification page to complete your process. This code will expire in <strong>5 minutes</strong> for your security.
                                                </p>

                                                <p style="font-size:15px; color:#333;">
                                                    If you didn’t request this verification, please ignore this email or contact our support team immediately.
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
        } elseif ($user['isNew'] === 'No' && isset($_SESSION['resetMode']) && $_SESSION['resetMode'] === true) {
            $_SESSION['email'] = $email;

            $verificationCode = random_int(100000, 999999);
            $_SESSION['verificationCode'] = $verificationCode;
            $verificationCodeExpiry = date('Y-m-d H:i:s', time() + (5 * 60));
            $_SESSION['verificationCodeExpiry'] = $verificationCodeExpiry;

            $_SESSION['success'] = 'resetVerificationSent';
            $loginStep = 'verification';

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
                $mail->Subject = "San Antonio e-Desk Password Reset Verification Code";
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
                                                    We received a request to reset your password for your <strong>San Antonio e-Desk</strong> account.
                                                </p>

                                                <p style="font-size:15px; color:#333;">Use the verification code below to continue resetting your password:</p>

                                                <h2 style="text-align:center; letter-spacing:5px; font-size:32px; color:#19AFA5; margin:20px 0;">' . $verificationCode . '</h2>

                                                <p style="font-size:15px; color:#333;">
                                                    Please enter this code on the password reset page to proceed. This code will expire in <strong>5 minutes</strong> for your security.
                                                </p>

                                                <p style="font-size:15px; color:#333;">
                                                    If you didn’t request a password reset, please ignore this email or contact our support team immediately.
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
        } else {
            $_SESSION['email'] = $email;
            
            $loginStep = 'existingPassword';
        }
    } else {
        $_SESSION['warning'] = 'notFoundEmail';
        
        $loginStep = 'email';
    }
}

if (isset($_POST['resetPassword'])) {
    $_SESSION['warning'] = 'enterEmailToReset';
    $_SESSION['resetMode'] = true;
    $loginStep = 'email';
}

if (isset($_POST['resend'])) {
    $email = $_SESSION['email'];

    $verificationCode = random_int(100000, 999999);
    $_SESSION['verificationCode'] = $verificationCode;
    $verificationCodeExpiry = date('Y-m-d H:i:s', time() + (5 * 60));
    $_SESSION['verificationCodeExpiry'] = $verificationCodeExpiry;

    $_SESSION['success'] = 'codeResent';
    $loginStep = "verification";

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
                                            We noticed you requested to resend your One-Time Password (OTP) for <strong>San Antonio e-Desk</strong>.
                                        </p>

                                        <p style="font-size:15px; color:#333;">Your new OTP is:</p>

                                        <h2 style="text-align:center; letter-spacing:5px; font-size:32px; color:#19AFA5; margin:20px 0;">' . $verificationCode . '</h2>

                                        <p style="font-size:15px; color:#333;">
                                            Please enter this code on the verification page to continue. This new code will expire in <strong>5 minutes</strong> for your security.
                                        </p>

                                        <p style="font-size:15px; color:#333;">
                                            If you did not request this new code, you can safely ignore this message — your account remains secure.
                                        </p>

                                        <p style="font-size:15px; color:#333;">Thank you,<br><strong>San Antonio e-Desk Team</strong></p>

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
    $email = $_SESSION['email'];
    $verificationCode = $_POST['verificationCode'];

    $verifyUserQuery = "SELECT * FROM users WHERE email = '$email'";
    $verifyUserResult = executeQuery($verifyUserQuery);
    $verifyUserRow = mysqli_fetch_assoc($verifyUserResult);

    if (trim((string)$verificationCode) === trim((string)$_SESSION['verificationCode'])) {
        $currentTime = date('Y-m-d H:i:s');
        if ($currentTime <= $_SESSION['verificationCodeExpiry']) {
            if ($verifyUserRow['isNew'] === 'No') {
                unset($_SESSION['resetMode']);
                $_SESSION['email'] = $email;

                $_SESSION['success'] = 'verifiedReset';
                $loginStep = 'newPassword';
            } else {
                $_SESSION['email'] = $email;
                
                $_SESSION['success'] = 'verifiedNewUser';
                $loginStep = 'notExistingPassword';
            }
        } else {
            $_SESSION['alert'] = 'verificationCodeExpired';
            $loginStep = 'verification';
        }
    } else {
        $_SESSION['alert'] = 'invalidVerificationCode';
        $loginStep = 'verification';
    }
}

if (isset($_POST['setPassword'])) {
    $email = $_SESSION['email'];
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    $isWeak = !preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^\w\s]|_).{8,}$/', $password);

    $userCheckQuery = "SELECT * FROM users WHERE email = '$email'";
    $userCheckResult = executeQuery($userCheckQuery);

    if (mysqli_num_rows($userCheckResult) > 0) {
        $userRow = mysqli_fetch_assoc($userCheckResult);

        if ($userRow['isNew'] === 'Yes') {  
            if ($isWeak) {
                $_SESSION['alert'] = 'weakPassword';
                $loginStep = 'notExistingPassword';
            } elseif ($password !== $confirmPassword) {
                $_SESSION['alert'] = 'mismatchPassword';
                $loginStep = 'notExistingPassword';
            } else {
                $password = str_replace("'", "", $password);

                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $updatePasswordQuery = "UPDATE users SET password = '$hashedPassword', isNew = 'No' WHERE email = '$email'";
                executeQuery($updatePasswordQuery);

                $userCheckResult = executeQuery($userCheckQuery);
                $user = mysqli_fetch_assoc($userCheckResult);

                $_SESSION['userID'] = $user['userID'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    header("Location: admin/index.php");
                } else {
                    $_SESSION['success'] = 'passwordCreated';

                    header("Location: profile.php");
                }
            }
        } elseif ($userRow['isNew'] === 'No') {
            if ($isWeak) {
                $_SESSION['alert'] = 'weakPassword';
                $loginStep = 'newPassword';
            } elseif ($password !== $confirmPassword) {
                $_SESSION['alert'] = 'mismatchPassword';
                $loginStep = 'newPassword';
            } else {
                $password = str_replace("'", "", $password);

                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $updatePasswordQuery = "UPDATE users SET password = '$hashedPassword', isNew = 'No' WHERE email = '$email'";
                executeQuery($updatePasswordQuery);

                $userCheckResult = executeQuery($userCheckQuery);
                $user = mysqli_fetch_assoc($userCheckResult);

                $_SESSION['userID'] = $user['userID'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    header("Location: admin/index.php");
                } else {
                    $_SESSION['success'] = 'passwordResetted';

                    header("Location: index.php");
                }
            }
        }
    }
}

if (isset($_POST['login'])) {
    // $conn = new mysqli("localhost", "u482770917_nnaes", "5-T79_Oo8Z", "u482770917_nnaes");

    // if ($conn->connect_error) {
    //     die("Connection failed: " . $conn->connect_error);
    // }

    $email = $_SESSION['email'];
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    $userCheckQuery = "SELECT * FROM users WHERE email = '$email'";
    $userCheckResult = executeQuery($userCheckQuery);

    if (mysqli_num_rows($userCheckResult) > 0) {
        $userRow = mysqli_fetch_assoc($userCheckResult);

        if ($userRow['isNew'] === 'No') {
            if (password_verify($password, $userRow['password'])) {
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

    <?php include("sharedAssets/loadingIndicator.php"); ?>

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
                            <?php if (isset($_SESSION['warning']) && $_SESSION['warning'] === 'userRestricted'): ?>
                                <div class="alert alert-warning" style="font-size: 14px; line-height: 1.4;"><i class="fa-solid fa-triangle-exclamation" style="margin-right:8px;"></i>Account restricted for about&nbsp;<strong><span id="countdown" style="white-space: nowrap;"></span></strong>&nbsp;due to <?php echo strtolower($restrictionReason); ?>.</div>
                                <?php unset($_SESSION['warning']); ?>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['warning']) && $_SESSION['warning'] === 'notFoundEmail'): ?>
                                <div class="alert alert-warning" style="font-size: 14px; line-height: 1.4;"><i class="fa-solid fa-triangle-exclamation" style="margin-right:8px;"></i>Email not found. Please sign up to create an account.</div>
                                <?php unset($_SESSION['warning']); ?>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['warning']) && $_SESSION['warning'] === 'enterEmailToReset'): ?>
                                <div class="alert alert-warning" style="font-size: 14px; line-height: 1.4;"><i class="fa-solid fa-triangle-exclamation" style="margin-right:8px;"></i>Please enter your email to reset your password.</div>
                                <?php unset($_SESSION['warning']); ?>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['success']) && $_SESSION['success'] === 'emailSent'): ?>
                                <div class="alert alert-success" style="font-size: 14px; line-height: 1.4;"><i class="fa-solid fa-circle-check" style="margin-right:8px;"></i>Verification code sent successfully! Please check your email to continue.</div>
                                <?php unset($_SESSION['success']); ?>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['success']) && $_SESSION['success'] === 'codeResent'): ?>
                                <div class="alert alert-success" style="font-size: 14px; line-height: 1.4;"><i class="fa-solid fa-circle-check" style="margin-right:8px;"></i>A new verification code has been sent to your email.</div>
                                <?php unset($_SESSION['success']); ?>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['success']) && $_SESSION['success'] === 'resetVerificationSent'): ?>
                                <div class="alert alert-success" style="font-size: 14px; line-height: 1.4;"><i class="fa-solid fa-circle-check" style="margin-right:8px;"></i>A verification code has been sent to your email to reset your password.</div>
                                <?php unset($_SESSION['success']); ?>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['success']) && $_SESSION['success'] === 'verifiedNewUser'): ?>
                                <div class="alert alert-success" style="font-size: 14px; line-height: 1.4;"><i class="fa-solid fa-circle-check" style="margin-right:8px;"></i>Your email has been successfully verified! Please create a password to complete your account setup.</div>
                                <?php unset($_SESSION['success']); ?>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['success']) && $_SESSION['success'] === 'verifiedReset'): ?>
                                <div class="alert alert-success" style="font-size: 14px; line-height: 1.4;"><i class="fa-solid fa-circle-check" style="margin-right:8px;"></i>Verification successful! Please create a new password for your account.</div>
                                <?php unset($_SESSION['success']); ?>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['alert']) && $_SESSION['alert'] === 'verificationCodeExpired'): ?>
                                <div class="alert alert-danger" style="font-size: 14px; line-height: 1.4;"><i class="fa-solid fa-circle-exclamation" style="margin-right:8px;"></i>Your verification code has expired. Please resend a new code.</div>
                                <?php unset($_SESSION['alert']); ?>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['alert']) && $_SESSION['alert'] === 'invalidVerificationCode'): ?>
                                <div class="alert alert-danger" style="font-size: 14px; line-height: 1.4;"><i class="fa-solid fa-circle-exclamation" style="margin-right:8px;"></i>Invalid verification code.</div>
                                <?php unset($_SESSION['alert']); ?>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['alert']) && $_SESSION['alert'] === 'weakPassword'): ?>
                                <div class="alert alert-danger" style="font-size: 14px; line-height: 1.4;"><i class="fa-solid fa-circle-exclamation" style="margin-right:8px;"></i>Oops! Password must be 8+ characters with uppercase, lowercase, number, and symbol.</div>
                                <?php unset($_SESSION['alert']); ?>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['alert']) && $_SESSION['alert'] === 'mismatchPassword'): ?>
                                <div class="alert alert-danger" style="font-size: 14px; line-height: 1.4;"><i class="fa-solid fa-circle-exclamation" style="margin-right:8px;"></i>Passwords do not match.</div>
                                <?php unset($_SESSION['alert']); ?>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['alert']) && $_SESSION['alert'] === 'invalidPassword'): ?>
                                <div class="alert alert-danger" style="font-size: 14px; line-height: 1.4;"><i class="fa-solid fa-circle-exclamation" style="margin-right:8px;"></i>Invalid Password.</div>
                                <?php unset($_SESSION['alert']); ?>
                            <?php endif; ?>
                        </div>

                        <?php if ($loginStep == "email") { ?>

                            <div class="col">
                                <div class="form-floating">
                                    <input type="email" class="form-control" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" id="emailInput" name="email" placeholder="Email address" required>
                                    <label for="emailInput">Email address</label>
                                </div>
                            </div>

                        <?php } elseif ($loginStep == "verification") { ?>

                            <div class="col">
                                <div class="form-floating">
                                    <input type="number" class="form-control" value="<?php echo isset($_POST['verificationCode']) ? htmlspecialchars($_POST['verificationCode']) : ''; ?>" id="verificationInput" name="verificationCode" placeholder="Enter 6-digit verification code" inputmode="numeric" pattern="[0-9]*" oninput="if(this.value.length > 6) this.value = this.value.slice(0, 6);" min="0" onkeydown="return !['e','E','-','+','.',','].includes(event.key)" required>
                                    <label for="verificationInput">Enter 6-digit verification code</label>
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

                        <?php } elseif ($loginStep == "newPassword") { ?>

                            <div class="col-12 mb-3">
                                <div class="form-floating">
                                    <input type="password" class="form-control" value="<?php echo isset($_POST['password']) ? htmlspecialchars($_POST['password']) : ''; ?>" id="passwordInput" name="password" placeholder="New Password" required>
                                    <label for="passwordInput">New Password</label>
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
                                <button class="btn btn-primary nextButton mb-3" type="submit" name="next">Next</button>
                                <span style="color: black;">Need an account?</span> <a href="signUp.php" style="color: #19AFA5;">Sign Up</a>
                            </div>
                        </div>

                    <?php } elseif ($loginStep == "verification") { ?>

                        <div class="row">
                            <div class="col text-center">
                                <button class="btn btn-primary verifyButton mb-3" type="submit" name="verify">Verify</button>
                                <span style="color: black;">Didn’t get the code?</span>
                                <button type="submit" name="resend" formnovalidate class="btn btn-link p-0" style="color: #19AFA5; text-decoration: none;">Resend Code</button>
                            </div>
                        </div>

                    <?php } elseif ($loginStep == "existingPassword") { ?>

                        <div class="row">
                            <div class="col text-center">
                                <button class="btn btn-primary loginButton mb-3" type="submit" name="login">Login</button>
                                <span style="color: black;">Having trouble?</span> <button type="button" class="btn btn-link resetPasswordButton p-0" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">Reset Password</button>
                            </div>
                        </div>

                        <div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header" style="background-color: #19AFA5; color: white;">
                                        <h1 class="modal-title fs-5" id="resetPasswordModalLabel">Reset Password</h1>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body text-center">
                                        <p class="mb-2">Are you sure you want to reset your password for:</p>
                                        <div style="font-size: 16px; color: #19AFA5;"><?php echo $email ?></div>
                                    </div>

                                    <div class="modal-footer border-0">
                                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" formnovalidate name="resetPassword" class="btn btn-primary confirmResetButton px-4">Confirm</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php } elseif ($loginStep == "notExistingPassword" || $loginStep == "newPassword") { ?>

                        <div class="row">
                            <div class="col text-center">
                                <button class="btn btn-primary setPasswordButton mb-3" type="submit" name="setPassword">Set Password & Login</button>
                            </div>
                        </div>

                    <?php } ?>
                    
                </form>

            </div>
        </div>
    </div>

    <?php if (isset($restrictionEnd)): ?>
        <script>
            var restrictionEnd = new Date("<?php echo $restrictionEnd ?>").getTime();
            var countdownElement = document.getElementById('countdown');
            var alertBox = countdownElement.closest('.alert');

            function updateCountdown() {
                var now = new Date().getTime();
                var distance = restrictionEnd - now;

                if (distance < 0) {
                    clearInterval(timer);

                    alertBox.classList.remove('alert-warning');
                    alertBox.classList.add('alert-success');
                    alertBox.innerHTML = `
                        <i class="fa-solid fa-circle-check" style="margin-right:8px;"></i>
                        Account restriction lifted. Click <strong>Next</strong> to proceed.
                    `;
                    return;
                }

                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                var timeString = "";
                if (days > 0) {
                    timeString += days + "d ";
                }
                if (hours > 0 || days > 0) {
                    timeString += hours + "h ";
                }
                if (minutes > 0 || hours > 0 || days > 0) {
                    timeString += minutes + "m ";
                }

                timeString += seconds + "s";
                countdownElement.innerHTML = timeString;
            }

            updateCountdown();
            var timer = setInterval(updateCountdown, 1000);
        </script>
    <?php endif; ?>

    <script src="assets/js/loadingIndicator/script.js"></script>

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
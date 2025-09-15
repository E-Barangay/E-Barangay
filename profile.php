<?php

include("sharedAssets/connect.php");

session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
}

$userID = $_SESSION['userID'];

$userQuery = "SELECT * FROM users 
            LEFT JOIN userInfo ON users.userID = userInfo.userID 
            LEFT JOIN addresses ON userInfo.userID = addresses.userInfoID  
            LEFT JOIN permanentAddresses ON userInfo.userInfoID = permanentAddresses.userInfoID
            WHERE users.userID = $userID";
$userResult = executeQuery($userQuery);

$userDataRow = mysqli_fetch_assoc($userResult); {
    $firstName = $userDataRow['firstName'];
    $middleName = $userDataRow['middleName'];
    $lastName = $userDataRow['lastName'];
    $suffix = $userDataRow['suffix'];
    $gender = $userDataRow['gender'];
    $birthDate = $userDataRow['birthDate'];
    $birthPlace = $userDataRow['birthPlace'];
    $bloodType = $userDataRow['bloodType'];
    $residencyType = $userDataRow['residencyType'];
    $civilStatus = $userDataRow['civilStatus'];
    $citizenship = $userDataRow['citizenship'];
    $occupation = $userDataRow['occupation'];
    $remarks = $userDataRow['remarks'];
    $phoneNumber = $userDataRow['phoneNumber'];
    $email = $userDataRow['email'];
    $blockLotNo = $userDataRow['houseNo'];
    $phase = $userDataRow['phase']; 
    $subdivisionName = $userDataRow['subdivisionName'];
    $purok = $userDataRow['purok'];
    $streetName = $userDataRow['streetName'];
    $barangayName = $userDataRow['barangayName'];
    $cityName = $userDataRow['cityName'];
    $provinceName = $userDataRow['provinceName'];
    $permanentBlockLotNo = $userDataRow['permanentHouseNo'];
    $permanentPhase = $userDataRow['permanentPhase']; 
    $permanentSubdivisionName = $userDataRow['permanentSubdivisionName'];
    $permanentPurok = $userDataRow['permanentPurok'];
    $permanentStreetName = $userDataRow['permanentStreetName'];
    $permanentBarangayName = $userDataRow['permanentBarangayName'];
    $permanentCityName = $userDataRow['permanentCityName'];
    $permanentProvinceName = $userDataRow['permanentProvinceName'];
};

// if (isset($_POST['saveBtn'])) {
//     $firstName = $_POST['firstName'];
//     $middleName = $_POST['middleName'];
//     $lastName = $_POST['lastName'];
//     $suffix = $_POST['suffix'] ?? '';
//     $gender = $_POST['gender'];
//     $birthDate = $_POST['birthDate'];
//     $phoneNumber = $_POST['phoneNumber'];
//     $email = $_POST['email'];
//     $provinceName = $_POST['provinceName'];
//     $cityName = $_POST['cityName'];
//     $barangayName = $_POST['barangayName'];
//     $streetName = $_POST['streetName'];
//     $houseNo = $_POST['houseNo'] ?? '';
//     $phase = $_POST['phase'] ?? '';
//     $subdivisionName = $_POST['subdivisionName'] ?? '';
//     $purok = $_POST['purok'] ?? '';

// }
    // ✅ userInfoID always exists now
    // $userInfoID = $data['userInfoID'];

    // --- UPDATE userInfo ---
    // executeQuery("
    //     UPDATE userInfo 
    //     SET firstName='$firstName',
    //         middleName='$middleName',
    //         lastName='$lastName',
    //         suffix='$suffix',
    //         gender='$gender',
    //         birthDate='$birthDate'
    //     WHERE userInfoID=$userInfoID
    // ");

    // --- Handle profile picture upload ---
    // if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] === UPLOAD_ERR_OK) {
    //     $uploadDir = "uploads/profile/";
    //     $ext = pathinfo($_FILES['profilePicture']['name'], PATHINFO_EXTENSION);
    //     $newFileName = uniqid("pp_", true) . "." . strtolower($ext);
    //     $targetPath = $uploadDir . $newFileName;

    //     // Delete old if exists
    //     if (!empty($data['profilePicture']) && file_exists($uploadDir . $data['profilePicture'])) {
    //         unlink($uploadDir . $data['profilePicture']);
    //     }

    //     if (move_uploaded_file($_FILES['profilePicture']['tmp_name'], $targetPath)) {
    //         executeQuery("UPDATE userInfo SET profilePicture = '$newFileName' WHERE userInfoID = $userInfoID");
    //     }
    // }

    // --- UPDATE users (contact info) ---
    // executeQuery("
    //     UPDATE users 
    //     SET phoneNumber='$phoneNumber',
    //         email='$email'
    //     WHERE userID=$userID
    // ");

    // --- UPDATE or INSERT addresses ---
//     $existingAddress = executeQuery("SELECT * FROM addresses WHERE userInfoID = $userInfoID");
//     if (mysqli_num_rows($existingAddress) > 0) {
//         executeQuery("
//             UPDATE addresses 
//             SET provinceName='$provinceName',
//                 cityName='$cityName',
//                 barangayName='$barangayName',
//                 streetName='$streetName',
//                 houseNo='$houseNo',
//                 phase='$phase',
//                 subdivisionName='$subdivisionName',
//                 purok='$purok'
//             WHERE userInfoID=$userInfoID
//         ");
//     } else {
//         executeQuery("
//             INSERT INTO addresses (userInfoID, provinceName, cityName, barangayName, streetName, houseNo, phase, subdivisionName, purok)
//             VALUES ($userInfoID, '$provinceName', '$cityName', '$barangayName', '$streetName', '$houseNo', '$phase', '$subdivisionName', '$purok')
//         ");
//     }

//     header("Location: profile.php");
//     exit();
// }
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Barangay | Profile</title>

    <!-- Icon -->
    <link rel="icon" href="assets/images/logoSanAntonio.png">

    <!-- Style Sheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/navbar/style.css">
    <link rel="stylesheet" href="assets/css/profile/style.css">
    <link rel="stylesheet" href="assets/css/footer/style.css">
</head>

<body data-bs-theme="light">

    <?php
        if (isset($_SESSION['userID'])) {
            include("sharedAssets/navbarLoggedIn.php");
        } else {
            include("sharedAssets/navbar.php");
        }
    ?>

    <div class="container pt-3">
        <div class="row">
            <div class="col">
                <div class="card profileCard p-5" style="width:100%; height: 100%;">
                    <div class="row pb-3">
                        <div class="col-lg-11 col-12 d-flex flex-column flex-md-row align-items-center text-center text-md-start">
                            <img src="assets/images/defaultProfile.png" class="profilePicture" alt="Profile Picture">
                            <div class="d-flex flex-column ps-0 ps-md-4 pt-3 pt-md-0">          
                                <span class="fullName">
                                    <?php
                                        $middleInitial = !empty($middleName) ? strtoupper($middleName[0]) . "." : ""; 
                                        echo $firstName . " " . $middleInitial . " " . $lastName; 
                                    ?>
                                </span>
                                <span class="email text-muted"><?php echo $email ?></span>
                            </div>
                        </div>
                        <div class="col-lg-1 col-12 d-flex justify-content-center justify-content-md-end align-items-center pt-3 pt-md-0">
                            <button class="btn btn-primary editButton">Edit</button>
                            <button class="btn btn-primary cancelButton d-none">Cancel</button>
                            <button class="btn btn-primary saveButton d-none">Save</button>
                        </div>
                    </div>

                    <hr>
                    
                    <form method="POST">

                        <div class="row pt-3">

                            <div class="col-12 mb-3">
                                <div class="personalInfo"> Personal Information</div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="firstNameInput" name="firstName" value="<?php echo $firstName ?>" placeholder="First Name" required>
                                    <label for="firstNameInput">First Name</label>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="middleNameInput" name="middleName" value="<?php echo $middleName ?>" placeholder="Middle Name">
                                    <label for="middleNameInput">Middle Name</label>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="lastNameInput" name="lastName" value="<?php echo $lastName ?>" placeholder="Last Name">
                                    <label for="lastNameInput">Last Name</label>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="suffixInput" name="suffix" value="<?php echo $suffix ?>" placeholder="Suffix">
                                    <label for="suffixInput">Suffix</label>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="genderInput" name="gender" value="<?php echo $gender ?>" placeholder="Suffix">
                                    <label for="genderInput">Gender</label>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-3 col-3 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="ageInput" name="age" placeholder="Age"
                                        value="<?php $birthDateObj = new DateTime($birthDate);
                                                    $today = new DateTime();
                                                    $age = $today->diff($birthDateObj)->y;
                                                    echo $age . " years old";
                                                ?>">
                                    <label for="ageInput">Age</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-4 col-9 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="birthDateInput" name="birthDate" value="<?php echo date("F j, Y", strtotime($birthDate)); ?>" placeholder="Date of Birth">
                                    <label for="birthDateInput">Date of Birth</label>
                                </div>
                            </div>

                            <div class="col-lg-5 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="birthPlaceInput" name="age" value="<?php echo $birthPlace ?>" placeholder="Place of Birth">
                                    <label for="birthPlaceInput">Place of Birth</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-5 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="bloodType" name="bloodType" value="<?php echo $bloodType ?>" placeholder="Blood Type">
                                    <label for="bloodType">Blood Type</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-7 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="recidencyType" name="recidencyType" value="<?php echo $residencyType ?>" placeholder="Type of Residency">
                                    <label for="recidencyType">Type of Residency</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="civilStatus" name="civilStatus" value="<?php echo $civilStatus ?>" placeholder="Age">
                                    <label for="civilStatus">Civil Status</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="citizenship" name="citizenship" value="<?php echo $citizenship ?>" placeholder="Citizenship">
                                    <label for="citizenship">Citizenship</label>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-5 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="occupation" name="occupation" value="<?php echo $occupation ?>" placeholder="Occupation">
                                    <label for="occupation">Occupation</label>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-5 col-12 mb-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="remarks" name="remarks" value="<?php echo $remarks ?>" placeholder="Remarks">
                                    <label for="remarks">Remarks</label>
                                </div>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-12 mb-3">
                                <div class="contactInfo">
                                    Contact Information
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-4 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" value="<?php echo $phoneNumber ?>" placeholder="Phone Number">
                                    <label for="phoneNumber">Phone Number</label>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-8 col-12 mb-4">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $email ?>" placeholder="Email Address">
                                    <label for="email">Email Address</label>
                                </div>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-12 mb-3">
                                <div class="addressInfo">
                                    Address
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-4 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="blockLotNo" name="blockLotNo" value="<?php echo $blockLotNo ?>" placeholder="Block & Lot No.">
                                    <label for="blockLotNo">Block & Lot No.</label>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-4 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="phase" name="phase" value="<?php echo $phase ?>" placeholder="Phase">
                                    <label for="phase">Phase</label>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="subdivision" name="subdivision" value="<?php echo $subdivisionName ?>" placeholder="Subdivision">
                                    <label for="subdivision">Subdivision</label>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="purok" name="purok" value="<?php echo $purok ?>" placeholder="Purok">
                                    <label for="purok">Purok</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-4 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="street" name="street" value="<?php echo $streetName ?>" placeholder="Street">
                                    <label for="street">Street</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-4 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="barangay" name="barangay" value="<?php echo $barangayName ?>" placeholder="Barangay">
                                    <label for="barangay">Barangay</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="city" name="city" value="<?php echo $cityName ?>" placeholder="City">
                                    <label for="city">City</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="province" name="province" value="<?php echo $provinceName ?>" placeholder="Province">
                                    <label for="province">Province</label>
                                </div>
                            </div>
                        
                        </div>

                        <div class="row">

                            <div class="col-12 mb-3">
                                <div class="permanentAddressInfo">
                                    Permanent Address
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-4 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="permanentBlockLotNo" name="permanentBlockLotNo" value="<?php echo $permanentBlockLotNo ?>" placeholder="Block & Lot No.">
                                    <label for="permanentBlockLotNo">Block & Lot No.</label>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-4 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="permanentPhase" name="permanentPhase" value="<?php echo $permanentPhase ?>" placeholder="Phase">
                                    <label for="permanentPhase">Phase</label>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="permanentSubdivisionName" name="permanentSubdivisionName" value="<?php echo $permanentSubdivisionName ?>" placeholder="Subdivision">
                                    <label for="permanentSubdivisionName">Subdivision</label>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="permanentPurok" name="permanentPurok" value="<?php echo $permanentPurok ?>" placeholder="Purok">
                                    <label for="permanentPurok">Purok</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-4 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="permanentStreet" name="permanentStreet" value="<?php echo $permanentStreetName ?>" placeholder="Street">
                                    <label for="permanentStreet">Street</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-4 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="permanentBarangay" name="permanentBarangay" value="<?php echo $permanentBarangayName ?>" placeholder="Barangay">
                                    <label for="permanentBarangay">Barangay</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="permanentCity" name="permanentCity" value="<?php echo $permanentCityName ?>" placeholder="City">
                                    <label for="permanentCity">City</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="permanentProvince" name="permanentProvince" value="<?php echo $permanentProvinceName ?>" placeholder="Province">
                                    <label for="permanentProvince">Province</label>
                                </div>
                            </div>
                        
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <?php include("sharedAssets/footer.php") ?>

    <script>
        const inputs = document.querySelectorAll('.form-control');
        const btnDesktop = document.getElementById('editBtnDesktop');
        const btnMobile = document.getElementById('editBtnMobile');
        const fileInput = document.getElementById('profilePictureInput');
        let isEdit = false;

        function handleButtonClick(e) {
            if (!isEdit) {
                e.preventDefault(); // Stop submission on first click
                inputs.forEach(input => input.removeAttribute('disabled'));

                // Show file input for profile picture
                if (fileInput) {
                    fileInput.classList.remove('d-none');
                }

                // Change both buttons to "Save"
                [btnDesktop, btnMobile].forEach(button => {
                    if (button) {
                        button.textContent = 'Save';
                        button.classList.replace('btn-primary', 'btn-success');
                    }
                });

                isEdit = true; // Next click will submit
            }
            // else → allow form submission
        }

        if (btnDesktop) {
            btnDesktop.addEventListener('click', handleButtonClick);
        }

        if (btnMobile) {
            btnMobile.addEventListener('click', handleButtonClick);
        }

        // Optional: live preview of new profile picture
        if (fileInput) {
            fileInput.addEventListener('change', function (e) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    document.querySelector('.profile-preview').src = event.target.result;
                };
                reader.readAsDataURL(e.target.files[0]);
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
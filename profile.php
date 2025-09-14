<?php

include("sharedAssets/connect.php");

session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
}

$userID = $_SESSION['userID'];

// ✅ Always join userInfo and addresses since they exist at signup
// $getUserQuery = "
//     SELECT 
//         u.*,
//         ui.userInfoID, ui.firstName, ui.middleName, ui.lastName, ui.suffix,
//         ui.gender, ui.age, ui.birthDate, ui.birthPlace, ui.profilePicture,
//         ui.residencyType, ui.lengthOfStay, ui.civilStatus, ui.citizenship, ui.occupation,
//         a.addressID, a.cityName, a.provinceName, a.barangayName, a.streetName,
//         a.houseNo, a.phase, a.subdivisionName, a.purok
//     FROM users u
//     JOIN userInfo ui ON u.userInfoID = ui.userInfoID
//     LEFT JOIN addresses a ON ui.userInfoID = a.userInfoID
//     WHERE u.userID = $userID
// ";

// $result = executeQuery($getUserQuery);
// $data = mysqli_fetch_assoc($result);

// if (isset($_POST['saveBtn'])) {
//     $firstName = $_POST['firstName'];
//     $middleName = $_POST['middleName'];
//     $lastName = $_POST['lastName'];
//     $suffix = $_POST['suffix'] ?? '';
//     $gender = $_POST['gender'];
//     $birthDate = $_POST['birthDate'];
//     $phoneNumber = $_POST['phoneNumber'];
//     $email = $_POST['email'];

    // Address fields
    // $provinceName = $_POST['provinceName'];
    // $cityName = $_POST['cityName'];
    // $barangayName = $_POST['barangayName'];
    // $streetName = $_POST['streetName'];
    // $houseNo = $_POST['houseNo'] ?? '';
    // $phase = $_POST['phase'] ?? '';
    // $subdivisionName = $_POST['subdivisionName'] ?? '';
    // $purok = $_POST['purok'] ?? '';

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
                        <div class="col-lg-11 col-12 d-flex flex-row align-items-center">
                            <img src="assets/images/defaultProfile.png" class="profilePicture" alt="Profile Picture">
                            <div class="d-flex flex-column ps-4">
                                <span class="fullName">John G. Doe</span>
                                <span class="email text-muted">johndoe@gmail.com</span>
                            </div>
                        </div>
                        <div class="col-lg-1 col-12  d-flex justify-content-end align-items-center">
                            <button class="btn btn-primary editButton">Edit</button>
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
                                    <input type="text" class="form-control" id="firstNameInput" name="firstName" placeholder="First Name" required>
                                    <label for="firstNameInput">First Name</label>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="middleNameInput" name="middleName" placeholder="Middle Name">
                                    <label for="middleNameInput">Middle Name</label>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="lastNameInput" name="lastName" placeholder="Last Name">
                                    <label for="lastNameInput">Last Name</label>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="suffixInput" name="suffix" placeholder="Suffix">
                                    <label for="suffixInput">Suffix</label>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-4 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="genderInput" name="suffix" placeholder="Suffix">
                                    <label for="genderInput">Gender</label>
                                </div>
                            </div>

                            <div class="col-lg-1 col-md-2 col-3 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="ageInput" name="age" placeholder="Age">
                                    <label for="ageInput">Age</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-4 col-9 mb-3">
                                <div class="form-floating">
                                    <input type="date" class="form-control" id="birthDateInput" name="birthDate" placeholder="Date of Birth">
                                    <label for="birthDateInput">Date of Birth</label>
                                </div>
                            </div>

                            <div class="col-lg-6 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="birthPlaceInput" name="age" placeholder="Place of Birth">
                                    <label for="birthPlaceInput">Place of Birth</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-5 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="bloodType" name="bloodType" placeholder="Blood Type">
                                    <label for="bloodType">Blood Type</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-7 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="recidencyType" name="recidencyType" placeholder="Type of Residency">
                                    <label for="recidencyType">Type of Residency</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="civilStatus" name="civilStatus" placeholder="Age">
                                    <label for="civilStatus">Civil Status</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="citizenship" name="citizenship" placeholder="Citizenship">
                                    <label for="citizenship">Citizenship</label>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-5 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="occupation" name="occupation" placeholder="Occupation">
                                    <label for="occupation">Occupation</label>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-5 col-12 mb-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="remarks" name="remarks" placeholder="Remarks">
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
                                    <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" placeholder="Phone Number">
                                    <label for="phoneNumber">Phone Number</label>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-8 col-12 mb-4">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email Address">
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
                                    <input type="text" class="form-control" id="blockLotNo" name="blockLotNo" placeholder="Block & Lot No.">
                                    <label for="blockLotNo">Block & Lot No.</label>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-4 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="phase" name="phase" placeholder="Phase">
                                    <label for="phase">Phase</label>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="subdivision" name="subdivision" placeholder="Subdivision">
                                    <label for="subdivision">Subdivision</label>
                                </div>
                            </div>

                            <div class="col-lg-4 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="street" name="street" placeholder="Street">
                                    <label for="street">Street</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="barangay" name="barangay" placeholder="Barangay">
                                    <label for="barangay">Barangay</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="city" name="city" placeholder="City">
                                    <label for="city">City</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="province" name="province" placeholder="Province">
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
                                    <input type="text" class="form-control" id="permanentBlockLotNo" name="permanentBlockLotNo" placeholder="Block & Lot No.">
                                    <label for="permanentBlockLotNo">Block & Lot No.</label>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-4 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="permanentPhase" name="permanentPhase" placeholder="Phase">
                                    <label for="permanentPhase">Phase</label>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="permanentSubdivision" name="permanentSubdivision" placeholder="Subdivision">
                                    <label for="permanentSubdivision">Subdivision</label>
                                </div>
                            </div>

                            <div class="col-lg-4 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="permanentStreet" name="permanentStreet" placeholder="Street">
                                    <label for="permanentStreet">Street</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="permanentBarangay" name="permanentBarangay" placeholder="Barangay">
                                    <label for="permanentBarangay">Barangay</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="permanentCity" name="permanentCity" placeholder="City">
                                    <label for="permanentCity">City</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="permanentProvince" name="permanentProvince" placeholder="Province">
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
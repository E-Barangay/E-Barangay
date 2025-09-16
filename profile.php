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

$userDataRow = mysqli_fetch_assoc($userResult);

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

if (isset($_POST['saveButton'])) {
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $suffix = $_POST['suffix'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $birthDate = $_POST['birthDate'];
    $birthPlace = $_POST['birthPlace'];
    $bloodType = $_POST['bloodType'];
    $residencyType = $_POST['recidencyType'];
    $civilStatus = $_POST['civilStatus'];
    $citizenship = $_POST['citizenship'];
    $occupation = $_POST['occupation'];
    $remarks = $_POST['remarks'];

    $phoneNumber = $_POST['phoneNumber'];
    $email = $_POST['email'];

    $blockLotNo = $_POST['houseNo'];
    $phase = $_POST['phase']; 
    $subdivisionName = $_POST['subdivisionName'];
    $purok = $_POST['purok'];
    $streetName = $_POST['streetName'];
    $barangayName = $_POST['barangayName'];
    $cityName = $_POST['cityName'];
    $provinceName = $_POST['provinceName'];

    $permanentBlockLotNo = $_POST['permanentHouseNo'];
    $permanentPhase = $_POST['permanentPhase']; 
    $permanentSubdivisionName = $_POST['permanentSubdivisionName'];
    $permanentPurok = $_POST['permanentPurok'];
    $permanentStreetName = $_POST['permanentStreetName'];
    $permanentBarangayName = $_POST['permanentBarangayName'];
    $permanentCityName = $_POST['permanentCityName'];
    $permanentProvinceName = $_POST['permanentProvinceName'];

    $updateUserInfoQuery = "UPDATE userInfo SET firstName = '$firstName', middleName = '$middleName', lastName = '$lastName', 
                        suffix = '$suffix', age = '$age', gender = '$gender', birthDate = '$birthDate', birthPlace = '$birthPlace',
                        bloodType = '$bloodType', residencyType = '$residencyType', civilStatus = '$civilStatus', citizenship = '$citizenship',
                        occupation = '$occupation', remarks = '$remarks' WHERE userID = $userID;";
    $updateUserInfoResult = executeQuery($updateUserInfoQuery);

    $updateUserContactQuery = "UPDATE users SET phoneNumber = '$phoneNumber', email = '$email' WHERE userID = $userID;";
    $updateUserInfoResult = executeQuery($updateUserInfoQuery);

    $updateAddressQuery = "UPDATE addresses SET houseNo = '$blockLotNo', phase = '$phase', subdivisionName = '$subdivisionName',
                        purok = '$purok', streetName = '$streetName', barangayName = '$barangayName', cityName = '$cityName',
                        provinceName = '$provinceName' WHERE userInfoID = $userID;";
    $updateAddressResult = executeQuery($updateAddressQuery);

    $updatePermanentAddressQuery = "UPDATE permanentAddresses SET permanentHouseNo = '$permanentBlockLotNo', permanentPhase = '$permanentPhase', 
                        permanentSubdivisionName = '$permanentSubdivisionName', permanentPurok = '$permanentPurok', 
                        permanentStreetName = '$permanentStreetName', permanentBarangayName = '$permanentBarangayName', 
                        permanentCityName = '$permanentCityName', permanentProvinceName = '$permanentProvinceName' 
                        WHERE userInfoID = $userID;";
    $updatePermanentAddressResult = executeQuery($updatePermanentAddressQuery);
}
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
                    
    <form method="POST">
        <div class="container pt-3">
            <div class="row">
                <div class="col">
                    <div class="card profileCard p-5" style="width:100%; height: 100%;">
                        <div class="row pb-3">
                            <div class="col-lg-11 col-12 d-flex flex-column flex-md-row align-items-center text-center text-md-start">
                                <img src="assets/images/defaultProfile.png" class="profilePicture" alt="Profile Picture">
                                <input type="file" name="profilePicture" class="form-control mt-2 d-none" id="profilePictureInput" accept="image/*">
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
                                <button class="btn btn-primary editButton" id="editButton" type="button">Edit</button>
                                <button class="btn btn-secondary cancelButton d-none me-2" id="cancelButton" type="button">Cancel</button>
                                <button class="btn btn-success saveButton d-none" id="saveButton" type="submit" name="saveButton">Save</button>
                            </div>
                        </div>

                        <hr>

                        <div class="row pt-3">

                            <div class="col-12 mb-3">
                                <div class="personalInfo"> Personal Information</div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="firstNameInput" name="firstName" value="<?php echo $firstName ?>" placeholder="First Name" disabled>
                                    <label for="firstNameInput">First Name</label>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="middleNameInput" name="middleName" value="<?php echo $middleName ?>" placeholder="Middle Name" disabled>
                                    <label for="middleNameInput">Middle Name</label>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="lastNameInput" name="lastName" value="<?php echo $lastName ?>" placeholder="Last Name" disabled>
                                    <label for="lastNameInput">Last Name</label>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="suffix" name="suffix" value="<?php echo $suffix ?>" placeholder="Suffix" disabled>
                                    <label for="suffix">Suffix</label>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="gender" name="gender" value="<?php echo $gender ?>" placeholder="Suffix" disabled>
                                    <label for="gender">Gender</label>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-3 col-3 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="age" name="age" placeholder="Age" disabled
                                        value="<?php $birthDateObj = new DateTime($birthDate);
                                                    $today = new DateTime();
                                                    $age = $today->diff($birthDateObj)->y;
                                                    echo $age . " years old";
                                                ?>">
                                    <label for="age">Age</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-4 col-9 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="birthDate" name="birthDate" value="<?php echo date("F j, Y", strtotime($birthDate)); ?>" placeholder="Date of Birth" disabled>
                                    <label for="birthDate">Date of Birth</label>
                                </div>
                            </div>

                            <div class="col-lg-5 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="birthPlace" name="birthPlace" value="<?php echo $birthPlace ?>" placeholder="Place of Birth" disabled>
                                    <label for="birthPlace">Place of Birth</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-5 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="bloodType" name="bloodType" value="<?php echo $bloodType ?>" placeholder="Blood Type" disabled>
                                    <label for="bloodType">Blood Type</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-7 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="recidencyType" name="recidencyType" value="<?php echo $residencyType ?>" placeholder="Type of Residency" disabled>
                                    <label for="recidencyType">Type of Residency</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="civilStatus" name="civilStatus" value="<?php echo $civilStatus ?>" placeholder="Civil Status" disabled>
                                    <label for="civilStatus">Civil Status</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="citizenship" name="citizenship" value="<?php echo $citizenship ?>" placeholder="Citizenship" disabled>
                                    <label for="citizenship">Citizenship</label>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-5 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="occupation" name="occupation" value="<?php echo $occupation ?>" placeholder="Occupation" disabled>
                                    <label for="occupation">Occupation</label>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-5 col-12 mb-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="remarks" name="remarks" value="<?php echo $remarks ?>" placeholder="Remarks" disabled>
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
                                    <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" value="<?php echo $phoneNumber ?>" placeholder="Phone Number" disabled>
                                    <label for="phoneNumber">Phone Number</label>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-8 col-12 mb-4">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $email ?>" placeholder="Email Address" disabled>
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
                                    <input type="text" class="form-control" id="blockLotNo" name="houseNo" value="<?php echo $blockLotNo ?>" placeholder="Block & Lot No." disabled>
                                    <label for="blockLotNo">Block & Lot No.</label>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-4 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="phase" name="phase" value="<?php echo $phase ?>" placeholder="Phase" disabled>
                                    <label for="phase">Phase</label>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="subdivision" name="subdivision" value="<?php echo $subdivisionName ?>" placeholder="Subdivision" disabled>
                                    <label for="subdivision">Subdivision</label>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="purok" name="purok" value="<?php echo $purok ?>" placeholder="Purok" disabled>
                                    <label for="purok">Purok</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-4 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="street" name="street" value="<?php echo $streetName ?>" placeholder="Street" disabled>
                                    <label for="street">Street</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-4 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="barangay" name="barangay" value="<?php echo $barangayName ?>" placeholder="Barangay" disabled>
                                    <label for="barangay">Barangay</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="city" name="city" value="<?php echo $cityName ?>" placeholder="City" disabled>
                                    <label for="city">City</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="province" name="province" value="<?php echo $provinceName ?>" placeholder="Province" disabled>
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
                                    <input type="text" class="form-control" id="permanentBlockLotNo" name="permanentBlockLotNo" value="<?php echo $permanentBlockLotNo ?>" placeholder="Block & Lot No." disabled>
                                    <label for="permanentBlockLotNo">Block & Lot No.</label>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-4 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="permanentPhase" name="permanentPhase" value="<?php echo $permanentPhase ?>" placeholder="Phase" disabled>
                                    <label for="permanentPhase">Phase</label>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="permanentSubdivisionName" name="permanentSubdivisionName" value="<?php echo $permanentSubdivisionName ?>" placeholder="Subdivision" disabled>
                                    <label for="permanentSubdivisionName">Subdivision</label>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="permanentPurok" name="permanentPurok" value="<?php echo $permanentPurok ?>" placeholder="Purok" disabled>
                                    <label for="permanentPurok">Purok</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-4 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="permanentStreet" name="permanentStreet" value="<?php echo $permanentStreetName ?>" placeholder="Street" disabled>
                                    <label for="permanentStreet">Street</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-4 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="permanentBarangay" name="permanentBarangay" value="<?php echo $permanentBarangayName ?>" placeholder="Barangay" disabled>
                                    <label for="permanentBarangay">Barangay</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="permanentCity" name="permanentCity" value="<?php echo $permanentCityName ?>" placeholder="City" disabled>
                                    <label for="permanentCity">City</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="permanentProvince" name="permanentProvince" value="<?php echo $permanentProvinceName ?>" placeholder="Province" disabled>
                                    <label for="permanentProvince">Province</label>
                                </div>
                            </div>
                        
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>

    <?php include("sharedAssets/footer.php") ?>

    <script>
        var editButton = document.getElementById('editButton');
        var cancelButton = document.getElementById('cancelButton');
        var saveButton = document.getElementById('saveButton');
        var inputs = document.querySelectorAll('.form-control');

        var isEdit = false;

        editButton.addEventListener('click', function () {
            isEdit = true;
            inputs.forEach(function (input) {
                input.removeAttribute('disabled');
            });

            editButton.classList.add('d-none');
            cancelButton.classList.remove('d-none');
            saveButton.classList.remove('d-none');
        });

        cancelButton.addEventListener('click', function () {
            isEdit = false;
            inputs.forEach(function (input) {
                input.setAttribute('disabled', true);
            });

            editButton.classList.remove('d-none');
            cancelButton.classList.add('d-none');
            saveButton.classList.add('d-none');
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
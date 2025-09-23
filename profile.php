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
$age = $userDataRow['age'];
$birthPlace = $userDataRow['birthPlace'];
$bloodType = $userDataRow['bloodType'];
$civilStatus = $userDataRow['civilStatus'];
$citizenship = $userDataRow['citizenship'];
$occupation = $userDataRow['occupation'];
$lengthOfStay = $userDataRow['lengthOfStay'];
$residencyType = $userDataRow['residencyType'];
$remarks = $userDataRow['remarks'];

$phoneNumber = $userDataRow['phoneNumber'];
$email = $userDataRow['email'];

function formatAddress($value) {
    return ucwords(strtolower($value));
}

$blockLotNo = $userDataRow['blockLotNo'];
$phase = $userDataRow['phase']; 
$subdivisionName = $userDataRow['subdivisionName'];
$purok = $userDataRow['purok'];
$streetName = $userDataRow['streetName'];
$barangayName = formatAddress($userDataRow['barangayName']);
$cityName = formatAddress($userDataRow['cityName']);
$provinceName = formatAddress($userDataRow['provinceName']);

$permanentBlockLotNo = $userDataRow['permanentBlockLotNo'];
$permanentPhase = $userDataRow['permanentPhase']; 
$permanentSubdivisionName = $userDataRow['permanentSubdivisionName'];
$permanentPurok = $userDataRow['permanentPurok'];
$permanentStreetName = $userDataRow['permanentStreetName'];
$permanentBarangayName = formatAddress($userDataRow['permanentBarangayName']);
$permanentCityName = formatAddress($userDataRow['permanentCityName']);
$permanentProvinceName = formatAddress($userDataRow['permanentProvinceName']);

$userInfoID = $userDataRow['userInfoID'];

if (isset($_POST['saveButton'])) {
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $suffix = $_POST['suffix'];
    $gender = $_POST['gender'];
    $birthDate = $_POST['birthDate'];
    $birthPlace = $_POST['birthPlace'];
    $bloodType = $_POST['bloodType'];
    $civilStatus = $_POST['civilStatus'];
    $citizenship = $_POST['citizenship'];
    $occupation = $_POST['occupation'];
    $lengthOfStay = $_POST['lengthOfStay'];
    $residencyType = $_POST['residencyType'];
    $remarks = $_POST['remarks'];

    $phoneNumber = $_POST['phoneNumber'];
    $email = $_POST['email'];

    $blockLotNo = $_POST['blockLotNo'];
    $phase = $_POST['phase']; 
    $subdivisionName = $_POST['subdivisionName'];
    $purok = $_POST['purok'];
    $streetName = $_POST['streetName'];
    $barangayName = strtoupper($_POST['barangayName']);
    $cityName = strtoupper($_POST['cityName']);
    $provinceName = strtoupper($_POST['provinceName']);

    $permanentBlockLotNo = $_POST['permanentBlockLotNo'];
    $permanentPhase = $_POST['permanentPhase']; 
    $permanentSubdivisionName = $_POST['permanentSubdivisionName'];
    $permanentPurok = $_POST['permanentPurok'];
    $permanentStreetName = $_POST['permanentStreetName'];
    $permanentBarangayName = strtoupper($_POST['permanentBarangayName']);
    $permanentCityName = strtoupper($_POST['permanentCityName']);
    $permanentProvinceName = strtoupper($_POST['permanentProvinceName']);

    $birthDateObj = new DateTime($birthDate);
    $today = new DateTime();
    $calculatedAge = $today->diff($birthDateObj)->y;

    $updateUserInfoQuery = "UPDATE userInfo SET firstName = '$firstName', middleName = '$middleName', lastName = '$lastName', 
                        suffix = '$suffix', gender = '$gender', birthDate = '$birthDate', age = '$calculatedAge', birthPlace = '$birthPlace',
                        bloodType = '$bloodType', civilStatus = '$civilStatus', citizenship = '$citizenship', occupation = '$occupation',
                        lengthOfStay = '$lengthOfStay', residencyType = '$residencyType', remarks = '$remarks' WHERE userID = $userID;";
    $updateUserInfoResult = executeQuery($updateUserInfoQuery);

    $updateUserContactQuery = "UPDATE users SET phoneNumber = '$phoneNumber', email = '$email' WHERE userID = $userID;";
    $updateUserInfoResult = executeQuery($updateUserInfoQuery);

    $updateAddressQuery = "UPDATE addresses SET blockLotNo = '$blockLotNo', phase = '$phase', subdivisionName = '$subdivisionName',
                        purok = '$purok', streetName = '$streetName', barangayName = '$barangayName', cityName = '$cityName',
                        provinceName = '$provinceName' WHERE userInfoID = $userInfoID;";
    $updateAddressResult = executeQuery($updateAddressQuery);

    $updatePermanentAddressQuery = "UPDATE permanentAddresses SET permanentBlockLotNo = '$permanentBlockLotNo', permanentPhase = '$permanentPhase', 
                        permanentSubdivisionName = '$permanentSubdivisionName', permanentPurok = '$permanentPurok', 
                        permanentStreetName = '$permanentStreetName', permanentBarangayName = '$permanentBarangayName', 
                        permanentCityName = '$permanentCityName', permanentProvinceName = '$permanentProvinceName' 
                        WHERE userInfoID = $userInfoID;";
    $updatePermanentAddressResult = executeQuery($updatePermanentAddressQuery);

    if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] === UPLOAD_ERR_OK) {
        $uploadProfilePicture = $_FILES['profilePicture']['name'];
        $targetPath = "uploads/profiles/" . basename($uploadProfilePicture);

        if (move_uploaded_file($_FILES['profilePicture']['tmp_name'], $targetPath)) {
            $profileUpdateQuery = "UPDATE userInfo SET profilePicture = '$uploadProfilePicture' WHERE userInfoID = $userInfoID";
            $profileUpdateResult = executeQuery($profileUpdateQuery);
        }
    }

    if (isset($_POST['confirmButton'])) {
        $updateProfilePictureQuery = "UPDATE userInfo SET profilePicture = NULL WHERE userInfoID = $userInfoID";
        $updateProfilePictureResult = executeQuery($updateProfilePictureQuery);
    }

    header("Location: profile.php");
    exit();
}

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
                    
    <form method="POST" enctype="multipart/form-data">
        <div class="container pt-3">
            <div class="row">
                <div class="col">
                    <div class="card profileCard p-4 p-md-3 p-lg-5" style="width:100%; height: 100%;">
                        <div class="row pb-2 pb-sm-3">
                            <div class="col-lg-11 col-md-10 col-12 d-flex flex-column flex-md-row align-items-center text-center text-md-start">

                                <div class="profile">
                                    
                                    <?php if (empty($userDataRow['profilePicture'])) { ?>

                                        <img src="uploads/profiles/defaultProfile.png" class="profilePicture" id="profilePreview" alt="Profile Picture">

                                        <label for="profilePictureInput" class="btn btn-primary addButton d-none" id="addButton">
                                            <i class="fa-solid fa-plus" style="font-size:15px; color:white;"></i>
                                        </label>

                                    <?php } else { ?>

                                        <img src="uploads/profiles/<?php echo $userDataRow['profilePicture'] ?>" class="profilePicture" id="profilePreview" alt="Profile Picture">

                                        <button class="btn btn-danger deleteButton d-none" type="button" id="deleteButton" data-bs-toggle="modal" data-bs-target="#removeProfileModal">
                                            <i class="fa-solid fa-trash" style="font-size: 15px; color: white;"></i>
                                        </button>

                                        <div class="modal fade" id="removeProfileModal" tabindex="-1" aria-labelledby="removeProfileLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">

                                                    <div class="modal-header" style="background-color: #19AFA5; color: white;">
                                                        <h1 class="modal-title fs-5" id="removeProfileLabel">Remove Profile Picture</h1>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>

                                                    <div class="modal-body">
                                                        Are you sure you want to remove your profile picture?  
                                                        You can upload a new one later.
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary confirmButton" name="confirmButton">
                                                            Confirm
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    <?php } ?>

                                </div>

                                <input type="file" name="profilePicture" class="form-control d-none" id="profilePictureInput" accept="image/*">

                                <div class="d-flex flex-column pt-3 pt-md-0">          
                                    <span class="fullName">
                                        <?php
                                            $middleInitial = !empty($middleName) ? strtoupper($middleName[0]) . "." : ""; 
                                            echo $firstName . " " . $middleInitial . " " . $lastName; 
                                        ?>
                                    </span>
                                    <span class="email text-muted"><?php echo $email ?></span>
                                </div>
                            </div>
                            <div class="col-lg-1 col-md-2 col-12 d-flex justify-content-center justify-content-md-end align-items-center pt-3 pt-md-0">
                                <button class="btn btn-primary editButton" id="editButton" type="button">Edit</button>
                                <button class="btn btn-secondary cancelButton d-none me-2" id="cancelButton" type="button">Cancel</button>
                                <button class="btn btn-primary saveButton d-none" id="saveButton" type="submit" name="saveButton">Save</button>
                            </div>
                        </div>

                        <hr>

                        <div class="row pt-2 pt-sm-3">

                            <div class="col-12 mb-3">
                                <div class="personalInfo"> Personal Information</div>
                            </div>

                            <div class="col-lg-4 col-md-3 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="firstNameInput" name="firstName" value="<?php echo $firstName ?>" placeholder="First Name" disabled>
                                    <label for="firstNameInput">First Name</label>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="middleNameInput" name="middleName" value="<?php echo $middleName ?>" placeholder="Middle Name" disabled>
                                    <label for="middleNameInput">Middle Name</label>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="lastNameInput" name="lastName" value="<?php echo $lastName ?>" placeholder="Last Name" disabled>
                                    <label for="lastNameInput">Last Name</label>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3 col-6 mb-3">
                                <div class="form-floating">
                                    <select class="form-select" id="suffix" name="suffix" disabled>
                                        <option value="" <?php echo empty($suffix) ? 'selected' : ''; ?>>Suffix</option>
                                        <option value="Jr." <?php echo ($suffix === 'Jr.') ? 'selected' : ''; ?>>Jr.</option>
                                        <option value="Sr." <?php echo ($suffix === 'Sr.') ? 'selected' : ''; ?>>Sr.</option>
                                        <option value="II" <?php echo ($suffix === 'II')  ? 'selected' : ''; ?>>II</option>
                                        <option value="III" <?php echo ($suffix === 'III') ? 'selected' : ''; ?>>III</option>
                                        <option value="IV" <?php echo ($suffix === 'IV')  ? 'selected' : ''; ?>>IV</option>
                                        <option value="V" <?php echo ($suffix === 'V')  ? 'selected' : ''; ?>>V</option>
                                    </select>
                                    <label for="suffix">Suffix</label>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-3 col-6 mb-3">
                                <div class="form-floating">
                                    <select class="form-select" id="gender" name="gender" disabled>
                                        <option value="" <?php echo empty($gender) ? 'selected' : ''; ?>>Gender</option>
                                        <option value="Male" <?php echo ($gender === 'Male')   ? 'selected' : ''; ?>>Male</option>
                                        <option value="Female" <?php echo ($gender === 'Female') ? 'selected' : ''; ?>>Female</option>
                                        <option value="Other" <?php echo ($gender === 'Other')  ? 'selected' : ''; ?>>Others</option>
                                    </select>
                                    <label for="gender">Gender</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-5 col-6 mb-3"> 
                                <div class="form-floating"> 
                                    <input type="text" class="form-control" id="birthDate" name="birthDate" value="<?php echo date("F j, Y", strtotime($birthDate)); ?>" placeholder="Date of Birth" disabled> 
                                    <label for="birthDate">Date of Birth</label>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-4 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="age" name="age" value="<?php echo $age . ($age === 1 ? ' year old' : ' years old'); ?>" placeholder="Age" readonly disabled>
                                    <label for="age">Age</label>
                                </div>
                            </div>

                            <div class="col-lg-5 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="birthPlace" name="birthPlace" value="<?php echo $birthPlace ?>" placeholder="Place of Birth" disabled>
                                    <label for="birthPlace">Place of Birth</label>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-3 col-6 mb-3">
                                <div class="form-floating">
                                    <select class="form-select" id="bloodType" name="bloodType" disabled>
                                        <option value="" <?php echo empty($bloodType) ? 'selected' : ''; ?>>Blood Type</option>
                                        <option value="A+" <?php echo ($bloodType === 'A+')   ? 'selected' : ''; ?>>A+</option>
                                        <option value="A-" <?php echo ($bloodType === 'A-') ? 'selected' : ''; ?>>A-</option>
                                        <option value="B+" <?php echo ($bloodType === 'B+')  ? 'selected' : ''; ?>>B+</option>
                                        <option value="B-" <?php echo ($bloodType === 'B-') ? 'selected' : ''; ?>>B-</option>
                                        <option value="AB+" <?php echo ($bloodType === 'AB+')  ? 'selected' : ''; ?>>AB+</option>
                                        <option value="AB-" <?php echo ($bloodType === 'AB-')  ? 'selected' : ''; ?>>AB-</option>
                                        <option value="O+" <?php echo ($bloodType === 'O+') ? 'selected' : ''; ?>>O+</option>
                                        <option value="O-" <?php echo ($bloodType === 'O-')  ? 'selected' : ''; ?>>O-</option>
                                    </select>
                                    <label for="bloodType">Blood Type</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-4 col-6 mb-3">
                                <div class="form-floating">
                                    <select class="form-select" id="civilStatus" name="civilStatus" disabled>
                                        <option value="" <?php echo empty($civilStatus) ? 'selected' : ''; ?>>Civil Status</option>
                                        <option value="Single" <?php echo ($civilStatus === 'Single')   ? 'selected' : ''; ?>>Single</option>
                                        <option value="Married" <?php echo ($civilStatus === 'Married') ? 'selected' : ''; ?>>Married</option>
                                        <option value="Divorced" <?php echo ($civilStatus === 'Divorced')  ? 'selected' : ''; ?>>Divorced</option>
                                        <option value="Widowed" <?php echo ($civilStatus === 'Widowed') ? 'selected' : ''; ?>>Widowed</option>
                                        <option value="Separated" <?php echo ($civilStatus === 'Separated')  ? 'selected' : ''; ?>>Separated</option>
                                    </select>
                                    <label for="civilStatus">Civil Status</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-5 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="citizenship" name="citizenship" value="<?php echo $citizenship ?>" placeholder="Citizenship" disabled>
                                    <label for="citizenship">Citizenship</label>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-7 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="occupation" name="occupation" value="<?php echo $occupation ?>" placeholder="Occupation" disabled>
                                    <label for="occupation">Occupation</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-5 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="lengthOfStay" name="lengthOfStay" value="<?php echo !empty($lengthOfStay) ? (int)$lengthOfStay . ((int)$lengthOfStay == 1 ? ' year' : ' years') : ''; ?>" placeholder="Length Of Stay (in years)" disabled>
                                    <label for="lengthOfStay">Length Of Stay (in years)</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-4 col-6 mb-3">
                                <div class="form-floating">
                                    <select class="form-select" id="residencyType" disabled readonly>
                                        <option value="" <?php echo empty($residencyType) ? 'selected' : ''; ?>>Type of Residency</option>
                                        <option value="Bonafide" <?php echo ($residencyType === 'Bonafide')   ? 'selected' : ''; ?>>Bonafide</option>
                                        <option value="Migrant" <?php echo ($residencyType === 'Migrant') ? 'selected' : ''; ?>>Migrant</option>
                                        <option value="Transient" <?php echo ($residencyType === 'Transient')  ? 'selected' : ''; ?>>Transient</option>
                                        <option value="Foreign" <?php echo ($residencyType === 'Foreign') ? 'selected' : ''; ?>>Foreign</option>
                                        <option value="" disabled>
                                            ℹ️ No need to select — this is set automatically based on your length of stay
                                        </option>

                                    <input type="hidden" id="residencyTypeHidden" name="residencyType" value="<?php echo $residencyType ?>">
                                    <label for="residencyType">Type of Residency</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-5 col-12 mb-4">
                                <div class="form-floating">
                                    <select class="form-select" id="remarks" name="remarks" disabled>
                                        <option value="" <?php echo empty($remarks) ? 'selected' : ''; ?>>Remarks</option>
                                        <option value="No Derogatory Record" <?php echo ($remarks === 'No Derogatory Record')   ? 'selected' : ''; ?>>No Derogatory Record</option>
                                    </select>
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

                            <div class="col-lg-3 col-md-6 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" value="<?php echo $phoneNumber ?>" placeholder="Phone Number" disabled>
                                    <label for="phoneNumber">Phone Number</label>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6 col-12 mb-4">
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

                            <div class="col-lg-3 col-md-6 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" list="provincesList" id="province" name="provinceName" value="<?php echo $provinceName ?>" placeholder="Province" disabled>
                                    <label for="province">Province</label>
                                    <datalist id="provincesList"></datalist>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" list="citiesList" id="city" name="cityName" value="<?php echo $cityName ?>" placeholder="City" disabled>
                                    <label for="city">City</label>
                                    <datalist id="citiesList"></datalist>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" list="barangaysList" id="barangay" name="barangayName" value="<?php echo $barangayName ?>" placeholder="Barangay" disabled>
                                    <label for="barangay">Barangay</label>
                                    <datalist id="barangaysList"></datalist>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="street" name="streetName" value="<?php echo $streetName ?>" placeholder="Street" disabled>
                                    <label for="street">Street</label>
                                </div>
                            </div>                            

                            <div class="col-lg-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="blockLotNo" name="blockLotNo" value="<?php echo $blockLotNo ?>" placeholder="Block & Lot No." disabled>
                                    <label for="blockLotNo">Block & Lot No.</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="phase" name="phase" value="<?php echo $phase ?>" placeholder="Phase" disabled>
                                    <label for="phase">Phase</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="subdivision" name="subdivisionName" value="<?php echo $subdivisionName ?>" placeholder="Subdivision" disabled>
                                    <label for="subdivision">Subdivision</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="purok" name="purok" value="<?php echo $purok ?>" placeholder="Purok" disabled>
                                    <label for="purok">Purok</label>
                                </div>
                            </div>
                        
                        </div>

                        <div class="row">

                            <div class="col-12 mb-3">
                                <div class="permanentAddressInfo">
                                    Permanent Address
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" list="permanentProvincesList" id="permanentProvince" name="permanentProvinceName" value="<?php echo $permanentProvinceName ?>" placeholder="Province" disabled>
                                    <label for="permanentProvince">Province</label>
                                    <datalist id="permanentProvincesList"></datalist>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" list="permanentCitiesList" id="permanentCity" name="permanentCityName" value="<?php echo $permanentCityName ?>" placeholder="City" disabled>
                                    <label for="permanentCity">City</label>
                                    <datalist id="permanentCitiesList"></datalist>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" list="permanentBarangaysList" id="permanentBarangay" name="permanentBarangayName" value="<?php echo $permanentBarangayName ?>" placeholder="Barangay" disabled>
                                    <label for="permanentBarangay">Barangay</label>
                                    <datalist id="permanentBarangaysList"></datalist>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="permanentStreet" name="permanentStreetName" value="<?php echo $permanentStreetName ?>" placeholder="Street" disabled>
                                    <label for="permanentStreet">Street</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="permanentBlockLotNo" name="permanentBlockLotNo" value="<?php echo $permanentBlockLotNo ?>" placeholder="Block & Lot No." disabled>
                                    <label for="permanentBlockLotNo">Block & Lot No.</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="permanentPhase" name="permanentPhase" value="<?php echo $permanentPhase ?>" placeholder="Phase" disabled>
                                    <label for="permanentPhase">Phase</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="permanentSubdivisionName" name="permanentSubdivisionName" value="<?php echo $permanentSubdivisionName ?>" placeholder="Subdivision" disabled>
                                    <label for="permanentSubdivisionName">Subdivision</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="permanentPurok" name="permanentPurok" value="<?php echo $permanentPurok ?>" placeholder="Purok" disabled>
                                    <label for="permanentPurok">Purok</label>
                                </div>
                            </div>
                        
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>

    <?php include("sharedAssets/footer.php") ?>

    <script src="assets/js/profile/script.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
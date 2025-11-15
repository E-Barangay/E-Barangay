<?php

include("sharedAssets/connect.php");

session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
}

$userID = $_SESSION['userID'];

$userQuery = "SELECT * FROM users 
            LEFT JOIN userInfo ON users.userID = userInfo.userID 
            LEFT JOIN addresses ON userInfo.userInfoID = addresses.userInfoID    
            LEFT JOIN permanentAddresses ON userInfo.userInfoID = permanentAddresses.userInfoID
            WHERE users.userID = $userID";
$userResult = executeQuery($userQuery);

$userDataRow = mysqli_fetch_assoc($userResult);

$firstName = $userDataRow['firstName'];
$middleName = $userDataRow['middleName'];
$lastName = $userDataRow['lastName'];
$suffix = $userDataRow['suffix'];
$profilePicture = $userDataRow['profilePicture'];
$gender = $userDataRow['gender'];
$birthDate = $userDataRow['birthDate'];
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

$age = '';
if (!empty($birthDate)) {
    $birthDateTime = new DateTime($birthDate);
    $currentDate = new DateTime();
    $ageYears = $currentDate->diff($birthDateTime)->y;
    $age = $ageYears . ' ' . ($ageYears <= 1 ? 'year' : 'years') . ' old';
}

function formatAddress($value)
{
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

$userInfoIDQuery = "SELECT userInfoID FROM userInfo WHERE userID = $userID";
$userInfoIDResult = executeQuery($userInfoIDQuery);
$userInfoIDRow = mysqli_fetch_assoc($userInfoIDResult);

$userInfoID = $userInfoIDRow['userInfoID'];

$educationalLevel = $userDataRow['educationalLevel'] ?? '';
$shsTrack = $userDataRow['shsTrack'] ?? '';
$collegeCourse = $userDataRow['collegeCourse'] ?? '';


$isProfileComplete = !(
    empty($firstName)
    || empty($lastName)
    || empty($profilePicture)
    || empty($gender)
    || empty($birthDate)
    || empty($birthPlace)
    || empty($civilStatus)
    || empty($citizenship)
    || empty($lengthOfStay)
    || empty($residencyType)
    || empty($phoneNumber)
    || empty($email)
    || empty($purok)
    || empty($barangayName)
    || empty($cityName)
    || empty($provinceName)
    || empty($permanentPurok)
    || empty($permanentBarangayName)
    || empty($permanentCityName)
    || empty($permanentProvinceName)
);

$incomplete = (!$isProfileComplete && isset($_SESSION['warning']) && ($_SESSION['warning'] === 'incompleteInformation1' || $_SESSION['warning'] === 'incompleteInformation2'));

if (isset($_POST['saveButton'])) {
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $suffix = $_POST['suffix'];
    $gender = $_POST['gender'];
    $birthDate = $_POST['birthDate'];
    $foreignPermanentAddress = isset($_POST['foreignPermanentAddress']) ? trim($_POST['foreignPermanentAddress']) : '';


    $age = '';
    if (!empty($birthDate)) {
        $birthDateTime = new DateTime($birthDate);
        $currentDate = new DateTime();
        $ageYears = $currentDate->diff($birthDateTime)->y;
        $age = $ageYears . ' ' . ($ageYears <= 1 ? 'year' : 'years') . ' old';
    }

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


    $address = [
        'blockLotNo' => $blockLotNo,
        'phase' => $phase,
        'subdivisionName' => $subdivisionName,
        'purok' => $purok,
        'streetName' => $streetName,
        'barangayName' => $barangayName,
        'cityName' => $cityName,
        'provinceName' => $provinceName
    ];

    $permanentAddress = [
        'blockLotNo' => $permanentBlockLotNo,
        'phase' => $permanentPhase,
        'subdivisionName' => $permanentSubdivisionName,
        'purok' => $permanentPurok,
        'streetName' => $permanentStreetName,
        'barangayName' => $permanentBarangayName,
        'cityName' => $permanentCityName,
        'provinceName' => $permanentProvinceName
    ];

    $ageNumeric = (int) filter_var($age, FILTER_SANITIZE_NUMBER_INT);
    $lengthOfStayNumeric = (int) $lengthOfStay;

    // Convert to uppercase for exact comparison
    $currentProvince = strtoupper($provinceName);
    $currentCity = strtoupper($cityName);
    $currentBarangay = strtoupper($barangayName);

    $permanentProvince = strtoupper($permanentProvinceName);
    $permanentCity = strtoupper($permanentCityName);
    $permanentBarangay = strtoupper($permanentBarangayName);

    if (strtoupper($citizenship) !== 'FILIPINO') {
        $residencyType = "Foreign";
    } else {
        // Check for the specific Bonafide condition
        if (
            $currentProvince === 'BATANGAS' &&
            $currentCity === 'SANTO TOMAS' &&
            $currentBarangay === 'SAN ANTONIO' &&
            $permanentProvince === 'BATANGAS' &&
            $permanentCity === 'SANTO TOMAS' &&
            $permanentBarangay === 'SAN ANTONIO' &&
            $ageNumeric === $lengthOfStayNumeric
        ) {
            $residencyType = "Bonafide";
        } else if ($lengthOfStayNumeric >= 3) {
            $residencyType = "Migrant";
        } else if ($lengthOfStayNumeric <= 2) {
            $residencyType = "Transient";
        } else {
            $residencyType = "";
        }
    }


    $educationalLevel = NULL;
    $shsTrack = NULL;
    $collegeCourse = NULL;

    $educationalLevel = isset($_POST['educationalLevel']) && !empty($_POST['educationalLevel'])
        ? mysqli_real_escape_string($conn, $_POST['educationalLevel'])
        : NULL;

    if ($educationalLevel !== NULL) {
        $levelLower = strtolower($educationalLevel);

        if (strpos($levelLower, 'senior high') !== false) {
            $shsTrack = isset($_POST['shsTrack']) && !empty($_POST['shsTrack'])
                ? mysqli_real_escape_string($conn, $_POST['shsTrack'])
                : NULL;
        } else if (strpos($levelLower, 'college') !== false) {
            $collegeCourse = isset($_POST['collegeCourse']) && !empty($_POST['collegeCourse'])
                ? mysqli_real_escape_string($conn, $_POST['collegeCourse'])
                : NULL;
        }
    }
    $updateUserInfoQuery = "UPDATE userInfo SET 
    firstName = '$firstName', 
    middleName = '$middleName', 
    lastName = '$lastName', 
    suffix = '$suffix', 
    gender = '$gender', 
    birthDate = '$birthDate', 
    age = '$age', 
    birthPlace = '$birthPlace',
    bloodType = '$bloodType', 
    civilStatus = '$civilStatus', 
    citizenship = '$citizenship', 
    occupation = '$occupation',
    educationalLevel = " . ($educationalLevel !== NULL ? "'$educationalLevel'" : "NULL") . ",
    shsTrack = " . ($shsTrack !== NULL ? "'$shsTrack'" : "NULL") . ",
    collegeCourse = " . ($collegeCourse !== NULL ? "'$collegeCourse'" : "NULL") . ",
    lengthOfStay = '$lengthOfStay', 
    residencyType = '$residencyType', 
    remarks = '$remarks'
    WHERE userID = $userID;";
    $updateUserInfoResult = executeQuery($updateUserInfoQuery);

    $updateUserContactQuery = "UPDATE users SET phoneNumber = '$phoneNumber', email = '$email' WHERE userID = $userID;";
    $updateUserInfoResult = executeQuery($updateUserContactQuery);

    $updateAddressQuery = "UPDATE addresses SET blockLotNo = '$blockLotNo', phase = '$phase', subdivisionName = '$subdivisionName',
                        purok = '$purok', streetName = '$streetName', barangayName = '$barangayName', cityName = '$cityName',
                        provinceName = '$provinceName' WHERE userInfoID = $userInfoID;";
    $updateAddressResult = executeQuery($updateAddressQuery);

    $updatePermanentAddressQuery = "UPDATE permanentAddresses SET 
    permanentBlockLotNo = " . ($citizenship === 'FILIPINO' ? "'$permanentBlockLotNo'" : "NULL") . ",
    permanentPhase = " . ($citizenship === 'FILIPINO' ? "'$permanentPhase'" : "NULL") . ",
    permanentSubdivisionName = " . ($citizenship === 'FILIPINO' ? "'$permanentSubdivisionName'" : "NULL") . ",
    permanentPurok = " . ($citizenship === 'FILIPINO' ? "'$permanentPurok'" : "NULL") . ",
    permanentStreetName = " . ($citizenship === 'FILIPINO' ? "'$permanentStreetName'" : "NULL") . ",
    permanentBarangayName = " . ($citizenship === 'FILIPINO' ? "'$permanentBarangayName'" : "NULL") . ",
    permanentCityName = " . ($citizenship === 'FILIPINO' ? "'$permanentCityName'" : "NULL") . ",
    permanentProvinceName = " . ($citizenship === 'FILIPINO' ? "'$permanentProvinceName'" : "NULL") . ",
    foreignPermanentAddress = " . ($citizenship !== 'FILIPINO' ? "'$foreignPermanentAddress'" : "NULL") . "
    WHERE userInfoID = $userInfoID;";
    executeQuery($updatePermanentAddressQuery);


    if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] === UPLOAD_ERR_OK) {
        $uploadProfilePicture = $_FILES['profilePicture']['name'];
        $targetPath = "uploads/profiles/" . basename($uploadProfilePicture);

        if (move_uploaded_file($_FILES['profilePicture']['tmp_name'], $targetPath)) {
            $profileUpdateQuery = "UPDATE userInfo SET profilePicture = '$uploadProfilePicture' WHERE userInfoID = $userInfoID";
            $profileUpdateResult = executeQuery($profileUpdateQuery);
        }
    }

    $_SESSION['success'] = 'profileUpdated';

    header("Location: profile.php");
    exit();
}

if (isset($_POST['confirmButton'])) {
    $updateProfilePictureQuery = "UPDATE userInfo SET profilePicture = NULL WHERE userInfoID = $userInfoID";
    $updateProfilePictureResult = executeQuery($updateProfilePictureQuery);

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
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

                    <?php if (isset($_SESSION['success']) && $_SESSION['success'] === 'newUser'): ?>
                        <div class="alert alert-success text-center mb-4" style="font-size: 14px; line-height: 1.4;"><i class="fa-solid fa-circle-check" style="margin-right:8px;"></i>Welcome! Kindly fill out your profile details
                            below so you can enjoy all the services we offer.</div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['success']) && $_SESSION['success'] === 'passwordCreated'): ?>
                        <div class="alert alert-success text-center mb-4" style="font-size: 14px; line-height: 1.4;"><i class="fa-solid fa-circle-check" style="margin-right:8px;"></i>Welcome! Kindly fill out your profile details
                            below so you can enjoy all the services we offer.</div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['success']) && $_SESSION['success'] === 'profileUpdated'): ?>
                        <div class="alert alert-success text-center mb-4" style="font-size: 14px; line-height: 1.4;"><i class="fa-solid fa-circle-check" style="margin-right:8px;"></i>Your profile has been successfully updated.</div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['warning']) && $_SESSION['warning'] === 'incompleteInformation1'): ?>
                        <div class="alert alert-warning text-center mb-4" style="font-size: 14px; line-height: 1.4;"><i class="fa-solid fa-triangle-exclamation" style="margin-right:8px;"></i>Please complete your profile information below to
                            proceed with your document request.</div>
                        <?php unset($_SESSION['warning']); ?>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['warning']) && $_SESSION['warning'] === 'incompleteInformation2'): ?>
                        <div class="alert alert-warning text-center mb-4" style="font-size: 14px; line-height: 1.4;"><i class="fa-solid fa-triangle-exclamation" style="margin-right:8px;"></i>Please complete your profile information below to
                            proceed with your complaint.</div>
                        <?php unset($_SESSION['warning']); ?>
                    <?php endif; ?>

                    <div class="card profileCard p-4 p-md-3 p-lg-5">
                        <div class="row pb-2 pb-sm-3">
                            <div
                                class="col-lg-11 col-md-10 col-12 d-flex flex-column flex-md-row align-items-center text-center text-md-start">

                                <div class="profile">

                                    <?php if (empty($userDataRow['profilePicture'])) { ?>

                                        <img src="uploads/profiles/defaultProfile.png"
                                            class="profilePicture <?php echo ($incomplete && empty($profilePicture)) ? 'border border-warning' : ''; ?>"
                                            id="profilePreview" alt="Profile Picture">

                                        <label for="profilePictureInput" class="btn btn-primary addButton d-none"
                                            id="addButton">
                                            <i class="fa-solid fa-plus" style="font-size:15px; color:white;"></i>
                                        </label>

                                    <?php } else { ?>

                                        <img src="uploads/profiles/<?php echo $userDataRow['profilePicture'] ?>"
                                            class="profilePicture" id="profilePreview" alt="Profile Picture">

                                        <button class="btn btn-danger deleteButton d-none" type="button" id="deleteButton"
                                            data-bs-toggle="modal" data-bs-target="#removeProfileModal">
                                            <i class="fa-solid fa-trash" style="font-size: 15px; color: white;"></i>
                                        </button>

                                        <div class="modal fade" id="removeProfileModal" tabindex="-1"
                                            aria-labelledby="removeProfileLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">

                                                    <div class="modal-header"
                                                        style="background-color: #19AFA5; color: white;">
                                                        <h1 class="modal-title fs-5" id="removeProfileLabel">Remove Profile
                                                            Picture</h1>
                                                        <button type="button" class="btn-close btn-close-white"
                                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>

                                                    <div class="modal-body">
                                                        Are you sure you want to remove your profile picture?
                                                        You can upload a new one later.
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary confirmButton"
                                                            name="confirmButton">
                                                            Confirm
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    <?php } ?>

                                </div>

                                <input type="file" name="profilePicture" class="form-control d-none"
                                    id="profilePictureInput" accept="image/*">

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
                            <div
                                class="col-lg-1 col-md-2 col-12 d-flex justify-content-center justify-content-md-end align-items-center pt-3 pt-md-0">
                                <button class="btn btn-primary editButton" id="editButton" type="button">Edit</button>
                                <button class="btn btn-secondary cancelButton d-none me-2" id="cancelButton"
                                    type="button">Cancel</button>
                                <button class="btn btn-primary saveButton d-none" id="saveButton" type="submit"
                                    name="saveButton">Save</button>
                            </div>
                        </div>

                        <hr>

                        <div class="row pt-2 pt-sm-3">

                            <div class="col-12 mb-3">
                                <div class="personalInfo"> Personal Information</div>
                            </div>

                            <div class="col-lg-4 col-md-3 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text"
                                        class="form-control <?php echo ($incomplete && empty($firstName)) ? 'border border-warning' : ''; ?>"
                                        id="firstNameInput" name="firstName" value="<?php echo $firstName ?>"
                                        placeholder="First Name" pattern="[A-Za-z\s]+"
                                        oninput="this.value=this.value.replace(/[^A-Za-z\s]/g,'')" disabled>
                                    <label for="firstNameInput">First Name</label>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="middleNameInput" name="middleName"
                                        value="<?php echo $middleName ?>" placeholder="Middle Name"
                                        pattern="[A-Za-z\s]+"
                                        oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')" disabled>
                                    <label for="middleNameInput">Middle Name</label>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text"
                                        class="form-control <?php echo ($incomplete && empty($lastName)) ? 'border border-warning' : ''; ?>"
                                        id="lastNameInput" name="lastName" value="<?php echo $lastName ?>"
                                        placeholder="Last Name" pattern="[A-Za-z\s]+"
                                        oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')" disabled>
                                    <label for="lastNameInput">Last Name</label>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3 col-6 mb-3">
                                <div class="form-floating">
                                    <select class="form-select" id="suffix" name="suffix" disabled>
                                        <option value="" <?php echo empty($suffix) ? 'selected' : ''; ?>>Suffix</option>
                                        <option value="Jr." <?php echo ($suffix === 'Jr.') ? 'selected' : ''; ?>>Jr.
                                        </option>
                                        <option value="Sr." <?php echo ($suffix === 'Sr.') ? 'selected' : ''; ?>>Sr.
                                        </option>
                                        <option value="II" <?php echo ($suffix === 'II') ? 'selected' : ''; ?>>II</option>
                                        <option value="III" <?php echo ($suffix === 'III') ? 'selected' : ''; ?>>III
                                        </option>
                                        <option value="IV" <?php echo ($suffix === 'IV') ? 'selected' : ''; ?>>IV</option>
                                        <option value="V" <?php echo ($suffix === 'V') ? 'selected' : ''; ?>>V</option>
                                    </select>
                                    <label for="suffix">Suffix</label>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-3 col-6 mb-3">
                                <div class="form-floating">
                                    <select
                                        class="form-select <?php echo ($incomplete && empty($gender)) ? 'border border-warning' : ''; ?>"
                                        id="gender" name="gender" disabled>
                                        <option value="" disabled <?php echo empty($gender) ? 'selected' : ''; ?>>Choose
                                            Gender</option>
                                        <option value="Male" <?php echo ($gender === 'Male') ? 'selected' : ''; ?>>Male
                                        </option>
                                        <option value="Female" <?php echo ($gender === 'Female') ? 'selected' : ''; ?>>
                                            Female</option>
                                        <option value="Other" <?php echo ($gender === 'Other') ? 'selected' : ''; ?>>
                                            Others</option>
                                    </select>
                                    <label for="gender">Gender</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-5 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="date"
                                        class="form-control <?php echo ($incomplete && empty($birthDate)) ? 'border border-warning' : ''; ?>"
                                        id="birthDate" name="birthDate" value="<?php echo $birthDate ?>"
                                        placeholder="Date of Birth" disabled>
                                    <label for="birthDate">Date of Birth</label>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-4 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="age" name="age"
                                        value="<?php echo $age ?>" placeholder="Age" readonly disabled>
                                    <input type="hidden" name="age" id="ageHidden" value="<?php echo $age ?>">
                                    <label for="age">Age</label>
                                </div>
                            </div>

                            <div class="col-lg-5 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text"
                                        class="form-control <?php echo ($incomplete && empty($birthPlace)) ? 'border border-warning' : ''; ?>"
                                        id="birthPlace" name="birthPlace" value="<?php echo $birthPlace ?>"
                                        placeholder="Place of Birth" disabled>
                                    <label for="birthPlace">Place of Birth</label>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-3 col-6 mb-3">
                                <div class="form-floating">
                                    <select class="form-select" id="bloodType" name="bloodType" disabled>
                                        <option value="" disabled <?php echo empty($bloodType) ? 'selected' : ''; ?>>
                                            Choose Blood Type</option>
                                        <option value="A+" <?php echo ($bloodType === 'A+') ? 'selected' : ''; ?>>A+
                                        </option>
                                        <option value="A-" <?php echo ($bloodType === 'A-') ? 'selected' : ''; ?>>A-
                                        </option>
                                        <option value="B+" <?php echo ($bloodType === 'B+') ? 'selected' : ''; ?>>B+
                                        </option>
                                        <option value="B-" <?php echo ($bloodType === 'B-') ? 'selected' : ''; ?>>B-
                                        </option>
                                        <option value="AB+" <?php echo ($bloodType === 'AB+') ? 'selected' : ''; ?>>AB+
                                        </option>
                                        <option value="AB-" <?php echo ($bloodType === 'AB-') ? 'selected' : ''; ?>>AB-
                                        </option>
                                        <option value="O+" <?php echo ($bloodType === 'O+') ? 'selected' : ''; ?>>O+
                                        </option>
                                        <option value="O-" <?php echo ($bloodType === 'O-') ? 'selected' : ''; ?>>O-
                                        </option>
                                    </select>
                                    <label for="bloodType">Blood Type</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-4 col-6 mb-3">
                                <div class="form-floating">
                                    <select
                                        class="form-select <?php echo ($incomplete && empty($civilStatus)) ? 'border border-warning' : ''; ?>"
                                        id="civilStatus" name="civilStatus" disabled>
                                        <option value="" disabled <?php echo empty($civilStatus) ? 'selected' : ''; ?>>
                                            Choose Civil Status</option>
                                        <option value="Single" <?php echo ($civilStatus === 'Single') ? 'selected' : ''; ?>>Single</option>
                                        <option value="Married" <?php echo ($civilStatus === 'Married') ? 'selected' : ''; ?>>Married</option>
                                        <option value="Divorced" <?php echo ($civilStatus === 'Divorced') ? 'selected' : ''; ?>>Divorced</option>
                                        <option value="Widowed" <?php echo ($civilStatus === 'Widowed') ? 'selected' : ''; ?>>Widowed</option>
                                        <option value="Separated" <?php echo ($civilStatus === 'Separated') ? 'selected' : ''; ?>>Separated</option>
                                    </select>
                                    <label for="civilStatus">Civil Status</label>
                                </div>
                            </div>

                            <?php
                            $citizenships = [
                                'FILIPINO',
                                'AFGHAN',
                                'ALBANIAN',
                                'ALGERIAN',
                                'AMERICAN',
                                'ANDORRAN',
                                'ANGOLAN',
                                'ARGENTINE',
                                'ARMENIAN',
                                'AUSTRALIAN',
                                'AUSTRIAN',
                                'AZERBAIJANI',
                                'BAHAMIAN',
                                'BAHRAINI',
                                'BANGLADESHI',
                                'BARBADIAN',
                                'BELARUSIAN',
                                'BELGIAN',
                                'BELIZEAN',
                                'BENINESE',
                                'BHUTANESE',
                                'BOLIVIAN',
                                'BOSNIAN',
                                'BRAZILIAN',
                                'BRITISH',
                                'BRUNEIAN',
                                'BULGARIAN',
                                'BURKINABE',
                                'BURMESE',
                                'BURUNDIAN',
                                'CAMBODIAN',
                                'CAMEROONIAN',
                                'CANADIAN',
                                'CAPE VERDEAN',
                                'CENTRAL AFRICAN',
                                'CHADIAN',
                                'CHILEAN',
                                'CHINESE',
                                'COLOMBIAN',
                                'COMORAN',
                                'CONGOLESE',
                                'COSTA RICAN',
                                'CROATIAN',
                                'CUBAN',
                                'CYPRIOT',
                                'CZECH',
                                'DANISH',
                                'DJIBOUTIAN',
                                'DOMINICAN',
                                'DUTCH',
                                'ECUADORIAN',
                                'EGYPTIAN',
                                'EMIRATI',
                                'EQUATORIAL GUINEAN',
                                'ERITREAN',
                                'ESTONIAN',
                                'ETHIOPIAN',
                                'FIJIAN',
                                'FINNISH',
                                'FRENCH',
                                'GABONESE',
                                'GAMBIAN',
                                'GEORGIAN',
                                'GERMAN',
                                'GHANAIAN',
                                'GREEK',
                                'GRENADIAN',
                                'GUATEMALAN',
                                'GUINEAN',
                                'GUYANESE',
                                'HAITIAN',
                                'HONDURAN',
                                'HUNGARIAN',
                                'ICELANDIC',
                                'INDIAN',
                                'INDONESIAN',
                                'IRANIAN',
                                'IRAQI',
                                'IRISH',
                                'ISRAELI',
                                'ITALIAN',
                                'IVORIAN',
                                'JAMAICAN',
                                'JAPANESE',
                                'JORDANIAN',
                                'KAZAKH',
                                'KENYAN',
                                'KUWAITI',
                                'KYRGYZ',
                                'LAOTIAN',
                                'LATVIAN',
                                'LEBANESE',
                                'LIBERIAN',
                                'LIBYAN',
                                'LIECHTENSTEINER',
                                'LITHUANIAN',
                                'LUXEMBOURGISH',
                                'MACEDONIAN',
                                'MALAGASY',
                                'MALAWIAN',
                                'MALAYSIAN',
                                'MALDIVIAN',
                                'MALIAN',
                                'MALTESE',
                                'MAURITANIAN',
                                'MAURITIAN',
                                'MEXICAN',
                                'MOLDOVAN',
                                'MONEGASQUE',
                                'MONGOLIAN',
                                'MONTENEGRIN',
                                'MOROCCAN',
                                'MOZAMBICAN',
                                'NAMIBIAN',
                                'NEPALESE',
                                'NEW ZEALANDER',
                                'NICARAGUAN',
                                'NIGERIAN',
                                'NIGERIEN',
                                'NORTH KOREAN',
                                'NORWEGIAN',
                                'OMANI',
                                'PAKISTANI',
                                'PALESTINIAN',
                                'PANAMANIAN',
                                'PAPUA NEW GUINEAN',
                                'PARAGUAYAN',
                                'PERUVIAN',
                                'POLISH',
                                'PORTUGUESE',
                                'QATARI',
                                'ROMANIAN',
                                'RUSSIAN',
                                'RWANDAN',
                                'SAINT LUCIAN',
                                'SALVADORAN',
                                'SAMOAN',
                                'SAN MARINESE',
                                'SAUDI',
                                'SENEGALESE',
                                'SERBIAN',
                                'SEYCHELLOIS',
                                'SIERRA LEONEAN',
                                'SINGAPOREAN',
                                'SLOVAK',
                                'SLOVENIAN',
                                'SOMALI',
                                'SOUTH AFRICAN',
                                'SOUTH KOREAN',
                                'SPANISH',
                                'SRI LANKAN',
                                'SUDANESE',
                                'SURINAMESE',
                                'SWEDISH',
                                'SWISS',
                                'SYRIAN',
                                'TAIWANESE',
                                'TAJIK',
                                'TANZANIAN',
                                'THAI',
                                'TOGOLESE',
                                'TONGAN',
                                'TRINIDADIAN',
                                'TUNISIAN',
                                'TURKISH',
                                'TURKMEN',
                                'UGANDAN',
                                'UKRAINIAN',
                                'URUGUAYAN',
                                'UZBEK',
                                'VENEZUELAN',
                                'VIETNAMESE',
                                'YEMENI',
                                'ZAMBIAN',
                                'ZIMBABWEAN'
                            ];
                            ?>

                            <div class="col-lg-3 col-md-5 col-6 mb-3">
                                <div class="form-floating">
                                    <select
                                        class="form-select text-uppercase <?php echo ($incomplete && empty($citizenship)) ? 'border border-warning' : ''; ?>"
                                        id="citizenship" name="citizenship" disabled>
                                        <?php foreach ($citizenships as $country): ?>
                                            <option value="<?php echo $country; ?>" <?php echo (!empty($citizenship) && strtoupper($citizenship) === $country) || (empty($citizenship) && $country === 'FILIPINO') ? 'selected' : ''; ?>>
                                                <?php echo $country; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="citizenship">Citizenship</label>
                                </div>
                            </div>


                            <div class="col-lg-4 col-md-6 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="occupation" name="occupation"
                                        placeholder="Occupation" value="<?php echo htmlspecialchars($occupation); ?>"
                                        disabled>
                                    <label for="occupation">Occupation</label>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6 col-6 mb-3" id="educationalLevelDiv">
                                <div class="form-floating">
                                    <select class="form-control" id="educationalLevel" name="educationalLevel" disabled>
                                        <option value="" disabled <?php echo empty($educationalLevel) ? 'selected' : ''; ?>>
                                            Select Level
                                        </option>

                                        <option value="None" <?php echo ($educationalLevel == 'None') ? 'selected' : ''; ?>>None</option>

                                        <option value="Elementary Undergraduate" <?php echo ($educationalLevel == 'Elementary Undergraduate') ? 'selected' : ''; ?>>
                                            Elementary Undergraduate</option>
                                        <option value="Elementary Graduate" <?php echo ($educationalLevel == 'Elementary Graduate') ? 'selected' : ''; ?>>Elementary Graduate</option>

                                        <option value="High School Undergraduate" <?php echo ($educationalLevel == 'High School Undergraduate') ? 'selected' : ''; ?>>High School Undergraduate
                                        </option>
                                        <option value="High School Graduate" <?php echo ($educationalLevel == 'High School Graduate') ? 'selected' : ''; ?>>High School Graduate</option>

                                        <option value="Senior High Undergraduate" <?php echo ($educationalLevel == 'Senior High Undergraduate') ? 'selected' : ''; ?>>Senior High Undergraduate
                                        </option>
                                        <option value="Senior High Graduate" <?php echo ($educationalLevel == 'Senior High Graduate') ? 'selected' : ''; ?>>Senior High Graduate</option>

                                        <option value="College Undergraduate" <?php echo ($educationalLevel == 'College Undergraduate') ? 'selected' : ''; ?>>College Undergraduate</option>
                                        <option value="College Graduate" <?php echo ($educationalLevel == 'College Graduate') ? 'selected' : ''; ?>>College Graduate</option>

                                        <option value="ALS" <?php echo ($educationalLevel == 'ALS') ? 'selected' : ''; ?>>
                                            ALS</option>
                                        <option value="TESDA" <?php echo ($educationalLevel == 'TESDA') ? 'selected' : ''; ?>>TESDA</option>
                                    </select>
                                    <label for="educationalLevel">Educational Level</label>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6 col-6 mb-3" id="shsTrackDiv" style="display:none;">
                                <div class="form-floating">
                                    <select class="form-select" id="shsTrack" name="shsTrack" disabled>
                                        <option value="" disabled <?php echo empty($shsTrack) ? 'selected' : ''; ?>>
                                            Select Track</option>
                                        <option value="STEM" <?php echo (isset($shsTrack) && $shsTrack == 'STEM') ? 'selected' : ''; ?>>STEM</option>
                                        <option value="ABM" <?php echo (isset($shsTrack) && $shsTrack == 'ABM') ? 'selected' : ''; ?>>ABM</option>
                                        <option value="HUMMS" <?php echo (isset($shsTrack) && $shsTrack == 'HUMMS') ? 'selected' : ''; ?>>HUMMS</option>
                                        <option value="ICT" <?php echo (isset($shsTrack) && $shsTrack == 'ICT') ? 'selected' : ''; ?>>ICT</option>
                                        <option value="GAS" <?php echo (isset($shsTrack) && $shsTrack == 'GAS') ? 'selected' : ''; ?>>GAS</option>
                                        <option value="TVL" <?php echo (isset($shsTrack) && $shsTrack == 'GAS') ? 'selected' : ''; ?>>TVL</option>
                                    </select>
                                    <label for="shsTrack">Senior High Track</label>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6 col-6 mb-3" id="collegeCourseDiv" style="display:none;">
                                <div class="form-floating">
                                    <select class="form-select" id="collegeCourse" name="collegeCourse" disabled>
                                        <option value="" disabled <?php echo empty($collegeCourse) ? 'selected' : ''; ?>>Select Course</option>
                                        <option value="BSIT" <?php echo (isset($collegeCourse) && $collegeCourse == 'BSIT') ? 'selected' : ''; ?>>BSIT</option>
                                        <option value="BSECE" <?php echo (isset($collegeCourse) && $collegeCourse == 'BSECE') ? 'selected' : ''; ?>>BSECE</option>
                                        <option value="BSEE" <?php echo (isset($collegeCourse) && $collegeCourse == 'BSEE') ? 'selected' : ''; ?>>BSEE</option>
                                        <option value="BSBA" <?php echo (isset($collegeCourse) && $collegeCourse == 'BSBA') ? 'selected' : ''; ?>>BSBA</option>
                                    </select>
                                    <label for="collegeCourse">College Course</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-5 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text"
                                        class="form-control <?php echo ($incomplete && empty($lengthOfStay)) ? 'border border-warning' : ''; ?>"
                                        id="lengthOfStay" name="lengthOfStay"
                                        value="<?php echo isset($lengthOfStay) ? (int) $lengthOfStay . ((int) $lengthOfStay == 1 ? ' year' : ' years') : ''; ?>"
                                        placeholder="Length Of Stay (in years)"
                                        oninput="if(this.value.length > 2) this.value = this.value.slice(0, 2);" min="0"
                                        onkeydown="return !['e','E','-','+','.',','].includes(event.key)" disabled>
                                    <label for="lengthOfStay">Length Of Stay (in years)</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-5 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text"
                                        class="form-control <?php echo ($incomplete && empty($residencyType)) ? 'border border-warning' : ''; ?>"
                                        id="residencyType" name="residencyType" value="<?php echo $residencyType ?>"
                                        placeholder="Type of Residency" readonly disabled>
                                    <label for="residencyType">Type of Residency</label>
                                    <input type="hidden" id="residencyTypeHidden" name="residencyType">
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-7 col-12 mb-4">
                                <div class="form-floating">
                                    <select class="form-select" id="remarks" name="remarks" disabled>
                                        <option value="" disabled <?php echo empty($remarks) ? 'selected' : ''; ?>>
                                            Select Remarks</option>
                                        <option value="No Derogatory" <?php echo ($remarks == 'No Derogatory') ? 'selected' : ''; ?>>No Derogatory</option>
                                        <option value="With Derogatory" <?php echo ($remarks == 'With Derogatory') ? 'selected' : ''; ?>>With Derogatory</option>
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
                                    <input type="tel"
                                        class="form-control <?php echo ($incomplete && empty($phoneNumber)) ? 'border border-warning' : ''; ?>"
                                        id="phoneNumber" name="phoneNumber" value="<?php echo $phoneNumber ?>"
                                        placeholder="Phone Number" inputmode="numeric" pattern="^09\d{9}$"
                                        maxlength="11"
                                        title="Phone number must start with 09 and be exactly 11 digits (e.g., 09123456789)"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '');" disabled>
                                    <label for="phoneNumber">Phone Number</label>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6 col-12 mb-4">
                                <div class="form-floating">
                                    <input type="email"
                                        class="form-control <?php echo ($incomplete && empty($email)) ? 'border border-warning' : ''; ?>"
                                        id="email" name="email" value="<?php echo $email ?>" placeholder="Email Address"
                                        disabled>
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
                                    <select
                                        class="form-select <?php echo ($incomplete && empty($provinceName)) ? 'border border-warning' : ''; ?>"
                                        id="province" name="provinceName" data-saved="<?php echo $provinceName; ?>"
                                        disabled>
                                        <option value="<?php echo $provinceName; ?>" selected>
                                            <?php echo $provinceName ? $provinceName : 'Select Province'; ?>
                                        </option>
                                    </select>
                                    <label for="province">Province</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-12 mb-3">
                                <div class="form-floating">
                                    <select
                                        class="form-select <?php echo ($incomplete && empty($cityName)) ? 'border border-warning' : ''; ?>"
                                        id="city" name="cityName" data-saved="<?php echo $cityName; ?>" disabled>
                                        <option value="<?php echo $cityName; ?>" selected>
                                            <?php echo $cityName ? $cityName : 'Select City'; ?>
                                        </option>
                                    </select>
                                    <label for="city">City</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-12 mb-3">
                                <div class="form-floating">
                                    <select
                                        class="form-select <?php echo ($incomplete && empty($barangayName)) ? 'border border-warning' : ''; ?>"
                                        id="barangay" name="barangayName" data-saved="<?php echo $barangayName; ?>"
                                        disabled>
                                        <option value="<?php echo $barangayName; ?>" selected>
                                            <?php echo $barangayName ? $barangayName : 'Select Barangay'; ?>
                                        </option>
                                    </select>
                                    <label for="barangay">Barangay</label>
                                </div>
                            </div>



                            <div class="col-lg-3 col-md-6 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="street" name="streetName"
                                        value="<?php echo $streetName ?>" placeholder="Street" disabled>
                                    <label for="street">Street</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="blockLotNo" name="blockLotNo"
                                        value="<?php echo $blockLotNo ?>" placeholder="Block & Lot/House No." disabled>
                                    <label for="blockLotNo">Block & Lot/House No.</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="phase" name="phase"
                                        value="<?php echo $phase ?>" placeholder="Phase" disabled>
                                    <label for="phase">Phase</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="subdivision" name="subdivisionName"
                                        value="<?php echo $subdivisionName ?>" placeholder="Subdivision" disabled>
                                    <label for="subdivision">Subdivision</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-4">
                                <div class="form-floating">
                                    <input type="text"
                                        class="form-control <?php echo ($incomplete && empty($purok)) ? 'border border-warning' : ''; ?>"
                                        id="purok" name="purok" value="<?php echo $purok ?>" placeholder="Purok"
                                        disabled>
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

                            <div class="col-12 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="sameAsCurrent" disabled>
                                    <label class="form-check-label" for="sameAsCurrent">Use current address as the
                                        permanent address</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-12 mb-3">
                                <div class="form-floating">
                                    <select
                                        class="form-select <?php echo ($incomplete && empty($permanentProvinceName)) ? 'border border-warning' : ''; ?>"
                                        id="permanentProvince" name="permanentProvinceName"
                                        data-saved="<?php echo $permanentProvinceName; ?>" disabled>
                                        <option value="<?php echo $permanentProvinceName; ?>" selected>
                                            <?php echo $permanentProvinceName ? $permanentProvinceName : 'Select Province'; ?>
                                        </option>
                                    </select>
                                    <label for="permanentProvince">Province</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-12 mb-3">
                                <div class="form-floating">
                                    <select
                                        class="form-select <?php echo ($incomplete && empty($permanentCityName)) ? 'border border-warning' : ''; ?>"
                                        id="permanentCity" name="permanentCityName"
                                        data-saved="<?php echo $permanentCityName; ?>" disabled>
                                        <option value="<?php echo $permanentCityName; ?>" selected>
                                            <?php echo $permanentCityName ? $permanentCityName : 'Select City'; ?>
                                        </option>
                                    </select>
                                    <label for="permanentCity">City</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-12 mb-3">
                                <div class="form-floating">
                                    <select
                                        class="form-select <?php echo ($incomplete && empty($permanentBarangayName)) ? 'border border-warning' : ''; ?>"
                                        id="permanentBarangay" name="permanentBarangayName"
                                        data-saved="<?php echo $permanentBarangayName; ?>" disabled>
                                        <option value="<?php echo $permanentBarangayName; ?>" selected>
                                            <?php echo $permanentBarangayName ? $permanentBarangayName : 'Select Barangay'; ?>
                                        </option>
                                    </select>
                                    <label for="permanentBarangay">Barangay</label>
                                </div>
                            </div>


                            <div class="col-lg-3 col-md-6 col-12 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="permanentStreet"
                                        name="permanentStreetName" value="<?php echo $permanentStreetName ?>"
                                        placeholder="Street" disabled>
                                    <label for="permanentStreet">Street</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="permanentBlockLotNo"
                                        name="permanentBlockLotNo" value="<?php echo $permanentBlockLotNo ?>"
                                        placeholder="Block & Lot/House No." disabled>
                                    <label for="permanentBlockLotNo">Block & Lot/House No.</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="permanentPhase" name="permanentPhase"
                                        value="<?php echo $permanentPhase ?>" placeholder="Phase" disabled>
                                    <label for="permanentPhase">Phase</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="permanentSubdivisionName"
                                        name="permanentSubdivisionName" value="<?php echo $permanentSubdivisionName ?>"
                                        placeholder="Subdivision" disabled>
                                    <label for="permanentSubdivisionName">Subdivision</label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6 mb-3">
                                <div class="form-floating">
                                    <input type="text"
                                        class="form-control <?php echo ($incomplete && empty($permanentPurok)) ? 'border border-warning' : ''; ?>"
                                        id="permanentPurok" name="permanentPurok" value="<?php echo $permanentPurok ?>"
                                        placeholder="Purok" disabled>
                                    <label for="permanentPurok">Purok</label>
                                </div>
                            </div>

                            <div class="col-12 mb-3" id="foreignAddressDiv" style="display: none;">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="foreignPermanentAddress"
                                        name="foreignPermanentAddress"
                                        value="<?php echo $userDataRow['foreignPermanentAddress'] ?? ''; ?>"
                                        placeholder="Enter foreign permanent address" disabled>
                                    <label for="foreignPermanentAddress">Foreign Permanent Address</label>
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
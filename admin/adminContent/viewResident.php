<?php
include_once __DIR__ . '/../../sharedAssets/connect.php';

// Add this debugging code
if (!isset($conn)) {
  die("Database connection failed! Check connect.php path and content.");
}
if (!$conn) {
  die("Database connection error: " . mysqli_connect_error());
}

// Rest of your code...


// ============================================
// FIXED SECTION 1: POST Handler with Prepared Statements
// ============================================
// Replace the entire POST handler at the top of your file with this:

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['saveUser'])) {
  $userID = intval($_POST['userID']);

  // Escape all input values to prevent SQL injection and allow special characters
  $firstName = mysqli_real_escape_string($conn, $_POST['firstName']);
  $middleName = mysqli_real_escape_string($conn, $_POST['middleName']);
  $lastName = mysqli_real_escape_string($conn, $_POST['lastName']);
  $suffix = mysqli_real_escape_string($conn, $_POST['suffix']);
  $phoneNumber = mysqli_real_escape_string($conn, $_POST['phoneNumber']);
  $gender = mysqli_real_escape_string($conn, $_POST['gender']);
  $age = mysqli_real_escape_string($conn, $_POST['age']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $birthDate = mysqli_real_escape_string($conn, $_POST['birthDate']);
  $birthPlace = mysqli_real_escape_string($conn, $_POST['birthPlace']);
  $civilStatus = mysqli_real_escape_string($conn, $_POST['civilStatus']);
  $citizenship = mysqli_real_escape_string($conn, $_POST['citizenship']);
  $occupation = mysqli_real_escape_string($conn, $_POST['occupation']);

  // ADD THIS SECTION - Educational fields handling
  $educationalLevel = NULL;
  $shsTrack = NULL;
  $collegeCourse = NULL;

  $educationalLevel = isset($_POST['educationalLevel']) && !empty($_POST['educationalLevel'])
    ? mysqli_real_escape_string($conn, $_POST['educationalLevel'])
    : NULL;

  if ($educationalLevel !== NULL) {
    $levelLower = strtolower($educationalLevel);

    if (strpos($levelLower, 'senior high') !== false) {
      // Check if user selected "Others" option
      if (isset($_POST['shsTrack']) && $_POST['shsTrack'] === 'Others') {
        $shsTrack = isset($_POST['shsTrackOther']) && !empty($_POST['shsTrackOther'])
          ? mysqli_real_escape_string($conn, $_POST['shsTrackOther'])
          : NULL;
      } else {
        $shsTrack = isset($_POST['shsTrack']) && !empty($_POST['shsTrack'])
          ? mysqli_real_escape_string($conn, $_POST['shsTrack'])
          : NULL;
      }
    } else if (strpos($levelLower, 'college') !== false) {
      if (isset($_POST['collegeCourse']) && $_POST['collegeCourse'] === 'Others') {
        $collegeCourse = isset($_POST['collegeCourseOther']) && !empty($_POST['collegeCourseOther'])
          ? mysqli_real_escape_string($conn, $_POST['collegeCourseOther'])
          : NULL;
      } else {
        $collegeCourse = isset($_POST['collegeCourse']) && !empty($_POST['collegeCourse'])
          ? mysqli_real_escape_string($conn, $_POST['collegeCourse'])
          : NULL;
      }
    }
  }
  $residencyType = mysqli_real_escape_string($conn, $_POST['residencyType']);
  $isVoter = mysqli_real_escape_string($conn, $_POST['isVoter']);
  $isOSY = mysqli_real_escape_string($conn, $_POST['isOSY']);
  $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);

  // NEW: Add blood type and length of stay
  $bloodType = mysqli_real_escape_string($conn, $_POST['bloodType']);
  $lengthOfStay = mysqli_real_escape_string($conn, $_POST['lengthOfStay']);

  // Escape address fields (this allows commas and special characters)
  $presentBlockLotNo = mysqli_real_escape_string($conn, $_POST['presentBlockLotNo']);
  $presentStreetName = mysqli_real_escape_string($conn, $_POST['presentStreetName']);
  $presentPhase = mysqli_real_escape_string($conn, $_POST['presentPhase']);
  $presentSubdivision = mysqli_real_escape_string($conn, $_POST['presentSubdivision']);
  $presentBarangay = mysqli_real_escape_string($conn, $_POST['presentBarangay']);
  $presentCity = mysqli_real_escape_string($conn, $_POST['presentCity']);
  $presentProvince = mysqli_real_escape_string($conn, $_POST['presentProvince']);
  $presentPurok = mysqli_real_escape_string($conn, $_POST['presentPurok']);

  $permanentBlockLotNo = mysqli_real_escape_string($conn, $_POST['permanentBlockLotNo'] ?? '');
  $permanentStreetName = mysqli_real_escape_string($conn, $_POST['permanentStreetName'] ?? '');
  $permanentPhase = mysqli_real_escape_string($conn, $_POST['permanentPhase'] ?? '');
  $permanentSubdivision = mysqli_real_escape_string($conn, $_POST['permanentSubdivision'] ?? '');
  $permanentBarangay = mysqli_real_escape_string($conn, $_POST['permanentBarangay'] ?? '');
  $permanentCity = mysqli_real_escape_string($conn, $_POST['permanentCity'] ?? '');
  $permanentProvince = mysqli_real_escape_string($conn, $_POST['permanentProvince'] ?? '');
  $permanentPurok = mysqli_real_escape_string($conn, $_POST['permanentPurok'] ?? '');

  // Update user table
  mysqli_query($conn, "UPDATE users SET phoneNumber='$phoneNumber', email='$email' WHERE userID=$userID");

  // FIXED: Update userInfo table - NOW INCLUDES bloodType and lengthOfStay
  // FIXED: Update userInfo table - NOW INCLUDES bloodType, lengthOfStay, AND educational fields
  mysqli_query($conn, "UPDATE userinfo SET 
  firstName='$firstName', 
  middleName='$middleName', 
  lastName='$lastName', 
  suffix='$suffix', 
  gender='$gender', 
  age='$age', 
  birthDate='$birthDate', 
  birthPlace='$birthPlace', 
  bloodType='$bloodType',
  civilStatus='$civilStatus', 
  citizenship='$citizenship', 
  occupation='$occupation', 
  lengthOfStay='$lengthOfStay',
  educationalLevel = " . ($educationalLevel !== NULL ? "'$educationalLevel'" : "NULL") . ",
  shsTrack = " . ($shsTrack !== NULL ? "'$shsTrack'" : "NULL") . ",
  collegeCourse = " . ($collegeCourse !== NULL ? "'$collegeCourse'" : "NULL") . ",
  isVoter='$isVoter', 
  remarks='$remarks', 
  residencyType='$residencyType', 
  isOSY='$isOSY'
WHERE userID=$userID");

  $getInfo = mysqli_query($conn, "SELECT userInfoID FROM userinfo WHERE userID = $userID");
  $row = mysqli_fetch_assoc($getInfo);
  $userInfoID = $row['userInfoID'];

  // Update Present Address (commas now work!)
  mysqli_query($conn, "UPDATE addresses SET 
    blockLotNo='$presentBlockLotNo', 
    streetName='$presentStreetName', 
    phase='$presentPhase', 
    subdivisionName='$presentSubdivision', 
    barangayName='$presentBarangay', 
    cityName='$presentCity', 
    provinceName='$presentProvince', 
    purok='$presentPurok'
  WHERE userInfoID = $userInfoID");

  // Get foreign address if provided
  $foreignPermanentAddress = isset($_POST['foreignPermanentAddress']) ? mysqli_real_escape_string($conn, $_POST['foreignPermanentAddress']) : '';

  // Update Permanent Address
  mysqli_query($conn, "UPDATE permanentaddresses SET
  permanentBlockLotNo=" . ($citizenship === 'FILIPINO' ? "'$permanentBlockLotNo'" : "NULL") . ",
  permanentStreetName=" . ($citizenship === 'FILIPINO' ? "'$permanentStreetName'" : "NULL") . ",
  permanentPhase=" . ($citizenship === 'FILIPINO' ? "'$permanentPhase'" : "NULL") . ",
  permanentSubdivisionName=" . ($citizenship === 'FILIPINO' ? "'$permanentSubdivision'" : "NULL") . ",
  permanentBarangayName=" . ($citizenship === 'FILIPINO' ? "'$permanentBarangay'" : "NULL") . ",
  permanentCityName=" . ($citizenship === 'FILIPINO' ? "'$permanentCity'" : "NULL") . ",
  permanentProvinceName=" . ($citizenship === 'FILIPINO' ? "'$permanentProvince'" : "NULL") . ",
  permanentPurok=" . ($citizenship === 'FILIPINO' ? "'$permanentPurok'" : "NULL") . ",
  foreignPermanentAddress=" . ($citizenship !== 'FILIPINO' ? "'$foreignPermanentAddress'" : "NULL") . "
WHERE userInfoID = $userInfoID");

  if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] === UPLOAD_ERR_OK) {
    $uploadProfilePicture = $_FILES['profilePicture']['name'];
    $targetPath = "../../uploads/profiles/" . basename($uploadProfilePicture);

    if (move_uploaded_file($_FILES['profilePicture']['tmp_name'], $targetPath)) {
      $profileUpdateQuery = "UPDATE userinfo SET profilePicture = '$uploadProfilePicture' WHERE userInfoID = $userInfoID";
      $profileUpdateResult = executeQuery($profileUpdateQuery);
    }
  }

  header("Location: viewResident.php?userID=$userID");
  exit;
}

if (isset($_GET['userID'])) {
  $userID = intval($_GET['userID']);
  $sql = "SELECT
  u.userID, u.phoneNumber, u.email, u.role, u.isNew,
  i.userInfoID, CONCAT(i.firstName, ' ', i.middleName, ' ', i.lastName, ' ', i.suffix) AS fullname, i.firstName, i.middleName, i.lastName, i.suffix,
  i.gender, i.age, i.bloodType, i.birthDate, i.birthPlace, i.profilePicture,
  i.residencyType, i.lengthOfStay, i.civilStatus, i.citizenship, i.occupation, isVoter, remarks, isOSY,
  i.educationalLevel, i.shsTrack, i.collegeCourse,

  -- PRESENT ADDRESS
  a.blockLotNo AS presentBlockLotNo,
  a.streetName AS presentStreetName,
  a.phase AS presentPhase,
  a.subdivisionName AS presentSubdivision,
  a.barangayName AS presentBarangay,
  a.cityName AS presentCity,
  a.provinceName AS presentProvince,
  a.purok AS presentPurok,

  -- PERMANENT ADDRESS
  pa.permanentBlockLotNo AS permanentBlockLotNo,
  pa.permanentStreetName AS permanentStreetName,
  pa.permanentPhase AS permanentPhase,
  pa.permanentSubdivisionName AS permanentSubdivision,
  pa.permanentBarangayName AS permanentBarangay,
  pa.permanentCityName AS permanentCity,
  pa.permanentProvinceName AS permanentProvince,
  pa.permanentPurok AS permanentPurok,
  pa.foreignPermanentAddress AS foreignPermanentAddress


FROM users u
INNER JOIN userinfo i ON i.userID = u.userID
LEFT JOIN addresses a ON a.userInfoID = i.userInfoID
LEFT JOIN permanentaddresses pa ON pa.userInfoID = i.userInfoID
WHERE u.userID = $userID";


  $result = mysqli_query($conn, $sql);
  if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
  } else {
    echo "❌ No resident found with this ID.";
    exit;
  }
} else {
  echo "⚠️ No resident ID provided.";
  exit;
}

$userInfoID = $user['userInfoID'];

if (isset($_POST['confirmButton'])) {
  $updateProfilePictureQuery = "UPDATE userinfo SET profilePicture = 'defaulProfile.png' WHERE userInfoID = $userInfoID";
  $updateProfilePictureResult = executeQuery($updateProfilePictureQuery);

  header("Location: viewResident.php?userID=$userID");
  exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Resident Details</title>
  <link rel="icon" href="../../assets/images/logoSanAntonio.png">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8f9fa;
    }
  </style>
</head>

<body>
  <div class="container-fluid p-3 p-md-4">
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="userID" value="<?= $user['userID'] ?>">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header text-white" style="background-color: #31afab;">
              <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                  <i class="fa-solid fa-user me-3 fs-4"></i>
                  <div>
                    <h4 class="mb-0 fw-semibold">Resident Details</h4>
                  </div>
                </div>
                <div class="d-flex gap-2">
                  <a href="../index.php?page=resident" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                  </a>
                </div>
              </div>
            </div>
          </div>

          <div class="row g-4">
            <div class="col-lg-8">
              <div class="card h-100">
                <div class="card-header bg-light">
                  <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0" style="color: black;">
                      <i class="fas fa-info-circle me-2"></i>Resident Information
                    </h5>
                  </div>
                </div>
                <div class="card-body" id="residentInfo">

                  <!-- Viewable Fields -->
                  <div class="row g-3" style="color: black;">
                    <div class="col-3 view-mode">
                      <div class="info-row ">
                        <?php if (empty($userDataRow['profilePicture'])) { ?>
                          <img src="../../uploads/profiles/defaultProfile.png"
                            style="width: 250px; height: 250px; object-fit: cover;" alt="Resident Profile">
                        <?php } else { ?>
                          <img src="../../uploads/profiles/<?= htmlspecialchars($user['profilePicture']) ?>"
                            style="width: 250px; height: 250px; object-fit: cover;" alt="Resident Profile">
                        <?php } ?>
                      </div>
                    </div>
                    <div class="col-9">
                      <div class="row">
                        <div class="col-md-4 mb-3 view-mode">
                          <div class="info-row">
                            <strong>Resident Name:</strong>
                            <div class="mt-1"><?= htmlspecialchars($user['fullname'] ?? 'N/A') ?></div>
                          </div>
                        </div>
                        <div class="col-md-4 mb-3 view-mode">
                          <div class="info-row">
                            <strong>Email:</strong>
                            <div class="mt-1"><?= htmlspecialchars($user['email'] ?? 'N/A') ?></div>
                          </div>
                        </div>
                        <div class="col-md-4 mb-3 view-mode">
                          <div class="info-row">
                            <strong>Phone Number:</strong>
                            <div class="mt-1"><?= htmlspecialchars($user['phoneNumber'] ?? 'N/A') ?></div>
                          </div>
                        </div>
                        <div class="col-md-4 my-3 view-mode">
                          <div class="info-row">
                            <strong>Date of Birth:</strong>
                            <div class="mt-1"><?= date('F d, Y', strtotime($user['birthDate'] ?? 'N/A')) ?></div>
                          </div>
                        </div>
                        <div class="col-md-4 my-3 view-mode">
                          <div class="info-row">
                            <strong>Age:</strong>
                            <div class="mt-1">
                              <?= !empty($user['age']) ? htmlspecialchars($user['age']) . ' ' . ((int) $user['age'] === 1 ? 'year' : 'years') . ' old' : 'N/A' ?>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4 my-3 view-mode">
                          <div class="info-row">
                            <strong>Place of Birth:</strong>
                            <div class="mt-1"><?= htmlspecialchars($user['birthPlace'] ?? 'N/A') ?></div>
                          </div>
                        </div>
                        <div class="col-md-4 my-3 view-mode">
                          <div class="info-row">
                            <strong>Gender:</strong>
                            <div class="mt-1"><?= htmlspecialchars($user['gender'] ?? 'N/A') ?></div>
                          </div>
                        </div>
                        <div class="col-md-4 my-3 view-mode">
                          <div class="info-row">
                            <strong>Blood Type:</strong>
                            <div class="mt-1"><?= htmlspecialchars($user['bloodType'] ?? 'N/A') ?></div>
                          </div>
                        </div>
                        <div class="col-md-4 my-3 view-mode">
                          <div class="info-row">
                            <strong>Civil Status:</strong>
                            <div class="mt-1"><?= htmlspecialchars($user['civilStatus'] ?? 'N/A') ?></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row g-3" style="color: black;">
                    <div class="col">
                      <div class="row">
                        <div class="col-md-3 my-3 view-mode">
                          <div class="info-row">
                            <strong>Citizenship:</strong>
                            <div class="mt-1"><?= htmlspecialchars($user['citizenship'] ?? 'N/A') ?></div>
                          </div>
                        </div>
                        <div class="col-md-3 my-3 view-mode">
                          <div class="info-row">
                            <strong>Occupation:</strong>
                            <div class="mt-1"><?= htmlspecialchars($user['occupation'] ?? 'N/A') ?></div>
                          </div>
                        </div>
                        <div class="col-md-3 my-3 view-mode">
                          <div class="info-row">
                            <strong>Educational Level:</strong>
                            <div class="mt-1"><?= htmlspecialchars($user['educationalLevel'] ?? 'N/A') ?></div>
                          </div>
                        </div>

                        <?php if (!empty($user['shsTrack']) && in_array($user['educationalLevel'], ['Senior High Undergraduate', 'Senior High Graduate'])): ?>
                          <div class="col-md-3 my-3 view-mode">
                            <div class="info-row">
                              <strong>Senior High Track:</strong>
                              <div class="mt-1"><?= htmlspecialchars($user['shsTrack'] ?? 'N/A') ?></div>
                            </div>
                          </div>
                        <?php endif; ?>

                        <?php if (!empty($user['collegeCourse']) && in_array($user['educationalLevel'], ['College Undergraduate', 'College Graduate'])): ?>
                          <div class="col-md-3 my-3 view-mode">
                            <div class="info-row">
                              <strong>College Course:</strong>
                              <div class="mt-1"><?= htmlspecialchars($user['collegeCourse'] ?? 'N/A') ?></div>
                            </div>
                          </div>
                        <?php endif; ?>

                        <div class="col-md-3 my-3 view-mode">
                          <div class="info-row">
                            <strong>Remarks:</strong>
                            <div class="mt-1"><?= htmlspecialchars($user['remarks'] ?? 'N/A') ?></div>
                          </div>
                        </div>
                        <div class="col-md-3 my-3 view-mode">
                          <div class="info-row">
                            <strong>Length Of Stay:</strong>
                            <div class="mt-1">
                              <span><?= !empty($user['lengthOfStay']) ? htmlspecialchars($user['lengthOfStay']) . ' ' . ((int) $user['lengthOfStay'] === 1 ? 'year' : 'years') : 'N/A' ?></span>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3 my-3 view-mode">
                          <div class="info-row">
                            <strong>Residency Type:</strong>
                            <div class="mt-1">
                              <span><?= htmlspecialchars($user['residencyType'] ?? 'N/A') ?></span>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3 my-3 view-mode">
                          <div class="info-row">
                            <strong>isVoter:</strong>
                            <div class="mt-1">
                              <span><?= htmlspecialchars($user['isVoter'] ?? 'N/A') ?></span>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3 my-3 view-mode">
                          <div class="info-row">
                            <strong>isOSY:</strong>
                            <div class="mt-1">
                              <span><?= htmlspecialchars($user['isOSY'] ?? 'N/A') ?></span>
                            </div>
                          </div>
                        </div>
                        <div class="col-3 my-3 view-mode">
                          <div class="info-row">
                            <strong>Address:</strong>
                            <div class="mt-1">
                              <?php
                              $addressParts = array_filter([
                                ucwords(strtolower($user['presentBlockLotNo'] ?? '')),
                                ucwords(strtolower($user['presentPurok'] ?? '')),
                                ucwords(strtolower($user['presentSubdivision'] ?? '')),
                                ucwords(strtolower($user['presentPhase'] ?? '')),
                                ucwords(strtolower($user['presentStreetName'] ?? '')),
                                ucwords(strtolower($user['presentBarangay'] ?? '')),
                                ucwords(strtolower($user['presentCity'] ?? '')),
                                ucwords(strtolower($user['presentProvince'] ?? ''))
                              ]);
                              $address = implode(', ', $addressParts); // Changed from ' ' to ', '
                              echo htmlspecialchars(!empty($address) ? $address : 'N/A');
                              ?>
                            </div>
                          </div>
                        </div>
                        <div class="col-3 my-3 view-mode">
                          <div class="info-row">
                            <strong>Permanent Address:</strong>
                            <div class="mt-1">
                              <?php
                              // Check if foreign citizen
                              if (isset($user['citizenship']) && strtoupper($user['citizenship']) !== 'FILIPINO') {
                                // Show foreign address
                                echo htmlspecialchars(!empty($user['foreignPermanentAddress']) ? $user['foreignPermanentAddress'] : 'N/A');
                              } else {
                                // Show Filipino address with commas
                                $addressParts = array_filter([
                                  ucwords(strtolower($user['permanentBlockLotNo'] ?? '')),
                                  ucwords(strtolower($user['permanentPurok'] ?? '')),
                                  ucwords(strtolower($user['permanentSubdivision'] ?? '')),
                                  ucwords(strtolower($user['permanentPhase'] ?? '')),
                                  ucwords(strtolower($user['permanentStreetName'] ?? '')),
                                  ucwords(strtolower($user['permanentBarangay'] ?? '')),
                                  ucwords(strtolower($user['permanentCity'] ?? '')),
                                  ucwords(strtolower($user['permanentProvince'] ?? ''))
                                ]);
                                $address = implode(', ', $addressParts);
                                echo htmlspecialchars(!empty($address) ? $address : 'N/A');
                              }
                              ?>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- Editable Fields -->
                  <div class="row g-3" style="color: black;">
                    <div class="col-md-3 edit-mode d-none" style="position: relative;">
                      <div class="info-row">

                        <div style="position: relative; width: 250px; height: 250px;"
                          onmouseover=" this.querySelector('img').style.filter='brightness(0.75)'; this.querySelector('.hoverBtn').style.opacity='1'; "
                          onmouseout=" this.querySelector('img').style.filter='brightness(1)'; this.querySelector('.hoverBtn').style.opacity='0';  ">

                          <?php if (empty($user['profilePicture'])) { ?>

                            <img src="../../uploads/profiles/defaultProfile.png" id="profilePreview"
                              style="width:250px; height:250px; object-fit:cover; transition:0.3s;" alt="Default Profile">

                            <label type="button" name="addButton" class="hoverBtn"
                              onclick="document.getElementById('profilePictureInput').click()"
                              style=" opacity:0; transition:0.3s; width:40px; height:40px; color:white; position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); border:none; background-color:#19AFA5; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center; ">
                              <i class="fa-solid fa-plus"></i>
                            </label>

                            <input type="file" name="profilePicture" class="form-control d-none" id="profilePictureInput"
                              accept="image/*" onchange="previewProfilePicture(event)">

                          <?php } else { ?>

                            <img src="../../uploads/profiles/<?= htmlspecialchars($user['profilePicture']) ?>"
                              style="width:250px; height:250px; object-fit:cover; transition:0.3s;"
                              alt="Resident Profile">

                            <button type="button" class="hoverBtn" onclick="deleteProfilePicture(<?= $user['userID'] ?>)"
                              style=" opacity:0; transition:0.3s; width:40px; height:40px; color:white; position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); border:none; background-color:#19AFA5; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center;"
                              data-bs-toggle="modal" data-bs-target="#removeProfileModal">
                              <i class="fa-solid fa-trash"></i>
                            </button>

                            <div class="modal fade" id="removeProfileModal" tabindex="-1"
                              aria-labelledby="removeProfileLabel" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">

                                  <div class="modal-header" style="background-color: #19AFA5; color: white;">
                                    <h1 class="modal-title fs-5" id="removeProfileLabel">Remove Profile
                                      Picture</h1>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                      aria-label="Close"></button>
                                  </div>

                                  <div class="modal-body">
                                    Are you sure you want to remove your profile picture?
                                    You can upload a new one later.
                                  </div>

                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                      data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary confirmButton" name="confirmButton">
                                      Confirm
                                    </button>
                                  </div>
                                </div>
                              </div>
                            </div>

                          <?php } ?>
                        </div>
                      </div>
                    </div>
                    <div class="col-9">
                      <div class="row">
                        <div class="col-md-4 mb-3 edit-mode d-none">
                          <div class="info-row">
                            <label for="firstName" class="form-label"><strong>First Name:</strong></label>
                            <input class="form-control" type="text" id="firstName" name="firstName"
                              value="<?= $user['firstName'] ?>">
                          </div>
                        </div>
                        <div class="col-md-3 mb-3 edit-mode d-none">
                          <div class="info-row">
                            <label for="middleName" class="form-label"><strong>Middle Name:</strong></label>
                            <input class="form-control" type="text" id="middleName" name="middleName"
                              value="<?= $user['middleName'] ?>">
                          </div>
                        </div>
                        <div class="col-md-3 mb-3 edit-mode d-none">
                          <div class="info-row">
                            <label for="lastName" class="form-label"><strong>Last Name:</strong></label>
                            <input class="form-control" type="text" id="lastName" name="lastName"
                              value="<?= $user['lastName'] ?>">
                          </div>
                        </div>
                        <div class="col-md-2 edit-mode d-none">
                          <div class="info-row">
                            <label for="suffix" class="form-label"><strong>Suffix:</strong></label>
                            <select class="form-select" id="suffix" name="suffix">
                              <option value="" disabled <?= empty($user['suffix']) ? 'selected' : '' ?>>Suffix</option>

                              <option value="Jr." <?= ($user['suffix'] === 'Jr.') ? 'selected' : '' ?>>Jr.</option>
                              <option value="Sr." <?= ($user['suffix'] === 'Sr.') ? 'selected' : '' ?>>Sr.</option>
                              <option value="II" <?= ($user['suffix'] === 'II') ? 'selected' : '' ?>>II</option>
                              <option value="III" <?= ($user['suffix'] === 'III') ? 'selected' : '' ?>>III</option>
                              <option value="IV" <?= ($user['suffix'] === 'IV') ? 'selected' : '' ?>>IV</option>
                              <option value="V" <?= ($user['suffix'] === 'V') ? 'selected' : '' ?>>V</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-5 mb-3 edit-mode d-none">
                          <div class="info-row">
                            <label for="email" class="form-label"><strong>Email:</strong></label>
                            <input class="form-control" type="email" id="email" name="email"
                              value="<?= $user['email'] ?>">
                          </div>
                        </div>
                        <div class="col-md-4 mb-3 edit-mode d-none">
                          <div class="info-row">
                            <label for="phoneNumber" class="form-label"><strong>Phone Number:</strong></label>
                            <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber"
                              value="<?= $user['phoneNumber'] ?>" placeholder="Phone Number" inputmode="numeric"
                              pattern="^09\d{9}$" maxlength="11"
                              title="Phone number must start with 09 and be exactly 11 digits (e.g., 09123456789)"
                              oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                          </div>
                        </div>
                        <div class="col-md-3 mb-3 edit-mode d-none">
                          <div class="info-row">
                            <label for="gender" class="form-label"><strong>Gender:</strong></label>
                            <select class="form-select" id="gender" name="gender">
                              <option value="" disabled <?= empty($user['gender']) ? 'selected' : '' ?>>Choose Gender
                              </option>
                              <option value="Male" <?= ($user['gender'] === 'Male.') ? 'selected' : '' ?>>Male</option>
                              <option value="Female" <?= ($user['gender'] === 'Female.') ? 'selected' : '' ?>> Female
                              </option>
                              <option value="Other" <?= ($user['gender'] === 'Others.') ? 'selected' : '' ?>> Others
                              </option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-3 mb-3 edit-mode d-none">
                          <div class="info-row">
                            <label for="birthDate" class="form-label"><strong>Birth Date:</strong></label>
                            <input type="date"
                              class="form-control <?= ($incomplete && empty($birthDate)) ? 'border border-warning' : ''; ?>"
                              id="birthDate" name="birthDate" value="<?php echo $user['birthDate'] ?? '' ?>"
                              placeholder="Date of Birth">
                          </div>
                        </div>
                        <div class="col-md-3 mb-3 edit-mode d-none">
                          <div class="info-row">
                            <?php
                            $age = (int) $user['age'];
                            $ageLabel = $age . ' ' . ($age === 1 ? 'year old' : 'years old');
                            ?>
                            <label for="age" class="form-label"><strong>Age:</strong></label>
                            <input type="text" class="form-control" id="age" value="<?= $ageLabel ?>" placeholder="Age"
                              readonly>
                            <input type="hidden" name="age" id="ageHidden" value="<?= $age ?? '' ?>">
                          </div>
                        </div>
                        <div class="col-md-6 mb-3 edit-mode d-none">
                          <div class="info-row">
                            <label for="birthPlace" class="form-label"><strong>Birth Place:</strong></label>
                            <input type="text" class="form-control" id="birthPlace" name="birthPlace"
                              value="<?= $user['birthPlace'] ?? '' ?>" placeholder="Place of Birth">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row g-3" style="color: black;">
                    <div class="col-md-3 mb-3 edit-mode d-none">
                      <div class="info-row">
                        <label for="civilStatus" class="form-label"><strong>Civil Status:</strong></label>
                        <select class="form-select" id="civilStatus" name="civilStatus">
                          <option value="" disabled <?= empty($user['civilStatus']) ? 'selected' : ''; ?>>Choose Civil
                            Status</option>
                          <option value="Single" <?= ($user['civilStatus'] === 'Single') ? 'selected' : ''; ?>>Single
                          </option>
                          <option value="Married" <?= ($user['civilStatus'] === 'Married') ? 'selected' : ''; ?>>Married
                          </option>
                          <option value="Divorced" <?= ($user['civilStatus'] === 'Divorced') ? 'selected' : ''; ?>>Divorced
                          </option>
                          <option value="Widowed" <?= ($user['civilStatus'] === 'Widowed') ? 'selected' : ''; ?>>Widowed
                          </option>
                          <option value="Separated" <?= ($user['civilStatus'] === 'Separated') ? 'selected' : ''; ?>>
                            Separated</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-3 mb-3 edit-mode d-none">
                      <label for="bloodType" class="form-label"><strong>Blood Type:</strong></label>
                      <select class="form-select" id="bloodType" name="bloodType">
                        <option value="" disabled <?= empty($user['bloodType']) ? 'selected' : ''; ?>>Choose Blood Type
                        </option>
                        <option value="A+" <?= ($user['bloodType'] === 'A+') ? 'selected' : ''; ?>>A+</option>
                        <option value="A-" <?= ($user['bloodType'] === 'A-') ? 'selected' : ''; ?>>A-</option>
                        <option value="B+" <?= ($user['bloodType'] === 'B+') ? 'selected' : ''; ?>>B+</option>
                        <option value="B-" <?= ($user['bloodType'] === 'B-') ? 'selected' : ''; ?>>B-</option>
                        <option value="AB+" <?= ($user['bloodType'] === 'AB+') ? 'selected' : ''; ?>>AB+</option>
                        <option value="AB-" <?= ($user['bloodType'] === 'AB-') ? 'selected' : ''; ?>>AB-</option>
                        <option value="O+" <?= ($user['bloodType'] === 'O+') ? 'selected' : ''; ?>>O+</option>
                        <option value="O-" <?= ($user['bloodType'] === 'O-') ? 'selected' : ''; ?>>O-</option>
                      </select>
                    </div>
                    <div class="col-md-3 my-3 edit-mode d-none">
                      <div class="info-row">
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
                        <label for="citizenship" class="form-label"><strong>Citizenship:</strong></label>
                        <select class="form-select text-uppercase" id="citizenship" name="citizenship">
                          <?php foreach ($citizenships as $country): ?>
                            <option value="<?= $country; ?>" <?= (!empty($citizenship) && strtoupper($citizenship) === $country) || (empty($citizenship) && $country === 'FILIPINO') ? 'selected' : ''; ?>>
                              <?= $country; ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-3 my-3 edit-mode d-none">
                      <div class="info-row">
                        <label for="occupation" class="form-label"><strong>Occupation:</strong></label>
                        <input type="text" class="form-control" id="occupation" name="occupation"
                          placeholder="Occupation" value="<?= $user['occupation'] ?? '' ?>">
                      </div>
                    </div>
                    <div class="col-md-3 my-3 edit-mode d-none">
                      <div class="info-row">
                        <label for="remarks" class="form-label"><strong>Remarks:</strong></label>
                        <select class="form-select" id="remarks" name="remarks">
                          <option value="" disabled <?= empty($user['remarks']) ? 'selected' : ''; ?>>Choose Remarks
                          </option>
                          <option value="No Derogatory Record" <?= ($user['remarks'] === 'No Derogatory Record') ? 'selected' : ''; ?>>No Derogatory Record</option>
                          <option value="With Derogatory Record" <?= ($user['remarks'] === 'With Derogatory Record') ? 'selected' : ''; ?>>With Derogatory Record</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-3 my-3 edit-mode d-none">
                      <div class="info-row">
                        <label for="lengthOfStay" class="form-label"><strong>Length of Stay:</strong></label>
                        <input type="number" class="form-control" id="lengthOfStay" name="lengthOfStay" min="0"
                          max="<?= $user['age'] ?>" value="<?= $user['lengthOfStay'] ?? '' ?>" placeholder="Years">
                        <!-- <small class="text-muted">Cannot exceed age (<?= $user['age'] ?> years)</small> -->
                      </div>
                    </div>
                    <div class="col-md-3 my-3 edit-mode d-none">
                      <div class="info-row">
                        <label for="residencyType" class="form-label"><strong>Residency Type:</strong></label>
                        <input type="text" class="form-control" id="residencyType" name="residencyType"
                          value="<?= $user['residencyType'] ?? '' ?>">
                      </div>
                    </div>
                    <div class="col-md-3 my-3 edit-mode d-none">
                      <div class="info-row">
                        <label for="isVoter" class="form-label"><strong>Registered Voter:</strong></label>
                        <input type="text" class="form-control" id="isVoter" name="isVoter"
                          value="<?= $user['isVoter'] ?>">
                      </div>
                    </div>
                    <div class="col-md-3 my-3 edit-mode d-none">
                      <div class="info-row">
                        <label for="isOSY" class="form-label"><strong>Out of School Youth:</strong></label>
                        <input type="text" class="form-control" id="isOSY" name="isOSY" value="<?= $user['isOSY'] ?>">
                      </div>
                    </div>

                    <div class="col-md-3 my-3 edit-mode d-none">
                      <div class="info-row">
                        <label for="occupation" class="form-label"><strong>Occupation:</strong></label>
                        <input type="text" class="form-control" id="occupation" name="occupation"
                          placeholder="Occupation" value="<?= htmlspecialchars($user['occupation'] ?? ''); ?>">
                      </div>
                    </div>

                    <!-- ADD EDUCATIONAL FIELDS -->
                    <div class="col-md-3 my-3 edit-mode d-none">
                      <div class="info-row">
                        <label for="educationalLevel" class="form-label"><strong>Educational Level:</strong></label>
                        <select class="form-control" id="educationalLevel" name="educationalLevel">
                          <option value="" disabled <?= empty($user['educationalLevel']) ? 'selected' : ''; ?>>
                            Select Level
                          </option>
                          <option value="None" <?= ($user['educationalLevel'] == 'None') ? 'selected' : ''; ?>>None
                          </option>
                          <option value="Elementary Undergraduate" <?= ($user['educationalLevel'] == 'Elementary Undergraduate') ? 'selected' : ''; ?>>
                            Elementary Undergraduate</option>
                          <option value="Elementary Graduate" <?= ($user['educationalLevel'] == 'Elementary Graduate') ? 'selected' : ''; ?>>
                            Elementary Graduate</option>
                          <option value="High School Undergraduate" <?= ($user['educationalLevel'] == 'High School Undergraduate') ? 'selected' : ''; ?>>
                            High School Undergraduate</option>
                          <option value="High School Graduate" <?= ($user['educationalLevel'] == 'High School Graduate') ? 'selected' : ''; ?>>
                            High School Graduate</option>
                          <option value="Senior High Undergraduate" <?= ($user['educationalLevel'] == 'Senior High Undergraduate') ? 'selected' : ''; ?>>
                            Senior High Undergraduate</option>
                          <option value="Senior High Graduate" <?= ($user['educationalLevel'] == 'Senior High Graduate') ? 'selected' : ''; ?>>
                            Senior High Graduate</option>
                          <option value="College Undergraduate" <?= ($user['educationalLevel'] == 'College Undergraduate') ? 'selected' : ''; ?>>
                            College Undergraduate</option>
                          <option value="College Graduate" <?= ($user['educationalLevel'] == 'College Graduate') ? 'selected' : ''; ?>>
                            College Graduate</option>
                          <option value="ALS" <?= ($user['educationalLevel'] == 'ALS') ? 'selected' : ''; ?>>ALS</option>
                          <option value="TESDA" <?= ($user['educationalLevel'] == 'TESDA') ? 'selected' : ''; ?>>TESDA
                          </option>
                        </select>
                      </div>
                    </div>

                    <?php
                    $predefinedTracks = ['STEM', 'ABM', 'HUMMS', 'ICT', 'GAS', 'TVL'];
                    $isCustomTrack = !empty($user['shsTrack']) && !in_array($user['shsTrack'], $predefinedTracks);
                    ?>

                    <div class="col-md-3 my-3 edit-mode d-none" id="shsTrackDiv" style="display:none;">
                      <div class="info-row">
                        <label for="shsTrack" class="form-label"><strong>Senior High Track:</strong></label>
                        <div class="position-relative">
                          <select class="form-select <?= $isCustomTrack ? 'd-none' : ''; ?>" id="shsTrack"
                            name="shsTrack" data-saved="<?= htmlspecialchars($user['shsTrack'] ?? ''); ?>">
                            <option value="" disabled <?= empty($user['shsTrack']) ? 'selected' : ''; ?>>Select Track
                            </option>
                            <option value="STEM" <?= (isset($user['shsTrack']) && $user['shsTrack'] == 'STEM') ? 'selected' : ''; ?>>STEM</option>
                            <option value="ABM" <?= (isset($user['shsTrack']) && $user['shsTrack'] == 'ABM') ? 'selected' : ''; ?>>ABM</option>
                            <option value="HUMMS" <?= (isset($user['shsTrack']) && $user['shsTrack'] == 'HUMMS') ? 'selected' : ''; ?>>HUMMS</option>
                            <option value="ICT" <?= (isset($user['shsTrack']) && $user['shsTrack'] == 'ICT') ? 'selected' : ''; ?>>ICT</option>
                            <option value="GAS" <?= (isset($user['shsTrack']) && $user['shsTrack'] == 'GAS') ? 'selected' : ''; ?>>GAS</option>
                            <option value="TVL" <?= (isset($user['shsTrack']) && $user['shsTrack'] == 'TVL') ? 'selected' : ''; ?>>TVL</option>
                            <option value="Others" <?= $isCustomTrack ? 'selected' : ''; ?>>Others | Specify</option>
                          </select>
                          <input type="text" class="form-control <?= $isCustomTrack ? '' : 'd-none'; ?>"
                            id="shsTrackText" name="shsTrackOther" placeholder="Specify SHS Track"
                            value="<?= $isCustomTrack ? htmlspecialchars($user['shsTrack']) : ''; ?>"
                            style="padding-right: 40px;">
                          <button type="button" class="btn btn-sm <?= $isCustomTrack ? '' : 'd-none'; ?>"
                            id="backToShsDropdownBtn"
                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); z-index: 10; padding: 2px 8px; font-size: 12px;"
                            title="Back to dropdown">
                            <i class="fa-solid fa-arrow-left"></i>
                          </button>
                        </div>
                      </div>
                    </div>

                    <?php
                    $predefinedCourses = ['BSIT', 'BSECE', 'BSEE', 'BSBA', 'BSTM', 'BSHRM', 'BSED', 'BSCE', 'BSME'];
                    $isCustomCourse = !empty($user['collegeCourse']) && !in_array($user['collegeCourse'], $predefinedCourses);
                    ?>

                    <div class="col-md-3 my-3 edit-mode d-none" id="collegeCourseDiv" style="display:none;">
                      <div class="info-row">
                        <label for="collegeCourse" class="form-label"><strong>College Course:</strong></label>
                        <div class="position-relative">
                          <select class="form-select <?= $isCustomCourse ? 'd-none' : ''; ?>" id="collegeCourse"
                            name="collegeCourse" data-saved="<?= htmlspecialchars($user['collegeCourse'] ?? ''); ?>">
                            <option value="" disabled <?= empty($user['collegeCourse']) ? 'selected' : ''; ?>>Select
                              Course</option>
                            <option value="BSIT" <?= (isset($user['collegeCourse']) && $user['collegeCourse'] == 'BSIT') ? 'selected' : ''; ?>>BSIT</option>
                            <option value="BSECE" <?= (isset($user['collegeCourse']) && $user['collegeCourse'] == 'BSECE') ? 'selected' : ''; ?>>BSECE</option>
                            <option value="BSEE" <?= (isset($user['collegeCourse']) && $user['collegeCourse'] == 'BSEE') ? 'selected' : ''; ?>>BSEE</option>
                            <option value="BSBA" <?= (isset($user['collegeCourse']) && $user['collegeCourse'] == 'BSBA') ? 'selected' : ''; ?>>BSBA</option>
                            <option value="BSTM" <?= (isset($user['collegeCourse']) && $user['collegeCourse'] == 'BSTM') ? 'selected' : ''; ?>>BSTM</option>
                            <option value="BSHRM" <?= (isset($user['collegeCourse']) && $user['collegeCourse'] == 'BSHRM') ? 'selected' : ''; ?>>BSHRM</option>
                            <option value="BSED" <?= (isset($user['collegeCourse']) && $user['collegeCourse'] == 'BSED') ? 'selected' : ''; ?>>BSED</option>
                            <option value="BSCE" <?= (isset($user['collegeCourse']) && $user['collegeCourse'] == 'BSCE') ? 'selected' : ''; ?>>BSCE</option>
                            <option value="BSME" <?= (isset($user['collegeCourse']) && $user['collegeCourse'] == 'BSME') ? 'selected' : ''; ?>>BSME</option>
                            <option value="Others" <?= $isCustomCourse ? 'selected' : ''; ?>>Others | Specify</option>
                          </select>
                          <input type="text" class="form-control <?= $isCustomCourse ? '' : 'd-none'; ?>"
                            id="collegeCourseText" name="collegeCourseOther" placeholder="Specify College Course"
                            value="<?= $isCustomCourse ? htmlspecialchars($user['collegeCourse']) : ''; ?>"
                            style="padding-right: 40px;">
                          <button type="button" class="btn btn-sm <?= $isCustomCourse ? '' : 'd-none'; ?>"
                            id="backToDropdownBtn"
                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); z-index: 10; padding: 2px 8px; font-size: 12px;"
                            title="Back to dropdown">
                            <i class="fa-solid fa-arrow-left"></i>
                          </button>
                        </div>
                      </div>
                    </div>

                    <!-- PRESENT ADDRESS -->

                    <div class="col-md-3 my-3 edit-mode d-none">
                      <label><strong>House / Block & Lot No:</strong></label>
                      <input type="text" class="form-control" name="presentBlockLotNo"
                        value="<?= $user['presentBlockLotNo'] ?>">
                    </div>

                    <div class="col-md-3 my-3 edit-mode d-none">
                      <label><strong>Purok:</strong></label>
                      <input type="text" class="form-control" name="presentPurok" value="<?= $user['presentPurok'] ?>">
                    </div>

                    <div class="col-md-3 my-3 edit-mode d-none">
                      <label><strong>Subdivision:</strong></label>
                      <input type="text" class="form-control" name="presentSubdivision"
                        value="<?= $user['presentSubdivision'] ?>">
                    </div>

                    <div class="col-md-3 my-3 edit-mode d-none">
                      <label><strong>Phase:</strong></label>
                      <input type="text" class="form-control" name="presentPhase" value="<?= $user['presentPhase'] ?>">
                    </div>

                    <div class="col-md-3 my-3 edit-mode d-none">
                      <label><strong>Street:</strong></label>
                      <input type="text" class="form-control" name="presentStreetName"
                        value="<?= $user['presentStreetName'] ?>">
                    </div>

                    <div class="col-md-3 my-3 edit-mode d-none">
                      <label><strong>Province:</strong></label>
                      <select class="form-select" id="province" name="presentProvince">
                        <option value="<?= $user['presentProvince'] ?>" selected>
                          <?= $user['presentProvince'] ?>
                        </option>
                      </select>
                    </div>

                    <div class="col-md-3 my-3 edit-mode d-none">
                      <label><strong>City:</strong></label>
                      <select class="form-select" id="city" name="presentCity">
                        <option value="<?= $user['presentCity'] ?>" selected>
                          <?= $user['presentCity'] ?>
                        </option>
                      </select>
                    </div>

                    <div class="col-md-3 my-3 edit-mode d-none">
                      <label><strong>Barangay:</strong></label>
                      <select class="form-select" id="barangay" name="presentBarangay">
                        <option value="<?= $user['presentBarangay'] ?>" selected>
                          <?= $user['presentBarangay'] ?>
                        </option>
                      </select>
                    </div>

                    <!-- PERMANENT ADDRESS -->

                    <div class="col-md-3 my-3 edit-mode d-none">
                      <label><strong>House / Block & Lot No:</strong></label>
                      <input type="text" class="form-control" name="permanentBlockLotNo"
                        value="<?= $user['permanentBlockLotNo'] ?>">
                    </div>

                    <div class="col-md-3 my-3 edit-mode d-none">
                      <label><strong>Purok:</strong></label>
                      <input type="text" class="form-control" name="permanentPurok"
                        value="<?= $user['permanentPurok'] ?>">
                    </div>

                    <div class="col-md-3 my-3 edit-mode d-none">
                      <label><strong>Subdivision:</strong></label>
                      <input type="text" class="form-control" name="permanentSubdivision"
                        value="<?= $user['permanentSubdivision'] ?>">
                    </div>

                    <div class="col-md-3 my-3 edit-mode d-none">
                      <label><strong>Phase:</strong></label>
                      <input type="text" class="form-control" name="permanentPhase"
                        value="<?= $user['permanentPhase'] ?>">
                    </div>

                    <div class="col-md-3 my-3 edit-mode d-none">
                      <label><strong>Street:</strong></label>
                      <input type="text" class="form-control" name="permanentStreetName"
                        value="<?= $user['permanentStreetName'] ?>">
                    </div>

                    <div class="col-md-3 my-3 edit-mode d-none">
                      <label><strong>Province:</strong></label>
                      <select class="form-select" id="permanentProvince" name="permanentProvince">
                        <option value="<?= $user['permanentProvince'] ?>" selected>
                          <?= $user['permanentProvince'] ?>
                        </option>
                      </select>
                    </div>

                    <div class="col-md-3 my-3 edit-mode d-none">
                      <label><strong>City:</strong></label>
                      <select class="form-select" id="permanentCity" name="permanentCity">
                        <option value="<?= $user['permanentCity'] ?>" selected>
                          <?= $user['permanentCity'] ?>
                        </option>
                      </select>
                    </div>

                    <div class="col-md-3 my-3 edit-mode d-none">
                      <label><strong>Barangay:</strong></label>
                      <select class="form-select" id="permanentBarangay" name="permanentBarangay">
                        <option value="<?= $user['permanentBarangay'] ?>" selected>
                          <?= $user['permanentBarangay'] ?>
                        </option>
                      </select>
                    </div>

                    <!-- After permanentPurok field, add this: -->
                    <div class="col-12 edit-mode d-none" id="foreignAddressDiv" style="display: none;">
                      <label><strong>Foreign Permanent Address:</strong></label>
                      <input type="text" class="form-control" name="foreignPermanentAddress"
                        id="foreignPermanentAddress"
                        value="<?= htmlspecialchars($user['foreignPermanentAddress'] ?? '') ?>">
                    </div>

                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-4">
              <div class="card">
                <div class="card-header bg-light">
                  <h5 class="card-title mb-0">
                    <i class="fas fa-tasks me-2"></i>Status & Actions
                  </h5>
                </div>
                <div class="card-body">
                  <div class="d-flex justify-content-end gap-2">
                    <a href="../index.php?page=resident" class="btn btn-secondary w-100 w-md-auto">
                      <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                    <button type="button" id="editBtn" class="btn btn-warning w-100 w-md-auto">
                      <i class="fas fa-edit me-1"></i> Edit
                    </button>
                  </div>
                  <div id="editActions" class="d-none mt-3">
                    <button type="submit" name="saveUser" class="btn btn-success me-2 w-100 w-md-auto">
                      <i class="fas fa-save me-1"></i> Save Changes
                    </button>
                    <button type="button" id="cancelEditBtn" class="btn btn-danger w-100 w-md-auto mt-2 mt-md-0">
                      <i class="fas fa-times me-1"></i> Cancel
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.getElementById('editBtn').addEventListener('click', function () {
      document.querySelectorAll('.view-mode').forEach(e => e.classList.add('d-none'));
      document.querySelectorAll('.edit-mode').forEach(e => e.classList.remove('d-none'));
      document.getElementById('editActions').classList.remove('d-none');
      this.classList.add('d-none');
    });

    document.getElementById('cancelEditBtn').addEventListener('click', function () {
      document.querySelectorAll('.view-mode').forEach(e => e.classList.remove('d-none'));
      document.querySelectorAll('.edit-mode').forEach(e => e.classList.add('d-none'));
      document.getElementById('editActions').classList.add('d-none');
      document.getElementById('editBtn').classList.remove('d-none');
    });

    function previewProfilePicture(event) {
      var file = event.target.files[0];
      var preview = document.getElementById('profilePreview');

      if (file && preview) {
        preview.src = URL.createObjectURL(file);
      }
    }

    // ===== AUTO-CALCULATE AGE =====
    function calculateAge(birthDateString) {
      if (!birthDateString) return '';

      const today = new Date();
      const birthDate = new Date(birthDateString);
      let age = today.getFullYear() - birthDate.getFullYear();
      const monthDiff = today.getMonth() - birthDate.getMonth();

      if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
      }

      return age;
    }

    // Add event listener for birthdate field
    const birthDateField = document.getElementById('birthDate');
    const lengthOfStayField = document.getElementById('lengthOfStay');

    if (birthDateField) {
      birthDateField.addEventListener('change', function () {
        const age = calculateAge(this.value);
        const ageField = document.getElementById('age');
        const ageHidden = document.getElementById('ageHidden');

        if (age !== '') {
          const ageLabel = age + ' ' + (age === 1 ? 'year old' : 'years old');
          ageField.value = ageLabel;
          ageHidden.value = age;

          // Update max value for lengthOfStay
          if (lengthOfStayField) {
            lengthOfStayField.setAttribute('max', age);

            // If current lengthOfStay exceeds new age, reset it
            if (parseInt(lengthOfStayField.value) > age) {
              lengthOfStayField.value = age;
            }
          }
        }
      });
    }

    // ===== VALIDATE LENGTH OF STAY =====
    if (lengthOfStayField) {
      lengthOfStayField.addEventListener('input', function () {
        const age = parseInt(document.getElementById('ageHidden')?.value || document.getElementById('age')?.value);
        const lengthOfStay = parseInt(this.value);

        if (lengthOfStay > age) {
          this.value = age;
        }

        if (lengthOfStay < 0) {
          this.value = 0;
        }
      });

      // Prevent typing negative numbers or values greater than age
      lengthOfStayField.addEventListener('keydown', function (e) {
        // Allow: backspace, delete, tab, escape, enter
        if ([46, 8, 9, 27, 13].indexOf(e.keyCode) !== -1 ||
          // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
          (e.keyCode === 65 && e.ctrlKey === true) ||
          (e.keyCode === 67 && e.ctrlKey === true) ||
          (e.keyCode === 86 && e.ctrlKey === true) ||
          (e.keyCode === 88 && e.ctrlKey === true) ||
          // Allow: home, end, left, right
          (e.keyCode >= 35 && e.keyCode <= 39)) {
          return;
        }

        // Prevent minus sign
        if (e.key === '-' || e.key === 'e' || e.key === '+' || e.key === '.') {
          e.preventDefault();
        }
      });
    }

    // ===== FORM SUBMISSION VALIDATION =====
    document.querySelector('form').addEventListener('submit', function (e) {
      const age = parseInt(document.getElementById('ageHidden')?.value || 0);
      const lengthOfStay = parseInt(document.getElementById('lengthOfStay')?.value || 0);

      if (lengthOfStay > age) {
        e.preventDefault();
        document.getElementById('lengthOfStay').value = age;
        document.getElementById('lengthOfStay').focus();
        return false;
      }
    });
  </script>
</body>


<script>
  // ========== ADDRESS HANDLING (Same as profile.php) ==========
  const provinceSelect = document.getElementById("province");
  const citySelect = document.getElementById("city");
  const barangaySelect = document.getElementById("barangay");

  const permanentProvinceSelect = document.getElementById("permanentProvince");
  const permanentCitySelect = document.getElementById("permanentCity");
  const permanentBarangaySelect = document.getElementById("permanentBarangay");

  // Get saved values from PHP
  const savedProvince = "<?= htmlspecialchars($user['presentProvince'] ?? '') ?>";
  const savedCity = "<?= htmlspecialchars($user['presentCity'] ?? '') ?>";
  const savedBarangay = "<?= htmlspecialchars($user['presentBarangay'] ?? '') ?>";

  const savedPermanentProvince = "<?= htmlspecialchars($user['permanentProvince'] ?? '') ?>";
  const savedPermanentCity = "<?= htmlspecialchars($user['permanentCity'] ?? '') ?>";
  const savedPermanentBarangay = "<?= htmlspecialchars($user['permanentBarangay'] ?? '') ?>";

  let jsonData = null;
  let isJsonLoaded = false;

  // Load JSON
  fetch("../../assets/json/philippine_provinces_cities_municipalities_and_barangays_2019v2.json")
    .then(response => response.json())
    .then(data => {
      jsonData = data;
      isJsonLoaded = true;

      // Helper functions (same as profile.php)
      function normalize(s) {
        if (!s) return '';
        return s.toString()
          .trim()
          .toLowerCase()
          .replace(/\b(city|municipality|municipal|province|of|the)\b/g, '')
          .replace(/[^a-z0-9\s]/g, '')
          .replace(/\s+/g, ' ')
          .trim();
      }

      function findProvinceKeyByName(name) {
        const target = normalize(name);
        if (!target) return null;
        for (const regionCode of Object.keys(jsonData)) {
          const provinces = jsonData[regionCode].province_list;
          for (const provKey of Object.keys(provinces)) {
            if (normalize(provKey) === target) return provKey;
          }
        }
        for (const regionCode of Object.keys(jsonData)) {
          const provinces = jsonData[regionCode].province_list;
          for (const provKey of Object.keys(provinces)) {
            if (normalize(provKey).includes(target) || target.includes(normalize(provKey))) return provKey;
          }
        }
        return null;
      }

      function findCityKeyByName(provinceKey, cityName) {
        if (!provinceKey) return null;
        const provinceObj = (() => {
          for (const rc of Object.keys(jsonData)) {
            const pList = jsonData[rc].province_list;
            if (pList[provinceKey]) return pList[provinceKey];
          }
          return null;
        })();
        if (!provinceObj) return null;
        const target = normalize(cityName);
        const cityList = provinceObj.municipality_list || {};
        for (const cityKey of Object.keys(cityList)) {
          if (normalize(cityKey) === target) return cityKey;
        }
        for (const cityKey of Object.keys(cityList)) {
          if (normalize(cityKey).includes(target) || target.includes(normalize(cityKey))) return cityKey;
        }
        return null;
      }

      function findBarangayByName(provinceKey, cityKey, barangayName) {
        if (!provinceKey || !cityKey) return null;
        const provinceObj = (() => {
          for (const rc of Object.keys(jsonData)) {
            const pList = jsonData[rc].province_list;
            if (pList[provinceKey]) return pList[provinceKey];
          }
          return null;
        })();
        if (!provinceObj) return null;
        const cityList = provinceObj.municipality_list || {};
        const cityObj = cityList[cityKey];
        if (!cityObj) return null;
        const barangays = cityObj.barangay_list || [];
        const target = normalize(barangayName);
        for (const b of barangays) {
          if (normalize(b) === target) return b;
        }
        for (const b of barangays) {
          if (normalize(b).includes(target) || target.includes(normalize(b))) return b;
        }
        return null;
      }

      // ========== POPULATE CURRENT ADDRESS ==========

      // Populate provinces
      provinceSelect.innerHTML = '<option value="">Select Province</option>';
      let savedProvinceKey = findProvinceKeyByName(savedProvince);

      const allProvinces = [];
      Object.keys(data).forEach(regionCode => {
        const provinces = data[regionCode].province_list;
        Object.keys(provinces).forEach(provinceKey => {
          allProvinces.push(provinceKey);
        });
      });
      allProvinces.sort((a, b) => a.localeCompare(b));

      allProvinces.forEach(provinceKey => {
        const opt = document.createElement("option");
        opt.value = provinceKey;
        opt.textContent = provinceKey;
        if (savedProvinceKey && provinceKey === savedProvinceKey) opt.selected = true;
        provinceSelect.appendChild(opt);
      });

      function populateCitiesByProvinceKey(provinceKey, tryToSelectCityName) {
        citySelect.innerHTML = '<option value="">Select City</option>';
        barangaySelect.innerHTML = '<option value="">Select Barangay</option>';

        if (!provinceKey || provinceKey.trim() === "") {
          citySelect.selectedIndex = 0;
          barangaySelect.selectedIndex = 0;
          return;
        }

        let municipality_list = null;
        for (const rc of Object.keys(jsonData)) {
          const pList = jsonData[rc].province_list;
          if (pList[provinceKey]) {
            municipality_list = pList[provinceKey].municipality_list;
            break;
          }
        }
        if (!municipality_list) {
          citySelect.selectedIndex = 0;
          return;
        }

        const sortedCities = Object.keys(municipality_list).sort((a, b) => a.localeCompare(b));
        sortedCities.forEach(cityKey => {
          const opt = document.createElement("option");
          opt.value = cityKey;
          opt.textContent = cityKey;
          citySelect.appendChild(opt);
        });

        if (tryToSelectCityName && tryToSelectCityName.toString().trim() !== "") {
          const matched = findCityKeyByName(provinceKey, tryToSelectCityName);
          if (matched) {
            citySelect.value = matched;
            populateBarangaysByKeys(provinceKey, matched, '');
          } else {
            citySelect.selectedIndex = 0;
          }
        } else {
          citySelect.selectedIndex = 0;
        }
      }
      window.populateCitiesByProvinceKey = populateCitiesByProvinceKey;

      function populateBarangaysByKeys(provinceKey, cityKey, tryToSelectBarangayName) {
        barangaySelect.innerHTML = '<option value="">Select Barangay</option>';

        if (!provinceKey || !cityKey || cityKey.toString().trim() === "") {
          setTimeout(() => {
            barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
            barangaySelect.selectedIndex = 0;
          }, 0);
          return;
        }

        let barangays = null;
        for (const rc of Object.keys(jsonData)) {
          const pList = jsonData[rc].province_list;
          if (pList[provinceKey]) {
            const cList = pList[provinceKey].municipality_list;
            if (cList && cList[cityKey]) {
              barangays = cList[cityKey].barangay_list;
            }
            break;
          }
        }
        if (!barangays || barangays.length === 0) {
          barangaySelect.selectedIndex = 0;
          return;
        }

        const sortedBarangays = [...barangays].sort((a, b) => a.localeCompare(b));
        sortedBarangays.forEach(b => {
          const opt = document.createElement("option");
          opt.value = b;
          opt.textContent = b;
          barangaySelect.appendChild(opt);
        });

        if (tryToSelectBarangayName && tryToSelectBarangayName.toString().trim() !== "") {
          const matched = findBarangayByName(provinceKey, cityKey, tryToSelectBarangayName);
          if (matched) {
            barangaySelect.value = matched;
          } else {
            barangaySelect.selectedIndex = 0;
          }
        } else {
          barangaySelect.selectedIndex = 0;
        }
      }
      window.populateBarangaysByKeys = populateBarangaysByKeys;

      // Restore saved current address
      if (savedProvince && savedProvince !== '') {
        if (!savedProvinceKey) savedProvinceKey = findProvinceKeyByName(savedProvince);

        if (savedProvinceKey) {
          provinceSelect.value = savedProvinceKey;

          if (savedCity && savedCity.trim() !== "") {
            const cityKey = findCityKeyByName(savedProvinceKey, savedCity);
            populateCitiesByProvinceKey(savedProvinceKey, savedCity);
            if (cityKey) {
              citySelect.value = cityKey;
              if (savedBarangay && savedBarangay.trim() !== "") {
                populateBarangaysByKeys(savedProvinceKey, cityKey, savedBarangay);
                const matchedBarangay = findBarangayByName(savedProvinceKey, cityKey, savedBarangay);
                if (matchedBarangay) barangaySelect.value = matchedBarangay;
              }
            }
          } else {
            populateCitiesByProvinceKey(savedProvinceKey, '');
            citySelect.selectedIndex = 0;
            barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
          }
        }
      }

      // Event listeners for current address
      provinceSelect.addEventListener("change", function () {
        const selectedProvinceKey = this.value;
        populateCitiesByProvinceKey(selectedProvinceKey, '');
        barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
      });

      citySelect.addEventListener("change", function () {
        const selectedProvinceKey = provinceSelect.value;
        const selectedCityKey = this.value;
        populateBarangaysByKeys(selectedProvinceKey, selectedCityKey, '');
      });

      // ========== POPULATE PERMANENT ADDRESS ==========

      permanentProvinceSelect.innerHTML = '<option value="">Select Province</option>';
      let savedPermanentProvinceKey = findProvinceKeyByName(savedPermanentProvince);

      allProvinces.forEach(provinceKey => {
        const opt = document.createElement("option");
        opt.value = provinceKey;
        opt.textContent = provinceKey;
        if (savedPermanentProvinceKey && provinceKey === savedPermanentProvinceKey) opt.selected = true;
        permanentProvinceSelect.appendChild(opt);
      });

      function populatePermanentCitiesByProvinceKey(provinceKey, tryToSelectCityName) {
        permanentCitySelect.innerHTML = '<option value="">Select City</option>';
        permanentBarangaySelect.innerHTML = '<option value="">Select Barangay</option>';

        if (!provinceKey || provinceKey.trim() === "") {
          permanentCitySelect.selectedIndex = 0;
          permanentBarangaySelect.selectedIndex = 0;
          return;
        }

        let municipality_list = null;
        for (const rc of Object.keys(jsonData)) {
          const pList = jsonData[rc].province_list;
          if (pList[provinceKey]) {
            municipality_list = pList[provinceKey].municipality_list;
            break;
          }
        }
        if (!municipality_list) {
          permanentCitySelect.selectedIndex = 0;
          return;
        }

        const sortedCities = Object.keys(municipality_list).sort((a, b) => a.localeCompare(b));
        sortedCities.forEach(cityKey => {
          const opt = document.createElement("option");
          opt.value = cityKey;
          opt.textContent = cityKey;
          permanentCitySelect.appendChild(opt);
        });

        if (tryToSelectCityName && tryToSelectCityName.trim() !== "") {
          const matchedCityKey = findCityKeyByName(provinceKey, tryToSelectCityName);
          if (matchedCityKey) {
            permanentCitySelect.value = matchedCityKey;
            populatePermanentBarangaysByKeys(provinceKey, matchedCityKey, '');
          } else {
            permanentCitySelect.selectedIndex = 0;
          }
        } else {
          permanentCitySelect.selectedIndex = 0;
          permanentBarangaySelect.innerHTML = '<option value="">Select Barangay</option>';
        }
      }
      window.populatePermanentCitiesByProvinceKey = populatePermanentCitiesByProvinceKey;

      function populatePermanentBarangaysByKeys(provinceKey, cityKey, tryToSelectBarangayName) {
        permanentBarangaySelect.innerHTML = '<option value="">Select Barangay</option>';

        if (!provinceKey || !cityKey || cityKey.trim() === "") {
          setTimeout(() => {
            permanentBarangaySelect.innerHTML = '<option value="">Select Barangay</option>';
            permanentBarangaySelect.selectedIndex = 0;
          }, 0);
          return;
        }

        let barangays = null;
        for (const rc of Object.keys(jsonData)) {
          const pList = jsonData[rc].province_list;
          if (pList[provinceKey]) {
            const cList = pList[provinceKey].municipality_list;
            if (cList && cList[cityKey]) {
              barangays = cList[cityKey].barangay_list;
            }
            break;
          }
        }
        if (!barangays || barangays.length === 0) {
          permanentBarangaySelect.selectedIndex = 0;
          return;
        }

        const sortedBarangays = [...barangays].sort((a, b) => a.localeCompare(b));
        sortedBarangays.forEach(b => {
          const opt = document.createElement("option");
          opt.value = b;
          opt.textContent = b;
          permanentBarangaySelect.appendChild(opt);
        });

        if (tryToSelectBarangayName && tryToSelectBarangayName.trim() !== "") {
          const matchedBarangay = findBarangayByName(provinceKey, cityKey, tryToSelectBarangayName);
          if (matchedBarangay) {
            permanentBarangaySelect.value = matchedBarangay;
          } else {
            permanentBarangaySelect.selectedIndex = 0;
          }
        } else {
          permanentBarangaySelect.selectedIndex = 0;
        }
      }
      window.populatePermanentBarangaysByKeys = populatePermanentBarangaysByKeys;

      // Restore saved permanent address
      if (savedPermanentProvince && savedPermanentProvince.trim() !== "") {
        if (!savedPermanentProvinceKey) savedPermanentProvinceKey = findProvinceKeyByName(savedPermanentProvince);

        if (savedPermanentProvinceKey) {
          permanentProvinceSelect.value = savedPermanentProvinceKey;

          if (savedPermanentCity && savedPermanentCity.trim() !== "") {
            populatePermanentCitiesByProvinceKey(savedPermanentProvinceKey, savedPermanentCity);

            const cityKey = findCityKeyByName(savedPermanentProvinceKey, savedPermanentCity);
            if (cityKey) {
              permanentCitySelect.value = cityKey;

              if (savedPermanentBarangay && savedPermanentBarangay.trim() !== "") {
                populatePermanentBarangaysByKeys(savedPermanentProvinceKey, cityKey, savedPermanentBarangay);
                const matchedBarangay = findBarangayByName(savedPermanentProvinceKey, cityKey, savedPermanentBarangay);
                if (matchedBarangay) permanentBarangaySelect.value = matchedBarangay;
              }
            }
          } else {
            populatePermanentCitiesByProvinceKey(savedPermanentProvinceKey, '');
            permanentCitySelect.selectedIndex = 0;
            permanentBarangaySelect.innerHTML = '<option value="">Select Barangay</option>';
          }
        }
      }

      // Event listeners for permanent address
      permanentProvinceSelect.addEventListener("change", function () {
        const selectedProvinceKey = this.value;
        populatePermanentCitiesByProvinceKey(selectedProvinceKey, '');
        permanentBarangaySelect.innerHTML = '<option value="">Select Barangay</option>';
      });

      permanentCitySelect.addEventListener("change", function () {
        const selectedProvinceKey = permanentProvinceSelect.value;
        const selectedCityKey = this.value;
        populatePermanentBarangaysByKeys(selectedProvinceKey, selectedCityKey, '');
      });

    })
    .catch(error => {
      console.error('Error loading JSON:', error);
    });
</script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const citizenshipSelect = document.getElementById('citizenship');
    const foreignAddressDiv = document.getElementById('foreignAddressDiv');
    const foreignAddressInput = document.getElementById('foreignPermanentAddress');

    // Get all permanent address field containers (edit mode)
    const phAddressFields = [
      'permanentBlockLotNo',
      'permanentPurok',
      'permanentSubdivision',
      'permanentPhase',
      'permanentStreetName',
      'permanentProvince',
      'permanentCity',
      'permanentBarangay'
    ];

    function toggleForeignAddress() {
      if (!citizenshipSelect) return;

      const citizenship = citizenshipSelect.value.trim().toUpperCase();
      const isEditMode = !citizenshipSelect.disabled;

      if (citizenship && citizenship !== 'FILIPINO') {
        // Show foreign address input (only in edit mode)
        if (foreignAddressDiv && isEditMode) {
          foreignAddressDiv.style.display = 'block';
        }

        // Hide PH address fields in edit mode
        if (isEditMode) {
          phAddressFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
              const wrapper = field.closest('.col-md-3, .col-12');
              if (wrapper && wrapper.classList.contains('edit-mode')) {
                wrapper.style.display = 'none';
              }
            }
          });

          // Also hide the permanent address province/city/barangay dropdowns
          const permProvince = document.getElementById('permanentProvince');
          const permCity = document.getElementById('permanentCity');
          const permBarangay = document.getElementById('permanentBarangay');

          if (permProvince) {
            const wrapper = permProvince.closest('.col-md-3');
            if (wrapper && wrapper.classList.contains('edit-mode')) wrapper.style.display = 'none';
          }
          if (permCity) {
            const wrapper = permCity.closest('.col-md-3');
            if (wrapper && wrapper.classList.contains('edit-mode')) wrapper.style.display = 'none';
          }
          if (permBarangay) {
            const wrapper = permBarangay.closest('.col-md-3');
            if (wrapper && wrapper.classList.contains('edit-mode')) wrapper.style.display = 'none';
          }
        }
      } else {
        // Hide foreign address input
        if (foreignAddressDiv) {
          foreignAddressDiv.style.display = 'none';
        }
        if (foreignAddressInput) {
          foreignAddressInput.value = '';
        }

        // Show PH address fields in edit mode
        if (isEditMode) {
          phAddressFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
              const wrapper = field.closest('.col-md-3, .col-12');
              if (wrapper && wrapper.classList.contains('edit-mode')) {
                wrapper.style.display = '';
              }
            }
          });

          // Show the permanent address dropdowns
          const permProvince = document.getElementById('permanentProvince');
          const permCity = document.getElementById('permanentCity');
          const permBarangay = document.getElementById('permanentBarangay');

          if (permProvince) {
            const wrapper = permProvince.closest('.col-md-3');
            if (wrapper && wrapper.classList.contains('edit-mode')) wrapper.style.display = '';
          }
          if (permCity) {
            const wrapper = permCity.closest('.col-md-3');
            if (wrapper && wrapper.classList.contains('edit-mode')) wrapper.style.display = '';
          }
          if (permBarangay) {
            const wrapper = permBarangay.closest('.col-md-3');
            if (wrapper && wrapper.classList.contains('edit-mode')) wrapper.style.display = '';
          }
        }
      }
    }

    // Make function globally accessible
    window.toggleForeignAddress = toggleForeignAddress;

    // Run on page load
    toggleForeignAddress();

    // Re-check when citizenship changes
    if (citizenshipSelect) {
      citizenshipSelect.addEventListener('change', toggleForeignAddress);
    }

    // Hook into edit button to run toggle when entering edit mode
    const editBtn = document.getElementById('editButton');
    if (editBtn) {
      editBtn.addEventListener('click', function () {
        setTimeout(toggleForeignAddress, 100);
      });
    }

    // Hook into cancel button to hide foreign address when canceling
    const cancelBtn = document.getElementById('cancelEditBtn');
    if (cancelBtn) {
      cancelBtn.addEventListener('click', function () {
        if (foreignAddressDiv) {
          foreignAddressDiv.style.display = 'none';
        }
      });
    }
  });
</script>

<script>
  // ========== EDUCATIONAL LEVEL FIELDS ==========
  document.addEventListener('DOMContentLoaded', function () {
    const educationalLevel = document.getElementById('educationalLevel');
    const shsTrackDiv = document.getElementById('shsTrackDiv');
    const collegeCourseDiv = document.getElementById('collegeCourseDiv');
    const shsTrackSelect = document.getElementById('shsTrack');
    const collegeCourseSelect = document.getElementById('collegeCourse');
    const shsTrackText = document.getElementById('shsTrackText');
    const collegeCourseText = document.getElementById('collegeCourseText');
    const backToShsDropdownBtn = document.getElementById('backToShsDropdownBtn');
    const backToDropdownBtn = document.getElementById('backToDropdownBtn');

    const predefinedTracks = ['STEM', 'ABM', 'HUMMS', 'ICT', 'GAS', 'TVL'];
    const predefinedCourses = ['BSIT', 'BSECE', 'BSEE', 'BSBA', 'BSTM', 'BSHRM', 'BSED', 'BSCE', 'BSME'];

    function updateEducationalFields(educationalLevelVal = null) {
      const educationalLevelValue = educationalLevelVal || educationalLevel.value;

      // Hide all by default
      if (shsTrackDiv) shsTrackDiv.style.display = 'none';
      if (collegeCourseDiv) collegeCourseDiv.style.display = 'none';

      // Senior High fields
      if (['Senior High Undergraduate', 'Senior High Graduate'].includes(educationalLevelValue)) {
        if (shsTrackDiv) shsTrackDiv.style.display = 'block';
        toggleShsTrackInput();
      }
      // College fields
      else if (['College Undergraduate', 'College Graduate'].includes(educationalLevelValue)) {
        if (collegeCourseDiv) collegeCourseDiv.style.display = 'block';
        toggleCollegeCourseInput();
      }
    }

    function toggleShsTrackInput() {
      if (!shsTrackSelect) return;
      const selectedValue = shsTrackSelect.value;
      const isEditMode = !shsTrackSelect.disabled;

      if (selectedValue === 'Others') {
        shsTrackSelect.classList.add('d-none');
        if (shsTrackText) shsTrackText.classList.remove('d-none');
        if (backToShsDropdownBtn) {
          if (isEditMode) {
            backToShsDropdownBtn.classList.remove('d-none');
          } else {
            backToShsDropdownBtn.classList.add('d-none');
          }
        }
        if (shsTrackText) {
          shsTrackText.disabled = !isEditMode;
          if (isEditMode) shsTrackText.focus();
        }
      } else {
        shsTrackSelect.classList.remove('d-none');
        if (shsTrackText) shsTrackText.classList.add('d-none');
        if (backToShsDropdownBtn) backToShsDropdownBtn.classList.add('d-none');
        if (selectedValue !== '' && shsTrackText) {
          shsTrackText.value = '';
        }
      }
    }

    function toggleCollegeCourseInput() {
      if (!collegeCourseSelect) return;
      const selectedValue = collegeCourseSelect.value;
      const isEditMode = !collegeCourseSelect.disabled;

      if (selectedValue === 'Others') {
        collegeCourseSelect.classList.add('d-none');
        if (collegeCourseText) collegeCourseText.classList.remove('d-none');
        if (backToDropdownBtn) {
          if (isEditMode) {
            backToDropdownBtn.classList.remove('d-none');
          } else {
            backToDropdownBtn.classList.add('d-none');
          }
        }
        if (collegeCourseText) {
          collegeCourseText.disabled = !isEditMode;
          if (isEditMode) collegeCourseText.focus();
        }
      } else {
        collegeCourseSelect.classList.remove('d-none');
        if (collegeCourseText) collegeCourseText.classList.add('d-none');
        if (backToDropdownBtn) backToDropdownBtn.classList.add('d-none');
        if (selectedValue !== '' && collegeCourseText) {
          collegeCourseText.value = '';
        }
      }
    }

    // Event listeners
    if (educationalLevel) {
      educationalLevel.addEventListener('change', function () {
        // Clear unrelated fields
        if (!['Senior High Undergraduate', 'Senior High Graduate'].includes(this.value)) {
          if (shsTrackSelect) shsTrackSelect.value = '';
          if (shsTrackText) shsTrackText.value = '';
        }
        if (!['College Undergraduate', 'College Graduate'].includes(this.value)) {
          if (collegeCourseSelect) collegeCourseSelect.value = '';
          if (collegeCourseText) collegeCourseText.value = '';
        }
        updateEducationalFields();
      });
    }

    if (shsTrackSelect) {
      shsTrackSelect.addEventListener('change', toggleShsTrackInput);
    }

    if (collegeCourseSelect) {
      collegeCourseSelect.addEventListener('change', toggleCollegeCourseInput);
    }

    if (backToShsDropdownBtn) {
      backToShsDropdownBtn.addEventListener('click', function () {
        if (shsTrackSelect && !shsTrackSelect.disabled) {
          shsTrackSelect.value = '';
          toggleShsTrackInput();
          shsTrackSelect.focus();
        }
      });
    }

    if (backToDropdownBtn) {
      backToDropdownBtn.addEventListener('click', function () {
        if (collegeCourseSelect && !collegeCourseSelect.disabled) {
          collegeCourseSelect.value = '';
          toggleCollegeCourseInput();
          collegeCourseSelect.focus();
        }
      });
    }

    // Initialize on page load
    const savedEducationalLevel = educationalLevel ? educationalLevel.value : '';
    updateEducationalFields(savedEducationalLevel);

    // Hook into edit button
    const editBtn = document.getElementById('editButton');
    if (editBtn) {
      const originalClickHandler = editBtn.onclick;
      editBtn.addEventListener('click', function () {
        setTimeout(function () {
          if (shsTrackSelect && shsTrackSelect.value === 'Others') {
            toggleShsTrackInput();
          }
          if (collegeCourseSelect && collegeCourseSelect.value === 'Others') {
            toggleCollegeCourseInput();
          }
        }, 100);
      });
    }

    // Hook into cancel button to restore state
    const cancelBtn = document.getElementById('cancelEditBtn');
    if (cancelBtn) {
      cancelBtn.addEventListener('click', function () {
        // Restore SHS Track state
        if (shsTrackSelect) {
          const savedTrack = shsTrackSelect.getAttribute('data-saved') || '';
          const isCustomTrack = savedTrack && !predefinedTracks.includes(savedTrack);

          if (isCustomTrack) {
            shsTrackSelect.value = 'Others';
            if (shsTrackText) shsTrackText.value = savedTrack;
          } else {
            shsTrackSelect.value = savedTrack;
            if (shsTrackText) shsTrackText.value = '';
          }
          toggleShsTrackInput();
        }

        // Restore College Course state
        if (collegeCourseSelect) {
          const savedCourse = collegeCourseSelect.getAttribute('data-saved') || '';
          const isCustomCourse = savedCourse && !predefinedCourses.includes(savedCourse);

          if (isCustomCourse) {
            collegeCourseSelect.value = 'Others';
            if (collegeCourseText) collegeCourseText.value = savedCourse;
          } else {
            collegeCourseSelect.value = savedCourse;
            if (collegeCourseText) collegeCourseText.value = '';
          }
          toggleCollegeCourseInput();
        }

        // Restore educational level visibility
        const originalEducationalLevel = educationalLevel ? educationalLevel.value : '';
        updateEducationalFields(originalEducationalLevel);
      });
    }
  });
</script>

</body>

</html>
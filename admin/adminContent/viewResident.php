<?php
include_once __DIR__ . '/../../sharedAssets/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['saveUser'])) {
  $userID = intval($_POST['userID']);
  $firstName = $_POST['firstName'];
  $middleName = $_POST['middleName'];
  $lastName = $_POST['lastName'];
  $suffix = $_POST['suffix'];
  $phoneNumber = $_POST['phoneNumber'];
  $gender = $_POST['gender'];
  $age = $_POST['age'];
  $birthDate = $_POST['birthDate'];
  $birthPlace = $_POST['birthPlace'];
  $civilStatus = $_POST['civilStatus'];
  $citizenship = $_POST['citizenship'];
  $occupation = $_POST['occupation'];
  $residencyType = $_POST['residencyType'];
  $isVoter = $_POST['isVoter'];
  $remarks = $_POST['remarks'];
  $presentBlockLotNo = $_POST['presentBlockLotNo'];
  $presentStreetName = $_POST['presentStreetName'];
  $presentPhase = $_POST['presentPhase'];
  $presentSubdivision = $_POST['presentSubdivision'];
  $presentBarangay = $_POST['presentBarangay'];
  $presentCity = $_POST['presentCity'];
  $presentProvince = $_POST['presentProvince'];
  $presentPurok = $_POST['presentPurok'];
  $permanentBlockLotNo = $_POST['permanentBlockLotNo'] ?? '';
  $permanentStreetName = $_POST['permanentStreetName'] ?? '';
  $permanentPhase = $_POST['permanentPhase'] ?? '';
  $permanentSubdivision = $_POST['permanentSubdivision'] ?? '';
  $permanentBarangay = $_POST['permanentBarangay'] ?? '';
  $permanentCity = $_POST['permanentCity'] ?? '';
  $permanentProvince = $_POST['permanentProvince'] ?? '';
  $permanentPurok = $_POST['permanentPurok'] ?? '';

  //Update user table
  mysqli_query($conn, "UPDATE users SET phoneNumber='$phoneNumber' 
  WHERE userID=$userID");

  //Update userInfo table
  mysqli_query($conn, "UPDATE userInfo SET firstName='$firstName', middleName='$middleName', lastName='$lastName', suffix='$suffix', gender='$gender', age='$age', birthDate='$birthDate', birthPlace='$birthPlace', civilStatus='$civilStatus', citizenship='$citizenship', occupation='$occupation', isVoter='$isVoter', remarks='$remarks', residencyType='$residencyType'  
  WHERE userID=$userID");

  $getInfo = mysqli_query($conn, "SELECT userInfoID FROM userInfo WHERE userID = $userID");
  $row = mysqli_fetch_assoc($getInfo);
  $userInfoID = $row['userInfoID'];

  //Update Present Address
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

  //Update Permanent Address
  mysqli_query($conn, "UPDATE permanentaddresses SET
  permanentBlockLotNo='$permanentBlockLotNo',
  permanentStreetName='$permanentStreetName',
  permanentPhase='$permanentPhase',
  permanentSubdivisionName='$permanentSubdivision',
  permanentBarangayName='$permanentBarangay',
  permanentCityName='$permanentCity',
  permanentProvinceName='$permanentProvince',
  permanentPurok='$permanentPurok'
  WHERE userInfoID = $userInfoID");

  if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] === UPLOAD_ERR_OK) {
      $uploadProfilePicture = $_FILES['profilePicture']['name'];
      $targetPath = "../../uploads/profiles/" . basename($uploadProfilePicture);

      if (move_uploaded_file($_FILES['profilePicture']['tmp_name'], $targetPath)) {
          $profileUpdateQuery = "UPDATE userInfo SET profilePicture = '$uploadProfilePicture' WHERE userInfoID = $userInfoID";
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
  i.userInfoID, CONCAT(i.firstName, ' ', i.middleName, ' ', i.lastName) AS fullname, i.firstName, i.middleName, i.lastName, i.suffix,
  i.gender, i.age, i.bloodType, i.birthDate, i.birthPlace, i.profilePicture,
  i.residencyType, i.lengthOfStay, i.civilStatus, i.citizenship, i.occupation, isVoter, remarks,

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
  pa.permanentPurok AS permanentPurok

FROM users u
INNER JOIN userInfo i ON i.userID = u.userID
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
  $updateProfilePictureQuery = "UPDATE userInfo SET profilePicture = 'defaulProfile.png' WHERE userInfoID = $userInfoID";
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
                        <img src="../../uploads/profiles/<?= htmlspecialchars($user['profilePicture']) ?>" style="width: 250px; height: 250px; object-fit: cover;" alt="Resident Profile">
                      </div>
                    </div>
                    <div class="col-9">
                      <div class="row">
                        <div class="col-md-4 mb-3 view-mode">
                          <div class="info-row">
                            <strong>Resident Name:</strong>
                            <div class="mt-1"><?= htmlspecialchars($user['fullname']) ?></div>
                          </div>
                        </div>
                        <div class="col-md-5 mb-3 view-mode">
                          <div class="info-row">
                            <strong>Email:</strong>
                            <div class="mt-1"><?= htmlspecialchars($user['email']) ?></div>
                          </div>
                        </div>
                        <div class="col-md-3 mb-3 view-mode">
                          <div class="info-row">
                            <strong>Phone Number:</strong>
                            <div class="mt-1"><?= htmlspecialchars($user['phoneNumber']) ?></div>
                          </div>
                        </div>
                        <div class="col-md-4 my-3 view-mode">
                          <div class="info-row">
                            <strong>Date of Birth:</strong>
                            <div class="mt-1"><?= date('F d, Y', strtotime($user['birthDate'])) ?></div>
                          </div>
                        </div>
                        <div class="col-md-4 my-3 view-mode">
                          <div class="info-row">
                            <strong>Age:</strong>
                            <div class="mt-1"><?= htmlspecialchars($user['age']) ?> years old</div>
                          </div>
                        </div>
                        <div class="col-md-4 my-3 view-mode">
                          <div class="info-row">
                            <strong>Place of Birth:</strong>
                            <div class="mt-1"><?= htmlspecialchars($user['birthPlace']) ?></div>
                          </div>
                        </div>
                        <div class="col-md-4 my-3 view-mode">
                          <div class="info-row">
                            <strong>Gender:</strong>
                            <div class="mt-1"><?= htmlspecialchars($user['gender']) ?></div>
                          </div>
                        </div>
                        <div class="col-md-4 my-3 view-mode">
                          <div class="info-row">
                            <strong>Blood Type:</strong>
                            <div class="mt-1"><?= htmlspecialchars($user['bloodType']) ?></div>
                          </div>
                        </div>
                        <div class="col-md-4 my-3 view-mode">
                          <div class="info-row">
                            <strong>Civil Status:</strong>
                            <div class="mt-1"><?= htmlspecialchars($user['civilStatus']) ?></div>
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
                            <div class="mt-1"><?= htmlspecialchars($user['citizenship']) ?></div>
                          </div>
                        </div>
                        <div class="col-md-3 my-3 view-mode">
                          <div class="info-row">
                            <strong>Occupation:</strong>
                            <div class="mt-1"><?= htmlspecialchars($user['occupation']) ?></div>
                          </div>
                        </div>
                        <div class="col-md-3 my-3 view-mode">
                          <div class="info-row">
                            <strong>Remarks:</strong>
                            <div class="mt-1"><?= htmlspecialchars($user['remarks']) ?></div>
                          </div>
                        </div>
                        <div class="col-md-3 my-3 view-mode">
                          <div class="info-row">
                            <strong>Length Of Stay:</strong>
                            <div class="mt-1">
                              <span><?= htmlspecialchars($user['lengthOfStay']) . ' ' . ((int)$user['lengthOfStay'] === 1 ? 'year' : 'years') ?></span>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-2 my-3 view-mode">
                          <div class="info-row">
                            <strong >Residency Type:</strong>
                            <div class="mt-1">
                              <span><?= htmlspecialchars($user['residencyType']) ?></span>
                            </div>
                          </div>
                        </div>
                        <div class="col-5 my-3 view-mode">
                          <div class="info-row">
                            <strong>Address:</strong>
                            <div class="mt-1">
                              <?php
                              $addressParts = array_filter([
                                ucwords(strtolower($user['presentBlockLotNo'])),
                                ucwords(strtolower($user['presentPurok'])),
                                ucwords(strtolower($user['presentSubdivision'])),
                                ucwords(strtolower($user['presentPhase'])),
                                ucwords(strtolower($user['presentStreetName'])),
                                ucwords(strtolower($user['presentBarangay'])),
                                ucwords(strtolower($user['presentCity'])),
                                ucwords(strtolower($user['presentProvince']))
                              ]);
                              echo htmlspecialchars(implode(' ', $addressParts));
                              ?>
                            </div>
                          </div>
                        </div>
                        <div class="col-5 my-3 view-mode">
                          <div class="info-row">
                            <strong>Permanent Address:</strong>
                            <div class="mt-1">
                              <?php
                              $addressParts = array_filter([
                                ucwords(strtolower($user['permanentBlockLotNo'])),
                                ucwords(strtolower($user['permanentPurok'])),
                                ucwords(strtolower($user['permanentSubdivision'])),
                                ucwords(strtolower($user['permanentPhase'])),
                                ucwords(strtolower($user['permanentStreetName'])),
                                ucwords(strtolower($user['permanentBarangay'])),
                                ucwords(strtolower($user['permanentCity'])),
                                ucwords(strtolower($user['permanentProvince']))
                              ]);
                              echo htmlspecialchars(implode(' ', $addressParts));
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

                        <div style="position: relative; width: 250px; height: 250px;" onmouseover=" this.querySelector('img').style.filter='brightness(0.75)'; this.querySelector('.hoverBtn').style.opacity='1'; " onmouseout=" this.querySelector('img').style.filter='brightness(1)'; this.querySelector('.hoverBtn').style.opacity='0';  ">

                            <?php if ($user['profilePicture'] == "defaultProfile.png") { ?>

                              <img src="../../uploads/profiles/<?= htmlspecialchars($user['profilePicture']) ?>" id="profilePreview" style="width:250px; height:250px; object-fit:cover; transition:0.3s;" alt="Default Profile">

                              <label type="button" name="addButton" class="hoverBtn" onclick="document.getElementById('profilePictureInput').click()" style=" opacity:0; transition:0.3s; width:40px; height:40px; color:white; position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); border:none; background-color:#19AFA5; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center; ">
                                  <i class="fa-solid fa-plus"></i>
                              </label>

                              <input type="file" name="profilePicture" class="form-control d-none" id="profilePictureInput" accept="image/*" onchange="previewProfilePicture(event)">

                            <?php } else { ?>

                              <img src="../../uploads/profiles/defaultProfile.png" style="width:250px; height:250px; object-fit:cover; transition:0.3s;" alt="Resident Profile">

                              <button type="button" class="hoverBtn" onclick="deleteProfilePicture(<?= $user['userID'] ?>)" style=" opacity:0; transition:0.3s; width:40px; height:40px; color:white; position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); border:none; background-color:#19AFA5; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center;" data-bs-toggle="modal" data-bs-target="#removeProfileModal">
                                  <i class="fa-solid fa-trash"></i>
                              </button>

                              <div class="modal fade" id="removeProfileModal" tabindex="-1" aria-labelledby="removeProfileLabel" aria-hidden="true">
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
                      </div>
                    </div>
                    <div class="col-9">
                      <div class="row">
                        <div class="col-md-4 mb-3 edit-mode d-none">
                          <div class="info-row">
                            <label for="firstName" class="form-label"><strong>First Name:</strong></label>
                            <input class="form-control" type="text" id="firstName" name="firstName" value="<?= $user['firstName'] ?>">
                          </div>
                        </div>
                        <div class="col-md-3 mb-3 edit-mode d-none">
                          <div class="info-row">
                            <label for="middleName" class="form-label"><strong>Middle Name:</strong></label>
                            <input class="form-control" type="text" id="middleName" name="middleName" value="<?= $user['middleName'] ?>">
                          </div>
                        </div>
                        <div class="col-md-3 mb-3 edit-mode d-none">
                          <div class="info-row">
                            <label for="lastName" class="form-label"><strong>Last Name:</strong></label>
                            <input class="form-control" type="text" id="lastName" name="lastName" value="<?= $user['lastName'] ?>">
                          </div>
                        </div>
                        <div class="col-md-2 edit-mode d-none">
                          <div class="info-row">
                            <label for="suffix" class="form-label"><strong>Suffix:</strong></label>
                            <select class="form-select" id="suffix" name="suffix">
                              <option value="" disabled <?= empty($user['suffix']) ? 'selected' : '' ?>>Suffix</option>

                              <option value="Jr." <?= ($user['suffix'] === 'Jr.') ? 'selected' : '' ?>>Jr.</option>
                              <option value="Sr." <?= ($user['suffix'] === 'Sr.') ? 'selected' : '' ?>>Sr.</option>
                              <option value="II"  <?= ($user['suffix'] === 'II')  ? 'selected' : '' ?>>II</option>
                              <option value="III" <?= ($user['suffix'] === 'III') ? 'selected' : '' ?>>III</option>
                              <option value="IV"  <?= ($user['suffix'] === 'IV')  ? 'selected' : '' ?>>IV</option>
                              <option value="V"   <?= ($user['suffix'] === 'V')   ? 'selected' : '' ?>>V</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-5 mb-3 edit-mode d-none">
                          <div class="info-row">
                            <label for="email" class="form-label"><strong>Email:</strong></label>
                            <input class="form-control" type="email" id="email" name="email" value="<?= $user['email'] ?>">
                          </div>
                        </div>
                        <div class="col-md-4 mb-3 edit-mode d-none">
                          <div class="info-row">
                            <label for="phoneNumber" class="form-label"><strong>Phone Number:</strong></label>
                            <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" value="<?= $user['phoneNumber'] ?>" placeholder="Phone Number" inputmode="numeric" pattern="^09\d{9}$" maxlength="11" title="Phone number must start with 09 and be exactly 11 digits (e.g., 09123456789)" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                          </div>
                        </div>
                        <div class="col-md-3 mb-3 edit-mode d-none">
                          <div class="info-row">
                            <label for="gender" class="form-label"><strong>Gender:</strong></label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="" disabled <?= empty($user['gender']) ? 'selected' : '' ?>>Choose Gender</option>
                                <option value="Male" <?= ($user['gender'] === 'Male.') ? 'selected' : '' ?>>Male</option>
                                <option value="Female" <?= ($user['gender'] === 'Female.') ? 'selected' : '' ?>> Female</option>
                                <option value="Other" <?= ($user['gender'] === 'Others.') ? 'selected' : '' ?>> Others</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-3 mb-3 edit-mode d-none">
                          <div class="info-row">
                            <label for="birthDate" class="form-label"><strong>Birth Date:</strong></label>
                            <input type="date" class="form-control <?= ($incomplete && empty($birthDate)) ? 'border border-warning' : ''; ?>" id="birthDate" name="birthDate" value="<?php echo $user['birthDate'] ?>" placeholder="Date of Birth">
                          </div>
                        </div>
                        <div class="col-md-3 mb-3 edit-mode d-none">
                          <div class="info-row">
                            <?php 
                              $age = (int) $user['age']; 
                              $ageLabel = $age . ' ' . ($age === 1 ? 'year old' : 'years old');
                            ?>
                            <label for="age" class="form-label"><strong>Age:</strong></label>
                            <input type="text" class="form-control" id="age" value="<?= $ageLabel  ?>" placeholder="Age" readonly>
                            <input type="hidden" name="age" id="ageHidden" value="<?= $age ?>">
                          </div>
                        </div>
                        <div class="col-md-6 mb-3 edit-mode d-none">
                          <div class="info-row">
                            <label for="birthPlace" class="form-label"><strong>Birth Place:</strong></label>
                            <input type="text" class="form-control" id="birthPlace" name="birthPlace" value="<?= $user['birthPlace'] ?>" placeholder="Place of Birth">
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
                            <option value="" disabled <?= empty($user['civilStatus']) ? 'selected' : ''; ?>>Choose Civil Status</option>
                            <option value="Single" <?= ($user['civilStatus'] === 'Single') ? 'selected' : ''; ?>>Single</option>
                            <option value="Married" <?= ($user['civilStatus'] === 'Married') ? 'selected' : ''; ?>>Married</option>
                            <option value="Divorced" <?= ($user['civilStatus'] === 'Divorced') ? 'selected' : ''; ?>>Divorced</option>
                            <option value="Widowed" <?= ($user['civilStatus'] === 'Widowed') ? 'selected' : ''; ?>>Widowed</option>
                            <option value="Separated" <?= ($user['civilStatus'] === 'Separated') ? 'selected' : ''; ?>>Separated</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-3 mb-3 edit-mode d-none">
                      <label for="bloodType" class="form-label"><strong>Blood Type:</strong></label>
                      <select class="form-select" id="bloodType" name="bloodType">
                          <option value="" disabled <?= empty($user['bloodType']) ? 'selected' : ''; ?>>Choose Blood Type</option>
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
                        <input type="text" class="form-control" id="occupation" name="occupation" placeholder="Occupation" value="<?= $user['occupation'] ?>">
                      </div>
                    </div>
                    <div class="col-md-3 my-3 edit-mode d-none">
                      <div class="info-row">
                        <label for="remarks" class="form-label"><strong>Remarks:</strong></label>
                        <select class="form-select" id="remarks" name="remarks">
                          <option value="" disabled <?= empty($user['remarks']) ? 'selected' : ''; ?>>Choose Remarks</option>
                          <option value="No Derogatory Record" <?= ($user['remarks'] === 'No Derogatory Record') ? 'selected' : ''; ?>>No Derogatory Record</option>
                          <option value="With Derogatory Record" <?= ($user['remarks'] === 'With Derogatory Record') ? 'selected' : ''; ?>>With Derogatory Record</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-3 my-3 edit-mode d-none">
                      <div class="info-row">
                        <label for="lengthOfStay" class="form-label"><strong>Length of Stay:</strong></label>
                        <input type="text" class="form-control" id="lengthOfStay" name="lengthOfStay" value="<?= $user['lengthOfStay'] ?>">
                      </div>
                    </div>
                    <div class="col-md-3 my-3 edit-mode d-none">
                      <div class="info-row">
                        <label for="residencyType" class="form-label"><strong>Residency Type:</strong></label>
                        <input type="text" class="form-control" id="residencyType" name="residencyType" value="<?= $user['residencyType'] ?>">
                      </div>
                    </div>
                    <div class="col-md-3 my-3 edit-mode d-none">
                      <div class="info-row">
                        <label for="isVoter" class="form-label"><strong>Registered Voter:</strong></label>
                        <input type="text" class="form-control" id="isVoter" name="isVoter" value="<?= $user['isVoter'] ?>">
                      </div>
                    </div>
                    <div class="col-md-3 my-3 edit-mode d-none">
                      <div class="info-row">
                        <label for="presentBlockLotNo" class="form-label"><strong>House / Block & Lot No:</strong></label>
                        <input type="text" class="form-control" id="presentBlockLotNo" name="presentBlockLotNo" value="<?= $user['presentBlockLotNo'] ?>">
                      </div>
                    </div>
                    <div class="col-md-3 my-3 edit-mode d-none">
                      <div class="info-row">
                        <label for="presentPurok" class="form-label"><strong>Purok:</strong></label>
                        <input type="text" class="form-control" id="presentPurok" name="presentPurok" value="<?= $user['presentPurok'] ?>">
                      </div>
                    </div>
                    <div class="col-md-3 my-3 edit-mode d-none">
                      <div class="info-row">
                        <label for="presentSubdivision" class="form-label"><strong>Subdivision:</strong></label>
                        <input type="text" class="form-control" id="presentSubdivision" name="presentSubdivision" value="<?= $user['presentSubdivision'] ?>">
                      </div>
                    </div>
                    <div class="col-md-3 my-3 edit-mode d-none">
                      <div class="info-row">
                        <label for="presentPhase" class="form-label"><strong>Phase:</strong></label>
                        <input type="text" class="form-control" id="presentPhase" name="presentPhase" value="<?= $user['presentPhase'] ?>">
                      </div>
                    </div>
                    <div class="col-md-3 my-3 edit-mode d-none">
                      <div class="info-row">
                        <label for="presentStreetName" class="form-label"><strong>Street:</strong></label>
                        <input type="text" class="form-control" id="presentStreetName" name="presentStreetName" value="<?= $user['presentStreetName'] ?>">
                      </div>
                    </div>
                    <div class="col-md-3 my-3 edit-mode d-none">
                      <div class="info-row">
                        <label for="presentBarangay" class="form-label"><strong>Barangay:</strong></label>
                        <input type="text" class="form-control" id="presentBarangay" name="presentBarangay" value="<?= $user['presentBarangay'] ?>">
                      </div>
                    </div>
                    <div class="col-md-3 my-3 edit-mode d-none">
                      <div class="info-row">
                        <label for="presentCity" class="form-label"><strong>City:</strong></label>
                        <input type="text" class="form-control" id="presentCity" name="presentCity" value="<?= $user['presentCity'] ?>">
                      </div>
                    </div>
                    <div class="col-md-3 my-3 edit-mode d-none">
                      <div class="info-row">
                        <label for="presentProvince" class="form-label"><strong>Province:</strong></label>
                        <input type="text" class="form-control" id="presentProvince" name="presentProvince" value="<?= $user['presentProvince'] ?>">
                      </div>
                    </div>
                    <div class="col-md-3 my-3 edit-mode d-none">
                      <div class="info-row">
                        <label for="permanentBlockLotNo" class="form-label"><strong>House / Block & Lot No:</strong></label>
                        <input type="text" class="form-control" id="permanentBlockLotNo" name="permanentBlockLotNo" value="<?= $user['permanentBlockLotNo'] ?>">
                      </div>
                    </div>
                    <div class="col-md-3 my-3 edit-mode d-none">
                      <div class="info-row">
                        <label for="permanentPurok" class="form-label"><strong>Purok:</strong></label>
                        <input type="text" class="form-control" id="permanentPurok" name="permanentPurok" value="<?= $user['permanentPurok'] ?>">
                      </div>
                    </div>
                    <div class="col-md-3 my-3 edit-mode d-none">
                      <div class="info-row">
                        <label for="permanentSubdivision" class="form-label"><strong>Subdivision:</strong></label>
                        <input type="text" class="form-control" id="permanentSubdivision" name="permanentSubdivision" value="<?= $user['permanentSubdivision'] ?>">
                      </div>
                    </div>
                    <div class="col-md-3 my-3 edit-mode d-none">
                      <div class="info-row">
                        <label for="permanentPhase" class="form-label"><strong>Phase:</strong></label>
                        <input type="text" class="form-control" id="permanentPhase" name="permanentPhase" value="<?= $user['permanentPhase'] ?>">
                      </div>
                    </div>
                    <div class="col-md-3 my-3 edit-mode d-none">
                      <div class="info-row">
                        <label for="permanentStreetName" class="form-label"><strong>Street:</strong></label>
                        <input type="text" class="form-control" id="permanentStreetName" name="permanentStreetName" value="<?= $user['permanentStreetName'] ?>">
                      </div>
                    </div>
                    <div class="col-md-3 my-3 edit-mode d-none">
                      <div class="info-row">
                        <label for="permanentBarangay" class="form-label"><strong>Barangay:</strong></label>
                        <input type="text" class="form-control" id="permanentBarangay" name="permanentBarangay" value="<?= $user['permanentBarangay'] ?>">
                      </div>
                    </div>
                    <div class="col-md-3 my-3 edit-mode d-none">
                      <div class="info-row">
                        <label for="permanentCity" class="form-label"><strong>City:</strong></label>
                        <input type="text" class="form-control" id="permanentCity" name="permanentCity" value="<?= $user['permanentCity'] ?>">
                      </div>
                    </div>
                    <div class="col-md-3 my-3 edit-mode d-none">
                      <div class="info-row">
                        <label for="permanentProvince" class="form-label"><strong>Province:</strong></label>
                        <input type="text" class="form-control" id="permanentProvince" name="permanentProvince" value="<?= $user['permanentProvince'] ?>">
                      </div>
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

  </script>
</body>

</html>
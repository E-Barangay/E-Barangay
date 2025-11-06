<?php
include_once __DIR__ . '/../../sharedAssets/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['saveUser'])) {
  $userID = intval($_POST['userID']);
  $firstName = $_POST['firstName'];
  $middleName = $_POST['middleName'];
  $lastName = $_POST['lastName'];
  $suffix = $_POST['suffix'];
  $email = $_POST['email'];
  $phoneNumber = $_POST['phoneNumber'];
  $gender = $_POST['gender'];
  $age = $_POST['age'];
  $birthDate = $_POST['birthDate'];
  $birthPlace = $_POST['birthPlace'];
  $civilStatus = $_POST['civilStatus'];
  $citizenship = $_POST['citizenship'];
  $occupation = $_POST['occupation'];
  $isVoter = $_POST['isVoter'];
  $remarks = $_POST['remarks'];
  $remarks = $_POST['residencyType'];
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
  mysqli_query($conn, "UPDATE users SET email='$email', phoneNumber='$phoneNumber' 
  WHERE userID=$userID");

  //Update userInfo table
  mysqli_query($conn, "UPDATE userInfo SET firstName='$firstName', middleName='$middleName', lastName='$lastName', suffix='$suffix', gender='$gender', age='$age', birthDate='$birthDate', birthPlace='$birthPlace', civilStatus='$civilStatus', citizenship='$citizenship', occupation='$occupation', isVoter='$isVoter', remarks='$remarks' residencyType='$residencyType'  
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

  header("Location: viewResident.php?userID=$userID&updated=1");
  exit;
}

if (isset($_GET['userID'])) {
  $userID = intval($_GET['userID']);
  $sql = "SELECT 
  u.userID, u.phoneNumber, u.email, u.role, u.isNew,
  i.userInfoID, i.firstName, i.middleName, i.lastName, i.suffix,
  i.gender, i.age, i.birthDate, i.birthPlace, i.profilePicture,
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Resident Details</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
</head>

<body>
  <div class="container-fluid p-3 p-md-4">
    <form method="POST" action="">
      <input type="hidden" name="userID" value="<?= $user['userID'] ?>">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header text-white" style="background-color: #31afab;">
              <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                  <i class="fas fa-file-alt me-3 fs-4"></i>
                  <div>
                    <h4 class="mb-0 fw-semibold">User Details</h4>
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
                    <h5 class="fw-bold mb-0">
                      <i class="fas fa-user text-primary me-2"></i>
                      <?= $user['firstName'] . " " . $user['middleName'] . " " . $user['lastName'] . " " . $user['suffix'] ?>
                    </h5>
                    <span class="badge bg-info"><?= ucfirst($user['role'] ?? 'No role') ?></span>
                    </span>
                  </div>
                </div>
                <div class="card-body" id="documentInfo">

                  <div class="row g-3">
                    <div class="col-md-3">
                      <div class="info-row">
                        <strong class="text-muted">First Name:</strong>
                        <div class="mt-1">
                          <span class="view-mode"><?= $user['firstName'] ?></span>
                          <input class="form-control edit-mode d-none" type="firstName" name="firstName"
                            value="<?= $user['firstName'] ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="info-row">
                        <strong class="text-muted">Middle Name:</strong>
                        <div class="mt-1">
                          <span class="view-mode"><?= $user['middleName'] ?></span>
                          <input class="form-control edit-mode d-none" type="firstName" name="middleName"
                            value="<?= $user['middleName'] ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="info-row">
                        <strong class="text-muted">Last Name:</strong>
                        <div class="mt-1">
                          <span class="view-mode"><?= $user['lastName'] ?></span>
                          <input class="form-control edit-mode d-none" type="firstName" name="lastName"
                            value="<?= $user['lastName'] ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="info-row">
                        <strong class="text-muted">Suffix:</strong>
                        <div class="mt-1">
                          <span class="view-mode"><?= $user['suffix'] ?></span>
                          <input class="form-control edit-mode d-none" type="firstName" name="suffix"
                            value="<?= $user['suffix'] ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="info-row">
                        <strong class="text-muted">Email:</strong>
                        <div class="mt-1">
                          <span class="view-mode"><?= $user['email'] ?></span>
                          <input class="form-control edit-mode d-none" type="email" name="email"
                            value="<?= $user['email'] ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="info-row">
                        <strong class="text-muted">Phone:</strong>
                        <div class="mt-1">
                          <span class="view-mode"><?= $user['phoneNumber'] ?></span>
                          <input class="form-control edit-mode d-none" type="text" name="phoneNumber"
                            value="<?= $user['phoneNumber'] ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="info-row">
                        <strong class="text-muted">Gender:</strong>
                        <div class="mt-1">
                          <span class="view-mode"><?= $user['gender'] ?></span>
                          <input class="form-control edit-mode d-none" type="text" name="gender"
                            value="<?= $user['gender'] ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="info-row">
                        <strong class="text-muted">Age:</strong>
                        <div class="mt-1">
                          <span class="view-mode"><?= $user['age'] ?></span>
                          <input class="form-control edit-mode d-none" type="number" name="age"
                            value="<?= $user['age'] ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="info-row">
                        <strong class="text-muted">Birthplace:</strong>
                        <div class="mt-1">
                          <span class="view-mode"><?= $user['birthPlace'] ?></span>
                          <input class="form-control edit-mode d-none" type="text" name="birthPlace"
                            value="<?= $user['birthPlace'] ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="info-row">
                        <strong class="text-muted">Birthdate:</strong>
                        <div class="mt-1">
                          <span class="view-mode"><?= $user['birthDate'] ?></span>
                          <input class="form-control edit-mode d-none" type="date" name="birthDate"
                            value="<?= $user['birthDate'] ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="info-row">
                        <strong class="text-muted">Civil Status:</strong>
                        <div class="mt-1">
                          <span class="view-mode"><?= $user['civilStatus'] ?></span>
                          <input class="form-control edit-mode d-none" type="text" name="civilStatus"
                            value="<?= $user['civilStatus'] ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="info-row">
                        <strong class="text-muted">Citizenship:</strong>
                        <div class="mt-1">
                          <span class="view-mode"><?= $user['citizenship'] ?></span>
                          <input class="form-control edit-mode d-none" type="text" name="citizenship"
                            value="<?= $user['citizenship'] ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="info-row">
                        <strong class="text-muted">Occupation:</strong>
                        <div class="mt-1">
                          <span class="view-mode"><?= $user['occupation'] ?></span>
                          <input class="form-control edit-mode d-none" type="text" name="occupation"
                            value="<?= $user['occupation'] ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="info-row">
                        <strong class="text-muted">Registered Voter:</strong>
                        <div class="mt-1">
                          <span class="view-mode"><?= $user['isVoter'] ?></span>
                          <input class="form-control edit-mode d-none" type="text" name="isVoter"
                            value="<?= $user['isVoter'] ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="info-row">
                        <strong class="text-muted">Status</strong>
                        <div class="mt-1">
                          <span class="view-mode"><?= $user['remarks'] ?></span>
                          <input class="form-control edit-mode d-none" type="text" name="remarks"
                            value="<?= $user['remarks'] ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="info-row">
                        <strong class="text-muted">Type</strong>
                        <div class="mt-1">
                          <span class="view-mode"><?= $user['residencyType'] ?></span>
                          <input class="form-control edit-mode d-none" type="text" name="residencyType"
                            value="<?= $user['residencyType'] ?>">
                        </div>
                      </div>
                    </div>

                    <!-- Present Address -->
                    <legend class="float-none">Present Address</legend>
                    <div class="col-md-4">
                      <div class="info-row">
                        <strong class="text-muted">House / Block & Lot No:</strong>
                        <div class="mt-1">
                          <span class="view-mode"><?= $user['presentBlockLotNo'] ?></span>
                          <input class="form-control edit-mode d-none" type="text" name="presentBlockLotNo"
                            value="<?= $user['presentBlockLotNo'] ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="info-row">
                        <strong class="text-muted">Purok:</strong>
                        <div class="mt-1">
                          <span class="view-mode"><?= $user['presentPurok'] ?></span>
                          <input class="form-control edit-mode d-none" type="text" name="presentPurok"
                            value="<?= $user['presentPurok'] ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="info-row">
                        <strong class="text-muted">Subdivision:</strong>
                        <div class="mt-1">
                          <span class="view-mode"><?= $user['presentSubdivision'] ?></span>
                          <input class="form-control edit-mode d-none" type="text" name="presentSubdivision"
                            value="<?= $user['presentSubdivision'] ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="info-row">
                        <strong class="text-muted">Phase:</strong>
                        <div class="mt-1">
                          <span class="view-mode"><?= $user['presentPhase'] ?></span>
                          <input class="form-control edit-mode d-none" type="text" name="presentPhase"
                            value="<?= $user['presentPhase'] ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="info-row">
                        <strong class="text-muted">Street:</strong>
                        <div class="mt-1">
                          <span class="view-mode"><?= $user['presentStreetName'] ?></span>
                          <input class="form-control edit-mode d-none" type="text" name="presentStreetName"
                            value="<?= $user['presentStreetName'] ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="info-row">
                        <strong class="text-muted">Barangay:</strong>
                        <div class="mt-1">
                          <span class="view-mode"><?= $user['presentBarangay'] ?></span>
                          <input class="form-control edit-mode d-none" type="text" name="presentBarangay"
                            value="<?= $user['presentBarangay'] ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="info-row">
                        <strong class="text-muted">City:</strong>
                        <div class="mt-1">
                          <span class="view-mode"><?= $user['presentCity'] ?></span>
                          <input class="form-control edit-mode d-none" type="text" name="presentCity"
                            value="<?= $user['presentCity'] ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="info-row">
                        <strong class="text-muted">Province:</strong>
                        <div class="mt-1">
                          <span class="view-mode"><?= $user['presentProvince'] ?></span>
                          <input class="form-control edit-mode d-none" type="text" name="presentProvince"
                            value="<?= $user['presentProvince'] ?>">
                        </div>
                      </div>
                    </div>

                    <!-- Permanent Address -->
                    <legend class="float-none">Permanent Address</legend>
                    <div class="col-md-4">
                      <div class="info-row">
                        <strong class="text-muted">House / Block & Lot No:</strong>
                        <div class="mt-1">
                          <span class="view-mode"><?= $user['presentBlockLotNo'] ?></span>
                          <input class="form-control edit-mode d-none" type="text" name="presentBlockLotNo"
                            value="<?= $user['presentBlockLotNo'] ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="info-row">
                        <strong class="text-muted">Purok:</strong>
                        <div class="mt-1">
                          <span class="view-mode"><?= $user['presentPurok'] ?></span>
                          <input class="form-control edit-mode d-none" type="text" name="presentPurok"
                            value="<?= $user['presentPurok'] ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="info-row">
                        <strong class="text-muted">Subdivision:</strong>
                        <div class="mt-1">
                          <span class="view-mode"><?= $user['presentSubdivision'] ?></span>
                          <input class="form-control edit-mode d-none" type="text" name="presentSubdivision"
                            value="<?= $user['presentSubdivision'] ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="info-row">
                        <strong class="text-muted">Phase:</strong>
                        <div class="mt-1">
                          <span class="view-mode"><?= $user['presentPhase'] ?></span>
                          <input class="form-control edit-mode d-none" type="text" name="presentPhase"
                            value="<?= $user['presentPhase'] ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="info-row">
                        <strong class="text-muted">Street:</strong>
                        <div class="mt-1">
                          <span class="view-mode"><?= $user['presentStreetName'] ?></span>
                          <input class="form-control edit-mode d-none" type="text" name="presentStreetName"
                            value="<?= $user['presentStreetName'] ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="info-row">
                        <strong class="text-muted">Barangay:</strong>
                        <div class="mt-1">
                          <span class="view-mode"><?= $user['presentBarangay'] ?></span>
                          <input class="form-control edit-mode d-none" type="text" name="presentBarangay"
                            value="<?= $user['presentBarangay'] ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="info-row">
                        <strong class="text-muted">City:</strong>
                        <div class="mt-1">
                          <span class="view-mode"><?= $user['presentCity'] ?></span>
                          <input class="form-control edit-mode d-none" type="text" name="presentCity"
                            value="<?= $user['presentCity'] ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="info-row">
                        <strong class="text-muted">Province:</strong>
                        <div class="mt-1">
                          <span class="view-mode"><?= $user['presentProvince'] ?></span>
                          <input class="form-control edit-mode d-none" type="text" name="presentProvince"
                            value="<?= $user['presentProvince'] ?>">
                        </div>
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
  </script>
</body>

</html>
<?php
include_once __DIR__ . '/../../sharedAssets/connect.php';

// --- Handle Update ---
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

  // Update user table
  mysqli_query($conn, "UPDATE users 
                          SET email='$email', phoneNumber='$phoneNumber' 
                        WHERE userID=$userID");

  // Update userInfo table
  mysqli_query($conn, "UPDATE userInfo 
                          SET firstName='$firstName', middleName='$middleName', lastName='$lastName', suffix='$suffix',
                              gender='$gender', age='$age', birthDate='$birthDate', birthPlace='$birthPlace',
                              civilStatus='$civilStatus', citizenship='$citizenship', occupation='$occupation'
                        WHERE userInfoID=(SELECT userInfoID FROM users WHERE userID=$userID)");

  header("Location: viewResident.php?userID=$userID&updated=1");
  exit;
}

// --- Load User ---
if (isset($_GET['userID'])) {
  $userID = intval($_GET['userID']);
  $sql = "SELECT u.userID,u.username,u.phoneNumber,u.email,u.role,u.isNew,
                 i.userInfoID,i.firstName,i.middleName,i.lastName,i.suffix,
                 i.gender,i.age,i.birthDate,i.birthPlace,i.profilePicture,
                 i.residencyType,i.lengthOfStay,i.civilStatus,i.citizenship,i.occupation,
                 a.houseNo,a.streetName,a.phase,a.subdivisionName,a.barangayName,a.cityName,a.provinceName,
                 pa.houseNo AS houseNoPermanent,pa.streetName AS streetNamePermanent,pa.phase AS phasePermanent,
                 pa.subdivisionName AS subdivisionNamePermanent,pa.barangayName AS barangayNamePermanent,
                 pa.cityName AS cityNamePermanent,pa.provinceName AS provinceNamePermanent
          FROM users u
          INNER JOIN userInfo i ON u.userInfoID = i.userInfoID
          LEFT JOIN addresses a ON i.userInfoID = a.userInfoID
          LEFT JOIN permanentAddresses pa ON i.userInfoID = pa.userInfoID
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
  <title>Complaint Details</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
</head>

<body>
  <div class="container px-3 mt-4">
    <form method="POST">
      <input type="hidden" name="userID" value="<?= $user['userID'] ?>">

      <div class="row g-4">
        <!-- User Details Card -->
        <div class="col-md-7">
          <div class="card shadow-sm border-0">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">
                  <i class="fas fa-user text-primary me-2"></i> User Details
                </h5>
                <span class="badge bg-info"><?= ucfirst($user['role'] ?? 'No role') ?></span>
              </div>

              <div class="row g-2">
                <div class="col-12 col-sm-6">
                  <p><strong>Full Name:</strong>
                    <span
                      class="view-mode"><?= $user['firstName'] . " " . $user['middleName'] . " " . $user['lastName'] . " " . $user['suffix'] ?></span>
                    <input class="form-control edit-mode d-none" type="text" name="firstName"
                      value="<?= $user['firstName'] ?>" placeholder="First Name">
                    <input class="form-control edit-mode d-none mt-1" type="text" name="middleName"
                      value="<?= $user['middleName'] ?>" placeholder="Middle Name">
                    <input class="form-control edit-mode d-none mt-1" type="text" name="lastName"
                      value="<?= $user['lastName'] ?>" placeholder="Last Name">
                    <input class="form-control edit-mode d-none mt-1" type="text" name="suffix"
                      value="<?= $user['suffix'] ?>" placeholder="Suffix">
                  </p>

                  <p><strong>Email:</strong>
                    <span class="view-mode"><?= $user['email'] ?></span>
                    <input class="form-control edit-mode d-none" type="email" name="email"
                      value="<?= $user['email'] ?>">
                  </p>

                  <p><strong>Phone:</strong>
                    <span class="view-mode"><?= $user['phoneNumber'] ?></span>
                    <input class="form-control edit-mode d-none" type="text" name="phoneNumber"
                      value="<?= $user['phoneNumber'] ?>">
                  </p>
                </div>

                <div class="col-12 col-sm-6">
                  <p><strong>Gender:</strong>
                    <span class="view-mode"><?= $user['gender'] ?></span>
                    <input class="form-control edit-mode d-none" type="text" name="gender"
                      value="<?= $user['gender'] ?>">
                  </p>

                  <p><strong>Age:</strong>
                    <span class="view-mode"><?= $user['age'] ?></span>
                    <input class="form-control edit-mode d-none" type="number" name="age" value="<?= $user['age'] ?>">
                  </p>

                  <p><strong>Birthdate:</strong>
                    <span class="view-mode"><?= $user['birthDate'] ?></span>
                    <input class="form-control edit-mode d-none" type="date" name="birthDate"
                      value="<?= $user['birthDate'] ?>">
                  </p>

                  <p><strong>Birthplace:</strong>
                    <span class="view-mode"><?= $user['birthPlace'] ?></span>
                    <input class="form-control edit-mode d-none" type="text" name="birthPlace"
                      value="<?= $user['birthPlace'] ?>">
                  </p>

                  <p><strong>Civil Status:</strong>
                    <span class="view-mode"><?= $user['civilStatus'] ?></span>
                    <input class="form-control edit-mode d-none" type="text" name="civilStatus"
                      value="<?= $user['civilStatus'] ?>">
                  </p>

                  <p><strong>Citizenship:</strong>
                    <span class="view-mode"><?= $user['citizenship'] ?></span>
                    <input class="form-control edit-mode d-none" type="text" name="citizenship"
                      value="<?= $user['citizenship'] ?>">
                  </p>

                  <p><strong>Occupation:</strong>
                    <span class="view-mode"><?= $user['occupation'] ?></span>
                    <input class="form-control edit-mode d-none" type="text" name="occupation"
                      value="<?= $user['occupation'] ?>">
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar Card -->
        <div class="col-md-4">
          <div class="card shadow-sm border-0">
            <div class="card-body">
              <h6 class="fw-bold mb-3">
                <i class="fas fa-tools me-1 text-dark"></i> Admin Controls
              </h6>

              <div class="d-flex justify-content-between flex-wrap gap-2">
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
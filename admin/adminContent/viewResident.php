<?php
include_once __DIR__ . '/../../sharedAssets/connect.php';

// Check if userID is provided in URL
if (isset($_GET['userID'])) {
  $userID = intval($_GET['userID']); // sanitize

  // Correct JOIN using userInfoID
  $sql = "SELECT 
            u.userID,
            u.username,
            u.phoneNumber,
            u.email,
            u.role,
            u.isNew,
            i.userInfoID,
            i.firstName,
            i.middleName,
            i.lastName,
            i.suffix,
            i.gender,
            i.age,
            i.birthDate,
            i.birthPlace,
            i.profilePicture,
            i.residencyType,
            i.lengthOfStay,
            i.civilStatus,
            i.citizenship,
            i.occupation,

            -- Current Address
            a.houseNo,
            a.streetName,
            a.phase,
            a.subdivisionName,
            a.barangayName,
            a.cityName,
            a.provinceName,

            -- Permanent Address
            pa.houseNo AS houseNoPermanent,
            pa.streetName AS streetNamePermanent,
            pa.phase AS phasePermanent,
            pa.subdivisionName AS subdivisionNamePermanent,
            pa.barangayName AS barangayNamePermanent,
            pa.cityName AS cityNamePermanent,
            pa.provinceName AS provinceNamePermanent

        FROM users u
        INNER JOIN userInfo i ON u.userInfoID = i.userInfoID
        LEFT JOIN addresses a ON i.userInfoID = a.userInfoID
        LEFT JOIN permanentAddresses pa ON i.userInfoID = pa.userInfoID
        WHERE u.userID = $userID";


  $result = mysqli_query($conn, $sql);

  if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result); // <--- assign to $user
  } else {
    echo "❌ No resident found with this ID.";
    exit; // stop the rest of the page if no user
  }
} else {
  echo "⚠️ No resident ID provided.";
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
                  <?= ($user['firstName'] ?? '') . " " . ($user['middleName'] ?? '') . " " . ($user['lastName'] ?? '') . " " . ($user['suffix'] ?? '') ?>
                </p>
                <p><strong>Username:</strong> <?= $user['username'] ?? '<span class="text-muted">No data</span>' ?></p>
                <p><strong>Email:</strong> <?= $user['email'] ?? '<span class="text-muted">No data</span>' ?></p>
                <p><strong>Phone:</strong> <?= $user['phoneNumber'] ?? '<span class="text-muted">No data</span>' ?></p>
              </div>
              <div class="col-12 col-sm-6">
                <p><strong>Gender:</strong> <?= $user['gender'] ?? '<span class="text-muted">No data</span>' ?></p>
                <p><strong>Age:</strong> <?= $user['age'] ?? '<span class="text-muted">No data</span>' ?></p>
                <p><strong>Birthdate:</strong> <?= $user['birthDate'] ?? '<span class="text-muted">No data</span>' ?>
                </p>
                <p><strong>Birthplace:</strong> <?= $user['birthPlace'] ?? '<span class="text-muted">No data</span>' ?>
                </p>
                <p><strong>Civil Status:</strong>
                  <?= $user['civilStatus'] ?? '<span class="text-muted">No data</span>' ?></p>
                <p><strong>Citizenship:</strong>
                  <?= $user['citizenship'] ?? '<span class="text-muted">No data</span>' ?></p>
                <p><strong>Occupation:</strong> <?= $user['occupation'] ?? '<span class="text-muted">No data</span>' ?>
                </p>
              </div>
            </div>

            <hr>

            <!-- Address Info -->
            <h6 class="fw-bold text-muted">Current Address</h6>
            <p>
              <?= !empty($user['houseNo']) ? $user['houseNo'] . ', ' : '' ?>
              <?= !empty($user['streetName']) ? $user['streetName'] . ', ' : '' ?>
              <?= !empty($user['phase']) ? 'Phase ' . $user['phase'] . ', ' : '' ?>
              <?= !empty($user['subdivisionName']) ? $user['subdivisionName'] . ', ' : '' ?>
              <?= !empty($user['barangayName']) ? 'Brgy. ' . $user['barangayName'] . ', ' : '' ?>
              <?= !empty($user['cityName']) ? $user['cityName'] . ', ' : '' ?>
              <?= !empty($user['provinceName']) ? $user['provinceName'] : '' ?>
            </p>

            <h6 class="fw-bold text-muted mt-3">Permanent Address</h6>
            <p>
              <?= !empty($user['houseNoPermanent']) ? $user['houseNoPermanent'] . ', ' : '' ?>
              <?= !empty($user['streetNamePermanent']) ? $user['streetNamePermanent'] . ', ' : '' ?>
              <?= !empty($user['phasePermanent']) ? 'Phase ' . $user['phasePermanent'] . ', ' : '' ?>
              <?= !empty($user['subdivisionNamePermanent']) ? $user['subdivisionNamePermanent'] . ', ' : '' ?>
              <?= !empty($user['barangayNamePermanent']) ? 'Brgy. ' . $user['barangayNamePermanent'] . ', ' : '' ?>
              <?= !empty($user['cityNamePermanent']) ? $user['cityNamePermanent'] . ', ' : '' ?>
              <?= !empty($user['provinceNamePermanent']) ? $user['provinceNamePermanent'] : '' ?>
            </p>
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

            <div class="mb-3">
              <label for="isNew" class="form-label">Account Status</label>
              <select name="isNew" id="isNew" class="form-select">
                <option value="1" <?= ($user['isNew'] ?? 0) == 1 ? 'selected' : '' ?>>New</option>
                <option value="0" <?= ($user['isNew'] ?? 1) == 0 ? 'selected' : '' ?>>Active</option>
              </select>
            </div>

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
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
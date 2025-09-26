<?php
include_once __DIR__ . '/../../sharedAssets/connect.php';

$documentID = $_GET['id'] ?? 0;

if (!$documentID) {
  header("Location: index.php?page=document");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_document'])) {
  $purpose = $_POST['purpose'];
  $documentTypeID = $_POST['documentTypeID'];
  $firstName = $_POST['firstName'];
  $middleName = $_POST['middleName'];
  $lastName = $_POST['lastName'];
  $suffix = $_POST['suffix'];
  $gender = $_POST['gender'];
  $age = $_POST['age'];
  $birthDate = $_POST['birthDate'];
  $birthPlace = $_POST['birthPlace'];
  $civilStatus = $_POST['civilStatus'];
  $citizenship = $_POST['citizenship'];
  $occupation = $_POST['occupation'];
  $residencyType = $_POST['residencyType'];
  $lengthOfStay = $_POST['lengthOfStay'];
  $phoneNumber = $_POST['phoneNumber'];
  $email = $_POST['email'];
  $houseNo = $_POST['houseNo'];
  $streetName = $_POST['streetName'];
  $barangayName = $_POST['barangayName'];
  $cityName = $_POST['cityName'];
  $provinceName = $_POST['provinceName'];
  $phase = $_POST['phase'];
  $subdivisionName = $_POST['subdivisionName'];
  $purok = $_POST['purok'];

  $updateDocQuery = "UPDATE documents SET purpose = '$purpose', documentTypeID = '$documentTypeID' WHERE documentID = '$documentID'";
  $updateDocResult = executeQuery($updateDocQuery);

  $updateUserInfoQuery = "UPDATE userinfo ui 
                         JOIN users u ON ui.userID = u.userID 
                         JOIN documents d ON u.userID = d.userID 
                         SET ui.firstName = '$firstName', ui.middleName = '$middleName', ui.lastName = '$lastName', 
                             ui.suffix = '$suffix', ui.gender = '$gender', ui.age = '$age', 
                             ui.birthDate = '$birthDate', ui.birthPlace = '$birthPlace', 
                             ui.civilStatus = '$civilStatus', ui.citizenship = '$citizenship', 
                             ui.occupation = '$occupation', ui.residencyType = '$residencyType', 
                             ui.lengthOfStay = '$lengthOfStay'
                         WHERE d.documentID = '$documentID'";
  $updateUserInfoResult = executeQuery($updateUserInfoQuery);

  $updateUserQuery = "UPDATE users u 
                     JOIN documents d ON u.userID = d.userID 
                     SET u.phoneNumber = '$phoneNumber', u.email = '$email'
                     WHERE d.documentID = '$documentID'";
  $updateUserResult = executeQuery($updateUserQuery);

  $updateAddressQuery = "UPDATE addresses a 
                        JOIN userinfo ui ON a.userInfoID = ui.userInfoID
                        JOIN users u ON ui.userID = u.userID
                        JOIN documents d ON u.userID = d.userID
                        SET a.houseNo = '$houseNo', a.streetName = '$streetName', 
                            a.barangayName = '$barangayName', a.cityName = '$cityName', 
                            a.provinceName = '$provinceName', a.phase = '$phase', 
                            a.subdivisionName = '$subdivisionName', a.purok = '$purok'
                        WHERE d.documentID = '$documentID'";
  $updateAddressResult = executeQuery($updateAddressQuery);

  $_SESSION['success_message'] = "Document information updated successfully!";
  header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $documentID);
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_status'])) {
  $newStatus = $_POST['new_status'];

  $updateQuery = "UPDATE documents SET documentStatus = '$newStatus' WHERE documentID = '$documentID'";
  if ($newStatus === 'Approved') {
    $updateQuery = "
            UPDATE documents 
            SET documentStatus = '$newStatus', 
                approvalDate = NOW()
            WHERE documentID = '$documentID'
        ";
  } else {
    $updateQuery = "
            UPDATE documents 
            SET documentStatus = '$newStatus',
                approvalDate = NULL
            WHERE documentID = '$documentID'
        ";
  }
  $updateResult = executeQuery($updateQuery);

  $_SESSION['success_message'] = "Status updated successfully!";
  header("Location: ../index.php?page=document");
  exit();
}

$documentQuery = "SELECT 
    d.documentID, 
    d.purpose, 
    d.documentStatus, 
    d.requestDate,
    d.approvalDate,
    d.documentTypeID,
    dt.documentName,
    dt.documentImage,
    c.categoryName,
    CONCAT(ui.firstName, ' ', ui.middleName, ' ', ui.lastName) AS fullname,
    ui.firstName,
    ui.middleName,
    ui.lastName,
    ui.suffix,
    ui.gender,
    ui.age,
    ui.birthDate,
    ui.birthPlace,
    ui.civilStatus,
    ui.citizenship,
    ui.occupation,
    ui.residencyType,
    ui.lengthOfStay,
    u.phoneNumber,
    u.email,
    a.cityName,
    a.provinceName,
    a.barangayName,
    a.streetName,
    a.houseNo,
    a.phase,
    a.subdivisionName,
    a.purok
FROM documents d
JOIN documenttypes dt ON d.documentTypeID = dt.documentTypeID
JOIN categories c ON dt.categoryID = c.categoryID
JOIN users u ON d.userID = u.userID
JOIN userinfo ui ON u.userID = ui.userID
LEFT JOIN addresses a ON ui.userInfoID = a.userInfoID
WHERE d.documentID = '$documentID'";

$documentResult = executeQuery($documentQuery);
$document = mysqli_fetch_assoc($documentResult);

if (!$document) {
  header("Location: index.php?page=document");
  exit();
}

$documentTypesQuery = "SELECT dt.documentTypeID, dt.documentName, c.categoryName 
                       FROM documenttypes dt 
                       JOIN categories c ON dt.categoryID = c.categoryID 
                       ORDER BY c.categoryName, dt.documentName";
$documentTypesResult = executeQuery($documentTypesQuery);
$documentTypes = [];
while ($row = mysqli_fetch_assoc($documentTypesResult)) {
  $documentTypes[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>View Document - <?= htmlspecialchars($document['documentName']) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="../../assets/images/logoSanAntonio.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8f9fa;
    }

    .card {
      box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
      border: none;
    }

    .info-row {
      border-bottom: 1px solid #e9ecef;
      padding: 12px 0;
    }

    .info-row:last-child {
      border-bottom: none;
    }

    .status-badge {
      font-size: 1rem;
      padding: 8px 16px;
    }

    .btn-primary {
      background-color: #31afab;
      border-color: #31afab;
    }

    .btn-primary:hover {
      background-color: #2a9995;
      border-color: #2a9995;
    }

    .edit-mode {
      display: none;
    }

    .view-mode {
      display: block;
    }

    .editing .edit-mode {
      display: block;
    }

    .editing .view-mode {
      display: none;
    }
  </style>
</head>

<body>
  <div class="container-fluid p-3 p-md-4">
    <div class="row">
      <div class="col-12">
        <div class="card mb-4">
          <div class="card-header text-white" style="background-color: #31afab;">
            <div class="d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center">
                <i class="fas fa-file-alt me-3 fs-4"></i>
                <div>
                  <h4 class="mb-0 fw-semibold">Document Details</h4>
                  <small class="opacity-75"><?= htmlspecialchars($document['documentName']) ?></small>
                </div>
              </div>
              <div class="d-flex gap-2">
                <a href="../index.php?page=document" class="btn btn-outline-light btn-sm">
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
                  <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>Document Information
                  </h5>
                  <button type="button" class="btn btn-primary btn-sm" id="editBtn">
                    <i class="fas fa-edit me-2"></i>Edit
                  </button>
                </div>
              </div>
              <div class="card-body" id="documentInfo">

                <div class="view-mode">
                  <div class="row g-3">
                    <div class="col-md-6">
                      <div class="info-row">
                        <strong class="text-muted">Document ID:</strong>
                        <div class="mt-1">#<?= str_pad($document['documentID'], 5, '0', STR_PAD_LEFT) ?></div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="info-row">
                        <strong class="text-muted">Document Type:</strong>
                        <div class="mt-1">
                          <span class="badge bg-info"><?= htmlspecialchars($document['documentName']) ?></span>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="info-row">
                        <strong class="text-muted">Category:</strong>
                        <div class="mt-1"><?= htmlspecialchars($document['categoryName']) ?></div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="info-row">
                        <strong class="text-muted">Request Date:</strong>
                        <div class="mt-1"><?= date('F d, Y g:i A', strtotime($document['requestDate'])) ?></div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="info-row">
                        <strong class="text-muted">Purpose:</strong>
                        <div class="mt-1"><?= htmlspecialchars($document['purpose']) ?></div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="info-row">
                        <strong class="text-muted">Full Name:</strong>
                        <div class="mt-1"><?= htmlspecialchars($document['fullname']) ?></div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="info-row">
                        <strong class="text-muted">Email:</strong>
                        <div class="mt-1"><?= htmlspecialchars($document['email']) ?></div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="info-row">
                        <strong class="text-muted">Phone:</strong>
                        <div class="mt-1"><?= htmlspecialchars($document['phoneNumber']) ?></div>
                      </div>
                    </div>
                    <?php if ($document['age']): ?>
                      <div class="col-md-6">
                        <div class="info-row">
                          <strong class="text-muted">Age:</strong>
                          <div class="mt-1"><?= htmlspecialchars($document['age']) ?> years old</div>
                        </div>
                      </div>
                    <?php endif; ?>
                    <?php if ($document['gender']): ?>
                      <div class="col-md-6">
                        <div class="info-row">
                          <strong class="text-muted">Gender:</strong>
                          <div class="mt-1"><?= htmlspecialchars($document['gender']) ?></div>
                        </div>
                      </div>
                    <?php endif; ?>
                    <?php if ($document['civilStatus']): ?>
                      <div class="col-md-6">
                        <div class="info-row">
                          <strong class="text-muted">Civil Status:</strong>
                          <div class="mt-1"><?= htmlspecialchars($document['civilStatus']) ?></div>
                        </div>
                      </div>
                    <?php endif; ?>
                    <?php if ($document['occupation']): ?>
                      <div class="col-md-6">
                        <div class="info-row">
                          <strong class="text-muted">Occupation:</strong>
                          <div class="mt-1"><?= htmlspecialchars($document['occupation']) ?></div>
                        </div>
                      </div>
                    <?php endif; ?>
                    <?php if ($document['barangayName'] || $document['cityName']): ?>
                      <div class="col-12">
                        <div class="info-row">
                          <strong class="text-muted">Address:</strong>
                          <div class="mt-1">
                            <?php
                            $addressParts = array_filter([
                              $document['houseNo'],
                              $document['streetName'],
                              $document['barangayName'],
                              $document['cityName'],
                              $document['provinceName']
                            ]);
                            echo htmlspecialchars(implode(', ', $addressParts));
                            ?>
                          </div>
                        </div>
                      </div>
                    <?php endif; ?>
                    <?php if ($document['residencyType']): ?>
                      <div class="col-md-6">
                        <div class="info-row">
                          <strong class="text-muted">Residency Type:</strong>
                          <div class="mt-1">
                            <span class="badge bg-secondary"><?= htmlspecialchars($document['residencyType']) ?></span>
                          </div>
                        </div>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>

                <div class="edit-mode">
                  <form method="POST" id="editForm">
                    <div class="row g-3">
                      <div class="col-12">
                        <label class="form-label"><strong>Document Type:</strong></label>
                        <select class="form-select" name="documentTypeID" required>
                          <?php foreach ($documentTypes as $docType): ?>
                            <option value="<?= $docType['documentTypeID'] ?>"
                              <?= $document['documentTypeID'] == $docType['documentTypeID'] ? 'selected' : '' ?>>
                              <?= htmlspecialchars($docType['categoryName']) ?> -
                              <?= htmlspecialchars($docType['documentName']) ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                      <div class="col-12">
                        <label class="form-label"><strong>Purpose:</strong></label>
                        <textarea class="form-control" name="purpose"
                          rows="3"><?= htmlspecialchars($document['purpose']) ?></textarea>
                      </div>
                      <div class="col-md-4">
                        <label class="form-label"><strong>First Name:</strong></label>
                        <input type="text" class="form-control" name="firstName"
                          value="<?= htmlspecialchars($document['firstName']) ?>">
                      </div>
                      <div class="col-md-4">
                        <label class="form-label"><strong>Middle Name:</strong></label>
                        <input type="text" class="form-control" name="middleName"
                          value="<?= htmlspecialchars($document['middleName']) ?>">
                      </div>
                      <div class="col-md-4">
                        <label class="form-label"><strong>Last Name:</strong></label>
                        <input type="text" class="form-control" name="lastName"
                          value="<?= htmlspecialchars($document['lastName']) ?>">
                      </div>
                      <div class="col-md-6">
                        <label class="form-label"><strong>Suffix:</strong></label>
                        <input type="text" class="form-control" name="suffix"
                          value="<?= htmlspecialchars($document['suffix']) ?>">
                      </div>
                      <div class="col-md-6">
                        <label class="form-label"><strong>Gender:</strong></label>
                        <select class="form-select" name="gender">
                          <option value="Male" <?= $document['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                          <option value="Female" <?= $document['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                        </select>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label"><strong>Age:</strong></label>
                        <input type="number" class="form-control" name="age"
                          value="<?= htmlspecialchars($document['age']) ?>">
                      </div>
                      <div class="col-md-6">
                        <label class="form-label"><strong>Birth Date:</strong></label>
                        <input type="date" class="form-control" name="birthDate"
                          value="<?= htmlspecialchars($document['birthDate']) ?>">
                      </div>
                      <div class="col-12">
                        <label class="form-label"><strong>Birth Place:</strong></label>
                        <input type="text" class="form-control" name="birthPlace"
                          value="<?= htmlspecialchars($document['birthPlace']) ?>">
                      </div>
                      <div class="col-md-6">
                        <label class="form-label"><strong>Civil Status:</strong></label>
                        <select class="form-select" name="civilStatus">
                          <option value="Single" <?= $document['civilStatus'] === 'Single' ? 'selected' : '' ?>>Single
                          </option>
                          <option value="Married" <?= $document['civilStatus'] === 'Married' ? 'selected' : '' ?>>Married
                          </option>
                          <option value="Divorced" <?= $document['civilStatus'] === 'Divorced' ? 'selected' : '' ?>>
                            Divorced</option>
                          <option value="Widowed" <?= $document['civilStatus'] === 'Widowed' ? 'selected' : '' ?>>Widowed
                          </option>
                        </select>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label"><strong>Citizenship:</strong></label>
                        <input type="text" class="form-control" name="citizenship"
                          value="<?= htmlspecialchars($document['citizenship']) ?>">
                      </div>
                      <div class="col-md-6">
                        <label class="form-label"><strong>Occupation:</strong></label>
                        <input type="text" class="form-control" name="occupation"
                          value="<?= htmlspecialchars($document['occupation']) ?>">
                      </div>
                      <div class="col-md-6">
                        <label class="form-label"><strong>Residency Type:</strong></label>
                        <select class="form-select" name="residencyType">
                          <option value="Bonafide" <?= $document['residencyType'] === 'Bonafide' ? 'selected' : '' ?>>
                            Bonafide
                          </option>
                          <option value="Transient" <?= $document['residencyType'] === 'Transient' ? 'selected' : '' ?>>
                            Transient
                          </option>
                          <option value="Migrant" <?= $document['residencyType'] === 'Migrant' ? 'selected' : '' ?>>
                            Migrant
                          </option>
                        </select>
                      </div>

                      <div class="col-md-6">
                        <label class="form-label"><strong>Email:</strong></label>
                        <input type="email" class="form-control" name="email"
                          value="<?= htmlspecialchars($document['email']) ?>">
                      </div>
                      <div class="col-md-6">
                        <label class="form-label"><strong>Phone:</strong></label>
                        <input type="text" class="form-control" name="phoneNumber"
                          value="<?= htmlspecialchars($document['phoneNumber']) ?>">
                      </div>
                      <div class="col-md-6">
                        <label class="form-label"><strong>House No:</strong></label>
                        <input type="text" class="form-control" name="houseNo"
                          value="<?= htmlspecialchars($document['houseNo']) ?>">
                      </div>
                      <div class="col-md-6">
                        <label class="form-label"><strong>Street:</strong></label>
                        <input type="text" class="form-control" name="streetName"
                          value="<?= htmlspecialchars($document['streetName']) ?>">
                      </div>
                      <div class="col-md-6">
                        <label class="form-label"><strong>Barangay:</strong></label>
                        <input type="text" class="form-control" name="barangayName"
                          value="<?= htmlspecialchars($document['barangayName']) ?>">
                      </div>
                      <div class="col-md-6">
                        <label class="form-label"><strong>City:</strong></label>
                        <input type="text" class="form-control" name="cityName"
                          value="<?= htmlspecialchars($document['cityName']) ?>">
                      </div>
                      <div class="col-md-6">
                        <label class="form-label"><strong>Province:</strong></label>
                        <input type="text" class="form-control" name="provinceName"
                          value="<?= htmlspecialchars($document['provinceName']) ?>">
                      </div>
                      <div class="col-md-6">
                        <label class="form-label"><strong>Length of Stay:</strong></label>
                        <input type="text" class="form-control" name="lengthOfStay"
                          value="<?= htmlspecialchars($document['lengthOfStay']) ?>">
                      </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                      <button type="button" class="btn btn-secondary" id="cancelBtn">
                        <i class="fas fa-times me-2"></i>Cancel
                      </button>
                      <button type="submit" name="update_document" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Changes
                      </button>
                    </div>
                  </form>
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
                <form method="POST">
                  <?php
                  $badgeClass = match ($document['documentStatus']) {
                    'Pending' => 'bg-warning text-dark',
                    'Approved' => 'bg-success',
                    'Denied' => 'bg-danger',
                    default => 'bg-secondary'
                  };
                  ?>
                  <div class="mb-3 text-center">
                    <span class="badge <?= $badgeClass ?> status-badge">
                      <i class="fas fa-circle me-2"></i><?= $document['documentStatus'] ?>
                    </span>
                  </div>

                  <div class="mb-3">
                    <label class="form-label"><strong>Change Status:</strong></label>
                    <select class="form-select" name="new_status" id="statusSelect">
                      <option value="Pending" <?= $document['documentStatus'] === 'Pending' ? 'selected' : '' ?>>Pending
                      </option>
                      <option value="Approved" <?= $document['documentStatus'] === 'Approved' ? 'selected' : '' ?>>Approved
                      </option>
                      <option value="Denied" <?= $document['documentStatus'] === 'Denied' ? 'selected' : '' ?>>Denied
                      </option>
                    </select>
                  </div>
                  <div class="d-flex justify-content-end gap-2">
                    <a href="../index.php?page=document" class="btn btn-secondary">
                      <i class="fas fa-arrow-left me-2"></i>Back
                    </a>

                    <button type="submit" name="change_status" class="btn btn-primary">
                      <i class="fas fa-check me-2"></i>Done
                    </button>
                  </div>

                  <?php if ($document['documentStatus'] === 'Approved' && $document['approvalDate']): ?>
                    <div class="mt-3 pt-3 border-top text-center">
                      <small class="text-muted">
                        <i class="fas fa-calendar-check me-1"></i>
                        Approved on <?= date('M d, Y', strtotime($document['approvalDate'])) ?>
                      </small>
                    </div>
                  <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.getElementById('editBtn').addEventListener('click', function () {
      document.getElementById('documentInfo').classList.add('editing');
      this.style.display = 'none';
    });

    document.getElementById('cancelBtn').addEventListener('click', function () {
      document.getElementById('documentInfo').classList.remove('editing');
      document.getElementById('editBtn').style.display = 'block';
    });
  </script>
</body>

</html>
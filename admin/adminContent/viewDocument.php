<?php
include_once __DIR__ . '/../../sharedAssets/connect.php';

$documentID = $_GET['id'] ?? 0;

if (!$documentID) {
  header("Location: index.php?page=document");
  exit();
}

if (!empty($documentID)) {
    $checkQuery = "SELECT documentID FROM documents WHERE documentID = " . (int)$documentID;
    $checkResult = mysqli_query($conn, $checkQuery);
    
    if (!$checkResult || mysqli_num_rows($checkResult) === 0) {
        header("Location: ../index.php?page=document");
        exit();
    }
}

$documentQuery = "SELECT 
    d.documentID, 
    d.purpose, 
    d.documentStatus,
    d.requestDate,
    d.approvalDate,
    d.cancelledDate,
    d.deniedDate,
    d.archiveDate,
    d.documentTypeID,
    d.businessName,
    d.businessAddress,
    d.businessNature,
    d.controlNo,
    d.ownership,
    d.spouseName,
    d.marriageYear,
    d.childNo,
    d.soloParentSinceDate,
    dt.documentName,
    dt.documentImage,
    CONCAT(ui.firstName, ' ', ui.middleName, ' ', ui.lastName) AS fullname,
    ui.firstName,
    ui.middleName,
    ui.lastName,
    ui.suffix,
    ui.gender,
    ui.age,
    ui.birthDate,
    ui.birthPlace,
    ui.bloodType,
    ui.civilStatus,
    ui.citizenship,
    ui.occupation,
    ui.residencyType,
    ui.lengthOfStay,
    u.phoneNumber,
    u.email,
    ui.remarks,
    a.cityName,
    a.provinceName,
    a.barangayName,
    a.streetName,
    a.blockLotNo AS houseNo,
    a.phase,
    a.subdivisionName,
    a.purok,
    pa.permanentCityName,
    pa.permanentProvinceName,
    pa.permanentBarangayName,
    pa.permanentStreetName,
    pa.permanentBlockLotNo AS permanentHouseNo,
    pa.permanentPhase,
    pa.permanentSubdivisionName,
    pa.permanentPurok
FROM documents d
JOIN documenttypes dt ON d.documentTypeID = dt.documentTypeID
JOIN users u ON d.userID = u.userID
JOIN userinfo ui ON u.userID = ui.userID
LEFT JOIN addresses a ON ui.userInfoID = a.userInfoID
LEFT JOiN permanentaddresses pa ON ui.userInfoID = a.userInfoID
WHERE d.documentID = '$documentID'";

$documentResult = executeQuery($documentQuery);
$document = mysqli_fetch_assoc($documentResult);

$documentTypeID = $document['documentTypeID'];

if (!$document) {
  header("Location: index.php?page=document");
  exit();
}

$documentTypesQuery = "SELECT dt.documentTypeID, dt.documentName
                       FROM documenttypes dt 
                       ORDER BY dt.documentName";
$documentTypesResult = executeQuery($documentTypesQuery);
$documentTypes = [];
while ($row = mysqli_fetch_assoc($documentTypesResult)) {
  $documentTypes[] = $row;
}

if (isset($_POST['update_document'])) {
  $purpose = $_POST['purpose'] ?? '';
  $businessName = $_POST['businessName'] ?? '';
  $businessAddress = $_POST['businessAddress'] ?? '';
  $businessNature = $_POST['businessNature'] ?? '';
  $controlNo = $_POST['controlNo'] ?? '';
  $ownership = $_POST['ownership'] ?? '';
  $educationStatus = $_POST['educationStatus'] ?? '';
  $spouseName = $_POST['spouseName'] ?? '';
  $marriageYear = $_POST['marriageYear'] ?? '';
  $childNo = $_POST['childNo'] ?? '';
  $soloParentSinceDate = $_POST['soloParentSinceDate'] ?? '';

  // 4, 6 Wala 

  if ($documentTypeID == 2) {
      $editRequestQuery = "UPDATE documents SET purpose = '$purpose', businessName = '$businessName', businessAddress = '$businessAddress', businessNature = '$businessNature', controlNo = $controlNo, ownership = '$ownership' WHERE documentID = $documentID";
  } elseif ($documentTypeID == 1 || $documentTypeID == 3|| $documentTypeID == 5 || $documentTypeID == 8) {
      $editRequestQuery = "UPDATE documents SET purpose = '$purpose' WHERE documentID = '$documentID'";
  } elseif ($documentTypeID == 7) {
      $editRequestQuery = "UPDATE documents SET spouseName = '$spouseName', marriageYear = $marriageYear WHERE documentID = $documentID";
  } elseif ($documentTypeID == 9) {
      $editRequestQuery = "UPDATE documents SET childNo = $childNo, soloParentSinceDate = '$soloParentSinceDate' WHERE documentID =$documentID";
  } else {
      die("Invalid document type ID: " . $documentTypeID);
  }

  $editRequestResult = executeQuery($editRequestQuery);
    
  header("Location: viewDocument.php?id=$documentID");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_document'])) {
  $purpose = $_POST['purpose'];
  $documentTypeID = $_POST['documentTypeID'];

  $updateDocQuery = "UPDATE documents 
                     SET purpose = '$purpose', documentTypeID = '$documentTypeID' 
                     WHERE documentID = '$documentID'";
  $updateDocResult = executeQuery($updateDocQuery);

  $_SESSION['success_message'] = "Document information updated successfully!";
  header("Location: viewDocument.php?id=$documentID");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_status'])) {
  $newStatus = $_POST['new_status'];

  if ($newStatus === 'Approved') {
      $updateQuery = "UPDATE documents SET documentStatus = '$newStatus', approvalDate = NOW(), cancelledDate = NULL, deniedDate = NULL, archiveDate = NULL WHERE documentID = '$documentID'";
  } elseif ($newStatus === 'Cancelled') {
      $updateQuery = "UPDATE documents SET documentStatus = '$newStatus', cancelledDate = NOW(), approvalDate = NULL, deniedDate = NULL, archiveDate = NULL WHERE documentID = '$documentID'";
  } elseif ($newStatus === 'Denied') {
      $updateQuery = "UPDATE documents SET documentStatus = '$newStatus', deniedDate = NOW(), approvalDate = NULL, cancelledDate = NULL, archiveDate = NULL WHERE documentID = '$documentID'";
  } elseif ($newStatus === 'Archived') {
      $updateQuery = "UPDATE documents SET documentStatus = '$newStatus', archiveDate = NOW(), approvalDate = NULL, cancelledDate = NULL, deniedDate = NULL WHERE documentID = '$documentID'";
  } else {
    $updateQuery = "UPDATE documents SET documentStatus = '$newStatus', approvalDate = NULL, cancelledDate = NULL, deniedDate = NULL, archiveDate = NULL WHERE documentID = '$documentID'";
  }

  $updateResult = executeQuery($updateQuery);

  $_SESSION['success_message'] = "Status updated successfully!";
  header("Location: ../index.php?page=document");
  exit();
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
      background-color: #19AFA5;
      border-color: #19AFA5;
    }

    .btn-primary:hover {
      background-color: #11A1A1;
      border-color: #11A1A1;
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

    .form-control:focus, .form-select:focus, .form-check-input:focus {
      border: 1px solid #19AFA5;
      outline: none;
      box-shadow: none;
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
                  <h5 class="card-title mb-0" style="color: black;">
                    <i class="fas fa-info-circle me-2"></i>Document Information
                  </h5>
                  <?php if ($documentTypeID != 4 && $documentTypeID != 6) { ?>
                    <button type="button" class="btn btn-primary btn-sm" id="editBtn">
                      <i class="fas fa-edit me-2"></i>Edit
                    </button>
                  <?php } ?>
                </div>
              </div>
              <div class="card-body" id="documentInfo">

                <div class="view-mode" style="color: black;">
                  <div class="row g-3">
                    <div class="col-md-4">
                      <div class="info-row">
                        <strong >Document ID:</strong>
                        <div class="mt-1">#<?= str_pad($document['documentID'], 5, '0', STR_PAD_LEFT) ?></div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="info-row">
                        <strong>Document Type:</strong>
                        <div class="mt-1">
                          <span><?= htmlspecialchars($document['documentName']) ?></span>
                        </div>
                      </div>
                    </div>
                    <?php if ($documentTypeID == 2) { ?>
                        
                      <div class="col-md-4">
                        <div class="info-row">
                          <strong>Business Name:</strong>
                          <div class="mt-1">
                            <span><?= htmlspecialchars($document['businessName']) ?></span>
                          </div>
                        </div>
                      </div>
                      
                      <div class="col-md-4">
                        <div class="info-row">
                          <strong>Business Address:</strong>
                          <div class="mt-1">
                            <span><?= htmlspecialchars($document['businessAddress']) ?></span>
                          </div>
                        </div>
                      </div>

                      <div class="col-md-4">
                        <div class="info-row">
                          <strong>Owner's Name:</strong>
                          <div class="mt-1">
                            <span><?= htmlspecialchars($document['fullname']) ?></span>
                          </div>
                        </div>
                      </div>

                      <div class="col-md-4">
                        <div class="info-row">
                          <strong>Owner's Address:</strong>
                          <div class="mt-1">
                            <span>
                              <?php
                                $addressParts = array_filter([
                                  ucwords(strtolower($document['permanentHouseNo'])),
                                  ucwords(strtolower($document['permanentPurok'])),
                                  ucwords(strtolower($document['permanentSubdivisionName'])),
                                  ucwords(strtolower($document['permanentPhase'])),
                                  ucwords(strtolower($document['permanentStreetName'])),
                                  ucwords(strtolower($document['permanentBarangayName'])),
                                  ucwords(strtolower($document['permanentCityName'])),
                                  ucwords(strtolower($document['permanentProvinceName']))
                                ]);
                                echo htmlspecialchars(implode(' ', $addressParts));
                              ?>
                            </span>
                          </div>
                        </div>
                      </div>

                      <div class="col-md-4">
                        <div class="info-row">
                          <strong>Nature of Business:</strong>
                          <div class="mt-1">
                            <span><?= htmlspecialchars($document['businessNature']) ?></span>
                          </div>
                        </div>
                      </div>

                      <div class="col-md-4">
                        <div class="info-row">
                          <strong>Control No:</strong>
                          <div class="mt-1">
                            <span><?= htmlspecialchars($document['controlNo']) ?></span>
                          </div>
                        </div>
                      </div>
                      
                      <div class="col-md-4">
                        <div class="info-row">
                          <strong>Purpose:</strong>
                          <div class="mt-1">
                            <span><?= htmlspecialchars($document['purpose']) ?></span>
                          </div>
                        </div>
                      </div>

                      <div class="col-md-4">
                        <div class="info-row">
                          <strong>Ownership:</strong>
                          <div class="mt-1">
                            <span><?= htmlspecialchars($document['ownership']) ?></span>
                          </div>
                        </div>
                      </div>

                    <?php } elseif ($documentTypeID == 1 || $documentTypeID == 5 || $documentTypeID == 8 ) { ?>
                      
                      <div class="col-md-4">
                        <div class="info-row">
                          <strong>Purpose:</strong>
                          <div class="mt-1">
                            <span><?= htmlspecialchars($document['purpose']) ?></span>
                          </div>
                        </div>
                      </div>

                    <?php } elseif ($documentTypeID == 3) { ?>
                      
                      <div class="col-md-4">
                        <div class="info-row">
                          <strong>Purpose:</strong>
                          <div class="mt-1">
                            <span><?= htmlspecialchars($document['purpose']) ?></span>
                          </div>
                        </div>
                      </div>

                    <?php } elseif ($documentTypeID == 7) { ?>

                      <div class="col-md-4">
                        <div class="info-row">
                          <strong>Spouse Name:</strong>
                          <div class="mt-1">
                            <span><?= htmlspecialchars($document['spouseName']) ?></span>
                          </div>
                        </div>
                      </div>

                      <div class="col-md-4">
                        <div class="info-row">
                          <strong>Year of Marriage:</strong>
                          <div class="mt-1">
                            <span><?= htmlspecialchars($document['marriageYear']) ?></span>
                          </div>
                        </div>
                      </div>

                    <?php } elseif ($documentTypeID == 9) { ?>

                      <div class="col-md-4">
                        <div class="info-row">
                          <strong>Number of Children:</strong>
                          <div class="mt-1">
                            <span><?= htmlspecialchars($document['childNo']) ?></span>
                          </div>
                        </div>
                      </div>

                      <div class="col-md-4">
                        <div class="info-row">
                          <strong>Solo Parent Since:</strong>
                          <div class="mt-1">
                            <span><?= htmlspecialchars($document['soloParentSinceDate']) ?></span>
                          </div>
                        </div>
                      </div>
                    
                    <?php } else { ?>

                      <div class="col-md-4">
                        <div class="info-row">
                          <strong>Purpose:</strong>
                          <div class="mt-1">General Request</div>
                        </div>
                      </div>

                    <?php } ?>
                    <div class="col-md-4">
                      <div class="info-row">
                        <strong>Requester's Full Name:</strong>
                        <div class="mt-1"><?= htmlspecialchars($document['fullname']) ?></div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="info-row">
                        <strong>Request Date & Time:</strong>
                        <div class="mt-1"><?= date('F d, Y g:i A', strtotime($document['requestDate'])) ?></div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="info-row">
                        <strong>Email:</strong>
                        <div class="mt-1"><?= htmlspecialchars($document['email']) ?></div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="info-row">
                        <strong>Phone Number:</strong>
                        <div class="mt-1"><?= htmlspecialchars($document['phoneNumber']) ?></div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="info-row">
                        <strong>Date of Birth:</strong>
                        <div class="mt-1"><?= date('F d, Y', strtotime($document['birthDate'])) ?></div>
                      </div>
                    </div>
                    <?php if ($document['age']): ?>
                      <div class="col-md-4">
                        <div class="info-row">
                          <strong>Age:</strong>
                          <div class="mt-1"><?= htmlspecialchars($document['age']) ?> years old</div>
                        </div>
                      </div>
                    <?php endif; ?>
                    <?php if ($document['birthPlace']): ?>
                      <div class="col-md-4">
                        <div class="info-row">
                          <strong>Place of Birth:</strong>
                          <div class="mt-1"><?= htmlspecialchars($document['birthPlace']) ?></div>
                        </div>
                      </div>
                    <?php endif; ?>
                    <?php if ($document['gender']): ?>
                      <div class="col-md-4">
                        <div class="info-row">
                          <strong>Gender:</strong>
                          <div class="mt-1"><?= htmlspecialchars($document['gender']) ?></div>
                        </div>
                      </div>
                    <?php endif; ?>
                    <?php if ($document['bloodType']): ?>
                      <div class="col-md-4">
                        <div class="info-row">
                          <strong>Blood Type:</strong>
                          <div class="mt-1"><?= htmlspecialchars($document['bloodType']) ?></div>
                        </div>
                      </div>
                    <?php endif; ?>
                    <?php if ($document['civilStatus']): ?>
                      <div class="col-md-4">
                        <div class="info-row">
                          <strong>Civil Status:</strong>
                          <div class="mt-1"><?= htmlspecialchars($document['civilStatus']) ?></div>
                        </div>
                      </div>
                    <?php endif; ?>
                    <?php if ($document['citizenship']): ?>
                      <div class="col-md-4">
                        <div class="info-row">
                          <strong>Citizenship:</strong>
                          <div class="mt-1"><?= htmlspecialchars($document['citizenship']) ?></div>
                        </div>
                      </div>
                    <?php endif; ?>
                    <?php if ($document['occupation']): ?>
                      <div class="col-md-4">
                        <div class="info-row">
                          <strong>Occupation:</strong>
                          <div class="mt-1"><?= htmlspecialchars($document['occupation']) ?></div>
                        </div>
                      </div>
                    <?php endif; ?>
                    <?php if ($document['remarks']): ?>
                      <div class="col-md-4">
                        <div class="info-row">
                          <strong>Remarks:</strong>
                          <div class="mt-1"><?= htmlspecialchars($document['remarks']) ?></div>
                        </div>
                      </div>
                    <?php endif; ?>
                    <?php if ($document['lengthOfStay']): ?>
                      <div class="col-md-4">
                        <div class="info-row">
                          <strong>Length Of Stay:</strong>
                          <div class="mt-1">
                            <span><?= htmlspecialchars($document['lengthOfStay']) . ' ' . ((int)$document['lengthOfStay'] === 1 ? 'year' : 'years') ?></span>
                          </div>
                        </div>
                      </div>
                    <?php endif; ?>
                    <?php if ($document['residencyType']): ?>
                      <div class="col-md-4">
                        <div class="info-row">
                          <strong >Residency Type:</strong>
                          <div class="mt-1">
                            <span><?= htmlspecialchars($document['residencyType']) ?></span>
                          </div>
                        </div>
                      </div>
                    <?php endif; ?>
                    <?php if ($document['barangayName'] || $document['cityName']): ?>
                      <?php if ($documentTypeID == 2 || $documentTypeID == 7 || $documentTypeID == 9) { ?>
                        <div class="col-4">
                          <div class="info-row">
                            <strong>Current Address:</strong>
                            <div class="mt-1">
                              <?php
                              $addressParts = array_filter([
                                ucwords(strtolower($document['houseNo'])),
                                ucwords(strtolower($document['purok'])),
                                ucwords(strtolower($document['subdivisionName'])),
                                ucwords(strtolower($document['phase'])),
                                ucwords(strtolower($document['streetName'])),
                                ucwords(strtolower($document['barangayName'])),
                                ucwords(strtolower($document['cityName'])),
                                ucwords(strtolower($document['provinceName']))
                              ]);
                              echo htmlspecialchars(implode(' ', $addressParts));
                              ?>
                            </div>
                          </div>
                        </div>
                      <?php } else { ?>
                        <div class="col-6">
                          <div class="info-row">
                            <strong>Current Address:</strong>
                            <div class="mt-1">
                              <?php
                              $addressParts = array_filter([
                                ucwords(strtolower($document['houseNo'])),
                                ucwords(strtolower($document['purok'])),
                                ucwords(strtolower($document['subdivisionName'])),
                                ucwords(strtolower($document['phase'])),
                                ucwords(strtolower($document['streetName'])),
                                ucwords(strtolower($document['barangayName'])),
                                ucwords(strtolower($document['cityName'])),
                                ucwords(strtolower($document['provinceName']))
                              ]);
                              echo htmlspecialchars(implode(', ', $addressParts));
                              ?>
                            </div>
                          </div>
                        </div>
                      <?php } ?>
                    <?php endif; ?>
                    <?php if ($document['barangayName'] || $document['cityName']): ?>
                      <?php if ($documentTypeID == 2 || $documentTypeID == 7 || $documentTypeID == 9) { ?>
                        <div class="col-4">
                          <div class="info-row">
                            <strong>Permanent Address:</strong>
                            <div class="mt-1">
                              <?php
                              $addressParts = array_filter([
                                ucwords(strtolower($document['permanentHouseNo'])),
                                ucwords(strtolower($document['permanentPurok'])),
                                ucwords(strtolower($document['permanentSubdivisionName'])),
                                ucwords(strtolower($document['permanentPhase'])),
                                ucwords(strtolower($document['permanentStreetName'])),
                                ucwords(strtolower($document['permanentBarangayName'])),
                                ucwords(strtolower($document['permanentCityName'])),
                                ucwords(strtolower($document['permanentProvinceName']))
                              ]);
                              echo htmlspecialchars(implode(', ', $addressParts));
                              ?>
                            </div>
                          </div>
                        </div>
                      <?php } else { ?>
                        <div class="col-6">
                          <div class="info-row">
                            <strong>Permanent Address:</strong>
                            <div class="mt-1">
                              <?php
                              $addressParts = array_filter([
                                ucwords(strtolower($document['permanentHouseNo'])),
                                ucwords(strtolower($document['permanentPurok'])),
                                ucwords(strtolower($document['permanentSubdivisionName'])),
                                ucwords(strtolower($document['permanentPhase'])),
                                ucwords(strtolower($document['permanentStreetName'])),
                                ucwords(strtolower($document['permanentBarangayName'])),
                                ucwords(strtolower($document['permanentCityName'])),
                                ucwords(strtolower($document['permanentProvinceName']))
                              ]);
                              echo htmlspecialchars(implode(', ', $addressParts));
                              ?>
                            </div>
                          </div>
                        </div>
                      <?php } ?>
                    <?php endif; ?>
                  </div>
                </div>

                <div class="edit-mode">
                  <form method="POST" id="editForm">
                    <div class="row g-3">
                      <div class="col-md-4">
                        <div class="info-row">
                          <strong>Document ID:</strong>
                          <div class="mt-1">#<?= str_pad($document['documentID'], 5, '0', STR_PAD_LEFT) ?></div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="info-row">
                          <strong>Document Type:</strong>
                          <div class="mt-1"><?= htmlspecialchars($document['documentName']) ?></div>
                        </div>
                      </div>
                        
                      <?php if ($documentTypeID == 2) { ?>
                        
                        <div class="col-md-4">
                          <label class="form-label"><strong>Business Name:</strong></label>
                          <input type="text" class="form-control" id="businessName" value="<?= htmlspecialchars($document['businessName']) ?>" name="businessName"  placeholder="Business Name" required>
                        </div>
                        
                        <div class="col-md-4">
                          <label class="form-label"><strong>Business Address:</strong></label>
                          <input type="text" class="form-control" id="businessAddress" value="<?= htmlspecialchars($document['businessAddress']) ?>" name="businessAddress" placeholder="Business Address" required>
                        </div>

                        <div class="col-md-4">
                          <label class="form-label"><strong>Owner's Name:</strong></label>
                          <div class="mt-1"><?= htmlspecialchars($document['fullname']) ?></div>
                        </div>

                        <div class="col-md-4">
                          <label class="form-label"><strong>Owner's Address:</strong></label>
                          <div class="mt-1">
                            <?php
                              $addressParts = array_filter([
                                ucwords(strtolower($document['permanentHouseNo'])),
                                ucwords(strtolower($document['permanentPurok'])),
                                ucwords(strtolower($document['permanentSubdivisionName'])),
                                ucwords(strtolower($document['permanentPhase'])),
                                ucwords(strtolower($document['permanentStreetName'])),
                                ucwords(strtolower($document['permanentBarangayName'])),
                                ucwords(strtolower($document['permanentCityName'])),
                                ucwords(strtolower($document['permanentProvinceName']))
                              ]);
                              echo htmlspecialchars(implode(' ', $addressParts));
                            ?>
                          </div>
                        </div>

                        <div class="col-md-4">
                          <label class="form-label"><strong>Nature of Business:</strong></label>
                          <select class="form-select" id="businessNature" name="businessNature" required>
                            <option value="Sari-Sari Store" <?= $document['businessNature'] === 'Sari-Sari Store' ? 'selected' : '' ?>>Sari-Sari Store</option>
                            <option value="Food & Beverage" <?= $document['businessNature'] === 'Food & Beverage' ? 'selected' : '' ?>>Food & Beverage</option>
                            <option value="Retail" <?= $document['businessNature'] === 'Retail' ? 'selected' : '' ?>>Retail</option>
                            <option value="Services" <?= $document['businessNature'] === 'Services' ? 'selected' : '' ?>>Services</option>
                            <option value="Agriculture" <?= $document['businessNature'] === 'Agriculture' ? 'selected' : '' ?>>Agriculture</option>
                            <option value="Manufacturing" <?= $document['businessNature'] === 'Manufacturing' ? 'selected' : '' ?>>Manufacturing</option>
                            <option value="Transportation" <?= $document['businessNature'] === 'Transportation' ? 'selected' : '' ?>>Transportation</option>
                          </select>
                        </div>

                        <div class="col-md-4">
                          <label class="form-label"><strong>Control No:</strong></label>
                          <input type="number" class="form-control" id="controlNo" value="<?= htmlspecialchars($document['controlNo']) ?>" name="controlNo" placeholder="Control No." min="0" onkeydown="return !['e','E','-','+','.',','].includes(event.key)">
                        </div>
                        
                        <div class="col-md-4">
                          <label class="form-label"><strong>Purpose:</strong></label>
                          <select class="form-select" id="businessClearancePurpose" name="purpose" required>
                            <option value="New" <?= $document['purpose'] === 'New' ? 'selected' : '' ?>>New</option>
                            <option value="Renewal" <?= $document['purpose'] === 'Renewal' ? 'selected' : '' ?>>Renewal</option>
                            <option value="Closure" <?= $document['purpose'] === 'Closure' ? 'selected' : '' ?>>Closure</option>
                            <option value="Expansion" <?= $document['purpose'] === 'Expansion' ? 'selected' : '' ?>>Expansion</option>
                          </select>
                        </div>

                        <div class="col-md-4">
                          <label class="form-label"><strong>Ownership:</strong></label>
                          <select class="form-select" id="ownership" name="ownership" required>
                            <option value="Sole Proprietorship" <?= $document['ownership'] === 'Sole Proprietorship' ? 'selected' : '' ?>>Sole Proprietorship</option>
                            <option value="Partnership" <?= $document['ownership'] === 'Partnership' ? 'selected' : '' ?>>Partnership</option>
                            <option value="Corporation" <?= $document['ownership'] === 'Corporation' ? 'selected' : '' ?>>Corporation</option>
                            <option value="Cooperative" <?= $document['ownership'] === 'Cooperative' ? 'selected' : '' ?>>Cooperative</option>
                          </select>
                        </div>

                      <?php } elseif ($documentTypeID == 1 || $documentTypeID == 5 || $documentTypeID == 8 ) { ?>
                        
                        <div class="col-md-4">
                          <label class="form-label"><strong>Purpose:</strong></label>
                          <select class="form-select selectPurpose" id="purpose" name="purpose" required>
                            <option value="Employment" <?= $document['purpose'] === 'Employment' ? 'selected' : '' ?>>Employment</option>
                            <option value="Job Requirement / Local Employment" <?= $document['purpose'] === 'Job Requirement / Local Employment' ? 'selected' : '' ?>>Job Requirement / Local Employment</option>
                            <option value="Overseas Employment (OFW)" <?= $document['purpose'] === 'Overseas Employment (OFW)' ? 'selected' : '' ?>>Overseas Employment (OFW)</option>
                            <option value="School Requirement / Enrollment" <?= $document['purpose'] === 'School Requirement / Enrollment' ? 'selected' : '' ?>>School Requirement / Enrollment</option>
                            <option value="Scholarship Application" <?= $document['purpose'] === 'Scholarship Application' ? 'selected' : '' ?>>Scholarship Application</option>
                            <option value="Medical Assistance" <?= $document['purpose'] === 'Medical Assistance' ? 'selected' : '' ?>>Medical Assistance</option>
                            <option value="Hospital Requirement" <?= $document['purpose'] === 'Hospital Requirement' ? 'selected' : '' ?>>Hospital Requirement</option>
                            <option value="Legal Requirement / Court Use" <?= $document['purpose'] === 'Legal Requirement / Court Use' ? 'selected' : '' ?>>Legal Requirement / Court Use</option>
                            <option value="NBI / Police Clearance" <?= $document['purpose'] === 'NBI / Police Clearance' ? 'selected' : '' ?>>NBI / Police Clearance</option>
                            <option value="Passport Application / Renewal" <?= $document['purpose'] === 'Passport Application / Renewal' ? 'selected' : '' ?>>Passport Application / Renewal</option>
                            <option value="Driver's License" <?= $document['purpose'] === "Driver's License" ? 'selected' : '' ?>>Driver's License</option>
                            <option value="Loan Application" <?= $document['purpose'] === 'Loan Application' ? 'selected' : '' ?>>Loan Application</option>
                          </select>
                        </div>

                      <?php } elseif ($documentTypeID == 3) { ?>
                        
                        <div class="col-md-4">
                          <label class="form-label"><strong>Purpose:</strong></label>
                          <select class="form-select" id="constructionClearancePurpose" name="purpose" required>
                            <option value="New Construction" <?= $document['purpose'] === 'New Construction' ? 'selected' : '' ?>>New Construction</option>
                            <option value="House Renovation" <?= $document['purpose'] === 'House Renovation' ? 'selected' : '' ?>>House Renovation</option>
                            <option value="Extension / Expansion" <?= $document['purpose'] === 'Extension / Expansion' ? 'selected' : '' ?>>Extension / Expansion</option>
                            <option value="Fence Construction" <?= $document['purpose'] === 'Fence Construction' ? 'selected' : '' ?>>Fence Construction</option>
                            <option value="Demolition" <?= $document['purpose'] === 'Demolition' ? 'selected' : '' ?>>Demolition</option>
                            <option value="Repair / Maintenance" <?= $document['purpose'] === 'Repair / Maintenance' ? 'selected' : '' ?>>Repair / Maintenance</option>
                          </select>
                        </div>

                      <?php } elseif ($documentTypeID == 7) { ?>

                        <div class="col-md-4">
                          <label class="form-label"><strong>Spouse Name:</strong></label>
                          <input type="text" class="form-control" id="spouseName" value="<?= htmlspecialchars($document['spouseName']) ?>" name="spouseName" placeholder="Spouse Name" required>
                        </div>

                        <div class="col-md-4">
                          <label class="form-label"><strong>Year of Marriage:</strong></label>
                          <input type="number" class="form-control" id="marriageYear" name="marriageYear" value="<?= htmlspecialchars($document['marriageYear']) ?>" placeholder="Year of Marriage (e.g., 2003)" min="1900" max="<?php echo date('Y'); ?>" oninput="if(this.value.length > 4) this.value = this.value.slice(0, 4);" onkeydown="return !['e','E','-','+','.',','].includes(event.key)" required>
                        </div>

                      <?php } elseif ($documentTypeID == 9) { ?>

                        <div class="col-md-4">
                          <label class="form-label"><strong>Number of Children:</strong></label>
                          <input type="number" class="form-control" id="childNo" name="childNo" value="<?= htmlspecialchars($document['childNo']) ?>" placeholder="Number of Children (e.g., 2)" min="0" oninput="if(this.value.length > 2) this.value = this.value.slice(0, 2);" onkeydown="return !['e','E','-','+','.',','].includes(event.key)" required>
                        </div>

                        <div class="col-md-4">
                          <label class="form-label"><strong>Solo Parent Since:</strong></label>
                          <input type="number" class="form-control" id="soloParentSinceDate" name="soloParentSinceDate" value="<?= htmlspecialchars($document['soloParentSinceDate']) ?>" placeholder="Solo Parent Since (e.g., 2003)" min="1900" max="<?php echo date('Y'); ?>" oninput="if(this.value.length > 4) this.value = this.value.slice(0, 4);" onkeydown="return !['e','E','-','+','.',','].includes(event.key)" required>
                        </div>
                      
                      <?php } else { ?>

                        <div class="col-md-4">
                          <div class="info-row">
                            <strong>Purpose:</strong>
                            <div class="mt-1">General Request</div>
                          </div>
                        </div>

                      <?php } ?>

                      <div class="col-md-4">
                        <div class="info-row">
                          <strong>Requester's Full Name:</strong>
                          <div class="mt-1"><?= htmlspecialchars($document['fullname']) ?></div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="info-row">
                          <strong>Request Date & Time:</strong>
                          <div class="mt-1"><?= date('F d, Y g:i A', strtotime($document['requestDate'])) ?></div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="info-row">
                          <strong>Email:</strong>
                          <div class="mt-1"><?= htmlspecialchars($document['email']) ?></div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="info-row">
                          <strong>Phone Number:</strong>
                          <div class="mt-1"><?= htmlspecialchars($document['phoneNumber']) ?></div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="info-row">
                          <strong>Date of Birth:</strong>
                          <div class="mt-1"><?= date('F d, Y', strtotime($document['birthDate'])) ?></div>
                        </div>
                      </div>
                      <?php if ($document['age']): ?>
                        <div class="col-md-4">
                          <div class="info-row">
                            <strong>Age:</strong>
                            <div class="mt-1"><?= htmlspecialchars($document['age']) ?> years old</div>
                          </div>
                        </div>
                      <?php endif; ?>
                      <?php if ($document['birthPlace']): ?>
                        <div class="col-md-4">
                          <div class="info-row">
                            <strong>Place of Birth:</strong>
                            <div class="mt-1"><?= htmlspecialchars($document['birthPlace']) ?></div>
                          </div>
                        </div>
                      <?php endif; ?>
                      <?php if ($document['gender']): ?>
                        <div class="col-md-4">
                          <div class="info-row">
                            <strong>Gender:</strong>
                            <div class="mt-1"><?= htmlspecialchars($document['gender']) ?></div>
                          </div>
                        </div>
                      <?php endif; ?>
                      <?php if ($document['bloodType']): ?>
                        <div class="col-md-4">
                          <div class="info-row">
                            <strong>Blood Type:</strong>
                            <div class="mt-1"><?= htmlspecialchars($document['bloodType']) ?></div>
                          </div>
                        </div>
                      <?php endif; ?>
                      <?php if ($document['civilStatus']): ?>
                        <div class="col-md-4">
                          <div class="info-row">
                            <strong>Civil Status:</strong>
                            <div class="mt-1"><?= htmlspecialchars($document['civilStatus']) ?></div>
                          </div>
                        </div>
                      <?php endif; ?>
                      <?php if ($document['citizenship']): ?>
                        <div class="col-md-4">
                          <div class="info-row">
                            <strong>Citizenship:</strong>
                            <div class="mt-1"><?= htmlspecialchars($document['citizenship']) ?></div>
                          </div>
                        </div>
                      <?php endif; ?>
                      <?php if ($document['occupation']): ?>
                        <div class="col-md-4">
                          <div class="info-row">
                            <strong>Occupation:</strong>
                            <div class="mt-1"><?= htmlspecialchars($document['occupation']) ?></div>
                          </div>
                        </div>
                      <?php endif; ?>
                      <?php if ($document['remarks']): ?>
                        <div class="col-md-4">
                          <div class="info-row">
                            <strong>Remarks:</strong>
                            <div class="mt-1"><?= htmlspecialchars($document['remarks']) ?></div>
                          </div>
                        </div>
                      <?php endif; ?>
                      <?php if ($document['lengthOfStay']): ?>
                        <div class="col-md-4">
                          <div class="info-row">
                            <strong>Length Of Stay:</strong>
                            <div class="mt-1">
                              <span><?= htmlspecialchars($document['lengthOfStay']) . ' ' . ((int)$document['lengthOfStay'] === 1 ? 'year' : 'years') ?></span>
                            </div>
                          </div>
                        </div>
                      <?php endif; ?>
                      <?php if ($document['residencyType']): ?>
                        <div class="col-md-4">
                          <div class="info-row">
                            <strong >Residency Type:</strong>
                            <div class="mt-1">
                              <span><?= htmlspecialchars($document['residencyType']) ?></span>
                            </div>
                          </div>
                        </div>
                      <?php endif; ?>
                      <?php if ($document['barangayName'] || $document['cityName']): ?>
                        <?php if ($documentTypeID == 2 || $documentTypeID == 7 || $documentTypeID == 9) { ?>
                          <div class="col-4">
                            <div class="info-row">
                              <strong>Address:</strong>
                              <div class="mt-1">
                                <?php
                                $addressParts = array_filter([
                                  ucwords(strtolower($document['houseNo'])),
                                  ucwords(strtolower($document['purok'])),
                                  ucwords(strtolower($document['subdivisionName'])),
                                  ucwords(strtolower($document['phase'])),
                                  ucwords(strtolower($document['streetName'])),
                                  ucwords(strtolower($document['barangayName'])),
                                  ucwords(strtolower($document['cityName'])),
                                  ucwords(strtolower($document['provinceName']))
                                ]);
                                echo htmlspecialchars(implode(', ', $addressParts));
                                ?>
                              </div>
                            </div>
                          </div>
                        <?php } else { ?>
                          <div class="col-6">
                            <div class="info-row">
                              <strong>Address:</strong>
                              <div class="mt-1">
                                <?php
                                $addressParts = array_filter([
                                  ucwords(strtolower($document['houseNo'])),
                                  ucwords(strtolower($document['purok'])),
                                  ucwords(strtolower($document['subdivisionName'])),
                                  ucwords(strtolower($document['phase'])),
                                  ucwords(strtolower($document['streetName'])),
                                  ucwords(strtolower($document['barangayName'])),
                                  ucwords(strtolower($document['cityName'])),
                                  ucwords(strtolower($document['provinceName']))
                                ]);
                                echo htmlspecialchars(implode(', ', $addressParts));
                                ?>
                              </div>
                            </div>
                          </div>
                        <?php } ?>
                      <?php endif; ?>
                      <?php if ($document['barangayName'] || $document['cityName']): ?>
                        <?php if ($documentTypeID == 2 || $documentTypeID == 7 || $documentTypeID == 9) { ?>
                          <div class="col-4">
                            <div class="info-row">
                              <strong>Permanent Address:</strong>
                              <div class="mt-1">
                                <?php
                                $addressParts = array_filter([
                                  ucwords(strtolower($document['permanentHouseNo'])),
                                  ucwords(strtolower($document['permanentPurok'])),
                                  ucwords(strtolower($document['permanentSubdivisionName'])),
                                  ucwords(strtolower($document['permanentPhase'])),
                                  ucwords(strtolower($document['permanentStreetName'])),
                                  ucwords(strtolower($document['permanentBarangayName'])),
                                  ucwords(strtolower($document['permanentCityName'])),
                                  ucwords(strtolower($document['permanentProvinceName']))
                                ]);
                                echo htmlspecialchars(implode(', ', $addressParts));
                                ?>
                              </div>
                            </div>
                          </div>
                        <?php } else { ?>
                          <div class="col-4">
                            <div class="info-row">
                              <strong>Permanent Address:</strong>
                              <div class="mt-1">
                                <?php
                                $addressParts = array_filter([
                                  ucwords(strtolower($document['permanentHouseNo'])),
                                  ucwords(strtolower($document['permanentPurok'])),
                                  ucwords(strtolower($document['permanentSubdivisionName'])),
                                  ucwords(strtolower($document['permanentPhase'])),
                                  ucwords(strtolower($document['permanentStreetName'])),
                                  ucwords(strtolower($document['permanentBarangayName'])),
                                  ucwords(strtolower($document['permanentCityName'])),
                                  ucwords(strtolower($document['permanentProvinceName']))
                                ]);
                                echo htmlspecialchars(implode(', ', $addressParts));
                                ?>
                              </div>
                            </div>
                          </div>
                        <?php } ?>
                      <?php endif; ?>
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
                <h5 class="card-title mb-0" style="color: black;">
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
                    'Cancelled' => 'bg-secondary',
                    'Archived' => 'bg-dark text-white',
                    default => 'bg-secondary'
                  };
                  ?>
                  <div class="mb-3 text-center">
                    <span class="badge <?= $badgeClass ?> status-badge">
                      <i class="fas fa-circle me-2"></i><?= $document['documentStatus'] ?>
                    </span>
                  </div>

                  <div class="mb-3">
                    <label class="form-label" style="color: black;"><strong>Change Status:</strong></label>
                    <select class="form-select" name="new_status" id="statusSelect">
                      <option value="Pending" <?= $document['documentStatus'] === 'Pending' ? 'selected' : '' ?>>Pending
                      </option>
                      <option value="Approved" <?= $document['documentStatus'] === 'Approved' ? 'selected' : '' ?>>Approved
                      </option>
                      <option value="Denied" <?= $document['documentStatus'] === 'Denied' ? 'selected' : '' ?>>Denied
                      </option>
                      <option value="Cancelled" <?= $document['documentStatus'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled
                      </option>
                      <option value="Archived" <?= $document['documentStatus'] === 'Archived' ? 'selected' : '' ?>>Archived
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
                  <?php elseif ($document['documentStatus'] === 'Cancelled' && $document['cancelledDate']): ?>
                    <div class="mt-3 pt-3 border-top text-center">
                      <small class="text-muted">
                        <i class="fas fa-times-circle me-1"></i>
                        Cancelled on <?= date('M d, Y', strtotime($document['cancelledDate'])) ?>
                      </small>
                    </div>
                  <?php elseif ($document['documentStatus'] === 'Denied' && $document['deniedDate']): ?>
                    <div class="mt-3 pt-3 border-top text-center">
                      <small class="text-muted">
                        <i class="fas fa-ban me-1"></i>
                        Denied on <?= date('M d, Y', strtotime($document['deniedDate'])) ?>
                      </small>
                    </div>
                  <?php elseif ($document['documentStatus'] === 'Archived' && $document['archiveDate']): ?>
                    <div class="mt-3 pt-3 border-top text-center">
                      <small class="text-muted">
                        <i class="fas fa-archive me-1"></i>
                        Archived on <?= date('M d, Y', strtotime($document['archiveDate'])) ?>
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
<?php

include_once __DIR__ . '/../../sharedAssets/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['documentID'])) {
  $documentID = $_POST['documentID'];
  $newStatus = $_POST['action'] === 'done' ? 'Approved' : 'Denied';

  $updateQuery = "UPDATE documents SET documentStatus = ? WHERE documentID = ?";
  $stmt = $pdo->prepare($updateQuery);
  $stmt->execute([$newStatus, $documentID]);

  echo "<script>window.location.href = 'index.php?page=document';</script>";
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_document'])) {
  $userIDString = $_POST['userID'];
  $userIDParts = explode('|', $userIDString);
  $userID = trim($userIDParts[0]);

  $documentTypeID = $_POST['documentTypeID'];

  // Initialize purpose variable
  $purpose = '';

  // Build purpose based on document type and additional fields
  switch ($documentTypeID) {
    case '2': // Business Clearance
      $businessName = $_POST['businessName'] ?? '';
      $businessAddress = $_POST['businessAddress'] ?? '';
      $ownerName = $_POST['ownerName'] ?? '';
      $ownerAddress = $_POST['ownerAddress'] ?? '';
      $businessNature = $_POST['businessNature'] ?? '';
      $controlNo = $_POST['controlNo'] ?? '';
      $businessPurpose = $_POST['purpose'] ?? '';
      $ownership = $_POST['ownership'] ?? '';

      $purpose = json_encode([
        'type' => 'Business Clearance',
        'businessName' => $businessName,
        'businessAddress' => $businessAddress,
        'ownerName' => $ownerName,
        'ownerAddress' => $ownerAddress,
        'businessNature' => $businessNature,
        'controlNo' => $controlNo,
        'purpose' => $businessPurpose,
        'ownership' => $ownership
      ]);
      break;

    case '1': // Barangay Clearance
    case '5':
    case '9':
      $purpose = $_POST['purpose'] ?? '';
      break;

    case '3': // Construction Clearance
      $purpose = $_POST['purpose'] ?? '';
      break;

    case '7': // Marriage Certificate
      $spouseName = $_POST['spouseName'] ?? '';
      $marriageYear = $_POST['marriageYear'] ?? '';

      $purpose = json_encode([
        'type' => 'Marriage Certificate',
        'spouseName' => $spouseName,
        'marriageYear' => $marriageYear
      ]);
      break;

    case '10': // Certificate of Number of Children
      $childNo = $_POST['childNo'] ?? '';

      $purpose = json_encode([
        'type' => 'Certificate of Number of Children',
        'numberOfChildren' => $childNo
      ]);
      break;

    default:
      $purpose = 'General Request';
      break;
  }

  $insertQuery = "INSERT INTO documents (documentTypeID, userID, purpose, documentStatus, requestDate) 
                  VALUES (?, ?, ?, 'Pending', NOW())";
  $stmt = $pdo->prepare($insertQuery);
  $stmt->execute([$documentTypeID, $userID, $purpose]);

  echo "<script>window.location.href = 'index.php?page=document';</script>";
  exit();
}

$search = $_GET['search'] ?? '';
$statusFilter = $_GET['status'] ?? '';
$dateFilter = $_GET['date'] ?? '';
$docTypeFilter = $_GET['doctype'] ?? '';
$currentPage = isset($_GET['pg']) ? (int) $_GET['pg'] : 1;
$recordsPerPage = 20;
$offset = ($currentPage - 1) * $recordsPerPage;

$documentsQuery = "SELECT 
    d.documentID, 
    d.purpose, 
    d.documentStatus, 
    d.requestDate,
    d.documentTypeID,
    dt.documentName,
    CONCAT(ui.firstName, ' ', ui.middleName, ' ', ui.lastName) AS fullname,
    u.phoneNumber
FROM documents d
JOIN users u ON d.userID = u.userID
JOIN userinfo ui ON u.userID = ui.userID
JOIN documenttypes dt ON d.documentTypeID = dt.documentTypeID
WHERE 1";

if ($search != '') {
  $documentsQuery .= " AND (
        CAST(d.documentID AS CHAR) LIKE '%$search%' OR 
        ui.firstName LIKE '%$search%' OR 
        ui.middleName LIKE '%$search%' OR 
        ui.lastName LIKE '%$search%' OR
        dt.documentName LIKE '%$search%'
    )";
}

if ($statusFilter != '' && $statusFilter != 'All') {
  $documentsQuery .= " AND d.documentStatus = '$statusFilter'";
}

if ($dateFilter != '') {
  $documentsQuery .= " AND DATE(d.requestDate) = '$dateFilter'";
}

if ($docTypeFilter != '' && $docTypeFilter != 'All') {
  $documentsQuery .= " AND d.documentTypeID = '$docTypeFilter'";
}

$countQuery = $documentsQuery;
$countResult = executeQuery($countQuery);
$totalRecords = $countResult->num_rows;
$totalPages = ceil($totalRecords / $recordsPerPage);

$documentsQuery .= " ORDER BY d.requestDate DESC LIMIT $recordsPerPage OFFSET $offset";

$documentsResults = executeQuery($documentsQuery);

$usersQuery = "SELECT * FROM users 
  LEFT JOIN userInfo ON users.userID = userInfo.userID 
  LEFT JOIN addresses ON userInfo.userID = addresses.userInfoID
  WHERE role = 'user'
  ORDER BY firstName";

$usersResults = executeQuery($usersQuery);

$blockLotNo = $phase = $subdivisionName = $purok = $streetName = $barangayName = $cityName = $provinceName = '';
if ($usersResults && mysqli_num_rows($usersResults) > 0) {
  $usersRow = mysqli_fetch_assoc($usersResults);
  $blockLotNo = $usersRow['blockLotNo'] ?? '';
  $phase = $usersRow['phase'] ?? '';
  $subdivisionName = $usersRow['subdivisionName'] ?? '';
  $purok = $usersRow['purok'] ?? '';
  $streetName = $usersRow['streetName'] ?? '';
  $barangayName = $usersRow['barangayName'] ?? '';
  $cityName = $usersRow['cityName'] ?? '';
  $provinceName = $usersRow['provinceName'] ?? '';
}
$docTypesQuery = "SELECT * FROM documenttypes ORDER BY documentName";
$docTypesResults = executeQuery($docTypesQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Document Requests</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />


  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8f9fa;
    }

    .card {
      box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
      border: none;
    }

    .btn-primary {
      background-color: #31afab;
      border-color: #31afab;
    }

    .btn-primary:hover {
      background-color: #2a9995;
      border-color: #2a9995;
    }

    .modal-header {
      background-color: #31afab;
      color: white;
    }

    .modal-header .btn-close {
      filter: invert(1);
    }

    .pagination .page-link {
      color: #31afab;
      background-color: white;
      border: 1px solid #dee2e6;
      transition: all 0.2s ease-in-out;
    }

    .pagination .page-item.active .page-link {
      background-color: #31afab;
      border-color: #31afab;
      color: white;
    }

    .pagination .page-link:hover {
      background-color: #e9f8f8;
      color: #31afab;
    }

    .btn-info {
      background-color: #31afab !important;
      border-color: #31afab !important;
    }

    .btn-info:hover {
      background-color: #2a9995 !important;
      border-color: #2a9995 !important;
    }
  </style>
</head>

<body>

  <div class="container-fluid p-3 p-md-4">
    <div class="card shadow border-0 rounded-3">
      <div class="card-body p-0">
        <div class="text-white p-4 rounded-top" style="background-color: #31afab;">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center">
              <i class="fas fa-file-alt me-3 fs-4"></i>
              <h1 class="h4 mb-0 fw-semibold">Document Requests Management</h1>
            </div>
            <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addDocumentModal">
              <i class="fas fa-plus me-2"></i>Add Document Request
            </button>
          </div>
        </div>

        <div class="p-3 p-md-4">
          <form method="GET" action="index.php">
            <input type="hidden" name="page" value="document">
            <div class="card shadow-sm mb-4">
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-lg-3 col-md-6">
                    <div class="input-group">
                      <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-search text-muted"></i>
                      </span>
                      <input type="text" class="form-control border-start-0" name="search"
                        placeholder="Search User or Document" value="<?= htmlspecialchars($search) ?>">
                    </div>
                  </div>

                  <div class="col-lg-2 col-md-6">
                    <select class="form-select" name="doctype">
                      <option value="All" <?= $docTypeFilter === '' || $docTypeFilter === 'All' ? 'selected' : '' ?>>All
                        Document Types</option>
                      <?php
                      mysqli_data_seek($docTypesResults, 0);
                      while ($docType = $docTypesResults->fetch_assoc()): ?>
                        <option value="<?= $docType['documentTypeID'] ?>" <?= $docTypeFilter == $docType['documentTypeID'] ? 'selected' : '' ?>>
                          <?= htmlspecialchars($docType['documentName']) ?>
                        </option>
                      <?php endwhile; ?>
                    </select>
                  </div>

                  <div class="col-lg-2 col-md-6">
                    <select class="form-select" name="status">
                      <option value="All" <?= $statusFilter === '' || $statusFilter === 'All' ? 'selected' : '' ?>>All Status
                      </option>
                      <?php foreach (['Pending', 'Approved', 'Denied'] as $status): ?>
                        <option value="<?= $status ?>" <?= $statusFilter === $status ? 'selected' : '' ?>><?= $status ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <div class="col-lg-3 col-md-6">
                    <input type="date" class="form-control" name="date" value="<?= htmlspecialchars($dateFilter) ?>">
                  </div>

                  <div class="col-lg-2 col-md-6">
                    <button class="btn btn-info text-white w-100" type="submit">
                      <i class="fas fa-filter me-2"></i>Filter
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </form>

          <div class="card shadow-sm">
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Date Submitted</th>
                      <th>Document Type</th>
                      <th>Requester Name</th>
                      <th>Purpose</th>
                      <th>Contact</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if ($documentsResults->num_rows > 0): ?>
                      <?php while ($row = $documentsResults->fetch_assoc()): ?>
                        <?php
                        $badgeClass = match ($row['documentStatus']) {
                          'Pending' => 'bg-warning text-dark',
                          'Approved' => 'bg-success',
                          'Denied' => 'bg-danger',
                          default => 'bg-secondary'
                        };
                        ?>
                        <tr>
                          <td><?= date('M d, Y', strtotime($row['requestDate'])) ?></td>
                          <td><span class="badge bg-info text-white"><?= htmlspecialchars($row['documentName']) ?></span>
                          </td>
                          <td><?= htmlspecialchars($row['fullname']) ?></td>
                          <td><?= htmlspecialchars($row['purpose']) ?></td>
                          <td><?= htmlspecialchars($row['phoneNumber']) ?></td>
                          <td><span class="badge rounded-pill <?= $badgeClass ?>"><?= $row['documentStatus'] ?></span></td>
                          <td>
                            <div class="d-flex gap-2 flex-wrap">
                              <a href="adminContent/viewDocument.php?id=<?= $row['documentID'] ?>"
                                class="btn btn-sm btn-outline-primary" title="View Document">
                                <i class="fas fa-eye"></i>
                              </a>

                              <?php if ($row['documentStatus'] === 'Approved'): ?>
                                <a href="adminContent/printDocument.php?documentID=<?= $row['documentID'] ?>"
                                  class="btn btn-sm btn-success" target="_blank" title="Print Document">
                                  <i class="fas fa-print"></i>
                                </a>
                              <?php else: ?>
                                <!-- Deny Button -->
                                <button class="btn btn-sm btn-danger" type="button" title="Deny" data-bs-toggle="modal"
                                  data-bs-target="#denyDocumentModal<?= $row['documentID'] ?>">
                                  <i class="fas fa-times"></i>
                                </button>

                                <div class="modal fade" id="denyDocumentModal<?= $row['documentID'] ?>" tabindex="-1">
                                  <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <h5 class="modal-title">Deny Document</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                      </div>
                                      <div class="modal-body">
                                        Are you sure you want to deny this document?
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                          data-bs-dismiss="modal">Cancel</button>

                                        <!-- Direct form submit -->
                                        <form method="POST" style="display:inline;">
                                          <input type="hidden" name="documentID" value="<?= $row['documentID'] ?>">
                                          <input type="hidden" name="action" value="deny">
                                          <button type="submit" class="btn btn-danger">Confirm Deny</button>
                                        </form>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              <?php endif; ?>
                            </div>
                          </td>
                        </tr>
                      <?php endwhile; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="7" class="text-center py-4">
                          <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                          <p class="text-muted">No document requests found.</p>
                        </td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>

            <?php if ($totalPages > 1): ?>
              <div class="card-footer bg-white">
                <div class="d-flex justify-content-between align-items-center">
                  <div class="text-muted small">
                    Showing <?= min($offset + 1, $totalRecords) ?> to <?= min($offset + $recordsPerPage, $totalRecords) ?>
                    of <?= $totalRecords ?> entries
                  </div>
                  <nav>
                    <ul class="pagination pagination-sm mb-0">
                      <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link"
                          href="?page=document&pg=<?= $currentPage - 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($statusFilter) ?>&date=<?= urlencode($dateFilter) ?>&doctype=<?= urlencode($docTypeFilter) ?>">
                          <i class="fas fa-chevron-left"></i> Back
                        </a>
                      </li>

                      <?php
                      $startPage = max(1, $currentPage - 2);
                      $endPage = min($totalPages, $currentPage + 2);

                      if ($startPage > 1): ?>
                        <li class="page-item">
                          <a class="page-link"
                            href="?page=document&pg=1&search=<?= urlencode($search) ?>&status=<?= urlencode($statusFilter) ?>&date=<?= urlencode($dateFilter) ?>&doctype=<?= urlencode($docTypeFilter) ?>">1</a>
                        </li>
                        <?php if ($startPage > 2): ?>
                          <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                      <?php endif; ?>

                      <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                          <a class="page-link"
                            href="?page=document&pg=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($statusFilter) ?>&date=<?= urlencode($dateFilter) ?>&doctype=<?= urlencode($docTypeFilter) ?>"><?= $i ?></a>
                        </li>
                      <?php endfor; ?>

                      <?php if ($endPage < $totalPages): ?>
                        <?php if ($endPage < $totalPages - 1): ?>
                          <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                        <li class="page-item">
                          <a class="page-link"
                            href="?page=document&pg=<?= $totalPages ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($statusFilter) ?>&date=<?= urlencode($dateFilter) ?>&doctype=<?= urlencode($docTypeFilter) ?>"><?= $totalPages ?></a>
                        </li>
                      <?php endif; ?>

                      <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link"
                          href="?page=document&pg=<?= $currentPage + 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($statusFilter) ?>&date=<?= urlencode($dateFilter) ?>&doctype=<?= urlencode($docTypeFilter) ?>">
                          Next <i class="fas fa-chevron-right"></i>
                        </a>
                      </li>
                    </ul>
                  </nav>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="addDocumentModal" tabindex="-1" aria-labelledby="addDocumentLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add Document Request</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST">
          <div class="modal-body">
            <div class="row g-3">

              <div class="col-12">
                <label class="form-label">Select User</label>
                <input class="form-control" list="userList" id="userSelect" name="userID"
                  placeholder="Type name, email, or birthdate..." required>

                <datalist id="userList">
                  <?php
                  mysqli_data_seek($usersResults, 0);
                  while ($user = $usersResults->fetch_assoc()):
                    $fullname = htmlspecialchars(trim("{$user['firstName']} {$user['middleName']} {$user['lastName']}"));
                    $email = htmlspecialchars($user['email']);
                    $birthDate = htmlspecialchars($user['birthDate']);
                    ?>
                    <?php
                    $ownerAddress = trim("{$user['blockLotNo']} {$user['streetName']} {$user['barangayName']} {$user['cityName']} {$user['provinceName']}"); ?>
                    <option
                      value="<?= $user['userID'] ?> | <?= $fullname ?> | <?= $email ?> | <?= $birthDate ?> | <?= $ownerAddress ?>">
                    </option>
                  <?php endwhile; ?>
                </datalist>
              </div>

              <div class="col-12">
                <div class="border rounded p-3 bg-light" id="userInfoBox" style="display:none;">
                  <p><strong>Name:</strong> <span id="infoName"></span></p>
                  <p><strong>Email:</strong> <span id="infoEmail"></span></p>
                  <p><strong>Birthdate:</strong> <span id="infoBirthdate"></span></p>
                </div>
              </div>

              <div class="col-12">
                <label class="form-label">Document Type</label>
                <select class="form-select" name="documentTypeID" required>
                  <option value="">Choose document type...</option>
                  <?php
                  mysqli_data_seek($docTypesResults, 0);
                  while ($docType = $docTypesResults->fetch_assoc()): ?>
                    <option value="<?= $docType['documentTypeID'] ?>"><?= htmlspecialchars($docType['documentName']) ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>

              <!-- Business Clearance (documentTypeID = 2) -->
              <div class="document-form-section" data-doc-type="business" style="display: none;">
                <p class="note mb-3">Please fill out your business details and choose the purpose of your request:</p>

                <div class="form-floating mb-3">
                  <input type="text" class="form-control" id="businessName" name="businessName"
                    placeholder="Business Name" required>
                  <label for="businessName">Business Name</label>
                </div>

                <div class="form-floating mb-3">
                  <input type="text" class="form-control" id="businessAddress" name="businessAddress"
                    placeholder="Business Address" required>
                  <label for="businessAddress">Business Address</label>
                </div>

                <div class="form-floating mb-3">
                  <input type="text" class="form-control" id="fullname" name="ownerName" placeholder="Owner's Name"
                    required readonly>
                  <label for="ownerName">Owner's Name</label>
                </div>

                <div class="form-floating mb-3">
                  <input type="text" class="form-control" id="ownerAddress" name="ownerAddress"
                    placeholder="Owner's Address" readonly required>
                  <label for="ownerAddress">Owner's Address</label>
                </div>

                <div class="form-floating mb-3">
                  <select class="form-select" id="businessNature" name="businessNature" required>
                    <option value="" selected disabled>Choose Nature of Business</option>
                    <option value="Sari-Sari Store">Sari-Sari Store</option>
                    <option value="Food & Beverage">Food & Beverage</option>
                    <option value="Retail">Retail</option>
                    <option value="Services">Services</option>
                    <option value="Agriculture">Agriculture</option>
                    <option value="Manufacturing">Manufacturing</option>
                    <option value="Transportation">Transportation</option>
                    <option value="Others">Others (Specify)</option>
                  </select>
                  <label for="businessNature">Nature of Business</label>
                </div>

                <div class="form-floating mb-3">
                  <input type="number" class="form-control" id="controlNo" name="controlNo" placeholder="Control No."
                    min="0" onkeydown="return !['e','E','-','+','.',','].includes(event.key)">
                  <label for="controlNo">Control No.</label>
                </div>

                <div class="form-floating mb-3">
                  <select class="form-select" id="businessClearancePurpose" name="purpose" required>
                    <option value="" selected disabled>Choose Purpose</option>
                    <option value="New">New</option>
                    <option value="Renewal">Renewal</option>
                    <option value="Closure">Closure</option>
                    <option value="Expansion">Expansion</option>
                  </select>
                  <label for="businessClearancePurpose">Purpose</label>
                </div>

                <div class="form-floating">
                  <select class="form-select" id="ownership" name="ownership" required>
                    <option value="" selected disabled>Choose Ownership</option>
                    <option value="Sole Proprietorship">Sole Proprietorship</option>
                    <option value="Partnership">Partnership</option>
                    <option value="Corporation">Corporation</option>
                    <option value="Cooperative">Cooperative</option>
                  </select>
                  <label for="ownership">Ownership</label>
                </div>
              </div>

              <!-- Barangay Clearance (documentTypeID = 1, 5, 9) -->
              <div class="document-form-section" data-doc-type="clearance" style="display: none;">
                <p class="note mb-3">Please select the purpose for your request:</p>

                <div class="form-floating">
                  <select class="form-select selectPurpose" id="purpose" name="purpose" required>
                    <option value="" selected disabled>Choose Purpose</option>
                    <option value="Employment">Employment</option>
                    <option value="Job Requirement / Local Employment">Job Requirement / Local Employment</option>
                    <option value="Overseas Employment (OFW)">Overseas Employment (OFW)</option>
                    <option value="School Requirement / Enrollment">School Requirement / Enrollment</option>
                    <option value="Scholarship Application">Scholarship Application</option>
                    <option value="Medical Assistance">Medical Assistance</option>
                    <option value="Hospital Requirement">Hospital Requirement</option>
                    <option value="Legal Requirement / Court Use">Legal Requirement / Court Use</option>
                    <option value="NBI / Police Clearance">NBI / Police Clearance</option>
                    <option value="Passport Application / Renewal">Passport Application / Renewal</option>
                    <option value="Driver's License">Driver's License</option>
                    <option value="Loan Application">Loan Application</option>
                  </select>
                  <label for="purpose">Purpose</label>
                </div>
              </div>

              <!-- Construction Clearance (documentTypeID = 3) -->
              <div class="document-form-section" data-doc-type="construction" style="display: none;">
                <p class="note mb-3">Please select the purpose for your request:</p>

                <div class="form-floating">
                  <select class="form-select" id="constructionClearancePurpose" name="purpose" required>
                    <option value="" selected disabled>Choose Purpose</option>
                    <option value="New Construction">New Construction</option>
                    <option value="House Renovation">House Renovation</option>
                    <option value="Extension / Expansion">Extension / Expansion</option>
                    <option value="Fence Construction">Fence Construction</option>
                    <option value="Demolition">Demolition</option>
                    <option value="Repair / Maintenance">Repair / Maintenance</option>
                  </select>
                  <label for="constructionClearancePurpose">Purpose</label>
                </div>
              </div>

              <!-- Marriage Certificate (documentTypeID = 7) -->
              <div class="document-form-section" data-doc-type="marriage" style="display: none;">
                <p class="note mb-3">Please fill out your marriage details below.</p>

                <div class="form-floating mb-3">
                  <input type="text" class="form-control" id="spouseName" name="spouseName" placeholder="Spouse Name"
                    required>
                  <label for="spouseName">Spouse Name</label>
                </div>

                <div class="form-floating">
                  <input type="number" class="form-control" id="marriageYear" name="marriageYear"
                    placeholder="Year of Marriage (e.g., 2003)" min="1900" max="<?php echo date('Y'); ?>"
                    oninput="if(this.value.length > 4) this.value = this.value.slice(0, 4);"
                    onkeydown="return !['e','E','-','+','.',','].includes(event.key)" required>
                  <label for="marriageYear">Year of Marriage (e.g., 2003)</label>
                </div>
              </div>

              <!-- Certificate of No. of Children (documentTypeID = 10) -->
              <div class="document-form-section" data-doc-type="children" style="display: none;">
                <p class="note mb-3">Please enter the number of children you have.</p>

                <div class="form-floating">
                  <input type="number" class="form-control" id="childNo" name="childNo"
                    placeholder="Number of Children (e.g., 2)" min="0"
                    oninput="if(this.value.length > 2) this.value = this.value.slice(0, 2);"
                    onkeydown="return !['e','E','-','+','.',','].includes(event.key)" required>
                  <label for="childNo">Number of Children (e.g., 2)</label>
                </div>
              </div>

              <div class="document-form-section" data-doc-type="default" style="display: none;">
                <p class="note">No additional details are required for this request.</p>
                <small class="text-muted">Click proceed for the confirmation.</small>
              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="add_document" class="btn btn-primary">
                  <i class="fas fa-save me-2"></i>Create Request
                </button>
              </div>
        </form>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"
    integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y"
    crossorigin="anonymous"></script>
  <script>


    document.addEventListener('DOMContentLoaded', function () {
      const docTypeSelect = document.querySelector('select[name="documentTypeID"]');
      const formSections = document.querySelectorAll('.document-form-section');

      function updateFormSections() {
        const selectedType = docTypeSelect.value;

        // Hide all sections and disable their required fields
        formSections.forEach(section => {
          section.style.display = 'none';
          const inputs = section.querySelectorAll('input, select, textarea');
          inputs.forEach(input => {
            input.removeAttribute('required');
            input.disabled = true;
          });
        });

        let activeSection = null;

        // Business Clearance (ID = 2)
        if (selectedType === '2') {
          activeSection = document.querySelector('[data-doc-type="business"]');
        }
        // Barangay Clearance (IDs = 1, 5, 9)
        else if (['1', '5', '9'].includes(selectedType)) {
          activeSection = document.querySelector('[data-doc-type="clearance"]');
        }
        // Construction Clearance (ID = 3)
        else if (selectedType === '3') {
          activeSection = document.querySelector('[data-doc-type="construction"]');
        }
        // Marriage Certificate (ID = 7)
        else if (selectedType === '7') {
          activeSection = document.querySelector('[data-doc-type="marriage"]');
        }
        // Certificate of Number of Children (ID = 10)
        else if (selectedType === '10') {
          activeSection = document.querySelector('[data-doc-type="children"]');
        }
        // Default case
        else if (selectedType) {
          activeSection = document.querySelector('[data-doc-type="default"]');
        }

        // Show active section and enable its required fields
        if (activeSection) {
          activeSection.style.display = 'block';
          const inputs = activeSection.querySelectorAll('input, select, textarea');
          inputs.forEach(input => {
            if (input.hasAttribute('data-required') || input.closest('.form-floating, .form-group')) {
              input.setAttribute('required', 'required');
            }
            input.disabled = false;
          });
        }
      }

      docTypeSelect.addEventListener('change', updateFormSections);

      // User select handler
      document.getElementById('userSelect').addEventListener('input', function () {
        const value = this.value;
        const parts = value.split("|");

        if (parts.length >= 5) {
          document.getElementById('infoName').textContent = parts[1].trim();
          document.getElementById('infoEmail').textContent = parts[2].trim();
          document.getElementById('infoBirthdate').textContent = parts[3].trim();

          const ownerAddress = parts[4].trim();
          const ownerAddressField = document.getElementById('ownerAddress');
          if (ownerAddressField) ownerAddressField.value = ownerAddress;

          const fullNameField = document.getElementById('fullname');
          if (fullNameField) fullNameField.value = parts[1].trim();

          document.getElementById('userInfoBox').style.display = 'block';
        } else {
          document.getElementById('userInfoBox').style.display = 'none';
        }
      });
    });
  </script>
</body>

</html>
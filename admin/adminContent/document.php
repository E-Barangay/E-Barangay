<?php
include_once __DIR__ . '/../../sharedAssets/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['documentID'])) {
  $documentID = $_POST['documentID'];
  $newStatus = $_POST['action'] === 'done' ? 'Approved' : 'Denied';

  $updateQuery = "UPDATE documents SET documentStatus = ? WHERE documentID = ?";
  $stmt = $pdo->prepare($updateQuery);
  $stmt->execute([$newStatus, $documentID]);

  header("Location: index.php?page=document");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_document'])) {
  $userID = $_POST['userID'];
  $documentTypeID = $_POST['documentTypeID'];
  $purpose = $_POST['purpose'];

  $insertQuery = "INSERT INTO documents (documentTypeID, userID, purpose, documentStatus, requestDate) 
                  VALUES (?, ?, ?, 'Pending', NOW())";
  $stmt = $pdo->prepare($insertQuery);
  $stmt->execute([$documentTypeID, $userID, $purpose]);

  header("Location: index.php?page=document");
  exit();
}

$search = $_GET['search'] ?? '';
$statusFilter = $_GET['status'] ?? '';
$dateFilter = $_GET['date'] ?? '';
$docTypeFilter = $_GET['doctype'] ?? '';
$currentPage = isset($_GET['pg']) ? (int)$_GET['pg'] : 1;
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

$usersQuery = "SELECT u.userID, ui.birthdate, 
       CONCAT(ui.firstName, ' ', ui.middleName, ' ', ui.lastName) AS fullname, 
       u.email
FROM users u 
JOIN userinfo ui ON u.userID = ui.userID 
WHERE u.role = 'user'
ORDER BY ui.firstName";
$usersResults = executeQuery($usersQuery);

$docTypesQuery = "SELECT * FROM documenttypes ORDER BY documentName";
$docTypesResults = executeQuery($docTypesQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Document Requests</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                      <option value="All" <?= $docTypeFilter === '' || $docTypeFilter === 'All' ? 'selected' : '' ?>>All Document Types</option>
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
                      <option value="All" <?= $statusFilter === '' || $statusFilter === 'All' ? 'selected' : '' ?>>All Status</option>
                      <?php foreach (['Pending', 'Approved', 'Denied'] as $status): ?>
                        <option value="<?= $status ?>" <?= $statusFilter === $status ? 'selected' : '' ?>><?= $status ?></option>
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
                          <td><span class="badge bg-info text-white"><?= htmlspecialchars($row['documentName']) ?></span></td>
                          <td><?= htmlspecialchars($row['fullname']) ?></td>
                          <td><?= htmlspecialchars($row['purpose']) ?></td>
                          <td><?= htmlspecialchars($row['phoneNumber']) ?></td>
                          <td><span class="badge rounded-pill <?= $badgeClass ?>"><?= $row['documentStatus'] ?></span></td>
                          <td>
                            <div class="d-flex gap-2 flex-wrap">
                              <a href="adminContent/viewDocument.php?id=<?= $row['documentID'] ?>" class="btn btn-sm btn-outline-primary" title="View Document">
                                <i class="fas fa-eye"></i>
                              </a>

                              <?php if ($row['documentStatus'] === 'Approved'): ?>
  <a href="adminContent/printDocument.php?documentID=<?= $row['documentID'] ?>"
     class="btn btn-sm btn-success"
     target="_blank"
     title="Print Document">
     <i class="fas fa-print"></i>
  </a>
                              <?php else: ?>
                                <form method="POST" style="display:inline;">
                                  <input type="hidden" name="documentID" value="<?= $row['documentID'] ?>">
                                  <input type="hidden" name="action" value="deny">
                                  <button class="btn btn-sm btn-danger" type="submit" title="Deny"
                                    onclick="return confirm('Are you sure you want to deny this request?')">
                                    <i class="fas fa-times"></i>
                                  </button>
                                </form>
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
                  Showing <?= min($offset + 1, $totalRecords) ?> to <?= min($offset + $recordsPerPage, $totalRecords) ?> of <?= $totalRecords ?> entries
                </div>
                <nav>
                  <ul class="pagination pagination-sm mb-0">
                    <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                      <a class="page-link" href="?page=document&pg=<?= $currentPage - 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($statusFilter) ?>&date=<?= urlencode($dateFilter) ?>&doctype=<?= urlencode($docTypeFilter) ?>">
                        <i class="fas fa-chevron-left"></i> Back
                      </a>
                    </li>

                    <?php
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($totalPages, $currentPage + 2);

                    if ($startPage > 1): ?>
                      <li class="page-item">
                        <a class="page-link" href="?page=document&pg=1&search=<?= urlencode($search) ?>&status=<?= urlencode($statusFilter) ?>&date=<?= urlencode($dateFilter) ?>&doctype=<?= urlencode($docTypeFilter) ?>">1</a>
                      </li>
                      <?php if ($startPage > 2): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                      <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                      <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                        <a class="page-link" href="?page=document&pg=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($statusFilter) ?>&date=<?= urlencode($dateFilter) ?>&doctype=<?= urlencode($docTypeFilter) ?>"><?= $i ?></a>
                      </li>
                    <?php endfor; ?>

                    <?php if ($endPage < $totalPages): ?>
                      <?php if ($endPage < $totalPages - 1): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                      <?php endif; ?>
                      <li class="page-item">
                        <a class="page-link" href="?page=document&pg=<?= $totalPages ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($statusFilter) ?>&date=<?= urlencode($dateFilter) ?>&doctype=<?= urlencode($docTypeFilter) ?>"><?= $totalPages ?></a>
                      </li>
                    <?php endif; ?>

                    <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                      <a class="page-link" href="?page=document&pg=<?= $currentPage + 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($statusFilter) ?>&date=<?= urlencode($dateFilter) ?>&doctype=<?= urlencode($docTypeFilter) ?>">
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

  <!-- Modal on Add -->
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
              <input class="form-control" list="userList" id="userSelect" name="userID" placeholder="Type name, email, or birthdate..." required>
              <datalist id="userList">
                <?php
                mysqli_data_seek($usersResults, 0);
                while ($user = $usersResults->fetch_assoc()):
                  $userInfo = htmlspecialchars(json_encode([
                    "fullname" => $user['fullname'],
                    "email" => $user['email'],
                    "birthdate" => $user['birthdate'] ?? 'N/A'
                  ]));
                ?>
                  <option value="<?= $user['userID'] ?> | <?= htmlspecialchars($user['fullname']) ?> | <?= htmlspecialchars($user['email']) ?> | <?= $user['birthdate'] ?>">
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
                  <option value="<?= $docType['documentTypeID'] ?>"><?= htmlspecialchars($docType['documentName']) ?></option>
                <?php endwhile; ?>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label">Purpose</label>
              <textarea class="form-control" name="purpose" rows="3" placeholder="Enter the purpose..." required></textarea>
            </div>
          </div>
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.getElementById('userSelect').addEventListener('input', function () {
    const value = this.value;
    const parts = value.split("|");

    if (parts.length === 4) {
      document.getElementById('infoName').textContent = parts[1].trim();
      document.getElementById('infoEmail').textContent = parts[2].trim();
      document.getElementById('infoBirthdate').textContent = parts[3].trim();
      document.getElementById('userInfoBox').style.display = 'block';
    } else {
      document.getElementById('userInfoBox').style.display = 'none';
    }
  });
</script>
</body>
</html>
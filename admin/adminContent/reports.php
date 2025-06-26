<?php
// adminContent/concerns.php
include_once __DIR__ . '/../../sharedAssets/connect.php';

// GET filters
$search = trim($_GET['search'] ?? '');
$statusFilter = $_GET['status'] ?? '';
$dateFilter = $_GET['date'] ?? '';

// Build query
$sql = "SELECT 
    r.reportID as concernID,
    r.requestDate,
    CONCAT(ui.firstName, ' ', ui.middleName, ' ', ui.lastName) AS reporterName,
    r.reportTitle AS concernType,
    u.phoneNumber,
    r.requestStatus as status
  FROM reports r
  JOIN users u ON r.userID = u.userID
  JOIN userinfo ui ON u.userID = ui.userID
  WHERE 1";

$params = [];
$types = '';

if ($search !== '') {
  $sql .= " AND (
    CONCAT(ui.firstName, ' ', ui.lastName) LIKE ?
    OR CAST(r.reportID AS CHAR) LIKE ?
    OR r.reportTitle LIKE ?
  )";
  $searchTerm = "%{$search}%";
  $params[] = $searchTerm;
  $params[] = $searchTerm;
  $params[] = $searchTerm;
  $types .= 'sss';
}

if ($statusFilter !== '' && $statusFilter !== 'All') {
  $sql .= " AND r.requestStatus = ?";
  $params[] = $statusFilter;
  $types .= 's';
}

if ($dateFilter !== '') {
  $sql .= " AND DATE(r.requestDate) = ?";
  $params[] = $dateFilter;
  $types .= 's';
}

$sql .= " ORDER BY r.requestDate DESC";

$stmt = $conn->prepare($sql);
if ($params) {
  $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

function getStatusBadgeClass($status) {
  return match ($status) {
    'Closed' => 'bg-secondary text-white',
    'Resolved' => 'bg-success text-white',
    'In Progress' => 'bg-primary text-white',
    default => 'bg-warning text-dark',
  };
}

function getBorderClass($status) {
  return match ($status) {
    'Closed' => 'border-secondary',
    'Resolved' => 'border-success',
    'In Progress' => 'border-primary',
    default => 'border-warning',
  };
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<<<<<<< Updated upstream
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Concerns Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: rgb(233, 233, 233);
            color: dark;
            height: 100vh;
            margin: 0;
            padding: 0;
        }
    </style>
=======
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Community Concerns Management</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', sans-serif; background: #e9e9e9; }
    .table-hover tbody tr:hover { background-color: rgba(0,0,0,.075); }
    .badge { font-size: 0.75em; }
  </style>
>>>>>>> Stashed changes
</head>

<body>
<<<<<<< Updated upstream
    <div class="container-fluid p-3 p-md-4">
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-body p-0">
                <div class="text-white p-4 rounded-top" style="background-color: rgb(49, 175, 171);">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-users-cog me-3 fs-4"></i>
                        <h1 class="h4 mb-0 fw-semibold">Community Concerns Management</h1>
                    </div>
                </div>

                <div class="p-3 p-md-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-lg-4 col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="fas fa-search text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0" placeholder="Search User"
                                            id="searchUser">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <select class="form-select" id="statusFilter">
                                        <option value="">All Status</option>
                                        <option value="Pending">Pending</option>
                                        <option value="In Progress">In Progress</option>
                                        <option value="Resolved">Resolved</option>
                                        <option value="Closed">Closed</option>
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <input type="date" class="form-control" id="datePicker">
                                </div>
                                <div class="col-lg-2 col-md-6">
                                    <button class="btn btn-info text-white w-100">
                                        <i class="fas fa-filter me-2"></i>Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-body p-0">
                            <div class="d-none d-lg-block">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="px-4 py-3 fw-semibold">Report ID</th>
                                                <th class="px-4 py-3 fw-semibold">Date Submitted</th>
                                                <th class="px-4 py-3 fw-semibold">Reporter Name</th>
                                                <th class="px-4 py-3 fw-semibold">Concern Type</th>
                                                <th class="px-4 py-3 fw-semibold">Contact</th>
                                                <th class="px-4 py-3 fw-semibold">Status</th>
                                                <th class="px-4 py-3 fw-semibold">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="reportTableBody">
                                            <?php
                                            if (mysqli_num_rows($result) > 0) {
                                                while ($resultRow = mysqli_fetch_assoc($result)) {

                                                    ?>
                                                    <tr>
                                                        <td class="px-4 py-3"><strong><?php echo $resultRow['reportID']?></strong></td>
                                                        <td class="px-4 py-3"><?php echo $resultRow['requestDate']?></td>
                                                        <td class="px-4 py-3"><?php echo $resultRow['fullName']; ?></td>
                                                        <td class="px-4 py-3">
                                                            <span class="badge rounded-pill bg-info"><?php echo $resultRow['reportTitle']?></span>
                                                        </td>
                                                        <td class="px-4 py-3"><?php echo $resultRow['phoneNumber']?></td>
                                                        <td class="px-4 py-3">
                                                            <span class="badge rounded-pill bg-warning text-dark">Pending</span>
                                                        </td>
                                                        <td class="px-4 py-3">
                                                            <button class="btn btn-sm btn-primary" onclick="viewReport(1)">
                                                                <i class="fas fa-eye me-1"></i>View
                                                            </button>
                                                        </td>
                                                    </tr>

                                                    <?php

                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="d-lg-none">
                                <div style="max-height: 70vh; overflow-y: auto;" class="p-3">
                                    <div class="card mb-3 border-start border-warning border-4">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0 fw-bold">Report ID: 1</h6>
                                                <span class="badge rounded-pill bg-warning text-dark">Pending</span>
                                            </div>
                                            <div class="row g-2 small">
                                                <div class="col-6"><strong>Date:</strong> 2025-01-01</div>
                                                <div class="col-6"><strong>Contact:</strong> 205-001-01</div>
                                                <div class="col-12"><strong>Name:</strong> John Doe</div>
                                                <div class="col-12">
                                                    <strong>Concern:</strong>
                                                    <span class="badge rounded-pill bg-info ms-1">Noise Complaint</span>
                                                </div>
                                                <div class="col-12 mt-2">
                                                    <button class="btn btn-sm btn-primary w-100"
                                                        onclick="viewReport(1)">
                                                        <i class="fas fa-eye me-1"></i>View Details
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card mb-3 border-start border-success border-4">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0 fw-bold">Report ID: 2</h6>
                                                <span class="badge rounded-pill bg-success">In Progress</span>
                                            </div>
                                            <div class="row g-2 small">
                                                <div class="col-6"><strong>Date:</strong> 2025-01-02</div>
                                                <div class="col-6"><strong>Contact:</strong> 205-002-02</div>
                                                <div class="col-12"><strong>Name:</strong> Jane Smith</div>
                                                <div class="col-12">
                                                    <strong>Concern:</strong>
                                                    <span class="badge rounded-pill bg-danger ms-1">Sanitation</span>
                                                </div>
                                                <div class="col-12 mt-2">
                                                    <button class="btn btn-sm btn-primary w-100"
                                                        onclick="viewReport(2)">
                                                        <i class="fas fa-eye me-1"></i>View Details
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-light">
                            <nav class="d-flex justify-content-center">
                                <ul class="pagination pagination-sm mb-0">
                                    <li class="page-item disabled">
                                        <a class="page-link"><i class="fas fa-chevron-left"></i></a>
                                    </li>
                                    <li class="page-item active">
                                        <a class="page-link" href="#">1</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link"><i class="fas fa-chevron-right"></i></a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
=======
<div class="container-fluid p-3 p-md-4">
  <div class="card shadow-lg border-0 rounded-3">
    <div class="card-body p-0">
      <div class="text-white p-4 rounded-top bg-info">
        <div class="d-flex align-items-center">
          <i class="fas fa-users-cog me-3 fs-4"></i>
          <h1 class="h4 mb-0 fw-semibold">Community Concerns Management</h1>
>>>>>>> Stashed changes
        </div>
      </div>
      <div class="p-3 p-md-4">

        <!-- Filter Form -->
<form method="GET" action="index.php" class="mb-4">
  <input type="hidden" name="page" value="reports">
  <div class="row g-3">
    <div class="col-md-4">
      <div class="input-group">
        <span class="input-group-text bg-white border-end-0">
          <i class="fas fa-search text-muted"></i>
        </span>
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control border-start-0" placeholder="Search Reporter, ID, or Title">
      </div>
    </div>
<<<<<<< Updated upstream

    <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-0 rounded-3 overflow-hidden">
                <div class="modal-header bg-primary text-white border-0 p-3">
                    <h5 class="modal-title fw-semibold fs-5" id="reportModalLabel">
                        <i class="fas fa-file-alt me-2"></i>
                        Report Details - ID: <span id="modalReportId">1</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-lg-8 col-12 mb-3 mb-lg-0">
                            <div class="card border rounded-3 h-100">
                                <div class="card-header bg-light border-bottom p-3">
                                    <h6 class="mb-0 fw-semibold">
                                        <i class="fas fa-clipboard-list me-2 text-primary"></i>
                                        Report Information
                                    </h6>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row mb-3">
                                        <div class="col-sm-4 mb-2 mb-sm-0 fw-semibold">Reporter Name:</div>
                                        <div class="col-sm-8" id="modalReporterName">John Doe</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4 mb-2 mb-sm-0 fw-semibold">Contact:</div>
                                        <div class="col-sm-8" id="modalContact">205-001-01</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4 mb-2 mb-sm-0 fw-semibold">Date Submitted:</div>
                                        <div class="col-sm-8" id="modalDateSubmitted">2025-01-01</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4 mb-2 mb-sm-0 fw-semibold">Concern Type:</div>
                                        <div class="col-sm-8">
                                            <span class="badge rounded-pill bg-info" id="modalConcernType">Noise
                                                Complaint</span>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4 mb-2 mb-sm-0 fw-semibold">Location:</div>
                                        <div class="col-sm-8" id="modalLocation">Barangay San Miguel, Majayjay, Laguna
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <div class="fw-semibold mb-2">Description:</div>
                                            <div class="p-3 bg-light rounded-3 border-start border-primary border-4">
                                                <p class="mb-0" id="modalDescription">
                                                    There is a persistent noise issue in our neighborhood coming from a
                                                    construction site that operates during late night hours. This has
                                                    been disturbing the peace and affecting the sleep of residents,
                                                    especially children and elderly members of our community.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <div class="fw-semibold mb-2">Location Map:</div>
                                            <div class="bg-light rounded-3 d-flex align-items-center justify-content-center border"
                                                style="height: 250px;">
                                                <div class="text-center text-muted">
                                                    <i class="fas fa-map-marker-alt fs-4 mb-2"></i>
                                                    <p class="mb-1">Interactive Map will be embedded here</p>
                                                    <small>Lat: 14.1480, Lng: 121.4584</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="fw-semibold mb-2">Attached Images:</div>
                                            <div class="row g-3">
                                                <div class="col-md-4 col-6">
                                                    <div class="bg-light rounded-3 d-flex align-items-center justify-content-center border"
                                                        style="height: 150px; cursor: pointer;"
                                                        onclick="openImageModal('image1.jpg')">
                                                        <div class="text-center text-muted">
                                                            <i class="fas fa-image fs-4 mb-2"></i>
                                                            <p class="mb-0 small">Image 1</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-6">
                                                    <div class="bg-light rounded-3 d-flex align-items-center justify-content-center border"
                                                        style="height: 150px; cursor: pointer;"
                                                        onclick="openImageModal('image2.jpg')">
                                                        <div class="text-center text-muted">
                                                            <i class="fas fa-image fs-4 mb-2"></i>
                                                            <p class="mb-0 small">Image 2</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-12">
                            <div class="card border rounded-3 h-100">
                                <div class="card-header bg-light border-bottom p-3">
                                    <h6 class="mb-0 fw-semibold">
                                        <i class="fas fa-cogs me-2 text-primary"></i>
                                        Manage Status
                                    </h6>
                                </div>
                                <div class="card-body p-3">
                                    <div class="mb-3">
                                        <label class="fw-semibold mb-2">Current Status:</label>
                                        <div>
                                            <span class="badge rounded-pill bg-warning text-dark"
                                                id="modalCurrentStatus">Pending</span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="statusSelect" class="fw-semibold mb-2">Update Status:</label>
                                        <select class="form-select" id="statusSelect">
                                            <option value="Pending">Pending</option>
                                            <option value="In Progress">In Progress</option>
                                            <option value="Under Review">Under Review</option>
                                            <option value="Resolved">Resolved</option>
                                            <option value="Closed">Closed</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="statusNotes" class="fw-semibold mb-2">Status Notes:</label>
                                        <textarea class="form-control" id="statusNotes" rows="4"
                                            placeholder="Add notes about status update..."></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <button class="btn btn-success w-100" onclick="updateStatus()">
                                            <i class="fas fa-save me-2"></i>
                                            Update Status
                                        </button>
                                    </div>

                                    <hr class="my-3">

                                    <div>
                                        <h6 class="fw-semibold mb-3">
                                            <i class="fas fa-history me-2 text-primary"></i>
                                            Status History
                                        </h6>
                                        <div class="timeline" style="max-height: 200px; overflow-y: auto;">
                                            <div
                                                class="p-3 bg-light rounded-3 mb-2 border-start border-primary border-3">
                                                <div class="small fw-semibold">Pending</div>
                                                <div class="small text-muted">2025-01-01 10:30 AM</div>
                                                <div class="small text-muted mt-1">Report submitted by John Doe</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Close
                    </button>
                    <button type="button" class="btn btn-danger" onclick="exportToHTML()">
                        <i class="fas fa-print me-2"></i>Export to HTML
                    </button>
                </div>
=======
            <div class="col-md-3">
              <select name="status" class="form-select">
                <option value="All" <?= ($statusFilter === '' || $statusFilter === 'All') ? 'selected' : '' ?>>All Status</option>
                <?php foreach (['Pending','In Progress','Resolved','Closed'] as $st): ?>
                  <option value="<?= $st ?>" <?= $statusFilter === $st ? 'selected' : '' ?>><?= $st ?></option>
                <?php endforeach; ?>
              </select>
>>>>>>> Stashed changes
            </div>
            <div class="col-md-3">
              <input type="date" name="date" value="<?= htmlspecialchars($dateFilter) ?>" class="form-control">
            </div>
            <div class="col-md-2">
              <button type="submit" class="btn btn-info w-100">
                <i class="fas fa-filter me-2"></i>Filter
              </button>
            </div>
          </div>
        </form>

        <!-- Table View -->
        <div class="card shadow-sm">
          <div class="card-body p-0">
            <div class="table-responsive d-none d-lg-block">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Report ID</th>
                    <th>Date Submitted</th>
                    <th>Reporter Name</th>
                    <th>Report Type</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                      <td><strong><?= htmlspecialchars($row['concernID']) ?></strong></td>
                      <td><?= date('M d, Y', strtotime($row['requestDate'])) ?></td>
                      <td><?= htmlspecialchars($row['reporterName']) ?></td>
                      <td><?= htmlspecialchars($row['concernType']) ?></td>
                      <td><?= htmlspecialchars($row['phoneNumber']) ?></td>
                      <td><span class="badge <?= getStatusBadgeClass($row['status']) ?>"><?= htmlspecialchars($row['status']) ?></span></td>
                      <td>
                        <a href="adminContent/viewReport.php?reportID=<?= $row['concernID'] ?>" class="btn btn-sm btn-primary" title="View Details">
                          <i class="fas fa-eye"></i>
                        </a>
                      </td>
                    </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr><td colspan="7" class="text-center py-4 text-muted">No concerns found.</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>

            <!-- Mobile View -->
            <div class="d-lg-none p-3" style="max-height:70vh;overflow-y:auto;">
              <?php 
              $result->data_seek(0); 
              if ($result->num_rows > 0): 
                while ($row = $result->fetch_assoc()): 
              ?>
              <div class="card mb-3 border-start border-4 <?= getBorderClass($row['status']) ?>">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="fw-bold mb-0">Report ID: <?= htmlspecialchars($row['concernID']) ?></h6>
                    <span class="badge <?= getStatusBadgeClass($row['status']) ?>"><?= htmlspecialchars($row['status']) ?></span>
                  </div>
                  <div class="small">
                    <p class="mb-1"><strong>Date:</strong> <?= date('M d, Y', strtotime($row['requestDate'])) ?></p>
                    <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($row['reporterName']) ?></p>
                    <p class="mb-1"><strong>Report:</strong> <?= htmlspecialchars($row['concernType']) ?></p>
                    <p class="mb-2"><strong>Contact:</strong> <?= htmlspecialchars($row['phoneNumber']) ?></p>
                    <a href="adminContent/viewReport.php?reportID=<?= $row['concernID'] ?>" class="btn btn-sm btn-primary w-100">
                      <i class="fas fa-eye me-2"></i>View Details
                    </a>
                  </div>
                </div>
              </div>
              <?php endwhile; ?>
              <?php else: ?>
                <div class="text-center py-4 text-muted">
                  <i class="fas fa-inbox fs-1 mb-3"></i>
                  <p>No concerns found.</p>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

      </div> <!-- /p-4 -->
    </div>
  </div>
</div>

<<<<<<< Updated upstream
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        const sampleReports = {
            1: {
                id: 1,
                reporterName: "John Doe",
                contact: "205-001-01",
                dateSubmitted: "2025-01-01",
                concernType: "Noise Complaint",
                location: "Barangay San Miguel, Majayjay, Laguna",
                description: "There is a persistent noise issue in our neighborhood coming from a construction site that operates during late night hours. This has been disturbing the peace and affecting the sleep of residents, especially children and elderly members of our community.",
                status: "Pending"
            },
            2: {
                id: 2,
                reporterName: "Jane Smith",
                contact: "205-002-02",
                dateSubmitted: "2025-01-02",
                concernType: "Sanitation",
                location: "Barangay Poblacion, Majayjay, Laguna",
                description: "Loud music and parties happening every weekend until early morning hours. Multiple residents have complained about this ongoing issue.",
                status: "In Progress"
            }
        };

        function viewReport(reportId) {
            const report = sampleReports[reportId];
            if (!report) return;

            document.getElementById('modalReportId').textContent = report.id;
            document.getElementById('modalReporterName').textContent = report.reporterName;
            document.getElementById('modalContact').textContent = report.contact;
            document.getElementById('modalDateSubmitted').textContent = report.dateSubmitted;
            document.getElementById('modalConcernType').textContent = report.concernType;
            document.getElementById('modalLocation').textContent = report.location;
            document.getElementById('modalDescription').textContent = report.description;
            document.getElementById('modalCurrentStatus').textContent = report.status;

            const statusBadge = document.getElementById('modalCurrentStatus');
            statusBadge.className = 'badge rounded-pill';
            switch (report.status) {
                case 'Pending':
                    statusBadge.classList.add('bg-warning', 'text-dark');
                    break;
                case 'In Progress':
                    statusBadge.classList.add('bg-success');
                    break;
                case 'Resolved':
                    statusBadge.classList.add('bg-primary');
                    break;
                case 'Closed':
                    statusBadge.classList.add('bg-danger');
                    break;
            }

            const modal = new bootstrap.Modal(document.getElementById('reportModal'));
            modal.show();
        }

        function updateStatus() {
            const newStatus = document.getElementById('statusSelect').value;
            const notes = document.getElementById('statusNotes').value;


            alert(`Status updated to: ${newStatus}\nNotes: ${notes}`);

            document.getElementById('modalCurrentStatus').textContent = newStatus;

            document.getElementById('statusNotes').value = '';
        }

        function exportToHTML() {
            const reportId = document.getElementById('modalReportId').textContent;
            const report = sampleReports[reportId];

            if (!report) return;

            // Create HTML content for export
            const htmlContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Report ${report.id} - ${report.reporterName}</title>
                    <style>
                        body { font-family: 'Poppins', Arial, sans-serif; margin: 20px; }
                        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; }
                        .content { padding: 20px; }
                        .field { margin-bottom: 15px; }
                        .label { font-weight: bold; color: #333; }
                        .value { margin-top: 5px; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>Community Concerns Report</h1>
                        <h2>Report ID: ${report.id}</h2>
                    </div>
                    <div class="content">
                        <div class="field">
                            <div class="label">Reporter Name:</div>
                            <div class="value">${report.reporterName}</div>
                        </div>
                        <div class="field">
                            <div class="label">Contact:</div>
                            <div class="value">${report.contact}</div>
                        </div>
                        <div class="field">
                            <div class="label">Date Submitted:</div>
                            <div class="value">${report.dateSubmitted}</div>
                        </div>
                        <div class="field">
                            <div class="label">Concern Type:</div>
                            <div class="value">${report.concernType}</div>
                        </div>
                        <div class="field">
                            <div class="label">Location:</div>
                            <div class="value">${report.location}</div>
                        </div>
                        <div class="field">
                            <div class="label">Description:</div>
                            <div class="value">${report.description}</div>
                        </div>
                        <div class="field">
                            <div class="label">Status:</div>
                            <div class="value">${report.status}</div>
                        </div>
                        <div class="field">
                            <div class="label">Generated On:</div>
                            <div class="value">${new Date().toLocaleString()}</div>
                        </div>
                    </div>
                </body>
                </html>
            `;

            // Create and download the HTML file
            const blob = new Blob([htmlContent], { type: 'text/html' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `report_${report.id}_${report.reporterName.replace(/\s+/g, '_')}.html`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }

        function openImageModal(imageName) {
            alert(`Opening image: ${imageName}\nIn a real implementation, this would open a larger view of the image.`);
        }

        document.getElementById('searchUser').addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            console.log('Searching for:', searchTerm);
        });

        document.getElementById('statusFilter').addEventListener('change', function () {
            const selectedStatus = this.value;
            console.log('Filtering by status:', selectedStatus);
        });

        document.getElementById('datePicker').addEventListener('change', function () {
            const selectedDate = this.value;
            console.log('Filtering by date:', selectedDate);
        });

        document.addEventListener('DOMContentLoaded', function () {
            console.log('Community Concerns Management System loaded');
        });
    </script>
</body>

</html>
=======
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
>>>>>>> Stashed changes

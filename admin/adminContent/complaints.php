<?php
include_once __DIR__ . '/../../sharedAssets/connect.php';

// ===================== INSERT HANDLER =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $complaintTitle = $_POST['complaintTitle'] ?? '';

  // If "Other", replace with custom input
  if ($complaintTitle === "Other" && !empty($_POST['otherComplaint'])) {
    $complaintTitle = $_POST['otherComplaint'];
  }

  $complaintStatus = 'Criminal'; // ✅ Always set as Criminal
  $complaintDescription = $_POST['complaintDescription'] ?? '';
  $phoneNumber = $_POST['complaintPhoneNumber'] ?? '';
  $complainantName = $_POST['complainantName'] ?? '';
  $complaintVictim = $_POST['complaintVictim'] ?? '';
  $victimAge = $_POST['victimAge'] ?? null;
  $complaintAccused = $_POST['complaintAccused'] ?? '';
  $victimRelationship = $_POST['victimRelationship'] ?? '';
  $actionTaken = $_POST['actionTaken'] ?? '';
  $complaintAddress = $_POST['complaintAddress'] ?? '';
  $requestDate = date('Y-m-d H:i:s');

  // Default values for unused columns
  $complaintCategoryID = 0;
  $complaintTypeID = 0;
  $evidenceFile = "";

  // Handle file upload
  if (!empty($_FILES['evidence']['name'])) {
    $uploadDir = __DIR__ . "/../../uploads/";
    if (!is_dir($uploadDir)) {
      mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . "_" . basename($_FILES['evidence']['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['evidence']['tmp_name'], $targetPath)) {
      $evidenceFile = $fileName; // store only filename in DB
    }
  }

  // ✅ Insert new complaint
  $stmt = $conn->prepare("INSERT INTO complaints 
    (userID, complaintCategoryID, complaintTypeID, complaintTitle, complaintDescription, requestDate, 
     complaintStatus, complaintPhoneNumber, complaintAccused, complaintAddress, complaintVictim, 
     complainantName, victimAge, victimRelationship, actionTaken, evidence, isDeleted) 
    VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'no')");

  $stmt->bind_param(
    "iisssssssssisss",
    $complaintCategoryID,
    $complaintTypeID,
    $complaintTitle,
    $complaintDescription,
    $requestDate,
    $complaintStatus,
    $phoneNumber,
    $complaintAccused,
    $complaintAddress,
    $complaintVictim,
    $complainantName,
    $victimAge,
    $victimRelationship,
    $actionTaken,
    $evidenceFile
  );

  $stmt->execute();
  $stmt->close();

  echo "<script>window.location.href = 'http://localhost/E-Barangay/E-Barangay/admin/index.php?page=complaints';</script>";
  exit;
}

if (isset($_GET['delete'])) {
  $complaintID = (int) $_GET['delete'];

  $stmt = $conn->prepare("UPDATE complaints SET isDeleted = 'yes' WHERE complaintID = ?");
  $stmt->bind_param("i", $complaintID);
  $stmt->execute();
  $stmt->close();

  echo "<script>window.location.href = 'http://localhost/E-Barangay/E-Barangay/admin/index.php?page=complaints';</script>";
  exit;
}

$search = trim($_GET['search'] ?? '');
$status = $_GET['status'] ?? '';
$date = $_GET['date'] ?? '';

$perPage = 10;
$currentPage = isset($_GET['p']) ? (int) $_GET['p'] : 1;
if ($currentPage < 1)
  $currentPage = 1;

$offset = ($currentPage - 1) * $perPage;

// ✅ Base query
$sql = "SELECT 
    r.complaintID AS concernID,
    r.requestDate,
    COALESCE(CONCAT(ui.firstName, ' ', ui.middleName, ' ', ui.lastName), complainantName) AS reporterName,
    r.complaintTitle AS concernType,
    r.complaintPhoneNumber AS phoneNumber,
    r.complaintStatus AS status
FROM complaints r
LEFT JOIN users u ON r.userID = u.userID
LEFT JOIN userinfo ui ON u.userID = ui.userID
WHERE r.isDeleted = 'no'";

$countSql = "SELECT COUNT(*) as total
FROM complaints r
LEFT JOIN users u ON r.userID = u.userID
LEFT JOIN userinfo ui ON u.userID = ui.userID
WHERE r.isDeleted = 'no'";

$params = [];
$types = '';

// ✅ Add filters
if ($search !== '') {
  $sql .= " AND (
        COALESCE(CONCAT(ui.firstName, ' ', ui.lastName), r.complainantName) LIKE ?
        OR CAST(r.complaintID AS CHAR) LIKE ?
        OR r.complaintTitle LIKE ?
    )";
  $countSql .= " AND (
        COALESCE(CONCAT(ui.firstName, ' ', ui.lastName), r.complainantName) LIKE ?
        OR CAST(r.complaintID AS CHAR) LIKE ?
        OR r.complaintTitle LIKE ?
    )";
  $term = "%$search%";
  $params = array_merge($params, [$term, $term, $term]);
  $types .= 'sss';
}

if ($status !== '' && $status !== 'All') {
  $sql .= " AND r.complaintStatus = ?";
  $countSql .= " AND r.complaintStatus = ?";
  $params[] = $status;
  $types .= 's';
}

if ($date !== '') {
  $sql .= " AND DATE(r.requestDate) = ?";
  $countSql .= " AND DATE(r.requestDate) = ?";
  $params[] = $date;
  $types .= 's';
}

// ✅ Count total records for pagination
$countStmt = $conn->prepare($countSql);
if (!empty($params)) {
  $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$totalRecords = $countStmt->get_result()->fetch_assoc()['total'] ?? 0;
$totalPages = max(1, ceil($totalRecords / $perPage));
$countStmt->close();

// ✅ Add order + limit
$sql .= " ORDER BY r.requestDate DESC LIMIT ? OFFSET ?";
$params[] = $perPage;
$params[] = $offset;
$types .= 'ii';

$stmt = $conn->prepare($sql);
if (!empty($params)) {
  $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

function getStatusBadgeClass($status)
{
  return match (strtolower($status)) {
    'criminal', 'civil' => 'bg-danger text-white',
    'mediation', 'conciliation', 'arbitration' => 'bg-info text-white',
    'repudiated', 'withdrawn', 'pending', 'dismissed', 'certified' => 'bg-success text-white',
    default => 'bg-secondary text-white',
  };
}

function getBorderClass($status)
{
  return match (strtolower($status)) {
    'criminal', 'civil' => 'border-danger',
    'mediation', 'conciliation', 'arbitration' => 'border-info',
    'repudiated', 'withdrawn', 'pending', 'dismissed', 'certified' => 'border-success',
    default => 'border-secondary',
  };
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Community Concerns Management</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
    rel="stylesheet" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #e9e9e9;
    }

    .table-hover tbody tr:hover {
      background-color: rgba(0, 0, 0, .075);
    }

    .badge {
      font-size: 0.75em;
    }

    .bg-custom {
      background-color: #31afab !important;
      color: #fff;
    }

    .btn-custom {
      background-color: #31afab;
      color: #fff;
    }

    .btn-custom:hover {
      background-color: #279995;
      color: #fff;
    }
  </style>
</head>

<body>
  <div class="container-fluid p-3 p-md-4">
    <div class="card shadow-lg border-0 rounded-3">
      <div class="card-body p-0">
        <div class="text-white p-4 rounded-top" style="background-color: rgb(49, 175, 171);">
          <div class="d-flex align-items-center">
            <i class="fas fa-users me-3 fs-4"></i>
            <h1 class="h4 mb-0 fw-semibold">Katarungang Pambarangay</h1>
          </div>
        </div>

        <div class="p-3 p-md-3">
          <div class="row g-3 mb-4">
            <!-- Filter Form -->
            <div class="col-md-10">
              <form method="GET" action="index.php" class="row g-3">
                <input type="hidden" name="page" value="complaints">

                <div class="col-md-5">
                  <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                      <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                      class="form-control border-start-0" placeholder="Search Reporter, ID, or Title">
                  </div>
                </div>

                <div class="col-md-3">
                  <select name="status" class="form-select">
                    <option value="All" <?= ($status === '' || $status === 'All') ? 'selected' : '' ?>>All Status</option>
                    <?php foreach (['Criminal', 'Civil', 'Mediation', 'Conciliation', 'Arbitration', 'Repudiated', 'Withdrawn', 'Pending', 'Dismissed', 'Certified'] as $st): ?>
                      <option value="<?= $st ?>" <?= $status === $st ? 'selected' : '' ?>><?= $st ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="col-md-2">
                  <input type="date" name="date" value="<?= htmlspecialchars($date) ?>" class="form-control">
                </div>

                <div class="col-md-2">
                  <button type="submit" class="btn btn-custom w-100">
                    <i class="fas fa-filter me-2"></i>Filter
                  </button>
                </div>
              </form>
            </div>

            <!-- Add Button (outside the form) -->
            <div class="col-md-2">
              <button class="btn btn-custom w-100" data-bs-toggle="modal" data-bs-target="#addComplaint">
                <i class="fas fa-user-plus me-1"></i> Add
              </button>
            </div>
          </div>

          <div class="card shadow-sm">
            <div class="card-body p-2">
              <div class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Reporter's Name</th>
                      <th>Date Recorded</th>
                      <th>Type</th>
                      <th>Contact</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if ($result->num_rows > 0): ?>
                      <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                          <td><?= htmlspecialchars($row['reporterName']) ?></td>
                          <td><?= date('M d, Y', strtotime($row['requestDate'])) ?></td>
                          <td><?= htmlspecialchars($row['concernType']) ?></td>
                          <td><?= htmlspecialchars($row['phoneNumber']) ?></td>
                          <td>
                            <span class="badge <?= getStatusBadgeClass($row['status']) ?>">
                              <?= htmlspecialchars($row['status']) ?>
                            </span>
                          </td>
                          <td>
                            <!-- View button -->
                            <a href="adminContent/viewComplaint.php?complaintID=<?= $row['concernID'] ?>"
                              class="btn btn-sm btn-success" title="View Details">
                              <i class="fas fa-eye me-1"></i>
                            </a>
                            <!-- Delete Button (trigger modal) -->
                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                              data-bs-target="#deleteModal<?= $row['concernID'] ?>">
                              <i class="fas fa-trash"></i>
                            </button>

                            <!-- Modal code unchanged -->
                          </td>
                        </tr>
                      <?php endwhile; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No concerns found.</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Pagination footer INSIDE the card -->
            <div class="card-footer bg-light">
              <div class="row align-items-center">
                <!-- Showing results -->
                <div class="col-12 col-md-6">
                  <div class="text-center text-md-start">
                    <?php
                    $start = ($totalRecords > 0) ? ($offset + 1) : 0;
                    $end = min($offset + $result->num_rows, $totalRecords);
                    ?>
                    <small class="text-muted">
                      Showing <?= $start ?>–<?= $end ?> of <?= $totalRecords ?>
                      complaint<?= $totalRecords !== 1 ? 's' : '' ?>
                    </small>
                  </div>
                </div>

                <!-- Pagination controls -->
                <div class="col-12 col-md-6">
                  <nav class="d-flex justify-content-center justify-content-md-end">
                    <ul class="pagination pagination-sm mb-0">
                      <?php
                      $queryBase = "page=complaints&search=" . urlencode($search) . "&status=" . urlencode($status) . "&date=" . urlencode($date);
                      ?>

                      <!-- Previous -->
                      <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?<?= $queryBase ?>&p=<?= max(1, $currentPage - 1) ?>"
                          aria-label="Previous">
                          <span aria-hidden="true">&laquo;</span>
                        </a>
                      </li>

                      <!-- Numbered pages -->
                      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                          <a class="page-link" href="?<?= $queryBase ?>&p=<?= $i ?>"><?= $i ?></a>
                        </li>
                      <?php endfor; ?>

                      <!-- Next -->
                      <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?<?= $queryBase ?>&p=<?= min($totalPages, $currentPage + 1) ?>"
                          aria-label="Next">
                          <span aria-hidden="true">&raquo;</span>
                        </a>
                      </li>
                    </ul>
                  </nav>
                </div>
              </div>
            </div>
          </div>

          <!-- Add Complaint Modal -->
          <div class="modal fade" id="addComplaint" tabindex="-1" aria-labelledby="addComplaintLabel"
            aria-hidden="true">
            <!-- modal content unchanged -->
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
include_once __DIR__ . '/../../sharedAssets/connect.php';

// ===================== INSERT HANDLER =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $complaintTitle = $_POST['complaintTitle'] ?? '';

    if ($complaintTitle === "Other" && !empty($_POST['otherComplaint'])) {
        $complaintTitle = $_POST['otherComplaint'];
    }

    $criminalComplaints = [
        "Alcohol-Related Disturbances",
        "Physical Assault and Threats",
        "Physical Abuse",
        "Sexual Abuse",
        "Curfew Violations"
    ];

    $complaintType = in_array($complaintTitle, $criminalComplaints) ? "Criminal" : "Civil";

    $complaintStatus = 'Pending';
    $complaintDescription = $_POST['complaintDescription'] ?? '';
    $phoneNumber = $_POST['phoneNumber'] ?? '';
    $complainantName = $_POST['complainantName'] ?? '';
    $complaintVictim = $_POST['complaintVictim'] ?? '';
    $victimAge = $_POST['victimAge'] ?? null;
    $complaintAccused = $_POST['complaintAccused'] ?? '';
    $victimRelationship = $_POST['victimRelationship'] ?? '';
    $actionTaken = $_POST['actionTaken'] ?? '';
    $complaintAddress = $_POST['complaintAddress'] ?? '';
    $requestDate = date('Y-m-d H:i:s');
    
    // Get latitude and longitude
    $latitude = !empty($_POST['latitude']) ? $_POST['latitude'] : null;
    $longitude = !empty($_POST['longitude']) ? $_POST['longitude'] : null;

    $evidenceFile = "";
    if (!empty($_FILES['evidence']['name'])) {
        $uploadDir = __DIR__ . "/../../uploads/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES['evidence']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['evidence']['tmp_name'], $targetPath)) {
            $evidenceFile = $fileName;
        }
    }

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO complaints (
        userID, complaintType, complaintTitle, complaintDescription, requestDate, 
        complaintStatus, complaintPhoneNumber, complaintAccused, complaintAddress, 
        complaintVictim, victimAge, victimRelationship, complainantName, actionTaken, evidence, 
        complaintLatitude, complaintLongitude
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $userID = 1;
    $stmt->bind_param(
        "isssssssssissssdd",
        $userID,              // 1. i - userID
        $complaintType,       // 2. s - complaintType
        $complaintTitle,      // 3. s - complaintTitle
        $complaintDescription,// 4. s - complaintDescription
        $requestDate,         // 5. s - requestDate
        $complaintStatus,     // 6. s - complaintStatus
        $phoneNumber,         // 7. s - complaintPhoneNumber
        $complaintAccused,    // 8. s - complaintAccused
        $complaintAddress,    // 9. s - complaintAddress
        $complaintVictim,     // 10. s - complaintVictim
        $victimAge,           // 11. i - victimAge
        $victimRelationship,  // 12. s - victimRelationship
        $complainantName,     // 13. s - complainantName
        $actionTaken,         // 14. s - actionTaken
        $evidenceFile,        // 15. s - evidence
        $latitude,            // 16. d - complaintLatitude
        $longitude            // 17. d - complaintLongitude
    );
    
    $stmt->execute();
    $stmt->close();

    echo "<script>window.location.href = 'index.php?page=complaints';</script>";
    exit;
}

if (isset($_GET['delete'])) {
    $complaintID = (int) $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM complaints WHERE complaintID = ?");
    $stmt->bind_param("i", $complaintID);
    $stmt->execute();
    $stmt->close();

    echo "<script>window.location.href = 'index.php?page=complaints';</script>";
    exit;
}

$search = trim($_GET['search'] ?? '');
$status = $_GET['complaintStatus'] ?? '';
$type = $_GET['complaintType'] ?? '';
$date = $_GET['date'] ?? '';

$perPage = 20;
$currentPage = isset($_GET['p']) ? (int) $_GET['p'] : 1;
if ($currentPage < 1)
    $currentPage = 1;

$offset = ($currentPage - 1) * $perPage;

// ✅ Base query with WHERE clause
$sql = "SELECT 
    r.*, 
    COALESCE(CONCAT_WS(' ', ui.firstName, ui.middleName, ui.lastName, ui.suffix), r.complainantName) AS reporterName,
    r.complaintID AS concernID,
    r.complaintTitle AS concernType,
    r.complaintPhoneNumber AS phoneNumber,
    r.complaintStatus AS status
FROM complaints r
LEFT JOIN users u ON r.userID = u.userID
LEFT JOIN userinfo ui ON u.userID = ui.userID
WHERE 1=1";

$countSql = "SELECT COUNT(*) AS total
FROM complaints r
LEFT JOIN users u ON r.userID = u.userID
LEFT JOIN userinfo ui ON u.userID = ui.userID
WHERE 1=1";

$params = [];
$types = '';

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

if ($type !== '' && $type !== 'All') {
    $sql .= " AND r.complaintTitle = ?";
    $countSql .= " AND r.complaintTitle = ?";
    $params[] = $type;
    $types .= 's';
}

if ($date !== '') {
    $sql .= " AND DATE(r.requestDate) = ?";
    $countSql .= " AND DATE(r.requestDate) = ?";
    $params[] = $date;
    $types .= 's';
}

$countStmt = $conn->prepare($countSql);
if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$totalRecords = $countStmt->get_result()->fetch_assoc()['total'] ?? 0;
$totalPages = max(1, ceil($totalRecords / $perPage));
$countStmt->close();

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
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Community Concerns Management</title>
  <link rel="icon" href="assets/images/logoSanAntonio.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #e9e9e9;
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

    #addComplaintModal .modal-body {
      max-height: calc(100vh - 200px);
      overflow-y: auto;
    }

    .filterButton {
      background-color: #19AFA5;
      border-color: #19AFA5;
      color: white;
    }

    .filterButton:hover {
      background-color: #11A1A1;
      border-color: #11A1A1;
      color: white;
    }

    .viewButton {
      background-color: transparent;
      border-color: #19AFA5;
      color: #19AFA5;
    }

    .viewButton:hover {
      background-color: #19AFA5;
      border-color: #19AFA5;
      color: white;
    }
  </style>
</head>

<body>
  <div class="container-fluid p-3 p-md-4">
    <div class="card shadow-lg border-0 rounded-3">
      <div class="card-body p-0">
        <div class="text-white p-4 rounded-top" style="background-color: #19AFA5;">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center">
              <i class="fas fa-file-alt me-3 fs-4"></i>
              <h1 class="h4 mb-0 fw-semibold">Katarungang Pambarangay</h1>
            </div>
            <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addComplaintModal">
              <i class="fas fa-plus me-2"></i>Add Complaint
            </button>

          </div>
        </div>

        <div class="p-3 p-md-4">
          <form method="GET" action="index.php">
            <input type="hidden" name="page" value="complaints">
            <div class="card shadow-sm mb-4">
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-lg-3 col-md-6">
                    <div class="input-group">
                      <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-search text-muted"></i>
                      </span>
                      <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                        class="form-control border-start-0" placeholder="Search Reporter, ID, or Title">
                    </div>
                  </div>

                  <div class="col-md-2">
                    <select name="complaintStatus" class="form-select">
                      <option value="All" <?= ($status === '' || $status === 'All') ? 'selected' : '' ?>>All Status</option>
                      <?php foreach (['Criminal', 'Civil', 'Mediation', 'Conciliation', 'Arbitration', 'Repudiated', 'Withdrawn', 'Pending', 'Dismissed', 'Certified'] as $st): ?>
                        <option value="<?= $st ?>" <?= $status === $st ? 'selected' : '' ?>><?= $st ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <div class="col-md-2">
                    <select name="complaintType" class="form-select">
                      <option value="All" <?= ($type === '' || $type === 'All') ? 'selected' : '' ?>>All Type</option>

                      <?php foreach ([
                        'Noise Complaints', 'Boundary and Land Disputes', 'Neighborhood Quarrels',
                        'Animal-Related Complaints', 'Youth-Related Issues', 'Barangay Clearance and Permit Concerns',
                        'Garbage and Sanitation Complaints', 'Alcohol-Related Disturbances', 'Traffic and Parking Issues',
                        'Physical Assault and Threats', 'Water Supply Disputes', 'Business-Related Conflicts',
                        'Curfew Violations', 'Smoking and Littering Violations', 'Illegal Structures and Encroachments',
                        'Physical Abuse', 'Sexual Abuse', 'Psychological Abuse/Emotional Abuse',
                        'Economic Abuse', 'Neglect', 'Other'
                      ] as $st): ?>
                        <option value="<?= $st ?>" <?= ($type === $st) ? 'selected' : '' ?>>
                          <?= $st ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>


                  <div class="col-md-3">
                    <input type="date" name="date" value="<?= htmlspecialchars($date) ?>" class="form-control">
                  </div>

                  <div class="col-md-2">
                    <button type="submit" class="btn btn-info filterButton text-white w-100">
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
                              class="btn btn-sm btn-outline-primary viewButton" title="View Details">
                              <i class="fas fa-eye gap"></i>
                            </a>
                            <!-- Delete Button (trigger modal) -->
                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                              data-bs-target="#deleteModal<?= $row['concernID'] ?>">
                              <i class="fas fa-times"></i>
                            </button>
                            <!-- Delete Confirmation Modal -->
                            <div class="modal fade" id="deleteModal<?= $row['concernID'] ?>" tabindex="-1"
                              aria-labelledby="deleteModalLabel<?= $row['concernID'] ?>" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel<?= $row['concernID'] ?>">
                                      <i class="fas fa-exclamation-triangle me-2"></i>Confirm Deletion
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                  </div>
                                  <div class="modal-body">
                                    Are you sure you want to delete this complaint?
                                    <br>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <a href="?page=complaints&delete=<?= $row['concernID'] ?>" class="btn btn-danger">
                                      <i class="fas fa-trash-alt me-1"></i> Delete
                                    </a>
                                  </div>
                                </div>
                              </div>
                            </div>
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
                      Showing <?= $totalRecords ?>
                      complaint<?= $totalRecords !== 1 ? 's' : '' ?>
                    </small>
                  </div>
                </div>

                <!-- Pagination controls -->
                <?php if ($totalRecords > 20): ?>
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
                <?php endif; ?>
              </div>

              <!-- Add Complaint Modal -->
              <div class="modal fade" id="addComplaintModal" tabindex="-1" aria-labelledby="addComplaintLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                  <div class="modal-content">

                    <div class="modal-header" style="background-color:#31afab;">
                      <h5 class="modal-title text-white fw-semibold" id="addComplaintLabel">
                        <i class="fas fa-plus me-2"></i>Add Complaint
                      </h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form method="POST" enctype="multipart/form-data">
                      <div class="modal-body">
                        <div class="row g-3">
                          <!-- Type of Complaint -->
                          <div class="col-6">
                            <label class="form-label fw-semibold">Type of Complaint</label>
                            <select name="complaintTitle" class="form-select" id="complaintTitle" required>
                              <option value="Noise Complaints">Noise Complaints</option>
                              <option value="Boundary and Land Disputes">Boundary and Land Disputes</option>
                              <option value="Neighborhood Quarrels">Neighborhood Quarrels</option>
                              <option value="Animal-Related Complaints">Animal-Related Complaints</option>
                              <option value="Youth-Related Issues">Youth-Related Issues</option>
                              <option value="Barangay Clearance and Permit Concerns">Barangay Clearance and Permit
                                Concerns</option>
                              <option value="Garbage and Sanitation Complaints">Garbage and Sanitation Complaints
                              </option>
                              <option value="Alcohol-Related Disturbances">Alcohol-Related Disturbances</option>
                              <option value="Traffic and Parking Issues">Traffic and Parking Issues</option>
                              <option value="Physical Assault and Threats">Physical Assault and Threats</option>
                              <option value="Water Supply Disputes">Water Supply Disputes</option>
                              <option value="Business-Related Conflicts">Business-Related Conflicts</option>
                              <option value="Curfew Violations">Curfew Violations</option>
                              <option value="Smoking and Littering Violations">Smoking and Littering Violations</option>
                              <option value="Illegal Structures and Encroachments">Illegal Structures and Encroachments
                              </option>
                              <option value="Physical Abuse">Physical Abuse</option>
                              <option value="Sexual Abuse">Sexual Abuse</option>
                              <option value="Psychological Abuse/Emotional Abuse">Psychological Abuse/Emotional Abuse
                              </option>
                              <option value="Economic Abuse">Economic Abuse</option>
                              <option value="Neglect">Neglect</option>
                              <option value="Other">Other</option>
                            </select>
                          </div>

                          <!-- Evidence Upload -->
                          <div class="col-6">
                            <label class="form-label fw-semibold">Evidence (Photo)</label>
                            <input type="file" name="evidence" class="form-control" accept="image/*">
                          </div>

                          <div class="col-12 d-none" id="otherComplaintDiv">
                            <label class="form-label fw-semibold">Please specify</label>
                            <input type="text" name="otherComplaint" class="form-control">
                          </div>

                          <!-- Complainant Info -->
                          <div class="col-md-6">
                            <label class="form-label fw-semibold">Complainant's Name</label>
                            <input type="text" name="complainantName" class="form-control" required>
                          </div>

                          <div class="col-md-6">
                            <label class="form-label fw-semibold">Contact Number</label>
                            <input type="text" name="phoneNumber" class="form-control" required pattern="^09\d{9}$"
                              title="Please enter a valid PH mobile number (e.g. 09123456789)">
                          </div>

                          <!-- Victim Info -->
                          <div class="col-md-6">
                            <label class="form-label fw-semibold">Victim</label>
                            <input type="text" name="complaintVictim" class="form-control" required>
                          </div>

                          <div class="col-md-6">
                            <label class="form-label fw-semibold">Victim's Age</label>
                            <input type="number" name="victimAge" class="form-control" required min="0">
                          </div>

                          <!-- Perpetrator Info -->
                          <div class="col-md-6">
                            <label class="form-label fw-semibold">Perpetrator</label>
                            <input type="text" name="complaintAccused" class="form-control" required>
                          </div>

                          <div class="col-md-6">
                            <label class="form-label fw-semibold">Relationship</label>
                            <input type="text" name="victimRelationship" class="form-control" required>
                          </div>

                          <!-- Description -->
                          <div class="col-12">
                            <label class="form-label fw-semibold">Complainant's Description</label>
                            <textarea name="complaintDescription" class="form-control" rows="3" required></textarea>
                          </div>

                          <!-- Actions Taken -->
                          <div class="col-12">
                            <label class="form-label fw-semibold">Actions Taken</label>
                            <textarea name="actionTaken" class="form-control" rows="3"></textarea>
                          </div>

                          <!-- Address -->
                          <div class="col-6">
                            <label class="form-label fw-semibold">Address</label>
                            <input type="text" name="complaintAddress" class="form-control" required>
                          </div>

                          <!-- Map -->
                          <div class="col-6">
                            <div class="card rounded-5 border-1" style="border-color: #19AFA5;">
                              <div class="rounded-5" style="position: relative; height: 288px; overflow: hidden;">
                                <div id="map" name="map" style="height: 100%;"></div>
                              </div>
                            </div>
                            <input type="hidden" name="latitude" id="lat">
                            <input type="hidden" name="longitude" id="lng">
                          </div>
                        </div>
                      </div>

                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" style="background-color:#31afab; border: #31afab;">Submit Complaint</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


  <script>
    document.getElementById("complaintTitle").addEventListener("change", function () {
      const otherDiv = document.getElementById("otherComplaintDiv");
      if (this.value === "Other") {
        otherDiv.classList.remove("d-none");
      } else {
        otherDiv.classList.add("d-none");
      }
    });
  </script>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    // Initialize map when modal is shown
    let map;
    let marker;

    document.getElementById('addComplaintModal').addEventListener('shown.bs.modal', function () {
      if (!map) {
        // Initialize map only once
        map = L.map('map').setView([14.111903674282024, 121.14573570538116], 17);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Add click event to place marker
        map.on('click', function (e) {
          const lat = e.latlng.lat;
          const lng = e.latlng.lng;

          // Remove existing marker if any
          if (marker) {
            map.removeLayer(marker);
          }

          // Add new marker
          marker = L.marker([lat, lng]).addTo(map);

          // Update hidden inputs
          document.getElementById('lat').value = lat;
          document.getElementById('lng').value = lng;
        });
      }

      // Fix map display issues after modal is shown
      setTimeout(function () {
        map.invalidateSize();
      }, 100);
    });

    // Complaint type handler
    document.getElementById("complaintTitle").addEventListener("change", function () {
      const otherDiv = document.getElementById("otherComplaintDiv");
      if (this.value === "Other") {
        otherDiv.classList.remove("d-none");
      } else {
        otherDiv.classList.add("d-none");
      }
    });
  </script>

  <!-- Bootstrap bundle already included below -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>
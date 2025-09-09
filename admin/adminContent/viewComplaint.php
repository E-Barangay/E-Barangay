<?php
include_once __DIR__ . '/../../sharedAssets/connect.php';

$complaintID = $_GET['complaintID'] ?? $_POST['complaintID'] ?? '';

// ✅ Handle status update (separate)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complaintStatus'])) {
  $newStatus = $_POST['complaintStatus'];
  $stmt = $conn->prepare("UPDATE complaints SET complaintStatus = ? WHERE complaintID = ?");
  $stmt->bind_param("si", $newStatus, $complaintID);
  $stmt->execute();
  $stmt->close();

  header("Location: ../index.php?page=complaints");
  exit;
}

// ✅ Handle full complaint update (edit mode)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['saveComplaint'])) {
  $fields = [
    'complaintTitle',
    'complainantName',
    'complaintPhoneNumber',
    'complaintDescription',
    'actionTaken',
    'complaintVictim',
    'victimAge',
    'complaintAccused',
    'victimRelationship'
  ];

  $updates = [];
  $params = [];
  $types = '';

  foreach ($fields as $field) {
    if (isset($_POST[$field])) {
      $updates[] = "$field = ?";
      $params[] = $_POST[$field];
      $types .= 's';
    }
  }

  if (!empty($updates)) {
    $sql = "UPDATE complaints SET " . implode(', ', $updates) . " WHERE complaintID = ?";
    $stmt = $conn->prepare($sql);
    $types .= 'i';
    $params[] = $complaintID;

    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $stmt->close();
  }

  header("Location: ?complaintID=$complaintID");
  exit;
}

// ✅ Fetch complaint details
$stmt = $conn->prepare("
    SELECT 
      r.complaintID, 
      r.requestDate, 
      r.complaintStatus, 
      r.complaintTitle,
      COALESCE(CONCAT(ui.firstName, ' ', ui.middleName, ' ', ui.lastName), r.complainantName) AS complainantName,
      r.complaintVictim,
      r.complaintDescription,
      r.actionTaken,
      r.victimAge,
      r.complaintAccused,
      r.victimRelationship,
      r.complaintPhoneNumber,
      r.evidence
    FROM complaints r
    LEFT JOIN users u ON r.userID = u.userID
    LEFT JOIN userinfo ui ON u.userID = ui.userID
    WHERE r.complaintID = ?
");
$stmt->bind_param('i', $complaintID);
$stmt->execute();
$result = $stmt->get_result();
$complaint = $result->fetch_assoc();
$stmt->close();

function getStatusBadgeClass($status)
{
  return match (strtolower($status)) {
    'criminal', 'civil' => 'bg-danger text-white',
    'mediation', 'conciliation', 'arbitration' => 'bg-info text-white',
    'repudiated', 'withdrawn', 'pending', 'dismissed', 'certified' => 'bg-success text-white',
    default => 'bg-secondary text-white',
  };
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
  <style>
    body {
      background: #f1f3f5;
    }

    .report-card {
      max-width: 1000px;
      margin: 30px auto;
      padding: 1.5rem;
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);

      /* force equal-height columns */
      display: flex;
      flex-wrap: nowrap;
      align-items: stretch;
    }

    .report-info {
      flex: 1 1 60%;
    }

    .sidebar-controls {
      border-left: 1px dashed #dee2e6;
      padding-left: 1rem;
      height: 100%;
      /* make border stretch full height */
    }

    .status-badge {
      font-size: 0.8rem;
      border-radius: 15px;
      padding: 0.2rem 0.6rem;
    }

    #map {
      height: 300px;
      width: 100%;
      border-radius: 10px;
    }
  </style>
</head>

<body>
  <div class="container px-3">
    <div class="report-card row g-4">
      <!-- Complaint Details Form -->
      <div class="col-md-7">
        <form method="POST" action="">
          <input type="hidden" name="complaintID" value="<?= $complaint['complaintID'] ?>">

          <div class="report-info">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="fw-bold mb-0">
                <i class="fas fa-file-alt text-primary me-2"></i> Complaint Details
              </h5>
              <span class="badge <?= getStatusBadgeClass($complaint['complaintStatus']) ?> status-badge">
                <?= ucfirst($complaint['complaintStatus'] ?? 'No data') ?>
              </span>
            </div>

            <!-- Complaint Info -->
            <div class="mb-3">
              <p><strong>Title:</strong>
                <span
                  class="view-mode"><?= $complaint['complaintTitle'] ?? '<span class="text-muted">No data</span>' ?></span>
                <input type="text" class="form-control d-none edit-mode" name="complaintTitle"
                  value="<?= htmlspecialchars($complaint['complaintTitle']) ?>">
              </p>
              <p><strong>Date Submitted:</strong>
                <?= $complaint['requestDate'] ?? '<span class="text-muted">No data</span>' ?></p>
              <p><strong>Complainant:</strong>
                <span
                  class="view-mode"><?= $complaint['complainantName'] ?? '<span class="text-muted">No data</span>' ?></span>
                <input type="text" class="form-control d-none edit-mode" name="complainantName"
                  value="<?= htmlspecialchars($complaint['complainantName']) ?>">
              </p>
              <p><strong>Phone Number:</strong>
                <span
                  class="view-mode"><?= $complaint['complaintPhoneNumber'] ?? '<span class="text-muted">No data</span>' ?></span>
                <input type="text" class="form-control d-none edit-mode" name="complaintPhoneNumber"
                  value="<?= htmlspecialchars($complaint['complaintPhoneNumber']) ?>">
              </p>
              <p><strong>Description:</strong>
                <span
                  class="view-mode"><?= $complaint['complaintDescription'] ?? '<span class="text-muted">No data</span>' ?></span>
                <textarea class="form-control d-none edit-mode"
                  name="complaintDescription"><?= htmlspecialchars($complaint['complaintDescription']) ?></textarea>
              </p>
              <p><strong>Action:</strong>
                <span
                  class="view-mode"><?= $complaint['actionTaken'] ?? '<span class="text-muted">No data</span>' ?></span>
                <textarea class="form-control d-none edit-mode"
                  name="actionTaken"><?= htmlspecialchars($complaint['actionTaken']) ?></textarea>
              </p>
            </div>

            <!-- Victim & Perpetrator Info -->
            <div class="row">
              <div class="col-md-6">
                <p><strong>Victim:</strong>
                  <span
                    class="view-mode"><?= $complaint['complaintVictim'] ?? '<span class="text-muted">No data</span>' ?></span>
                  <input type="text" class="form-control d-none edit-mode" name="complaintVictim"
                    value="<?= htmlspecialchars($complaint['complaintVictim']) ?>">
                </p>
              </div>
              <div class="col-md-6">
                <p><strong>Victim Age:</strong>
                  <span
                    class="view-mode"><?= $complaint['victimAge'] ?? '<span class="text-muted">No data</span>' ?></span>
                  <input type="number" class="form-control d-none edit-mode" name="victimAge"
                    value="<?= htmlspecialchars($complaint['victimAge']) ?>">
                </p>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <p><strong>Perpetrator:</strong>
                  <span
                    class="view-mode"><?= $complaint['complaintAccused'] ?? '<span class="text-muted">No data</span>' ?></span>
                  <input type="text" class="form-control d-none edit-mode" name="complaintAccused"
                    value="<?= htmlspecialchars($complaint['complaintAccused']) ?>">
                </p>
              </div>
              <div class="col-md-6">
                <p><strong>Relationship:</strong>
                  <span
                    class="view-mode"><?= $complaint['victimRelationship'] ?? '<span class="text-muted">No data</span>' ?></span>
                  <input type="text" class="form-control d-none edit-mode" name="victimRelationship"
                    value="<?= htmlspecialchars($complaint['victimRelationship']) ?>">
                </p>
              </div>
            </div>

            <!-- Evidence (not editable) -->
            <div class="mb-3">
              <strong>Evidence:</strong>
              <?php if (!empty($complaint['evidence'])): ?>
                <button type="button" class="btn btn-sm btn-primary ms-2" data-bs-toggle="modal"
                  data-bs-target="#evidenceModal">
                  <i class="fas fa-eye me-1"></i> View Evidence
                </button>
                <div class="modal fade" id="evidenceModal" tabindex="-1">
                  <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Evidence Preview</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body text-center">
                        <img src="../../uploads/<?= htmlspecialchars($complaint['evidence']) ?>"
                          class="img-fluid rounded">
                      </div>
                    </div>
                  </div>
                </div>
              <?php else: ?>
                <span class="text-muted ms-2">No evidence</span>
              <?php endif; ?>
            </div>

            <!-- Map -->
            <div class="mt-4">
              <h6 class="fw-semibold mb-2"><i class="fas fa-map-marker-alt text-danger me-1"></i> Location Map
                (Placeholder)</h6>
              <div id="map"></div>
            </div>

            <!-- Save/Cancel Buttons -->
            <div id="editActions" class="d-none mt-3">
              <button type="submit" name="saveComplaint" class="btn btn-success me-2">
                <i class="fas fa-save me-1"></i>Save Changes
              </button>
              <button type="button" id="cancelEditBtn" class="btn btn-danger">
                <i class="fas fa-times me-1"></i>Cancel
              </button>
            </div>
          </div>
        </form>
      </div>

      <!-- Sidebar -->
      <div class="col-md-5">
        <div class="sidebar-controls">
          <h6 class="fw-bold mb-3">
            <i class="fas fa-tools me-1 text-dark"></i>Admin Controls
          </h6>

          <form method="POST" action="">
            <input type="hidden" name="complaintID" value="<?= $complaint['complaintID'] ?>">

            <div class="mb-3">
              <label for="complaintStatus" class="form-label">Change Status</label>
              <select name="complaintStatus" id="complaintStatus" class="form-select" required>
                <option disabled>-- Select Status --</option>
                <?php foreach (['Criminal', 'Civil', 'Mediation', 'Conciliation', 'Arbitration', 'Repudiated', 'Withdrawn', 'Pending', 'Dismissed', 'Certified', 'VAWC'] as $st): ?>
                  <option value="<?= $st ?>" <?= $complaint['complaintStatus'] == $st ? 'selected' : '' ?>>
                    <?= $st ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- Buttons in one row -->
            <div class="d-flex justify-content-between gap-2 flex-wrap">
              <a href="../index.php?page=complaints" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back
              </a>
              <button type="button" id="editBtn" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i>Edit
              </button>

              <button type="submit" class="btn btn-success">
                <i class="fas fa-save me-1"></i>Update Status
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    const map = L.map('map').setView([14.5995, 120.9842], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Toggle edit mode
    const editBtn = document.getElementById('editBtn');
    const cancelBtn = document.getElementById('cancelEditBtn');
    const editActions = document.getElementById('editActions');
    const viewFields = document.querySelectorAll('.view-mode');
    const editFields = document.querySelectorAll('.edit-mode');

    editBtn.addEventListener('click', () => {
      viewFields.forEach(el => el.classList.add('d-none'));
      editFields.forEach(el => el.classList.remove('d-none'));
      editActions.classList.remove('d-none');
      editBtn.classList.add('d-none');
    });

    cancelBtn.addEventListener('click', () => {
      viewFields.forEach(el => el.classList.remove('d-none'));
      editFields.forEach(el => el.classList.add('d-none'));
      editActions.classList.add('d-none');
      editBtn.classList.remove('d-none');
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
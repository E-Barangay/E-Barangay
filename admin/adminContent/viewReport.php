<?php
include_once __DIR__ . '/../../sharedAssets/connect.php';

$complaintID = $_GET['complaintID'] ?? $_POST['complaintID'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complaintStatus'])) {
  $newStatus = $_POST['complaintStatus'];
  $stmt = $conn->prepare("UPDATE complaints SET complaintStatus = ? WHERE complaintID = ?");
  $stmt->bind_param("si", $newStatus, $complaintID);
  $stmt->execute();
  $stmt->close();

  header("Location: ../index.php?page=complaintsKP");
  exit;
}

// ✅ Updated SELECT query to handle both registered users and manual complainants
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
  <title>Complaint Details</title>
  <link rel="icon" href="../../assets/images/logoSanAntonio.png">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <style>
    body {
      background: #f1f3f5;
    }

    .report-card {
      max-width: 1000px;
      margin: 40px auto;
      padding: 2rem;
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      display: flex;
      gap: 2rem;
      flex-wrap: wrap;
    }

    .report-info {
      flex: 1 1 60%;
    }

    .sidebar-controls {
      flex: 1 1 30%;
      border-left: 2px dashed #dee2e6;
      padding-left: 2rem;
    }

    .status-badge {
      font-size: 0.85rem;
      border-radius: 20px;
      padding: 0.25rem 0.75rem;
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
    <div class="report-card">
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
            <?= !empty($complaint['complaintTitle']) ? $complaint['complaintTitle'] : '<span class="text-muted">No data</span>' ?>
          </p>
          <p><strong>Date Submitted:</strong>
            <?= !empty($complaint['requestDate']) ? $complaint['requestDate'] : '<span class="text-muted">No data</span>' ?>
          </p>
          <p><strong>Complainant:</strong>
            <?= !empty($complaint['complainantName']) ? $complaint['complainantName'] : '<span class="text-muted">No data</span>' ?>
          </p>
          <p><strong>Phone Number:</strong>
            <?= !empty($complaint['complaintPhoneNumber']) ? $complaint['complaintPhoneNumber'] : '<span class="text-muted">No data</span>' ?>
          </p>
          <p><strong>Description:</strong>
            <?= !empty($complaint['complaintDescription']) ? $complaint['complaintDescription'] : '<span class="text-muted">No data</span>' ?>
          </p>
          <p><strong>Action:</strong>
            <?= !empty($complaint['actionTaken']) ? $complaint['actionTaken'] : '<span class="text-muted">No data</span>' ?>
          </p>
        </div>

        <!-- Victim & Perpetrator Info -->
        <div class="row">
          <div class="col-md-6">
            <p><strong>Victim:</strong>
              <?= !empty($complaint['complaintVictim']) ? $complaint['complaintVictim'] : '<span class="text-muted">No data</span>' ?>
            </p>
          </div>
          <div class="col-md-6">
            <p><strong>Victim Age:</strong>
              <?= !empty($complaint['victimAge']) ? $complaint['victimAge'] : '<span class="text-muted">No data</span>' ?>
            </p>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <p><strong>Perpetrator:</strong>
              <?= !empty($complaint['complaintAccused']) ? $complaint['complaintAccused'] : '<span class="text-muted">No data</span>' ?>
            </p>
          </div>
          <div class="col-md-6">
            <p><strong>Relationship:</strong>
              <?= !empty($complaint['victimRelationship']) ? $complaint['victimRelationship'] : '<span class="text-muted">No data</span>' ?>
            </p>
          </div>
        </div>

        <!-- Contact & Evidence -->
        <div class="mb-3">
          <strong>Evidence:</strong>
          <?php if (!empty($complaint['evidence'])): ?>
            <button type="button" class="btn btn-sm btn-primary ms-2" data-bs-toggle="modal"
              data-bs-target="#evidenceModal">
              <i class="fas fa-eye me-1"></i> View Evidence
            </button>
            <div class="modal fade" id="evidenceModal" tabindex="-1" aria-labelledby="evidenceModalLabel"
              aria-hidden="true">
              <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="evidenceModalLabel">Evidence Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body text-center">
                    <img src="../../uploads/<?= htmlspecialchars($complaint['evidence']) ?>" class="img-fluid rounded"
                      alt="Evidence">
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
          <h6 class="fw-semibold mb-2">
            <i class="fas fa-map-marker-alt text-danger me-1"></i> Location Map (Placeholder)
          </h6>
          <div id="map"></div>
        </div>
      </div>

      <div class="sidebar-controls">
        <h6 class="fw-bold mb-3"><i class="fas fa-tools me-1 text-dark"></i>Admin Controls</h6>
        <form method="POST" action="">
          <!-- FIXED typo: complaintID not complainttID -->
          <input type="hidden" name="complaintID" value="<?= $complaint['complaintID'] ?>">

          <div class="mb-3">
            <label for="complaintStatus" class="form-label">Change Status</label>
            <select name="complaintStatus" id="complaintStatus" class="form-select" required>
              <option disabled>-- Select Status --</option>
              <?php foreach (['Criminal', 'Civil', 'Mediation', 'Conciliation', 'Arbitration', 'Repudiated', 'Withdrawn', 'Pending', 'Dismissed', 'Certified'] as $st): ?>
                <option value="<?= $st ?>" <?= $complaint['complaintStatus'] == $st ? 'selected' : '' ?>>
                  <?= $st ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="d-flex justify-content-between">
            <a href="../index.php?page=complaintsKP" class="btn btn-secondary">
              <i class="fas fa-arrow-left me-1"></i>Back
            </a>
            <!-- FIXED: pure submit button, no nested <a> -->
            <button type="submit" class="btn btn-success">
              <i class="fas fa-save me-1"></i>Update Status
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    const map = L.map('map').setView([14.5995, 120.9842], 13); // Placeholder: Manila
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
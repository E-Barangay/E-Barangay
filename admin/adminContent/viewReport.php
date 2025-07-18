<?php
include_once __DIR__ . '/../../sharedAssets/connect.php';

$reportID = $_GET['reportID'] ?? $_POST['reportID'] ?? '';

// 1. Handle status update and redirect
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['requestStatus'])) {
    $newStatus = $_POST['requestStatus'];
    $stmt = $conn->prepare("UPDATE reports SET requestStatus = ? WHERE reportID = ?");
    $stmt->bind_param("si", $newStatus, $reportID);
    $stmt->execute();
    $stmt->close();

    header("Location: ../index.php?page=reports");
    exit;
}

// 2. Fetch report info (now includes phone number)
$stmt = $conn->prepare("SELECT r.reportID, r.requestDate, r.requestStatus, r.reportTitle,
    CONCAT(ui.firstName, ' ', ui.middleName, ' ', ui.lastName) AS requesterName,
    u.phoneNumber
    FROM reports r
    JOIN users u ON r.userID = u.userID
    JOIN userinfo ui ON u.userID = ui.userID
    WHERE r.reportID = ?");
$stmt->bind_param('i', $reportID);
$stmt->execute();
$result = $stmt->get_result();
$report = $result->fetch_assoc();
$stmt->close();

function getStatusBadgeClass($status) {
  return match (strtolower($status)) {
    'pending' => 'bg-warning text-dark',
    'in progress' => 'bg-primary text-white',
    'resolved' => 'bg-success text-white',
    'closed' => 'bg-secondary text-white',
    default => 'bg-light text-dark',
  };
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Report Details</title>
  <link rel="icon" href="../../assets/images/logoSanAntonio.png">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
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
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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
      <!-- Report Info -->
      <div class="report-info">
        <h5 class="fw-bold mb-3"><i class="fas fa-file-alt text-primary me-2"></i>Report Details</h5>

        <p><strong>Report ID:</strong> <?= $report['reportID'] ?></p>
        <p><strong>Date Submitted:</strong> <?= $report['requestDate'] ?></p>
        <p><strong>Requester Name:</strong> <?= $report['requesterName'] ?></p>
        <p><strong>Phone Number:</strong> <?= $report['phoneNumber'] ?></p>
        <p><strong>Report Title:</strong> <?= $report['reportTitle'] ?></p>
        <p><strong>Status:</strong> 
          <span class="badge <?= getStatusBadgeClass($report['requestStatus']) ?> status-badge">
            <?= ucfirst($report['requestStatus']) ?>
          </span>
        </p>

        <div class="mt-4">
          <h6 class="fw-semibold mb-2"><i class="fas fa-map-marker-alt text-danger me-1"></i>Location Map (Placeholder)</h6>
          <div id="map"></div>
        </div>
      </div>

      <!-- Admin Controls -->
      <div class="sidebar-controls">
        <h6 class="fw-bold mb-3"><i class="fas fa-tools me-1 text-dark"></i>Admin Controls</h6>
        <form method="POST" action="">
          <input type="hidden" name="reportID" value="<?= $report['reportID'] ?>">

          <div class="mb-3">
            <label for="requestStatus" class="form-label">Change Status</label>
            <select name="requestStatus" id="requestStatus" class="form-select" required>
              <option disabled>-- Select Status --</option>
              <option value="Pending" <?= $report['requestStatus'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
              <option value="In Progress" <?= $report['requestStatus'] == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
              <option value="Resolved" <?= $report['requestStatus'] == 'Resolved' ? 'selected' : '' ?>>Resolved</option>
              <option value="Closed" <?= $report['requestStatus'] == 'Closed' ? 'selected' : '' ?>>Closed</option>
            </select>
          </div>

          <div class="d-flex justify-content-between">
            <a href="../index.php?page=reports" class="btn btn-secondary">
              <i class="fas fa-arrow-left me-1"></i>Back
            </a>
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
</body>
</html>

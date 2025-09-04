<?php
include_once __DIR__ . '/../../sharedAssets/connect.php';

$complaintID = $_GET['complaintID'] ?? $_POST['complaintID'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complaintStatus'])) {
  $newStatus = $_POST['complaintStatus'];
  $stmt = $conn->prepare("UPDATE complaints SET complaintStatus = ? WHERE complaintID = ?");
  $stmt->bind_param("si", $newStatus, $complaintID);
  $stmt->execute();
  $stmt->close();

  header("Location: ../index.php?page=complaints");
  exit;
}

$stmt = $conn->prepare("SELECT r.complaintID, r.requestDate, r.complaintStatus, r.complaintTitle,
    CONCAT(ui.firstName, ' ', ui.middleName, ' ', ui.lastName) AS requesterName,
    phoneNumber
    FROM complaints r
    JOIN users u ON r.userID = u.userID
    JOIN userinfo ui ON u.userID = ui.userID
    WHERE r.complaintID = ?");
$stmt->bind_param('i', $complaintID);
$stmt->execute();
$result = $stmt->get_result();
$complaint = $result->fetch_assoc();
$stmt->close();

function getStatusBadgeClass($status)
{
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
        <h5 class="fw-bold mb-3"><i class="fas fa-file-alt text-primary me-2"></i>Complaint Details</h5>

        <p><strong>Complaint ID:</strong> <?= $complaint['complaintID'] ?></p>
        <p><strong>Date Submitted:</strong> <?= $complaint['requestDate'] ?></p>
        <p><strong>Requester Name:</strong> <?= $complaint['requesterName'] ?></p>
        <p><strong>Phone Number:</strong> <?= $complaint['phoneNumber'] ?></p>
        <p><strong>Complaint Title:</strong> <?= $complaint['complaintTitle'] ?></p>
        <p><strong>Status:</strong>
          <span class="badge <?= getStatusBadgeClass($complaint['complaintStatus']) ?> status-badge">
            <?= ucfirst($complaint['complaintStatus']) ?>
          </span>
        </p>

        <div class="mt-4">
          <h6 class="fw-semibold mb-2"><i class="fas fa-map-marker-alt text-danger me-1"></i>Location Map (Placeholder)
          </h6>
          <div id="map"></div>
        </div>
      </div>

      <div class="sidebar-controls">
        <h6 class="fw-bold mb-3"><i class="fas fa-tools me-1 text-dark"></i>Admin Controls</h6>
        <form method="POST" action="">
          <input type="hidden" name="complainttID" value="<?= $complaint['complaintID'] ?>">

          <div class="mb-3">
            <label for="complaintStatus" class="form-label">Change Status</label>
            <select name="complaintStatus" id="complaintStatus" class="form-select" required>
              <option disabled>-- Select Status --</option>
              <option value="Pending" <?= $complaint['complaintStatus'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
              <option value="In Progress" <?= $complaint['complaintStatus'] == 'In Progress' ? 'selected' : '' ?>>In
                Progress
              </option>
              <option value="Resolved" <?= $complaint['complaintStatus'] == 'Resolved' ? 'selected' : '' ?>>Resolved
              </option>
              <option value="Closed" <?= $complaint['complaintStatus'] == 'Closed' ? 'selected' : '' ?>>Closed</option>
            </select>
          </div>

          <div class="d-flex justify-content-between">
            <a href="../index.php?page=complaintsKP" class="btn btn-secondary">
              <i class="fas fa-arrow-left me-1"></i>Back
            </a>
            <button type="submit" class="btn btn-success">
              <a href="../index.php?page=complaintsKP" class="btn btn-success">
                <i class="fas fa-save me-1"></i>Update Status
              </a>
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
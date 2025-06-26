<?php
include("../../sharedAssets/connect.php");

// Get document ID from query
$docID = $_GET['documentID'] ?? 0;
$message = "";

// Update status logic
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $newStatus = $_POST['documentStatus'];
  $approvalDate = in_array($newStatus, ['Approved', 'Denied']) ? date('Y-m-d') : NULL;

  $stmt = $conn->prepare("UPDATE documents SET documentStatus = ?, approvalDate = ? WHERE documentID = ?");
  $stmt->bind_param("ssi", $newStatus, $approvalDate, $docID);

  if ($stmt->execute()) {
    header("Location: ../index.php?page=document&updated=1");
    exit();
  } else {
    $message = "Error updating status.";
  }
}

// Fetch document details
$stmt = $conn->prepare("SELECT d.*, CONCAT(ui.firstName, ' ', ui.middleName, ' ', ui.lastName) AS fullname, u.phoneNumber
                        FROM documents d 
                        JOIN users u ON d.userID = u.userID
                        JOIN userinfo ui ON u.userID = ui.userID
                        WHERE d.documentID = ?");
$stmt->bind_param("i", $docID);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Document</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f4f4f4;
      font-family: 'Poppins', sans-serif;
    }
    .modal-style {
      max-width: 900px;
      margin: 40px auto;
      background: white;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
      display: flex;
      overflow: hidden;
    }
    .modal-left {
      flex: 2;
      padding: 30px;
      border-right: 1px solid #ddd;
    }
    .modal-right {
      flex: 1;
      padding: 30px;
      background: #f9f9f9;
    }
    .section-title {
      font-weight: 600;
      margin-bottom: 10px;
      color: #333;
    }
    .label {
      font-weight: 500;
      color: #555;
    }
  </style>
</head>
<body>

<div class="container">
  <?php if ($message): ?>
    <div class="alert alert-danger mt-3"><?= $message ?></div>
  <?php endif; ?>

  <?php if ($data): ?>
  <div class="modal-style">
    <!-- Left: Details -->
    <div class="modal-left">
      <h4 class="mb-4"><i class="fas fa-file-alt me-2 text-primary"></i>Document Request Details</h4>
      <p><span class="label">Report ID:</span> <?= htmlspecialchars($data['documentID']) ?></p>
      <p><span class="label">Date Submitted:</span> <?= htmlspecialchars($data['requestDate']) ?></p>
      <p><span class="label">Requester Name:</span> <?= htmlspecialchars($data['fullname']) ?></p>
      <p><span class="label">Purpose:</span> <?= htmlspecialchars($data['purpose']) ?></p>
      <p><span class="label">Contact:</span> <?= htmlspecialchars($data['phoneNumber']) ?></p>
      <p><span class="label">Current Status:</span> 
        <span class="badge 
          <?= match($data['documentStatus']) {
            'Pending' => 'bg-warning text-dark',
            'Approved' => 'bg-success',
            'Denied' => 'bg-danger',
            default => 'bg-secondary'
          } ?>
        "><?= htmlspecialchars($data['documentStatus']) ?></span>
      </p>
    </div>

    <!-- Right: Manage Status -->
    <div class="modal-right">
      <h5 class="section-title"><i class="fas fa-sliders-h me-1 text-primary"></i>Manage Status</h5>
      <form method="POST">
        <div class="mb-3">
          <label for="documentStatus" class="form-label">Update Status:</label>
          <select class="form-select" name="documentStatus" id="documentStatus" required>
            <?php
              $statuses = ['Pending', 'Approved', 'Denied' ];
              foreach ($statuses as $status):
            ?>
              <option value="<?= $status ?>" <?= $data['documentStatus'] === $status ? 'selected' : '' ?>>
                <?= $status ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="d-flex justify-content-between mt-4">
          <a href="../index.php?page=document" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
          </a>
          <button type="submit" class="btn btn-success">
            <i class="fas fa-check"></i> Update Status
          </button>
        </div>
      </form>
    </div>
  </div>
  <?php else: ?>
    <div class="alert alert-warning mt-4">Document not found or invalid ID.</div>
  <?php endif; ?>
</div>

</body>
</html>

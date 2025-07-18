<?php
include_once __DIR__ . '/../../sharedAssets/connect.php';

// Handle status update (Done / Denied)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['documentID'])) {
  $documentID = $_POST['documentID'];
  $newStatus = $_POST['action'] === 'done' ? 'Approved' : 'Denied';

  $updateQuery = "UPDATE documents SET documentStatus = ? WHERE documentID = ?";
  $stmt = $pdo->prepare($updateQuery);
  $stmt->execute([$newStatus, $documentID]);

  header("Location: index.php?page=document");
  exit();
}

// Filters
$search = $_GET['search'] ?? '';
$statusFilter = $_GET['status'] ?? '';
$dateFilter = $_GET['date'] ?? '';

$documentsQuery = "SELECT 
    d.documentID, 
    d.purpose, 
    d.documentStatus, 
    d.requestDate,
    CONCAT(ui.firstName, ' ', ui.middleName, ' ', ui.lastName) AS fullname,
    u.phoneNumber
FROM documents d
JOIN users u ON d.userID = u.userID
JOIN userinfo ui ON u.userID = ui.userID
WHERE 1";

if ($search != '') {
  $documentsQuery .= " AND (
        CAST(d.documentID AS CHAR) LIKE '%$search%' OR 
        ui.firstName LIKE '%$search%' OR 
        ui.middleName LIKE '%$search%' OR 
        ui.lastName LIKE '%$search%'
    )";
}

if ($statusFilter != '' && $statusFilter != 'All') {
  $documentsQuery .= " AND d.documentStatus = '$statusFilter'";
}

if ($dateFilter != '') {
  $documentsQuery .= " AND DATE(d.requestDate) = '$dateFilter'";
}

$documentsQuery .= " ORDER BY d.requestDate DESC";

$documentsResults = executeQuery($documentsQuery);
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
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f0f0f0;
    }
  </style>
</head>

<body>

  <div class="container-fluid p-3 p-md-4">
    <div class="card shadow border-0 rounded-3">
      <div class="card-body p-0">
        <!-- Header -->
        <div class="text-white p-4 rounded-top" style="background-color: #31afab;">
          <div class="d-flex align-items-center">
            <i class="fas fa-file-alt me-3 fs-4"></i>
            <h1 class="h4 mb-0 fw-semibold">Document Requests Management</h1>
          </div>
        </div>

        <!-- Filter Form -->
        <div class="p-3 p-md-4">
          <form method="GET" action="index.php">
            <input type="hidden" name="page" value="document">
            <div class="card shadow-sm mb-4">
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-lg-4 col-md-6">
                    <div class="input-group">
                      <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-search text-muted"></i>
                      </span>
                      <input type="text" class="form-control border-start-0" name="search" placeholder="Search User"
                        value="<?= htmlspecialchars($search) ?>">
                    </div>
                  </div>

                  <div class="col-lg-3 col-md-6">
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

          <!-- Table -->
          <div class="card shadow-sm">
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th class="px-4 py-3">Date Submitted</th>
                      <th class="px-4 py-3">Requester Name</th>
                      <th class="px-4 py-3">Purpose</th>
                      <th class="px-4 py-3">Contact</th>
                      <th class="px-4 py-3">Status</th>
                      <th class="px-4 py-3">Action</th>
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
                          <td class="px-4 py-3"><?= htmlspecialchars($row['requestDate']) ?></td>
                          <td class="px-4 py-3"><?= htmlspecialchars($row['fullname']) ?></td>
                          <td class="px-4 py-3"><span
                              class="badge bg-info text-white"><?= htmlspecialchars($row['purpose']) ?></span></td>
                          <td class="px-4 py-3"><?= htmlspecialchars($row['phoneNumber']) ?></td>
                          <td class="px-4 py-3"><span
                              class="badge rounded-pill <?= $badgeClass ?>"><?= $row['documentStatus'] ?></span></td>
                          <td class="px-4 py-3 d-flex gap-2">
                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal"
                              data-id="<?= $row['documentID'] ?>" data-name="<?= htmlspecialchars($row['fullname']) ?>"
                              data-number="<?= htmlspecialchars($row['phoneNumber']) ?>"
                              data-purpose="<?= htmlspecialchars($row['purpose']) ?>">
                              <i class="fas fa-check"></i>
                            </button>
                            <form method="POST" style="display:inline;">
                              <input type="hidden" name="documentID" value="<?= $row['documentID'] ?>">
                              <input type="hidden" name="action" value="deny">
                              <button class="btn btn-sm btn-danger" type="submit">
                                <i class="fas fa-times"></i>
                              </button>
                            </form>
                          </td>
                        </tr>
                      <?php endwhile; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="6" class="text-center py-4">No document requests found.</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form method="POST" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="approveLabel">Confirm Approval</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p><strong>Name:</strong> <span id="modalName"></span></p>
          <p><strong>Number:</strong> <span id="modalNumber"></span></p>
          <p><strong>Purpose:</strong> <span id="modalPurpose"></span></p>
          <p class="mt-3">Are you sure you want to mark this as <strong>Done</strong>?</p>
          <input type="hidden" name="documentID" id="modalDocumentID">
          <input type="hidden" name="action" value="done">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Confirm</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    document.getElementById('approveModal').addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      document.getElementById('modalDocumentID').value = button.getAttribute('data-id');
      document.getElementById('modalName').textContent = button.getAttribute('data-name');
      document.getElementById('modalNumber').textContent = button.getAttribute('data-number');
      document.getElementById('modalPurpose').textContent = button.getAttribute('data-purpose');
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
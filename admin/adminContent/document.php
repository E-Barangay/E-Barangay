<?php
<<<<<<< Updated upstream
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../index.php");
  exit();
}

=======
include_once __DIR__ . '/../../sharedAssets/connect.php';

$search = trim($_GET['search'] ?? '');
$statusFilter = $_GET['status'] ?? '';
$dateFilter = $_GET['date'] ?? '';

$sql = "SELECT 
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

$params = [];
$types = '';

// Add filters
if (!empty($search)) {
    $sql .= " AND (
        CAST(d.documentID AS CHAR) LIKE ?
        OR ui.firstName LIKE ?
        OR ui.middleName LIKE ?
        OR ui.lastName LIKE ?
    )";

    $searchParam = "%$search%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= 'ssss';
}
if (!empty($statusFilter) && $statusFilter !== 'All') {
    $sql .= " AND d.documentStatus = ?";
    $params[] = $statusFilter;
    $types .= 's';
}
if (!empty($dateFilter)) {
    $sql .= " AND DATE(d.requestDate) = ?";
    $params[] = $dateFilter;
    $types .= 's';
}

$sql .= " ORDER BY d.requestDate DESC";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
>>>>>>> Stashed changes
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Document Requests</title>
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
<form method="GET" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
  <input type="hidden" name="page" value="document">
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-lg-4 col-md-6">
          <div class="input-group">
            <span class="input-group-text bg-white border-end-0">
              <i class="fas fa-search text-muted"></i>
            </span>
            <input type="text" class="form-control border-start-0" name="search" placeholder="Search User" value="<?= htmlspecialchars($search) ?>">
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
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

        <!-- Table -->
        <div class="card shadow-sm">
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th class="px-4 py-3 fw-semibold">Report ID</th>
                    <th class="px-4 py-3 fw-semibold">Date Submitted</th>
                    <th class="px-4 py-3 fw-semibold">Requester Name</th>
                    <th class="px-4 py-3 fw-semibold">Purpose</th>
                    <th class="px-4 py-3 fw-semibold">Contact</th>
                    <th class="px-4 py-3 fw-semibold">Status</th>
                    <th class="px-4 py-3 fw-semibold">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                      <tr>
                        <td class="px-4 py-3"><strong><?= $row['documentID'] ?></strong></td>
                        <td class="px-4 py-3"><?= htmlspecialchars($row['requestDate']) ?></td>
                        <td class="px-4 py-3"><?= htmlspecialchars($row['fullname']) ?></td>
                        <td class="px-4 py-3"><span class="badge bg-info text-white"><?= htmlspecialchars($row['purpose']) ?></span></td>
                        <td class="px-4 py-3"><?= htmlspecialchars($row['phoneNumber']) ?></td>
                        <td class="px-4 py-3">
                          <?php
                            $status = $row['documentStatus'];
                            $badgeClass = match ($status) {
                              'Pending' => 'bg-warning text-dark',
                              'Approved' => 'bg-success',
                              'Denied' => 'bg-danger',
                              default => 'bg-secondary'
                            };
                          ?>
                          <span class="badge rounded-pill <?= $badgeClass ?>"><?= $status ?></span>
                        </td>
                        <td class="px-4 py-3">
                          <a href="adminContent/viewDocument.php?documentID=<?= $row['documentID'] ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye me-1"></i>View
                          </a>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="7" class="text-center py-4">No document requests found.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div> <!-- end table card -->

      </div> <!-- end p-md-4 -->
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

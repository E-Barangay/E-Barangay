<?php
include_once __DIR__ . '/../../sharedAssets/connect.php';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
  $newStatus = $_POST['status'] ?? '';
  $reportID = $_POST['reportID'] ?? '';

  if ($newStatus && $reportID) {
    $updateStmt = $conn->prepare("UPDATE reports SET requestStatus = ? WHERE reportID = ?");
    $updateStmt->bind_param('si', $newStatus, $reportID);
    $updateStmt->execute();
    $updateStmt->close();
  }
  // Redirect to avoid resubmission
  header("Location: index.php?page=reports&search=" . urlencode($_GET['search'] ?? '') . "&status=" . urlencode($_GET['status'] ?? '') . "&date=" . urlencode($_GET['date'] ?? ''));
  exit;
}

// Filters
$search = trim($_GET['search'] ?? '');
$statusFilter = $_GET['status'] ?? '';
$dateFilter = $_GET['date'] ?? '';

// Query
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
  $searchTerm = "%$search%";
  $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
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
?>
<div class="container-fluid p-3 p-md-4">
  <div class="card shadow-lg border-0 rounded-3">
    <div class="card-body p-0">
      <div class="text-white p-4 rounded-top bg-info">
        <div class="d-flex align-items-center">
          <i class="fas fa-users-cog me-3 fs-4"></i>
          <h1 class="h4 mb-0 fw-semibold">Community Concerns Management</h1>
        </div>
      </div>
      <div class="p-3 p-md-4">
        <!-- Filter Form -->
        <form method="GET" action="index.php" class="mb-4">
          <input type="hidden" name="page" value="reports">
          <div class="row g-3">
            <div class="col-md-4">
              <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control border-start-0" placeholder="Search Reporter, ID, or Title">
              </div>
            </div>
            <div class="col-md-3">
              <select name="status" class="form-select">
                <option value="All" <?= $statusFilter === '' || $statusFilter === 'All' ? 'selected' : '' ?>>All Status</option>
                <?php foreach (['Pending','In Progress','Resolved','Closed'] as $st): ?>
                  <option value="<?= $st ?>" <?= $statusFilter === $st ? 'selected' : '' ?>><?= $st ?></option>
                <?php endforeach; ?>
              </select>
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

        <!-- Table -->
        <div class="card shadow-sm">
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Report ID</th>
                    <th>Date Submitted</th>
                    <th>Reporter Name</th>
                    <th>Report Type</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th>Change Status</th>
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
                      <td>
                        <span class="badge <?= getStatusBadgeClass($row['status']) ?>"><?= htmlspecialchars($row['status']) ?></span>
                      </td>
                      <td>
                        <form method="POST" action="index.php?page=reports">
                          <input type="hidden" name="reportID" value="<?= $row['concernID'] ?>">
                          <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                            <?php foreach (['Pending','In Progress','Resolved','Closed'] as $status): ?>
                              <option value="<?= $status ?>" <?= $status === $row['status'] ? 'selected' : '' ?>><?= $status ?></option>
                            <?php endforeach; ?>
                          </select>
                          <input type="hidden" name="update_status" value="1">
                        </form>
                      </td>
                      <td>
                        <a href="viewReport.php?reportID=<?= $row['concernID'] ?>" class="btn btn-sm btn-primary">
                          <i class="fas fa-eye"></i>
                        </a>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr><td colspan="8" class="text-center py-4 text-muted">No concerns found.</td></tr>
                <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div> <!-- /card -->
      </div> <!-- /p-4 -->
    </div>
  </div>
</div>

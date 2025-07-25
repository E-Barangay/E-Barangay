<?php
include_once __DIR__ . '/../../sharedAssets/connect.php';

$search = trim($_GET['search'] ?? '');
$status = $_GET['status'] ?? '';
$date = $_GET['date'] ?? '';

$sql = "SELECT 
  r.reportID AS concernID,
  r.requestDate,
  CONCAT(ui.firstName, ' ', ui.middleName, ' ', ui.lastName) AS reporterName,
  r.reportTitle AS concernType,
  u.phoneNumber,
  r.requestStatus AS status
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
  $term = "%$search%";
  $params = array_merge($params, [$term, $term, $term]);
  $types .= 'sss';
}

if ($status !== '' && $status !== 'All') {
  $sql .= " AND r.requestStatus = ?";
  $params[] = $status;
  $types .= 's';
}

if ($date !== '') {
  $sql .= " AND DATE(r.requestDate) = ?";
  $params[] = $date;
  $types .= 's';
}

$sql .= " ORDER BY r.requestDate DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
  $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

function getStatusBadgeClass($status)
{
  return match ($status) {
    'Closed' => 'bg-secondary text-white',
    'Resolved' => 'bg-success text-white',
    'In Progress' => 'bg-primary text-white',
    default => 'bg-warning text-dark',
  };
}

function getBorderClass($status)
{
  return match ($status) {
    'Closed' => 'border-secondary',
    'Resolved' => 'border-success',
    'In Progress' => 'border-primary',
    default => 'border-warning',
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
        <div class="p-4 rounded-top bg-custom">
          <div class="d-flex align-items-center">
            <i class="fas fa-users-cog me-3 fs-4"></i>
            <h1 class="h4 mb-0 fw-semibold">Community Concerns Management</h1>
          </div>
        </div>

        <div class="p-3 p-md-4">
          <form method="GET" action="index.php" class="mb-4">
            <input type="hidden" name="page" value="reports">
            <div class="row g-3">
              <div class="col-md-4">
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
                  <?php foreach (['Pending', 'In Progress', 'Resolved', 'Closed'] as $st): ?>
                    <option value="<?= $st ?>" <?= $status === $st ? 'selected' : '' ?>><?= $st ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-3">
                <input type="date" name="date" value="<?= htmlspecialchars($date) ?>" class="form-control">
              </div>

              <div class="col-md-2">
                <button type="submit" class="btn btn-custom w-100">
                  <i class="fas fa-filter me-2"></i>Filter
                </button>
              </div>
            </div>
          </form>

          <div class="card shadow-sm">
            <div class="card-body p-0">
              <div class="table-responsive d-none d-lg-block">
                <table class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Report ID</th>
                      <th>Date Submitted</th>
                      <th>Reporter Name</th>
                      <th>Report Type</th>
                      <th>Contact</th>
                      <th>Status</th>
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
                          <td><span
                              class="badge <?= getStatusBadgeClass($row['status']) ?>"><?= htmlspecialchars($row['status']) ?></span>
                          </td>
                          <td>
                            <a href="adminContent/viewReport.php?reportID=<?= $row['concernID'] ?>"
                              class="btn btn-sm btn-primary" title="View Details">
                              <i class="fas fa-eye"></i>
                            </a>
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

              <div class="d-lg-none p-3" style="max-height:70vh;overflow-y:auto;">
                <?php
                $result->data_seek(0);
                if ($result->num_rows > 0):
                  while ($row = $result->fetch_assoc()): ?>
                    <div class="card mb-3 border-start border-4 <?= getBorderClass($row['status']) ?>">
                      <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                          <h6 class="fw-bold mb-0">Report ID: <?= htmlspecialchars($row['concernID']) ?></h6>
                          <span
                            class="badge <?= getStatusBadgeClass($row['status']) ?>"><?= htmlspecialchars($row['status']) ?></span>
                        </div>
                        <div class="small">
                          <p class="mb-1"><strong>Date:</strong> <?= date('M d, Y', strtotime($row['requestDate'])) ?></p>
                          <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($row['reporterName']) ?></p>
                          <p class="mb-1"><strong>Report:</strong> <?= htmlspecialchars($row['concernType']) ?></p>
                          <p class="mb-2"><strong>Contact:</strong> <?= htmlspecialchars($row['phoneNumber']) ?></p>
                          <a href="adminContent/viewReport.php?reportID=<?= $row['concernID'] ?>"
                            class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-eye me-2"></i>View Details
                          </a>
                        </div>
                      </div>
                    </div>
                  <?php endwhile; else: ?>
                  <div class="text-center py-4 text-muted">
                    <i class="fas fa-inbox fs-1 mb-3"></i>
                    <p>No concerns found.</p>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>
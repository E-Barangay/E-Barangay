<?php
<<<<<<< Updated upstream
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../index.php");
  exit();
}

=======
include_once __DIR__ . '/../../sharedAssets/connect.php';

$search = $_GET['search'] ?? '';
if (!empty($search)) {
    $searchQuery = "SELECT * FROM announcements WHERE title LIKE ? OR description LIKE ? ORDER BY dateTime DESC";
    $searchStmt = $pdo->prepare($searchQuery);
    $searchStmt->execute(["%$search%", "%$search%"]);
    $announcements = $searchStmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $fetchQuery = "SELECT * FROM announcements ORDER BY dateTime DESC";
    $fetchStmt = $pdo->prepare($fetchQuery);
    $fetchStmt->execute();
    $announcements = $fetchStmt->fetchAll(PDO::FETCH_ASSOC);
}
>>>>>>> Stashed changes
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Announcement Details</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.6/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: rgb(233, 233, 233);
      color: dark;
      height: 100vh;
      margin: 0;
      padding: 0;
    }
    .important-badge {
      font-size: 0.75rem;
      padding: 0.25rem 0.5rem;
      border-radius: 0.375rem;
      background-color: #f8f9fa;
      color: #6c757d;
    }
    .important-badge.bg-warning {
      background-color: #ffc107 !important;
      color: #000 !important;
    }
  </style>
</head>

<body>
  <div class="container-fluid p-3 p-md-4">
    <div class="card shadow-lg border-0 rounded-3">
      <div class="card-body p-0">
        <div class="text-white p-4 rounded-top" style="background-color: rgb(49, 175, 171);">
          <div class="d-flex align-items-center">
            <i class="fas fa-bullhorn me-3 fs-4"></i>
            <h1 class="h4 mb-0 fw-semibold">Announcement Details</h1>
          </div>
        </div>

        <div class="p-3 p-md-4">
          <!-- Table View -->
          <div class="card shadow-sm mb-4">
            <div class="card-body">
              <div class="row g-3">
                <div class="col-lg-6 col-md-6">
                  <form method="GET">
                    <div class="input-group">
                      <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-search text-muted"></i>
                      </span>
                      <input type="text" class="form-control border-start-0" name="search"
                             placeholder="Search Announcement" value="<?php echo ($search ?? ''); ?>">
                    </div>
                  </form>
                </div>
                <div class="col-lg-6 col-md-6 d-flex justify-content-md-end">
<a href="adminContent/addAnnouncement.php" class="btn btn-primary" style="background-color: rgb(49, 175, 171); border: none;">
    <i class="fas fa-plus me-2"></i> Add Announcement
</a>
                </div>
              </div>
            </div>
          </div>

          <!-- Main Table -->
          <div class="card shadow-sm">
            <div class="card-body p-0">
              <div class="d-none d-lg-block">
                <div class="table-responsive">
                  <table class="table table-hover mb-0 border">
                    <thead class="table-light">
                      <tr>
                        <th class="px-4 py-3 fw-semibold">ID</th>
                        <th class="px-4 py-3 fw-semibold">Title</th>
                        <th class="px-4 py-3 fw-semibold">Date</th>
                        <th class="px-4 py-3 fw-semibold">Description</th>
                        <th class="px-4 py-3 fw-semibold">Image</th>
                        <th class="px-4 py-3 fw-semibold">Action</th>
                        <th class="px-4 py-3 fw-semibold">Important</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($announcements as $a): ?>
                        <tr>
                          <td class="px-4 py-3"><?php echo $a['announcementID']; ?></td>
                          <td class="px-4 py-3"><?php echo ($a['title']); ?></td>
                          <td class="px-4 py-3"><?php echo date('Y-m-d', strtotime($a['dateTime'])); ?></td>
                          <td class="px-4 py-3">
                            <span class="d-inline-block text-truncate" style="max-width: 180px;">
                              <?php echo ($a['description']); ?>
                            </span>
                          </td>
<td class="px-4 py-3">
  <?php if (!empty($a['image'])): ?>
    <img src="/e-baranggay/E-Barangay/<?php echo $a['image']; ?>" 
         width="50" height="50" 
         class="rounded" style="object-fit: cover;">
  <?php else: ?>
    <span class="text-muted">No image</span>
  <?php endif; ?>
</td>
                          <td class="px-4 py-3">
<a href="adminContent/editAnnouncement.php?id=<?php echo $a['announcementID']; ?>" class="btn btn-warning btn-sm me-1">
    <i class="fas fa-edit me-1"></i> Edit
</a>
                            <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $a['announcementID']; ?>">
                              <i class="fas fa-trash me-1"></i>Delete
                            </button>
                          </td>
  <td class="px-4 py-3 text-center">
  <?php if ($a['isImportant']): ?>
    <span class="badge bg-primary">Important</span>
  <?php else: ?>
    <span class="text-muted">—</span>
  <?php endif; ?>
</td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Mobile View -->
              <div class="d-lg-none">
                <div style="max-height: 70vh; overflow-y: auto;" class="p-3">
                  <?php foreach ($announcements as $a): ?>
                    <div class="card mb-3 border-start <?php echo $a['isImportant'] ? 'border-warning' : ''; ?> border-4">
                      <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                          <h6 class="card-title mb-0 fw-bold">ID: <?php echo $a['announcementID']; ?></h6>
                          <span class="badge bg-info"><?php echo ($a['title']); ?></span>
                        </div>
                        <div class="small mb-2"><strong>Date:</strong> <?php echo $a['dateTime']; ?></div>
                        <div class="mb-2">
                          <strong>Description:</strong>
                          <p class="mb-1"><?php echo ($a['description']); ?></p>
                        </div>
                        <?php if ($a['image']): ?>
                          <img src="<?php echo $a['image']; ?>" class="img-fluid rounded mb-2" style="max-height: 150px;" alt="Image">
                        <?php endif; ?>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                          <div>
<a href="adminContent/editAnnouncement.php?id=<?php echo $a['announcementID']; ?>" class="btn btn-warning btn-sm me-1">
    <i class="fas fa-edit me-1"></i> Edit
</a>
                            <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $a['announcementID']; ?>">
                              <i class="fas fa-trash me-1"></i>Delete
                            </button>
                          </div>
    <div class="form-check">
  <?php if ($a['isImportant']): ?>
    <span class="badge bg-primary">Important</span>
  <?php else: ?>
    <span class="text-muted small">—</span>
  <?php endif; ?>
</div>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>

            <div class="card-footer bg-light">
              <nav class="d-flex justify-content-center">
                <ul class="pagination pagination-sm mb-0">
                  <li class="page-item disabled"><a class="page-link"><i class="fas fa-chevron-left"></i></a></li>
                  <li class="page-item active"><a class="page-link" href="#">1</a></li>
                  <li class="page-item"><a class="page-link"><i class="fas fa-chevron-right"></i></a></li>
                </ul>
              </nav>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.6/js/bootstrap.bundle.min.js"></script>
    
<script>
</script>
</body>

</html>
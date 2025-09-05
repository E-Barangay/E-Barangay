<?php
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
      height: 100vh;
      margin: 0;
      padding: 0;
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
                             placeholder="Search Announcement" value="<?php echo htmlspecialchars($search ?? ''); ?>">
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
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($announcements as $a): ?>
                        <tr>
                          <td class="px-4 py-3"><?php echo htmlspecialchars($a['announcementID']); ?></td>
                          <td class="px-4 py-3"><?php echo htmlspecialchars($a['title']); ?></td>
                          <td class="px-4 py-3"><?php echo date('Y-m-d', strtotime($a['dateTime'])); ?></td>
                          <td class="px-4 py-3">
                            <span class="d-inline-block text-truncate" style="max-width: 180px;">
                              <?php echo htmlspecialchars($a['description']); ?>
                            </span>
                          </td>
                          <td class="px-4 py-3">
                            <?php if (!empty($a['image'])): ?>
                              <img src="../assets/images/announcements/<?php echo $a['image'] ?>" 
                                   width="50" height="50" 
                                   class="rounded" style="object-fit: cover;" 
                                   alt="Announcement Image">
                            <?php else: ?>
                              <span class="text-muted">No image</span>
                            <?php endif; ?>
                          </td>
                          <td class="px-4 py-3">
                            <div class="d-flex flex-column gap-2">
                              <a href="adminContent/editAnnouncement.php?id=<?php echo htmlspecialchars($a['announcementID']); ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i> Edit
                              </a>
                              <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo htmlspecialchars($a['announcementID']); ?>">
                                <i class="fas fa-trash me-1"></i> Delete
                              </button>
                            </div>
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
      <div class="card mb-3 border-start border-4 shadow-sm">
        <div class="card-body pt-3">

          <h5 class="fw-bold mb-2"><?php echo htmlspecialchars($a['title']); ?></h5>

          <div class="small text-muted mb-1">
            <strong>Date:</strong> <?php echo htmlspecialchars($a['dateTime']); ?>
          </div>

          <!-- Description -->
          <div class="mb-2">
            <strong>Description:</strong>
            <p class="mb-1 small"><?php echo htmlspecialchars($a['description']); ?></p>
          </div>

          <!-- Image -->
          <div class="mb-2">
            <?php if (!empty($a['image'])): ?>
              <img src="../assets/images/announcements/<?php echo $a['image'] ?>" 
                   class="rounded w-100" style="object-fit: cover; max-height: 200px;" 
                   alt="Announcement Image">
            <?php else: ?>
              <span class="text-muted">No image</span>
            <?php endif; ?>
          </div>

          <!-- Buttons -->
          <div class="d-flex flex-column gap-2 mt-3">
            <a href="adminContent/editAnnouncement.php?id=<?php echo htmlspecialchars($a['announcementID']); ?>" class="btn btn-warning btn-sm w-100">
              <i class="fas fa-edit me-1"></i> Edit
            </a>
            <button class="btn btn-danger btn-sm w-100 delete-btn" data-id="<?php echo htmlspecialchars($a['announcementID']); ?>">
              <i class="fas fa-trash me-1"></i> Delete
            </button>
          </div>

        </div>
      </div>
    <?php endforeach; ?>
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
    // Add delete functionality if needed
    document.querySelectorAll('.delete-btn').forEach(button => {
      button.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        if (confirm('Are you sure you want to delete this announcement?')) {
          // Add your delete logic here
          console.log('Delete announcement with ID:', id);
        }
      });
    });
  </script>
</body>
</html>

<?php
include_once __DIR__ . '/../../sharedAssets/connect.php';

// ---------- SAVE ANNOUNCEMENT ----------
if (isset($_POST['save_announcement'])) {
  $title = $_POST['title'];
  $description = $_POST['description'];
  $dateTime = $_POST['dateTime'];
  $image = '';

  // upload image if available
  if (!empty($_FILES['image']['name'])) {
    $uploadDir = __DIR__ . '/../../assets/images/announcements/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $fileName = time() . '_' . basename($_FILES['image']['name']);
    $targetFile = $uploadDir . $fileName;
    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
      $image = $fileName;
    } else {
      echo "<script>alert('Image upload failed!');</script>";
    }
  }

  // insert query
  $query = "INSERT INTO announcements (title, description, dateTime, image) 
              VALUES ('$title', '$description', '$dateTime', '$image')";
  $result = mysqli_query($conn, $query);
  if ($result) {
    echo "<script>window.location.href='?page=announcement';</script>";
    exit;
  } else {
    echo "<script>alert('Failed to save announcement.');</script>";
  }
}

// ---------- FETCH ANNOUNCEMENTS ----------
$search = $_GET['search'] ?? '';
if (!empty($search)) {
  $search = mysqli_real_escape_string($conn, $search);
  $query = "SELECT * FROM announcements 
              WHERE title LIKE '%$search%' OR description LIKE '%$search%' 
              ORDER BY announcementID DESC";
} else {
  $query = "SELECT * FROM announcements ORDER BY announcementID DESC";
}
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Announcement Details</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.6/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: rgb(233, 233, 233);
    }
  </style>
</head>

<body>
  <div class="container-fluid p-3 p-md-4">
    <div class="card shadow-lg border-0 rounded-3">
      <div class="card-body p-0">
        <div class="text-white p-4 rounded-top" style="background-color:#31afab;">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center">
              <i class="fas fa-file-alt me-3 fs-4"></i>
              <h1 class="h4 mb-0 fw-semibold">Announcement Details</h1>
            </div>
            <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">
              <i class="fas fa-plus me-2"></i>Add Announcement
            </button>
          </div>
        </div>

        <div class="p-3 p-md-4">
          <div class="card shadow-sm mb-4">
            <div class="card-body">
              <form method="GET">
                <div class="input-group w-50">
                  <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                  <input type="text" class="form-control border-start-0" name="search"
                    placeholder="Search Announcement" value="<?php echo htmlspecialchars($search); ?>">
                </div>
              </form>
            </div>
          </div>

          <div class="card shadow-sm">
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover mb-0 border">
                  <thead class="table-light">
                    <tr>
                      <th>Title</th>
                      <th>Date</th>
                      <th>Description</th>
                      <th>Image</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0):
                      $counter = 1; // sequential ID for table display
                      while ($a = mysqli_fetch_assoc($result)): ?>
                        <tr>
                          <td><?php echo htmlspecialchars($a['title']); ?></td>
                          <td><?php echo date('Y-m-d H:i', strtotime($a['dateTime'])); ?></td>
                          <td style="max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            <?php echo htmlspecialchars($a['description']); ?>
                          </td>
                          <td>
                            <?php
                            if (!empty($a['image'])): ?>
                              <img src="/E-Barangay/assets/images/announcements/<?php echo $a['image']; ?>"
                                width="50" height="50" class="rounded" style="object-fit:cover;">
                            <?php else: ?>
                              <span class="text-muted">No image</span>
                            <?php endif; ?>
                          </td>

                          <td>
                            <a href="editAnnouncement.php?id=<?php echo $a['announcementID']; ?>" class="btn btn-warning btn-sm">
                              <i class="fas fa-edit"></i>
                            </a>
                            <a href="deleteAnnouncement.php?id=<?php echo $a['announcementID']; ?>"
                              onclick="return confirm('Delete this announcement?')"
                              class="btn btn-danger btn-sm">
                              <i class="fas fa-trash"></i>
                            </a>
                          </td>
                        </tr>
                      <?php endwhile;
                    else: ?>
                      <tr>
                        <td colspan="6" class="text-center py-4">No announcements found.</td>
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

  <!-- Add Announcement Modal -->
  <div class="modal fade" id="addAnnouncementModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg rounded-3">
        <div class="modal-header text-white" style="background-color:#31afab;">
          <h5 class="modal-title fw-semibold"><i class="fas fa-plus-circle me-2"></i>Add New Announcement</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST" enctype="multipart/form-data">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold"><i class="fas fa-heading me-2"></i>Title</label>
                <input type="text" name="title" class="form-control" required placeholder="Enter title">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold"><i class="fas fa-calendar me-2"></i>Date & Time</label>
                <input type="datetime-local" name="dateTime" class="form-control" required>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold"><i class="fas fa-align-left me-2"></i>Description</label>
              <textarea name="description" class="form-control" rows="4" required placeholder="Enter description"></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold"><i class="fas fa-image me-2"></i>Image</label>
              <input type="file" name="image" class="form-control" accept="image/*">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="save_announcement" class="btn text-white" style="background-color:#31afab;">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.6/js/bootstrap.bundle.min.js"></script>
</body>

</html>
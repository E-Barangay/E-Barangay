<?php
include_once __DIR__ . '/../../sharedAssets/connect.php';

if (isset($_POST['confirm_delete'])) {
  $id = intval($_POST['delete_id']);

  $imgQuery = mysqli_query($conn, "SELECT image FROM announcements WHERE announcementID = $id");
  if ($imgRow = mysqli_fetch_assoc($imgQuery)) {
    $imgPath = __DIR__ . '/../../assets/images/announcements/' . $imgRow['image'];
    if (!empty($imgRow['image']) && file_exists($imgPath)) {
      unlink($imgPath);
    }
  }

  $delete = mysqli_query($conn, "DELETE FROM announcements WHERE announcementID = $id");
}

$successMessage = '';
$errorMessage = '';

if (isset($_POST['save_announcement'])) {
  $title = mysqli_real_escape_string($conn, $_POST['title']);
  $description = mysqli_real_escape_string($conn, $_POST['description']);
  $dateTime = mysqli_real_escape_string($conn, $_POST['dateTime']);
  $image = '';

  if (!empty($_FILES['image']['name'])) {
    $uploadDir = __DIR__ . '/../../assets/images/announcements/';
    if (!is_dir($uploadDir)) {
      mkdir($uploadDir, 0777, true);
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    $fileType = $_FILES['image']['type'];

    if (in_array($fileType, $allowedTypes)) {
      $fileName = time() . '_' . basename($_FILES['image']['name']);
      $targetFile = $uploadDir . $fileName;

      if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        $image = $fileName;
      } else {
        $errorMessage = 'Image upload failed!';
      }
    } else {
      $errorMessage = 'Invalid image format. Only JPG, PNG, and GIF allowed.';
    }
  }

  if (empty($errorMessage)) {
    $query = "INSERT INTO announcements (title, description, dateTime, image) VALUES (?, ?, ?, ?)";
    if ($stmt = mysqli_prepare($conn, $query)) {
      mysqli_stmt_bind_param($stmt, "ssss", $title, $description, $dateTime, $image);
      if (mysqli_stmt_execute($stmt)) {
        $successMessage = 'Announcement successfully uploaded!';
      } else {
        $errorMessage = 'Failed to save announcement.';
      }
      mysqli_stmt_close($stmt);
    }
  }
}

$search = $_GET['search'] ?? '';
$dateFilter = $_GET['date'] ?? '';
$currentPage = isset($_GET['pg']) ? (int) $_GET['pg'] : 1;
$recordsPerPage = 20;
$offset = ($currentPage - 1) * $recordsPerPage;

$query = "SELECT * FROM announcements WHERE 1=1";
$countQuery = "SELECT COUNT(*) as total FROM announcements WHERE 1=1";

if (!empty($search)) {
  $search = mysqli_real_escape_string($conn, $search);
  $query .= " AND (title LIKE '%$search%' OR description LIKE '%$search%')";
  $countQuery .= " AND (title LIKE '%$search%' OR description LIKE '%$search%')";
}
if (!empty($dateFilter)) {
  $dateFilter = mysqli_real_escape_string($conn, $dateFilter);
  $query .= " AND DATE(dateTime) = '$dateFilter'";
  $countQuery .= " AND DATE(dateTime) = '$dateFilter'";
}

$countResult = mysqli_query($conn, $countQuery);
$totalRecords = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

$query .= " ORDER BY announcementID DESC LIMIT $recordsPerPage OFFSET $offset";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Announcement Management</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.6/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
    rel="stylesheet" />
</head>
<style>
  body {
    font-family: 'Poppins', sans-serif;
    background-color: #f8f9fa;
  }

  .btn-custom {
    background-color: #31afab;
    color: #fff;
  }

  .btn-custom:hover {
    background-color: #279995;
    color: #fff;
  }

  .card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: none;
  }

  .pagination .page-link {
    color: #31afab;
    background-color: white;
    border: 1px solid #dee2e6;
    transition: all 0.2s ease-in-out;
  }

  .pagination .page-item.active .page-link {
    background-color: #31afab;
    border-color: #31afab;
    color: white;
  }

  .pagination .page-link:hover {
    background-color: #e9f8f8;
    color: #31afab;
  }

  .filterButton {
    background-color: #19AFA5;
    border-color: #19AFA5;
    color: white;
  }

  .filterButton:hover {
    background-color: #11A1A1;
    border-color: #11A1A1;
    color: white;
  }
</style>

<body>
  <div class="container-fluid p-3 p-md-4">
    <?php if (!empty($successMessage)): ?>
      <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($successMessage); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?>
      <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($errorMessage); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <div class="card shadow-lg border-0 rounded-3">
      <div class="card-body p-0">
        <div class="text-white p-4 rounded-top" style="background-color:#19AFA5;">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center">
              <i class="fas fa-bullhorn me-2 fs-5 fs-md-4"></i>
              <h1 class="h4 h-md-4 mb-0 fw-semibold">Announcement Management</h1>
            </div>
            <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">
              <i class="fas fa-plus me-2"></i>Add Announcement
            </button>
          </div>
        </div>

        <div class="p-3 p-md-4">
          <div class="card shadow-sm mb-4">
            <div class="card-body">
              <form method="GET" action="./index.php" class="row g-3">
                <input type="hidden" name="page" value="announcement">

                <div class="col-12 col-md-6">
                  <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                      <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" name="search"
                      placeholder="Search by title or description..." value="<?php echo htmlspecialchars($search); ?>">
                  </div>
                </div>
                <div class="col-12 col-md-3">
                  <input type="date" class="form-control" name="date"
                    value="<?php echo htmlspecialchars($dateFilter); ?>">
                </div>
                <div class="col-12 col-md-3">
                  <button type="submit" class="btn btn-custom filterButton w-100">
                    <i class="fas fa-filter me-2"></i>Filter
                  </button>
                </div>
              </form>
            </div>
          </div>

          <div class="card shadow-sm d-none d-lg-block">
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                  <thead class="table-light">
                    <tr>
                      <th style="width: 25%;">Title</th>
                      <th style="width: 15%;">Date & Time</th>
                      <th style="width: 30%;">Description</th>
                      <th class="text-center" style="width: 15%;">Image</th>
                      <th class="text-center" style="width: 15%;">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if (isset($result) && mysqli_num_rows($result) > 0):
                      while ($announcement = mysqli_fetch_assoc($result)):
                        $imagePath = "../assets/images/announcements/" . htmlspecialchars($announcement['image']);
                        ?>
                        <tr>
                          <td><?php echo htmlspecialchars($announcement['title']); ?></td>
                          <td>
                            <small class="text-muted">
                              <i
                                class="far fa-calendar me-1"></i><?php echo date('M d, Y', strtotime($announcement['dateTime'])); ?><br>
                              <i
                                class="far fa-clock me-1"></i><?php echo date('h:i A', strtotime($announcement['dateTime'])); ?>
                            </small>
                          </td>
                          <td>
                            <div
                              style="max-width:300px;overflow:hidden;text-overflow:ellipsis;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">
                              <?php echo htmlspecialchars($announcement['description']); ?>
                            </div>
                          </td>
                          <td class="text-center">
                            <?php if (!empty($announcement['image'])): ?>
                              <img src="<?php echo $imagePath; ?>"
                                style="width:60px;height:60px;object-fit:cover;border-radius:8px;cursor:pointer;border:2px solid #dee2e6;"
                                onclick="showImageModal('<?php echo $imagePath; ?>')" alt="Announcement image">
                            <?php else: ?>
                              <span class="badge bg-secondary">No image</span>
                            <?php endif; ?>
                          </td>
                          <td class="text-center">
                            <div class="d-flex justify-content-center gap-2 flex-wrap">
                              <a href="../admin/adminContent/viewAnnouncement.php?id=<?php echo $announcement['announcementID']; ?>"
                                class="btn btn-warning btn-sm" title="Edit">
                                <i class="fas fa-edit"></i>
                              </a>

                              <button class="btn btn-sm btn-danger" type="button" title="Delete" data-bs-toggle="modal"
                                data-bs-target="#deleteAnnouncementModal<?= $announcement['announcementID'] ?>">
                                <i class="fas fa-trash"></i>
                              </button>

                              <div class="modal fade" id="deleteAnnouncementModal<?= $announcement['announcementID'] ?>"
                                tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                  <div class="modal-content border-0 shadow-lg rounded-3">
                                    <div class="modal-header bg-danger text-white">
                                      <h5 class="modal-title fw-semibold"><i
                                          class="fas fa-exclamation-triangle me-2"></i>Delete Announcement</h5>
                                      <button type="button" class="btn-close btn-close-white"
                                        data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                      <p class="mb-0 text-secondary">
                                        Are you sure you want to <strong>delete</strong> this announcement titled
                                        "<strong><?= htmlspecialchars($announcement['title']) ?></strong>"?
                                      </p>
                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="fas fa-times me-1"></i>Cancel
                                      </button>
                                      <form method="POST" style="display:inline;">
                                        <input type="hidden" name="delete_id"
                                          value="<?= $announcement['announcementID'] ?>">
                                        <button type="submit" name="confirm_delete" class="btn btn-danger">
                                          <i class="fas fa-trash me-1"></i>Confirm Delete
                                        </button>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </td>
                        </tr>
                      <?php endwhile;
                    else: ?>
                      <tr>
                        <td colspan="5" class="text-center py-5">
                          <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                          <p class="text-muted">No announcements found.</p>
                        </td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>

            <?php if ($totalPages > 1): ?>
              <div class="card-footer bg-white">
                <div class="d-flex justify-content-between align-items-center">
                  <div class="text-muted small">
                    Showing <?= min($offset + 1, $totalRecords) ?> to <?= min($offset + $recordsPerPage, $totalRecords) ?>
                    of <?= $totalRecords ?> entries
                  </div>
                  <nav>
                    <ul class="pagination pagination-sm mb-0">
                      <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link"
                          href="?page=announcement&pg=<?= $currentPage - 1 ?>&search=<?= urlencode($search) ?>&date=<?= urlencode($dateFilter) ?>">
                          <i class="fas fa-chevron-left"></i> Back
                        </a>
                      </li>

                      <?php
                      $startPage = max(1, $currentPage - 2);
                      $endPage = min($totalPages, $currentPage + 2);

                      if ($startPage > 1): ?>
                        <li class="page-item">
                          <a class="page-link"
                            href="?page=announcement&pg=1&search=<?= urlencode($search) ?>&date=<?= urlencode($dateFilter) ?>">1</a>
                        </li>
                        <?php if ($startPage > 2): ?>
                          <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                      <?php endif; ?>

                      <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                          <a class="page-link"
                            href="?page=announcement&pg=<?= $i ?>&search=<?= urlencode($search) ?>&date=<?= urlencode($dateFilter) ?>"><?= $i ?></a>
                        </li>
                      <?php endfor; ?>

                      <?php if ($endPage < $totalPages): ?>
                        <?php if ($endPage < $totalPages - 1): ?>
                          <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                        <li class="page-item">
                          <a class="page-link"
                            href="?page=announcement&pg=<?= $totalPages ?>&search=<?= urlencode($search) ?>&date=<?= urlencode($dateFilter) ?>"><?= $totalPages ?></a>
                        </li>
                      <?php endif; ?>

                      <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link"
                          href="?page=announcement&pg=<?= $currentPage + 1 ?>&search=<?= urlencode($search) ?>&date=<?= urlencode($dateFilter) ?>">
                          Next <i class="fas fa-chevron-right"></i>
                        </a>
                      </li>
                    </ul>
                  </nav>
                </div>
              </div>
            <?php endif; ?>
          </div>

          <div class="d-lg-none">
            <?php
            if (isset($result) && mysqli_num_rows($result) > 0):
              mysqli_data_seek($result, 0);
              while ($announcement = mysqli_fetch_assoc($result)):
                $imagePath = "/Baranggya/E-Barangay/assets/images/announcements/" . htmlspecialchars($announcement['image']);
                $mobileModalId = "deleteAnnouncementModal_mobile_" . $announcement['announcementID'];
                ?>
                <div class="card shadow-sm mb-3 border-0" style="border-radius: 12px;">
                  <div class="card-body">
                    <h5 class="fw-semibold text-primary mb-2" style="color:#31afab !important;">
                      <?= htmlspecialchars($announcement['title']); ?>
                    </h5>
                    <p class="text-muted mb-2">
                      <i class="far fa-calendar me-1"></i><?= date('M d, Y', strtotime($announcement['dateTime'])); ?> •
                      <i class="far fa-clock me-1"></i><?= date('h:i A', strtotime($announcement['dateTime'])); ?>
                    </p>

                    <?php if (!empty($announcement['image'])): ?>
                      <div class="mb-3">
                        <img src="<?= $imagePath; ?>" alt="Announcement image" class="img-fluid rounded"
                          style="width: 100%; max-height: 220px; object-fit: cover; cursor: pointer; border:2px solid #dee2e6;"
                          onclick="showImageModal('<?= $imagePath; ?>')">
                      </div>
                    <?php endif; ?>

                    <p class="text-secondary mb-3" style="line-height:1.6;">
                      <?= nl2br(htmlspecialchars($announcement['description'])); ?>
                    </p>

                    <div class="d-flex flex-wrap gap-2">
                      <a href="../admin/adminContent/viewAnnouncement.php?id=<?= $announcement['announcementID']; ?>"
                        class="btn btn-warning btn-sm flex-grow-1">
                        <i class="fas fa-edit me-1"></i>Edit
                      </a>
                      <button class="btn btn-danger btn-sm flex-grow-1" data-bs-toggle="modal"
                        data-bs-target="#<?= $mobileModalId; ?>">
                        <i class="fas fa-trash me-1"></i>Delete
                      </button>
                    </div>
                  </div>
                </div>

                <div class="modal fade" id="<?= $mobileModalId; ?>" tabindex="-1">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-3">
                      <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-semibold">
                          <i class="fas fa-exclamation-triangle me-2"></i>Delete Announcement
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <p class="mb-0 text-secondary">
                          Are you sure you want to <strong>delete</strong> this announcement titled
                          "<strong><?= htmlspecialchars($announcement['title']) ?></strong>"?
                        </p>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                          <i class="fas fa-times me-1"></i>Cancel
                        </button>
                        <form method="POST" style="display:inline;">
                          <input type="hidden" name="delete_id" value="<?= $announcement['announcementID'] ?>">
                          <button type="submit" name="confirm_delete" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i>Confirm Delete
                          </button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endwhile;
              
              if ($totalPages > 1): ?>
                <div class="card shadow-sm mt-3">
                  <div class="card-body">
                    <div class="text-center text-muted small mb-2">
                      Showing <?= min($offset + 1, $totalRecords) ?> to <?= min($offset + $recordsPerPage, $totalRecords) ?>
                      of <?= $totalRecords ?> entries
                    </div>
                    <nav>
                      <ul class="pagination pagination-sm justify-content-center mb-0">
                        <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                          <a class="page-link"
                            href="?page=announcement&pg=<?= $currentPage - 1 ?>&search=<?= urlencode($search) ?>&date=<?= urlencode($dateFilter) ?>">
                            <i class="fas fa-chevron-left"></i>
                          </a>
                        </li>

                        <?php
                        $startPage = max(1, $currentPage - 1);
                        $endPage = min($totalPages, $currentPage + 1);

                        for ($i = $startPage; $i <= $endPage; $i++): ?>
                          <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                            <a class="page-link"
                              href="?page=announcement&pg=<?= $i ?>&search=<?= urlencode($search) ?>&date=<?= urlencode($dateFilter) ?>"><?= $i ?></a>
                          </li>
                        <?php endfor; ?>

                        <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                          <a class="page-link"
                            href="?page=announcement&pg=<?= $currentPage + 1 ?>&search=<?= urlencode($search) ?>&date=<?= urlencode($dateFilter) ?>">
                            <i class="fas fa-chevron-right"></i>
                          </a>
                        </li>
                      </ul>
                    </nav>
                  </div>
                </div>
              <?php endif;
              
            else: ?>
              <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                <p class="text-muted">No announcements found.</p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="addAnnouncementModal" tabindex="-1">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
          <div class="modal-header text-white" style="background-color:#31afab;">
            <h5 class="modal-title fw-semibold">
              <i class="fas fa-plus-circle me-2"></i>Add New Announcement
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <form method="POST" enctype="multipart/form-data">
            <div class="modal-body">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-semibold"><i class="fas fa-heading me-2"></i>Title</label>
                  <input type="text" name="title" class="form-control" required placeholder="Enter announcement title">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-semibold"><i class="fas fa-calendar-alt me-2"></i>Date & Time</label>
                  <input type="datetime-local" name="dateTime" class="form-control" required>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold"><i class="fas fa-align-left me-2"></i>Description</label>
                <textarea name="description" class="form-control" rows="4" required
                  placeholder="Enter announcement description"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold"><i class="fas fa-image me-2"></i>Image (Optional)</label>
                <input type="file" name="image" id="imageInput" class="form-control" accept="image/*"
                  onchange="previewImage(event)">
                <div id="fileName" style="margin-top:10px;font-size:0.9rem;color:#31afab;font-weight:500;"></div>
                <img id="imagePreview"
                  style="max-width:100%;max-height:250px;margin-top:15px;border-radius:8px;display:none;border:2px solid #dee2e6;"
                  alt="Preview">
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                  class="fas fa-times me-2"></i>Cancel</button>
              <button type="submit" name="save_announcement" class="btn text-white" style="background-color:#31afab;">
                <i class="fas fa-save me-2"></i>Save Announcement
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="modal fade" id="imageModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3">
          <div class="modal-header text-white" style="background-color:#31afab;">
            <h5 class="modal-title"><i class="fas fa-image me-2"></i>Image Preview</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body text-center p-4">
            <img id="modalImage" src="" class="img-fluid rounded" alt="Full size image">
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.6/js/bootstrap.bundle.min.js"></script>
    <script>
      function previewImage(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('imagePreview');
        const fileName = document.getElementById('fileName');
        if (file) {
          const reader = new FileReader();
          reader.onload = function (e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            fileName.innerHTML = '<i class="fas fa-file-image me-2"></i><strong>Selected:</strong> ' + file.name;
          }
          reader.readAsDataURL(file);
        } else {
          preview.style.display = 'none';
          fileName.innerHTML = '';
        }
      }

      function showImageModal(src) {
        const modalImg = document.getElementById('modalImage');
        modalImg.src = src;
        setTimeout(() => {
          const imgModal = new bootstrap.Modal(document.getElementById('imageModal'));
          imgModal.show();
        }, 100);
      }

      setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => new bootstrap.Alert(alert).close());
      }, 5000);

      document.getElementById('addAnnouncementModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('imagePreview').style.display = 'none';
        document.getElementById('fileName').innerHTML = '';
        this.querySelector('form').reset();
      });
    </script>
  </div>
</body>

</html>
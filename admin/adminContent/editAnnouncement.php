<?php
include_once __DIR__ . '/../../sharedAssets/connect.php';

// Get and validate announcement ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    header("Location: ../index.php?page=announcement");
    exit;
}

// Fetch announcement details
$stmt = mysqli_prepare($conn, "SELECT * FROM announcements WHERE announcementID = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$announcement = mysqli_fetch_assoc($result);

if (!$announcement) {
    header("Location: ../index.php?page=announcement");
    exit;
}

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $title = trim($_POST['title']);
    $date = $_POST['date'];
    $description = trim($_POST['description']);

    if (empty($title) || empty($date) || empty($description)) {
        $message = 'All fields are required.';
        $messageType = 'danger';
    } else {
        $imagePath = $announcement['image'];

        // Handle image upload
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === 0) {
            $uploadDir = '../../assets/images/announcements/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $fileType = $_FILES['image']['type'];

            if (in_array($fileType, $allowedTypes)) {
                $fileExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = time() . '_' . uniqid() . '.' . $fileExtension;
                $fullPath = $uploadDir . $filename;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $fullPath)) {
                    if (!empty($announcement['image']) && file_exists('../../' . $announcement['image'])) {
                        unlink('../../' . $announcement['image']);
                    }
                    $imagePath = 'assets/images/announcements/' . $filename;
                } else {
                    $message = 'Failed to upload image.';
                    $messageType = 'danger';
                }
            } else {
                $message = 'Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.';
                $messageType = 'danger';
            }
        }

        // If no upload errors
        if (empty($message)) {
            $updateStmt = mysqli_prepare($conn, "UPDATE announcements SET title=?, dateTime=?, description=?, image=?, isImportant=? WHERE announcementID=?");
            mysqli_stmt_bind_param($updateStmt, "ssssii", $title, $date, $description, $imagePath, $isImportant, $id);

            if (mysqli_stmt_execute($updateStmt)) {
                header("Location: ../index.php?page=announcement&updated=1");
                exit;
            } else {
                $message = 'Error updating announcement: ' . mysqli_error($conn);
                $messageType = 'danger';
            }
        }
    }
}

// Helper for full image path
function getImagePath($imagePath)
{
    if (empty($imagePath))
        return null;
    return (strpos($imagePath, 'assets/') === 0) ? '../../' . $imagePath : $imagePath;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Announcement</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../assets/images/logoSanAntonio.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: linear-gradient(135deg, rgb(49, 175, 171), rgb(40, 150, 146));
            border-radius: 0.5rem 0.5rem 0 0 !important;
        }

        .btn-primary {
            background-color: rgb(49, 175, 171);
            border-color: rgb(49, 175, 171);
        }

        .btn-primary:hover {
            background-color: rgb(40, 150, 146);
            border-color: rgb(40, 150, 146);
        }

        .current-image {
            border: 2px solid #dee2e6;
            border-radius: 0.5rem;
            max-width: 100%;
            height: auto;
            max-height: 300px;
            object-fit: cover;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
        }
    </style>
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-white">
                        <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Announcement</h4>
                    </div>
                    <div class="card-body p-4">

                        <?php if (!empty($message)): ?>
                            <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($message) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?= $announcement['announcementID'] ?>">

                            <div class="mb-3">
                                <label class="form-label">Title *</label>
                                <input type="text" name="title" class="form-control" required maxlength="255"
                                    value="<?= htmlspecialchars($announcement['title']) ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Date *</label>
                                <input type="date" name="date" class="form-control" required
                                    value="<?= substr($announcement['dateTime'], 0, 10) ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description *</label>
                                <textarea name="description" class="form-control" rows="4" required
                                    maxlength="1000"><?= htmlspecialchars($announcement['description']) ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Image</label>

                                <?php
                                $imageFilename = $announcement['image'] ?? '';
                                $imageFilename = basename($imageFilename);
                                $webPath = '../../assets/images/announcements/' . $imageFilename;
                                $fullPath = __DIR__ . '/../../assets/images/announcements/' . $imageFilename;

                                if (!empty($imageFilename) && file_exists($fullPath)): ?>
                                    <div class="mb-3 text-center">
                                        <p class="text-muted mb-2">Current Image:</p>
                                        <img src="<?= htmlspecialchars($webPath) ?>" alt="Current announcement image"
                                            class="current-image shadow-sm">
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted">No image found or path is incorrect.</p>
                                <?php endif; ?>

                                <input type="file" name="image" class="form-control"
                                    accept="image/jpeg,image/png,image/gif,image/webp">
                                <div class="form-text">Leave empty to keep current image. Max size 5MB.</div>
                            </div>

                            <div class="d-flex flex-column flex-lg-row justify-content-between gap-2 mt-3">
                                <button type="submit" class="btn btn-primary order-1 order-lg-2 w-100 w-lg-auto">
                                    <i class="fas fa-save me-2"></i>Update Announcement
                                </button>
                                <a href="../index.php?page=announcement"
                                    class="btn btn-secondary order-2 order-lg-1 w-100 w-lg-auto">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Announcements
                                </a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
include_once __DIR__ . '/../../sharedAssets/connect.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: ../index.php?page=announcement");
    exit;
}

$query = "SELECT * FROM announcements WHERE announcementID = $id";
$result = mysqli_query($conn, $query);
$announcement = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']); // From hidden input
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $date = $_POST['date'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $isImportant = isset($_POST['important']) ? 1 : 0;

    // Keep existing image unless a new one is uploaded
    $imagePath = $_POST['existing_image'] ?? $announcement['image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadDir = '../../assets/images/announcements/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filename = time() . '_' . basename($_FILES['image']['name']);
        $fullPath = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $fullPath)) {
            $imagePath = 'assets/images/announcements/' . $filename;
        }
    }

    $update = "UPDATE announcements 
               SET title='$title', dateTime='$date', description='$description', image='$imagePath', isImportant=$isImportant 
               WHERE announcementID = $id";

    if (mysqli_query($conn, $update)) {
        header("Location: ../index.php?page=announcement&updated=1");
        exit;
    } else {
        echo "MySQL Error: " . mysqli_error($conn);
    }
}
?>

<!-- HTML Form UI -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Announcement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header text-white" style="background-color: rgb(49, 175, 171);">
                <h4 class="mb-0">Edit Announcement</h4>
            </div>
            <form method="POST" enctype="multipart/form-data" class="p-4">
                <input type="hidden" name="id" value="<?= $announcement['announcementID'] ?>">
                <input type="hidden" name="existing_image" value="<?= $announcement['image'] ?>">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Title</label>
                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($announcement['title']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Date</label>
                    <input type="date" name="date" class="form-control" value="<?= substr($announcement['dateTime'], 0, 10) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($announcement['description']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Image</label><br>
                    <?php if (!empty($announcement['image'])): ?>
                        <img src="/e-baranggay/E-Barangay/<?= $announcement['image'] ?>" 
                             alt="Current" 
                             class="img-fluid rounded mb-2" 
                             style="max-width: 100%; height: auto; max-height: 300px;">
                    <?php endif; ?>
                    <input type="file" name="image" class="form-control">
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="important" class="form-check-input" id="importantCheck" <?= $announcement['isImportant'] ? 'checked' : '' ?>>
                    <label for="importantCheck" class="form-check-label">Mark as Important</label>
                </div>

                <div class="text-end">
                    <a href="../index.php?page=announcement" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn text-white" style="background-color: rgb(49, 175, 171);">Update</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

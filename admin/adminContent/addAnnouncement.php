<?php
include("../../sharedAssets/connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $dateTime = $_POST['dateTime'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $isImportant = isset($_POST['important']) ? 1 : 0;
    $imagePath = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadDir = '../../assets/images/announcements/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $absolutePath = $uploadDir . $imageName;
        $browserPath = 'assets/images/announcements/' . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $absolutePath)) {
            $imagePath = $browserPath;
        }
    }

    $query = "INSERT INTO announcements (title, dateTime, description, image, isImportant)
              VALUES ('$title', '$date', '$description', '$imagePath', $isImportant)";
    if (mysqli_query($conn, $query)) {
        header("Location: ../index.php?page=announcement&success=1");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!-- HTML Form UI -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Announcement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header text-white" style="background-color: rgb(49, 175, 171);">
                <h4 class="mb-0">Add New Announcement</h4>
            </div>
            <form method="POST" enctype="multipart/form-data" class="p-4">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Date</label>
                    <input type="date" name="date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" class="form-control" rows="4" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Image</label>
                    <input type="file" name="image" class="form-control">
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" name="important" class="form-check-input" id="importantCheck">
                    <label for="importantCheck" class="form-check-label">Mark as Important</label>
                </div>
                <div class="text-end">
                    <a href="../index.php?page=announcement" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn text-white" style="background-color: rgb(49, 175, 171);">Save</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
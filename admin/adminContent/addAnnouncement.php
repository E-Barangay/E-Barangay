<?php
include("../../sharedAssets/connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $dateTime = $_POST['dateTime']; // Fixed: was 'date' in form but 'dateTime' in variable
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $imagePath = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadDir = '../../assets/images/announcements/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $absolutePath = $uploadDir . $imageName;
        $browserPath = $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $absolutePath)) {
            $imagePath = $browserPath;
        }
    }

    // Fixed: was using $date instead of $dateTime
    $query = "INSERT INTO announcements (title, dateTime, description, image)
              VALUES ('$title', '$dateTime', '$description', '$imagePath')";
    if (mysqli_query($conn, $query)) {
        header("Location: ../index.php?page=announcement&success=1");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Announcement</title>
    <link rel="icon" href="../../assets/images/logoSanAntonio.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: rgb(233, 233, 233);
            color: dark;
        }

        .custom-file-input {
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .custom-file-input:hover {
            border-color: rgb(49, 175, 171);
            background-color: #f8f9fa;
        }

        .image-preview {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-body p-0">
                <div class="text-white p-4 rounded-top" style="background-color: rgb(49, 175, 171);">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-plus-circle me-3 fs-4"></i>
                        <h4 class="mb-0 fw-semibold">Add New Announcement</h4>
                    </div>
                </div>

                <form method="POST" enctype="multipart/form-data" class="p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-heading me-2"></i>Title
                                </label>
                                <input type="text" name="title" class="form-control" required
                                    placeholder="Enter announcement title">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-calendar me-2"></i>Date & Time
                                </label>
                                <!-- Fixed: changed name from 'date' to 'dateTime' to match variable -->
                                <input type="datetime-local" name="dateTime" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-align-left me-2"></i>Description
                        </label>
                        <textarea name="description" class="form-control" rows="4" required
                            placeholder="Enter announcement description"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-image me-2"></i>Image
                        </label>
                        <div class="custom-file-input" onclick="document.getElementById('imageInput').click()">
                            <i class="fas fa-cloud-upload-alt fs-2 text-muted mb-2"></i>
                            <p class="mb-0">Click to upload image</p>
                            <small class="text-muted">Supported formats: JPG, PNG, GIF</small>
                        </div>
                        <input type="file" name="image" id="imageInput" class="d-none" accept="image/*"
                            onchange="previewImage(this)">
                        <div id="imagePreview" class="mt-2"></div>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="../index.php?page=announcement" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                        <button type="submit" class="btn text-white" style="background-color: rgb(49, 175, 171);">
                            <i class="fas fa-save me-2"></i>Save Announcement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'image-preview img-fluid';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function (e) {
            const title = document.querySelector('input[name="title"]').value.trim();
            const dateTime = document.querySelector('input[name="dateTime"]').value;
            const description = document.querySelector('textarea[name="description"]').value.trim();

            if (!title || !dateTime || !description) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return false;
            }

            // Check if date is not in the past
            const selectedDate = new Date(dateTime);
            const now = new Date();

            if (selectedDate < now) {
                const confirm = window.confirm('The selected date is in the past. Do you want to continue?');
                if (!confirm) {
                    e.preventDefault();
                    return false;
                }
            }
        });
    </script>
</body>

</html>
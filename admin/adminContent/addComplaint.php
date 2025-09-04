<?php
include_once __DIR__ . '/../../sharedAssets/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $complaintTitle = $_POST['complaintTitle'] ?? '';
    $complaintStatus = $_POST['complaintStatus'] ?? 'Pending';
    $complaintDescription = $_POST['complaintDescription'] ?? '';
    $phoneNumber = $_POST['phoneNumber'] ?? '';
    $requestDate = date('Y-m-d H:i:s');

    // Default values for columns not yet in the form
    $userID = null;
    $complaintCategoryID = null;
    $complaintTypeID = null;
    $complaintAccussed = null;
    $complaintAddress = null;
    $complaintVictim = null;

    // Insert new complaint
    $stmt = $conn->prepare("INSERT INTO complaints 
        (userID, complaintCategoryID, complaintTypeID, complaintTitle, complaintDescription, requestDate, complaintStatus, complaintPhoneNumber, complaintAccused, complaintAddress, complaintVictim) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "iiissssssss",
        $userID,
        $complainCategoryID,
        $complaintTypeID,
        $complaintTitle,
        $complaintDescription,
        $requestDate,
        $complaintStatus,
        $phoneNumber,
        $complaintAccussed,
        $complaintAddress,
        $complaintVictim
    );
    $stmt->execute();
    $stmt->close();

    header("Location: ../index.php?page=complaintsKP");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Complaint</title>
    <link rel="icon" href="../../assets/images/logoSanAntonio.png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body style="background:#f1f3f5;">
    <div class="container px-3">
        <div class="report-card mx-auto mt-5 p-4 bg-white rounded-3 shadow-sm" style="max-width: 700px;">
            <h5 class="fw-bold mb-4"><i class="fas fa-plus text-primary"></i>Add Complaint</h5>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Type of Complaint</label>
                    <select name="complaintTitle" class="form-select" required>
                        <option value="Illegal Parking">Illegal Parking</option>
                        <option value="Physical Abuse">Physical Abuse</option>
                        <option value="Stray Cats">Stray Cats</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="complaintStatus" class="form-select">
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Resolved">Resolved</option>
                        <option value="Closed">Closed</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Complainant's Name</label>
                    <input type="text" name="requesterName" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Complainant's Description</label>
                    <textarea name="complaintDescription" class="form-control" rows="4" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Contact Number</label>
                    <input type="text" name="phoneNumber" class="form-control" required>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">Submit Complaint</button>
                    <a href="../index.php?page=complaintsKP" class="btn btn-secondary w-100">Back</a>
                </div>
            </form>

        </div>
    </div>
</body>

</html>
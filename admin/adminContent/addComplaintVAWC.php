<?php
include_once __DIR__ . '/../../sharedAssets/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $complaintTitle = $_POST['complaintTitle'] ?? '';

    // If "Other", replace with custom input
    if ($complaintTitle === "Other" && !empty($_POST['otherComplaint'])) {
        $complaintTitle = $_POST['otherComplaint'];
    }

    $complaintStatus = 'Pending'; // ✅ Always set as Pending
    $complaintDescription = $_POST['complaintDescription'] ?? '';
    $phoneNumber = $_POST['phoneNumber'] ?? '';
    $complainantName = $_POST['complainantName'] ?? '';
    $complaintVictim = $_POST['complaintVictim'] ?? '';
    $victimAge = $_POST['victimAge'] ?? null;
    $complaintAccused = $_POST['complaintAccused'] ?? '';
    $victimRelationship = $_POST['victimRelationship'] ?? '';
    $actionTaken = $_POST['actionTaken'] ?? '';
    $requestDate = date('Y-m-d H:i:s');

    // Default values for unused columns
    $userID = null;
    $complaintCategoryID = null;
    $complaintTypeID = null;
    $complaintAddress = null;
    $evidence = null;

    // ✅ Handle file upload (Evidence)
    if (isset($_FILES['evidence']) && $_FILES['evidence']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . "/../../uploads/evidence/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create folder if not exists
        }
        $fileName = time() . "_" . basename($_FILES['evidence']['name']);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['evidence']['tmp_name'], $targetFile)) {
            $evidence = "uploads/evidence/" . $fileName; // Save relative path
        }
    }

    // ✅ Insert new complaint
    $stmt = $conn->prepare("INSERT INTO complaints 
        (userID, complaintCategoryID, complaintTypeID, complaintTitle, complaintDescription, requestDate, 
         complaintStatus, complaintPhoneNumber, complaintAccused, complaintAddress, complaintVictim, 
         complainantName, victimAge, victimRelationship, actionTaken, evidence) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "iiissssssssssiss",
        $userID,
        $complaintCategoryID,
        $complaintTypeID,
        $complaintTitle,
        $complaintDescription,
        $requestDate,
        $complaintStatus,
        $phoneNumber,
        $complaintAccused,
        $complaintAddress,
        $complaintVictim,
        $complainantName,
        $victimAge,
        $victimRelationship,
        $actionTaken,
        $evidence
    );

    $stmt->execute();
    $stmt->close();

    header("Location: ../index.php?page=complaintsVAWC");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VAWC</title>
    <link rel="icon" href="../../assets/images/logoSanAntonio.png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" />
</head>

<<body style="background:#19AFA5;">
    <div class="container px-3">
        <div class="report-card mx-auto mt-5 p-4 bg-white rounded-3 shadow-sm" style="max-width: 700px;">
            <h3 class="fw-bold mb-4 text-center"><i class="fas fa-plus text-primary"></i>Violence against Women and
                Children</h3>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Type of Violence</label>
                    <select name="complaintTitle" class="form-select" id="complaintTitle" required>
                        <option value="Physical Abuse">Physical Abuse</option>
                        <option value="Sexual Abuse">Sexual Abuse</option>
                        <option value="Psychological Abuse/Emotional Abuse">Psychological Abuse/Emotional Abuse</option>
                        <option value="Economic Abuse">Economic Abuse</option>
                        <option value="Neglect">Neglect</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <!-- Hidden input for custom complaint -->
                <div class="mb-3 d-none" id="otherComplaintDiv">
                    <label class="form-label">Please specify</label>
                    <input type="text" name="otherComplaint" class="form-control">
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Complainant's Name</label>
                        <input type="text" name="complainantName" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Contact Number</label>
                        <input type="text" name="phoneNumber" class="form-control" required pattern="^09\d{9}$"
                            title="Please enter a valid PH mobile number (e.g. 09123456789)">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Victim</label>
                        <input type="text" name="complaintVictim" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Victim's Age</label>
                        <input type="number" name="victimAge" class="form-control" required min="0">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Perpetrator</label>
                        <input type="text" name="complaintAccused" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Relationship</label>
                        <input type="text" name="victimRelationship" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Complainant's Description</label>
                    <textarea name="complaintDescription" class="form-control" rows="4" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Actions Taken</label>
                    <textarea name="actionTaken" class="form-control" rows="4"></textarea>
                </div>

                <!-- Evidence Upload -->
                <div class="mb-3">
                    <label class="form-label">Evidence (Photo)</label>
                    <input type="file" name="evidence" class="form-control" accept="image/*">
                </div>

                <div class="d-flex gap-2">
                    <a href="../index.php?page=complaintsVAWC" class="btn btn-danger w-100">Back</a>
                    <button type="submit" class="btn btn-success w-100">Submit Complaint</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById("complaintTitle").addEventListener("change", function () {
            const otherDiv = document.getElementById("otherComplaintDiv");
            if (this.value === "Other") {
                otherDiv.classList.remove("d-none");
            } else {
                otherDiv.classList.add("d-none");
            }
        });
    </script>
    </body>

</html>
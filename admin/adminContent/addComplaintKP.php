<?php
include_once __DIR__ . '/../../sharedAssets/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $complaintTitle = $_POST['complaintTitle'] ?? '';

    // If "Other", replace with custom input
    if ($complaintTitle === "Other" && !empty($_POST['otherComplaint'])) {
        $complaintTitle = $_POST['otherComplaint'];
    }

    $complaintStatus = 'Criminal'; // ✅ Always set as Criminal
    $complaintDescription = $_POST['complaintDescription'] ?? '';
    $actionTaken = $_POST['actionTaken'] ?? '';
    $phoneNumber = $_POST['phoneNumber'] ?? '';
    $complainantName = $_POST['complainantName'] ?? '';
    $requestDate = date('Y-m-d H:i:s');

    // Default values for unused columns
    $userID = null;
    $complaintCategoryID = null;
    $complaintTypeID = null;
    $complaintAccused = null;
    $complaintAddress = null;
    $complaintVictim = null;

    // Handle file upload
    $evidenceFile = null;
    if (!empty($_FILES['evidence']['name'])) {
        $uploadDir = __DIR__ . "/../../uploads/"; // make sure this folder exists and is writable
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = time() . "_" . basename($_FILES['evidence']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['evidence']['tmp_name'], $targetPath)) {
            $evidenceFile = $fileName; // store only filename in DB
        }
    }

    // Insert new complaint with evidence
    $stmt = $conn->prepare("INSERT INTO complaints 
    (userID, complaintCategoryID, complaintTypeID, complaintTitle, complaintDescription, requestDate, complaintStatus, complaintPhoneNumber, complaintAccused, complaintAddress, complaintVictim, complainantName, actionTaken, evidence) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "iiisssssssssss",
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
        $actionTaken,
        $evidenceFile
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
    <title>Katarungang Pambarangay</title>
    <link rel="icon" href="../../assets/images/logoSanAntonio.png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" />
</head>

<<body style="background:#19AFA5;">
    <div class="container px-3">
        <div class="report-card mx-auto mt-5 p-4 bg-white rounded-3 shadow-sm" style="max-width: 700px;">
            <h3 class="fw-bold mb-4 text-center"><i class="fas fa-plus text-primary"></i>Katarungang Pambarangay</h3>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Type of Complaint</label>
                    <select name="complaintTitle" class="form-select" id="complaintTitle" required>
                        <option value="Noise Complaints">Noise Complaints</option>
                        <option value="Boundary and Land Disputes">Boundary and Land Disputes</option>
                        <option value="Neighborhood Quarrels">Neighborhood Quarrels</option>
                        <option value="Animal-Related Complaints">Animal-Related Complaints</option>
                        <option value="Youth-Related Issues">Youth-Related Issues</option>
                        <option value="Barangay Clearance and Permit Concerns">Barangay Clearance and Permit Concerns
                        </option>
                        <option value="Garbage and Sanitation Complaints">Garbage and Sanitation Complaints</option>
                        <option value="Alcohol-Related Disturbances">Alcohol-Related Disturbances</option>
                        <option value="Traffic and Parking Issues">Traffic and Parking Issues</option>
                        <option value="Physical Assault and Threats">Physical Assault and Threats</option>
                        <option value="Water Supply Disputes">Water Supply Disputes</option>
                        <option value="Business-Related Conflicts">Business-Related Conflicts</option>
                        <option value="Curfew Violations">Curfew Violations</option>
                        <option value="Smoking and Littering Violations">Smoking and Littering Violations</option>
                        <option value="Illegal Structures and Encroachments">Illegal Structures and Encroachments
                        </option>
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

                <div class="mb-3">
                    <label class="form-label">Complainant's Description</label>
                    <textarea name="complaintDescription" class="form-control" rows="4" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Actions Taken</label>
                    <textarea name="actionTaken" class="form-control" rows="4"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Evidence (Photo)</label>
                    <input type="file" name="evidence" class="form-control" accept="image/*">
                </div>

                <div class="d-flex gap-2">
                    <a href="../index.php?page=complaintsKP" class="btn btn-danger w-100">Back</a>
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
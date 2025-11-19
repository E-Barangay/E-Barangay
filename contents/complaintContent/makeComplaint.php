<?php
$userName = '';
$userAge = '';
if (isset($_SESSION['userID'])) {
    $userID = $_SESSION['userID'];

    $userQuery = "
        SELECT ui.firstName, ui.middleName, ui.lastName, ui.suffix, ui.age,
               u.phoneNumber
        FROM userInfo ui
        INNER JOIN users u ON ui.userID = u.userID
        WHERE u.userID = '$userID'
        LIMIT 1
    ";
    $result = mysqli_query($conn, $userQuery);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Build full name
        $userName = trim(
            $user['firstName'] . ' ' .
            (!empty($user['middleName']) ? $user['middleName'] . ' ' : '') .
            $user['lastName'] .
            (!empty($user['suffix']) ? ', ' . $user['suffix'] : '')
        );

        $userAge = $user['age'];
        $userPhone = $user['phoneNumber'];
    } else {
        echo "<div class='alert alert-danger'>User not found.</div>";
    }
}

if (isset($_POST['submit'])) {
    $complaintTitle = $_POST['title'];
    $complaintAddress = $_POST['address'];
    $complaintAccused = $_POST['accused'];
    $complaintVictim = $_POST['victim'];
    $relationshipVictim = $_POST['relationship'];
    $phoneNumber = $_POST['phoneNumber'];
    $complaintDescription = $_POST['complaintDescription'];
    $requestStatus = 'pending';
    $isDeleted = 'NO';
    $isAction = 'NO';
    $latitude = mysqli_real_escape_string($conn, $_POST['latitude']);
    $longitude = mysqli_real_escape_string($conn, $_POST['longitude']);
    $complaintTypes = [
        "Physical Assault and Threats" => "Criminal",
        "Alcohol-Related Disturbances" => "Criminal",
        "Curfew Violations" => "Criminal",
        "Smoking and Littering Violations" => "Criminal",
        "Vandalism and Property Damage" => "Criminal",
        "Theft and Stolen Property Incidents" => "Criminal",

        "Noise Complaints" => "Civil",
        "Boundary and Land Disputes" => "Civil",
        "Neighborhood Quarrels" => "Civil",
        "Animal-Related Complaints" => "Civil",
        "Youth-Related Issues" => "Civil",
        "Barangay Clearance and Permit Concerns" => "Civil",
        "Garbage and Sanitation Complaints" => "Civil",
        "Traffic and Parking Issues" => "Civil",
        "Water Supply Disputes" => "Civil",
        "Business-Related Conflicts" => "Civil",
        "Illegal Structures and Encroachments" => "Civil",
    ];

    if ($complaintTitle === "Other") {
        $complaintType = "Other";
    } else {
        $complaintType = $complaintTypes[$complaintTitle] ?? "Other";
    }

    if (!empty($complaintDescription) && !empty($complaintTitle)) {
        $userID = $_SESSION['userID'];

        $sql = "INSERT INTO complaints 
                (userID, complaintTitle, complaintDescription, requestDate, complaintStatus, complaintPhoneNumber, complaintAccused, complaintAddress, complainantName, complaintVictim, victimAge, isDeleted, victimRelationship, ActionTaken, latitude, longitude, complaintType)
                VALUES 
                ('$userID', '$complaintTitle', '$complaintDescription', NOW(), '$requestStatus', '$phoneNumber', '$complaintAccused', '$complaintAddress', '$userName', '$complaintVictim', '$userAge', '$isDeleted', '$relationshipVictim', '$isAction', '$latitude', '$longitude', '$complaintType')";
        if (mysqli_query($conn, $sql)) {

            $complaintID = mysqli_insert_id($conn);

            if (!empty($_FILES['evidence']['name'][0])) {
                $uploadDir = "uploads/evidence/";

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                foreach ($_FILES['evidence']['tmp_name'] as $key => $tmpName) {
                    $originalName = $_FILES['evidence']['name'][$key];
                    $fileExt = pathinfo($originalName, PATHINFO_EXTENSION);

                    // Generate unique filename
                    $uniqueName = uniqid('evidence_', true) . '.' . strtolower($fileExt);

                    $targetPath = $uploadDir . $uniqueName;

                    if (move_uploaded_file($tmpName, $targetPath)) {
                        $evidenceQuery = "INSERT INTO complaintEvidence (complaintID, filePath, uploadedAt) 
                          VALUES ('$complaintID', '$targetPath', NOW())";
                        mysqli_query($conn, $evidenceQuery);
                    }
                }

            }

            echo "<script>window.location.href='complaints.php?page=makeComplaint&success';</script>";

            exit();
        } else {
            echo "<div class='alert alert-danger'>Error submitting complaint.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Please fill in all fields.</div>";
    }
}
?>


<div class="content outline p-md-5 p-4">

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-body py-4">
                    <!-- <div class="mb-3">
                        <span style="font-size: 48px;">✅</span>
                    </div> -->
                    <h5 class="fw-bold" style="color: #19AFA5;">Success!</h5>
                    <p class="mb-0">✅ Complaint submitted successfully!</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn filterButton" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <script>
            // Show modal on page load
            document.addEventListener('DOMContentLoaded', function () {
                var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();

                if (window.history.replaceState) {
                    const url = new URL(window.location);
                    url.searchParams.delete('success');
                    window.history.replaceState({}, document.title, url.toString());
                }
            });
        </script>
    <?php endif; ?>


    <div class="row">
        <div class="col-4 col-md-4">
            <div class="d-flex justify-content-center align-items-center mb-3">
                <a href="tel:09174528364" class="d-flex align-items-center text-decoration-none">
                    <div class="card p-2 p-md-3 text-center rounded-4" style="background-color: #19AFA5;">
                        <img src="assets/images/reports/call logo.png" alt="Call Logo" class="img-fluid"
                            style="width: 16px; height: 16px;">
                    </div>
                    <div class="fs-6 ms-2 d-none d-md-block" style="color: #19AFA5;">0917 452 8364</div>
                </a>
            </div>
        </div>

        <div class="col-4 col-md-4">
            <div class="d-flex justify-content-center mb-3">
                <div class="card p-2 p-md-3 text-center" style="background-color: #19AFA5; border: none;">
                    <h2 class="fw-bold fs-6 fs-md-5 text-light mb-0 d-none d-md-block">Make A Complaint</h2>
                    <h2 class="fw-bold text-light mb-0 d-block d-md-none complaint-text" style>Make A Complaint</h2>
                </div>
            </div>
        </div>

        <div class="col-4 col-md-4">
            <div class="d-flex justify-content-center align-items-center mb-3">
                <a href="https://www.facebook.com/sanantonioofficial" target="_blank"
                    class="d-flex align-items-center text-decoration-none">
                    <div class="card p-2 p-md-3 text-center rounded-4" style="background-color: #19AFA5;">
                        <img src="assets/images/reports/facebook logo.png" alt="Facebook Logo" class="img-fluid"
                            style="width: 16px; height: 16px;">
                    </div>
                    <div class="fs-6 ms-2 d-none d-md-block" style="color: #19AFA5;">San Antonio</div>
                </a>
            </div>
        </div>
    </div>

    <div class="p-2">
        <form action="" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
            <!-- Row 1 -->
            <div class="container">
                <div class="row">
                    <!-- 1st row -->
                    <div class="col-12 col-md-6">
                        <div class="row mb-2">
                            <div class="d-flex align-items-center">
                                <label for="title" class="form-label me-2"
                                    style="color: #19AFA5; min-width: 80px;">Title:</label>
                                <select class="form-select" id="title" name="title" required>
                                    <option value="" disabled selected>Select Complaint Type</option>
                                    <option value="Physical Assault and Threats">Physical Assault and Threats</option>
                                    <option value="Alcohol-Related Disturbances">Alcohol-Related Disturbances</option>
                                    <option value="Curfew Violations">Curfew Violations</option>
                                    <option value="Smoking and Littering Violations">Smoking and Littering Violations
                                    </option>
                                    <option value="Vandalism and Property Damage">Vandalism and Property Damage</option>
                                    <option value="Theft and Stolen Property Incidents">Theft and Stolen Property
                                        Incidents</option>

                                    <option value="Noise Complaints">Noise Complaints</option>
                                    <option value="Boundary and Land Disputes">Boundary and Land Disputes</option>
                                    <option value="Neighborhood Quarrels">Neighborhood Quarrels</option>
                                    <option value="Animal-Related Complaints">Animal-Related Complaints</option>
                                    <option value="Youth-Related Issues">Youth-Related Issues</option>
                                    <option value="Barangay Clearance and Permit Concerns">Barangay Clearance and Permit Concerns</option>
                                    <option value="Garbage and Sanitation Complaints">Garbage and Sanitation Complaints
                                    </option>
                                    <option value="Traffic and Parking Issues">Traffic and Parking Issues</option>
                                    <option value="Water Supply Disputes">Water Supply Disputes</option>
                                    <option value="Business-Related Conflicts">Business-Related Conflicts</option>
                                    <option value="Illegal Structures and Encroachments">Illegal Structures and Encroachments</option>

                                    <option value="Other">Other</option>

                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class=" d-flex align-items-center mt-md-0">
                                <label for="address" class="form-label me-2"
                                    style="color: #19AFA5; min-width: 80px;">Address:</label>
                                <input type="text" class="form-control" id="address" name="address" required
                                    placeholder="Address">
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="d-flex align-items-center">
                                <label for="victim" class="form-label me-2"
                                    style="color: #19AFA5; min-width: 80px;">Victim:</label>
                                <input type="text" class="form-control" id="victim" name="victim"
                                    placeholder="Optional">
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="d-flex align-items-center">
                                <label for="relationship" class="form-label me-2"
                                    style="color: #19AFA5; min-width: 80px;">Relation:</label>
                                <input type="text" class="form-control" id="relationship" placeholder="Optional"
                                    name="relationship">
                            </div>
                        </div>


                        <!-- 2nd row -->
                        <div class="row mb-2">
                            <div class="d-flex align-items-center">
                                <label for="phone" class="form-label me-2"
                                    style="color: #19AFA5; min-width: 80px;">Phone:</label>
                                <input type="number" class="form-control" id="phone" name="phoneNumber"
                                    value="<?php echo htmlspecialchars($userPhone ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="d-flex align-items-center">
                                <label for="accused" class="form-label me-2"
                                    style="color: #19AFA5; min-width: 80px;">Accused:</label>
                                <input type="text" class="form-control" id="accused" name="accused"
                                    placeholder="Optional">
                            </div>
                        </div>
                    </div>

                    <!-- map -->
                    <div class="col-12 col-md-6">
                        <div class="card rounded-5 border-1" style="border-color: #19AFA5;">
                            <div class="rounded-5" style="position: relative; height: 288px; overflow: hidden;">
                                <div id="map" name="map" style="height: 100%;"></div>
                            </div>
                        </div>

                        <input type="hidden" name="latitude" id="lat">
                        <input type="hidden" name="longitude" id="lng">
                    </div>
                </div>

                <div class="fs-6 fw-bold mb-2" style="color: #19AFA5">Write A Statement</div>

                <!-- Textarea -->
                <div class="mb-3">
                    <textarea id="reportContent" name="complaintDescription" class="form-control" rows="15"
                        style="resize: none; height: 30vh; border-color: #19AFA5;" required
                        placeholder="Type here..."></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold" style="color: #19AFA5 ">Upload Evidence</label>
                    <input type="file" id="fileInput" name="evidence[]" accept="image/*" multiple
                        style="display: none;">
                    <button type="button" class="btn text-light" style="background-color: #19AFA5" id="uploadBtn">Choose
                        Images</button>
                </div>

                <div class="row g-3" id="cardRow">
                    <!-- Image cards appear here -->
                </div>

                <!-- Submit buttons -->
                <div class="d-flex justify-content-center justify-content-md-end mt-2 gap-2">
                    <a href="complaints.php?page=complaintSection.php" class="btn filterButton">Cancel</a>

                    <button type="button" class="btn filterButton" id="submitBtn">
                        Submit
                    </button>

                </div>

                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: #19AFA5;">
                                <h5 class="modal-title text-light">Confirm Submission</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-dark">
                                Are you sure you want to submit?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn filterButton" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn filterButton" name="submit">Confirm</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Warning Modal -->
                <div class="modal fade" id="warningModal" tabindex="-1" aria-labelledby="warningModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: #dc3545;">
                                <h5 class="modal-title text-light">Warning</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Please fill out all required fields before submitting.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn filterButton" data-bs-dismiss="modal">OK</button>
                            </div>
                        </div>
                    </div>
                </div>


        </form>

        <!-- File size limit modal -->
        <div class="modal fade" id="fileSizeModal" tabindex="-1" aria-labelledby="fileSizeModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center">
                    <div class="modal-body">
                        <p class="fw-bold text-danger mb-0">❌ Total file size limit exceeded (5 MB maximum).</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn filterButton" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
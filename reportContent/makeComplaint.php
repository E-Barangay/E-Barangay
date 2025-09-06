<?php
if (isset($_POST['submit'])) {
    $reportContent = $_POST['reportContent'];
    $title = $_POST['title'];
    $requestStatus = 'pending';

    if (!empty($reportContent) && !empty($title)) {
        $userID = $_SESSION['userID'];

        $sql = "INSERT INTO reports (userID, reportTitle, reportDescription, requestDate, requestStatus)
                VALUES ('$userID', '$title', '$reportContent', NOW(), '$requestStatus')";
        mysqli_query($conn, $sql);
    } else {
        echo "<div class='alert alert-danger'>Please fill in all fields.</div>";
    }
}
?>

<div class="content outline p-md-5 p-4">

    <div class="col">
        <div class="row">
            <div class="col-12 col-md-4">
            </div>

            <div class="col-12 col-md-4">
                <div class="d-flex justify-content-center mb-3">
                    <div class="card p-3 text-center" style="background-color: #0C8888; border: none;">
                        <h2 class="fw-bold fs-5 text-light mb-0">Make A Report</h2>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-4">

                <div class="d-flex justify-content-center align-items-center mb-3">
                    <div class="card p-3 text-center rounded-4" style="background-color: #0C8888;">
                        <img src="assets/images/reports/call logo.png" alt="Report Icon"
                            style="width: 20px; height: 20px;">
                    </div>
                    <div class="fs-6 ms-2" style="color: #0C8888;">0917 452 8364</div>
                </div>
            </div>
            <div class="col-12 col-md-4">
            </div>

            <div class="col-12 col-md-4">
                <div class="d-flex justify-content-center align-items-center mb-3">
                    <div class="card p-3 text-center rounded-4" style="background-color: #0C8888;">
                        <img src="assets/images/reports/facebook logo.png" alt="Report Icon"
                            style="width: 20px; height: 20px;">
                    </div>
                    <div class="fs-6 ms-2" style="color: #0C8888;">San Antonio</div>
                </div>
            </div>

        </div>
    </div>




    <div class="p-2">
        <form action="" method="post">
            <!-- Row 1 -->
            <div class="container">
                <!-- 1st row -->
                <div class="row mb-2">
                    <div class="col-md-6 d-flex align-items-center mb-2">
                        <label for="title" class="form-label me-2"
                            style="color: #19AFA5; min-width: 80px;">Title:</label>
                        <select class="form-select" id="title" name="title" required>
                            <option value="" disabled selected></option>
                            <option value="mr">Mr.</option>
                            <option value="ms">Ms.</option>
                            <option value="mrs">Mrs.</option>
                            <option value="dr">Dr.</option>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-center">
                        <label for="address" class="form-label me-2"
                            style="color: #19AFA5; min-width: 80px;">Address:</label>
                        <input type="text" class="form-control" id="address" name="address" required>
                    </div>
                </div>

                <!-- 2nd row -->
                <div class="row mb-2">
                    <div class="col-md-6 d-flex align-items-center mb-2">
                        <label for="phone" class="form-label me-2"
                            style="color: #19AFA5; min-width: 80px;">Phone:</label>
                        <input type="text" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div class="col-md-6 d-flex align-items-center">
                        <label for="accused" class="form-label me-2"
                            style="color: #19AFA5; min-width: 80px;">Accused:</label>
                        <input type="text" class="form-control" id="accused" name="accused" required>
                    </div>
                </div>
            </div>


            <div class="fs-6 fw-bold mb-2" style="color: #19AFA5">Write A Statement</div>

            <!-- Textarea -->
            <div class="mb-3">
                <textarea id="reportContent" name="reportContent" class="form-control" rows="15"
                    style="resize: none; height: 30vh; border-color: #19AFA5;" required></textarea>
            </div>

            <style>
                .thumb-card {
                    position: relative;
                    height: 100px;
                    border-radius: 0.5rem;
                    overflow: hidden;
                    border: 2px solid #19AFA5;
                }

                .thumb-card img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                }

                .remove-btn {
                    position: absolute;
                    top: 4px;
                    right: 4px;
                    background: rgba(0, 0, 0, 0.5);
                    border: none;
                    color: white;
                    width: 24px;
                    height: 24px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 16px;
                    cursor: pointer;
                }
            </style>

            <div class="mb-3">
                <label class="form-label fw-bold" style="color: #19AFA5 ">Upload Evidence</label>
                <input type="file" id="fileInput" accept="image/*" multiple style="display:none">
                <button type="button" class="btn text-light" style="background-color: #19AFA5" id="uploadBtn">Choose
                    Images</button>
            </div>

            <div class="row g-3" id="cardRow">
                <!-- Image cards appear here -->
            </div>

            <!-- Submit buttons -->
            <div class="d-flex justify-content-center justify-content-md-end mt-2 gap-2">
                <!-- <button type="button" class="btn filterButton me-3">Upload</button> -->

                <button type="button" class="btn filterButton" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    Cancel
                </button>

                <button type="button" class="btn filterButton" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    Submit
                </button>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-body">
                            Confirm Submission?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success" name="submit">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="assets/js/signUp/report.js"></script>
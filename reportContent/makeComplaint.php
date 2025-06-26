<?php
if (isset($_POST['submit'])) {
    $reportContent = $_POST['reportContent'];
    $title = $_POST['title'];
    $requestStatus = 'sent';

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
    <div class="d-flex justify-content-center mb-3">
        <div class="card p-3 text-center" style="background-color: #0C8888; border: none;">
            <h2 class="fw-bold fs-5 text-light mb-0">Make A Report</h2>
        </div>
    </div>
    <form action="" method="post">
        <div class="mb-3">
            <div class="d-flex flex-row justify-content-start align-items-center">
                <label for="exampleFormControlInput1" class="form-label me-3">Title:</label>
                <input type="text" class="form-control w-100" name="title" id="exampleFormControlInput1" placeholder=""
                    required>
            </div>
        </div>

        <textarea id="reportContent" name="reportContent" class="form-control" rows="15"
            style="resize: none; height: 50vh;" required></textarea>

        <div class="d-flex justify-content-center justify-content-md-end mt-2">
            <!-- <button type="button" class="btn filterButton me-3">Upload</button> -->
            <button type="button" class="btn filterButton" data-bs-toggle="modal" data-bs-target="#exampleModal">
                Submit
            </button>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
    </form> <!-- Close the form here -->
</div>
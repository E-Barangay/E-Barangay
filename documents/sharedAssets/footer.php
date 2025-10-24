<div class="d-flex justify-content-end border-top mt-auto">
    <a href="../documents.php">
        <button class="btn btn-secondary cancelButton my-4 mx-2" type="button">Cancel</button>
    </a>
    <button class="btn btn-primary confirmButton my-4" id="confirmButton" type="button" data-bs-toggle="modal" data-bs-target="#confirmationModal">
        Confirm Request
    </button>
</div>

<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            
            <div class="modal-header" style="background-color: #19AFA5; color: white;">
                <h1 class="modal-title fs-5 text-start" id="confirmationModalLabel">Confirm Request</h1>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <p class="mb-0">Are you sure you want to proceed with this request?</p>
            </div>

            <div class="modal-footer d-flex justify-content-end">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">No</button>
                <button type="submit" class="btn btn-primary yesButton px-4" name="yes">Yes</button>
            </div>
            
        </div>
    </div>
</div>

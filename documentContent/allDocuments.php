<?php

$allDocumentsQuery = "SELECT * FROM documentTypes ORDER BY documentName ASC";
$allDocumentsResult = executeQuery($allDocumentsQuery);

while ($allDocumentsRow = mysqli_fetch_assoc($allDocumentsResult)) {
    ?>

    <div class="col-6 col-md-4 col-lg-4 p-1">
        <div class="documentCard card">
            <img src="assets/images/documents/<?php echo $allDocumentsRow['documentImage'] ?>" class="card-img-top"
                style="width: 100%; height: 500px; object-fit: cover;" alt="...">
            <div class="mt-auto">
                <button type="button" class="btn btn-primary documentButton mt-2" data-bs-toggle="modal" data-bs-target="#documentModal">
                    <?php echo $allDocumentsRow['documentName'] ?>
                </button>
                <input type="hidden" placeholder="<?php echo $allDocumentsRow['documentName'] ?>">
            </div>
        </div>
    </div>

<?php } ?>
<?php

$mioDocumentsQuery = "SELECT * FROM documentTypes WHERE categoryID = 2";
$mioDocumentsResult = executeQuery($mioDocumentsQuery);

while ($mioDocumentsRow = mysqli_fetch_assoc($mioDocumentsResult)) {
    ?>
    
    <div class="col-6 col-md-4 col-lg-4 p-1">
        <div class="documentCard card">
            <img src="assets/images/announcements/image.png" class="card-img-top"
                style="width: 100%; height: 500px; object-fit: cover;" alt="...">
            <div class="mt-auto d-flex">
                <button class="btn btn-primary documentButton mt-2"><?php echo $mioDocumentsRow['documentName'] ?></button>
            </div>
        </div>
    </div>

<?php } ?>
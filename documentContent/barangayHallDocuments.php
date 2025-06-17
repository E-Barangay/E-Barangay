<?php

$barangayHallDocumentsQuery = "SELECT * FROM documentTypes WHERE categoryID = 1";
$barangayHallDocumentsResult = executeQuery($barangayHallDocumentsQuery);

while ($barangayHallDocumentsRow = mysqli_fetch_assoc($barangayHallDocumentsResult)) {
    ?>

    <div class="col-6 col-md-4 col-lg-4 p-1">
        <div class="documentCard card">
            <img src="assets/images/announcements/image.png" class="card-img-top"
                style="width: 100%; height: 500px; object-fit: cover;" alt="...">
            <div class="mt-auto d-flex">
                <button class="btn btn-primary documentButton mt-2"><?php echo $barangayHallDocumentsRow['documentName'] ?></button>
            </div>
        </div>
    </div>

<?php } ?>
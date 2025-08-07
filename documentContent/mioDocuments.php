<?php

while ($mioDocumentsRow = mysqli_fetch_assoc($mioDocumentsResult)) {
    ?>

    <div class="col-6 col-md-4 col-lg-4 p-1">
        <div class="documentCard card my-2">
            <img src="assets/images/documents/<?php echo $mioDocumentsRow['documentImage'] ?>" class="card-img-top"
                style="width: 100%; height: 500px; object-fit: cover;" alt="...">
            <div class="mt-auto">
                <a href="documents/documentView.php?documentTypeID=<?php echo $mioDocumentsRow['documentTypeID'] ?>">
                    <button type="button" class="btn btn-primary documentButton mt-2">
                        <?php echo $mioDocumentsRow['documentName'] ?>
                    </button>
                </a>
            </div>
        </div>
    </div>

<?php } ?>
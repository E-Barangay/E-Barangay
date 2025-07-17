<?php

while ($allDocumentsRow = mysqli_fetch_assoc($allDocumentsResult)) {
    ?>

    <div class="col-6 col-md-4 col-lg-4 p-1">
        <div class="documentCard card my-2">
            <img src="assets/images/documents/<?php echo $allDocumentsRow['documentImage'] ?>" class="card-img-top"
                style="width: 100%; height: 500px; object-fit: cover;" alt="...">
            <div class="mt-auto">
                <a href="documentContent/document.php?documentTypeID=<?php echo $allDocumentsRow['documentTypeID'] ?>">
                    <button type="button" class="btn btn-primary documentButton mt-2">
                        <?php echo $allDocumentsRow['documentName'] ?>
                    </button>
                </a>
            </div>
        </div>
    </div>

<?php } ?>
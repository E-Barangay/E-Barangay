<?php

while ($barangayHallDocumentsRow = mysqli_fetch_assoc($barangayHallDocumentsResult)) {
    ?>

    <div class="col-6 col-md-4 col-lg-4 p-1">
        <div class="documentCard card my-2">
            <img src="assets/images/documents/<?php echo $barangayHallDocumentsRow['documentImage'] ?>" class="card-img-top"
                style="width: 100%; height: 500px; object-fit: cover;" alt="...">
            <div class="mt-auto">
                <a href="documents/documentView.php?documentTypeID=<?php echo $barangayHallDocumentsRow['documentTypeID'] ?>">
                    <button type="button" class="btn btn-primary documentButton mt-2">
                        <?php echo $barangayHallDocumentsRow['documentName'] ?>
                    </button>
                </a>
            </div>
        </div>
    </div>

<?php } ?>
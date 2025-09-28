<?php

while ($documentsRow = mysqli_fetch_assoc($documentsResult)) {
    ?>

    <div class="col-6 col-md-4 col-lg-4 p-1">
        <div class="documentCard card my-2">
            <img src="assets/images/documents/<?php echo $documentsRow['documentImage'] ?>" class="card-img-top"
                style="width: 100%; height: 500px; object-fit: cover;" alt="...">
            <div class="mt-auto">
                <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#barangayClearanceModal">
                    <a href="documents/documentView.php?documentTypeID=<?php echo $documentsRow['documentTypeID'] ?>">
                        <button type="button" class="btn btn-primary documentButton mt-2">
                            <?php echo $documentsRow['documentName'] ?>
                        </button>
                    </a>
                </button>
            </div>
        </div>
    </div>

<?php } ?>
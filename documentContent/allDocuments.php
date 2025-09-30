<?php

function toCamelCase($string) {
    $string = ucwords(strtolower($string));
    $string = str_replace(' ', '', $string);
    return lcfirst($string);
}

while ($documentsRow = mysqli_fetch_assoc($documentsResult)) { ?>

    <div class="col-6 col-md-4 col-lg-4 p-1">
        <div class="documentCard card my-2">
            <img src="assets/images/documents/<?php echo $documentsRow['documentImage'] ?>" class="card-img-top"
                style="width: 100%; height: 500px; object-fit: cover;" alt="...">
            <div class="mt-auto">
                <button class="btn btn-primary documentButton mt-2" type="button" data-bs-toggle="modal" data-bs-target="#<?php echo toCamelCase($documentsRow['documentName']); ?>">
                    <?php echo $documentsRow['documentName'] ?>
                </button>
            </div>
        </div>
    </div>

<?php } ?>
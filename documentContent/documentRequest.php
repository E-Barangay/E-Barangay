<?php

$documentRequestQuery = "SELECT * FROM documents LEFT JOIN documentTypes ON documents.documentTypeID = documentTypes.documentTypeID";
$documentRequestResult = executeQuery($documentRequestQuery);

?>

<div class="col">
    <table class="table table-striped text-center">
        <thead>
            <tr>
                <th class="align-middle" scope="col">No.</th>
                <th class="align-middle" scope="col">Date Issued</th>
                <th class="align-middle" scope="col">Document Type</th>
                <th class="align-middle" scope="col">Purpose</th>
                <th class="align-middle" scope="col">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php

            $counter = 1;
            while ($documentRequestRow = mysqli_fetch_assoc($documentRequestResult)) { ?>

                <tr>
                    <th scope="row" class="align-middle"><?php echo $counter++; ?></th>
                    <td class="align-middle"><?php echo date("F j, Y h:i a", strtotime($documentRequestRow['requestDate'])); ?></td>
                    <td class="align-middle"><?php echo $documentRequestRow['documentName'] ?></td>
                    <td class="align-middle"><?php echo $documentRequestRow['purpose'] ?></td>
                    <td class="align-middle"><?php echo $documentRequestRow['documentStatus'] ?></td>
                </tr>

            <?php } ?>
        </tbody>
    </table>
</div>
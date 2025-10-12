<?php

$documentRequestQuery = "SELECT * FROM documents LEFT JOIN documentTypes ON documents.documentTypeID = documentTypes.documentTypeID";
$documentRequestResult = executeQuery($documentRequestQuery);

?>

<div class="col">
    <div class="card p-3 text-center" style="background-color: #0C8888; border: none;">
        <h2 class="fw-bold fs-5 text-light mb-0">Your Submitted Complaints</h2>
    </div>

    <div class="table-responsive">
        <table class="table table-striped text-center">

            <thead>
                <tr>
                    <th class="align-middle" scope="col">Document Type</th>
                    <th class="align-middle" scope="col">Purpose</th>
                    <th class="align-middle" scope="col">Date Issued</th>
                    <th class="align-middle" scope="col">Status</th>
                </tr>
            </thead>

            <tbody>

                <?php

                    $counter = 1;

                    if(mysqli_num_rows($documentRequestResult) > 0) {
                        while ($documentRequestRow = mysqli_fetch_assoc($documentRequestResult)) { ?>

                            <tr>
                                <td class="align-middle"><?php echo $documentRequestRow['documentName'] ?></td>
                                <td class="align-middle"><?php echo $documentRequestRow['purpose'] ?></td>
                                <td class="align-middle"><?php echo date("F j, Y h:i a", strtotime($documentRequestRow['requestDate'])); ?></td>
                                <td class="align-middle"><?php echo $documentRequestRow['documentStatus'] ?></td>
                            </tr>

                    <?php } 
                
                    } else { ?>
                        
                        <tr>

                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-2"></i>
                                <p class="text-muted m-0">You have not made any document requests yet.</p>
                            </td>
                            
                        </tr>

                    <?php } ?>

            </tbody>

        </table>
    </div>
</div>
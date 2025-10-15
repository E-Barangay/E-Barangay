<?php

$documentRequestQuery = "SELECT * FROM documents LEFT JOIN documentTypes ON documents.documentTypeID = documentTypes.documentTypeID ORDER BY requestDate DESC";
$documentRequestResult = executeQuery($documentRequestQuery);

?>

<div class="col p-1">
    
    <div class="card py-2 mt-2 text-center" style="background-color: #19AFA5; border: none; border-radius: 12px;">
        <span class="m-0" style="font-size: clamp(16px, 4vw, 20px); font-weight: bold; color: white;">Your Submitted Request</span>
    </div>

    <div class="table-responsive">
        <table class="table table-striped text-center">

            <thead>
                <tr>
                    <th class="align-middle py-3" scope="col"style="white-space: nowrap;">Document Type</th>
                    <th class="align-middle py-3" scope="col"style="white-space: nowrap;">Purpose</th>
                    <th class="align-middle py-3" scope="col"style="white-space: nowrap;">Date Requested</th>
                    <th class="align-middle py-3" scope="col"style="white-space: nowrap;">Status</th>
                </tr>
            </thead>

            <tbody>

                <?php

                    $counter = 1;

                    if(mysqli_num_rows($documentRequestResult) > 0) {
                        while ($documentRequestRow = mysqli_fetch_assoc($documentRequestResult)) { ?>

                            <tr>
                                <td class="align-middle" style="white-space: nowrap;"><?php echo $documentRequestRow['documentName'] ?></td>
                                <td class="align-middle" style="white-space: nowrap;"><?php echo !empty($documentRequestRow['purpose']) ? $documentRequestRow['purpose'] : '<span class="text-muted">N/A</span>'; ?></td>
                                <td class="align-middle" style="white-space: nowrap;"><?php echo date("F j, Y h:i a", strtotime($documentRequestRow['requestDate'])); ?></td>
                                <td class="align-middle" style="white-space: nowrap;"><?php echo $documentRequestRow['documentStatus'] ?></td>
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
<?php

$page = 1;
$start = 0;

$limit = 20;
$countQuery = "SELECT COUNT(*) AS total FROM documents WHERE userID = $userID AND documentStatus != 'Archived'";
$countResult = executeQuery($countQuery);
$total = mysqli_fetch_assoc($countResult)['total'];
$pages = ceil($total / $limit);

$documentRequestQuery = "SELECT * FROM documents LEFT JOIN documentTypes ON documents.documentTypeID = documentTypes.documentTypeID WHERE userID = $userID AND documentStatus != 'Archived' ORDER BY requestDate DESC";
$documentRequestResult = executeQuery($documentRequestQuery);

?>

<div class="col p-1">
    
    <div class="card py-3 mt-2 text-center" style="background-color: #19AFA5; border: none; border-radius: 12px;">
        <span class="m-0" style="font-size: clamp(16px, 4vw, 20px); font-weight: bold; color: white;">Your Submitted Request</span>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">

            <thead>
                <tr>
                    <th class="align-middle py-3" scope="col"style="white-space: nowrap;">Document Type</th>
                    <th class="align-middle py-3" scope="col"style="white-space: nowrap;">Purpose</th>
                    <th class="align-middle py-3" scope="col"style="white-space: nowrap;">Date Requested</th>
                    <th class="align-middle py-3" scope="col"style="white-space: nowrap;">Status</th>
                    <th class="align-middle py-3 text-center" scope="col"style="white-space: nowrap;">Action</th>
                </tr>
            </thead>

            <tbody>

                <?php

                    if(mysqli_num_rows($documentRequestResult) > 0) {
                        while ($documentRequestRow = mysqli_fetch_assoc($documentRequestResult)) { ?>

                            <tr>
                                <td class="align-middle" style="white-space: nowrap;"><?php echo $documentRequestRow['documentName'] ?></td>
                                <td class="align-middle" style="white-space: nowrap;"><?php echo $documentRequestRow['purpose'] ?></td>
                                <td class="align-middle" style="white-space: nowrap;">
                                    <?php 
                                        $date = date("F j, Y", strtotime($documentRequestRow['requestDate']));
                                        $time = date("h:i A", strtotime($documentRequestRow['requestDate']));
                                    ?>
                                    <div>
                                        <span><?= $date ?></span><br>
                                        <small class="text-muted"><?= $time ?></small>
                                    </div>
                                </td>
                                <td class="align-middle" style="white-space: nowrap;"><?php echo $documentRequestRow['documentStatus'] ?></td>
                                <td class="align-middle text-center" style="white-space: nowrap;">
                                    <?php if ($documentRequestRow['documentStatus'] === 'Cancelled' || $documentRequestRow['documentStatus'] === 'Denied') { ?>

                                        <button type="button" class="btn btn-sm restoreButton" data-bs-toggle="modal" data-bs-target="#restoreModal<?php echo $documentRequestRow['documentID']; ?>">
                                            <i class="fa-solid fa-rotate-left"></i>
                                        </button>

                                        <div class="modal fade" id="restoreModal<?php echo $documentRequestRow['documentID']; ?>" tabindex="-1" aria-labelledby="restoreModalLabel<?php echo $documentRequestRow['documentID']; ?>" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    
                                                    <div class="modal-header" style="background-color: #19AFA5; color: white;">
                                                        <h1 class="modal-title fs-5 text-start" id="restoreModalLabel<?php echo $documentRequestRow['documentID']; ?>">Restore Request</h1>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>

                                                    <div class="modal-body">
                                                        <p class="mb-0 text-start">Are you sure you want restore this request?</p>
                                                    </div>

                                                    <div class="modal-footer d-flex justify-content-end">
                                                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                                                        <form method="POST">
                                                            <input type="hidden" name="documentID" value="<?php echo $documentRequestRow['documentID']; ?>">
                                                            <button type="submit" class="btn btn-primary restoreRequestButton px-4" name="restoreRequestButton">Confirm Restore</button>
                                                        </form>
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                        </div>

                                    <?php } elseif ($documentRequestRow['documentStatus'] === 'Pending') { ?>

                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal<?php echo $documentRequestRow['documentID']; ?>">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>

                                        <div class="modal fade" id="cancelModal<?php echo $documentRequestRow['documentID']; ?>" tabindex="-1" aria-labelledby="cancelModalLabel<?php echo $documentRequestRow['documentID']; ?>" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    
                                                    <div class="modal-header" style="background-color: #dc3545; color: white;">
                                                        <h1 class="modal-title fs-5 text-start" id="cancelModalLabel<?php echo $documentRequestRow['documentID']; ?>">Cancel Request</h1>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>

                                                    <div class="modal-body">
                                                        <p class="mb-0 text-start">Are you sure you want to cancel this request?</p>
                                                    </div>

                                                    <div class="modal-footer d-flex justify-content-end">
                                                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                                                        <form method="POST">
                                                            <input type="hidden" name="documentID" value="<?php echo $documentRequestRow['documentID']; ?>">
                                                            <button type="submit" class="btn btn-danger px-4" name="confirmCancelButton">Confirm Cancel</button>
                                                        </form>
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                        </div>

                                    <?php } elseif ($documentRequestRow['documentStatus'] === 'Approved' || $documentRequestRow['documentStatus'] === 'Denied') { ?>
                                        
                                        <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#approvedModal<?php echo $documentRequestRow['documentID']; ?>">
                                            <i class="fa-solid fa-check"></i>
                                        </button>

                                        <div class="modal fade" id="approvedModal<?php echo $documentRequestRow['documentID']; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-success text-white">
                                                        <h5 class="modal-title">Request Approved</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        This request has already been approved.
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    <?php } ?>

                                </td>
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

    <?php if ($total > 0 && $pages > 1): ?>
        <div class="d-flex justify-content-center align-items-center mt-3">
            <nav aria-label="pageNavigation">
                <ul class="pagination" id="documentPagination">
                    <li class="page-item previousButton me-2" onclick="previousDocumentPage()">
                        <a class="page-link" aria-label="Previous">Previous</a>
                    </li>

                    <?php for ($i = 1; $i <= $pages; $i++): ?>
                        <li class="page-item pageNumber">
                            <a class="page-link" href="javascript:void(0)" onclick="goToDocumentPage(<?php echo $i; ?>)">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item nextButton ms-2" onclick="nextDocumentPage()">
                        <a class="page-link" aria-label="Next">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
    
</div>
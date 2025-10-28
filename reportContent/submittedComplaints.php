<?php
$complaints = [];

if (isset($_SESSION['userID'])) {
  $userID = $_SESSION['userID'];

  $sql = "SELECT * FROM complaints WHERE userID = '$userID' ORDER BY requestDate ASC";
  $result = mysqli_query($conn, $sql);

  if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
      $imgQuery = "SELECT filePath FROM complaintEvidence WHERE complaintID = '{$row['complaintID']}'";
      $imgResult = mysqli_query($conn, $imgQuery);
      $images = [];
      if ($imgResult && mysqli_num_rows($imgResult) > 0) {
        while ($imgRow = mysqli_fetch_assoc($imgResult)) {
          $images[] = $imgRow['filePath'];
        }
      }
      $row['images'] = $images;
      $complaints[] = $row;
    }
  }
}
?>


<div class="content">
  <div class="p-md-1 p-0">

  <div class="card py-3 mt-2 mb-2 text-center" style="background-color: #19AFA5; border: none; border-radius: 12px;">
        <span class="m-0" style="font-size: clamp(16px, 4vw, 20px); font-weight: bold; color: white;">Your Submitted Complaints</span>
    </div>

    <!-- <div class="d-flex justify-content-center mb-3">
      <div class="card p-3 text-center" style="background-color: #19AFA5; border: none;">
        <h2 class="fw-bold fs-5 text-light mb-0">Your Submitted Complaints</h2>
      </div>
    </div> -->

    <div class="table-responsive">
      <table class="mt-2 table table-striped text-center">
        <thead>
          <tr>
            <th>No.</th>
            <th>Date Submitted</th>
            <th>Title</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($complaints)): ?>
            <?php foreach ($complaints as $index => $complaint): ?>
              <tr>
                <td><?= $index + 1 ?></td>
                <td><?= date("Y-m-d h:i A", strtotime($complaint['requestDate'])) ?></td>
                <td><?= htmlspecialchars($complaint['complaintTitle']) ?></td>
                <td><?= ucfirst($complaint['complaintStatus']) ?></td>
                <td>
                  <button class="btn btn-sm filterButton" data-bs-toggle="modal"
                    data-bs-target="#complaintModal<?= $complaint['complaintID'] ?>">
                    View
                  </button>
                </td>
              </tr>

              <!-- Modal -->
              <div class="modal fade" id="complaintModal<?= $complaint['complaintID'] ?>" tabindex="-1"
                aria-labelledby="modalLabel<?= $complaint['complaintID'] ?>" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                  <div class="modal-content printableArea">
                    <div class="modal-header" style="background-color: #19AFA5">
                      <h5 class="modal-title text-light" id="modalLabel<?= $complaint['complaintID'] ?>">Complaint Details</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <div class="row mb-2">
                        <div class="col-md-6"><strong>Title:</strong> <?= htmlspecialchars($complaint['complaintTitle']) ?>
                        </div>
                        <div class="col-md-6"><strong>Relation:</strong>
                          <?= htmlspecialchars($complaint['victimRelationship']) ?></div>
                      </div>
                      <div class="row mb-2">
                        <div class="col-md-6"><strong>Address:</strong>
                          <?= htmlspecialchars($complaint['complaintAddress']) ?></div>
                        <div class="col-md-6"><strong>Phone:</strong>
                          <?= htmlspecialchars($complaint['complaintPhoneNumber']) ?></div>
                      </div>
                      <div class="row mb-2">
                        <div class="col-md-6"><strong>Victim:</strong>
                          <?= htmlspecialchars($complaint['complaintVictim']) ?>
                        </div>
                        <div class="col-md-6"><strong>Accused:</strong>
                          <?= htmlspecialchars($complaint['complaintAccused']) ?></div>
                      </div>
                      <div class="mb-3">
                        <strong>Statement:</strong>
                        <p><?= htmlspecialchars($complaint['complaintDescription']) ?></p>
                      </div>

                      <?php if (!empty($complaint['images'])): ?>
                        <div class="mb-3">
                          <strong>Uploaded Evidence:</strong>
                          <div class="d-flex flex-wrap">
                            <?php foreach ($complaint['images'] as $img): ?>
                              <img src="<?= htmlspecialchars($img) ?>" alt="Evidence Image">
                            <?php endforeach; ?>
                          </div>
                        </div>
                      <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                      <!-- <button onclick="printComplaint('complaintModal<?= $complaint['complaintID'] ?>')"
                        class="btn filterButton">Print Complaint</button> -->
                      <button type="button" class="btn filterButton" data-bs-dismiss="modal">Close</button>
                    </div>
                  </div>
                </div>
              </div>

            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center py-4">
                  <i class="fas fa-inbox fa-3x text-muted mb-2"></i>
                  <p class="text-muted m-0">You have not made any complaints yet.</p>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>

<script>
  function printComplaint(modalId) {
    const modalContent = document.querySelector(`#${modalId} .printableArea`).innerHTML;
    const printWindow = window.open('', '_blank', 'width=1000,height=800');
    printWindow.document.write('<html><head><title>Complaint</title>');
    printWindow.document.write('<style>');
    printWindow.document.write(`
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: white !important;
            display: flex;
            justify-content: center; /* horizontal center */
            align-items: flex-start; /* top align */
            min-height: 100vh;
        }
        .print-container {
            width: 80%;
            margin: 20px 0;
        }
        .modal-content { background: white !important; }
        p { white-space: pre-line; margin:0; }
        img {
            max-width: 300px !important;
            height: auto !important;
            margin: 5px 0 !important;
            border: 1px solid #ccc !important;
            padding: 3px !important;
        }
        strong { display: inline-block; width: 120px; }
        .filterButton, .btn-close { display: none !important; }
    `);
    printWindow.document.write('</style></head><body>');
    printWindow.document.write('<div class="print-container">');
    printWindow.document.write(modalContent);
    printWindow.document.write('</div>');
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
  }

</script>
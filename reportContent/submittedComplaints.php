<?php

$reports = [];

if (isset($_SESSION['userID'])) {
  $userID = $_SESSION['userID'];

  $sql = "SELECT * FROM reports WHERE userID = '$userID'";
  $result = mysqli_query($conn, $sql);

  if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
      $reports[] = $row;
    }
  }
}
?>


<div class="content">
  <div class="p-md-5 p-4">
    <div class="d-flex justify-content-center mb-3">
      <div class="card p-3 text-center" style="background-color: #0C8888; border: none;">
        <h2 class="fw-bold fs-5 text-light mb-0">Submitted Reports</h2>
      </div>
    </div>
    <table class="mt-2 table table-striped text-center">
      <thead>
        <tr>
          <th>No.</th>
          <th>Date Issued</th>
          <th>Subject</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($reports)): ?>
          <?php foreach ($reports as $index => $report): ?>
            <tr>
              <td><?= $index + 1 ?></td>
              <td><?= date("Y-m-d h:i A", strtotime($report['requestDate'])) ?></td>
              <td><?= htmlspecialchars($report['reportTitle']) ?></td>
              <td><?= ucfirst($report['requestStatus']) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="4">No reports submitted yet.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
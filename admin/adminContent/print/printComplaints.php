<?php
include_once __DIR__ . "/../../../sharedAssets/connect.php";

$from = isset($_GET['from']) ? trim($_GET['from']) : null;
$to = isset($_GET['to']) ? trim($_GET['to']) : null;

function valid_date($date)
{
  if (!$date)
    return false;
  $check = DateTime::createFromFormat('Y-m-d', $date);
  return $check && $check->format('Y-m-d') === $date;
}

if (!valid_date($from))
  $from = null;
if (!valid_date($to))
  $to = null;

if ($from && $to) {
  $complaintsQuery = "
        SELECT 
            complaintID,
            complaintTitle,
            complaintStatus,
            DATE(requestDate) AS requestDate
        FROM complaints
        WHERE DATE(requestDate) BETWEEN '$from' AND '$to'
        ORDER BY requestDate DESC
    ";
} else {
  $complaintsQuery = "
        SELECT 
            complaintID,
            complaintTitle,
            complaintStatus,
            DATE(requestDate) AS requestDate
        FROM complaints
        ORDER BY requestDate DESC
    ";
}

$complaintsResults = $conn->query($complaintsQuery);

$complaintsData = [];
if ($complaintsResults) {
  while ($row = $complaintsResults->fetch_assoc()) {
    $complaintsData[] = $row;
  }
}

$total = count($complaintsData);
$primaryCriminal = 0;
$resolved = 0;
$escalated = 0;
$vawc = 0;

foreach ($complaintsData as $row) {
  $status = strtolower(trim($row['complaintStatus'] ?? ''));

  if (in_array($status, ['criminal', 'civil'])) {
    $primaryCriminal++;
  }

  if (in_array($status, ['withdrawn', 'repudiated', 'dismissed', 'certified', 'resolved'])) {
    $resolved++;
  }

  if (in_array($status, ['mediation', 'conciliation', 'arbitration', 'pending'])) {
    $escalated++;
  }

  if (strpos($status, 'vawc') !== false) {
    $vawc++;
  }
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Barangay Incidents Report</title>
    <link rel="icon" href="../../../assets/images/logoSanAntonio.png">

  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: Arial, Helvetica, sans-serif;
      padding: 18px;
      font-size: 13px;
    }

    .summary {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-bottom: 12px;
    }

    .summary .card {
      min-width: 120px;
    }

    @media print {
      .no-print {
        display: none;
      }
    }
  </style>
</head>

<body>
  <div class="d-flex justify-content-between mb-3">
    <div>
      <h4>Barangay Incidents Report</h4>
      <?php if ($from && $to): ?>
        <div><small>Period: <?php echo htmlspecialchars($from) . " to " . htmlspecialchars($to); ?></small></div>
      <?php else: ?>
        <div><small>Period: All</small></div>
      <?php endif; ?>
    </div>
    <div class="no-print">
      <button onclick="window.print()" class="btn btn-primary btn-sm">Print / Save as PDF</button>
    </div>
  </div>

  <div class="summary mb-3">
    <div class="card bg-danger text-white">
      <div class="card-body text-center">
        <h5><?php echo $total; ?></h5>
        <small>Total Incidents</small>
      </div>
    </div>
    <div class="card bg-primary text-white">
      <div class="card-body text-center">
        <h5><?php echo $primaryCriminal; ?></h5>
        <small>Primary Criminal</small>
      </div>
    </div>
    <div class="card bg-success text-white">
      <div class="card-body text-center">
        <h5><?php echo $resolved; ?></h5>
        <small>Resolved</small>
      </div>
    </div>
    <div class="card bg-warning text-dark">
      <div class="card-body text-center">
        <h5><?php echo $escalated; ?></h5>
        <small>Escalated</small>
      </div>
    </div>
    <div class="card bg-dark text-white">
      <div class="card-body text-center">
        <h5><?php echo $vawc; ?></h5>
        <small>VAWC Records</small>
      </div>
    </div>
  </div>

  <table class="table table-bordered table-sm">
    <thead class="table-secondary">
      <tr>
        <th>ID</th>
        <th>Type</th>
        <th>Status</th>
        <th>Request Date</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($complaintsData as $row): ?>
        <tr>
          <td><?php echo htmlspecialchars($row['complaintID']); ?></td>
          <td><?php echo htmlspecialchars($row['complaintTitle']); ?></td>
          <td><?php echo htmlspecialchars($row['complaintStatus']); ?></td>
          <td><?php echo htmlspecialchars($row['requestDate']); ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>

</html>
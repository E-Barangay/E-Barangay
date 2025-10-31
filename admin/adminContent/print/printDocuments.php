<?php
include_once __DIR__ . "/../../../sharedAssets/connect.php";

$from = isset($_GET['from']) ? trim($_GET['from']) : null;
$to   = isset($_GET['to']) ? trim($_GET['to']) : null;
$type = isset($_GET['type']) ? trim($_GET['type']) : null; // <--- added

function valid_date($date) {
    if (!$date) return false;
    $check = DateTime::createFromFormat('Y-m-d', $date);
    return $check && $check->format('Y-m-d') === $date;
}

if (!valid_date($from)) $from = null;
if (!valid_date($to)) $to = null;

$whereClauses = [];
if ($from && $to) $whereClauses[] = "DATE(requestDate) BETWEEN '$from' AND '$to'";
if ($type) $whereClauses[] = "documenttypes.documentName = '" . $conn->real_escape_string($type) . "'";

$whereSQL = "";
if (!empty($whereClauses)) {
    $whereSQL = "WHERE " . implode(" AND ", $whereClauses);
}

$servicesQuery  = "
    SELECT 
        documentID,
        documentName,
        documentStatus,
        DATE(requestDate) AS requestDate
    FROM documents
    INNER JOIN documenttypes ON documents.documentTypeID = documenttypes.documentTypeID
    $whereSQL
    ORDER BY requestDate DESC
";

$servicesResults = $conn->query($servicesQuery);

$servicesData = [];
if ($servicesResults) {
    while ($row = $servicesResults->fetch_assoc()) {
        $servicesData[] = $row;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Barangay Services Report</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <style>
body {
  font-family: 'Poppins', sans-serif;
  padding: 20px;
  font-size: 13px;
}
    table {
      font-size: 12px;
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
      <h4>Barangay Services Report</h4>
      <?php if ($from && $to): ?>
        <div><small>Period: <?= htmlspecialchars($from) ?> to <?= htmlspecialchars($to) ?></small></div>
      <?php else: ?>
        <div><small>Period: All</small></div>
      <?php endif; ?>
      <?php if ($type): ?>
        <div><small>Type: <?= htmlspecialchars($type) ?></small></div>
      <?php endif; ?>
    </div>
    <div class="no-print">
      <button onclick="window.print()" class="btn btn-primary btn-sm">Print / Save as PDF</button>
    </div>
  </div>

  <table class="table table-bordered table-sm">
    <thead class="table-secondary">
      <tr>
        <th>Document ID</th>
        <th>Type</th>
        <th>Status</th>
        <th>Request Date</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($servicesData) > 0): ?>
        <?php foreach ($servicesData as $row): ?>
          <tr>
            <td><?= htmlspecialchars($row['documentID']) ?></td>
            <td><?= htmlspecialchars($row['documentName']) ?></td>
            <td><?= htmlspecialchars($row['documentStatus']) ?></td>
            <td><?= htmlspecialchars($row['requestDate']) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="4" class="text-center text-muted">No records found</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</body>
</html>

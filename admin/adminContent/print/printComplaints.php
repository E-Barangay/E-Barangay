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

// FIXED: Added complaintType to the query
if ($from && $to) {
  $complaintsQuery = "
        SELECT 
            complaintID,
            complaintTitle,
            complaintType,
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
            complaintType,
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

$criminal = 0;
$civil = 0;
$others = 0;

$mediation = 0;
$conciliation = 0;
$arbitration = 0;

$repudiated = 0;
$withdrawn = 0;
$pending = 0;
$dismissed = 0;
$certified = 0;
$referred = 0;

// FIXED: Separate logic for complaintType and complaintStatus
foreach ($complaintsData as $row) {
  $type = strtolower(trim($row['complaintType'] ?? ''));
  $status = strtolower(trim($row['complaintStatus'] ?? ''));

  // Count by COMPLAINT TYPE (Nature of Dispute)
  if (stripos($type, 'criminal') !== false) {
    $criminal++;
  } elseif (stripos($type, 'civil') !== false) {
    $civil++;
  } else {
    $others++;
  }

  // Count by COMPLAINT STATUS (Settled Cases)
  if ($status === 'mediation') {
    $mediation++;
  } elseif ($status === 'conciliation') {
    $conciliation++;
  } elseif ($status === 'arbitration') {
    $arbitration++;
  }

  // Count by COMPLAINT STATUS (Unsettled Cases)
  if ($status === 'repudiated') {
    $repudiated++;
  } elseif ($status === 'withdrawn') {
    $withdrawn++;
  } elseif ($status === 'pending') {
    $pending++;
  } elseif ($status === 'dismissed') {
    $dismissed++;
  } elseif ($status === 'certified') {
    $certified++;
  } elseif ($status === 'referred') {
    $referred++;
  }
}

$totalDispute = $criminal + $civil + $others;
$settledTotal = $mediation + $conciliation + $arbitration;
$unsettledTotal = $repudiated + $withdrawn + $pending + $dismissed + $certified + $referred;
$estimatedSavings = $settledTotal * 0;
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Katarungang Pambarangay Compliance Report</title>
  <link rel="icon" href="../../../assets/images/logoSanAntonio.png">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">

  <style>
    .print-container {
      width: auto;
      margin: 0 auto !important;
    }

    @media print {
      @page {
        size: A4 landscape;
        margin: 12mm;
      }

      html,
      body {
        width: 100%;
        margin: 0;
        padding: 0;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }

      .print-container {
        width: 100%;
        display: flex;
        justify-content: center;
        box-sizing: border-box;
      }

      .kp-table {
        width: auto;
        max-width: 100%;
        margin: 0 auto;
        border-collapse: collapse;
      }

      .no-print {
        display: none !important;
      }
    }



    table.kp-table {
      border-collapse: collapse;
      width: 100%;
      table-layout: fixed;
      font-size: 10px;
    }

    table.kp-table th,
    table.kp-table td {
      border: 1px solid #000;
      padding: 4px;
      vertical-align: middle;
      word-wrap: break-word;
    }

    .wrap-center {
      text-align: center;
      white-space: normal;
    }
  </style>
</head>

<body style="font-family: Arial, Helvetica, sans-serif; font-size:11px; background:#fff;">

  <div class="print-container">
    <div class="container-fluid print-wrapper" style="padding:12px 16px;">
      <div class="row mb-2 no-print">
        <div class="col-12">
          <button onclick="window.print()" class="btn btn-primary btn-sm">Print / Save as PDF</button>
        </div>
      </div>

      <div class="row" style="margin-bottom:6px;">
        <div class="col-12 text-center">
          <div style="font-size:14px; font-weight:bold; line-height:1;">
            CONSOLIDATED KATARUNGANG PAMBARANGAY
          </div>
          <div style="font-size:14px; font-weight:bold; line-height:1;">
            COMPLIANCE REPORT ON THE ACTION TAKEN BY THE LUPONG TAGAPAMAYAPA
          </div>

          <?php if ($from && $to): ?>
            <div style="font-size:15px; font-weight:700; margin-top:6px;">
              <?php echo date("F d, Y", strtotime($from)) . " - " . date("F d, Y", strtotime($to)); ?>
            </div>
          <?php else: ?>
            <div style="font-size:15px; font-weight:700; margin-top:6px;">
              All Records
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="row" style="margin-top:6px; margin-bottom:6px;">
        <div class="col-12" style="font-weight:bold; font-size:11px;">
          CONSOLIDATED KATARUNGANG PAMBARANGAY
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <table class="kp-table" style="border:1px solid #000;">
            <thead>
              <tr>
                <th rowspan="3" style="width:80px; font-weight:bold;">PROVINCE<br>CITY<br>(1)</th>
                <th rowspan="3" style="width:40px; font-weight:bold;">C/M</th>

                <th colspan="4" class="wrap-center" style="background:#ffc7ce; font-weight:bold;">NATURE OF DISPUTE (2)</th>

                <th colspan="4" class="wrap-center" style="background:#bdd7ee; font-weight:bold;">SETTLED CASES (3)</th>

                <th colspan="5" class="wrap-center" style="background:#e2efda; font-weight:bold;">UNSETTLED CASES (4)</th>

                <th rowspan="3" style="width:90px; background:#e2efda; font-weight:bold;">REFERRED TO<br>CONCERNED<br>AGENCY<br>(4f)</th>
                <th rowspan="3" style="width:60px; background:#e2efda; font-weight:bold;">TOTAL<br>(4g)</th>
                <th rowspan="3" style="width:110px; background:#fff; font-weight:bold;">ESTIMATED<br>GOVT<br>SAVINGS</th>
              </tr>

              <tr>
                <th style="background:#ffc7ce; font-weight:bold;">CRIMINAL<br>(2a)</th>
                <th style="background:#ffc7ce; font-weight:bold;">CIVIL<br>(2b)</th>
                <th style="background:#ffc7ce; font-weight:bold;">OTHERS<br>(2c)</th>
                <th style="background:#ffc7ce; font-weight:bold;">TOTAL<br>(2d)</th>

                <th style="background:#bdd7ee; font-weight:bold;">MEDIATION<br>(3a)</th>
                <th style="background:#bdd7ee; font-weight:bold;">CONCILIATION<br>(3b)</th>
                <th style="background:#bdd7ee; font-weight:bold;">ARBITRATION<br>(3c)</th>
                <th style="background:#bdd7ee; font-weight:bold;">TOTAL<br>(3d)</th>

                <th style="background:#e2efda; font-weight:bold;">REPUDIATED<br>(4a)</th>
                <th style="background:#e2efda; font-weight:bold;">WITHDRAWN<br>(4b)</th>
                <th style="background:#e2efda; font-weight:bold;">PENDING<br>(4c)</th>
                <th style="background:#e2efda; font-weight:bold;">DISMISSED<br>(4d)</th>
                <th style="background:#e2efda; font-weight:bold;">CERTIFIED<br>(4e)</th>
              </tr>

              <tr>
                <th style="background:#ffc7ce;">&nbsp;</th>
                <th style="background:#ffc7ce;">&nbsp;</th>
                <th style="background:#ffc7ce;">&nbsp;</th>
                <th style="background:#ffc7ce;">&nbsp;</th>

                <th style="background:#bdd7ee;">&nbsp;</th>
                <th style="background:#bdd7ee;">&nbsp;</th>
                <th style="background:#bdd7ee;">&nbsp;</th>
                <th style="background:#bdd7ee;">&nbsp;</th>

                <th style="background:#e2efda;">&nbsp;</th>
                <th style="background:#e2efda;">&nbsp;</th>
                <th style="background:#e2efda;">&nbsp;</th>
                <th style="background:#e2efda;">&nbsp;</th>
                <th style="background:#e2efda;">&nbsp;</th>
              </tr>
            </thead>

            <tbody>
              <tr>
                <td style="text-align:left; font-weight:bold;">CITY OF STO. TOMAS</td>
                <td></td>

                <td style="font-weight:bold;"><?php echo $criminal; ?></td>
                <td style="font-weight:bold;"><?php echo $civil; ?></td>
                <td style="font-weight:bold;"><?php echo $others; ?></td>
                <td style="font-weight:bold;"><?php echo $totalDispute; ?></td>

                <td style="font-weight:bold;"><?php echo $mediation; ?></td>
                <td style="font-weight:bold;"><?php echo $conciliation; ?></td>
                <td style="font-weight:bold;"><?php echo $arbitration; ?></td>
                <td style="font-weight:bold;"><?php echo $settledTotal; ?></td>

                <td style="font-weight:bold;"><?php echo $repudiated; ?></td>
                <td style="font-weight:bold;"><?php echo $withdrawn; ?></td>
                <td style="font-weight:bold;"><?php echo $pending; ?></td>
                <td style="font-weight:bold;"><?php echo $dismissed; ?></td>
                <td style="font-weight:bold;"><?php echo $certified; ?></td>

                <td style="font-weight:bold;"><?php echo $referred; ?></td>
                <td style="font-weight:bold;"><?php echo $unsettledTotal; ?></td>
                <td style="font-weight:bold;">₱<?php echo number_format($estimatedSavings, 2); ?></td>
              </tr>

              <?php for ($i = 0; $i < 7; $i++): ?>
                <tr>
                  <?php for ($c = 0; $c < 18; $c++): ?>
                    <td>&nbsp;</td>
                  <?php endfor; ?>
                </tr>
              <?php endfor; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- NOTE (commented out). You can uncomment when you want it visible -->
      <!--
    <div class="row" style="margin-top:12px;">
      <div class="col-12">
        <div style="background:#ff0000; color:#fff; padding:8px; font-weight:bold;">
          NOTE: KAILANGAN PO BALANCE ANG SETTLED CASES AT UNSETTLED CASES NITO SA TOTAL NG DISPUTE CASES
        </div>
      </div>
    </div>
    -->

      <div class="row" style="margin-top:28px;">
        <div class="col-6" style="padding-left:6px;">
          <div style="font-weight:bold; font-size:11px;">PREPARED AND SUBMITTED BY:</div>
          <div style="height:36px;"></div>
          <div style="font-weight:bold;">Mary Joy M. Martinez</div>
          <div>Lupon Secretary</div>
        </div>

        <div class="col-6 text-end" style="padding-right:6px;">
        </div>
      </div>

    </div>
  </div>

</body>

</html>
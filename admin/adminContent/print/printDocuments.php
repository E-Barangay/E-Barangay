<?php
include_once __DIR__ . "/../../../sharedAssets/connect.php";

$from = isset($_GET['from']) ? trim($_GET['from']) : null;
$to = isset($_GET['to']) ? trim($_GET['to']) : null;
$type = isset($_GET['type']) ? trim($_GET['type']) : null;

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

$whereClauses = [];
if ($from && $to)
  $whereClauses[] = "DATE(documents.requestDate) BETWEEN '$from' AND '$to'";

if ($type && strtoupper($type) !== "ALL") {
  $whereClauses[] = "documenttypes.documentName = '" . $conn->real_escape_string($type) . "'";
}

$whereSQL = "";
if (!empty($whereClauses)) {
  $whereSQL = "WHERE " . implode(" AND ", $whereClauses);
}

$documentTypeName = ($type && strtoupper($type) !== "ALL") ? $type : "ALL DOCUMENT TYPES";

$servicesQuery = "
    SELECT 
        documents.documentID,
        documenttypes.documentName,
        documents.documentStatus,
        DATE(documents.requestDate) AS requestDate,
        DATE(documents.approvalDate) AS approvalDate,
        userinfo.lastName,
        userinfo.firstName,
        userinfo.middleName,
        userinfo.age,
        userinfo.gender,
        userinfo.civilStatus,
        userinfo.occupation,
        CONCAT(
            addresses.streetName, ', ',
            addresses.blockLotNo, ', ',
            addresses.subdivisionName, ', ',
            addresses.purok, ', ',
            addresses.barangayName, ', ',
            addresses.cityName, ', ',
            addresses.provinceName
        ) AS address,
        users.phoneNumber AS contactNumber,
        documents.purpose,
        YEAR(documents.requestDate) AS requestYear,
        MONTH(documents.requestDate) AS requestMonth,
        DAY(documents.requestDate) AS requestDay
    FROM documents
    INNER JOIN documenttypes ON documents.documentTypeID = documenttypes.documentTypeID
    LEFT JOIN userinfo ON documents.userID = userinfo.userID
    LEFT JOIN users ON userinfo.userID = users.userID
    LEFT JOIN addresses ON userinfo.userID = addresses.userInfoID
    $whereSQL
    ORDER BY documenttypes.documentName ASC, documents.requestDate DESC, userinfo.lastName ASC
";

$servicesResults = $conn->query($servicesQuery);
$servicesData = [];
if ($servicesResults) {
  while ($row = $servicesResults->fetch_assoc())
    $servicesData[] = $row;
}

$periodText = "FOR THE MONTH OF " . strtoupper(date('F Y'));
if ($from && $to) {
  $fromDate = new DateTime($from);
  $toDate = new DateTime($to);
  if ($fromDate->format('Y-m') === $toDate->format('Y-m')) {
    $periodText = "FOR THE MONTH OF " . strtoupper($fromDate->format('F Y'));
  } else {
    $periodText = strtoupper($fromDate->format('F Y')) . " TO " . strtoupper($toDate->format('F Y'));
  }
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Barangay Services Report - <?= htmlspecialchars($documentTypeName) ?></title>
    <link rel="icon" href="../../../assets/images/logoSanAntonio.png">


  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <meta name="viewport" content="width=device-width,initial-scale=1">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      position: relative;
      min-height: 100vh;
      margin: 0;
      padding-bottom: 100px;
    }

    .header {
      text-align: center;
      margin-bottom: 20px;
      border-bottom: 3px solid #000;
      padding-bottom: 10px;
    }

    .header h1 {
      font-size: 18px;
      font-weight: bold;
      margin: 5px 0;
    }

    .header h2 {
      font-size: 14px;
      font-weight: bold;
      margin: 3px 0;
    }

    .header p {
      font-size: 12px;
      margin: 2px 0;
    }


    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
      font-size: 10px;
    }

    table th {
      background-color: #31afab;
      border: 1px solid #000;
      padding: 8px 4px;
      text-align: center;
      font-weight: bold;
      font-size: 9px;
    }

    table td {
      border: 1px solid #000;
      padding: 6px 4px;
      text-align: center;
      vertical-align: middle;
    }

    .name-column {
      text-align: left;
      padding-left: 8px;
    }

    .signature-section {
      margin-top: 80px;
      display: flex;
      justify-content: space-between;
    }

    .signature-box {
      text-align: center;
      width: 40%;
    }

    .signature-line {
      border-top: 2px solid #000;
      margin-top: 50px;
      padding-top: 5px;
      font-weight: bold;
    }

    .signature-title {
      font-size: 14px;
      font-style: italic;
      margin-top: 2px;
    }

    @media print {
      .no-print {
        display: none !important;
      }

      body {
        padding: 10px;
      }

      @page {
        size: landscape;
        margin: 0.5in;
      }

      body::after {
        content: "";
        display: block;
        height: 120px;
      }

      .footer-section {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        text-align: right;
        padding: 0 40px 10px 0;
        font-size: 12px;
        background: white;
        z-index: 1;
      }

    }
  </style>
</head>

<body>

  <div class="container-fluid">

    <div class="row">
      <div class="col-12 text-end no-print">
        <button onclick="window.print()" class="btn btn-primary mt-2">🖨️ Print / Save as PDF</button>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="header text-center">

<div class="d-flex justify-content-center align-items-center w-100 mb-3">
  <div class="d-flex align-items-center justify-content-between w-75">
    <img src="../../../assets/images/logoSanAntonio.png"
         class="img-fluid me-2"
         alt="Logo 1"
         width="90">

    <div class="text-center flex-grow-1">
      <h1 class="mb-0 fw-bold">BARANGAY SAN ANTONIO</h1>
      <h2 class="mb-0"><?= strtoupper(htmlspecialchars($documentTypeName)) ?></h2>
      <p class="mb-0"><?= $periodText ?></p>
    </div>

    <img src="../../../assets/images/logoSantoTomas.png"
         class="img-fluid ms-2"
         alt="Logo 2"
         width="90">
  </div>
</div>

</div>
    </div>
    <div class="row">
  <div class="col-12 table-responsive">

    <?php if (strtoupper($documentTypeName) === "ALL DOCUMENT TYPES"): ?>
      <table>
        <thead>
          <tr>
            <th>DOCUMENT TYPE</th>
            <th>REQUEST DATE</th>
            <th>FULL NAME</th>
            <th>PURPOSE</th>
            <th>STATUS</th>
            <th>APPROVAL DATE</th>
            <th>ADDRESS</th>
            <th>CONTACT NUMBER</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($servicesData) > 0): ?>
            <?php foreach ($servicesData as $row): ?>
              <?php $fullName = trim(($row['lastName'] ?? '') . ', ' . ($row['firstName'] ?? '') . ' ' . ($row['middleName'] ?? '')); ?>
              <tr>
                <td><?= htmlspecialchars($row['documentName']) ?></td>
                <td><?= htmlspecialchars($row['requestDate']) ?></td>
                <td class="name-column"><?= htmlspecialchars($fullName) ?></td>
                <td><?= htmlspecialchars($row['purpose'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['documentStatus']) ?></td>
                <td><?= htmlspecialchars($row['approvalDate'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['address'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['contactNumber'] ?? '') ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" style="text-align:center;padding:20px;">No records found</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>

    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th rowspan="2" style="width: 15%;">NAME OF BENEFICIARY/AVAILEE</th>
            <th rowspan="2" style="width: 5%;">AGE</th>
            <th colspan="3" style="width: 12%;">DATE OF BIRTH</th>
            <th colspan="2" style="width: 8%;">SEX/<br>GENDER</th>
            <th rowspan="2" style="width: 12%;">CIVIL STATUS</th>
            <th rowspan="2" style="width: 15%;">OCCUPATION</th>
            <th rowspan="2" style="width: 10%;">STATUS</th>
            <th rowspan="2" style="width: 10%;">REQUEST DATE</th>
          </tr>
          <tr>
            <th>Month</th>
            <th>Date</th>
            <th>Year</th>
            <th>M</th>
            <th>F</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($servicesData) > 0): ?>
            <?php foreach ($servicesData as $row): ?>
              <?php
              $fullName = trim(($row['lastName'] ?? '') . ', ' . ($row['firstName'] ?? '') . ' ' . ($row['middleName'] ?? ''));
              $gender = strtolower($row['gender'] ?? '');
              $isMale = (strpos($gender, 'male') !== false && strpos($gender, 'female') === false);
              $isFemale = (strpos($gender, 'female') !== false);
              ?>
              <tr>
                <td class="name-column"><?= htmlspecialchars($fullName) ?></td>
                <td><?= htmlspecialchars($row['age'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['requestMonth'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['requestDay'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['requestYear'] ?? '') ?></td>
                <td><?= $isMale ? '✓' : '' ?></td>
                <td><?= $isFemale ? '✓' : '' ?></td>
                <td><?= htmlspecialchars($row['civilStatus'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['occupation'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['documentStatus']) ?></td>
                <td><?= htmlspecialchars($row['requestDate']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="11" style="text-align:center;padding:20px;">No records found</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>

    <?php endif; ?>

  </div>
</div>
    <div class="row mt-5">
      <div class="col-12">
        <div class="signature-section">
          <div class="signature-box">
            <div class="signature-line">MRS. MARY JOY M. MARTIREZ</div>
            <div class="signature-title">Barangay Secretary</div>
          </div>
          <div class="signature-box">
            <div class="signature-line">HON.ULYSES M. RELLAMA</div>
            <div class="signature-title">Punong Barangay</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="footer-section d-block"
        style="position:absolute; bottom:0; right:0; width:100%; padding:0 40px 10px 0; z-index:9999; background:transparent; font-size:12px;">

        <div class="d-block text-end" style="margin:0; line-height:1.1; font-size:12px;">
          Prepared by:
        </div>

        <div class="d-block text-end" style="margin:0; line-height:1.1; font-size:12px;">
          DATED: <?= date('m-d-Y') ?>
        </div>

      </div>
    </div>
  </div>
</body>

</html>
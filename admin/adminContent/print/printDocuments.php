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

if (!valid_date($from)) $from = null;
if (!valid_date($to)) $to = null;

$whereClauses = [];
if ($from && $to)
  $whereClauses[] = "DATE(documents.requestDate) BETWEEN '$from' AND '$to'";
if ($type && strtoupper($type) !== "ALL")
  $whereClauses[] = "documenttypes.documentName = '" . $conn->real_escape_string($type) . "'";

$whereSQL = "";
if (!empty($whereClauses))
  $whereSQL = "WHERE " . implode(" AND ", $whereClauses);

$documentTypeName = ($type && strtoupper($type) !== "ALL") ? $type : "ALL DOCUMENT TYPES";

// Query for First Time Job Seeker
$query = "
  SELECT 
    userinfo.lastName,
    userinfo.firstName,
    userinfo.middleName,
    userinfo.age,
    userinfo.gender,
    userinfo.birthDate,
    userinfo.studentLevel,
    userinfo.shsTrack,
    userinfo.collegeCourse,
    userinfo.collegeYear,
    userinfo.outOfSchoolYouth
  FROM userinfo
  INNER JOIN documents ON userinfo.userID = documents.userID
  INNER JOIN documenttypes ON documents.documentTypeID = documenttypes.documentTypeID
  $whereSQL
  ORDER BY userinfo.lastName ASC, userinfo.firstName ASC
";

// Query for All Document Types
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
        documents.purpose
    FROM documents
    INNER JOIN documenttypes ON documents.documentTypeID = documenttypes.documentTypeID
    LEFT JOIN userinfo ON documents.userID = userinfo.userID
    LEFT JOIN users ON userinfo.userID = users.userID
    LEFT JOIN addresses ON userinfo.userID = addresses.userInfoID
    $whereSQL
    ORDER BY documenttypes.documentName ASC, documents.requestDate DESC, userinfo.lastName ASC
";

$result = $conn->query($query);
$rows = [];
if ($result) {
  while ($r = $result->fetch_assoc()) $rows[] = $r;
}

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
  <title>Barangay Report - <?= htmlspecialchars($documentTypeName) ?></title>
  <link rel="icon" href="../../../assets/images/logoSanAntonio.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', sans-serif; margin: 40px; }
    .header-logo { width: 80px; }
    .table th, .table td {
      border: 1px solid #000 !important;
      font-size: 12px;
      text-align: center;
      vertical-align: middle;
      padding: 3px;
    }
    .table thead th {
      background-color: #f4a460 !important;
      color: #000;
      font-weight: bold;
      text-transform: uppercase;
    }
    .table-all thead th {
      background-color: #31afab !important;
    }
    .title-section h4 { font-weight: 700; margin-bottom: 0; }
    .title-section h6 { font-weight: 600; margin-bottom: 2px; }
    .signature-block {
      margin-top: 50px;
      text-align: center;
    }
    .signature-line {
      border-top: 1px solid #000;
      width: 250px;
      margin: 0 auto 3px auto;
      padding-top: 2px;
      font-weight: bold;
    }
    .small-note { font-size: 11px; }
    .name-column { text-align: left; padding-left: 8px; }
    @media print {
      .no-print { display: none; }
      body { margin: 0; }
    }
  </style>
</head>
<body>

<div class="text-end no-print mb-3">
  <button onclick="window.print()" class="btn btn-primary">🖨️ Print / Save as PDF</button>
</div>

<div class="text-center mb-3 title-section">
  <div class="d-flex justify-content-center align-items-center mb-2">
    <img src="../../../assets/images/logoSanAntonio.png" class="header-logo me-4">
    <div>
      <h4>BARANGAY SAN ANTONIO</h4>
      <h6><?= strtoupper($documentTypeName) ?></h6>
      <span><?= $periodText ?></span>
    </div>
    <img src="../../../assets/images/logoSantoTomas.png" class="header-logo ms-4">
  </div>
</div>

<?php if (strtoupper($documentTypeName) === "FIRST TIME JOB SEEKER"): ?>
<div class="table-responsive">
  <table class="table table-bordered align-middle">
    <thead>
      <tr>
        <th colspan="3">NAME OF BENEFICIARY / AVAILEE</th>
        <th rowspan="2">AGE</th>
        <th colspan="3">DATE OF BIRTH</th>
        <th colspan="2">SEX / GENDER</th>
        <th colspan="4">EDUCATIONAL LEVEL (Check Applicable)</th>
      </tr>
      <tr>
        <th>Last Name</th>
        <th>First Name</th>
        <th>Middle Name</th>
        <th>Month</th>
        <th>Date</th>
        <th>Year</th>
        <th>M</th>
        <th>F</th>
        <th>Elem/HS/SHS</th>
        <th>College</th>
        <th>Course</th>
        <th>Out of School Youth</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($rows) > 0): ?>
        <?php foreach ($rows as $r):
          $birth = new DateTime($r['birthDate'] ?? '0000-00-00');
          $gender = strtolower($r['gender'] ?? '');
          $isMale = strpos($gender, 'male') !== false && strpos($gender, 'female') === false;
          $isFemale = strpos($gender, 'female') !== false;
          $studentLevel = strtolower($r['studentLevel'] ?? '');
          
          // Convert student level to code
          $levelCode = '';
          if ($studentLevel === 'elementary') {
            $levelCode = 'ELEM';
          } elseif ($studentLevel === 'high school') {
            $levelCode = 'HS';
          } elseif ($studentLevel === 'shs' || $studentLevel === 'senior high school') {
            $levelCode = 'SHS';
          }
        ?>
        <tr>
          <td class="name-column"><?= htmlspecialchars(strtoupper($r['lastName'] ?? '')) ?></td>
          <td class="name-column"><?= htmlspecialchars(strtoupper($r['firstName'] ?? '')) ?></td>
          <td class="name-column"><?= htmlspecialchars(strtoupper($r['middleName'] ?? '')) ?></td>
          <td><?= htmlspecialchars($r['age'] ?? '') ?></td>
          <td><?= $birth->format('m') != '00' ? $birth->format('m') : '' ?></td>
          <td><?= $birth->format('d') != '00' ? $birth->format('d') : '' ?></td>
          <td><?= $birth->format('Y') != '0000' ? $birth->format('Y') : '' ?></td>
          <td><?= $isMale ? '✓' : '' ?></td>
          <td><?= $isFemale ? '✓' : '' ?></td>
          <td><?= $levelCode ?></td>
          <td><?= ($studentLevel === 'college') ? '✓' : '' ?></td>
          <td><?= htmlspecialchars($r['collegeCourse'] ?? '') ?></td>
          <td><?= ($r['outOfSchoolYouth'] == 1) ? '✓' : '' ?></td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="13" class="text-center py-3">No records found</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php else: ?>
<!-- Table for ALL DOCUMENT TYPES -->
<div class="table-responsive">
  <table class="table table-bordered align-middle table-all">
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
</div>
<?php endif; ?>

<div class="row mt-5 text-center">
  <div class="col-md-6 signature-block">
    <div class="signature-line">MRS. MARY JOY M. MARTIREZ</div>
    <div class="small-note"><i>Barangay Secretary</i></div>
  </div>
  <div class="col-md-6 signature-block">
    <div class="signature-line">HON. ULYSES M. RELLAMA</div>
    <div class="small-note"><i>Punong Barangay</i></div>
  </div>
</div>

<div class="text-end mt-4 small-note">
  <b>Prepared by:</b><br>
  DATED: <?= date('m-d-Y') ?>
</div>

</body>
</html>
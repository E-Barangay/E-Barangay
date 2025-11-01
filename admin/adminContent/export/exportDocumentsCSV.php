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

$where = [];
if ($from && $to) {
    $where[] = "DATE(d.requestDate) BETWEEN '$from' AND '$to'";
}
if ($type && strtolower($type) !== 'all') {
    $where[] = "dt.documentName = '" . $conn->real_escape_string($type) . "'";
}
$where[] = "dt.categoryID = 1";

$whereSQL = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";

$documentTypeName = ($type && strtolower($type) !== 'all') ? $type : "ALL DOCUMENT TYPES";
$isAllTypes = (strtoupper($documentTypeName) === "ALL DOCUMENT TYPES");
$colspan = $isAllTypes ? '8' : '11';

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

$documentquery = "
    SELECT 
        d.documentID,
        dt.documentName,
        d.documentStatus,
        DATE(d.requestDate) AS requestDate,
        DATE(d.approvalDate) AS approvalDate,
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
        d.purpose,
        YEAR(d.requestDate) AS requestYear,
        MONTH(d.requestDate) AS requestMonth,
        DAY(d.requestDate) AS requestDay
    FROM documents d
    JOIN documenttypes dt ON d.documentTypeID = dt.documentTypeID
    LEFT JOIN userinfo ON d.userID = userinfo.userID
    LEFT JOIN users ON userinfo.userID = users.userID
    LEFT JOIN addresses ON userinfo.userID = addresses.userInfoID
    $whereSQL
    ORDER BY dt.documentName ASC, d.requestDate DESC, userinfo.lastName ASC
";

$result = $conn->query($documentquery);
$data = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Documents_Report_' . date('Y-m-d') . '.xls"');
header('Cache-Control: max-age=0');
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }

        .header-title {
            font-size: 18px;
            font-weight: bold;
        }

        .header-subtitle {
            font-size: 14px;
            font-weight: bold;
        }

        .header-period {
            font-size: 12px;
        }

        .table-header {
            background-color: #31AFAB;
            font-weight: bold;
            font-size: 9px;
        }

        .name-column {
            text-align: left;
        }

        .signature-section {
            text-align: center;
            font-weight: bold;
        }

        .signature-title {
            font-style: italic;
            font-size: 12px;
        }

        .footer-section {
            text-align: right;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <table>
        <tr>
            <td colspan="<?= $isAllTypes ? '2' : '3' ?>" style="text-align: center;">[Manually Insert LOGO HERE]</td>
            <td colspan="<?= $isAllTypes ? '4' : '5' ?>" style="text-align: center;">
                <div class="header-title">BARANGAY SAN ANTONIO</div>
                <div class="header-subtitle"><?= strtoupper(htmlspecialchars($documentTypeName)) ?></div>
                <div class="header-period"><?= $periodText ?></div>
            </td>
            <td colspan="<?= $isAllTypes ? '2' : '3' ?>" style="text-align: center;">[Manually Insert LOGO HERE]</td>
        </tr>
        <tr>
            <td colspan="<?= $colspan ?>">&nbsp;</td>
        </tr>

        <?php if ($isAllTypes): ?>
            <tr class="table-header">
                <th>DOCUMENT TYPE</th>
                <th>REQUEST DATE</th>
                <th>FULL NAME</th>
                <th>PURPOSE</th>
                <th>STATUS</th>
                <th>APPROVAL DATE</th>
                <th>ADDRESS</th>
                <th>CONTACT NUMBER</th>
            </tr>

            <?php if (count($data) > 0): ?>
                <?php foreach ($data as $row): ?>
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

        <?php else: ?>
            <tr class="table-header">
                <th rowspan="2">NAME OF BENEFICIARY/AVAILEE</th>
                <th rowspan="2">AGE</th>
                <th colspan="3">DATE OF BIRTH</th>
                <th colspan="2">SEX/GENDER</th>
                <th rowspan="2">CIVIL STATUS</th>
                <th rowspan="2">OCCUPATION</th>
                <th rowspan="2">STATUS</th>
                <th rowspan="2">REQUEST DATE</th>
            </tr>
            <tr class="table-header">
                <th>Month</th>
                <th>Date</th>
                <th>Year</th>
                <th>M</th>
                <th>F</th>
            </tr>

            <?php if (count($data) > 0): ?>
                <?php foreach ($data as $row): ?>
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
        <?php endif; ?>

        <tr>
            <td colspan="<?= $colspan ?>">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="<?= $colspan ?>">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="<?= $colspan ?>">&nbsp;</td>
        </tr>

        <tr>
            <td colspan="<?= $isAllTypes ? '3' : '4' ?>" class="signature-section">
                <strong>MRS. MARY JOY M. MARTIREZ</strong><br>
                <span class="signature-title">Barangay Secretary</span>
            </td>
            <td colspan="<?= $isAllTypes ? '2' : '3' ?>">&nbsp;</td>
            <td colspan="<?= $isAllTypes ? '3' : '4' ?>" class="signature-section">
                <strong>HON. ULYSES M. RELLAMA</strong><br>
                <span class="signature-title">Punong Barangay</span>
            </td>
        </tr>

        <tr>
            <td colspan="<?= $colspan ?>">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="<?= $colspan ?>" class="footer-section">
                Prepared by:<br>
                DATED: <?= date('m-d-Y') ?>
            </td>
        </tr>
    </table>
</body>

</html>
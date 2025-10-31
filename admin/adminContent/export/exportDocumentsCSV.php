<?php
include_once __DIR__ . "/../../../sharedAssets/connect.php";

$from = isset($_GET['from']) ? trim($_GET['from']) : null;
$to   = isset($_GET['to']) ? trim($_GET['to']) : null;
$type = isset($_GET['type']) ? trim($_GET['type']) : null; // <-- added

function valid_date($date) {
    if (!$date) return false;
    $check = DateTime::createFromFormat('Y-m-d', $date);
    return $check && $check->format('Y-m-d') === $date;
}

if (!valid_date($from)) $from = null;
if (!valid_date($to)) $to = null;

$whereClauses = [];
if ($from && $to) $whereClauses[] = "DATE(d.requestDate) BETWEEN '$from' AND '$to'";
if ($type) $whereClauses[] = "dt.documentName = '" . $conn->real_escape_string($type) . "'";

$whereSQL = "";
if (!empty($whereClauses)) {
    $whereSQL = "WHERE " . implode(" AND ", $whereClauses);
}

$documentsQuery = "
    SELECT 
        d.documentID,
        dt.documentName AS type,
        d.documentStatus AS status,
        DATE(d.requestDate) AS requestDate
    FROM documents d
    INNER JOIN documenttypes dt ON d.documentTypeID = dt.documentTypeID
    $whereSQL
    ORDER BY d.requestDate DESC
";

$result = $conn->query($documentsQuery);

$filenameParts = [];
if ($from && $to) $filenameParts[] = "{$from}_to_{$to}";
if ($type) $filenameParts[] = str_replace(' ', '_', $type);
$filename = 'Barangay_Services_' . (!empty($filenameParts) ? implode('_', $filenameParts) : date('Ymd')) . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Document ID', 'Type', 'Status', 'Request Date']);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['documentID'],
            $row['type'],
            $row['status'],
            $row['requestDate']
        ]);
    }
} else {
    fputcsv($output, ['No records found']);
}

fclose($output);
exit;
?>

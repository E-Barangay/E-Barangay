<?php
include_once __DIR__ . "/../../../sharedAssets/connect.php";

$from = isset($_GET['from']) ? trim($_GET['from']) : null;
$to   = isset($_GET['to']) ? trim($_GET['to']) : null;

function valid_date($date) {
    if (!$date) return false;
    $check = DateTime::createFromFormat('Y-m-d', $date);
    return $check && $check->format('Y-m-d') === $date;
}

if (!valid_date($from)) $from = null;
if (!valid_date($to)) $to = null;

if ($from && $to) {
    $complaintsQuery = "
        SELECT 
            complaintID, 
            complaintTitle AS type, 
            complaintStatus AS status, 
            DATE(requestDate) AS requestDate
        FROM complaints
        WHERE DATE(requestDate) BETWEEN '$from' AND '$to'
        ORDER BY requestDate DESC
    ";
} else {
    $complaintsQuery = "
        SELECT 
            complaintID, 
            complaintTitle AS type, 
            complaintStatus AS status, 
            DATE(requestDate) AS requestDate
        FROM complaints
        ORDER BY requestDate DESC
    ";
}

$complaintsQueryResults = $conn->query($complaintsQuery);

$complaintsData = [];
if ($complaintsQueryResults) {
    while ($row = $complaintsQueryResults->fetch_assoc()) {
        $complaintsData[] = $row;
    }
}

$filename = 'Barangay_Incidents_' . ($from && $to ? $from . '_to_' . $to : date('Ymd')) . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Complaint ID', 'Type', 'Status', 'Request Date']);

foreach ($complaintsData as $row) {
    fputcsv($output, [
        $row['complaintID'],
        $row['type'],
        $row['status'],
        $row['requestDate']
    ]);
}

fclose($output);
exit;

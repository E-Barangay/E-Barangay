<?php
header('Content-Type: application/json; charset=utf-8');
include_once __DIR__ . "/../../../sharedAssets/connect.php";

$from = isset($_GET['from']) ? ($_GET['from']) : null;
$to   = isset($_GET['to']) ? ($_GET['to']) : null;

// Validate date format
function valid_date($date) {
    if (!$date) return false;
    $check = DateTime::createFromFormat('Y-m-d', $date);
    return $check && $check->format('Y-m-d') === $date;
}

if (!valid_date($from)) $from = null;
if (!valid_date($to)) $to = null;

// Query setup
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

// Collect results
$complaintsData = [];
if ($complaintsQueryResults) {
    while ($row = $complaintsQueryResults->fetch_assoc()) {
        $complaintsData[] = $row;
    }
}

echo json_encode($complaintsData);
exit;

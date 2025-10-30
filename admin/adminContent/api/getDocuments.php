<?php
header('Content-Type: application/json; charset=utf-8');
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
    $documentsQuery = "
        SELECT 
            d.documentID,
            dt.documentName AS type,
            d.documentStatus AS status,
            DATE(d.requestDate) AS requestDate
        FROM documents d
        JOIN documenttypes dt ON d.documentTypeID = dt.documentTypeID
        WHERE DATE(d.requestDate) BETWEEN '$from' AND '$to'
        ORDER BY d.requestDate DESC
    ";
} else {
    $documentsQuery = "
        SELECT 
            d.documentID,
            dt.documentName AS type,
            d.documentStatus AS status,
            DATE(d.requestDate) AS requestDate
        FROM documents d
        JOIN documenttypes dt ON d.documentTypeID = dt.documentTypeID
        ORDER BY d.requestDate DESC
    ";
}

$documentsQueryResults = $conn->query($documentsQuery);

$documentsData = [];
if ($documentsQueryResults) {
    while ($row = $documentsQueryResults->fetch_assoc()) {
        $documentsData[] = $row;
    }
}

echo json_encode($documentsData);
exit;

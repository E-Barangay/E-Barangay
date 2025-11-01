<?php
header('Content-Type: application/json; charset=utf-8');
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

$query = "
    SELECT 
        d.documentID,
        dt.documentName AS type,
        d.documentStatus AS status,
        DATE(d.requestDate) AS requestDate
    FROM documents d
    JOIN documenttypes dt ON d.documentTypeID = dt.documentTypeID
    WHERE 1=1
";

if ($from && $to) {
    $query .= " AND DATE(d.requestDate) BETWEEN '$from' AND '$to'";
}

if ($type && strtolower($type) !== 'all') {
    $safeType = $conn->real_escape_string($type);
    $query .= " AND dt.documentName = '$safeType'";
}

$query .= " ORDER BY d.requestDate DESC";

$result = $conn->query($query);
$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data);
exit;
?>
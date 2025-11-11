<?php
include_once __DIR__ . '/../../sharedAssets/connect.php';

$totalPopulation = $totalUsers = $totalDocuments = $totalComplaints = 0;
$maleCount = $femaleCount = $kids = $teens = $adults = $seniors = 0;
$bonafide = $migrant = $transient = 0;

if ($conn) {
    $q = "SELECT COUNT(*) AS total FROM users WHERE role = 'user'";
    $totalUsers = (int) (mysqli_fetch_assoc(mysqli_query($conn, $q))['total'] ?? 0);

    $q = "SELECT COUNT(*) AS total FROM userinfo";
    $totalPopulation = (int) (mysqli_fetch_assoc(mysqli_query($conn, $q))['total'] ?? 0);

    $totalDocuments = (int) (mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM documents"))['total'] ?? 0);
    $totalComplaints = (int) (mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM complaints"))['total'] ?? 0);

    $g = mysqli_fetch_assoc(mysqli_query($conn, "SELECT 
        SUM(CASE WHEN gender='Male' THEN 1 ELSE 0 END) AS male,
        SUM(CASE WHEN gender='Female' THEN 1 ELSE 0 END) AS female
        FROM userinfo"));
    $maleCount = (int) ($g['male'] ?? 0);
    $femaleCount = (int) ($g['female'] ?? 0);

    $a = mysqli_fetch_assoc(mysqli_query($conn, "SELECT 
        SUM(CASE WHEN TIMESTAMPDIFF(YEAR,birthDate,CURDATE()) BETWEEN 0 AND 12 THEN 1 ELSE 0 END) AS kids,
        SUM(CASE WHEN TIMESTAMPDIFF(YEAR,birthDate,CURDATE()) BETWEEN 13 AND 17 THEN 1 ELSE 0 END) AS teens,
        SUM(CASE WHEN TIMESTAMPDIFF(YEAR,birthDate,CURDATE()) BETWEEN 18 AND 59 THEN 1 ELSE 0 END) AS adults,
        SUM(CASE WHEN TIMESTAMPDIFF(YEAR,birthDate,CURDATE()) >= 60 THEN 1 ELSE 0 END) AS seniors
        FROM userinfo"));
    $kids = (int) ($a['kids'] ?? 0);
    $teens = (int) ($a['teens'] ?? 0);
    $adults = (int) ($a['adults'] ?? 0);
    $seniors = (int) ($a['seniors'] ?? 0);

    $r = mysqli_fetch_assoc(mysqli_query($conn, "SELECT
        SUM(CASE WHEN residencyType='Bonafide' THEN 1 ELSE 0 END) AS bonafide,
        SUM(CASE WHEN residencyType='Migrant' THEN 1 ELSE 0 END) AS migrant,
        SUM(CASE WHEN residencyType='Transient' THEN 1 ELSE 0 END) AS transient
        FROM userinfo"));
    $bonafide = (int) ($r['bonafide'] ?? 0);
    $migrant = (int) ($r['migrant'] ?? 0);
    $transient = (int) ($r['transient'] ?? 0);
}

$totalResidency = $bonafide + $migrant + $transient;
$bonafidePercent = $totalResidency > 0 ? round(($bonafide / $totalResidency) * 100) : 0;
$migrantPercent = $totalResidency > 0 ? round(($migrant / $totalResidency) * 100) : 0;
$transientPercent = $totalResidency > 0 ? round(($transient / $totalResidency) * 100) : 0;

$totalGender = $maleCount + $femaleCount;
$malePercent = $totalGender > 0 ? round(($maleCount / $totalGender) * 100, 1) : 0;
$femalePercent = $totalGender > 0 ? round(($femaleCount / $totalGender) * 100, 1) : 0;

$purokStats = [];
for ($i = 1; $i <= 7; $i++) {
    $purokStats["Purok $i"] = [
        'purok' => "Purok $i",
        'total_residents' => 0,
        'total_voters' => 0,
        'voter_percent' => 0
    ];
}

if ($conn) {
    $res = mysqli_query($conn, "SELECT a.purok,
        COUNT(ui.userInfoID) AS total_residents,
        SUM(CASE WHEN ui.isVoter='Yes' THEN 1 ELSE 0 END) AS total_voters
        FROM addresses a
        JOIN userinfo ui ON a.userInfoID = ui.userInfoID
        GROUP BY a.purok");

    while ($row = mysqli_fetch_assoc($res)) {
        $p = preg_match('/^Purok\s*\d+/i', $row['purok']) ? $row['purok'] : 'Purok ' . preg_replace('/\D/', '', $row['purok']);
        if (!isset($purokStats[$p])) continue;

        $totalR = (int) $row['total_residents'];
        $totalV = (int) $row['total_voters'];
        $pct = $totalR > 0 ? round(($totalV / $totalR) * 100, 1) : 0;

        $purokStats[$p] = [
            'purok' => $p,
            'total_residents' => $totalR,
            'total_voters' => $totalV,
            'voter_percent' => $pct
        ];
    }
}

$purokDataForJS = array_values($purokStats);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Barangay San Antonio Dashboard</title>
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
:root {
  --primary-color: #19AFA5;
  --primary-dark: #158B82;
}
body { min-height: 100vh; background: #f5f7f8; font-family: Arial, Helvetica, sans-serif; }
.stat-card { background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); color: #fff; transition: .3s; }
.stat-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,.15)!important; }
.map-section, .population-section { background: #3B7979; }
#map { height: 300px; border-radius: 10px; }
.residency-card { transition: .3s; border: 3px solid transparent; }
.residency-card:hover { transform: translateY(-5px); }
.residency-card.bonified { border-color: var(--primary-color); }
.residency-card.migrant { border-color: #FF9800; }
.residency-card.transient { border-color: #2196F3; }
.residency-card.bonified:hover { box-shadow: 0 0 15px rgba(25,175,165,0.4); }
.residency-card.migrant:hover { box-shadow: 0 0 15px rgba(255,152,0,0.4); }
.residency-card.transient:hover { box-shadow: 0 0 15px rgba(33,150,243,0.4); }
.percentage.bonified { color: var(--primary-color); }
.percentage.migrant { color: #FF9800; }
.percentage.transient { color: #2196F3; }
.chart-container { width: 120px; height: 120px; position: relative; }
@media(min-width:768px) {
  .chart-container { width: 150px; height: 150px; }
}
</style>
</head>
<body>
<div class="container-fluid p-3 p-md-4">
<div class="row justify-content-center">
<div class="col-12 col-xl-10">
<div class="bg-white bg-opacity-95 rounded-4 shadow-lg p-3 p-md-4">

<div class="row g-3 g-md-4 mb-4">
  <div class="col-6 col-lg-3"><div class="stat-card text-white p-3 p-md-4 rounded-3 shadow-sm text-center"><h6 class="mb-2 opacity-75 small">Total Population</h6><div class="display-6 fw-bold mb-0"><?= number_format($totalPopulation) ?></div></div></div>
  <div class="col-6 col-lg-3"><div class="stat-card text-white p-3 p-md-4 rounded-3 shadow-sm text-center"><h6 class="mb-2 opacity-75 small">Total Users</h6><div class="display-6 fw-bold mb-0"><?= number_format($totalUsers) ?></div></div></div>
  <div class="col-6 col-lg-3"><div class="stat-card text-white p-3 p-md-4 rounded-3 shadow-sm text-center"><h6 class="mb-2 opacity-75 small">Total Barangay Document Requests</h6><div class="display-6 fw-bold mb-0"><?= number_format($totalDocuments) ?></div></div></div>
  <div class="col-6 col-lg-3"><div class="stat-card text-white p-3 p-md-4 rounded-3 shadow-sm text-center"><h6 class="mb-2 opacity-75 small">Total Complaints</h6><div class="display-6 fw-bold mb-0"><?= number_format($totalComplaints) ?></div></div></div>
</div>

<div class="row g-3 g-md-4 mb-4">
  <div class="col-12 col-lg-6">
    <div class="map-section text-white p-3 p-md-4 rounded-3 shadow-sm h-100">
      <h5 class="mb-2">Barangay San Antonio Registered Voters</h5>
      <p class="small opacity-75 mb-3">Click a Purok to view residents, voters and voter %</p>
      <div id="map"></div>
    </div>
  </div>
  <div class="col-12 col-lg-6">
    <div class="population-section text-white p-3 p-md-4 rounded-3 shadow-sm h-100">
      <h5 class="mb-2">Barangay Population Breakdown</h5>
      <p class="small opacity-75 mb-3">As of <?= date('F Y') ?></p>
      <div class="d-flex flex-column gap-2">
        <div class="d-flex justify-content-between align-items-center p-2 bg-white bg-opacity-10 rounded-2"><span class="small">Male</span><span class="small fw-bold"><?= number_format($maleCount) ?> (<?= $malePercent ?>%)</span></div>
        <div class="d-flex justify-content-between align-items-center p-2 bg-white bg-opacity-10 rounded-2"><span class="small">Female</span><span class="small fw-bold"><?= number_format($femaleCount) ?> (<?= $femalePercent ?>%)</span></div>
        <div class="d-flex justify-content-between align-items-center p-2 bg-white bg-opacity-10 rounded-2"><span class="small">Kids (0–12)</span><span class="small fw-bold"><?= number_format($kids) ?></span></div>
        <div class="d-flex justify-content-between align-items-center p-2 bg-white bg-opacity-10 rounded-2"><span class="small">Teenagers (13–17)</span><span class="small fw-bold"><?= number_format($teens) ?></span></div>
        <div class="d-flex justify-content-between align-items-center p-2 bg-white bg-opacity-10 rounded-2"><span class="small">Adults (18–59)</span><span class="small fw-bold"><?= number_format($adults) ?></span></div>
        <div class="d-flex justify-content-between align-items-center p-2 bg-white bg-opacity-10 rounded-2"><span class="small">Senior Citizens (60+)</span><span class="small fw-bold"><?= number_format($seniors) ?></span></div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 g-md-4">
  <div class="col-12 col-md-4">
    <div class="residency-card bonified bg-white p-3 p-md-4 rounded-3 shadow-sm text-center">
      <div class="chart-container mx-auto mb-3"><canvas id="bonafideChart"></canvas></div>
      <h5 class="mb-2 text-dark">Bonafide</h5>
      <div class="percentage bonified display-5 fw-bold mb-1"><?= $bonafidePercent ?>%</div>
      <div class="text-muted small"><?= number_format($bonafide) ?> residents</div>
    </div>
  </div>
  <div class="col-12 col-md-4">
    <div class="residency-card migrant bg-white p-3 p-md-4 rounded-3 shadow-sm text-center">
      <div class="chart-container mx-auto mb-3"><canvas id="migrantChart"></canvas></div>
      <h5 class="mb-2 text-dark">Migrant</h5>
      <div class="percentage migrant display-5 fw-bold mb-1"><?= $migrantPercent ?>%</div>
      <div class="text-muted small"><?= number_format($migrant) ?> residents</div>
    </div>
  </div>
  <div class="col-12 col-md-4">
    <div class="residency-card transient bg-white p-3 p-md-4 rounded-3 shadow-sm text-center">
      <div class="chart-container mx-auto mb-3"><canvas id="transientChart"></canvas></div>
      <h5 class="mb-2 text-dark">Transient</h5>
      <div class="percentage transient display-5 fw-bold mb-1"><?= $transientPercent ?>%</div>
      <div class="text-muted small"><?= number_format($transient) ?> residents</div>
    </div>
  </div>
</div>

</div></div></div></div>

<script>
const purokData = <?= json_encode($purokDataForJS, JSON_HEX_TAG | JSON_HEX_AMP) ?>;
const purokPolygons = {
  "Purok 1": [[14.1189,121.1552],[14.1187,121.1566],[14.1179,121.1568],[14.1181,121.1553]],
  "Purok 2": [[14.1196,121.1569],[14.1193,121.1582],[14.1185,121.1584],[14.1188,121.1569]],
  "Purok 3": [[14.1179,121.1578],[14.1176,121.1592],[14.1168,121.1590],[14.1169,121.1576]],
  "Purok 4": [[14.1170,121.1558],[14.1168,121.1572],[14.1160,121.1569],[14.1162,121.1555]],
  "Purok 5": [[14.1180,121.1542],[14.1178,121.1556],[14.1170,121.1553],[14.1172,121.1539]],
  "Purok 6": [[14.1190,121.1536],[14.1188,121.1550],[14.1180,121.1547],[14.1182,121.1533]],
  "Purok 7": [[14.1198,121.1548],[14.1195,121.1562],[14.1187,121.1559],[14.1190,121.1545]]
};
const barangayBoundary = [
  [14.1205,121.1525],[14.1155,121.1525],[14.1155,121.1605],[14.1205,121.1605]
];
const map = L.map('map',{zoomControl:false}).setView([14.1185,121.1560],16);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'© OpenStreetMap'}).addTo(map);
L.polygon(barangayBoundary,{color:'#2c3e50',weight:3,fill:false,dashArray:'6 6'}).addTo(map).bindPopup('<strong>Boundary — Barangay San Antonio</strong>');
function statsFor(p){return purokData.find(x=>x.purok.toLowerCase()==p.toLowerCase())||{total_residents:0,total_voters:0,voter_percent:0};}
function colorFor(r,v){if(r==0)return'#bdbdbd';if(v>r/2)return'#b2f5b0';else return'#f5b0b0';}
Object.keys(purokPolygons).forEach(p=>{
  const d=statsFor(p);
  const poly=L.polygon(purokPolygons[p],{
    color:colorFor(d.total_residents,d.total_voters),
    fillColor:colorFor(d.total_residents,d.total_voters),
    fillOpacity:.6
  }).addTo(map);
  poly.bindPopup(`<strong>${p}</strong><br>Residents: ${d.total_residents}<br>Voters: ${d.total_voters}<br>Voter %: ${d.voter_percent}%`);
});
map.scrollWheelZoom.disable();
function pie(id,val,color){
  new Chart(document.getElementById(id),{
    type:'doughnut',
    data:{datasets:[{data:[val,100-val],backgroundColor:[color,'#E0E0E0'],borderWidth:0}]},
    options:{cutout:'70%',plugins:{legend:{display:false}},responsive:true}
  });
}
document.addEventListener('DOMContentLoaded',()=>{
  pie('bonafideChart',<?= $bonafidePercent ?>,'#19AFA5');
  pie('migrantChart',<?= $migrantPercent ?>,'#FF9800');
  pie('transientChart',<?= $transientPercent ?>,'#2196F3');
});
</script>
</body>
</html>

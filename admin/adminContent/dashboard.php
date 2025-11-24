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
        $p = preg_match('/^Purok\s*\d+/i', $row['purok'] ?? '') ? ($row['purok'] ?? '') : 'Purok ' . preg_replace('/\D/', '', $row['purok'] ?? '');
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
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
:root {
  --primary-color: #19AFA5;
  --primary-dark: #158B82;
}
body { min-height: 100vh; background: #f5f7f8; font-family: 'Poppins', sans-serif; }
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
  "Purok 1": [[14.105763,121.149019],[14.105942,121.149047],[14.106593,121.149494],[14.106743,121.149622],[14.108062,121.150113],[14.109322,121.149727],[14.112246,121.147324],[14.112503,121.147139],[14.112716,121.146991],[14.11334,121.146133],[14.114165,121.145411],[14.112469,121.143888],[14.112266,121.143748],[14.112293,121.143644],[14.106395,121.148126],[14.106107,121.148287]],
  "Purok 2": [[14.112716,121.146991],[14.112503,121.147139],[14.112246,121.147324],[14.109322,121.149727],[14.108062,121.150113],[14.107658,121.15124],[14.106539,121.152012],[14.106851,121.153364],[14.107726,121.15294],[14.108308,121.154109],[14.108779,121.153863],[14.109805,121.153285],[14.110164,121.152944],[14.114193,121.150072],[14.114416,121.149704],[14.114842,121.149398],[14.114983,121.149347],[14.115123,121.149259],[14.115604,121.14996],[14.116808,121.149177],[14.11668,121.148944],[14.117052,121.148745],[14.116943,121.148367],[14.116179,121.147174],[14.114165,121.145411],[14.11334,121.146133]],
  "Purok 3": [[14.114416,121.149704],[14.114842,121.149398],[14.114983,121.149347],[14.115123,121.149259],[14.115604,121.14996],[14.116808,121.149177],[14.11668,121.148944],[14.117052,121.148745],[14.116943,121.148367],[14.117845,121.147807],[14.118177,121.147614],[14.118558,121.148394],[14.118148,121.148735],[14.118978,121.150217],[14.11873,121.150344],[14.117005,121.151462],[14.116655,121.151638],[14.115905,121.152028],[14.114742,121.152718],[14.112699,121.154145],[14.110895,121.155151],[14.110903,121.155169],[14.109596,121.15585],[14.109192,121.155098],[14.108903,121.155239],[14.108308,121.154109],[14.108779,121.153863],[14.109805,121.153285],[14.110164,121.152944],[14.114193,121.150072]],
  "Purok 4": [[14.121659,121.151283],[14.120946,121.151894],[14.121328,121.152659],[14.121231,121.152895],[14.119293,121.153702],[14.119526,121.154295],[14.119574,121.154408],[14.119698,121.154605],[14.119657,121.154649],[14.119737,121.154778],[14.119789,121.154772],[14.120593,121.154444],[14.120612,121.154636],[14.120704,121.154834],[14.120738,121.154865],[14.120818,121.155034],[14.12123,121.154818],[14.121412,121.155215],[14.121529,121.155318],[14.121719,121.155254],[14.123173,121.153577],[14.12372,121.154375],[14.12384,121.154509],[14.123866,121.154606],[14.12374,121.154699],[14.123936,121.155631],[14.124038,121.155889],[14.124152,121.156101],[14.124257,121.156286],[14.123222,121.156887],[14.123616,121.157613],[14.124577,121.156978],[14.125174,121.158317],[14.125356,121.158269],[14.125671,121.158945],[14.126834,121.158369],[14.126745,121.158877],[14.126587,121.160386],[14.130409,121.15935],[14.13038,121.159269],[14.130744,121.159108],[14.131258,121.15882],[14.130552,121.157307],[14.131402,121.156793],[14.131682,121.156596],[14.131738,121.156528],[14.131891,121.156414],[14.131539,121.155725],[14.130893,121.15525],[14.129891,121.154748],[14.129274,121.154774],[14.128773,121.155083],[14.128665,121.1552220],[14.128197,121.154095],[14.128018,121.153926],[14.127099,121.151969],[14.124619,121.150339],[14.124451,121.150125],[14.124403,121.150078],[14.124296,121.149926],[14.123867,121.149381],[14.123725,121.1492],[14.123425,121.148932],[14.123409,121.148877],[14.123368,121.148827],[14.123032,121.148108],[14.122866,121.146057],[14.122842,121.145858],[14.12275,121.14537],[14.122395,121.144843],[14.121267,121.145548],[14.121629,121.146141],[14.120317,121.146707],[14.119962,121.146917],[14.119878,121.146964],[14.119839,121.146893],[14.119777,121.146742],[14.119613,121.146036],[14.119612,121.146031],[14.119545,121.145838],[14.11935,121.145547],[14.119128,121.145236],[14.118017,121.142963],[14.117975,121.142818],[14.116818,121.143294],[14.117373,121.144465],[14.117732,121.1443],[14.118256,121.145528],[14.116849,121.146122],[14.117135,121.146753],[14.116179,121.147174],[14.116943,121.148367],[14.117845,121.147807],[14.118177,121.147614],[14.118558,121.148394],[14.118148,121.148735],[14.118978,121.150217],[14.11873,121.150344],[14.117005,121.151462],[14.116655,121.151638],[14.115905,121.152028],[14.114742,121.152718],[14.112699,121.154145],[14.110895,121.155151],[14.111949,121.156956],[14.116912,121.154341],[14.116995,121.154296],[14.117252,121.15413],[14.11732,121.15411],[14.11763,121.153941],[14.121659,121.151283]],
  "Purok 5": [[14.117252,121.15413],[14.116995,121.154296],[14.116955,121.15432],[14.116912,121.154341],[14.116778,121.154421],[14.116758,121.154433],[14.116713,121.154453],[14.111949,121.156956],[14.112214,121.157406],[14.112034,121.157524],[14.112043,121.15765],[14.111856,121.157786],[14.112049,121.158173],[14.112385,121.158665],[14.118137,121.155445],[14.118677,121.155207],[14.119219,121.154908],[14.119657,121.154649],[14.119698,121.154605],[14.119574,121.154408],[14.119526,121.154295],[14.119293,121.153702],[14.121231,121.152895],[14.121328,121.152659],[14.120946,121.151894],[14.121659,121.151283],[14.11763,121.153941],[14.117389,121.154071],[14.11732,121.15411]],
  "Purok 6": [[14.118137,121.155445],[14.112385,121.158665],[14.112937,121.160624],[14.113807,121.160274],[14.117421,121.157927],[14.117462,121.157881],[14.117606,121.157754],[14.117703,121.157844],[14.117841,121.157777],[14.118148,121.157408],[14.118281,121.157206],[14.118502,121.15713],[14.118651,121.157148],[14.118868,121.157117],[14.118973,121.157063],[14.119184,121.156807],[14.119376,121.156808],[14.119596,121.15661],[14.12021,121.156395],[14.120564,121.156354],[14.120915,121.15619],[14.121209,121.156199],[14.121558,121.156811],[14.121821,121.157225],[14.121983,121.157259],[14.122067,121.157377],[14.122523,121.157339],[14.122892,121.157098],[14.123222,121.156887],[14.124257,121.156286],[14.124152,121.156101],[14.124038,121.155889],[14.123936,121.155631],[14.12374,121.154699],[14.123866,121.154606],[14.12384,121.154509],[14.12372,121.154375],[14.123173,121.153577],[14.121719,121.155254],[14.121529,121.155318],[14.121412,121.155215],[14.12123,121.154818],[14.120818,121.155034],[14.120738,121.154865],[14.120704,121.154834],[14.120612,121.154636],[14.120593,121.154444],[14.119789,121.154772],[14.119737,121.154778],[14.119657,121.154649],[14.119219,121.154908],[14.118677,121.155207]],
  "Purok 7": [[14.120972,121.172164],[14.120351,121.170772],[14.118447,121.168433],[14.116967,121.165246],[14.116852,121.164265],[14.116404,121.162798],[14.116276,121.1627],[14.116197,121.16261],[14.11598,121.162122],[14.116156,121.161483],[14.116104,121.161101],[14.115982,121.161164],[14.115247,121.161384],[14.11472,121.160899],[14.114109,121.160987],[14.113807,121.160274],[14.11729,121.158005],[14.117421,121.157927],[14.117462,121.157881],[14.117606,121.157754],[14.117703,121.157844],[14.117841,121.157777],[14.118148,121.157408],[14.118281,121.157206],[14.118502,121.15713],[14.118651,121.157148],[14.118868,121.157117],[14.118973,121.157063],[14.119184,121.156807],[14.119376,121.156808],[14.119596,121.15661],[14.12021,121.156395],[14.120564,121.156354],[14.120915,121.15619],[14.121209,121.156199],[14.121558,121.156811],[14.121821,121.157225],[14.121983,121.157259],[14.122067,121.157377],[14.122523,121.157339],[14.122892,121.157098],[14.123222,121.156887],[14.123616,121.157613],[14.124577,121.156978],[14.125174,121.158317],[14.125356,121.158269],[14.125671,121.158945],[14.126834,121.158369],[14.126745,121.158877],[14.126587,121.160386],[14.130409,121.15935],[14.13038,121.159269],[14.130744,121.159108],[14.131258,121.15882],[14.130552,121.157307],[14.131402,121.156793],[14.131682,121.156596],[14.131738,121.156528],[14.132855,121.155703],[14.133189,121.155519],[14.134274,121.157932],[14.136733,121.159493],[14.136898,121.160094],[14.136576,121.16088],[14.133124,121.171789],[14.122592,121.173886]]
};
const barangayBoundary = [[14.13288,121.192086],[14.133124,121.171789],[14.122592,121.173886],[14.105763,121.149019],[14.106107,121.148287],[14.13288,121.192086]];
const map = L.map('map',{zoomControl:false}).setView([14.1185,121.1560],13.5);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'© OpenStreetMap'}).addTo(map);
L.polygon(barangayBoundary,{color:'#19AFA5',weight:3,fill:false,dashArray:'4 4'}).addTo(map).bindPopup('<strong>Boundary — Barangay San Antonio</strong>');
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

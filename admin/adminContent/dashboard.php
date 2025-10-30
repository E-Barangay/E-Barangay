<?php
include_once __DIR__ . '/../../sharedAssets/connect.php';

$totalPopulation = 0;
$totalUsers = 0;
$totalDocuments = 0;
$totalComplaints = 0;

if ($conn) {
    $totalPopulation = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM userinfo"))['total'] ?? 0;
    $totalUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"))['total'] ?? 0;
    $totalDocuments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM documents"))['total'] ?? 0;
    $totalComplaints = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM complaints"))['total'] ?? 0;

    $maleCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM userinfo WHERE gender='Male'"))['total'] ?? 0;
    $femaleCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM userinfo WHERE gender='Female'"))['total'] ?? 0;

    $kids = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM userinfo WHERE TIMESTAMPDIFF(YEAR, birthDate, CURDATE()) BETWEEN 0 AND 12"))['total'] ?? 0;
    $teens = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM userinfo WHERE TIMESTAMPDIFF(YEAR, birthDate, CURDATE()) BETWEEN 13 AND 17"))['total'] ?? 0;
    $adults = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM userinfo WHERE TIMESTAMPDIFF(YEAR, birthDate, CURDATE()) BETWEEN 18 AND 59"))['total'] ?? 0;
    $seniors = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM userinfo WHERE TIMESTAMPDIFF(YEAR, birthDate, CURDATE()) >= 60"))['total'] ?? 0;

    $bonafide = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM userinfo WHERE residencyType='Bonafide'"))['total'] ?? 0;
    $migrant = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM userinfo WHERE residencyType='Migrant'"))['total'] ?? 0;
    $transient = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM userinfo WHERE residencyType='Transient'"))['total'] ?? 0;

    $totalResidency = $bonafide + $migrant + $transient;
    $bonafidePercent = $totalResidency > 0 ? round(($bonafide / $totalResidency) * 100) : 0;
    $migrantPercent = $totalResidency > 0 ? round(($migrant / $totalResidency) * 100) : 0;
    $transientPercent = $totalResidency > 0 ? round(($transient / $totalResidency) * 100) : 0;

    $totalGender = $maleCount + $femaleCount;
    $malePercent = $totalGender > 0 ? round(($maleCount / $totalGender) * 100, 1) : 0;
    $femalePercent = $totalGender > 0 ? round(($femaleCount / $totalGender) * 100, 1) : 0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay San Antonio Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chart.js/4.4.0/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --primary-color: #19AFA5;
            --primary-dark: #158B82;
            --primary-light: #4BC0B7;
        }

        body {
            min-height: 100vh;
        }

        .stat-card {
            background: linear-gradient(135deg, #19AFA5, #158B82);
            transition: .3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15) !important;
        }

        .map-section,
        .population-section {
            background: #3B7979;
        }

        #map {
            height: 300px;
            border-radius: 10px;
        }

        .residency-card {
            transition: .3s;
            border: 3px solid transparent;
        }

        .residency-card:hover {
            transform: translateY(-5px);
        }

        .residency-card.bonified {
            border-color: var(--primary-color);
        }

        .residency-card.migrant {
            border-color: #FF9800;
        }

        .residency-card.transient {
            border-color: #2196F3;
        }

        .percentage.bonified {
            color: var(--primary-color);
        }

        .percentage.migrant {
            color: #FF9800;
        }

        .percentage.transient {
            color: #2196F3;
        }

        .chart-container {
            width: 120px;
            height: 120px;
            position: relative;
        }

        @media(min-width:768px) {
            .chart-container {
                width: 150px;
                height: 150px;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid p-3 p-md-4">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="bg-white bg-opacity-95 rounded-4 shadow-lg p-3 p-md-4">

                    <div class="row g-3 g-md-4 mb-4">
                        <div class="col-6 col-lg-3">
                            <div class="stat-card text-white p-3 p-md-4 rounded-3 shadow-sm text-center">
                                <h6 class="mb-2 opacity-75 small">Total Population</h6>
                                <div class="display-6 fw-bold mb-0"><?= number_format($totalPopulation) ?></div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="stat-card text-white p-3 p-md-4 rounded-3 shadow-sm text-center">
                                <h6 class="mb-2 opacity-75 small">Total Users</h6>
                                <div class="display-6 fw-bold mb-0"><?= number_format($totalUsers) ?></div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="stat-card text-white p-3 p-md-4 rounded-3 shadow-sm text-center">
                                <h6 class="mb-2 opacity-75 small">Total Barangay Document Requests</h6>
                                <div class="display-6 fw-bold mb-0"><?= number_format($totalDocuments) ?></div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="stat-card text-white p-3 p-md-4 rounded-3 shadow-sm text-center">
                                <h6 class="mb-2 opacity-75 small">Total Complaints</h6>
                                <div class="display-6 fw-bold mb-0"><?= number_format($totalComplaints) ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 g-md-4 mb-4">
                        <div class="col-12 col-lg-6">
                            <div class="map-section text-white p-3 p-md-4 rounded-3 shadow-sm h-100">
                                <h5 class="mb-2">Barangay San Antonio Registered Voters</h5>
                                <p class="small opacity-75 mb-3">Showing voter registration coverage across different
                                    areas</p>
                                <div id="map"></div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="population-section text-white p-3 p-md-4 rounded-3 shadow-sm h-100">
                                <h5 class="mb-2">Barangay Population Breakdown</h5>
                                <p class="small opacity-75 mb-3">As of <?= date('F Y') ?></p>
                                <div class="d-flex flex-column gap-2">
                                    <div
                                        class="d-flex justify-content-between align-items-center p-2 bg-white bg-opacity-10 rounded-2">
                                        <span class="small">Male</span>
                                        <span class="small fw-bold"><?= number_format($maleCount) ?>
                                            (<?= $malePercent ?>%)</span>
                                    </div>
                                    <div
                                        class="d-flex justify-content-between align-items-center p-2 bg-white bg-opacity-10 rounded-2">
                                        <span class="small">Female</span>
                                        <span class="small fw-bold"><?= number_format($femaleCount) ?>
                                            (<?= $femalePercent ?>%)</span>
                                    </div>
                                    <div
                                        class="d-flex justify-content-between align-items-center p-2 bg-white bg-opacity-10 rounded-2">
                                        <span class="small">Kids (0–12)</span>
                                        <span class="small fw-bold"><?= number_format($kids) ?></span>
                                    </div>
                                    <div
                                        class="d-flex justify-content-between align-items-center p-2 bg-white bg-opacity-10 rounded-2">
                                        <span class="small">Teenagers (13–17)</span>
                                        <span class="small fw-bold"><?= number_format($teens) ?></span>
                                    </div>
                                    <div
                                        class="d-flex justify-content-between align-items-center p-2 bg-white bg-opacity-10 rounded-2">
                                        <span class="small">Adults (18–59)</span>
                                        <span class="small fw-bold"><?= number_format($adults) ?></span>
                                    </div>
                                    <div
                                        class="d-flex justify-content-between align-items-center p-2 bg-white bg-opacity-10 rounded-2">
                                        <span class="small">Senior Citizens (60+)</span>
                                        <span class="small fw-bold"><?= number_format($seniors) ?></span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 g-md-4">
                        <div class="col-12 col-md-4">
                            <div class="residency-card bonified bg-white p-3 p-md-4 rounded-3 shadow-sm text-center">
                                <div class="chart-container mx-auto mb-3"><canvas id="bonifiedChart"></canvas></div>
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

                </div>
            </div>
        </div>
    </div>

    <script>
        const map = L.map('map', { zoomControl: false, minZoom: 15, maxZoom: 17 }).setView([14.1185, 121.1561], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);

        const puroks = [
            { name: "Purok 1", coords: [[14.1188, 121.1555], [14.1186, 121.1570], [14.1175, 121.1571], [14.1177, 121.1554]] },
            { name: "Purok 2", coords: [[14.1195, 121.1580], [14.1192, 121.1595], [14.1181, 121.1596], [14.1184, 121.1581]] },
            { name: "Purok 3", coords: [[14.1175, 121.1580], [14.1172, 121.1595], [14.1161, 121.1594], [14.1164, 121.1580]] }
        ];
        puroks.forEach(p => {
            const poly = L.polygon(p.coords, { color: '#19AFA5', fillColor: '#19AFA5', fillOpacity: .5 }).addTo(map);
            poly.bindPopup(`<strong>${p.name}</strong>`);
        });
        map.scrollWheelZoom.disable();

        function pie(id, val, color) {
            new Chart(document.getElementById(id), {
                type: 'doughnut',
                data: { datasets: [{ data: [val, 100 - val], backgroundColor: [color, '#E0E0E0'], borderWidth: 0 }] },
                options: { cutout: '70%', plugins: { legend: { display: false } }, responsive: true }
            });
        }
        document.addEventListener('DOMContentLoaded', () => {
            pie('bonifiedChart', <?= $bonafidePercent ?>, '#19AFA5');
            pie('migrantChart', <?= $migrantPercent ?>, '#FF9800');
            pie('transientChart', <?= $transientPercent ?>, '#2196F3');
        });
    </script>
</body>

</html>
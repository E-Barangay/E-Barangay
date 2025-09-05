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
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15) !important;
        }
        
        .map-section, .population-section {
            background: #3B7979;
        }
        
        #map {
            height: 300px;
            border-radius: 10px;
        }
        
        .residency-card {
            transition: all 0.3s ease;
            border: 3px solid transparent;
        }
        
        .residency-card:hover {
            transform: translateY(-5px);
        }
        
        .residency-card.bonified { border-color: var(--primary-color); }
        .residency-card.migrant { border-color: #FF9800; }
        .residency-card.transient { border-color: #2196F3; }
        
        .percentage.bonified { color: var(--primary-color); }
        .percentage.migrant { color: #FF9800; }
        .percentage.transient { color: #2196F3; }
        
        .chart-container {
            position: relative;
            width: 120px;
            height: 120px;
        }
        
        @media (min-width: 768px) {
            .chart-container {
                width: 150px;
                height: 150px;
            }
        }
        
        .leaflet-popup-content-wrapper {
            background: var(--primary-color);
            color: white;
            border-radius: 8px;
        }
        
        .leaflet-popup-tip {
            background: var(--primary-color);
        }
        
        .animate-fade-in {
            animation: fadeInUp 0.6s ease forwards;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid p-3 p-md-4">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="bg-white bg-opacity-95 rounded-4 shadow-lg p-3 p-md-4 animate-fade-in">
                    
                    <!-- Top Statistics Cards -->
                    <div class="row g-3 g-md-4 mb-4">
                        <div class="col-6 col-lg-3">
                            <div class="stat-card text-white p-3 p-md-4 rounded-3 shadow-sm text-center">
                                <h6 class="mb-2 opacity-75 small">Total Population</h6>
                                <div class="display-6 fw-bold mb-0">15,429</div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="stat-card text-white p-3 p-md-4 rounded-3 shadow-sm text-center">
                                <h6 class="mb-2 opacity-75 small">Total Users</h6>
                                <div class="display-6 fw-bold mb-0">8,234</div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="stat-card text-white p-3 p-md-4 rounded-3 shadow-sm text-center">
                                <h6 class="mb-2 opacity-75 small">Total Barangay Document Requests</h6>
                                <div class="display-6 fw-bold mb-0">1,567</div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="stat-card text-white p-3 p-md-4 rounded-3 shadow-sm text-center">
                                <h6 class="mb-2 opacity-75 small">Total Complaints</h6>
                                <div class="display-6 fw-bold mb-0">89</div>
                            </div>
                        </div>
                    </div>

                    <!-- Middle Section -->
                    <div class="row g-3 g-md-4 mb-4">
                        <!-- Map Section -->
                        <div class="col-12 col-lg-6">
                            <div class="map-section text-white p-3 p-md-4 rounded-3 shadow-sm h-100">
                                <h5 class="mb-2">Barangay San Antonio Registered Voters</h5>
                                <p class="small opacity-75 mb-3">Showing voter registration coverage across different areas</p>
                                <div id="map" class="rounded-2"></div>
                            </div>
                        </div>

                        <!-- Population Breakdown -->
                        <div class="col-12 col-lg-6">
                            <div class="population-section text-white p-3 p-md-4 rounded-3 shadow-sm h-100">
                                <h5 class="mb-2">Barangay Population Breakdown</h5>
                                <p class="small opacity-75 mb-3">As of July 2025</p>
                                <div class="d-flex flex-column gap-2">
                                    <div class="d-flex justify-content-between align-items-center p-2 bg-white bg-opacity-10 rounded-2">
                                        <span class="small">Male</span>
                                        <span class="small fw-bold">7,890 (51.1%)</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center p-2 bg-white bg-opacity-10 rounded-2">
                                        <span class="small">Female</span>
                                        <span class="small fw-bold">7,539 (48.9%)</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center p-2 bg-white bg-opacity-10 rounded-2">
                                        <span class="small">Kids (0-12)</span>
                                        <span class="small fw-bold">3,086 (20.0%)</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center p-2 bg-white bg-opacity-10 rounded-2">
                                        <span class="small">Adults (18-59)</span>
                                        <span class="small fw-bold">9,257 (60.0%)</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center p-2 bg-white bg-opacity-10 rounded-2">
                                        <span class="small">Senior Citizen (60+)</span>
                                        <span class="small fw-bold">3,086 (20.0%)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 g-md-4">
                        <div class="col-12 col-md-4">
                            <div class="residency-card bonified bg-white p-3 p-md-4 rounded-3 shadow-sm text-center">
                                <div class="chart-container mx-auto mb-3">
                                    <canvas id="bonifiedChart"></canvas>
                                </div>
                                <h5 class="mb-2 text-dark">Bonafide</h5>
                                <div class="percentage bonified display-5 fw-bold mb-1">65%</div>
                                <div class="text-muted small">10,029 residents</div>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="residency-card migrant bg-white p-3 p-md-4 rounded-3 shadow-sm text-center">
                                <div class="chart-container mx-auto mb-3">
                                    <canvas id="migrantChart"></canvas>
                                </div>
                                <h5 class="mb-2 text-dark">Migrant</h5>
                                <div class="percentage migrant display-5 fw-bold mb-1">25%</div>
                                <div class="text-muted small">3,857 residents</div>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="residency-card transient bg-white p-3 p-md-4 rounded-3 shadow-sm text-center">
                                <div class="chart-container mx-auto mb-3">
                                    <canvas id="transientChart"></canvas>
                                </div>
                                <h5 class="mb-2 text-dark">Transient</h5>
                                <div class="percentage transient display-5 fw-bold mb-1">10%</div>
                                <div class="text-muted small">1,543 residents</div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <script>
        const map = L.map('map').setView([14.1078, 121.1419], 15);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        const voterAreas = [
            {
                name: "Poblacion Area",
                coords: [[14.1100, 121.1400], [14.1090, 121.1430], [14.1070, 121.1425], [14.1080, 121.1395]],
                voters: 1250,
                registered: true
            },
            {
                name: "Residential Zone A",
                coords: [[14.1050, 121.1380], [14.1040, 121.1410], [14.1020, 121.1405], [14.1030, 121.1375]],
                voters: 890,
                registered: true
            },
            {
                name: "Commercial Area",
                coords: [[14.1120, 121.1440], [14.1110, 121.1470], [14.1090, 121.1465], [14.1100, 121.1435]],
                voters: 650,
                registered: true
            },
            {
                name: "Rural Area",
                coords: [[14.1000, 121.1350], [14.0990, 121.1380], [14.0970, 121.1375], [14.0980, 121.1345]],
                voters: 0,
                registered: false
            }
        ];

        // Add polygons to map
        voterAreas.forEach(area => {
            const polygon = L.polygon(area.coords, {
                color: area.registered ? '#19AFA5' : '#ff4444',
                fillColor: area.registered ? '#19AFA5' : '#ff4444',
                fillOpacity: area.registered ? 0.6 : 0.3,
                weight: 2
            }).addTo(map);

            if (area.registered) {
                polygon.bindPopup(`
                    <strong>${area.name}</strong><br>
                    Registered Voters: ${area.voters.toLocaleString()}
                `);
            }
        });

        // Chart.js configurations
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeOutQuart'
            }
        };

        // Plugin to draw percentage inside chart
        const centerTextPlugin = {
            id: 'centerText',
            afterDraw(chart) {
                const { width, height, ctx } = chart;
                ctx.save();
                const dataset = chart.config.data.datasets[0];
                const value = dataset.data[0]; // percentage value
                ctx.font = (height / 5) + "px sans-serif";
                ctx.fillStyle = dataset.backgroundColor[0];
                ctx.textAlign = "center";
                ctx.textBaseline = "middle";
                ctx.fillText(value + "%", width / 2, height / 2);
                ctx.restore();
            }
        };

        // Create animated pie charts
        function createPieChart(canvasId, percentage, color) {
            const ctx = document.getElementById(canvasId).getContext('2d');
            return new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [percentage, 100 - percentage],
                        backgroundColor: [color, '#E0E0E0'],
                        borderWidth: 0
                    }]
                },
                options: chartOptions,
                plugins: [centerTextPlugin]
            });
        }

        // Initialize charts after page load
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                createPieChart('bonifiedChart', 65, '#19AFA5');
                createPieChart('migrantChart', 25, '#FF9800');
                createPieChart('transientChart', 10, '#2196F3');
            }, 500);
        });

        // Responsive map adjustments
        window.addEventListener('resize', function() {
            map.invalidateSize();
        });

        // Animate statistics on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all cards for animation
        document.querySelectorAll('.stat-card, .residency-card, .map-section, .population-section').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });
    </script>
</body>
</html>

<?php
include_once __DIR__ . "/../../sharedAssets/connect.php";

function getDemographicsData() {
    $query = "SELECT ui.userInfoID as id, ui.firstName, ui.lastName, ui.age, ui.gender 
              FROM userinfo ui 
              JOIN users u ON ui.userID = u.userID 
              WHERE u.role = 'user' AND ui.firstName IS NOT NULL";
    $result = executeQuery($query);

    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

// Get documents data with date filtering
function getDocumentsData($fromDate = null, $toDate = null) {
    $whereClause = "";
    if ($fromDate && $toDate) {
        $whereClause = " WHERE DATE(d.requestDate) BETWEEN '$fromDate' AND '$toDate'";
    }

    $query = "SELECT d.documentID, dt.documentName as type, d.documentStatus as status, 
              DATE(d.requestDate) as requestDate
              FROM documents d 
              JOIN documenttypes dt ON d.documentTypeID = dt.documentTypeID" . $whereClause . "
              ORDER BY d.requestDate DESC";

    $result = executeQuery($query);

    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

// Get complaints data with date filtering
function getComplaintsData($fromDate = null, $toDate = null) {
    $whereClause = "";
    if ($fromDate && $toDate) {
        $whereClause = " WHERE DATE(c.requestDate) BETWEEN '$fromDate' AND '$toDate'";
    }

    $query = "SELECT c.complaintID, c.complaintTitle as type, c.complaintStatus as status,
              DATE(c.requestDate) as requestDate
              FROM complaints c" . $whereClause . "
              ORDER BY c.requestDate DESC";

    $result = executeQuery($query);

    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

// Handle AJAX requests
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');

    if ($_GET['ajax'] == 'demographics') {
        echo json_encode(getDemographicsData());
        exit;
    }

    if ($_GET['ajax'] == 'documents') {
        $fromDate = $_GET['from'] ?? null;
        $toDate = $_GET['to'] ?? null;
        echo json_encode(getDocumentsData($fromDate, $toDate));
        exit;
    }

    if ($_GET['ajax'] == 'complaints') {
        $fromDate = $_GET['from'] ?? null;
        $toDate = $_GET['to'] ?? null;
        echo json_encode(getComplaintsData($fromDate, $toDate));
        exit;
    }
}

// Get initial data for page load
$demographicsData = getDemographicsData();
$documentsData    = getDocumentsData();
$complaintsData   = getComplaintsData();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Reports Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chart.js/4.4.0/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .bg-custom {
      background-color: #31afab !important;
      color: #fff;
    }
</style>
</head>

<body class="bg-white">
    <div class="container-fluid py-4">
        <div class="row mb-5">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-custom text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Demographic Reports</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-custom text-white">
                                        <h6 class="mb-0">Age Distribution</h6>
                                    </div>
                                    <div class="card-body" style="height:350px">
                                        <canvas id="ageChart"></canvas>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-custom text-dark">
                                        <h6 class="mb-0">Gender Distribution</h6>
                                    </div>
                                    <div class="card-body" style="height:350px">
                                        <canvas id="genderChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-start mt-3">
                            <button class="btn btn-success rounded-5 px-4 fw-bold" onclick="exportToExcel('demographics')">
                                Export to Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documents Services Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-custom text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Barangay Services Reports</h4>
                        <span class="badge bg-custom text-primary">Document Requests</span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4 p-3 bg-light rounded">
                            <div class="col-md-3 mb-2">
                                <label class="form-label fw-semibold">From Date:</label>
                                <input type="date" id="servicesFromDate" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label fw-semibold">To Date:</label>
                                <input type="date" id="servicesToDate" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3 mb-2 d-flex align-items-end">
                                <button class="btn btn-success btn-sm" onclick="filterServicesData()">
                                    Filter Data
                                </button>
                            </div>
                            <div class="col-md-3 mb-2 d-flex align-items-end justify-content-end">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-outline-danger btn-sm" onclick="exportToPDF('services')">
                                        📄 PDF
                                    </button>
                                    <button class="btn btn-outline-success btn-sm" onclick="exportToCSV('services')">
                                        📊 CSV
                                    </button>
                                    <button class="btn btn-outline-primary btn-sm" onclick="exportToExcel('services')">
                                        📈 Excel
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 mb-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">Document Requests by Type</h6>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="servicesBarChart" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-custom text-dark">
                                        <h6 class="mb-0">Monthly Document Requests</h6>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="servicesLineChart" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h5 id="totalRequests">0</h5>
                                        <small>Total Requests</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h5 id="completedRequests">0</h5>
                                        <small>Completed</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h5 id="pendingRequests">0</h5>
                                        <small>Pending</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h5 id="rejectedRequests">0</h5>
                                        <small>Rejected</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-custom text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Incident & Complaint Reports</h4>
                        <span class="badge bg-light text-danger">Blotter Records</span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4 p-3 bg-light rounded">
                            <div class="col-md-3 mb-2">
                                <label class="form-label fw-semibold">From Date:</label>
                                <input type="date" id="incidentFromDate" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label fw-semibold">To Date:</label>
                                <input type="date" id="incidentToDate" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3 mb-2 d-flex align-items-end">
                                <button class="btn btn-success btn-sm" onclick="filterIncidentData()">
                                    Filter Data
                                </button>
                            </div>
                            <div class="col-md-3 mb-2 d-flex align-items-end justify-content-end">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-outline-danger btn-sm" onclick="exportToPDF('incidents')">
                                        📄 PDF
                                    </button>
                                    <button class="btn btn-outline-success btn-sm" onclick="exportToCSV('incidents')">
                                        📊 CSV
                                    </button>
                                    <button class="btn btn-outline-primary btn-sm" onclick="exportToExcel('incidents')">
                                        📈 Excel
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 mb-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-danger text-white">
                                        <h6 class="mb-0">Incidents by Type</h6>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="incidentPieChart" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-custom text-white">
                                        <h6 class="mb-0">Complaint Status Overview</h6>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="complaintDoughnutChart" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-custom text-white">
                                        <h6 class="mb-0">Monthly Incident Trends</h6>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="incidentTrendChart" height="100"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2 mb-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h5 id="totalIncidents">0</h5>
                                        <small>Total Incidents</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="card bg-dark text-white">
                                    <div class="card-body text-center">
                                        <h5 id="disputes">0</h5>
                                        <small>Disputes</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h5 id="resolvedCases">0</h5>
                                        <small>Resolved</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="card bg-secondary text-white">
                                    <div class="card-body text-center">
                                        <h5 id="escalatedCases">0</h5>
                                        <small>Escalated</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    let ageChart, genderChart, residentsData = [];
    let servicesBarChart, servicesLineChart, incidentPieChart, complaintDoughnutChart, incidentTrendChart;

    const initialDemographicsData = <?php echo json_encode($demographicsData); ?>;
    const initialDocumentsData = <?php echo json_encode($documentsData); ?>;
    const initialComplaintsData = <?php echo json_encode($complaintsData); ?>;

    function getAgeGroup(age) {
        if (age <= 12) return "0-12 (Children)";
        if (age <= 17) return "13-17 (Teenagers)";
        if (age <= 59) return "18-59 (Adults)";
        return "60+ (Seniors)";
    }

    function formatMonthLabelsOrdered(obj) {
        const months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
        const map = new Map();
        for (const k in obj) map.set(k, obj[k]);

        const labels = months.filter(m => map.has(m));
        const data = labels.map(l => map.get(l) || 0);
        return { labels, data };
    }

    // Demographics Charts
    function initDemographicCharts() {
        const ageCtx = document.getElementById('ageChart').getContext('2d');
        ageChart = new Chart(ageCtx, {
            type: 'bar',
            data: {
                labels: ['0-12 (Children)', '13-17 (Teenagers)', '18-59 (Adults)', '60+ (Seniors)'],
                datasets: [{
                    label: 'Population',
                    data: [0,0,0,0],
                    backgroundColor: ['#dc3545','#6c757d','#007bff','#ffc107']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });

        const genderCtx = document.getElementById('genderChart').getContext('2d');
        genderChart = new Chart(genderCtx, {
            type: 'doughnut',
            data: {
                labels: ['Male','Female'],
                datasets: [{
                    data: [0,0],
                    backgroundColor: ['#007bff','#dc3545']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    function updateDemographicCharts(data) {
        const ageGroups = {'0-12 (Children)':0,'13-17 (Teenagers)':0,'18-59 (Adults)':0,'60+ (Seniors)':0};
        const genderCount = {Male:0,Female:0};

        data.forEach(resident => {
            const age = Number(resident.age) || 0;
            const gender = resident.gender || 'Male';
            ageGroups[getAgeGroup(age)]++;
            
            if (/female/i.test(gender)) {
                genderCount.Female++;
            } else {
                genderCount.Male++;
            }
        });

        ageChart.data.datasets[0].data = Object.values(ageGroups);
        ageChart.update();

        genderChart.data.datasets[0].data = [genderCount.Male, genderCount.Female];
        genderChart.update();
    }

    // Documents Charts
    function initializeServicesCharts() {
        const servicesBarCtx = document.getElementById('servicesBarChart').getContext('2d');
        servicesBarChart = new Chart(servicesBarCtx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Number of Requests',
                    data: [],
                    backgroundColor: '#007bff',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        const servicesLineCtx = document.getElementById('servicesLineChart').getContext('2d');
        servicesLineChart = new Chart(servicesLineCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Monthly Requests',
                    data: [],
                    backgroundColor: 'rgba(0,123,255,0.1)',
                    borderColor: '#007bff',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    function updateServicesCharts(data) {
        const processed = processDocumentsData(data);
        
        servicesBarChart.data.labels = Object.keys(processed.documentRequests);
        servicesBarChart.data.datasets[0].data = Object.values(processed.documentRequests);
        servicesBarChart.update();

        servicesLineChart.data.labels = processed.monthlyTrends.labels;
        servicesLineChart.data.datasets[0].data = processed.monthlyTrends.data;
        servicesLineChart.update();

        updateServicesSummary(processed.summary);
    }

    function updateServicesSummary(summary) {
        document.getElementById('totalRequests').textContent = summary.total || 0;
        document.getElementById('completedRequests').textContent = summary.completed || 0;
        document.getElementById('pendingRequests').textContent = summary.pending || 0;
        document.getElementById('rejectedRequests').textContent = summary.rejected || 0;
    }

    function processDocumentsData(rows) {
        const documentRequests = {};
        const monthly = {};
        const summary = { total: 0, completed: 0, pending: 0, rejected: 0 };

        rows.forEach(r => {
            summary.total++;
            const status = (r.status || '').toLowerCase();
            if (status === 'completed') summary.completed++;
            else if (status === 'pending') summary.pending++;
            else if (status === 'rejected') summary.rejected++;

            const type = r.type || 'Uncategorized';
            documentRequests[type] = (documentRequests[type] || 0) + 1;

            const dt = new Date(r.requestDate);
            if (!isNaN(dt)) {
                const mon = dt.toLocaleString('default', { month: 'short' });
                monthly[mon] = (monthly[mon] || 0) + 1;
            }
        });

        const monthlyOrdered = formatMonthLabelsOrdered(monthly);
        return { documentRequests, monthlyTrends: monthlyOrdered, summary };
    }

    // Complaints Charts
    function initializeIncidentCharts() {
        const incidentPieCtx = document.getElementById('incidentPieChart').getContext('2d');
        incidentPieChart = new Chart(incidentPieCtx, {
            type: 'pie',
            data: {
                labels: [],
                datasets: [{ data: [], backgroundColor: ['#dc3545', '#ffc107', '#28a745', '#007bff', '#6c757d'] }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        const complaintDoughnutCtx = document.getElementById('complaintDoughnutChart').getContext('2d');
        complaintDoughnutChart = new Chart(complaintDoughnutCtx, {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: [{ data: [], backgroundColor: ['#28a745', '#ffc107', '#dc3545'] }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        const incidentTrendCtx = document.getElementById('incidentTrendChart').getContext('2d');
        incidentTrendChart = new Chart(incidentTrendCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    { label: 'New Incidents', data: [], borderColor: '#dc3545', tension: 0.1 },
                    { label: 'Resolved Cases', data: [], borderColor: '#28a745', tension: 0.1 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    function updateIncidentCharts(data) {
        const processed = processComplaintsData(data);
        
        incidentPieChart.data.labels = Object.keys(processed.incidentTypes);
        incidentPieChart.data.datasets[0].data = Object.values(processed.incidentTypes);
        incidentPieChart.update();

        complaintDoughnutChart.data.labels = Object.keys(processed.complaintStatus);
        complaintDoughnutChart.data.datasets[0].data = Object.values(processed.complaintStatus);
        complaintDoughnutChart.update();

        incidentTrendChart.data.labels = processed.monthlyTrends.labels;
        incidentTrendChart.data.datasets[0].data = processed.monthlyTrends.incidents;
        incidentTrendChart.data.datasets[1].data = processed.monthlyTrends.resolved;
        incidentTrendChart.update();

        updateIncidentSummary(processed.summary);
    }

    function updateIncidentSummary(summary) {
        document.getElementById('totalIncidents').textContent = summary.total || 0;
        document.getElementById('theftCases').textContent = summary.theft || 0;
        document.getElementById('domesticViolence').textContent = summary.domesticViolence || 0;
        document.getElementById('disputes').textContent = summary.disputes || 0;
        document.getElementById('resolvedCases').textContent = summary.resolved || 0;
        document.getElementById('escalatedCases').textContent = summary.escalated || 0;
    }

    function processComplaintsData(rows) {
        const incidentTypes = {};
        const complaintStatus = {};
        const monthlyIncidents = {};
        const monthlyResolved = {};

        rows.forEach(r => {
            const type = (r.type || 'Uncategorized').toString();
            const status = (r.status || '').toString().toLowerCase();

            incidentTypes[type] = (incidentTypes[type] || 0) + 1;
            complaintStatus[status] = (complaintStatus[status] || 0) + 1;

            const dt = new Date(r.requestDate);
            if (!isNaN(dt)) {
                const mon = dt.toLocaleString('default', { month: 'short' });
                monthlyIncidents[mon] = (monthlyIncidents[mon] || 0) + 1;
                if (status === 'resolved') monthlyResolved[mon] = (monthlyResolved[mon] || 0) + 1;
            }
        });

        const orderedIncidents = formatMonthLabelsOrdered(monthlyIncidents);
        const orderedResolved = formatMonthLabelsOrdered(monthlyResolved);

        return {
            incidentTypes,
            complaintStatus,
            monthlyTrends: {
                labels: orderedIncidents.labels,
                incidents: orderedIncidents.data,
                resolved: orderedResolved.data.length ? orderedResolved.data : new Array(orderedIncidents.data.length).fill(0)
            },
            summary: {
                total: rows.length,
                theft: (incidentTypes['Theft'] || incidentTypes['theft'] || 0),
                domesticViolence: (incidentTypes['Domestic Violence'] || incidentTypes['domestic violence'] || 0),
                disputes: (incidentTypes['Dispute'] || incidentTypes['Property Dispute'] || incidentTypes['dispute'] || 0),
                resolved: complaintStatus['resolved'] || 0,
                escalated: complaintStatus['escalated'] || 0
            }
        };
    }

    // Filter functions
    async function filterServicesData() {
        const fromDate = document.getElementById('servicesFromDate').value;
        const toDate = document.getElementById('servicesToDate').value;
        
        if (!fromDate || !toDate) {
            alert('Please select both from and to dates');
            return;
        }

        try {
            const response = await fetch(`?ajax=documents&from=${fromDate}&to=${toDate}`);
            const data = await response.json();
            updateServicesCharts(data);
        } catch (error) {
            console.error('Error filtering services data:', error);
            alert('Error loading filtered data');
        }
    }

    async function filterIncidentData() {
        const fromDate = document.getElementById('incidentFromDate').value;
        const toDate = document.getElementById('incidentToDate').value;
        
        if (!fromDate || !toDate) {
            alert('Please select both from and to dates');
            return;
        }

        try {
            const response = await fetch(`?ajax=complaints&from=${fromDate}&to=${toDate}`);
            const data = await response.json();
            updateIncidentCharts(data);
        } catch (error) {
            console.error('Error filtering incident data:', error);
            alert('Error loading filtered data');
        }
    }

    // Export functions
    function exportToExcel(reportType) {
        if (!reportType || reportType === 'demographics') {
            const ws_data = [['ID', 'First Name', 'Last Name', 'Age', 'Gender', 'Age Group']];
            residentsData.forEach(r => {
                ws_data.push([r.id, r.firstName, r.lastName, r.age, r.gender, getAgeGroup(r.age)]);
            });
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(ws_data);
            XLSX.utils.book_append_sheet(wb, ws, "Demographics");
            XLSX.writeFile(wb, "Barangay_Demographics.xlsx");
            return;
        }

        if (reportType === 'services') {
            const fromDate = document.getElementById('servicesFromDate').value;
            const toDate = document.getElementById('servicesToDate').value;
            fetch(`?ajax=documents&from=${fromDate}&to=${toDate}`)
                .then(r => r.json())
                .then(rows => {
                    const ws_data = [['Document ID', 'Type', 'Status', 'Request Date']];
                    rows.forEach(row => {
                        ws_data.push([row.documentID, row.type, row.status, row.requestDate]);
                    });
                    const wb = XLSX.utils.book_new();
                    const ws = XLSX.utils.aoa_to_sheet(ws_data);
                    XLSX.utils.book_append_sheet(wb, ws, "Services");
                    XLSX.writeFile(wb, "Barangay_Services.xlsx");
                })
                .catch(err => {
                    console.error(err);
                    alert('Export failed');
                });
            return;
        }

        if (reportType === 'incidents') {
            const fromDate = document.getElementById('incidentFromDate').value;
            const toDate = document.getElementById('incidentToDate').value;
            fetch(`?ajax=complaints&from=${fromDate}&to=${toDate}`)
                .then(r => r.json())
                .then(rows => {
                    const ws_data = [['Complaint ID', 'Type', 'Status', 'Request Date']];
                    rows.forEach(row => {
                        ws_data.push([row.complaintID, row.type, row.status, row.requestDate]);
                    });
                    const wb = XLSX.utils.book_new();
                    const ws = XLSX.utils.aoa_to_sheet(ws_data);
                    XLSX.utils.book_append_sheet(wb, ws, "Complaints");
                    XLSX.writeFile(wb, "Barangay_Complaints.xlsx");
                })
                .catch(err => {
                    console.error(err);
                    alert('Export failed');
                });
            return;
        }
    }

    function exportToPDF(reportType) {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        doc.setFontSize(20);
        if (reportType === 'services') {
            doc.text("Barangay Services Report", 20, 20);
        } else if (reportType === 'incidents') {
            doc.text("Incident & Complaint Report", 20, 20);
        } else {
            doc.text("Demographics Report", 20, 20);
        }

        doc.setFontSize(12);
        doc.text("Generated on: " + new Date().toLocaleDateString(), 20, 40);
        
        doc.save(`Barangay_${reportType}_Report.pdf`);
    }

    function exportToCSV(reportType) {
        let csvContent = "data:text/csv;charset=utf-8,";
        let rows = [];

        if (reportType === 'services') {
            rows.push(['Document ID', 'Type', 'Status', 'Request Date']);
        } else if (reportType === 'incidents') {
            rows.push(['Complaint ID', 'Type', 'Status', 'Request Date']);
        } else {
            rows.push(['ID', 'First Name', 'Last Name', 'Age', 'Gender']);
            residentsData.forEach(r => {
                rows.push([r.id, r.firstName, r.lastName, r.age, r.gender]);
            });
        }

        rows.forEach(function(rowArray) {
            let row = rowArray.join(",");
            csvContent += row + "\r\n";
        });

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", `barangay_${reportType}_report.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function setDefaultDates() {
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        
        document.getElementById('servicesFromDate').value = firstDay.toISOString().split('T')[0];
        document.getElementById('servicesToDate').value = today.toISOString().split('T')[0];
        document.getElementById('incidentFromDate').value = firstDay.toISOString().split('T')[0];
        document.getElementById('incidentToDate').value = today.toISOString().split('T')[0];
    }

    document.addEventListener('DOMContentLoaded', function() {
        initDemographicCharts();
        initializeServicesCharts();
        initializeIncidentCharts();

        residentsData = initialDemographicsData;
        updateDemographicCharts(initialDemographicsData);
        updateServicesCharts(initialDocumentsData);
        updateIncidentCharts(initialComplaintsData);

        setDefaultDates();
    });
    </script>

</body>
</html>
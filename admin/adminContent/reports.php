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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
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
                                    <div class="card-header bg-custom text-white">
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

        <div class="row mb-5">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-custom text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Barangay Services Reports</h4>
                        <span class="badge bg-light text-info">Document Requests</span>
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
                                    <button class="btn btn-outline-danger btn-sm" onclick="showExportModal('services', 'pdf')">
                                        📄 PDF
                                    </button>
                                    <button class="btn btn-outline-success btn-sm" onclick="showExportModal('services', 'csv')">
                                        📊 CSV
                                    </button>
                                    <button class="btn btn-outline-primary btn-sm" onclick="showExportModal('services', 'excel')">
                                        📈 Excel
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 mb-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-custom text-white">
                                        <h6 class="mb-0">Document Requests by Type</h6>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="servicesBarChart" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-custom text-white">
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
                                        <h5 id="approvedRequests">0</h5>
                                        <small>Approved</small>
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
                                        <h5 id="deniedRequests">0</h5>
                                        <small>Denied</small>
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
                                    <button class="btn btn-outline-danger btn-sm" onclick="exportIncidentToPDF()">
                                        📄 PDF
                                    </button>
                                    <button class="btn btn-outline-success btn-sm" onclick="exportIncidentToCSV()">
                                        📊 CSV
                                    </button>
                                    <button class="btn btn-outline-primary btn-sm" onclick="exportIncidentToExcel()">
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
                            <div class="col-md-3 mb-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h5 id="totalIncidents">0</h5>
                                        <small>Total Incidents</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h5 id="resolvedCases">0</h5>
                                        <small>Resolved</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
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

    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-custom text-white">
                    <h5 class="modal-title" id="exportModalLabel">Select Document Type</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="documentTypeSelect" class="form-label">Document Type:</label>
                        <select class="form-select" id="documentTypeSelect">
                            <option value="First Time Job Seeker">First Time Job Seeker</option>
                            <option value="All">All Document Types</option>
                            <option value="Barangay Clearance">Barangay Clearance</option>
                            <option value="Business Clearance">Business Clearance</option>
                            <option value="Construction Clearance">Construction Clearance</option>
                            <option value="Good Health">Good Health</option>
                            <option value="Good Moral">Good Moral</option>
                            <option value="Joint Cohabitation">Joint Cohabitation</option>
                            <option value="Residency">Residency</option>
                            <option value="Solo Parent">Solo Parent</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="confirmExport()">Export</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    let ageChart, genderChart, residentsData = [];
    let servicesBarChart, servicesLineChart, incidentPieChart, complaintDoughnutChart, incidentTrendChart;
    let currentExportType = '';
    let currentServicesData = [];
    let currentComplaintsData = [];

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
        currentServicesData = data;
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
        document.getElementById('approvedRequests').textContent = summary.approved || 0;
        document.getElementById('pendingRequests').textContent = summary.pending || 0;
        document.getElementById('deniedRequests').textContent = summary.denied || 0;
    }

    function processDocumentsData(rows) {
        const documentRequests = {};
        const monthly = {};
        const summary = { total: 0, approved: 0, pending: 0, denied: 0 };

        rows.forEach(r => {
            summary.total++;
            const status = (r.status || '').toLowerCase();
            if (status === 'approved') summary.approved++;
            else if (status === 'pending') summary.pending++;
            else if (status === 'denied') summary.denied++;

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
        currentComplaintsData = data;
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
                resolved: complaintStatus['resolved'] || 0,
                escalated: complaintStatus['escalated'] || 0
            }
        };
    }

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

    // Export Modal Functions
    function showExportModal(reportType, exportType) {
        currentExportType = exportType;
        const modal = new bootstrap.Modal(document.getElementById('exportModal'));
        modal.show();
    }

    function confirmExport() {
        const documentType = document.getElementById('documentTypeSelect').value;
        const modal = bootstrap.Modal.getInstance(document.getElementById('exportModal'));
        modal.hide();

        if (currentExportType === 'pdf') {
            exportServicesToPDF(documentType);
        } else if (currentExportType === 'csv') {
            exportServicesToCSV(documentType);
        } else if (currentExportType === 'excel') {
            exportServicesToExcel(documentType);
        }
    }

//    Services Export Functions
    function exportServicesToPDF(documentType) {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        let filteredData = currentServicesData;
        if (documentType !== 'All') {
            filteredData = currentServicesData.filter(row => row.type === documentType);
        }

        if (filteredData.length === 0) {
            alert('No data available for the selected document type.');
            return;
        }

        doc.setFontSize(18);
        doc.setFont(undefined, 'bold');
        doc.text("Barangay Services Report", 14, 20);
        
        const fromDate = document.getElementById('servicesFromDate').value;
        const toDate = document.getElementById('servicesToDate').value;
        doc.setFontSize(10);
        doc.setFont(undefined, 'normal');
        doc.text(`Date Range: ${fromDate} to ${toDate}`, 14, 28);
        doc.text(`Document Type: ${documentType}`, 14, 34);
        doc.text(`Generated on: ${new Date().toLocaleDateString()}`, 14, 40);

        const headers = [['Document ID', 'Type', 'Status', 'Request Date']];
        const tableData = filteredData.map(row => [
            row.documentID,
            row.type,
            row.status.charAt(0).toUpperCase() + row.status.slice(1),
            row.requestDate
        ]);

        doc.autoTable({
            head: headers,
            body: tableData,
            startY: 45,
            theme: 'grid',
            styles: { fontSize: 9 },
            headStyles: { fillColor: [49, 175, 171] }
        });

        const finalY = doc.lastAutoTable.finalY + 10;
        doc.setFontSize(12);
        doc.setFont(undefined, 'bold');
        doc.text('Summary:', 14, finalY);
        doc.setFont(undefined, 'normal');
        doc.setFontSize(10);
        
        const approved = filteredData.filter(r => r.status.toLowerCase() === 'approved').length;
        const pending = filteredData.filter(r => r.status.toLowerCase() === 'pending').length;
        const denied = filteredData.filter(r => r.status.toLowerCase() === 'denied').length;

        doc.text(`Total Requests: ${filteredData.length}`, 14, finalY + 8);
        doc.text(`Approved: ${approved}`, 14, finalY + 14);
        doc.text(`Pending: ${pending}`, 14, finalY + 20);
        doc.text(`Denied: ${denied}`, 14, finalY + 26);

        doc.save(`Barangay_Services_${documentType.replace(/\s+/g, '_')}_Report.pdf`);
    }

    function exportServicesToCSV(documentType) {
        let filteredData = currentServicesData;
        if (documentType !== 'All') {
            filteredData = currentServicesData.filter(row => row.type === documentType);
        }

        if (filteredData.length === 0) {
            alert('No data available for the selected document type.');
            return;
        }

        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "Document ID,Type,Status,Request Date\n";

        filteredData.forEach(row => {
            csvContent += `${row.documentID},"${row.type}","${row.status}",${row.requestDate}\n`;
        });

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", `Barangay_Services_${documentType.replace(/\s+/g, '_')}_Report.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function exportServicesToExcel(documentType) {
        let filteredData = currentServicesData;
        if (documentType !== 'All') {
            filteredData = currentServicesData.filter(row => row.type === documentType);
        }

        if (filteredData.length === 0) {
            alert('No data available for the selected document type.');
            return;
        }

        const ws_data = [['Document ID', 'Type', 'Status', 'Request Date']];
        filteredData.forEach(row => {
            ws_data.push([row.documentID, row.type, row.status, row.requestDate]);
        });

        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(ws_data);
        XLSX.utils.book_append_sheet(wb, ws, "Services");
        XLSX.writeFile(wb, `Barangay_Services_${documentType.replace(/\s+/g, '_')}_Report.xlsx`);
    }

    // Incident Export Functions
    function exportIncidentToPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        if (currentComplaintsData.length === 0) {
            alert('No data available to export.');
            return;
        }

        doc.setFontSize(18);
        doc.setFont(undefined, 'bold');
        doc.text("Incident & Complaint Report", 14, 20);
        
        const fromDate = document.getElementById('incidentFromDate').value;
        const toDate = document.getElementById('incidentToDate').value;
        doc.setFontSize(10);
        doc.setFont(undefined, 'normal');
        doc.text(`Date Range: ${fromDate} to ${toDate}`, 14, 28);
        doc.text(`Generated on: ${new Date().toLocaleDateString()}`, 14, 34);

        const headers = [['Complaint ID', 'Type', 'Status', 'Request Date']];
        const tableData = currentComplaintsData.map(row => [
            row.complaintID,
            row.type,
            row.status.charAt(0).toUpperCase() + row.status.slice(1),
            row.requestDate
        ]);

        doc.autoTable({
            head: headers,
            body: tableData,
            startY: 40,
            theme: 'grid',
            styles: { fontSize: 9 },
            headStyles: { fillColor: [49, 175, 171] }
        });

        const finalY = doc.lastAutoTable.finalY + 10;
        doc.setFontSize(12);
        doc.setFont(undefined, 'bold');
        doc.text('Summary:', 14, finalY);
        doc.setFont(undefined, 'normal');
        doc.setFontSize(10);
        
        const resolved = currentComplaintsData.filter(r => r.status.toLowerCase() === 'resolved').length;
        const escalated = currentComplaintsData.filter(r => r.status.toLowerCase() === 'escalated').length;

        doc.text(`Total Incidents: ${currentComplaintsData.length}`, 14, finalY + 8);
        doc.text(`Resolved: ${resolved}`, 14, finalY + 14);
        doc.text(`Escalated: ${escalated}`, 14, finalY + 20);

        doc.save(`Barangay_Incidents_Report.pdf`);
    }

    function exportIncidentToCSV() {
        if (currentComplaintsData.length === 0) {
            alert('No data available to export.');
            return;
        }

        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "Complaint ID,Type,Status,Request Date\n";

        currentComplaintsData.forEach(row => {
            csvContent += `${row.complaintID},"${row.type}","${row.status}",${row.requestDate}\n`;
        });

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", `Barangay_Incidents_Report.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function exportIncidentToExcel() {
        if (currentComplaintsData.length === 0) {
            alert('No data available to export.');
            return;
        }

        const ws_data = [['Complaint ID', 'Type', 'Status', 'Request Date']];
        currentComplaintsData.forEach(row => {
            ws_data.push([row.complaintID, row.type, row.status, row.requestDate]);
        });

        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(ws_data);
        XLSX.utils.book_append_sheet(wb, ws, "Complaints");
        XLSX.writeFile(wb, `Barangay_Incidents_Report.xlsx`);
    }

    // Demographics Export
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
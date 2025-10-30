<?php
    include_once __DIR__ . "/../../sharedAssets/connect.php";

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Barangay Reports Dashboard</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <style>
        :root {
            --brand: #31afab;
            --card-radius: .6rem
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        .bg-brand {
            background: var(--brand) !important;
            color: #fff
        }

        .card {
            border-radius: var(--card-radius);
            box-shadow: 0 6px 20px rgba(0, 0, 0, .04)
        }

        .chart-container {
            height: 320px;
            min-height: 200px
        }

        .summary-card {
            border-radius: .5rem;
            color: #fff
        }

        .btn-outline-brand {
            color: var(--brand);
            border-color: var(--brand)
        }

        .btn-outline-brand:hover {
            background: var(--brand);
            color: #fff
        }

        @media(max-width:900px) {
            .chart-container {
                height: 260px
            }

            .card-body {
                padding: .9rem
            }
        }

        @media(max-width:576px) {
            .chart-container {
                height: 220px
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-brand d-flex justify-content-between align-items-center">
              <h1 class="h4 mb-0 fw-semibold">Demographic Reports</h1>
                        <button id="exportDemographicsBtn" class="btn btn-success btn-sm fw-bold">Export</button>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card border-0">
                                    <div class="card-header bg-brand text-white">
                                        <h6 class="mb-0">Age Distribution</h6>
                                    </div>
                                    <div class="card-body chart-container"><canvas id="ageChart"></canvas></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-0">
                                    <div class="card-header bg-brand text-white">
                                        <h6 class="mb-0">Gender Distribution</h6>
                                    </div>
                                    <div class="card-body chart-container"><canvas id="genderChart"></canvas></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-brand d-flex justify-content-between align-items-center">
                        <h1 class="h4 mb-0 fw-semibold">Barangay Services Reports</h1>
                        <span class="badge bg-light text-info">Document Requests</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-2 mb-3 p-3 bg-light rounded">
                            <div class="col-sm-6 col-md-3">
                                <label class="form-label small mb-1">From Date</label>
                                <input id="servicesFromDate" type="date" class="form-control form-control-sm">
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <label class="form-label small mb-1">To Date</label>
                                <input id="servicesToDate" type="date" class="form-control form-control-sm">
                            </div>
                            <div class="col-sm-6 col-md-3 d-flex align-items-end">
                                <button id="filterServicesBtn" class="btn btn-success btn-sm">Filter Data</button>
                            </div>
                            <div class="col-sm-6 col-md-3 d-flex align-items-end justify-content-end">
                                <div class="btn-group">
                                    <a id="servicesCsvBtn" class="btn btn-outline-brand btn-sm" href="#">📊 CSV</a>
                                    <a id="servicesPrintBtn" class="btn btn-outline-secondary btn-sm" href="#">📄
                                        Print</a>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <div class="card border-0">
                                    <div class="card-header bg-brand text-white">
                                        <h6 class="mb-0">Document Requests by Type</h6>
                                    </div>
                                    <div class="card-body"><canvas id="servicesBarChart" height="200"></canvas></div>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <div class="card border-0">
                                    <div class="card-header bg-brand text-white">
                                        <h6 class="mb-0">Monthly Document Requests</h6>
                                    </div>
                                    <div class="card-body"><canvas id="servicesLineChart" height="200"></canvas></div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-6 col-md-3">
                                <div class="summary-card bg-primary card text-center">
                                    <div class="card-body">
                                        <h5 id="totalRequests">0</h5><small>Total Requests</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="summary-card" style="background:#28a745">
                                    <div class="card-body text-center">
                                        <h5 id="approvedRequests">0</h5><small>Approved</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="summary-card" style="background:#ffc107;color:#212529">
                                    <div class="card-body text-center">
                                        <h5 id="pendingRequests">0</h5><small>Pending</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="summary-card" style="background:#dc3545">
                                    <div class="card-body text-center">
                                        <h5 id="deniedRequests">0</h5><small>Denied</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-brand d-flex justify-content-between align-items-center">
                        <h1 class="h4 mb-0 fw-semibold">Incident & Complaint Reports</h1>
                        <span class="badge bg-light text-danger">Blotter Records</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-2 mb-3 p-3 bg-light rounded">
                            <div class="col-sm-6 col-md-3">
                                <label class="form-label small mb-1">From Date</label>
                                <input id="incidentFromDate" type="date" class="form-control form-control-sm">
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <label class="form-label small mb-1">To Date</label>
                                <input id="incidentToDate" type="date" class="form-control form-control-sm">
                            </div>
                            <div class="col-sm-6 col-md-3 d-flex align-items-end">
                                <button id="filterIncidentBtn" class="btn btn-success btn-sm">Filter Data</button>
                            </div>
                            <div class="col-sm-6 col-md-3 d-flex align-items-end justify-content-end">
                                <div class="btn-group">
                                    <a id="incidentsCsvBtn" class="btn btn-outline-brand btn-sm" href="#">📊 CSV</a>
                                    <a id="incidentsPrintBtn" class="btn btn-outline-secondary btn-sm" href="#">📄
                                        Print</a>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <div class="card border-0">
                                    <div class="card-header bg-danger text-white">
                                        <h6 class="mb-0">Incidents by Type</h6>
                                    </div>
                                    <div class="card-body"><canvas id="incidentPieChart" height="200"></canvas></div>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <div class="card border-0">
                                    <div class="card-header bg-brand text-white">
                                        <h6 class="mb-0">Complaint Status Overview</h6>
                                    </div>
                                    <div class="card-body"><canvas id="complaintDoughnutChart" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row my-3">
                            <div class="col-12">
                                <div class="card border-0">
                                    <div class="card-header bg-brand text-white">
                                        <h6 class="mb-0">Monthly Incident Trends</h6>
                                    </div>
                                    <div class="card-body"><canvas id="incidentTrendChart" height="120"></canvas></div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-6 col-md-2">
                                <div class="summary-card" style="background:#dc3545">
                                    <div class="card-body text-center">
                                        <h5 id="totalIncidents">0</h5><small>Total Incidents</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-2">
                                <div class="summary-card" style="background:#0d6efd">
                                    <div class="card-body text-center">
                                        <h5 id="primaryCriminal">0</h5><small>Primary Criminal</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-2">
                                <div class="summary-card" style="background:#28a745">
                                    <div class="card-body text-center">
                                        <h5 id="resolvedCases">0</h5><small>Resolved</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-2">
                                <div class="summary-card" style="background:#ffc107;color:#212529">
                                    <div class="card-body text-center">
                                        <h5 id="escalatedCases">0</h5><small>Escalated</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-2">
                                <div class="summary-card" style="background:#343a40">
                                    <div class="card-body text-center">
                                        <h5 id="vawcRecords">0</h5><small>VAWC Records</small>
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

        const basePath = "/Baranggya/E-Barangay/admin/adminContent/"; // relative to this file: adminContent/
        const API_BASE = basePath + "api/";
        const EXPORT_BASE = basePath + "export/";
        const PRINT_BASE = basePath + "print/";

        function formatMonthLabelsOrdered(obj) {
            const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
            const map = new Map();
            for (const k in obj) map.set(k, obj[k]);
            const labels = months.filter(m => map.has(m));
            const data = labels.map(l => map.get(l) || 0);
            return { labels, data };
        }

        let ageChart, genderChart, servicesBarChart, servicesLineChart, incidentPieChart, complaintDoughnutChart, incidentTrendChart;

        function initCharts() {
            const ageCtx = document.getElementById('ageChart').getContext('2d');
            ageChart = new Chart(ageCtx, { type: 'bar', data: { labels: ['0-12', '13-17', '18-59', '60+'], datasets: [{ label: 'Population', data: [0, 0, 0, 0], backgroundColor: ['#198754', '#6c757d', '#0d6efd', '#ffc107'] }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } } });

            const genderCtx = document.getElementById('genderChart').getContext('2d');
            genderChart = new Chart(genderCtx, { type: 'doughnut', data: { labels: ['Male', 'Female'], datasets: [{ data: [0, 0], backgroundColor: ['#0d6efd', '#dc3545'] }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } } });

            const servicesBarCtx = document.getElementById('servicesBarChart').getContext('2d');
            servicesBarChart = new Chart(servicesBarCtx, { type: 'bar', data: { labels: [], datasets: [{ label: 'Requests', data: [], backgroundColor: '#0d6efd' }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } } });

            const servicesLineCtx = document.getElementById('servicesLineChart').getContext('2d');
            servicesLineChart = new Chart(servicesLineCtx, { type: 'line', data: { labels: [], datasets: [{ label: 'Monthly Requests', data: [], tension: 0.2, fill: true, backgroundColor: 'rgba(13,110,253,0.08)', borderColor: '#0d6efd' }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } } });

            incidentPieChart = new Chart(document.getElementById('incidentPieChart'), { type: 'pie', data: { labels: [], datasets: [{ data: [], backgroundColor: ['#dc3545', '#ffc107', '#28a745', '#0d6efd', '#6c757d'] }] }, options: { responsive: true, maintainAspectRatio: false } });

            complaintDoughnutChart = new Chart(document.getElementById('complaintDoughnutChart'), { type: 'doughnut', data: { labels: [], datasets: [{ data: [], backgroundColor: ['#28a745', '#ffc107', '#dc3545'] }] }, options: { responsive: true, maintainAspectRatio: false } });

            incidentTrendChart = new Chart(document.getElementById('incidentTrendChart'), { type: 'line', data: { labels: [], datasets: [{ label: 'New Incidents', data: [], borderColor: '#dc3545', tension: 0.2 }, { label: 'Resolved', data: [], borderColor: '#28a745', tension: 0.2 }] }, options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } } });
        }


        function getAgeGroup(age) {
            if (!age || isNaN(Number(age))) return '60+';
            age = Number(age);
            if (age <= 12) return '0-12';
            if (age <= 17) return '13-17';
            if (age <= 59) return '18-59';
            return '60+';
        }

        function updateDemographicCharts(data) {
            const ageGroups = { '0-12': 0, '13-17': 0, '18-59': 0, '60+': 0 };
            const genderCount = { Male: 0, Female: 0 };
            data.forEach(u => {
                const group = getAgeGroup(u.age);
                ageGroups[group] = (ageGroups[group] || 0) + 1;
                const g = (u.gender || '').toLowerCase();
                if (/female/i.test(g)) genderCount.Female++; else genderCount.Male++;
            });
            ageChart.data.datasets[0].data = [ageGroups['0-12'], ageGroups['13-17'], ageGroups['18-59'], ageGroups['60+']];
            ageChart.update();
            genderChart.data.datasets[0].data = [genderCount.Male, genderCount.Female];
            genderChart.update();
        }

        function processDocumentsData(rows) {
            const docTypes = {};
            const monthly = {};
            const summary = { total: 0, approved: 0, pending: 0, denied: 0 };
            rows.forEach(r => {
                summary.total++;
                const s = (r.status || '').toLowerCase();
                if (s === 'approved') summary.approved++;
                else if (s === 'pending') summary.pending++;
                else if (s === 'denied') summary.denied++;
                const type = r.type || 'Uncategorized';
                docTypes[type] = (docTypes[type] || 0) + 1;
                const dt = new Date(r.requestDate);
                if (!isNaN(dt)) {
                    const mon = dt.toLocaleString('default', { month: 'short' });
                    monthly[mon] = (monthly[mon] || 0) + 1;
                }
            });
            return { docTypes, monthly: formatMonthLabelsOrdered(monthly), summary };
        }

        function updateServicesCharts(rows) {
            const p = processDocumentsData(rows);
            servicesBarChart.data.labels = Object.keys(p.docTypes);
            servicesBarChart.data.datasets[0].data = Object.values(p.docTypes);
            servicesBarChart.update();
            servicesLineChart.data.labels = p.monthly.labels;
            servicesLineChart.data.datasets[0].data = p.monthly.data;
            servicesLineChart.update();
            document.getElementById('totalRequests').textContent = p.summary.total || 0;
            document.getElementById('approvedRequests').textContent = p.summary.approved || 0;
            document.getElementById('pendingRequests').textContent = p.summary.pending || 0;
            document.getElementById('deniedRequests').textContent = p.summary.denied || 0;
        }

        function processComplaintsData(rows) {
            const types = {};
            const statusMap = {};
            const monthlyIncidents = {};
            const monthlyResolved = {};

            rows.forEach(r => {
                const t = r.type || 'Uncategorized';
                const st = (r.status || '').toLowerCase();
                types[t] = (types[t] || 0) + 1;
                statusMap[st] = (statusMap[st] || 0) + 1;

                const dt = new Date(r.requestDate);
                if (!isNaN(dt)) {
                    const mon = dt.toLocaleString('default', { month: 'short' });
                    monthlyIncidents[mon] = (monthlyIncidents[mon] || 0) + 1;
                    if (['withdrawn', 'repudiated', 'dismissed', 'certified', 'resolved'].includes(st)) {
                        monthlyResolved[mon] = (monthlyResolved[mon] || 0) + 1;
                    }
                }
            });

            const total = rows.length;
            const primaryCriminal = rows.filter(r => ['criminal', 'civil'].includes((r.status || '').toLowerCase())).length;
            const resolved = rows.filter(r => ['withdrawn', 'repudiated', 'dismissed', 'certified', 'resolved'].includes((r.status || '').toLowerCase())).length;
            const escalated = rows.filter(r => ['mediation', 'conciliation', 'arbitration', 'pending'].includes((r.status || '').toLowerCase())).length;
            const vawc = rows.filter(r => ((r.status || '').toLowerCase().indexOf('vawc') !== -1)).length;

            return {
                types,
                statusMap,
                monthly: { labels: formatMonthLabelsOrdered(monthlyIncidents).labels, incidents: formatMonthLabelsOrdered(monthlyIncidents).data, resolved: formatMonthLabelsOrdered(monthlyResolved).data },
                summary: { total, primaryCriminal, resolved, escalated, vawc }
            };
        }

        function updateIncidentCharts(rows) {
            const p = processComplaintsData(rows);
            incidentPieChart.data.labels = Object.keys(p.types);
            incidentPieChart.data.datasets[0].data = Object.values(p.types);
            incidentPieChart.update();

            complaintDoughnutChart.data.labels = Object.keys(p.statusMap);
            complaintDoughnutChart.data.datasets[0].data = Object.values(p.statusMap);
            complaintDoughnutChart.update();

            incidentTrendChart.data.labels = p.monthly.labels;
            incidentTrendChart.data.datasets[0].data = p.monthly.incidents;
            incidentTrendChart.data.datasets[1].data = p.monthly.resolved.length ? p.monthly.resolved : new Array(p.monthly.incidents.length).fill(0);
            incidentTrendChart.update();

            document.getElementById('totalIncidents').textContent = p.summary.total || 0;
            document.getElementById('primaryCriminal').textContent = p.summary.primaryCriminal || 0;
            document.getElementById('resolvedCases').textContent = p.summary.resolved || 0;
            document.getElementById('escalatedCases').textContent = p.summary.escalated || 0;
            document.getElementById('vawcRecords').textContent = p.summary.vawc || 0;
        }

        async function fetchJSON(url) {
            const res = await fetch(url, { cache: "no-store" });
            if (!res.ok) throw new Error('Network response was not ok');
            return await res.json();
        }

        let currentServicesData = [], currentComplaintsData = [], currentDemographicsData = [];

        async function loadInitialData() {
            const demUrl = API_BASE + "getDemographics.php";
            const docsUrl = API_BASE + "getDocuments.php";
            const compUrl = API_BASE + "getComplaints.php";
            const [dem, docs, comps] = await Promise.all([fetchJSON(demUrl), fetchJSON(docsUrl), fetchJSON(compUrl)]);
            currentDemographicsData = dem || [];
            currentServicesData = docs || [];
            currentComplaintsData = comps || [];
            updateDemographicCharts(currentDemographicsData);
            updateServicesCharts(currentServicesData);
            updateIncidentCharts(currentComplaintsData);
        }

        function ensureDateInputsDefault() {
            const today = new Date();
            const first = new Date(today.getFullYear(), today.getMonth(), 1);
            const isoToday = today.toISOString().split('T')[0];
            const isoFirst = first.toISOString().split('T')[0];
            if (!document.getElementById('servicesFromDate').value) document.getElementById('servicesFromDate').value = isoFirst;
            if (!document.getElementById('servicesToDate').value) document.getElementById('servicesToDate').value = isoToday;
            if (!document.getElementById('incidentFromDate').value) document.getElementById('incidentFromDate').value = isoFirst;
            if (!document.getElementById('incidentToDate').value) document.getElementById('incidentToDate').value = isoToday;
        }

        document.addEventListener('DOMContentLoaded', async function () {
            initCharts();
            ensureDateInputsDefault();

            try {
                await loadInitialData();
            } catch (err) {
                console.error('Error loading initial data', err);
                alert('Error loading initial data. Check console and ensure API files exist.');
            }

            document.getElementById('filterServicesBtn').addEventListener('click', async function () {
                const from = document.getElementById('servicesFromDate').value;
                const to = document.getElementById('servicesToDate').value;
                if (!from || !to) return alert('Please select both from and to dates.');
                try {
                    const rows = await fetchJSON(API_BASE + `getDocuments.php?from=${from}&to=${to}`);
                    currentServicesData = rows;
                    updateServicesCharts(rows);
                } catch (err) {
                    console.error(err);
                    alert('Could not load filtered services data.');
                }
            });

            document.getElementById('filterIncidentBtn').addEventListener('click', async function () {
                const from = document.getElementById('incidentFromDate').value;
                const to = document.getElementById('incidentToDate').value;
                if (!from || !to) return alert('Please select both from and to dates.');
                try {
                    const rows = await fetchJSON(API_BASE + `getComplaints.php?from=${from}&to=${to}`);
                    currentComplaintsData = rows;
                    updateIncidentCharts(rows);
                } catch (err) {
                    console.error(err);
                    alert('Could not load filtered incident data.');
                }
            });

            document.getElementById('servicesCsvBtn').addEventListener('click', function (e) {
                e.preventDefault();
                const from = document.getElementById('servicesFromDate').value;
                const to = document.getElementById('servicesToDate').value;
                window.location = EXPORT_BASE + `exportDocumentsCSV.php?from=${from}&to=${to}`;
            });
            document.getElementById('servicesPrintBtn').addEventListener('click', function (e) {
                e.preventDefault();
                const from = document.getElementById('servicesFromDate').value;
                const to = document.getElementById('servicesToDate').value;
                window.open(PRINT_BASE + `printDocuments.php?from=${from}&to=${to}`, '_blank');
            });

            document.getElementById('incidentsCsvBtn').addEventListener('click', function (e) {
                e.preventDefault();
                const from = document.getElementById('incidentFromDate').value;
                const to = document.getElementById('incidentToDate').value;
                window.location = EXPORT_BASE + `exportComplaintsCSV.php?from=${from}&to=${to}`;
            });
            document.getElementById('incidentsPrintBtn').addEventListener('click', function (e) {
                e.preventDefault();
                const from = document.getElementById('incidentFromDate').value;
                const to = document.getElementById('incidentToDate').value;
                window.open(PRINT_BASE + `printComplaints.php?from=${from}&to=${to}`, '_blank');
            });

            document.getElementById('exportDemographicsBtn').addEventListener('click', function () {
                const rows = currentDemographicsData || [];
                if (!rows.length) return alert('No demographics data to export.');
                const ws = [['ID', 'First Name', 'Last Name', 'Age', 'Gender', 'Age Group']];
                rows.forEach(r => ws.push([r.id, r.firstName, r.lastName, r.age, r.gender, getAgeGroup(r.age)]));
                const wb = XLSX.utils.book_new();
                const wsSheet = XLSX.utils.aoa_to_sheet(ws);
                XLSX.utils.book_append_sheet(wb, wsSheet, 'Demographics');
                XLSX.writeFile(wb, 'Barangay_Demographics.xlsx');
            });
        });
    </script>
</body>

</html>
const basePath = "../admin/adminContent/";
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

    complaintDoughnutChart = new Chart(document.getElementById('complaintDoughnutChart'), { type: 'doughnut', data: { labels: [], datasets: [{ data: [], backgroundColor: ['#ffc107', '#28a745', '#dc3545'] }] }, options: { responsive: true, maintainAspectRatio: false } });

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

    await loadInitialData();


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

    let exportAction = "";
    const exportModal = new bootstrap.Modal(document.getElementById('documentExportModal'));

    document.getElementById('servicesCsvBtn').addEventListener('click', function (e) {
        e.preventDefault();
        exportAction = "csv";
        exportModal.show();
    });

    document.getElementById('servicesPrintBtn').addEventListener('click', function (e) {
        e.preventDefault();
        exportAction = "print";
        exportModal.show();
    });

    document.getElementById('confirmExportBtn').addEventListener('click', function () {
        const from = document.getElementById('servicesFromDate').value;
        const to = document.getElementById('servicesToDate').value;
        const documentType = encodeURIComponent(document.getElementById('documentTypeSelect').value);
        exportModal.hide();

        if (!from || !to) {
            alert("Please select both 'From' and 'To' dates before proceeding.");
            return;
        }

        if (exportAction === "csv") {
            window.location = `${EXPORT_BASE}exportDocumentsCSV.php?from=${from}&to=${to}&type=${documentType}`;
        } else if (exportAction === "print") {
            window.open(`${PRINT_BASE}printDocuments.php?from=${from}&to=${to}&type=${documentType}`, '_blank');
        }
    });

    document.getElementById('documentExportModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('documentTypeSelect').value = "First Time Job Seeker";
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
        let rows = currentDemographicsData || [];
        if (!rows.length) return alert('No demographics data to export.');
        rows = rows.sort((a, b) => a.lastName.localeCompare(b.lastName, 'en', { sensitivity: 'base' }));

        const ws = [[
            'Last Name', 'First Name', 'Middle Name', 'Suffix', 'Gender',
            'Birth Date', 'Age', 'Birth Place', 'Blood Type', 'Civil Status',
            'Citizenship', 'Occupation', 'Length Of Stay', 'Residency Type',
            'Remarks', 'Address'
        ]];

        rows.forEach(r => ws.push([
            r.lastName || '',
            r.firstName || '',
            r.middleName || '',
            r.suffix || '',
            r.gender || '',
            r.birthDate || '',
            r.age || '',
            r.birthPlace || '',
            r.bloodType || '',
            r.civilStatus || '',
            r.citizenship || '',
            r.occupation || '',
            r.lengthOfStay || '',
            r.residencyType || '',
            r.remarks || '',
            r.address || ''
        ]));

        const wb = XLSX.utils.book_new();
        const wsSheet = XLSX.utils.aoa_to_sheet(ws);
        XLSX.utils.book_append_sheet(wb, wsSheet, 'Demographics');
        XLSX.writeFile(wb, 'Barangay_Demographics.xlsx');
    });

});

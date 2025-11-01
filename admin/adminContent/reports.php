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
        <!-- Document Export Modal -->
        <div class="modal fade" id="documentExportModal" tabindex="-1" aria-labelledby="documentExportModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-3">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="documentExportModalLabel">Export or Print Documents</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="documentTypeSelect" class="form-label fw-semibold">Select Document Type:</label>
                            <select id="documentTypeSelect" class="form-select">
                                <option value="All">All</option>
                                <?php
                                include_once __DIR__ . "/../../../sharedAssets/connect.php";
                                $query = "SELECT documentName FROM documenttypes ORDER BY documentName ASC";
                                $result = $conn->query($query);
                                if ($result && $result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo '<option value="' . htmlspecialchars($row['documentName']) . '">' . htmlspecialchars($row['documentName']) . '</option>';
                                    }
                                } else {
                                    echo '<option disabled>No document types found</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <p class="text-muted small mb-0">
                            <i class="bi bi-info-circle"></i>
                            Select the document type you want to export or print.
                        </p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success px-4" id="confirmExportBtn">Proceed</button>
                    </div>
                </div>
            </div>
        </div>



        <script src="../admin/assets/js/scriptReport.js"></script>
</body>

</html>
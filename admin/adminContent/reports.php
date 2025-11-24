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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
</head>

<body style="font-family:'Poppins',sans-serif;background-color:#f0f2f5;">
    <div class="container-fluid py-4 px-3 px-lg-4">

        <!-- Demographic Reports Section -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius:0.5rem;">
            <div class="d-flex flex-wrap justify-content-between align-items-center p-3"
                style="background:#31afab;color:white;border-radius:0.5rem 0.5rem 0 0;">
                <h5 class="mb-0 fw-semibold">Demographic Reports</h5>
                <button id="exportDemographicsBtn" class="btn btn-success text-white fw-bold px-3 py-2"
                    style="font-size:0.875rem;">Export</button>
            </div>
            <div class="card-body p-3 p-lg-4">
                <div class="row g-3 g-lg-4">
                    <div class="col-12 col-lg-6">
                        <div class="border-0 shadow-sm" style="background:white;border-radius:0.5rem;overflow:hidden;">
                            <div class="text-white fw-semibold p-2 px-3" style="background:#31afab;font-size:0.95rem;">
                                Age Distribution</div>
                            <div class="p-3 d-flex align-items-center justify-content-center" style="height:320px;">
                                <canvas id="ageChart" style="max-width:100%;max-height:100%;"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="border-0 shadow-sm" style="background:white;border-radius:0.5rem;overflow:hidden;">
                            <div class="text-white fw-semibold p-2 px-3" style="background:#31afab;font-size:0.95rem;">
                                Gender Distribution</div>
                            <div class="p-3 d-flex align-items-center justify-content-center" style="height:320px;">
                                <canvas id="genderChart" style="max-width:100%;max-height:100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barangay Services Reports Section -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius:0.5rem;">
            <div class="d-flex flex-wrap justify-content-between align-items-center p-3 gap-2"
                style="background:#31afab;color:white;border-radius:0.5rem 0.5rem 0 0;">
                <h5 class="mb-0 fw-semibold">Barangay Services Reports</h5>
                <span class="badge bg-light text-info px-3 py-2" style="border-radius:20px;">Document Requests</span>
            </div>
            <div class="card-body p-3 p-lg-4">
                <div class="bg-light rounded p-3 mb-4">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label fw-semibold mb-2" style="font-size:0.875rem;">From Date</label>
                            <input id="servicesFromDate" type="date" class="form-control">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label fw-semibold mb-2" style="font-size:0.875rem;">To Date</label>
                            <input id="servicesToDate" type="date" class="form-control">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3 d-flex align-items-end">
                            <button id="filterServicesBtn" class="btn btn-success w-50 fw-semibold">Filter Data</button>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3 d-flex align-items-end">
                            <div class="d-flex gap-2 w-100">
                                <a id="servicesCsvBtn"
                                    class="btn btn-outline-info flex-fill text-center text-decoration-none fw-semibold"
                                    href="#" style="font-size:0.875rem;">📊 CSV</a>
                                <a id="servicesPrintBtn"
                                    class="btn btn-outline-secondary flex-fill text-center text-decoration-none fw-semibold"
                                    href="#" style="font-size:0.875rem;">📄 Print</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 g-lg-4 mb-4">
                    <div class="col-12 col-lg-6">
                        <div class="border-0 shadow-sm" style="background:white;border-radius:0.5rem;overflow:hidden;">
                            <div class="text-white fw-semibold p-2 px-3" style="background:#31afab;font-size:0.95rem;">
                                Document Requests by Type</div>
                            <div class="p-3 d-flex align-items-center justify-content-center" style="height:320px;">
                                <canvas id="servicesBarChart" style="max-width:100%;max-height:100%;"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="border-0 shadow-sm" style="background:white;border-radius:0.5rem;overflow:hidden;">
                            <div class="text-white fw-semibold p-2 px-3" style="background:#31afab;font-size:0.95rem;">
                                Monthly Document Requests</div>
                            <div class="p-3 d-flex align-items-center justify-content-center" style="height:320px;">
                                <canvas id="servicesLineChart" style="max-width:100%;max-height:100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-6 col-lg-3">
                        <div class="rounded text-center text-white p-4" style="background:#31afab;">
                            <h4 id="totalRequests" class="mb-2 fw-bold" style="font-size:1rem;"></h4>
                            <p class="mb-0" style="font-size:0.9rem;">Total Requests</p>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="rounded text-center text-white p-4" style="background:#3CB371;">
                            <h4 id="approvedRequests" class="mb-2 fw-bold" style="font-size:1rem;"></h4>
                            <p class="mb-0" style="font-size:0.9rem;">Approved</p>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="rounded text-center text-white p-4" style="background:#E0B72E;">
                            <h4 id="pendingRequests" class="mb-2 fw-bold" style="font-size:1rem;"></h4>
                            <p class="mb-0" style="font-size:0.9rem;">Pending</p>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="rounded text-center text-white p-4" style="background:#DC3545;">
                            <h4 id="deniedRequests" class="mb-2 fw-bold" style="font-size:1rem;"></h4>
                            <p class="mb-0" style="font-size:0.9rem;">Denied</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Incident & Complaint Reports Section -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius:0.5rem;">
            <div class="d-flex flex-wrap justify-content-between align-items-center p-3 gap-2"
                style="background:#31afab;color:white;border-radius:0.5rem 0.5rem 0 0;">
                <h5 class="mb-0 fw-semibold">Incident & Complaint Reports</h5>
                <span class="badge bg-light text-danger px-3 py-2" style="border-radius:20px;">Blotter Records</span>
            </div>
            <div class="card-body p-3 p-lg-4">
                <div class="bg-light rounded p-3 mb-4">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label fw-semibold mb-2" style="font-size:0.875rem;">From Date</label>
                            <input id="incidentFromDate" type="date" class="form-control">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label fw-semibold mb-2" style="font-size:0.875rem;">To Date</label>
                            <input id="incidentToDate" type="date" class="form-control">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3 d-flex align-items-end">
                            <button id="filterIncidentBtn" class="btn btn-success w-50 fw-semibold">Filter Data</button>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3 d-flex align-items-end">
                            <div class="d-flex gap-2 w-100">
                                <a id="incidentsCsvBtn"
                                    class="btn btn-outline-info flex-fill text-center text-decoration-none fw-semibold"
                                    href="#" style="font-size:0.875rem;">📊 CSV</a>
                                <a id="incidentsPrintBtn"
                                    class="btn btn-outline-secondary flex-fill text-center text-decoration-none fw-semibold"
                                    href="#" style="font-size:0.875rem;">📄 Print</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 g-lg-4 mb-4">
                    <div class="col-12 col-lg-6">
                        <div class="border-0 shadow-sm" style="background:white;border-radius:0.5rem;overflow:hidden;">
                            <div class="text-white fw-semibold p-2 px-3" style="background:#dc3545;font-size:0.95rem;">
                                Incidents by Type</div>
                            <div class="p-3 d-flex align-items-center justify-content-center" style="height:320px;">
                                <canvas id="incidentPieChart" style="max-width:100%;max-height:100%;"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="border-0 shadow-sm" style="background:white;border-radius:0.5rem;overflow:hidden;">
                            <div class="text-white fw-semibold p-2 px-3" style="background:#31afab;font-size:0.95rem;">
                                Complaint Status Overview</div>
                            <div class="p-3 d-flex align-items-center justify-content-center" style="height:320px;">
                                <canvas id="complaintDoughnutChart" style="max-width:100%;max-height:100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-12">
                        <div class="border-0 shadow-sm" style="background:white;border-radius:0.5rem;overflow:hidden;">
                            <div class="text-white fw-semibold p-2 px-3" style="background:#31afab;font-size:0.95rem;">
                                Monthly Incident Trends</div>
                            <div class="p-3 d-flex align-items-center justify-content-center" style="height:320px;">
                                <canvas id="incidentTrendChart" style="max-width:100%;max-height:100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-6 col-md-4 col-lg">
                        <div class="rounded text-center text-white p-3" style="background:#31afab;">
                            <h6 id="totalIncidents" class="mb-2 fw-bold" style="font-size:1.5rem;">4</h6>
                            <p class="mb-0" style="font-size:0.75rem;">Total Incidents</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-lg">
                        <div class="rounded text-center text-white p-3" style="background:#dc3545;">
                            <h6 id="primaryCriminal" class="mb-2 fw-bold" style="font-size:1.5rem;">0</h6>
                            <p class="mb-0" style="font-size:0.75rem;">Primary Criminal</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-lg">
                        <div class="rounded text-center text-white p-3" style="background:#3CB371;">
                            <h6 id="resolvedCases" class="mb-2 fw-bold" style="font-size:1.5rem;">0</h6>
                            <p class="mb-0" style="font-size:0.75rem;">Resolved</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-6 col-lg">
                        <div class="rounded text-center text-white p-3" style="background:#E0B72E;">
                            <h6 id="escalatedCases" class="mb-2 fw-bold" style="font-size:1.5rem;">4</h6>
                            <p class="mb-0" style="font-size:0.75rem;">Escalated</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg">
                        <div class="rounded text-center text-white p-3" style="background:#343a40;">
                            <h6 id="vawcRecords" class="mb-2 fw-bold" style="font-size:1.5rem;">0</h6>
                            <p class="mb-0" style="font-size:0.75rem;">VAWC Records</p>
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
            <div class="modal-content" style="border-radius:1rem;">
                <div class="modal-header text-white" style="background:#31afab;">
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
                        Select the document type you want to export or print.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmExportBtn">Proceed</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="../admin/assets/js/scriptReport.js"></script>
</body>

</html>
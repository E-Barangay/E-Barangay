<?php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../index.php");
  exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Document Requests</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<style>
  body {
    font-family: 'Poppins', sans-serif;
    background-color: rgb(233, 233, 233);
    color: dark;
    height: 100vh;
    margin: 0;
    padding: 0;
  }
</style>

<body>

  <div class="container-fluid p-3 p-md-4">
    <div class="card shadow-lg border-0 rounded-3">
      <div class="card-body p-0">

        <div class="text-white p-4 rounded-top" style="background-color: rgb(49, 175, 171);">
          <div class="d-flex align-items-center">
            <i class="fas fa-file-alt me-3 fs-4"></i>
            <h1 class="h4 mb-0 fw-semibold">Document Requests Management</h1>
          </div>
        </div>

        <div class="p-3 p-md-4">

          <div class="card shadow-sm mb-4">
            <div class="card-body">
              <div class="row g-3">
                <div class="col-lg-4 col-md-6">
                  <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                      <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" placeholder="Search User">
                  </div>
                </div>
                <div class="col-lg-3 col-md-6">
                  <select class="form-select">
                    <option selected>All Status</option>
                    <option>Pending</option>
                    <option>Approved</option>
                    <option>Denied</option>
                    <option>In Progress</option>
                  </select>
                </div>
                <div class="col-lg-3 col-md-6">
                  <input type="date" class="form-control">
                </div>
                <div class="col-lg-2 col-md-6">
                  <button class="btn btn-info text-white w-100">
                    <i class="fas fa-filter me-2"></i>Filter
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow-sm">
            <div class="card-body p-0">
              <div class="d-none d-lg-block">
                <div class="table-responsive">
                  <table class="table table-hover mb-0">
                    <thead class="table-light">
                      <tr>
                        <th class="px-4 py-3 fw-semibold">Report ID</th>
                        <th class="px-4 py-3 fw-semibold">Date Submitted</th>
                        <th class="px-4 py-3 fw-semibold">Requester Name</th>
                        <th class="px-4 py-3 fw-semibold">Purpose</th>
                        <th class="px-4 py-3 fw-semibold">Contact</th>
                        <th class="px-4 py-3 fw-semibold">Status</th>
                        <th class="px-4 py-3 fw-semibold">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td class="px-4 py-3"><strong>1</strong></td>
                        <td class="px-4 py-3">2025-01-01</td>
                        <td class="px-4 py-3">John Doe</td>
                        <td class="px-4 py-3">
                          <span class="badge rounded-pill bg-info">For Job</span>
                        </td>
                        <td class="px-4 py-3">205-001-01</td>
                        <td class="px-4 py-3">
                          <span class="badge rounded-pill bg-warning text-dark">Pending</span>
                        </td>
                        <td class="px-4 py-3">
                          <button class="btn btn-sm btn-primary">
                            <i class="fas fa-eye me-1"></i>View
                          </button>
                        </td>
                      </tr>
                      <tr>
                        <td class="px-4 py-3"><strong>2</strong></td>
                        <td class="px-4 py-3">2025-01-02</td>
                        <td class="px-4 py-3">Jane Smith</td>
                        <td class="px-4 py-3">
                          <span class="badge rounded-pill bg-danger">Noise Complaint</span>
                        </td>
                        <td class="px-4 py-3">205-002-02</td>
                        <td class="px-4 py-3">
                          <span class="badge rounded-pill bg-success">In Progress</span>
                        </td>
                        <td class="px-4 py-3">
                          <button class="btn btn-sm btn-primary">
                            <i class="fas fa-eye me-1"></i>View
                          </button>
                        </td>
                      </tr>
                      <tr>
                        <td class="px-4 py-3"><strong>3</strong></td>
                        <td class="px-4 py-3">2025-01-03</td>
                        <td class="px-4 py-3">Robert Johnson</td>
                        <td class="px-4 py-3">
                          <span class="badge rounded-pill bg-secondary">Verification</span>
                        </td>
                        <td class="px-4 py-3">205-003-03</td>
                        <td class="px-4 py-3">
                          <span class="badge rounded-pill bg-primary">Approved</span>
                        </td>
                        <td class="px-4 py-3">
                          <button class="btn btn-sm btn-primary">
                            <i class="fas fa-eye me-1"></i>View
                          </button>
                        </td>
                      </tr>
                      <tr>
                        <td class="px-4 py-3"><strong>4</strong></td>
                        <td class="px-4 py-3">2025-01-04</td>
                        <td class="px-4 py-3">Maria Garcia</td>
                        <td class="px-4 py-3">
                          <span class="badge rounded-pill bg-warning text-dark">Application</span>
                        </td>
                        <td class="px-4 py-3">205-004-04</td>
                        <td class="px-4 py-3">
                          <span class="badge rounded-pill bg-danger">Denied</span>
                        </td>
                        <td class="px-4 py-3">
                          <button class="btn btn-sm btn-primary">
                            <i class="fas fa-eye me-1"></i>View
                          </button>
                        </td>
                      </tr>
                      <tr>
                        <td class="px-4 py-3"><strong>5</strong></td>
                        <td class="px-4 py-3">2025-01-05</td>
                        <td class="px-4 py-3">David Wilson</td>
                        <td class="px-4 py-3">
                          <span class="badge rounded-pill bg-success">General Concern</span>
                        </td>
                        <td class="px-4 py-3">205-005-05</td>
                        <td class="px-4 py-3">
                          <span class="badge rounded-pill bg-warning text-dark">Pending</span>
                        </td>
                        <td class="px-4 py-3">
                          <button class="btn btn-sm btn-primary">
                            <i class="fas fa-eye me-1"></i>View
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <div class="d-lg-none">
                <div style="max-height: 70vh; overflow-y: auto;" class="p-3">
                  <div class="card mb-3 border-start border-info border-4">
                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="card-title mb-0 fw-bold">Report ID: 1</h6>
                        <span class="badge rounded-pill bg-warning text-dark">Pending</span>
                      </div>
                      <div class="row g-2 small">
                        <div class="col-6"><strong>Date:</strong> 2025-01-01</div>
                        <div class="col-6"><strong>Contact:</strong> 205-001-01</div>
                        <div class="col-12"><strong>Name:</strong> John Doe</div>
                        <div class="col-12">
                          <strong>Purpose:</strong>
                          <span class="badge rounded-pill bg-info ms-1">For Job</span>
                        </div>
                        <div class="col-12 mt-2">
                          <button class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-eye me-1"></i>View Details
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="card mb-3 border-start border-success border-4">
                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="card-title mb-0 fw-bold">Report ID: 2</h6>
                        <span class="badge rounded-pill bg-success">In Progress</span>
                      </div>
                      <div class="row g-2 small">
                        <div class="col-6"><strong>Date:</strong> 2025-01-02</div>
                        <div class="col-6"><strong>Contact:</strong> 205-002-02</div>
                        <div class="col-12"><strong>Name:</strong> Jane Smith</div>
                        <div class="col-12">
                          <strong>Purpose:</strong>
                          <span class="badge rounded-pill bg-danger ms-1">Noise Complaint</span>
                        </div>
                        <div class="col-12 mt-2">
                          <button class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-eye me-1"></i>View Details
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="card mb-3 border-start border-primary border-4">
                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="card-title mb-0 fw-bold">Report ID: 3</h6>
                        <span class="badge rounded-pill bg-primary">Approved</span>
                      </div>
                      <div class="row g-2 small">
                        <div class="col-6"><strong>Date:</strong> 2025-01-03</div>
                        <div class="col-6"><strong>Contact:</strong> 205-003-03</div>
                        <div class="col-12"><strong>Name:</strong> Robert Johnson</div>
                        <div class="col-12">
                          <strong>Purpose:</strong>
                          <span class="badge rounded-pill bg-secondary ms-1">Verification</span>
                        </div>
                        <div class="col-12 mt-2">
                          <button class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-eye me-1"></i>View Details
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="card mb-3 border-start border-danger border-4">
                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="card-title mb-0 fw-bold">Report ID: 4</h6>
                        <span class="badge rounded-pill bg-danger">Denied</span>
                      </div>
                      <div class="row g-2 small">
                        <div class="col-6"><strong>Date:</strong> 2025-01-04</div>
                        <div class="col-6"><strong>Contact:</strong> 205-004-04</div>
                        <div class="col-12"><strong>Name:</strong> Maria Garcia</div>
                        <div class="col-12">
                          <strong>Purpose:</strong>
                          <span class="badge rounded-pill bg-warning text-dark ms-1">Application</span>
                        </div>
                        <div class="col-12 mt-2">
                          <button class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-eye me-1"></i>View Details
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="card mb-3 border-start border-warning border-4">
                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="card-title mb-0 fw-bold">Report ID: 5</h6>
                        <span class="badge rounded-pill bg-warning text-dark">Pending</span>
                      </div>
                      <div class="row g-2 small">
                        <div class="col-6"><strong>Date:</strong> 2025-01-05</div>
                        <div class="col-6"><strong>Contact:</strong> 205-005-05</div>
                        <div class="col-12"><strong>Name:</strong> David Wilson</div>
                        <div class="col-12">
                          <strong>Purpose:</strong>
                          <span class="badge rounded-pill bg-success ms-1">General Concern</span>
                        </div>
                        <div class="col-12 mt-2">
                          <button class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-eye me-1"></i>View Details
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="card-footer bg-light">
              <nav class="d-flex justify-content-center">
                <ul class="pagination pagination-sm mb-0">
                  <li class="page-item disabled">
                    <a class="page-link"><i class="fas fa-chevron-left"></i></a>
                  </li>
                  <li class="page-item active">
                    <a class="page-link" href="#">1</a>
                  </li>
                  <li class="page-item">
                    <a class="page-link" href="#">2</a>
                  </li>
                  <li class="page-item">
                    <a class="page-link" href="#">3</a>
                  </li>
                  <li class="page-item">
                    <a class="page-link"><i class="fas fa-chevron-right"></i></a>
                  </li>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
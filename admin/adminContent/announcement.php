<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcement Details</title>
<link rel="icon" href="../assets/images/logoSanAntonio.png">    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.6/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
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
</head>

<body>
    <div class="container-fluid p-3 p-md-4">
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-body p-0">
                <div class="text-white p-4 rounded-top" style="background-color: rgb(49, 175, 171);">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-bullhorn me-3 fs-4"></i>
                        <h1 class="h4 mb-0 fw-semibold">Announcement Details</h1>
                    </div>
                </div>

                <div class="p-3 p-md-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-lg-6 col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="fas fa-search text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0"
                                            placeholder="Search Announcement" id="searchAnnouncement">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="d-flex justify-content-md-end">
                                        <button class="btn btn-primary bg-gradient" id="addAnnouncementBtn"
                                            style="background-color: rgb(49, 175, 171); border: none;">
                                            <i class="fas fa-plus me-2"></i>
                                            Add Announcement
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-body p-0">
                            <div class="d-none d-lg-block">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 border">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="px-4 py-3 fw-semibold">Announcement ID</th>
                                                <th class="px-4 py-3 fw-semibold">Title</th>
                                                <th class="px-4 py-3 fw-semibold">Date Posted</th>
                                                <th class="px-4 py-3 fw-semibold">Content</th>
                                                <th class="px-4 py-3 fw-semibold">Image</th>
                                                <th class="px-4 py-3 fw-semibold">Action</th>
                                                <th class="px-4 py-3 fw-semibold">Important</th>
                                            </tr>
                                        </thead>
                                        <tbody id="announcementTableBody">
                                            <tr>
                                                <td class="px-4 py-3 fw-medium">1</td>
                                                <td class="px-4 py-3 fw-medium">Vaccine</td>
                                                <td class="px-4 py-3">2025-01-01</td>
                                                <td class="px-4 py-3">
                                                    <span class="d-inline-block text-truncate"
                                                        style="max-width: 180px;">Lorem ipsum dolor sit amet,
                                                        consectetur adipiscing elit...</span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <button class="btn btn-sm btn-success">
                                                        <i class="fas fa-upload me-1"></i>Upload
                                                    </button>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <button class="btn btn-warning btn-sm me-1">
                                                        <i class="fas fa-edit me-1"></i>Edit
                                                    </button>
                                                    <button class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash me-1"></i>Delete
                                                    </button>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="importantAnnouncement">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="bg-light">
                                                <td class="px-4 py-3 fw-medium">2</td>
                                                <td class="px-4 py-3 fw-medium">Community Meeting</td>
                                                <td class="px-4 py-3">2025-01-02</td>
                                                <td class="px-4 py-3">
                                                    <span class="d-inline-block text-truncate"
                                                        style="max-width: 180px;">Important community meeting scheduled
                                                        for next week...</span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <button class="btn btn-sm btn-success">
                                                        <i class="fas fa-upload me-1"></i>Upload
                                                    </button>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <button class="btn btn-warning btn-sm me-1">
                                                        <i class="fas fa-edit me-1"></i>Edit
                                                    </button>
                                                    <button class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash me-1"></i>Delete
                                                    </button>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="importantAnnouncement" checked>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-3 fw-medium">3</td>
                                                <td class="px-4 py-3 fw-medium">Health Program</td>
                                                <td class="px-4 py-3">2025-01-03</td>
                                                <td class="px-4 py-3">
                                                    <span class="d-inline-block text-truncate"
                                                        style="max-width: 180px;">Free health checkup program for all
                                                        residents...</span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <button class="btn btn-sm btn-success">
                                                        <i class="fas fa-upload me-1"></i>Upload
                                                    </button>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <button class="btn btn-warning btn-sm me-1">
                                                        <i class="fas fa-edit me-1"></i>Edit
                                                    </button>
                                                    <button class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash me-1"></i>Delete
                                                    </button>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="importantAnnouncement">
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="d-lg-none">
                                <div style="max-height: 70vh; overflow-y: auto;" class="p-3">
                                    <div class="card mb-3 border-start border-4">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0 fw-bold">ID: 1</h6>
                                                <span class="badge rounded-pill bg-info">Vaccine</span>
                                            </div>
                                            <div class="row g-2 small">
                                                <div class="col-6"><strong>Date:</strong> 2025-01-01</div>
                                                <div class="col-6 text-end">
                                                    <span class="important-badge">Not Important</span>
                                                </div>
                                                <div class="col-12">
                                                    <strong>Content:</strong>
                                                    <p class="mb-1">Lorem ipsum dolor sit amet, consectetur adipiscing
                                                        elit...</p>
                                                </div>
                                                <div class="col-12 mt-2">
                                                    <div class="d-flex justify-content-between">
                                                        <button class="btn btn-sm btn-success">
                                                            <i class="fas fa-upload me-1"></i>Upload
                                                        </button>
                                                        <button class="btn btn-sm btn-warning mx-1">
                                                            <i class="fas fa-edit me-1"></i>Edit
                                                        </button>
                                                        <button class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash me-1"></i>Delete
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card mb-3 border-start border-warning border-4">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0 fw-bold">ID: 2</h6>
                                                <span class="badge rounded-pill bg-primary">Community Meeting</span>
                                            </div>
                                            <div class="row g-2 small">
                                                <div class="col-6"><strong>Date:</strong> 2025-01-02</div>
                                                <div class="col-6 text-end">
                                                    <span class="important-badge bg-warning text-dark">Important</span>
                                                </div>
                                                <div class="col-12">
                                                    <strong>Content:</strong>
                                                    <p class="mb-1">Important community meeting scheduled for next
                                                        week...</p>
                                                </div>
                                                <div class="col-12 mt-2">
                                                    <div class="d-flex justify-content-between">
                                                        <button class="btn btn-sm btn-success">
                                                            <i class="fas fa-upload me-1"></i>Upload
                                                        </button>
                                                        <button class="btn btn-sm btn-warning mx-1">
                                                            <i class="fas fa-edit me-1"></i>Edit
                                                        </button>
                                                        <button class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash me-1"></i>Delete
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card mb-3 border-start border-4">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0 fw-bold">ID: 3</h6>
                                                <span class="badge rounded-pill bg-success">Health Program</span>
                                            </div>
                                            <div class="row g-2 small">
                                                <div class="col-6"><strong>Date:</strong> 2025-01-03</div>
                                                <div class="col-6 text-end">
                                                    <span class="important-badge">Not Important</span>
                                                </div>
                                                <div class="col-12">
                                                    <strong>Content:</strong>
                                                    <p class="mb-1">Free health checkup program for all residents...</p>
                                                </div>
                                                <div class="col-12 mt-2">
                                                    <div class="d-flex justify-content-between">
                                                        <button class="btn btn-sm btn-success">
                                                            <i class="fas fa-upload me-1"></i>Upload
                                                        </button>
                                                        <button class="btn btn-sm btn-warning mx-1">
                                                            <i class="fas fa-edit me-1"></i>Edit
                                                        </button>
                                                        <button class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash me-1"></i>Delete
                                                        </button>
                                                    </div>
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

    <div class="modal fade" id="announcementModal" tabindex="-1" aria-labelledby="announcementModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 rounded-3 overflow-hidden">
                <div class="modal-header bg-primary text-white border-0 p-3">
                    <h5 class="modal-title fw-semibold fs-5" id="announcementModalLabel">
                        <i class="fas fa-bullhorn me-2"></i>
                        <span id="modalAction">Add</span> Announcement
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="announcementForm">
                        <input type="hidden" id="announcementId">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="announcementTitle" class="form-label fw-semibold">Title</label>
                                <input type="text" class="form-control" id="announcementTitle" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="announcementDate" class="form-label fw-semibold">Date</label>
                                <input type="date" class="form-control" id="announcementDate" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Important Announcement</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="importantCheck">
                                    <label class="form-check-label" for="importantCheck">
                                        Mark as important
                                    </label>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="announcementContent" class="form-label fw-semibold">Content</label>
                                <textarea class="form-control" id="announcementContent" rows="5" required></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="announcementImage" class="form-label fw-semibold">Image</label>
                                <input class="form-control" type="file" id="announcementImage" accept="image/*">
                                <small class="text-muted">Upload an image for this announcement (optional)</small>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-primary bg-gradient" id="saveAnnouncementBtn">
                        <i class="fas fa-save me-1"></i>Save Announcement
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.6/js/bootstrap.bundle.min.js"></script>
</body>

</html>
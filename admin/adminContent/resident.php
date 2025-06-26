<?php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../index.php");
  exit();
}

$sql = "SELECT 
            users.userID,
            users.username,
            users.email,
            users.phoneNumber,
            users.role,
            userInfo.firstName,
            userInfo.middleName,
            userInfo.lastName,
            userInfo.birthDate,
            userInfo.gender,
            userInfo.profilePicture,
            barangays.barangayName,
            cities.cityName
        FROM users
        INNER JOIN userInfo ON users.userInfoID = userInfo.userInfoID
        LEFT JOIN addresses ON userInfo.userInfoID = addresses.userInfoID
        LEFT JOIN barangays ON addresses.barangayID = barangays.barangayID
        LEFT JOIN cities ON addresses.cityID = cities.cityID
        WHERE users.role = 'user'";
$result = executeQuery($sql)
    ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Residents Listing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
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
                        <i class="fas fa-users me-3 fs-4"></i>
                        <h1 class="h4 mb-0 fw-semibold">Barangay Residents Listing</h1>
                    </div>
                </div>

                <div class="p-3 p-md-4">

                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-lg-4 col-md-6">
                                    <label class="form-label fw-bold">Search User</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="fas fa-search text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0"
                                            placeholder="Enter name or details...">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <label class="form-label fw-bold">Sort By</label>
                                    <select class="form-select">
                                        <option>Select sorting option</option>
                                        <option>Last Name</option>
                                        <option>First Name</option>
                                        <option>Birth Date</option>
                                        <option>Gender</option>
                                    </select>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <label class="form-label fw-bold">Order By</label>
                                    <select class="form-select">
                                        <option>Select order</option>
                                        <option>Ascending</option>
                                        <option>Descending</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-body p-0">
                            <div class="d-none d-lg-block">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-dark">
                                            <tr>
                                                <th class="px-4 py-3 text-center fw-semibold">Last Name</th>
                                                <th class="px-4 py-3 text-center fw-semibold">First Name</th>
                                                <th class="px-4 py-3 text-center fw-semibold">Middle Name</th>
                                                <th class="px-4 py-3 text-center fw-semibold">Birth Date</th>
                                                <th class="px-4 py-3 text-center fw-semibold">Gender</th>
                                                <th class="px-4 py-3 text-center fw-semibold">Address</th>
                                                <th class="px-4 py-3 text-center fw-semibold">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (mysqli_num_rows($result) > 0) {
                                                while ($resultRow = mysqli_fetch_assoc($result)) {
                                                    ?>
                                                    <tr>
                                                        <td class="px-4 py-3 text-center fw-bold">
                                                            <?php echo $resultRow['lastName']; ?>
                                                        </td>
                                                        <td class="px-4 py-3 text-center fw-bold">
                                                            <?php echo $resultRow['firstName']; ?>
                                                        </td>
                                                        <td class="px-4 py-3 text-center fw-bold">
                                                            <?php echo $resultRow['middleName']; ?>
                                                        </td>
                                                        <td class="px-4 py-3 text-center fw-bold">
                                                            <?php echo $resultRow['birthDate']; ?>
                                                        </td>
                                                        <td class="px-4 py-3 text-center fw-bold">
                                                            <?php echo $resultRow['gender']; ?>
                                                        </td>
                                                        <td class="px-4 py-3 text-center fw-bold">
                                                            <?php echo $resultRow['barangayName'] . ', ' . $resultRow['cityName'] ?>
                                                        </td>
                                                        <td class="px-4 py-3 text-center">
                                                            <button class="btn btn-warning btn-sm me-1">
                                                                <i class="fas fa-edit me-1"></i>Edit
                                                            </button>
                                                            <button class="btn btn-success btn-sm">
                                                                <i class="fas fa-eye me-1"></i>View
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <?php
                                                }
                                            }
                                            ?>
                                    </table>
                                </div>
                            </div>

                            <!-- <div class="d-lg-none">
                                <div style="max-height: 70vh; overflow-y: auto;" class="p-3">
                                    <div class="card mb-3 border-start border-primary border-4">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0 fw-bold">John Air Doe</h6>
                                                <span class="badge rounded-pill bg-primary">Male</span>
                                            </div>
                                            <div class="row g-2 small">
                                                <div class="col-6"><strong>Birth Date:</strong> 2025-01-01</div>
                                                <div class="col-6"><strong>Gender:</strong> Male</div>
                                                <div class="col-12"><strong>Address:</strong> Mamatid Cab.</div>
                                                <div class="col-12 mt-2">
                                                    <div class="d-flex gap-2">
                                                        <button class="btn btn-sm btn-warning flex-fill">
                                                            <i class="fas fa-edit me-1"></i>Edit
                                                        </button>
                                                        <button class="btn btn-sm btn-success flex-fill">
                                                            <i class="fas fa-eye me-1"></i>View
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card mb-3 border-start border-danger border-4">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0 fw-bold">Jane Water Smith</h6>
                                                <span class="badge rounded-pill bg-danger">Female</span>
                                            </div>
                                            <div class="row g-2 small">
                                                <div class="col-6"><strong>Birth Date:</strong> 1995-05-15</div>
                                                <div class="col-6"><strong>Gender:</strong> Female</div>
                                                <div class="col-12"><strong>Address:</strong> Poblacion</div>
                                                <div class="col-12 mt-2">
                                                    <div class="d-flex gap-2">
                                                        <button class="btn btn-sm btn-warning flex-fill">
                                                            <i class="fas fa-edit me-1"></i>Edit
                                                        </button>
                                                        <button class="btn btn-sm btn-success flex-fill">
                                                            <i class="fas fa-eye me-1"></i>View
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card mb-3 border-start border-danger border-4">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0 fw-bold">Maria Fire Garcia</h6>
                                                <span class="badge rounded-pill bg-danger">Female</span>
                                            </div>
                                            <div class="row g-2 small">
                                                <div class="col-6"><strong>Birth Date:</strong> 1988-12-20</div>
                                                <div class="col-6"><strong>Gender:</strong> Female</div>
                                                <div class="col-12"><strong>Address:</strong> San Isidro</div>
                                                <div class="col-12 mt-2">
                                                    <div class="d-flex gap-2">
                                                        <button class="btn btn-sm btn-warning flex-fill">
                                                            <i class="fas fa-edit me-1"></i>Edit
                                                        </button>
                                                        <button class="btn btn-sm btn-success flex-fill">
                                                            <i class="fas fa-eye me-1"></i>View
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                        </div>

                        <div class="card-footer bg-light">
                            <div class="row align-items-center">
                                <div class="col-12 col-md-6">
                                    <div class="text-center text-md-start">
                                        <small class="text-muted">
                                            Showing <?= mysqli_num_rows($result) ?>
                                            resident<?= mysqli_num_rows($result) !== 1 ? 's' : '' ?>
                                        </small>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <nav class="d-flex justify-content-center justify-content-md-end">
                                        <ul class="pagination pagination-sm mb-0">
                                            <li class="page-item disabled">
                                                <a class="page-link" href="#" aria-label="Previous">
                                                    <span aria-hidden="true">&laquo;</span>
                                                </a>
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
                                                <a class="page-link" href="#" aria-label="Next">
                                                    <span aria-hidden="true">&raquo;</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
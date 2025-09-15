<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../sharedAssets/connect.php";

// ===================== AUTH CHECK =====================
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../resident.php");
    exit();
}

// ===================== NOTIFICATIONS =====================
$modalNotif = "";       // for modal errors
$modalNotifType = "";   // success | danger
$showModal = false;     // 🔥 flag to reopen modal if needed

$pageNotif = "";        // for page success messages
$pageNotifType = "";    // success | danger

// ===================== INSERT HANDLER =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ... your insert code unchanged ...
}

// ===================== GET FILTER INPUTS =====================
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sortBy = isset($_GET['sortBy']) ? $_GET['sortBy'] : 'lastName';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// ===================== PAGINATION =====================
$perPage = 10; // rows per page
$currentPage = isset($_GET['p']) ? (int) $_GET['p'] : 1;
if ($currentPage < 1) {
    $currentPage = 1;
}
$offset = ($currentPage - 1) * $perPage;

// ===================== COUNT TOTAL RECORDS =====================
$countSql = "SELECT COUNT(*) as total 
FROM users u
INNER JOIN userInfo ui ON u.userInfoID = ui.userInfoID
LEFT JOIN addresses a ON ui.userInfoID = a.userInfoID
WHERE u.role = 'user'";

$countParams = [];
$countTypes = '';

if ($search !== '') {
    $countSql .= " AND (
        ui.firstName LIKE ? OR
        ui.middleName LIKE ? OR
        ui.lastName LIKE ? OR
        ui.birthDate LIKE ? OR
        ui.gender LIKE ? OR
        CONCAT_WS(' ', a.houseNo, a.streetName, a.phase, a.subdivisionName, a.cityName, a.provinceName) LIKE ?
    )";
    $term = "%$search%";
    $countParams = [$term, $term, $term, $term, $term, $term];
    $countTypes = 'ssssss';
}

$countStmt = $conn->prepare($countSql);
if (!empty($countParams)) {
    $countStmt->bind_param($countTypes, ...$countParams);
}
$countStmt->execute();
$countResult = $countStmt->get_result()->fetch_assoc();
$totalRecords = $countResult['total'];
$totalPages = max(1, ceil($totalRecords / $perPage));

// ===================== BASE SQL =====================
$sql = "SELECT 
    u.userID,
    u.username,
    u.email,
    u.phoneNumber,
    u.role,
    ui.userInfoID,
    ui.firstName,
    ui.middleName,
    ui.lastName,
    ui.gender,
    ui.age,
    ui.birthDate,
    ui.birthPlace,
    ui.residencyType,
    ui.lengthOfStay,
    ui.civilStatus,
    ui.citizenship,
    ui.occupation,
    a.cityName,
    a.provinceName,
    a.barangayName,
    a.streetName,
    a.houseNo,
    a.phase,
    a.subdivisionName,
    a.purok
FROM users u
INNER JOIN userInfo ui ON u.userInfoID = ui.userInfoID
LEFT JOIN addresses a ON ui.userInfoID = a.userInfoID
WHERE u.role = 'user'";

$params = [];
$types = '';

// ===================== ADD SEARCH =====================
if ($search !== '') {
    $sql .= " AND (
        ui.firstName LIKE ? OR
        ui.middleName LIKE ? OR
        ui.lastName LIKE ? OR
        ui.birthDate LIKE ? OR
        ui.gender LIKE ? OR
        CONCAT_WS(' ', a.houseNo, a.streetName, a.phase, a.subdivisionName, a.cityName, a.provinceName) LIKE ?
    )";

    $term = "%$search%";
    $params = array_merge($params, [$term, $term, $term, $term, $term, $term]);
    $types .= 'ssssss';
}

// ===================== ADD SORT + LIMIT =====================
$sql .= " ORDER BY $sortBy $order LIMIT ? OFFSET ?";
$params[] = $perPage;
$params[] = $offset;
$types .= 'ii';

// ===================== PREPARE & EXECUTE =====================
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

?>

<!-- ===================== MODAL ALERT ===================== -->
<?php if (!empty($modalNotif)): ?>
    <div class="alert alert-<?= $modalNotifType ?> mb-3">
        <?= $modalNotif ?>
    </div>
<?php endif; ?>

<!-- ===================== PAGE ALERT ===================== -->
<?php if (!empty($pageNotif)): ?>
    <div class="alert alert-<?= $pageNotifType ?> mb-3">
        <?= $pageNotif ?>
    </div>
<?php endif; ?>

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

    .btn-custom {
        background-color: #31afab;
        color: #fff;
    }

    .btn-custom:hover {
        background-color: #279995;
        color: #fff;
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

                <div class="p-3 p-md-3">
                    <div class="row g-3 mb-4">
                        <!-- Filter Form -->
                        <div class="col-md-10">
                            <form method="GET" action="index.php" class="row g-3">
                                <input type="hidden" name="page" value="resident">

                                <div class="col-md-5">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="fas fa-search text-muted"></i>
                                        </span>
                                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                                            class="form-control border-start-0"
                                            placeholder="Enter name, birthdate, gender, or address">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <select name="sortBy" class="form-select">
                                        <option value="lastName" <?= $sortBy == 'lastName' ? 'selected' : '' ?>>Last Name
                                        </option>
                                        <option value="firstName" <?= $sortBy == 'firstName' ? 'selected' : '' ?>>First
                                            Name</option>
                                        <option value="birthDate" <?= $sortBy == 'birthDate' ? 'selected' : '' ?>>Birth
                                            Date</option>
                                        <option value="gender" <?= $sortBy == 'gender' ? 'selected' : '' ?>>Gender</option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <select name="order" class="form-select">
                                        <option value="ASC" <?= $order == 'ASC' ? 'selected' : '' ?>>Ascending</option>
                                        <option value="DESC" <?= $order == 'DESC' ? 'selected' : '' ?>>Descending</option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-custom w-100">
                                        <i class="fas fa-filter me-2"></i>Filter
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Add Button (outside the form) -->
                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-custom w-100" data-bs-toggle="modal"
                                data-bs-target="#addResidentModal">
                                <i class="fas fa-user-plus me-1"></i> Add
                            </button>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-body p-2">
                            <div class="table-responsive"> <!-- this makes table scrollable on small screens -->
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Last Name</th>
                                            <th>First Name</th>
                                            <th>Middle Name</th>
                                            <th>Birth Date</th>
                                            <th>Gender</th>
                                            <th>Address</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (mysqli_num_rows($result) > 0): ?>
                                            <?php while ($resultRow = mysqli_fetch_assoc($result)): ?>
                                                <tr>
                                                    <td>
                                                        <?= htmlspecialchars($resultRow['lastName']); ?>
                                                    </td>
                                                    <td>
                                                        <?= htmlspecialchars($resultRow['firstName']); ?>
                                                    </td>
                                                    <td>
                                                        <?= htmlspecialchars($resultRow['middleName']); ?>
                                                    </td>
                                                    <td>
                                                        <?= htmlspecialchars($resultRow['birthDate']); ?>
                                                    </td>
                                                    <td>
                                                        <?= htmlspecialchars($resultRow['gender']); ?>
                                                    </td>
                                                    <td>
                                                        <?= htmlspecialchars($resultRow['cityName'] . ', ' . $resultRow['provinceName']); ?>
                                                    </td>
                                                    <td>
                                                        <a href="adminContent/viewResident.php?userID=<?= $resultRow['userID'] ?: 0 ?>"
                                                            class="btn btn-sm btn-success">
                                                            <i class="fas fa-eye me-1"></i>
                                                        </a>
                                                        <button class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash me-1"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="modal fade" id="addResidentModal" tabindex="-1"
                            aria-labelledby="addResidentModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content shadow-lg">
                                    <div class="modal-header text-white" style="background-color: rgb(49, 175, 171);">
                                        <h5 class="modal-title fw-bold" id="addResidentModalLabel">
                                            <i class="fas fa-user-plus me-2"></i> Add New Resident
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <?php if (!empty($modalNotif)): ?>
                                            <div class="alert alert-<?= $modalNotifType ?> mb-3">
                                                <?= $modalNotif ?>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Form to add resident -->
                                        <form id="addResidentForm" method="POST" action="">
                                            <input type="hidden" name="addResident" value="1">
                                            <div class="row g-3">
                                                <!-- Personal Information -->
                                                <div class="col-md-4">
                                                    <label class="form-label">First Name</label>
                                                    <input type="text" class="form-control" name="firstName"
                                                        value="<?= isset($_POST['firstName']) ? htmlspecialchars($_POST['firstName']) : '' ?>"
                                                        required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Middle Name</label>
                                                    <input type="text" class="form-control" name="middleName"
                                                        value="<?= isset($_POST['middleName']) ? htmlspecialchars($_POST['middleName']) : '' ?>">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Last Name</label>
                                                    <input type="text" class="form-control" name="lastName"
                                                        value="<?= isset($_POST['lastName']) ? htmlspecialchars($_POST['lastName']) : '' ?>"
                                                        required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Gender</label>
                                                    <select class="form-select" name="gender" required>
                                                        <option value="" disabled <?= !isset($_POST['gender']) ? 'selected' : '' ?>>Select Gender</option>
                                                        <option value="Male" <?= (isset($_POST['gender']) && $_POST['gender'] === 'Male') ? 'selected' : '' ?>>Male
                                                        </option>
                                                        <option value="Female" <?= (isset($_POST['gender']) && $_POST['gender'] === 'Female') ? 'selected' : '' ?>>Female
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Phone Number</label>
                                                    <input type="tel" class="form-control" name="phoneNumber"
                                                        value="<?= isset($_POST['phoneNumber']) ? htmlspecialchars($_POST['phoneNumber']) : '' ?>">
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label">Blood Type</label>
                                                    <select class="form-select" name="bloodType">
                                                        <option value="" disabled <?= !isset($_POST['bloodType']) ? 'selected' : '' ?>>Select Blood Type</option>
                                                        <?php foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bt): ?>
                                                            <option value="<?= $bt ?>" <?= (isset($_POST['bloodType']) && $_POST['bloodType'] === $bt) ? 'selected' : '' ?>><?= $bt ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Birth Place</label>
                                                    <input type="text" class="form-control" name="birthPlace"
                                                        value="<?= isset($_POST['birthPlace']) ? htmlspecialchars($_POST['birthPlace']) : '' ?>">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Birth Date</label>
                                                    <input type="date" id="birthDate" class="form-control"
                                                        name="birthDate"
                                                        value="<?= isset($_POST['birthDate']) ? htmlspecialchars($_POST['birthDate']) : '' ?>"
                                                        required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Age</label>
                                                    <input type="number" id="age" class="form-control" name="age"
                                                        value="<?= isset($_POST['age']) ? htmlspecialchars($_POST['age']) : '' ?>"
                                                        required>
                                                </div>

                                                <!-- Address Section -->
                                                <div class="col-12 mt-3">
                                                    <h6 class="fw-bold text-uppercase border-bottom pb-2">Address</h6>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Block & Lot | House no.</label>
                                                    <input type="text" class="form-control" id="houseNo" name="houseNo"
                                                        value="<?= isset($_POST['houseNo']) ? htmlspecialchars($_POST['houseNo']) : '' ?>"
                                                        required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Street</label>
                                                    <input type="text" class="form-control" id="streetName"
                                                        name="streetName"
                                                        value="<?= isset($_POST['streetName']) ? htmlspecialchars($_POST['streetName']) : '' ?>">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Phase</label>
                                                    <input type="text" class="form-control" id="phase" name="phase"
                                                        value="<?= isset($_POST['phase']) ? htmlspecialchars($_POST['phase']) : '' ?>">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Subdivision</label>
                                                    <input type="text" class="form-control" id="subdivisionName"
                                                        name="subdivisionName"
                                                        value="<?= isset($_POST['subdivisionName']) ? htmlspecialchars($_POST['subdivisionName']) : '' ?>">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Barangay</label>
                                                    <input type="text" class="form-control" id="barangayName"
                                                        name="barangayName"
                                                        value="<?= isset($_POST['barangayName']) ? htmlspecialchars($_POST['barangayName']) : 'San Antonio' ?>"
                                                        required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">City</label>
                                                    <input type="text" class="form-control" id="cityName"
                                                        name="cityName"
                                                        value="<?= isset($_POST['cityName']) ? htmlspecialchars($_POST['cityName']) : 'Sto. Tomas' ?>"
                                                        required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Province</label>
                                                    <input type="text" class="form-control" id="provinceName"
                                                        name="provinceName"
                                                        value="<?= isset($_POST['provinceName']) ? htmlspecialchars($_POST['provinceName']) : 'Batangas' ?>"
                                                        required>
                                                </div>

                                                <!-- Permanent Address Section -->
                                                <div class="col-12 mt-3">
                                                    <h6 class="fw-bold text-uppercase border-bottom pb-2">Permanent
                                                        Address</h6>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Block & Lot | House no.</label>
                                                    <input type="text" class="form-control" id="houseNoPermanent"
                                                        name="houseNoPermanent"
                                                        value="<?= isset($_POST['houseNo']) ? htmlspecialchars($_POST['houseNo']) : '' ?>"
                                                        required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Street</label>
                                                    <input type="text" class="form-control" id="streetNamePermanent"
                                                        name="streetNamePermanent"
                                                        value="<?= isset($_POST['streetName']) ? htmlspecialchars($_POST['streetName']) : '' ?>">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Phase</label>
                                                    <input type="text" class="form-control" id="phasePermanent"
                                                        name="phasePermanent"
                                                        value="<?= isset($_POST['phase']) ? htmlspecialchars($_POST['phase']) : '' ?>">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Subdivision</label>
                                                    <input type="text" class="form-control"
                                                        id="subdivisionNamePermanent" name="subdivisionNamePermanent"
                                                        value="<?= isset($_POST['subdivisionName']) ? htmlspecialchars($_POST['subdivisionName']) : '' ?>">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Barangay</label>
                                                    <input type="text" class="form-control" id="barangayNamePermanent"
                                                        name="barangayNamePermanent"
                                                        value="<?= isset($_POST['barangayName']) ? htmlspecialchars($_POST['barangayName']) : 'San Antonio' ?>"
                                                        required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">City</label>
                                                    <input type="text" class="form-control" id="cityNamePermanent"
                                                        name="cityNamePermanent"
                                                        value="<?= isset($_POST['cityName']) ? htmlspecialchars($_POST['cityName']) : 'Sto. Tomas' ?>"
                                                        required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Province</label>
                                                    <input type="text" class="form-control" id="provinceNamePermanent"
                                                        name="provinceNamePermanent"
                                                        value="<?= isset($_POST['provinceName']) ? htmlspecialchars($_POST['provinceName']) : 'Batangas' ?>"
                                                        required>
                                                </div>

                                                <!-- Permanent addresss checkbox -->
                                                <div class="col-12 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="sameAsCurrent">
                                                        <label class="form-check-label" for="sameAsCurrent">
                                                            Permanent address is same as current address
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Other Details -->
                                                <div class="col-12 mt-3">
                                                    <h6 class="fw-bold text-uppercase border-bottom pb-2">Other Details
                                                    </h6>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Residency Type</label>
                                                    <select class="form-select" name="residencyType" required>
                                                        <option value="" disabled <?= !isset($_POST['residencyType']) ? 'selected' : '' ?>>Select Type</option>
                                                        <?php foreach (['Bonafide', 'Migrant', 'Transient', 'Foreign'] as $rt): ?>
                                                            <option value="<?= $rt ?>" <?= (isset($_POST['residencyType']) && $_POST['residencyType'] === $rt) ? 'selected' : '' ?>>
                                                                <?= $rt ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Citizenship</label>
                                                    <input type="text" class="form-control" name="citizenship"
                                                        value="<?= isset($_POST['citizenship']) ? htmlspecialchars($_POST['citizenship']) : 'Filipino' ?>"
                                                        required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Civil Status</label>
                                                    <select class="form-select" name="civilStatus" required>
                                                        <option value="" disabled <?= !isset($_POST['civilStatus']) ? 'selected' : '' ?>>Select Status</option>
                                                        <?php foreach (['Single', 'Married', 'Widowed', 'Separated'] as $cs): ?>
                                                            <option value="<?= $cs ?>" <?= (isset($_POST['civilStatus']) && $_POST['civilStatus'] === $cs) ? 'selected' : '' ?>><?= $cs ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Occupation</label>
                                                    <input type="text" class="form-control" name="occupation"
                                                        value="<?= isset($_POST['occupation']) ? htmlspecialchars($_POST['occupation']) : '' ?>"
                                                        required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Landlord Name</label>
                                                    <input type="text" class="form-control" name="landlordName"
                                                        value="<?= isset($_POST['landlordName']) ? htmlspecialchars($_POST['landlordName']) : '' ?>">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Company Name</label>
                                                    <input type="text" class="form-control" name="companyName"
                                                        value="<?= isset($_POST['companyName']) ? htmlspecialchars($_POST['companyName']) : '' ?>">
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Contact Person</label>
                                                    <input type="text" class="form-control" name="contactPerson"
                                                        value="<?= isset($_POST['contactPerson']) ? htmlspecialchars($_POST['contactPerson']) : '' ?>"
                                                        required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Contact Number</label>
                                                    <input type="tel" class="form-control" name="contactNumber"
                                                        value="<?= isset($_POST['contactNumber']) ? htmlspecialchars($_POST['contactNumber']) : '' ?>"
                                                        required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Years of Stay</label>
                                                    <input type="number" class="form-control" name="lengthOfStay"
                                                        value="<?= isset($_POST['lengthOfStay']) ? htmlspecialchars($_POST['lengthOfStay']) : '' ?>"
                                                        required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label d-block">Voter's List</label>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="voterList"
                                                            id="voterYes" value="Yes" <?= (isset($_POST['voterList']) && $_POST['voterList'] === 'Yes') ? 'checked' : '' ?> required>
                                                        <label class="form-check-label" for="voterYes">Yes</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="voterList"
                                                            id="voterNo" value="No" <?= (isset($_POST['voterList']) && $_POST['voterList'] === 'No') ? 'checked' : '' ?> required>
                                                        <label class="form-check-label" for="voterNo">No</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" class="form-control" name="email"
                                                        value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                                                        required>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" form="addResidentForm" class="btn btn-custom">Save
                                            Resident</button>
                                    </div>
                                </div>
                            </div>
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
                                            <?php
                                            // Preserve filters
                                            $queryBase = "page=resident&search=" . urlencode($search) . "&sortBy=" . urlencode($sortBy) . "&order=" . urlencode($order);
                                            ?>

                                            <!-- Previous -->
                                            <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                                                <a class="page-link"
                                                    href="?<?= $queryBase ?>&p=<?= max(1, $currentPage - 1) ?>"
                                                    aria-label="Previous">
                                                    <span aria-hidden="true">&laquo;</span>
                                                </a>
                                            </li>

                                            <!-- Numbered pages -->
                                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                                <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                                                    <a class="page-link" href="?<?= $queryBase ?>&p=<?= $i ?>"><?= $i ?></a>
                                                </li>
                                            <?php endfor; ?>

                                            <!-- Next -->
                                            <li
                                                class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                                                <a class="page-link"
                                                    href="?<?= $queryBase ?>&p=<?= min($totalPages, $currentPage + 1) ?>"
                                                    aria-label="Next">
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
    <?php if ($showModal): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                let myModalEl = document.getElementById("addResidentModal");
                let myModal = new bootstrap.Modal(myModalEl);
                myModal.show();

                <?php if ($modalNotifType === "success"): ?>
                    // Auto-close modal on success after 3 seconds
                    setTimeout(() => {
                        myModal.hide();
                        // Optional: clear form
                        document.getElementById("addResidentForm").reset();
                    }, 3000);
                <?php endif; ?>
            });
        </script>
    <?php endif; ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => alert.remove());
            }, 5000); // removes alerts after 5 seconds
        });
    </script>
    <script>
        const birthDateInput = document.getElementById('birthDate');
        const ageInput = document.getElementById('age');

        function calculateAge(dateString) {
            if (!dateString) return '';
            const today = new Date();
            const birthDate = new Date(dateString);
            let age = today.getFullYear() - birthDate.getFullYear();
            const m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            return age;
        }

        birthDateInput.addEventListener('change', function () {
            ageInput.value = calculateAge(this.value);
        });

        // Optional: Update age as user types manually in birthdate
        birthDateInput.addEventListener('input', function () {
            ageInput.value = calculateAge(this.value);
        });
    </script>
    <script>
        document.getElementById('sameAsCurrent').addEventListener('change', function () {
            const isChecked = this.checked;

            const fields = ['houseNo', 'streetName', 'phase', 'subdivisionName', 'barangayName', 'cityName', 'provinceName'];

            fields.forEach(field => {
                const current = document.getElementById(field);
                const permanent = document.getElementById(field + 'Permanent');

                if (isChecked) {
                    permanent.value = current.value;
                    permanent.readOnly = true; // optional, prevent editing
                } else {
                    permanent.readOnly = false;
                    permanent.value = '';
                }

                // Optional: if you want it to live-update as you type
                if (isChecked) {
                    current.addEventListener('input', () => {
                        permanent.value = current.value;
                    });
                }
            });
        });
    </script>
</body>

</html>
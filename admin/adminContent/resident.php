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

// ===================== HANDLE ADD RESIDENT FORM =====================
$modalNotif = '';
$modalNotifType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addResident'])) {
    // Escape all inputs
    $firstName = mysqli_real_escape_string($conn, $_POST['firstName']);
    $middleName = mysqli_real_escape_string($conn, $_POST['middleName'] ?? '');
    $lastName = mysqli_real_escape_string($conn, $_POST['lastName']);
    $suffix = mysqli_real_escape_string($conn, $_POST['suffix'] ?? '');
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $birthDate = mysqli_real_escape_string($conn, $_POST['birthDate']);
    $age = mysqli_real_escape_string($conn, $_POST['age']);
    $birthPlace = mysqli_real_escape_string($conn, $_POST['birthPlace'] ?? '');
    $bloodType = mysqli_real_escape_string($conn, $_POST['bloodType'] ?? '');
    $civilStatus = mysqli_real_escape_string($conn, $_POST['civilStatus']);
    $citizenship = mysqli_real_escape_string($conn, $_POST['citizenship']);
    $occupation = mysqli_real_escape_string($conn, $_POST['occupation'] ?? '');
    $lengthOfStay = mysqli_real_escape_string($conn, $_POST['lengthOfStay']);
    $contactNumber = mysqli_real_escape_string($conn, $_POST['contactNumber'] ?? '');
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Educational fields
    $educationalLevel = isset($_POST['educationalLevel']) && !empty($_POST['educationalLevel'])
        ? mysqli_real_escape_string($conn, $_POST['educationalLevel'])
        : NULL;

    $shsTrack = NULL;
    $collegeCourse = NULL;

    if ($educationalLevel !== NULL) {
        $levelLower = strtolower($educationalLevel);

        if (strpos($levelLower, 'senior high') !== false) {
            if (isset($_POST['shsTrack']) && $_POST['shsTrack'] === 'Others') {
                $shsTrack = isset($_POST['shsTrackOther']) && !empty($_POST['shsTrackOther'])
                    ? mysqli_real_escape_string($conn, $_POST['shsTrackOther'])
                    : NULL;
            } else {
                $shsTrack = isset($_POST['shsTrack']) && !empty($_POST['shsTrack'])
                    ? mysqli_real_escape_string($conn, $_POST['shsTrack'])
                    : NULL;
            }
        } else if (strpos($levelLower, 'college') !== false) {
            if (isset($_POST['collegeCourse']) && $_POST['collegeCourse'] === 'Others') {
                $collegeCourse = isset($_POST['collegeCourseOther']) && !empty($_POST['collegeCourseOther'])
                    ? mysqli_real_escape_string($conn, $_POST['collegeCourseOther'])
                    : NULL;
            } else {
                $collegeCourse = isset($_POST['collegeCourse']) && !empty($_POST['collegeCourse'])
                    ? mysqli_real_escape_string($conn, $_POST['collegeCourse'])
                    : NULL;
            }
        }
    }

    // Determine residency type
    $currentProvince = strtoupper(mysqli_real_escape_string($conn, $_POST['provinceName']));
    $currentCity = strtoupper(mysqli_real_escape_string($conn, $_POST['cityName']));
    $currentBarangay = strtoupper(mysqli_real_escape_string($conn, $_POST['barangayName']));

    $residencyType = '';
    $isSpecificCurrentAddress =
        $currentProvince === 'BATANGAS' &&
        $currentCity === 'SANTO TOMAS' &&
        $currentBarangay === 'SAN ANTONIO';

    if (strtoupper($citizenship) !== 'FILIPINO') {
        $residencyType = 'Foreign';
    } else if ($isSpecificCurrentAddress && (int) $lengthOfStay === (int) $age) {
        $residencyType = 'Bonafide';
    } else if ($isSpecificCurrentAddress && (int) $lengthOfStay >= 3) {
        $residencyType = 'Migrant';
    } else if ($isSpecificCurrentAddress && (int) $lengthOfStay <= 2) {
        $residencyType = 'Transient';
    }

    // Remarks
    $remarksValue = (isset($_POST['remarks']) && $_POST['remarks'] === 'Yes')
        ? 'With Derogatory Record'
        : 'No Derogatory Record';

    $isVoter = mysqli_real_escape_string($conn, $_POST['isVoter']);

    // Insert user
    $insertUser = "INSERT INTO users (phoneNumber, email, role, isNew, isRestricted) 
                   VALUES ('$contactNumber', '$email', 'user', 'No', 'No')";

    if (mysqli_query($conn, $insertUser)) {
        $userID = mysqli_insert_id($conn);

        // Insert userinfo with educational fields
        $insertResident = "INSERT INTO userinfo (
            userID, firstName, middleName, lastName, suffix, gender, birthDate, age, birthPlace, 
            bloodType, civilStatus, citizenship, occupation, lengthOfStay, residencyType, 
            isVoter, remarks, educationalLevel, shsTrack, collegeCourse
        ) VALUES (
            $userID, '$firstName', '$middleName', '$lastName', '$suffix', '$gender', '$birthDate', 
            '$age', '$birthPlace', '$bloodType', '$civilStatus', '$citizenship', '$occupation', 
            '$lengthOfStay', " . ($residencyType ? "'$residencyType'" : "NULL") . ", 
            '$isVoter', '$remarksValue',
            " . ($educationalLevel !== NULL ? "'$educationalLevel'" : "NULL") . ",
            " . ($shsTrack !== NULL ? "'$shsTrack'" : "NULL") . ",
            " . ($collegeCourse !== NULL ? "'$collegeCourse'" : "NULL") . "
        )";

        if (mysqli_query($conn, $insertResident)) {
            $userInfoID = mysqli_insert_id($conn);

            // Current address
            $blockLotNo = mysqli_real_escape_string($conn, $_POST['blockLotNo'] ?? '');
            $streetName = mysqli_real_escape_string($conn, $_POST['streetName'] ?? '');
            $phase = mysqli_real_escape_string($conn, $_POST['phase'] ?? '');
            $subdivisionName = mysqli_real_escape_string($conn, $_POST['subdivisionName'] ?? '');
            $purok = mysqli_real_escape_string($conn, $_POST['purok']);

            $insertAddress = "INSERT INTO addresses (
                userInfoID, blockLotNo, streetName, phase, subdivisionName, barangayName, cityName, provinceName, purok
            ) VALUES (
                $userInfoID, '$blockLotNo', '$streetName', '$phase', '$subdivisionName', 
                '$currentBarangay', '$currentCity', '$currentProvince', '$purok'
            )";
            mysqli_query($conn, $insertAddress);

            // Permanent address - handle both Filipino and Foreign
            if (strtoupper($citizenship) === 'FILIPINO') {
                $permanentBlockLotNo = mysqli_real_escape_string($conn, $_POST['blockLotNoPermanent'] ?? '');
                $permanentStreetName = mysqli_real_escape_string($conn, $_POST['streetNamePermanent'] ?? '');
                $permanentPhase = mysqli_real_escape_string($conn, $_POST['phasePermanent'] ?? '');
                $permanentSubdivisionName = mysqli_real_escape_string($conn, $_POST['subdivisionNamePermanent'] ?? '');
                $permanentBarangayName = strtoupper(mysqli_real_escape_string($conn, $_POST['barangayNamePermanent']));
                $permanentCityName = strtoupper(mysqli_real_escape_string($conn, $_POST['cityNamePermanent']));
                $permanentProvinceName = strtoupper(mysqli_real_escape_string($conn, $_POST['provinceNamePermanent']));
                $permanentPurok = mysqli_real_escape_string($conn, $_POST['purokPermanent']);

                $insertPermanent = "INSERT INTO permanentAddresses (
                    userInfoID, permanentBlockLotNo, permanentStreetName, permanentPhase, 
                    permanentSubdivisionName, permanentBarangayName, permanentCityName, 
                    permanentProvinceName, permanentPurok, foreignPermanentAddress
                ) VALUES (
                    $userInfoID, '$permanentBlockLotNo', '$permanentStreetName', '$permanentPhase', 
                    '$permanentSubdivisionName', '$permanentBarangayName', '$permanentCityName', 
                    '$permanentProvinceName', '$permanentPurok', NULL
                )";
            } else {
                $foreignPermanentAddress = mysqli_real_escape_string($conn, $_POST['foreignPermanentAddress']);

                $insertPermanent = "INSERT INTO permanentAddresses (
                    userInfoID, permanentBlockLotNo, permanentStreetName, permanentPhase, 
                    permanentSubdivisionName, permanentBarangayName, permanentCityName, 
                    permanentProvinceName, permanentPurok, foreignPermanentAddress
                ) VALUES (
                    $userInfoID, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$foreignPermanentAddress'
                )";
            }
            mysqli_query($conn, $insertPermanent);

            $modalNotif = "Resident successfully added!";
            $modalNotifType = "success";
            $_POST = [];
        } else {
            $modalNotif = "Error adding resident info: " . mysqli_error($conn);
            $modalNotifType = "danger";
        }
    } else {
        $modalNotif = "Error adding user account: " . mysqli_error($conn);
        $modalNotifType = "danger";
    }
}

// ===================== HANDLE RESTRICT USER =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userID'], $_POST['newStatus'])) {
    $userID = $_POST['userID'];
    $status = $_POST['newStatus'];
    $restrictionReason = $_POST['restrictionReason'] ?? NULL;

    if ($status === 'No') {
        $unrestrictUserQuery = "UPDATE users SET isRestricted = '$status', restrictionStart = NULL, restrictionEnd = NULL,
restrictionReason = NULL WHERE userID = '$userID'";
        executeQuery($unrestrictUserQuery);
    } else {
        $restrictUserQuery = "UPDATE users SET isRestricted = '$status', restrictionStart = NOW(), restrictionEnd =
DATE_ADD(NOW(), INTERVAL 7 DAY), restrictionReason = '$restrictionReason' WHERE userID = '$userID'";
        executeQuery($restrictUserQuery);
    }
}

// ===================== GET RESIDENTS =====================
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sortBy = isset($_GET['sortBy']) ? $_GET['sortBy'] : 'lastName';
$order = isset($_GET['order']) && strtoupper($_GET['order']) === 'DESC' ? 'DESC' : 'ASC';
$residencyFilter = isset($_GET['residencyType']) ? $_GET['residencyType'] : '';
$restrictedFilter = isset($_GET['restricted']) ? $_GET['restricted'] : '';

// Pagination
$limit = 20;
$currentPage = isset($_GET['p']) && is_numeric($_GET['p']) ? (int) $_GET['p'] : 1;
$offset = ($currentPage - 1) * $limit;

// Base queries
$sql = "SELECT
u.email,
u.phoneNumber,
ui.userInfoID,
ui.userID,
CONCAT(ui.firstName, ' ', ui.middleName, ' ', ui.lastName) AS fullname,
ui.suffix,
ui.gender,
ui.birthDate,
ui.residencyType,
ui.isVoter,
a.cityName,
a.provinceName,
u.isRestricted
FROM userinfo ui
INNER JOIN users u ON ui.userID = u.userID
LEFT JOIN addresses a ON ui.userInfoID = a.userInfoID
WHERE u.role = 'user'";

$countSql = "SELECT COUNT(*) AS total FROM userinfo ui
INNER JOIN users u ON ui.userID = u.userID
WHERE u.role = 'user'";

// ===================== APPLY SEARCH =====================
if ($search !== '') {
    $term = "%$search%";
    $sql .= " AND (
ui.firstName LIKE '$term' OR
ui.middleName LIKE '$term' OR
ui.lastName LIKE '$term' OR
ui.birthDate LIKE '$term' OR
ui.gender LIKE '$term' OR
CONCAT_WS(' ', a.blockLotNo, a.streetName, a.phase, a.subdivisionName, a.cityName, a.provinceName) LIKE '$term'
)";
    $countSql .= " AND (
ui.firstName LIKE '$term' OR
ui.middleName LIKE '$term' OR
ui.lastName LIKE '$term' OR
ui.birthDate LIKE '$term' OR
ui.gender LIKE '$term'
)";
}

// ===================== APPLY RESTRICTED FILTER =====================
if ($restrictedFilter === 'restricted') {
    $sql .= " AND u.isRestricted = 'Yes'";
    $countSql .= " AND u.isRestricted = 'Yes'";
} elseif ($restrictedFilter === 'not_restricted') {
    $sql .= " AND (u.isRestricted = 'No' OR u.isRestricted IS NULL)";
    $countSql .= " AND (u.isRestricted = 'No' OR u.isRestricted IS NULL)";
}

// ===================== APPLY RESIDENCY FILTER =====================
$residencyTypes = ['Bonafide', 'Migrant', 'Transient', 'Foreign'];
if (in_array($residencyFilter, $residencyTypes)) {
    $sql .= " AND ui.residencyType = '$residencyFilter'";
    $countSql .= " AND ui.residencyType = '$residencyFilter'";
}

// Pagination
$sql .= " ORDER BY $sortBy $order LIMIT $limit OFFSET $offset";

// Execute queries
$countResult = mysqli_query($conn, $countSql);
$totalRows = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalRows / $limit);

$result = mysqli_query($conn, $sql);
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

    .btn-custom {
        background-color: #19AFA5;
        color: #fff;
    }

    .btn-custom:hover {
        background-color: #11A1A1;
        color: #fff;
    }


    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: none;
    }

    .btn-primary {
        background-color: #19AFA5;
        border-color: #19AFA5;
    }

    .btn-primary:hover {
        background-color: #11A1A1;
        border-color: #11A1A1;
    }

    .modal-header {
        background-color: #19AFA5;
        color: white;
    }

    .modal-header .btn-close {
        filter: invert(1);
    }

    .pagination .page-link {
        color: #19AFA5;
        background-color: white;
        border: 1px solid #dee2e6;
        transition: all 0.2s ease-in-out;
    }

    .pagination .page-item.active .page-link {
        background-color: #19AFA5;
        border-color: #19AFA5;
        color: white;
    }

    .pagination .page-link:hover {
        background-color: #e9f8f8;
        color: #31afab;
    }

    .btn-info {
        background-color: #31afab !important;
        border-color: #31afab !important;
    }

    .btn-info:hover {
        background-color: #2a9995 !important;
        border-color: #2a9995 !important;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, .075);
    }

    .badge {
        font-size: 0.75em;
    }

    .bg-custom {
        background-color: #31afab !important;
        color: #fff;
    }

    .viewButton {
        background-color: transparent;
        border-color: #19AFA5;
        color: #19AFA5;
    }

    .viewButton:hover {
        background-color: #19AFA5;
        border-color: #19AFA5;
        color: white;
    }

    .form-control:focus {
        box-shadow: none !important;
        outline: none;
        border: 1px solid #19AFA5;
    }

    .form-select:focus {
        box-shadow: none !important;
        outline: none;
        border: 1px solid #19AFA5;
    }

    .filterButton {
        background-color: #19AFA5;
        border-color: #19AFA5;
        color: white;
    }

    .filterButton:hover {
        background-color: #11A1A1;
        border-color: #11A1A1;
        color: white;
    }
</style>

<body>

    <div class="container-fluid p-3 p-md-4">
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-body p-0">

                <div class="text-white p-4 rounded-top" style="background-color: #19AFA5;">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file-alt me-3 fs-4"></i>
                            <h1 class="h4 mb-0 fw-semibold">Barangay Resident Listing</h1>
                        </div>
                        <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addResidentModal">
                            <i class="fas fa-plus me-2"></i>Add Resident
                        </button>

                    </div>
                </div>

                <div class="p-3 p-md-4">
                    <form method="GET" action="index.php">
                        <input type="hidden" name="page" value="resident">
                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-lg-4 col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-end-0">
                                                <i class="fas fa-search text-muted"></i>
                                            </span>
                                            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                                                class="form-control border-start-0"
                                                placeholder="Enter name, birthdate, gender, or address">
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <select name="residencyType" class="form-select">
                                            <option value="" <?= empty($_GET['residencyType']) ? 'selected' : '' ?>>All
                                                Residents</option>
                                            <option value="Bonafide" <?= isset($_GET['residencyType']) && $_GET['residencyType'] == 'Bonafide' ? 'selected' : '' ?>>Bonafide
                                            </option>
                                            <option value="Migrant" <?= isset($_GET['residencyType']) && $_GET['residencyType'] == 'Migrant' ? 'selected' : '' ?>>Migrant</option>
                                            <option value="Transient" <?= isset($_GET['residencyType']) && $_GET['residencyType'] == 'Transient' ? 'selected' : '' ?>>Transient
                                            </option>
                                            <option value="Foreign" <?= isset($_GET['residencyType']) && $_GET['residencyType'] == 'Foreign' ? 'selected' : '' ?>>Foreign</option>
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <select name="restricted" class="form-select">
                                            <option value="" <?= empty($_GET['restricted']) ? 'selected' : '' ?>>All
                                                Users</option>
                                            <option value="restricted" <?= isset($_GET['restricted']) && $_GET['restricted'] == 'restricted' ? 'selected' : '' ?>>Restricted
                                            </option>
                                            <option value="not_restricted" <?= isset($_GET['restricted']) && $_GET['restricted'] == 'not_restricted' ? 'selected' : '' ?>>Not
                                                Restricted</option>
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <select name="order" class="form-select">
                                            <option value="ASC" <?= $order == 'ASC' ? 'selected' : '' ?>>Ascending</option>
                                            <option value="DESC" <?= $order == 'DESC' ? 'selected' : '' ?>>Descending
                                            </option>
                                        </select>
                                    </div>


                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-custom filterButton w-100">
                                            <i class="fas fa-filter me-2"></i>Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="card shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr class="align-middle">
                                            <th class="align-middle">Resident Name</th>
                                            <th>Email Address</th>
                                            <th>Phone Number</th>
                                            <th>Date of Birth</th>
                                            <th>Gender</th>
                                            <th>Residency Type</th>
                                            <th>isRestricted</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($result->num_rows > 0): ?>
                                            <?php while ($row = $result->fetch_assoc()): ?>
                                                <tr class="align-middle">
                                                    <td><?= htmlspecialchars($row['fullname'] ?? '') ?></td>
                                                    <td><?= htmlspecialchars($row['email']); ?></td>
                                                    <td><?= htmlspecialchars($row['phoneNumber']); ?></td>
                                                    <td><?= !empty($row['birthDate']) ? date('F d, Y', strtotime($row['birthDate'])) : '' ?>
                                                    </td>
                                                    <td><?= htmlspecialchars($row['gender'] ?? ''); ?></td>
                                                    <td><?= htmlspecialchars($row['residencyType'] ?? ''); ?>
                                                    <td><?= htmlspecialchars($row['isRestricted']); ?>
                                                    </td>
                                                    <td>
                                                        <a href="adminContent/viewResident.php?userID=<?= $row['userID'] ?: 0 ?>"
                                                            class="btn btn-sm viewButton">
                                                            <i class="fas fa-eye gap"></i>
                                                        </a>
                                                        <!-- Toggle Restrict Button -->
                                                        <button
                                                            class="btn btn-sm <?= ($row['isRestricted'] === 'Yes') ? 'btn-success' : 'btn-danger' ?>"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#restrictUserModal<?= $row['userID'] ?>">
                                                            <i
                                                                class="fa-solid <?= ($row['isRestricted'] === 'Yes') ? 'fa-circle-check' : 'fa-ban' ?>"></i>
                                                        </button>

                                                        <form method="POST" style="display:inline;">
                                                            <!-- Modal -->
                                                            <div class="modal fade" id="restrictUserModal<?= $row['userID'] ?>"
                                                                tabindex="-1">
                                                                <div class="modal-dialog modal-dialog-centered">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title">
                                                                                <?= ($row['isRestricted'] === 'Yes') ? 'Unrestrict User' : 'Restrict User' ?>
                                                                            </h5>
                                                                            <button type="button" class="btn-close"
                                                                                data-bs-dismiss="modal"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            Are you sure you want to
                                                                            <?= ($row['isRestricted'] === 'Yes') ? 'unrestrict' : 'restrict' ?>
                                                                            this user?

                                                                            <?php if ($row['isRestricted'] !== 'Yes'): ?>
                                                                                <div class="mt-3">
                                                                                    <label for="restrictionReason"
                                                                                        class="form-label">Restriction
                                                                                        Reason</label>
                                                                                    <select name="restrictionReason"
                                                                                        id="restrictionReason" class="form-select"
                                                                                        required>
                                                                                        <option value="" disabled selected>Select
                                                                                            restriction reason</option>
                                                                                        <option value="Multiple invalid attempts">
                                                                                            Multiple invalid attempts</option>
                                                                                        <option
                                                                                            value="Providing invalid or false information">
                                                                                            Providing invalid or false information
                                                                                        </option>
                                                                                        <option
                                                                                            value="Abusive or inappropriate behavior">
                                                                                            Abusive or inappropriate behavior
                                                                                        </option>
                                                                                        <option
                                                                                            value="Suspicious or fraudulent activity">
                                                                                            Suspicious or fraudulent activity
                                                                                        </option>
                                                                                        <option value="Violation of platform rules">
                                                                                            Violation of platform rules</option>
                                                                                    </select>
                                                                                </div>
                                                                            <?php endif; ?>
                                                                        </div>


                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary"
                                                                                data-bs-dismiss="modal">Cancel</button>

                                                                            <input type="hidden" name="userID"
                                                                                value="<?= $row['userID'] ?>">
                                                                            <input type="hidden" name="newStatus"
                                                                                value="<?= ($row['isRestricted'] === 'Yes') ? 'No' : 'Yes' ?>">
                                                                            <button type="submit" class="btn btn-danger">
                                                                                Confirm
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center">No users found</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="modal fade" id="addResidentModal" tabindex="-1"
                            aria-labelledby="addResidentModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content shadow-lg">
                                    <div class="modal-header text-white" style="background-color: rgb(49, 175, 171);">
                                        <h5 class="modal-title fw-bold" id="addResidentModalLabel">
                                            <i class="fas fa-user-plus me-2"></i> Add New Resident
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                                        <?php if (!empty($modalNotif)): ?>
                                            <div class="alert alert-<?= $modalNotifType ?> mb-3">
                                                <?= $modalNotif ?>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Form to add resident -->
                                        <form id="addResidentForm" method="POST" action="">
                                            <input type="hidden" name="addResident" value="1">

                                            <!-- Personal Information -->
                                            <h6 class="fw-bold text-uppercase border-bottom pb-2 mb-3">Personal
                                                Information</h6>
                                            <div class="row g-3 mb-4">
                                                <div class="col-md-3">
                                                    <label class="form-label">First Name <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="firstName" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Middle Name</label>
                                                    <input type="text" class="form-control" name="middleName">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Last Name <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="lastName" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Suffix</label>
                                                    <select class="form-select" name="suffix">
                                                        <option value="">None</option>
                                                        <option value="Jr.">Jr.</option>
                                                        <option value="Sr.">Sr.</option>
                                                        <option value="II">II</option>
                                                        <option value="III">III</option>
                                                        <option value="IV">IV</option>
                                                        <option value="V">V</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Gender <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select" name="gender" required>
                                                        <option value="" disabled selected>Select Gender</option>
                                                        <option value="Male">Male</option>
                                                        <option value="Female">Female</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Birth Date <span
                                                            class="text-danger">*</span></label>
                                                    <input type="date" id="addBirthDate" class="form-control"
                                                        name="birthDate" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Age <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" id="addAge" class="form-control" name="age"
                                                        readonly required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Birth Place</label>
                                                    <input type="text" class="form-control" name="birthPlace">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Blood Type</label>
                                                    <select class="form-select" name="bloodType">
                                                        <option value="" selected>Select</option>
                                                        <option value="A+">A+</option>
                                                        <option value="A-">A-</option>
                                                        <option value="B+">B+</option>
                                                        <option value="B-">B-</option>
                                                        <option value="AB+">AB+</option>
                                                        <option value="AB-">AB-</option>
                                                        <option value="O+">O+</option>
                                                        <option value="O-">O-</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Civil Status <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select" name="civilStatus" required>
                                                        <option value="" disabled selected>Select Status</option>
                                                        <option value="Single">Single</option>
                                                        <option value="Married">Married</option>
                                                        <option value="Widowed">Widowed</option>
                                                        <option value="Separated">Separated</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Citizenship <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select text-uppercase" id="addCitizenship"
                                                        name="citizenship" required>
                                                        <option value="FILIPINO" selected>FILIPINO</option>
                                                        <?php
                                                        $otherCitizenships = ['AMERICAN', 'BRITISH', 'CANADIAN', 'CHINESE', 'JAPANESE', 'KOREAN'];
                                                        foreach ($otherCitizenships as $c):
                                                            ?>
                                                            <option value="<?= $c ?>"><?= $c ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Occupation</label>
                                                    <input type="text" class="form-control" name="occupation">
                                                </div>
                                            </div>

                                            <!-- Educational Information -->
                                            <h6 class="fw-bold text-uppercase border-bottom pb-2 mb-3">Educational
                                                Information</h6>
                                            <div class="row g-3 mb-4">
                                                <div class="col-md-4">
                                                    <label class="form-label">Educational Level</label>
                                                    <select class="form-select" id="addEducationalLevel"
                                                        name="educationalLevel">
                                                        <option value="" selected>Select Level</option>
                                                        <option value="None">None</option>
                                                        <option value="Elementary Undergraduate">Elementary
                                                            Undergraduate</option>
                                                        <option value="Elementary Graduate">Elementary Graduate</option>
                                                        <option value="High School Undergraduate">High School
                                                            Undergraduate</option>
                                                        <option value="High School Graduate">High School Graduate
                                                        </option>
                                                        <option value="Senior High Undergraduate">Senior High
                                                            Undergraduate</option>
                                                        <option value="Senior High Graduate">Senior High Graduate
                                                        </option>
                                                        <option value="College Undergraduate">College Undergraduate
                                                        </option>
                                                        <option value="College Graduate">College Graduate</option>
                                                        <option value="ALS">ALS</option>
                                                        <option value="TESDA">TESDA</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4" id="addShsTrackDiv" style="display:none;">
                                                    <label class="form-label">Senior High Track</label>
                                                    <select class="form-select" id="addShsTrack" name="shsTrack">
                                                        <option value="">Select Track</option>
                                                        <option value="STEM">STEM</option>
                                                        <option value="ABM">ABM</option>
                                                        <option value="HUMMS">HUMMS</option>
                                                        <option value="ICT">ICT</option>
                                                        <option value="GAS">GAS</option>
                                                        <option value="TVL">TVL</option>
                                                        <option value="Others">Others | Specify</option>
                                                    </select>
                                                    <input type="text" class="form-control mt-2 d-none"
                                                        id="addShsTrackOther" name="shsTrackOther"
                                                        placeholder="Specify track">
                                                </div>
                                                <div class="col-md-4" id="addCollegeCourseDiv" style="display:none;">
                                                    <label class="form-label">College Course</label>
                                                    <select class="form-select" id="addCollegeCourse"
                                                        name="collegeCourse">
                                                        <option value="">Select Course</option>
                                                        <option value="BSIT">BSIT</option>
                                                        <option value="BSECE">BSECE</option>
                                                        <option value="BSEE">BSEE</option>
                                                        <option value="BSBA">BSBA</option>
                                                        <option value="BSTM">BSTM</option>
                                                        <option value="BSHRM">BSHRM</option>
                                                        <option value="BSED">BSED</option>
                                                        <option value="BSCE">BSCE</option>
                                                        <option value="BSME">BSME</option>
                                                        <option value="Others">Others | Specify</option>
                                                    </select>
                                                    <input type="text" class="form-control mt-2 d-none"
                                                        id="addCollegeCourseOther" name="collegeCourseOther"
                                                        placeholder="Specify course">
                                                </div>
                                            </div>

                                            <!-- Contact Information -->
                                            <h6 class="fw-bold text-uppercase border-bottom pb-2 mb-3">Contact
                                                Information</h6>
                                            <div class="row g-3 mb-4">
                                                <div class="col-md-4">
                                                    <label class="form-label">Phone Number</label>
                                                    <input type="tel" class="form-control" name="contactNumber"
                                                        pattern="^09\d{9}$" maxlength="11" placeholder="09XXXXXXXXX">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Email <span
                                                            class="text-danger">*</span></label>
                                                    <input type="email" class="form-control" name="email" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Length of Stay (years) <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" id="addLengthOfStay" class="form-control"
                                                        name="lengthOfStay" min="0" required>
                                                </div>
                                            </div>

                                            <!-- Current Address -->
                                            <h6 class="fw-bold text-uppercase border-bottom pb-2 mb-3">Current Address
                                            </h6>
                                            <div class="row g-3 mb-4">
                                                <div class="col-md-3">
                                                    <label class="form-label">Province <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select" id="addProvince" name="provinceName"
                                                        required>
                                                        <option value="">Select Province</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">City <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select" id="addCity" name="cityName" required>
                                                        <option value="">Select City</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Barangay <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select" id="addBarangay" name="barangayName"
                                                        required>
                                                        <option value="">Select Barangay</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">
                                                        Purok <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="form-select" name="purok" required>
                                                        <option value="">Select Purok</option>
                                                        <?php for ($i = 1; $i <= 7; $i++): ?>
                                                            <option value="Purok <?= $i; ?>">Purok
                                                                <?= $i; ?>
                                                            </option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Block & Lot / House No.</label>
                                                    <input type="text" class="form-control" name="blockLotNo">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Street</label>
                                                    <input type="text" class="form-control" name="streetName">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Phase</label>
                                                    <input type="text" class="form-control" name="phase">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Subdivision</label>
                                                    <input type="text" class="form-control" name="subdivisionName">
                                                </div>
                                            </div>

                                            <!-- Permanent Address -->
                                            <h6 class="fw-bold text-uppercase border-bottom pb-2 mb-3">Permanent Address
                                            </h6>
                                            <div class="row g-3 mb-4">
                                                <div class="col-12 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="addSameAsCurrent">
                                                        <label class="form-check-label" for="addSameAsCurrent">
                                                            Same as current address
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Filipino Permanent Address Fields -->
                                                <div id="addFilipinoPermanentFields">
                                                    <div class="row g-3">
                                                        <div class="col-md-3">
                                                            <label class="form-label">Province <span
                                                                    class="text-danger">*</span></label>
                                                            <select class="form-select" id="addPermanentProvince"
                                                                name="provinceNamePermanent" required>
                                                                <option value="">Select Province</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label">City <span
                                                                    class="text-danger">*</span></label>
                                                            <select class="form-select" id="addPermanentCity"
                                                                name="cityNamePermanent" required>
                                                                <option value="">Select City</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label">Barangay <span
                                                                    class="text-danger">*</span></label>
                                                            <select class="form-select" id="addPermanentBarangay"
                                                                name="barangayNamePermanent" required>
                                                                <option value="">Select Barangay</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label">
                                                                Purok <span class="text-danger">*</span>
                                                            </label>
                                                            <select class="form-select" name="purokPermanent"
                                                                id="addPermanentPurok" required>
                                                                <option value="">Select Purok</option>
                                                                <?php for ($i = 1; $i <= 7; $i++): ?>
                                                                    <option value="Purok <?= $i; ?>">Purok
                                                                        <?= $i; ?>
                                                                    </option>
                                                                <?php endfor; ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label">Block & Lot / House No.</label>
                                                            <input type="text" class="form-control"
                                                                name="blockLotNoPermanent" id="addPermanentBlockLot">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label">Street</label>
                                                            <input type="text" class="form-control"
                                                                name="streetNamePermanent" id="addPermanentStreet">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label">Phase</label>
                                                            <input type="text" class="form-control"
                                                                name="phasePermanent" id="addPermanentPhase">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label">Subdivision</label>
                                                            <input type="text" class="form-control"
                                                                name="subdivisionNamePermanent"
                                                                id="addPermanentSubdivision">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Foreign Permanent Address Field -->
                                                <div class="col-12 d-none" id="addForeignPermanentField">
                                                    <label class="form-label">Foreign Permanent Address <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control"
                                                        name="foreignPermanentAddress" id="addForeignAddress"
                                                        placeholder="Enter complete foreign address">
                                                </div>
                                            </div>

                                            <!-- Other Details -->
                                            <h6 class="fw-bold text-uppercase border-bottom pb-2 mb-3">Other Details
                                            </h6>
                                            <div class="row g-3">
                                                <div class="col-md-3">
                                                    <label class="form-label">Residency Type</label>
                                                    <input type="text" class="form-control" name="residencyType">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label d-block">Voter's List <span
                                                            class="text-danger">*</span></label>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="isVoter"
                                                            value="Yes" required>
                                                        <label class="form-check-label">Yes</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="isVoter"
                                                            value="No" required>
                                                        <label class="form-check-label">No</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label d-block">Derogatory Record <span
                                                            class="text-danger">*</span></label>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="remarks"
                                                            value="Yes" required>
                                                        <label class="form-check-label">Yes</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="remarks"
                                                            value="No" required>
                                                        <label class="form-check-label">No</label>
                                                    </div>
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
                                <?php if ($totalRows > 20): ?>
                                    <div class="col-12 col-md-6">
                                        <nav class="d-flex justify-content-center justify-content-md-end">
                                            <ul class="pagination pagination-sm mb-0">
                                                <?php
                                                $queryBase = "page=resident&search=" . urlencode($search) . "&sortBy=" . urlencode($sortBy) . "&order=" . urlencode($order);
                                                ?>

                                                <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                                                    <a class="page-link"
                                                        href="?<?= $queryBase ?>&p=<?= max(1, $currentPage - 1) ?>"
                                                        aria-label="Previous">
                                                        <span aria-hidden="true">&laquo;</span>
                                                    </a>
                                                </li>

                                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                                    <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                                                        <a class="page-link" href="?<?= $queryBase ?>&p=<?= $i ?>"><?= $i ?></a>
                                                    </li>
                                                <?php endfor; ?>

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
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
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

            const fields = ['blockLotNo', 'streetName', 'phase', 'subdivisionName', 'barangayName', 'cityName', 'provinceName', 'purok'];

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
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll('.toggle-restrict-btn').forEach(button => {
                button.addEventListener('click', function () {

                    const userID = this.getAttribute('data-user-id');
                    const currentStatus = this.getAttribute('data-status');
                    const newStatus = currentStatus === 'Yes' ? 'No' : 'Yes';

                    // Send AJAX request to update in backend
                    fetch('restrictUserHandler.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `userID=${userID}&newStatus=${newStatus}`
                    })
                        .then(response => response.text())
                        .then(result => {
                            if (result === 'success') {

                                // ✅ Update button display (icon & color)
                                this.setAttribute('data-status', newStatus);

                                if (newStatus === 'Yes') {
                                    this.classList.remove('btn-danger');
                                    this.classList.add('btn-success');
                                    this.innerHTML = '<i class="fas fa-check"></i>'; // ✅ Show check
                                } else {
                                    this.classList.remove('btn-success');
                                    this.classList.add('btn-danger');
                                    this.innerHTML = '<i class="fas fa-times"></i>'; // ❌ Show cross
                                }
                            }
                        });
                });
            });
        });
    </script>

    <script>
        // ========== ADD RESIDENT MODAL JAVASCRIPT ==========
        document.addEventListener('DOMContentLoaded', function () {
            // Load JSON for address dropdowns
            // Path: admin/adminContent/resident.php -> ../../assets/json/...
            // OR if accessed via admin/index.php -> ../assets/json/...
            const jsonPath = '../assets/json/philippine_provinces_cities_municipalities_and_barangays_2019v2.json';

            fetch(jsonPath)
                .catch(err => {
                    // Try alternative path if first one fails
                    console.log('Trying alternative path...');
                    return fetch('../../assets/json/philippine_provinces_cities_municipalities_and_barangays_2019v2.json');
                })
                .then(response => response.json())
                .then(data => {
                    const addProvinceSelect = document.getElementById('addProvince');
                    const addCitySelect = document.getElementById('addCity');
                    const addBarangaySelect = document.getElementById('addBarangay');
                    const addPermanentProvinceSelect = document.getElementById('addPermanentProvince');
                    const addPermanentCitySelect = document.getElementById('addPermanentCity');
                    const addPermanentBarangaySelect = document.getElementById('addPermanentBarangay');

                    // Helper functions
                    function normalize(s) {
                        if (!s) return '';
                        return s.toString().trim().toLowerCase()
                            .replace(/\b(city|municipality|municipal|province|of|the)\b/g, '')
                            .replace(/[^a-z0-9\s]/g, '').replace(/\s+/g, ' ').trim();
                    }

                    // Populate provinces
                    const allProvinces = [];
                    Object.keys(data).forEach(regionCode => {
                        const provinces = data[regionCode].province_list;
                        Object.keys(provinces).forEach(provinceKey => {
                            allProvinces.push(provinceKey);
                        });
                    });
                    allProvinces.sort((a, b) => a.localeCompare(b));

                    // Add provinces to both selects
                    allProvinces.forEach(provinceKey => {
                        addProvinceSelect.add(new Option(provinceKey, provinceKey));
                        addPermanentProvinceSelect.add(new Option(provinceKey, provinceKey));
                    });

                    // Populate cities
                    function populateCities(provinceKey, citySelect) {
                        citySelect.innerHTML = '<option value="">Select City</option>';
                        if (!provinceKey) return;

                        for (const rc of Object.keys(data)) {
                            const provinces = data[rc].province_list;
                            if (provinces[provinceKey]) {
                                const cities = Object.keys(provinces[provinceKey].municipality_list).sort();
                                cities.forEach(cityKey => {
                                    citySelect.add(new Option(cityKey, cityKey));
                                });
                                break;
                            }
                        }
                    }

                    // Populate barangays
                    function populateBarangays(provinceKey, cityKey, barangaySelect) {
                        barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
                        if (!provinceKey || !cityKey) return;

                        for (const rc of Object.keys(data)) {
                            const provinces = data[rc].province_list;
                            if (provinces[provinceKey]) {
                                const cities = provinces[provinceKey].municipality_list;
                                if (cities[cityKey]) {
                                    const barangays = cities[cityKey].barangay_list.sort();
                                    barangays.forEach(brgy => {
                                        barangaySelect.add(new Option(brgy, brgy));
                                    });
                                }
                                break;
                            }
                        }
                    }

                    // Event listeners for current address
                    addProvinceSelect.addEventListener('change', function () {
                        populateCities(this.value, addCitySelect);
                        addBarangaySelect.innerHTML = '<option value="">Select Barangay</option>';
                    });

                    addCitySelect.addEventListener('change', function () {
                        populateBarangays(addProvinceSelect.value, this.value, addBarangaySelect);
                    });

                    // Event listeners for permanent address
                    addPermanentProvinceSelect.addEventListener('change', function () {
                        populateCities(this.value, addPermanentCitySelect);
                        addPermanentBarangaySelect.innerHTML = '<option value="">Select Barangay</option>';
                    });

                    addPermanentCitySelect.addEventListener('change', function () {
                        populateBarangays(addPermanentProvinceSelect.value, this.value, addPermanentBarangaySelect);
                    });

                    // Same as current address
                    document.getElementById('addSameAsCurrent').addEventListener('change', function () {
                        if (this.checked) {
                            addPermanentProvinceSelect.value = addProvinceSelect.value;
                            populateCities(addProvinceSelect.value, addPermanentCitySelect);
                            setTimeout(() => {
                                addPermanentCitySelect.value = addCitySelect.value;
                                populateBarangays(addProvinceSelect.value, addCitySelect.value, addPermanentBarangaySelect);
                                setTimeout(() => {
                                    addPermanentBarangaySelect.value = addBarangaySelect.value;
                                    document.getElementById('addPermanentPurok').value = document.querySelector('[name="purok"]').value;
                                    document.getElementById('addPermanentBlockLot').value = document.querySelector('[name="blockLotNo"]').value;
                                    document.getElementById('addPermanentStreet').value = document.querySelector('[name="streetName"]').value;
                                    document.getElementById('addPermanentPhase').value = document.querySelector('[name="phase"]').value;
                                    document.getElementById('addPermanentSubdivision').value = document.querySelector('[name="subdivisionName"]').value;
                                }, 100);
                            }, 100);
                        }
                    });
                });

            // Auto-calculate age
            document.getElementById('addBirthDate').addEventListener('change', function () {
                const birthDate = new Date(this.value);
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const monthDiff = today.getMonth() - birthDate.getMonth();
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                document.getElementById('addAge').value = age;

                // Update max for length of stay
                const lengthOfStay = document.getElementById('addLengthOfStay');
                lengthOfStay.max = age;
                if (parseInt(lengthOfStay.value) > age) {
                    lengthOfStay.value = age;
                }
            });

            // Length of stay validation
            document.getElementById('addLengthOfStay').addEventListener('input', function () {
                const age = parseInt(document.getElementById('addAge').value) || 0;
                const length = parseInt(this.value) || 0;
                if (length > age) {
                    this.value = age;
                }
                if (length < 0) {
                    this.value = 0;
                }
            });

            // Citizenship change - show/hide permanent address fields
            document.getElementById('addCitizenship').addEventListener('change', function () {
                const isFilipino = this.value.toUpperCase() === 'FILIPINO';
                const filipinoFields = document.getElementById('addFilipinoPermanentFields');
                const foreignField = document.getElementById('addForeignPermanentField');
                const foreignInput = document.getElementById('addForeignAddress');

                if (isFilipino) {
                    filipinoFields.classList.remove('d-none');
                    foreignField.classList.add('d-none');
                    foreignInput.removeAttribute('required');

                    // Re-enable Filipino fields
                    document.getElementById('addPermanentProvince').setAttribute('required', 'required');
                    document.getElementById('addPermanentCity').setAttribute('required', 'required');
                    document.getElementById('addPermanentBarangay').setAttribute('required', 'required');
                    document.getElementById('addPermanentPurok').setAttribute('required', 'required');
                } else {
                    filipinoFields.classList.add('d-none');
                    foreignField.classList.remove('d-none');
                    foreignInput.setAttribute('required', 'required');

                    // Disable Filipino fields requirements
                    document.getElementById('addPermanentProvince').removeAttribute('required');
                    document.getElementById('addPermanentCity').removeAttribute('required');
                    document.getElementById('addPermanentBarangay').removeAttribute('required');
                    document.getElementById('addPermanentPurok').removeAttribute('required');
                }
            });

            // Educational level change
            document.getElementById('addEducationalLevel').addEventListener('change', function () {
                const shsDiv = document.getElementById('addShsTrackDiv');
                const collegeDiv = document.getElementById('addCollegeCourseDiv');
                const value = this.value;

                shsDiv.style.display = 'none';
                collegeDiv.style.display = 'none';

                if (value.includes('Senior High')) {
                    shsDiv.style.display = 'block';
                } else if (value.includes('College')) {
                    collegeDiv.style.display = 'block';
                }
            });

            // SHS Track - Others option
            document.getElementById('addShsTrack').addEventListener('change', function () {
                const otherInput = document.getElementById('addShsTrackOther');
                if (this.value === 'Others') {
                    otherInput.classList.remove('d-none');
                    otherInput.setAttribute('required', 'required');
                } else {
                    otherInput.classList.add('d-none');
                    otherInput.removeAttribute('required');
                    otherInput.value = '';
                }
            });

            // College Course - Others option
            document.getElementById('addCollegeCourse').addEventListener('change', function () {
                const otherInput = document.getElementById('addCollegeCourseOther');
                if (this.value === 'Others') {
                    otherInput.classList.remove('d-none');
                    otherInput.setAttribute('required', 'required');
                } else {
                    otherInput.classList.add('d-none');
                    otherInput.removeAttribute('required');
                    otherInput.value = '';
                }
            });
        });
    </script>

</body>

</html>
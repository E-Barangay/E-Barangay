<?php
include("sharedAssets/connect.php");
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: signin.php");
    exit();
}

$userID = $_SESSION['userID'];

// ✅ Always join userInfo and addresses since they exist at signup
$getUserQuery = "
    SELECT 
        u.*,
        ui.userInfoID, ui.firstName, ui.middleName, ui.lastName, ui.suffix,
        ui.gender, ui.age, ui.birthDate, ui.birthPlace, ui.profilePicture,
        ui.residencyType, ui.lengthOfStay, ui.civilStatus, ui.citizenship, ui.occupation,
        a.addressID, a.cityName, a.provinceName, a.barangayName, a.streetName,
        a.houseNo, a.phase, a.subdivisionName, a.purok
    FROM users u
    JOIN userInfo ui ON u.userInfoID = ui.userInfoID
    LEFT JOIN addresses a ON ui.userInfoID = a.userInfoID
    WHERE u.userID = $userID
";

$result = executeQuery($getUserQuery);
$data = mysqli_fetch_assoc($result);

if (isset($_POST['saveBtn'])) {
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $suffix = $_POST['suffix'] ?? '';
    $gender = $_POST['gender'];
    $birthDate = $_POST['birthDate'];
    $phoneNumber = $_POST['phoneNumber'];
    $email = $_POST['email'];

    // Address fields
    $provinceName = $_POST['provinceName'];
    $cityName = $_POST['cityName'];
    $barangayName = $_POST['barangayName'];
    $streetName = $_POST['streetName'];
    $houseNo = $_POST['houseNo'] ?? '';
    $phase = $_POST['phase'] ?? '';
    $subdivisionName = $_POST['subdivisionName'] ?? '';
    $purok = $_POST['purok'] ?? '';

    // ✅ userInfoID always exists now
    $userInfoID = $data['userInfoID'];

    // --- UPDATE userInfo ---
    executeQuery("
        UPDATE userInfo 
        SET firstName='$firstName',
            middleName='$middleName',
            lastName='$lastName',
            suffix='$suffix',
            gender='$gender',
            birthDate='$birthDate'
        WHERE userInfoID=$userInfoID
    ");

    // --- Handle profile picture upload ---
    if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "uploads/profile/";
        $ext = pathinfo($_FILES['profilePicture']['name'], PATHINFO_EXTENSION);
        $newFileName = uniqid("pp_", true) . "." . strtolower($ext);
        $targetPath = $uploadDir . $newFileName;

        // Delete old if exists
        if (!empty($data['profilePicture']) && file_exists($uploadDir . $data['profilePicture'])) {
            unlink($uploadDir . $data['profilePicture']);
        }

        if (move_uploaded_file($_FILES['profilePicture']['tmp_name'], $targetPath)) {
            executeQuery("UPDATE userInfo SET profilePicture = '$newFileName' WHERE userInfoID = $userInfoID");
        }
    }

    // --- UPDATE users (contact info) ---
    executeQuery("
        UPDATE users 
        SET phoneNumber='$phoneNumber',
            email='$email'
        WHERE userID=$userID
    ");

    // --- UPDATE or INSERT addresses ---
    $existingAddress = executeQuery("SELECT * FROM addresses WHERE userInfoID = $userInfoID");
    if (mysqli_num_rows($existingAddress) > 0) {
        executeQuery("
            UPDATE addresses 
            SET provinceName='$provinceName',
                cityName='$cityName',
                barangayName='$barangayName',
                streetName='$streetName',
                houseNo='$houseNo',
                phase='$phase',
                subdivisionName='$subdivisionName',
                purok='$purok'
            WHERE userInfoID=$userInfoID
        ");
    } else {
        executeQuery("
            INSERT INTO addresses (userInfoID, provinceName, cityName, barangayName, streetName, houseNo, phase, subdivisionName, purok)
            VALUES ($userInfoID, '$provinceName', '$cityName', '$barangayName', '$streetName', '$houseNo', '$phase', '$subdivisionName', '$purok')
        ");
    }

    header("Location: profile.php");
    exit();
}
?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Barangay | Profile</title>

    <!-- Icon -->
    <link rel="icon" href="assets/images/logoSanAntonio.png">

    <!-- Style Sheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/navbar/style.css">
    <link rel="stylesheet" href="assets/css/profile/style.css">
    <link rel="stylesheet" href="assets/css/footer/style.css">
</head>

<body data-bs-theme="light">
    <?php
    if (isset($_SESSION['userID'])) {
        include("sharedAssets/navbarLoggedIn.php");
    } else {
        include("sharedAssets/navbar.php");
    }
    ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <form method="POST" enctype="multipart/form-data">
                    <div class="card p-3 p-md-5 m-2 m-md-4">

                        <div class="position-relative">
                            <button type="submit" id="editBtnDesktop" name="saveBtn"
                                class="btn btn-primary position-absolute top-0 end-0 d-none d-md-block">Edit</button>

                            <!-- Profile Header Section -->
                            <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start mb-4">
                                <div class="flex-shrink-0 mb-3 mb-md-0">
                                    <img src="<?php echo !empty($data['profilePicture'])
                                        ? 'uploads/profile/' . $data['profilePicture']
                                        : 'assets/images/defaultProfile.png'; ?>"
                                        class="rounded-circle profile-preview"
                                        style="width: 120px; height: 120px; object-fit: cover;">

                                    <!-- File input for editing (hidden until Edit is pressed) -->
                                    <input type="file" name="profilePicture" class="form-control mt-2 d-none"
                                        id="profilePictureInput" accept="image/*">
                                </div>

                                <div class="ms-md-4 text-center text-md-start">
                                    <h3 class="mb-2">
                                        <?php echo $data['firstName'] . ' ' . $data['middleName'] . ' ' . $data['lastName'] ?>
                                    </h3>
                                    <p class="text-break mb-0"><?php echo $data['email'] ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-between-end mb-3 d-md-none">
                            <button type="submit" id="editBtnMobile" name="saveBtn"
                                class="btn btn-primary w-100">Edit</button>
                        </div>

                        <!-- Form Fields -->
                        <div class="row g-3">

                            <!-- User Info -->
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold">First Name</label>
                                <input name="firstName" class="form-control"
                                    value="<?php echo $data['firstName'] ?? '' ?>" disabled>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold">Middle Name</label>
                                <input name="middleName" class="form-control"
                                    value="<?php echo $data['middleName'] ?? '' ?>" disabled>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold">Last Name</label>
                                <input name="lastName" class="form-control"
                                    value="<?php echo $data['lastName'] ?? '' ?>" disabled>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Gender</label>
                                <input name="gender" class="form-control" value="<?php echo $data['gender'] ?? '' ?>"
                                    disabled>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Birth Date</label>
                                <input name="birthDate" class="form-control" type="date"
                                    value="<?php echo $data['birthDate'] ?? '' ?>" disabled>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Phone Number</label>
                                <input name="phoneNumber" class="form-control"
                                    value="<?php echo $data['phoneNumber'] ?? '' ?>" disabled>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input name="email" class="form-control" value="<?php echo $data['email'] ?? '' ?>"
                                    disabled>
                            </div>

                            <!-- Address -->
                            <div class="col-12 col-md-3">
                                <label class="form-label fw-semibold">House No.</label>
                                <input name="houseNo" class="form-control" value="<?php echo $data['houseNo'] ?? '' ?>"
                                    disabled>
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label fw-semibold">Phase</label>
                                <input name="phase" class="form-control" value="<?php echo $data['phase'] ?? '' ?>"
                                    disabled>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Purok</label>
                                <input name="purok" class="form-control" value="<?php echo $data['purok'] ?? '' ?>"
                                    disabled>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Street</label>
                                <input name="streetName" class="form-control"
                                    value="<?php echo $data['streetName'] ?? '' ?>" disabled>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Subdivision</label>
                                <input name="subdivisionName" class="form-control"
                                    value="<?php echo $data['subdivisionName'] ?? '' ?>" disabled>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Barangay</label>
                                <input name="barangayName" class="form-control"
                                    value="<?php echo $data['barangayName'] ?? '' ?>" disabled>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">City</label>
                                <input name="cityName" class="form-control"
                                    value="<?php echo $data['cityName'] ?? '' ?>" disabled>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Province</label>
                                <input name="provinceName" class="form-control"
                                    value="<?php echo $data['provinceName'] ?? '' ?>" disabled>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include("sharedAssets/footer.php") ?>

    <script>
        const inputs = document.querySelectorAll('.form-control');
        const btnDesktop = document.getElementById('editBtnDesktop');
        const btnMobile = document.getElementById('editBtnMobile');
        const fileInput = document.getElementById('profilePictureInput');
        let isEdit = false;

        function handleButtonClick(e) {
            if (!isEdit) {
                e.preventDefault(); // Stop submission on first click
                inputs.forEach(input => input.removeAttribute('disabled'));

                // Show file input for profile picture
                if (fileInput) {
                    fileInput.classList.remove('d-none');
                }

                // Change both buttons to "Save"
                [btnDesktop, btnMobile].forEach(button => {
                    if (button) {
                        button.textContent = 'Save';
                        button.classList.replace('btn-primary', 'btn-success');
                    }
                });

                isEdit = true; // Next click will submit
            }
            // else → allow form submission
        }

        if (btnDesktop) {
            btnDesktop.addEventListener('click', handleButtonClick);
        }

        if (btnMobile) {
            btnMobile.addEventListener('click', handleButtonClick);
        }

        // Optional: live preview of new profile picture
        if (fileInput) {
            fileInput.addEventListener('change', function (e) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    document.querySelector('.profile-preview').src = event.target.result;
                };
                reader.readAsDataURL(e.target.files[0]);
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
include("sharedAssets/connect.php");
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: signin.php");
    exit();
}

$userID = $_SESSION['userID'];

// Get user info and address
$getUserQuery = "
    SELECT u.*, ui.userInfoID, ui.firstName, ui.middleName, ui.lastName, ui.gender, ui.birthDate,
           a.addressID, a.cityID, a.provinceID, a.barangayID, a.streetID,
           p.provinceName, c.cityName, b.barangayName, s.streetName
    FROM users u
    JOIN userinfo ui ON u.userInfoID = ui.userInfoID
    LEFT JOIN addresses a ON ui.userInfoID = a.userInfoID
    LEFT JOIN provinces p ON a.provinceID = p.provinceID
    LEFT JOIN cities c ON a.cityID = c.cityID
    LEFT JOIN barangays b ON a.barangayID = b.barangayID
    LEFT JOIN streets s ON a.streetID = s.streetID
    WHERE u.userID = $userID
";
$result = executeQuery($getUserQuery);
$data = mysqli_fetch_assoc($result);

if (isset($_POST['saveBtn'])) {
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $gender = $_POST['gender'];
    $birthDate = $_POST['birthDate'];
    $phoneNumber = $_POST['phoneNumber'];
    $email = $_POST['email'];

    $province = $_POST['province'];
    $city = $_POST['city'];
    $barangay = $_POST['barangay'];
    $street = $_POST['street'];

    $userInfoID = $data['userInfoID'];

    // Update name & contact info
    executeQuery("UPDATE userinfo SET firstName='$firstName', middleName='$middleName', lastName='$lastName', gender='$gender', birthDate='$birthDate' WHERE userInfoID=$userInfoID");
    executeQuery("UPDATE users SET phoneNumber='$phoneNumber', email='$email' WHERE userID=$userID");

    // Lookup or insert province
    $provinceResult = mysqli_query($conn, "SELECT provinceID FROM provinces WHERE provinceName='$province'");
    $provinceID = (mysqli_num_rows($provinceResult) > 0) ? mysqli_fetch_assoc($provinceResult)['provinceID'] : (mysqli_query($conn, "INSERT INTO provinces (provinceName) VALUES ('$province')") ? mysqli_insert_id($conn) : null);

    // Lookup or insert city
    $cityResult = mysqli_query($conn, "SELECT cityID FROM cities WHERE cityName='$city' AND provinceID=$provinceID");
    $cityID = (mysqli_num_rows($cityResult) > 0) ? mysqli_fetch_assoc($cityResult)['cityID'] : (mysqli_query($conn, "INSERT INTO cities (cityName, provinceID) VALUES ('$city', $provinceID)") ? mysqli_insert_id($conn) : null);

    // Lookup or insert barangay
    $barangayResult = mysqli_query($conn, "SELECT barangayID FROM barangays WHERE barangayName='$barangay' AND cityID=$cityID");
    $barangayID = (mysqli_num_rows($barangayResult) > 0) ? mysqli_fetch_assoc($barangayResult)['barangayID'] : (mysqli_query($conn, "INSERT INTO barangays (barangayName, cityID) VALUES ('$barangay', $cityID)") ? mysqli_insert_id($conn) : null);

    // Lookup or insert street
    $streetResult = mysqli_query($conn, "SELECT streetID FROM streets WHERE streetName='$street'");
    $streetID = (mysqli_num_rows($streetResult) > 0) ? mysqli_fetch_assoc($streetResult)['streetID'] : (mysqli_query($conn, "INSERT INTO streets (streetName, barangayID) VALUES ('$street', $barangayID)") ? mysqli_insert_id($conn) : null);

    // Update or insert address
    $existingAddress = executeQuery("SELECT * FROM addresses WHERE userInfoID = $userInfoID");
    if (mysqli_num_rows($existingAddress) > 0) {
        executeQuery("UPDATE addresses SET cityID=$cityID, provinceID=$provinceID, barangayID=$barangayID, streetID=$streetID WHERE userInfoID=$userInfoID");
    } else {
        executeQuery("INSERT INTO addresses (userInfoID, cityID, provinceID, barangayID, streetID) VALUES ($userInfoID, $cityID, $provinceID, $barangayID, $streetID)");
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
    <link rel="icon" href="assets/images/logoSanAntonio.png">
    <!-- Style Sheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/navbar/style.css">
    <link rel="stylesheet" href="assets/css/profile/style.css">
    <link rel="stylesheet" href="assets/css/footer/style.css">
</head>

<body data-bs-theme="dark">
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
                <form method="POST">
                    <div class="card p-3 p-md-5 m-2 m-md-4">

                        <div class="position-relative">
                            <button type="submit" id="editBtnDesktop" name="saveBtn"
                                class="btn btn-primary position-absolute top-0 end-0 d-none d-md-block">Edit</button>

                            <!-- Profile Header Section -->
                            <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start mb-4">
                                <div class="flex-shrink-0 mb-3 mb-md-0">
                                    <img src="assets/images/defaultProfile.png" class="rounded-circle"
                                        style="width: 120px; height: 120px;">
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

                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold">First Name</label>
                                <input name="firstName" class="form-control" value="<?php echo $data['firstName'] ?? '' ?>"
                                    disabled>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold">Middle Name</label>
                                <input name="middleName" class="form-control" value="<?php echo $data['middleName'] ?? '' ?>"
                                    disabled>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold">Last Name</label>
                                <input name="lastName" class="form-control" value="<?php echo $data['lastName'] ?? '' ?>"
                                    disabled>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Gender</label>
                                <input name="gender" class="form-control" value="<?php echo $data['gender'] ?? '' ?>" disabled>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Birth Date</label>
                                <input name="birthDate" class="form-control" type="date"
                                    value="<?php echo $data['birthDate'] ?? '' ?>" disabled>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Phone Number</label>
                                <input name="phoneNumber" class="form-control" value="<?php echo $data['phoneNumber'] ?? '' ?>"
                                    disabled>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input name="email" class="form-control" value="<?php echo $data['email'] ?? '' ?>" disabled>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Province</label>
                                <input name="province" class="form-control" value="<?php echo $data['provinceName'] ?? '' ?>"
                                    disabled>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">City</label>
                                <input name="city" class="form-control" value="<?php echo $data['cityName'] ?? '' ?>" disabled>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Barangay</label>
                                <input name="barangay" class="form-control" value="<?php echo $data['barangayName'] ?? '' ?>"
                                    disabled>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Street</label>
                                <input name="street" class="form-control" value="<?php echo $data['streetName'] ?? '' ?>"
                                    disabled>
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
        let isEdit = false;

        function handleButtonClick(e) {
            if (!isEdit) {
                e.preventDefault();
                inputs.forEach(input => input.removeAttribute('disabled'));

                // Update both buttons
                [btnDesktop, btnMobile].forEach(button => {
                    if (button) {
                        button.textContent = 'Save';
                        button.classList.replace('btn-primary', 'btn-success');
                    }
                });

                isEdit = true;
            }
        }

        if (btnDesktop) {
            btnDesktop.addEventListener('click', handleButtonClick);
        }

        if (btnMobile) {
            btnMobile.addEventListener('click', handleButtonClick);
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
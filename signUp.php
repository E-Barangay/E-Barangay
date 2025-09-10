<?php

session_start();

include('sharedAssets/connect.php');

if (isset($_POST["submit"])) {
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $birthDate = $_POST['birthDate'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $reEnterPassword = $_POST['reEnterPassword'];
    $phoneNumber = $_POST['phoneNumber'];
    $provinceName = $_POST['province'];
    $cityName = $_POST['city'];
    $barangayName = $_POST['barangay'];
    $streetName = $_POST['street'];
    $gender = $_POST['gender'];
    $username = $email; // you can change this to use a different field
    $role = "user";

    if ($password === $reEnterPassword) {
        // Step 1: Insert into userInfo
        $insertInfo = "INSERT INTO userInfo (firstName, middleName, lastName, gender, birthDate, profilePicture) 
                       VALUES ('$firstName', '$middleName', '$lastName', '$gender', '$birthDate', NULL)";
        $infoResult = executeQuery($insertInfo);

        if ($infoResult) {
            $userInfoID = mysqli_insert_id($conn); // get the inserted ID

            // Step 2: Insert into users table
            $insertUser = "INSERT INTO users (userInfoID, username, email, password, phoneNumber, role) 
                           VALUES ('$userInfoID', '$username', '$email', '$password', '$phoneNumber', '$role')";
            $userResult = executeQuery($insertUser);

            if ($userResult) {
                // Step 3: Insert or retrieve province
                $checkProvince = "SELECT provinceID FROM provinces WHERE provinceName = '$provinceName'";
                $provinceResult = mysqli_query($conn, $checkProvince);

                if ($provinceResult && mysqli_num_rows($provinceResult) > 0) {
                    $provinceRow = mysqli_fetch_assoc($provinceResult);
                    $provinceID = $provinceRow['provinceID'];
                } else {
                    mysqli_query($conn, "INSERT INTO provinces (provinceName) VALUES ('$provinceName')");
                    $provinceID = mysqli_insert_id($conn);
                }

                // Step 4: Insert or retrieve city
                $checkCity = "SELECT cityID FROM cities WHERE cityName = '$cityName' AND provinceID = '$provinceID'";
                $cityResult = mysqli_query($conn, $checkCity);

                if ($cityResult && mysqli_num_rows($cityResult) > 0) {
                    $cityRow = mysqli_fetch_assoc($cityResult);
                    $cityID = $cityRow['cityID'];
                } else {
                    mysqli_query($conn, "INSERT INTO cities (cityName, provinceID) VALUES ('$cityName', '$provinceID')");
                    $cityID = mysqli_insert_id($conn);
                }

                // Step 5: Insert or retrieve barangay
                $checkBrgy = "SELECT barangayID FROM barangays WHERE barangayName = '$barangayName' AND cityID = '$cityID'";
                $barangayResult = mysqli_query($conn, $checkBrgy);

                if ($barangayResult && mysqli_num_rows($barangayResult) > 0) {
                    $barangayRow = mysqli_fetch_assoc($barangayResult);
                    $barangayID = $barangayRow['barangayID'];
                } else {
                    mysqli_query($conn, "INSERT INTO barangays (barangayName, cityID) VALUES ('$barangayName', '$cityID')");
                    $barangayID = mysqli_insert_id($conn);
                }

                // Step 6: Insert or retrieve street
                $checkStreet = "SELECT streetID FROM streets WHERE streetName = '$streetName'";
                $streetResult = mysqli_query($conn, $checkStreet);

                if ($streetResult && mysqli_num_rows($streetResult) > 0) {
                    $streetRow = mysqli_fetch_assoc($streetResult);
                    $streetID = $streetRow['streetID'];
                } else {
                    mysqli_query($conn, "INSERT INTO streets (streetName) VALUES ('$streetName')");
                    $streetID = mysqli_insert_id($conn);
                }

                // Step 7: Insert into addresses table
                $insertAddress = "INSERT INTO addresses (userInfoID, cityID, provinceID, barangayID, streetID) 
                                  VALUES ('$userInfoID', '$cityID', '$provinceID', '$barangayID', '$streetID')";
                executeQuery($insertAddress);

                // Done: Redirect user
                $_SESSION['userID'] = $userInfoID;
                header("Location: index.php");
                exit();
            }
        }
    } else {
        echo "<script>alert('Passwords do not match');</script>";
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Barangay | Sign Up</title>

    <!-- Icon -->
    <link rel="icon" href="assets/images/logoSanAntonio.png">

    <!-- Style Sheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/navbar/style.css">
    <link rel="stylesheet" href="assets/css/signup/style.css">
    <link rel="stylesheet" href="assets/css/footer/style.css">
</head>

<body data-bs-theme="light">
    
    <div class="container-fluid">
        <div class="row min-vh-100">
            
            <div class="col-lg-7 d-none d-lg-block p-0">
                <img src="assets/images/bgHall.jpeg" class="leftBackgroundImage" alt="Barangay Background">
            </div>

            <div class="col-lg-5 col-12 d-flex flex-column justify-content-center px-5">
                <div class="row">
                    <div class="col d-flex justify-content-center">
                        <img src="assets/images/logoSanAntonio.png" class="logoSanAntonio me-2" alt="Logo San Antonio">
                        <img src="assets/images/logoSantoTomas.png" class="logoSantoTomas" alt="Logo Santo Tomas">
                    </div>
                </div>
                <div class="row mt-1">
                    <div class="col">
                        <div class="d-flex flex-column">
                            <span class="barangay mt-2">Barangay San Antonio</span>
                            <span class="city">Santo Tomas City, Batangas</span>
                        </div>
                    </div>
                </div>

                <form method="POST">
                    <div class="row my-4">
                        <div class="col-4">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="floatingInput" placeholder="First Name" required>
                                <label for="floatingInput">First Name</label>
                            </div>
                        </div>
                        <div class="col-4 p-0">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="floatingInput" placeholder="Middle Name" required>
                                <label for="floatingInput">Middle Name</label>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="floatingInput" placeholder="Last Name" required>
                                <label for="floatingInput">Last Name</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com">
                                <label for="floatingInput">Email address</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="floatingInput" placeholder="09123456789" required>
                                <label for="floatingInput">Phone Number</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating mb-3 position-relative">
                                <input type="password" class="form-control" id="floatingPassword" placeholder="Password">
                                <label for="floatingPassword">Password</label>
                                <i class="fa-regular fa-eye" id="togglePassword" style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer; color: #6c757d;"></i>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating position-relative">
                                <input type="password" class="form-control" id="floatingConfirmPassword" placeholder="Confirm Password">
                                <label for="floatingConfirmPassword">Confirm Password</label>
                                 <i class="fa-regular fa-eye" id="togglePassword" style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer; color: #6c757d;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col text-center">
                            <button class="btn btn-primary signUpButton mb-3 mt-2">Sign Up</button>
                            <span class="pt-2" style="color: black;">Already have an account?</span> <a href="login.php" style="color: #19AFA5;">Login</a>
                        </div>
                    </div>
                </form>
                
            </div>
        </div>
    </div>

    <!-- <div class="container-fluid">
        <div class="row"> -->
            <!-- Left Side Image -->
            <!-- <div class="col-md-6 d-none d-md-block p-0">
                <img src="assets/images/bgHall.jpeg" class="left-img" alt="Barangay Background">
            </div> -->

            <!-- Right Side Sign In -->
            <!-- <div class="col-md-6 col-12 d-flex justify-content-center align-items-start">
                <div class="container">
                    <div class="w-100 sign-in-card">
                        <div class="">
                            <div class="row">
                                <div class="col-12 ">
                                    <div class="text-center mt-3 mb-2 ">
                                        <div class="d-flex flex-row justify-content-center align-items-center gap-3">
                                            <img src="assets/images/logoSanAntonio.png" class="img-fluid"
                                                style="max-width: 60px;" alt="Logo">
                                            <img src="assets/images/logoSantoTomas.png" class="img-fluid"
                                                style="max-width: 60px;" alt="Logo">
                                        </div>

                                        <h2 class="mb-0">Barangay San Antonio</h2>
                                        <div class="fs-6">Sto. Tomas, Batangas</div>

                                        <p class="fs-6 fst-italic text-center">
                                            Serving the vibrant community of Barangay San Antonio, Santo Tomas, Batangas
                                            — where
                                            tradition meets progress.
                                        </p>

                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                <div class="d-flex flex-md-row flex-column">
                                    <div class="col-md-4 col-12 p-md-2 p-1">
                                        <div class="mb-md-2 mt-md-2 mt-0 mb-0">
                                            <form action="" method="POST">
                                                <div class="row">
                                                    <div class="col-12 my-1">
                                                        <label class="form-label text-start">First Name</label>
                                                        <input type="text" name="firstName"
                                                            class="form-control w-100 h-50" required>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-12 my-1">
                                                        <label class="form-label text-start">Middle Name</label>
                                                        <input type="text" name="middleName"
                                                            class="form-control w-100 h-50" required>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-12 my-1">
                                                        <label class="form-label text-start">Last Name</label>
                                                        <input type="text" name="lastName"
                                                            class="form-control w-100 h-50" required>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-12 my-1">
                                                        <label class="form-label text-start">Birth Date</label>
                                                        <input type="date" name="birthDate"
                                                            class="form-control w-100 h-50" required>
                                                    </div>
                                                </div>



                                        </div>
                                    </div>

                                    <div class="col-md-4 col-12  p-md-2 p-1">
                                        <div class="mb-md-2 mt-md-2 mt-0 mb-0">

                                            <div class="row">
                                                <div class="col-12 my-1">
                                                    <label class="form-label text-start">Email</label>
                                                    <input type="email" name="email" class="form-control w-100 h-50"
                                                        required>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 my-1">
                                                    <label class="form-label text-start">Password</label>
                                                    <input type="password" name="password"
                                                        class="form-control w-100 h-50" required>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 my-1">
                                                    <label class="form-label text-start">Re-enter Password</label>
                                                    <input type="password" name="reEnterPassword"
                                                        class="form-control w-100 h-50" required>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 my-1">
                                                    <label class="form-label text-start">Phone Number</label>
                                                    <input type="number" name="phoneNumber"
                                                        class="form-control w-100 h-50">
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-md-4 col-12 p-md-2 p-1">
                                        <div class="mb-md-2 mt-md-2 mt-0 mb-0">

                                            <div class="row">
                                                <div class="col-12 my-1">
                                                    <label for="provinceInput" class="form-label">Province</label>
                                                    <input class="form-control w-100 h-50" list="dataListProvince"
                                                        id="provinceInput" name="province" placeholder="Province"
                                                        required>
                                                    <datalist id="dataListProvince"></datalist>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 my-1">
                                                    <label for="cityInput" class="form-label">City</label>
                                                    <input class="form-control w-100 h-50" list="dataListCity"
                                                        id="cityInput" name="city" placeholder="City" required>
                                                    <datalist id="dataListCity"></datalist>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 col-12 my-1">
                                                    <label for="barangayInput" class="form-label">Barangay</label>
                                                    <input class="form-control w-100 h-50" list="dataListBrgy"
                                                        id="barangayInput" name="barangay" placeholder="Barangay"
                                                        required>
                                                    <datalist id="dataListBrgy"></datalist>
                                                </div>


                                                <div class="col-md-6 col-12 my-1">
                                                    <label for="cityInput" class="form-label">Street</label>
                                                    <input class="form-control w-100 h-50" list="dataListCity"
                                                        id="cityInput" name="street" placeholder="Street">
                                                    <datalist id="dataListCity"></datalist>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 my-1">
                                                    <p>Gender</p>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" value = "Male" name="gender"
                                                            id="radioDefault1" checked>
                                                        <label class="form-check-label"  for="radioDefault1">
                                                            Male
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" value = "Female" name="gender"
                                                            id="radioDefault2">
                                                        <label class="form-check-label"  for="radioDefault2">
                                                            Female
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex flex-column align-items-center">

                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" value="" id="checkDefault"
                                            required>
                                        <label class="form-check-label" for="checkDefault">
                                            I Accept The <a href="">Terms & Condition</a>
                                        </label>
                                    </div>

                                    <button type="submit" name="submit" class="rounded-5 btn btn-custom fs-5 mb-3"
                                        style="width: 200px; background-color: #19AFA5">Create Account</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>


        <script src="assets/js/signUp/signUp.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
            crossorigin="anonymous"></script>
</body>

</html>
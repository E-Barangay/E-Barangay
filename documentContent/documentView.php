<?php

include("../sharedAssets/connect.php");

session_start();

$userID = $_SESSION['userID'];

$documentTypeID = isset($_GET['documentTypeID']) ? $_GET['documentTypeID'] : '';

$userQuery = "SELECT * FROM users 
              LEFT JOIN userInfo ON users.userID = userInfo.userID 
              LEFT JOIN addresses ON userInfo.userInfoID = addresses.userInfoID 
              LEFT JOIN streets ON addresses.streetID = streets.streetID
              LEFT JOIN barangays ON streets.barangayID = barangays.barangayID
              LEFT JOIN cities ON barangays.cityID = cities.cityID
              LEFT JOIN provinces ON cities.provinceID = provinces.provinceID
              WHERE users.userID = $userID";
$userResult = executeQuery($userQuery);

$userRow = mysqli_fetch_assoc($userResult);

$firstName = $userRow['firstName'];
$middleName = $userRow['middleName'];
$lastName = $userRow['lastName'];
$birthDate = $userRow['birthDate'];
$gender = $userRow['gender'];
$profilePicture = $userRow['profilePicture'];
$residencyType = $userRow['residencyType'];
$streetName = $userRow['streetName'];
$barangayName = $userRow['barangayName'];
$cityName = $userRow['cityName'];
$provinceName = $userRow['provinceName'];

$documentQuery = "SELECT * FROM documentTypes WHERE documentTypeID = $documentTypeID";
$documentResult = executeQuery($documentQuery);

$documentRow = mysqli_fetch_assoc($documentResult);

$documentName = $documentRow['documentName'];

if (isset($_POST['submit'])) {
    $purpose = $_POST['purpose'];

    $submitQuery = "INSERT INTO documents (documentTypeID, userID, purpose, requestDate) VALUES ($documentTypeID, $userID, '$purpose', NOW())";
    executeQuery($submitQuery);

    header("Location: ../documents.php?content=documentRequest");
    exit();
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Barangay | <?php echo $documentName ?></title>

    <!-- Icon -->
    <link rel="icon" href="../assets/images/logoSanAntonio.png">

    <!-- Style Sheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }
        
        .purposeSelect {
            height: 30px;
            align-items: center;
        }

        .submitButton {
            background-color: #19AFA5;
            border: none;
        }
    </style>
</head>

<body data-bs-theme="light">
    
    <form method="POST">
        <div class="container py-4">
            <div class="row">
                <div class="col">
                    <div class="row align-items-center">
                        <div class="col-4 d-none d-sm-block text-end">
                            <img src="../assets/images/logoSanAntonio.png" alt="Logo 1" style=" height: 125px;">
                        </div>
                        <div class="col-lg-4 col-12 text-center">
                            <div>Republic of the Philippines</div>
                            <div>Province of Batangas</div>
                            <div>City of Sto. Tomas</div>
                            <div>Barangay San Antonio</div>
                            <div>Telefax: (043) 784-3812</div>
                        </div>
                        <div class="col-4 d-none d-sm-block text-start">
                            <img src="../assets/images/logoSantoTomas.png" alt="Logo 2" style="height: 125px;">
                        </div>
                    </div>
                    <div class="row border-top border-bottom mt-3">
                        <div class="col text-center py-2">
                            <div class="h5 m-0"><?php echo $documentName ?></div>
                        </div>
                    </div>
                    <div class="row mt-3 justify-content-center">
                        <div class="col-3 text-center">
                            <div>HON. ULYSES "TETET" M. RELLAMA</div>
                            <div class="pt-3">COUNCILORS</div>
                            <div>HON. JAYMAR A. MANARIN</div>
                            <div>HON. NOLITO S. AVENIDO</div>
                            <div>HON. LIEZEL M. ALCOZER</div>
                            <div>HON. BOBBIT M. GUEVARRA</div>
                            <div>HON. CRISTITN M. MANDAYO</div>
                            <div>HON. JOEL C. MARASIGAN</div>
                            <div>HON. APOLINARIO C. MANARIN</div>
                            <div class="pt-3">S.K. Chairwoman</div>
                            <div>HON. BABYANN E. MANARIN</div>
                            <div class="pt-3">Barangay Secretary</div>
                            <div>MARY JOY M. MARTIREZ</div>
                            <div class="pt-3">Barangay Treasurer</div>
                            <div>AMELIA A. MALACAMAN</div>
                            <div class="pt-3">Barangay Record Keeper</div>
                            <div>LEXTER M. DOLOR</div>
                        </div>
                        <div class="col-8">
                            <div class="row">
                                <div class="col">
                                    <div>To whom it may concern,</div>
                                    <p class="pt-2" style="text-indent: 2em; text-align: justify;">This is to certify that the person whose name, picture, right thumb print and
                                        signature appears hereon, has requested a <?php echo $documentName ?> from this office with the following
                                        information:</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-9">
                                    <div>
                                        <p><strong>Name:</strong> <?php echo $firstName . " " . $middleName . " " . $lastName ?></p>
                                        <p><strong>Address:</strong> <?php echo $streetName . " " . $barangayName . ", " . $cityName . ", " . $provinceName ?></p>
                                        <p><strong>Permanent Address:</strong> <?php echo $streetName . " " . $barangayName . ", " . $cityName . ", " . $provinceName ?></p>
                                        <p><strong>Place of Birth:</strong> <?php echo $streetName . " " . $barangayName . ", " . $cityName . ", " . $provinceName ?></p>
                                        <p><strong>Date of Birth:</strong> <?php echo date("F j, Y", strtotime($birthDate)); ?></p>
                                        <p><strong>Age:</strong> 
                                            <?php 
                                                $birthDateObj = new DateTime($birthDate);
                                                $today = new DateTime();
                                                $age = $today->diff($birthDateObj)->y;
                                                echo $age . " years old"; 
                                            ?>
                                        </p>
                                        <p><strong>Gender: </strong><?php echo $gender ?></p>
                                        <p><strong>Civil Status: </strong>Single</p>
                                        <p><strong>Length of Stay: </strong>20 years</p>
                                        <p><strong>Type of Residency: </strong><?php echo $residencyType ?></p>
                                        <p><strong>Landlord: </strong></p>
                                        <p><strong>Company: </strong></p>
                                        <div class="d-flex">
                                            <p><strong>Purpose:</strong></p>
                                            <select class="form-select form-select-sm ms-2 purposeSelect" name="purpose" aria-label="Purpose Select">
                                                <option selected>Choose Purpose</option>
                                                <option value="Employment">Employment</option>
                                                <option value="Job Requirement / Local Employment">Job Requirement / Local Employment</option>
                                                <option value="Overseas Employment (OFW)">Overseas Employment (OFW)</option>
                                                <option value="School Requirement / Enrollment">School Requirement / Enrollment</option>
                                                <option value="Scholarship Application">Scholarship Application</option>
                                                <option value="Medical Assistance">Medical Assistance</option>
                                                <option value="Hospital Requirement">Hospital Requirement</option>
                                                <option value="Legal Requirement / Court Use">Legal Requirement / Court Use</option>
                                                <option value="NBI / Police Clearance">NBI / Police Clearance</option>
                                                <option value="Passport Application / Renewal">Passport Application / Renewal</option>
                                                <option value="Driver’s License">Driver’s License</option>
                                                <option value="Loan Application">Loan Application</option>
                                            </select>
                                        </div>
                                        <p><strong>Remarks:</strong> No Derogatory Record</p>
                                        <p><strong>Issued On:</strong> <?php echo date("F j, Y", time()); ?></p>
                                        <p><strong>Issued At:</strong> Barangay San Antonio, Santo Tomas City, Batangas</p>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <img src="../assets/images/<?php echo $profilePicture ?>" class="bg-secondary align-items-center" style="width: 100%; height: 150px" alt="">
                                    <img src="../assets/images/<?php echo $profilePicture ?>" class="bg-secondary text-center mt-2" style="width: 100%; height: 150px;" alt="">
                                    <div class="signature-label mt-5 text-center border-top">Signature</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row border-top border-bottom">
                        <div class="col">
                            <div class="row">
                                <div class="col">
                                    <div class="h5 mt-3 text-center fst-italic">
                                        "MAKABAGONG PUTOL, MAKIKINABANG ALL"
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-4 text-start d-flex align-items-end">
                                    NOTE: Not valid without dry seal.
                                </div>
                                <div class="col-4">
                                    <div class="d-flex justify-content-center">
                                        <img src="../assets/images/aksyonBilis.png" class="me-2" style="width: 100%; height: 150px;" alt="">
                                        <img src="../assets/images/tet.png" style="width: 100%; height: 150px;" alt="">
                                    </div>
                                </div>
                                <div class="col-4 d-flex justify-content-end align-items-end">
                                    <div class="text-center">
                                        <strong>HON. ULYSES "TETET" M. RELLAMA</strong><br>
                                        Barangay Chairman
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <a href="../documents.php">
                            <button class="btn btn-secondary cancelButton me-2" type="button">Cancel</button>
                        </a>
                        <button class="btn btn-primary submitButton" id="submitButton" type="submit" name="submit">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>

</body>

</html>
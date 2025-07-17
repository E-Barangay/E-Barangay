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
  $streetName = $userRow['streetName'];
  $barangayName = $userRow['barangayName'];
  $cityName = $userRow['cityName'];
  $provinceName = $userRow['provinceName'];

$documentQuery = "SELECT * FROM documentTypes WHERE documentTypeID = $documentTypeID";
$documentResult = executeQuery($documentQuery);

$documentRow = mysqli_fetch_assoc($documentResult);

  $documentName = $documentRow['documentName'];

if(isset($_POST['submit'])){
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
  <title>E-Barangay | Document</title>

  <!-- Icon -->
  <link rel="icon" href="../assets/images/logoSanAntonio.png">

  <!-- Style Sheets -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
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
  </style>

</head>

<body data-bs-theme="dark">

  <div class="container py-4">
    <form method="POST">
    <div class="row justify-content-center align-items-center pb-4">
      <div class="col-4 d-none d-sm-block text-end">
        <img src="../assets/images/logoSanAntonio.png" alt="Logo 1" style=" height: 150px;">
      </div>
      <div class="col-lg-4 col-12 text-center">
        <div>Republic of the Philippines</div>
        <div>Province of Batangas</div>
        <div>City of Sto. Tomas</div>
        <div>Barangay San Antonio</div>
        <div>Telefax: (043) 784-3812</div>
      </div>
      <div class="col-4 d-none d-sm-block text-start">
        <img src="../assets/images/logoSantoTomas.png" alt="Logo 1" class="img-fluid" style="height: 150px;">
      </div>
    </div>

    <div class="row border-top border-bottom">
      <div class="col text-center py-4">
        <div class="h3 m-0"><?php echo $documentName ?></div>
      </div>
    </div>

    <div class="row py-3">
      <div class="col-3 border text-center py-3">
        <div>Barangay Chairman</div>
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
      <div class="col-8 py-3">
        <div class="row">
          <div class="col-9">
            <div>To whom it may concern,</div>
            <div>
              <p class="pt-2" style="text-indent: 2em; text-align: justify;">This is to certify that the person whose name, picture, right thumb print and
                signature appears hereon, has requested a Barangay Clearance from this office with the following
                information:</p>
            </div>
            <div>
              <p><strong>Name:</strong> <?php echo $firstName . " " . $middleName . " " . $lastName ?></p>
              <p><strong>Address:</strong> <?php echo $streetName . " " . $barangayName . " , " . $cityName . " , " . $provinceName ?></p>
              <p><strong>Permanent Address:</strong> <?php echo $streetName . " " . $barangayName . " , " . $cityName . " , " . $provinceName ?></p>
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
              <p><strong>Purpose:</strong></p>
              <input name="purpose" type="text" class="form-control">
            </div>
          </div>
          <div class="col-3">
            <img src="../assets/images/<?php echo $profilePicture ?>" class="bg-secondary" style="width: 200px; height: 200px;" alt="">
            <!-- <div class="signature-label mt-auto">Signature</div> -->
          </div>
        </div>
      </div>
    </div>

    <div class="row border-top">
      <div class="col">
        <div class="mt-4 text-center fst-italic">
          "MAKABAGONG PUTOL, MAKIKINABANG ALL"
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col d-flex align-items-end justify-content-center">
        <div class="note mt-4 text-start">
          NOTE: Not valid without dry seal.
        </div>
        <div class="text-center mt-2 d-flex justify-content-center">
          <img src="../assets/images/aksyonBilis.png" class="me-2" style="width: 100%; height: 100px;" alt="">
          <img src="../assets/images/tet.png" style="width: 100%; height: 100px;" alt="">
        </div>

        <div class="text-end">
          <strong>HON. ULYSES "TETET" M. RELLAMA</strong><br>
          Barangay Chairman
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-end my-3">
        <a href="../documents.php">
            <button class="btn btn-secondary cancelButton me-2" type="button">Cancel</button>
        </a>
        <button class="btn btn-primary submitButton" id="submitButton" type="submit" name="submit">Submit</button>
    </div>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
    crossorigin="anonymous"></script>

</body>

</html>
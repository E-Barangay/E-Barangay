<?php

if (isset($_POST['logOut'])) {
    session_destroy();
    header("location: index.php");
}

$firstName = "User"; // default fallback
if (isset($_SESSION['userID'])) {
    $userID = $_SESSION['userID'];

    // Query to get the first name from your userInfo table
    $query = "SELECT firstName FROM userinfo WHERE userInfoID = '$userID'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $firstName = $row['firstName'];
    }
}
?>

<nav class="navbar navbar-expand-lg">
    <div class="container navbarStyling p-1">

        <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
            <img class="sanAntonioLogo" src="assets/images/logoSanAntonio.png" alt="San Antonio Logo">
            <img class="santoTomasLogo" src="assets/images/logoSantoTomas.png" alt="Santo Tomas Logo">
            <span class="barangayName d-none d-md-block">Barangay San Antonio</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto d-flex align-items-center">
                <li class="nav-item mx-1">
                    <a class="nav-link active1" href="index.php">Home</a>
                </li>
                <li class="nav-item mx-1">
                    <a class="nav-link active2" href="documents.php">Documents</a>
                </li>
                <li class="nav-item mx-1">
                    <a class="nav-link active3" href="reports.php">Reports</a>
                </li>
                <li class="nav-item mx-1 dropdown">
                    <a class="nav-link active4 text-center dropdown-toggle" href="#" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Hi, <?php echo $firstName ?> <i class="fa-solid fa-user"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.php"><i class="fa-solid fa-user dropdown-icon"></i>
                                Profile</a></li>
                        <li><button class="dropdown-item"><i class="fa-solid fa-sun dropdown-icon"></i> Light
                                Mode</button></li>
                        <form action="" method="POST">
                            <li>
                                <button class="dropdown-item" type="submit" name="logOut">
                                    <i class="fa-solid fa-right-from-bracket dropdown-icon"></i> Log-out
                                </button>
                            </li>
                        </form>
                    </ul>
                </li>
            </ul>
        </div>

    </div>
</nav>
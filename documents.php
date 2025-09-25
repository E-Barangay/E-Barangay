<?php

include("sharedAssets/connect.php");

session_start();

$content = "allDocuments";

if (isset($_GET['content'])) {
    $content = $_GET['content'];
    switch ($content) {
        case "allDocuments":
            if (!isset($_SESSION['userID'])) {
                header("Location: signIn.php");
            }
            break;
        case "barangayHallDocuments":
            $content = "barangayHallDocuments";
            break;
        case "mioDocuments":
            $content = "mioDocuments";
            break;
        case "barangayHealthDocuments":
            $content = "barangayHealthDocuments";
            break;
        case "documentRequest":
            $content = "documentRequest";
            break;
        default:
            header("Location: ?content=allDocuments");
            break;
    }
} else {
    header("Location: ?content=allDocuments");
}

$userQuery = "SELECT * FROM users LEFT JOIN userInfo ON users.userID = userInfo.userID";
$userResult = executeQuery($userQuery);

$searchTerm = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$allDocumentsQuery = "SELECT * FROM documentTypes";
$barangayHallDocumentsQuery = "SELECT * FROM documentTypes WHERE categoryID = 1";
$mioDocumentsQuery = "SELECT * FROM documentTypes WHERE categoryID = 2";

if (!empty($searchTerm)) {
    $allDocumentsQuery .= " WHERE documentName LIKE '%$searchTerm%'";
    $barangayHallDocumentsQuery .= " AND documentName LIKE '%$searchTerm%'";
    $mioDocumentsQuery .= " AND documentName LIKE '%$searchTerm%'";
}

$allDocumentsResult = executeQuery($allDocumentsQuery);
$barangayHallDocumentsResult = executeQuery($barangayHallDocumentsQuery);
$mioDocumentsResult = executeQuery($mioDocumentsQuery);

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Barangay | Documents</title>

    <!-- Icon -->
    <link rel="icon" href="assets/images/logoSanAntonio.png">

    <!-- Style Sheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/navbar/style.css">
    <link rel="stylesheet" href="assets/css/documents/style.css">
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

    <div class="container pt-3">
        <div class="row">
            <div class="col-12 col-lg-3 p-0">

                <!-- Pop up when screen is in small size -->

                <form method="GET">

                    <!-- Search Bar -->
                    <div class="searchBarPop position-relative d-block d-sm-none mb-4 mx-1">
                        <input type="hidden" name="content" value="<?php echo $content; ?>">
                        <input class="form-control rounded-pill ps-5" name="search" type="search" placeholder="Search Documents" value="<?php echo $searchTerm ?>">
                        <i class="fa-solid fa-magnifying-glass searchIcon text-muted"></i>
                    </div>

                    <!-- Filter Button -->
                    <div class="d-block d-sm-none dropdown">
                        <button class="filterButtonPop dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-filter"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item <?php echo ($content == 'allDocuments') ? 'active' : ''; ?>" href="?content=allDocuments">All Documents</a></li>
                            <li><a class="dropdown-item <?php echo ($content == 'barangayHallDocuments') ? 'active' : ''; ?>" href="?content=barangayHallDocuments">Barangay Hall</a></li>
                            <li><a class="dropdown-item <?php echo ($content == 'mioDocuments') ? 'active' : ''; ?>" href="?content=mioDocuments">Migrant Information Office</a></li>
                            <li><a class="dropdown-item <?php echo ($content == 'documentRequest') ? 'active' : ''; ?>" href="?content=documentRequest">Submitted Request</a></li>
                        </ul>
                    </div>

                </form>

                <!-- Pop up when screen is in big size -->

                <form method="GET">

                    <div class="filterCard card m-1 p-2 d-none d-sm-block">

                        <div class="row">
                            <div class="col m-2">
                                <div class="position-relative">

                                    <!-- Search Bar -->
                                    <input type="hidden" name="content" value="<?php echo $content; ?>">
                                    <input class="form-control rounded-pill ps-5" name="search" type="search" placeholder="Search Documents" value="<?php echo $searchTerm ?>">
                                    <i class="fa-solid fa-magnifying-glass searchIcon text-muted"></i>
                                    
                                </div>
                            </div>
                        </div>

                        <div class="row d-flex flex-column mt-4">

                            <!-- Filter -->
                            <div class="col d-flex flex-column justify-content-center">
                                <a href="?content=allDocuments" class="btn btn-primary filterButton m-2 <?php echo ($content == 'allDocuments') ? 'active' : ''; ?>">All Documents</a>
                                <a href="?content=documentRequest" class="btn btn-primary filterButton m-2 <?php echo ($content == 'barangayHallDocuments') ? 'active' : ''; ?>">Submitted Request</a>
                            </div>

                        </div>
                    </div>

                </form>
            </div>

            <div class="col-12 col-lg-9 p-0">
                <div class="contentCard card m-1 p-2">
                    <div class="row px-3 py-2" id="scrollable" style="max-height: 100vh; overflow-y: auto;">

                        <?php include("documentContent/" . $content . ".php"); ?>

                    </div>
                </div>
            </div>
            
        </div>
    </div>

    <?php include("sharedAssets/footer.php") ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    
</body>

</html>
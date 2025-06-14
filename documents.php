<?php 

$content = "allDocuments";

if (isset($_GET['content'])) {
    $content = $_GET['content'];
    switch ($content) {
        case "allDocuments":
            $content = "allDocuments";
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

include ("sharedAssets/connect.php");

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

<body data-bs-theme="dark">
    <?php include("sharedAssets/navbar.php") ?>
    
    <div class="container pt-3">
        <div class="row">
            <div class="col-12 col-lg-3 p-0">

                <!-- Pop Up when screen is in small size -->

                    <!-- Search Bar -->
                    <div class="searchBarPop position-relative d-block d-sm-none mb-4 mx-1">
                        <input class="form-control rounded-pill ps-5" type="search" placeholder="Search Documents">
                        <i class="fa-solid fa-magnifying-glass searchIcon text-muted"></i>
                    </div>

                    <!-- Filter Button -->
                    <div class="d-block d-sm-none">
                        <button class="filterButtonPop"><i class="fa-solid fa-filter"></i></button>
                    </div>

                <div class="filterCard card m-1 p-2 d-none d-sm-block">
                    <div class="row">
                        <div class="col m-2">
                            <div class="position-relative">
                                <input class="form-control rounded-pill ps-5" type="search" placeholder="Search Documents">
                                <i class="fa-solid fa-magnifying-glass searchIcon text-muted"></i>
                            </div>
                        </div>
                    </div>
                    <div class="row d-flex flex-column mt-4">

                        <!-- Filter -->
                        <div class="col d-flex flex-column justify-content-center">
                            <a href="?content=allDocuments" class="btn btn-primary filterButton m-2">All Documents</a>
                            <a href="?content=barangayHallDocuments" class="btn btn-primary filterButton m-2">Barangay Hall</a>
                            <a href="?content=mioDocuments" class="btn btn-primary filterButton m-2">Migrant Information Office</a>
                            <a href="?content=barangayHealthDocuments" class="btn btn-primary filterButton m-2">Barangay Health</a>
                        </div>

                        <!-- Document Request -->
                        <div class="col d-flex justify-content-center">
                            <a href="?content=documentRequest" class="btn btn-primary documentRequestButton activeButton5 m-2 mt-5">Document Request</a>
                        </div>

                    </div>
                </div>
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
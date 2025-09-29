<?php

include("sharedAssets/connect.php");

session_start();

$content = "allDocuments";

if (isset($_GET['content'])) {
    $content = $_GET['content'];
    switch ($content) {
        case "allDocuments":
            if (!isset($_SESSION['userID'])) {
                header("Location: login.php");
            }
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

$documentsQuery = "SELECT * FROM documentTypes WHERE categoryID = 1";

if (!empty($searchTerm)) {
    $documentsQuery .= " WHERE documentName LIKE '%$searchTerm%'";
}

$documentsResult = executeQuery($documentsQuery);

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

                <form method="GET">

                    <div class="filterCard card m-1 p-2">

                        <div class="row">
                            <div class="col m-2">
                                <div class="position-relative">

                                    <input type="hidden" name="content" value="<?php echo $content; ?>">
                                    <input class="form-control rounded-pill ps-5" name="search" type="search" placeholder="Search Documents" value="<?php echo $searchTerm ?>">
                                    <i class="fa-solid fa-magnifying-glass searchIcon text-muted"></i>
                                    
                                </div>
                            </div>
                        </div>

                        <div class="row d-flex flex-column mt-4">

                            <div class="col d-flex flex-column justify-content-center">
                                <a href="?content=allDocuments" class="btn btn-primary filterButton m-2 <?php echo ($content == 'allDocuments') ? 'active' : ''; ?>">All Documents</a>
                                <a href="?content=documentRequest" class="btn btn-primary filterButton m-2 <?php echo ($content == 'documentRequest') ? 'active' : ''; ?>">Submitted Request</a>
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

    <div class="modal fade" id="businessClearanceModal" tabindex="-1" aria-labelledby="businessClearanceLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header" style="background-color: #19AFA5; color: white;">
                    <h1 class="modal-title fs-5" id="businessClearanceLabel">Business Clearance</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary confirmButton" name="confirmButton">
                        Confirm Request
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="barangayClearanceModal" tabindex="-1" aria-labelledby="barangayClearanceLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header" style="background-color: #19AFA5; color: white;">
                    <h1 class="modal-title fs-5" id="barangayClearanceLabel">Barangay Clearance</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary confirmButton" name="confirmButton">
                        Confirm Request
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="constructionClearanceModal" tabindex="-1" aria-labelledby="constructionClearanceLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header" style="background-color: #19AFA5; color: white;">
                    <h1 class="modal-title fs-5" id="constructionClearanceLabel">Construction Clearance</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary confirmButton" name="confirmButton">
                        Confirm Request
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="firstTimeJobSeekerModal" tabindex="-1" aria-labelledby="firstTimeJobSeekerLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header" style="background-color: #19AFA5; color: white;">
                    <h1 class="modal-title fs-5" id="firstTimeJobSeekerLabel">First Time Job Seeker</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary confirmButton" name="confirmButton">
                        Confirm Request
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="goodHealthModal" tabindex="-1" aria-labelledby="goodHealthLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header" style="background-color: #19AFA5; color: white;">
                    <h1 class="modal-title fs-5" id="goodHealthLabel">Good Health</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary confirmButton" name="confirmButton">
                        Confirm Request
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="goodMoralModal" tabindex="-1" aria-labelledby="goodMoralLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header" style="background-color: #19AFA5; color: white;">
                    <h1 class="modal-title fs-5" id="goodMoralLabel">Good Moral</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary confirmButton" name="confirmButton">
                        Confirm Request
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="jointCohabitationModal" tabindex="-1" aria-labelledby="jointCohabitationLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header" style="background-color: #19AFA5; color: white;">
                    <h1 class="modal-title fs-5" id="jointCohabitationLabel">Joint Cohabitation</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary confirmButton" name="confirmButton">
                        Confirm Request
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="residencyModal" tabindex="-1" aria-labelledby="residencyLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header" style="background-color: #19AFA5; color: white;">
                    <h1 class="modal-title fs-5" id="residencyLabel">Residency</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary confirmButton" name="confirmButton">
                        Confirm Request
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="soloParentModal" tabindex="-1" aria-labelledby="soloParentLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header" style="background-color: #19AFA5; color: white;">
                    <h1 class="modal-title fs-5" id="soloParentLabel">Solo Parent</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary confirmButton" name="confirmButton">
                        Confirm Request
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <?php include("sharedAssets/footer.php") ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    
</body>

</html>
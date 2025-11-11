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

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
} else {
    $userID = $_SESSION['userID'];
}

$userQuery = "SELECT * FROM users 
            LEFT JOIN userInfo ON users.userID = userInfo.userID 
            LEFT JOIN addresses ON userInfo.userInfoID = addresses.userInfoID  
            LEFT JOIN permanentAddresses ON userInfo.userInfoID = permanentAddresses.userInfoID
            WHERE users.userID = $userID";
$userResult = executeQuery($userQuery);

$userDataRow = mysqli_fetch_assoc($userResult);
$firstName = $userDataRow['firstName'];
$middleName = $userDataRow['middleName'];
$lastName = $userDataRow['lastName'];
$fullName = $firstName . " " . ($middleName ? $middleName[0] . ". " : "") . $lastName;
$profilePicture = $userDataRow['profilePicture'];

function formatAddress($value) {
    return ucwords(strtolower($value));
}

$blockLotNo = $userDataRow['blockLotNo'];
$phase = $userDataRow['phase']; 
$subdivisionName = $userDataRow['subdivisionName'];
$purok = $userDataRow['purok'];
$streetName = $userDataRow['streetName'];
$barangayName = formatAddress($userDataRow['barangayName']);
$cityName = formatAddress($userDataRow['cityName']);
$provinceName = formatAddress($userDataRow['provinceName']);

$permanentBlockLotNo = $userDataRow['permanentBlockLotNo'];
$permanentPhase = $userDataRow['permanentPhase']; 
$permanentSubdivisionName = $userDataRow['permanentSubdivisionName'];
$permanentPurok = $userDataRow['permanentPurok'];
$permanentStreetName = $userDataRow['permanentStreetName'];
$permanentBarangayName = formatAddress($userDataRow['permanentBarangayName']);
$permanentCityName = formatAddress($userDataRow['permanentCityName']);
$permanentProvinceName = formatAddress($userDataRow['permanentProvinceName']);

$searchTerm = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$documentsQuery = "SELECT * FROM documentTypes";

if (!empty($searchTerm)) {
    $documentsQuery .= " WHERE documentName LIKE '%$searchTerm%'";
}

$documentsResult = executeQuery($documentsQuery);

$searchDisplay = !empty($searchTerm) ? htmlspecialchars($searchTerm) : 'document';

$isProfileComplete = !(
    empty($firstName)
    || empty($lastName)
    || empty($profilePicture)
    || empty($gender)
    || empty($birthDate)
    || empty($birthPlace)
    || empty($civilStatus)
    || empty($citizenship)
    || empty($lengthOfStay)
    || empty($residencyType)
    || empty($phoneNumber)
    || empty($email)
    || empty($purok)
    || empty($barangayName)
    || empty($cityName)
    || empty($provinceName)
    || empty($permanentPurok)
    || empty($permanentBarangayName)
    || empty($permanentCityName)
    || empty($permanentProvinceName)
);

if (isset($_POST['documentButton'])) {
    if (!$isProfileComplete) {
        $_SESSION['warning'] = 'incompleteInformation1';
        header("Location: profile.php");
        exit;
    }
}

if (isset($_POST['proceedButton'])) {
    $documentTypeID = $_POST['documentTypeID'] ?? '';

    $_SESSION['purpose'] = $_POST['purpose'] ?? '';
    $_SESSION['businessName'] = $_POST['businessName'] ?? '';
    $_SESSION['businessAddress'] = $_POST['businessAddress'] ?? '';
    $_SESSION['businessNature'] = $_POST['businessNature'] ?? '';
    $_SESSION['controlNo'] = $_POST['controlNo'] ?? '';
    $_SESSION['ownership'] = $_POST['ownership'] ?? '';
    $_SESSION['spouseName'] = $_POST['spouseName'] ?? '';
    $_SESSION['marriageYear'] = $_POST['marriageYear'] ?? '';
    $_SESSION['childNo'] = $_POST['childNo'] ?? '';
    $_SESSION['soloParentSinceDate'] = $_POST['soloParentSinceDate'] ?? '';
    
    header("Location: documents/viewDocument.php?documentTypeID=$documentTypeID");
    exit();
}

if (isset($_POST['confirmCancelButton'])) {
    $documentID = $_POST['documentID'] ?? '';

    $cancelRequestQuery = "UPDATE documents SET documentStatus = 'Cancelled', cancelledDate = NOW() WHERE documentID = $documentID";
    $cancelRequestResult = executeQuery($cancelRequestQuery);
    
    header("Location: documents.php?content=documentRequest");
    exit();
} elseif (isset($_POST['restoreRequestButton'])) {
    $documentID = $_POST['documentID'] ?? '';

    $cancelRequestQuery = "UPDATE documents SET documentStatus = 'Pending', cancelledDate = NULL WHERE documentID = $documentID";
    $cancelRequestResult = executeQuery($cancelRequestQuery);
    
    header("Location: documents.php?content=documentRequest");
    exit();
}

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

                <?php if (isset($_SESSION['success']) && $_SESSION['success'] === 'requestConfirmed'): ?>
                    <div class="alert alert-success text-center mb-4">Your request for <?php echo htmlspecialchars($_SESSION['documentName']); ?> has been submitted successfully!</div>
                    <?php unset($_SESSION['success']); unset($_SESSION['documentName']); ?>
                <?php endif; ?>

                <div class="col-12 col-lg-3 p-0">

                    <form method="GET">

                        <div class="filterCard card m-1 p-2">

                            <div class="row">
                                <div class="col m-2">
                                    <div class="position-relative">

                                        <input type="hidden" name="content" value="<?php echo $content; ?>">
                                        <input class="form-control rounded-pill ps-5" name="search" type="search" id="searchInput" placeholder="Search Documents" value="<?php echo $searchTerm ?>">
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
                    <div class="contentCard card m-1 p-2" id="contentCard">
                        <div class="row px-3 py-2" id="scrollable">

                            <?php include("contents/documentContent/" . $content . ".php"); ?>

                        </div>
                    </div>
                </div>
                
            </div>
        </div>

    </form>
    
    <?php include("sharedAssets/footer.php") ?>

    <script>
        const search = document.getElementById('searchInput');
        const container = document.getElementById('contentCard');

        search.addEventListener('keyup', () => {
            const term = search.value.trim();
            const url = term
                ? 'documents.php?content=allDocuments&search=' + encodeURIComponent(term)
                : 'documents.php?content=allDocuments';

            fetch(url)
                .then(r => r.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.querySelector('#contentCard').innerHTML;
                    container.innerHTML = newContent;
                });
        });

        var documentPage = 1;
        var documentsPerPage = <?php echo $limit; ?>;
        var totalDocumentPages = <?php echo $pages; ?>;

        function goToDocumentPage(pageNumber) {
            if (pageNumber >= 1 && pageNumber <= totalDocumentPages) {
                documentPage = pageNumber;
                updateDocumentPage();
            }
        }

        function nextDocumentPage() {
            if (documentPage < totalDocumentPages) {
                documentPage += 1;
                updateDocumentPage();
            }
        }

        function previousDocumentPage() {
            if (documentPage > 1) {
                documentPage -= 1;
                updateDocumentPage();
            }
        }

        function updateDocumentPage() {
            var allDocumentRows = document.querySelectorAll('tbody tr');
            var startIndex = (documentPage - 1) * documentsPerPage;
            var endIndex = documentPage * documentsPerPage;

            for (var i = 0; i < allDocumentRows.length; i++) {
                allDocumentRows[i].style.display = 'none';
            }

            for (var i = startIndex; i < endIndex && i < allDocumentRows.length; i++) {
                allDocumentRows[i].style.display = '';
            }

            updateDocumentPagination();
        }

        function updateDocumentPagination() {
            var paginationItems = document.getElementById('documentPagination').getElementsByClassName('page-item');

            for (var i = 0; i < paginationItems.length - 2; i++) {
                var pageNum = i + 1;
                if (pageNum === documentPage) {
                    paginationItems[i + 1].classList.add('active');
                } else {
                    paginationItems[i + 1].classList.remove('active');
                }
            }

            if (documentPage === 1) {
                paginationItems[0].classList.add('disabled');
            } else {
                paginationItems[0].classList.remove('disabled');
            }

            if (documentPage === totalDocumentPages) {
                paginationItems[paginationItems.length - 1].classList.add('disabled');
            } else {
                paginationItems[paginationItems.length - 1].classList.remove('disabled');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            updateDocumentPage();
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    
</body>

</html>
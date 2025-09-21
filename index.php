<?php

include("sharedAssets/connect.php");

session_start();

$newAnnouncementQuery = "SELECT * FROM announcements WHERE dateTime >= NOW() - INTERVAL 7 DAY ORDER BY dateTime DESC";
$newAnnouncementResult = executeQuery($newAnnouncementQuery);

$recentAnnouncementQuery = "SELECT * FROM announcements WHERE dateTime < NOW() - INTERVAL 7 DAY ORDER BY dateTime DESC";
$recentAnnouncementResult = executeQuery($recentAnnouncementQuery);

$totalRecentCardQuery = "SELECT COUNT(*) as total FROM announcements WHERE dateTime < NOW() - INTERVAL 7 DAY";
$totalRecentCardResult = executeQuery($totalRecentCardQuery);

$totalRecentRow = mysqli_fetch_assoc($totalRecentCardResult);
$totalRecent = $totalRecentRow['total'];

$cardsPerPage = 6;
$totalPages = ceil($totalRecent / $cardsPerPage);

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Barangay | Home</title>

    <!-- Icon -->
    <link rel="icon" href="assets/images/logoSanAntonio.png">

    <!-- Style Sheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/navbar/style.css">
    <link rel="stylesheet" href="assets/css/index/style.css">
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

    <div class="container-fluid p-0 pt-4 overflow-hidden">
        <div class="row">
            <div class="col p-0">
                <div class="card bannerCard">
                    <img src="assets/images/banner.jpeg" class="bannerImage" alt="Banner Image">
                    <div class="bannerContent">
                        <div class="header">
                            Barangay San Antonio <br> E-Services
                        </div>
                        <div class="tagline">
                            Makabagong Putol, Makikinabang All
                        </div>
                        <div class="d-flex justify-content-center m-4">
                            <a class="btn btn-primary viewButton p-2" href='documents.php'>View Our Services</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container pt-5">

        <div class="row">
            <div class="col">
                <div class="announcements">
                    Announcements
                </div>
            </div>
        </div>

        <div class="row pt-4">
            <div class="col">
                <div class="whatIsNew">
                    What's New
                </div>
            </div>
        </div>

        <div class="row pb-3 pt-4">

            <?php if (mysqli_num_rows($newAnnouncementResult) == 0) { ?>

                <div class="col">
                    <div class="noNewAnnouncements text-muted">
                        No new announcements.
                    </div>
                </div>

            <?php } else { ?>

                <?php while($newAnnouncementRow = mysqli_fetch_assoc($newAnnouncementResult)) { ?>

                    <div class="col-lg-4 col-md-6 col-12 pb-4">
                        <div class="card newCard">
                            <div class="row">
                                <div class="col d-flex flex-row align-items-center px-4 py-3">
                                    <img src="assets/images/logoSanAntonio.png" class="logo" alt="Logo">
                                    <div class="d-flex flex-column justify-content-center ps-2">
                                        <span class="barangay">Barangay San Antonio</span>
                                        <span class="date"><?php echo date("F d, Y", strtotime($newAnnouncementRow['dateTime'])); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <img src="assets/images/announcements/<?php echo $newAnnouncementRow['image']; ?>" class="newAnnouncementImage" alt="New Announcement Image">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col px-4 pt-3">
                                    <span class="title"><?php echo $newAnnouncementRow['title']; ?></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col px-4 pt-2 pb-3">
                                    <p class="description m-0">
                                        <?php echo $newAnnouncementRow['description']; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php } ?>

            <?php } ?>

            
        </div>

        <div class="row pt-1">
            <div class="col">
                <div class="recents">
                    Recents
                </div>
            </div>
        </div>

        <div class="row pt-3">
            
                <?php while($recentAnnouncementRow = mysqli_fetch_assoc($recentAnnouncementResult)) { ?>

                    <div class="col-lg-4 col-md-6 col-12 pb-4 d-flex align-items-center">
                        <div class="card recentCard">
                            <div class="row">
                                <div class="col">
                                    <img src="assets/images/announcements/<?php echo $recentAnnouncementRow['image'] ?>" class="recentAnnouncementImage" alt="Recent Announcement Image">
                                </div>
                            </div>
                            <div class="row px-3 pt-3">
                                <div class="col d-flex flex-row align-items-center">
                                    <img src="assets/images/logoSanAntonio.png" class="logo" alt="Logo">
                                    <div class="d-flex flex-column ps-2">
                                        <span class="barangay">Barangay San Antonio</span>
                                        <span class="date text-start">
                                            <?php echo date("F d, Y", strtotime($recentAnnouncementRow['dateTime'])); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row px-3 py-3">
                                <div class="col">
                                    <span class="title"><?php echo $recentAnnouncementRow['title'] ?></span>
                                </div>
                            </div>
                            <div class="row px-3 pb-3">
                                <div class="col">
                                    <input type="hidden" value="<?php echo $recentAnnouncementRow['announcementID']; ?>">
                                    <button class="btn btn-primary viewDetailsButton" type="button" data-bs-toggle="modal" data-bs-target="#<?php echo $recentAnnouncementRow['announcementID']; ?>">View More Details</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="<?php echo $recentAnnouncementRow['announcementID']; ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="postModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body p-0"> 
                                    <div class="row g-0">
                                        <div class="col-lg-7 col-12 d-flex align-items-center justify-content-center">
                                            <img src="assets/images/announcements/<?php echo $recentAnnouncementRow['image'] ?>" class="modalRecentAnnouncementImage" alt="Recent Announcement Image">
                                        </div>
                                        <div class="col-lg-5 col-12 d-flex flex-column">
                                            <div class="row px-3 pt-3">
                                                <div class="col d-flex flex-row align-items-center ">
                                                    <img src="assets/images/logoSanAntonio.png" class="modalLogo" alt="Logo">
                                                    <div class="d-flex flex-column ps-2">
                                                        <span class="modalBarangay">Barangay San Antonio</span>
                                                        <span class="modalDate text-start">
                                                            <?php echo date("F d, Y", strtotime($recentAnnouncementRow['dateTime'])); ?>
                                                        </span>
                                                    </div>
                                                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                            </div>
                                            <div class="row px-3 py-3">
                                                <div class="col">
                                                    <span class="modalTitle"><?php echo $recentAnnouncementRow['title'] ?></span>
                                                </div>
                                            </div>
                                            <div class="row px-3 pb-3">
                                                <div class="col">
                                                    <span class="modalDescription"><?php echo $recentAnnouncementRow['description'] ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php } ?>

            </div>

            <div class="row mt-3">
                <div class="col d-flex justify-content-center align-items-center">

                    <nav aria-label="pageNavigation">

                        <ul class="pagination" id="pagination">

                            <li class="page-item" onclick="previousPage()">
                                <a class="page-link" aria-label="Previous">Previous</a>
                            </li>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item">
                                    <a class="page-link" href="javascript:void(0)" onclick="goToPage(<?php echo $i; ?>)">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item" onclick="nextPage()">
                                <a class="page-link" aria-label="Next">Next</a>
                            </li>

                        </ul>

                    </nav>

                </div>
            </div>

        </div>
    </div>

    <?php include("sharedAssets/footer.php") ?>

    <script>
        
        var page = 1;
        var cardsPerPage = <?php echo $cardsPerPage; ?>;
        var totalPages = <?php echo $totalPages; ?>;

        function goToPage(pageNumber) {
            if (pageNumber >= 1 && pageNumber <= totalPages) {
                page = pageNumber;
                updatePage();
            }
        }

        function nextPage() {
            if (page < totalPages) {
                page += 1;
                updatePage();
            }
        }

        function previousPage() {
            if (page > 1) {
                page -= 1;
                updatePage();
            }
        }

        function updatePage() {
            var allrecentCards = document.getElementsByClassName('recentCard');
            var startIndex = (page - 1) * cardsPerPage;
            var endIndex = page * cardsPerPage;

            for (var i = 0; i < allrecentCards.length; i++) {
                allrecentCards[i].style.display = 'none';
            }

            for (var i = startIndex; i < endIndex && i < allrecentCards.length; i++) {
                allrecentCards[i].style.display = 'block';
            }

            updatePagination();
        }

        function updatePagination() {
            var paginationItems = document.getElementById('pagination').getElementsByClassName('page-item');

            for (var i = 0; i < paginationItems.length - 2; i++) {
                var pageNum = i + 1;
                if (pageNum === page) {
                    paginationItems[i + 1].classList.add('active');
                } else {
                    paginationItems[i + 1].classList.remove('active');
                }
            }

            if (page === 1) {
                paginationItems[0].classList.add('disabled');
            } else {
                paginationItems[0].classList.remove('disabled');
            }

            if (page === totalPages) {
                paginationItems[paginationItems.length - 1].classList.add('disabled');
            } else {
                paginationItems[paginationItems.length - 1].classList.remove('disabled');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            updatePage();
        });

    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>

</body>

</html>
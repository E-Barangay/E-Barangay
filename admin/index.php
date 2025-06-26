<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../index.php");
  exit();
}

$page = "dashboard";
if (isset($_GET['page'])) {
  switch ($_GET['page']) {
    case "dashboard":
    case "resident":
    case "announcement":
    case "reports":
    case "document":
      $page = $_GET['page'];
      break;
    default:
      header("Location: ?page=dashboard");
      exit();
  }
} else {
  header("Location: ?page=dashboard");
  exit();
}

include("../sharedAssets/connect.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>E-Barangay | Home</title>
  <link rel="icon" href="../assets/images/logoSanAntonio.png">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../admin/assets/css/style.css">
</head>

<body>

  <div class="container-fluid p-0">
    <div class="row g-0">

      <div class="col-12 d-lg-none">
        <nav class="navbar mobile-navbar p-3 sticky-top shadow-sm" style="background-color: #19AFA5">
          <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="#">
              <img src="../assets/images/logoSanAntonio.png" width="35" height="35" class="me-2">
              Barangay San Antonio
            </a>
            <button class="navbar-toggler p-2" type="button" data-bs-toggle="offcanvas"
              data-bs-target="#sidebarOffcanvas">
              <i class="fas fa-bars fs-4"></i>
            </button>
          </div>
        </nav>
      </div>

      <div class="col-lg-2 d-none d-lg-block">
        <div class="sidebar p-4 d-flex flex-column">
          <div class="text-center mb-4 pb-4 border-bottom border-3" style="border-color: #CCCCCC !important;">
            <img src="../assets/images/logoSanAntonio.png" alt="Logo" width="100" height="100">
            <h4 class="sidebar-title mt-3 mb-0">Barangay</h4>
            <p class="sidebar-subtitle mb-0">San Antonio</p>
          </div>

          <nav class="flex-grow-1 d-flex flex-column gap-3">
            <a href="?page=dashboard"
              class="nav-button py-3 px-4 d-flex align-items-center justify-content-start text-decoration-none <?php echo ($page == 'dashboard') ? 'active' : ''; ?>">
              <i class="fas fa-tachometer-alt me-3" style="width: 20px;"></i>
              <span>DASHBOARD</span>
            </a>
            <a href="?page=resident"
              class="nav-button py-3 px-4 d-flex align-items-center justify-content-start text-decoration-none <?php echo ($page == 'resident') ? 'active' : ''; ?>">
              <i class="fas fa-users me-3" style="width: 20px;"></i>
              <span>RESIDENT DATA</span>
            </a>
            <a href="?page=announcement"
              class="nav-button py-3 px-4 d-flex align-items-center justify-content-start text-decoration-none <?php echo ($page == 'announcement') ? 'active' : ''; ?>">
              <i class="fas fa-bullhorn me-3" style="width: 20px;"></i>
              <span>ANNOUNCEMENT</span>
            </a>
            <a href="?page=reports"
              class="nav-button py-3 px-4 d-flex align-items-center justify-content-start text-decoration-none <?php echo ($page == 'reports') ? 'active' : ''; ?>">
              <i class="fas fa-chart-bar me-3" style="width: 20px;"></i>
              <span>REPORT DATA</span>
            </a>
            <a href="?page=document"
              class="nav-button py-3 px-4 d-flex align-items-center justify-content-start text-decoration-none <?php echo ($page == 'document') ? 'active' : ''; ?>">
              <i class="fas fa-file-alt me-3" style="width: 20px;"></i>
              <span>DOCUMENT</span>
            </a>
          </nav>

          <div class="mt-auto pt-3">
            <a href="../sharedAssets/logOut.php"
              class="logout-button py-3 px-4 d-flex align-items-center justify-content-start text-decoration-none w-100"
              onclick="return confirm('Are you sure you want to logout?')">
              <i class="fas fa-sign-out-alt me-3" style="width: 20px;"></i>
              <span>LOGOUT</span>
            </a>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-10">
        <div class="main-content p-4">
          <?php include("adminContent/" . $page . ".php"); ?>
        </div>
      </div>

    </div>
  </div>

  <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarOffcanvas">
    <div class="offcanvas-header p-4">
      <div class="d-flex align-items-center">
        <img src="../assets/images/logoSanAntonio.png" alt="Logo" width="50" height="50" class="me-3">
        <div>
          <h5 class="sidebar-title mb-0">Barangay</h5>
          <small class="sidebar-subtitle">San Antonio</small>
        </div>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body p-4 d-flex flex-column">
      <nav class="flex-grow-1 d-flex flex-column gap-3">
        <a href="?page=dashboard"
          class="nav-button py-3 px-4 d-flex align-items-center justify-content-start text-decoration-none <?php echo ($page == 'dashboard') ? 'active' : ''; ?>"
          data-bs-dismiss="offcanvas">
          <i class="fas fa-tachometer-alt me-3" style="width: 20px;"></i>
          <span>DASHBOARD</span>
        </a>
        <a href="?page=resident"
          class="nav-button py-3 px-4 d-flex align-items-center justify-content-start text-decoration-none <?php echo ($page == 'resident') ? 'active' : ''; ?>"
          data-bs-dismiss="offcanvas">
          <i class="fas fa-users me-3" style="width: 20px;"></i>
          <span>RESIDENT DATA</span>
        </a>
        <a href="?page=announcement"
          class="nav-button py-3 px-4 d-flex align-items-center justify-content-start text-decoration-none <?php echo ($page == 'announcement') ? 'active' : ''; ?>"
          data-bs-dismiss="offcanvas">
          <i class="fas fa-bullhorn me-3" style="width: 20px;"></i>
          <span>ANNOUNCEMENT</span>
        </a>
        <a href="?page=reports"
          class="nav-button py-3 px-4 d-flex align-items-center justify-content-start text-decoration-none <?php echo ($page == 'reports') ? 'active' : ''; ?>"
          data-bs-dismiss="offcanvas">
          <i class="fas fa-chart-bar me-3" style="width: 20px;"></i>
          <span>REPORT DATA</span>
        </a>
        <a href="?page=document"
          class="nav-button py-3 px-4 d-flex align-items-center justify-content-start text-decoration-none <?php echo ($page == 'document') ? 'active' : ''; ?>"
          data-bs-dismiss="offcanvas">
          <i class="fas fa-file-alt me-3" style="width: 20px;"></i>
          <span>DOCUMENT</span>
        </a>
      </nav>

      <div class="mt-auto pt-3">
        <a href="../sharedAssets/logOut.php"
          class="logout-button py-3 px-4 d-flex align-items-center justify-content-start text-decoration-none w-100"
          onclick="return confirm('Are you sure you want to logout?')">
          <i class="fas fa-sign-out-alt me-3" style="width: 20px;"></i>
          <span>LOGOUT</span>
        </a>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
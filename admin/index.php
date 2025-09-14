<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../index.php");
  exit();
}

$page = $_GET['page'] ?? 'dashboard';
$validPages = ['dashboard', 'resident', 'announcement', 'complaints', 'document', 'reports'];
if (!in_array($page, $validPages)) {
  header("Location: ?page=dashboard");
  exit();
}
include("../sharedAssets/connect.php");
?>
<!DOCTYPE html>
<html lang="en">

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="../assets/images/logoSanAntonio.png">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      background-color: #f2f6f6ff;
    }

    .wrapper {
      display: flex;
    }

    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      width: 260px;
      background-color: rgb(49, 175, 171);
      color: #fff;
      display: flex;
      flex-direction: column;
      transition: width 0.3s;
      z-index: 1040;
    }

    .sidebar.collapsed {
      width: 70px;
    }

    .sidebar .sidebar-logo {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding-top: 2rem;
      padding-bottom: 1.5rem;
      height: auto;
      text-align: center;
    }

    .sidebar .sidebar-logo img {
      transition: all 0.3s ease;
      width: auto;
      height: 100px;
      display: block;
      margin: 0 auto;
      border-radius: 50%;
      cursor: pointer;
    }

    .sidebar.collapsed .sidebar-logo img {
      height: 45px;
    }

    .sidebar-text {
      transition: 0.3s;
    }

    .sidebar.collapsed .sidebar-text {
      display: none;
    }

    .sidebar-nav {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .sidebar-nav .sidebar-item {
      padding: 0;
    }

    .sidebar-nav .sidebar-link {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      padding: 12px 20px;
      color: #fff;
      text-decoration: none;
      gap: 10px;
      transition: 0.3s;
    }

    .sidebar.collapsed .sidebar-link {
      justify-content: center;
    }

    .sidebar-nav .sidebar-link:hover,
    .sidebar-nav .sidebar-link.active {
      background-color: #e7e7e7ff;
      color: black;
    }

    .sidebar.collapsed .sidebar-link span {
      display: none;
    }

    .sidebar-link i,
    .logout-custom i {
      font-size: 1.2rem;
    }

    .logout-wrapper {
      margin-top: auto;
      padding: 1rem 1rem 2rem;
      display: flex;
      justify-content: center;
    }

    .logout-custom {
      color: #fff;
      width: 100%;
      padding: 12px 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      text-decoration: none;
      background-color: #f01a13ff;
      border-radius: 8px;
      transition: background-color 0.3s ease;
      text-align: center;
    }

    .logout-custom:hover,
    .logout-custom.active {
      background-color: #6d0b07ff;
    }

    .sidebar.collapsed .logout-custom span {
      display: none;
    }

    .sidebar.collapsed .logout-custom {
      justify-content: center;
      padding: 12px;
      width: 100%;
    }

    .main {
      margin-left: 260px;
      padding: 20px;
      width: 100%;
      transition: margin-left 0.3s;
    }

    .sidebar.collapsed~.main {
      margin-left: 70px;
    }

    .mobile-nav {
      display: none;
    }

    @media (max-width: 991px) {
      .sidebar {
        display: none;
      }

      .sidebar.active {
        display: flex;
      }

      .main {
        margin-left: 0;
      }

      .mobile-nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 15px;
        background: rgb(49, 175, 171);
        color: white;
      }

      .mobile-nav img {
        height: 40px;
      }

      .sidebar-toggler {
        font-size: 1.5rem;
        cursor: pointer;
        color: white;
      }
    }

    .table-responsive {
      overflow-x: auto;
    }
  </style>

  <div class="mobile-nav d-lg-none">
    <img src="../assets/images/logoSanAntonio.png" alt="Logo" id="toggleSidebarMobile">
    <i class="bi bi-list sidebar-toggler" id="mobileMenuToggle"></i>
  </div>

  <div class="wrapper">

    <aside class="sidebar" id="sidebar">
      <div class="sidebar-logo text-center py-3">
        <img src="../assets/images/logoSanAntonio.png" alt="Logo" id="toggleSidebar" class="mb-2">

        <div class="sidebar-text">
          <h6 class="mb-0" style="font-size: 16px; font-weight: 600;">Barangay</h6>
          <h6 style="font-size: 18px; font-weight: bold;">San Antonio</h6>
        </div>
      </div>
      <ul class="sidebar-nav">
        <li class="sidebar-item">
          <a href="?page=dashboard" class="sidebar-link <?php if ($page === 'dashboard')
            echo 'active'; ?>">
            <i class="bi bi-speedometer2"></i> <span>DASHBOARD</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a href="?page=resident" class="sidebar-link <?php if ($page === 'resident')
            echo 'active'; ?>">
            <i class="bi bi-person"></i> <span>RESIDENT</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a href="?page=announcement" class="sidebar-link <?php if ($page === 'announcement')
            echo 'active'; ?>">
            <i class="bi bi-megaphone"></i> <span>ANNOUNCEMENT</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a href="?page=complaints" class="sidebar-link <?php if ($page === 'complaints')
            echo 'active'; ?>">
            <i class="bi bi-exclamation-diamond"></i> <span>COMPLAINTS</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a href="?page=document" class="sidebar-link <?php if ($page === 'document')
            echo 'active'; ?>">
            <i class="bi bi-file-earmark-text"></i> <span>DOCUMENT</span>
          </a>
        </li>
                <li class="sidebar-item">
          <a href="?page=reports" class="sidebar-link <?php if ($page === 'reports')
            echo 'active'; ?>">
            <i class="bi bi-bar-chart"></i> <span>REPORTS</span>
          </a>
        </li>
      </ul>
      <div class="logout-wrapper">
        <a href="../sharedAssets/logOut.php" onclick="return confirm('Are you sure you want to logout?')"
          class="sidebar-link logout-custom d-flex align-items-center">
          <i class="bi bi-box-arrow-right me-2"></i>
          <span>LOGOUT</span>
        </a>
      </div>
    </aside>

    <div class="main">
      <?php
      $pageMap = [
        'dashboard' => 'adminContent',
        'resident' => 'adminContent',
        'announcement' => 'adminContent',
        'complaints' => 'adminContent',
        'document' => 'adminContent',
        'reports' => 'adminContent',

      ];

      $folder = $pageMap[$page] ?? 'adminContent';
      $filePath = "$folder/$page.php";

      if (file_exists($filePath)) {
        include($filePath);
      } else {
        echo "<h4 class='text-danger'>Error: Page file '$filePath' not found.</h4>";
      }
      ?>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../admin/assets/js/script.js"></script>
</body>

</html>
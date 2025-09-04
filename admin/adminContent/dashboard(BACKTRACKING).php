<?php

$totalResult = executeQuery("SELECT COUNT(*) AS total FROM users WHERE role = 'user'");
$totalUsers = mysqli_fetch_assoc($totalResult)['total'];

$activeResult = executeQuery("SELECT COUNT(*) AS active FROM users WHERE role = 'user' AND lastLogin >= NOW() - INTERVAL 30 DAY");
$activeUsers = mysqli_fetch_assoc($activeResult)['active'];

$inactiveUsers = $totalUsers - $activeUsers;

$signupResult = executeQuery("SELECT MONTHNAME(dateCreated) AS month, COUNT(*) AS count 
    FROM users 
    WHERE role = 'user' 
    GROUP BY MONTH(dateCreated), MONTHNAME(dateCreated) 
    ORDER BY MONTH(dateCreated)");
$signupLabels = $signupData = [];
while ($row = mysqli_fetch_assoc($signupResult)) {
  $signupLabels[] = $row['month'];
  $signupData[] = $row['count'];
}

$loginResult = executeQuery("SELECT MONTHNAME(lastLogin) AS month, COUNT(*) AS count 
    FROM users 
    WHERE role = 'user' AND lastLogin IS NOT NULL 
    GROUP BY MONTH(lastLogin), MONTHNAME(lastLogin) 
    ORDER BY MONTH(lastLogin)");
$loginLabels = $loginData = [];
while ($row = mysqli_fetch_assoc($loginResult)) {
  $loginLabels[] = $row['month'];
  $loginData[] = $row['count'];
}

if (empty($signupLabels)) {
  $signupLabels = ["No data"];
  $signupData = [0];
}
if (empty($loginLabels)) {
  $loginLabels = ["No data"];
  $loginData = [0];
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>E-Barangay | Admin Dashboard</title>
  <link rel="icon" href="assets/images/logoSanAntonio.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: rgb(233, 233, 233);
      color: dark;
      height: 100vh;
      margin: 0;
      padding: 0;
    }
  </style>
</head>

<body>
  <div class="container-fluid px-3 px-md-4 px-lg-3 min-vh-100">
    <div class="row">
      <div class="col-12 py-4">
        <div class="row align-items-center justify-content-between mb-4">
          <div class="col-12 pt-3">
            <h1 class="mb-0 display-5">Hello, <span style="color: #19AFA5;">Admin</span></h1>
          </div>
        </div>
        <!-- USER STATISTICS -->
        <div class="mb-4">
          <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            <!-- Card 1 -->
            <div class="col">
              <div class="card text-dark rounded-4 border-0 h-100" style="background-color: rgb(49, 175, 171);">
                <div class="card-body p-md-5 text-center">
                  <p class="display-6 fw-bold mb-3"><?= $totalUsers ?></p>
                  <h5 class="mb-0 fw-bold">Total Population</h5>
                </div>
              </div>
            </div>
            <!-- Card 2 -->
            <div class="col">
              <div class="card text-dark rounded-4 border-0 h-100" style="background-color: rgb(49, 175, 171);">
                <div class="card-body p-md-5 text-center">
                  <p class="display-6 fw-bold mb-3"><?= $totalUsers ?></p>
                  <h5 class="mb-0 fw-bold">Total Users</h5>
                </div>
              </div>
            </div>
            <!-- Card 3 -->
            <div class="col">
              <div class="card text-dark rounded-4 border-0 h-100" style="background-color: rgb(49, 175, 171);">
                <div class="card-body p-md-5 text-center">
                  <p class="display-6 fw-bold mb-3"><?= $totalUsers ?></p>
                  <h5 class="mb-0 fw-bold">Total Document Requests</h5>
                </div>
              </div>
            </div>
            <!-- Card 4 -->
            <div class="col">
              <div class="card text-dark rounded-4 border-0 h-100" style="background-color: rgb(49, 175, 171);">
                <div class="card-body p-md-5 text-center">
                  <p class="display-6 fw-bold mb-3"><?= $totalUsers ?></p>
                  <h5 class="mb-0 fw-bold">Total Complaints</h5>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <!--70%-->
      <div class="col-8">
        <div class="card text-dark rounded-4 border-0 h-100" style="background-color: rgb(49, 175, 171);">
          <div class="card-body p-md-5 text-center">
            <h5 class="mb-0 fw-bold">Diagram Map</h5>
          </div>
        </div>
      </div>
      <!--30%-->
      <div class="col-4">
        <div class="card text-dark rounded-4 border-0 h-100" style="background-color: rgb(49, 175, 171);">
          <div class="card-body p-md-5 text-center">
            <h4 class="mb-2 fw-bold">Barangay Population Breakdown</h4>
            <p class="mb-4 fw-bold">as of July 2025</p>

            <div class="d-flex flex-column gap-2">
              <div class="d-flex justify-content-between w-100">
                <h5 class="mb-0 fw-bold">Male:</h5>
                <h5 class="mb-0 fw-bold"><?= $totalUsers ?></h5>
              </div>
              <div class="d-flex justify-content-between w-100">
                <h5 class="mb-0 fw-bold">Female:</h5>
                <h5 class="mb-0 fw-bold"><?= $totalUsers ?></h5>
              </div>
              <div class="d-flex justify-content-between w-100">
                <h5 class="mb-0 fw-bold">Age (0-12):</h5>
                <h5 class="mb-0 fw-bold"><?= $totalUsers ?></h5>
              </div>
              <div class="d-flex justify-content-between w-100">
                <h5 class="mb-0 fw-bold">Age (18-59):</h5>
                <h5 class="mb-0 fw-bold"><?= $totalUsers ?></h5>
              </div>
              <div class="d-flex justify-content-between w-100">
                <h5 class="mb-0 fw-bold">Age (60+):</h5>
                <h5 class="mb-0 fw-bold"><?= $totalUsers ?></h5>
              </div>
              <div class="d-flex justify-content-between w-100">
                <h5 class="mb-0 fw-bold">Registered:</h5>
                <h5 class="mb-0 fw-bold"><?= $totalUsers ?></h5>
              </div>
              <div class="d-flex justify-content-between w-100">
                <h5 class="mb-0 fw-bold">Unregistered:</h5>
                <h5 class="mb-0 fw-bold"><?= $totalUsers ?></h5>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div style="max-width: 500px; margin: auto;">
    <canvas id="myDoughnutChart"></canvas>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>
    const ctx = document.getElementById('myDoughnutChart');

    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['Bonafide', 'Transient', 'Migrant', 'Isa pa na type of Residency'],
        datasets: [{
          label: 'Population Distribution',
          data: [300, 50, 100, 1234],
          backgroundColor: [
            'rgb(255, 99, 132)',
            'rgb(54, 162, 235)',
            'rgb(255, 205, 86)',
            'rgb(22, 41, 90)'
          ],
          hoverOffset: 4
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'bottom'
          }
        }
      }
    });
  </script>
  <!-- <script>
    const chartOptions = {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: true,
          position: 'top',
          labels: {
            color: 'black',
            font: {
              family: 'Poppins',
              size: function (context) {
                const width = context.chart.width;
                return width < 768 ? 14 : 16;
              }
            },
            padding: 20
          }
        },
        tooltip: {
          backgroundColor: 'rgba(0, 0, 0, 0.8)',
          titleFont: { family: 'Poppins', size: 16 },
          bodyFont: { family: 'Poppins', size: 14 }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            color: 'black',
            font: {
              family: 'Poppins',
              size: function (context) {
                return context.chart.width < 768 ? 12 : 14;
              }
            }
          },
          grid: { color: 'rgba(114, 117, 117, 0.3)' }
        },
        x: {
          ticks: {
            color: 'black',
            font: {
              family: 'Poppins',
              size: function (context) {
                return context.chart.width < 768 ? 12 : 14;
              }
            },
            maxRotation: 45,
            minRotation: 0
          },
          grid: { color: 'rgba(114, 117, 117, 0.3)' }
        }
      },
      layout: {
        padding: { top: 20, bottom: 20, left: 20, right: 20 }
      }
    };

    const signupData = {
      labels: <?= json_encode($signupLabels) ?>,
      datasets: [{
        label: 'Signups',
        data: <?= json_encode($signupData) ?>,
        backgroundColor: 'rgba(255, 183, 153, 0.8)',
        borderColor: 'rgba(255, 183, 153, 1)',
        borderWidth: 2,
        borderRadius: 6,
        borderSkipped: false
      }]
    };

    const loginData = {
      labels: <?= json_encode($loginLabels) ?>,
      datasets: [{
        label: 'Logins',
        data: <?= json_encode($loginData) ?>,
        backgroundColor: 'rgba(255, 183, 153, 0.8)',
        borderColor: 'rgba(255, 183, 153, 1)',
        borderWidth: 2,
        borderRadius: 6,
        borderSkipped: false
      }]
    };

    // Fixed: Assign chart instances to variables
    const signupChart = new Chart(document.getElementById('userSignupsChart').getContext('2d'), {
      type: 'bar',
      data: signupData,
      options: chartOptions
    });

    const loginChart = new Chart(document.getElementById('loginActivityChart').getContext('2d'), {
      type: 'bar',
      data: loginData,
      options: chartOptions
    });

    // Now this will work correctly
    window.addEventListener('resize', function () {
      signupChart.resize();
      loginChart.resize();
    }); -->
  </script>
</body>

</html>
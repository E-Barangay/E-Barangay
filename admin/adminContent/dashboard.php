<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>E-Barangay | Admin Dashboard</title>
  <link rel="icon" href="assets/images/logoSanAntonio.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
</head>
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

<body>
  <div class="container-fluid px-3 px-md-4 px-lg-3 min-vh-100">
    <div class="row">
      <div class="col-12 py-4">

        <div class="row align-items-center justify-content-between mb-4">
          <div class="col-12 pt-3">
            <h1 class="mb-0 display-5">Hello, <span style="color: #19AFA5;">Admin</span></h1>
          </div>
        </div>

        <div class="mb-4">
          <h4 class="mb-4 fw-bold">USER STATISTICS</h4>
          <hr class="border-top border-dark opacity-100" style="border-width: 2px !important;">

          <div class="card text-dark rounded-4 border-0" style="background-color: rgb(49, 175, 171);">
            <div class="card-body p-4 p-md-5">
              <div class="row text-center">
                <div class="col-12 col-lg-4 mb-4 mb-lg-0">
                  <p class="display-6 fw-bold mb-3">100</p>
                  <h5 class="mb-0 fw-bold">TOTAL USERS</h5>
                </div>
                <div class="col-12 col-lg-4 mb-4 mb-lg-0">
                  <p class="display-6 fw-bold mb-3">65</p>
                  <h5 class="mb-0 fw-bold">ACTIVE USERS</h5>
                </div>
                <div class="col-12 col-lg-4 mb-4 mb-lg-0">
                  <p class="display-6 fw-bold mb-3">35</p>
                  <h5 class="mb-0 fw-bold">INACTIVE USERS</h5>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="mb-4">
          <h4 class="mb-4 fw-bold">WEBSITE ENGAGEMENT</h4>
          <hr class="border-top border-dark opacity-100" style="border-width: 2px !important;">

          <div class="row">
            <div class="col-12 mb-5">
              <h5 class="fw-bold mb-4">MONTHLY USER SIGNUPS</h5>
              <div class="bg-light rounded-4 p-4 p-md-5">
                <div class="position-relative" style="height: 400px;">
                  <canvas id="userSignupsChart" class="w-100 h-100 border border-2 border-dark rounded-3"></canvas>
                </div>
              </div>
            </div>

            <div class="col-12 mb-5">
              <h5 class="fw-bold mb-4">MONTHLY LOGIN ACTIVITY</h5>
              <div class="bg-light rounded-4 p-4 p-md-5">
                <div class="position-relative" style="height: 400px;">
                  <canvas id="loginActivityChart" class="w-100 h-100 border border-2 border-dark rounded-3"></canvas>
                </div>
              </div>
            </div>
          </div>

        </div>

      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>
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
          titleFont: {
            family: 'Poppins',
            size: 16
          },
          bodyFont: {
            family: 'Poppins',
            size: 14
          }
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
                const width = context.chart.width;
                return width < 768 ? 12 : 14;
              }
            }
          },
          grid: {
            color: 'rgba(114, 117, 117, 0.3)'
          }
        },
        x: {
          ticks: {
            color: 'black',
            font: {
              family: 'Poppins',
              size: function (context) {
                const width = context.chart.width;
                return width < 768 ? 12 : 14;
              }
            },
            maxRotation: 45,
            minRotation: 0
          },
          grid: {
            color: 'rgba(114, 117, 117, 0.3)'
          }
        }
      },
      layout: {
        padding: {
          top: 20,
          bottom: 20,
          left: 20,
          right: 20
        }
      }
    };

    const signupData = {
      labels: ["January", "February", "March", "April", "May"],
      datasets: [{
        label: 'Signups',
        data: [3, 1, 0, 1, 0],
        backgroundColor: 'rgba(255, 183, 153, 0.8)',
        borderColor: 'rgba(255, 183, 153, 1)',
        borderWidth: 2,
        borderRadius: 6,
        borderSkipped: false
      }]
    };

    const loginData = {
      labels: ["January", "February", "March", "April", "May"],
      datasets: [{
        label: 'Logins',
        data: [2, 1, 1, 0, 1],
        backgroundColor: 'rgba(255, 183, 153, 0.8)',
        borderColor: 'rgba(255, 183, 153, 1)',
        borderWidth: 2,
        borderRadius: 6,
        borderSkipped: false
      }]
    };

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


    window.addEventListener('resize', function () {
      signupChart.resize();
      loginChart.resize();
    });
  </script>

</body>

</html>
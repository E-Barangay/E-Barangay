<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>E-Barangay | Admin Dashboard</title>
  <link rel="icon" href="" />
  <link href="https://fonts.googleapis.com/css2?family=Lexend+Exa&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
</head>
<style>
  body {
    font-family: 'Poppins', sans-serif;
    background-color: #212529;
    color: white;
    height: 100vh;
    margin: 0;
    padding: 0;
  }
</style>

<body>
  <div class="container-fluid h-100"
    style="padding-left: 70px; padding-right: 70px; transition: margin-left 0.25s ease-in-out;">
    <div class="row h-100">
      <div class="col-12 py-4 px-4 overflow-auto">

        <div class="row align-items-center justify-content-between">
          <div class="col-12 col-md-6 pt-3 pt-md-4" style="padding-left: 35px;">
            <h4>Hello, <span style="color: #ffc107;">Admin</span></h4>
          </div>
        </div>

        <!-- User Statistics -->
        <div class="container-fluid py-4 px-4 mb-3">
          <div class="row">
            <div class="col-12">
              <h5 class="mb-3">USER STATISTICS</h5>
              <hr class="border-top border-light opacity-100">
            </div>
          </div>

          <div class="card bg-primary text-white rounded-4">
            <div class="row text-center m-3">
              <div class="col-12 col-md-4 mb-3 mb-md-0">
                <p class="fs-3 fw-bold">100</p>
                <h6 class="pb-2">TOTAL USERS</h6>
              </div>
              <div class="col-12 col-md-4 mb-3 mb-md-0">
                <p class="fs-3 fw-bold">65</p>
                <h6 class="pb-2">ACTIVE USERS</h6>
              </div>
              <div class="col-12 col-md-4 mb-3 mb-md-0">
                <p class="fs-3 fw-bold">35</p>
                <h6 class="pb-2">INACTIVE USERS</h6>
              </div>
            </div>
          </div>
        </div>

        <!-- Website Engagement Charts -->
        <div class="container-fluid py-4 px-4">
          <div class="row">
            <div class="col-12">
              <h5 class="mb-3">WEBSITE ENGAGEMENT</h5>
              <hr class="border-top border-light opacity-100">
            </div>

            <!-- Monthly User Signups Chart -->
            <div class="col-12 mt-4">
              <p class="fw-bold">MONTHLY USER SIGNUPS</p>
              <div class="bg-tertiary rounded-4 p-4">
                <div style="height: 400px; position: relative;">
                  <canvas id="userSignupsChart" style="border: 2px solid white; border-radius: 10px;"></canvas>
                </div>
              </div>
            </div>

            <!-- Monthly Login Activity Chart -->
            <div class="col-12 mt-5">
              <p class="fw-bold">MONTHLY LOGIN ACTIVITY</p>
              <div class="bg-tertiary rounded-4 p-4">
                <div style="height: 400px; position: relative;">
                  <canvas id="loginActivityChart" style="border: 2px solid white; border-radius: 10px;"></canvas>
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

  <!-- Chart.js Initialization -->
  <script>
    const chartOptions = {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            color: 'white',
            font: {
              family: 'Poppins',
              size: 12
            }
          },
          grid: {
            color: '#ffffff33'
          }
        },
        x: {
          ticks: {
            color: 'white',
            font: {
              family: 'Poppins',
              size: 14
            }
          },
          grid: {
            color: '#ffffff33'
          }
        }
      },
      plugins: {
        legend: {
          labels: {
            color: 'white',
            font: {
              family: 'Poppins',
              size: 16
            }
          }
        },
        tooltip: {
          backgroundColor: 'rgba(0, 0, 0, 0.8)',
          titleFont: {
            family: 'Poppins',
            size: 14
          },
          bodyFont: {
            family: 'Poppins',
            size: 12
          }
        }
      }
    };

    const signupData = {
      labels: ["January", "February", "March", "April", "May"],
      datasets: [{
        label: 'Signups',
        data: [10, 25, 30, 15, 20],
        backgroundColor: '#ffc09f',
        borderColor: '#ffffff',
        borderWidth: 1
      }]
    };

    const loginData = {
      labels: ["January", "February", "March", "April", "May"],
      datasets: [{
        label: 'Logins',
        data: [20, 35, 40, 20, 30],
        backgroundColor: '#ffee93',
        borderColor: '#ffffff',
        borderWidth: 1
      }]
    };

    new Chart(document.getElementById('userSignupsChart').getContext('2d'), {
      type: 'bar',
      data: signupData,
      options: chartOptions
    });

    new Chart(document.getElementById('loginActivityChart').getContext('2d'), {
      type: 'bar',
      data: loginData,
      options: chartOptions
    });
  </script>

</body>

</html>
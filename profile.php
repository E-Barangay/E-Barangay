<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Barangay | Profile</title>

    <!-- Icon -->
    <link rel="icon" href="assets/images/logoSanAntonio.png">

    <!-- Style Sheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/navbar/style.css">
    <link rel="stylesheet" href="assets/css/profile/style.css">
    <link rel="stylesheet" href="assets/css/footer/style.css">
</head>

<body data-bs-theme="dark">
    <?php include("sharedAssets/navbar.php") ?>

    <div class="container">
        <div class="row">
            <div class="col">
                <div class="card p-5 m-4 position-relative">
                    <button id="editBtn" class="btn btn-primary position-absolute top-0 end-0 m-5">
                        Edit
                    </button>
                    <div class="d-flex align-items-center">
                        <img src="assets/images/defaultProfile.png" class="rounded-circle" alt="Profile Picture"
                            style="width: 200px; height: 200px; object-fit: cover;">
                        <div class="ms-3">
                            <div class="h3 mb-1">John Doe</div>
                            <div class="p mb-0">johndoe90@gmail.com</div>
                        </div>
                    </div>
                    <div class="row g-3 mt-4">
                        <!-- Full Name -->
                        <div class="col-md-6">
                            <label for="fullName" class="form-label">Full Name</label>
                            <input id="fullName" class="form-control" type="text" disabled>
                        </div>

                        <!-- Gender -->
                        <div class="col-md-6">
                            <label for="gender" class="form-label">Gender</label>
                            <input id="gender" class="form-control" type="text" disabled>
                        </div>

                        <!-- Date of Birth -->
                        <div class="col-md-6">
                            <label for="dob" class="form-label">Date of Birth</label>
                            <input id="dob" class="form-control" type="date" disabled>
                        </div>

                        <!-- Address -->
                        <div class="col-md-6">
                            <label for="address" class="form-label">Address</label>
                            <input id="address" class="form-control" type="text" disabled>
                        </div>

                        <!-- Phone Number -->
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input id="phone" class="form-control" type="tel" disabled>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" class="form-control" type="email" disabled>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include("sharedAssets/footer.php") ?>
    <script>
        // grab all form controls
        const inputs = document.querySelectorAll('.card-body input, .card-body select, .card-body textarea, .form-control');
        const btn = document.getElementById('editBtn');

        btn.addEventListener('click', () => {
            inputs.forEach(el => el.removeAttribute('disabled'));
            // optionally change button into "Save" or hide it
            btn.textContent = 'Save';
            btn.classList.replace('btn-primary', 'btn-success');
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
</body>

</html>
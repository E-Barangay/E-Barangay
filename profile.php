<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile</title>
    <!-- Style Sheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/navbar/style.css">
    <link rel="stylesheet" href="assets/css/profile/style.css">
</head>

<body data-bs-theme="dark">
    <?php include("sharedAssets/navbar.php") ?>

    <div class="container">
        <div class="row">
            <div class="col">
                <div class="card p-5 m-4 position-relative">
                    <button class="btn btn-primary position-absolute top-0 end-0 m-5">Edit</button>
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
                            <input id="fullName" class="form-control" type="text">
                        </div>

                        <!-- Gender -->
                        <div class="col-md-6">
                            <label for="gender" class="form-label">Gender</label>
                            <input id="gender" class="form-control" type="text">
                        </div>

                        <!-- Date of Birth -->
                        <div class="col-md-6">
                            <label for="dob" class="form-label">Date of Birth</label>
                            <input id="dob" class="form-control" type="date">
                        </div>

                        <!-- Address -->
                        <div class="col-md-6">
                            <label for="address" class="form-label">Address</label>
                            <input id="address" class="form-control" type="text">
                        </div>

                        <!-- Phone Number -->
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input id="phone" class="form-control" type="tel">
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" class="form-control" type="email">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include("sharedAssets/footer.php") ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
</body>

</html>
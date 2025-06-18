<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/navbar/style.css">
    <link rel="stylesheet" href="assets/css/signup/style.css">

</head>

<body data-bs-theme="light">


    <div class="container-fluid">
        <div class="row">
            <!-- Left Side Image -->
            <div class="col-md-6 d-none d-md-block p-0">
                <img src="assets/images/bg.png" class="left-img position-relative" alt="Barangay Background">
            </div>

            <!-- Right Side Sign In -->
            <div class="col-md-6 col-12 d-flex align-items-center justify-content-center">
                <div class="container">
                        <div class="w-100">
                        <div class="mb-4">
                            <div class="row">
                                <div class="col-12">
                                    <div class="text-center mt-3 mb-2">
                                        <div class="d-flex flex-row justify-content-center align-items-center gap-3">
                                            <img src="assets/images/logoSanAntonio.png" class="img-fluid"
                                                style="max-width: 60px;" alt="Logo">
                                            <img src="assets/images/logoSantoTomas.png" class="img-fluid"
                                                style="max-width: 60px;" alt="Logo">
                                        </div>

                                        <h2 class="mb-0">Brgy. San Antonio</h2>
                                        <div class="fs-6">Sto. Tomas, Batangas</div>

                                        <p class="fs-6 fst-italic text-center">
                                            Serving the vibrant community of Barangay San Antonio, Santo Tomas, Batangas
                                            — where
                                            tradition meets progress.
                                        </p>

                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                <div class="d-flex flex-md-row flex-column">
                                    <div class="col-md-4 col-12 p-md-2 p-1">
                                        <div class="mb-md-2 mt-md-2 mt-0 mb-0">

                                            <div class="row">
                                                <div class="col-12 my-3">
                                                    <label class="form-label text-start">First Name</label>
                                                    <input type="text" class="form-control w-100 h-75">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 my-3">
                                                    <label class="form-label text-start">Middle Name</label>
                                                    <input type="text" class="form-control w-100 h-75">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 my-3">
                                                    <label class="form-label text-start">Last Name</label>
                                                    <input type="text" class="form-control w-100 h-75">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 my-3">
                                                    <label class="form-label text-start">Birth Date</label>
                                                    <input type="date" class="form-control w-100 h-75">
                                                </div>
                                            </div>



                                        </div>
                                    </div>

                                    <div class="col-md-4 col-12  p-md-2 p-1">
                                        <div class="mb-md-2 mt-md-2 mt-0 mb-0">

                                            <div class="row">
                                                <div class="col-12 my-3">
                                                    <label class="form-label text-start">Email</label>
                                                    <input type="email" class="form-control w-100 h-75">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 my-3">
                                                    <label class="form-label text-start">Password</label>
                                                    <input type="password" class="form-control w-100 h-75">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 my-3">
                                                    <label class="form-label text-start">Re-enter Password</label>
                                                    <input type="password" class="form-control w-100 h-75">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 my-3">
                                                    <label class="form-label text-start">Phone Number</label>
                                                    <input type="number" class="form-control w-100 h-75">
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-md-4 col-12 p-md-2 p-1">
                                        <div class="mb-md-2 mt-md-2 mt-0 mb-0">

                                            <div class="row">
                                                <div class="col-12 my-3">
                                                    <label for="provinceInput" class="form-label">Province</label>
                                                    <input class="form-control w-100 h-75" list="dataListProvince"
                                                        id="provinceInput" placeholder="Province">
                                                    <datalist id="dataListProvince"></datalist>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 my-3">
                                                    <label for="cityInput" class="form-label">City</label>
                                                    <input class="form-control w-100 h-75" list="dataListCity"
                                                        id="cityInput" placeholder="City">
                                                    <datalist id="dataListCity"></datalist>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 my-3">
                                                    <label for="barangayInput" class="form-label">Barangay</label>
                                                    <input class="form-control w-100 h-75" list="dataListBrgy"
                                                        id="barangayInput" placeholder="Barangay">
                                                    <datalist id="dataListBrgy"></datalist>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 my-3">
                                                    <p>Gender</p>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="radioDefault"
                                                            id="radioDefault1" checked>
                                                        <label class="form-check-label" for="radioDefault1">
                                                            Male
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="radioDefault"
                                                            id="radioDefault2">
                                                        <label class="form-check-label" for="radioDefault2">
                                                            Female
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex flex-column align-items-center">

                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" value="" id="checkDefault">
                                        <label class="form-check-label" for="checkDefault">
                                            I Accept The Terms & Condition
                                        </label>
                                    </div>

                                    <button type="button" class="rounded-5 btn btn-custom fs-5"
                                        style="width: 200px; background-color: #19AFA5">Create Account</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <script src="assets/js/signUp/signUp.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
            crossorigin="anonymous"></script>
</body>

</html>
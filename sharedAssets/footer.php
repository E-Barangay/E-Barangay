<div class="container">
    <footer class="footer text-center pt-4 mt-4">
        <div class="row p-5" style="border-top: 1px solid #19AFA5; border-bottom: 1px solid #19AFA5;">
            <div class="col-12 mb-5 mt-4 text-adapt">
                <div class="h3" style="font-weight: bold; color: black;">
                    <span style="color:#19AFA5;">Makabagong</span> putol, <span
                        style="color:#19AFA5;">Makikinabang</span> all!</span>
                </div>
            </div>
            <div class="col-12 col-lg-5 text-center text-lg-start py-4 px-3">
                <div class="h5 mb-3 text-adapt" style="color: black;">
                    Barangay<span style="color:#19AFA5;"> San Antonio</span>
                </div>
                <p class="text-adapt" style="color: black;">
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Non tempore officia inventore minima ipsam
                    quis facilis. Aut vel quae excepturi ad, magni omnis cupiditate incidunt est eveniet vitae! Illum,
                    rerum!
                </p>
            </div>
            <hr class="d-block d-lg-none">
            <div class="col-12 col-lg-2 text-center text-lg-start py-4">
                <div class="h5 mb-3" style="color:#19AFA5;">About Us</div>
                <ul class="nav flex-column">
                    <li class="footer-item mb-3"><a href="javascript:void(0)" onclick="showSection('mission-section')"
                            class="footer-link p-0">Mission</a></li>
                    <li class="footer-item mb-3"><a href="javascript:void(0)" onclick="showSection('vision-section')"
                            class="footer-link p-0">Vision</a></li>
                    <li class="footer-item mb-3"><a href="javascript:void(0)"
                            onclick="showSection('executives-section')" class="footer-link p-0">Executives</a></li>
                </ul>

                <div class="d-block d-lg-none">
                    <section id="mission-section-sm" class="container py-3" style="display: none;">
                        <h2 class="mb-3" style="color:#19AFA5; font-weight: bold;">Mission</h2>
                        <p style="color: black;">
                            To eradicate extreme poverty by raising the standard of living of the people and sustaining the people in their development towards the future.
                        </p>
                    </section>

                    <section id="vision-section-sm" class="container py-3" style="display: none;">
                        <h2 class="mb-3" style="color:#19AFA5; font-weight: bold;">Vision</h2>
                        <p style="color: black;">
                            A progressive, healthy and peaceful community where citizenz are united and participate together in change and decision-making towards good governance. Vibrant and genuine service as promised to achieve a strong foundation that will inspire every individual.
                        </p>
                    </section>

                    <section id="executives-section-sm" class="container py-3" style="display: none;">
                        <h2 class="mb-3" style="color:#19AFA5;">Executives</h2>
                        <img src="assets/images/executives.jpg" alt="Barangay Executives" class="img-fluid rounded">
                    </section>
                </div>
            </div>
            <hr class="d-block d-lg-none">
            <div class="col-12 col-lg-2 text-center text-lg-start py-4">
                <div class="h5 mb-3" style="color:#19AFA5;">Services</div>
                <ul class="nav flex-column">
                    <li class="footer-item mb-3"><a href="index.php" class="footer-link p-0">Announcements</a></li>
                    <li class="footer-item mb-3"><a href="documents.php" class="footer-link p-0">Documents</a></li>
                    <li class="footer-item mb-3"><a href="reports.php" class="footer-link p-0">Complaints</a></li>
                </ul>
            </div>
            <hr class="d-block d-lg-none">
            <div class="col-12 col-lg-3 text-center text-lg-start py-4">
                <div class="h5 mb-3" style="color:#19AFA5;">Contacts</div>
                <ul class="nav flex-column">
                    <li class="footer-item mb-3"><a href="https://www.facebook.com/profile.php?id=61553441500742"
                            class="footer-link p-0"><i class="fa-brands fa-facebook"
                                style="font-size: 20px; vertical-align: middle;"></i> Barangay San Antonio</a></li>
                    <li class="footer-item mb-3"><a href="#" class="footer-link p-0"><i class="fa-solid fa-envelope"
                                style="font-size: 20px; vertical-align: middle;"></i> sanantonio@gmail.com</a></li>
                    <li class="footer-item mb-3"><a href="#" class="footer-link p-0"><i class="fa-solid fa-phone"
                                style="font-size: 20px; vertical-align: middle;"></i> 0912 345 6789</a></li>
                </ul>
            </div>

            <div class="d-none d-lg-block">
                <section id="mission-section" class="container py-3" style="display: none;">
                    <h2 class="mb-3" style="color:#19AFA5; font-weight: bold;">Mission</h2>
                    <p style="color: black;">
                        To eradicate extreme poverty by raising the standard of living of the people and sustaining the people in their development towards the future.
                    </p>
                </section>

                <section id="vision-section" class="container py-3" style="display: none;">
                    <h2 class="mb-3" style="color:#19AFA5; font-weight: bold;">Vision</h2>
                    <p style="color: black;">
                        A progressive, healthy and peaceful community where citizenz are united and participate together in change and decision-making towards good governance. Vibrant and genuine service as promised to achieve a strong foundation that will inspire every individual.
                    </p>
                </section>

                <section id="executives-section" class="container py-3" style="display: none;">
                    <h2 class="mb-3" style="color:#19AFA5;">Executives</h2>
                    <img src="assets/images/executives.jpg" alt="Barangay Executives" class="img-fluid rounded">
                </section>
            </div>
            
        </div>
        <div class="row">
            <div class="col text-center py-4 text-adapt" style="color: black;">
                © 2025 Barangay San Antonio. All Rights Reserved.
            </div>
        </div>

    </footer>
    <script>
        function showSection(sectionId) {
            var sections = ['mission-section', 'vision-section', 'executives-section',
                            'mission-section-sm', 'vision-section-sm', 'executives-section-sm'];
            sections.forEach(function (id) {
                var el = document.getElementById(id);
                if (el) el.style.display = (id === sectionId || id === sectionId + '-sm') ? 'block' : 'none';
            });
        }
    </script>

</div>
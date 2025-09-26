<div class="col-3 text-center">
    <div>HON. ULYSES "TETET" M. RELLAMA</div>
    <div class="pt-3">COUNCILORS</div>
    <div>HON. JAYMAR A. MANARIN</div>
    <div>HON. NOLITO S. AVENIDO</div>
    <div>HON. LIEZEL M. ALCOZER</div>
    <div>HON. BOBBIT M. GUEVARRA</div>
    <div>HON. CRISTITN M. MANDAYO</div>
    <div>HON. JOEL C. MARASIGAN</div>
    <div>HON. APOLINARIO C. MANARIN</div>
    <div class="pt-3">S.K. Chairwoman</div>
    <div>HON. BABYANN E. MANARIN</div>
    <div class="pt-3">Barangay Secretary</div>
    <div>MARY JOY M. MARTIREZ</div>
    <div class="pt-3">Barangay Treasurer</div>
    <div>AMELIA A. MALACAMAN</div>
    <div class="pt-3">Barangay Record Keeper</div>
    <div>LEXTER M. DOLOR</div>
</div>
<div class="col-8">
    <div class="row">
        <div class="col">
            <div>To whom it may concern,</div>
            <p class="pt-2" style="text-indent: 2em; text-align: justify;">This is to certify that the person whose name, picture, right thumb print and
                signature appears hereon, has requested a <?php echo $documentName ?> from this office with the following
                information:</p>
        </div>
    </div>
    <div class="row">
        <div class="col-9">
            <div>
                <p><strong>Name:</strong> <?php echo $firstName . " " . $middleName . " " . $lastName ?></p>
                <p><strong>Address:</strong> <?php echo $streetName . " " . $barangayName . ", " . $cityName . ", " . $provinceName ?></p>
                <p><strong>Permanent Address:</strong> <?php echo $streetName . " " . $barangayName . ", " . $cityName . ", " . $provinceName ?></p>
                <p><strong>Place of Birth:</strong> <?php echo $streetName . " " . $barangayName . ", " . $cityName . ", " . $provinceName ?></p>
                <p><strong>Date of Birth:</strong> <?php echo date("F j, Y", strtotime($birthDate)); ?></p>
                <p><strong>Age:</strong> 
                    <?php 
                        $birthDateObj = new DateTime($birthDate);
                        $today = new DateTime();
                        $age = $today->diff($birthDateObj)->y;
                        echo $age . " years old"; 
                    ?>
                </p>
                <p><strong>Gender: </strong><?php echo $gender ?></p>
                <p><strong>Civil Status: </strong>Single</p>
                <p><strong>Length of Stay: </strong>20 years</p>
                <div class="d-flex">
                    <p><strong>Type of Residency:</strong></p>
                    <select class="form-select form-select-sm ms-2 residencySelect" id="residency" name="residency" aria-label="Select Residency">
                        <option value="<?php echo $residecyType ?>" selected><?php echo $residencyType ?></option>
                        <option value="Migrant">Migrant</option>
                        <option value="Transient">Transient</option>
                        <option value="Foreign">Foreign</option>
                    </select>
                </div>
                <p><strong>Landlord: </strong></p>
                <p><strong>Company: </strong></p>
                <div class="d-flex">
                    <p><strong>Purpose:</strong></p>
                    <select class="form-select form-select-sm ms-2 purposeSelect" name="purpose" aria-label="Purpose Select">
                        <option selected>Choose Purpose</option>
                        <option value="Employment">Employment</option>
                        <option value="Job Requirement / Local Employment">Job Requirement / Local Employment</option>
                        <option value="Overseas Employment (OFW)">Overseas Employment (OFW)</option>
                        <option value="School Requirement / Enrollment">School Requirement / Enrollment</option>
                        <option value="Scholarship Application">Scholarship Application</option>
                        <option value="Medical Assistance">Medical Assistance</option>
                        <option value="Hospital Requirement">Hospital Requirement</option>
                        <option value="Legal Requirement / Court Use">Legal Requirement / Court Use</option>
                        <option value="NBI / Police Clearance">NBI / Police Clearance</option>
                        <option value="Passport Application / Renewal">Passport Application / Renewal</option>
                        <option value="Driver’s License">Driver’s License</option>
                        <option value="Loan Application">Loan Application</option>
                    </select>
                </div>
                <p><strong>Remarks:</strong> No Derogatory Record</p>
                <p><strong>Issued On:</strong> <?php echo date("F j, Y", time()); ?></p>
                <p><strong>Issued At:</strong> Barangay San Antonio, Santo Tomas City, Batangas</p>
            </div>
        </div>
        <div class="col-3">
            <img src="../assets/images/<?php echo $profilePicture ?>" class="bg-secondary align-items-center" style="width: 100%; height: 150px" alt="">
            <img src="../assets/images/<?php echo $profilePicture ?>" class="bg-secondary text-center mt-2" style="width: 100%; height: 150px;" alt="">
            <div class="signature-label mt-5 text-center border-top">Signature</div>
        </div>
    </div>
</div>
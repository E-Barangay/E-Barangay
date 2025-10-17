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
            <p><strong>Name:</strong> <?php echo $fullName ?></p>
            <p><strong>Address:</strong> <?php echo $blockLotNo . ", " . $phase . ", " . $subdivisionName . ", " .$purok . ", " .$streetName . ", " .$barangayName . ", " .$cityName . ", " .$provinceName ?></p>
            <p><strong>Permanent Address:</strong> <?php echo $permanentBlockLotNo . ", " . $permanentPhase . ", " . $permanentSubdivisionName . ", " . $permanentPurok . ", " . $permanentStreetName . ", " . $permanentBarangayName . ", " . $permanentCityName . ", " . $permanentProvinceName; ?></p>
            <p><strong>Place of Birth:</strong> <?php echo $birthPlace ?></p>
            <p><strong>Date of Birth:</strong> <?php echo date("F j, Y", strtotime($birthDate)); ?></p>
            <p><strong>Age: </strong> <?php echo $age . " years old"; ?></p>
            <p><strong>Gender:</strong> <?php echo $gender ?></p>
            <p><strong>Civil Status:</strong> <?php echo $civilStatus ?></p>
            <p><strong>Length of Stay:</strong> <?php echo $lengthOfStay ? (int)$lengthOfStay . ((int)$lengthOfStay == 1 ? ' year' : ' years') : ''; ?></p>
            <p><strong>Type of Residency:</strong> <?php echo $residencyType ?></p>
            <p><strong>Landlord:</strong></p>
            <p><strong>Company:</strong></p>
            <p><strong>Purpose:</strong> <?php echo $purpose ?></p>
            <p><strong>Remarks:</strong> <?php echo $remarks ?></p>
            <p><strong>Issued On:</strong> <?php echo date("F j, Y", time()); ?></p>
            <p><strong>Issued At:</strong> Barangay San Antonio, Santo Tomas City, Batangas</p>
        </div>
        <div class="col-3">
            <img src="../uploads/profiles/<?php echo $profilePicture ?>" style="width: 100%; height: 150px; object-fit: cover; object-position: center; " alt="">
            <div class="signature-label mt-5 text-center border-top">Signature</div>
        </div>
    </div>
</div>
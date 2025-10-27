<div class="col-lg-9 col-12" style="color: black;">
    <div class="row">
        <div class="col">
            <p>To whom it may concern,</p>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <p style="text-indent: 2em; text-align: justify;">This is to certify that the person whose name, picture, right thumb print and signature appears hereon, has requested a <?php echo $documentName ?> from this office with the following information:</p>
        </div>
    </div>
    <div class="row">
        <div class="col">
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
    </div>
</div>
<div class="col-lg-3 col-12 d-flex flex-row flex-md-column justify-content-start align-items-center" style="color: black;">
    <div class="row w-100">
        <div class="col-lg-12 col-6 mb-0 mb-lg-3 text-center">
            <img src="../../uploads/profiles/<?php echo $profilePicture ?>" alt="Profile Picture" style="width: 150px; height: 150px; object-fit: cover; object-position: center;">
        </div>
        <div class="col-lg-12 col-6 d-flex flex-column justify-content-end align-items-center pt-5">
            <img src="../../uploads/profiles/<?php echo $profilePicture ?>" alt="Signature" style="width: 100%; max-width: 250px; height: auto; aspect-ratio: 5 / 1; object-fit: contain; object-position: center;">
            <div class="signature-label mt-2 text-center border-top w-100">Signature</div>
        </div>
    </div>
</div>
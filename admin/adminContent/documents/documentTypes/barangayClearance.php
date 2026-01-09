<div class="row">
    <div class="col" style="color: black;">
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
            <div class="col-9 text-start">
                <p class="mb-1"><strong>Name:</strong> <?php echo $fullName ?></p>
                <p class="mb-1"><strong>Permanent Address:</strong>
                    <?php if($residencyType === 'FILIPINO') { ?>
                        <?php echo implode(', ', array_filter([$permanentBlockLotNo, $permanentSubdivisionName, $permanentPhase, $permanentPurok, $permanentStreetName, $permanentBarangayName, $permanentCityName, $permanentProvinceName])) ?>
                    <?php } else { ?>
                        <?php echo $foreignAddress ?>
                    <?php } ?>
                </p>
                <p class="mb-1"><strong>Place of Birth:</strong> <?php echo $birthPlace ?></p>
                <p class="mb-1"><strong>Date of Birth:</strong> <?php echo date("F j, Y", strtotime($birthDate)); ?></p>
                <p class="mb-1"><strong>Age: </strong> <?php echo $age . " years old"; ?></p>
                <p class="mb-1"><strong>Gender:</strong> <?php echo $gender ?></p>
                <p class="mb-1"><strong>Civil Status:</strong> <?php echo $civilStatus ?></p>
                <p class="mb-1"><strong>Length of Stay:</strong> <?php echo $lengthOfStay ? (int)$lengthOfStay . ((int)$lengthOfStay == 1 ? ' year' : ' years') : ''; ?></p>
                <p class="mb-1"><strong>Type of Residency:</strong> <?php echo $residencyType ?></p>
                <p class="mb-1"><strong>Purpose:</strong> <?php echo $purpose ?></p>
                <p class="mb-1"><strong>Remarks:</strong> <?php echo $remarks ?></p>
                <p class="mb-1"><strong>Issued On:</strong> <?php echo date("F j, Y", time()); ?></p>
                <p class="mb-1"><strong>Issued At:</strong> Barangay San Antonio, Santo Tomas City, Batangas</p>
            </div>
            <div class="col-3 d-flex flex-column justify-content-start align-items-center" style="color: black;">
                <div class="row w-100">
                    <div class="col text-center">
                        <img src="../../uploads/profiles/<?php echo $profilePicture ?>" alt="Profile Picture" style="width: 150px; height: 150px; object-fit: cover; object-position: center;">
                    </div>
                    <div class="col d-flex flex-column justify-content-end align-items-center pt-5">
                        <div class="signature-label mt-2 text-center border-top w-100">Signature</div>
                    </div>
                </div>
            </div>
        </div>
    </div>  
    
</div>

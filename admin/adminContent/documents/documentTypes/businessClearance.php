<div class="col-12 d-flex flex-column justify-content-center align-items-center" style="color: black;">
    <div class="row">
        <div class="col-lg-9">
            <div class="row">
                <div class="col">
                    <p>To whom it may concern:</p>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <p style="color: black; text-indent: 48px; text-align: justify;">This is to certify that the person whose named appears hereon, has requested a Barangay Business Clearance from the office with the following information:</p>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <p style="color: black;"><strong>Business Name:</strong> <?php echo $businessName ?></p>
                    <p style="color: black;"><strong>Business Address:</strong> <?php echo $businessAddress ?></p>
                    <p style="color: black;"><strong>Owner's Name:</strong> <?php echo $fullName ?></p>
                    <p style="color: black;"><strong>Owner's Address:</strong> <?php echo $streetName . " " . $barangayName . ", " . $cityName . ", " . $provinceName ?></p>
                    <p style="color: black;"><strong>Nature of Business:</strong> <?php echo $businessNature ?></p>
                    <p style="color: black;"><strong>Control No:</strong> <?php echo $controlNo ?></p>
                    <p style="color: black;"><strong>Purpose:</strong> <?php echo $purpose ?></p>
                    <p style="color: black;"><strong>Ownership:</strong> <?php echo $ownership ?></p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 d-none d-md-block d-flex flex-column justify-content-start align-items-center">
            <div class="row">
                <div class="col text-center">
                    <img src="../uploads/profiles/<?php echo $profilePicture ?>" alt="Profile Picture" style="width: 150px; height: 150px; object-fit: cover; object-position: center;">
                </div>
            </div> 
        </div>
    <div class="row">
        <div class="col" style="text-align: justify;">
            <p>_____ Complying with the provisions of existing Barangay Ordinances, Rules and Regulation herein implemented.</p>
            <p>_____ Partially complying with the provisions of existing Barangay Ordinances, Rules and Regulation herein enforced.</p>
            <p>_____ Interposes No objection on the issuance of the corresponding Mayor's Permit being applied for.</p>
            <p>_____ Recommends the issuance of a "Temporary Mayor's Permit" for not more than three (3) months and within that period shall submit the requirements sought, otherwise this Barangay shall take the necessary actions within legal bounds to issue a temporary restraining order until such time that the requirements sought are complied with.</p>

            <p><strong>Date Issued:</strong> <?php echo date("F jS, Y"); ?></p>
            <p><strong>Date Expired:</strong> <?php echo date("F j, Y", strtotime("December 31")); ?></p>
            <p><strong>O.R. Number:</strong> N/A</p>
            <p><strong>Amount Paid:</strong> N/A</p>
        </div>
    </div>
</div>
<div class="col-12 d-block d-md-none">
    <div class="row">
        <div class="col text-center mb-3">
            <img src="../uploads/profiles/<?php echo $profilePicture ?>" alt="Profile Picture" style="width: 150px; height: 150px; object-fit: cover; object-position: center;">
        </div>
    </div>
</div>
<?php

function toCamelCase($string) {
    $string = ucwords(strtolower($string));
    $string = str_replace(' ', '', $string);
    return lcfirst($string);
}

if(mysqli_num_rows($documentsResult) > 0)   {
    while ($documentsRow = mysqli_fetch_assoc($documentsResult)) { 
        $modalID = toCamelCase($documentsRow['documentName']);    
    ?>

        <div class="col-6 col-md-4 col-lg-4 p-1">
            <div class="documentCard card my-0 my-sm-2">
                <img src="assets/images/documents/<?php echo $documentsRow['documentImage'] ?>" class="card-img-top"
                    style="width: 100%; height: 500px; object-fit: cover;" alt="Document">
                <div class="mt-auto">
                    <button class="btn btn-primary documentButton mt-2" type="button" data-bs-toggle="modal" data-bs-target="#<?php echo toCamelCase($documentsRow['documentName']) . "Modal"; ?>">
                        <?php echo $documentsRow['documentName'] ?>
                    </button>
                </div>
            </div>
        </div>

        <div class="modal fade" id="<?php echo $modalID; ?>Modal" tabindex="-1" aria-labelledby="<?php echo $modalID; ?>Label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST">
                        <div class="modal-header" style="background-color: #19AFA5; color: white;">
                            <h1 class="modal-title fs-5" id="<?php echo $modalID; ?>Label">
                                <?php echo $documentsRow['documentName']; ?>
                            </h1>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">

                            <?php if ($documentsRow['documentTypeID'] == 2) { ?>
                                
                                <p class="note mb-3">Please fill out your business details and choose the purpose of your request:</p>

                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="businessName" name="businessName" placeholder="Business Name" required>
                                    <label for="businessName">Business Name</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="businessAddress" name="businessAddress" placeholder="Business Address" required>
                                    <label for="businessAddress">Business Address</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" value="<?php echo $fullName ?>" id="ownerName" name="ownerName" placeholder="Owner's Name" required readonly>
                                    <label for="ownerName">Owner's Name</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" value="<?php echo $blockLotNo . ", " . $phase . ", " . $subdivisionName . ", " .$purok . ", " .$streetName . ", " .$barangayName . ", " .$cityName . ", " .$provinceName  ?>" id="ownerAddress" name="ownerAddress" placeholder="Owner's Address" required readonly>
                                    <label for="ownerAddress">Owner's Address</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <select class="form-select" id="businessNature" name="businessNature" required>
                                        <option value="" selected disabled>Choose Nature of Business</option>
                                        <option value="Sari-Sari Store">Sari-Sari Store</option>
                                        <option value="Food & Beverage">Food & Beverage</option>
                                        <option value="Retail">Retail</option>
                                        <option value="Services">Services</option>
                                        <option value="Agriculture">Agriculture</option>
                                        <option value="Manufacturing">Manufacturing</option>
                                        <option value="Transportation">Transportation</option>
                                        <option value="Others">Others (Specify)</option>
                                    </select>
                                    <label for="businessNature">Nature of Business</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="number" class="form-control" id="controlNo" name="controlNo" placeholder="Control No." min="0" onkeydown="return !['e','E','-','+','.',','].includes(event.key)">
                                    <label for="controlNo">Control No.</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <select class="form-select" id="businessClearancePurpose" name="purpose" required>
                                        <option value="" selected disabled>Choose Purpose</option>
                                        <option value="New">New</option>
                                        <option value="Renewal">Renewal</option>
                                        <option value="Closure">Closure</option>
                                        <option value="Expansion">Expansion</option>
                                    </select>
                                    <label for="businessClearancePurpose">Purpose</label>
                                </div>

                                <div class="form-floating">
                                    <select class="form-select" id="ownership" name="ownership" required>
                                        <option value="" selected disabled>Choose Ownership</option>
                                        <option value="Sole Proprietorship">Sole Proprietorship</option>
                                        <option value="Partnership">Partnership</option>
                                        <option value="Corporation">Corporation</option>
                                        <option value="Cooperative">Cooperative</option>
                                    </select>
                                    <label for="ownership">Ownership</label>
                                </div>

                            <?php } elseif ($documentsRow['documentTypeID'] == 1 || $documentsRow['documentTypeID'] == 5 || $documentsRow['documentTypeID'] == 9) { ?>
                                
                                <p class="note mb-3">Please select the purpose for your request:</p>

                                <div class="form-floating">
                                    <select class="form-select selectPurpose" id="purpose" name="purpose" required>
                                        <option value="" selected disabled>Choose Purpose</option>
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
                                    <label for="purpose">Purpose</label>
                                </div>

                            <?php } elseif ($documentsRow['documentTypeID'] == 3) { ?>
                                
                                <p class="note mb-3">Please select the purpose for your request:</p>

                                <div class="form-floating">
                                    <select class="form-select" id="constructionClearancePurpose" name="purpose" required>
                                        <option value="" selected disabled>Choose Purpose</option>
                                        <option value="New Construction">New Construction</option>
                                        <option value="House Renovation">House Renovation</option>
                                        <option value="Extension / Expansion">Extension / Expansion</option>
                                        <option value="Fence Construction">Fence Construction</option>
                                        <option value="Demolition">Demolition</option>
                                        <option value="Repair / Maintenance">Repair / Maintenance</option>
                                    </select>
                                    <label for="constructionClearancePurpose">Purpose</label>
                                </div>

                            <?php } elseif ($documentsRow['documentTypeID'] == 7) { ?>

                                <p class="note mb-3">Please fill out your marriage details below.</p>
                            
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="spouseName" name="spouseName" placeholder="Spouse Name" required>
                                    <label for="spouseName">Spouse Name</label>
                                </div>

                                <div class="form-floating">
                                    <input type="number" class="form-control" id="marriageYear" name="marriageYear" placeholder="Year of Marriage (e.g., 2003)" min="1900" max="<?php echo date('Y'); ?>" oninput="if(this.value.length > 4) this.value = this.value.slice(0, 4);" onkeydown="return !['e','E','-','+','.',','].includes(event.key)" required>
                                    <label for="marriageYear">Year of Marriage (e.g., 2003)</label>
                                </div>

                            <?php } elseif ($documentsRow['documentTypeID'] == 10) { ?>

                                <p class="note mb-3">Please enter the number of children you have.</p>

                                <div class="form-floating">
                                    <input type="number" class="form-control" id="childNo" name="childNo" placeholder="Number of Children (e.g., 2)" min="0" oninput="if(this.value.length > 2) this.value = this.value.slice(0, 2);" onkeydown="return !['e','E','-','+','.',','].includes(event.key)" required>
                                    <label for="childNo">Number of Children (e.g., 2)</label>
                                </div>

                            <?php } else { ?>
                                
                                <p class="note">No additional details are required for this request.</p>

                                <small class="text-muted">Click proceed for the confirmation.</small>

                            <?php } ?>
                            
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <input type="hidden" value="<?php echo $documentsRow['documentTypeID']; ?>" name="documentTypeID">
                            <button type="submit" class="btn btn-primary proceedButton" name="proceedButton">
                                Proceed
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    <?php } 
    
    } else { ?>

        <div class="col">
            <div class="noDocument text-center text-muted pt-4">
                <i class="fas fa-inbox fa-3x text-muted mb-2"></i>
                <p>No "<?php echo $searchDisplay; ?>" document found.</p>
            </div>
        </div>

    <?php } ?>
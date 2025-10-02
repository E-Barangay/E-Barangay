<?php

function toCamelCase($string) {
    $string = ucwords(strtolower($string));
    $string = str_replace(' ', '', $string);
    return lcfirst($string);
}

while ($documentsRow = mysqli_fetch_assoc($documentsResult)) { 
    $modalID = toCamelCase($documentsRow['documentName']);    
?>

    <div class="col-6 col-md-4 col-lg-4 p-1">
        <div class="documentCard card my-2">
            <img src="assets/images/documents/<?php echo $documentsRow['documentImage'] ?>" class="card-img-top"
                style="width: 100%; height: 500px; object-fit: cover;" alt="...">
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
        
                <div class="modal-header" style="background-color: #19AFA5; color: white;">
                    <h1 class="modal-title fs-5" id="<?php echo $modalID; ?>Label">
                        <?php echo $documentsRow['documentName']; ?>
                    </h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <?php if ($documentsRow['documentName'] == "Business Clearance") { ?>
                        
                        <p class="mb-3">Please fill in your business details and choose the purpose of your request:</p>

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="businessName" name="businessName" placeholder="Business Name" required>
                            <label for="businessName">Business Name</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="businessAddress" name="businessAddress" placeholder="Business Address" required>
                            <label for="businessAddress">Business Address</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="ownerName" name="ownerName" placeholder="Owner's Name" required>
                            <label for="ownerName">Owner's Name</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="ownerAddress" name="ownerAddress" placeholder="Owner's Address" required>
                            <label for="ownerAddress">Owner's Address</label>
                        </div>

                        <div class="form-floating mb-3">
                            <select class="form-select" id="natureOfBusiness" name="natureOfBusiness" required>
                                <option selected disabled>Choose Nature of Business</option>
                                <option value="Sari-Sari Store">Sari-Sari Store</option>
                                <option value="Food & Beverage">Food & Beverage</option>
                                <option value="Retail">Retail</option>
                                <option value="Services">Services</option>
                                <option value="Agriculture">Agriculture</option>
                                <option value="Manufacturing">Manufacturing</option>
                                <option value="Transportation">Transportation</option>
                                <option value="Others">Others (Specify)</option>
                            </select>
                            <label for="natureOfBusiness">Nature of Business</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="controlNo" name="controlNo" placeholder="Control No." required>
                            <label for="controlNo">Control No.</label>
                        </div>

                        <div class="form-floating mb-3">
                            <select class="form-select" id="purpose" name="purpose" required>
                                <option selected disabled>Choose Purpose</option>
                                <option value="New">New</option>
                                <option value="Renewal">Renewal</option>
                                <option value="Closure">Closure</option>
                                <option value="Expansion">Expansion</option>
                            </select>
                            <label for="purpose">Purpose</label>
                        </div>

                        <div class="form-floating">
                            <select class="form-select" id="ownership" name="ownership" required>
                                <option selected disabled>Choose Ownership</option>
                                <option value="Sole Proprietorship">Sole Proprietorship</option>
                                <option value="Partnership">Partnership</option>
                                <option value="Corporation">Corporation</option>
                                <option value="Cooperative">Cooperative</option>
                            </select>
                            <label for="ownership">Ownership</label>
                        </div>

                    <?php } elseif ($documentsRow['documentName'] == "Barangay Clearance" || $documentsRow['documentName'] == "Good Health" || $documentsRow['documentName'] == "Residency") { ?>
                        
                        <p class="mb-3">Please select the purpose for your request:</p>

                        <div class="form-floating">
                            <select class="form-select selectPurpose" id="<?php echo $modalID; ?>" name="purpose" required>
                                <option selected disabled>Choose Purpose</option>
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

                    <?php } elseif ($documentsRow['documentName'] == "Construction Clearance") { ?>
                        
                        <p class="mb-3">Please select the purpose for your request:</p>

                        <div class="form-floating">
                            <select class="form-select" id="purpose" name="purpose" required>
                                <option selected disabled>Choose Purpose</option>
                                <option value="New Construction">New Construction</option>
                                <option value="House Renovation">House Renovation</option>
                                <option value="Extension / Expansion">Extension / Expansion</option>
                                <option value="Fence Construction">Fence Construction</option>
                                <option value="Demolition">Demolition</option>
                                <option value="Repair / Maintenance">Repair / Maintenance</option>
                            </select>
                            <label for="purpose">Purpose</label>
                        </div>

                    <?php } elseif ($documentsRow['documentName'] == "Joint Cohabitation") { ?>
                    
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="spouseName" name="spouseName" placeholder="Spouse Name" required>
                            <label for="spouseName">Spouse Name</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="number" class="form-control" id="yearOfMarriage" name="yearOfMarriage" placeholder="Year of Marriage" min="1900" max="<?php echo date('Y'); ?>" required>
                            <label for="yearOfMarriage">Year of Marriage</label>
                        </div>

                    <?php } else { ?>
                        
                        <p>No additional details are required for this request.</p>

                        <small class="text-muted">Click proceed for the confirmation.</small>

                    <?php } ?>
                    
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary proceedButton" name="proceedButton">
                        Proceed
                    </button>
                </div>

            </div>
        </div>
    </div>

<?php } ?>
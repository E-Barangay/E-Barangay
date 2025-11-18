<div class="row">
    <div class="col" style="color: black;">
        <div class="row">
            <div class="col">
                <p style="text-align: center;">(FIRST TIME JOB SEEKERS ASSISTANCE ACT - R.A. NO.11261)</p>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <p>To whom it may concern:</p>
            </div>
        </div>
        <div class="row">
            <div class="col" style="text-indent: 48px; text-align: justify;">
                <p>This is to certify that <strong><?php echo $fullName ?></strong>, <?php echo $age . " years old, " . $gender . ", " . $civilStatus . " " . $citizenship . " is a " . $residencyType . " resident of " ?><?php echo implode(', ', array_filter([$blockLotNo, $subdivisionName, $phase, $purok, $streetName, $barangayName, $cityName, $provinceName])) ?>, and <?php echo ($gender === "Male" ? 'he' : 'she') . " is residing in this Barangay since "?><strong><?php echo $residingYear ?></strong> up to present. And qualified availee of <strong>RA 11261 or the First Time Jobseekers Assistance Act of 2019.</strong></p>
                <p>This certifies that the holder/bearer was informed of <?php echo ($gender === "Male" ? 'his' : 'her') ?> rights including the duties and responsibilities accorded by <strong>RA 11261</strong> through the <strong>Oath of Undertaking</strong> <?php echo ($gender === "Male" ? 'he' : 'she'); ?> has signed and executed in the presence of our Barangay Officials.</p>
                <p>This certification is valid only for one [1] year upon the date of the issuance and by the request of the above-named person for is a first time job seeker.</p>
                <p>Issued this <strong><?php echo date("jS") ?> day of <?php echo date("F, Y") ?> </strong> at the office of the Barangay Chairman, Barangay San Antonio, Santo Tomas City, Batangas.</p>
            </div>
        </div>
    </div>
</div>
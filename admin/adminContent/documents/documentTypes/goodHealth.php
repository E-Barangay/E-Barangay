<div class="row">
    <div class="col" style="color: black;">
        <div class="row">
            <div class="col">
                <p>To whom it may concern:</p>
            </div>
        </div>
        <div class="row">
            <div class="col" >
                <p style="text-indent: 48px; text-align: justify;">This is to certify that <strong><?php echo $fullName ?></strong>, Legal Age, <?php echo $gender . ", " . $citizenship . " and a " . $residencyType . " resident of " ?><?php echo implode(', ', array_filter([$blockLotNo, $subdivisionName, $phase, $purok, $streetName, $barangayName, $cityName, $provinceName])) ?>.</p>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <p style="text-indent: 48px; text-align: justify;">It is further certified that the following individual has been examined and found to be in <strong>Good Physical and Mental Health</strong>, and is fit to work.</p>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <p style="text-indent: 48px; text-align: justify;">This certification is being issued upon the request of the above-named person for <strong><?php echo $purpose ?></strong>.</p>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <p style="text-indent: 48px; text-align: justify;">Issued this <strong><?php echo date("jS") ?> day of <?php echo date("F, Y") ?></strong> at the office of the Barangay Chairman, Barangay San Antonio, Santo Tomas City, Batangas.</p>
            </div>
        </div>
    </div>
</div>

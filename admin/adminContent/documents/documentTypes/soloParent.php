<div class="row">
    <div class="col" style="color: black;">
        <div class="row">
            <div class="col">
                <p>To whom it may concern:</p>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <p style="text-indent: 48px; text-align: justify;">This is to certify that <strong><?php echo $fullName ?></strong>, <?php echo $age .  " years old, " . $residencyType . " of Barangay San Antonio, Santo Tomas, Batangas is a Solo Parent "?>since <strong><?php echo $soloParentSinceDate ?></strong>.</p>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <p style="text-indent: 48px; text-align: justify;">This is to certify that furthermore that <?php echo ($gender === "Male" ? 'he' : 'she') . " is living with " . ($gender === "Male" ? 'his' : 'her') ?> <strong><?php echo $childNo . ($childNo == 1 ? ' child' : ' children') ?></strong> who <?php echo ($childNo == 1 ? 'depend' : 'depends') . " on " . ($gender === "Male" ? 'his' : 'her') . " support."; ?></p>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <p style="text-indent: 48px; text-align: justify;">Issued this <strong><?php echo date("jS") ?> day of <?php echo date("F, Y") ?></strong> at the office of the Barangay Chairman, Barangay San Antonio, Santo Tomas City, Batangas.</p>
            </div>
        </div>
    </div>
</div>
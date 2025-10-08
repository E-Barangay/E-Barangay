<div class="col">
    <div class="row">
        <div class="col">
            <span>To whom it may concern:</span>
        </div>
    </div>
    <div class="row pt-4">
        <div class="col">
            <p style="text-indent: 48px; text-align: justify;">This is to certify that <?php echo $fullName . ", " . $age .  " years old, " . $residencyType . " of Barangay San Antonio, Santo Tomas, Batangas is a Solo Parent since 2020." ?></p>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <p style="text-indent: 48px; text-align: justify;">This is to certify that furthermore that <?php echo ($gender === "Male" ? 'he' : 'she') . " is living with " . ($gender === "Male" ? 'his' : 'her') . " " . $childNo . ($childNo == 1 ? ' child' : ' children') . ", age ____, who " . ($childNo == 1 ? 'depend' : 'depends') . " on " . ($gender === "Male" ? 'his' : 'her') . " support."; ?></p>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <p style="text-indent: 48px; text-align: justify;">Issued this <?php echo date("jS") ?> day of <?php echo date("F, Y") ?> at the office of the Barangay Chairman, Barangay San Antonio, Santo Tomas City, Batangas.</p>
        </div>
    </div>
</div>
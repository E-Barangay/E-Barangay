<?php
include_once __DIR__ . '/../../sharedAssets/connect.php';

$documentID = $_GET['id'] ?? 0;

if (!$documentID) {
    header("Location: index.php?page=document");
    exit();
}

$documentQuery = "SELECT 
    d.documentID, 
    d.purpose, 
    d.documentStatus, 
    d.requestDate,
    d.approvalDate,
    dt.documentName,
    c.categoryName,
    CONCAT(ui.firstName, ' ', ui.middleName, ' ', ui.lastName) AS fullname,
    ui.firstName,
    ui.middleName,
    ui.lastName,
    ui.suffix,
    ui.gender,
    ui.age,
    ui.birthDate,
    ui.birthPlace,
    ui.civilStatus,
    ui.citizenship,
    ui.occupation,
    ui.residencyType,
    ui.lengthOfStay,
    u.phoneNumber,
    u.email,
    a.cityName,
    a.provinceName,
    a.barangayName,
    a.streetName,
    a.houseNo,
    a.phase,
    a.subdivisionName,
    a.purok
FROM documents d
JOIN documenttypes dt ON d.documentTypeID = dt.documentTypeID
JOIN categories c ON dt.categoryID = c.categoryID
JOIN users u ON d.userID = u.userID
JOIN userinfo ui ON u.userID = ui.userID
LEFT JOIN addresses a ON ui.userInfoID = a.userInfoID
WHERE d.documentID = ? AND d.documentStatus = 'Approved'";

$stmt = $pdo->prepare($documentQuery);
$stmt->execute([$documentID]);
$document = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$document) {
    header("Location: index.php?page=document");
    exit();
}

$addressParts = array_filter([
    $document['houseNo'],
    $document['streetName'],
    $document['barangayName'],
    $document['cityName'],
    $document['provinceName']
]);
$fullAddress = implode(', ', $addressParts);

$middleInitial = $document['middleName'] ? strtoupper(substr($document['middleName'], 0, 1)) . '.' : '';
$formattedName = trim($document['firstName'] . ' ' . $middleInitial . ' ' . $document['lastName'] . ' ' . $document['suffix']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="../../assets/images/logoSanAntonio.png">

    <title><?= htmlspecialchars($document['documentName']) ?> - <?= htmlspecialchars($formattedName) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Times+New+Roman:wght@400;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4;
            margin: 1in;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            line-height: 1.4;
            color: #000;
            background: white;
        }
        
        .document-container {
            max-width: 8.5in;
            margin: 0 auto;
            background: white;
            min-height: 11in;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .letterhead {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .letterhead h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 5px 0;
        }
        
        .letterhead h2 {
            font-size: 16px;
            font-weight: normal;
            margin: 3px 0;
        }
        
        .letterhead p {
            font-size: 12px;
            margin: 2px 0;
        }
        
        .document-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            text-decoration: underline;
            margin: 40px 0 30px 0;
            letter-spacing: 2px;
        }
        
        .document-body {
            font-size: 14px;
            line-height: 1.8;
            text-align: justify;
            margin: 30px 0;
        }
        
        .signature-section {
            margin-top: 80px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            text-align: center;
            width: 250px;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
            height: 50px;
        }
        
        .official-seal {
            margin-top: 50px;
            text-align: left;
        }
        
        .seal-box {
            width: 150px;
            height: 150px;
            border: 2px solid #000;
            border-radius: 50%;
            display: inline-block;
            position: relative;
            margin-right: 50px;
        }
        
        .seal-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 10px;
            text-align: center;
            font-weight: bold;
        }
        
        .document-footer {
            margin-top: 50px;
            font-size: 12px;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        
        @media print {
            body {
                margin: 0;
                background: white;
            }
            
            .document-container {
                box-shadow: none;
                margin: 0;
                max-width: none;
            }
            
            .no-print {
                display: none;
            }
        }
        
        .print-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .info-line {
            margin: 8px 0;
        }
        
        .text-uppercase {
            text-transform: uppercase;
        }
    </style>
</head>

<body>
    <div class="print-controls no-print">
        <button onclick="window.print()" class="btn btn-primary btn-sm me-2">
            <i class="fas fa-print"></i> Print
        </button>
        <button onclick="window.close()" class="btn btn-secondary btn-sm">
            Close
        </button>
    </div>

    <div class="document-container">
        <div class="letterhead">
            <h1>REPUBLIC OF THE PHILIPPINES</h1>
            <h2>PROVINCE OF <?= strtoupper($document['provinceName'] ?? 'LAGUNA') ?></h2>
            <h2>MUNICIPALITY/CITY OF <?= strtoupper($document['cityName'] ?? 'MAJAYJAY') ?></h2>
            <h1>BARANGAY <?= strtoupper($document['barangayName'] ?? 'BURGOS') ?></h1>
            <p>Tel. No: (049) 123-4567 | Email: barangay@<?= strtolower($document['barangayName'] ?? 'burgos') ?>.gov.ph</p>
        </div>

        <div class="document-title">
            <?= strtoupper($document['documentName']) ?>
        </div>

        <div class="document-body">
            <p><strong>TO WHOM IT MAY CONCERN:</strong></p>
            
            <p style="text-indent: 50px;">
                This is to certify that 
                <strong class="text-uppercase"><?= htmlspecialchars($formattedName) ?></strong>,
                <?= $document['age'] ? htmlspecialchars($document['age']) . ' years of age' : '' ?>
                <?= $document['gender'] ? ', ' . strtolower($document['gender']) : '' ?>
                <?= $document['civilStatus'] ? ', ' . strtolower($document['civilStatus']) : '' ?>
                <?= $document['citizenship'] ? ', ' . htmlspecialchars($document['citizenship']) . ' citizen' : '' ?>
                is a 
                <?= $document['residencyType'] ? strtolower($document['residencyType']) : 'bonafide' ?> 
                resident of this barangay.
            </p>

            <?php if ($fullAddress): ?>
            <div class="info-line">
                <strong>Address:</strong> <?= htmlspecialchars($fullAddress) ?>
            </div>
            <?php endif; ?>

            <?php if ($document['occupation']): ?>
            <div class="info-line">
                <strong>Occupation:</strong> <?= htmlspecialchars($document['occupation']) ?>
            </div>
            <?php endif; ?>

            <?php if ($document['birthDate']): ?>
            <div class="info-line">
                <strong>Date of Birth:</strong> <?= date('F d, Y', strtotime($document['birthDate'])) ?>
            </div>
            <?php endif; ?>

            <?php if ($document['birthPlace']): ?>
            <div class="info-line">
                <strong>Place of Birth:</strong> <?= htmlspecialchars($document['birthPlace']) ?>
            </div>
            <?php endif; ?>

            <p style="text-indent: 50px; margin-top: 30px;">
                This certification is being issued upon request of the above-named person 
                for <strong><?= strtoupper($document['purpose']) ?></strong> 
                and for whatever legal purpose it may serve.
            </p>

            <p style="text-indent: 50px;">
                Given this <?= date('jS') ?> day of <?= date('F Y') ?> 
                at Barangay <?= htmlspecialchars($document['barangayName'] ?? 'Burgos') ?>, 
                <?= htmlspecialchars($document['cityName'] ?? 'Majayjay') ?>, 
                <?= htmlspecialchars($document['provinceName'] ?? 'Laguna') ?>.
            </p>
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <strong>APPLICANT'S SIGNATURE</strong>
                <br>
                <small>(Signature over Printed Name)</small>
            </div>
            
            <div class="signature-box">
                <div class="signature-line"></div>
                <strong>BARANGAY CAPTAIN</strong>
                <br>
                <small>Punong Barangay</small>
            </div>
        </div>

        <div class="official-seal">
            <div class="seal-box">
                <div class="seal-text">
                    OFFICIAL<br>
                    SEAL<br>
                    BARANGAY<br>
                    <?= strtoupper($document['barangayName'] ?? 'BURGOS') ?>
                </div>
            </div>
            <div style="display: inline-block; vertical-align: top; margin-top: 50px;">
                <div><strong>Document Control No:</strong> <?= str_pad($document['documentID'], 6, '0', STR_PAD_LEFT) ?></div>
                <div><strong>Date Issued:</strong> <?= date('F d, Y') ?></div>
                <div><strong>Valid Until:</strong> <?= date('F d, Y', strtotime('+1 year')) ?></div>
            </div>
        </div>

        <div class="document-footer">
            <p><em>This document is valid only for the purpose stated above and within the period specified.</em></p>
            <p><strong>NOT VALID WITHOUT THE OFFICIAL SEAL</strong></p>
        </div>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
        
        window.onafterprint = function() {
            setTimeout(function() {
                window.close();
            }, 1000);
        };
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
</body>

</html>
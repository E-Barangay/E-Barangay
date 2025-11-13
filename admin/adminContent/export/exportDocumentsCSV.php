<?php
include_once __DIR__ . "/../../../sharedAssets/connect.php";

$from = isset($_GET['from']) ? trim($_GET['from']) : null;
$to = isset($_GET['to']) ? trim($_GET['to']) : null;
$type = isset($_GET['type']) ? trim($_GET['type']) : null;

function valid_date($date)
{
    if (!$date)
        return false;
    $check = DateTime::createFromFormat('Y-m-d', $date);
    return $check && $check->format('Y-m-d') === $date;
}

if (!valid_date($from))
    $from = null;
if (!valid_date($to))
    $to = null;

$where = [];
if ($from && $to) {
    $where[] = "DATE(d.requestDate) BETWEEN '$from' AND '$to'";
}
if ($type && strtolower($type) !== 'all') {
    $where[] = "dt.documentName = '" . $conn->real_escape_string($type) . "'";
}

$whereSQL = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";

$documentTypeName = ($type && strtolower($type) !== 'all') ? $type : "ALL DOCUMENT TYPES";
$isAllTypes = (strtoupper($documentTypeName) === "ALL DOCUMENT TYPES");

$periodText = "FOR THE MONTH OF " . strtoupper(date('F Y'));
if ($from && $to) {
    $fromDate = new DateTime($from);
    $toDate = new DateTime($to);
    if ($fromDate->format('Y-m') === $toDate->format('Y-m')) {
        $periodText = "FOR THE MONTH OF " . strtoupper($fromDate->format('F Y'));
    } else {
        $periodText = strtoupper($fromDate->format('F Y')) . " TO " . strtoupper($toDate->format('F Y'));
    }
}

$documentquery = "
    SELECT 
        d.documentID,
        dt.documentName,
        d.documentStatus,
        DATE(d.requestDate) AS requestDate,
        DATE(d.approvalDate) AS approvalDate,
        userinfo.lastName,
        userinfo.firstName,
        userinfo.middleName,
        userinfo.age,
        userinfo.gender,
        userinfo.civilStatus,
        userinfo.occupation,
        userinfo.studentLevel,
        userinfo.collegeYear,
        userinfo.collegeCourse,
        userinfo.shsTrack,
        userinfo.work AS outOfSchoolYouth,
        CONCAT(
            addresses.streetName, ', ',
            addresses.blockLotNo, ', ',
            addresses.subdivisionName, ', ',
            addresses.purok, ', ',
            addresses.barangayName, ', ',
            addresses.cityName, ', ',
            addresses.provinceName
        ) AS address,
        users.phoneNumber AS contactNumber,
        d.purpose,
        YEAR(d.requestDate) AS requestYear,
        MONTH(d.requestDate) AS requestMonth,
        DAY(d.requestDate) AS requestDay
    FROM documents d
    JOIN documenttypes dt ON d.documentTypeID = dt.documentTypeID
    LEFT JOIN userinfo ON d.userID = userinfo.userID
    LEFT JOIN users ON userinfo.userID = users.userID
    LEFT JOIN addresses ON userinfo.userID = addresses.userInfoID
    $whereSQL
    ORDER BY dt.documentName ASC, d.requestDate DESC, userinfo.lastName ASC
";

$result = $conn->query($documentquery);
$data = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

$filename = 'San_Antonio-' . str_replace(' ', '_', $documentTypeName) . '_month_of_' . ($from && $to ? date('F_Y', strtotime($from)) : date('F_Y')) . '.xls';

header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

echo '<?xml version="1.0"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
  <Author>Barangay San Antonio</Author>
  <Created>' . date('Y-m-d\TH:i:s\Z') . '</Created>
 </DocumentProperties>
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Center" ss:Horizontal="Center"/>
   <Font ss:FontName="Arial" ss:Size="10"/>
  </Style>
  <Style ss:ID="HeaderOrange">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="Arial" ss:Size="9" ss:Bold="1"/>
   <Interior ss:Color="#31afab" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="DataCell">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="Arial" ss:Size="9"/>
  </Style>
  <Style ss:ID="DataCellLeft">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="Arial" ss:Size="9"/>
  </Style>
  <Style ss:ID="TitleCell">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Font ss:FontName="Arial" ss:Size="14" ss:Bold="1"/>
  </Style>
  <Style ss:ID="SubtitleCell">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Font ss:FontName="Arial" ss:Size="12" ss:Bold="1"/>
  </Style>
  <Style ss:ID="SignatureCell">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Font ss:FontName="Arial" ss:Size="11" ss:Bold="1"/>
  </Style>
  <Style ss:ID="SignatureTitleCell">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Font ss:FontName="Arial" ss:Size="10" ss:Italic="1"/>
  </Style>
 </Styles>
 <Worksheet ss:Name="' . htmlspecialchars($documentTypeName) . '">
  <Table ss:DefaultRowHeight="15">';

if ($isAllTypes) {
    echo '
   <Column ss:Width="120"/>
   <Column ss:Width="80"/>
   <Column ss:Width="150"/>
   <Column ss:Width="150"/>
   <Column ss:Width="80"/>
   <Column ss:Width="80"/>
   <Column ss:Width="200"/>
   <Column ss:Width="100"/>';
} else {
    echo '
   <Column ss:Width="80"/>
   <Column ss:Width="80"/>
   <Column ss:Width="80"/>
   <Column ss:Width="50"/>
   <Column ss:Width="50"/>
   <Column ss:Width="50"/>
   <Column ss:Width="50"/>
   <Column ss:Width="40"/>
   <Column ss:Width="40"/>
   <Column ss:Width="80"/>
   <Column ss:Width="50"/>
   <Column ss:Width="120"/>
   <Column ss:Width="80"/>';
}

echo '
   <Row ss:Height="60">
    <Cell ss:MergeAcross="' . ($isAllTypes ? '1' : '2') . '" ss:StyleID="TitleCell">
     <Data ss:Type="String">[Logo]</Data>
    </Cell>
    <Cell ss:MergeAcross="' . ($isAllTypes ? '3' : '9') . '" ss:StyleID="TitleCell">
     <Data ss:Type="String">BARANGAY SAN ANTONIO</Data>
    </Cell>
    <Cell ss:MergeAcross="' . ($isAllTypes ? '1' : '2') . '" ss:StyleID="TitleCell">
     <Data ss:Type="String">[Logo]</Data>
    </Cell>
   </Row>
   <Row ss:Height="30">
    <Cell ss:MergeAcross="' . ($isAllTypes ? '1' : '2') . '"/>
    <Cell ss:MergeAcross="' . ($isAllTypes ? '3' : '9') . '" ss:StyleID="SubtitleCell">
     <Data ss:Type="String">' . htmlspecialchars($documentTypeName) . '</Data>
    </Cell>
    <Cell ss:MergeAcross="' . ($isAllTypes ? '1' : '2') . '"/>
   </Row>
   <Row ss:Height="25">
    <Cell ss:MergeAcross="' . ($isAllTypes ? '1' : '2') . '"/>
    <Cell ss:MergeAcross="' . ($isAllTypes ? '3' : '9') . '" ss:StyleID="SubtitleCell">
     <Data ss:Type="String">' . $periodText . '</Data>
    </Cell>
    <Cell ss:MergeAcross="' . ($isAllTypes ? '1' : '2') . '"/>
   </Row>
   <Row ss:Height="10"/>';

if ($isAllTypes) {
    echo '
   <Row ss:Height="30">
    <Cell ss:StyleID="HeaderOrange"><Data ss:Type="String">DOCUMENT TYPE</Data></Cell>
    <Cell ss:StyleID="HeaderOrange"><Data ss:Type="String">REQUEST DATE</Data></Cell>
    <Cell ss:StyleID="HeaderOrange"><Data ss:Type="String">FULL NAME</Data></Cell>
    <Cell ss:StyleID="HeaderOrange"><Data ss:Type="String">PURPOSE</Data></Cell>
    <Cell ss:StyleID="HeaderOrange"><Data ss:Type="String">STATUS</Data></Cell>
    <Cell ss:StyleID="HeaderOrange"><Data ss:Type="String">APPROVAL DATE</Data></Cell>
    <Cell ss:StyleID="HeaderOrange"><Data ss:Type="String">ADDRESS</Data></Cell>
    <Cell ss:StyleID="HeaderOrange"><Data ss:Type="String">CONTACT NUMBER</Data></Cell>
   </Row>';
    
    if (count($data) > 0) {
        foreach ($data as $row) {
            $fullName = trim(($row['lastName'] ?? '') . ', ' . ($row['firstName'] ?? '') . ' ' . ($row['middleName'] ?? ''));
            echo '
   <Row>
    <Cell ss:StyleID="DataCell"><Data ss:Type="String">' . htmlspecialchars($row['documentName']) . '</Data></Cell>
    <Cell ss:StyleID="DataCell"><Data ss:Type="String">' . htmlspecialchars($row['requestDate']) . '</Data></Cell>
    <Cell ss:StyleID="DataCellLeft"><Data ss:Type="String">' . htmlspecialchars($fullName) . '</Data></Cell>
    <Cell ss:StyleID="DataCell"><Data ss:Type="String">' . htmlspecialchars($row['purpose'] ?? '') . '</Data></Cell>
    <Cell ss:StyleID="DataCell"><Data ss:Type="String">' . htmlspecialchars($row['documentStatus']) . '</Data></Cell>
    <Cell ss:StyleID="DataCell"><Data ss:Type="String">' . htmlspecialchars($row['approvalDate'] ?? '') . '</Data></Cell>
    <Cell ss:StyleID="DataCell"><Data ss:Type="String">' . htmlspecialchars($row['address'] ?? '') . '</Data></Cell>
    <Cell ss:StyleID="DataCell"><Data ss:Type="String">' . htmlspecialchars($row['contactNumber'] ?? '') . '</Data></Cell>
   </Row>';
        }
    } else {
        echo '
   <Row>
    <Cell ss:MergeAcross="7" ss:StyleID="DataCell">
     <Data ss:Type="String">No records found</Data>
    </Cell>
   </Row>';
    }
} else {
    echo '
   <Row ss:Height="40">
    <Cell ss:MergeAcross="2" ss:StyleID="HeaderOrange"><Data ss:Type="String">NAME OF BENEFICIARY/AVAILEE</Data></Cell>
    <Cell ss:MergeDown="1" ss:StyleID="HeaderOrange"><Data ss:Type="String">AGE</Data></Cell>
    <Cell ss:MergeAcross="2" ss:StyleID="HeaderOrange"><Data ss:Type="String">DATE OF BIRTH</Data></Cell>
    <Cell ss:MergeAcross="1" ss:StyleID="HeaderOrange"><Data ss:Type="String">SEX/&#10;GENDER</Data></Cell>
    <Cell ss:MergeAcross="4" ss:StyleID="HeaderOrange"><Data ss:Type="String">EDUCATIONAL LEVEL (check applicable)</Data></Cell>
   </Row>
   <Row ss:Height="35">
    <Cell ss:StyleID="HeaderOrange"><Data ss:Type="String">Last Name</Data></Cell>
    <Cell ss:StyleID="HeaderOrange"><Data ss:Type="String">First Name</Data></Cell>
    <Cell ss:StyleID="HeaderOrange"><Data ss:Type="String">Middle Name</Data></Cell>
    <Cell ss:Index="5" ss:StyleID="HeaderOrange"><Data ss:Type="String">Month</Data></Cell>
    <Cell ss:StyleID="HeaderOrange"><Data ss:Type="String">Date</Data></Cell>
    <Cell ss:StyleID="HeaderOrange"><Data ss:Type="String">Year</Data></Cell>
    <Cell ss:StyleID="HeaderOrange"><Data ss:Type="String">M</Data></Cell>
    <Cell ss:StyleID="HeaderOrange"><Data ss:Type="String">F</Data></Cell>
    <Cell ss:StyleID="HeaderOrange"><Data ss:Type="String">Elementary/&#10;High School</Data></Cell>
    <Cell ss:StyleID="HeaderOrange"><Data ss:Type="String">SHS</Data></Cell>
    <Cell ss:StyleID="HeaderOrange"><Data ss:Type="String">College</Data></Cell>
    <Cell ss:StyleID="HeaderOrange"><Data ss:Type="String">Course</Data></Cell>
    <Cell ss:StyleID="HeaderOrange"><Data ss:Type="String">Out of School&#10;Youth</Data></Cell>
   </Row>';
    
    if (count($data) > 0) {
        foreach ($data as $row) {
            $gender = strtolower($row['gender'] ?? '');
            $isMale = (strpos($gender, 'male') !== false && strpos($gender, 'female') === false);
            $isFemale = (strpos($gender, 'female') !== false);
            
            $studentLevel = strtolower($row['studentLevel'] ?? '');
            $isSHS = (strpos($studentLevel, 'shs') !== false || strpos($studentLevel, 'senior high') !== false);
            $isCollege = (strpos($studentLevel, 'college') !== false);
            $isElementaryOrHS = (!$isSHS && !$isCollege && !empty($studentLevel));
            
            $courseDisplay = '';
            if ($isSHS) {
                $courseDisplay = htmlspecialchars($row['shsTrack'] ?? '');
            } elseif ($isCollege) {
                $courseDisplay = htmlspecialchars($row['collegeCourse'] ?? '');
            }
            
            echo '
   <Row>
    <Cell ss:StyleID="DataCellLeft"><Data ss:Type="String">' . htmlspecialchars($row['lastName'] ?? '') . '</Data></Cell>
    <Cell ss:StyleID="DataCellLeft"><Data ss:Type="String">' . htmlspecialchars($row['firstName'] ?? '') . '</Data></Cell>
    <Cell ss:StyleID="DataCellLeft"><Data ss:Type="String">' . htmlspecialchars($row['middleName'] ?? '') . '</Data></Cell>
    <Cell ss:StyleID="DataCell"><Data ss:Type="String">' . htmlspecialchars($row['age'] ?? '') . '</Data></Cell>
    <Cell ss:StyleID="DataCell"><Data ss:Type="String">' . htmlspecialchars($row['requestMonth'] ?? '') . '</Data></Cell>
    <Cell ss:StyleID="DataCell"><Data ss:Type="String">' . htmlspecialchars($row['requestDay'] ?? '') . '</Data></Cell>
    <Cell ss:StyleID="DataCell"><Data ss:Type="String">' . htmlspecialchars($row['requestYear'] ?? '') . '</Data></Cell>
    <Cell ss:StyleID="DataCell"><Data ss:Type="String">' . ($isMale ? '✓' : '') . '</Data></Cell>
    <Cell ss:StyleID="DataCell"><Data ss:Type="String">' . ($isFemale ? '✓' : '') . '</Data></Cell>
    <Cell ss:StyleID="DataCell"><Data ss:Type="String">' . ($isElementaryOrHS ? '✓' : '') . '</Data></Cell>
    <Cell ss:StyleID="DataCell"><Data ss:Type="String">' . ($isSHS ? '✓' : '') . '</Data></Cell>
    <Cell ss:StyleID="DataCell"><Data ss:Type="String">' . ($isCollege ? '✓' : '') . '</Data></Cell>
    <Cell ss:StyleID="DataCell"><Data ss:Type="String">' . $courseDisplay . '</Data></Cell>
    <Cell ss:StyleID="DataCell"><Data ss:Type="String"></Data></Cell>
   </Row>';
        }
    } else {
        echo '
   <Row>
    <Cell ss:MergeAcross="13" ss:StyleID="DataCell">
     <Data ss:Type="String">No records found</Data>
    </Cell>
   </Row>';
    }
}

echo '
   <Row ss:Height="10"/>
   <Row ss:Height="10"/>
   <Row ss:Height="10"/>
   <Row ss:Height="10"/>
   <Row ss:Height="20">
    <Cell ss:MergeAcross="' . ($isAllTypes ? '2' : '4') . '" ss:StyleID="SignatureCell">
     <Data ss:Type="String">MRS. MARY JOY M. MARTIREZ</Data>
    </Cell>
    <Cell ss:MergeAcross="' . ($isAllTypes ? '1' : '1') . '"/>
    <Cell ss:MergeAcross="' . ($isAllTypes ? '2' : '4') . '" ss:StyleID="SignatureCell">
     <Data ss:Type="String">HON. ULYSES M. RELLAMA</Data>
    </Cell>
   </Row>
   <Row ss:Height="18">
    <Cell ss:MergeAcross="' . ($isAllTypes ? '2' : '4') . '" ss:StyleID="SignatureTitleCell">
     <Data ss:Type="String">Barangay Secretary</Data>
    </Cell>
    <Cell ss:MergeAcross="' . ($isAllTypes ? '1' : '1') . '"/>
    <Cell ss:MergeAcross="' . ($isAllTypes ? '2' : '4') . '" ss:StyleID="SignatureTitleCell">
     <Data ss:Type="String">Punong Barangay</Data>
    </Cell>
   </Row>
   <Row ss:Height="10"/>
   <Row ss:Height="10"/>
   <Row ss:Height="15">
    <Cell ss:MergeAcross="' . ($isAllTypes ? '7' : '13') . '" ss:StyleID="DataCellLeft">
     <Data ss:Type="String">Prepared by:</Data>
    </Cell>
   </Row>
   <Row ss:Height="15">
    <Cell ss:MergeAcross="' . ($isAllTypes ? '7' : '13') . '" ss:StyleID="DataCellLeft">
     <Data ss:Type="String">DATED: ' . date('m-d-Y') . '</Data>
    </Cell>
   </Row>
  </Table>
  <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
   <PageSetup>
    <Layout x:Orientation="Landscape"/>
    <Header x:Margin="0.3"/>
    <Footer x:Margin="0.3"/>
    <PageMargins x:Bottom="0.47" x:Left="0.47" x:Right="0.47" x:Top="0.47"/>
   </PageSetup>
   <Print>
    <ValidPrinterInfo/>
    <PaperSizeIndex>9</PaperSizeIndex>
    <HorizontalResolution>600</HorizontalResolution>
    <VerticalResolution>600</VerticalResolution>
   </Print>
  </WorksheetOptions>
 </Worksheet>
</Workbook>';
exit;
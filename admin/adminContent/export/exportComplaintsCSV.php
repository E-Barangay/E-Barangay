<?php
include_once __DIR__ . "/../../../sharedAssets/connect.php";

$from = isset($_GET['from']) ? trim($_GET['from']) : null;
$to = isset($_GET['to']) ? trim($_GET['to']) : null;

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

if ($from && $to) {
  $complaintsQuery = "
        SELECT 
            complaintID,
            complaintTitle,
            complaintStatus,
            DATE(requestDate) AS requestDate
        FROM complaints
        WHERE DATE(requestDate) BETWEEN '$from' AND '$to'
        ORDER BY requestDate DESC
    ";
} else {
  $complaintsQuery = "
        SELECT 
            complaintID,
            complaintTitle,
            complaintStatus,
            DATE(requestDate) AS requestDate
        FROM complaints
        ORDER BY requestDate DESC
    ";
}

$complaintsResults = $conn->query($complaintsQuery);

$complaintsData = [];
if ($complaintsResults) {
  while ($row = $complaintsResults->fetch_assoc()) {
    $complaintsData[] = $row;
  }
}

$criminal = $civil = $others = 0;
$mediation = $conciliation = $arbitration = 0;
$repudiated = $withdrawn = $pending = $dismissed = $certified = $referred = 0;

foreach ($complaintsData as $row) {
  $status = strtolower(trim($row['complaintStatus'] ?? ''));

  if ($status === 'criminal') {
    $criminal++;
  } elseif ($status === 'civil') {
    $civil++;
  } elseif (in_array($status, ['mediation', 'conciliation', 'arbitration', 'repudiated', 'withdrawn', 'pending', 'dismissed', 'certified', 'referred'])) {
  } else {
    $others++;
  }

  if ($status === 'mediation') {
    $mediation++;
  } elseif ($status === 'conciliation') {
    $conciliation++;
  } elseif ($status === 'arbitration') {
    $arbitration++;
  }

  if ($status === 'repudiated') {
    $repudiated++;
  } elseif ($status === 'withdrawn') {
    $withdrawn++;
  } elseif ($status === 'pending') {
    $pending++;
  } elseif ($status === 'dismissed') {
    $dismissed++;
  } elseif ($status === 'certified') {
    $certified++;
  } elseif ($status === 'referred') {
    $referred++;
  }
}

$totalDispute = $criminal + $civil + $others;
$settledTotal = $mediation + $conciliation + $arbitration;
$unsettledTotal = $repudiated + $withdrawn + $pending + $dismissed + $certified + $referred;
$estimatedSavings = $settledTotal * 15000;

$filename = 'KP_Compliance_Report_' . ($from && $to ? $from . '_to_' . $to : date('Ymd')) . '.xls';

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
  <Author>Katarungang Pambarangay</Author>
  <Created>' . date('Y-m-d\TH:i:s\Z') . '</Created>
 </DocumentProperties>
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Center" ss:Horizontal="Center"/>
   <Font ss:FontName="Arial" ss:Size="10"/>
  </Style>
  <Style ss:ID="s62">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="Arial" ss:Size="10" ss:Bold="1"/>
   <Interior ss:Color="#FFC7CE" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s63">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="Arial" ss:Size="10" ss:Bold="1"/>
   <Interior ss:Color="#FFC7CE" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s64">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="Arial" ss:Size="10" ss:Bold="1"/>
   <Interior ss:Color="#BDD7EE" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s65">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="Arial" ss:Size="10" ss:Bold="1"/>
   <Interior ss:Color="#E2EFDA" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s66">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="Arial" ss:Size="10" ss:Bold="1"/>
  </Style>
  <Style ss:ID="s67">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="Arial" ss:Size="10" ss:Bold="1"/>
  </Style>
  <Style ss:ID="s68">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="Arial" ss:Size="10"/>
  </Style>
  <Style ss:ID="s69">
   <Alignment ss:Horizontal="Center" ss:Vertical="Top"/>
   <Font ss:FontName="Arial" ss:Size="14" ss:Bold="1"/>
  </Style>
  <Style ss:ID="s70">
   <Alignment ss:Horizontal="Center" ss:Vertical="Top"/>
   <Font ss:FontName="Arial" ss:Size="15" ss:Bold="1"/>
  </Style>
  <Style ss:ID="s71">
   <Alignment ss:Horizontal="Left" ss:Vertical="Top"/>
   <Font ss:FontName="Arial" ss:Size="11" ss:Bold="1"/>
  </Style>
 </Styles>
 <Worksheet ss:Name="KP Compliance Report">
  <Table ss:DefaultRowHeight="15">
   <Column ss:Width="80"/>
   <Column ss:Width="40"/>
   <Column ss:Width="60"/>
   <Column ss:Width="60"/>
   <Column ss:Width="60"/>
   <Column ss:Width="60"/>
   <Column ss:Width="70"/>
   <Column ss:Width="80"/>
   <Column ss:Width="80"/>
   <Column ss:Width="60"/>
   <Column ss:Width="80"/>
   <Column ss:Width="80"/>
   <Column ss:Width="70"/>
   <Column ss:Width="80"/>
   <Column ss:Width="80"/>
   <Column ss:Width="80"/>
   <Column ss:Width="60"/>
   <Column ss:Width="90"/>
   <Column ss:Width="60"/>
   <Column ss:Width="110"/>
   
   <!-- Title Row 1 -->
   <Row ss:Height="18">
    <Cell ss:MergeAcross="18" ss:StyleID="s69">
     <Data ss:Type="String">CONSOLIDATED KATARUNGANG PAMBARANGAY</Data>
    </Cell>
   </Row>
   
   <!-- Title Row 2 -->
   <Row ss:Height="18">
    <Cell ss:MergeAcross="18" ss:StyleID="s69">
     <Data ss:Type="String">COMPLIANCE REPORT ON THE ACTION TAKEN BY THE LUPONG TAGAPAMAYAPA</Data>
    </Cell>
   </Row>
   
   <!-- Date Period Row -->
   <Row ss:Height="20">
    <Cell ss:MergeAcross="18" ss:StyleID="s70">
     <Data ss:Type="String">';

if ($from && $to) {
  echo date("F d, Y", strtotime($from)) . " - " . date("F d, Y", strtotime($to));
} else {
  echo "All Records";
}

echo '</Data>
    </Cell>
   </Row>
   
   <!-- Empty Row -->
   <Row ss:Height="10"/>
   
   <!-- Section Title -->
   <Row ss:Height="15">
    <Cell ss:MergeAcross="18" ss:StyleID="s71">
     <Data ss:Type="String">CONSOLIDATED KATARUNGANG PAMBARANGAY</Data>
    </Cell>
   </Row>
   
   <!-- Empty Row -->
   <Row ss:Height="5"/>
   
   <!-- Header Row 1 -->
   <Row ss:Height="45">
    <Cell ss:MergeDown="2" ss:StyleID="s62"><Data ss:Type="String">PROVINCE&#10;CITY&#10;(1)</Data></Cell>
    <Cell ss:MergeDown="2" ss:StyleID="s62"><Data ss:Type="String">C/M</Data></Cell>
    <Cell ss:MergeAcross="3" ss:StyleID="s63"><Data ss:Type="String">NATURE OF DISPUTE (2)</Data></Cell>
    <Cell ss:MergeAcross="3" ss:StyleID="s64"><Data ss:Type="String">SETTLED CASES (3)</Data></Cell>
    <Cell ss:MergeAcross="6" ss:StyleID="s65"><Data ss:Type="String">UNSETTLED CASES (4)</Data></Cell>
    <Cell ss:MergeDown="2" ss:StyleID="s62"><Data ss:Type="String">REFERRED&#10;TO&#10;CONCERNED&#10;AGENCY&#10;(4g)</Data></Cell>
    <Cell ss:MergeDown="2" ss:StyleID="s62"><Data ss:Type="String">TOTAL&#10;(4g)</Data></Cell>
    <Cell ss:MergeDown="2" ss:StyleID="s62"><Data ss:Type="String">ESTIMATED&#10;GOVT&#10;SAVINGS</Data></Cell>
   </Row>
   
   <!-- Header Row 2 -->
   <Row ss:Height="35">
    <Cell ss:Index="3" ss:StyleID="s63"><Data ss:Type="String">CRIMINAL&#10;(2a)</Data></Cell>
    <Cell ss:StyleID="s63"><Data ss:Type="String">CIVIL&#10;(2b)</Data></Cell>
    <Cell ss:StyleID="s63"><Data ss:Type="String">OTHERS&#10;(2c)</Data></Cell>
    <Cell ss:StyleID="s63"><Data ss:Type="String">TOTAL&#10;(2d)</Data></Cell>
    <Cell ss:StyleID="s64"><Data ss:Type="String">MEDIATION&#10;(3a)</Data></Cell>
    <Cell ss:StyleID="s64"><Data ss:Type="String">CONCILIATION&#10;(3b)</Data></Cell>
    <Cell ss:StyleID="s64"><Data ss:Type="String">ARBITRATION&#10;(3c)</Data></Cell>
    <Cell ss:StyleID="s64"><Data ss:Type="String">TOTAL&#10;(3d)</Data></Cell>
    <Cell ss:StyleID="s65"><Data ss:Type="String">REPUDIATED&#10;(4a)</Data></Cell>
    <Cell ss:StyleID="s65"><Data ss:Type="String">WITHDRAWN&#10;(4b)</Data></Cell>
    <Cell ss:StyleID="s65"><Data ss:Type="String">PENDING&#10;(4c)</Data></Cell>
    <Cell ss:StyleID="s65"><Data ss:Type="String">DISMISSED&#10;(4d)</Data></Cell>
    <Cell ss:StyleID="s65"><Data ss:Type="String">CERTIFIED&#10;(4e)</Data></Cell>
    <Cell ss:StyleID="s65"><Data ss:Type="String">(4f)</Data></Cell>
    <Cell ss:StyleID="s65"><Data ss:Type="String">(4g)</Data></Cell>
   </Row>
   
   <!-- Header Row 3 (Empty cells with colors) -->
   <Row ss:Height="15">
    <Cell ss:Index="3" ss:StyleID="s63"><Data ss:Type="String"></Data></Cell>
    <Cell ss:StyleID="s63"><Data ss:Type="String"></Data></Cell>
    <Cell ss:StyleID="s63"><Data ss:Type="String"></Data></Cell>
    <Cell ss:StyleID="s63"><Data ss:Type="String"></Data></Cell>
    <Cell ss:StyleID="s64"><Data ss:Type="String"></Data></Cell>
    <Cell ss:StyleID="s64"><Data ss:Type="String"></Data></Cell>
    <Cell ss:StyleID="s64"><Data ss:Type="String"></Data></Cell>
    <Cell ss:StyleID="s64"><Data ss:Type="String"></Data></Cell>
    <Cell ss:StyleID="s65"><Data ss:Type="String"></Data></Cell>
    <Cell ss:StyleID="s65"><Data ss:Type="String"></Data></Cell>
    <Cell ss:StyleID="s65"><Data ss:Type="String"></Data></Cell>
    <Cell ss:StyleID="s65"><Data ss:Type="String"></Data></Cell>
    <Cell ss:StyleID="s65"><Data ss:Type="String"></Data></Cell>
    <Cell ss:StyleID="s65"><Data ss:Type="String"></Data></Cell>
    <Cell ss:StyleID="s65"><Data ss:Type="String"></Data></Cell>
   </Row>
   
   <!-- Data Row -->
   <Row ss:Height="20">
    <Cell ss:StyleID="s67"><Data ss:Type="String">CITY OF STO. TOMAS</Data></Cell>
    <Cell ss:StyleID="s66"><Data ss:Type="String"></Data></Cell>
    <Cell ss:StyleID="s66"><Data ss:Type="Number">' . $criminal . '</Data></Cell>
    <Cell ss:StyleID="s66"><Data ss:Type="Number">' . $civil . '</Data></Cell>
    <Cell ss:StyleID="s66"><Data ss:Type="Number">' . $others . '</Data></Cell>
    <Cell ss:StyleID="s66"><Data ss:Type="Number">' . $totalDispute . '</Data></Cell>
    <Cell ss:StyleID="s66"><Data ss:Type="Number">' . $mediation . '</Data></Cell>
    <Cell ss:StyleID="s66"><Data ss:Type="Number">' . $conciliation . '</Data></Cell>
    <Cell ss:StyleID="s66"><Data ss:Type="Number">' . $arbitration . '</Data></Cell>
    <Cell ss:StyleID="s66"><Data ss:Type="Number">' . $settledTotal . '</Data></Cell>
    <Cell ss:StyleID="s66"><Data ss:Type="Number">' . $repudiated . '</Data></Cell>
    <Cell ss:StyleID="s66"><Data ss:Type="Number">' . $withdrawn . '</Data></Cell>
    <Cell ss:StyleID="s66"><Data ss:Type="Number">' . $pending . '</Data></Cell>
    <Cell ss:StyleID="s66"><Data ss:Type="Number">' . $dismissed . '</Data></Cell>
    <Cell ss:StyleID="s66"><Data ss:Type="Number">' . $certified . '</Data></Cell>
    <Cell ss:StyleID="s66"><Data ss:Type="Number">' . $referred . '</Data></Cell>
    <Cell ss:StyleID="s66"><Data ss:Type="Number">' . $unsettledTotal . '</Data></Cell>
    <Cell ss:StyleID="s66"><Data ss:Type="String"></Data></Cell>
    <Cell ss:StyleID="s66"><Data ss:Type="String">₱' . number_format($estimatedSavings, 2) . '</Data></Cell>
   </Row>';

for ($i = 0; $i < 7; $i++) {
  echo '<Row ss:Height="20">';
  for ($c = 0; $c < 19; $c++) {
    echo '<Cell ss:StyleID="s68"><Data ss:Type="String"></Data></Cell>';
  }
  echo '</Row>';
}

echo '
   <!-- Empty Rows -->
   <Row ss:Height="10"/>
   <Row ss:Height="10"/>
   <Row ss:Height="10"/>
   
   <!-- Footer -->
   <Row ss:Height="15">
    <Cell ss:MergeAcross="8" ss:StyleID="s71">
     <Data ss:Type="String">PREPARED AND SUBMITTED BY:</Data>
    </Cell>
   </Row>
   <Row ss:Height="15"/>
   <Row ss:Height="15"/>
   <Row ss:Height="15"/>
   <Row ss:Height="15">
    <Cell ss:MergeAcross="8" ss:StyleID="s71">
     <Data ss:Type="String">Mary Joy M. Martinez</Data>
    </Cell>
   </Row>
   <Row ss:Height="15">
    <Cell ss:MergeAcross="8">
     <Data ss:Type="String">Lupon Secretary</Data>
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
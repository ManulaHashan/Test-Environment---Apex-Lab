<!DOCTYPE html>
<html>
<head>
    <title>Barcode Print</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
        }

        table {
            width: 20%;
            border-collapse: collapse;
            margin: 10px auto;
            
        }

        td {
            border: 1px solid #000;
            padding: 2px;
            padding-right: 1px;
            vertical-align: top;
            text-align: center;
        }

        .barcode-row {
            display: block;
            margin-bottom: 20px; 
        }

        .barcode-label {
            width: 100%;
            background-color: #fff;
            transform: scale(0.9);
            transform-origin: top left;
            padding: 5px;
        }

        .barcode-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: bold;
            font-size: 14px;
            height: 40px;
            padding: 0 5px;
        }
        .barcode-left {
            text-align: left;
            flex: 1;
        }
        .barcode-right {
            text-align: right;
        }

        .barcode-info {
            margin-top: 5px;
            font-size: 12px;
            text-align: left;
        }

        .barcode-footer {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            margin-top: 5px;
            
        }

        canvas {
            display: block;
            margin-left: auto;
            margin-right: 8px auto;
        }
        .hr-wrapper {
            position: relative;
            margin-left: 10px;
            margin-right: 10px; /* optional for spacing */
            margin-top: 10px;
        }

        hr.solid {
            border: none;
            border-top: 1px solid #141414;
            margin: 0;
            margin-right: -4px;
        }

        .hr-arrow {
            position: absolute;
            top: -6px; /* adjust to vertically align with <hr> */
            right: -13px;
            font-size: 10px;
            color: #141414;
            background-color: white; /* background to "cut" the line */
            padding-left: 2px;
           
        }
    </style>
</head>
<body>

 



 {{-- *-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*- --}}
<?php
  $specialChar = '';
  $labLid = $_SESSION['lid'];

$duplicateBarsetRow = DB::table('addpatientconfigs')
    ->where('lab_lid', $labLid)
    ->select('duplicate_barset')
    ->first();

// if ($duplicateBarsetRow && !empty($tgids)) {
//     foreach ($tgids as $tgid) {
//         if (preg_match("/{$tgid}:(\w)/", $duplicateBarsetRow->duplicate_barset, $matches)) {
//             $specialChar = $matches[1];
//             echo $specialChar;
            
//             break;
//         }
//     }
// }
?>



 {{-- *-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*- --}}

<?php


// Get patient and arrival time
$patient_pid = "";
$arival_time = "";

$result_get_patient_pid = DB::select("select patient_pid, arivaltime FROM lps WHERE date = ? AND sampleNo LIKE ? GROUP BY patient_pid", [$date, $sno . '%']);
foreach ($result_get_patient_pid as $lps_details) {
    $patient_pid = $lps_details->patient_pid;
    $arival_time = $lps_details->arivaltime;
}

// Get patient details
$fname = $lname = $gender_data = $age = $months = $days = $initials = $refby = "";

$result_get_patient_details = DB::select("
    SELECT u.fname, u.lname, u.gender_idgender, 
           p.age, p.months, p.days, p.initials, l.refby
    FROM patient AS p
    JOIN user AS u ON p.user_uid = u.uid
    JOIN lps AS l ON p.pid = l.patient_pid
    WHERE p.pid = ?
    GROUP BY p.pid
", [$patient_pid]);

foreach ($result_get_patient_details as $patient_details) {
    $fname = $patient_details->fname;
    $lname = $patient_details->lname;
    $gender_data = ($patient_details->gender_idgender == "1") ? "Male" : "Female";
    $age = $patient_details->age;
    $months = $patient_details->months;
    $days = $patient_details->days;
    $initials = $patient_details->initials;
    $refby = $patient_details->refby;
}

$sample_containerName = DB::select("select s.name as containerName from Testgroup tg 
    join sample_containers s on tg.sample_containers_scid = s.scid
    where tg.tgid = ?", [$tgid]);

foreach ($sample_containerName as $sample_containerName) {
    $container_Name = $sample_containerName->containerName;
}

if ($rep_barcode == "1") {
   $repeatBarcode = " - R";
}else{
   $repeatBarcode = "";
}

?>

<?php if ($isGroup == "false"): ?>
    <!-- Single Barcode -->

    <?php 


        if (preg_match("/{$tgid}:(\w)/", $duplicateBarsetRow->duplicate_barset, $matches)) {
            $specialChar = $matches[1];?>

            <table>
                <tr class="barcode-row">
                    <td>
                        <div class="barcode-label">
                            <div class="barcode-top">
                                <div class="barcode-left"><?= htmlspecialchars($sno) . " - " . $specialChar . "" .$repeatBarcode ?></div>
                                <div class="barcode-right"><?= $container_Name ?></div>
                            </div>
                            <canvas id="barcodeCanvas_single"></canvas>
                            <div class="hr-wrapper">
                                <hr class="solid">
                                <div class="hr-arrow">▶</div>
                            </div>

                            <div class="barcode-info">
                                <div><strong><?= htmlspecialchars("$initials $fname $lname") ?></strong></div>
                                <div><?= htmlspecialchars($testGroupName) ?></div>
                                <div class="barcode-footer">
                                     <span><?= htmlspecialchars("$date ($gender_data)") ?></span>
                                    <span><?= htmlspecialchars($arival_time) ?></span>
                                    <span><?= htmlspecialchars("$age : Y $months : M $days : D") ?></span>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </table><?php 
            DB::table('lps')
                ->where('sampleNo','like', $sno . '%')
                ->where('date', $date)
                ->where('Testgroup_tgid', $tgid)
                ->update(['status' => 'barcorded']);  
        }
     else { ?>
            <table>
                <tr class="barcode-row">
                    <td>
                        <div class="barcode-label">
                            <div class="barcode-top">
                                <div class="barcode-left"><?= htmlspecialchars($sno) . "" .$repeatBarcode ?></div>
                                <div class="barcode-right"><?= $container_Name ?></div>
                            </div>
                            <canvas id="barcodeCanvas_single"></canvas>
                            <div class="hr-wrapper">
                                <hr class="solid">
                                <div class="hr-arrow">▶</div>
                            </div>
                            <div class="barcode-info">
                                <div><strong><?= htmlspecialchars("$initials $fname $lname") ?></strong></div>
                                <div><?= htmlspecialchars($testGroupName) ?></div>
                                <div class="barcode-footer">
                                     <span><?= htmlspecialchars("$date ($gender_data)") ?></span>
                                    <span><?= htmlspecialchars($arival_time) ?></span>
                                    <span><?= htmlspecialchars("$age : Y $months : M $days : D") ?></span>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
            <script>
                JsBarcode("#barcodeCanvas_single", "<?= addslashes($sno) ?>", {
                    format: "CODE128",
                    displayValue: false,
                    lineColor: "#000",
                    width: 3,
                    height: 60,
                    margin: 0,
                });
            </script>
            <?php
            // Update lps table status to 'barcorded'
           DB::table('lps')
                ->where('sampleNo','like', $sno . '%')
                ->where('date', $date)
                ->where('Testgroup_tgid', $tgid)
                ->update(['status' => 'barcorded']);  
        } ?>

<?php else: ?>
    <!-- Grouped Barcodes -->
    <table>
        <?php

        

        
$groupedTests = [];

$results = DB::select("
    SELECT a.Testgroup_tgid, b.sample_containers_scid 
    FROM lps a 
    LEFT JOIN Testgroup b ON a.Testgroup_tgid = b.tgid  
    WHERE a.date = ? AND a.sampleNo LIKE ?
", [$date, $sno . '%']);

foreach ($results as $row) {
    $scid = $row->sample_containers_scid;
    $tgid = $row->Testgroup_tgid;

    if (!isset($groupedTests[$scid])) {
        $groupedTests[$scid] = [];
    }

    if (!in_array($tgid, $groupedTests[$scid])) {
        $groupedTests[$scid][] = $tgid;
    }
}

// Load all duplicate barset once
$duplicateBarsetRow = DB::table('addpatientconfigs')
    ->where('lab_lid', $labLid)
    ->select('duplicate_barset')
    ->first();

$duplicateMap = [];
if ($duplicateBarsetRow) {
    preg_match_all('/(\d+):(\w)/', $duplicateBarsetRow->duplicate_barset, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $duplicateMap[$match[1]] = $match[2]; // tgid => specialChar
    }
}

foreach ($groupedTests as $scid => $tgids) {
    $normalTests = [];
    foreach ($tgids as $tgid) {
        $tgRow = DB::selectOne("SELECT name FROM Testgroup WHERE tgid = ?", [$tgid]);
        $abbr = '';
        if ($tgRow) {
            $words = explode(' ', $tgRow->name);
            foreach ($words as $word) {
                $abbr .= strtoupper(substr($word, 0, 1));
            }
        }

        if (isset($duplicateMap[$tgid])) {

            // Load all duplicate barset once
            $sample_container = DB::table('sample_containers')
            ->where('scid', $scid)     
            ->select('name as sample')
           ->first();
            // special barcode
            $barcodeID = "barcodeCanvas_" . $scid . "_special_" . $tgid;
            $barcodeCode = $sno . '-' . $duplicateMap[$tgid] .'-' . $sample_container->sample;
            $barcodeCodeGEN = $sno;            
            ?>
            <tr class="barcode-row">
                <td>
                    <div class="barcode-label">
                        <div class="barcode-top">
                            <div class="barcode-left"><?= htmlspecialchars($sno) ." - " . $duplicateMap[$tgid] . "" .$repeatBarcode   ?></div>
                            <div class="barcode-right"><?=$sample_container->sample ?></div>
                        </div>
                        <canvas id="<?= htmlspecialchars($barcodeID) ?>"></canvas>
                        <div class="hr-wrapper">
                            <hr class="solid">
                            <div class="hr-arrow">▶</div>
                        </div>

                        <div class="barcode-info">
                            <div><strong><?= htmlspecialchars("$initials $fname $lname") ?></strong></div>
                            <div><?= htmlspecialchars($abbr) ?></div>
                            <div class="barcode-footer">
                                <span><?= htmlspecialchars("$date ($gender_data)") ?></span>
                                <span><?= htmlspecialchars($arival_time) ?></span>
                                <span><?= htmlspecialchars("$age : Y $months : M $days : D") ?></span>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            <script>
                JsBarcode("#<?= addslashes($barcodeID) ?>", "<?= addslashes($barcodeCodeGEN) ?>", {
                    format: "CODE128",
                    displayValue: false,
                    lineColor: "#000",
                    width: 3,
                    height: 60,
                    margin: 0,
                    
                });
            </script>
            <?php
        } else {
            // normal test, group later
            $normalTests[] = $abbr;
        }
    }

    if (!empty($normalTests)) {

        // Load all duplicate barset once
        $sample_container = DB::table('sample_containers')
        ->where('scid', $scid)     
        ->select('name as sample')
        ->first();

        $testsList = implode(', ', $normalTests);
        $barcodeID = "barcodeCanvas_" . $scid . "_normal";
        $barcodeCode = $sno . '-' . $sample_container->sample;
        $barcodeCodeGEN = $sno;
        ?>
        <tr class="barcode-row">
            <td>
                <div class="barcode-label">
                    <div class="barcode-top">
                        <div class="barcode-left"><?= htmlspecialchars($sno) . "" .$repeatBarcode  ?></div>
                        <div class="barcode-right"><?=$sample_container->sample ?></div>
                    </div>
                    <canvas id="<?= htmlspecialchars($barcodeID) ?>"></canvas>
                   <div class="hr-wrapper">
                        <hr class="solid">
                        <div class="hr-arrow">▶</div>
                    </div>

                    <div class="barcode-info">
                        <div><strong><?= htmlspecialchars("$initials $fname $lname") ?></strong></div>
                        <div><?= htmlspecialchars($testsList) ?></div>
                        <div class="barcode-footer">
                            <span><?= htmlspecialchars("$date ($gender_data)") ?></span>
                            <span><?= htmlspecialchars($arival_time) ?></span>
                            <span><?= htmlspecialchars("$age : Y $months : M $days : D") ?></span>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        <script>
            JsBarcode("#<?= addslashes($barcodeID) ?>", "<?= addslashes($barcodeCodeGEN) ?>", {
                format: "CODE128",
                displayValue: false,
                lineColor: "#000",
                width: 3,
                height: 60,
                margin: 0,
            });
        </script>
        <?php
    }
}

                DB::table('lps')
                ->where('sampleNo','like', $sno . '%')
                ->where('date', $date)
                ->where('Testgroup_tgid', $tgid)
                ->update(['status' => 'barcorded']);  

?>

    </table>
<?php endif; ?>

</body>
</html>

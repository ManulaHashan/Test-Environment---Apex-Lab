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
            margin: 30px;
        }

        td {
            border: 1px solid #000;
            padding: 8px;
            padding-right: 2px;
            vertical-align: top;
        }

        .barcode-row {
            display: block;
            margin-bottom: 20px; /* row අතර space එක */
        }

        .barcode-label {
            width: 100%;
            background-color: #fff;
            transform: scale(0.9);
            transform-origin: top left;
            padding: 5px;
        }

        .barcode-top {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
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
    </style>
</head>
<body>

<?php


// Get patient and arrival time
$patient_pid = "";
$arival_time = "";

$result_get_patient_pid = DB::select("SELECT patient_pid, arivaltime FROM lps WHERE date = ? AND sampleNo LIKE ? GROUP BY patient_pid", [$date, $sno . '%']);
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
?>

<?php if ($isGroup == "false"): ?>
    <!-- Single Barcode -->
    <table>
        <tr class="barcode-row">
            <td>
                <div class="barcode-label">
                    <div class="barcode-top"><?= htmlspecialchars($sno) ?></div>
                    <canvas id="barcodeCanvas_single"></canvas>
                    <div class="barcode-info">
                        <div><strong><?= htmlspecialchars("$initials $fname $lname") ?></strong></div>
                        <div><?= htmlspecialchars($testGroupName) ?></div>
                        <div class="barcode-footer">
                            <span><?= htmlspecialchars($date) ?></span>
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

        foreach ($groupedTests as $scid => $tgids) {
            $testNames = [];
            if (!empty($tgids)) {
                $placeholders = implode(',', array_fill(0, count($tgids), '?'));
                $tgResults = DB::select("SELECT name FROM Testgroup WHERE tgid IN ($placeholders)", $tgids);
                foreach ($tgResults as $tg) {
                    
                    $words = explode(' ', $tg->name);
                    $abbr = '';
                    foreach ($words as $word) {
                        $abbr .= strtoupper(substr($word, 0, 1));
                    }
                    $testNames[] = $abbr;
                }
            }

            $testsList = implode(', ', $testNames);
            $barcodeID = "barcodeCanvas_" . $scid;
        ?>
        <tr class="barcode-row">
            <td>
                <div class="barcode-label">
                    <div class="barcode-top"><?= htmlspecialchars($sno) ?></div>
                    <canvas id="<?= htmlspecialchars($barcodeID) ?>"></canvas>
                    <div class="barcode-info">
                        <div><strong><?= htmlspecialchars("$initials $fname $lname") ?></strong></div>
                        <div><?= htmlspecialchars($testsList) ?></div>
                        <div class="barcode-footer">
                            <span><?= htmlspecialchars($date) ?></span>
                            <span><?= htmlspecialchars($arival_time) ?></span>
                            <span><?= htmlspecialchars("$age : Y $months : M $days : D") ?></span>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        <script>
            JsBarcode("#<?= addslashes($barcodeID) ?>", "<?= addslashes($sno . '-' . $scid) ?>", {
                format: "CODE128",
                displayValue: false,
                lineColor: "#000",
                width: 3,
                height: 60,
                margin: 0,
            });
        </script>
        <?php } ?>
    </table>
<?php endif; ?>

</body>
</html>

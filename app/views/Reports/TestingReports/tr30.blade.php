<?php
date_default_timezone_set("Asia/Colombo");
if (!isset($_SESSION)) {
    session_start();
}
?>

@extends('Templates/ReportTemplate') 

@section('title')
Laboratory Report
@stop

@section('head')

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<script src="{{ asset('JS/chart.js') }}"></script>
<script src="{{ asset('JS/jquery-3.1.0.js') }}"></script>
<script type="text/javascript">

window.onload = function () {
    loadChart();
};

var clabels;
var cdata;
var old_chartLabels;
var old_chartData;
function loadChart() {

    var OneHx = parseFloat($("#2375").html().replace(/\s/g, "").replace("", " ").trim().replace("<b>", "").replace("</b>", ""));



    if (document.getElementById("ogtt") && !isNaN(OneHx)) {

        var arr = "Fasting,60,120";
//        var arr = "Fasting,30,60,90,120";

        var fasting = parseFloat($("#2374").html().replace(/\s/g, "").replace("", " ").replace("<b>", "").replace("</b>", "").trim());
        var OneH = parseFloat($("#2375").html().replace(/\s/g, "").replace("", " ").replace("<b>", "").replace("</b>", "").trim());

        var twoH = parseFloat($("#2376").html().replace(/\s/g, "").replace("", " ").replace("<b>", "").replace("</b>", "").trim());



//        var halfH = parseFloat($("#617").html().replace(/\s/g, "").replace("", " ").replace("<b>", "").replace("</b>", "").trim());
        var halfH = 0;
//        if (isNaN(halfH)) {
        halfH = (fasting + OneH) / 2;
//        }

//        var OhalfH = parseFloat($("#619").html().replace(/\s/g, "").replace("", " ").replace("<b>", "").replace("</b>", "").trim());
        var OhalfH = 0;
//        if (isNaN(OhalfH)) {
        OhalfH = (OneH + twoH) / 2;
//        }

//        var arr2 = fasting + "," + halfH + "," + OneH + "," + OhalfH + "," + twoH;
        var arr2 = fasting + "," + OneH + "," + twoH;

//        alert(arr2);



//        var arr3 = "180,180,180,180,180";
        var arr3 = "140,140,140";

        clabels = arr.split(",");
        cdata = arr2.split(",");
        cdata2 = arr3.split(",");

        var ctx = document.getElementById("outChart");

        var myChart = new Chart(ctx, {
            type: "line",
            data: {
                labels: clabels,
                datasets: [{
                        label: 'Patient Result',
                        data: cdata,
                        backgroundColor: [
                            'rgba(255, 255, 255, 0)'
                        ],
                        borderColor: [
                            'rgba(0,0,0,1)'
                        ],
                        borderWidth: 2
                    }, {
                        label: 'Cut Off Value',
                        data: cdata2,
                        backgroundColor: [
                            'rgba(255, 255, 255, 0)'
                        ],
                        borderColor: [
                            'rgba(255,99,132,1)'
                        ],
                        borderWidth: 2
                    }]
            },
            options: {

                animation: false,
                legends: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'The Graph of Time Vs Plasma Glucose Concentration'
                },
                scales: {
                    xAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'Time (min)',
                                fontColor: 'black'
                            }, gridLines: {
                                zeroLineColor: 'rgba(0,0,100,1)'
                            }, ticks: {
                                fontColor: 'blue'
                            }
                        }],
                    yAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'Result (mg / dl)',
                                fontColor: 'black'
                            },
                            ticks: {
                                beginAtZero: true,
                                fontColor: 'blue',
//                                suggestedMin: 0,
//                                steps: 10, 
//                                stepValue: 20,
                                max: 300,
                                min: 50
                            }, gridLines: {
                                zeroLineColor: 'rgba(0,0,100,1)'
                            }

                        }]
                }
            }
        });

    }
}
</script>
@stop

@section('heading')
{{ $repHead or '' }}
@stop

@section('content')

<img src="{{ asset('images/mlwsbcode.jpg') }}" width="200" height="30" style="position:absolute; right:0px; top:185px;"> 

<?php
$snoforHead = "";

$Specimen = "";

$repResultForHeading = DB::select("select a.entered_uid,a.fastinghours,DATE_FORMAT(a.blooddraw, '%h:%i:%p') as blooddraw,DATE_FORMAT(a.arivaltime, '%h:%i:%p') as arivaltime,a.specialnote,c.fname,c.lname,b.age,b.months,b.days,b.initials,c.gender_idgender,refference_idref,a.sampleNo,a.date as regdate,a.Lab_lid from lps a, patient b, user c where a.patient_pid = b.pid and b.user_uid = c.uid and a.lpsid = '" . $lpsid . "'");
foreach ($repResultForHeading as $lpsItemforHeading) {
    $snoforHead = $lpsItemforHeading->sampleNo;
}
if (isset($onlprep)) {
    //show rep heading
//    if (substr($snoforHead, 0, 2) == "RC") {
//        
    ?>  

                                                            <!--<img src="{{ asset('images/LabHeadersFooters/Lab8_RC.png') }}" style="position: absolute; top: 0; left: 0; z-index: -1; margin-top: 10px" width="98%">-->
    <?php
//    } else {
    ?>  
    <img src="{{ asset('images/LabHeadersFooters/Lab30.png') }}" style="position: absolute; top: 0; left: 0; z-index: -1; margin-top: -20px" width="100%">
    <br>
    <br>
    <?php
//    }
} else {

//    if (substr($snoforHead, 0, 2) == "RC") {
    ?>
    <!--<p style="font-size: 11pt; font-style: italic; float: right; text-align: right;" >All test done by MEDITECH. <br/>222/B, Kandy Road, Dalugama, Kelaniya.   Reg. No. PHSRC/L/200.</p>-->
    <?php
//    }
}
?>



<?php
$CategoryArr = array();
$resultx = DB::select("select name from testingcategory where tcid in (select testingcategory_tcid from Lab_has_test where lab_lid = '" . $_SESSION['lid'] . "' and test_tid in (select test_tid from lps_has_test where lps_lpsid='" . $lpsid . "'))");
foreach ($resultx as $resx) {
    array_push($CategoryArr, $resx->name);
}
$Category = implode(", ", $CategoryArr);

$resultsys = DB::select("select d.tgid,d.name from lps_has_test b,Lab_has_test c, Testgroup d where c.Testgroup_tgid = d.tgid and c.test_tid = b.test_tid and b.lps_lpsid='" . $lpsid . "' and d.Lab_lid = '" . $_SESSION['lid'] . "' group by d.tgid order by c.orderno ASC, c.lhtid ASC");
foreach ($resultsys as $ressys) {
    if ($ressys->name == "COMPLETE   BLOOD  COUNT (AUTOMATED COUNT)") {
        ?>
                        <!-- <img src="{{ asset('images/sysmexlogo.jpg') }}" width="200" style="margin-top: -35px;"> -->
        <?php
    }
}
?>

<!--<center><h4 style="font-size: 11pt;">{{ $Category or '' }}</h4>  </center>-->

<!--<div style="font-size: 12pt; font-family: 'Courier New', Courier, 'Lucida Sans Typewriter', 'Lucida Typewriter', monospace;">-->
<div style="font-size: 10pt; font-family: Bookman Old Style">
    <!--<div style="font-family:Garamond; font-size: 12pt; font-weight: bold;">-->
    <?php
    $repResultxxxxx = DB::select("SELECT fname FROM user where uid = (select user_uid from labUser where luid = '" . $_SESSION['luid'] . "')");
    foreach ($repResultxxxxx as $userRes) {
        $userfname = $userRes->fname;
    }
    ?>

    <?php
    $repResult = DB::select("select a.entered_uid,a.fastinghours,DATE_FORMAT(a.blooddraw, '%h:%i:%p') as blooddraw,DATE_FORMAT(a.arivaltime, '%h:%i:%p') as arivaltime,DATE_FORMAT(a.finishtime, '%h:%i:%p') as finishtime,a.specialnote,c.fname,c.lname,b.age,b.months,b.days,b.initials,c.gender_idgender,refference_idref,a.sampleNo,a.date as regdate,a.Lab_lid from lps a, patient b, user c where a.patient_pid = b.pid and b.user_uid = c.uid and a.lpsid = '" . $lpsid . "'");
    foreach ($repResult as $lpsItem) {

        $page_rep_head = false;

        $PName = strtoupper($lpsItem->fname . " " . $lpsItem->lname);
        $age = $lpsItem->age;
        $months = $lpsItem->months;
        $days = $lpsItem->days;

        $fastingHours = $lpsItem->fastinghours;

        $sno = $lpsItem->sampleNo;
        $drawTime = $lpsItem->arivaltime;
        $finishTime = $lpsItem->finishtime;

        $specialNote = $lpsItem->specialnote;
        $regDate = $lpsItem->regdate;
        $entered_uid = $lpsItem->entered_uid;

        if ($lpsItem->initials != "" && $lpsItem->initials != null) {
            $initials = strtoupper($lpsItem->initials) . ".";
        } else {
            //initian genaretion~~~~
            $initials = "";

            if ($age == "") {
                $initials = "Baby.";
            }

            if ($age != "") {
                if ($age < 7) {
                    $initials = "Baby.";
                } elseif ($age > 3 && $age < 18) {
                    $initials = "Mr.";
                } elseif ($age >= 18) {
                    $initials = "Mr.";
                    if ($lpsItem->gender_idgender != 1) {
                        $initials = "Ms.";
                    }
                }
            }
            //~~~~~~~~~~~~~~~~~~~~~~
        }

        if ($lpsItem->gender_idgender == 1) {
            $gender = "Male";
        } else {
            $gender = "Female";
        }

        if ($lpsItem->refference_idref == null) {
            $refby = "";
        } else {
            $repResultx = DB::select("SELECT name FROM refference where idref = '" . $lpsItem->refference_idref . "' and lid = '" . $_SESSION['lid'] . "'");
            foreach ($repResultx as $lpsItemx) {
                $refby = ucwords(ucwords($lpsItemx->name, "."), " ");
            }
        }

        $Resultx = DB::select("select * from reportconfigs where lab_lid = '" . $_SESSION['lid'] . "'");
        foreach ($Resultx as $resx) {
            $agelabel = $resx->agelabel;
            $date = $resx->date;
            $sign = $resx->sign;
            $valueState = $resx->valuestate;

            $viewSno = $resx->viewsno;
            $viewRegDate = $resx->viewregdate;
            $viewinitials = $resx->viewinitials;
            $viewSpecialNote = $resx->viewspecialnote;

            if ($resx->fontitelic) {
                $fontitelic = "font-style: italic;";
            } else {
                $fontitelic = "";
            }
        }

        if ($agelabel) {
            $monthsLBL = "/12";
            $daysLBL = "/365";
        } else {
            $monthsLBL = "Months";
            $daysLBL = "Days";
        }
    }

    $specimenArr = array();
    $result0 = DB::select("select name from testinginput where tiid in (select testinginput_tiid from Lab_has_test where lab_lid = '" . $_SESSION["lid"] . "' and test_tid in (select test_tid from lps_has_test where lps_lpsid='" . $lpsid . "')) limit 1");
    foreach ($result0 as $res0) {
        $Specimen = $res0->name;
    }
    ?>

    <div>
        <table width="100%" cellspacing='5' style="font-weight: bold;">
            <tr>
                <td width="120px">Patient Name</td>
                <td width="300px"> : 

                    @if($viewinitials)
                    {{ $initials }} 
                    @endif

                    {{ $PName }}

                </td>
                <td width="5"></td>

                @if($viewRegDate)
                <td width="100">Received On</td>
                <td width="190">: <?php echo $regDate . " " . $drawTime; ?> </td>
                @endif

            </tr>
            <tr>
                <td>Gender/Age</td>
                <td>: {{ $gender }} &nbsp;
                    @if($age != 0)
                    {{ $age }} Years &nbsp;
                    @endif

                    @if($months != 0)
                    {{ $months }} {{ $monthsLBL or '' }} &nbsp;
                    @endif

                    @if($days != 0)
                    {{ $days }} {{ $daysLBL or '' }} &nbsp;
                    @endif
                </td>
                <td></td>

                <td >Reported On</td>
                <td >: <?php echo $regDate . " " . $finishTime; ?> </td>

            </tr>
            <tr>
                <td width="100">Reference No</td>
                <td>: {{ $sno }}</td>
                <td></td>
                <td>Specimen</td>
                <td>: {{$Specimen}}</td>

            </tr>

            <tr>
                <td>Referred By</td>
                <td style="font-size: 12pt; font-weight: lighter;">: {{ $refby }}</td>
                <td></td>
                <td>Department</td>
                <td>: {{ $Category or '' }}</td>
            </tr>

        </table>
        <hr/>
    </div>
    <div>
        <!--<p style="font-style: italic; font-family: Arial; font-weight: normal;">Specimen : {{ $Specimen or '' }}</p>-->    

        <!--<blockquote>-->
            <?php
//to show table heading
            $tableHead = true;


//for enable disable value state for each test
            $tmpValState = true;
            $advRef = false;

            $ColHeadAlreadyAdded = false;
            $lastTestGroup = "";


            $tData = "";
            $result0 = DB::select("select d.comment,d.age_ref,d.name_col_align, d.result_col_align, d.unit_col_align, d.flag_col_align, d.ref_col_align, d.custom_configs,d.rep_heading,d.name_col, d.value_col, d.unit_col, d.flag_col, d.ref_col, d.name_col_head, d.value_col_head, d.unit_col_head, d.flag_col_head, d.ref_col_head, d.name_col_width, d.value_col_width, d.unit_col_width, d.flag_col_width, d.ref_col_width, d.tgid,d.name from lps_has_test b,Lab_has_test c, Testgroup d where c.Testgroup_tgid = d.tgid and c.test_tid = b.test_tid and b.lps_lpsid='" . $lpsid . "' and d.Lab_lid = '" . $_SESSION['lid'] . "' group by d.tgid");
            foreach ($result0 as $res0) {

                $custom_configs = $res0->custom_configs;
                if ($custom_configs == "1") {
                    $custom_configs = true;
                } else {
                    $custom_configs = false;
                }

                $rep_heading = $res0->rep_heading;
                $name_col = $res0->name_col;
                $value_col = $res0->value_col;
                $unit_col = $res0->unit_col;
                $flag_col = $res0->flag_col;
                $ref_col = $res0->ref_col;

                $name_col_head = $res0->name_col_head;
                $value_col_head = $res0->value_col_head;
                $unit_col_head = $res0->unit_col_head;
                $flag_col_head = $res0->flag_col_head;
                $ref_col_head = $res0->ref_col_head;

                $name_col_width = $res0->name_col_width;
                $value_col_width = $res0->value_col_width;
                $unit_col_width = $res0->unit_col_width;
                $flag_col_width = $res0->flag_col_width;
                $ref_col_width = $res0->ref_col_width;

                $name_col_align = $res0->name_col_align;
                $result_col_align = $res0->result_col_align;
                $unit_col_align = $res0->unit_col_align;
                $flag_col_align = $res0->flag_col_align;
                $ref_col_align = $res0->ref_col_align;

                $advRef = $res0->age_ref;

                //14.11.2018 Pasindu samarakoon
                $tableHeadNew = false;
                $tableHead = false;
                $tableHeadNewR = false;
                $tmpValState = false;
                $tableHeadR = false;
                $tableHeadRL = false;
                $tableHeadRNON = false;
                $tableHeadNewWR = false;
                $tableHeadNew2 = false;
                $sysmexReport = false;
                $TGGLYC = false;
                $TGComment = false;
                $tableHeadNewWR2 = false;
                $separateRefRangeCreatinine = false;
                $separateRefRangeSGPT = false;
                $tableHeadNew3 = false;
                $tableHeadNewLeft = false;
                $OverrideFlag = false;
                $boldValue = 'normal';

                $TGCommentText = $res0->comment;

                if ($res0->comment != "") {
                    $TGComment = true;
                    $TGCommentText = $res0->comment;
                }


                if ($res0->name == "STOOLS FOR OCCULT BLOOD") {
                    $TGComment = true;
                    $TGCommentText = $res0->comment;
                }

                if ($res0->name == "HAEMOGLOBIN  A1C") {
                    $TGComment = true;
                    $TGCommentText = $res0->comment;
                }

                if ($res0->name == "PROGESTERONE") {
                    $TGComment = true;
                    $TGCommentText = $res0->comment;
                }

                if ($res0->name == "RETICULOCYTE COUNT") {
                    $TGComment = true;
                    $TGCommentText = $res0->comment;
                }


                if ($res0->name == "BLOOD SUGAR SERIES (1 Hour)") {
                    $TGBSS = true;
                    $tableHeadNew = true;
                    $alignTop = "top";
                }
                if ($res0->name == "BLOOD SUGAR SERIES (2 Hour)") {
                    $TGBSS = true;
                    $tableHeadNew = true;
                    $alignTop = "top";
                }
                if ($res0->name == "FASTING PLASMA GLUCOSE") {
                    $TGFPG = true;
                    $showFHours = true;
                    $tableHeadNewR = true;

                    // $tableHead = true;
                }
                if ($res0->name == "SERUM  CHOLESTEROL") {
                    $tableHeadNewR = true;
                    $alignTop = "top";
                }
                // if ($res0->name == "URINE CULTURE  & A.B.S.T.") {
                //     $TGUCABST = true;
                // }
                if ($res0->name == "SERUM  CREATININE WITH eGFR") {
                    $TGSECRE = true;
                    $tableHeadNewR = true;
                    $separateRefRangeCreatinine = true;
                }
                if ($res0->name == "BLOOD  UREA") {
                    $TGBUREA = true;
                    $tableHeadNewR = true;
                }
                if ($res0->name == "SGPT(ALT)") {
                    $TGSGPT = true;
                    $tableHeadNewR = true;
                    $separateRefRangeSGPT = true;
                }

                if ($res0->name == "SGOT(AST)") {
                    $TGSGPT = true;
                    $tableHeadNewR = true;
                }
                if ($res0->name == "POST  PRANDIAL  PLASMA   GLUCOSE (2 hours)") {
                    $TGPPPG = true;
                    $tableHead = true;
//                    $alignTop = "top";
                }
                if ($res0->name == "POST  PRANDIAL  PLASMA   GLUCOSE (1 hour)") {
                    $TGPPPG1 = true;
                    $tableHeadNew = true;
//                    $alignTop = "top";
                }
                if ($res0->name == "RHEUMATOID  FACTOR") {
                    // $TGRFT = true;
                    $tableHead = true;
                }
                if ($res0->name == "RANDOM PLASMA GLUCOSE") {
                    $TGRPG = true;
                    $tableHeadNewWR2 = true;
                }
                if ($res0->name == "SPUTUM  CULTURE  & A.B.S.T.") {
                    $TGSCABST = true;
                    $alignRight = "left";
                }
                if ($res0->name == "STOOL  CULTURE  & A.B.S.T.") {
                    $tableHeadR = true;
                }
                if ($res0->name == "ASOT") {
                    $TGASOT = true;
                    $tableHeadNew = true;
                    $TGComment = true;
                    $TGCommentText = $res0->comment;
                }
                if ($res0->name == "C REACTIVE PROTEIN") {
                    // $TGCRP = true;
                    $tableHeadNewR = true;
                }
                if ($res0->name == "WBC/DC") {
                    $TGWBCDC = true;
                    $tableHeadNew = true;
                }
                if ($res0->name == "HAEMOGLOBIN") {
                    $TGH = true;
                    $tableHeadNew2 = true;
                }

                if ($res0->name == "LIPID PROFILE") {
                    // $TGLP = true;
                    $tableHeadNewR = true;
                    $showFHours = true;
                }
                if ($res0->name == "SERUM  TOTAL PROTEIN") {
                    $TGSTP = true;
                    $tableHeadNewR = true;
                }
                if ($res0->name == "DENGUE VIRUS NS1 ANTIGEN") {
                    $TGDVNA = true;
                }
                if ($res0->name == "DENGUE  VIRUS  ANTIBODY  TEST") {
                    $TGDVABT = true;
                }
                if ($res0->name == "HIV   1 & 2  ANTIBODY") {
                    $TGHIVAB = true;
                    $TGComment = true;
                    $TGCommentText = $res0->comment;
                }
                if ($res0->name == "HEPATITIS  B  SURFACE  ANTIGEN") {
                    $tableHeadRNON = true;
                    // $TGHBSA = true;
                }
                if ($res0->name == "HAEMOGLOBIN  A1C") {
                    $TGHAOC = true;
                    $tableHeadNew = true;
                }
                if ($res0->name == "TROPONIN I (QUANTITATIVE)") {
                    $TGTICANTI = true;
//                    $tableHeadNew = true;
                    $TGComment = true;
                    $TGCommentText = $res0->comment;
                }
                if ($res0->name == "URINE  FOR  MICRO  ALBUMIN") {
                    $TGUFMA = true;
                    $tableHeadNew = true;
                }
                if ($res0->name == "GLUCOSE  CHALANGE  TEST ( GCT ) 50g") {
                    $TGGCT50 = true;
                    $tableHeadNewWR = true;
                }
                if ($res0->name == "GLUCOSE  CHALANGE  TEST ( GCT ) 75g") {
                    // $TGGCT75 = true;
                    $tableHead = true;
                }
                if ($res0->name == "LIVER  PROFILE") {

                    $tableHead = true;
                    $separateRefRangeSGPT = true;
                }

                if ($res0->name == "CORRECTED  CALCIUM") {
                    $tableHead = true;
                }

                if ($res0->name == "SERUM MAGNESIUM ") {
                    $tableHead = true;
                }

                if ($res0->name == "SERUM  IRON & T.I.B.C") {
                    $tableHead = true;
                }

                if ($res0->name == "RANDOM  BLOOD  GLUCOSE ( CAPILLARY )") {
                    $tableHead = true;
                    // $TGRPGC = true;
                }
                if ($res0->name == "SERUM IONORGANIC  PHOSPHORUS (PO4 3-)") {
                    $tableHead = true;
                }

                if ($res0->name == "SERUM URIC  ACID") {
                    $tableHead = true;
                }

                if ($res0->name == "SERUM  ELECTROLYTES") {
                    $tableHead = true;
                }

                if ($res0->name == "CK  (C.P.K)") {
                    $tableHead = true;
                }

                if ($res0->name == "SERUM  TOTAL  CALCIUM") {
                    $tableHead = true;
                }

                if ($res0->name == "REANAL  PROFILE") {
                    $TGREANALPRO = true;
                    $tableHead = true;
                }

                if ($res0->name == "SERUM  ALBUMIN") {
                    $tableHead = true;
                }

                if ($res0->name == "SERUM GAMMA - GT (GGT)") {
                    $tableHead = true;
                }

                if ($res0->name == "SERUM  ALKALINE  PHOSPHATASE (ALP)") {
                    $tableHead = true;
                }

                if ($res0->name == "SERUM BILIRUBIN - TOTAL") {
                    $tableHead = true;
                }
                if ($res0->name == "SERUM BILIRUBIN - DIRECT") {
                    $tableHead = true;
                }
                if ($res0->name == "BILIRUBIN - TOTAL &  DIRECT") {
                    $tableHead = true;
                }
                if ($res0->name == "URINE  PROTEIN / CREATININE RATIO") {
                    $TGUPCR = true;
                    $tableHeadNew = true;
                }
                if ($res0->name == "THYROID    STIMULATING    HORMONE   (TSH)") {
                    $TGTSH = true;
                    $tableHeadNew = true;
                }
                if ($res0->name == "FREE  TRIIODOTHYRONINE(F T3)") {
                    $TGFT3 = true;
                    $tableHeadNewR = true;
                    $TGComment = true;
                    $TGCommentText = $res0->comment;
                }
                if ($res0->name == "FREE  THYROXINE (F T4)") {
                    $TGFT4 = true;
                    $tableHeadNewR = true;
                    $TGComment = true;
                    $TGCommentText = $res0->comment;
                }
                if ($res0->name == "SERUM  BETA  H.C.G  ") {
                    $TGSBHCG = true;
                    $tableHeadNew = true;
                }
                if ($res0->name == "PROLACTIN") {
                    $TGPROLACTIN = true;
                    $tableHeadNew = true;
                }
                if ($res0->name == "FOLLICLE  STIMULATING  HORMONE ") {
                    $TGFSH = true;
                    $tableHeadNew = true;
                }
                if ($res0->name == "LUTEINIZING  HORMONE ") {
                    $TGLUH = true;
                    $tableHeadNew = true;
                }

                if ($res0->name == "TESTOSTERON ( 2nd Generation) ") {
                    $TGT2G = true;
                    $tableHeadNew = true;
                }

                if ($res0->name == "SERUM  FERRITIN") {
                    $TGSF = true;
                    $tableHeadNew = true;
                }

//                if ($res0->name == "PROSTATE &nbsp; SPECIFIC &nbsp; ANTIGEN - TOTAL") {
                if ($res0->name == "PROSTATE   SPECIFIC   ANTIGEN - TOTAL") {
                    $TGPSAT = true;
                    $tableHeadNew = true;
                }

                if ($res0->name == "ANTI NUCLEAR FACTOR ") {
                    $TGANF = true;
                }

                if ($res0->name == "INFLUENZA  A & B") {
                    $TGNAAB = true;
                }

                if ($res0->name == "URINE OSMOLALITY") {
                    $TGIO = true;
                    $tableHeadNewR = true;
                }

                if ($res0->name == "BLOOD PICTURE") {
                    $TGBP = true;
                    $tableHeadRL = true;
                    $alignRight = "left";
                }

                if ($res0->name == "URINE FULL REPORT") {
                    $UFRNEW = true;
                    $alignRight = "left";
                    // $tableHeadNewLeft = true;
//                    $freeTDWidth = "250";
                }

                if ($res0->name == "GLUCOSE  TOLERANCE TEST (3 Samples)") {
                    // $OGTT = true;
                    $tableHeadNew3 = true;
                    // $flag = true;
                }
                if ($res0->name == "ORAL  GLUCOSE  TOLERANCE TEST 2nd Hour") {
//                    $OGTT = true;
                    $tableHeadNewR = true;
                    $flag = true;
                }

                if ($res0->name == "BLOOD GROUPING AND Rh") {
                    $tableHeadRNON = true;
                    $alignRight = "left";
                }

                if ($res0->name == "URINE hCG") {
                    $tableHeadRNON = true;
                    // $tableHeadR = true;
                    $alignRight = "left";
                }

                if ($res0->name == "STOOL  FULL  REPORT") {
                    $alignRight = "left";
                }

                if ($res0->name == "BT  CT") {
                    $tableHeadNewR = true;
                }

                if ($res0->name == "SERUM IRON") {
                    $tableHeadNewR = true;
                }

                if ($res0->name == "Glycaemic Control") {
                    $TGGLYC = true;
                }

                if ($res0->name == "ERYTROCYTE   SEDIMENTATION   RATE") {
                    // $ESR = true;
                    $tableHeadNew = true;
                }

                if ($res0->name == "CA 125") {
                    $TGComment = true;
                    $TGCommentText = $res0->comment;
                }


                if ($res0->name == "HEPATITIS A ANTIBODY") {
//        $tableHeadR = true; 
                    $alignRight = "center";
                    $TGComment = true;
                    $TGCommentText = $res0->comment;
                }

                if ($res0->name == "SPECIMEN CULTURE & A.B.S.T.") {
                    $tableHeadRNON = true;
//        $alignRight = "center"; 
                    $alignRight = "left";
                }

                if ($res0->name == "PLATELET COUNT") {
                    $tableHead = true;
                }

                if ($res0->name == "PREGNACY TEST (serum)") {
                    $tableHeadRNON = true;
                }

                if ($res0->name == "SERUM CREATININE") {
                    $tableHead = true;
                    $separateRefRangeCreatinine = true;
                }

                if ($res0->name == "BLOOD SUGAR SERIES (4 Samples, 2 Hours)") {
                    $tableHead = true;
                }

                if ($res0->name == "BLOOD SUGAR SERIES (4 Samples, 1 Hour)") {
                    $tableHead = true;
                }

                if ($res0->name == "BLOOD SUGAR SERIES (6 Samples, 1 Hour)") {
                    $tableHead = true;
                }

                if ($res0->name == "BLOOD SUGAR SERIES (6 Samples, 2 Hours)") {
                    $tableHead = true;
                }

                if ($res0->name == "BLOOD SUGAR SERIES (3 Samples, 1 Hour)") {
                    $tableHeadNew = true;
                }

                if ($res0->name == "BLOOD SUGAR SERIES (3 Samples, 2 Hours)") {
                    $tableHeadNew = true;
                }

                if ($res0->name == "FULL   BLOOD  COUNT (AUTOMATED COUNT)") {
                    $tableHead = true;
                }

                if ($res0->name == "PPBS 3 SAMPLES - POST PRANDIAL PLASMA GLUCOSE") {
                    $alignTop = "top";
                    $alignRight = "center";
                }

                if ($res0->name == "OGTT 3 SAMPLES (75g Oral Glucose Tolerance Test)") {
                    $OGTT = true;
                }

                $UCABST = false;
                if ($res0->name == "URINE CULTURE  & A.B.S.T.") {
                    $UCABST = true;
                }

                //Flag enabled for range included
                if (isset($tableHeadNewR) || isset($tableHeadR)) {
                    $tmpValState = true;
                } else if (isset($tableHead) || isset($tableHeadNew)) {
                    $tmpValState = false;
                } else {
                    $tmpValState = false;
                }
                ?>          


                <?php
                $test_name_for_heading = $res0->name;
                if (count(explode("-", $test_name_for_heading)) > 1) {
                    $test_name_for_heading = explode("-", $test_name_for_heading)[1];
                }
                ?>

                <table width="100%" style="border: 1px solid black; width: 100%; font-size: 12pt; margin-left: 0px; padding-left: 0px;"><tr><td><b>TEST&nbsp;&nbsp;:&nbsp;&nbsp;{{ $test_name_for_heading }}</b></td></tr></table>

                <br/><br/> 


                @if(isset($TGUCABST))
                <p>ORGANISM (1) =  <b>Coliform</b> organism. Isolated<br/> &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; Colony count > 10<sup>5</sup> /ml</p>
                @endif

                @if(isset($TGSCABST))
                <p>ORGANISM (1) =  Streptococcus pneumoniae isolated.</p>
                @endif

                @if(isset($TGDVABT))
                <p>Dengue  Virus</p>
                @endif


                <!-- @if(isset($UFC))
                <u>UNCENTRIFUGED DEPOSITS</u>
                @endif -->



                <table width="100%">


                    <!-- Fixed from web layout -->    

                    <?php if ($lastTestGroup != $res0->name) { ?>
                        <tr>
                            <td>&nbsp;</td> 

                        </tr>
                        <?php
                        $lastTestGroup = $res0->name;
                        $ColHeadAlreadyAdded = true;
                    }
                    ?>

                    <?php
                    if (!$custom_configs && $rep_heading) {

                        $alignRight = $result_col_align;
                        ?> 
                        <thead style="margin-bottom: -30px;">
                            <tr >
                                <td align="{{ $name_col_align or ''}}" width="{{ $name_col_width or ''}}" style="font-size: 10pt;"><u>{{ $name_col_head or ''}}</u></td> 
                                <th width="0"></th>

                                <td align="{{ $result_col_align or ''}}" width="{{ $value_col_width or ''}}" style="font-size: 10pt;"><u>{{ $value_col_head or ''}}</u></td>
                                <th width="0"></th>

                                <td align="{{ $unit_col_align or ''}}" width="{{ $unit_col_width or ''}}" style="font-size: 10pt;"><u>{{ $unit_col_head or ''}}</u></td>

                                <td align="{{ $flag_col_align or ''}}" width="{{ $flag_col_width or ''}}" style="font-size: 10pt;"><u>{{ $flag_col_head  or ''}}</u></td>
                                <td align="{{ $ref_col_align or ''}}" width="{{ $ref_col_width or ''}}" style="font-size: 10pt;"><u>{{ $ref_col_head or ''}}</u></td>
                            </tr>
                        <thead>
                            <?php
                            $ColHeadAlreadyAdded = true;
                        }
                        ?>


                        <!-- Fixed from web layout -->                           

                        <?php
                        $flag = true;

                        if ($res0->name == "FREE  TRIIODOTHYRONINE(F T3)") {
                            $flag = false;
                        }

                        if ($res0->name == "FREE  THYROXINE (F T4)") {
                            $flag = false;
                        }

                        if ($res0->name == "SEMINAL FLUID ANALYSIS") {
                            $flag = false;
                        }

                        if ($res0->name == "TROPONIN I (HIGH SENSITIVE TROPONIN I)") {
                            
                        }

                        $DCtestTr = "";
                        $viewAna = false;
                        $anaID = '';
                        $result2 = DB::select("select a.tid,a.name as testname,c.reportname,c.measurement,b.value,a.minrate,a.maxrate,c.viewnorvals,c.viewanalyzer,c.analyzers_anid as anid, d.refference_min, d.refference_max, c.advance_ref,c.lhtid from test a,lps_has_test b,Lab_has_test c,labtestingdetails d where d.Lab_lid = c.Lab_lid and a.tid=d.test_tid and c.Testgroup_tgid = '" . $res0->tgid . "' and a.tid=b.test_tid and a.tid=c.test_tid and b.lps_lpsid='" . $lpsid . "' group by a.tid order by c.orderno ASC , c.lhtid ASC");
                        foreach ($result2 as $res) {
                            $name = $res->reportname;
                            $value = $res->value;
                            $mes = $res->measurement;
                            $tname = $res->testname;

                            $advRef = $res->advance_ref;
                            $LHTID = $res->lhtid;

                            //enable flag according to view normal value boolean in DB
                            if ($res->viewnorvals) {
                                $valueState = true;
                            } else {
                                $valueState = false;
                            }

                            //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                            //--------------------For make invisible empty values in urine culture (start)----------------------------
                            if ($UCABST && $value == "") {
                                continue;
                            }
                            //--------------------For make invisible empty values in urine culture (end)----------------------------

                            if ($res->viewanalyzer) {
                                $viewAna = true;
                                $anaID = $res->anid;
                            }

                            if ($res0->name == "TROPONIN I (HIGH SENSITIVE TROPONIN I)") {
                                $ResultValue = $value;
                                if ($ResultValue[0] == "<") {
                                    $flag = false;
                                    $OverrideFlag = true;
                                }
                            }

                            if ($res0->name == "TSH (3rd GENARATION)") {
                                $ResultValue = $value;
                                if ($ResultValue[0] == "<") {
                                    $flag = false;
                                    $OverrideFlag = true;
                                    $boldValue = 'bold';
                                }
                            }

                            if ($name == "Fasting Blood Sugar<br/>&nbsp;") {

                                if ($value < $res->refference_min) {
                                    $DCtestTr .= "<tr><td width='140'>" . $name . "</td><td width='240'></td><td align='right'><b>" . $value . "</b></td><td width='30'>&nbsp;</td><td>" . $mes . "</td>";
                                    if ($valueState && $tmpValState) {
                                        $DCtestTr .= "<td align='center'>L</td>";
                                    }
                                    $DCtestTr .= "";
                                } elseif ($value > $res->refference_max) {
                                    $DCtestTr .= "<tr><td width='140'>" . $name . "</td><td width='240'></td><td align='right'><b>" . $value . "</b></td><td width='30'>&nbsp;</td><td>" . $mes . "</td>";
                                    if ($valueState && $tmpValState) {
                                        $DCtestTr .= "<td align='center'>H</td>";
                                    }
                                    $DCtestTr .= "";
                                } else {
                                    $DCtestTr .= "<tr><td width='140'>" . $name . "</td><td width='240'></td><td align='right'>" . $value . "</td><td width='30'>&nbsp;</td><td >" . $mes . "</td>";
                                    if ($valueState && $tmpValState) {
                                        $DCtestTr .= "<td align='center'></td>";
                                    }
                                    $DCtestTr .= "";
                                }

                                if ($res->viewnorvals) {
                                    $DCtestTr .= "<td align='center'>(" . $res->refference_min . " - " . $res->refference_max . " " . $res->measurement . ")</td>";
                                } else {
                                    $DCtestTr .= "<td></td>";
                                }
                                echo $DCtestTr;
                            } else if ($name == "RANDOM PLASMA GLUCOSE") {

                                if ($value < $res->refference_min) {
                                    $DCtestTr .= "<tr><td width='140'>" . $name . "</td><td width='240'></td><td align='right'><b>" . $value . "</b></td><td width='30'>&nbsp;</td><td>" . $mes . "</td>";
                                    if ($valueState && $tmpValState) {
                                        $DCtestTr .= "<td align='center'>L</td>";
                                    }
                                    $DCtestTr .= "";
                                } elseif ($value > $res->refference_max) {
                                    $DCtestTr .= "<tr><td width='140'>" . $name . "</td><td width='240'></td><td align='right'><b>" . $value . "</b></td><td width='30'>&nbsp;</td><td>" . $mes . "</td>";
                                    if ($valueState && $tmpValState) {
                                        $DCtestTr .= "<td align='center'>H</td>";
                                    }
                                    $DCtestTr .= "";
                                } else {
                                    $DCtestTr .= "<tr><td width='140'>" . $name . "</td><td width='240'></td><td align='right'>" . $value . "</td><td width='30'>&nbsp;</td><td >" . $mes . "</td>";
                                    if ($valueState && $tmpValState) {
                                        $DCtestTr .= "<td align='center'></td>";
                                    }
                                    $DCtestTr .= "";
                                }

                                if ($res->viewnorvals) {
                                    $DCtestTr .= "<td align='center'>(" . $res->refference_min . " - " . $res->refference_max . " " . $res->measurement . ")</td>";
                                } else {
                                    $DCtestTr .= "<td></td>";
                                }
                                echo $DCtestTr;
                            } else if ($tname == "bp_RBC") {
                                $flag = false;
                                $DCtestTr .= "<tr><td valign='top'>" . $name . "</td><td>" . $value . "</td></tr>"
                                        . "<tr><td valign='top'>&nbsp;</td><td>&nbsp;</td></tr>";
                                echo $DCtestTr;
                            } else if ($tname == "BC_WBC") {
                                $DCtestTr = "";
                                $flag = false;
                                $DCtestTr .= "<tr><td valign='top'>" . $name . "</td><td>" . $value . "</td></tr>"
                                        . "<tr><td valign='top'>&nbsp;</td><td>&nbsp;</td></tr>";
                                echo $DCtestTr;
                            } else if ($tname == "bc_Comment") {
                                $DCtestTr = "";
                                $flag = false;
                                $DCtestTr .= "<tr><td valign='top'>&nbsp;</td><td>&nbsp;</td></tr>"
                                        . "<tr><td valign='top'>" . $name . "</td><td>" . $value . "</td></tr>"
                                        . "<tr><td valign='top'>&nbsp;</td><td>&nbsp;</td></tr>";
                                echo $DCtestTr;
                            } else if ($tname == "Comment 2") {
                                $DCtestTr = "";
                                $flag = false;
                                $DCtestTr .= ""
                                        . "<tr><td valign='top'>" . $name . "</td><td>" . $value . "</td></tr>"
                                        . "<tr><td valign='top'>&nbsp;</td><td>&nbsp;</td></tr>";
                                echo $DCtestTr;
                            } else if ($tname == "bp_Suggest") {
                                $DCtestTr = "";
                                $flag = false;
                                if ($value !== "") {
                                    $DCtestTr .= "<tr><td valign='top'>" . $name . "</td><td>" . $value . "</td></tr>"
                                            . "<tr><td valign='top'>&nbsp;</td><td>&nbsp;</td></tr>";
                                }
                                echo $DCtestTr;
                            } else if ($tname == "Platelet") {
                                $DCtestTr = "";
                                $flag = false;
                                $DCtestTr .= "<tr><td valign='top'>" . $name . "</td><td align='left'>" . $value . "</td></tr>";
                                echo $DCtestTr;
                            } else if ($name == "RANDOM PLASMA GLUCOSE") {

                                if ($value < $res->refference_min) {
                                    $DCtestTr .= "<tr><td width='140'>" . $name . "</td><td width='240'></td><td align='right'><b>" . $value . "</b></td><td width='30'>&nbsp;</td><td>" . $mes . "</td>";
                                    if ($valueState && $tmpValState) {
                                        $DCtestTr .= "<td align='center'>L</td>";
                                    }
                                    $DCtestTr .= "";
                                } elseif ($value > $res->refference_max) {
                                    $DCtestTr .= "<tr><td width='140'>" . $name . "</td><td width='240'></td><td align='right'><b>" . $value . "</b></td><td width='30'>&nbsp;</td><td>" . $mes . "</td>";
                                    if ($valueState && $tmpValState) {
                                        $DCtestTr .= "<td align='center'>H</td>";
                                    }
                                    $DCtestTr .= "";
                                } else {
                                    $DCtestTr .= "<tr><td width='140'>" . $name . "</td><td width='240'></td><td align='right'>" . $value . "</td><td width='30'>&nbsp;</td><td >" . $mes . "</td>";
                                    if ($valueState && $tmpValState) {
                                        $DCtestTr .= "<td align='center'></td>";
                                    }
                                    $DCtestTr .= "";
                                }

                                if ($res->viewnorvals) {
                                    $DCtestTr .= "<td align='center'>(" . $res->refference_min . " - " . $res->refference_max . " " . $res->measurement . ")</td>";
                                } else {
                                    $DCtestTr .= "<td></td>";
                                }
                                echo $DCtestTr;
                            } else if ($name == "Colour") {
                                $flag = false;
                                $DCtestTr .= "<tr><td width='140'>" . $name . "</td><td width='240'></td><td align='right'><b>" . $value . "</b></td><td width='30'>&nbsp;</td><td>" . $mes . "</td>";
                                if ($valueState && $tmpValState) {
                                    $DCtestTr .= "<td align='center'></td>";
                                }
                                $DCtestTr .= "";
                                echo $DCtestTr;
                            } else if ($name == "HUMAN CHORIONIC GONEDOTROPHINE HORMONE (HCG)") {
                                $flag = false;
                                $DCtestTr .= "<tr><td width='140'>" . $name . "</td><td width='240'></td><td align='right'><b>" . $value . "</b></td><td width='30'>&nbsp;</td><td>" . $mes . "</td>";
                                if ($valueState && $tmpValState) {
                                    $DCtestTr .= "<td align='center'></td>";
                                }
                                $DCtestTr .= "";
                                echo $DCtestTr;
                            } else if ($name == "Fasting plasma glucose ") {
                                $flag = false;
                                $DCtestTr .= "<tr><td width='140'>" . $name . "</td><td width='240'></td><td align='right'><b>" . $value . "</b></td><td width='30'>&nbsp;</td><td>" . $mes . "</td>";
                                if ($valueState && $tmpValState) {
                                    $DCtestTr .= "<td align='center'></td>";
                                }
                                $DCtestTr .= "";
                                echo $DCtestTr;
                            } else if ($name == "Comment") {
                                $testComment = "<br/><br/> Comments : <br/><br/>" . $value;
                            } elseif ($name == "Urine Culture") {
                                $UrineCulture = "<br/> <u>Urine Culture</u> <br/><br/>" . $value;
                            } elseif ($name == "Norfloxacine-1") {
                                $nor = "Norfloxacine";
                                $nor1 = $value;
                            } elseif ($name == "Norfloxacine-2") {
                                $nor = "Norfloxacine";
                                $nor2 = $value;
                            } elseif ($name == "Cefalexin-1") {
                                $cef = "Cefalexin";
                                $cef1 = $value;
                            } elseif ($name == "Cefalexin-2") {
                                $cef = "Cefalexin";
                                $cef2 = $value;
                            } elseif ($name == "Cotrimoxasole-1") {
                                $cot = "Cotrimoxasole";
                                $cot1 = $value;
                            } elseif ($name == "Cotrimoxasole-2") {
                                $cot = "Cotrimoxasole";
                                $cot2 = $value;
                            } elseif ($name == "Nitrofurantoin-1") {
                                $nit = "Nitrofurantoin";
                                $nit1 = $value;
                            } elseif ($name == "Nitrofurantoin-2") {
                                $nit = "Nitrofurantoin";
                                $nit2 = $value;
                            } elseif ($name == "Cefuroxime-1") {
                                $cefu = "Cefuroxime";
                                $cefu1 = $value;
                            } elseif ($name == "Cefuroxime-2") {
                                $cefu = "Cefuroxime";
                                $cefu2 = $value;
                            } elseif ($name == "Augmentin-1") {
                                $aug = "Augmentin";
                                $aug1 = $value;
                            } elseif ($name == "Augmentin-2") {
                                $aug = "Augmentin";
                                $aug2 = $value;
                            } elseif ($name == "Pus Cells") {

                                $UFRpusTr = "<br/><tr><td><b><u>Centrifuged Deposits</u></b></td></tr>";

                                $UFRpusTr .= "<tr><td width='140'>" . $name . "</td><td width='1'></td><td align='right'><br/><b>" . $value . "</b></td><td width='30'>&nbsp;</td><td><br/>" . $mes . "</td><td width='30'>";

                                $UFRpusTr .= "<td></td>";

                                $UFRpusTr .= "</td>";

                                echo $UFRpusTr;
                            } else {
                                ?>

                                <tr>

                                    <?php
                                    if ($advRef) {

                                        $refMin = "";
                                        $refMax = "";

                                        //make age to days
                                        $ageFromDays = $age * 365;
                                        $monthFromDays = $months * 30;
                                        $age_days = $ageFromDays + $monthFromDays + $days;



                                        $resultAR = DB::select("select min,max from reference_values where Lab_has_test_lhtid = '" . $LHTID . "' and age_range_id in (select id from age_range where min< '" . $age_days . "' and max >= '" . $age_days . "') and gender_idgender = (select idgender from gender where gender = '" . $gender . "')");
                                        foreach ($resultAR as $resAR) {
                                            $refMin = $resAR->min;
                                            $refMax = $resAR->max;
                                        }
                                        if ($refMin == "") {
                                            $resultAR = DB::select("select min,max from reference_values where Lab_has_test_lhtid = '" . $LHTID . "' and age_range_id in (select id from age_range where min< '" . $age_days . "' and max >= '" . $age_days . "')");
                                            foreach ($resultAR as $resAR) {
                                                $refMin = $resAR->min;
                                                $refMax = $resAR->max;
                                            }
                                        }
                                    } else {

//                -----------------Age and gedner wise reference ranges (start)-----------------------

                                        if ($name == "CK  ( C.P.K. )") {

                                            if ($gender == "Male") {
                                                $res->refference_min = "0";
                                                $res->refference_max = "171";
                                            } else if ($gender == "Female") {
                                                $res->refference_min = "0";
                                                $res->refference_max = "145";
                                            }
                                        }

                                        if ($name == "BLOOD  UREA") {
                                            if (($age == 0) && ($months == 0) && ($days != 0)) {
                                                $res->refference_min = "12.9";
                                                $res->refference_max = "42.9";
                                            } else if (($age == 0) && ($months != 0)) {
                                                $res->refference_min = "12.9";
                                                $res->refference_max = "42.9";
                                            } else if ($age != 0) {
                                                if ($age >= 21 && $age <= 60) {
                                                    $res->refference_min = "12.9";
                                                    $res->refference_max = "42.9";
                                                } else if ($age >= 61 && $age <= 90) {
                                                    $res->refference_min = "17.2";
                                                    $res->refference_max = "49.3";
                                                } else if ($age >= 91 && $age <= 120) {
                                                    $res->refference_min = "21.4";
                                                    $res->refference_max = "66.5";
                                                }
                                            }
                                        }

                                        if ($name == "SGPT (ALT)") {
                                            if ($gender == "Male") {
                                                $res->refference_min = "0";
                                                $res->refference_max = "45";
                                            } else if ($gender == "Female") {
                                                $res->refference_min = "0";
                                                $res->refference_max = "34";
                                            }
                                        }

                                        if ($name == "GAMMA -  G.T.") {
                                            if ($gender == "Male") {
                                                $res->refference_min = "10";
                                                $res->refference_max = "71";
                                            } else if ($gender == "Female") {
                                                $res->refference_min = "6";
                                                $res->refference_max = "42";
                                            }
                                        }

                                        if ($name == "BILIRUBIN - TOTAL") {
                                            if (($age == 0) && ($months == 0) && ($days != 0)) {
                                                if ($days >= 10 && $days <= 31) {
                                                    $res->refference_min = "0.2";
                                                    $res->refference_max = "1.2";
                                                }
                                            } else if (($age == 0) && ($months != 0)) {
                                                $res->refference_min = "0.2";
                                                $res->refference_max = "1.2";
                                            } else if ($age != 0) {
                                                $res->refference_min = "0.2";
                                                $res->refference_max = "1.2";
                                            }
                                        }

                                        if ($name == "URIC  ACID") {
                                            if ($gender == "Male") {
                                                $res->refference_min = "3.5";
                                                $res->refference_max = "7.2";
                                            } else if ($gender == "Female") {
                                                $res->refference_min = "2.6";
                                                $res->refference_max = "6.0";
                                            }
                                        }

                                        if ($name == "SERUM  CREATININE") {
                                            if ($gender == "Male") {
                                                $res->refference_min = "0.4";
                                                $res->refference_max = "1.4";
                                            } else if ($gender == "Female") {
                                                $res->refference_min = "0.4";
                                                $res->refference_max = "1.3";
                                            }
                                        }
                                        

                                        if ($name == "T.S.H (3 rd Generation)") {
                                            if (($age == 0) && ($months == 0) && ($days != 0)) {
                                                $res->refference_min = "1.406";
                                                $res->refference_max = "17.28";
                                            } else if (($age == 0) && ($months != 0)) {
                                                $res->refference_min = "0.75";
                                                $res->refference_max = "13.00";
                                            } else if ($age != 0) {
                                                if ($age >= 1 && $age <= 6) {
                                                    $res->refference_min = "0.49";
                                                    $res->refference_max = "6.9";
                                                } else if ($age >= 7 && $age <= 11) {
                                                    $res->refference_min = "0.40";
                                                    $res->refference_max = "5.7";
                                                } else if ($age >= 12 && $age <= 20) {
                                                    $res->refference_min = "0.36";
                                                    $res->refference_max = "4.91";
                                                } else if ($age >= 21 && $age <= 120) {
                                                    $res->refference_min = "0.30";
                                                    $res->refference_max = "4.50";
                                                }
                                            }
                                        }

                                        if ($name == "FREE THYROXINE (F.T4)") {
                                            if (($age == 0) && ($months == 0) && ($days != 0)) {
                                                $res->refference_min = "0.70";
                                                $res->refference_max = "2.53";
                                            } else if (($age == 0) && ($months != 0)) {
                                                $res->refference_min = "0.753";
                                                $res->refference_max = "2.36";
                                            } else if ($age != 0) {
                                                if ($age >= 1 && $age <= 6) {
                                                    $res->refference_min = "0.785";
                                                    $res->refference_max = "2.26";
                                                } else if ($age >= 7 && $age <= 20) {
                                                    $res->refference_min = "0.789";
                                                    $res->refference_max = "2.14";
                                                } else if ($age >= 21 && $age <= 120) {
                                                    $res->refference_min = "0.89";
                                                    $res->refference_max = "1.72";
                                                }
                                            }
                                        }

                                        if ($name == "SERUM  FERRITIN") {
                                            if ($gender == "Male") {
                                                $res->refference_min = "25";
                                                $res->refference_max = "350";
                                            } else if ($gender == "Female") {
                                                $res->refference_min = "13";
                                                $res->refference_max = "232";
                                            }
                                        }
                                        
                                        if($name == "<br/>HAEMOGLOBIN" || $name == "HAEMOGLOBIN"){
                                            if($gender == "Female"){
                                                $res->refference_min = "11.0";
                                                $res->refference_max = "16.5";
                                            }else if($gender == "Male"){
                                                $res->refference_min = "11.5";
                                                $res->refference_max = "18.0";
                                            }
                                        }







//                ---------------------Age and gedner wise reference ranges (end)-----------------------------

                                        $refMin = $res->refference_min;
                                        $refMax = $res->refference_max;
                                    }

                                    if (is_numeric($value)) {
//            if(true){




                                        if ($value < $refMin) {
                                            ?>  

                                            <td width="{{ $freeTDWidth or '200'}}">{{ $name }}</td><td width="1" ></td><td id="{{ $res->tid }}" align="{{ $alignRight or 'left' }}" valign="{{ $alignTop or 'bottom' }}" width="{{ $freeTDWidth or '100'}}" >
                                                <?php if ($valueState) { ?><b><?php } ?>
                                                    {{ $value }}
                                                    <?php if ($valueState) { ?></b><?php } ?>
                                            </td><td width='30'>&nbsp;</td><td width="{{ $freeTDWidth or '60'}}" valign="{{ $alignTop or 'bottom' }}" align="center">{{ $mes }}</td><td  align="center" width="30" valign="{{ $alignTop or 'bottom' }}">
                                                @if($valueState != 0 && $tmpValState && $flag)
                                                L
                                                @endif
                                            </td>   

                                            <?php
                                        } elseif ($value > $refMax) {
                                            ?>

                                            <td width="{{ $freeTDWidth or '200'}}" >{{ $name }}</td><td width="1"></td><td id="{{ $res->tid }}" align="{{ $alignRight or 'left' }}" valign="{{ $alignTop or 'bottom' }}" width="{{ $freeTDWidth or '100'}}" >
                                                <?php if ($valueState) { ?><b><?php } ?>
                                                    {{ $value }}
                                                    <?php if ($valueState) { ?></b><?php } ?>
                                            </td><td width='30' valign="bottom">&nbsp;</td><td width="{{ $freeTDWidth or '60'}}" align="center"  valign="{{ $alignTop or 'bottom' }}">{{ $mes }}</td><td align="center" width="30" valign="{{ $alignTop or 'bottom' }}"> 
                                                @if($valueState != 0 && $tmpValState && $flag)
                                                H
                                                @endif
                                                <!-- ------ Added by Selaka-----(Start)----- -->
                                                <?php
                                                if ($OverrideFlag) {
                                                    echo "L";
                                                }
                                                ?>
                                                <!-- ------ Added by Selaka-----(End)----- -->
                                            </td>

                                            <?php
                                        } else {
                                            ?>
                                            <td width="{{ $freeTDWidth or '200'}}">{{ $name }}</td><td width="1"></td><td id="{{ $res->tid }}" align="{{ $alignRight or 'left' }}" valign="{{ $alignTop or 'bottom' }}" width="{{ $freeTDWidth or '100'}}" >{{ $value }}</td><td width='30'>&nbsp;</td><td width="{{ $freeTDWidth or '60'}}" valign="{{ $alignTop or 'bottom' }}" align="center" >{{ $mes }}</td><td align="center" width="30" valign="{{ $alignTop or 'bottom' }}">
                                                @if($valueState != 0 && $tmpValState && $flag)
                                                <!--                                        [NORMAL]-->
                                                @endif

                                            </td>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <td width="{{ $freeTDWidth or '200'}}">{{ $name }}</td><td width="1"></td><td id="{{ $res->tid }}" align="{{ $alignRight or 'left' }}" valign="{{ $alignTop or 'bottom' }}" width="{{ $freeTDWidth or '100'}}" style="font-weight: {{ $boldValue or normal  }}">{{ $value }}</td><td width='30'>&nbsp;</td><td width="{{ $freeTDWidth or '60'}}" valign="{{ $alignTop or 'bottom' }}" align="center" >{{ $mes }}</td><td align="center" width="30" valign="{{ $alignTop or 'bottom' }}">
                                            @if($valueState != 0 && $tmpValState && $flag)
                                            <!--                                        [NORMAL]-->
                                            @endif
                                            <?php
                                                if ($OverrideFlag) {
                                                    echo "L";
                                                }
                                                ?>
                                        </td>
                                        <?php
                                    }
                                    ?>

                                    <?php
                                    if ($res->viewnorvals) {
                                        if ($res->refference_min != "") {
                                            $max = $res->refference_max;
                                            if ($res->refference_max == 0) {
                                                $max = "<";
                                            }
                                            ?>



                                            <!-- select words as reference -->
                                            <?php
                                            if ($name == "1 hour") {
                                                ?>    
                                                <td align="center" valign="{{ $alignTop or 'bottom' }}">Less than 180</td>
                                            <?php } else if ($name == "2 hours") { ?> 
                                                <td align="center" valign="{{ $alignTop or 'bottom' }}">Less than 138</td>
                                            <?php } else if ($name == "RHEUMATOID  FACTORx") { ?> 
                                                <td align="center" valign="{{ $alignTop or 'bottom' }}">< 8.0</td> 
                                            <?php } else if ($name == "RANDOM BLOOD GLUCOSE(Capillary)") { ?> 
                                                <td align="center" valign="{{ $alignTop or 'bottom' }}">( Normal < 200 mg/dL )</td>
                                            <?php } else if ($name == "After giving 75 g of Glucose<br/><br/>2<sup>nd</sup> hour PLASMA GLUCOSE") { ?> 
                                                <td align="center" valign="{{ $alignTop or 'bottom' }}">( Normal < 200 mg/dL )</td>
                                            <?php } else if ($name == "<br/>GFR ") { ?> 
                                                <td align="center" valign="{{ $alignTop or 'bottom' }}"> >90 </td>
                                            <?php } else { ?>





                                                <?php
                                                // --------added by Selaka--(Start)------
                                                if ($refMin == "0") {

                                                    $refMax = "< " . $refMax;
                                                    $lesThanValue = true;
                                                    // --------added by Selaka--(End)------
                                                }
                                                ?>

                                                <td align="center" valign="{{ $alignTop or 'bottom' }}"> <?php
                                                    if ($refMin == 0) {
                                                        echo $refMax;
                                                    } else if ($refMin == "-") {
                                                        echo $refMax;
                                                    } else {
                                                        echo $refMin . " - " . $refMax;
                                                    }
                                                    ?></td>


                                            <?php } ?>  

                                            <?php
                                        } else {
                                            ?>
                                            <td></td>    
                                            <?php
                                        }
                                        ?>

                                        <?php
                                    } else {
                                        ?>
                                        <td></td>
                                        <?php
                                    }
                                    ?>   

                                </tr>


                                <?php
                            }
                        }
                        ?>

                </table>


                <div style="color: black; font-size: 12pt;">           <?php
                    if (isset($testComment)) {
                        echo $testComment;
                    }

                    if (isset($UrineCulture)) {
                        echo $UrineCulture;
                    }
                    ?>

                    <?php
                    if (isset($cef)) {
                        ?>
                        <br/>
                        <hr/>
                        <i>Antibiotics Pattern</i>
                        <br/>
                        <table width="400">
                            <tr>
                                <th style="border-color: #000; border-width: 1px; border-style: solid;">Name</th>
                                <th style="border-color: #000; border-width: 1px; border-style: solid;">1st Line</th>
                                <th style="border-color: #000; border-width: 1px; border-style: solid;">2nd Line</th>
                            </tr>
                            <tr>
                                <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $nor; ?></td>
                                <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $nor1; ?></td>
                                <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $nor2; ?></td>
                            </tr>
                            <tr>
                                <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $cef; ?></td>
                                <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $cef1; ?></td>
                                <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $cef2; ?></td>
                            </tr>
                            <tr>
                                <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $cot; ?></td>
                                <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $cot1; ?></td>
                                <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $cot2; ?></td>
                            </tr>
                            <tr>
                                <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $nit; ?></td>
                                <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $nit1; ?></td>
                                <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $nit2; ?></td>
                            </tr>
                            <tr>
                                <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $cefu; ?></td>
                                <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $cefu1; ?></td>
                                <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $cefu2; ?></td>
                            </tr>
                            <tr>
                                <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $aug; ?></td>
                                <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $aug1; ?></td>
                                <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $aug2; ?></td>
                            </tr>
                        </table>
                        <?php
                    }
                    ?>




                    <br/> 

                    <?php
                }

                echo $tData;
                ?>




                <!-- 
                    Special Note view
                -->

                @if($viewSpecialNote)
                <!--<br/>-->
                <br/>
                @if(isset($showFHours) && $fastingHours !== '0' && $fastingHours !== Null && $fastingHours !== "")
                <div style="font-size: 11pt; font-weight: bold;">
                    {{ $fastingHours }} Hours Fasting
                </div>
                @endif

                <div style="font-size: 10pt;font-weight: bold;">{{$specialNote}}
                    @endif
                </div>
                <br/>

                <!-- End -->


                <!-- Comment From Database -->    
                <!--<div>-->
                    <?php
//                    if ($TGComment == true) {
                    ?>
                <div style="font-size:11pt;">
                
                    <?php
                        echo $TGCommentText;
                       ?> 
                
                </div>
                <?php
//                    }
                    ?>
                    <!-- Comment From Database -->  
                    
                    <?php
                        if ($viewAna) {
                            if ($anaID != '' && $anaID != 'null' && $anaID != null) {
                                $resulta = DB::select("select name from analyzers where anid = '" . $anaID . "'");
                                foreach ($resulta as $resa) {
                                    $analyzerName = $resa->name;
                                }
                                ?>
                        <br/>
                        <p style="font-size: 12pt;font-family: Courier New;">PERFORMED BY : {{ $analyzerName or '' }}</p>


            <?php
        }
    }
    ?>




                    <?php // Pasindu Samarakoon ========================================================================================================               ?>
                    @if(isset($TGBSS) || isset($TGFPG))

                    <br/>

                    <div>
                        <table style="font-size: 9pt;" border="1">
                            <tr><td><p style="font-size: 9pt;"><b>CONVERSION FACTOR</b></br>mg/dl  x  0.055  = mmol/L </p></td></tr>
                        </table>
                        <br/>
                    </div>
                    @endif

                    @if(isset($TGUCABST))
                    <div>

                        <table style="font-size: 12px;">
                            <tr><td>S = Highly  Sensitive &nbsp; &nbsp;</td><td>R = Resistant&nbsp; &nbsp;</td><td>MS = Moderatly  Sensitive</td></tr>
                        </table>
                        <br/>
                        <br/>
                        <p style="font-size: 9pt;">MRI - Ref. Number :  C/ 901</p>
                    </div>
                    @endif

                    <br>
                    <br>
                    <br>
                    @if(isset($TGSECRE))
                    <div>
                        <p><u>Average estimated GFR</u></p>
                        <table style="font-size: 12px;">
                            <tr><td></td><td>Male</td><td></td><td>Female</td><td></td></tr>
                            <tr><td>Age</td><td>Mean</td><td>Range</td><td>Mean</td><td>Range</td></tr>
                            <tr><td>20 - 29</td><td>128</td><td>77 - 179</td><td>118</td><td>71 - 165</td></tr>
                            <tr><td>30 - 39</td><td>116</td><td>70 - 162</td><td>107</td><td>64 - 149</td></tr>
                            <tr><td>40 - 49</td><td>105</td><td>63 - 147</td><td>97 </td><td>58 - 135</td></tr>
                            <tr><td>50 - 59</td><td>93 </td><td>56 - 130</td><td>86 </td><td>51 - 120</td></tr>
                            <tr><td>60 - 69</td><td>81 </td><td>49 - 113</td><td>75 </td><td>45 - 104</td></tr>
                            <tr><td>70 - 79</td><td>70 </td><td>42 - 98 </td><td>64 </td><td>39 - 90</td></tr>
                            <tr><td>80 - 89</td><td>58 </td><td>35 - 81 </td><td>53 </td><td>32 - 75 </td></tr>
                        </table>

                        <p>e.GFR estimates between 60 and 89 do not indicate CKD unless there are other existing laboratory/clinical evidence of disease-NHS Guidlines (UK)</p></br>
                        <p>* Serum creatinine method has been calibrated to be traceable to GC-IDMS.</br>* e GFR is calcuated using, GC-IDMS traceable CKD epi study Equation.</p>

                    </div>
                    @endif

                    @if(isset($TGPPPG))
                    <div>

                        <table style="font-size: 12px;">
                            <tr><td>Ref.Ranges (mg/dl)</td></tr>
                            <tr><td>Normal < 140</td></tr><tr><td>Impaired 140 - 200</td></tr><tr><td>High > 200</td></tr>
                        </table></br>
                        <table style="font-size: 12px;" border="1">
                            <tr><td><p><b>CONVERSION FACTOR</b></br>mg/dl  x  0.055  = mmol/L </p></td></tr>
                        </table>
                        <br/>
                    </div>
                    @endif

                    @if(isset($TGPPPG1))
                    <div>

                        <table style="font-size: 12px;">
                            <tr><td>Ref.Ranges (mg/dl)</td></tr>
                            <tr><td>< 180</td></tr><tr><td></td></tr><tr><td></td></tr>
                        </table></br>
                        <table style="font-size: 12px;" border="1">
                            <tr><td><p><b>CONVERSION FACTOR</b></br>mg/dl  x  0.055  = mmol/L </p></td></tr>
                        </table>
                        <br/>
                    </div>
                    @endif

                    @if(isset($TGRFT))
                    <div>

                        <table style="font-size: 12px;">
                            <tr><td>Comment  :</td></tr>
                            <tr><td>Ref Range (Test) < 30 IU/ml</td></tr>
                            <tr><td>Ref Range <  08 mg/l </td></tr><tr><td>Test done by fully Automated Turbidimetry Assay.</td></tr><tr><td></td></tr>
                            <!--<tr><td>Ref Range <  08 mg/l </td></tr><tr><td></td></tr><tr><td></td></tr>-->
                        </table>
                    </div>
                    @endif

                    @if(isset($TGREANALPRO))
                    <div style="margin-top: -60px;">
                        <p><u>Average estimated GFR</u></p>
                        <table style="font-size: 12px;">
                            <tr><td></td><td>Male</td><td></td><td>Female</td><td></td></tr>
                            <tr><td>Age</td><td>Mean</td><td>Range</td><td>Mean</td><td>Range</td></tr>
                            <tr><td>20 - 29</td><td>128</td><td>77 - 179</td><td>118</td><td>71 - 165</td></tr>
                            <tr><td>30 - 39</td><td>116</td><td>70 - 162</td><td>107</td><td>64 - 149</td></tr>
                            <tr><td>40 - 49</td><td>105</td><td>63 - 147</td><td>97 </td><td>58 - 135</td></tr>
                            <tr><td>50 - 59</td><td>93 </td><td>56 - 130</td><td>86 </td><td>51 - 120</td></tr>
                            <tr><td>60 - 69</td><td>81 </td><td>49 - 113</td><td>75 </td><td>45 - 104</td></tr>
                            <tr><td>70 - 79</td><td>70 </td><td>42 - 98 </td><td>64 </td><td>39 - 90</td></tr>
                            <tr><td>80 - 89</td><td>58 </td><td>35 - 81 </td><td>53 </td><td>32 - 75 </td></tr>
                        </table>

                        <p>e.GFR estimates between 60 and 89 do not indicate CKD unless there are other existing laboratory/clinical evidence of disease-NHS Guidlines (UK)</p>
                        <p>* Serum creatinine method has been calibrated to be traceable to GC-IDMS.</br>* e GFR is calcuated using, GC-IDMS traceable CKD epi study Equation.</p>

                    </div>


                    @endif

                    @if(isset($TG))

                    <div style="color: black; font-size: 11pt;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <b>Reference Range : Less than 200</b></div>
                    <div>

                        <table style="font-size: 12px;">
                            <tr><td></td></tr>
                        </table></br>
                        <table style="font-size: 12px;" border="1">
                            <tr><td><p><b>CONVERSION FACTOR</b></br>mg/dl  x  0.055  = mmol/L </p></td></tr>
                        </table>
                        <br/>
                    </div>
                    @endif

                    @if(isset($TGRPGC))

                    <div style="color: black; font-size: 11pt;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <b>Reference Range : Less than 200</b></div>

                    @endif

                    @if(isset($TGSCABST))
                    <div>
                        <table style="font-size: 12px;">
                            <tr><td>S = Highly  Sensitive &nbsp;&nbsp;&nbsp; </td><td>R = Resistant &nbsp;&nbsp;&nbsp;</td><td>MS = Moderatly  Sensitive</td></tr>
                        </table>
                        <br/>
                    </div>
                    @endif

                    @if(isset($TGCRP))
                    <div>
                        <table style="font-size: 12px;">
                            <tr><td>Comment  :</td></tr>
                            <tr><td>Test done by fully Automated Turbidimetry Assay.</td></tr><tr><td></td></tr>
                        </table>
                    </div>
                    @endif

                    @if(isset($TGH)) 
                    <div>
                        <table style="font-size: 10pt;">
                            <tr><td width="80">Ref.  Ranges:</td><td width="80"></td><td></td><td width="80"></td><td></td></tr>
                            <tr><td></td><td>Male</td><td></td><td>Female</td><td></td></tr>
                            <tr><td>RBC</td><td>(4.5 - 5.6)</td><td></td><td>(3.9 - 4.9)</td><td>× 10^6/ µL</td></tr>
                            <tr><td>HGB</td><td>(13.5 - 16.5)</td><td></td><td>(11.8 - 14.8)</td><td>g / dL</td></tr>
                            <tr><td>HCT</td><td>(40 - 47)</td><td></td><td>(36 - 44)</td><td>%</td></tr>
                            <tr><td>MCV</td><td>(76 - 96)</td><td></td><td></td><td>fl</td></tr>
                            <tr><td>MCH</td><td>(27 - 33)</td><td></td><td></td><td>pg</td></tr>
                            <tr><td>MCHC</td><td>(32 - 36)</td><td></td><td></td><td>g / dl</td></tr>
                        </table>
                        <br/>
                    </div>
                    @endif

                    @if ($sysmexReport)
                    <div style="position: absolute; right: 10px; top: 490px;">
                        <table style="font-size: 10pt;">
                            <tr><td width="80">Ref.  Ranges:</td><td width="80"></td><td></td><td width="80"></td><td></td></tr>
                            <tr><td></td><td>Male</td><td></td><td>Female</td><td></td></tr>
                            <tr><td>RBC</td><td>(4.5 - 5.6)</td><td></td><td>(3.9 - 4.9)</td></tr>
                            <tr><td>HGB</td><td>(13.5 - 16.5)</td><td></td><td>(11.8 - 14.8)</td></tr>
                            <tr><td>HCT</td><td>(40 - 47)</td><td></td><td>(36 - 44)</td></tr>
                            <tr><td>MCV</td><td>(76 - 96)</td><td></td><td></td></tr>
                            <tr><td>MCH</td><td>(27 - 33)</td><td></td><td></td></tr>
                            <tr><td>MCHC</td><td>(32 - 36)</td><td></td><td></td></tr>
                        </table>
                        <br/>
                    </div>
                    @endif

                    @if(isset($TGLP))
                    <div>
                        <table style="font-size: 12px;">
                            <tr><td><u>Comment :</u></td><td></td><td></td><td></td><td></td></tr>
                            <tr><td><b>Cholesterol - Total</b></td><td></td><td><b>Cholesterol - LDL</b></td><td></td><td></td></tr>
                            <tr><td>< 200   ( < 5.2 mmol/L)     </td><td>:Desirable</td><td>< 100   ( < 2.6 mmol/L )      </td><td>:Desirable</td><td></td></tr>
                            <tr><td>200-239 ( 5.2 - 6.2 mmol/L) </td><td>:Borderline</td><td>130-159 ( 3.4 - 4.1 mmol/L)   </td><td>:Borderline</td><td></td></tr>
                            <tr><td>> 240   ( > 6.2 mmol/L)     </td><td>:High</td><td>> 160   ( 4.1 mmol/L )        </td><td>:High</td><td></td></tr>
                            <tr><td></td><td></td><td></td><td></td><td></td></tr>
                            <tr><td><b>Cholesterol - HDL</b></td><td></td><td><b>Triglycerides</b></td><td></td><td></td></tr>
                            <tr><td>50 - 59 ( 1.3 - 1.5 mmol/L )</td><td>:Better</td><td>< 150   ( < 1.7 mmo/L)         </td><td>:Desirable</td><td></td></tr>
                            <tr><td>> 60    ( > 1.5 mmol/L )    </td><td>:Best</td><td>150 - 199 ( 1.7 - 2.2 mmol/L)</td><td>:Borderline</td><td></td></tr>
                            <tr><td></td><td></td><td>200-499  (2.3-5.6 mmol/L)     </td><td>:High</td><td></td></tr>
                            <tr><td></td><td></td><td>> 500   ( > 5.6 mmol/L )       </td><td>:Very High</td><td></td></tr>
                            <tr><td></td><td></td><td></td><td></td><td></td></tr>
                            <tr><td><u>Conversion  Factor</u></td><td></td><td></td><td></td><td></td></tr>
                            <tr><td> Cholesterol </td><td>=mmol/L x 38.66 = mg/dl</td><td></td><td></td><td></td></tr>
                            <tr><td> Triglycerides </td><td>=mmol/L x 87.5   = mg/dl</td><td></td><td></td><td></td></tr>
                        </table>
                        <br/>

                    </div>
                    @endif

                    @if(isset($TGDVNA))
                    <div>
                        <table style="font-size: 12px;">
                            <tr><td>Comment  :</td></tr>
                            <tr><td>NS1 antigen was found cirulating in infected patients from the first day and up to 9 days after onset of fever.</td></tr>
                            <tr><td>A negative test result cannot exclude a recent infection.</br>This is a qualitative test</td></tr>
                        </table>
                    </div>
                    @endif

                    @if(isset($TGHIVAB))
                    <!--                    <div>
                                            <table style="font-size: 12px;">
                                                <tr><td>Comment  :</td></tr>
                                                <tr><td>Test done  by Micropaticle Enzyme  immunoassay.</td></tr>
                                                <tr><td>Specificity = 99.3 %</br>Sensitivity = 100 %</td></tr>
                                            </table>
                                        </div>-->
                    @endif

                    @if(isset($TGHBSA))
                    <div>
                        <table style="font-size: 12px;">
                            <tr><td>Comment  :</td></tr>
                            <tr><td>Test done  by Enzyme  immunoassay.</td></tr>
                            <tr><td>Specificity = 99.2 %</br>Sensitivity = 98.9 %</td></tr>
                        </table>
                    </div>
                    @endif

                    @if(isset($TGHAOC))

                    <!-- <div>
                        <table style="font-size: 12px;">
                            <tr><td><u>Comment :</u></td><td></td><td></td><td></td><td></td></tr>
                            <tr><td>Low Pressure Iron Exchange Liquid Chromatography(LPLC).</br>Interpretation  of  Results</td></tr>
                        </table>
                        <table style="font-size: 12px;">
                            <tr><td><b>%HbA1C</b></td><td></td><td><b></b></td><td align="right"></td><td>mmol/mol  HbA1C</td></tr>
                            <tr><td>>   8   </td><td></td><td>Action  Suggested       </td><td></td><td>>  64</td></tr>
                            <tr><td>7  -   8</td><td></td><td>Good  Control           </td><td></td><td>53  -  64</td></tr>
                            <tr><td><   7   </td><td></td><td>Goal                    </td><td></td><td><  53</td></tr>
                            <tr><td>6  -   7</td><td></td><td>Near  Normal  Glycemia  </td><td></td><td>42  -  53</td></tr>
                            <tr><td><  6    </td><td></td><td>Non  -  Diabetic  Level </td><td></td><td><  42</td></tr>
                        </table>    
                        <table style="font-size: 12px;">    
                            <tr><td>This HbA1C Program has been certified by the NGSP (National Glycohemoglobin Standardization Program) - USA as having documented traceability to the Diabetes control and Complications Trial Reference Method.</td></tr>
                        </table>
                        <br/>

                    </div> -->
                    @endif

                    @if(isset($TGUFMA))
                    <div>
                        <table style="font-size: 12px;">
                            <tr><td><u>Comment : </u></td><td></td><td></td><td></td><td></td></tr>
                            <tr><td><b>Test  Method</b></td><td></td><td></td><td></td><td></td></tr>
                            <tr><td>Fully  Automated  Immunoturbidimetric  Assay.</td></tr>
                        </table>
                        <table style="font-size: 12px;">
                            <tr><td><b>Reference  range</b></td><td></td><td><b></b></td><td></td><td></td></tr>
                            <tr><td><  30       mg</td><td></td><td>albumin / g  Creatinine       =</td><td></td><td>Normal</td></tr>
                            <tr><td>30  -  300  mg</td><td></td><td>albumin / g  Creatinine       =</td><td></td><td>Microalbuminuria</td></tr>
                            <tr><td>>  300      mg</td><td></td><td>albumin / g  Creatinine       =</td><td></td><td>Clinical  albuminuria</td></tr>
                        </table>    

                    </div>
                    @endif

                    @if(isset($TGGCT50))                
                    <div style="color: black; font-size: 11pt;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <b>Reference Range : Less than 180 mg/dl</b>
                    </div>
                    <br/>
                    <p style="color: black; font-size: 11pt;"><i>Non fasting after 1 hour</i></p>

                    @endif

                    @if(isset($TGGCT75))
                    <div style="color: black; font-size: 11pt;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <b>Reference Range : Less than 200 mg/dl</b>
                    </div>

                    <br/>
                    <p style="color: black; font-size: 11pt;"><i>Non fasting after 2 hours</i></p>
                    @endif

                    @if(isset($TGUPCR))
                    <div>
                        <table style="font-size: 12px;">
                            <tr><td><u>Comment : </u></td><td></td><td></td><td></td><td></td></tr>
                        </table>
                        <br/>
                        <table style="font-size: 12px;">
                            <tr><td><b>Reference  range</b></td><td></td><td><b></b></td><td></td><td></td></tr>
                            <tr><td> &nbsp; &nbsp; &nbsp; Normal</td><td></td><td></td><td></td><td></td></tr>
                            <tr><td> &nbsp; &nbsp; &nbsp; Under 2 years</td><td></td><td> = &nbsp; &nbsp; &nbsp; < 0.5</td><td></td><td></td></tr>
                            <tr><td> &nbsp; &nbsp; &nbsp; Adults</td><td></td><td> = &nbsp; &nbsp; &nbsp; < 0.2</td><td></td><td></td></tr>
                            <tr><td> &nbsp; &nbsp; &nbsp; Nephrotic</td><td></td><td> = &nbsp; &nbsp; &nbsp; > 3.5</td><td></td><td></td></tr>
                        </table>    

                    </div>
                    @endif

                    @if(isset($TGTSH))
                    <div>
                        <table style="font-size: 12px;">
                            <tr><td><b>Reference  range</b></td><td></td><td><b></b></td><td></td><td></td></tr>
                            <tr><td></td><td>Adults            </td><td></td><td></td><td>: 0.38  -  4.31</td></tr>
                            <tr><td></td><td>New Born          </td><td></td><td></td><td>: 0.70  -  15.2</td></tr>
                            <tr><td></td><td>6 days –  3 months</td><td></td><td></td><td>: 0.72  -  11.0</td></tr>
                            <tr><td></td><td>4      - 12 months</td><td></td><td></td><td>: 0.73  -  8.30</td></tr>
                            <tr><td></td><td>1      -  6 years</td><td></td><td></td><td>: 0.70  -  5.97</td></tr>
                            <tr><td></td><td>7      - 11 years</td><td></td><td></td><td>: 0.60  -  4.84</td></tr>
                            <tr><td></td><td>12     - 20 years </td><td></td><td></td><td>: 0.51  -  4.30</td></tr>
                        </table>    
                        <table style="font-size: 12px;">
                            <tr><td>comment  :</td></tr>
                            <tr><td>Analytical  sensitivity      :   0.01  µIU/ml</td></tr>    
                            <tr><td>Fluorescence Enzyme Immunoassay  method specific reference  ranges given.</br>(Test done using   TOSOH AIA - 360 Fully Automated Immunoassay Analyzer)</td></tr>
                        </table>
                    </div>
                    @endif

                    @if(isset($TGFT3))
                    <div>
                        <table style="font-size: 12px;">
                            <tr><td><u>Comment : </u></td><td></td><td></td><td></td><td></td></tr>
                        </table>
                        <table style="font-size: 12px;">
                            <tr><td>Analytical  sensitivity      :   0.1  pg/mL</td></tr>
                            <tr><td>Fluorescence Enzyme Immunoassay  method specific reference  ranges given.</br>(Test done using   TOSOH AIA - 360 Fully Automated Immunoassay Analyzer)</td></tr>
                        </table>    

                    </div>
                    @endif

                    @if(isset($TGFT4))
                    <div>
                        <table style="font-size: 12px;">
                            <tr><td><u>Comment : </u></td><td></td><td></td><td></td><td></td></tr>
                        </table>
                        <table style="font-size: 12px;">
                            <tr><td>Analytical  sensitivity      :   0.1  ng/dl</td></tr>
                            <tr><td>Fluorescence Enzyme Immunoassay  method specific reference  ranges given.</br>(Test done using   TOSOH AIA - 360 Fully Automated Immunoassay Analyzer)</td></tr>
                        </table>    

                    </div>
                    @endif

                    @if(isset($TGSBHCG))
                    <div>
                        <table style="font-size: 12px;">
                            <tr><td>HCG Ranges during normal Pregnancy :</td></tr>
                            <tr><td>Weeks post LMP </td></tr>
                            <tr><td>(Last Menstrual Period)</td></tr>
                        </table>
                        <table style="font-size: 12px;">
                            <tr><td><b>Reference  range</b></td><td></td><td><b></b></td><td></td><td></td></tr>
                            <tr><td></td><td>1    Week     </td><td></td><td></td><td>         < 50.0</td></tr>
                            <tr><td></td><td>2    Weeks    </td><td></td><td></td><td>    40 -   1000</td></tr>
                            <tr><td></td><td>3    Weeks    </td><td></td><td></td><td>   100 -   5000</td></tr>
                            <tr><td></td><td>4    Weeks    </td><td></td><td></td><td>   600 -  10000</td></tr>
                            <tr><td></td><td>5 - 6 Weeks   </td><td></td><td></td><td>  1500 - 100000</td></tr>
                            <tr><td></td><td>7 - 8 Weeks   </td><td></td><td></td><td> 16000 - 200000</td></tr>
                            <tr><td></td><td>2 - 3 Months  </td><td></td><td></td><td> 12000 - 300000</td></tr>
                            <tr><td></td><td>2 nd trimester</td><td></td><td></td><td> 24000 -  55000</td></tr>
                            <tr><td></td><td>3 rd trimester</td><td></td><td></td><td>  6000 -  48000</td></tr>
                            <tr><td></td><td>Non pregnant  </td><td></td><td></td><td>          < 5.0</td></tr>
                            <tr><td></td><td>Male          </td><td></td><td></td><td>          < 5.0</td></tr>
                        </table>    
                        <table style="font-size: 12px;">
                            <tr><td>comment  :</td></tr>
                            <tr><td>Method :Analytical  sensitivity      :   0.5  mIU/ml</td></tr>    
                            <tr><td>Fluorescence Enzyme Immunoassay  method specific reference  ranges given.</br>(Test done using   TOSOH AIA - 360 Fully Automated Immunoassay Analyzer)</td></tr>
                        </table>
                    </div>
                    @endif

                    @if(isset($TGPROLACTIN))
                    <!--                    <div>
                                            <table style="font-size: 12px;">
                                                <tr><td>ADULT REF. RANGES</td><td></td><td></td><td></td><td></td></tr>
                                                <tr><td>Females</td><td></td><td>4.1 - 28.9   ng/ml</td><td></td><td></td></tr>
                                                <tr><td>Male</td><td></td><td>3.6 - 16.3   ng/ml</td><td></td><td></td></tr>
                                            </table>
                                            <table style="font-size: 12px;">
                                                <tr><td><u>Comment : </u></td><td></td><td></td><td></td><td></td></tr>
                                            </table>
                                            <table style="font-size: 12px;">
                                                <tr><td>Method :Analytical  sensitivity      :   1.0  mIU/ml</td></tr>
                                                <tr><td>Fluorescence Enzyme Immunoassay  method specific reference  ranges given.</br>(Test done using   TOSOH AIA - 360 Fully Automated Immunoassay Analyzer)</td></tr>
                                            </table>    
                    
                                        </div>-->
                    @endif

                    @if(isset($TGFSH))
                    <div>
                        <table style="font-size: 12px;">
                            <tr><td>ADULT REF. RANGES ( mIU/ml )</td><td></td><td></td><td></td><td></td></tr>
                            <tr><td></td><td>Females Folicular Phase </td><td></td><td></td><td>4.5  -  11.0</td></tr>
                            <tr><td></td><td>Females Ovalatory       </td><td></td><td></td><td>3.6  -  20.6</td></tr>
                            <tr><td></td><td>Females Luteal phase    </td><td></td><td></td><td>1.5  -  10.8</td></tr>
                            <tr><td></td><td>Post menopausal         </td><td></td><td></td><td>36.6 - 168.8</td></tr>
                            <tr><td></td><td>Male                    </td><td></td><td></td><td>2.1  -  18.6</td></tr>
                        </table>
                        <table style="font-size: 12px;">
                            <tr><td><u>Comment : </u></td><td></td><td></td><td></td><td></td></tr>
                        </table>
                        <table style="font-size: 12px;">
                            <tr><td>Method :Analytical  sensitivity      :   1.0  mIU/ml</td></tr>
                            <tr><td>Fluorescence Enzyme Immunoassay  method specific reference  ranges given.</br>(Test done using   TOSOH AIA - 360 Fully Automated Immunoassay Analyzer)</td></tr>
                        </table>    

                    </div>
                    @endif

                    @if(isset($TGLUH))
                    <div>
                        <table style="font-size: 12px;">
                            <tr><td>ADULT REF. RANGES ( mIU/ml )</td><td></td><td></td><td></td><td></td></tr>
                            <tr><td></td><td>Females Folicular Phase </td><td></td><td></td><td> 1.7 - 13.3</td></tr>
                            <tr><td></td><td>Females Ovalatory       </td><td></td><td></td><td> 4.1 - 68.7</td></tr>
                            <tr><td></td><td>Females Luteal phase    </td><td></td><td></td><td> 0.5 - 19.8</td></tr>
                            <tr><td></td><td>Post menopausal         </td><td></td><td></td><td>14.4 - 62.2</td></tr>
                            <tr><td></td><td>Male                    </td><td></td><td></td><td> 1.7 - 11.2</td></tr>
                        </table>
                        <table style="font-size: 12px;">
                            <tr><td><u>Comment : </u></td><td></td><td></td><td></td><td></td></tr>
                        </table>
                        <table style="font-size: 12px;">
                            <tr><td>Method :Analytical  sensitivity      :   0.2  mIU/ml</td></tr>
                            <tr><td>Fluorescence Enzyme Immunoassay  method specific reference  ranges given.</br>(Test done using   TOSOH AIA - 360 Fully Automated Immunoassay Analyzer)</td></tr>
                        </table>    

                    </div>
                    @endif

                    @if(isset($TGT2G))
                    <div>
                        <table style="font-size: 12px;">
                            <tr><td><u>REF. RANGES ( ng/dl )</u></td><td></td><td></td><td></td><td></td></tr>
                        </table>
                        <table style="font-size: 12px;">
                            <tr><td>male</td><td></td><td>female</td><td></td><td></td></tr>
                            <tr><td>Newborn     </td><td>75 - 400  </td><td>Newborn     </td><td>20 - 64</td><td></td></tr>
                            <tr><td>1 -5 months </td><td>14 - 363  </td><td>1 -5 months </td><td>< 20</td><td></td></tr>
                            <tr><td>6 -24 months</td><td>< 37      </td><td>6 -24 months</td><td>< 9</td><td></td></tr>
                            <tr><td>2 -3 years  </td><td>< 15      </td><td>2 -3 years  </td><td>< 20</td><td></td></tr>
                            <tr><td>4 -5 years  </td><td>< 19      </td><td>4 -5 years  </td><td>< 30</td><td></td></tr>
                            <tr><td>6 -7 years  </td><td>< 13      </td><td>6 -7 years  </td><td>< 7</td><td></td></tr>
                            <tr><td>8 -9 years  </td><td>2 - 8     </td><td>8 -9 years  </td><td>1 - 11</td><td></td></tr>
                            <tr><td>10 -11 years</td><td>2 - 165   </td><td>10 -11 years</td><td>3 - 32</td><td></td></tr>
                            <tr><td>12 -13 years</td><td>3 - 619   </td><td>12 -13 years</td><td>6 - 50</td><td></td></tr>
                            <tr><td>14 -15 years</td><td>31 -733   </td><td>14 -15 years</td><td>6 - 52</td><td></td></tr>
                            <tr><td>16 -17 years</td><td>158 - 826 </td><td>16 -17 years</td><td>9 - 58</td><td></td></tr>
                            <tr><td>18 -39 years</td><td>300 - 1080</td><td>18 -39 years</td><td>9 - 55</td><td></td></tr>
                            <tr><td>40 -59 years</td><td>300 - 890 </td><td>40 -59 years</td><td>9 - 55</td><td></td></tr>
                            <tr><td>> 60 years  </td><td>300 - 720 </td><td>> 60 years  </td><td>5 - 32</td><td></td></tr>
                        </table>
                        <table style="font-size: 12px;">
                            <tr><td><u>Comment : </u></td><td></td><td></td><td></td><td></td></tr>
                        </table>
                        <table style="font-size: 12px;">
                            <tr><td>Method :Analytical  sensitivity      :   1.0  mIU/ml</td></tr>
                            <tr><td>Fluorescence Enzyme Immunoassay  method specific reference  ranges given.</br>(Test done using   TOSOH AIA - 360 Fully Automated Immunoassay Analyzer)</td></tr>
                        </table>    

                    </div>
                    @endif

                    @if(isset($TGSF))
                    <div>
                        <table style="font-size: 12px;">
                            <tr><td><u>REF. RANGES</u></td><td></td><td></td><td></td><td></td></tr>
                        </table>
                        <table style="font-size: 12px;">
                            <tr><td>male</td><td>&nbsp;</td><td>&nbsp;&nbsp;&nbsp;</td><td>female</td><td>&nbsp;</td></tr>
                            <tr><td>14 - 15 Years</td><td>13 - 83 </td><td></td><td>14 - 15 Years</td><td>6 - 67</td><td></td></tr>
                            <tr><td>16 - 19 Years</td><td>11 - 172</td><td></td><td>16 - 19 Years</td><td>6 - 67</td><td></td></tr>
                            <tr><td>20 - 39 Years</td><td>20 - 345</td><td></td><td>20 - 39 Years</td><td>10 - 154</td><td></td></tr>
                            <tr><td>40 - 59 Years</td><td>20 - 380</td><td></td><td>40 - 59 Years</td><td>10 - 232</td><td></td></tr>
                            <tr><td>> 59    Years</td><td>20 - 380</td><td></td><td>> 59    Years</td><td>20 - 288</td><td></td></tr>
                        </table>
                        <table style="font-size: 12px;">
                            <tr><td><u>Paediatric  Ref. range  :  male/Female</u></td><td></td><td></td><td></td><td></td></tr>
                        </table>
                        <table style="font-size: 12px;">
                            <tr><td>4 - 14  Days      </td><td></td><td>100 - 717</td><td></td><td></td></tr>
                            <tr><td>15 days - 5 months</td><td></td><td> 14 - 647</td><td></td><td></td></tr>
                            <tr><td>6 - 11  months    </td><td></td><td>  8 - 182</td><td></td><td></td></tr>
                            <tr><td>1 -  4  Years     </td><td></td><td>  5 - 100</td><td></td><td></td></tr>
                            <tr><td>5 - 13  Years     </td><td></td><td> 14 -  79</td><td></td><td></td></tr>
                        </table>
                        <table style="font-size: 12px;">
                            <tr><td><u>Comment : </u></td><td></td><td></td><td></td><td></td></tr>
                        </table>
                        <table style="font-size: 12px;">
                            <tr><td>Analytical  sensitivity      :   3.0  ng/ml</td></tr>
                            <tr><td>Fluorescence Enzyme Immunoassay  method specific reference  ranges given.</br>(Test done using   TOSOH AIA - 360 Fully Automated Immunoassay Analyzer)</td></tr>
                        </table>    

                    </div>
                    @endif               

                    @if(isset($TGPSAT))
                    <div>
                        <table style="font-size: 12px;">
                            <tr><td><u>REF. RANGES</u></td><td></td><td></td><td></td><td></td></tr>
                        </table>
                        <table style="font-size: 12px;">
                            <tr><td>< 49    Years</td><td>&nbsp;</td><td></td><td>< 2.0</td><td></td><td></td></tr>
                            <tr><td>50 - 59 Years</td><td></td><td></td><td>< 3.5</td><td></td><td></td></tr>
                            <tr><td>60 - 69 Years</td><td></td><td></td><td>< 4.5</td><td></td><td></td></tr>
                            <tr><td>70 - 79 Years</td><td></td><td></td><td>< 6.5</td><td></td><td></td></tr>
                        </table>
                        <br/><br/><br/>
                        <table style="font-size: 12px;">
                            <tr><td><u>Comment : </u></td><td></td><td></td><td></td><td></td></tr>
                        </table>
                        <table style="font-size: 12px;">
                            <tr><td>Analytical  sensitivity      :   0.01  ng/ml</td></tr>
                            <tr><td>Fluorescence Enzyme Immunoassay  method specific reference  ranges given.</br>(Test done using   TOSOH AIA - 360 Fully Automated Immunoassay Analyzer)</td></tr>
                        </table>    

                    </div>
                    @endif  

                    @if(isset($TGANF))
                    <div>
                        <table style="font-size: 12px;">
                            <tr><td><u>Comment : </u></td><td></td><td></td><td></td><td></td></tr>
                        </table>
                        <table style="font-size: 12px;">
                            <tr><td>&nbsp;Positive  result of ANA is indicative of an autoimmune disease. The presence of ANA is indicative of lupus erythematosus(present in 80-90% of cases), though they also appear in some other auto -immune diseases such as Rheumatoid arthritis autoimmune hepatitis scleroderma, polymyositis,dermatomyositis and various non-rheumatological conditions associated with tissue damage. Other conditions are Addison disease, Idiopathic thrombocytopenic purpura(ITP), Hashimoto Autoimmune hemolytic anemia, Type 1 diabetes mellitus, Mixed connective tissue disorder. </td></tr>
                        </table>    

                    </div>
                    @endif  

                    @if(isset($TGNAAB))
                    <div>
                        <table style="font-size: 12px;">
                            <tr><td><u>Comment : </u></td><td></td><td></td><td></td><td></td></tr>
                        </table>
                        <table style="font-size: 12px;">
                            <tr><td>* H1N1 sensitivity = 76.8 %   Specificity = 100 %</td></tr>
                            <tr><td>* Test result must be evaluated in conjunction with other clinical data available to the physician.</td></tr>
                            <tr><td>* A negative result may occur if the level of antigen in a sample is below the detection limit of the test or from improper sample collection. Negative test results are not intended to rule out other non- influenza viral infections.</td></tr>
                            <tr><td>* Influenza A (H1N1) virus exhibits a high frequency of mutation. Specimens from patients  infected with a viral mutation can exhibit a negative test result on A(H1N1) line, while still presenting a positive result on the influenza A line.</td></tr>
                        </table>    

                    </div>
                    @endif   

                    @if(isset($TGIO))
                    <div>
                        <table style="font-size: 12px;">
                            <tr><td><u>Comment : </u></td><td></td><td></td><td></td><td></td></tr>
                        </table>
                        <table style="font-size: 12px;">
                            <tr><td>Urine osmolality is a measure of  the concentration of osmotically active particles, principally Sodium, potassium, Chloride, Urea and Glucose can contribute significantly to the osmolality when present in substantial  amounts in  urine. Urine osmolality is a  marker for how well the kidnyes are working. It corresponds to urine specific gravity in on disease states.</td></tr>
                        </table>    

                    </div>
                    @endif  



                    @if(isset($TGBP))
                    <!--                <div>
                                        <table style="font-size: 12px;">
                                            <tr><td><u>Comment : </u></td><td></td><td></td><td></td><td></td></tr>
                                        </table>
                                        <table style="font-size: 12px;">
                                            <tr><td>- Moderate hypochromic microcytic anaemia  Hb 8.6 g/dL.</td></tr>
                                            <tr><td>- Red cell morphology is suggestion of Iron deficiency anaemia +/ - bleeding .</td></tr>  
                                        </table>
                                        <table style="font-size: 12px;">
                                            <tr><td>Suggestive of:</td><td>- Serum Ferritin</td></tr>
                                            <tr><td></td><td>- Retic Count</td></tr> 
                                            <tr><td></td><td>- Exclude occult bleeding & treat underlying cause.</td></tr> 
                                            <tr><td></td><td>- Treat with oral iron, Vit C for 6/12.</td></tr> 
                                            <tr><td></td><td>- Repeat FBC in 3/12 & FBC, Blood picture & serum ferritin on completion of treatment.</td></tr> 
                                        </table> 
                    
                                    </div>-->
                    @endif  




                    <?php // Pasindu Samarakoon ========================================================================================================              ?>



                    @if(isset($CholOK))
                    <br/>
                    Total Cholesterol Normal Range
                    <table width="300" border="1">
                        <tr align="left">
                            <th>Desirable</th>
                            <th>Border Line</th>
                            <th>High</th>
                        <tr>
                        <tr>
                            <td> 140 - 200 </td>
                            <td> 200 - 239</td>
                            <td> > 245 </td>
                        </tr>
                    </table>
                    @endif

                    @if(isset($HCG))
                    Comment :<br/><br/>
                    Human Chorionic Gonadotrophin (HCG) is a glycoprotein hormone secreted by the developing placenta <br/>
                    beginning shortly after fertilization. The early appearance of HCG in urine following conception have <br/>
                    been made the marker of choice in the early detection off pregnancy. 
                    @endif

                    @if(isset($TSH))

                    Adult Reference Range      0.3 – 4.0 mIU/L
                    <br/><br/>
                    Pediatric Reference Range

                    <table width="300">
                        <tr>
                            <td>1  -  11 months</td>
                            <td>0.65 – 8.6</td>
                        </tr>
                        <tr>
                            <td>1  -  5  years</td>
                            <td>0.55 – 7.0</td>
                        </tr>
                        <tr>
                            <td>6  -  10 years</td>
                            <td>0.45 – 6.4</td>
                        </tr>
                        <tr>
                            <td>11 -   15 years</td>
                            <td>0.35 – 5.9</td>
                        </tr>
                    </table>

                    <br/>

                    Comments : 
                    <br/>
                    <br/>
                    <br/>
                    Method : ELISA

                    @endif

                    @if(isset($TGTICANTI))

                    <!--Reference Range : < 0.06 ng/mL-->


                    <!--                    <br/>
                                        <br/>
                    
                    
                                        hS Troponin I detects lower level of Troponin I, than conventional Troponin I assays, hence facilitating early diagnosis of a MI as early as the first hour of onset.<br/>
                                        <br/>
                                        An increase in hS Troponin I above 0.06 (99th percentile URL) indicates myocardial necrosis and therefore risk of arrhythmia or ongoing infarction. However hS Troponin <0.06 does not exclude a clinical diagnosis of acute coronary syndrome , early myocardial infarction (or other etiologies of myocardial damage). Suggest repeat quantitative hS Troponin I after 1 to 3 hours interval to check rising titer in order to firmly confirm or exclude an ongoing myocardial necrosis in highly suspicious cases.<br/>
                                        <br/>
                                        Reference: Third Universal Definition of Myocardial infarction, circulation, 2012; 126:202-2035
                                        @endif
                    
                                        @if(isset($ESR))
                                        Reference Range: <br/><br/>
                                        Male
                                        <table width="300" border="0">			
                                            <tr>
                                                <td width="150"> Age (Years) </td>
                                                <td width="150" align="center"> Upper Limit</td>			   
                                            </tr>
                                            <tr>
                                                <td> 17 - 50 </td>
                                                <td align="center"> 10 </td>			   
                                            </tr>
                                            <tr>
                                                <td> 51 - 60 </td>
                                                <td align="center"> 12 </td>			   
                                            </tr>
                                            <tr>
                                                <td> 61 - 70 </td>
                                                <td align="center"> 10 </td>			   
                                            </tr>
                                            <tr>
                                                <td> > 70 </td>
                                                <td align="center"> 30 </td>			   
                                            </tr>
                                        </table>
                                        <br/>
                                        Female
                                        <table width="300" border="0">			
                                            <tr>
                                                <td> Age (Years) </td>
                                                <td> Upper Limit</td>			   
                                            </tr>
                                            <tr>
                                                <td width="150"> 17 - 50 </td>
                                                <td width="150" align="center"> 12 </td>			   
                                            </tr>
                                            <tr>
                                                <td> 51 - 60 </td>
                                                <td align="center"> 19 </td>			   
                                            </tr>
                                            <tr>
                                                <td> 61 - 70 </td>
                                                <td align="center"> 20 </td>			   
                                            </tr>
                                            <tr>
                                                <td> > 70 </td>
                                                <td align="center"> 35 </td>			   
                                            </tr>
                                        </table>-->
                    @endif

                    @if(isset($FBCOK))
                    <p style="font-weight: bold; font-size: 13pt; margin-left: 0px; font-style: italic;">Manual Differential Count</p>
                    <table width="650">
                        <?php
                        echo $DCtestTr;
                        ?>
                    </table>
                    @endif

                    @if(isset($FBS))
                    <div>
                        Comment: 
                        <br/>
                        <br/>

                        <table style="border-collapse: collapse; color: black;" border="1" >
                            <tr>
                                <td width="150">
                                    In serum Or Plasma
                                </td>
                                <td width="60">
                                    mg/dl
                                </td>
                                <td width="60">
                                    mmol/l
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    new brn,1 day
                                </td>
                                <td>
                                    40-60
                                </td>
                                <td>
                                    2.2-3.3
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    New born>1 day
                                </td>
                                <td>
                                    50-80
                                </td>
                                <td>
                                    2.8-4.4
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Children
                                </td>
                                <td>
                                    60-100
                                </td>
                                <td>
                                    3.3-5.6
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Adult
                                </td>
                                <td>
                                    74-106
                                </td>
                                <td>
                                    4.1-5.9
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    60-90 Years
                                </td>
                                <td>
                                    82-115
                                </td>
                                <td>
                                    4.6-6.4
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    >90 Years
                                </td>
                                <td>
                                    75-121
                                </td>
                                <td>
                                    4.2-6.7
                                </td>
                            </tr>

                        </table>
                        <br/>
                        10% less in pregnancy

                        <br/>
                        <br/>
                        Hyperglycemia higher than 300mg/dl(16.5mmol/l)may induse keto-acidosis and hyperosmolar coma. 
                        <br/>
                        In prolonged hypoglycemia,lower than30/dl(1.7mmol/l),severe irrevercible encephalic damage may occur.
                    </div>
                    @endif

                    @if(isset($Lipid))
                    <br/>
                    <br/>
                    <div>

                        <p><b>Comment:-</b></p>
                        <table style="font-size: 12px;" width="85%">
                            <tr>
                                <td width="180">Analysis</td>
                                <td width="20"></td>
                                <td width="180" align="center">Prognositicaly Favoarable</td>
                                <td width="40"></td>
                                <td width="150" align="center">Risk Indicator</td>
                                <td width="40"></td>
                            </tr>
                            <tr>
                                <td>TOTAL CHOLESTEROL</td>
                                <td></td>
                                <td align="center">Less than 200</td>
                                <td >mg/dl</td>
                                <td align="center">More than 240 </td>
                                <td >mg/dl</td>
                            </tr>
                            <tr>
                                <td>HDL CHOLESTEROL</td>
                                <td>Male</td>
                                <td align="center">More than 55 </td>
                                <td >mg/dl</td>
                                <td align="center">Less than 35 </td>
                                <td >mg/dl</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>Female</td>
                                <td align="center">More than 65 </td>
                                <td >mg/dl</td>
                                <td align="center">Less than 45 </td>
                                <td >mg/dl</td>
                            </tr>
                            <tr>
                                <td>LDL CHOLESTEROL</td>
                                <td></td>
                                <td align="center">Less than 150 </td>
                                <td >mg/dl</td>
                                <td align="center">More than 190 </td>
                                <td >mg/dl</td>
                            </tr>
                            <tr>
                                <td>VLDL</td>
                                <td></td>
                                <td align="center">Less than 10 </td>
                                <td >mg/dl</td>
                                <td align="center">More than 41 </td>
                                <td >mg/dl</td>
                            </tr>
                            <tr>
                                <td>TRIGLYCERIDES</td>
                                <td></td>
                                <td align="center">Less than 160 </td>
                                <td >mg/dl</td>
                                <td align="center">More than 200 </td>
                                <td >mg/dl</td>
                            </tr>
                            <tr>
                                <td>RISK FACTOR</td>
                                <td>Male</td>
                                <td align="center">Less than 3.8</td>
                                <td ></td>
                                <td align="center">More than 5.9</td>
                                <td ></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>Female</td>
                                <td align="center">Less than 3.1</td>
                                <td ></td>
                                <td align="center">More than 4.6</td>
                                <td ></td>
                            </tr>
                        </table>
                        <br/>


                    </div>
                    @endif

                    @if(isset($crp))

                    Method:- Rapid latex agglutination 
                    <br/><br/><br/>
                    <div>
                        Comment:<br/>
                        C - reactive protein (CRP), The Classic Acute Phase of human serum, is synthesized by hepatocyte.<br/>
                        Normally it is present only in trace amount in serum, but it can increase as much as 1,000 fold in response toadinjury or infection.The clinical measurement of CRP in serum the therefore appears to be a voluble screening <br/>
                        test fororganic disease and sensitive index of disease in inflammatory, infection and ischemic condition.<br/>
                    </div>
                    @endif

                    @if(isset($LipidNFBS))
                    <br/>
                    <br/>
                    <div>

                        <p><b>Comment:-</b></p>
                        <table style="font-size: 12px;" width="85%">
                            <tr>
                                <td width="180">Analysis</td>
                                <td width="20"></td>
                                <td width="180" align="center">Prognositicaly Favoarable</td>
                                <td width="40"></td>
                                <td width="150" align="center">Risk Indicator</td>
                                <td width="40"></td>
                            </tr>
                            <tr>
                                <td>TOTAL CHOLESTEROL</td>
                                <td></td>
                                <td align="center">Less than 200</td>
                                <td >mg/dl</td>
                                <td align="center">More than 240 </td>
                                <td >mg/dl</td>
                            </tr>
                            <tr>
                                <td>HDL CHOLESTEROL</td>
                                <td>Male</td>
                                <td align="center">More than 55 </td>
                                <td >mg/dl</td>
                                <td align="center">Less than 35 </td>
                                <td >mg/dl</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>Female</td>
                                <td align="center">More than 65 </td>
                                <td >mg/dl</td>
                                <td align="center">Less than 45 </td>
                                <td >mg/dl</td>
                            </tr>
                            <tr>
                                <td>LDL CHOLESTEROL</td>
                                <td></td>
                                <td align="center">Less than 150 </td>
                                <td >mg/dl</td>
                                <td align="center">More than 190 </td>
                                <td >mg/dl</td>
                            </tr>
                            <tr>
                                <td>VLDL</td>
                                <td></td>
                                <td align="center">Less than 10 </td>
                                <td >mg/dl</td>
                                <td align="center">More than 41 </td>
                                <td >mg/dl</td>
                            </tr>
                            <tr>
                                <td>TRIGLYCERIDES</td>
                                <td></td>
                                <td align="center">Less than 160 </td>
                                <td >mg/dl</td>
                                <td align="center">More than 200 </td>
                                <td >mg/dl</td>
                            </tr>
                            <tr>
                                <td>RISK FACTOR</td>
                                <td>Male</td>
                                <td align="center">Less than 3.8</td>
                                <td ></td>
                                <td align="center">More than 5.9</td>
                                <td ></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>Female</td>
                                <td align="center">Less than 3.1</td>
                                <td ></td>
                                <td align="center">More than 4.6</td>
                                <td ></td>
                            </tr>
                        </table>
                        <br/>


                    </div>
                    @endif

                    @if(isset($PPBS4) | isset($PPBS))
                    Comment:<br/>
                    <table style="border-collapse: collapse;" border="1" width="200">
                        <tr>
                            <td>< 140</td>
                            <td>Normal </td>
                        </tr>
                        <tr>
                            <td>140 - 200</td>
                            <td>Impaired</td>
                        </tr>
                        <tr>
                            <td>> 200</td>
                            <td>High</td>
                        </tr>
                    </table>
                    @endif

                    @if(isset($scret))
                    <br/>
                    <br/>
                    Serum Creatinine Normal Values
                    <br/>
                    <table width="250px" cellpadding="5">
                        <tr>
                            <td><b>Male</b></td>
                            <td><b>Female</b></td>
                        </tr>
                        <tr>
                            <td>0.7-1.2 mg%</td>
                            <td>0.5-1.0 mg%</td>
                        </tr>
                    </table>
                    @endif

                    @if(isset($TGRPG))

                    Comment : < 200 mg/dl

                    <br/>
                    <br/>

                    @endif

                    @if(isset($ppbs))
                    <br/>       
                    2 Hours After Break Fast
                    @endif

                    @if(isset($ASOT))
                    Titer Up To <br/>
                    Interpretations : <br/><br/>
                    <table width="300" style="margin-left: 30px;"> 
                        <tr>
                            <td>Adults</td>
                            <td><200 IU /ml</td>
                        </tr>
                        <tr>
                            <td>Yong Adults</td>
                            <td>166-250 IU/ml</td>
                        </tr>
                        <tr>
                            <td>Children</td>
                            <td><100IU /ml</td>
                        </tr>
                    </table>
                    @endif

                    @if(isset($TROPT))
                    <span>INTERPRITAION:</span><br/>
                    Negative – No detectable cardiac Troponin I<br/>
                    Positive – Level of cardiac Troponin I Equal or higher than 1ng/ml
                    @endif

                    @if(isset($Hb))
                    <span><u>Haemoglobin Normal Range</u></span><br/>
                    Male &nbsp; &nbsp; : 12.0 - 17.5<br/>
                    Female : 11.0 - 15.0
                    @endif

                    @if(isset($UMAL))
                    <u>Reference Range</u>
                    <table width="400">
                        <TR>
                            <td>Normal</td>
                            <td>< 30 mg Alb/g Cre</td>
                        </TR>
                        <TR>
                            <td>Microalbuminurea</td>
                            <td>30 - 300 mg Alb/g Cre</td>
                            <td></td>
                        </TR>
                        <TR>
                            <td>Clinical Microalbuminurea</td>
                            <td>>300 mg Alb/g Cre</td>
                        </TR>
                    </table>
                    @endif

                    @if(isset($HB1C))
                    HbA1C Reference Range</br></br>

                    <table width="60%">
                        <tr>
                            <td></td>
                            <td></td>
                            <td>%(NGSP)</td>
                        </tr>
                        <tr>
                            <td>Diagnosing of Diabetes</td>
                            <td>Normal</td>
                            <td>4.3 - 5.6</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>High Risk</td>
                            <td>5.7 - 6.5</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Diabetes</td>
                            <td>> 6.5</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Treatment of Diabetes</td>
                            <td>Expected Levels</td>
                            <td><7.0</td> 
                        </tr>

                    </table> 
                    <br/>
                    Reference : American Diabetes Association (ADA) 2016 Guidelines<br/>
                    Machine Used : BIORAD-D-10<br/>
                    Methid : NGSP certified method and standardized to DCCT assay.
                    @endif

                    @if(isset($OGTT))
                    <div style="width:40%;">
                        <input type="hidden" id="ogtt">
                        <canvas id="outChart">

                        </canvas>
                    </div>

                    @endif

                    @if(isset($TGGLYC) && $TGGLYC == true)
                    <p>Comment :-</p>
                    <p><span style="text-decoration: underline;">Reference ralges:</span></p>
                    <p>280 - 330  = Good Control</p>
                    <p>330 - 380  = Fair Control</p>
                    <p>380 - 450  = Bad Control</p>
                    <p>> 450       = Requires urgent attention.</p>
                    @endif

                    @if(isset($gly))
                    <p><u>xpected values</u></p>
                    <table width="500">
                        <tr>
                            <td>Glycohemoglobin A1C</td>
                            <td>Glycohemoglobin A1 </td>
                        </tr>
                        <tr>
                            <td><b> Normal Range     4.2 – 6.2 %.</b></td>
                            <td><b>Normal Range     6.0 – 8.0 %.</b></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>Diabetic</td>
                            <td>Diabetic</td>
                        </tr>
                        <tr>
                            <td>
                                <table>
                                    <tr>
                                        <td>Good control</td>
                                        <td>5.5 – 6.8 %</td>
                                    </tr>
                                    <tr>
                                        <td>Fair control</td>
                                        <td>6.8 – 7.6 %</td>
                                    </tr>
                                    <tr>
                                        <td>Poor control</td>
                                        <td>Above 7.6%</td>
                                    </tr>
                                </table>
                            </td>
                            <td>
                                <table>
                                    <tr>
                                        <td>Good control</td>
                                        <td>7.5 – 8.9   %</td>
                                    </tr>
                                    <tr>
                                        <td>Fair control</td>
                                        <td>9.0 – 10.0 %</td>
                                    </tr>
                                    <tr>
                                        <td>Poor control</td>
                                        <td>Above   10.0 %</td>
                                    </tr>
                                </table>
                            </td>

                        </tr>
                    </table>
                    <p>** Estimation of Mean Blood Glucose  =  33.3 (%HbA1c value) - 86</p>

                    @endif

                    @if(isset($LDL))
                    <br/>
                    <br/>
                    <br/>
                    <br/>
                    <br/>
                    <br/>
                    <br/>
                    <br/>
                    <br/>
                    <br/>
                    <br/>
                    <br/>
                    <br/>
                    <br/>
                    <br/>
                    <div>

                        <p><b>CORONARY HEART DISEASE (CHD) RISK INDICATOR LEVEL</b></p>
                        <table>
                            <tr>
                                <td width="180"></td>
                                <td width="150">DESIRABLE LEVELS(LOW RISK) CHD</td>
                                <td width="150">BORDERLINE LEVELS(AVERAGE RISK) CHD</td>
                                <td width="150">HIGH LEVELS(HIGH RISK) CHD</td>
                            </tr>
                            <tr>
                                <td>CHOLESTEROL - LDL</td>
                                <td>< 130 mg/dl</td>
                                <td>130 – 160 mg/dl</td>
                                <td> > 160  mg/dl</td>
                            </tr>
                        </table>

                        <p><b>LIPID LEVELS FOR INDIVIDUALS WITH PREMATURE CHD ( MALE < 55YRS, FEMALE < 65 YRS)</b></p>
                        <table>
                            <tr>
                                <td width="180">CHOLESTEROL - LDL</td>
                                <td width="150">< 110 mg/dl</td>
                                <td width="150">110 – 130 mg/dl</td>
                                <td width="150">>= 130  mg/dl</td>
                            </tr>
                        </table>

                    </div>
                    @endif
<!--                </div>-->

                <center><p style="font-size: 10pt;">** End of Report **</p></center>
                <table width='100%'>
                    <tr>
                        <td>

                            <table style="position: absolute; bottom: 60px; right: 60px; width: 100%;">
                                <tr>

                                    <?php
//if (isset($onlprep)) {
                                    $Resultx = DB::select("select sign_img from labUser where luid = (select luid from labUser where user_uid = '" . $entered_uid . "')");
                                    foreach ($Resultx as $resx) {
                                        if ($resx->sign_img != null && $resx->sign_img != '') {
//                                            echo "OK";

                                            $db = mysqli_connect("appexsl.com", "appexsl2_mlwsus", "mlws@avissawella", "appexsl2_mlws");
                                            $sql = "select sign_img from labUser where luid = (select luid from labUser where user_uid = '" . $entered_uid . "')";
                                            $sth = $db->query($sql);
                                            $result = mysqli_fetch_array($sth);
                                            echo '<img src="data:image/jpeg;base64,' . base64_encode($result['sign_img']) . '" style="position: absolute; bottom: 90px; right: 90px; width:35px;"/>';
//                                            echo $entered_uid;
                                            ?>
                                            >
                                            <?php
                                        }
                                    }
//                                        }
                                    ?>


                                    @if($sign)
<!--                                    <td align='right' vlign='bottom'>
                                        <br/><br/><br/>
                                        <p>...................................................................</p>
                                        <p style="{{ $fontitelic or '' }}">Medical Laboratory Technologist</p>
                                    </td>-->
                                    @endif
                                </tr>
                            </table>

                            <div style="position: absolute; bottom: 100px; left: 50px;">

                                <?php
// echo date("d-M-yy h:i:s a");
                                ?>

<!--<p style="font-size: 10px; font-weight: bold">Dr.B.K.T.P.Dayanath <span style="font-size: 9px;"><br/>MBBS, D.Path, MD (Chem.Path), MAACB, FAACC<br/>Consultant Chemical Pathologist <span></p>-->

                            </div>

                        </td>
                    </tr>
                </table>


            </div>

    </div>

    @stop






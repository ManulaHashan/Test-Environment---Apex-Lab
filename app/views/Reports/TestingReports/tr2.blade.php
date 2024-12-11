<?php
if (!isset($_SESSION)) {
    session_start();
}
?>

@extends('Templates/ReportTemplate')

@section('title')
Laboratory Report
@stop

@section('head')
<script type="text/javascript">

</script>
@stop

@section('heading')
{{ $repHead or '' }}
@stop

@section('content')

<?php
$repResultxxxxx = DB::select("SELECT fname FROM user where uid = (select user_uid from labUser where luid = '" . $_SESSION['luid'] . "')");
foreach ($repResultxxxxx as $userRes) {
    $userfname = $userRes->fname;
}
?>



<?php
$repResult = DB::select("select a.specialnote,c.fname,c.lname,b.age,b.months,b.days,b.initials,c.gender_idgender,refference_idref,a.sampleNo,a.date as regdate,a.Lab_lid from lps a, patient b, user c where a.patient_pid = b.pid and b.user_uid = c.uid and a.lpsid = '" . $lpsid . "'");
foreach ($repResult as $lpsItem) {
    
    $page_rep_head = false;
    
    $PName = ucwords($lpsItem->fname . " " . $lpsItem->lname);
    $age = $lpsItem->age;
    $months = $lpsItem->months;
    $days = $lpsItem->days;

    $sno = $lpsItem->sampleNo;
    $regDate = $lpsItem->regdate;

    $specialNote = $lpsItem->specialnote;

    if ($lpsItem->initials != "" && $lpsItem->initials != null) {
        $initials = $lpsItem->initials . ".";
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
                $initials = "Master.";
            } elseif ($age >= 18) {
                $initials = "Mr.";
                if ($lpsItem->gender_idgender != 1) {
                    $initials = "Ms.";
                }
            }
        }
    }
    //~~~~~~~~~~~~~~~~~~~~~~

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
            $refby = $lpsItemx->name;
        }
    }


    $Resultx = DB::select("select * from reportconfigs where lab_lid = '" . $_SESSION['lid'] . "'");
    foreach ($Resultx as $resx) {
        $agelabel = $resx->agelabel;
        $date = $resx->date;
        $sign = $resx->sign;
        $valueState = $resx->valuestate;
        $viewinitials = $resx->viewinitials;

        $viewSno = $resx->viewsno;
        $viewRegDate = $resx->viewregdate;

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
?>

<br/>

<div style="font-family:Arial, Helvetica, sans-serif; font-size: 11pt;">
    <table width="100%" cellspacing='8'>
        <tr>
            <td width="100px">Patient Name</td>
            <td>: &nbsp; 
                @if($viewinitials)
                {{ $initials }} 
                @endif

                {{ $PName }}

            </td>

            @if($viewRegDate)
            <td width="20%"></td>
            <td align="right">Reg. Date : </td>
            <td width="90px" align="right"> {{ $regDate }} </td>
            @endif

        </tr>
        <tr>
            <td>Age</td>
            <td>: &nbsp; 
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

            @if($viewSno)
            <td width="1%"></td>
            <td width="18%" align="right">Specimen No : </td>
            <td align="right"> {{ $sno }} </td>
            @endif

        </tr>
        <tr>
            <td>Gender</td>
            <td>: &nbsp; {{ $gender }}</td>
        </tr>
    </table>
    <table width="100%" cellspacing='8'>
        <tr>
            <td width="115">Referred By &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :</td>
            <td style="overflow-x: visible" align="left"> {{ $refby }}</td>
        </tr>

    </table>
    <hr/>

    <br/>

    <?php
    $TGCommentText = ""; 
    
    $CategoryArr = array();
    $resultx = DB::select("select name from testingcategory where tcid in (select testingcategory_tcid from Lab_has_test where lab_lid = '" . $_SESSION['lid'] . "' and test_tid in (select test_tid from lps_has_test where lps_lpsid='" . $lpsid . "'))");
    foreach ($resultx as $resx) {
        array_push($CategoryArr, $resx->name);
    }

    $Category = implode(", ", $CategoryArr);
    ?>

    <?php
    if ($Category == 'HAEMATOLOGY' | $Category == 'General') {
        
    } else {
        $specimenArr = array();
        $result0 = DB::select("select name from testinginput where tiid in (select testinginput_tiid from Lab_has_test where lab_lid = '" . $_SESSION['lid'] . "' and test_tid in (select test_tid from lps_has_test where lps_lpsid='" . $lpsid . "'))");
        foreach ($result0 as $res0) {
            array_push($specimenArr, $res0->name);
        }

        $Specimen = implode(", ", $specimenArr);
        ?>
        <p style="font-size: 11pt; font-weight: bold; {{ $fontitelic or '' }}">Specimen :- {{ $Specimen or '' }}</p>
        <?php
    }
    ?>


    <br/>
    <?php
    if ($Category == "General") {
        $Category = "";
    }
    ?>
    <h4 class="repSubHeading" style="font-size: 11pt; {{ $fontitelic or '' }}">{{ $Category or '' }}</h4>  

    <div style="margin-left: 30px; margin-top: 0px;">
        <blockquote>

            <table width="100%" style="font-size: 11pt;">


                <?php
//for enable disable value state for each test
                $tmpValState = true;

                $TG_NUMBER = 1;

                $lastTestGroup = "";

                $ColHeadAlreadyAdded = false;
                
                $valign = "";

                $tData = "";
                
                  
                
                $result0 = DB::select("select d.comment,d.name_col_align, d.result_col_align, d.unit_col_align, d.flag_col_align, d.ref_col_align, d.custom_configs,d.rep_heading,d.name_col, d.value_col, d.unit_col, d.flag_col, d.ref_col, d.name_col_head, d.value_col_head, d.unit_col_head, d.flag_col_head, d.ref_col_head, d.name_col_width, d.value_col_width, d.unit_col_width, d.flag_col_width, d.ref_col_width, d.tgid,d.name from lps_has_test b,Lab_has_test c, Testgroup d where c.Testgroup_tgid = d.tgid and c.test_tid = b.test_tid and b.lps_lpsid='" . $lpsid . "' and d.Lab_lid = '" . $_SESSION['lid'] . "' group by d.tgid");
                foreach ($result0 as $res0) {

                    $custom_configs = $res0->custom_configs;
                    if ($custom_configs == "1") {
                        $custom_configs = true;
                    } else {
                        $custom_configs = false;
                    }

                    $TGCommentText = $res0->comment;

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


                    if ($res0->name == "Macroscopy") {
                        $headingOK = true;
                        $tmpValState = false;
                    }

                    if ($res0->name == "Serum Cholestarol") {
                        $CholOK = true;
                    }

                    if ($res0->name == "ESR") {
                        $ESROK = true;
                    }

                    if ($res0->name == "Full Blood Count (FBC)") {
                        $FBCOK = true;
                    }

                    if ($res0->name == "SC") {
                        $SCtrue = true;
                    }

                    if ($res0->name == "LFT.") {
                        $SCtrue = true;
                    }

                    unset($lipid);
                    if ($res0->name == "Lipid Profile") {
                        $lipid = true;
//                echo "<div style='margin-left:-20px;'><u><b> " . $res0->name . ":-</u> </b> </div>";
                    }

                    if ($res0->name == "FBC") {
                        $val_align = "right";
                    }

                    if ($res0->name == "Urine") {
                        $val_align = "left";
                        $page_rep_head = true;
                    }
                    
                    if ($res0->name == "Oral Glucose Challenge Test") {
                        $page_rep_head = true; 
                    }
                    
                    if ($res0->name == "Oral Glucose Tolerance Test") {
                        $page_rep_head = true; 
                        $valign = "top";
                    } 
                    
                    if ($res0->name == "Erythrocytes Sedimentation Rate") {
//                        $page_rep_head = true; 
                        $valign = "bottom"; 
                    } 

                    
                    
                    ?>          

                    <?php if (!$custom_configs && $rep_heading && !$ColHeadAlreadyAdded && $page_rep_head) { ?>
                    
                    <br/> <b>{{ $res0->name }}</b> 
                    
                    <?php
                        }
                        ?>



                                <!--<table border="1" width="100%" style="font-size: 11pt;">-->

                    <?php 
                    
                    
                    if (!$custom_configs && $rep_heading && !$ColHeadAlreadyAdded) { ?>
                        <thead style="margin-bottom: -30px;">    
                            <tr >
                                <td align="{{ $name_col_align or ''}}" width="{{ $name_col_width or ''}}"><u>{{ $name_col_head or ''}}</u></td> 
                                <td align="{{ $result_col_align or ''}}" width="{{ $value_col_width or ''}}"><u>{{ $value_col_head or ''}}</u></td>
                                <td align="{{ $unit_col_align or ''}}" width="{{ $unit_col_width or ''}}"><u>{{ $unit_col_head or ''}}</u></td>
                                <td align="{{ $flag_col_align or ''}}" width="{{ $flag_col_width or ''}}"><u>{{ $flag_col_head  or ''}}</u></td>
                                <td align="{{ $ref_col_align or ''}}" width="{{ $ref_col_width or ''}}"><u>{{ $ref_col_head or ''}}</u></td>
                            </tr> 
                        <thead> 
                            <?php
                            $ColHeadAlreadyAdded = true;
                        }
                        ?>

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
                        $HCTr = "";
                        $ESRTr = "";
                        $PLTTr = "";
                        $RBCTr = "";
                        $HCT0Tr = "";
                        $HEATr = "";
                        $WBCTr = "";
                        $DCtestTr = "";
                        $LFTTr = "";



                        $UrineCDTr = "";
                        $UrineNoteCDTr = "";

                        $HCGTr = "";

                        $CRPTr = "";

                        $urine = "";

                        $comment = "";

                        $viewAna = false;
                        $anaID = '';
                        $result2 = DB::select("select d.refference_min, d.refference_max,a.tid,a.name,c.reportname,c.measurement,b.value,a.minrate,a.maxrate,c.viewnorvals,c.viewanalyzer,c.analyzers_anid as anid,d.refference_min,d.refference_max from test a,lps_has_test b,Lab_has_test c,labtestingdetails d where d.Lab_lid = c.Lab_lid and a.tid=d.test_tid and c.Testgroup_tgid = '" . $res0->tgid . "' and a.tid=b.test_tid and a.tid=c.test_tid and b.lps_lpsid='" . $lpsid . "' group by a.tid order by c.orderno");
                        foreach ($result2 as $res) {
                            $name = $res->reportname;
                            $tname = $res->name;
                            $value = $res->value;
                            $mes = $res->measurement;

                            if ($res->viewanalyzer) {
                                $viewAna = true;
                                $anaID = $res->anid;
                            }

                            //FBC ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                            if ($name == "Neutrophils" | $name == "Lymphocytes" | $name == "Eosinophils" | $name == "Monocytes" | $name == "Basophils") {

                                $DCtestTr .= "<tr><td>&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp " . $name . "</td><td align='left' style='padding-left: 10px;'>" . $value . "</td><td>" . $mes . "</td><td width='80'></td>";

                                if ($res->viewnorvals) {
                                    $DCtestTr .= "</tr><tr><td width='240'>(Normal Value : " . $res->minrate . " - " . $res->maxrate . " " . $res->measurement . ")</td></tr>";
                                } else {
                                    $DCtestTr .= "</tr><tr><td width='240'></td></tr>";
                                }
                            } else if ($name == "Erythrocytes Sedimentation Rate") {
                                $ESRTr .= "<tr><td>" . $name . "</td><td align='left' style='padding-left: 10px;'></td><td></td><td width='80'></td>";
                                $ESRTr .= "<tr><td align='right'>1st Hour &nbsp </td><td align='left' style='padding-left: 10px;'>" . $value . "</td><td>" . $mes . "</td><td width='80'></td>";

                                if ($res->viewnorvals) {
                                    $ESRTr .= "<tr><td width='240'>(Normal Value : " . $res->minrate . " - " . $res->maxrate . " " . $res->measurement . ")</td></tr>";
                                } else {
                                    $ESRTr .= "<tr><td width='240'></td></tr>";
                                }
                            } else if ($name == "White Blood Cell Count") {
                                $WBCTr .= "<tr><td>" . $name . "</td><td width='50' align='left' style='padding-left: 10px;'>" . number_format($value) . "</td><td>" . $mes . "</td><td width='80'></td>";



                                if ($res->viewnorvals) {
                                    $WBCTr .= "<tr><td width='240'>(Normal Value : " . $res->minrate . " - " . $res->maxrate . " " . $res->measurement . ")</td></tr>";
                                } else {
                                    $WBCTr .= "<tr><td width='240'></td></tr>";
                                }
                            } else if ($name == "Haemoglobin") {
                                $HEATr .= "<tr><td>" . $name . "</td><td width='50' align='left' style='padding-left: 10px;'>" . $value . "</td><td>" . $mes . "</td><td width='80'></td>";



                                if ($res->viewnorvals) {
                                    $HEATr .= "<tr><td width='240'>(Normal Value : " . $res->minrate . " - " . $res->maxrate . " " . $res->measurement . ")</td></tr>";
                                } else {
                                    $HEATr .= "<tr><td width='240'></td></tr>";
                                }
                            } else if ($name == "MCV" | $name == "MCH" | $name == "MCHC" | $name == "RDW-CV" | $name == "RDW-SD") {
                                $HCTr .= "<tr><td> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp " . $name . "</td><td align='left' style='padding-left: 10px;'>" . $value . "</td><td>" . $mes . "</td><td width='80'></td>";

                                if ($res->viewnorvals) {
                                    $HCTr .= "<tr><td width='240'>(Normal Value : " . $res->minrate . " - " . $res->maxrate . " " . $res->measurement . ")</td></tr>";
                                } else {
                                    $HCTr .= "<tr><td width='240'></td></tr>";
                                }
                            } else if ($name == "Platelet Count") {
                                $PLTTr .= "<tr><td>" . $name . "</td><td align='left' style='padding-left: 10px;'>" . number_format($value) . "</td><td>" . $mes . "</td><td width='80'></td>";

                                if ($res->viewnorvals) {
                                    $PLTTr .= "<tr><td width='240'>(Normal Value : " . $res->minrate . " - " . $res->maxrate . " " . $res->measurement . ")</td></tr>";
                                } else {
                                    $PLTTr .= "<tr><td width='240'></td></tr>";
                                }
                            } else if ($name == "RBC") {
                                $RBCTr .= "<tr><td>&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp " . $name . "</td><td align='left' style='padding-left: 10px;'>" . $value . "</td><td>" . $mes . "</td><td width='80'></td>";

                                if ($res->viewnorvals) {
                                    $RBCTr .= "<tr><td width='240'>(Normal Value : " . $res->minrate . " - " . $res->maxrate . " " . $res->measurement . ")</td></tr>";
                                } else {
                                    $RBCTr .= "<tr><td width='240'></td></tr>";
                                }
                            } else if ($name == "HCT") {
                                $HCT0Tr = "<tr><td>&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp " . $name . "</td><td align='left' style='padding-left: 10px;'>" . $value . "</td><td>" . $mes . "</td><td width='80'></td>";



                                if ($res->viewnorvals) {
                                    $HCT0Tr .= "<tr><td width='240'>(Normal Value : " . $res->minrate . " - " . $res->maxrate . " " . $res->measurement . ")</td></tr>";
                                } else {
                                    $HCT0Tr .= "<tr><td width='240'></td></tr>";
                                }
                            }
//
//                        //LFT ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//                        else if ($name == "Serum Bilirubin (Total):-" && $tname != "LFTD SB Total") {
//                            $LFTTr .= "<tr style='line-height: 30px;'><td width='300'><p style='margin-left:-30px; font-size:12pt; font-style: italic;'>(Performed by Evolution 3000 Auto Analyzer)</p></td><td width='100'></td><td align='left' width='60'></td><td></td><td width='220'></td>";
//                            $LFTTr .= "<tr style='text-decoration: underline; line-height: 50px;'><td>Testing</td><td width='100'>Result</td><td align='left' width='60'>Unit</td><td></td><td width='220'>Reference Range</td>";
//                            $LFTTr .= "<tr style='line-height: 60px;'><td>" . $name . "</td><td>" . $value . "</td><td align='left'>" . $mes . "</td><td></td><td>" . $res->minrate . " - " . $res->maxrate . " " . $res->measurement . "</td>";
//                        } else if ($name == "S.G.O.T.(A.S.T.):-") {
//                            $LFTTr .= "<tr style='line-height: 60px;'><td width='300'>" . $name . "</td><td>" . $value . "</td><td align='left'>" . $mes . "</td><td></td><td> < " . $res->maxrate . " " . $res->measurement . "</td>";
//                        } else if ($name == "S.G.P.T.(A.L.T.):-") {
//                            $LFTTr .= "<tr style='line-height: 60px;'><td width='300'>" . $name . "</td><td>" . $value . "</td><td align='left'>" . $mes . "</td><td></td><td> < " . $res->maxrate . " " . $res->measurement . "</td>";
//                        } else if ($name == "Serum Alkaline Phosphatase: -" && $tname != "LFTD ALP") {
//                            $LFTTr .= "<tr style='line-height: 60px;'><td width='300'>" . $name . "</td><td>" . $value . "</td><td align='left'>" . $mes . "</td><td></td><td>" . $res->minrate . " - " . $res->maxrate . " " . $res->measurement . "</td>";
//                        } else if ($name == "Gamma G.T. :-") {
//                            $LFTTr .= "<tr style='line-height: 60px;'><td width='300'>" . $name . "</td><td>" . $value . "</td><td align='left'>" . $mes . "</td><td></td><td>Male: 11-61 , Female: 9-39</td>";
//                        }
//
//                        //LFT direct ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//                        else if ($tname == "LFTD SB Total" | $tname == "Serum Bilirubin (Direct)" | $tname == "LFTD ALP") {
////                        $LFTTr .= "<tr style='line-height: 30px;'><td width='300'><p style='margin-left:-30px; font-size:12pt; font-style: italic;'>(Performed by Evolution 3000 Auto Analyzer)</p></td><td width='100'></td><td align='left' width='60'></td><td></td><td width='220'></td>";
////                        $LFTTr .= "<tr style='text-decoration: underline; line-height: 50px;'><td>Testing</td><td width='100'>Result</td><td align='left' width='60'>Unit</td><td></td><td width='220'>Reference Range</td>";
//                            $LFTTr .= "<tr style='line-height: 60px;'><td>" . $name . "</td><td>" . $value . "</td><td align='left'>" . $mes . "</td><td></td><td>" . $res->refference_min . " - " . $res->refference_max . " " . $res->measurement . "</td>";
//                        } else if ($tname == "LFTD SB Indirect") {
////                        $LFTTr .= "<tr style='line-height: 30px;'><td width='300'><p style='margin-left:-30px; font-size:12pt; font-style: italic;'>(Performed by Evolution 3000 Auto Analyzer)</p></td><td width='100'></td><td align='left' width='60'></td><td></td><td width='220'></td>";
////                        $LFTTr .= "<tr style='text-decoration: underline; line-height: 50px;'><td>Testing</td><td width='100'>Result</td><td align='left' width='60'>Unit</td><td></td><td width='220'>Reference Range</td>";
//                            $LFTTr .= "<tr style='line-height: 60px;'><td>" . $name . "</td><td>" . $value . "</td><td align='left'>" . $mes . "</td><td></td><td></td>";
//                        } else if ($tname == "Colour") {
//                            $urine .= "<tr style='line-height: 25px;'><td>" . $name . "</td><td valign='bottom'>" . $value . "</td><td align='left'>" . $mes . "</td><td></td><td></td>";
//                            echo $urine;
//                            $urine = "";
//                        } else if ($tname == "Transparency ") {
//                            $urine .= "<tr style='line-height: 25px;'><td>" . $name . "</td><td>" . $value . "</td><td align='left'>" . $mes . "</td><td></td><td></td>";
//                            echo $urine;
//                            $urine = "";
//                        } else if ($tname == "pH") {
//                            $urine .= "<tr style='line-height: 25px;'><td>" . $name . "</td><td>" . $value . "</td><td align='left'>" . $mes . "</td><td></td><td></td>";
//                            echo $urine;
//                            $urine = "";
//                        } else if ($tname == "Albumin") {
//                            $urine .= "<tr style='line-height: 25px;'><td>" . $name . "</td><td>" . $value . "</td><td align='left'>" . $mes . "</td><td></td><td></td>";
//                            echo $urine;
//                            $urine = "";
//                        } else if ($tname == "Sugar") {
//                            $urine .= "<tr style='line-height: 25px;'><td>" . $name . "</td><td>" . $value . "</td><td align='left'>" . $mes . "</td><td></td><td></td>";
//                            echo $urine;
//                            $urine = "";
//                        } else if ($tname == "Bile") {
//                            $urine .= "<tr style='line-height: 25px;'><td>" . $name . "</td><td>" . $value . "</td><td align='left'>" . $mes . "</td><td></td><td></td>";
//                            echo $urine;
//                            $urine = "";
//                        } else if ($tname == "Urobilin ") {
//                            $urine .= "<tr style='line-height: 25px;'><td>" . $name . "</td><td>" . $value . "</td><td align='left'>" . $mes . "</td><td></td><td></td>";
//                            echo $urine;
//                            $urine = "";
//                        } else if ($tname == "Pus Cells") {
//
//                            $customUOM = $mes;
//                            if ($value == "Occasionally seen" | $value == "Nil") {
//                                $customUOM = "";
//                            }
//
//                            $urine .= "<tr style='line-height: 25px;'><td width='30%'>&nbsp;&nbsp;&nbsp;</td><td></td><td align='left'></td><td></td><td></td>";
//                            $urine .= "<tr style='line-height: 25px;'><td><u>Centrifuged Deposits :- </u></td><td></td><td align='left'></td><td></td><td></td>";
//                            $urine .= "<tr style='line-height: 25px;'><td>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;" . $name . "</td><td>" . $value . "</td><td align='right'>" . $customUOM . "</td><td width = '40%'></td><td></td>";
//                            echo $urine;
//                            $urine = "";
//                        } else if ($tname == "Red Cells") {
//
//                            $customUOM = $mes;
//                            if ($value == "Occasionally seen" | $value == "Nil") {
//                                $customUOM = "";
//                            }
//
//                            $urine .= "<tr style='line-height: 25px;'><td>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;" . $name . "</td><td>" . $value . "</td><td align='right'>" . $customUOM . "</td><td></td><td></td>";
//                            echo $urine;
//                            $urine = "";
//                        } else if ($tname == "Epithelial cells") {
//                            $urine .= "<tr style='line-height: 25px;'><td>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;" . $name . "</td><td>" . $value . "</td><td align='left'>" . $mes . "</td><td></td><td></td>";
//                            echo $urine;
//                            $urine = "";
//                        } else if ($tname == "Casts") {
//                            $urine .= "<tr style='line-height: 25px;'><td>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;" . $name . "</td><td>" . $value . "</td><td align='left'>" . $mes . "</td><td></td><td></td>";
//                            echo $urine;
//                            $urine = "";
//                        } else if ($tname == "Crystals") {
//                            $urine .= "<tr style='line-height: 25px;'><td>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;" . $name . "</td><td>" . $value . "</td><td align='left'>" . $mes . "</td><td></td><td></td>";
//                            echo $urine;
//                            $urine = "";
//                        } else if ($tname == "Organisms") {
//                            $urine .= "<tr style='line-height: 25px;'><td>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;" . $name . "</td><td>" . $value . "</td><td align='left'>" . $mes . "</td><td></td><td></td>";
//                            echo $urine;
//                            $urine = "";
//                        }
//
//                        // Protein ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//                        else if ($name == "Serum Protein :-") {
////                        $LFTTr .= "<tr style='line-height: 30px;'><td width='300'><p style='margin-left:-30px; font-size:12pt; font-style: italic;'>(Performed by Evolution 3000 Auto Analyzer)</p></td><td width='100'></td><td align='left' width='60'></td><td></td><td width='220'></td>";
//                            $LFTTr .= "<tr style='text-decoration: underline; line-height: 50px;'><td>Testing</td><td width='100'>Result</td><td align='left' width='60'>Unit</td><td></td><td width='220'>Reference Range</td>";
//                            $LFTTr .= "<tr style='line-height: 60px;'><td>" . $name . "</td><td>" . $value . "</td><td align='left'>" . $mes . "</td><td></td><td>" . $res->minrate . " - " . $res->maxrate . " " . $res->measurement . "</td>";
//                        } else if ($name == "Serum  :-" | $name == "Globulin :-" | $name == "A/G Ratio") {
//                            $LFTTr .= "<tr style='line-height: 60px;'><td width='300'>" . $name . "</td><td>" . $value . "</td><td align='left'>" . $mes . "</td><td></td><td>" . $res->minrate . " - " . $res->maxrate . " " . $res->measurement . "</td>";
//                        }
//
//                        //Urine ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                        else if ($name == "Pus cells" | $name == "Red cells" | $name == "Epithelial cells" | $name == "Casts" | $name == "Crystals" | $name == "Organisms") {
                            $UrineCDTr .= "<tr><td style='height:25px;'> <div>&nbsp &nbsp &nbsp &nbsp " . $name . "</div></td><td>" . $value . "</td><td >" . $mes . "</td><td></td>";

//                            if ($res->viewnorvals) {
//                                $UrineCDTr .= "<tr><td width='150'>(Normal Value : " . $res->minrate . " - " . $res->maxrate . " " . $res->measurement . ")</td></tr>";
//                            } else {
//                                $UrineCDTr .= "<tr><td width='150'></td></tr>";
//                            }
                        } 
//                        else if ($name == "Note1" | $name == "Note2") {
//                            $UrineNoteCDTr .= "<tr><td></td><td width='1'></td><td align='left' style='padding-left: 10px;'><div style='margin-left: -350px; font-size:12pt; margin-bottom:-10px;'>" . $value . "</div></td><td></td><td width='80'></td>";
//
//
//
//                            if ($res->viewnorvals) {
//                                $UrineNoteCDTr .= "<tr><td width='240'>(Normal Value : " . $res->minrate . " - " . $res->maxrate . " " . $res->measurement . ")</td></tr>";
//                            } else {
//                                $UrineNoteCDTr .= "<tr><td width='240'></td></tr>";
//                            }
//                        }
//
//                        //HCG ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//                        else if ($name == "Human Chorionic Gonadotropin") {
//                            $HCGTr .= "<tr><td width='250'> <div style='margin:0;'> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp " . $name . "</div></td><td width='75' align='left'>" . $value . "</td><td>" . $mes . "</td><td width='80'></td>";
//
//                            if ($res->viewnorvals) {
//                                $HCGTr .= "<tr><td width='350'>(Normal Value : " . $res->minrate . " - " . $res->maxrate . " " . $res->measurement . ")</td></tr>";
//                            } else {
//                                $HCGTr .= "<tr><td width='350'></td></tr>";
//                            }
//
//                            $HCGTr .= "<tr><td width='350'>&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp</td></tr>";
//                            $HCGTr .= "<tr><td width='350'>&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp</td></tr>";
//                            $HCGTr .= "<tr><td width='350' style='font-size:10pt;'>&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp(Sensitivity of the test :- 25 mu / ml)</td></tr>";
//                        }
//
//                        //CRP ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                            else if ($name == "C - Reactive protein") {
                                $CRPtrue = true;
                            $comment = "<tr><td> <div style='margin:0;'>" . $name . "</div></td><td>" . $value . "</td><td>" . $mes . "</td><td></td>";
                            
                            }
//
//                        

                            //Normal Testing~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                            else {
                                ?>

                                <?php
                                if (!$custom_configs) {

                                    $refMin = "";
                                    $refMax = "";

                                    if ($res->refference_min !== "") {
                                        $refMin = $res->refference_min . " - ";
                                    }

                                    if ($res->refference_max !== "") {
                                        $refMax = $res->refference_max;
                                    }

                                    if (is_numeric($value)) {
                                        if (strlen(substr(strrchr($value, "."), 1)) > 0) {
                                            
                                        } else {
                                            $value = number_format($value);
                                        }
                                    }
//                                
                                    ?>
                                    <tr>
                                    <tr style="font-size: 11pt; height: 30px;">
                                        <td><div>{{ $name }}</div></td>
                                        <td align="{{$val_align or 'right'}}" valign="bottom">{{ $value }}</td>
                                        <td align="right" valign="bottom">{{ $mes }}</td>
                                        <td align="right" valign="bottom"></td>
                                        <td valign="bottom"><?php echo $refMin . $refMax; ?></td>
                                    </tr> 


                                    </tr>
                                <?php } else { ?>

                                    <tr style="font-size: 11pt; height: 30px;">
                                        <td width="35%"><div>{{ $name }}</div></td>
                                        <td width="15%" align="right">{{ $value }}</td>
                                        <td width="10%" align="right">{{ $mes }}</td>
                                        <td width="40%"></td>
                                    </tr>

                                    <?php
                                    if ($res->viewnorvals) {
                                        ?>

                                        <tr><td style="font-size: 11pt;">Normal Value : <?php echo $res->minrate . " - " . $res->maxrate . " " . $res->measurement; ?></td></tr>
                                        <tr><td>&nbsp;</td></tr>
                                        <?php
                                    } else {
                                        ?>
                            <!--                            <tr><td width="100"></td></tr>
                                        <tr><td width="200"></td></tr>-->
                                        <?php
                                    }
                                    ?>   

                                    <!--                        </tr>-->


                                    <?php
                                }
                            }
                            
                            //Serum Creatinine~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                            if ($name == "Serum Creatinine:-") {
//                            $SCtrue = true;
//                            $SCtr = "<tr style='font-size: 12pt; height: 30px;'><td width='35%'><div> " . $name . " </div></td><td width='15%' align='right' style='padding-right: 10px;'>" . $value . "</td><td width='10%' align='right'>" . $mes . "</td><td width='40%'></td>";

                                if ($res->viewnorvals) {
                                    $comment = "<tr><td width='150'>&nbsp;</td></tr>";
                                    $comment .= "<tr border='1'><td width='180' style='font-size:11pt'>Normal Values :-  </br>Male : &nbsp; &nbsp; &nbsp; 0.7 - 1.2 " . $res->measurement . " </td></tr>";
                                    $comment .= "<tr><td width='150' style='font-size:11pt'>Female :&nbsp; 0.5 - 1.0 " . $res->measurement . "</td></tr>";
                                    $comment .= "<tr><td width='150'>&nbsp;</td></tr>";
                                    $comment .= "<tr><td width='150'>&nbsp;</td></tr>";
                                } else {
                                    $comment .= "<tr><td width='150'></td></tr>";
                                }
                            }
                            
                        }

                        echo $comment;




                        //Urine Order ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

                        if ($UrineCDTr != "") {
                            echo "<h3><u>URINE ANALYSIS</u></h3>";
                            echo "<tr><td>&nbsp</td></tr>"
                            . "<tr><td><u>Cenrifuged Deposits </u> </td></tr>" . "<tr><td>&nbsp</td></tr>" . $UrineCDTr . $UrineNoteCDTr;
                        }

                        // HCG Order ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                        if ($HCGTr != "") {
                            echo $HCGTr;
                        }


                        // CRP Order ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                        if ($CRPTr != "") {
                            echo $CRPTr . "<tr><td>&nbsp</td></tr>";
                        }


                        //FBC Order~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                        if ($WBCTr != "") {
                            echo $WBCTr . "<tr><td>&nbsp</td></tr>";
                        }

                        if ($DCtestTr != "") {
                            echo "<tr><td> &nbsp &nbsp &nbsp <u>Differential Count </u> </td></tr>" . $DCtestTr . "<tr><td>&nbsp</td></tr>";
                        }

                        if ($ESRTr != "") {
                            echo $ESRTr . "<tr><td>&nbsp</td></tr>";
                        }

                        if ($HEATr != "") {
                            echo $HEATr;
                        }

                        if ($RBCTr != "") {
                            echo $RBCTr;
                        }

                        if ($HCT0Tr != "") {
                            echo $HCT0Tr;
                        }

                        if ($HCTr != "") {
                            echo $HCTr . "<tr><td>&nbsp</td></tr>";
                        }

                        if ($PLTTr != "") {
                            echo $PLTTr;
                        }

                        if ($LFTTr != "") {
                            echo $LFTTr;
                        }
                        ?>

                        <!--</table>-->

                        @if(isset($lipid))
                        <!--        </blockquote>
                                <div style="margin-top: -10px;">
                                    &nbsp; &nbsp; <u>Expected values:-</u>
                                    <br/><br/>
                        
                                    <table width="92%" style="margin-left: 40px; font-size: 11pt;">
                                        <tr style="height: 10px;">
                                            <td width="35%">Total Cholesterol: -</td>
                                            <td width="15%" align="right">150.0 – 225.0</td>
                                            <td width="10%" align="right">mg %</td>
                                            <td width="40%"></td>
                                        </tr>
                                        <tr style="height: 10px;">
                                            <td>Triglycerides: -</td>
                                            <td align="right">10.0 – 190.0</td>
                                            <td align="right">mg %</td>
                                        </tr>
                                        <tr style="height: 10px;">
                                            <td>HDL- Cholesterol: -</td>
                                            <td align="right">30.0 – 85.0</td>
                                            <td align="right">mg %</td>
                                        </tr>
                                        <tr style="height: 10px;">
                                            <td>LDL Cholesterol:-</td>
                                            <td align="right">75.0 – 159.0</td>
                                            <td align="right">mg %</td>
                                        </tr>
                                        <tr style="height: 10px;">
                                            <td>VLDL-Cholesterol</td>
                                            <td align="right">10.0 – 39.0</td>
                                            <td align="right">mg %</td>
                                        </tr>
                                        <tr style="height: 10px;">
                                            <td>Total cholesterol / HDL</td>
                                            <td align="right">2.0 – 5.6</td>
                                            <td></td>
                                        </tr>
                                    </table> 
                        
                                </div>-->
                        @endif


                        <?php
                        if (isset($CRPtrue)) {
                            ?>

                        <div style="margin-left: 100px; margin-top: 30px; font-size: 11pt; ">
                            <table cellpadding="5">
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><u>Reference Range</u> :-</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;&nbsp;&nbsp; < 0.8 mg / dl</td>
                                    <td>&nbsp;&nbsp;Excludes any inflammatory process</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;&nbsp;&nbsp; 0.8 - 4.0 mg / dl</td>
                                    <td>&nbsp;&nbsp;Slight to moderate inflammatory diseases</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;&nbsp;&nbsp; > 4.0 mg / dl</td>
                                    <td>&nbsp;&nbsp;Extensive inflammatory diseases</td>
                                </tr>
                            </table>
                        </div>
                        <?php
                    }
                    ?>


                    <?php
                    if ($viewAna) {
                        if ($anaID != '' && $anaID != 'null' && $anaID != null) {
                            $resulta = DB::select("select name from analyzers where anid = '" . $anaID . "'");
                            foreach ($resulta as $resa) {
                                $analyzerName = $resa->name;
                            }
                            ?>
                            <p>&nbsp;[ Performed by : {{ $analyzerName or '' }} ]</p>


                            <?php
                        }
                    }
                    ?>

                    <?php
                }

                echo $tData;
                ?>


                


            </table>    
            
            <br/>
            
            @if($viewSpecialNote)
            <div style="font-size: 9pt; font-weight: bold; margin-left: 50px;">{{$specialNote or ''}}</div>
                    @endif

            
                    <!-- Comment From Database -->    
                    <?php
                    if ($TGCommentText != "") {
                        echo $TGCommentText;
                    }
                    ?>
                    <!-- Comment From Database -->  


            @if(isset($CholOK))
            <br/>
            <table width="300" border="1" style="font-size: 11pt;">
                <tr align="left">
                    <th>Desirable</th>
                    <th>Border Line</th>
                    <th>High</th>
                <tr>
                <tr>
                    <td> 150 - 200 </td>
                    <td> 200 - 245</td>
                    <td> > 245 </td>
                </tr>
            </table>
            @endif

            @if(isset($ESROK))
            <br/>
            Normal Values
            <table width="300" border="1" style="font-size: 11pt;">			
                <tr>
                    <td> Male </td>
                    <td> 2 - 15 mm</td>			   
                </tr>
                <tr>
                    <td> Female </td>
                    <td> 2 - 20 mm</td>			   
                </tr>
            </table>
            @endif



            @if(isset($FBCOK))
            <p style="font-weight: bold; font-size: 11pt; margin-left: 0px; font-style: italic;">Manual Differential Count</p>
            <table width="700">
                <?php
                echo $DCtestTr;
                ?>
            </table>
            @endif


        </blockquote>
    </div>

    <table width='100%'>
        <tr>
            <td>
                <table style="position: absolute; bottom: 120px; width: 100%; font-size: 11pt;">
                    <tr>
                        @if($date)
                        <td width='50%'>
                            <!--<p>Date : <?php // echo date('Y-m-d');                  ?></p>-->
                            <p style="font-size: 16px; font-style: italic;">Date : {{ $regDate }}</p>
                            <!--<p>Issued By : {{ $userfname or '' }}</p>-->

                        </td> 
                        @endif

                        @if($sign)
                        <td align='center' vlign='bottom'>

                            <p>..................................................................................</p>
                            <p style="{{ $fontitelic or '' }}"></p>
                        </td>
                        @endif
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table width="100%" style="position: absolute; bottom: 0px; right: 0px;">
        <tr>
            <td align="right" style="font-size: 8pt;">


            </td>
        </tr>
    </table>

</div>

@stop






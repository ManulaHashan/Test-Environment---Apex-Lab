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
Urine Full Report
@stop

@section('content')

<?php
$repResultxxxxx = DB::select("SELECT fname FROM user where uid = (select user_uid from labUser where luid = '" . $_SESSION['luid'] . "')");
foreach ($repResultxxxxx as $userRes) {
    $userfname = $userRes->fname;
}
?>

<?php
$repResult = DB::select("select c.fname,c.lname,b.age,b.months,b.days,c.gender_idgender,refference_idref,a.sampleNo,a.Lab_lid from lps a, patient b, user c where a.patient_pid = b.pid and b.user_uid = c.uid and a.lpsid = '" . $lpsid . "'");
foreach ($repResult as $lpsItem) {
    $PName = $lpsItem->fname . " " . $lpsItem->lname;
    $age = $lpsItem->age;
    $months = $lpsItem->months;
    $days = $lpsItem->days;

    if ($lpsItem->Lab_lid == '4') {
        $sno = $lpsItem->sampleNo;
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
            $refby = $lpsItemx->name;
        }
    }

    $Resultx = DB::select("select * from reportconfigs where lab_lid = '" . $_SESSION['lid'] . "'");
    foreach ($Resultx as $resx) {
        $agelabel = $resx->agelabel;
        $date = $resx->date;
        $sign = $resx->sign;
        
        $viewSno = $resx->viewsno;
        $viewRegDate = $resx->viewregdate;

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
<div style="font-family:Arial, Helvetica, sans-serif; ">
    <table cellspacing='8'>
        <tr>
            <td>Patient Name</td>
            <td>: &nbsp; {{ $PName }} ( {{ $sno or '' }} )</td>
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
        </tr>
        <tr>
            <td>Gender</td>
            <td>: &nbsp; {{ $gender }}</td>
        </tr><tr>
            <td>Referred By</td>
            <td>: &nbsp; {{ $refby }}</td>
        </tr>

    </table>
    <hr/>

    <?php
    $specimenArr = array();
    $result0 = DB::select("select name from testinginput where tiid in (select testinginput_tiid from Lab_has_test where test_tid in (select test_tid from lps_has_test where lps_lpsid='" . $lpsid . "'))");
    foreach ($result0 as $res0) {
        array_push($specimenArr, $res0->name);
    }

    $Specimen = implode(", ", $specimenArr);
    ?>

    <p style="font-size: 11pt; font-weight: bold; {{ $fontitelic or '' }}">Specimen :- {{ $Specimen or '' }}</p>    
    <br/>
    <!--<h4 class="repSubHeading" style="font-size: 11pt; {{ $fontitelic or '' }}">BIOCHEMISTRY</h4>-->  

    <blockquote>

        <?php
        $tData = "";
        $result0 = DB::select("select d.tgid,d.name from lps_has_test b,Lab_has_test c, Testgroup d where c.Testgroup_tgid = d.tgid and c.test_tid = b.test_tid and b.lps_lpsid='" . $lpsid . "' and d.Lab_lid = '" . $_SESSION['lid'] . "' group by d.tgid");
        foreach ($result0 as $res0) {

            if ($res0->name == "Macroscopy") {
                $headingOK = true;
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
            ?>          

            <b>{{ $res0->name }}</b>

            <table width="700" style="font-size: 11pt;">
                <?php
                $DCtestTr = "";
                $viewAna = false;
                $anaID = '';
                $result2 = DB::select("select a.tid,c.reportname,c.measurement,b.value,a.minrate,a.maxrate,c.viewnorvals,c.viewanalyzer,c.analyzers_anid as anid from test a,lps_has_test b,Lab_has_test c where c.Testgroup_tgid = '" . $res0->tgid . "' and a.tid=b.test_tid and a.tid=c.test_tid and b.lps_lpsid='" . $lpsid . "' group by a.tid");
                foreach ($result2 as $res) {
                    $name = $res->reportname;
                    $value = $res->value;
                    $mes = $res->measurement;

                    if ($res->viewanalyzer) {
                        $viewAna = true;
                        $anaID = $res->anid;
                    }

                    if ($name == "Neutrophils" | $name == "Lymphocytes" | $name == "Eosinophils" | $name == "Monocytes" | $name == "Basophils") {

                        if ($value < $res->minrate) {
                            $DCtestTr .= "<tr><td><b>" . $name . "</b></td><td width='1'>:</td><td style='padding-left: 10px;'><b>" . $value . "</b></td><td>" . $mes . "</td><td width='80'>[Low]</td>";
                        } elseif ($value > $res->maxrate) {
                            $DCtestTr .= "<tr><td><b>" . $name . "</b></td><td width='1'>:</td><td style='padding-left: 10px;'><b>" . $value . "</b></td><td>" . $mes . "</td><td width='80'>[High]</td>";
                        } else {
                            $DCtestTr .= "<tr><td>" . $name . "</td><td width='1'>:</td><td style='padding-left: 10px;'>" . $value . "</td><td>" . $mes . "</td><td width='80'>[Normal]</td>";
                        }


                        if ($res->viewnorvals) {
                            $DCtestTr .= "<td width='240'>(Normal Value : " . $res->minrate . " - " . $res->maxrate . " " . $res->measurement . ")</td>";
                        } else {
                            $DCtestTr .= "<td width='240'></td>";
                        }
                    } else {
                        ?>

                        <tr>

                            <?php
                            if ($value < $res->minrate) {
                                ?>  

                                <td width="240"><b>{{ $name }}</b></td><td width="1">:</td><td width="80" style="padding-left: 10px;"><b>{{ $value }}</b></td><td width="60">{{ $mes }}</td><td width="80">[Low]</td>

                                <?php
                            } elseif ($value > $res->maxrate) {
                                ?>

                                <td width="240"><b>{{ $name }}</b></td><td width="1">:</td><td width="80" style="padding-left: 10px;"><b>{{ $value }}</b></td><td width="60">{{ $mes }}</td><td width="80">[High]</td>

                                <?php
                            } else {
                                ?>
                                <td width="240">{{ $name }}</td><td width="1">:</td><td width="80" style="padding-left: 10px;">{{ $value }}</td><td width="60">{{ $mes }}</td><td width="80">[Normal]</td>
                                <?php
                            }
                            ?>

                            <?php
                            if ($res->viewnorvals) {
                                ?>
                                <td width="200">(Normal Value : <?php echo $res->minrate . " - " . $res->maxrate . " " . $res->measurement; ?>)</td>
                                <?php
                            } else {
                                ?>
                                <td width="200"></td>
                                <?php
                            }
                            ?>   

                        </tr>


                        <?php
                    }
                }
                ?>

            </table>


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

            <br/>

            <?php
        }

        echo $tData;
        ?>

        <br/>

        @if(isset($CholOK))
        <br/>
        <table width="300" border="1">
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
        <table width="300" border="1">			
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
        <p style="font-weight: bold; font-size: 13pt; margin-left: 0px; font-style: italic;">Manual Differential Count</p>
        <table width="700">
            <?php
            echo $DCtestTr;
            ?>
        </table>
        @endif


    </blockquote>


    <table width='100%'>
        <tr>
            <td>
                <table style="position: absolute; bottom: 60px; width: 100%;">
                    <tr>
                        @if($date)
                        <td width='50%'>
                            <p>Date : <?php echo date('Y-m-d'); ?></p>
                            <!--<p>Issued By : {{ $userfname or '' }}</p>-->

                        </td> 
                        @endif

                        @if($sign)
                        <td align='center' vlign='bottom'>
                            <br/><br/><br/>
                            <p>...................................................................</p>
                            <p style="{{ $fontitelic or '' }}">Medical Laboratory Technologist</p>
                        </td>
                        @endif
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</div>

@stop






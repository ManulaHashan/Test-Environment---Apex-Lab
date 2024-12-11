<?php
date_default_timezone_set("Asia/Colombo");
if (!isset($_SESSION)) {
    session_start();
}
?>

@extends('Templates/WorksheetTemplate')

@section('title')
Laboratory Worksheet
@stop

@section('head')
<script type="text/javascript"> 

</script>
@stop

@section('content')

<style type="text/css">

    @media print {
        .pagebreak { page-break-after: left; } /* page-break-after works, as well */ 


        @page {
            size: a5 portrait;
            margin: 0;
        }
    }  

</style>

<?php
$repResultxxxxx = DB::select("SELECT fname FROM user where uid = (select user_uid from labUser where luid = '" . $_SESSION['luid'] . "')");
foreach ($repResultxxxxx as $userRes) {
    $userfname = $userRes->fname;
}
?>

<?php
//echo "select c.fname,c.lname,b.age,b.months,b.days,c.gender_idgender,refference_idref,a.sampleNo,a.Lab_lid,a.lpsid,a.date,a.arivaltime as time from lps a, patient b, user c where a.patient_pid = b.pid and b.user_uid = c.uid and a.sampleno = '" . $sno . "' and a.date = '" . $date . "' and a.lab_lid = '" . $_SESSION['lid'] . "'";
$PName = "";
$repResult = DB::select("select b.initials,c.fname,c.lname,b.age,b.months,b.days,c.gender_idgender,refference_idref,a.sampleNo,a.Lab_lid,a.lpsid,a.date,a.arivaltime as time from lps a, patient b, user c where a.patient_pid = b.pid and b.user_uid = c.uid and a.sampleno = '" . $sno . "' and a.date = '" . $date . "' and a.lab_lid = '" . $_SESSION['lid'] . "'");
foreach ($repResult as $lpsItem) {
    $PName = $lpsItem->initials . " " . $lpsItem->fname . " " . $lpsItem->lname;
    $age = $lpsItem->age;
    $months = $lpsItem->months;
    $days = $lpsItem->days;
    $lpsid = $lpsItem->lpsid;
    $enter_date = $lpsItem->date;
    $enter_time = $lpsItem->time;
    $Cashier = "";

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
//        $date = $resx->date;
        $sign = $resx->sign;

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

<?php
$crnt_dept = 0;

$ci = 0;

$resultc = DB::select("select tcid,name from testingcategory where tcid in (select c.testingcategory_tcid from lps_has_test b,Lab_has_test c, Testgroup d where c.Testgroup_tgid = d.tgid and c.test_tid = b.test_tid and b.lps_lpsid in (select lpsid from lps where sampleno like '" . $sno . "%' and date = '" . $date . "' and Lab_lid = '" . $_SESSION['lid'] . "') and d.Lab_lid = '" . $_SESSION['lid'] . "'group by c.testingcategory_tcid)");
$len = count($resultc);
foreach ($resultc as $resc) {
    $ci++;
    
    
    ?>

</br> 
 
    Department - <?php echo $resc->name; ?>

    <br/>

    

        <?php
        if ($crnt_dept !== $resc->tcid) {
            ?>
    <h3 style="margin: 0px;">Patient Worksheet <b> [ <?php echo $sno; ?> ] &nbsp;&nbsp; </b> </h3>  
            <?php
        } else {
            ?>
            <h3 style="margin: 0px;">Patient Worksheet <b> [ <?php echo $sno; ?> ] </b> </h3>  
        <?php } ?>

            <br/>

    

    <div style="font-family:Arial, Helvetica, sans-serif; "> 

        Patient : {{ $PName }} &nbsp;&nbsp; | &nbsp;&nbsp; Age :  

        @if($age != 0)
        {{ $age }} Years &nbsp;
        @endif

        @if($months != 0)
        {{ $months }} {{ $monthsLBL or '' }} &nbsp;
        @endif

        @if($days != 0)
        {{ $days }} {{ $daysLBL or '' }} &nbsp;
        @endif
        
        &nbsp;&nbsp;

        |
        
        &nbsp;&nbsp;

        {{ $gender }}

        <br/>

        Date : {{ $enter_date }} &nbsp;&nbsp; | &nbsp;&nbsp; Time : {{ $enter_time }} &nbsp;&nbsp; | &nbsp;&nbsp; Cashier : {{ $Cashier }}

        <br/>

        
        
        Referred By : {{ $refby }} &nbsp;&nbsp; | &nbsp;&nbsp; Center : 

        <hr/>

        <?php
        $crnt_dept = $resc->tcid;

        $tData = "";
//        echo "select d.tgid,d.name,b.lps_lpsid from lps_has_test b,Lab_has_test c, Testgroup d where c.Testgroup_tgid = d.tgid and c.test_tid = b.test_tid and b.lps_lpsid in (select lpsid from lps where sampleno like '" . $sno . "%' and date = '" . $date . "' and Lab_lid = '" . $_SESSION['lid'] . "') and d.Lab_lid = '" . $_SESSION['lid'] . "' group by d.tgid";
        $result0 = DB::select("select d.tgid,d.name,b.lps_lpsid from lps_has_test b,Lab_has_test c, Testgroup d where c.Testgroup_tgid = d.tgid and c.test_tid = b.test_tid and b.lps_lpsid in (select lpsid from lps where sampleno like '" . $sno . "%' and date = '" . $date . "' and Lab_lid = '" . $_SESSION['lid'] . "') and d.Lab_lid = '" . $_SESSION['lid'] . "' and c.testingcategory_tcid = '" . $resc->tcid . "' group by d.tgid");
        foreach ($result0 as $res0) {
            
            $tgname = count(explode("-", $res0->name)) > 1 ? explode("-", $res0->name)[0] : $res0->name;
            
            ?>          


            <b>{{ $tgname }}</b>

            <table style="font-size: 10pt;">

        <?php
        $DCtestTr = "";
        $viewAna = false;
        $anaID = '';

        $result2 = DB::select("select a.tid,a.name as testname,c.reportname,c.measurement,b.value,a.minrate,a.maxrate,c.viewnorvals,c.viewanalyzer,c.analyzers_anid as anid, d.refference_min, d.refference_max from test a,lps_has_test b,Lab_has_test c,labtestingdetails d where d.Lab_lid = c.Lab_lid and a.tid=d.test_tid and c.Testgroup_tgid = '" . $res0->tgid . "' and a.tid=b.test_tid and a.tid=c.test_tid and b.lps_lpsid='" . $res0->lps_lpsid . "' group by a.tid order by c.orderno ASC , c.lhtid ASC");

        foreach ($result2 as $res) {
            $name = $res->reportname;
            $tname = $res->testname;

            $value = $res->value;
            $mes = $res->measurement;

            if ($tname == "Neutrophils_absolute" | $tname == "Lymphocytes_absolute" | $tname == "Monocytes_absolute" | $tname == "Eosinophils_absolute" | $tname == "Basophils_absolute") {
                continue;
            }

            if ($res->viewanalyzer) {
                $viewAna = true;
                $anaID = $res->anid;
            }
            ?>



                    <tr >

            <?php ?>
                        <td style="min-width: 200px;">{{ $name }}</td><td width="1">:</td><td width="80" style="padding-left: 10px; border-bottom:1pt solid black;">{{ $value }}</td><td width="1"></td><td width="1"></td>
                        <?php ?>

                        <?php
                        if ($res->viewnorvals) {
                            ?>
                            <td width="200">(<?php echo $res->refference_min . " - " . $res->refference_max; ?>)</td>
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
        ?>
            </table>
                <?php ?>


        <?php ?>
            <br/>

        <?php
    }


    if ($ci == $len) {
        
    } else {
        ?>
            <div class="pagebreak"> </div>
            <?php
        }
    }
    ?>
</div>

@stop






<?php
if (!isset($_SESSION)) {
    session_start();
}
?>
@extends('Templates/ReportTemplate')

@section('title')
Invoice Receipt Report
@stop

@section('head')
<script type="text/javascript">
    window.onload = print();
</script>
@stop

@section('heading')
Due Payment Report
@stop

@section('content')
<?php
//echo "<pre>".print_r($_REQUEST)."</pre>";

$date = Input::get('option');
$date2 = Input::get('optiony2');
$year = Input::get('option2');
$RID = Input::get('RID');
$ref = Input::get('ref');

if ($ref == "0" or $ref == "null") {
    $refx = "";
} else {
    $refx = "and b.refference_idref = '" . $ref . "'";
}

$brcode = Input::get("brcode");

if ($brcode == "0" || $brcode == "All" || $brcode == "") {
    $brcode = "and b.sampleno like '%'";
} else {
    $brcode = "and b.sampleno like '" . Input::get("brcode") . "%'";
}

if (Input::get("brcode") == "None") {
    $brcode = "and b.sampleno REGEXP '^[0-9]'";
}



$rtype = "Due Payment Report";
$detail = "on " . $date;

$res = DB::select("select u.fname,u.lname,a.iid as iid ,a.date as invdate,a.total as totalamount,a.gtotal as gtot,a.paymentmethod as method,a.cashier as cash,b.sampleNo as smple,a.paid as paid,b.arivaltime,b.repcollected  as rcoll,a.paid as paidamount, a.gtotal-a.paid as due from invoice a, lps b,patient p,user u where b.patient_pid=p.pid and u.uid=p.user_uid and a.lps_lpsid=b.lpsid and a.date between '" . $date . "' and '" . $date2 . "' and b.Lab_lid='" . $_SESSION['lid'] . "' " . $refx . " " . $brcode . "");
//    $res1 = DB::select("select count(a.iid) as invoicecount,sum(a.gtotal) as toatalsum, sum(i.amount) as totalpaid, (sum(a.gtotal)-sum(i.amount)) as totaldue  from invoice a, lps b,invoice_payments i where a.iid=i.invoice_iid and a.lps_lpsid=b.lpsid and a.date between '" . $date . "' and '" . $date2 . "' and b.Lab_lid='" . $_SESSION['lid'] . "' ".$refx." ".$brcode."");
?>
<?php

function month($number) {
    $month = "";
    if ($number == "1") {
        $month = "January";
    } elseif ($number == "2") {
        $month = "February";
    } elseif ($number == "3") {
        $month = "March";
    } elseif ($number == "4") {
        $month = "April";
    } elseif ($number == "5") {
        $month = "May";
    } elseif ($number == "6") {
        $month = "June";
    } elseif ($number == "7") {
        $month = "July";
    } elseif ($number == "8") {
        $month = "August";
    } elseif ($number == "9") {
        $month = "September";
    } elseif ($number == "10") {
        $month = "Octcber";
    } elseif ($number == "11") {
        $month = "November";
    } elseif ($number == "12") {
        $month = "December";
    }
    return $month;
}
?>

<div style="text-align: center; font-size: 24px; font-family: Arial, Helvetica, sans-serif; font-style: normal; padding-bottom: 20px;"><?php echo $rtype ?> Report-<?php echo $detail ?> </div>
<table style="font-family: Arial, Helvetica, sans-serif; font-style: normal; font-size: 11pt;" >
    <thead>
        <tr>
            <th>Invoice No</th>
            <th>Patient Name</th> 
            <th width="80">Date</th>
            <th>Sample Number</th>
            <!--<th>Report Collected</th>-->
            <th>Bill Amount</th>
            <th>Paid Amount</th>
            <th>Due Amount</th>
        </tr>    
    </thead>
    <tbody>
<?php
$totbillam = 0;
$totpaid = 0;
$totdue = 0;
$totc = 0;

foreach ($res as $cols) {

    if ($cols->gtot - $cols->paidamount <= 0) {
        continue;
    }

    $totc++;
    $totbillam += $cols->gtot;
    $totpaid += $cols->paidamount;
    $totdue += $cols->due;
    ?>
            <tr>
                <td style="text-align: center;"><?php echo $cols->iid; ?></td>
                <td style="text-align: left;"><?php echo $cols->fname . " " . $cols->lname; ?></td>
                <td style="text-align: center;"><?php echo $cols->invdate; ?></td>
                <td style="text-align: center;"><?php echo $cols->smple; ?></td>
                <!--<td style="text-align: center;"><?php echo $cols->rcoll; ?></td>-->
                <td style="text-align: right;"><?php echo $cols->gtot; ?></td>
                <td style="text-align: right;"><?php echo $cols->paidamount; ?></td>
                <td style="text-align: right;"><?php echo $cols->due; ?></td>
            </tr>

    <?php
}
?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="8"><hr></th>
        </tr>
        <tr>
            <th>Total Count: <?php echo $totc; ?></th>
            <th></th>
            <th></th>
            <th></th>
            <th>Total in Rs.</th>
            <th style="text-align: right;"><?php echo number_format($totbillam, 2) ?></th>
            <th style="text-align: right;"><?php echo number_format($totpaid, 2) ?></th>
            <th style="text-align: right;"><?php echo number_format($totdue, 2) ?></th>
        </tr>
        <tr>
            <th colspan="9"><hr></th>
        </tr>
    </tfoot>
</table>
<hr/>

@stop

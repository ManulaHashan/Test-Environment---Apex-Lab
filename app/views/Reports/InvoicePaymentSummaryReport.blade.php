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
Invoice Payment Summary Report
@stop

@section('content')
<?php
$date = Input::get('option');
$year = Input::get('option2');
$RID = Input::get('RID');
$ref = Input::get('ref');

if($ref == "0" or $ref == "null"){
    $refx = "";
}else{
    $refx = "and b.refference_idref = '" . $ref . "'";
}

$brcode = Input::get("brcode");

if ($brcode == "0" || $brcode == "All" || $brcode == "") {
    $brcode = "and b.sampleno like '%'";
}else{
    $brcode = "and b.sampleno like '" . Input::get("brcode") . "%'";
}

if (Input::get("brcode") == "None") {
    $brcode = "and b.sampleno REGEXP '^[0-9]'";
}



    $rtype = "Daily Invoice Summary";
    $detail = "on " . $date;

    $res = DB::select("select u.fname,u.lname,a.iid as iid ,a.date as invdate,a.total as totalamount,a.gtotal as gtot,a.paymentmethod as method,a.cashier as cash,b.sampleNo as smple,a.paid as paid,b.arivaltime,b.repcollected  as rcoll,i.amount as paidamount, a.gtotal-(select sum(amount) from invoice_payments where invoice_iid = a.iid ) as due from invoice a, lps b,patient p,user u,invoice_payments i where a.iid=i.invoice_iid and b.patient_pid=p.pid and u.uid=p.user_uid and a.lps_lpsid=b.lpsid and i.date='" . $date . "' and b.Lab_lid='" . $_SESSION['lid'] . "' ".$refx." ".$brcode."");
    $res1 = DB::select("select count(a.iid) as invoicecount,sum(a.gtotal) as toatalsum, sum(i.amount) as totalpaid, (sum(a.gtotal)-sum(i.amount)) as totaldue  from invoice a, lps b,invoice_payments i where a.iid=i.invoice_iid and a.lps_lpsid=b.lpsid and i.date='" . $date . "' and b.Lab_lid='" . $_SESSION['lid'] . "' ".$refx." ".$brcode."");
    
 
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

<div style="text-align: center; font-size: 24px; padding-bottom: 20px;"><?php echo $rtype ?> Report-<?php echo $detail ?> </div>
<table style="font-family: Arial, Helvetica, sans-serif; font-style: normal; font-size: 11pt;">
    <thead>
    <tr>
        <th>Invoice No</th>
        <th>Patient Name</th>
        <th>Invoice Date</th>
        <th>Sample Number</th>
        <th>Report Collected</th>
        <th>Amount</th>
        <th>Receipt Amount</th>
        <th>Due Amount</th>
    </tr>    
    </thead>
    <tbody>
        <?php
        $totaldue_= 0 ;
    foreach ($res as $cols) {
        $totaldue_+=($cols->gtot-$cols->paidamount)>0?($cols->gtot-$cols->paidamount):0;
        ?>
        <tr>
            <td style="text-align: center;"><?php echo $cols->iid; ?></td>
            <td style="text-align: left;"><?php echo $cols->fname." ".$cols->lname; ?></td>
            <td style="text-align: center;"><?php echo $cols->invdate; ?></td>
            <td style="text-align: center;"><?php echo $cols->smple; ?></td>
            <td style="text-align: center;"><?php echo $cols->rcoll; ?></td>
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
        <?php
        foreach ($res1 as $cols1) {?>
        <tr>
        <th>Total Count: <?php echo $cols1->invoicecount ?></th>
        <th></th>
        <th></th>
        <th></th>
        <th>Total in Rs.</th>
        <th style="text-align: right;"><?php echo number_format( $cols1->toatalsum,2) ?></th>
        <th style="text-align: right;"><?php echo number_format( $cols1->totalpaid,2) ?></th>
        <th style="text-align: right;"><?php echo number_format( $totaldue_>0?$totaldue_:0,2) ?></th>
        </tr>
        <tr>
        <th colspan="9"><hr></th>
        </tr>
        <?php }?>
    </tfoot>
</table>
<hr/>

@stop

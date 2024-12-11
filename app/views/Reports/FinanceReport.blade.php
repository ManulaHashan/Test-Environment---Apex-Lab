<?php
if (!isset($_SESSION)) {
    session_start();
}
?>
@extends('Templates/ReportTemplate')

@section('title')
Finance Report
@stop

@section('head')
<script type="text/javascript">
    window.onload = print();
</script>
@stop

@section('heading')
Finance Report
@stop

@section('content')
<?php

//echo "Print ".Input::get('option2');;

$date = Input::get('option');
$date2 = Input::get('option2');
$year = Input::get('option2');
$RID = Input::get('RID');
$ref = Input::get('ref');
$br = Input::get('brcode');
$payMethod = Input::get('paymentMethod');
$payStatus = Input::get('paymentStatus');

$paymentStatus = "";
$query1 = "";
$query2 = "";

if($payMethod == "0"){
    $payMethod = "All";
}

if($payMethod != "0" && $payMethod != "All"){
    $query2 = " and c.paymethod = (select idpaymethod from paymethod where name = '".$payMethod."') ";
}

if($payStatus == "0" || $payStatus == "All"){
    $paymentStatus = "All";
}else if($payStatus == "fullPaid"){
    $paymentStatus = "Full Paid";
    $query1 = " and a.paid >= a.gtotal";
}else if($payStatus == "halfPaid"){
    $paymentStatus = "Half Paid";
    $query1 = " and a.paid < a.gtotal and a.paid > 0";
}else if($payStatus == "notPaid"){
    $paymentStatus = "Not Paid";
    $query1 = " and a.paid = 0";
    $payMethod = "N/A";
    $query2 = "";
}





$refname = "";

if ($ref == "0" or $ref == "null") {
    $refx = "";
} else {
//    $refx = "and b.refference_idref = '" . $ref . "'";    
    
    $result1xref = DB::select("select name from refference where idref = '" . $ref . "' and lid = '" . $_SESSION['lid'] . "' ");
    foreach ($result1xref as $cols1xref){
        $refname = "Reference : ".$cols1xref->name;
        $refName = $cols1xref->name; 
    }
    
    $refx = "and b.refference_idref in (SELECT idref FROM refference r where lid = '".$_SESSION['lid']."' and name like '%".$refName."%')";

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

if ($RID == '1') {
    
//    echo "select a.Discount_did,u.fname,u.lname,p.dob,u.tpno,a.iid as iid ,a.date as invdate,a.total as totalamount,a.gtotal as gtot,a.paymentmethod as method,a.cashier as cash,b.sampleNo as smple,a.paid as paid,b.arivaltime from invoice a, lps b,patient p,user u where b.patient_pid=p.pid and u.uid=p.user_uid and a.lps_lpsid=b.lpsid and a.date between '" . $date . "' and '" . $date2 . "' and b.Lab_lid='" . $_SESSION['lid'] . "' " . $refx . " " . $brcode;
    
    $rtype = "Daily Income";
    $detail = " From " . $date . " To ". $date2;
    $res = DB::select("select a.Discount_did,u.fname,u.lname,p.dob,u.tpno,a.iid as iid ,a.date as invdate,a.total as totalamount,a.gtotal as gtot,a.paymentmethod as method,a.cashier as cash,b.sampleNo as smple,a.paid as paid,b.arivaltime from lps b,patient p,user u, invoice a left join invoice_payments c on a.iid = c.invoice_iid where b.patient_pid=p.pid and u.uid=p.user_uid and a.lps_lpsid=b.lpsid and a.date between '" . $date . "' and '" . $date2 . "' and b.Lab_lid='" . $_SESSION['lid'] . "' " . $refx . " " . $brcode . " ".$query1." ".$query2."");

    $res1 = DB::select("select count(a.iid) as invoicecount,sum(a.gtotal) as toatalsum, sum(a.paid) as totalpaid, sum(a.total) as tamount from  lps b, invoice a left join invoice_payments c on a.iid = c.invoice_iid where a.lps_lpsid=b.lpsid and a.date between '" . $date . "' and '" . $date2 . "' and b.Lab_lid='" . $_SESSION['lid'] . "' " . $refx . " " . $brcode . " ".$query1." ".$query2."");

} else if ($RID == '4') {
    $rtype = "Monthly Income";
    $detail = "Month " . month($date) . "-" . $year;
    $res = DB::select("select a.Discount_did,u.fname,u.lname,p.dob,u.tpno,a.iid as iid ,a.date as invdate,a.total as totalamount,a.gtotal as gtot,a.paymentmethod as method,a.cashier as cash,b.sampleNo as smple,a.paid as paid,b.arivaltime from invoice a, lps b,patient p,user u where b.patient_pid=p.pid and u.uid=p.user_uid and a.lps_lpsid=b.lpsid and month(a.date)='" . $date . "' and year(a.date)='" . $year . "' and b.Lab_lid='" . $_SESSION['lid'] . "' " . $brcode . "");
    $res1 = DB::select("select count(a.iid) as invoicecount,sum(a.gtotal) as toatalsum, sum(a.paid) as totalpaid, sum(a.total) as tamount  from invoice a, lps b where a.lps_lpsid=b.lpsid and month(a.date)='" . $date . "' and year(a.date)='" . $year . "' and b.Lab_lid='" . $_SESSION['lid'] . "' " . $brcode . "");
} else if ($RID == '7') {
    $rtype = "Annual Income";
    $detail = Input::get('option');
    $res = DB::select("select a.Discount_did,u.fname,u.lname,p.dob,u.tpno,a.iid as iid ,a.date as invdate,a.total as totalamount,a.gtotal as gtot,a.paymentmethod as method,a.cashier as cash,b.sampleNo as smple,a.paid as paid,b.arivaltime from invoice a, lps b,patient p,user u where b.patient_pid=p.pid and u.uid=p.user_uid and a.lps_lpsid=b.lpsid and year(a.date)='" . $date . "' and b.Lab_lid='" . $_SESSION['lid'] . "' " . $brcode . "");
    $res1 = DB::select("select count(a.iid) as invoicecount,sum(a.gtotal) as toatalsum, sum(a.paid) as totalpaid, sum(a.total) as tamount  from invoice a, lps b where a.lps_lpsid=b.lpsid and year(a.date)='" . $date . "' and b.Lab_lid='" . $_SESSION['lid'] . "' " . $brcode . "");
}
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
<p>Branch : 
    <?php
    $result1x = DB::select("select name from labbranches where code = '" . $br . "' and lab_lid = '" . $_SESSION['lid'] . "' ");
    foreach ($result1x as $cols1x){
        echo $cols1x->name;
    }
    ?>
</p>

<p>{{$refname}}</p>
<p>Payment Method : {{ $payMethod }}</p>
<p>Payment Status : {{ $paymentStatus }}</p>
<table >
    <thead>
        <tr>
            <th>Invoice ID</th>
            <th>Patient Name</th>
            <!--<th>D.O.B.</th>-->
            <!--<th>Contact NO</th>-->
            <th>Date</th>
            <th>Arrival Time</th>
            <th>Sample Number</th>
            <th>Total Amount</th>            
            <th>Discount Amount</th>

            <th>Grand Total</th>
            <th>Paid Amount</th>
            <!--<th>Payment Method</th>-->
            <!--<th>Cashier</th>-->
        </tr>    
    </thead>
    <tbody>
        <?php
        foreach ($res as $cols) {
            ?>
            <tr>
                <td style="text-align: center;"><?php echo $cols->iid; ?></td>
                <td style="text-align: center;"><?php echo $cols->fname . " " . $cols->lname; ?></td>
                <!--<td style="text-align: center;"><?php echo $cols->dob; ?></td>-->
                <!--<td style="text-align: center;"><?php echo $cols->tpno; ?></td>-->
                <td style="text-align: center;"><?php echo $cols->invdate; ?></td>
                <td style="text-align: center;"><?php echo $cols->arivaltime; ?></td>
                <td style="text-align: center;"><?php echo $cols->smple; ?></td>
                <td style="text-align: center;"><?php echo $cols->totalamount; ?></td>
                
                <td style="text-align: center;"><?php echo $cols->totalamount - $cols->gtot;?></td>
                
                
                <td style="text-align: center;"><?php echo $cols->gtot; ?></td>
                <td style="text-align: center;"><?php echo $cols->paid; ?></td>
                <!--<td style="text-align: center;"><?php echo $cols->method; ?></td>-->
                <!--<td style="text-align: center;"><?php echo $cols->cash; ?></td>-->
            </tr>

            <?php
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="10"><hr></th>
        </tr>
        <?php foreach ($res1 as $cols1) { ?>
            <tr>
                <th>Total Count: <?php echo $cols1->invoicecount ?></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total in Rs.</th>
                <th><?php echo number_format($cols1->tamount, 2) ?></th>
                <th><?php echo number_format($cols1->toatalsum, 2) ?></th>
                <th><?php echo number_format($cols1->totalpaid, 2) ?></th>
                <th><?php echo "Discount : ".number_format($cols1->tamount - $cols1->toatalsum, 2) ?></th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <th colspan="10"><hr></th>
            </tr>
        <?php } ?>
    </tfoot>
</table>
<hr/>

@stop

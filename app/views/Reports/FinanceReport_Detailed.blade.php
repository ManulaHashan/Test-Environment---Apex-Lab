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
<link href="{{ asset('CSS/Stylie.css') }}" rel='stylesheet' type='text/css' />
<script src="{{ asset('JS/jquery-3.1.0.js') }}"></script>
<script type="text/javascript">
//    window.onload = print();

function export_excel() {

//        alert();

    var dataz = $("#data_table_div").html();
//        
//        alert(dataz);

    var url = "export_excel";
    $.ajax({
        type: 'POST',
        url: url,
        data: {'data': dataz, 'filename': 'Detail_Report'},
        success: function (data) {

//                alert(data);

            var page = "exported_report";
//
            window.location = encodeURI(page);
        }
    });

}
</script>
@stop

@section('heading')
Finance Detailed Report
@stop

@section('content')
<?php
//echo "Detailed Report";
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
    $refx = "and b.refference_idref = '" . $ref . "'";

    $result1xref = DB::select("select name from refference where idref = '" . $ref . "' and lid = '" . $_SESSION['lid'] . "' ");
    foreach ($result1xref as $cols1xref) {
        $refname = "Reference : " . $cols1xref->name;
    }
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
    $rtype = "Daily Income";
    $detail = " From " . $date . " To " . $date2;
    $res = DB::select("select b.status as billstate, b.refference_idref,g.gender, p.age, p.initials,u.fname,u.lname,DATE_FORMAT(p.dob, '%d %b %Y') as dob,u.tpno,a.iid as iid ,a.date as invdate,a.total as totalamount,a.gtotal as gtot,a.paymentmethod as method,a.cashier as cash,b.sampleNo as smple,a.paid as paid,b.arivaltime,b.lpsid from lps b,patient p,user u, gender g, invoice a left join invoice_payments c on a.iid = c.invoice_iid where u.gender_idgender = g.idgender and b.patient_pid=p.pid and u.uid=p.user_uid and a.lps_lpsid=b.lpsid and a.date between '" . $date . "' and '" . $date2 . "' and b.Lab_lid='" . $_SESSION['lid'] . "' " . $refx . " " . $brcode . " ".$query1." ".$query2." order by a.iid ASC, b.lpsid ASC");

    $res1 = DB::select("select count(a.iid) as invoicecount,sum(a.gtotal) as toatalsum, sum(a.paid) as totalpaid, sum(a.total) as tamount from  lps b, invoice a left join invoice_payments c on a.iid = c.invoice_iid where a.lps_lpsid=b.lpsid and a.date between '" . $date . "' and '" . $date2 . "' and b.Lab_lid='" . $_SESSION['lid'] . "' " . $refx . " " . $brcode . " ".$query1." ".$query2."");
} else if ($RID == '4') {
    $rtype = "Monthly Income";
    $detail = "Month " . month($date) . "-" . $year;
    $res = DB::select("select b.status as billstate,b.refference_idref,g.gender, p.age,p.initials,u.fname,u.lname,DATE_FORMAT(p.dob, '%d %b %Y') as dob,u.tpno,a.iid as iid ,a.date as invdate,a.total as totalamount,a.gtotal as gtot,a.paymentmethod as method,a.cashier as cash,b.sampleNo as smple,a.paid as paid,b.arivaltime from invoice a, lps b,patient p,user u, gender g where u.gender_idgender = g.idgender and b.patient_pid=p.pid and u.uid=p.user_uid and a.lps_lpsid=b.lpsid and month(a.date)='" . $date . "' and year(a.date)='" . $year . "' and b.Lab_lid='" . $_SESSION['lid'] . "' " . $brcode . " order by a.iid ASC, b.lpsid");
    $res1 = DB::select("select count(a.iid) as invoicecount,sum(a.gtotal) as toatalsum, sum(a.paid) as totalpaid, sum(a.total) as tamount  from invoice a, lps b where a.lps_lpsid=b.lpsid and month(a.date)='" . $date . "' and year(a.date)='" . $year . "' and b.Lab_lid='" . $_SESSION['lid'] . "' " . $brcode . "");
} else if ($RID == '7') {
    $rtype = "Annual Income";
    $detail = Input::get('option');
    $res = DB::select("select b.status as billstate,b.refference_idref,g.gender, p.age,p.initials,u.fname,u.lname,DATE_FORMAT(p.dob, '%d %b %Y') as dob,u.tpno,a.iid as iid ,a.date as invdate,a.total as totalamount,a.gtotal as gtot,a.paymentmethod as method,a.cashier as cash,b.sampleNo as smple,a.paid as paid,b.arivaltime from invoice a, lps b,patient p,user u, gender g where u.gender_idgender = g.idgender and b.patient_pid=p.pid and u.uid=p.user_uid and a.lps_lpsid=b.lpsid and year(a.date)='" . $date . "' and b.Lab_lid='" . $_SESSION['lid'] . "' " . $brcode . " order by a.iid ASC, b.lpsid");
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
    foreach ($result1x as $cols1x) {
        echo $cols1x->name;
    }
    ?>
</p>
<p>{{$refname}}</p>
<p>Payment Method : {{ $payMethod }}</p>
<p>Payment Status : {{ $paymentStatus }}</p>

<div id="data_table_div">

    <p>{{$refname}}</p>
    <table style="font-family: serif;">
        <thead>
            <tr>
                <th>Invoice ID</th>
                <th>Initials</th>
                <th>Patient Name</th>
                <th>D.O.B.</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Contact NO</th>
                <th>Date</th>
                <th>Arrival Time</th>
                <th>Sample Number</th>            
                <th>Tests</th>
                <th>Total Amount</th>
                <th>Grand Total</th>
                <th>Paid Amount</th>
                <th>Payment Method</th>                
                <th>Reference</th>
                <th>Cashier</th>
                <th>Status</th>
            </tr>    
        </thead>
        <tbody>
            <?php
            foreach ($res as $cols) {
                $bill_state = $cols->billstate;
                if($cols->billstate == "pending"){
                    $bill_state = "Active";
                }
                
                ?>
                <tr>
                    <td style="text-align: center; vertical-align: top;"><?php echo $cols->iid; ?></td>
                    <td style="text-align: center; vertical-align: top;"><?php echo $cols->initials; ?></td>
                    <td style="text-align: left; vertical-align: top;"><?php echo $cols->fname . " " . $cols->lname; ?></td>
                    <td style="text-align: center; vertical-align: top;"><?php echo $cols->dob; ?></td>
                    <td style="text-align: center; vertical-align: top;"><?php echo $cols->age; ?></td>
                    <td style="text-align: center; vertical-align: top;"><?php echo $cols->gender; ?></td>
                    <td style="text-align: center; vertical-align: top;"><?php echo $cols->tpno; ?></td>
                    <td style="text-align: center; vertical-align: top;"><?php echo $cols->invdate; ?></td>
                    <td style="text-align: center; vertical-align: top;"><?php echo $cols->arivaltime; ?></td>
                    <td style="text-align: center; vertical-align: top;"><?php echo $cols->smple; ?></td>

                    <td style="text-align: left;">

                        <?php
                        $resTests = DB::select("SELECT *,FORMAT(price,2) as fprice FROM Testgroup where tgid in (select Testgroup_tgid from Lab_has_test where Lab_lid = '" . $_SESSION['lid'] . "' and test_tid in (select test_tid from lps_has_test where lps_lpsid in (select lpsid from lps where sampleno like '" . $cols->smple . "%' and lab_lid = '" . $_SESSION['lid'] . "' and date = '" . $cols->invdate . "')))");
                        foreach ($resTests as $col_tests) {
                            echo "<table width='100%'><tr><td><p>".$col_tests->name . "</p> </td><td align='right'><p>".$col_tests->fprice."</td></tr></table></p>";
                        }
                        ?>

                    </td>

                    <td style="text-align: center; vertical-align: top;"><?php echo $cols->totalamount; ?></td>
                    <td style="text-align: center; vertical-align: top;"><?php echo $cols->gtot; ?></td>
                    <td style="text-align: center; vertical-align: top;"><?php echo $cols->paid; ?></td>
                    <td style="text-align: center; vertical-align: top;"><?php echo $cols->method; ?></td>

                    <?php
                    $resTestsx = DB::select("select name from refference where idref = '" . $cols->refference_idref . "'");
                    foreach ($resTestsx as $col_testsx) { 
                        ?>
                        <td style="text-align: center; vertical-align: top;"><?php echo $col_testsx->name; ?></td>
                        <?php
                    }
                    ?>


                    <td style="text-align: center; vertical-align: top;"><?php echo $cols->cash; ?></td>
                    <td style="text-align: center; vertical-align: top;"><?php echo $bill_state; ?></td>
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
                    <th></th>
                    <th></th>
                </tr>
                <tr>
                    <th colspan="10"><hr></th>
                </tr>
            <?php } ?>
        </tfoot>
    </table>

</div>
<hr/>

<input class="btn" type="button" value="Export To Excel File" name="export" onclick="export_excel()"/>

@stop

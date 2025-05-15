<?php

// $date = date('Y-m-d', strtotime($date));
// echo "This is bill of lab ".$_SESSION["lid"]. "and SampleNO : ". $sno . " Date : ".$date ;

$result_get_lab_details = DB::select("select name,address,email,tpno from Lab where lid = '" . $_SESSION["lid"] . "'");
foreach ($result_get_lab_details as $lab_details){
    $labname = $lab_details->name;;
    $labaddress = $lab_details->address; 
    $labemail = $lab_details->email; 
    $labtpno = $lab_details->tpno; 
}

$patient_pid = "";

$result_get_patient_pid = DB::select("select patient_pid from lps where date = '" . $date . "' and sampleNo like '" . $sno . "%' group by patient_pid");
foreach ($result_get_patient_pid as $lps_details){
    $patient_pid = $lps_details->patient_pid;
}






$result_get_patient_details = DB::select("
    SELECT u.fname, u.lname, u.gender_idgender, 
           p.age, p.months, p.days, p.initials, l.refby
    FROM patient AS p, user AS u, lps AS l
    WHERE p.user_uid = u.uid 
      AND p.pid = '".$patient_pid."'
      group by p.pid 
      
");


$fname = "";
$lname = "";
$gender_data = "";
$age = ""; 
$months = "";
$days = "";
$initials = "";
$refby = "";

    
foreach ($result_get_patient_details as $patient_details) {
        $fname = $patient_details->fname;
        $lname = $patient_details->lname;
    
        if ($patient_details->gender_idgender == "1") {
            $gender_data = "Male";
        }else{
            $gender_data = "Female";
        }

        $age = $patient_details->age;
        $months = $patient_details->months;
        $days = $patient_details->days;
        $initials = $patient_details->initials;
        $refby = $patient_details->refby;
    }

    
$result_get_testgroup_details = DB::select("
    SELECT b.name, b.price 
    FROM lps AS a, Testgroup AS b 
    WHERE a.Testgroup_tgid = b.tgid 
      AND a.sampleNo LIKE '" . $sno . "%'
");


$result_get_invoiceData = DB::select("
    SELECT i.iid, i.total, i.paid, i.gtotal, i.discount, i.status,i.cashier, 
           i.paymentmethod, i.multiple_delivery_methods, d.value, d.did, a.sampleNo
    FROM invoice AS i
    JOIN lps AS a ON i.lps_lpsid = a.lpsid
    LEFT JOIN Discount AS d ON i.Discount_did = d.did
    WHERE a.sampleNo LIKE ? 
    AND a.date = ? 
    AND a.Lab_lid = ?
", [$sno . '%', $date, $_SESSION['lid']]);


    $iid = "";
    $total = 0;
    $paid = 0;
    $gtotal = 0;
    $discount = 0;
    $status = "";
    $paymentmethod = "";
    $multiple_delivery_methods = "";
    $value = "";
    $did = "";
    $cashier ="";
    $due = 0;
// Accessing invoice data
foreach ($result_get_invoiceData as $invoice){
   
    $iid = $invoice->iid;
    $total = $invoice->total;
    $paid = $invoice->paid;
    $gtotal = $invoice->gtotal;
    $discount = $invoice->discount;
    $status = $invoice->status;
    $paymentmethod = $invoice->paymentmethod;
    $multiple_delivery_methods = $invoice->multiple_delivery_methods;
    $value = $invoice->value;
    $did = $invoice->did;
    $cashier = $invoice->cashier;
    $due = $gtotal-$paid;
   
}



?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Receipt</title>
</head>
<body>
    <table style="width: 100%; border-collapse: collapse; margin: 0 auto; font-family: Arial, sans-serif; font-size: 14px;">
        <tr>
            <td colspan="2" style="text-align: center; font-weight: bold;"><?php echo $labname; ?></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center; font-weight: bold;"><?php echo $labaddress; ?><br>Tel: <?php echo $labtpno; ?><br>Web: <br>Email: <?php echo $labemail; ?></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center; font-weight: bold;">PATIENT RECEIPT</td>
        </tr>
        <tr>
            <td>Date: <?php echo date('Y-m-d'); ?> Time: <?php echo date('H:i:s'); ?></td>
            <td style="text-align: right;">Reference NO: <?php echo $sno; ?></td>
        </tr>
        <tr>
            <td>Patient: <?php echo $initials.". ".$fname." ".$lname; ?></td>
            <td style="text-align: right;">Age: <?php echo $age; ?> years Gender: <?php echo $gender_data; ?></td>
        </tr>
        <tr>
            <td colspan="2">Referred By: <?php echo $refby; ?></td>
        </tr>
        <?php 


        foreach ($result_get_testgroup_details as $testgroup_details) 
        {?>
            <tr>
                <td style="border: 1px solid #000; padding: 5px;"><?php echo $testgroup_details->name; ?></td>
                <td style="border: 1px solid #000; padding: 5px; text-align: right;"><?php echo number_format($testgroup_details->price, 2); ?></td>
            </tr><?php
        }
        ?>

  
        <tr>
            <td>Total Amount Rs.</td>
            <td style="text-align: right;"><?php echo number_format($total, 2); ?></td>
        </tr>
        <tr>
            <td>Discount Rs.</td>
            <td style="text-align: right;"><?php echo number_format($discount, 2); ?></td>
        </tr>
        <tr>
            <td>Grand Total Rs.</td>
            <td style="text-align: right;"><?php echo number_format($gtotal, 2); ?></td>
        </tr>
        <tr>
            <td>Due</td>
            <td style="text-align: right;"><?php echo number_format($due, 2); ?></td>
        </tr>
        <tr>
            <td>Method: <?php echo $paymentmethod; ?></td>
            <td>Issued by: <?php echo $cashier; ?></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center; font-weight: bold;">PLEASE COLLECT YOUR REPORTS WITHIN ONE MONTH</td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">Software By Apex Software Solutions (PVT) LTD - www.apexsol.com</td>
        </tr>
    </table>
</body>
</html>
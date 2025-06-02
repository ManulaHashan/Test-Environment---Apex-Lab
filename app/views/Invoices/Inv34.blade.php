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
      And a.date = '" . $date . "'
      AND a.Lab_lid = " . $_SESSION['lid'] . "
");


$result_get_invoiceData = DB::select("
    SELECT i.iid, i.total, i.paid, i.gtotal, i.discount, i.status,i.cashier, 
           i.paymentmethod, i.multiple_delivery_methods, d.value, d.did, a.sampleNo, a.arivaltime, a.date
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
    $inv_date ="";
    $inv_time ="";
    $due = 0;
    $balance=0;
// Accessing invoice data
foreach ($result_get_invoiceData as $invoice){
   
    $iid = $invoice->iid;
    $inv_date = $invoice->date;
    $inv_time = $invoice->arivaltime;
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
    $balance = $paid - $gtotal;

        if ($balance < 0) {
            $balance = 0;
        }

   
}



?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Receipt</title>
</head>
<body>
    <table style="width: 100%; border-collapse: collapse; margin: 0 auto; font-family: Arial, sans-serif; ">
        <tr>
            <td colspan="2" style="text-align: center; font-weight: bold;font-size: 35px;"><?php echo $labname; ?></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center; "><?php echo $labaddress; ?><br>Tel: <?php echo $labtpno; ?><br>Web:www.synergy.com <br>Email: <?php echo $labemail; ?></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">
                <div style="font-weight: bold; font-size: 28px; margin-top: 20px;">PATIENT RECEIPT</div>
            </td>
        </tr>

        <tr>
            <td style="padding-top: 30px; font-size: 18px;">Date: <?php echo $inv_date; ?> Time: <?php echo $inv_time; ?></td>
            <td style="text-align: right; font-weight: bold; padding-top: 5px; font-size: 18px;">
                <span style="color: #000; ">Reference NO</span>
                 <div style="font-size: 30px; margin-top: 5px;"><?php echo $sno; ?></div>
            </td>

           
        </tr>
        <tr>
            
            <td style="font-size: 18px;">Patient: <?php echo $initials.". ".$fname." ".$lname; ?></td>
            
        </tr>
        <tr>
            <td style="text-align: left;font-size: 18px;">
                Age: <?php echo $age; ?> Years
                <span style="margin-left: 20px;font-size: 18px;">Gender: <?php echo $gender_data; ?></span>
            </td>

          
        </tr>
        <tr>
           
            <td style="font-size: 18px;" colspan="2">Referred By: <?php echo $refby; ?></td>
        </tr>
        <tr>
        <td colspan="2" style="height: 20px;"></td>
        </tr>
        <tr>
            <th style="text-align: left; padding: 5px;border-bottom: 2px solid #000;">Test Name</th>
            <th style="text-align: right; padding: 5px; border-bottom: 2px solid #000;">Price</th>
        </tr>
       
        
        <?php 


        foreach ($result_get_testgroup_details as $testgroup_details) 
        {?>

            <tr>
                <td style="padding: 5px; text-align: left;"><?php echo $testgroup_details->name; ?></td>
                <td style="padding: 5px; text-align: right;"><?php echo number_format($testgroup_details->price, 2); ?></td>
            </tr>
            <?php
        }
        ?>

      

   <tr>
    <td style="border-top: 2px solid #000; text-align: left; padding-top: 20px; font-size: 22px; width: 50%;">
        <span style="display: inline-block; width: 150px;">Total Amount</span>
        <span style="display: inline-block; width: 50px; text-align: right;">Rs.</span>
        <span style="display: inline-block; width: 100px; text-align: right;"><?php echo number_format($total, 2); ?></span>
    </td>
    <td style="border-top: 2px solid #000; text-align: right; padding-top: 20px; font-size: 22px; width: 50%;">
        <span style="display: inline-block; width: 150px;">Payment</span>
        <span style="display: inline-block; width: 50px; text-align: right;">Rs.</span>
        <span style="display: inline-block; width: 100px; text-align: right;"><?php echo number_format($paid, 2); ?></span>
    </td>
</tr>

<tr>
    <td style="text-align: left; font-size: 22px;">
        <span style="display: inline-block; width: 150px;">Discount</span>
        <span style="display: inline-block; width: 50px; text-align: right;">Rs.</span>
        <span style="display: inline-block; width: 100px; text-align: right;"><?php echo number_format($discount, 2); ?></span>
    </td>
    <td style="text-align: right; font-size: 22px;">
        <span style="display: inline-block; width: 150px;">Balance</span>
        <span style="display: inline-block; width: 50px; text-align: right;">Rs.</span>
        <span style="display: inline-block; width: 100px; text-align: right;"><?php echo number_format($balance, 2); ?></span>
    </td>
</tr>

<tr>
    <td style="text-align: left; font-size: 22px;">
        <span style="display: inline-block; width: 150px;">Grand Total</span>
        <span style="display: inline-block; width: 50px; text-align: right;">Rs.</span>
        <span style="display: inline-block; width: 100px; text-align: right;"><?php echo number_format($gtotal, 2); ?></span>
    </td>
    <td style="text-align: right; font-size: 22px;">
        <span style="display: inline-block; width: 150px;">Due</span>
        <span style="display: inline-block; width: 50px; text-align: right;">Rs.</span>
        <span style="display: inline-block; width: 100px; text-align: right;"><?php echo number_format($due, 2); ?></span>
    </td>
</tr>

    
           <tr>
                <td style="border-top: 2px solid #000; text-align: left; padding-top: 20px; vertical-align: top;">
                    Issued by: <span style="margin-left: 10px;"><?php echo $cashier; ?></span>
                </td>
                <td style="border-top: 2px solid #000; text-align: center; padding-top: 20px;">
                    <!-- First Line: Method -->
                    <div style="margin-bottom: 10px; text-align: right;">
                        Method: <span style="margin-left: 10px;"><?php echo $paymentmethod; ?></span>
                    </div>
                    <!-- Second Line: Cashier -->
                    <div style="display: inline-block; text-align: center;">
                        <hr style="border: none; border-top: 1px dotted; width: 200px;">
                        <em>Cashier</em>
                    </div>
                </td>
                <td style="border-top: 2px solid #000; text-align: left; padding-top: 20px; vertical-align: top;">
                    <!-- Empty cell to maintain the table structure -->
                </td>
        </tr>



      
        <tr>
            <td colspan="2" style="text-align: center; font-weight: bold;padding-top: 20px;">PLEASE COLLECT YOUR REPORTS WITHIN ONE MONTH</td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: left; font-size: 10px;">Software By Apex Software Solutions (PVT) LTD - www.apexsol.com</td>
        </tr>
    </table>
</body>
</html>
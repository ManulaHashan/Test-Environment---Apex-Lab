<?php

if (!isset($_SESSION)) {
    session_start();
}
date_default_timezone_set('Asia/Colombo');

class InvoiceController extends Controller {

    function getTestUnitPrice() {
        $testId = Input::get("testid");
        $result = null;
        if ($testId == null) {
            $result = "Please Select Test";
            return json_encode($result);
        } else if ($testId == "") {
            $result = "Please Select Test";
            return json_encode($result);
        } else if ($testId == "0") {
            $result = "Please Select Test";
            return json_encode($result);
        } else {
            $result = DB::select("select price from Lab_has_test where test_tid='" . $testId . "' and Lab_lid = '" . $_SESSION['lid'] . "'");
            return json_encode($result);
        }
    }

    function saveInvoice() {

        $total_tests = Input::get("total_tests");
        $total_qty = Input::get("total_qty");
        $total_cost = Input::get("total_cost");
        $company = Input::get("company");
        $data = Input::get("result");

        if ($total_tests != "" && $total_qty != "" && $total_cost != "" && $company != "" && $data != "") {

            $currentDate = date("Y-m-d");

//            $ips = DB::select("SELECT `lps`.`lpsid` AS ipsid FROM `patient` INNER JOIN `user` ON (`patient`.`user_uid` = `user`.`uid`) INNER JOIN `lps`ON (`lps`.`patient_pid` = `patient`.`pid`) WHERE user.`uid`='".$company."'");
//             echo "SELECT `lps`.`lpsid` AS ipsid FROM `patient` INNER JOIN `user` ON (`patient`.`user_uid` = `user`.`uid`) INNER JOIN `lps`ON (`lps`.`patient_pid` = `patient`.`pid`) WHERE user.`uid`='".$company."'";
//            foreach ($ips as $ip) {
            // $ipsid = $ip->ipsid;
            $res = DB::insert("insert into companyinvoices(pid,gtotal,paid,paiddate,status,paymentmethod,cashier,Discount_did,total_qty)"
                            . "values('$company','$total_tests','$total_cost','$currentDate','1','cash','cashier','1','$total_qty')");
            if ($res == 1) {
                $id = DB::select("SELECT MAX(ciid)AS ciid FROM companyinvoices");
                $invoiceid = 0;
                foreach ($id as $iid) {
                    $invoiceid = $iid->ciid;
                }
                if ($invoiceid != null && $invoiceid != 0) {
                    $val_decode = json_decode($data, true);
                    foreach ($val_decode as $a) {
                        foreach ($a as $b) {
                            $testid = $b["s_1"];
                            $qty = $b["s_3"];
                            $up = $b["s_2"];
                            $dup = $b["s_4"];
                            $tot = $b["s_5"];
                            $issuedate = $b["s_6"];
                            $issuetime = $b["s_7"];
                            DB::Insert("insert into companyinvoices_has_test (ciid,tid,amount,unit_price,discounted_up,total,issue_date,issue_time) values ('$invoiceid','$testid','$qty','$up','$dup','$tot','$issuedate','$issuetime')");
                        }
                    }
                    $invoiceContent = $this->createInvoiceEmailContent($invoiceid);
                    if ($invoiceContent != null) {
                            echo $invoiceid . "+" . $invoiceContent;
                    }
                }
            }
            // }
        } else {
            echo 'Please enter valid data to save';
        }
    }

    function getInvoiceCustomer($invoiceid) {
        $customer_resul = DB::select("select a.fname,a.lname,c.paiddate from user a,patient b,companyinvoices c where a.uid=b.user_uid AND b.pid=c.pid AND c.ciid='$invoiceid'");
        if (sizeof($customer_resul) != 0) {
            foreach ($customer_resul as $data) {
                $upperContent = '<td style="text-align: left;">Date : ' . $data->paiddate . '</td>
                    <td style="text-align: right;">Customer : ' . $data->fname . " " . $data->lname . '</td>';
            }
        }
        return $upperContent;
    }

    function createInvoiceEmailContent($invoiceid) {
        if ($invoiceid != null && $invoiceid != "") {
            $content = null;
            $upperContent = $this->getInvoiceCustomer($invoiceid);
            $invoiceData = DB::select("SELECT
    `test`.`name` AS name
    , `companyinvoices_has_test`.`amount` AS amount
    , `companyinvoices_has_test`.`unit_price` AS unit_price
    , `companyinvoices_has_test`.`discounted_up` AS discounted_up
    , `companyinvoices_has_test`.`total` AS total
    , `companyinvoices_has_test`.`issue_date` AS issue_date
    , `companyinvoices_has_test`.`issue_time` AS issue_time
FROM
    `companyinvoices_has_test`
    INNER JOIN `test` 
        ON (`companyinvoices_has_test`.`tid` = `test`.`tid`)
    INNER JOIN `companyinvoices` 
        ON (`companyinvoices_has_test`.`ciid` = `companyinvoices`.`ciid`) WHERE `companyinvoices`.`ciid`=" . $invoiceid . "");
            if (sizeof($invoiceData) != 0) {
                $testcount = 0;
                $testqtycount = 0;
                $grandtotal = 0;
                foreach ($invoiceData as $idata) {
                    $testcount++;
                    $testqtycount += ($idata->amount);
                    $grandtotal += ($idata->total);

                    $content .= "<tr id='trid" . $invoiceid . "'><td style='text-align: center;'><label style=''>" . $idata->name . "</label></td><td style='text-align: center;'><label style='margin-right: 0px;'>Rs:</label>" . $idata->unit_price . ".00</td><td style='text-align: center;'><label>" . $idata->amount . "</label></td><td style='text-align: center;'><label style='margin-right: 0px;'>Rs:</label>" . $idata->discounted_up . "</td><td style='text-align: center;'><label style='margin-right: 0px;'>Rs:</label>" . $idata->total . ".00</td><td style='text-align: center;'><label>" . $idata->issue_date . "</label></td><td style='text-align: center;'><label>" . $idata->issue_time . "</label></td></tr>";
                }
            }
            $invoiceLayout = '<div id="main" style="margin-top:10px;">

    <table width="98%" style="margin-left: 10px;margin-top: 20px;">
        <tr>' . $upperContent . '</tr>
    </table>
<div style="text-align: center;width: 100%;margin-top: 50px;" width="98%">
        <table class="table-row-bodered" width="98%" style="margin-left: 10px;" border="1" id="invoice">
            <thead>
                <tr class="viewTHead">
                    <th>Test</th>
                    <th>Unit Price</th>
                    <th>Quantity</th>
                    <th>Discounted Unit Price</th>
                    <th>Sub Total</th>
                    <th>Issuing Date</th>
                    <th>Issuing Time</th>
                </tr>
            </thead>
            <tbody id="invoicetbody">' . $content . '
                   
            </tbody>
        </table>
        <table width="80%" style="float: right;">
            <tr width="80%"><td style="text-align: right;padding-right: 20px;font-size: 15px;"><label>Issued By : </label><label>Malinda Senanayake</label></td></tr>
        </table>
        <table width="100%">
            <tr>
                <td>
                    <table width="80%" style="margin-left: 10px;margin-top: 25px;z-index: auto;">
                        <tr width="98%">
                            <td style="text-align: left;padding-top: 8px;padding-bottom: 8px;width: 20px;">Total Tests </td>
                            <td style="text-align: left;"><label style="margin-right: 10px;">: </label><label style="font-size: 20px;">' . $testcount . '</label></td>
                        </tr>
                        <tr>
                            <td style="text-align: left;padding-top: 8px;padding-bottom: 8px;">Total Test Quantity </td>
                            <td style="text-align: left;width: 95px;"><label style="margin-right: 10px;">: </label><label style="font-size: 20px;">' . $testqtycount . '</label></td>
                        </tr>
                        <tr>
                            <td style="text-align: left;padding-top: 8px;padding-bottom: 8px;font-size: 18px;font-weight: 700;">Grand Total </td>
                            <td style="text-align: left;"><label style="margin-right: 10px;font-size: 18px;font-weight: 700;">: Rs </label><label style="font-size: 18px;font-weight: 700;">' . $grandtotal . '.00</label></td>
                        </tr>
                    </table>
                </td>
                <td style="text-align: right;padding-top: 80px;">
                    <table width="80%" style="float: right;">
                        <tr width="80%">
                            <td style="text-align: right;">...............................................</td>
                        </tr>
                        <tr width="80%">
                            <td style="text-align: right;"><label style="">Authorized By</label></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>';
        }

        return $invoiceLayout;
    }

    function call() {
        echo $iid;
    }

    function removeInvoice() {
        $iid = Input::get("iid");
        if ($iid != null && $iid != "" && $iid != 0) {
            
        } else {
            return 0;
        }
    }

    function updateInvoice() {
        $iid = Input::get("iid");
        $total_tests = Input::get("total_tests");
        $total_qty = Input::get("total_qty");
        $total_cost = Input::get("total_cost");
        $company = Input::get("company");
        $data = Input::get("result");

        if ($total_tests != "" && $total_qty != "" && $total_cost != "" && $company != "" && $data != "") {
            $currentDate = date("Y-m-d");
            $resUpdate = DB::update("UPDATE `companyinvoices`
SET
  `pid` = '$company',
  `gtotal` = '$total_tests',
  `paid` = '$total_cost',
  `paiddate` = '$currentDate',
  `status` = '1',
  `paymentmethod` = 'cash',
  `cashier` = 'cashier',
  `Discount_did` = '1',
  `total_qty` = '$total_qty'
WHERE `ciid` = '$iid'");
            if (sizeof($resUpdate) != 0) {
                if ($iid != null && $iid != 0) {
                    $val_decode = json_decode($data, true);

                    //remove old rows
                    $getOldRecors = DB::select("SELECT * FROM `companyinvoices_has_test` WHERE `ciid`='$iid'");

                    if (sizeof($getOldRecors) != 0) {
                        foreach ($getOldRecors as $record) {
                            $invoice_has_test_id = $record->id;
                            DB::delete("delete from companyinvoices_has_test WHERE id='$invoice_has_test_id'");
                        }
                    }
                    //remove old rows
                    foreach ($val_decode as $a) {
                        foreach ($a as $b) {

                            //add new records
                            $testid = $b["s_1"];
                            $qty = $b["s_3"];
                            $up = $b["s_2"];
                            $dup = $b["s_4"];
                            $tot = $b["s_5"];
                            $issuedate = $b["s_6"];
                            $issuetime = $b["s_7"];
                            DB::Insert("insert into companyinvoices_has_test (ciid,tid,amount,unit_price,discounted_up,total,issue_date,issue_time) values ('$iid','$testid','$qty','$up','$dup','$tot','$issuedate','$issuetime')");
                        }
                    }
                    echo 1;
                }
            }
        } else {
            echo 'Please enter valid data to save';
        }
    }

    function getInvoice() {
        $iid = Input::get("iid");
        if ($iid != null && $iid != "") {
            $result = DB::select("select a.fname,a.lname,c.paiddate,c.gtotal,c.paid,c.cashier,c.paymentmethod,c.status from user a,patient b,companyinvoices c where a.user_id=b.user_id AND b.pid=c.pid AND c.ciid='$iid'");

            if (sizeof($result) != 0) {
                foreach ($result as $data) {
                    $name = $data->fname . " " . $data->lname;
                    $dataRow = $data->iid . "+" . $name . "+" . $data->paiddate . "+" . $data->gtotal . "+" . $data->paid . "+" . $data->cashier . "+" . $data->paymentmethod . "+" . $data->status;
                }
                return $dataRow;
            } else {
                return "empty";
            }
        } else {
            echo 'invalid Invoice No';
        }
    }

    function getDateInvoice() {
        $inc = 0;
        $dataRow[] = null;
        $date = Input::get("date");
        if ($date != null && $date != "") {
            
            $result = DB::select("select c.ciid,a.fname,a.lname,c.paiddate,c.gtotal,c.paid,c.cashier,c.paymentmethod,c.status from user a,patient b,companyinvoices c where a.user_id=b.user_id AND b.pid=c.pid AND c.`paiddate`='$date' ORDER BY `c`.`ciid` DESC");
            if (sizeof($result) != 0) {
                foreach ($result as $data) {
                    $name = $data->fname . " " . $data->lname;
                    $dataRow [$inc] = $data->ciid . "+" . $name . "+" . $data->paiddate . "+" . $data->gtotal . "+" . $data->paid . "+" . $data->cashier . "+" . $data->paymentmethod . "+" . $data->status;
                    $inc++;
                }
                if ($dataRow != null) {
                    return json_encode($dataRow);
                } else {
                    echo "empty";
                }
            } else {
                echo "empty";
            }
        } else {
            echo 'invalid Invoice No';
        }
    }

    function getCustomerInvoice() {
        $inc = 0;
        $dataRow[] = null;
        $uid = Input::get("uid");
        if ($uid != null && $uid != "") {
            $result = DB::select("select c.ciid,a.fname,a.lname,c.paiddate,c.gtotal,c.paid,c.cashier,c.paymentmethod,c.status from user a,patient b,companyinvoices c where a.user_id=b.user_id AND b.pid=c.pid AND a.`uid`='$uid' GROUP BY `c`.`ciid` DESC");
            if (sizeof($result) !== 0) {
                foreach ($result as $data) {
                    $name = $data->fname . " " . $data->lname;
                    $dataRow[$inc] = $data->iid . "+" . $name . "+" . $data->date . "+" . $data->totaltests . "+" . $data->gtotal . "+" . $data->cashier . "+" . $data->pmethod . "+" . $data->status;
                    $inc++;
                }
                if ($dataRow !== null) {
                    return json_encode($dataRow);
                } else {
                    echo "empty";
                }
            } else {
                echo "empty";
            }
        } else {
            echo 'invalid Invoice No';
        }
    }

}

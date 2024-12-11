<style type="text/css">
    .tblinvoice {
        border-collapse: collapse;
    }
    .tblinvoice tr td {
        border: 1px solid black;
    }
    .tbltest
    {
        margin: 15px 15px 15px 0px;
        padding: 0px;
        border-collapse: separate;
        border-spacing: 15px;

    }
    .tblcustomer{margin-top: 15px;}
    .tdleft{text-align: right;padding-left: 160px;padding-top: 10px;}
    .tbltdleft tr td{padding-top: 15px;}
    .tbltdright tr td{padding-top: 15px;}
    #invoice{
        border-collapse: collapse;
    }
    #invoice th td {
        border: 1px solid black;
    }
</style>
<div id="main" style="margin-top: 250px;">

    <table width="98%" style="margin-left: 10px;margin-top: 20px;">
        <tr>
            <?php
            $result = DB::select("SELECT
    `user`.`fname` AS fname
    , `user`.`lname` AS lname
    , `invoice`.`date` AS date
    , `invoice`.`total` AS total
    , `invoice`.`discount` AS discount
    , `invoice`.`gtotal` AS gtotal
    , `invoice`.`paid` AS paid
    , `invoice`.`paiddate` AS paiddate
    , `invoice`.`paymentmethod` AS paymentmethod
    , `invoice`.`cashier` AS cashier
FROM
    `patient`
    INNER JOIN `user` 
        ON (`patient`.`user_uid` = `user`.`uid`)
    INNER JOIN `lps` 
        ON (`lps`.`patient_pid` = `patient`.`pid`)
    INNER JOIN `invoice` 
        ON (`invoice`.`lps_lpsid` = `lps`.`lpsid`) WHERE invoice.`iid`=" . $iid . "");
            if ($result != null) {
                foreach ($result as $data) {
                    ?>
                    <td style="text-align: left;">Date : <?php echo $data->date; ?></td>
                    <td style="text-align: right;">Customer : <?php echo $data->fname . " " . $data->lname; ?></td>
                    <?php
                }
            }
            ?>

        </tr>
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
            <tbody id="invoicetbody">
                <?php
                $invoiceData = DB::select("SELECT
    `test`.`name` AS name
    , `invoice_has_test`.`amount` AS amount
    , `invoice_has_test`.`unit_price` AS unit_price
    , `invoice_has_test`.`discounted_up` AS discounted_up
    , `invoice_has_test`.`total` AS total
    , `invoice_has_test`.`issue_date` AS issue_date
    , `invoice_has_test`.`issue_time` AS issue_time
FROM
    `invoice_has_test`
    INNER JOIN `test` 
        ON (`invoice_has_test`.`tid` = `test`.`tid`)
    INNER JOIN `invoice` 
        ON (`invoice_has_test`.`iid` = `invoice`.`iid`) WHERE `invoice`.`iid`=" . $iid . "");
                if ($invoiceData != null) {
                    $testcount = 0;
                    $testqtycount = 0;
                    $grandtotal = 0;
                    foreach ($invoiceData as $idata) {
                        $testcount++;
                        $testqtycount += ($idata->amount);
                        $grandtotal += ($idata->total);
                        ?>
                        <tr class="viewTHead">
                            <td style="text-align: center;"><label style=""><?php echo $idata->name; ?></label></td>
                            <td style="text-align: center;"><label style="margin-right: 0px;">Rs:</label><?php echo $idata->unit_price; ?>.00</td>
                            <td style="text-align: center;"><label><?php echo $idata->amount; ?></label></td>
                            <td style="text-align: center;"><label style="margin-right: 0px;">Rs:</label><?php echo $idata->discounted_up; ?>.00</td>
                            <td style="text-align: center;"><label style="margin-right: 0px;">Rs:</label><?php echo $idata->total; ?>.00</td>
                            <td style="text-align: center;"><label><?php echo $idata->issue_date; ?></label></td>
                            <td style="text-align: center;"><label><?php echo $idata->issue_time; ?></label></td>
                        </tr>
                        <?php
                    }
                }
                ?>      
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
                            <td style="text-align: left;"><label style="margin-right: 10px;">: </label><label style="font-size: 20px;"><?php echo $testcount; ?></label></td>
                        </tr>
                        <tr>
                            <td style="text-align: left;padding-top: 8px;padding-bottom: 8px;">Total Test Quantity </td>
                            <td style="text-align: left;width: 95px;"><label style="margin-right: 10px;">: </label><label style="font-size: 20px;"><?php echo $testqtycount; ?></label></td>
                        </tr>
                        <tr>
                            <td style="text-align: left;padding-top: 8px;padding-bottom: 8px;font-size: 18px;font-weight: 700;">Grand Total </td>
                            <td style="text-align: left;"><label style="margin-right: 10px;font-size: 18px;font-weight: 700;">: Rs </label><label style="font-size: 18px;font-weight: 700;"><?php echo $grandtotal; ?>.00</label></td>
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
    </div>
</div>
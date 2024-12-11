@extends('Templates/WiTemplate')
<?php
session_start();
date_default_timezone_set('Asia/Colombo');
?>
@section('title')
Invoice Management
@stop

@section('head')
<script type="text/javascript">
    $(document).ready(function () {
        $("#uprice").prop("disabled", true);
        $("#discount").prop("disabled", true);
        $("#qty").prop("disabled", true);
        $("#tcost").prop("disabled", true);
    });
    function setCurrentTime() {

        var time = new Date();
        time = time.toLocaleString('en-US', {hour: 'numeric', minute: 'numeric', hour12: true});
        document.getElementById('ptime').value = time;
    }

    function searchInvoiceData() {
        var iid = $('#iid').val();
        if (iid !== null && iid !== "") {
            $.ajax({
                type: 'POST',
                url: "getInvoice",
                data: {'iid': iid},
                success: function (data) {
                    if (data.trim() === "empty") {
                        alert("No Invoice Found");
                    } else if (data.trim() === "invalid Invoice No") {
                        alert('Invalid Invoice No,Please check the Invoice No');
                    } else {
                        var arr = data.split("+");
                        var iid = arr[0];
                        var name = arr[1];
                        var date = arr[2];
                        var tottest = arr[3];
                        var gtot = arr[4];
                        var issueby = arr[5];
                        var pm = arr[6];
                        var status = arr[7];
                        var dataRow = "<tr><td>" + iid + "</td><td>" + name + "</td><td>" + date + "</td><td>" + tottest + "</td><td>" + gtot + "</td><td>" + issueby + "</td><td>" + pm + "</td><td>" + status + "</td><td><input type='button' name='view' class='btn' id="+iid+" value='View More' onclick='viewMore(this.id);'></td></tr>";
                        var table = document.getElementById("invoicebody");
                        table.innerHTML = "";
                        table.innerHTML = table.innerHTML + dataRow;
                    }
                }
            });
        } else {
            location.reload();
        }
    }
    function searchInvoiceDataByCustomer() {
        var uid = $('#customer').val();
        if (uid !== null && uid !== "") {
            $.ajax({
                type: 'POST',
                url: "getCustomerInvoice",
                data: {'uid': uid},
                success: function (data) {
                    if (data.trim() === "empty") {
                        alert("No Invoice Found");
                    } else if (data.trim() === "invalid Invoice No") {
                        alert('Customer not found,Please select the valid customer');
                    } else {
                        console.log(data);
                        var x = JSON.parse(data)
                        console.log(x.length);
                        var table = document.getElementById("invoicebody");
                        table.innerHTML = "";
                        for (var i = 0; i < x.length; i++) {
                            var y = x[i];
                            console.log(y);
                            var arr = y.split("+");
                            var iid = arr[0];
                            var name = arr[1];
                            var date = arr[2];
                            var tottest = arr[3];
                            var gtot = arr[4];
                            var issueby = arr[5];
                            var pm = arr[6];
                            var status = arr[7];
                            var dataRow = "<tr><td>" + iid + "</td><td>" + name + "</td><td>" + date + "</td><td>" + tottest + "</td><td>" + gtot + "</td><td>" + issueby + "</td><td>" + pm + "</td><td>" + status + "</td><td><input type='button' name='view' class='btn' id="+iid+" value='View More' onclick='viewMore(this.id);'></td></tr>";
                            table.innerHTML = table.innerHTML + dataRow;
                        }
                    }
                }
            });
        } else {
            location.reload();
        }
    }
    function getDateInvoices() {
        var date = $('#idate').val();
        if (date != null && date != "") {
            $.ajax({
                type: 'POST',
                url: "getDateInvoice",
                data: {'date': date, '_token': $('input[name=_token]').val()},
                success: function (data) {
                    if (data.trim() === "empty") {
                        alert("No Invoice Found");
                    } else if (data.trim() === "invalid Invoice No") {
                        alert('Invalid Date,Please check the date');
                    } else {
                        console.log(data);
                        var x = JSON.parse(data)
                        console.log(x.length);
                        var table = document.getElementById("invoicebody");
                        table.innerHTML = "";
                        for (var i = 0; i < x.length; i++) {
                            var y = x[i];
                            console.log(y);
                            var arr = y.split("+");
                            var iid = arr[0];
                            var name = arr[1];
                            var date = arr[2];
                            var tottest = arr[3];
                            var gtot = arr[4];
                            var issueby = arr[5];
                            var pm = arr[6];
                            var status = arr[7];
                            var dataRow = "<tr><td>" + iid + "</td><td>" + name + "</td><td>" + date + "</td><td>" + tottest + "</td><td>" + gtot + "</td><td>" + issueby + "</td><td>" + pm + "</td><td>" + status + "</td><td><input type='button' name='view' class='btn' id="+iid+" value='View More' onclick='viewMore(this.id);'></td></tr>";
                            table.innerHTML = table.innerHTML + dataRow;
                        }

                    }
                }
            });
        } else {
            location.reload();
        }
    }
    function viewMore(id) {
        window.open("loadInvoice/" + id, '_blank');
    }
</script>
@stop

@section('body')
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
    #btnremove{margin-top: 5px;}
    #btnremove:hover{cursor: pointer;-moz-transform: scale(1.2); -webkit-transform: scale(1.2);transform: scale(1.2);}
    #btnupdate{margin-top: 12px;}
    #btnupdate:hover{cursor: pointer;-moz-transform: scale(1.2); -webkit-transform: scale(1.2);transform: scale(1.2);}
</style>

<blockquote>
    <h3 class="pageheading">Invoice Results</h3>
    <br/>
    <br/>
    <table class="tbltest">
        <tr>
            <td>Invoice No :</td>
            <td>
                <input type="text" name="iid" id="iid" placeholder="Invoice No" class="input-text">
            </td>
            <td>Issue Date :</td>
            <td>
                <input type="date" name="idate" id="idate" class="input-text" value="<?php echo date("Y-m-d"); ?>" onchange="getDateInvoices();">
            </td>
            <td>
                Customer :
            </td>
            <td>
                <select id="customer" class="select-basic" onchange="searchInvoiceDataByCustomer();">
                    <option value="0">~~Select Customer~~</option>
                    <?php
                    $cusData = DB::select("SELECT uid,fname,lname FROM user WHERE usertype_idusertype='3'");
                    if ($cusData != null) {
                        foreach ($cusData as $cdata) {
                            ?>
                            <option value="<?php echo $cdata->uid; ?>"><?php echo $cdata->fname . " " . $cdata->lname; ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </td>
            <td>
                <input type="button" class="btn" value="Search" style="margin-right: 0px; margin-left: 0px; width: 100px;float: left;" onclick="searchInvoiceData()">
            </td>
        </tr>
    </table>
    <div style="overflow-y:scroll;">
        <table class="table-row-bodered" id="invoicetable" width="100%">
            <thead>
                <tr class="viewTHead">
                    <th>Invoice No</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total Tests</th>
                    <th>Total Cost</th>
                    <th>Issued By</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="invoicebody">
                <?php
                $result = DB::select("SELECT
    `invoice`.`iid` AS iid
    , `user`.`fname` AS fname
    , `user`.`lname` AS lname
    , `invoice`.`date` AS date
    , `invoice`.`total` AS totaltests
    , `invoice`.`gtotal` AS gtotal
    , `invoice`.`cashier` AS cashier
    , `invoice`.`paymentmethod` AS pmethod
    , `invoice`.`status` AS status
FROM
   `patient`
    INNER JOIN `user` 
        ON (`patient`.`user_uid` = `user`.`uid`)
    INNER JOIN `lps` 
        ON (`lps`.`patient_pid` = `patient`.`pid`)
    INNER JOIN `invoice` 
        ON (`invoice`.`lps_lpsid` = `lps`.`lpsid`) where invoice.companyinvoice='1' ORDER BY `invoice`.`iid` DESC");
                foreach ($result as $data) {
                    ?>
                    <tr>
                        <td><?php echo $data->iid; ?></td>
                        <td><?php echo $data->fname . " " . $data->lname; ?></td>
                        <td><?php echo $data->date; ?></td>
                        <td><?php echo $data->totaltests; ?></td>
                        <td><?php echo $data->gtotal; ?>.00</td>
                        <td><?php echo $data->cashier; ?></td>
                        <td><?php echo $data->pmethod; ?></td>
                        <td><?php echo $data->status; ?></td>
                        <td><input type="button" name="view"  class="btn" id="<?php echo $data->iid; ?>" value="View More" onclick="viewMore(this.id);"></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</blockquote>

@stop
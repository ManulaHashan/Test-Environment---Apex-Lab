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
        if ($('#selectediid').val() !== "" && $('#selectediid').val() !== "undefined") {
            $('#customer').val($('#selectediid').val());
            $("#discount").prop("disabled", false);
            $("#qty").prop("disabled", false);
        } else if ($('#selectediid').val().trim() === "undefined") {
            $('#customer').val(0);
        }
    });
    function setCurrentTime() {
        var time = new Date();
        time = time.toLocaleString('en-US', {hour: 'numeric', minute: 'numeric', hour12: true});
        document.getElementById('ptime').value = time;
    }
</script>
<script type="text/javascript">
    var Tests = [];
    var IssueDates = [];
    var IssueTimes = [];
    function getUnitPrice(tid) {
        var urll=null;
        if($('#update').val()!==null){
            urll="getUnitPrice";
        }else{
           urll="getUnitPrice"; 
        }
        
        $.ajax({
            type: 'POST',
            url: urll,
            data: {'testid': tid, '_token': $('input[name=_token]').val()},
            success: function (data) {
                data = JSON.parse(data);
                $('#uprice').val(data[0].price + ".00");
                $('#discount').val(data[0].price + ".00");
                $("#discount").prop("disabled", false);
                $("#qty").prop("disabled", false);
                $("#qty").val(1);
                $("#tcost").val($('#discount').val());
            }
        });
    }
    function getCustomers() {
        $.ajax({
            type: 'POST',
            url: "getCustomers",
            data: {'_token': $('input[name=_token]').val()},
            success: function (data) {
                data = JSON.parse(data);
            }
        });
    }
    function validateDiscount(discount) {
        $("#qty").val("0");
        $("#tcost").val("00.00");
        var uprice = parseFloat($('#uprice').val());
        var dis = parseFloat(discount);
        if (dis > uprice) {
            $('#discount').val($('#uprice').val());
        }
    }

    function resetFormFields() {
        $('#test option')[0].selected = true;
        var uprice = $('#uprice').val("00.00");
        var qty = $('#qty').val("0");
        var discount = $('#discount').val("00.00");
        var total = $('#tcost').val("00.00");
    }

    function calculateCost(qty) {
        var discount = $('#discount').val();
        $("#tcost").val((discount * qty) + ".00");
    }

    function addRow() {
        var s_1 = $('#test').val();
        var test = document.getElementById('test').options[document.getElementById('test').selectedIndex].text;
        var rows = document.getElementById("invoicetable").getElementsByTagName("tbody")[0].getElementsByTagName("tr").length;

        if ($('#test').val() != 0 && $('#uprice').val() != null && $('#qty').val() != null && $('#discount').val() != null && $('#tcost').val() != null && $('#pdate').val() != null && $('#ptime').val() != null) {
            if (rows == 0) {
                md_addrow();
            } else {
                var arr = $('#invoicetbody tr').find('td:first').map(function () {
                    return $(this).find('input[type=hidden]').val();
                }).get();
                for (var i = 0; i <= arr.length; i++) {
                    if (arr[i] == $('#test').val()) {
                        var qty = $("#trid" + arr[i]).find('td:nth-child(3)').text();
                        var up = $("#trid" + arr[i]).find('td:nth-child(4)').text();
                        var dataset = arr[i] + "+" + qty + "+" + up;
                        var arr = dataset.split("+");
                        alert("dataset=" + dataset);
                        var testID = arr[0];
                        var Qty = arr[1];
                        var uPrice = arr[2];
                        $("#trid" + testID).remove();

                        $('#lbltotaltest').text(($('#lbltotaltest').text()) - 1);
                        $('#lbltotaltestqty').text(($('#lbltotaltestqty').text()) - (Qty));
                        $('#lblgrandtotal').text(($('#lblgrandtotal').text()) - (uPrice * Qty));
                        break;
                    }
                }
                md_addrow();
            }
        } else {
            alert('Please enter valid data to Add Invoice');
        }
    }
    function validateInvoiceData() {
        if ($('#test').val() != "0" && $('#uprice').val() != null && $('#qty').val() != null && $('#discount').val() != null && $('#tcost').val() != null && $('#pdate').val() != null && $('#ptime').val() != null) {
            return true;
        } else {
            return false;
        }
    }
    function getTableTestColumnData() {
        var arr = $('#invoicetbody tr').find('td:first').map(function () {
            return $(this).find('input[type=hidden]').val();
        }).get();
        console.log(arr);
        for (var i = 0; i <= arr.length; i++) {
            if (arr[i] == $('#test').val()) {
                var qty = $("#trid" + arr[i]).find('td:nth-child(3)').text();
                var up = $("#trid" + arr[i]).find('td:nth-child(4)').text();
                var dataset = arr[i] + "+" + qty + "+" + up;
                removeDataRow(dataset);
                break;
            }
        }
    }
    function md_addrow() {
        var s_1 = $('#test').val();
        var test = document.getElementById('test').options[document.getElementById('test').selectedIndex].text;

        var s_2 = $('#uprice').val();
        var s_3 = $('#qty').val();
        var s_4 = $('#discount').val();
        var s_5 = $('#tcost').val();
        var s_6 = $('#pdate').val();
        var s_7 = $('#ptime').val();

        var table = document.getElementById("invoicetbody");
        var st_row = "<tr id='trid" + s_1 + "'><td><input type='hidden' id='testid' value='" + s_1 + "'>" + test + "</td><td>" + s_2 + "</td><td class='iqty'>" + s_3 + "</td><td>" + s_4 + "</td><td class='isubtotal'>" + s_5 + "</td><td><input type='date' name='date' id='pdate' class='input-text' style='width: 125px;' value=" + s_6 + "><input type='hidden' id='hideDate' value=" + s_6 + "></td><td><input type='hidden' value=" + s_7 + "><input type='text' name='time' id='ptime' class='input-text' style='width: 125px;' value=" + s_7 + "></td><td><img id=" + s_1 + "+" + s_3 + "+" + s_4 + " src='images/del.png' width='15' height='15' onclick='removeDataRow(id);' id='btnremove'></td><td><img id=" + s_1 + "+" + s_2 + "+" + s_3 + "+" + s_4 + "+" + s_5 + "+" + s_6 + "+" + s_7 + " src='images/update.png' height='15' width='15' id='btnupdate' onclick='updateDataRow(id);'></td></tr>";

        table.innerHTML = table.innerHTML + st_row;

        calculateTestCount();
        calculateTestQtyCount();
        calculateGrandTotal();
        resetFormFields();
    }
    function md_update() {
        var myJSON = {};
        var obj = [];
        var trcount = 1;
        $('#invoicetbody tr').each(function () {

            var m = {};
            var count = 1;
//            $(this).find('td:not(:last)').each(function () {
            $(this).find('td').each(function () {

                if (count === 1) {
                    m['s_1'] = $(this).find('input[type=hidden]').val();
                }
                if (count === 2) {
                    m['s_2'] = $(this).html();
                }
                if (count === 3) {
                    m['s_3'] = $(this).html();
                }
                if (count === 4) {
                    m['s_4'] = $(this).html();
                }
                if (count === 5) {
                    m['s_5'] = $(this).html();
                }
                if (count === 6) {
                    m['s_6'] = $(this).find('input[type=date]').val();
                }
                if (count === 7) {

                    m['s_7'] = $(this).find('input[type=text]').val();
                }
                count++;
            });

            obj.push({[trcount]: m});
            trcount++;

        });
        var myJSONnew = JSON.stringify(obj);
        console.log(myJSONnew);
        ajax_senditUpdate(myJSONnew);
    }
    function md_save() {
        var myJSON = {};
        var obj = [];
        var trcount = 1;
        $('#invoicetbody tr').each(function () {

            var m = {};
            var count = 1;
//            $(this).find('td:not(:last)').each(function () {
            $(this).find('td').each(function () {

                if (count === 1) {
                    m['s_1'] = $(this).find('input[type=hidden]').val();
                }
                if (count === 2) {
                    m['s_2'] = $(this).html();
                }
                if (count === 3) {
                    m['s_3'] = $(this).html();
                }
                if (count === 4) {
                    m['s_4'] = $(this).html();
                }
                if (count === 5) {
                    m['s_5'] = $(this).html();
                }
                if (count === 6) {
                    m['s_6'] = $(this).find('input[type=date]').val();
                }
                if (count === 7) {

                    m['s_7'] = $(this).find('input[type=text]').val();
                }
                count++;
            });

            obj.push({[trcount]: m});
            trcount++;

        });
        var myJSONnew = JSON.stringify(obj);
        ajax_sendit(myJSONnew);
    }
    function ajax_senditUpdate(myJSONnew) {
        var total_tests = $('#lbltotaltest').text();
        var total_qty = $('#lbltotaltestqty').text();
        var total_cost = $('#lblgrandtotal').text();
        var company = $('#customer').val();
        var iid = $('#hideiid').val();
        alert(iid);
        var rowCount = $('#invoicetbody tr').length;
        if (total_tests !== null && total_qty !== null && total_cost !== null && company !== null && iid !== null && rowCount !== 0 && rowCount !== "" && rowCount !== null) {
            $.ajax({
                type: 'POST',
                url: "../updateInvoice",
                data: {'iid': iid, 'total_tests': total_tests, 'total_qty': total_qty, 'total_cost': total_cost, 'company': company, 'result': myJSONnew, '_token': $('input[name=_token]').val()},
                success: function (data) {
                    console.log(data);
//                    if (data.trim() === 1) {
//                        alert("Invoice has been successfully updated");
//                    } else {
//                        alert(data.trim());
//                    }
                }
            });
        }
    }
    function calls() {
        alert(document.getElementById('invoiceContent').innerHTML);
    }
    function ajax_sendit(myJSONnew) {
        var total_tests = $('#lbltotaltest').text();
        var total_qty = $('#lbltotaltestqty').text();
        var total_cost = $('#lblgrandtotal').text();
        var company = $('#customer').val();
        if (total_tests != null && total_tests != "" && total_qty != null && total_qty != "" && total_cost != null && total_tests != "" && company != "0" && company != null && company != "") {
            $.ajax({
                type: 'POST',
                url: "saveInvoice",
                data: {'total_tests': total_tests, 'total_qty': total_qty, 'total_cost': total_cost, 'company': company, 'result': myJSONnew, '_token': $('input[name=_token]').val()},
                success: function (data) {
                    alert(data)
                    var arr = data.trim().split("+");
                    alert(arr[0]);
                    alert(arr[1]);
                    document.getElementById('invoiceContent').innerHTML = arr[1];
//                    $('#invoiceContent').innerHTML = arr[1];
//                    printReport(arr[0].trim());
                }
            });
        } else {
            alert("Please enter valid data to Save");
        }


    }
    function printReport(data) {
        alert(data);
        var rows = document.getElementById("invoicetable").getElementsByTagName("tbody")[0].getElementsByTagName("tr").length;
        if (rows > 0) {
            var win = window.open("invoiceReport/" + data, '_blank');
            win.print();
            setTimeout(function () {
                //win.close();
            }, 3000);
        } else {
            alert("Data not found,Please add invoice Data to Save");
        }
    }
    function printAddedReport(data) {
        alert(data);
        var rows = document.getElementById("invoicetable").getElementsByTagName("tbody")[0].getElementsByTagName("tr").length;
        if (rows > 0) {
            var win = window.open("../invoiceReport/" + data, '_blank');
            win.print();
            setTimeout(function () {
                //win.close();
            }, 3000);
        } else {
            alert("Data not found,Please add invoice Data to Save");
        }
    }

    function updateDataRow(dataset) {

        var arr = dataset.split("+");
        var testID = arr[0];
        var uPrice = arr[1];
        var Qty = arr[2];
        var dup = arr[3];
        var tot = arr[4];
        var issuDate = arr[5];
        var issueTime = arr[6];

        alert(testID + " " + uPrice + " " + Qty + " " + dup + " " + tot + " " + issuDate + " " + issueTime);

        $("#test").val(testID);
        $('#uprice').val(uPrice);
        $('#discount').val(dup);
        $('#qty').val(Qty);
        $('#tcost').val(tot);
        $('#pdate').val(issuDate);
        $('#ptime').val(issueTime);
//        $("#" + id).closest('tr').remove();
    }

    function removeDataRow(dataset) {
        var dialogmsg = confirm("Do you want to Delete Test");
        if (dialogmsg == true) {
            var arr = dataset.split("+");
            alert("dataset=" + dataset);
            var testID = arr[0];
            var Qty = arr[1];
            var uPrice = arr[2];
            $("#trid" + testID).remove();

            $('#lbltotaltest').text(($('#lbltotaltest').text()) - 1);
            $('#lbltotaltestqty').text(($('#lbltotaltestqty').text()) - (Qty));
            $('#lblgrandtotal').text(($('#lblgrandtotal').text()) - (uPrice * Qty));
        } else {
            return false;
        }

    }

    function searchInvoice() {
        window.open("searchInvoice", '_blank');
    }

    function calculateTestCount() {
        var testcount = $("#invoicetable").find("tr").not("thead tr").length;
        document.getElementById('lbltotaltest').innerHTML = testcount;
    }
    function calculateTestQtyCount() {
        var sum = 0;
        $(".iqty").each(function () {
            var value = $(this).text();
            // add only if the value is number
            if (!isNaN(value) && value.length != 0) {
                sum += parseFloat(value);
            }
        });
        document.getElementById('lbltotaltestqty').innerHTML = sum;
    }
    function calculateGrandTotal() {
        var sum = 0;
        $(".isubtotal").each(function () {

            var value = $(this).text();
            // add only if the value is number
            if (!isNaN(value) && value.length != 0) {
                sum += parseFloat(value);
            }
        });
        document.getElementById('lblgrandtotal').innerHTML = sum + ".00";
    }
    function html2json() {
        var json = '{';
        var otArr = [];
        var tbl2 = $('#invoicetbody tr').each(function (i) {
            x = $(this).children();
            var itArr = [];
            x.each(function () {
                if ($(this).text() == "") {
                    itArr.push('"' + $(this).find('input[type=hidden]').val() + '"');

                } else {
                    itArr.push('"' + $(this).text() + '"');
                }
            });
            otArr.push('"' + i + '": [' + itArr.join(',') + ']');
        })
        json += otArr.join(",") + '}'

        return(json);
//        alert(json);
    }
    function removeInvoice(id) {
        alert(id);
        $.ajax({
            type: 'POST',
            url: "../removeInvoice",
            data: {'iid': id, '_token': $('input[name=_token]').val()},
            success: function (data) {
                alert(data.trim());
            }
        });
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
<table>
    <tr valign="top">
        <td style="width: 280px;">
            <blockquote style="width: 300px;">
                <h3 class="pageheading">Add Test Details</h3>
                <br/>
                <br/>
                <table class="tbltest">
                    <tr>
                        <td>Test :</td>
                        <td>
                            <select id="test" class="select-basic" style="width: 150px;" class="select-basic" onchange="getUnitPrice(value);">
                                <option value="0">Select Test</option>
                                <?php
                                $refferenceResult = DB::select("select * from Lab_has_test where Lab_lid = '".$_SESSION['lid']."' group by test_tid");
                                foreach ($refferenceResult as $result) {
                                    ?>
                                    <option value="<?php echo $result->test_tid; ?>" id="<?php echo $result->test_tid; ?>"><?php echo $result->reportname; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Unit Price : </td>
                        <td><input type="text" name="uprice" id="uprice" class="input-text" style="width: 125px;" disabled value="00.00"></td>
                    </tr>
                    <tr>
                        <td>Discount : </td>
                        <td>
                            <input type="text" name="discount" id="discount" class="input-text" style="width: 125px;" onkeyup="validateDiscount(this.value);" value="00.00">
                        </td>
                    </tr>
                    <tr>
                        <td>Quantity : </td>
                        <td><input type="number" name="qty" id="qty" class="input-text" style="width: 125px;" min="1" onkeypress="return event.charCode >= 48 && event.charCode <= 57" max="10000" onkeyup="calculateCost(this.value);" onchange="calculateCost(this.value);" value="0"></td>
                    </tr>
                    <tr>
                        <td>Total Cost : </td>
                        <td><input type="text" name="tcost" id="tcost" class="input-text" style="width: 125px;" disabled value="00.00"></td>
                    </tr>
                    <tr>
                        <td>Report Issuing Date : </td>
                        <td><input type="date" name="date" id="pdate" class="input-text" style="width: 125px;" value="<?php echo date("Y-m-d"); ?>"></td>
                    </tr>
                    <tr>
                        <td>Report Issuing Time : </td>
                        <td><input type="text" name="time" id="ptime"  class="input-text" style="width: 125px;" value="<?php echo date("H:i"); ?>"></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <!--<input type="button" name="Update" class="btn" id="search" value="Update" style="margin-right: 10px; margin-left: 0px; width: 100px;float: left;">-->
                            <input type="button" name="add" class="btn" id="Add" value="Add" style="margin-right: 0px; margin-left: 0px; width: 100px;float: left;" onclick="addRow();">
                            <input type="button" name="search" class="btn" id="Search" value="Search" style="margin-right: 0px; margin-left: 0px; width: 100px;float: left;" onclick="searchInvoice();">
                        </td>
                    </tr>
                </table>
            </blockquote>
        </td>
        <td style="width:800px;">
            <blockquote>
                <h3 class="pageheading">Invoice</h3>
                <?php if (isset($iid)) { ?>
                    <input type="hidden" id="hideiid" value="{{$iid or ''}}">
                <?php } ?>
                <?php
                if (isset($iid)) {
                    $rr = DB::select("SELECT
    `user`.`uid` AS uid
    , `user`.`fname`
    , `user`.`lname`
FROM
    `patient`
    INNER JOIN `user` 
        ON (`patient`.`user_uid` = `user`.`uid`)
    INNER JOIN `lps` 
        ON (`lps`.`patient_pid` = `patient`.`pid`)
    INNER JOIN `invoice` 
        ON (`invoice`.`lps_lpsid` = `lps`.`lpsid`)
    INNER JOIN `invoice_has_test` 
        ON (`invoice_has_test`.`iid` = `invoice`.`iid`) WHERE `invoice`.`iid`='$iid' GROUP BY `invoice`.`iid`");
                    foreach ($rr as $r) {
                        $uid = $r->uid;
                    }
                    ?>
                    <input type="hidden" id="selectediid" value="{{ $uid or '' }}"/>

                <?php } ?>

                <?php
                if (isset($iid)) {
                    $qty = 0;
                    $invoiceResult = DB::select("select a.*,b.amount from invoice a,invoice_has_test b where a.iid=b.iid and a.iid = " . $iid . "");
                    foreach ($invoiceResult as $result) {
//                         number_format
                        $gtot = ($result->gtotal);
                        $tCount = $result->total;
                        $qty += $result->amount;
                        ?>
                        <?php
                    }
                }
                ?>
                <br/>
                <br/>
                <div>
                    <table class="table-row-bodered" id="invoicetable" width="50%">
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
                            if (isset($iid)) {
                                $testResult = DB::select("select a.*, b.name, b.tid from invoice_has_test a, test b where a.tid = b.tid and iid = " . $iid . "");
                                foreach ($testResult as $resultTest) {
                                    $s_1 = $resultTest->tid;
                                    $test = $resultTest->name;
                                    $s_2 = $resultTest->unit_price;
                                    $s_3 = $resultTest->amount;
                                    $s_4 = $resultTest->discounted_up;
                                    $s_5 = $result->gtotal;
                                    $s_6 = $resultTest->issue_date;
                                    $s_7 = $resultTest->issue_time;
                                    echo "<tr id='trid" . $s_1 . "'><td><input type='hidden' id='testid' value='" . $s_1 . "'>" . $test . "</td><td>" . $s_2 . "</td><td class='iqty'>" . $s_3 . "</td><td>" . $s_4 . "</td><td class='isubtotal'>" . $s_5 . "</td><td><input type='date' name='date' id='pdate' class='input-text' style='width: 125px;' value=" . $s_6 . "><input type='hidden' id='hideDate' value=" . $s_6 . "></td><td><input type='hidden' value=" . $s_7 . "><input type='text' name='time' id='ptime' class='input-text' style='width: 125px;' value=" . $s_7 . "></td><td><img id=" . $s_1 . "+" . $s_3 . "+" . $s_4 . " src='../images/del.png' width='15' height='15' onclick='removeDataRow(id);' id='btnremove'></td><td><img id=" . $s_1 . "+" . $s_2 . "+" . $s_3 . "+" . $s_4 . "+" . $s_5 . "+" . $s_6 . "+" . $s_7 . " src='../images/update.png' height='15' width='15' id='btnupdate' onclick='updateDataRow(id);'></td></tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                    <hr>
                    <table width="100%">
                        <tr>
                            <td class="tdright">
                                <table class="tbltdright" width="100%">
                                    <tr>
                                        <td>Total Tests:</td>
                                        <td><label class="form-label" id="lbltotaltest" style="width: 300px;padding-right: 10px;">{{ $tCount or '' }}</label></td>
                                    </tr>
                                    <tr>
                                        <td>Total Tests Qty:</td>
                                        <td><label class="form-label" id="lbltotaltestqty" style="padding-right: 10px;">{{ $qty or ''}}</label></td>
                                    </tr>
                                </table>
                            </td>
                            <td class="tdleft">
                                <table class="tbltdleft" width="100%">
                                    <tr>
                                        <td></td>
                                        <td>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Grand Total:</td>
                                        <td>Rs:<label class="form-label" id="lblgrandtotal" style="padding-right: 10px;">{{ $gtot or '' }}</label></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <hr>
                    <table class="tblcustomer">
                        <tr>
                            <td>Customer :</td>
                            <td>
                                <select id="customer" class="select-basic" style="width: 300px;" class="select-basic">
                                    <option value="0">Select Customer</option>
                                    <?php
                                    $refferenceResult = DB::select("select * from user where usertype_idusertype='3'");
                                    foreach ($refferenceResult as $result) {
                                        ?>
                                        <option value="<?php echo $result->uid; ?>"><?php echo $result->fname . " " . $result->lname; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>

                        </tr>
                    </table>
                    <hr>
                    <table>
                        <tr>
                            <?php if (!isset($iid)) { ?>
                                <td>
                                    <input type="button" name="save" class="btn" id="search" value="Save" style="margin-right: 0px; margin-left: 0px; width: 100px;float: left;" onclick="md_save();">
                                </td>
                            <?php }
                            ?>
                            <?php if (isset($iid)) { ?>
                                <td>
                                    <input type="button" name="update" class="btn" id="update" value="Update" style="margin-right: 0px; margin-left: 0px; width: 100px;float: left;" onclick="md_update();">
                                </td>
                                <td>
                                    <input type="button" name="remove" class="btn" id="remove<?php echo $iid; ?>" value="Remove" style="margin-right: 0px; margin-left: 0px; width: 100px;float: left;" onclick="removeInvoice(this.id);">
                                </td>
                                <td>
                                    <input type="button" name="print" class="btn" id="print" value="Print" style="margin-right: 0px; margin-left: 0px; width: 100px;float: left;" onclick="printAddedReport($('#hideiid').val());">
                                </td>

                            <?php } ?>
                        </tr>
                    </table>
                    <hr>
                    <h5 class="pageheading">Send Invoice E-mail</h5>
                    <br>
                    <?php
                    $message = "Dear Sir/Madum;<br><br>"
                            . "This Invoice is Regarding to Laboratory Test for " . $result->fname . " " . $result->lname . " by Venus Hospital (Pvt) Limited<br><br>"
                            . "<div id='invoiceContent' style='display:none;'></div>";
                    echo $message;
                    ?>
                    <table>
                        <tr>
                            <td><input type="text" name="email" id="email" placeholder="Ex : sample@gmail.com" class="input-text" style="width: 350px;"></td>
                            <td><input type="button" value="Send Mail" id="btnsendmail" class="btn" onclick="calls();"></td>
                        </tr>
                    </table>
            </blockquote>

        </td>
    </tr>
</table>


@stop
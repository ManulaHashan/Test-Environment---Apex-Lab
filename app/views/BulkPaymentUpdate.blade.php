<?php ?>
@extends('Templates/WiTemplate')


@section('title')
Bulk Payment Update
@stop

@section('head')

<script src="{{ asset('JS/ReportCalculations.js') }}"></script>


<script>
    $(document).ready(function() {

        loadRecordToTable();


        $('#searchBtn').click(function() {
            loadRecordToTable();
            calculateTotalDue();
        });

    });




    document.addEventListener("DOMContentLoaded", function() {
        const today = new Date().toISOString().split('T')[0];
        $('#date_from, #date_to, #paid_date').val(today);
    });



    function loadRecordToTable() {
        var dateFrom = $('#date_from').val();
        var dateTo = $('#date_to').val();
        var branch = $('#labBranchDropdown').val() || '%';
        var reference = $('#refNameDropdown').val() || '%';


        $.ajax({
            type: "GET",
            url: "getAllSamples",
            data: {
                date_from: dateFrom,
                date_to: dateTo,
                branch: branch,
                reference: reference
            },
            success: function(response) {
                $('#record_tbl').html(response.html);
            },
            error: function(xhr, status, error) {
                alert('Error: ' + xhr.status + ' - ' + xhr.statusText + '\nDetails: ' + xhr.responseText);
                console.error('Error details:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });
            }
        });
    }

    function TotalDueLabel() {
        var totalDue = 0;

        $('.select-due:checked').each(function() {
            var due = parseFloat($(this).data('due'));
            if (!isNaN(due)) {
                totalDue += due;
            }
        });
    }

    function calculateTotalDue() {
        var totalDue = 0;
        table.rows().every(function() {
            var data = this.data();
            var due = parseFloat(data[6]) || 0; // Assume 'Due' is in the 3rd column (index 2)
            totalDue += due;
        });

        // Update the label with the total due amount
        $('#totalDueLabel').text('Total Due: ' + totalDue.toFixed(6));
    }

    $('#searchBtn').click(function() {
        loadRecordToTable();
    });





    function paymentMethodCheckSelect(selectedCheckboxID) {

        const checkboxes = ['cash', 'card', 'cheque'];


        checkboxes.forEach(id => {
            const checkbox = document.getElementById(id);
            if (id !== selectedCheckboxID) {
                checkbox.checked = false;
            }
        });
    }


    $(document).ready(function() {
        $('#record_tbl').on('change', '.select-due', function() {
            var totalDue = 0;


            $('.select-due:checked').each(function() {
                var due = parseFloat($(this).data('due'));
                if (!isNaN(due)) {
                    totalDue += due;
                }
            });

            $('#dueSum').text(totalDue.toFixed(2));
        });
    });




    let selectedInvoice = [];

    function UpdatePayments() {
        selectedInvoice = [];
        var paymentDate = $('#paid_date').val();

        document.querySelectorAll('.select-due:checked').forEach((checkbox) => {
            selectedInvoice.push(checkbox.value);
        });

        var paymentMethod = '';
        if ($('#cash').is(':checked')) {
            paymentMethod = '1';
        } else if ($('#card').is(':checked')) {
            paymentMethod = '2';
        } else if ($('#cheque').is(':checked')) {
            paymentMethod = '3';
        }


        $.ajax({
            type: "POST",
            url: "/updatePayments",
            data: {
                selectedInvoice: selectedInvoice,
                payment_method: paymentMethod,
                paymentDate: paymentDate

            },
            success: function(response) {
                //alert(response);
                if (response.success && response.error === "updated") {
                    alert('Payment updated successfully!');
                    loadRecordToTable();
                } else {
                    alert('Failed to update Payment.');
                }
            },
            error: function(xhr, status, error) {
                alert('Error: ' + xhr.status + ' - ' + xhr.statusText);
            }
        });
    }
</script>



<style>
    /* Add this to your CSS file or inline styles */

    .container {
        width: 100%;
        height: 100vh;
        display: flex;
        flex-direction: row;
    }

    .card {
        width: 100%;
        margin: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        background-color: #fff;
        border: 1px solid #ccc;

    }

    .card-body {
        padding: 5px;
    }

    .card-title {
        font-size: 18px;
        font-weight: bold;
    }

    .card-text {
        font-size: 14px;
    }
</style>
@stop

@section('body')


<h2 class="pageheading" style="margin-top: 5px;"> Bulk Payment Update
</h2>
<div class="container" style="margin-top: 20px;">

    <div class="card" style="height: 958px;">

        <div class="card-body">

            <div style="width: 1000px; display: flex;">

                <div style="flex: 1; padding: 10px; margin-right: 5px;">

                    <div style="display: flex; align-items: center; margin-bottom: 10px; margin-top: 10px;">
                        <label style="width: 90px;font-size: 18px;">Date From:</label>
                        <input type="date" name=" date_from" class="input-text" id="date_from"
                            style="width: 130px;font-size: 18px;">
                        <label style="width: 70px;font-size: 18px;margin-left: 15px; ">Date To:</label>
                        <input type="date" name=" date_to" class="input-text" id="date_to"
                            style="width: 130px;font-size: 18px;">
                        <label style="width: 120px;font-size: 18px; margin-left: 15px;">Branch Name &nbsp;:</label>
                        <select name="labbranch" style="width: 230px" class="input-text" id="labBranchDropdown">
                            <option value="%"> All</option>
                            <?php

                            $Result = DB::select("Select name, code FROM labbranches WHERE Lab_lid = '" . $_SESSION['lid'] . "' ORDER BY name ASC");

                            foreach ($Result as $res) {
                                $branchName = $res->name;
                                $code = $res->code;

                                if (isset($labbranch) && $labbranch == $bid) {
                            ?>
                                    <option value="{{ $code }}" selected="selected">{{ $branchName }}</option>
                                <?php
                                } else {
                                ?>
                                    <option value="{{ $code }}">{{ $branchName }}</option>
                            <?php
                                }
                            }

                            // If no branch is selected, set the default value as '%'
                            if (!isset($labbranch)) {
                                $labbranch = "%";
                            }
                            ?>

                        </select>
                        <label style="width: 80px;font-size: 18px;margin-left:15px">Reference</label>
                        <select name="refname" style="width: 230px" class="input-text" id="refNameDropdown">
                            <option value="%">All</option>
                            <?php
                            $Result = DB::select("select idref, name FROM refference where lid = '" . $_SESSION['lid'] . "' order by name ASC ");
                            foreach ($Result as $res) {
                                $Refid = $res->idref;
                                $Refname = $res->name;

                                if (isset($refname) && $refname == $Refname) {
                            ?>
                                    <option value="{{ $Refname }}" selected="selected">{{ $Refname }}</option>
                                <?php
                                } else {
                                ?>
                                    <option value="{{ $Refname }}">{{ $Refname }}</option>
                            <?php
                                }
                            }

                            if (!isset($refname)) {
                                $refname = "%";
                            }
                            ?>
                        </select>
                        <input type="button" style="color:green; font-size: 20px; margin-left: 15px;" class="btn" id="searchBtn" value="Search" onclick="">
                    </div>

                    <div style="display: flex; justify-content: flex-end; align-items: center; margin-bottom: 10px; margin-top: 10px;">
                        <input type="checkbox" name="cal_dis" class="input-check" id="cal_dis" style="margin-right: 5px;">
                        <label for="cal_dis" style="font-size: 18px;">Auto Calculate Discount</label>
                    </div>

                </div>

            </div>

            <div style="flex: 1; padding: 10px; border: 2px #8e7ef7 solid; border-radius: 10px;">
                <label for="" style="font-size: 20px; "><b><i><u>Sample Details</u></i></b></label>
                <div class="pageTableScope" style="height: 450px; margin-top: 10px;">
                    <table style="font-family: Futura, 'Trebuchet MS', Arial, 
                    sans-serif; font-size: 13pt;" id="sampledataTable" width="100%" border="0" cellspacing="2" cellpadding="0">
                        <thead>
                            <tr class="viewTHead">
                                <td align="center" class="fieldText" style="width: 50px;">Date</td>
                                <td align="center" class="fieldText" style="width: 30px;">Invoice No</td>
                                <td align="center" class="fieldText" style="width: 30px;">Sample No</td>
                                <td align="center" class="fieldText" style="width: 30px;">Total(Rs)</td>
                                <td align="center" class="fieldText" style="width: 30px;">Grand Total(Rs)</td>
                                <td align="center" class="fieldText" style="width: 30px;">Paid(Rs)</td>
                                <td align="center" class="fieldText" style="width: 30px;">Due(Rs)</td>
                                <td align="center" class="fieldText" style="width: 30px;">Select</td>
                            </tr>
                        </thead>
                        <tbody id="record_tbl">
                            <!-- Dynamic rows will be inserted here via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <div style="display: flex; align-items: center; margin-bottom: 10px; margin-top: 10px;">
            <label style="width: 250px;font-size: 18px;margin-left: 15px; ">Total Amount(Selected) Rs:</label>
            <label style="width: 70px;font-size: 35px; color:rgb(18, 117, 63); margin-left: 15px;" id="dueSum">0000.00</label>
        </div>
        <div style="display: flex; align-items: center; margin-bottom: 10px; margin-top: 10px;">
            <label style="width: 250px;font-size: 18px;margin-left: 15px; ">Total Discount Rs:</label>
            <label style="width: 70px;font-size: 35px;margin-left: 15px; color:rgb(18, 117, 63);">0000.00</label>
        </div>
        <div style="display: flex; align-items: center; margin-bottom: 10px; margin-top: 10px;">
            <label style="width: 250px;font-size: 18px;margin-left: 15px; ">Total Due Rs:</label>
            <label style="width: 70px;font-size: 35px;margin-left: 15px; color:rgb(18, 117, 63);" id="totalDue">0000.00</label>
        </div>
        <div style="display: flex; align-items: center; margin-bottom: 10px; margin-top: 10px;">
            <label style="width: 250px;font-size: 18px;margin-left: 15px;">Paid On:</label>
            <input type="date" name=" paid_date" class="input-text" id="paid_date"
                style="width: 130px;font-size: 18px; height: 20px;">
        </div>
        <div style="display: flex; align-items: center; margin-bottom: 10px; margin-top: 10px;">
            <label style="width: 250px; font-size: 18px; margin-left: 15px;">Payment Method</label>

            <input type="checkbox" name="cash" class="input-check" id="cash" style="margin-right: 5px; width: 20px; height: 20px;" onclick="paymentMethodCheckSelect(this.id)" checked>
            <label for="cash" style="font-size: 18px; color:rgb(16, 88, 197); ">Cash</label>

            <input type="checkbox" name="card" class="input-check" id="card" style="margin-right: 5px;width: 20px; height: 20px;margin-left: 20px;" onclick="paymentMethodCheckSelect(this.id)">
            <label for="card" style="font-size: 18px; color:rgb(16, 88, 197); ">Card</label>

            <input type="checkbox" name="cheque" class="input-check" id="cheque" style="margin-right: 5px;width: 20px; height: 20px;margin-left: 20px;" onclick="paymentMethodCheckSelect(this.id)">
            <label for="cheque" style="font-size: 18px; color:rgb(16, 88, 197); ">Cheque</label>

            <input type="button" style="color:blue; font-size: 20px; margin-left: 15px;" class="btn" id="updateBtn" value="Update Payment" onclick="UpdatePayments()">

        </div>



    </div>

</div>





@stop
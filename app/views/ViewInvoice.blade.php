<!-- **********~~~~~~~~~~~~~Manula Hashan's Devolopment~~~~~~~~~~~~~********** -->


<?php ?>
@extends('Templates/WiTemplate')


@section('title')
View Invices
@stop

@section('head')

<script src="{{ asset('JS/ReportCalculations.js') }}"></script>

<script>
    $(document).ready(function() {
        loadRecordToTable();
    });

    // Function to load data into the table
    function loadRecordToTable() {

        $.ajax({
            type: "GET",
            url: "getAllInvoices",
            success: function(tbl_records) {
                // alert('Successfully loaded data.');
                $('#inv_record_tbl').html(tbl_records);
            },
            error: function(xhr, status, error) {
                alert('Error: ' + xhr.status + ' - ' + xhr.statusText + '\n' + 'Details: ' + xhr.responseText);
                console.error('Error details:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });
            }

        });
    }








    //*************************************************************************************************
    //*************************************************************************************************
    //*************************************************************************************************
    //*************************************************************************************************
    //*************************************************************************************************
    //*************************************************************************************************
    //*************************************************************************************************
    //*************************************************************************************************
    //*************************************************************************************************
    //*************************************************************************************************





</script>



<style>
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

    .warning-container {
        display: flex;
        align-items: center;
        margin: 5px 0;
        padding: 5px;
        border: 1px solid #f5c2c2;
        background-color: #f8d7da;
        border-radius: 4px;
        font-size: 16px;
        color: #842029;
        width: 100%;
    }

    .warning-icon {
        font-size: 20px;
        margin-right: 10px;
        color: #d63333;
    }

    .warning-text {
        font-weight: bold;
    }

    .selected-row {
        background-color: #1977c9 !important;
        /* Light blue color */
    }

    /* //--------------- */
    .suggestion-box {
        position: absolute;
        background: #fff;
        border: 1px solid #ccc;
        max-height: 150px;
        overflow-y: auto;
        width: 210px;
        z-index: 1000;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    }

    .suggestion-item {
        padding: 5px 10px;
        cursor: pointer;
    }

    .suggestion-item:hover {
        background-color: #f0f0f0;
    }
</style>
@stop

@section('body')



<div class="container">
    <div class="card" style="height: 850px; margin-top: 50px; background-color:rgb(222, 222, 223);">
        <div class="card-body" style="display: flex; max-width: 1350px; margin: auto; padding: 20px;">
            <!-- Main Content Area (70%) -->
            <div style="flex: 0 0 100%; padding-right: 20px;">
                <div style="flex: 0 0 70%; padding-right: 20px;">
                </div>
                <div style="display: flex; align-items: center; margin-top: 5px;">
                   
                    <label style="width: 70px;font-size: 18px;margin-left: 15px"><b>Center</b></label>
                    <select name="labbranch" style="width: 250px; height: 30px" class="input-text" id="labBranchDropdown" onchange="loadcurrentSampleNo(); load_test();">
                        <option value="%" data-code="ML" data-maxno="0" data-mainlab="true">Main Lab</option>
                        <?php
                        $Result = DB::select("SELECT name, code, bid FROM labbranches WHERE Lab_lid = '" . $_SESSION['lid'] . "' ORDER BY name ASC");

                        foreach ($Result as $res) {
                            $branchName = $res->name;
                            $branchCode = $res->code;
                            $bid = $res->bid;


                            $displayText = $branchCode . " : " . $branchName;
                        ?>
                            <option value="<?= $bid ?>"><?= $displayText ?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <input type="checkbox" name="edit" id="edit1" class="ref_chkbox" value="1">
                    <label style="font-size: 16px; margin-left: 5px;"><b>With Other Branches</b></label>
                    <label style="font-size: 16px; margin-left: 5px; width: 570px;"></label>
                    <input type="checkbox" name="edit" id="edit2" class="ref_chkbox" value="1">
                    <label style="font-size: 16px; margin-left: 5px;"><b>Due Bills Only</b></label>
                </div>

                <div style="display: flex; align-items: center; margin-top: 5px;">
                   
                    <input type="checkbox" name="edit" id="edit2" class="ref_chkbox" style="margin-bottom: 5px;" value="1" checked>
                    <label style="font-size: 14px; margin-left: 3px; width: 70px;"><b>By Date </b></label>
                    <input type="date" name="ser_date" class="input-text" id="ser_date" style="width: 100px">
                    <label style="font-size: 16px; margin-left: 5px;"><b>Invoice No </b></label>
                    <input type="text" name="lname" class="input-text" id="lname" style="width: 90px">
                    <label style="font-size: 16px; margin-left: 5px;"><b>First Name </b></label>
                    <input type="text" name="lname" class="input-text" id="lname" style="width: 90px">
                    <label style="font-size: 16px; margin-left: 5px;"><b>Last Name </b></label>
                    <input type="text" name="lname" class="input-text" id="lname" style="width: 90px">
                    <label style="font-size: 16px; margin-left: 5px;"><b>Contact </b></label>
                    <input type="text" name="lname" class="input-text" id="lname" style="width: 90px">
                    <label style="font-size: 16px; margin-left: 5px;"><b>Patient Type </b></label>
                    <select type="text" name="type" class="input-text" id="type" style="width: 70px; height: 30px">
                        <option value="In">In</option>
                        <option value="Out">Out</option>
                    </select>
                    <input type="button" style="flex: 0 0 80px; margin-left: 10px;" class="btn" id="ser_btn" value="Search" onclick="">
                </div>
            </div>
        
           
           
        </div>

        <div class="pageTableScope" style="display: flex; height: 350px; margin-top: 10px; width: 100%;">
            <!-- Left Side: Table -->
            <div style="flex: 1; padding-right: 10px;">
                <table style="font-family: Futura, 'Trebuchet MS', Arial, sans-serif; font-size: 13pt;" id="invdataTable" width="100%" border="0" cellspacing="2" cellpadding="0">
                    <tbody>
                        <tr>
                            <td valign="top">
                                <table border="1" style="border-color: #ffffff;" cellpadding="0" cellspacing="0" class="TableWithBorder" width="100%">
                                    <thead>
                                        <tr class="viewTHead">
                                            <td width="12%" class="fieldText" align="center">Sample No</td>
                                            <td width="18%" class="fieldText" align="center">First Name</td>
                                            <td width="18%" class="fieldText" align="center">Last Name</td>
                                            <td width="10%" class="fieldText" align="center">Status</td>
                                            <td width="8%" class="fieldText" align="center">Total Amount</td>
                                            <td width="10%" class="fieldText" align="center">Paid</td>
                                            <td width="10%" class="fieldText" align="center">Due</td>
                                            <td width="10%" class="fieldText" align="center">User</td>
                                        </tr>
                                    </thead>
                                    <tbody id="inv_record_tbl">
                                        <!-- Dynamic content goes here -->
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        
            <!-- Right Side: Additional Content -->
            <div style="flex: 0 0 20%; padding-left: 10px;">
                <h3>Additional Information</h3>
                <p>This section can be used for additional information, links, or any other content you want to display.</p>
            </div>
        </div>
        
        
    </div>

</div>




@stop
<!-- **********~~~~~~~~~~~~~Manula Hashan's Devolopment~~~~~~~~~~~~~********** -->


<?php ?>
@extends('Templates/WiTemplate')


@section('title')
Invoice Payments
@stop

@section('head')

<script src="{{ asset('JS/ReportCalculations.js') }}"></script>

<script>










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
                    <select name="labbranch" style="width: 250px; height: 30px" class="input-text" id="labbranch" onchange="">
                        <option value="%:@" data-code="ML" data-maxno="0" data-mainlab="true">Main Lab</option>
                        <?php
                        $Result = DB::select("SELECT name, code, bid FROM labbranches WHERE Lab_lid = '" . $_SESSION['lid'] . "' ORDER BY name ASC");

                        foreach ($Result as $res) {
                            $branchName = $res->name;
                            $branchCode = $res->code;
                            $bid = $res->bid;


                            $displayText = $branchCode . " : " . $branchName;
                        ?>
                            <option value="<?= $bid . ":" . $branchCode ?>"><?= $displayText ?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <input type="checkbox" name="with_other_branches" id="with_other_branches" class="ref_chkbox" value="0">
                    <label style="font-size: 16px; margin-left: 5px;"><b>With Other Branches</b></label>
                    <label style="font-size: 16px; margin-left: 5px; width: 570px;"></label>
                    <input type="checkbox" name="due_bills_only" id="due_bills_only" class="ref_chkbox" value="0">
                    <label style="font-size: 16px; margin-left: 5px;"><b>Due Bills Only</b></label>
                </div>

                <div style="display: flex; align-items: center; margin-top: 5px;">
                   
                    <input type="checkbox" name="by_date" id="by_date" class="ref_chkbox" style="margin-bottom: 5px;" value="1" checked>
                    <label style="font-size: 14px; margin-left: 3px; width: 70px;"><b>By Date </b></label>
                    <input type="date" name="idate" class="input-text" id="idate" style="width: 100px">
                    <label style="font-size: 16px; margin-left: 5px;"><b>Invoice No </b></label>
                    <input type="text" name="invoice_no" class="input-text" id="invoice_no" style="width: 90px">
                    <label style="font-size: 16px; margin-left: 5px;"><b>First Name </b></label>
                    <input type="text" name="first_name" class="input-text" id="first_name" style="width: 90px">
                    <label style="font-size: 16px; margin-left: 5px;"><b>Last Name </b></label>
                    <input type="text" name="last_name" class="input-text" id="last_name" style="width: 90px">
                    <label style="font-size: 16px; margin-left: 5px;"><b>Contact </b></label>
                    <input type="text" name="contact" class="input-text" id="contact" style="width: 90px">
                    <label style="font-size: 16px; margin-left: 5px;"><b>Patient Type </b></label>
                    <select type="text" name="type" class="input-text" id="type" style="width: 70px; height: 30px">
                        <option value="In">In</option>
                        <option value="Out">Out</option>
                    </select>
                    <input type="button" style="flex: 0 0 80px; margin-left: 10px;" class="btn" id="ser_btn" value="Search" onclick="loadRecordToTable();sampleRecordToTableClear();">
                </div>
            </div>
        
        </div>
        

        <div class="pageTableScope" style="display: flex; height: 350px; margin-top: 10px; width: 100%;">
            <!-- Left Side: Table -->

            <div style="flex: 0 0 30%; padding-left: 10px;">
                <table style="font-family: Futura, 'Trebuchet MS', Arial, sans-serif; font-size: 13pt;" id="sampledataTable" width="100%" border="0" cellspacing="2" cellpadding="0">
                    <tbody>
                        <tr>
                            <td valign="top">
                                <table border="1" style="border-color: #ffffff;" cellpadding="0" cellspacing="0" class="TableWithBorder" width="100%">
                                    <thead>
                                        <tr class="viewTHead">
                                            <td width="12%" class="fieldText" align="center">Sample No</td>
                                            <td width="18%" class="fieldText" align="center">Test</td>
                                        </tr>
                                    </thead>
                                    <tbody id="sample_record_tbl">
                                        <!-- Dynamic content goes here -->
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        
            <!-- Right Side: Additional Content -->
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
        </div>


{{-- ############################################################################################################# --}}


       
    </div>

</div>




@stop
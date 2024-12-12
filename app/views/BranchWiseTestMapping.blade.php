<?php ?>
@extends('Templates/WiTemplate')


@section('title')
Branch Wise Test Mapping
@stop

@section('head')

<script src="{{ asset('JS/ReportCalculations.js') }}"></script>


<script>
    $(document).ready(function() {
        // Initial load of the data when the page is ready
        loadRecordToTable();


        $('#labBranchDropdown').change(function() {
            loadRecordToTable(); 
            searchRecords(); 
        });


        // $('#Ser_name').on('input', function() {
           
        // });


        $('#selectAllCheckbox').change(function() {
            var isChecked = $(this).prop('checked');
            $('#record_tbl .select-test').each(function() {
                $(this).prop('checked', isChecked);
            });
        });
    });



    // ******************Function to load data into the table*********************
    function loadRecordToTable() {
        var labBranchDropdown = $('#labBranchDropdown').val(); 
        var name = $('#Ser_name').val(); 

        $.ajax({
            type: "GET",
            url: "getAllBranchTests", 
            data: {
                labBranchDropdown: labBranchDropdown,
                name: name
            },
            success: function(tbl_records) {
                $('#record_tbl').html(tbl_records); 
                BranchInvoiceCount(); 
            },
            error: function(xhr, status, error) {
                alert('Error: ' + xhr.status + ' - ' + xhr.statusText + '\n' + 'Details: ' + xhr.responseText);
            }
        });
    }

    function BranchInvoiceCount() {
        var rowCount = $('#record_tbl tr').length; 
        $('#invoicecount').text(rowCount); 
    }
    // ******************Function to search the branch data******************

    function searchRecords() {
        var name = $('#Ser_name').val(); 
        var labBranchDropdown = $('#labBranchDropdown').val(); 

        $.ajax({
            type: "GET",
            url: "searchAllBranchTests", 
            data: {
                name: name,
                labBranchDropdown: labBranchDropdown
            },
            success: function(tbl_records) {
                $('#record_tbl').html(tbl_records); 
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


<h2 class="pageheading" style="margin-top: -1px;"> Branch Wise Test Mapping
</h2>
<div class="container">
    <div class="card" style="height: 750px;">
        <div class="card-body">
            <div style="flex: 1; padding: 10px; border: 2px #8e7ef7 solid; margin-right: 5px; border-radius: 10px;">
                <div style="display: flex; align-items: center; margin-bottom: 10px; margin-top: 10px;">
                    <label style="width: 150px;font-size: 18px;">Branch Name &nbsp;:</label>
                    <select name="labbranch" style="width: 273px" class="input-text" id="labBranchDropdown">
                        <option value="%"> Main Lab</option>
                        <?php
                        $Result = DB::select("select name, bid FROM labbranches WHERE Lab_lid = '" . $_SESSION['lid'] . "'");

                        foreach ($Result as $res) {
                            $branchName = $res->name;
                            $bid = $res->bid;

                            if (isset($labbranch) && $labbranch == $bid) {
                        ?>
                                <option value="{{ $bid }}" selected="selected">{{ $branchName }}</option>
                            <?php
                            } else {
                            ?>
                                <option value="{{ $bid }}">{{ $branchName }}</option>
                        <?php
                            }
                        }

                        // If no branch is selected, set the default value as '%'
                        if (!isset($labbranch)) {
                            $labbranch = "%";
                        }
                        ?>

                    </select>

                    <input type="hidden" name="Branch_id" id="Branch_id">
                </div>

                <div style="flex: 1; padding: 10px; border: 2px #8e7ef7 solid; border-radius: 10px;">
                    <b><u><i>Test List</i></u></b><br><br>
                    <div style="display: flex; align-items: center;">
                        <label style="width: 150px; font-size: 18px;">Search By Name :</label>
                        <input type="text" name="Ser_name" class="input-text" id="Ser_name" style="width: 400px" title="" value="" oninput="searchRecords()">&nbsp;&nbsp;
                    </div>
                    <div class="pageTableScope" style="height: 250px; margin-top: 10px;">
                        <table style="font-family: Futura, 'Trebuchet MS', Arial, sans-serif; font-size: 13pt;" id="branchdataTable" width="100%" border="0" cellspacing="2" cellpadding="0">
                            <thead>
                                <tr class="viewTHead">
                                    <td class="fieldText" style="width: 80px;">Test Group ID</td>
                                    <td class="fieldText" style="width: 350px;">Name</td>
                                    <td class="fieldText" style="width: 80px;">Price</td>
                                    <td class="fieldText" style="width: 50px;">selecet</td>
                                </tr>
                            </thead>
                            <tbody id="record_tbl">
                                <!-- Dynamic rows will be inserted here via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div><br>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <!-- Left aligned content -->
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <label style="font-size: 18px; color: blue;">Test Count:</label>
                        <label style="font-size: 18px; color: green;" id="invoicecount">0</label>
                    </div>

                    <!-- Right aligned content -->
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <label style="font-size: 18px; color: blue;">Select All</label>
                        <input class="form-check-input" type="checkbox" id="selectAllCheckbox" />
                    </div>
                </div>

            </div><br>

            <div style="width: 1000px; display: flex;">
                <!-- Add test package part -->
                <div style="flex: 1; padding: 10px; border: 2px #8e7ef7 solid; margin-right: 5px; border-radius: 10px;">


                    <div style="display: flex; justify-content: flex-center; gap: 5px; margin-bottom: 10px;">
                        <input type="button" style="color:green" class="btn" id="saveBtn" value="Save" onclick="if (validateOnSubmit()) saveDiscount()">
                        <input type="button" style="color:Blue" class="btn" id="updateBtn" value="Update" onclick="if (validateOnSubmit()) updateDiscount()">
                        <input type="button" style="color:red" class="btn" id="deleteBtn" value="Delete" onclick="deleteDiscount()">
                        <input type="button" style="color:gray" class="btn" id="resetbtn" value="Reset" onclick="resetFields()">
                    </div>
                </div>

                <!-- selected tests part -->

                <div style="flex: 1; padding: 10px; border: 2px #8e7ef7 solid; border-radius: 10px;">

                </div>

            </div>
        </div>

    </div>
</div>




@stop
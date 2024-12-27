<?php ?>
@extends('Templates/WiTemplate')


@section('title')
Branch Wise Test Mapping
@stop

@section('head')

<script src="{{ asset('JS/ReportCalculations.js') }}"></script>


<script>
    // ****************** Onload ready Function*********************
    $(document).ready(function() {
        loadRecordToBranchTable()
        loadRecordToTable();

        $('#labBranchDropdown').change(function() {
            loadRecordToTable();
            searchRecords();
        });

        $('#selectAllCheckbox').change(function() {
            var isChecked = $(this).prop('checked');
            $('#record_tbl .select-test').each(function() {
                $(this).prop('checked', isChecked);
            });
        });
        $('#selectBranchCheckbox').change(function() {
            var isChecked = $(this).prop('checked');
            $('#Branch_record_tbl .test-branch').each(function() {
                $(this).prop('checked', isChecked);
            });
        });

        if ($('#labBranchDropdown').val() === "%") {
            $('#selectAllBtn').hide();
        }

        // When Baranch Name = 'Main Lab' then hide Remove button
        $('#labBranchDropdown').on('change', function() {
            if ($(this).val() === "%") {
                $('#selectAllBtn').hide();
            } else {
                $('#selectAllBtn').show();
            }
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

    // ******************Function to get branch test count******************
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

    //#############################################################################################################
    //*********************************************Branch Creating Section*****************************************

    // Function to load branch data into the table
    function loadRecordToBranchTable() {
        $.ajax({
            type: "GET",
            url: "getAllBranches",
            success: function(tbl_records) {
                $('#Branch_record_tbl').html(tbl_records);
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

    // ******************Function to save the reference data**************************
    function saveBranches() {
        // Get values from input fields by their IDs
        var brnName = $('#Branch_name').val();
        var brnCode = $('#Branch_code').val();
        var brnContact = $('#Branch_contact').val();
        var brnAddress = $('#Branch_address').val();

        if (brnName === '') {
            alert('Branch name is required.');
            return;
        }

        if (brnCode === '') {
            alert('Branch code is required.');
            return;
        }

        $.ajax({
            type: "POST",
            url: "/saveBranch",
            data: {
                branchName: brnName,
                branchCode: brnCode,
                branchContact: brnContact,
                branchAddress: brnAddress
            },
            success: function(response) {
                if (response.error === "saved") {
                    alert('Branch saved successfully!');
                    loadRecordToBranchTable();
                    $('#Branch_name').val('');
                    $('#Branch_code').val('');
                    $('#Branch_contact').val('');
                    $('#Branch_address').val('');
                } else if (response.error === "name_exist") {
                    alert('Branch Name already exists!');
                } else if (response.error === "code_exist") {
                    alert('Branch Code already exists!');
                } else {
                    alert('Error occurred while saving!');
                }
            },
            error: function(xhr) {
                console.log('Error:', xhr.responseText);
                alert('AJAX error occurred');
            }
        });
    }
    // ********************Function to load selected record into the input field when clicking on a table row*********
    function selectBranch(brnID, brnName, brnCode, brnContact, brnAddress) {
        $('#Branch_id').val(brnID);
        $('#Branch_name').val(brnName);
        $('#Branch_code').val(brnCode);
        $('#Branch_contact').val(brnContact);
        $('#Branch_address').val(brnAddress);
        $('#Branch_code').prop('readonly', true);
    }


    //****************** */ Function to reset the input fields*************************
    function resetFields() {
        document.getElementById('Branch_id').value = '';
        document.getElementById('Branch_name').value = '';
        document.getElementById('Branch_code').value = '';
        document.getElementById('Branch_contact').value = '';
        document.getElementById('Branch_address').value = '';
        $('#Branch_code').prop('readonly', false);
        $('.select-test').prop('checked', false);
        $('#selectAllCheckbox').prop('checked', false);
        $('#saveBtn').prop('disabled', false);
        $('#saveBtn').show();
    }

    //*******************Save button disable */
    $(document).ready(function() {
        $('#Branch_record_tbl').on('click', 'td', function() {
            if ($(this).index() === 0 || $(this).index() === 1 || $(this).index() === 2) {
                $('#saveBtn').hide();
            }
        });
    });

    // ******************Function to update the Branch data******************

    function updateBranch() {
        var brnID = $('#Branch_id').val();
        var brnName = $('#Branch_name').val();
        var brnCode = $('#Branch_code').val();
        var brnContact = $('#Branch_contact').val();
        var brnAddress = $('#Branch_address').val();

        if (!brnID || !brnName || !brnCode) {
            alert('Please select a valid record and fill out all fields.');
            return;
        }

        // AJAX request to update the discount
        $.ajax({
            type: "POST",
            url: "/updateBranch",
            data: {
                'Branch_id': brnID,
                'Branch_name': brnName,
                'Branch_code': brnCode,
                'Branch_contact': brnContact,
                'Branch_address': brnAddress
            },
            success: function(response) {
                if (response.success && response.error === "updated") {
                    alert('Branch updated successfully!');
                    loadRecordToBranchTable();
                    resetFields();
                } else if (!response.success && response.error === "Branch_name_exist") {
                    alert('The Branch Name is already in use. Please use a unique name.');
                } else if (!response.success && response.error === "not_updated") {
                    alert('No changes made to the Branche.');
                } else {
                    alert('Error in updating Branche.');
                }
            },
            error: function(xhr) {
                console.log('Error:', xhr);
                alert('Error in updating Branche.');
            }
        });
    }

    // ******************Function to update the validate Branch Code******************
    function validateBranchCode() {
        var inputField = document.getElementById('Branch_code');
        var value = inputField.value;

        // Convert input to uppercase
        inputField.value = value.toUpperCase();

        // Allow only letters (A-Z)
        inputField.value = inputField.value.replace(/[^A-Z]/g, '');

        // Limit the input to 2 characters
        if (inputField.value.length > 2) {
            inputField.value = inputField.value.slice(0, 2);
        }
    }


    // ******************Function to update Test Branches******************
    let selectedTests = [];
    let testBranches = [];

    function updateTestBranches() {
        selectedTests = [];
        testBranches = [];
        document.querySelectorAll('.select-test:checked').forEach((checkbox) => {
            selectedTests.push(checkbox.value);
        });
        document.querySelectorAll('.test-branch:checked').forEach((checkbox) => {
            testBranches.push(checkbox.value);
        })
        //alert(selectedTests + " " + testBranches);
        $.ajax({
            type: "POST",
            url: "/updateTestBranches",
            data: {
                'selectedTests': selectedTests,
                'testBranches': testBranches,
                'isSelected': document.getElementById('priceUpdate').checked
            },
            success: function(response) {
                if (response.success && response.error === "updated") {
                    alert('Test Branches updated successfully!');
                    loadRecordToBranchTable();
                    resetFields();
                } else {
                    alert('Error in updating Test Branches.');
                }
            }
        });
    }


    //**************************function Remove Test From Branch */
    function RemoveTestFromBranch() {
        selectedTests = [];
        let labBranchDropdown = document.getElementById('labBranchDropdown').value;
        document.querySelectorAll('.select-test:checked').forEach((checkbox) => {
            selectedTests.push(checkbox.value);
        });
        $.ajax({
            type: "POST",
            url: "/RemoveTestFromBranch",
            data: {
                'selectedTests': selectedTests,
                'labBranchDropdown': labBranchDropdown

            },
            success: function(response) {
                if (response.success && response.error === "deleted") {
                    alert('Test Delete successfully!');
                    loadRecordToTable();
                    resetFields();
                } else {
                    alert('Error in updating Test Branches.');
                }

            }
        })
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

    #Branch_code[readonly] {
        cursor: not-allowed;
    }
</style>
@stop

@section('body')


<h2 class="pageheading" style="margin-top: -1px;"> Branch Wise Test Mapping
</h2>
<div class="container">
    <div class="card" style="height: 870px;">
        <div class="card-body">
            <div style="flex: 1; padding: 10px; border: 2px #8e7ef7 solid; margin-right: 5px; border-radius: 10px;">
                <div style="display: flex; align-items: center; margin-bottom: 10px; margin-top: 10px;">
                    <label style="width: 150px;font-size: 18px;">Branch Name &nbsp;:</label>
                    <select name="labbranch" style="width: 273px" class="input-text" id="labBranchDropdown">
                        <option value="%"> Main Lab</option>
                        <?php
                        //$lid = $_SESSION['lid'];
                        //Result = DB::select("SELECT name, bid FROM labbranches WHERE Lab_lid = ? ORDER BY name ASC", [$lid]);

                        $Result = DB::select("Select name, bid FROM labbranches WHERE Lab_lid = '" . $_SESSION['lid'] . "' ORDER BY name ASC");

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

                    <input type="hidden" name="crBranch_id" id="crBranch_id">
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
                                    <td align="center" class="fieldText" style="width: 80px;">Test Group ID</td>
                                    <td align="center" class="fieldText" style="width: 350px;">Name</td>
                                    <td align="center" class="fieldText" style="width: 80px;">Price</td>
                                    <td align="center" class="fieldText" style="width: 50px;">selecet</td>
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
                        <input type="button" style="color:red" class="btn" id="selectAllBtn" value="Remove" onclick="RemoveTestFromBranch()">
                        <label style="font-size: 18px; color: blue;">Select All</label>
                        <input class="form-check-input" type="checkbox" id="selectAllCheckbox" />
                    </div>
                </div>

            </div><br>

            <div style="width:1350px; display: flex;">
                <!-- Create New Branch -->
                <div style="flex: 1; padding: 10px; border: 2px #8e7ef7 solid; margin-right: 5px; border-radius: 10px;">

                    <b><u><i>Create New Branch</i></u></b><br>
                    <div style="display: flex; align-items: center; margin-bottom: 10px; margin-top: 10px;">
                        <label style="width: 150px;font-size: 18px;">Branch Name &nbsp;:</label>
                        <input type="text" name=" Branch_name" class="input-text" id="Branch_name" style="width: 250px">
                        <input type="hidden" name="Branch_id" id="Branch_id">
                    </div>
                    <div style="display: flex; align-items: center; margin-bottom: 10px;">
                        <label style="width: 150px;font-size: 18px;">Branch Code:</label>
                        <input type="text" name=" Branch_code" maxlength="2" class="input-text" id="Branch_code" style="width: 250px" oninput="validateBranchCode()">
                    </div><br>
                    <div style="display: flex; align-items: center; margin-bottom: 10px;">
                        <label style="width: 150px;font-size: 18px;">Branch Contact:</label>
                        <input type="text" name=" Branch_contact" maxlength="10" class="input-text" id="Branch_contact" style="width: 250px">
                    </div><br>
                    <div style="display: flex; align-items: center; margin-bottom: 10px;">
                        <label style="width: 150px;font-size: 18px;">Branch Address:</label>
                        <input type="text" name=" Branch_address" class="input-text" id="Branch_address" style="width: 250px">
                    </div><br>
                    <div style="display: flex; justify-content: flex-center; gap: 5px; margin-bottom: 10px;">
                        <input type="button" style="color:green" class="btn" id="saveBtn" value="Save" onclick="saveBranches()">
                        <input type="button" style="color:Blue" class="btn" id="updateBtn" value="Update" onclick="updateBranch()">
                        <input type="button" style="color:gray" class="btn" id="resetbtn" value="Reset" onclick="resetFields()">
                    </div>
                </div>


                <!-- Created Branches -->

                <div style="flex: 2; padding: 10px; border: 2px #8e7ef7 solid; border-radius: 10px;">
                    <div class="pageTableScope" style="height: 250px; margin-top: 10px;">
                        <table style="font-family: Futura, 'Trebuchet MS', Arial, sans-serif; font-size: 13pt;" id="branchdataTable" width="100%" border="0" cellspacing="2" cellpadding="0">
                            <thead>
                                <tr class="viewTHead">
                                    <td align="center" class="fieldText" style="width: 20px;">Branch ID</td>
                                    <td align="center" class="fieldText" style="width: 250px;">Branch Name</td>
                                    <td align="center" class="fieldText" style="width: 20px;">Branch Code</td>
                                    <td align="center" class="fieldText" style="width: 10px;">selecet</td>
                                </tr>
                            </thead>
                            <tbody id="Branch_record_tbl">
                                <!-- Dynamic rows will be inserted here via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div style="display: flex; justify-content: flex-end; align-items: center; gap: 10px;">
                <input type="button" style="color:green; width: 150px; height: 50px" class="btn" id="udateTestBranches" value="Update Tests" onclick="updateTestBranches()">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <label style="font-size: 18px; color: blue;">Change Price If Test Exists</label>
                    <input class="form-check-input" type="checkbox" id="priceUpdate" />
                    <label style="font-size: 18px; color: blue;">Select All Branches</label>
                    <input class="form-check-input" type="checkbox" id="selectBranchCheckbox" />
                </div>
            </div>
        </div>

    </div>
</div>




@stop
<?php ?>
@extends('Templates/WiTemplate')


@section('title')
Branch Wise Test Mapping
@stop

@section('head')

<script src="{{ asset('JS/ReportCalculations.js') }}"></script>


<script>
    $(document).ready(function() {
        loadRecordToTable();
        $('#labBranchDropdown').change(function() {
            loadRecordToTable();
        });
    });


    // ******************Function to load data into the table*********************
    function loadRecordToTable() {
        var labBranchDropdown = $('#labBranchDropdown').val();
        $.ajax({
            type: "GET",
            url: "getAllBranchTests",
            data: {
                labBranchDropdown: labBranchDropdown
            },
            success: function(tbl_records) {
                $('#record_tbl').html(tbl_records);
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
                        <option value="%"></option>
                        <?php
                        // Fetch lab branches dynamically based on the session's Lab_lid
                        $Result = DB::select("select name, bid FROM labbranches WHERE Lab_lid = '" . $_SESSION['lid'] . "'");

                        foreach ($Result as $res) {
                            $branchName = $res->name;
                            $bid = $res->bid;

                            // Check if the labbranch is set and if it matches the current branch's ID
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
                    <b><u><i>Test List</i></u></b><br>
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
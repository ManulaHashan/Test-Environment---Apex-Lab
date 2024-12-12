<?php ?>
@extends('Templates/WiTemplate')


@section('title')
Create Discount
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
            url: "getAllDiscount", // Ensure this matches the route definition
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

    //****************** */ Function to reset the input fields*************************
    function resetFields() {
        document.getElementById('Discount_id').value = '';
        document.getElementById('Discount_name').value = '';
        document.getElementById('Discount_value').value = '';
        console.log("All fields have been reset!");
        $('#saveBtn').prop('disabled', false);
        $('#saveBtn').show(); 
    }


    // ******************Function to save the reference data**************************
    function saveDiscount() {
        // Get values from input fields by their IDs
        var disName = $('#Discount_name').val();
        var disValue = $('#Discount_value').val();

        if (disName === '') {
            alert('Discount name is required.');
            return;
        }

        if (disValue === '') {
            alert('Discount value is required.');
            return;
        }

        $.ajax({
            type: "POST",
            url: "/saveDiscount",
            data: {
                discountName: disName,
                discountValue: disValue,
            },
            success: function(response) {
                if (response.error === "saved") {
                    alert('Discount saved successfully!');
                    loadRecordToTable();
                    $('#Discount_name').val('');
                    $('#Discount_value').val('');
                } else if (response.error === "exist") {
                    alert('Discount Name already exists!');
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
    function selectRecord(disID, disName, disValue) {
        $('#Discount_id').val(disID);
        $('#Discount_name').val(disName);
        $('#Discount_value').val(disValue);

    }

    // ******************Function to update the Discount data******************

    function updateDiscount() {
        var disID = $('#Discount_id').val();
        var disName = $('#Discount_name').val();
        var disValue = $('#Discount_value').val();

        if (!disID || !disName || !disValue) {
            alert('Please select a valid record and fill out all fields.');
            return;
        }

        // AJAX request to update the discount
        $.ajax({
            type: "POST",
            url: "/updateDiscount",
            data: {
                'Discount_id': disID,
                'Discount_name': disName,
                'Discount_value': disValue
            },
            success: function(response) {
                if (response.success && response.error === "updated") {
                    alert('Discount updated successfully!');
                    loadRecordToTable();
                    resetFields();
                } else if (!response.success && response.error === "exist") {
                    alert('The Discount Value is already in use. Please use a unique Value.');
                } else if (!response.success && response.error === "not_updated") {
                    alert('No changes made to the Discount.');
                } else {
                    alert('Error in updating Discount.');
                }
            },
            error: function(xhr) {
                console.log('Error:', xhr);
                alert('Error in updating Discount.');
            }
        });
    }

    // ******************Function to Delete Discount**************************
    function deleteDiscount() {
        var Discount_id = $('#Discount_id').val();

        if (Discount_id === '') {
            alert('Please select a Discount Data to delete.');
            return;
        }


        if (confirm('Are you sure you want to delete this Disount?')) {
            $.ajax({
                type: "POST",
                url: "/deleteDiscount",
                data: {
                    'Discount_id': Discount_id
                },
                success: function(response) {
                    if (response == "deleted") {
                        alert('Discount deleted successfully!');
                        loadRecordToTable();
                        resetFields();
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error:', error);
                    alert('Error in deleting reference.');
                }
            });
        }
    }

    //*************validation for Discount value feild to enter 1-100 numbers*************** */
    function validateOnSubmit() {
        let value = parseFloat($('#Discount_value').val());

        if (value < 1 || value > 100) {
            alert('Discount value must be between 1 and 100.');
            return false;
        }

        return true;
    }

    //*************validation for Discount value feild to can't enter charactors and desimal accepted*************** */
    $(document).ready(function() {
        $('#Discount_value').on('input', function() {
            var value = $(this).val();
            var regex = /^[+]?\d*\.?\d*$/;

            if (value && !regex.test(value)) {
                $(this).val(value.slice(0, -1));
            }
        });
    });

    $(document).ready(function() {
        $('#record_tbl ').on('click', 'tr', function() {
            $('#saveBtn').hide();
        });
    });
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


<h2 class="pageheading" style="margin-top: -1px;"> Create Discounts
</h2>
<div class="container">
    <div class="card" style="height: 750px;">
        <div class="card-body">
            <div style="width: 1000px; display: flex;">
                <!-- Add test package part -->
                <div style="flex: 1; padding: 10px; border: 2px #8e7ef7 solid; margin-right: 5px; border-radius: 10px;">
                    <b><u><i>Add New Discount</i></u></b><br>
                    <div style="display: flex; align-items: center; margin-bottom: 10px; margin-top: 10px;">
                        <label style="width: 150px;font-size: 18px;">Discount Name &nbsp;:</label>
                        <input type="text" name=" Discount_name" class="input-text" id="Discount_name" style="width: 250px">
                        <input type="hidden" name="Discount_id" id="Discount_id">
                    </div>
                    <div style="display: flex; align-items: center; margin-bottom: 10px;">
                        <label style="width: 150px;font-size: 18px;">Discount value(%):</label>
                        <input type="text" name=" Discount_value" class="input-text" id="Discount_value" style="width: 250px">
                    </div>

                    <div style="display: flex; justify-content: flex-center; gap: 5px; margin-bottom: 10px;">
                        <input type="button" style="color:green" class="btn" id="saveBtn" value="Save" onclick="if (validateOnSubmit()) saveDiscount()">
                        <input type="button" style="color:Blue" class="btn" id="updateBtn" value="Update" onclick="if (validateOnSubmit()) updateDiscount()">
                        <input type="button" style="color:red" class="btn" id="deleteBtn" value="Delete" onclick="deleteDiscount()">
                        <input type="button" style="color:gray" class="btn" id="resetbtn" value="Reset" onclick="resetFields()">
                    </div>
                </div>

                <!-- selected tests part -->

                <div style="flex: 1; padding: 10px; border: 2px #8e7ef7 solid; border-radius: 10px;">
                    <b><u><i>Created Discounts</i></u></b><br>
                    <div class="pageTableScope" style="height: 250px; margin-top: 10px;">
                        <table style="font-family: Futura, 'Trebuchet MS', Arial, sans-serif; font-size: 13pt;" id="pdataTable" width="100%" border="0" cellspacing="2" cellpadding="0">
                            <thead>
                                <tr class="viewTHead">
                                    <td class="fieldText" style="width: 350px;">Name</td>
                                    <td class="fieldText" style="width: 80px;">value(%)</td>
                                </tr>
                            </thead>
                            <tbody id="record_tbl">
                                <!-- Dynamic rows will be inserted here via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>




@stop
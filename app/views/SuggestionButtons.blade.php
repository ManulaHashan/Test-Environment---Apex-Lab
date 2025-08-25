<?php ?>
@extends('Templates/WiTemplate')


@section('title')
Create Button Style
@stop

@section('head')

<script src="{{ asset('JS/ReportCalculations.js') }}"></script>


<script>
    $(document).ready(function() {
        loadRecordToTable();
    });


    // ******************Function to save the reference data**************************


    function saveButtonStyle(){

        var testGroupId = $('#lab_teats').val();
        var colorValue = $('#color_value').val();
        var orderNo = $('#order_no').val();

        if (testGroupId === '') {
            alert('Please select a test group.');
            return;
        }

        if (colorValue === '') {
            alert('Please enter a color value.');
            return;
        }

        if (orderNo === '') {
            alert('Please enter an order number.');
            return;
        }

        $.ajax({
            type: "POST",
            url: "/saveButtonStyle",
            data: {
                testGroupId: testGroupId,
                colorValue: colorValue,
                orderNo: orderNo
            },
            success: function(response) {
                if (response.error === "saved") {
                    alert('Button style saved successfully!');
                    loadRecordToTable();
                    $('#lab_teats').val('');
                    $('#color_value').val('');
                    $('#order_no').val('');
                } else if (response.error === "exist") {
                    alert('Button style already exists!');
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

function loadRecordToTable() {
    $.ajax({
        type: "GET",
        url: "/getButtonStyles",
        success: function(response) {
            if (response.error === "success") {
                var rows = '';
                $.each(response.data, function(index, record) {
                    rows += '<tr data-tgid="' + record.tgid + '" onclick="selectRecord(\'' + record.tgid + '\', \'' + record.color + '\', \'' + record.orderno + '\')">' +
                        '<td class="fieldText" style="width: 100px;">' + record.testGroupName + '</td>' +
                        '<td class="fieldText" style="width: 100px;">' +
                            '<div style="width: 50px;text-align:center; height: 20px; background-color: ' + record.color + '; border: 1px solid #ccc;"></div>' +
                        '</td>' +
                        '<td class="fieldText" style="width: 80px; text-align:center">' + record.orderno + '</td>' +
                    '</tr>';
                });
                $('#btn_record_tbl').html(rows);
            } else {
                alert('Error loading data!');
                $('#btn_record_tbl').html('');
            }
        },
        error: function(xhr) {
            console.log('Error:', xhr.responseText);
            alert('AJAX error occurred while loading data');
            $('#btn_record_tbl').html('');
        }
    });
}
  
    function selectRecord(testGroupId, colorValue, orderNo) {
        $('#lab_teats').val(testGroupId);
        $('#color_value').val(colorValue);
        $('#order_no').val(orderNo);
        $('#lab_teats').prop('disabled', true);
        $('#btn_record_tbl tr').removeClass('selected-row');
        $('tr[data-tgid="' + testGroupId + '"]').addClass('selected-row');
    }


    function updateButtonStyle() {
        var testGroupId = $('#lab_teats').val();
        var colorValue = $('#color_value').val();
        var orderNo = $('#order_no').val();

        if (testGroupId === '') {
            alert('Please select a test group.');
            return;
        }

        if (colorValue === '') {
            alert('Please enter a color value.');
            return;
        }

        if (orderNo === '') {
            alert('Please enter an order number.');
            return;
        }

        $.ajax({
            type: "POST",
            url: "/updateButtonStyle",
            data: {
                testGroupId: testGroupId,
                colorValue: colorValue,
                orderNo: orderNo
            },
            success: function(response) {
                if (response.error === "updated") {
                    alert('Button style updated successfully!');
                    $('#lab_teats').val('');
                     $('#btn_record_tbl tr').removeClass('selected-row');
                    $('#color_value').val('');
                    $('#order_no').val('');
                    loadRecordToTable();
                } else if (response.error === "not_found") {
                    alert('Record not found!');
                } else if (response.error === "order_exists") {
                    alert('Order number already exists! Please choose a different order number.');
                } else {
                    alert('Error occurred while updating!');
                }
            },
            error: function(xhr) {
                console.log('Error:', xhr.responseText);
                alert('AJAX error occurred');
            }
        });
    }

    function resetFields() {
        $('#lab_teats').val('');
        $('#color_value').val('');
        $('#order_no').val('');
        $('#lab_teats').prop('disabled', false);
        $('#btn_record_tbl tr').removeClass('selected-row');
    }


    function deleteButtonStyle() {
        var testGroupId = $('#lab_teats').val();

        if (testGroupId === '') {
            alert('Please select a test group to delete.');
            return;
        }

        if (!confirm('Are you sure you want to delete this button style?')) {
            return;
        }

        $.ajax({
            type: "POST",
            url: "/deleteButtonStyle",
            data: {
                testGroupId: testGroupId
            },
            success: function(response) {
                if (response.error === "deleted") {
                    alert('Button style deleted successfully!');
                    $('#lab_teats').val('');
                    $('#color_value').val('');
                    $('#order_no').val('');
                    $('#lab_teats').prop('disabled', false);
                    $('#btn_record_tbl tr').removeClass('selected-row');
                    loadRecordToTable();
                } else if (response.error === "not_found") {
                    alert('Record not found!');
                } else {
                    alert('Error occurred while deleting!');
                }
            },
            error: function(xhr) {
                console.log('Error:', xhr.responseText);
                alert('AJAX error occurred');
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
     #btn_dataTable tr {
        cursor: pointer;
    }

    #btn_dataTable tr:hover {
        background-color: #90EE90; 
    }

    #btn_dataTable tr.selected-row {
        background-color: #FFC1CC; 
    }
</style>
@stop

@section('body')


<h2 class="pageheading" style="margin-top: -1px;"> Billing Buttons Customization
</h2>
<div class="container">
    <div class="card" style="height: 750px;">
        <div class="card-body">
            <div style="width: 1000px; display: flex;">
                <!-- Add test package part -->
                <div style="flex: 1; padding: 10px; border: 2px #8e7ef7 solid; margin-right: 5px; border-radius: 10px;">
                    <b><u><i>Add New Button</i></u></b><br>
                    <div style="display: flex; align-items: center; margin-bottom: 10px; margin-top: 10px;">
                        <label for="test_group" style="font-size: 14px; min-width: 150px;"><b>Select Test Group:</b></label>
                        <select name="labbranch" style="width: 270px; height: 30px" class="input-text" id="lab_teats">
                            <option value="">-- Select Test Group --</option>
                            <?php
                            $Result = DB::select("SELECT tgid, name FROM Testgroup WHERE Lab_lid = '" . $_SESSION['lid'] . "' ORDER BY name ASC");
                            foreach ($Result as $res) {
                                $tgid = $res->tgid;        
                                $name = $res->name;        
                                $displayText = $tgid . " : " . $name;
                            ?>
                            <option value="<?= $tgid ?>"><?= $displayText ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div style="display: flex; align-items: center; margin-bottom: 10px;">
                        <label style="width: 150px;font-size: 18px;">Color</label>
                        <input type="color" name="color_value" class="input-text" id="color_value" style="width: 250px;height: 35px;">
                    </div>

                     <div style="display: flex; align-items: center; margin-bottom: 10px;">
                        <label style="width: 150px;font-size: 18px;">Order No</label>
                        <input type="number" name="order_no" class="input-text" id="order_no" style="width: 250px">
                    </div>

                    <div style="display: flex; justify-content: flex-center; gap: 5px; margin-bottom: 10px;">
                        <input type="button" style="color:green" class="btn" id="saveBtn" value="Save" onclick="saveButtonStyle()">
                        <input type="button" style="color:Blue" class="btn" id="updateBtn" value="Update" onclick="updateButtonStyle()">
                        <input type="button" style="color:red" class="btn" id="deleteBtn" value="Delete" onclick="deleteButtonStyle()">
                        <input type="button" style="color:gray" class="btn" id="resetbtn" value="Reset" onclick="resetFields()">
                    </div>
                </div>

                <!-- selected tests part -->

                <div style="flex: 1; padding: 10px; border: 2px #8e7ef7 solid; border-radius: 10px;">
                    <b><u><i>Created Discounts</i></u></b><br>
                    <div class="pageTableScope" style="height: 250px; margin-top: 10px;">
                        <table style="font-family: Futura, 'Trebuchet MS', Arial, sans-serif; font-size: 13pt;" id="btn_dataTable" width="100%" border="0" cellspacing="2" cellpadding="0">
                            <thead>
                                <tr class="viewTHead">
                                    <td class="fieldText" style="width: 220px;">Test</td>
                                    <td class="fieldText" style="width: 20px;">Color</td>
                                    <td class="fieldText" style="width: 10px;">Order No</td>
                                </tr>
                            </thead>
                            <tbody id="btn_record_tbl">
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
<?php ?>
@extends('Templates/WiTemplate')


@section('title')
Test parameter mapping
@stop

@section('head')

<script src="{{ asset('JS/ReportCalculations.js') }}"></script>


<script>
    $(document).ready(function() {
        const today = new Date();
        const formattedDate = today.toISOString().split('T')[0];
        $('#date').val(formattedDate);

        loadRecordToTable(formattedDate, null);
        $('#searchBtn').click(function() {
            const date = $('#date').val();
            const sampleNo = $('#sample_no').val();
            loadRecordToTable(date, sampleNo);
        });
    });


    // Function to load records to table
    function loadRecordToTable(date = null, sampleNo = null) {

       

        $.ajax({
            type: "GET",
            url: "getAllSamplesParaTable",
            data: {
                date: date,
                sample_no: sampleNo
            },
            success: function(tbl_records) {

                $('#record_tbl').html(tbl_records.html);
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


    // Function to update Test Parameters

    let selectedSamples = [];


    function updateTestParameters() {
        selectedSamples = [];
        document.querySelectorAll('.select-test:checked').forEach((checkbox) => {
            selectedSamples.push(checkbox.value);
        });

        //alert(selectedSamples);
        $.ajax({
            type: "POST",
            url: "/updateTestParameters",
            data: {
                'selectedSamples': selectedSamples

            },
            success: function(response) {
                //var jobject = JSON.parse(response);
                if (response.success && response.error === "updated") {
                    alert('Test Parameters updated successfully!');

                } else {
                    alert('Failed to update Test Parameters.');
                }
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


<h2 class="pageheading" style="margin-top: 5px;"> Test Parameter Mapping
</h2>
<div class="container" style="margin-top: 20px;">
    <div class="card" style="height: 658px;">
        <div class="card-body">
            <div style="width: 1000px; display: flex;">
                <div style="flex: 1; padding: 10px; margin-right: 5px;">
                    <div style="display: flex; align-items: center; margin-bottom: 10px; margin-top: 10px;">
                        <label style="width: 80px;font-size: 25px;">Date:</label>
                        <input type="date" name=" date" class="input-text" id="date"
                            style="width: 150px;font-size: 20px;">
                        <label style="width: 150px;font-size: 25px; margin-left: 15px;">Sample No:</label>
                        <input type="text" name=" sample_no" class="input-text" id="sample_no"
                            style="width: 150px;font-size: 20px;">
                        <input type="button" style="color:green; font-size: 20px; margin-left: 15px;" class="btn" id="searchBtn" value="Search" onclick="">
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
                                <td class="fieldText" style="width: 10px;">lpsid</td>
                                <td class="fieldText" style="width: 50px;">Sample No</td>
                                <td class="fieldText" style="width: 110px;">Patient Name</td>
                                <td class="fieldText" style="width: 30px;">Test ID</td>
                                <td class="fieldText" style="width: 100px;">Test Name</td>
                                <td class="fieldText" style="width: 10px;">Select</td>
                            </tr>
                        </thead>
                        <tbody id="record_tbl">
                            <!-- Dynamic rows will be inserted here via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div style="display: flex; justify-content: center; align-items: center;">
                <input type="button" style="color:green; font-size: 16px; margin-left: 15px;" class="btn" id="updateParaBtn" value="Update Parameters" onclick="updateTestParameters()">
            </div>

        </div>

    </div>
</div>




@stop
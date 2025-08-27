<!-- **********~~~~~~~~~~~~~Manula Hashan's Devolopment~~~~~~~~~~~~~********** -->


<?php ?>
@extends('Templates/WiTemplate')


@section('title')
View Invices
@stop

@section('head')

<script src="{{ asset('JS/ReportCalculations.js') }}"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css"/>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>

<script>



function loadRecordToTable() {
    var sample_date = $('#sample_date').val();
    var sample_no = $('#sample_no').val();

    $.ajax({
        type: "GET",
        url: "getSampleDataEditRecords", 
        data: {
            sample_date: sample_date,
            sample_no: sample_no
        },
        success: function(tbl_records) {
            $('#lps_record_tbl').html(tbl_records); 
             $('.datepicker').datepicker({
            dateFormat: 'yy-mm-dd'
            });
            $('.timepicker').timepicker({
                timeFormat: 'HH:mm:ss',
                interval: 1,
                dynamic: false,
                dropdown: true,
                scrollbar: true
            });
            $('.datetimepicker').datetimepicker({
                format: 'Y-m-d H:i:s',   
                step: 1
    });
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


    $(document).ready(function() {
        $('#sample_date').val(new Date().toISOString().slice(0, 10));
        loadRecordToTable();
    });

    $('#ser_btn').on('click', function() {
        loadRecordToTable();
    });


    $(document).on('click', '.updateRowBtn', function() {
        var $row = $(this).closest('tr');
        var lpsid = $(this).data('lpsid');

        
        var rowData = {
            lpsid: lpsid,
            sampleNo: $row.find('input[name="sampleNo"]').val(),
            date: $row.find('input[name="date"]').val(),
            patient_pid: $row.find('input[name="patient_pid"]').val(),
            arivaltime: $row.find('input[name="arivaltime"]').val(),
            finishtime: $row.find('input[name="finishtime"]').val(),
            finishdate: $row.find('input[name="finishdate"]').val(),
            collecteddate: $row.find('input[name="collecteddate"]').val(),
            status: $row.find('input[name="status"]').val(),
            refference_idref: $row.find('input[name="refference_idref"]').val(),
            blooddraw: $row.find('input[name="blooddraw"]').val(),
            repcollected: $row.find('input[name="repcollected"]').val(),
            fastinghours: $row.find('input[name="fastinghours"]').val(),
            fastingtime: $row.find('input[name="fastingtime"]').val(),
            entered_uid: $row.find('input[name="entered_uid"]').val(),
            reference_in_invoice: $row.find('input[name="reference_in_invoice"]').val(),
            Testgroup_tgid: $row.find('input[name="Testgroup_tgid"]').val(),
            urgent_sample: $row.find('input[name="urgent_sample"]').val()
        };

        $.ajax({
            type: "POST",
            url: "updateSampleRecord", 
            data: rowData,
            success: function(response) {
               if (response.success) {
                alert('Record updated successfully!');
                loadRecordToTable(); 
                } else {
                    alert(response.message); 
                    loadRecordToTable(); 
                }
            },
            error: function(xhr, status, error) {
                alert('Update Error: ' + xhr.status + ' - ' + xhr.statusText);
            }
        });
    });

    $(document).on('input', 'input[name="status"]', function() {
    this.value = this.value.replace(/[^a-zA-Z]/g, '');
    });
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

    #invdataTable table tr:hover {
    background-color: #28acbd; /* light cyan on hover */
    cursor: pointer;
    }

    #invdataTable tbody tr.selected {
        background-color: #4f8de5 !important; /* green for selected row */
    }

    .input-text::placeholder {
        color: #ffffff; 
        opacity: 2; 
    }
    

</style>
@stop

@section('body')

<div class="container">
    <div class="card" style="height: 850px; margin-top: 50px; background-color:rgb(222, 222, 223);">
        <div class="card-body" style="max-width: 1350px; margin: auto; padding: 20px;">
            <!-- Filters Row: All filters in a single line -->
            <div class="filters-row" style="display: flex; flex-wrap: wrap; align-items: center; gap: 15px; margin-bottom: 15px; background: #e9ecef; padding: 10px 15px; border-radius: 8px;">
                <label style="width: 40px;"></label>
                <label style="font-size: 16px; min-width: 60px;"><b>Date</b></label>
                <input type="date" name="idate" class="input-text" id="sample_date" style="width: 120px; height: 30px;">
                <label style="font-size: 16px; min-width: 90px;"><b>Sample No</b></label>
                <input type="text" name="invoice_no" class="input-text" id="sample_no" style="width: 100px; height: 30px;">
                <input type="button" class="btn" id="ser_btn" value="Search" style="width: 90px; height: 32px; margin-left: 10px;" onclick="loadRecordToTable();">
            </div>

            <!-- Table Area: Horizontal scroll -->
            <div class="pageTableScope" style="height: 350px; margin-top: 10px; width: 100%; overflow-x: auto; background: #f8f9fa; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.05);">
                <table style="font-family: Futura, 'Trebuchet MS', Arial, sans-serif; font-size: 13pt; min-width: 1500px;" id="lpsdataTable" border="0" cellspacing="2" cellpadding="0">
                    <tbody>
                        <tr>
                            <td valign="top">
                                <table border="1" style="border-color: #ffffff; min-width: 1500px;" cellpadding="0" cellspacing="0" class="TableWithBorder" width="100%">
                                    <thead>
                                        <tr class="viewTHead">
                                            <td width="12%" class="fieldText" align="center">LPS ID</td>
                                            <td width="12%" class="fieldText" align="center">Sample No</td>
                                            <td width="12%" class="fieldText" align="center">Date</td>
                                            <td width="18%" class="fieldText" align="center">Patient ID</td>
                                            <td width="18%" class="fieldText" align="center">Arivaltime</td>
                                            <td width="10%" class="fieldText" align="center">Finish time</td>
                                            <td width="8%" class="fieldText" align="center">Finish date</td>
                                            <td width="10%" class="fieldText" align="center">Collected date</td>
                                            <td width="10%" class="fieldText" align="center">Status</td>
                                            <td width="10%" class="fieldText" align="center">Refference_idref</td>
                                            <td width="10%" class="fieldText" align="center">Blooddraw</td>
                                            <td width="10%" class="fieldText" align="center">Repcollected</td>
                                            <td width="10%" class="fieldText" align="center">Fastinghours</td>
                                            <td width="10%" class="fieldText" align="center">Fastingtime</td>
                                            <td width="10%" class="fieldText" align="center">Entered_uid</td>
                                            <td width="10%" class="fieldText" align="center">Reference_in_invoice</td>
                                            <td width="10%" class="fieldText" align="center">Testgroup_tgid</td>
                                            <td width="10%" class="fieldText" align="center">Urgent_sample</td>
                                            <td width="10%" class="fieldText" align="center">Action</td>
                                        </tr>
                                    </thead>
                                    <tbody id="lps_record_tbl">
                                        <!-- Dynamic content goes here -->
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@stop
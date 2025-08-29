<!-- **********~~~~~~~~~~~~~Manula Hashan's Devolopment~~~~~~~~~~~~~********** -->


<?php ?>
@extends('Templates/WiTemplate')


@section('title')
Sample Data Edit
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


$(document).ready(function() {
    $('#labbranch').val('%:@'); // set Main Lab as default
    $('#sample_date').val(new Date().toISOString().slice(0, 10));
    loadRecordToTable();

    $('#labbranch').on('change', function() {
        var val = $(this).val();
        var parts = val.split(':');
        if (val === '%:@' || val === '%') {
            $('#sample_no').val(''); 
        } else if (parts.length > 1 && parts[1] !== '') {
            $('#sample_no').val(parts[1]); 
        } else {
            $('#sample_no').val('');
        }
        loadRecordToTable(); 
    });

    $('#sample_date').on('change', function() {
        loadRecordToTable();
    });
});



function loadRecordToTable() {
    var sample_date = $('#sample_date').val();
    var sample_no = $('#sample_no').val();
    var labbranchVal = $('#labbranch').val();
    var branchCode = '';
    if (labbranchVal) {
       
        var parts = labbranchVal.split(':');
        branchCode = parts.length > 1 ? parts[1] : labbranchVal;
    }

    $.ajax({
        type: "GET",
        url: "getSampleDataEditRecords", 
        data: {
            sample_date: sample_date,
            sample_no: sample_no,
            branch_code: branchCode 
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


    

    $('#ser_btn').on('click', function() {
        loadRecordToTable();
    });


    function updateSelectedRows() {
    var checkedBoxes = $('#lps_record_tbl input[type="checkbox"]:checked');
    var total = checkedBoxes.length;
    var updated = 0;
    var errorShown = false;

    if (total === 0) {
        alert('Please select a row for update');
        return;
    }

    checkedBoxes.each(function() {
        var $row = $(this).closest('tr');
        var lpsid = $row.find('.updateRowBtn').data('lpsid');
        function cleanDate(val) {
            return (val === null || val === undefined || val.trim() === '' || val === '0') ? '' : val;
        }
        var rowData = {
            lpsid: lpsid,
            sampleNo: $row.find('input[name="sampleNo"]').val(),
            date: $row.find('input[name="date"]').val(),
            patient_pid: $row.find('input[name="patient_pid"]').val(),
            arivaltime: $row.find('input[name="arivaltime"]').val(),
            finishtime: cleanDate($row.find('input[name="finishtime"]').val()),
            finishdate: cleanDate($row.find('input[name="finishdate"]').val()),
            collecteddate: cleanDate($row.find('input[name="collecteddate"]').val()),
            status: $row.find('select[name="status"]').val(),
            refference_idref: $row.find('select[name="refference_idref"]').val(),
            blooddraw: cleanDate($row.find('input[name="blooddraw"]').val()),
            repcollected: cleanDate($row.find('input[name="repcollected"]').val()),
            fastinghours: $row.find('input[name="fastinghours"]').val(),
            fastingtime: $row.find('input[name="fastingtime"]').val(),
            entered_uid: $row.find('select[name="entered_uid"]').val(),
            reference_in_invoice: $row.find('input[name="reference_in_invoice"]').val(),
            Testgroup_tgid: $row.find('select[name="Testgroup_tgid"]').val(),
            urgent_sample: $row.find('select[name="urgent_sample"]').val()
        };

        $.ajax({
            type: "POST",
            url: "updateSampleRecord",
            data: rowData,
            success: function(response) {
                if (response.success) {
                    updated++;
                    if (updated === total && !errorShown) {
                        alert('Data update success!');
                        loadRecordToTable();
                    }
                } else {
                    if (!errorShown) {
                        alert(response.message);
                        errorShown = true;
                        loadRecordToTable();
                    }
                }
            },
            error: function(xhr, status, error) {
                if (!errorShown) {
                    alert('Update Error: ' + xhr.status + ' - ' + xhr.statusText);
                    errorShown = true;
                    loadRecordToTable();
                }
            }
        });
    });
}

    $(document).on('input', 'input[name="status"]', function() {
    this.value = this.value.replace(/[^a-zA-Z]/g, '');
});

// Time fields validation: only allow HH:MM:SS format and prevent letters
$(document).on('input', 'input[name="arivaltime"], input[name="finishtime"], input[name="fastingtime"]', function() {
    // Remove all except digits and colons
    this.value = this.value.replace(/[^0-9:]/g, '');
    // Enforce max length 8 (HH:MM:SS)
    if (this.value.length > 8) {
        this.value = this.value.slice(0, 8);
    }
    // Optionally, auto-format as HH:MM:SS (if desired)
    // You can uncomment below for auto-formatting
    // var v = this.value.replace(/[^0-9]/g, '');
    // if (v.length >= 6) {
    //     this.value = v.slice(0,2) + ':' + v.slice(2,4) + ':' + v.slice(4,6);
    // }
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


    /* Table row height increase */
    .TableWithBorder tr {
        height: 40px;
        border-bottom: 3px solid rgb(53, 39, 250);
    }

    #invdataTable table tr:hover {
        background-color: #28acbd; 
        cursor: pointer;
    }

    #invdataTable tbody tr.selected {
        background-color: #4f8de5 !important; 
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
             <input type="button" value="Update Selected" id="updateBtn" 
            onclick="updateSelectedRows()" 
            class="btn btn-success" 
            style="margin-top: 10px; float: right;">
    </div>
</div>

@stop
<!-- **********~~~~~~~~~~~~~Manula Hashan's Devolopment~~~~~~~~~~~~~********** -->


<?php ?>
@extends('Templates/WiTemplate')


@section('title')
EReport Log
@stop

@section('head')

<script src="{{ asset('JS/ReportCalculations.js') }}"></script>

<script>

    $(document).ready(function () {
        // Set default date on page load
        const today = new Date().toISOString().split('T')[0];
        $('#ereport_date').val(today);

        loadRecordToTable(); // auto-load table
    });

    function loadRecordToTable() {
        const filters = {
            date: $('#ereport_date').val(),
            user: $('#labuser_name').val(),
            sampleNo: $('#erepor_sample_no').val(),
            testName: $('#testgroup_name').val(),
            status: $('#ereport_status').val()
        };

        $.ajax({
            url: 'getEreportLogData',
            type: 'GET',
            data: filters,
            dataType: 'json',
            success: function (data) {
                let html = '';
                $.each(data, function (index, item) {
                    html += '<tr>' +
                        '<td align="center">' + item.date + '</td>' +
                        '<td align="center">' + item.time + '</td>' +
                        '<td align="center">' + item.fname + ' ' + (item.mname ?? '') + '</td>' +
                        '<td align="center">' + item.sampleNo + '</td>' +
                        '<td align="center">' + item.test_name + '</td>' +
                        '<td align="center">' + item.status + '</td>' +
                        '<td align="center">' + item.source + '</td>' +
                        '</tr>';
                });
                $('#ereport_record_tbl').html(html);
            },
            error: function () {
                alert('Failed to load eReport data.');
            }
        });
    }

    function SearchEreportData() {
        loadRecordToTable(); // search on button click
    }



    function saveEreportLog() {
      
        var currentDate = new Date().toISOString().split('T')[0]; 
        var currentTime = new Date().toTimeString().split(' ')[0]; 
        var method = "SMS";
        var Lps_lpsid = 619391;
        var source = "Sourse02";
        var status = "Pending";

        $.ajax({
            type: "POST",
            url: "/saveEreportLog",
            data: {
                date: currentDate,
                time: currentTime,
                method: method,
                Lps_lpsid: Lps_lpsid,
                source: source,
                status: status
            },
            success: function(response) {
                if (response.success) {
                    alert('eReport Log saved successfully!');
                    loadRecordToTable(); 
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                console.log('Error:', xhr.responseText);
                alert('AJAX error occurred');
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

    #ereport_logdataTable table tr:hover {
    background-color: #28acbd; /* light cyan on hover */
    cursor: pointer;
    }

    #ereport_logdataTable tbody tr.selected {
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
    <div class="card" style="height: 650px; margin-top: 50px; background-color:rgb(222, 222, 223);">
        <div class="card-body" style="display: flex; max-width: 1350px; margin: auto; padding: 20px;">
            <!-- Main Content Area (70%) -->
            <div style="flex: 0 0 100%; padding-right: 20px;">

                <div style="display: flex; align-items: center; margin-top: 5px;">
                   
                    
                    <label style="font-size: 14px; margin-left: 3px; width: 70px;"><b>Date </b></label>
                    <input type="date" name="chlog_date" class="input-text" id="ereport_date" style="width: 100px">

                    <label style="font-size: 16px; margin-left: 5px;"><b>User </b></label>
                    <select name="labuser_name" style="width: 150px; height: 30px; margin-left: 15px;" class="input-text" id="labuser_name">
                        <option value="%">All</option>
                        <?php
                        $query = "select a.uid, a.fname, a.lname 
                            FROM user a
                            INNER JOIN labUser b ON a.uid = b.user_uid
                            INNER JOIN Lab_labUser c ON b.luid = c.labUser_luid

                            WHERE c.lab_lid = '" . $_SESSION['lid'] . "' AND a.fname IS NOT NULL
                            AND a.fname != '' ORDER BY a.fname ASC";
                        
                        $Result = DB::select($query);
                        
                        foreach ($Result as $res) {
                            $uid = $res->uid;
                            $fullName = $res->fname . ' ' . $res->lname;
                            $displayText =  $fullName;
                            echo "<option value='{$fullName}'>
                                {$displayText}
                                </option>";
                        }
                        ?>
                    </select>

                    <label style="font-size: 16px; margin-left: 5px;"><b>Sample No</b></label>
                    <input type="text" name="chlog_button" class="input-text" id="erepor_sample_no" style="width: 130px">

                    <label style="font-size: 16px; margin-left: 5px;"><b>Test Name</b></label>
                   <select name="testgroup_name" style="width: 150px; height: 30px; margin-left: 15px;" class="input-text" id="testgroup_name">
                        <option value="%">All</option>
                        <?php
                        $query = "SELECT `tgid`, `name` FROM Testgroup WHERE `Lab_lid` = '" . $_SESSION['lid'] . "' ORDER BY `name` ASC";
                        $Result = DB::select($query);

                        foreach ($Result as $res) {
                            $tgid = $res->tgid;
                            $name = $res->name;
                            echo "<option value='{$tgid}'>{$name}</option>";
                        }
                        ?>
                    </select>

                     <label style="font-size: 16px; margin-left: 5px;"><b>Status</b></label>
                   <select name="ereport_status" style="width: 150px; height: 30px; margin-left: 15px;" class="input-text" id="ereport_status">
                        <option value="%">All</option>
                        <option value="pending">pending</option>
                        <option value="Done">Done</option>
                    </select>

                    
                    <input type="button" style="flex: 0 0 80px; margin-left: 10px;" class="btn" id="log_ser_btn" value="Search" onclick="SearchEreportData();">
                </div>
            </div>
        
        </div>

        <div class="pageTableScope" style="display: flex; height: 350px; margin-top: 10px; width: 100%;">
            <div style="flex: 1; padding-right: 10px;">
                <table style="font-family: Futura, 'Trebuchet MS', Arial, sans-serif; font-size: 13pt;" id="ereport_logdataTable" width="100%" border="0" 
                    cellspacing="2" cellpadding="0">
                    <tbody>
                        <tr>
                            <td valign="top">
                                <table border="1" style="border-color: #ffffff;" cellpadding="0" cellspacing="0" class="TableWithBorder" width="100%">
                                    <thead>
                                        <tr class="viewTHead">
                                            <td width="8%" class="fieldText" align="center">Date</td>
                                            <td width="8%" class="fieldText" align="center">Time</td>
                                            <td width="12%" class="fieldText" align="center">User</td>
                                            <td width="10%" class="fieldText" align="center">Sample No</td>
                                            <td width="18%" class="fieldText" align="center">Report Test Name</td>
                                            <td width="8%" class="fieldText" align="center">Delivery Status</td>
                                            <td width="10%" class="fieldText" align="center">Source </td>
                                        
                                        </tr>
                                    </thead>
                                    <tbody id="ereport_record_tbl">
                                        <!-- Dynamic content goes here -->
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <input type="button" style="flex: 0 0 80px; margin-left: 10px;" class="btn" id="log_save_btn" value="Save Log" onclick="saveEreportLog();">
            </div>
        </div>



        {{-- ############################################################################################################# --}}


    </div>

</div>




@stop
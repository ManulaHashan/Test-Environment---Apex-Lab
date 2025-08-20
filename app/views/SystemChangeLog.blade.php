<!-- **********~~~~~~~~~~~~~Manula Hashan's Devolopment~~~~~~~~~~~~~********** -->


<?php ?>
@extends('Templates/WiTemplate')


@section('title')
    Change Log
@stop

@section('head')

    <script src="{{ asset('JS/ReportCalculations.js') }}"></script>

    <script>
        $(document).ready(function() {
            loadRecordToTable();
        });

        document.addEventListener("DOMContentLoaded", function() {
            const today = new Date();
            const formattedDate = today.toISOString().split('T')[0];
            document.getElementById('chlog_from_date').value = formattedDate;
            document.getElementById('chlog_to_date').value = formattedDate;

        });

        // function loadRecordToTable() 
        // {
        //     $.ajax({
        //         url: 'getSystemChangeLogs',
        //         type: 'GET',
        //         dataType: 'json',
        //         success: function (data) {
        //             var html = '';

        //             if (data.length > 0) {
        //                 $.each(data, function (i, item) {
        //                     html += '<tr>' +
        //                         '<td align="center">' + item.date + '</td>' +
        //                         '<td align="center">' + item.time + '</td>' +
        //                         '<td align="center">' + item.page + '</td>' +
        //                         '<td align="center">' + item.button + '</td>' +
        //                         '<td align="center">System Change</td>' +
        //                         '<td align="center">' + item.user_luid + '</td>' +
        //                         '<td align="center">' + item.fname + '</td>' +
        //                         '<td align="center">' + item.position + '</td>' +
        //                         '</tr>';
        //                 });
        //             } else {
        //                 html = '<tr><td colspan="8" align="center">No records found</td></tr>';
        //             }

        //             $('#change_log_record_tbl').html(html);
        //         },
        //         error: function () {
        //             alert('Error loading data.');
        //         }
        //     });
        // }


        function loadRecordToTable() {
            const fromDate = $('#chlog_from_date').val();
            const toDate = $('#chlog_to_date').val();
            const page = $('#chlog_page').val();
            const button = $('#chlog_button').val();
            const uid = $('#labuser_uid').val();
            const fname = $('#labuser_name').val();

            $.ajax({
                url: 'getSystemChangeLogs',
                type: 'GET',
                dataType: 'json',
                data: {
                    fromDate: fromDate || '',
                    toDate: toDate || '',
                    page: page || '',
                    button: button || '',
                    uid: uid || '%',
                    fname: fname || '%'
                },
                success: function(data) {
                    let html = '';

                    if (data.length > 0) {
                        $.each(data, function(i, item) {
                            html += '<tr>' +
                                '<td align="center">' + item.date + '</td>' +
                                '<td align="center">' + item.time + '</td>' +
                                '<td align="left">' + item.page + '</td>' +
                                '<td align="left">' + item.button + '</td>' +
                                '<td align="left">' + item.descreption + ' </td>' +
                                '<td align="center">' + item.user_luid + '</td>' +
                                '<td align="center">' + item.fname + ' ' + item.lname + '</td>' +
                                '<td align="center">' + item.position + '</td>' +
                                '</tr>';
                        });
                    } else {
                        html = '<tr><td colspan="8" align="center">No records found</td></tr>';
                    }

                    $('#change_log_record_tbl').html(html);
                },
                error: function() {
                    alert('Error loading data.');
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

        #change_logdataTable table tr:hover {
            background-color: #28acbd;
            /* light cyan on hover */
            cursor: pointer;
        }

        #change_logdataTable tbody tr.selected {
            background-color: #4f8de5 !important;
            /* green for selected row */
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


                        <label style="font-size: 14px; margin-left: 3px; width: 70px;"><b>From </b></label>
                        <input type="date" name="chlog_from_date" class="input-text" id="chlog_from_date"
                            style="width: 130px">

                        <label style="font-size: 14px; margin-left: 5px; width: 60px;"><b>To </b></label>
                        <input type="date" name="chlog_to_date" class="input-text" id="chlog_to_date"
                            style="width: 130px">


                        <label style="font-size: 16px; margin-left: 5px;"><b>Page </b></label>
                        <input type="text" name="chlog_page" class="input-text" id="chlog_page" style="width: 200px">

                        <label style="font-size: 16px; margin-left: 5px;"><b>Button</b></label>
                        <input type="text" name="chlog_button" class="input-text" id="chlog_button" style="width: 130px">



                        <label style="font-size: 16px; margin-left: 5px;"><b>User's Name </b></label>
                        <select name="labuser_name01" style="width: 150px; height: 30px; margin-left: 15px;"
                            class="input-text" id="labuser_name">
                            <option value="%">All</option>
                            <?php
                            $query =
                                "select a.uid, a.fname, a.lname 
                                                            FROM user a
                                                            INNER JOIN labUser b ON a.uid = b.user_uid
                                                            INNER JOIN Lab_labUser c ON b.luid = c.labUser_luid
                            
                                                            WHERE c.lab_lid = '" .
                                $_SESSION['lid'] .
                                "' AND a.fname IS NOT NULL
                                                            AND a.fname != '' ORDER BY a.fname ASC";
                            
                            $Result = DB::select($query);
                            
                            foreach ($Result as $res) {
                                $uid = $res->uid;
                                $fullName = $res->fname . ' ' . $res->lname;
                                $displayText = $fullName;
                                echo "<option value='{$fullName}'>
                                                                {$displayText}
                                                                </option>";
                            }
                            ?>
                        </select>


                        <input type="button" style="flex: 0 0 80px; margin-left: 10px;" class="btn" id="log_ser_btn"
                            value="Search" onclick="loadRecordToTable();">
                    </div>
                </div>

            </div>

            <div class="pageTableScope" style="display: flex; height: 500px; margin-top: 10px; width: 100%;">
                <div style="flex: 1; padding-right: 10px;">
                    <table style="font-family: Futura, 'Trebuchet MS', Arial, sans-serif; font-size: 13pt;"
                        id="change_logdataTable" width="100%" border="0" cellspacing="2" cellpadding="0">
                        <tbody>
                            <tr>
                                <td valign="top">
                                    <table border="1" style="border-color: #ffffff;" cellpadding="0" cellspacing="0"
                                        class="TableWithBorder" width="100%">
                                        <thead>
                                            <tr class="viewTHead">
                                                <td width="8%" class="fieldText" align="center">Date</td>
                                                <td width="8%" class="fieldText" align="center">Time</td>
                                                <td width="12%" class="fieldText" align="center">Page</td>
                                                <td width="10%" class="fieldText" align="center">Button Name</td>
                                                <td width="18%" class="fieldText" align="center">Description</td>
                                                <td width="8%" class="fieldText" align="center">User_UID</td>
                                                <td width="10%" class="fieldText" align="center">User's Name</td>
                                                <td width="10%" class="fieldText" align="center">Designation</td>
                                            </tr>
                                        </thead>
                                        <tbody id="change_log_record_tbl">
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

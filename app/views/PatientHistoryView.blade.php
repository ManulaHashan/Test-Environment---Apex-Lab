<!-- **********~~~~~~~~~~~~~Manula Hashan's Devolopment~~~~~~~~~~~~~********** -->


<?php ?>
@extends('Templates/WiTemplate')


@section('title')
Patient History View
@stop

@section('head')

<script src="{{ asset('JS/ReportCalculations.js') }}"></script>

<script>


 $(function () {
        var invoiceId = sessionStorage.getItem('invoiceId');
        if (invoiceId) {
            $('#invoiceId').val(invoiceId); 
            loadRecordToPhistoryTable();    
        }
    });


    
    // function loadRecordToPhistoryTable() {
    //     var iid = $('#invoiceId').val(); // Get the value from the input field

    //     if (!iid) {
    //         alert("Invoice ID not set!");
    //         return;
    //     }

    //     $.ajax({
    //         type: "GET",
    //         url: "getAllPatientHistoryRecords",
    //         data: { iid: iid }, // Now dynamically sending it
    //         success: function(tbl_records) {
    //             $('#patient_history_rec_tbl').html(tbl_records);
    //         },
    //         error: function(xhr, status, error) {
    //             alert('Error: ' + xhr.status + ' - ' + xhr.statusText + '\n' + 'Details: ' + xhr.responseText);
    //             console.error('Error details:', {
    //                 status: xhr.status,
    //                 statusText: xhr.statusText,
    //                 responseText: xhr.responseText,
    //                 error: error
    //             });
    //         }
    //     });
    // }
 $(document).ready(function () {
    // Handle row double click
    $('#patient_history_rec_tbl').on('dblclick', 'tr', function () {
        var invoiceId = $(this).find('td').eq(7).text().trim(); // Get invoice ID from 7th column

        if (invoiceId) {
            var url = "/invoicePayments?iid=" + invoiceId;
            window.open(url, '_blank'); // open in new tab
        } else {
            alert("Invoice ID not found in selected row.");
        }
    });
});



    function loadRecordToPhistoryTable() {
        var iid = $('#invoiceId').val();

        if (!iid) {
            alert("Invoice ID not set!");
            return;
        }

        $.ajax({
            type: "GET",
            url: "getAllPatientHistoryRecords",
            data: { iid: iid },
            dataType: "json",
            success: function(response) {
                $('#patient_history_rec_tbl').html(response.html);

                let fname = response.patient.fname || "";
                let mname = response.patient.mname || "";
                $('#patirnt_name').text(fname + ' ' + mname);
                $('#patirnt_pid').text(response.patient.pid || "");
                $('#patirnt_contact').text(response.patient.tpno || "");
                $('#tot_visit').text(response.total || 0);
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


 

    //*************************************************************************************************

   


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

      .selected-row {
        background-color: #f44336 !important; /* Red */
        color: white;
    }
    

</style>

@stop

@section('body')



<div class="container">
    <div class="card" style="height: 550px; margin-top: 50px; background-color:rgb(222, 222, 223);">
        <div class="card-body" style="display: flex; max-width: 1350px; margin: auto; padding: 20px;">
            <!-- Main Content Area (70%) -->
        <div style="display: flex; align-items: center; gap: 15px; margin-top: 5px; flex-wrap: wrap;">
            <input type="hidden" id="invoiceId" value="">
            <label style="font-size: 16px; font-weight: bold;">Name:</label>
            <label id="patirnt_name" style="font-size: 16px; font-style: timesnewramon;"></label>

            <label style="font-size: 16px; font-weight: bold; margin-left: 20px;">PID:</label>
            <label id="patirnt_pid" style="font-size: 16px;font-style: timesnewramon;"></label>

            <label style="font-size: 16px; font-weight: bold; margin-left: 20px;">Contact NO:</label>
            <label id="patirnt_contact" style="font-size: 16px;"></label>

            <label style="font-size: 16px; font-weight: bold; margin-left: 20px;">Total Visits:</label>
            <label id="tot_visit" style="font-size: 16px;"></label>

        </div>
        
        </div>

        <div class="pageTableScope" style="display: flex; height: 350px; margin-top: 10px; width: 100%;">
            <div style="flex: 1; padding-right: 10px;">
                <table style="font-family: Futura, 'Trebuchet MS', Arial, sans-serif; font-size: 13pt;" id="pat_hist_dataTable" width="100%" border="0" cellspacing="2" cellpadding="0">
                    <tbody>
                        <tr>
                            <td valign="top">
                                <table border="1" style="border-color: #ffffff;" cellpadding="0" cellspacing="0" class="TableWithBorder" width="100%">
                                    <thead>
                                        <tr class="viewTHead">
                                            <td width="8%" class="fieldText" align="center">Sample NO</td>
                                            <td width="8%" class="fieldText" align="center">Date</td>
                                            <td width="8%" class="fieldText" align="center">Time</td>
                                            <td width="18%" class="fieldText" align="center">Patient Name</td>
                                            <td width="10%" class="fieldText" align="center">Invoice Status</td>
                                            <td width="8%" class="fieldText" align="center">Report Status </td>
                                            <td width="18%" class="fieldText" align="center">Delivery Status</td>
                                            <td width="8%" class="fieldText" align="center">Invoice ID</td>
                                        </tr>
                                    </thead>
                                    <tbody id="patient_history_rec_tbl">
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
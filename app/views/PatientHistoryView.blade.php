<!-- **********~~~~~~~~~~~~~Manula Hashan's Devolopment~~~~~~~~~~~~~********** -->


<?php ?>
@extends('Templates/WiTemplate')


@section('title')
Patient History View
@stop

@section('head')

<script src="{{ asset('JS/ReportCalculations.js') }}"></script>

<script>




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


    

</style>

@stop

@section('body')



<div class="container">
    <div class="card" style="height: 550px; margin-top: 50px; background-color:rgb(222, 222, 223);">
        <div class="card-body" style="display: flex; max-width: 1350px; margin: auto; padding: 20px;">
            <!-- Main Content Area (70%) -->
            <div style="flex: 0 0 100%; padding-right: 20px;">
                <div style="flex: 0 0 70%; padding-right: 20px;">
                </div>
                

                <div style="display: flex; align-items: center; margin-top: 5px;">
                    <label style="font-size: 16px; margin-left: 5px;"><b>Name: </b></label>
                    <input type="text" name="first_name" class="input-text" id="patirnt_name" style="width: 90px">
                    <label style="font-size: 16px; margin-left: 5px;"><b>PID</b></label>
                    <input type="text" name="last_name" class="input-text" id="patirnt_pid" style="width: 90px">
                    <label style="font-size: 16px; margin-left: 5px;"><b>Contact NO </b></label>
                    <input type="text" name="contact" class="input-text" id="patirnt_contact" style="width: 90px">
                    <label style="font-size: 16px; margin-left: 5px;"><b>Total Visits</b></label>
                    <input type="text" name="contact" class="input-text" id="tot_visit" style="width: 90px">
 
                </div>
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
                                            <td width="12%" class="fieldText" align="center">Date</td>
                                            <td width="18%" class="fieldText" align="center">Time</td>
                                            <td width="18%" class="fieldText" align="center">Patient Name</td>
                                            <td width="10%" class="fieldText" align="center">Invoice Status</td>
                                            <td width="8%" class="fieldText" align="center">Report Status </td>
                                            <td width="10%" class="fieldText" align="center">Delivery Status</td>
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
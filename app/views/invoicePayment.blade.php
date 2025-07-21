<!-- **********~~~~~~~~~~~~~Manula Hashan's Devolopment~~~~~~~~~~~~~********** -->


<?php ?>
@extends('Templates/WiTemplate')


@section('title')
Invoice Payments
@stop

@section('head')

<script src="{{ asset('JS/ReportCalculations.js') }}"></script>

<script>

$(document).ready(function ()
     {
         loadRecordToInvPaymentTable();
         loadInvoicePatientDetails();
        const today = new Date().toISOString().split('T')[0];
        $('#inv_date').val(today);

     
});

function loadRecordToInvPaymentTable() {
    const params = new URLSearchParams(window.location.search);
    const invoiceId = params.get('iid');
       
    $.ajax({
        type: "GET",
        url: "getAllpayments",
        data: { invoice_iid: invoiceId },
        success: function(tbl_records) {
            $('#inv_record_tbl').html(tbl_records);
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


function loadInvoicePatientDetails() {
    const params = new URLSearchParams(window.location.search);
    const invoiceId = params.get('iid');
    $.ajax({
        type: "GET",
        url: "getInvoicePatientDetails",
        data: { 
            invoice_iid: invoiceId 
        },
        success: function (response) {

            if (response.success && response.data.length > 0) {
                var invoiceDetails = response.data[0]; // Access the first result
                // Update the text inside the <b> tags within the labels
                $('#inv_id ').text(invoiceDetails.invoice_id);
                $('#pname ').text(invoiceDetails.patient_name);
                $('#inv_total ').text(invoiceDetails.total_amount);
                $('#inv_paid ').text(invoiceDetails.paid_amount);
                $('#inv_days ').text(invoiceDetails.days_left);
                  // Store original due amount in a data attribute
                  $('#due_amount')
                    .text(invoiceDetails.due_amount)
                    .data('original-due', parseFloat(invoiceDetails.due_amount) || 0);
                
                // Initialize tender amount listener
                setupTenderAmountListener();
            } else {
                alert('This is Not Paid Invoice. No Payment data found for this patient');
            }
        },
        error: function (xhr, status, error) {
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


$(document).on('click', '.delete-btn', function () {
    const ipid = $(this).data('ipid');

    $.ajax({
        type: "POST",
        url: "/deletePayment",
        data: {
            ipid: ipid
        },
        success: function (response) {
            alert(response.message);
            loadRecordToInvPaymentTable();
            loadInvoicePatientDetails();
        },
        error: function (xhr) {
            alert('Error deleting payment: ' + xhr.responseText);
        }
    });
});

function savePayment() {
    const params = new URLSearchParams(window.location.search);
    const invoiceId = params.get('iid');
    // Get the values from the input fields
    // var invoiceId = $('#inv_id').text();
    var date = $('#inv_date').val();
    var amount = $('#paid_amount').val();
    var chequeNo = $('#cheque_amount').val();
    var type = $('#Payment_method').val();


   

    // AJAX request to save the payment data
    $.ajax({
        type: "POST",
        url: "savePayment", // Endpoint for saving payment
        data: {
            'INVID': invoiceId,
            'date': date,
            'amount': amount,
            'cno': chequeNo,
            'type': type
        },
        success: function(response) {
            if (response.status == "success") {
                alert('Payment saved successfully!');
                loadRecordToInvPaymentTable();
               
                // Reset input fields
                $('#paid_amount').val('');
                $('#tender').val('');
                $('#cheque_amount').val('');
                loadInvoicePatientDetails(); 
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr) {
            console.log('Error:', xhr); 
            var errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'An unexpected error occurred.';
            alert(errorMsg); 
        }
    });
}





    //*************************************************************************************************
function setupTenderAmountListener() {
        const tenderInput = $('#tender');
        const dueAmountLabel = $('#due_amount');
        const originalLabel = $('label[for="due_amount"]');
        const paidAmountInput = $('#paid_amount');
        
        tenderInput.on('input', function() {
            const originalDueAmount = dueAmountLabel.data('original-due') || 0;
            const tenderAmount = parseFloat($(this).val()) || 0;

            const paidAmount = Math.min(tenderAmount, originalDueAmount);

            const balance = tenderAmount - originalDueAmount;
            const remainingDue = originalDueAmount - tenderAmount;

            paidAmountInput.val(paidAmount.toFixed(2));

            if (tenderAmount > originalDueAmount) {
                // Show balance as positive number
                dueAmountLabel.text(Math.abs(balance).toFixed(2));
                originalLabel.html('<b style="color: green;">Balance Rs:</b>');
                dueAmountLabel.css('color', 'green');
            } else {
                // Show remaining due amount
                dueAmountLabel.text(remainingDue.toFixed(2));
                originalLabel.html('<b style="color: red;">Due Rs:</b>');
                dueAmountLabel.css('color', 'red');
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



    

</style>
@stop

@section('body')



<div class="container">
    
    <div class="card" style="height: 850px; margin-top: 50px; background-color:rgb(222, 222, 223);">
            <h1>Invoice Payments</h1>
            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                <label for="inv_id" style="font-size: 14px; min-width: 90px;"><b>Inv ID:</b></label>
                <label id="inv_id" style="font-size: 18px; width: 190px;"></label>

                <label for="inv_pname" style="font-size: 14px; min-width: 90px;">Name:</label>
                <label id="pname" style="font-size: 18px;width: 290px;"></label>

                <label for="inv_total" style="font-size: 14px; min-width: 90px;"><b>Total Rs:</b></label>
                <label id="inv_total" style="font-size: 18px;width: 90px;"></label>

                <label for="inv_paid" style="font-size: 14px; min-width: 160px;"><b>Total Paid Rs:</b></label>
                <label id="inv_paid" style="font-size: 18px; width: 90px;"></label>

                <label for="inv_days" style="font-size: 14px; min-width: 90px;"><b>Days:</b></label>
                <label id="inv_days" style="font-size: 18px;width: 90px;"></label>
              </div>
        <div class="pageTableScope" style="display: flex; height: 350px; margin-top: 10px; width: 100%;">
          
            <!-- Left Side: Table -->
            <div style="flex: 0 0 30%; padding-left: 10px;">

                <!-- Date -->
               
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <label for="inv_date" style="font-size: 14px; min-width: 90px;"><b>By Date:</b></label>
                    <input type="date" name="inv_date" id="inv_date" class="input-text" style="width: 180px; height: 30px; font-size: 14px;">
                </div>
                <!-- Payment Method -->
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <label for="Payment_method" style="font-size: 14px; min-width: 90px;"><b>Method:</b></label>
                    <select name="Payment_method" id="Payment_method" class="input-text" style="width: 200px; height: 30px; font-size: 14px;">
                        <?php
                        $Result = DB::select("SELECT idpaymethod, name FROM paymethod");
                        foreach ($Result as $res) {
                            $paymentName = $res->name;
                            $paymentId = $res->idpaymethod;
                            $displayText = $paymentId . " : " . $paymentName;
                        ?>
                            <option value="<?= $paymentId ?>" <?= $paymentId == 1 ? 'selected' : '' ?>> <!-- Use only the payment ID -->
                                <?= $displayText ?>
                            </option>
                        <?php } ?>
                    </select>
                    
                </div>
                <!-- Tender -->
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <label for="tender" style="font-size: 14px; min-width: 90px;"><b>Tender Rs:</b></label>
                    <input type="text" name="tender" id="tender" class="input-text" style="width: 180px; height: 30px; font-size: 14px;">
                </div>
                <!-- Paid Amount -->
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <label for="paid_amount" style="font-size: 14px; min-width: 90px;"><b>Paid Rs:</b></label>
                    <input type="text" name="paid_amount" id="paid_amount" class="input-text" readonly style="width: 180px; height: 30px; font-size: 14px;">
                </div>
                <!-- Due Amount -->
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <label for="due_amount" style="font-size: 14px; min-width: 90px; color: red;"><b>Due Rs:</b></label>
                    <label id="due_amount" style="font-size: 18px;color: red;"></label>
                </div>
                <!-- Cheque No -->
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <label for="cheque_amount" style="font-size: 14px; min-width: 90px;"><b>Cheque No:</b></label>
                    <input type="text" name="cheque_amount" id="cheque_amount" class="input-text" style="width: 180px; height: 30px; font-size: 14px;">
                </div>
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <input type="button" style="flex: 0 0 80px; margin-left: 10px; color:green" class="btn" id="ser_btn" value="Save" onclick="savePayment()">
                    <input type="button" style="flex: 0 0 80px; margin-left: 10px; color:rgb(23, 43, 179)" class="btn" id="ser_btn" value="Print Bill">
                   
                </div>
              
              </div>
              
            <!-- Right Side: Additional Content -->
            <div style="flex: 1; padding-right: 10px;">
                <table style="font-family: Futura, 'Trebuchet MS', Arial, sans-serif; font-size: 13pt;" id="inv_paymet_record_tbl" width="100%" border="0" cellspacing="2" cellpadding="0">
                    <tbody>
                        <tr>
                            <td valign="top">
                                <table border="1" style="border-color: #ffffff;" cellpadding="0" cellspacing="0" class="TableWithBorder" width="100%">
                                    <thead>
                                        <tr class="viewTHead">
                                            <td width="15%" class="fieldText" align="center">ID</td>
                                            <td width="15%" class="fieldText" align="center">Date</td>
                                            <td width="15%" class="fieldText" align="center">Method</td>
                                            <td width="15%" class="fieldText" align="center">Amount Rs</td>
                                            <td width="15%" class="fieldText" align="center">Cheque No</td>
                                            <td width="25%" class="fieldText" align="center">User</td>
                                            <td width="25%" class="fieldText" align="center">Action</td>
                                        </tr>
                                    </thead>
                                    <tbody id="inv_record_tbl">
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
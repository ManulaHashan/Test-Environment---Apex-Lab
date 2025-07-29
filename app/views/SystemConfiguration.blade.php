<!-- **********~~~~~~~~~~~~~Manula Hashan's Devolopment~~~~~~~~~~~~~********** -->


<?php ?>
@extends('Templates/WiTemplate')


@section('title')
System Configuration
@stop

@section('head')

<script src="{{ asset('JS/ReportCalculations.js') }}"></script>

<script>



 $(document).ready(function() {
    $.ajax({
        url: 'getAllAddPatientConfigs',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                let data = response.data[0]; 
                if (data) {
                    // Select fields (1 = Active, 0 = Inactive)
                    $('#tp_no').val(data.tpno);
                    $('#address').val(data.address);
                    $('#refby').val(data.refby);
                    $('#type').val(data.type);
                    $('#typedv').val(data.typedv);
                    $('#viewinvoice').val(data.viewinvoice);
                    $('#tot').val(data.tot);
                    $('#discount').val(data.discount);
                    $('#discount_dv').val(data.discountdv);
                    $('#gtot').val(data.gtot);
                    $('#paymeth').val(data.paymeth);
                    $('#payment').val(data.payment);
                    $('#printinvoicedv').val(data.printinvoicedv);
                    $('#directresultenter').val(data.directresultenter);
                    $('#patientsuggestion').val(data.patientsuggestion);
                    $('#focusonpayment').val(data.focusonpayment);
                    $('#patientinitials').val(data.patientinitials);
                    $('#invoice_copy').val(data.invoice_copy);
                    $('#duplicate_barcodes').val(data.duplicate_barcodes);
                    $('#autoadd_center_discount').val(data.autoadd_center_discount);
                    $('#print_bill_barcode').val(data.print_bill_barcode);
                    $('#additional_test_barcode_name').val(data.additional_test_barcode_name);
                    $('#invoice_sms').val(data.invoice_sms);
                    $('#disable_branch_bill_print').val(data.disable_branch_bill_print);
                    $('#bulk_special_barcode_skip').val(data.bulk_special_barcode_skip);
                    $('#register_by_token').val(data.registerbytoken);

                    // Text fields
                    $('#ref_by_dv').val(data.refbydv);
                    $('#gender_dv').val(data.genderdv);
                    $('#pay_meth_dv').val(data.paymethdv);
                    $('#duplicate_barset').val(data.duplicate_barset);
                    $('#bill_allowed_amount_limit').val(data.bill_allowed_amount_limit);
                    $('#bill_duplicate_count').val(data.bill_duplicate_count);
                    $('#print_center_receipt').val(data.print_center_receipt);
                    $('#grand_total_roundup').val(data.grandtotal_roundup);
                    $('#inward_price_increase').val(data.inward_priceincrease);

                    // Basic fields
                    $('#id').val(data.id);
                    $('#lab_lid').val(data.lab_lid);
                }
            } else {
                alert("Error Occored: " + response.message);
            }
        },
        error: function(xhr) {
            alert("Server error: " + xhr.responseText);
        }
    });

     $.ajax({
        url: '/getAllReportConfigs',
        type: 'GET',
        success: function (response) {
            if (response.status === 'success') {
                const data = response.data[0]; 
                // Text fields
                $('#rcid').val(data.id);
                $('#rclab_lid').val(data.lab_lid);
                $('#headerurl').val(data.headerurl);
                $('#footerurl').val(data.footerurl);
                $('#sign').val(data.sign);
                $('#viewregdate').val(data.viewregdate);
                $('#headerdefault').val(data.headerdefault);
                $('#rcdob').val(data.dob);

                // Select fields
                setSelect('header', data.header);
                setSelect('footer', data.footer);
                setSelect('pageheading', data.pageheading);
                setSelect('rcdate', data.date);
                setSelect('confidential', data.confidential);
                setSelect('fontitelic', data.fontitelic);
                setSelect('agelabel', data.agelabel);
                setSelect('valuestate', data.valuestate);
                setSelect('viewsno', data.viewsno);
                setSelect('viewinitials', data.viewinitials);
                setSelect('viewspecialnote', data.viewspecialnote);
                setSelect('enableblooddrew', data.enableblooddrew);
                setSelect('enablecollected', data.enablecollected);
                setSelect('reference_in_invoice', data.reference_in_invoice);
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function (xhr) {
            alert('Server error: ' + xhr.statusText);
        }
    });

    function setSelect(id, value) {
        $('#' + id).val(value === 1 ? '1' : '0');
        }

        $.ajax({
                url: '/getAllConfigConfigs',
                type: 'GET',
                success: function (response) {
                    if (response.status === 'success') {
                        const data = response.data[0];

                        $('#config_id').val(data.idconfigs); 
                        $('#config_lab_lid').val(data.Lab_lid); 
                        $('#separate_prices_branch').val(data.separate_prices_branch);
                        $('#worksheet_pertest').val(data.worksheet_pertest);
                        $('#worksheet_perdept').val(data.worksheet_perdept);
                        $('#report_auth_1').val(data.report_auth_1);
                        $('#report_auth_2').val(data.report_auth_2);
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function (xhr, status, error) {
                    alert('AJAX Error: ' + error);
                }
            });

              $.ajax({
                url: '/getAllSMSConfigs',
                type: 'GET',
                success: function (response) {
                    if (response.status === 'success') {
                        const data = response.data[0]; 
                        // console.log(data); 
                        $('#sms_id').val(data.id); 
                        $('#sms_lab_lid').val(data.Lab_lid); 
                        $('#smsusername').val(data.username);
                        $('#password').val(data.password);
                        $('#src').val(data.src);
                        $('#isactiveauto').val(data.isactiveauto);
                        
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function (xhr, status, error) {
                    alert('AJAX Error: ' + error);
                }
            });
       
    });



    //*************************************************************************************************


    function updateAddpatientConfigurations() 
    {
        var data = {
            id: $('#id').val(),
            lab_lid: $('#lab_lid').val(),
            tp_no: $('#tp_no').val(),
            address: $('#address').val(),
            refby: $('#refby').val(),
            refbydv: $('#ref_by_dv').val(),
            type: $('#type').val(),
            typedv: $('#typedv').val(),
            genderdv: $('#gender_dv').val(),
            viewinvoice: $('#viewinvoice').val(),
            tot: $('#tot').val(),
            discount: $('#discount').val(),
            discountdv: $('#discount_dv').val(),
            gtot: $('#gtot').val(),
            paymeth: $('#paymeth').val(),
            paymethdv: $('#pay_meth_dv').val(),
            payment: $('#payment').val(),
            printinvoicedv: $('#printinvoicedv').val(),
            directresultenter: $('#directresultenter').val(),
            patientsuggestion: $('#patientsuggestion').val(),
            focusonpayment: $('#focusonpayment').val(),
            patientinitials: $('#patientinitials').val(),
            invoice_copy: $('#invoice_copy').val(),
            duplicate_barcodes: $('#duplicate_barcodes').val(),
            duplicate_barset: $('#duplicate_barset').val(),
            bill_allowed_amount_limit: $('#bill_allowed_amount_limit').val(),
            bill_duplicate_count: $('#bill_duplicate_count').val(),
            print_center_receipt: $('#print_center_receipt').val(),
            autoadd_center_discount: $('#autoadd_center_discount').val(),
            print_bill_barcode: $('#print_bill_barcode').val(),
            additional_test_barcode_name: $('#additional_test_barcode_name').val(),
            invoice_sms: $('#invoice_sms').val(),
            disable_branch_bill_print: $('#disable_branch_bill_print').val(),
            bulk_special_barcode_skip: $('#bulk_special_barcode_skip').val(),
            inward_priceincrease: $('#inward_price_increase').val(),
            grandtotal_roundup: $('#grand_total_roundup').val(),
            registerbytoken: $('#register_by_token').val()
        };

        $.ajax({
            url: 'updateAddPatientConfig',
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.status === 'success') {
                    // alert('Details Update Successfull');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Server error: ' + xhr.responseText);
            }
        });
    }



    //*************************************************************************************************
    function updateReportConfigurations() 
    {
            var data = {
                rcid: $('#rcid').val(),
                rclab_lid: $('#rclab_lid').val(),
                header: $('#header').val(),
                headerurl: $('#headerurl').val(),
                footer: $('#footer').val(),
                footerurl: $('#footerurl').val(),
                pageheading: $('#pageheading').val(),
                rcdate: $('#rcdate').val(),
                sign: $('#sign').val(),
                confidential: $('#confidential').val(),
                fontitelic: $('#fontitelic').val(),
                agelabel: $('#agelabel').val(),
                headerdefault: $('#headerdefault').val(),
                valuestate: $('#valuestate').val(),
                viewsno: $('#viewsno').val(),
                viewregdate: $('#viewregdate').val(),
                viewinitials: $('#viewinitials').val(),
                viewspecialnote: $('#viewspecialnote').val(),
                enableblooddrew: $('#enableblooddrew').val(),
                enablecollected: $('#enablecollected').val(),
                reference_in_invoice: $('#reference_in_invoice').val(),
                rcdob: $('#rcdob').val()
            };

            $.ajax({
                url: 'updateReportConfig', 
                type: 'POST',
                data: data,
                success: function(response) {
                    if (response.status === 'success') {
                        // alert('Report Configurations Updated Successfully!');
                    } else {
                        alert('Update Failed: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Server error: ' + xhr.responseText);
                }
            });
    }

    //*************************************************************************************************
    function updateConfigConfigurations() 
    {
    
        const config_id = $('#config_id').val();
        const separate_prices_branch = $('#separate_prices_branch').val();
        const worksheet_pertest = $('#worksheet_pertest').val();
        const worksheet_perdept = $('#worksheet_perdept').val();
        const report_auth_1 = $('#report_auth_1').val();
        const report_auth_2 = $('#report_auth_2').val();

        $.ajax({
            url: '/updateConfigConfigurations',
            type: 'POST',
            data: {
                config_id: config_id,
                separate_prices_branch: separate_prices_branch,
                worksheet_pertest: worksheet_pertest,
                worksheet_perdept: worksheet_perdept,
                report_auth_1: report_auth_1,
                report_auth_2: report_auth_2
            },
            success: function (response) {
                if (response.status === 'success') {
                    // alert('Successfully Updated!');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function (xhr, status, error) {
                alert('AJAX Error: ' + error);
            }
        });
    }
    //*************************************************************************************************
    function updateSMSConfigurations() 
    {
        const sms_id = $('#sms_id').val();
        const smsusername = $('#smsusername').val();
        const password = $('#password').val();
        const src = $('#src').val();
        const isactiveauto = $('#isactiveauto').val();

        $.ajax({
            url: '/updatSMSConfigurations',
            type: 'POST',
            data: {
                sms_id: sms_id,
                smsusername: smsusername,
                password: password,
                src: src,
                isactiveauto: isactiveauto
            },
            success: function (response) {
                if (response.status === 'success') {
                    alert('Successfully Updated!');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function (xhr, status, error) {
                alert('AJAX Error: ' + error);
            }
        });
    }

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

 
       .TableWithBorder td, .TableWithBorder th {
        padding: 15px;
        border: 1px solid #999;
        white-space: nowrap;
    }

    .TableWithBorder {
        border-collapse: collapse;
    }

</style>

@stop

@section('body')



<div class="container">
    <div class="card" style="height: 3550px; margin-top: 50px; background-color:rgb(222, 222, 223);">
        <div class="card-body" style="display: flex; max-width: 1350px; margin: auto; padding: 20px;">
            <!-- Main Content Area (70%) -->
        <div style="display: flex; align-items: left; gap: 15px; margin-top: 5px; flex-wrap: wrap;">
            <h1>System Configurations</h1>
           
        </div>
        
        </div>

    


        <table>
        <tr>
           <td>
                <div style="display: flex; align-items: left; gap: 15px; margin-top: 5px; flex-wrap: wrap;">
                   
                    <label style="font-size: 16px; font-weight: bold;">Add Patient Config Table Configurations</label>

                </div>
                <div class="card" style="width: 620px; margin-top: 10px;">
                    <div class="card-body" style="display: flex; flex-direction: column; gap: 12px;">

                    <div style="display: flex; align-items: center;">
                    <label for="id" style="font-size:14px; min-width:200px; font-weight:700;">ID:</label>
                    <input type="text" name="id" id="id" class="form-control" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;" disabled>
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="lab_lid" style="font-size:14px; min-width:200px; font-weight:700;">Lab LID:</label>
                    <input type="text" name="lab_lid" id="lab_lid" class="form-control" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;"disabled>
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="tp_no1" style="font-size:14px; min-width:200px; font-weight:700;">TP No:</label>
                    <select name="tp_no" id="tp_no" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="address" style="font-size:14px; min-width:200px; font-weight:700;">Address:</label>
                    <select name="address" id="address" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="ref_by" style="font-size:14px; min-width:200px; font-weight:700;">Ref By:</label>
                    <select name="refby" id="refby" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="ref_by_dv" style="font-size:14px; min-width:200px; font-weight:700;">Ref By DV:</label>
                   <input type="text" name="ref_by_dv" id="ref_by_dv" class="form-control"
                        style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="type" style="font-size:14px; min-width:200px; font-weight:700;">Type:</label>
                    <select name="type" id="type" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="type_dv" style="font-size:14px; min-width:200px; font-weight:700;">Type DV:</label>
                    <select name="typedv" id="typedv" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="gender_dv" style="font-size:14px; min-width:200px; font-weight:700;">Gender DV:</label>
                    <input type="text" name="gender_dv" id="gender_dv" class="form-control" 
                        style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="view_invoice" style="font-size:14px; min-width:200px; font-weight:700;">View Invoice:</label>
                    <select name="viewinvoice" id="viewinvoice" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="total" style="font-size:14px; min-width:200px; font-weight:700;">Total:</label>
                    <select name="tot" id="tot" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="discount" style="font-size:14px; min-width:200px; font-weight:700;">Discount:</label>
                    <select name="discount" id="discount" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="discount_dv" style="font-size:14px; min-width:200px; font-weight:700;">Discount DV:</label>
                    <input type="text" name="discount_dv" id="discount_dv" class="form-control" 
                        style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="grand_total" style="font-size:14px; min-width:200px; font-weight:700;">Grand Total:</label>
                    <select name="gtot" id="gtot" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="pay_meth" style="font-size:14px; min-width:200px; font-weight:700;">Pay Meth:</label>
                    <select name="paymeth" id="paymeth" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="pay_meth_dv" style="font-size:14px; min-width:200px; font-weight:700;">Pay Meth DV:</label>
                    <input type="text" name="pay_meth_dv" id="pay_meth_dv" class="form-control" 
                        style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="payment" style="font-size:14px; min-width:200px; font-weight:700;">Payment:</label>
                    <select name="payment" id="payment" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="print_invoice_dv" style="font-size:14px; min-width:200px; font-weight:700;">Print Invoice DV:</label>
                    <select name="printinvoicedv" id="printinvoicedv" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="direct_result_enter" style="font-size:14px; min-width:200px; font-weight:700;">Direct Result Enter:</label>
                        <select name="directresultenter" id="directresultenter" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="patient_suggestion" style="font-size:14px; min-width:200px; font-weight:700;">Patient Suggestion:</label>
                    <select name="patientsuggestion" id="patientsuggestion" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="focus_on_payment" style="font-size:14px; min-width:200px; font-weight:700;">Focus On Payment:</label>
                    <select name="focusonpayment" id="focusonpayment" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="patient_initials" style="font-size:14px; min-width:200px; font-weight:700;">Patient Initials:</label>
                    <select name="patientinitials" id="patientinitials" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="invoice_copy" style="font-size:14px; min-width:200px; font-weight:700;">Invoice Copy:</label>
                    <select name="invoice_copy" id="invoice_copy" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="duplicate_barcodes" style="font-size:14px; min-width:200px; font-weight:700;">Duplicate Barcodes:</label>
                    <select name="duplicate_barcodes" id="duplicate_barcodes" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="duplicate_barset" style="font-size:14px; min-width:200px; font-weight:700;">Duplicate Barset:</label>
                    <input type="text" name="duplicate_barset" id="duplicate_barset" class="form-control" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="bill_allowed_amount_limit" style="font-size:14px; min-width:200px; font-weight:700;">Bill Allowed Amount Limit:</label>
                    <input type="text" name="bill_allowed_amount_limit" id="bill_allowed_amount_limit" class="form-control" 
                        style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;"
                          oninput="this.value = this.value
                                    .replace(/[^0-9.]/g, '')               
                                    .replace(/^(\.)/, '0.')               
                                    .replace(/(\..*?)\..*/g, '$1')        
                                    .replace(/^(\d+)(\.\d{0,2})?.*$/, '$1$2') ">
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="bill_duplicate_count" style="font-size:14px; min-width:200px; font-weight:700;">Bill Duplicate Count:</label>
                    <input type="text" name="bill_duplicate_count" id="bill_duplicate_count" class="form-control" 
                        style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="print_center_receipt" style="font-size:14px; min-width:200px; font-weight:700;">Print Center Receipt:</label>
                    <input type="text" name="print_center_receipt" id="print_center_receipt" class="form-control" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="auto_add_center_discount" style="font-size:14px; min-width:200px; font-weight:700;">Auto Add Center Discount:</label>
                    <select name="autoadd_center_discount" id="autoadd_center_discount" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="print_bill_barcode" style="font-size:14px; min-width:200px; font-weight:700;">Print Bill Barcode:</label>
                    <select name="print_bill_barcode" id="print_bill_barcode" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="additional_test_barcode_name" style="font-size:14px; min-width:200px; font-weight:700;">Additional Test Barcode Name:</label>
                    <select name="additional_test_barcode_name" id="additional_test_barcode_name" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="invoice_sms" style="font-size:14px; min-width:200px; font-weight:700;">Invoice SMS:</label>
                    <select name="invoice_sms" id="invoice_sms" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="disable_branch_bill_print" style="font-size:14px; min-width:200px; font-weight:700;">Disable Branch Bill Print:</label>
                    <select name="disable_branch_bill_print" id="disable_branch_bill_print" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="bulk_special_barcode_skip" style="font-size:14px; min-width:200px; font-weight:700;">Bulk Special Barcode Skip:</label>
                    <select name="bulk_special_barcode_skip" id="bulk_special_barcode_skip" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="inward_price_increase" style="font-size:14px; min-width:200px; font-weight:700;">Inward Price Increase:</label>
                    <input type="text" name="inward_price_increase" id="inward_price_increase" class="form-control" 
                        style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;"
                        oninput="this.value = this.value
                                    .replace(/[^0-9.]/g, '')               
                                    .replace(/^(\.)/, '0.')               
                                    .replace(/(\..*?)\..*/g, '$1')        
                                    .replace(/^(\d+)(\.\d{0,2})?.*$/, '$1$2') ">
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="grand_total_roundup" style="font-size:14px; min-width:200px; font-weight:700;">Grand Total Roundup:</label>
                    <input type="text" name="grand_total_roundup" id="grand_total_roundup" class="form-control" 
                        style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;"
                        oninput="this.value = this.value
                                    .replace(/[^0-9.]/g, '')               
                                    .replace(/^(\.)/, '0.')               
                                    .replace(/(\..*?)\..*/g, '$1')        
                                    .replace(/^(\d+)(\.\d{0,2})?.*$/, '$1$2') ">
                    </div>

                    <div style="display: flex; align-items: center;">
                    <label for="register_by_token" style="font-size:14px; min-width:200px; font-weight:700;">Register by Token:</label>
                    <select name="registerByToken" id="register_by_token" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    </div>
                        
                    </div>
                    <input type="button" style="display: none;flex: 0 0 80px; margin-left: 10px;" class="btn" id="update_btn" value="Update" onclick="updateAddpatientConfigurations()">
                </div>
           </td>

           <td>
                <div style="display: flex; align-items: left; gap: 15px; margin-top: -640px; flex-wrap: wrap;">
                    <input type="hidden" id="invoiceId" value="">
                    <label style="font-size: 16px; font-weight: bold;">Add Report Config Table Configurations</label>

                </div>
                <div class="card" style="width: 620px; margin-top: 10px;">
                <div class="card-body" style="display: flex; flex-direction: column; gap: 12px;">

                <div style="display: flex; align-items: center;">
                <label for="id" style="font-size:14px; min-width:200px; font-weight:700;">ID:</label>
                <input type="text" name="rcid" id="rcid" class="form-control" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;"disabled>
                </div>

                <div style="display: flex; align-items: center;">
                <label for="lab_lid" style="font-size:14px; min-width:200px; font-weight:700;">Lab LID:</label>
                <input type="text" name="rclab_lid" id="rclab_lid" class="form-control" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;"disabled>
                </div>

                <div style="display: flex; align-items: center;">
                <label for="header" style="font-size:14px; min-width:200px; font-weight:700;">Header:</label>
                <select name="header" id="header" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                </div>

                <div style="display: flex; align-items: center;">
                <label for="headerurl" style="font-size:14px; min-width:200px; font-weight:700;">Heade Rurl:</label>
                <input type="text" name="headerurl" id="headerurl" class="form-control" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                </div>

                <div style="display: flex; align-items: center;">
                <label for="footer" style="font-size:14px; min-width:200px; font-weight:700;">Footer:</label>
                <select name="footer" id="footer" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                </div>

                <div style="display: flex; align-items: center;">
                <label for="footerurl" style="font-size:14px; min-width:200px; font-weight:700;">Foote Rurl:</label>
                <input type="text" name="footerurl" id="footerurl" class="form-control" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                </div>

                <div style="display: flex; align-items: center;">
                <label for="pageheading" style="font-size:14px; min-width:200px; font-weight:700;">Page Heading:</label>
                <select name="pageheading" id="pageheading" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                </div>

                <div style="display: flex; align-items: center;">
                <label for="rcdate" style="font-size:14px; min-width:200px; font-weight:700;">Date:</label>
                <select name="rcdate" id="rcdate" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                </div>

                <div style="display: flex; align-items: center;">
                <label for="sign" style="font-size:14px; min-width:200px; font-weight:700;">Sign</label>
                  <select name="sign" id="sign" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                </div>

                <div style="display: flex; align-items: center;">
                <label for="confidential" style="font-size:14px; min-width:200px; font-weight:700;">Confidential:</label>
                <select name="confidential" id="confidential" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                </div>

                <div style="display: flex; align-items: center;">
                <label for="fontitelic" style="font-size:14px; min-width:200px; font-weight:700;">Font Itelic:</label>
                <select name="fontitelic" id="fontitelic" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                </div>

                <div style="display: flex; align-items: center;">
                <label for="agelabel" style="font-size:14px; min-width:200px; font-weight:700;">Age Label:</label>
                <select name="agelabel" id="agelabel" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                </div>

                <div style="display: flex; align-items: center;">
                <label for="headerdefault" style="font-size:14px; min-width:200px; font-weight:700;">Header Default:</label>
                <select name="headerdefault" id="headerdefault" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                </div>

                <div style="display: flex; align-items: center;">
                <label for="valuestate" style="font-size:14px; min-width:200px; font-weight:700;">Value State:</label>
                <select name="valuestate" id="valuestate" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                </div>

                <div style="display: flex; align-items: center;">
                <label for="viewsno" style="font-size:14px; min-width:200px; font-weight:700;">View Sno:</label>
                <select name="viewsno" id="viewsno" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                </div>

                <div style="display: flex; align-items: center;">
                <label for="viewregdate1" style="font-size:14px; min-width:200px; font-weight:700;">View Reg. Date:</label>
                  <select name="viewregdate" id="viewregdate" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                </div>

                <div style="display: flex; align-items: center;">
                <label for="viewinitials" style="font-size:14px; min-width:200px; font-weight:700;">View Initials:</label>
                <select name="viewinitials" id="viewinitials" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                </div>

                <div style="display: flex; align-items: center;">
                <label for="viewspecialnote" style="font-size:14px; min-width:200px; font-weight:700;">View Special Note:</label>
                <select name="viewspecialnote" id="viewspecialnote" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                </div>

                <div style="display: flex; align-items: center;">
                <label for="enableblooddrew" style="font-size:14px; min-width:200px; font-weight:700;">Enable Bloodd Rew:</label>
                    <select name="enableblooddrew" id="enableblooddrew" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                </div>

                <div style="display: flex; align-items: center;">
                <label for="enablecollected" style="font-size:14px; min-width:200px; font-weight:700;">Enable Collected:</label>
                <select name="enablecollected" id="enablecollected" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                </div>

                <div style="display: flex; align-items: center;">
                <label for="reference_in_invoice" style="font-size:14px; min-width:200px; font-weight:700;">Reference In Invoice:</label>
                <select name="reference_in_invoice" id="reference_in_invoice" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                </div>

                <div style="display: flex; align-items: center;">
                <label for="rcdob" style="font-size:14px; min-width:200px; font-weight:700;">DOB:</label>
                <input type="text" name="rcdob" id="rcdob" class="form-control" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                </div>
           
                </div>
                 <input type="button" style="display: none;flex: 0 0 80px; margin-left: 10px;" class="btn" id="repoupdate_btn" value="Update" onclick="updateReportConfigurations()">
            </div>
           </td>
        </tr>

        <tr>
            <td>
                <div style="display: flex; align-items: left; gap: 15px; margin-top: 50px; flex-wrap: wrap;">
                   
                    <label style="font-size: 16px; font-weight: bold;">Add Config Table Configurations</label>

                </div>
                <div class="card" style="width: 620px; margin-top: 10px;">
                    <div class="card-body" style="display: flex; flex-direction: column; gap: 12px;">

                        <div style="display: flex; align-items: center;">
                        <label for="config_id" style="font-size:14px; min-width:200px; font-weight:700;">ID:</label>
                        <input type="text" name="config_id" id="config_id" class="form-control" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;" disabled>
                        </div>

                        <div style="display: flex; align-items: center;">
                        <label for="lab_lid" style="font-size:14px; min-width:200px; font-weight:700;">Lab LID:</label>
                        <input type="text" name="config_lab_lid" id="config_lab_lid" class="form-control" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;"disabled>
                        </div>

                        <div style="display: flex; align-items: center;">
                        <label for="separate_prices_branch" style="font-size:14px; min-width:200px; font-weight:700;">Separate Prices Branch:</label>
                        <select name="separate_prices_branch" id="separate_prices_branch" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        </div>

                        <div style="display: flex; align-items: center;">
                        <label for="worksheet_pertest" style="font-size:14px; min-width:200px; font-weight:700;">Worksheet Pertest:</label>
                        <select name="worksheet_pertest" id="worksheet_pertest" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        </div>

                        <div style="display: flex; align-items: center;">
                        <label for="worksheet_perdept" style="font-size:14px; min-width:200px; font-weight:700;">Worksheet Perdept:</label>
                        <select name="worksheet_perdept" id="worksheet_perdept" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        </div>

                        <div style="display: flex; align-items: center;">
                        <label for="report_auth_1" style="font-size:14px; min-width:200px; font-weight:700;">Report Auth 1:</label>
                         <select name="report_auth_1" id="report_auth_1" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        </div>

                        <div style="display: flex; align-items: center;">
                        <label for="report_auth_2" style="font-size:14px; min-width:200px; font-weight:700;">Report Auth 2:</label>
                        <select name="report_auth_2" id="report_auth_2" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        </div>

                 

                   
                    </div>
                    <input type="button" style="display: none;flex: 0 0 80px; margin-left: 10px;" class="btn" id="config_update_btn" value="Update" onclick="updateConfigConfigurations()">
                </div>
           </td>

            <td>
                <div style="display: flex; align-items: left; gap: 15px; margin-top: 55px; flex-wrap: wrap;">
                   
                    <label style="font-size: 16px; font-weight: bold;">Add SMS Profile  Table Configurations</label>

                </div>
                <div class="card" style="width: 620px; margin-top: 10px;height: 300px;">
                    <div class="card-body" style="display: flex; flex-direction: column; gap: 12px;">

                        <div style="display: flex; align-items: center;">
                        <label for="sms_id" style="font-size:14px; min-width:200px; font-weight:700;">ID:</label>
                        <input type="text" name="sms_id" id="sms_id" class="form-control" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;" disabled>
                        </div>

                        <div style="display: flex; align-items: center;">
                        <label for="sms_lab_lid" style="font-size:14px; min-width:200px; font-weight:700;">Lab LID:</label>
                        <input type="text" name="sms_lab_lid" id="sms_lab_lid" class="form-control" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;"disabled>
                        </div>

                        <div style="display: flex; align-items: center;">
                        <label for="username" style="font-size:14px; min-width:200px; font-weight:700;">Username:</label>
                         <input type="text" name="smsusername" id="smsusername" class="form-control" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                        </div>

                        <div style="display: flex; align-items: center;">
                        <label for="password" style="font-size:14px; min-width:200px; font-weight:700;">Password:</label>
                         <input type="text" name="password" id="password" class="form-control" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                        </div>

                        <div style="display: flex; align-items: center;">
                        <label for="src" style="font-size:14px; min-width:200px; font-weight:700;">SRC:</label>
                         <input type="text" name="src" id="src" class="form-control" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                        </div>

                      

                        <div style="display: flex; align-items: center;">
                        <label for="isactiveauto" style="font-size:14px; min-width:200px; font-weight:700;">Is Active Auto:</label>
                        <select name="isactiveauto" id="isactiveauto" style="flex-grow:1; height:30px; font-size:14px; margin-left:10px;">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        </div>
                         

                 

                   
                    </div>
                    <input type="button" style="display: none;flex: 0 0 80px; margin-left: 10px;" class="btn" id="smsupdate_btn" value="Update" onclick="updateSMSConfigurations()">
                </div>
           </td>
        </tr>

        </table>
   
    
<input type="button" style="flex: 0 0 80px; margin-left: 10px;" class="btn" id="Allupdate_btn" value="Update" onclick="updateSMSConfigurations(),updateAddpatientConfigurations(),updateReportConfigurations(),updateConfigConfigurations()">





{{-- ############################################################################################################# --}}


       
    </div>

</div>




@stop
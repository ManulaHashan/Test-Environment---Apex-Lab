<!-- **********~~~~~~~~~~~~~Manula Hashan's Devolopment~~~~~~~~~~~~~********** -->


<?php ?>
@extends('Templates/WiTemplate')


@section('title')
Add New Patient
@stop

@section('head')

<script src="{{ asset('JS/ReportCalculations.js') }}"></script>

<script>
    $(document).ready(function() {
        loadRecordToTable();
    });

    // Function to load data into the table
    function loadRecordToTable() {

        $.ajax({
            type: "GET",
            url: "getAllRefference",
            success: function(tbl_records) {
                // alert('Successfully loaded data.');
                $('#record_tbl').html(tbl_records);
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

    // Set the current date as the default value
    document.addEventListener("DOMContentLoaded", function() {
        const today = new Date();
        const formattedDate = today.toISOString().split('T')[0]; // Format as YYYY-MM-DD
        document.getElementById('jdate').value = formattedDate; // Set the value
    });

    // ********************Function to load selected record into the input field when clicking on a table row*********
    function selectRecord(refID, refcode, refName, refAddress, refContact, refDegree, refJoinedDate) {
        $('#refID').val(refID);
        $('#refcode').val(refcode);
        $('#Ref_name').val(refName);
        $('#Ref_address').val(refAddress);
        $('#Ref_contact').val(refContact);
        $('#Ref_degree').val(refDegree);
        $('#jdate').val(refJoinedDate);
        loadInvoiceCount(refID);
    }


    // ******************Function to save the  data**************************
    function savePatient() {
        // Get the values from the input fields
        var refID = $('#refcode').val();
        var refName = $('#Ref_name').val();
        var refAddress = $('#Ref_address').val();
        var refContact = $('#Ref_contact').val();
        var refDegree = $('#Ref_degree').val();
        var refJoinedDate = $('#jdate').val();


        if (refID === '') {
            alert('Reference Code is required.');
            return;
        }
        if (refName === '') {
            alert('Reference name is required.');
            return;
        }
        if (refContact === '') {
            alert('Reference contact is required.');
            return;
        }
        if (refContact !== '' && !/^\d{10}$/.test(refContact)) {
            alert('Please enter a valid 10-digit contact number.');
            return;
        }

        // AJAX request to save the reference data
        $.ajax({
            type: "POST",
            url: "saveReference",
            data: {
                'refID': refID,
                'refName': refName,
                'refAddress': refAddress,
                'refContact': refContact,
                'refDegree': refDegree,
                'refJoinedDate': refJoinedDate
            },
            success: function(response) {

                if (response.error == "saved") {
                    alert('Reference saved successfully!');
                    loadRecordToTable();
                    $('#refcode').val('');
                    $('#Ref_name').val('');
                    $('#Ref_address').val('');
                    $('#Ref_contact').val('');
                    $('#Ref_degree').val('');
                    const today = new Date();
                    const formattedDate = today.toISOString().split('T')[0]; // Format as YYYY-MM-DD
                    document.getElementById('jdate').value = formattedDate; // Set the value
                } else if (response.error == "exist") {
                    alert('Code already exist!');
                } else {
                    alert('Error in saving process.');

                }
            },
            error: function(xhr) {
                console.log('Error:', xhr); // Log the full error response for debugging
                var errorMsg = xhr.responseJSON ? xhr.responseJSON.error : 'An unexpected error occurred.';
                alert(errorMsg); // Display the error message to the user
            }
        });
    }

    //****************** */ Function to reset the input fields*************************
    function resetFields() {
        document.getElementById('refID').value = '';
        document.getElementById('refcode').value = '';
        document.getElementById('invoicecount').innerHTML = '0';;
        document.getElementById('Ref_name').value = '';
        document.getElementById('Ref_address').value = '';
        document.getElementById('Ref_contact').value = '';
        document.getElementById('Ref_degree').value = '';
        const today = new Date();
        const formattedDate = today.toISOString().split('T')[0]; // Format as YYYY-MM-DD
        document.getElementById('jdate').value = formattedDate;

        console.log("All fields have been reset!");

    }
    //**************************function validateNumbersOnly on contact feild***************
    function validateNumbersOnly(input) {
        // Remove any non-numeric characters
        input.value = input.value.replace(/[^0-9]/g, '');
    }

    //**************************function validateLettersOnly on name feild***************
    function validateLettersOnly(input) {
        // Allow only letters and spaces
        input.value = input.value.replace(/[^a-zA-Z\s]/g, '');
    }



    // ******************Function to delete the reference data**********************
    function deleteReference() {
        var refID = $('#refID').val();

        if (refID === '') {
            alert('Please select a reference to delete.');
            return;
        }


        if (confirm('Are you sure you want to delete this reference?')) {
            $.ajax({
                type: "POST",
                url: "/deleteReference",
                data: {
                    'refID': refID
                },
                success: function(response) {
                    if (response == "deleted") {
                        alert('Reference deleted successfully!');
                        loadRecordToTable();
                        resetFields();
                    } else {
                        alert('Cant delete this reference. Because it is used for billing process.');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error:', error);
                    alert('Error in deleting reference.');
                }
            });
        }
    }

    // ******************Function to update the reference data******************

    function updateReference() {
        var refID = $('#refID').val();
        var refCode = $('#refcode').val();
        var refName = $('#Ref_name').val();
        var refAddress = $('#Ref_address').val();
        var refContact = $('#Ref_contact').val();
        var refDegree = $('#Ref_degree').val();
        var refJoinedDate = $('#jdate').val();

        if (refName === '') {
            alert('Select record to update.');
            return;
        }


        // AJAX request to update the reference data
        $.ajax({
            type: "POST",
            url: "updateReference",
            data: {
                'refID': refID,
                'refcode': refCode,
                'refName': refName,
                'refAddress': refAddress,
                'refContact': refContact,
                'refDegree': refDegree,
                'refJoinedDate': refJoinedDate
            },
            success: function(response) {
                if (response.success && response.error === "updated") {
                    alert('Reference updated successfully!');
                    loadRecordToTable();
                    resetFields();
                } else if (!response.success && response.error === "exist") {
                    alert('The reference code is already in use. Please use a unique code.');
                } else if (!response.success && response.error === "not_updated") {
                    alert('No changes made to the reference.');
                } else {
                    alert('Error in updating reference.');
                }
            },
            error: function(xhr) {
                console.log('Error:', xhr);
                alert('Error in updating reference.');
            }
        });
    }

    // ******************Function to search the reference data******************

    function searchRecords() {
        var name = $('#Ser_name').val();
        var code = $('#Ser_code').val();

        $.ajax({
            type: "GET",
            url: "/getAllRefference",
            data: {
                name: name,
                code: code
            },
            success: function(tbl_records) {
                $('#record_tbl').html(tbl_records);
            },
            error: function(xhr, status, error) {
                alert('Error: ' + xhr.status + ' - ' + xhr.statusText);
            }
        });
    }

    var selectChecks = [];

    function getData() {

        var refID = $('#refID').val();

        if (refID === '') {
            alert('Please select a reference to merge.');
            return;
        } else {
            selectChecks.length = 0;
            $('.ref_chkbox:checked').each(function() {
                selectChecks.push($(this).val());
            })

            $.ajax({
                type: "POST",
                url: "mergeReference",
                data: {
                    'Main_refID': refID,
                    'effected_refIds': selectChecks
                },
                success: function(response) {
                    alert(response.message);
                    selectChecks.length = 0;
                    loadRecordToTable();
                }
            });
        }


    }

    //*********************Function for veiw invoice count******************************
    function loadInvoiceCount(refID) {
        $.ajax({
            url: '/getInvoiceCountForReference',
            type: 'GET',
            data: {
                refID: refID
            },
            success: function(response) {
                if (response.success) {
                    $('#invoicecount').text(response.count);
                }

            }
        })

    }
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

    .warning-container {
        display: flex;
        align-items: center;
        margin: 5px 0;
        padding: 5px;
        border: 1px solid #f5c2c2;
        background-color: #f8d7da;
        border-radius: 4px;
        font-size: 16px;
        color: #842029;
        width: 100%;
    }

    .warning-icon {
        font-size: 20px;
        margin-right: 10px;
        color: #d63333;
    }

    .warning-text {
        font-weight: bold;
    }
</style>
@stop

@section('body')


<h2 class="pageheading" style="margin-top: 15px;"> Add Patient
</h2><br>
<div class="container">
    <div class="card" style="height: 1250px; margin-top: 20px;">
        <div class="card-body">
            <div style="width: 1350px; display: inline-block;">
                <!-- Input group container -->
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <label style="width: 150px;font-size: 18px;">Sample No</label>
                    <input type="text" name="refcode" class="input-text" id="sampleNo" style="width: 200px; height: 40px;font-size: 38px;">
                    <!-- <input type="hidden" name="refID" id="refID"> -->
                    <label style="width: 10px;font-size: 16px;  "></label>
                    <input type="checkbox" name="edit" id="edit" class="ref_chkbox" value="1">
                    <label style="width: 80px;font-size: 18px;  ">Edit</label>
                    <label style="width: 100px;font-size: 18px; ">Center</label>
                    <select type="text" name="Ref_address" class="input-text" id="center" style="width: 350px; height: 30px" pattern="[A-Za-z0-9]{1,10}" title="" value="">
                        <option value="%">Main</option>
                        <option value="2">Center 2</option>
                    </select>
                    <input type="checkbox" name="lock_branch" id="lock_branch" value="1">
                    <label style="width: 140px;font-size: 16px;  "><b>Lock Branch</b></label>
                    <label style="width: 10px;font-size: 16px;  "></label>
                    <label style="width: 135px;font-size: 16px;  "><b>Search Bill</b></label>
                    <input type="date" name="ser_date" class="input-text" id="ser_date" style="width: 150px">
                    <label style="width: 30px;font-size: 16px;  "></label>
                    <label style="width: 135px;font-size: 16px;  "><b>Sample No</b></label>
                    <input type="text" name="ser_sampleno" class="input-text" id="ser_sampleno" style="width: 150px">
                    <input type="button" style="width: 80px" class="btn" id="ser_btn" value="Search" onclick="">
                </div>

                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <label style="width: 89px;font-size: 18px; ">Search</label>
                    <input type="text" name="ser_pdetails" class="input-text" id="ser_pdetails" style="width: 210px">
                    <label style="width: 200px;font-size: 18px;"></label>
                    <label style="width: 50px;font-size: 18px;">Type</label>
                    <select type="text" name="type" class="input-text" id="type" style="width: 80px; height: 30px">
                        <option value="1">In</option>
                        <option value="2">Out</option>
                    </select>
                    <label style="width: 105px;font-size: 18px;"></label>
                    <label style="width: 80px;font-size: 16px;  "><b>Source</b></label>
                    <select type="text" name="source" disabled class="input-text" id="source" style="width: 135px; height: 30px">
                        <option value="1">Walking</option>
                        <option value="2">Centers</option>
                    </select>
                    <label style="width: 10px;font-size: 16px;  "></label>
                    <input type="checkbox" name="ignore_date" class="ignore_date" value="1">
                    <label style="width: 140px;font-size: 16px;  "><b>Ignore Date</b></label>
                </div>
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <label style="width: 90px;font-size: 18px; ">T.P.NO</label>
                    <input type="text" name="tpno" class="input-text" id="tpno" style="width: 210px" pattern="[A-Za-z0-9]{1,10}" title="" value="">
                    <label style="width: 850px;font-size: 18px;"></label>
                    <input type="button" style="width: 80px" class="btn" id="backBtn" value="Back" onclick="">
                    <input type="button" style="width: 80px" class="btn" id="frontBtn" value="Front" onclick="">
                </div>
            </div>

            <!-- --------------*********************************************************************************---------
 ------------------------------------------------------------------------------------------------------------
                 -->

            <div style="width:1350px; display: flex;">
                <!--Left Side -->
                <div style="flex: 1; padding: 10px; border: 2px #8e7ef7 solid; margin-right: 5px; border-radius: 10px;">

                    <div style="display: flex; align-items: center;  ">
                        <label style="width: 150px;font-size: 18px; ">First Name:</label>
                        <select type="text" name="initial" class="input-text" id="initial" style="width: 80px; height: 30px; ">
                            <option value="1">Mr</option>
                            <option value="2">Mrs</option>
                            <option value="3">Miss</option>
                            <option value="4">Dr</option>
                            <option value="4">Hons</option>
                        </select>
                        <input type="text" name=" fname" maxlength="2" class="input-text" id="fname" style="width: 380px">
                    </div>
                    <div style="display: flex; align-items: center; margin-top: 5px;">
                        <label style="width: 150px;font-size: 18px; ">Last Name:</label>
                        <input type="text" name=" lname" maxlength="2" class="input-text" id="lname" style="width: 250px">
                        <label style="width: 50px;font-size: 16px;  ">DOB</label>
                        <input type="date" name="dob" class="input-text" id="dob" style="width: 140px">
                    </div>
                    <div style="display: flex; align-items: center;margin-top: 5px; ">
                        <label style="width: 150px;font-size: 18px; ">Age:</label>
                        <label style="width: 50px;font-size: 18px;  ">Years</label>
                        <input type="text" name=" years" maxlength="3" class="input-text" id="years" style="width: 60px;margin-right:15px">
                        <label style="width: 65px;font-size: 18px; ">Months</label>
                        <input type="text" name=" months" maxlength="2" class="input-text" id="months" style="width: 60px;margin-right:15px">
                        <label style="width: 45px;font-size: 18px; ">Days</label>
                        <input type="text" name=" days" maxlength="3" class="input-text" id="days" style="width: 60px">
                    </div>
                    <div style="display: flex; align-items: center; margin-top: 5px;">
                        <label style="width: 150px;font-size: 18px; ">Gender:</label>
                        <label style="display: flex; align-items: center; cursor: pointer; ">
                            <input type="radio" name="male" id="male" value="male" style="margin-right: 5px;"> Male
                        </label>
                        <label style="display: flex; align-items: center; cursor: pointer; ">
                            <input type="radio" name="female" id="female" value="female" style="margin-right: 5px;"> Female
                        </label>
                    </div>
                    <div style="display: flex; align-items: center; margin-top: 5px;">
                        <label style="width: 150px;font-size: 18px; ">NIC NO:</label>
                        <input type="text" name=" nic" class="input-text" id="nic" style="width: 450px">
                    </div>
                    <div style="display: flex; align-items: center; margin-top: 5px;">
                        <label style="width: 150px;font-size: 18px; ">Address:</label>
                        <input type="text" name="address" class="input-text" id="address" style="width: 450px">
                    </div>
                    <div style="display: flex; align-items: center; margin-top: 5px;">
                        <label style="width: 150px;font-size: 18px; ">Ref.Code:</label>
                        <input type="text" name=" refcode" class="input-text" id="refcode" style="width: 250px">
                        <input type="button" style="color:gray" class="btn" id="resetbtn" value="Add New Reference" onclick="">
                    </div>
                    <div style="display: flex; align-items: center; margin-top: 5px;">
                        <label style="width: 150px;font-size: 18px; ">Refered:</label>
                        <input type="text" name=" ref" class="input-text" id="ref" style="width: 450px">
                    </div>
                    <div style="display: flex; align-items: center; margin-top: 5px;">
                        <label style="width: 150px;font-size: 18px; "><b>Test Name</b>:</label>
                        <input type="text" name="testname" class="input-text" id="testname" list="testlist" style="width: 350px">
                        <datalist id="testlist">
                            <option value="111 : Test 1 : 300 : 20min">
                            <option value="122:Test 2:400:20min">
                            <option value="133:Test 3:500:20min">
                            <option value="144:Test 4:600:20min">
                        </datalist>
                        <input type="checkbox" name="byname" id="byname" class="ref_chkbox" value="1">
                        <label style="width: 70px;font-size: 16px;  "><b>By Name</b></label>
                    </div>
                    <div style="display: flex; align-items: center; margin-top: 5px;">
                        <label style="width: 150px;font-size: 18px; "><b>Package Name</b>:</label>
                        <input type="text" name="pkgname" class="input-text" id="pkgname" style="width: 230px">
                        <label style="width: 120px;font-size: 18px; "><b>Fasting Time</b>:</label>
                        <input type="text" name=" fast_time" class="input-text" id="fast_time" style="width: 80px">
                        <input type="checkbox" name="fastcheck" id="fastcheck" class="ref_chkbox" value="1">
                    </div>

                    <hr style=" background-color: rgb(19, 153, 211); height: 5px; border: none; margin-top: 20px;">

                    <div style="display: flex; align-items: center;margin-top: 5px; ">
                        <label style="width: 125px;font-size: 18px; ">Total Amount:</label>
                        <label style="width: 30px;font-size: 18px; ">Rs: </label>
                        <label style="width: 155px;font-size: 18px; " id="total_amt">000,000.00</label>
                        <label style="width: 80px;font-size: 18px; ">Discount:</label>
                        <input type="text" name=" discount" class="input-text" id="discount" style="width: 80px">
                        <select type="text" name="discount_precentage" class="input-text" id="discount_precentage" style="width: 80px; height: 30px">
                            <option value="1">5%</option>
                            <option value="2">10%</option>
                        </select>
                    </div>
                    <div style="display: flex; align-items: center;margin-top: 20px; ">
                        <label style="width: 125px;font-size: 18px; "><b>Grand Total:</b></label>
                        <label style="width: 30px;font-size: 18px; ">Rs: </label>
                        <label style="width: 150px;font-size: 18px;" id="grand_total">000,000.00</label>
                        <label> <input type="radio" name="cash" id="cash" value="cash"> Cash</label>
                        <label><input type="radio" name="card" id="card" value="card"> Card</label>
                        <label><input type="radio" name="credit" id="credit" value="credit"> Credit</label>
                        <label><input type="radio" name="cheqe" id="cheqe" value="cheque"> Cheque</label>
                        <label><input type="radio" name="split" id="split" value="cheque"> Split</label>
                    </div>
                    <div style="display: flex; align-items: center;margin-top: 20px; ">
                        <label style="width: 125px;font-size: 18px; ">Payment:</label>
                        <label style="width: 30px;font-size: 18px; ">Rs: </label>
                        <input type="text" name=" paid" class="input-text" id="paid" style="width: 97px">
                        <label style="width: 35px;font-size: 18px; "></label>
                        <label style="width: 60px;font-size: 18px; ">Due:</label>
                        <label style="width: 30px;font-size: 18px; ">Rs:</label>
                        <label style="width: 150px;font-size: 18px; " id="due">000,000.00</label>
                    </div>
                    <div style="display: flex; align-items: center;margin-top: 15px; ">
                        <label style="width: 405px;font-size: 18px; "></label>
                        <input type="button" style="color:black; width: 210px; height: 50px" class="btn" id="update_payment" value="Update Payment " onclick="">
                    </div>


                </div>


                <!-- Right Side -->

                <div style="flex: 1; padding: 10px; border: 2px #8e7ef7 solid; border-radius: 10px;">
                    <div class="pageTableScope" style="height: 250px; margin-top: 10px;">
                        <table style="font-family: Futura, 'Trebuchet MS', Arial, sans-serif; font-size: 13pt;" id="test_tbl" width="100%" border="0" cellspacing="2" cellpadding="0">
                            <thead>
                                <tr class="viewTHead">
                                    <td align="center" class="fieldText" style="width: 20px;">Test Id </td>
                                    <td align="center" class="fieldText" style="width: 250px;">Test Name</td>
                                    <td align="center" class="fieldText" style="width: 20px;"> price</td>
                                    <td align="center" class="fieldText" style="width: 10px;">Testing time</td>
                                    <td align="center" class="fieldText" style="width: 10px;">Fasting Time</td>
                                    <td align="center" class="fieldText" style="width: 10px;"> Barcode</td>
                                    <td align="center" class="fieldText" style="width: 10px;">Priority</td>
                                </tr>
                            </thead>
                            <tbody id="Branch_record_tbl">
                                <!-- Dynamic rows will be inserted here via AJAX -->
                            </tbody>
                        </table>
                    </div>
                    <div style="display: flex; align-items: center;margin-top: 5px; ">
                        <label style="width: 150px;font-size: 18px; ">Pending Samples:</label>
                        <input type="button" style="color:gray;" class="btn" id="make_priority" value="Make Priority" onclick="">
                        <input type="button" style="color:gray;" class="btn" id="remove_priority" value="Remove Priority " onclick="">
                        <input type="button" style="color:gray;" class="btn" id="delete" value="Delete" onclick="">
                    </div>
                    <div style="display: flex; align-items: center;margin-top: 5px; ">
                        <label style="width: 150px;font-size: 18px; "><b>Invoice Remark:</b></label>
                        <input type="text" name=" inv_remark" class="input-text" id="inv_remark" style="width: 430px">
                    </div>
                    <hr style=" background-color: rgb(19, 153, 211); height: 5px; border: none;">
                    <div style="display: flex; align-items: center;margin-top: 5px; ">
                        <label style="width: 150px;font-size: 18px; "><b>Barcode Options:</b></label>
                        <input type="button" style="color:gray" class="btn" id="group_barcode" value="Group Barcodes" onclick="">
                        <input type="button" style="color:gray" class="btn" id="print_barcode" value="Print Barcode " onclick="">
                        <input type="button" style="color:gray" class="btn" id="bulck_barcode" value="Bulck Barcode" onclick="">
                        <input type="button" style="color:gray" class="btn" id="remove_barcode" value="Remove Barcode" onclick="">
                    </div>
                    <hr style=" background-color:rgb(19, 153, 211); height: 5px; border: none;">
                    <div style="display: flex; align-items: center;margin-top: 5px; ">
                        <label style="width: 140px;font-size: 16px;  "><b>Repeat Samples</b></label>
                        <input type="checkbox" name="rep_chkbox" id="rep_chkbox" class="ref_chkbox" value="1">
                        <input type="button" style="color:gray" class="btn" id="cash_drawer" value="Cash Drawer" onclick="">
                    </div>
                    <div style="display: flex; align-items: center;margin-top: 5px; ">
                        <label style="width: 230px;font-size: 18px; "><b>Report Collection Method</b></label>
                        <select type="text" name="rep_collection" class="input-text" id="rep_collection" style="width: 120px; height: 30px; ">
                            <option value="1">Hard Coppy</option>
                            <option value="2">SMS</option>
                            <option value="3">Email</option>
                            <option value="4">Whatsapp</option>
                        </select>
                        <input type="checkbox" name="print_bill" id="print_bill" class="ref_chkbox" value="1">
                        <label style="width: 90px;font-size: 16px;  "><b>PrintBill</b></label>
                        <input type="checkbox" name="claim_bill" id="claim_bill" class="ref_chkbox" value="1">
                        <label style="width: 90px;font-size: 16px;  "><b>Claim Bill</b></label>
                        <input type="checkbox" name="two_copies" id="two_copies" class="ref_chkbox" value="1">
                        <label style="width: 90px;font-size: 16px;  "><b>2 Copies</b></label>

                    </div>
                    <hr style=" background-color: rgb(19, 153, 211); height: 5px; border: none;">


                    <div style="display: flex; align-items: center;margin-top: 4px; ">
                        <label style="width: 50px;font-size: 16px;  "></label>
                        <input type="button" style="color:black; width: 210px; height: 50px" class="btn" id="savebtn" value="Save" onclick="">
                        <input type="button" style="color:black; width: 210px; height: 50px" class="btn" id="updatebtn" value="Update Details " onclick="">
                        <input type="button" style="color:black; width: 210px; height: 50px" class="btn" id="getlastpatientbtn" value="Get Last patient" onclick="">
                    </div>
                    <div style="display: flex; align-items: center;margin-top: 5px; ">
                        <label style="width: 50px;font-size: 16px;  "></label>
                        <input type="button" style="color:black; width: 210px; height: 50px" class="btn" id="resetbtn" value="Reset" onclick="">
                        <input type="button" style="color:black; width: 210px; height: 50px" class="btn" id="print_invoicebtn" value="Print Invoice " onclick="">
                        <input type="button" style="color:black; width: 210px; height: 50px" class="btn" id="view_invoicebtn" value="View Invoice" onclick="">
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>
</div>




@stop
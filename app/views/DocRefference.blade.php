<!-- **********~~~~~~~~~~~~~Manula Hashan's Devolopment~~~~~~~~~~~~~********** -->


<?php ?>
@extends('Templates/WiTemplate')


@section('title')
Enter Doctor Refference
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
        document.getElementById('jdate').value = formattedDate; 
        document.getElementById('Ref_dob').value = formattedDate;
    });

    // ********************Function to load selected record into the input field when clicking on a table row*********
    function selectRecord(refID, refcode, refName, refAddress, refContact, refDegree, refJoinedDate, refCategory, refUnit, refArea, refCoodinator, refDob,refSpeciality) {
        $('#refID').val(refID);
        $('#refcode').val(refcode);
        $('#Ref_name').val(refName);
        $('#Ref_address').val(refAddress);
        $('#Ref_contact').val(refContact);
        $('#Ref_degree').val(refDegree);
        $('#jdate').val(refJoinedDate);
        $('#Ref_category').val(refCategory);
        $('#Ref_unit').val(refUnit);
        $('#Ref_area').val(refArea);
        $('#Ref_coodinator').val(refCoodinator);
        $('#Ref_dob').val(refDob);
        $('#Ref_speciality').val(refSpeciality);
        $('#saveBtn').hide();
        loadInvoiceCount(refID);
    }


    // ******************Function to save the reference data**************************
    function saveReference() {
        // Get the values from the input fields
        var refID = $('#refcode').val();
        var refName = $('#Ref_name').val();
        var refAddress = $('#Ref_address').val();
        var refContact = $('#Ref_contact').val();
        var refDegree = $('#Ref_degree').val();
        var refJoinedDate = $('#jdate').val();
        var refCategory = $('#Ref_category').val();
        var refUnit = $('#Ref_unit').val();
        var refArea = $('#Ref_area').val();
        var refCoodinator = $('#Ref_coodinator').val();
        var refDob = $('#Ref_dob').val();
        var refSpeciality = $('#Ref_speciality').val();

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
                'refJoinedDate': refJoinedDate,
                'refCategory': refCategory,
                'refUnit': refUnit,
                'refArea': refArea,
                'refCoodinator': refCoodinator,
                'refDob': refDob,
                'refSpeciality': refSpeciality
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
                    $('#Ref_category').val('');
                    $('#Ref_speciality').val('');
                    $('#Ref_unit').val('');
                    $('#Ref_area').val('');
                    $('#Ref_coodinator').val('');
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
        document.getElementById('Ref_category').value = '';
        document.getElementById('Ref_unit').value = '';
        document.getElementById('Ref_area').value = '';
        document.getElementById('Ref_coodinator').value = '';        
        document.getElementById('Ref_speciality').value = '';
        const today = new Date();
        const formattedDate = today.toISOString().split('T')[0]; // Format as YYYY-MM-DD
        document.getElementById('jdate').value = formattedDate;
        document.getElementById('Ref_dob').value = formattedDate;
        $('#saveBtn').show();

        console.log("All fields have been reset!");

    }
    //**************************function validateNumbersOnly on contact feild***************
    function validateNumbersOnly(input) {
        // Remove any non-numeric characters
        input.value = input.value.replace(/[^0-9]/g, '');
    }

    //**************************function validateLettersOnly on name feild***************
    function replaceSingleQuote(input) {
        
        input.value = input.value.replace(/'/g, '"');
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
        var refCategory = $('#Ref_category').val();
        var refSpeciality = $('#Ref_speciality').val();
        var refUnit = $('#Ref_unit').val();
        var refArea = $('#Ref_area').val();
        var refCoodinator = $('#Ref_coodinator').val();
        var refDob = $('#Ref_dob').val();

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
                'refJoinedDate': refJoinedDate,
                'refCategory': refCategory,
                'refSpeciality': refSpeciality,
                'refUnit': refUnit,
                'refArea': refArea,
                'refCoodinator': refCoodinator,
                'refDob': refDob
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

     function confirmMerge() {
        if (confirm("Are you sure you want to merge the selected references?")) {
            getData(); 
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

    //*********************Genarate CODE******************************

    // $(document).ready(function() {
    //     // Handle the click event of the Generate button
    //     $('#generateCodeBtn').click(function() {
    //         $.ajax({
    //             url: '/generateCode', // The route to handle the code generation
    //             type: 'GET', // Use GET method for fetching data
    //             success: function(response) {
    //                 // Assuming the response contains the generated code
    //                 alert("Generated Code: " + response.code);
    //                 // You can also display the generated code in an input field or somewhere else on the page
    //                 $('#generatedCodeInput').val(response.code);
    //             },
    //             error: function(xhr, status, error) {
    //                 alert("Error generating code. Please try again.");
    //             }
    //         });
    //     });
    // });
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
    /*        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            border: 1px solid #ccc;*/

        }

    /*    .card-body {
            padding: 5px;
        }

        .card-title {
            font-size: 18px;
            font-weight: bold;
        }

        .card-text {
            font-size: 14px;
        }*/

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


<h2 class="pageheading" style="margin-top: 5px; margin-bottom: 5px;"> Manage Invoice References
</h2>
<div class="container">
    <div class="card" >
        <div class="card-body">
            <div style="width: 1100px; display: inline-block;">
                <!-- Input group container -->

                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <label style="width: 150px;font-size: 18px;">Code &nbsp;</label>
                    <input type="text" name="refcode" class="input-text" id="refcode" style="width: 150px" pattern="[A-Za-z0-9]{1,10}" title="" value="">
                    <input type="hidden" name="refID" id="refID">
                    <!-- Input field to display generated code -->
                    <!-- <input type="text" id="generatedCodeInput" class="form-control" readonly>
                    <input type="button" class="btn" id="generateCodeBtn" value="Genarate" onclick=""> -->
                    &nbsp;&nbsp;&nbsp&nbsp&nbsp;
                    <label style="width: 150px;font-size: 18px; color: blue;">Invoice Count</label>
                    <label style="width: 150px;font-size: 18px; color: green;" id="invoicecount">0</label>

                     <label style="width: 150px;font-size: 18px; margin-left: -5px;">Ref.Category &nbsp;</label>
                    <input type="text" name="Ref_address" class="input-text" id="Ref_category" style="width: 450px"  title="" value="">
                </div>

                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <label style="width: 150px;font-size: 18px;">Name &nbsp;</label>
                    <input type="text" oninput="replaceSingleQuote(this)" 
                        name="Ref_name" class="input-text" id="Ref_name" style="width: 450px">

                    <label style="width: 150px;font-size: 18px; margin-left: 20px;">Ref.Speciality  &nbsp;</label>
                    <input type="text" name="Ref_address" class="input-text" id="Ref_speciality" style="width: 450px"  title="" value="">
                </div>
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <label style="width: 150px;font-size: 18px;">Address &nbsp;</label>
                    <input type="text" name="Ref_address" class="input-text" id="Ref_address" style="width: 450px" pattern="[A-Za-z0-9]{1,10}" title="" value="">
                
                    <label style="width: 150px;font-size: 18px;margin-left: 20px;">Ref. Unit &nbsp;</label>
                    <input type="text" name="Ref_address" class="input-text" id="Ref_unit" style="width: 450px; "  title="" value="">
                </div>
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <label style="width: 150px;font-size: 18px;">Contact No &nbsp;</label>
                    <input type="text"
                        name="Ref_contact"
                        class="input-text"
                        id="Ref_contact"
                        style="width: 450px"
                        maxlength="10"
                        oninput="validateNumbersOnly(this)">

                        <label style="width: 150px;font-size: 18px;margin-left: 20px;">Ref. Nearest Area &nbsp;</label>
                    <input type="text" name="Ref_address" class="input-text" id="Ref_area" style="width: 450px;" title="" value="">
                </div>


                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <label style="width: 150px;font-size: 18px;">Ref.Initials  &nbsp;</label>
                    <input type="text" name="Ref_degree" class="input-text" id="Ref_degree" style="width: 450px" pattern="[A-Za-z0-9]{1,10}" title="" value="">
                    <label style="width: 150px;font-size: 18px; margin-left: 20px;">Coordinator &nbsp;</label>
                     <input type="text" name="Ref_address" class="input-text" id="Ref_coodinator" style="width: 450px;"  title="" value="">
                </div>
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <label style="width: 150px;font-size: 18px;">Joined Date &nbsp;</label>
                    <input type="date" name="jdate" class="input-text" id="jdate" style="width: 150px">
                    <label style="width: 150px;font-size: 18px; margin-left: 320px;">Date of Birth &nbsp;</label>
                    <input type="date" name="jdate" class="input-text" id="Ref_dob" style="width: 150px">
                </div>
                 


                <div style="display: flex; justify-content: flex-center; gap: 5px; margin-bottom: 10px;">
                    <input type="button" style="color:green; width:100px;" class="btn" id="saveBtn" value="Save" onclick="saveReference()">
                    <input type="button" style="color:Blue; width:100px;" class="btn" id="updateBtn" value="Update" onclick="updateReference()">
                    <input type="button" style="color:red; width:100px;" class="btn" id="deleteBtn" value="Delete" onclick="deleteReference()">
                    <input type="button" style="color:gray; width:100px;" class="btn" id="resetbtn" value="Reset" onclick="resetFields()">
                </div>


                <hr>

                <div style="display: flex; align-items: center;">
                    <label style="width: 150px; font-size: 18px;">Search By Name :</label>
                    <input type="text" name="Ser_name" class="input-text" id="Ser_name" style="width: 400px" title="" value="" oninput="searchRecords()">&nbsp;&nbsp;
                    <label style="width: 150px; font-size: 18px;">Search By Code :</label>
                    <input type="text" name="Ser_code" class="input-text" id="Ser_code" style="width: 150px" title="" value="" oninput="searchRecords()">
                </div>



                <div class="pageTableScope" style="height: 350px; margin-top: 10px; width: 100%;">
                    <table style="font-family: Futura, 'Trebuchet MS', Arial, sans-serif; font-size: 13pt;" id="pdataTable" width="100%" border="0" cellspacing="2" cellpadding="0">
                        <tbody>
                            <tr>
                                <td valign="top">
                                    <table border="1px" style="border-color: #ffffff; " cellpadding="0" cellspacing="0" class="TableWithBorder">
                                        <thead>
                                            <tr class="viewTHead">
                                                <td width="5%" class="fieldText">Code</td>
                                                <td width="25%" class="fieldText">Name</td>
                                                <td width="15%" class="fieldText">Address</td>
                                                <td width="10%" class="fieldText">Contact No</td>
                                                <td width="15%" class="fieldText">Degree</td>
                                                <td width="10%" class="fieldText">Joined Date</td>
                                                <td width="5%" class="fieldText">RefID</td>
                                                <td  width="5%" class="fieldText"> Select</td>
                                            </tr>
                                        </thead>
                                        <tbody id="record_tbl">

                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div style="display: flex; justify-content: flex-start; margin-top: 10px">
                    <div class="warning-container">
                        <span class="warning-icon">⚠️</span>
                        <span class="warning-text">
                            Merge references is permanent. Please double-check your selections before proceeding.
                        </span>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="button"  class="btn" style="margin: 0 5px; color:red; margin-left: 400px;" name="remove" value="Merge Reference" onclick="confirmMerge()">
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>




@stop
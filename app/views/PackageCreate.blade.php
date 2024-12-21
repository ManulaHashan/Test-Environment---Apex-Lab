<?php ?>
@extends('Templates/WiTemplate')


@section('title')
Create Test Package
@stop

@section('head')

<script src="{{ asset('JS/ReportCalculations.js') }}"></script>


<script>
    $(document).ready(function() {

        loadRecordToTable();

    });

    // Function to load Created Packages into the table


    function loadRecordToTable() {
        $.ajax({
            type: "GET",
            url: "/getAllTestPackages",
            success: function(tbl_records) {
                //alert('Successfully loaded data.');
                if (tbl_records) {
                    $('#record_tbl').html(tbl_records);
                } else {
                    alert('No records found');
                }
            }
        });
    }



    // ********************Function to load selected record into the input field when clicking on a table row*********
    function selectRecord(pkgID, pkgName, pkgPrice) {
        $('#idlabpackages').val(pkgID);
        $('#Pkg_name').val(pkgName);
        $('#Pkg_price').val(pkgPrice);
        loadSelectedPackageTests(pkgID);

    }

    // *********************function for select test add to table*******************

    let totalTestPrice = 0;


    function updateTotalPriceLabel() {
        document.querySelector('#totalPriceLabel').textContent = totalTestPrice.toFixed(2);
    }

    //**************load package included tests to table when package is selected in the table*********************
    function loadSelectedPackageTests(pkgID) {

        $.ajax({
            type: "GET",
            url: "/loadPackageTests",
            data: {
                packageID: pkgID
            },
            success: function(response) {
                var jobject = JSON.parse(response);

                $('#selectedTests').html(jobject.tbldata);
                document.querySelector('#totalPriceLabel').textContent = jobject.total_test_amount.toFixed(2);
                totalTestPrice = jobject.total_test_amount;

                for (var i = 0; i < jobject.testgrouparray.length; i++) {
                    //alert(jobject.testgrouparray[i]);
                    pkgTests.push(jobject.testgrouparray[i]);
                }

            }
        });
    }







    document.addEventListener("DOMContentLoaded", function() {
        const dropdown = document.getElementById("testDropdown");
        dropdown.addEventListener("change", function() {
            const selectedValue = this.value;
            const selectedText = this.options[this.selectedIndex].text;

            if (selectedValue && selectedValue !== "%") {
                const tableBody = document.getElementById("selectedTests");

                const existingRows = Array.from(tableBody.getElementsByTagName("tr"));
                const isAlreadyAdded = existingRows.some(row => row.cells[0].textContent === selectedValue);

                if (!isAlreadyAdded) {

                    const priceMatch = selectedText.match(/- (\d+(\.\d+)?)/);
                    const price = priceMatch ? parseFloat(priceMatch[1]) : 0;

                    totalTestPrice += price;


                    const newRow = document.createElement("tr");
                    const idCell = document.createElement("td");
                    idCell.textContent = selectedValue;

                    const nameCell = document.createElement("td");
                    nameCell.textContent = selectedText;

                    const actionCell = document.createElement("td");
                    const deleteButton = document.createElement("button");
                    deleteButton.textContent = "Remove";
                    deleteButton.style.padding = "5px 10px";
                    deleteButton.style.backgroundColor = "#ff4d4d";
                    deleteButton.style.color = "white";
                    deleteButton.style.border = "none";
                    deleteButton.style.cursor = "pointer";


                    deleteButton.addEventListener("click", function() {

                        totalTestPrice -= price;
                        updateTotalPriceLabel();
                        tableBody.removeChild(newRow);
                    });

                    actionCell.appendChild(deleteButton);

                    newRow.appendChild(idCell);
                    newRow.appendChild(nameCell);
                    newRow.appendChild(actionCell);

                    tableBody.appendChild(newRow);


                    updateTotalPriceLabel();
                } else {
                    alert("This test is already added.");
                }
            }
        });
    });

    // ******************Function to save Package data**************************
    var pkgTests = [];

    function savePackage() {
        var pkgName = $('#Pkg_name').val();
        var pkgPrice = $('#Pkg_price').val();


        // Collect selected tests from the table
        $('#selectedTests tr').each(function() {
            var testID = $(this).find('td:first').text();
            if (testID) {
                pkgTests.push(testID);
            }
        });

        // Validate the input fields
        if (pkgName === '') {
            alert('Package Name is required.');
            return;
        }
        if (pkgPrice === '') {
            alert('Package Price is required.');
            return;
        }
        if (pkgTests.length === 0) {
            alert('Please select at least one test to save the package.');
            return;
        }
        // alert(pkgTests);
        // AJAX request to save the package
        $.ajax({
            type: "POST",
            url: "/savePackage",
            data: {
                pkgName: pkgName,
                pkgPrice: pkgPrice,
                pkgTests: pkgTests
            },
            success: function(response) {
                // alert(response);

                var jobject = JSON.parse(response);

                console.log(response);
                if (jobject.error === "saved") {
                    alert("Package saved successfully!");
                    resetFields()
                    loadRecordToTable();
                } else if (jobject.error === "exist") {
                    alert('Package Name already exists!');
                } else if (jobject.error === "empty") {
                    alert('Please fill all required fields!');
                } else if (jobject.error === "saveerror") {
                    alert('An error occurred while saving the package!');
                } else {
                    alert('Unknown error occurred.');
                }
            },

            error: function(xhr, status, error) {
                alert('AJAX Error: ' + error);
            }
        });
    }

    // ******************Function to Update Package data**************************
    function updatePackage() {
        var pkgID = $('#idlabpackages').val();
        var pkgName = $('#Pkg_name').val();
        var pkgPrice = $('#Pkg_price').val();
        var pkgTests = [];


        $('#selectedTests tr').each(function() {
            var testID = $(this).find('td:first').text();
            if (testID) {
                pkgTests.push(testID);
            }
        });

        if (pkgID === '') {
            alert('Please select a package to update.');
            return;
        }
        if (pkgName === '') {
            alert('Package Name is required.');
            return;
        }
        if (pkgPrice === '') {
            alert('Package Price is required.');
            return;
        }
        if (pkgTests.length === 0) {
            alert('Please select at least one test for the package.');
            return;
        }

        // AJAX request to update the package
        $.ajax({
            type: "POST",
            url: "/updatePackage",
            data: {
                pkgID: pkgID,
                pkgName: pkgName,
                pkgPrice: pkgPrice,
                pkgTests: pkgTests
            },
            success: function(response) {
                var jobject = JSON.parse(response);

                if (jobject.error === "updated") {
                    alert('Package updated successfully!');
                    resetFields()
                    loadRecordToTable();
                } else if (jobject.error === "notfound") {
                    alert('Package not found!');
                } else {
                    alert('Error updating package.');
                }
            },
            error: function(xhr, status, error) {
                alert('AJAX Error: ' + error);
            }
        });
    }

    // ******************Function to Delete Package data**************************
    function deletePackage() {
        var pkgID = $('#idlabpackages').val();

        if (!pkgID) {
            alert('Please select a package to delete.');
            return;
        }

        if (!confirm('Are you sure you want to deactivate this package?')) {
            return;
        }

        $.ajax({
            type: "POST",
            url: "/deletePackage",
            data: {
                pkgID: pkgID
            },
            success: function(response) {
                var jobject = JSON.parse(response);

                if (jobject.error === "deleted") {
                    alert('Package deactivated successfully!');
                    resetFields()
                    loadRecordToTable(); // Reload the records
                } else if (jobject.error === "notfound") {
                    alert('Package not found.');
                } else if (jobject.error === "deleteerror") {
                    alert('An error occurred while deactivating the package.');
                } else {
                    alert('Unknown error occurred.');
                }
            },
            error: function(xhr, status, error) {
                alert('AJAX Error: ' + error);
            }
        });
    }


    // ******************Function to reset the form fields and selected tests table**************************
    function resetFields() {
        // Clear text input fields
        $('#Pkg_name').val('');
        $('#Pkg_price').val('');
        $('#idlabpackages').val('');
        $('#selectedTests').empty();
        $('#testDropdown').prop('selectedIndex', 0);
        pkgTests = [];
        $('#saveBtn').prop('disabled', false);
        $('#saveBtn').show();
        totalTestPrice = 0;
        updateTotalPriceLabel();
    }

    function removeTest(button, testID, testPrice) {
        // Remove the test ID from the pkgTests array
        var index = pkgTests.indexOf(testID);
        totalTestPrice -= parseFloat(testPrice);
        updateTotalPriceLabel();
        if (index !== -1) {
            pkgTests.splice(index, 1);
        }

        // Find and remove the corresponding table row
        var row = button.closest('tr');
        if (row) {
            row.remove();
        }
    }



    // ******************Function to Disable save button when table row is clicked**********************************
    $(document).ready(function() {
        $('#createdTestPackages tbody').on('click', 'tr', function() {
            $('#saveBtn').hide();
        });
    });


    // ******************Functionprice text feild validation**********************************
    $(document).ready(function() {
        $('#Pkg_price').on('input', function() {
            var value = $(this).val();
            var regex = /^[+]?\d+(\.\d+)?$/;

            if (value && !regex.test(value)) {
                $(this).val(value.slice(0, -1));
            }
        });
    });
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

    /* Style for the disabled Save button */
    #saveBtn:disabled {
        background-color: #d3d3d3;
        color: #a9a9a9;
        cursor: not-allowed;


    }
</style>
@stop

@section('body')


<h2 class="pageheading" style="margin-top: -1px;"> Create Test Packages
</h2>
<div class="container">
    <div class="card" style="height: 750px;">
        <div class="card-body">
            <div style="width: 1000px; display: flex;">
                <!-- Add test package part -->
                <div style="flex: 1; padding: 10px; border: 2px #8e7ef7 solid; margin-right: 5px; border-radius: 10px;">
                    <b><u><i>Add New Test Package</i></u></b>
                    <div style="display: flex; align-items: center; margin-bottom: 10px; margin-top: 10px;">
                        <label style="width: 150px;font-size: 18px;">Package Name &nbsp;:</label>
                        <input type="text" name=" Pkg_name" class="input-text" id="Pkg_name" style="width: 250px">
                        <input type="hidden" name="idlabpackages" id="idlabpackages">
                    </div>
                    <div style="display: flex; align-items: center; margin-bottom: 10px;">
                        <label style="width: 150px;font-size: 18px;">Package Price &nbsp;:</label>
                        <input type="text" name=" Pkg_price" class="input-text" id="Pkg_price" style="width: 250px">
                    </div>
                    <div style="display: flex; align-items: center; margin-bottom: 10px;">
                        <label style="width: 150px;font-size: 18px;">Tests &nbsp;:</label>
                        <select name="tgroup" style="width: 273px" class="input-text" id="testDropdown">
                            <option value="%"></option>
                            <?php
                            $Result = DB::select("select c.name,c.tgid, c.price from test a, Lab_has_test b,Testgroup c where a.tid = b.test_tid and b.testgroup_tgid = c.tgid and b.lab_lid='" . $_SESSION['lid'] . "' group by c.name order by c.name");
                            foreach ($Result as $res) {
                                $tgid = $res->tgid;
                                $group = $res->name;
                                $price = $res->price;

                                if (isset($tgroup) && $tgroup == $tgid) {
                            ?>
                                    <option value="{{ $tgid}}" selected="selected">{{ $group }} - {{ $price }}</option>
                                <?php
                                } else {
                                ?>
                                    <option value="{{ $tgid}}">{{ $group }} - {{ $price }}</option>
                            <?php
                                }
                            }

                            if (!isset($tgroup)) {
                                $tgroup = "%";
                            }
                            ?>
                        </select>
                    </div> <br><br><br><br>
                    <div style="display: flex; justify-content: flex-center; gap: 5px; margin-bottom: 10px;">
                        <input type="button" style="color:green" class="btn" id="saveBtn" value="Save" onclick="savePackage()">
                        <input type="button" style="color:Blue" class="btn" id="updateBtn" value="Update" onclick="updatePackage()">
                        <input type="button" style="color:red" class="btn" id="deleteBtn" value="Delete" onclick="deletePackage()">
                        <input type="button" style="color:gray" class="btn" id="resetbtn" value="Reset" onclick="resetFields()">
                    </div>
                </div>

                <!-- selected tests part -->

                <div style="flex: 1; padding: 10px; border: 2px #8e7ef7 solid; border-radius: 10px;">
                    <b><u><i>Selected Tests</i></u></b>
                    <div class="pageTableScope" style="height: 250px; margin-top: 10px;">
                        <table style="font-family: Futura, 'Trebuchet MS', Arial, sans-serif; font-size: 13pt;" id="pdataTable" width="100%" border="0" cellspacing="2" cellpadding="0">
                            <thead>
                                <tr class="viewTHead">
                                    <td class="fieldText" style="width: 50px;">Test ID</td>
                                    <td class="fieldText" style="width: 350px;">Name</td>
                                    <td class="fieldText" style="width: 80px;">Action</td>
                                </tr>
                            </thead>
                            <tbody id="selectedTests">
                                <!-- Dynamic rows will be added here -->
                            </tbody>
                        </table>

                    </div>

                </div>

            </div>

            <label style="width: 150px;font-size: 18px;">Total Amount</label>
            <label id="totalPriceLabel" style="width: 150px;font-size: 18px;">000.00</label>



            <div style="width: 1000px; display: flex;">

                <div style="flex: 1; padding: 10px; ">
                    <b><u><i>Crerated Test Packages</i></u></b>
                    <div class="pageTableScope" style="height: 250px; margin-top: 10px;">

                        <table id="createdTestPackages" width="100%" border="0" cellspacing="2" cellpadding="0">
                            <thead>
                                <tr class="viewTHead">
                                    <td class="fieldText" style="width: 150px;">Package ID</td>
                                    <td class="fieldText" style="width: 350px;">Name</td>
                                    <td class="fieldText" style="width: 150px;">Price</td>
                                </tr>
                            </thead>
                            <tbody id="record_tbl">

                            </tbody>
                        </table>

                    </div>
                </div>

            </div>
        </div>

    </div>
</div>




@stop
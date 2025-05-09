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
        const today = new Date().toISOString().split('T')[0];
        $('#ser_date').val(today);
        const params = new URLSearchParams(window.location.search);

        const sampleNo = params.get('sampleNo');
        const date = params.get('date');

        if (sampleNo) {
            document.getElementById('sampleNo').value = sampleNo;
            view_selected_patient(sampleNo,date);
            
        }else {
            loadcurrentSampleNo();
        }

        if (date) {
            document.getElementById('selected_date').value = date;
        }

        load_test();
        

        // if (!sampleNo && date) {
        //     loadcurrentSampleNo();
        //     load_test();
        // }
        

    });

    //view selected invoice
    function view_selected_patient(sampleNo, date)
    {
        $.ajax({
            type: "GET",
            url: "getSelectedInvoice",
            data: {
                'sampleNo': sampleNo,
                'date': date
            },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    const patientData = response.data.patient;
                    const testData = response.data.tests;
                    const invoiceData = response.data.invoice;
                    const lpsRecords = response.data.lpsRecords;
                    const firstRecord = lpsRecords[0] || {};
                    // Populate patient fields
                    $('#initial').val(patientData.initials || ''); // Use empty string if null
                    $('#fname').val(patientData.fname || '');
                    $('#lname').val(patientData.lname || '');
                    $('#dob').val(patientData.dob || '');
                    $('#nic').val(patientData.nic || '');
                    $('#address').val(patientData.address || '');
                    $('input[name="gender"][value="' + patientData.gender_idgender + '"]').prop('checked', true);
                    $('#years').val(patientData.age || '');
                    $('#months').val(patientData.months || '');
                    $('#days').val(patientData.days || '');
                    $('#Ser_tpno').val(patientData.tpno || '');
                    $('#invoiceId').val(invoiceData.iid || '');


                    $('#refDropdown').val(firstRecord.ref_id || '');  
                    $('#refDropdown option').filter(function() {
                        return $(this).text().trim() === firstRecord.refby;
                    }).prop('selected', true);


                    $('#refcode').val(firstRecord.code || '');
                    $('#inv_remark').val(firstRecord.specialnote || '');


                // Set the discount dropdown value
                    if (invoiceData && invoiceData.did && invoiceData.value) {
                        $('#discount_percentage').val(invoiceData.did + ":" + invoiceData.value);
                    } else {
                        $('#discount_percentage').val(''); 
                    }
                    $('#total_amount').text(invoiceData.total ? invoiceData.total.toFixed(2) : '0.00'); 
                    $('#discount').val(invoiceData.discount || 0); 
                    $('#grand_total').text(invoiceData.gtotal ? invoiceData.gtotal.toFixed(2) : '0.00'); 
                    $('#paid').val(invoiceData.paid || 0); 
                    $('#due').text((invoiceData.gtotal - invoiceData.paid).toFixed(2)); 

                    var paymeth = "";
                        $('input[name="payment_method"]').prop('checked', false); 
                        if(invoiceData.paymentmethod == 'cash') {
                            paymeth = "1"; 
                        } else if(invoiceData.paymentmethod == 'card') {
                            paymeth = "2"; 
                        } else if(invoiceData.paymentmethod == 'voucher') {
                            paymeth = "6"; 
                        } else if(invoiceData.paymentmethod == 'split') {
                            paymeth = "5"; 
                        } else if(invoiceData.paymentmethod == 'credit') {
                            paymeth = "credit"; 
                        } else if(invoiceData.paymentmethod == 'cheque') {
                            paymeth = "3"; 
                        } 
                    $('input[name="payment_method"][value="' + paymeth + '"]').prop('checked', true); 

                        if (invoiceData && invoiceData.multiple_delivery_methods) {
                            try {
                                console.log("Raw delivery methods:", invoiceData.multiple_delivery_methods);
                                const deliveryMethods = invoiceData.multiple_delivery_methods
                                    .split(',')
                                    .map(method => method.trim());
                                
                                console.log("Parsed methods:", deliveryMethods);
                                $('#hard_copy, #sms, #email, #whatsapp').prop('checked', false);
                                deliveryMethods.forEach(method => {
                                    $(`#${method.toLowerCase().replace(' ', '_')}`).prop('checked', true);
                                });
                                
                            } catch (e) {
                                console.error("Error parsing delivery methods:", e);
                                $('#hard_copy').prop('checked', true);
                            }
                        } else {
                            console.log("No delivery methods found, using default");
                            $('#hard_copy').prop('checked', true);
                        }

                
                    $('#Branch_record_tbl').empty();
                    // testData.forEach(test => {
                    //     const newRow = `
                    //         <tr data-id="${test.tgid}" data-lpsid="${test.lpsid}">
                    //             <td align="left">${test.tgid}</td>
                    //             <td align="left">${test.group}</td>
                    //             <td align="right" class="price-column">${test.price.toFixed(2)}</td>
                    //             <td align="left">${test.time}</td>
                    //             <td align="right">${test.f_time}</td>
                    //             <td align="left">
                    //                 <input type="checkbox" class="barcode-checkbox" checked>
                    //             </td>
                    //             <td align="center">${test.urgent_sample}</td>  
                    //             <td align="center">${test.type}</td>  
                    //         </tr>`;
                    //     $('#Branch_record_tbl').append(newRow);
                    // });
                    testData.forEach(test => {
                        let rowStyle = '';
                        let urgentDisplay = '';

                        // Check if the test's lpsid is marked as urgent in lpsRecords
                        const matchingLps = lpsRecords.find(lps => lps.lpsid == test.lpsid && lps.urgent_sample == 1);

                        if (matchingLps) {
                            rowStyle = 'style="background-color: pink;"';
                            urgentDisplay = '<span style="color: red; font-weight: bold;">***</span>';
                        }

                        const newRow = `
                            <tr data-id="${test.tgid}" data-lpsid="${test.lpsid}" ${rowStyle}>
                                <td align="left">${test.tgid}</td>
                                <td align="left">${test.group}</td>
                                <td align="right" class="price-column">${test.price.toFixed(2)}</td>
                                <td align="left">${test.time}</td>
                                <td align="right">${test.f_time}</td>
                                <td align="left">
                                    <input type="checkbox" class="barcode-checkbox" checked>
                                </td>
                                <td align="center">${urgentDisplay}</td>  
                                <td align="center">${test.type}</td>  
                            </tr>`;

                        $('#Branch_record_tbl').append(newRow);
                    });



                
                } else {
                    alert(response.message || 'No data found.');
                }
            },
            error: function(xhr) {
                console.log('Error:', xhr);
                var errorMsg = xhr.responseJSON ? xhr.responseJSON.error : 'An unexpected error occurred.';
                alert(errorMsg);
            }
        });
    }



    function load_test() {
        var labBranchDropdown = $('#labBranchDropdown').val();

        $.ajax({
            type: "GET",
            url: "getTests",
            data: {
                'labBranchId': labBranchDropdown
            },
            dataType: "json",
            success: function(response) {
                $('#testlist').empty();

                if (response.options) {
                    $('#testlist').html(response.options);
                } else {
                    $('#testlist').append('<option value="">No Tests Available</option>');
                }

            },
            error: function(xhr) {
                console.log('Error:', xhr);
                var errorMsg = xhr.responseJSON ? xhr.responseJSON.error : 'An unexpected error occurred.';
                alert(errorMsg);
            }
        });
    }



    // *---***-----------******Sample Number Generator***********----------------
    function loadcurrentSampleNo() {
        var labBranchDropdown = document.getElementById("labBranchDropdown");


        $.ajax({
            type: "GET",
            url: "getCurrentSampleNumber",
            data: {
                'labBranchId': labBranchDropdown.value

            },
            success: function(response) {
                $('#sampleNo').val(response);
            },
            error: function(xhr) {
                console.log('Error:', xhr);
                var errorMsg = xhr.responseJSON ? xhr.responseJSON.error : 'An unexpected error occurred.';
                alert(errorMsg);
            }
        });

    }

    // ----------------------------*******Add tests to the patient Registration Table**********---------------- 
    var itemListTestData = [];

    function setDataToTable(selectedValue) 
    {
        if (!selectedValue) return;

        var parts = selectedValue.split(":");
        if (parts.length < 4) {
            alert("Invalid test format!");
            return;
        }

        var tgid = parts[0];
        var group = parts[1];
        var price = parseFloat(parts[2]) || 0;
        var time = parts[3];
        var f_time = $('#fast_time').val();
        var type = $('#type').val();

        var tstData = `${tgid}@${group}@${price}@${time}@${f_time}@${type}`;

        if (itemListTestData.includes(tstData)) {
            alert("This test is already added!");
            $('#testname').val('');
            return;
        }

        itemListTestData.push(tstData);

        var newRow = `
            <tr data-id="${tgid}">
                <td align="center">${tgid}</td>
                <td align="center">${group}</td>
                <td align="center" class="price-column">${price.toFixed(2)}</td>
                <td align="center">${time}</td>
                <td align="center">${f_time}</td>
                <td align="center">
                    <input type="checkbox" class="barcode-checkbox" checked>
                </td>
                <td align="center">-</td>  
                <td align="center">${type}</td>  
                <td align="center">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeTestItemInTable('${tgid}', '${tstData}')">
                    <i class="fas fa-trash-alt"></i>
                </button>

                </td>
            </tr>`;

        $('#Branch_record_tbl').append(newRow);
        updateTotalAmount();
        $('#testname').val('');
        $('#fast_time').val('0');
    }


    function removeTestItemInTable(tgid, ArrData) {
        var index = itemListTestData.indexOf(ArrData);
        if (index !== -1) {
            itemListTestData.splice(index, 1);
        }

        $(`tr[data-id='${tgid}']`).remove();
        updateTotalAmount();
    }




    // **********Package test adding to table function
    function load_package_tests() {
        var packageId = $('#packageDropdown').val();
        var f_time = $('#fast_time').val();
        itemListTestData = [];
        document.getElementById("Branch_record_tbl").innerHTML = "";

        let packageParts = packageId.split(":");
        let amount = parseFloat(packageParts[2]);

        if (!isNaN(amount)) {
            $('#total_amount').text(amount.toFixed(2));
            $('#grand_total').text(amount.toFixed(2));
            $('#due').text(amount.toFixed(2));
        } else {
            $('#total_amount').text("0.00");
            $('#grand_total').text("0.00");
            $('#due').text("0.00");
        }


        $.ajax({
            type: "GET",
            url: "getPackageTests",
            data: {
                'packageId': packageId

            },
            dataType: "json",
            success: function(response) {

                if (response.testData && response.testData.length > 0) {

                    $.each(response.testData, function(index, test) {
                        var tst_part = test.split("@");

                        var tstData = tst_part[0] + "@" + tst_part[1] + "@" + tst_part[2] + "@" + tst_part[3] + "@" + f_time;
                        var x = itemListTestData.indexOf(tstData);

                        if (x == -1) {
                            itemListTestData.push(tstData);

                            var tr = "<tr id='tblTesttr" + tst_part[0] + "'><td>" + tst_part[0] + "</td><td>" + tst_part[1] + "</td><td align='right'>" + tst_part[2] + "</td><td align='center'>" + tst_part[3] + "</td><td align='center'>" + f_time + "</td><td align='center'><input type='checkbox' id='chk_bcode" + tst_part[0] + "' checked></td>";
                            tr += "<td><center></center></td></tr>";

                            $('#Branch_record_tbl').append(tr);

                            $('#packageDropdown').val("");
                            $('#fast_time').val("0");
                        } else {
                            alert("This test already exists in the table!");
                        }
                    });

                } else {
                    alert("No test data assigned to this package.");
                }
            },
            error: function(xhr) {
                console.log('Error:', xhr);
                var errorMsg = xhr.responseJSON ? xhr.responseJSON.error : 'An unexpected error occurred.';
                alert(errorMsg);
            }
        });
    }


    // **********************grand total genereting*******************
    function updateTotalAmount() {
        let total = 0;

        $('.price-column').each(function() {
            total += parseFloat($(this).text()) || 0;
        });

        $('#total_amount').text(total.toFixed(2));
        $('#grand_total').text(total.toFixed(2));
        $('#due').text(total.toFixed(2));

        resetDiscountAndPaymentFields();
    }



    // **********************payment details discount reset*******************
    function resetDiscountAndPaymentFields() {
        $('#discount').val('');
        $('#discount_percentage').val('');
        $('#paid').val('');
        
    }

    // $(document).on('click', '.remove-row', function() {
    //     $(this).closest('tr').remove();
    //     updateTotalAmount();
    // });

    //*************************************************************************************************
    // Apply discount
    // function applyDiscount() 
    // {

    //     var totalAmountText = $('#total_amount').text().replace(/,/g, '').trim();
    //     var totalAmount = parseFloat(totalAmountText) || 0;        
    //     var discountData = $('#discount_percentage').val().split(":");
    //     var discountPercentage = parseFloat(discountData[1]) || 0;  
    //     var manualDiscount = parseFloat($('#discount').val()) || 0;


    //     let discountAmount = 0;

    //     if (discountPercentage > 0) {
    //         discountAmount = (totalAmount * discountPercentage) / 100;
    //         $('#discount').val(discountAmount.toFixed(2)); 
    //     } else if (manualDiscount > 0) {
        
    //         discountAmount = manualDiscount;
    //         $('#discount_percentage').val('');
    //     }

    
    //     if (discountAmount > totalAmount) {
    //         alert("Discount cannot exceed total amount!");
    //         discountAmount = 0;
    //         $('#discount, #discount_percentage').val('');
    //     }


    //     var grandTotal = totalAmount - discountAmount;

    
    //     $('#grand_total').text(grandTotal.toFixed(2));
    //     $('#paid').val('');
    //     $('#due').text(grandTotal.toFixed(2));
    // }

    function applyDiscount() {
        var totalAmountText = $('#total_amount').text().replace(/,/g, '').trim();
        var totalAmount = parseFloat(totalAmountText) || 0;        
        var discountData = $('#discount_percentage').val().split(":");
        var discountPercentage = parseFloat(discountData[1]) || 0;  
        var manualDiscount = parseFloat($('#discount').val()) || 0;

        let discountAmount = 0;

        if (discountPercentage > 0) {
            discountAmount = (totalAmount * discountPercentage) / 100;
            $('#discount').val(discountAmount.toFixed(2)); 
            $('#Voucher_No, #vaucher_amount, #split_cash_amount, #split_card_amount').val('');
        } else if (manualDiscount > 0) {
            discountAmount = manualDiscount;
            $('#discount_percentage').val('');
            $('#Voucher_No, #vaucher_amount, #split_cash_amount, #split_card_amount').val('');
        }

        if (discountAmount > totalAmount) {
            alert("Discount cannot exceed total amount!");
            discountAmount = 0;
            $('#discount, #discount_percentage').val('');
        }

        var grandTotal = totalAmount - discountAmount;

        $('#grand_total').text(grandTotal.toFixed(2));
        $('#paid').val('');
        $('#due').text(grandTotal.toFixed(2));
}



$('#discount, #discount_percentage').on('input change', function() 
    {
        applyDiscount();
    });


    //*************************************************************************************************
    // Calculate due amount
    function calculateDue() {
        var grandTotal = parseFloat($('#grand_total').text()) || 0;
        var payment = parseFloat($('#paid').val()) || 0;
        var due = grandTotal - payment;

        $('#due').text(due.toFixed(2));
    }



    //*************************************************************************************************
    // Table Row selection function
    $(document).on('click', '#Branch_record_tbl tr', function(event) {
        event.stopPropagation();
        $('#Branch_record_tbl tr').removeClass('selected-row');
        $(this).addClass('selected-row');
    });

    $(document).on('click', function() {

        $('#Branch_record_tbl tr').removeClass('selected-row');
    });


    //*************************************************************************************************
    // Priority button pe=rocess function
    $(document).ready(function() {
        $('#make_priority').on('click', function() {
            let selectedRow = $('#Branch_record_tbl tr.selected-row');

            if (selectedRow.length === 0) {
                alert("Please select a test!");
                return;
            }

            let priorityCell = selectedRow.find('td:nth-child(7)');

            if (priorityCell.text().trim() === '***') {

                priorityCell.text('-');
                $(this).val('Make Priority').css('color', 'gray');
                selectedRow.css('background-color', '');
            } else {

                priorityCell.html('<span style="color: red;">***</span>');
                $(this).val('Remove Priority').css('color', 'red');
                selectedRow.css('background-color', 'pink');
            }
        });


        $(document).on('click', '#Branch_record_tbl tr', function() {
            $('#Branch_record_tbl tr').removeClass('selected-row');
            $(this).addClass('selected-row');

            let priorityCell = $(this).find('td:nth-child(7)');

            if (priorityCell.text().trim() === '***') {
                $('#make_priority').val('Remove Priority').css('color', 'red');
                $(this).css('background-color', 'pink');
            } else {
                $('#make_priority').val('Make Priority').css('color', 'gray');
                $(this).css('background-color', '');
            }
        });
    });


    //*************************************************************************************************
    // When dob enter then calculate age function
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("dob").addEventListener("change", function() {
            var dobInput = this.value;

            if (!dobInput) return;

            var dob = new Date(dobInput);
            var today = new Date();

            if (dob > today) {
                alert("Date of Birth cannot be in the future!");
                return;
            }

            var ageYears = today.getFullYear() - dob.getFullYear();
            var ageMonths = today.getMonth() - dob.getMonth();
            var ageDays = today.getDate() - dob.getDate();


            if (ageDays < 0) {
                ageMonths--;
                var lastMonth = new Date(today.getFullYear(), today.getMonth(), 0);
                ageDays += lastMonth.getDate();
            }

            if (ageMonths < 0) {
                ageYears--;
                ageMonths += 12;
            }

            document.getElementById("years").value = ageYears;
            document.getElementById("months").value = ageMonths;
            document.getElementById("days").value = ageDays;
        });
    });

    //##############*****************###################*****************##########################*****************##############################

    //gender select for initial
    document.addEventListener("DOMContentLoaded", function() {
        var initialSelect = document.getElementById("initial");

        function updateGender() {
            var selectedValue = initialSelect.value;

            if (selectedValue == "Mr") {
                document.getElementById("male").checked = true;
            } else if (selectedValue == "Miss" || selectedValue == "Mrs") {
                document.getElementById("female").checked = true;
            }
        }
        updateGender();

        initialSelect.addEventListener("change", updateGender);
    });

    //split method total amount set process
    document.addEventListener("DOMContentLoaded", function () 
    {
        const splitCashInput = document.getElementById("split_cash_amount");
        const splitCardInput = document.getElementById("split_card_amount");
        const paidInput = document.getElementById("paid");

        function updatePaidAmount() {
            const cash = parseFloat(splitCashInput.value) || 0;
            const card = parseFloat(splitCardInput.value) || 0;
            paidInput.value = (cash + card).toFixed(2);
            calculateDue(); 
        }

        splitCashInput.addEventListener("input", updatePaidAmount);
        splitCardInput.addEventListener("input", updatePaidAmount);
    });

 //voucher method total amount set process
document.addEventListener("DOMContentLoaded", function () 
{

    const voucherAmountInput = document.getElementById("vaucher_amount");
    const paidInput = document.getElementById("paid");

    function updatePaidAmount() {
        const voucherAmount = parseFloat(voucherAmountInput.value) || 0;
        paidInput.value = (voucherAmount).toFixed(2);
        calculateDue(); 
    }

    voucherAmountInput.addEventListener("input", updatePaidAmount);
});


    var allRecords = [];

    function getAllTableRecords() 
    {

        allRecords = [];

        $('#Branch_record_tbl tr').each(function() {
            let tgid = $(this).find('td:nth-child(1)').text().trim();
            let group = $(this).find('td:nth-child(2)').text().trim();
            let price = parseFloat($(this).find('td:nth-child(3)').text().trim()) || 0;
            let time = $(this).find('td:nth-child(4)').text().trim();
            let f_time = $(this).find('td:nth-child(5)').text().trim();
            let bar_code = $(this).find('td:nth-child(6)').text().trim();
            let priority = $(this).find('td:nth-child(7)').text().trim() === '***' ? '1' : '0';
            let type = $(this).find('td:nth-child(8)').text().trim();
            let testData = `${tgid}@${group}@${price}@${time}@${f_time}@${priority}@${type}`;

            allRecords.push(testData);
        });

        return allRecords;
        // allRecords.forEach(record => console.log(record));

    }

    // ******************Function to save the  data*************************************************************



    function savePatientDetails() 
    {
        var testData = [];
        var rowCount = 0;

        var sampleSufArray = ["", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"];
        var sampleNumberPrefix = $('#sampleNo').val();

        var user_uid_data = $('#user_Uid').val();

        var sampleNo = $('#sampleNo').val();
        var labbranch = $('#labBranchDropdown').val();
        var type = $('#type').val();
        var source = $('#source').val();
        var tpno = $('#Ser_tpno').val();
        var initial = $('#initial').val();
        var fname = $('#fname').val();
        var lname = $('#lname').val();
        var dob = $('#dob').val();

        var years = $('#years').val().trim();
        var months = $('#months').val().trim();
        var days = $('#days').val().trim();

        var gender = $('input[name="gender"]:checked').val();
        var nic = $('#nic').val();
        var address = $('#address').val();
        var refcode = $('#refcode').val();
        var ref = $('#refDropdown').val();
        var testname = $('#testname').val();
        var pkgname = $('#pkgname').val();
        var fast_time = $('#fast_time').val();

        var total_amount = $('#total_amount').text().trim() || '0.00';
        var discount = $('#discount').val().trim() || '0.00'; // Get discount value
        var discount_percentage = $('#discount_percentage').val().trim() || '0'; // Get discount percentage
        var discountId = $('#discount_percentage option:selected').val().split(":")[0]; // Get selected discount ID
        var split_cash_amount = $('#split_cash_amount').val().trim() || '0.00';
        var split_card_amount = $('#split_card_amount').val().trim() || '0.00';
        var vaucher_amount = $('#vaucher_amount').val().trim() || '0.00';

        var grand_total = $('#grand_total').text().trim() || '0.00';
    
        var payment_method = $('input[name="payment_method"]:checked').val() || 'cash';
        var paid = $('#paid').val().trim() || '0.00';
        var due = $('#due').text().trim() || '0.00';

        var inv_remark = $('#inv_remark').val();

        // Collect delivery methods
            var deliveryMethods = [];
            if ($('#hard_copy').is(':checked')) deliveryMethods.push('Hard Copy');
            if ($('#sms').is(':checked')) deliveryMethods.push('SMS');
            if ($('#email').is(':checked')) deliveryMethods.push('Email');
            if ($('#whatsapp').is(':checked')) deliveryMethods.push('WhatsApp');

        // Join with comma if multiple methods selected
        var deliveryMethodsString = deliveryMethods.join(', ');

        // Validate essential fields
        if (!fname || !lname) {
            alert('First Name and Last Name are required.');
            return;
        }
        // if (years === '' || months === '' || days === '') {
        //     alert('All age fields (Years, Months, Days) are required.');
        //     return;
        // }

        if (!gender) {
            alert('Gender is required.');
            return;
        }

        // Build testData array
        $('#Branch_record_tbl tr').each(function(index) {
            var tgid = $(this).find('td:nth-child(1)').text().trim();
            var group = $(this).find('td:nth-child(2)').text().trim();
            var price = parseFloat($(this).find('td:nth-child(3)').text().trim()) || 0;
            var time = $(this).find('td:nth-child(4)').text().trim();
            var f_time = $(this).find('td:nth-child(5)').text().trim();
            var bar_code = $(this).find('td:nth-child(6) input[type="checkbox"]').is(':checked') ? 'Yes' : 'No';
            var priority = $(this).find('td:nth-child(7)').text().trim() === '***' ? '1' : '0';
            var testType = $(this).find('td:nth-child(8)').text().trim();
            var sampleSuffix = (index < sampleSufArray.length) ? sampleSufArray[index] : '';
            var fullSampleNo = sampleNumberPrefix + sampleSuffix;

            testData.push({
                sampleNo: fullSampleNo,
                tgid: tgid,
                group: group,
                price: price,
                time: time,
                f_time: f_time,
                bar_code: bar_code,
                priority: priority,
                type: testType,

            });
        });

        if (testData.length === 0) {
            alert('Please add at least one test to the table.');
            return;
        }

   
        var token = $('input[name="_token"]').val();

        var postData = {
            _token: token, 
            userUID: user_uid_data,
            sampleNo: sampleNumberPrefix,
            labbranch: labbranch,
            type: type,
            source: source,
            tpno: tpno,
            initial: initial,
            fname: fname,
            lname: lname,
            dob: dob,
            years: years,
            months: months,
            days: days,
            gender: gender,
            nic: nic,
            address: address,
            refcode: refcode,
            ref: ref,
            testname: testname,
            pkgname: pkgname,
            fast_time: fast_time,
            test_data: testData,
            total_amount: total_amount,
            discount: discount, // Include discount value
            discountId: discountId, // Include discount ID
            split_cash_amount: split_cash_amount,
            split_card_amount: split_card_amount,
            vaucher_amount: vaucher_amount,
            grand_total: grand_total,
            payment_method: payment_method,
            paid: paid,
            due: due,
            inv_remark: inv_remark,
            delivery_methods: deliveryMethodsString
        };

        console.log("Data to be sent:", postData);

        $.ajax({
            type: "POST",
            url: "/savePatient",
            data: postData, // NOT JSON.stringify – Laravel 4.2 doesn't auto-handle JSON well
            beforeSend: function() {
                console.log("Sending data to server...");
            },
            success: function(response) {

                console.log("Server Response:", response);
                alert(response.message || 'Patient saved successfully!');
                resetPage();

            },
            error: function(xhr) {
                console.error('Error:', xhr);
                var errorMsg = xhr.responseJSON ?
                    (xhr.responseJSON.error || xhr.responseJSON.message || 'Unknown error') :
                    'Server communication failed';
                alert('Error: ' + errorMsg);
            }
        });
    }


// ******************Bill search Function************************************

   

    function view_search_patient()
    {
        var searchDate = $('#ser_date').val();
        var searchSampleNo = $('#ser_sampleno').val();
        $.ajax({
            type: "GET",
            url: "getSearchPatient",
            data: {
                'searchDate': searchDate,
                'searchSampleNo': searchSampleNo
            },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    const patientData = response.data.patient;
                    const testData = response.data.tests;
                    const invoiceData = response.data.invoice;
                    const lpsRecords = response.data.lpsRecords;
                    const firstRecord = lpsRecords[0] || {};
                    // Populate patient fields
                    $('#initial').val(patientData.initials || ''); // Use empty string if null
                    $('#fname').val(patientData.fname || '');
                    $('#lname').val(patientData.lname || '');
                    $('#dob').val(patientData.dob || '');
                    $('#nic').val(patientData.nic || '');
                    $('#address').val(patientData.address || '');
                    $('input[name="gender"][value="' + patientData.gender_idgender + '"]').prop('checked', true);
                    $('#years').val(patientData.age || '');
                    $('#months').val(patientData.months || '');
                    $('#days').val(patientData.days || '');
                    $('#Ser_tpno').val(patientData.tpno || '');
                    $('#invoiceId').val(invoiceData.iid || '');


                    $('#refDropdown').val(firstRecord.ref_id || '');  
                    $('#refDropdown option').filter(function() {
                        return $(this).text().trim() === firstRecord.refby;
                    }).prop('selected', true);


                    $('#refcode').val(firstRecord.code || '');
                    $('#inv_remark').val(firstRecord.specialnote || '');


                // Set the discount dropdown value
                    if (invoiceData && invoiceData.did && invoiceData.value) {
                        $('#discount_percentage').val(invoiceData.did + ":" + invoiceData.value);
                    } else {
                        $('#discount_percentage').val(''); 
                    }
                    $('#total_amount').text(invoiceData.total ? invoiceData.total.toFixed(2) : '0.00'); 
                    $('#discount').val(invoiceData.discount || 0); 
                    $('#grand_total').text(invoiceData.gtotal ? invoiceData.gtotal.toFixed(2) : '0.00'); 
                    $('#paid').val(invoiceData.paid || 0); 
                    $('#due').text((invoiceData.gtotal - invoiceData.paid).toFixed(2)); 

                    var paymeth = "";
                        $('input[name="payment_method"]').prop('checked', false); 
                        if(invoiceData.paymentmethod == 'cash') {
                            paymeth = "1"; 
                        } else if(invoiceData.paymentmethod == 'card') {
                            paymeth = "2"; 
                        } else if(invoiceData.paymentmethod == 'voucher') {
                            paymeth = "6"; 
                        } else if(invoiceData.paymentmethod == 'split') {
                            paymeth = "5"; 
                        } else if(invoiceData.paymentmethod == 'credit') {
                            paymeth = "credit"; 
                        } else if(invoiceData.paymentmethod == 'cheque') {
                            paymeth = "3"; 
                        } 
                    $('input[name="payment_method"][value="' + paymeth + '"]').prop('checked', true); 

                        if (invoiceData && invoiceData.multiple_delivery_methods) {
                            try {
                                console.log("Raw delivery methods:", invoiceData.multiple_delivery_methods);
                                const deliveryMethods = invoiceData.multiple_delivery_methods
                                    .split(',')
                                    .map(method => method.trim());
                                
                                console.log("Parsed methods:", deliveryMethods);
                                $('#hard_copy, #sms, #email, #whatsapp').prop('checked', false);
                                deliveryMethods.forEach(method => {
                                    $(`#${method.toLowerCase().replace(' ', '_')}`).prop('checked', true);
                                });
                                
                            } catch (e) {
                                console.error("Error parsing delivery methods:", e);
                                $('#hard_copy').prop('checked', true);
                            }
                        } else {
                            console.log("No delivery methods found, using default");
                            $('#hard_copy').prop('checked', true);
                        }

                
                    $('#Branch_record_tbl').empty();
                    // testData.forEach(test => {
                    //     const newRow = `
                    //         <tr data-id="${test.tgid}" data-lpsid="${test.lpsid}">
                    //             <td align="left">${test.tgid}</td>
                    //             <td align="left">${test.group}</td>
                    //             <td align="right" class="price-column">${test.price.toFixed(2)}</td>
                    //             <td align="left">${test.time}</td>
                    //             <td align="right">${test.f_time}</td>
                    //             <td align="left">
                    //                 <input type="checkbox" class="barcode-checkbox" checked>
                    //             </td>
                    //             <td align="center">${test.urgent_sample}</td>  
                    //             <td align="center">${test.type}</td>  
                    //         </tr>`;
                    //     $('#Branch_record_tbl').append(newRow);
                    // });
                    testData.forEach(test => {
                        let rowStyle = '';
                        let urgentDisplay = '';

                        // Check if the test's lpsid is marked as urgent in lpsRecords
                        const matchingLps = lpsRecords.find(lps => lps.lpsid == test.lpsid && lps.urgent_sample == 1);

                        if (matchingLps) {
                            rowStyle = 'style="background-color: pink;"';
                            urgentDisplay = '<span style="color: red; font-weight: bold;">***</span>';
                        }

                        const newRow = `
                            <tr data-id="${test.tgid}" data-lpsid="${test.lpsid}" ${rowStyle}>
                                <td align="left">${test.tgid}</td>
                                <td align="left">${test.group}</td>
                                <td align="right" class="price-column">${test.price.toFixed(2)}</td>
                                <td align="left">${test.time}</td>
                                <td align="right">${test.f_time}</td>
                                <td align="left">
                                    <input type="checkbox" class="barcode-checkbox" checked>
                                </td>
                                <td align="center">${urgentDisplay}</td>  
                                <td align="center">${test.type}</td>  
                            </tr>`;

                        $('#Branch_record_tbl').append(newRow);
                    });



                
                } else {
                    alert(response.message || 'No data found.');
                }
            },
            error: function(xhr) {
                console.log('Error:', xhr);
                var errorMsg = xhr.responseJSON ? xhr.responseJSON.error : 'An unexpected error occurred.';
                alert(errorMsg);
            }
        });
    }


    //*************************************************************************************************
    function resetPage() 
    {
        window.location.href = '/patientRegistration';
    }

    // function resetForm() {
    //     $('#type').val('');
    //     $('#source').val('');
    //     $('#tpno').val('');
    //     $('#initial').val('');
    //     $('#fname').val('');
    //     $('#lname').val('');
    //     $('#dob').val('');
    //     $('#years').val('');
    //     $('#months').val('');
    //     $('#days').val('');
    //     $('#nic').val('');
    //     $('#address').val('');
    //     $('#refcode').val('');
    //     $('#refDropdown').val('');
    //     $('#testname').val('');
    //     $('#pkgname').val('');
    //     $('#Ser_tpno').val('');
    //     $('#fast_time').val('');
    //     $('#total_amount').text('0.00');
    //     $('#discount').val('0.00');
    //     $('#discount_percentage').val('0');
    //     $('#grand_total').text('0.00');
    //     // $('input[name="payment_method"]:checked').prop('checked', false);
    //     $('#paid').val('0.00');
    //     $('#due').text('0.00');
    //     itemListTestData = [];
    //     $('#Branch_record_tbl').empty();
    //     $('#Branch_record_tbl tbody').empty();
    //     loadcurrentSampleNo();
    // }
    //*************************************************************************************************

    //   ***********#######TP Search########*************
    document.addEventListener('click', function(event) 
    {
        var inputField = document.getElementById('Ser_tpno');
        var suggestionBox = document.getElementById('tpno_suggestions');

        if (!inputField.contains(event.target) && !suggestionBox.contains(event.target)) {
            suggestionBox.style.display = 'none';
        }

        var inputField = document.getElementById('refcode');
        var suggestionBox = document.getElementById('refcode_suggestions');

        if (!inputField.contains(event.target) && !suggestionBox.contains(event.target)) {
            suggestionBox.style.display = 'none';
        }
    });

    function searchUserRecords()
    {
        var Usertpno = $('#Ser_tpno').val();

        if (Usertpno.length < 3) {
            $('#tpno_suggestions').hide();
            return;
        }

        $.ajax({
            type: "GET",
            url: "/getAllUsers",
            data: {
                Usertpno: Usertpno
            },
            success: function(data) {
                var suggestionsHtml = '';
                if (data.length > 0) {

                    $.each(data, function(index, user) {
                        suggestionsHtml += '<div class="suggestion-item" onclick="selectTP(\'' + user.tpno + '\', \'' + user.uid + '\')">' +
                            user.tpno + ' - ' + user.fname + ' ' + user.lname + '</div>';
                    });

                    $('#tpno_suggestions').html(suggestionsHtml).show();
                } else {
                    $('#tpno_suggestions').hide();
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr.statusText);
            }
        });
    }

    
    function selectTP(tpno, userID) 
    {
        $('#Ser_tpno').val(tpno);
        $('#tpno_suggestions').hide();
        $('#user_Uid').val(userID);
        $.ajax({
            type: "GET",
            url: "/getUserDetailsByTP",
            data: {
                useruid: userID
            },
            success: function(data) {
                $('#initial').val(data.initials);
                $('#fname').val(data.fname);
                $('#lname').val(data.lname);
                $('#dob').val(data.dob || '');
                $('#nic').val(data.nic);
                $('#address').val(data.address);
                $('input[name="gender"]').prop('checked', false);
                if (data.gender_idgender == 1) {
                    $('#male').prop('checked', true);
                } else if (data.gender_idgender == 2) {
                    $('#female').prop('checked', true);
                }


                if (data.dob) {

                    var dob = new Date(data.dob);
                    var today = new Date();

                    var ageYears = today.getFullYear() - dob.getFullYear();
                    var ageMonths = today.getMonth() - dob.getMonth();
                    var ageDays = today.getDate() - dob.getDate();

                    if (ageDays < 0) {
                        ageMonths--;
                        var lastMonth = new Date(today.getFullYear(), today.getMonth(), 0);
                        ageDays += lastMonth.getDate();
                    }

                    if (ageMonths < 0) {
                        ageYears--;
                        ageMonths += 12;
                    }

                    $('#years').val(ageYears);
                    $('#months').val(ageMonths);
                    $('#days').val(ageDays);
                } else {

                    $('#years').val(data.age || '');
                    $('#months').val(data.months || '');
                    $('#days').val(data.days || '');
                }
            },
            error: function(xhr) {
                alert('Error loading user data: ' + xhr.statusText);
            }
        });
    }

    function searchRefferenceCode() 
    {
        var refCode = $('#refcode').val();

        if (refCode.length < 1) {
            $('#refcode_suggestions').hide();
            return;
        }

        $.ajax({
            type: "GET",
            url: "/getRefCode",
            data: {
                keyword: refCode
            },
            success: function(data) {
                var suggestionsHtml = '';
                if (data.length > 0) {
                    $.each(data, function(index, ref) {
                        suggestionsHtml += '<div class="suggestion-item" onclick="selectRef(\'' + ref.code + '\', \'' + ref.idref + '\')">' +
                            ref.code + ' - ' + ref.name + '</div>';
                    });
                    $('#refcode_suggestions').html(suggestionsHtml).show();
                } else {
                    $('#refcode_suggestions').hide();
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr.statusText);
            }
        });
    }

    function selectRef(code, idref) 
    {

        $('#refcode').val(code);
        $('#refcode_suggestions').hide();
        $('#hidden_idref').val(idref);
        $('#refDropdown').val(idref);
    }
//-----------------------------------------------------------------------
    $('#refDropdown').on('change', function() {
    var selectedOption = $(this).find('option:selected');
    var selectedCode = selectedOption.data('code');
    
    $('#refcode').val(selectedCode || '');
    
    if($('#hidden_idref').length) {
        $('#hidden_idref').val($(this).val());
    }
});

    // document.getElementById('refDropdown').addEventListener('change', function ()
    //  {
    //     var selectedOption = this.options[this.selectedIndex];
    //     var selectedCode = selectedOption.getAttribute('data-code');

    //     if (selectedCode) {
    //         document.getElementById('refcode').value = selectedCode;
    //     } else {
    //         document.getElementById('refcode').value = '';
    //     }
    //     document.getElementById('hidden_idref').value = this.value;
    // });

//-----------------------------------------------------------------------

    function updateRefCode() 
    {
        var dropdown = document.getElementById("refDropdown");
        var selectedOption = dropdown.options[dropdown.selectedIndex];
        var refCode = selectedOption.getAttribute("data-code") || '';
        document.getElementById("refcode").value = refCode; 
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
    //*************************************************************************************************




    //--------------------------Feilds validation Process----------------------------------------------

    $(document).ready(function() 
    {
        $("#tpno").on("input", function() {
            let value = $(this).val();
            $(this).val(value.replace(/[^0-9]/g, ""));
        });
    });

    $(document).ready(function() 
    {
        $("#fname").on("input", function() {
            let value = $(this).val();
            $(this).val(value.replace(/[^a-zA-Z\s]/g, ""));
        });
    });

    //when payment method split and voucher hided feild show and hide function
    document.addEventListener("DOMContentLoaded", function()
    {
   
        document.getElementById("vaucher_div").style.display = "none";
        document.getElementById("split_div").style.display = "none";

        const paymentRadios = document.getElementsByName("payment_method");
        

        paymentRadios.forEach(radio => {
            radio.addEventListener("change", function() {
                // Hide both divs initially
                document.getElementById("vaucher_div").style.display = "none";
                document.getElementById("split_div").style.display = "none";

        
                $("#split_cash_amount").val('');
                $("#split_card_amount").val('');
                $("#vaucher_amount").val('');
                $("#Voucher_No").val('');
                $("#paid").val('');
                calculateDue();

                if (this.value === "6") {
                    document.getElementById("vaucher_div").style.display = "flex";
                } else if (this.value === "5") {
                    document.getElementById("split_div").style.display = "flex";
                }
            });
        });
    });

    function goToViewInvoice() 
    {
        window.location.href = '/viewinvoices';
    }

    // document.getElementById('refDropdown').addEventListener('change', function () {
    //     var selectedOption = this.options[this.selectedIndex];
    //     var refCode = selectedOption.getAttribute('data-code') || '';
    //     document.getElementById('refcode').value = refCode;
    // });


    //When Payment method split and voucher payment feild disable function
    function togglePaidField() {
        const paidField = document.getElementById('paid');
        const voucherSelected = document.getElementById('voucher').checked;
        const splitSelected = document.getElementById('split').checked;
        const cashSelected = document.getElementById('cash').checked;
        const chequeSelected = document.getElementById('cheque').checked;
        const creditSelected = document.getElementById('credit').checked;
        const cardSelected = document.getElementById('card').checked;
    

        if (voucherSelected || splitSelected) {
            paidField.readOnly = true;
            paidField.value = '';
        } else if  (cashSelected || chequeSelected || creditSelected || cardSelected){
            paidField.readOnly = false;
        }
}


document.addEventListener('DOMContentLoaded', function() {
    togglePaidField();
});


function viewSelectedInvoicePayments() {
    var invoiceId = $('#invoiceId').val();
    var due = $('#due').val(); 


    window.open("invoicePayments?iid=" + invoiceId+ "&due=" + $('#due').val(), "_blank");
    
      
}


// Function to open the modal
function openModal() {
  document.getElementById("myModal").style.display = "block";
}

// Function to close the modal
function closeModal() {
  document.getElementById("myModal").style.display = "none";
}

// Close the modal if the user clicks outside of it
window.onclick = function(event) {
  if (event.target == document.getElementById("myModal")) {
    closeModal();
  }
}

// Save payment function (example placeholder)
function savePayment() {
  alert('Payment saved!');
}


// *-*-*-*-*-*-*-*-*-*-*PAYMENT UPDATE PROCESS-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*



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

    .selected-row {
        background-color: #1977c9 !important;
        /* Light blue color */
    }

    /* //--------------- */
    .suggestion-box {
        position: absolute;
        background: #fff;
        border: 1px solid #ccc;
        max-height: 150px;
        overflow-y: auto;
        width: 210px;
        z-index: 1000;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    }
   

    .suggestion-item {
        padding: 5px 10px;
        cursor: pointer;
    }

    .suggestion-item:hover {
        background-color: #4b9bf0;
    }

    .autocomplete-suggestions 
    {
    border: 1px solid #e1dede;
    border-radius: 15px;
    background-color: white;
    max-height: 150px;
    overflow-y: auto;
    position: absolute;
    z-index: 1000;
    width: 250px;
    margin-top: 120px;
    margin-left: 150px;
    
    
    }


    /* Modal styles */
    .modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4);
    padding-top: 60px;
    }

    .modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    }

    .close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    }

    .close:hover,
    .close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
    }
</style>

@stop

@section('body')



<div class="container">
    <div class="card" style="height: 1250px; margin-top: 20px; background-color:rgb(222, 222, 223);">
        <div class="card-body">
            <div style="width: 1350px; display: inline-block;">
                <!-- Input group container -->
                <div style="display: flex; align-items: center; margin-bottom: 10px;">

                    <label style="width: 150px;font-size: 18px;">Sample No</label>
                    <input type="text" name="sampleNo" class="input-text" id="sampleNo" style="width: 200px; height: 40px;font-size: 38px;" disabled>

                    <!-- <input type="hidden" name="refID" id="refID"> -->
                    <label style="width: 10px;font-size: 16px;  "></label>
                    <input type="checkbox" name="edit" id="edit" class="ref_chkbox" value="1">
                    <label style="width: 80px;font-size: 18px;">Edit</label>

                    <label style="width: 100px;font-size: 18px; ">Center</label>

                    <select name="labbranch" style="width: 200px; height: 30px" class="input-text" id="labBranchDropdown" onchange="loadcurrentSampleNo(); load_test();">
                        <option value="%" data-code="ML" data-maxno="0" data-mainlab="true">Main Lab</option>
                        <?php
                        $Result = DB::select("SELECT name, code, bid FROM labbranches WHERE Lab_lid = '" . $_SESSION['lid'] . "' ORDER BY name ASC");

                        foreach ($Result as $res) {
                            $branchName = $res->name;
                            $branchCode = $res->code;
                            $bid = $res->bid;


                            $displayText = $branchCode . " : " . $branchName;
                        ?>
                            <option value="<?= $bid ?>"><?= $displayText ?></option>
                        <?php
                        }
                        ?>
                    </select>





                    <input type="hidden" name="crBranch_id" id="crBranch_id">
                    <input type="checkbox" name="lock_branch" id="lock_branch" value="1">
                    <label style="width: 140px;font-size: 16px;  "><b>Lock Branch</b></label>
                    <label style="width: 10px;font-size: 16px;  "></label>
                    <label style="width: 135px;font-size: 16px;  "><b>Search Bill</b></label>
                    <input type="date" name="ser_date" class="input-text" id="ser_date" style="width: 150px">
                    <label style="width: 30px;font-size: 16px;  "></label>
                    <label style="width: 135px;font-size: 16px;  "><b>Sample No</b></label>
                    <input type="text" name="ser_sampleno" class="input-text" id="ser_sampleno" style="width: 150px">
                    <input type="button" style="width: 80px" class="btn" id="ser_btn" value="Search" onclick="view_search_patient()">
                </div>

                <div style="display: flex; align-items: center;">
                    <label style="width: 89px;font-size: 18px; ">Search</label>
                    <input type="text" name="ser_pdetails" class="input-text" id="ser_pdetails" style="width: 210px">

                    <input type="text" name="invoiceId" class="input-text" id="invoiceId" style="width: 210px">

                    <!-- <label style="width: 50px;font-size: 18px;">Type</label>
                    <select type="text" name="type" class="input-text" id="type" style="width: 80px; height: 30px">
                        <option value="1">In</option>
                        <option value="2">Out</option>
                    </select> -->

                    <label style="width: 80px;font-size: 16px;  "><b>Source</b></label>
                    <select type="text" name="source" class="input-text" id="source" style="width: 135px; height: 30px">
                        <option value="1">Walking</option>
                        <option value="2">Centers</option>
                    </select>
                    <label style="width: 10px;font-size: 16px;  "></label>
                    <input type="checkbox" name="ignore_date" class="ignore_date" value="1">
                    <label style="width: 140px;font-size: 16px;  "><b>Ignore Date</b></label>
                </div>
                <div style="display: flex; align-items: center;">
                    <label style="width: 90px; font-size: 18px;">T.P.NO</label>
                    <input type="hidden" id="user_Uid">
                    <input type="text" id="Ser_tpno" class="input-text" oninput="searchUserRecords()" maxlength="10" autocomplete="off" placeholder="Enter TP Number" style="width: 210px;">
                    <div id="tpno_suggestions"
                        style="position: absolute; background: #fff; 
                    border: 1px solid #ccc; max-height: 150px; 
                    overflow-y: auto; width: 510px; z-index: 1100; 
                    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); display:none; top: 250px; left: 50px;"></div>

                    <label style="width: 850px; font-size: 18px;"></label>
                    <input type="button" style="width: 80px" class="btn" id="backBtn" value="Back" onclick="">
                    <input type="button" style="width: 80px" class="btn" id="frontBtn" value="Front" onclick="">
                </div>

            </div>

            <!-- --------------*********************************************************************************-------------------->
            <div style="width:1350px; display: flex;">
                <!--Left Side -->
                <div style="flex: 1; padding: 10px; border: 2px #8e7ef7 solid; margin-right: 5px; border-radius: 10px;">

                    <div style="display: flex; align-items: center;  ">
                        <label style="width: 150px;font-size: 18px; ">First Name:</label>
                        <select type="text" name="initial" class="input-text" id="initial" style="width: 80px; height: 30px; ">
                            <option value="Mr">Mr</option>
                            <option value="Mrs">Mrs</option>
                            <option value="Miss">Miss</option>
                            <option value="Dr">Dr</option>
                            <option value="Hons">Hons</option>
                        </select>
                        <input type="text" name=" fname" class="input-text" id="fname" style="width: 380px">
                    </div>
                    <div style="display: flex; align-items: center; margin-top: 5px;">
                        <label style="width: 150px;font-size: 18px; ">Last Name:</label>
                        <input type="text" name=" lname" class="input-text" id="lname" style="width: 250px">
                        <label style="width: 50px;font-size: 16px;  ">DOB</label>
                        <input type="date" name="dob" class="input-text" id="dob" style="width: 140px">
                    </div>
                    <div style="display: flex; align-items: center;margin-top: 5px; ">
                        <label style="width: 150px;font-size: 18px; ">Age:</label>
                        <label style="width: 50px;font-size: 18px;  ">Years</label>
                        <input type="number" name=" years" class="input-text" id="years" style="width: 60px;margin-right:15px">
                        <label style="width: 65px;font-size: 18px; ">Months</label>
                        <input type="number" name=" months" class="input-text" id="months" style="width: 60px;margin-right:15px">
                        <label style="width: 45px;font-size: 18px; ">Days</label>
                        <input type="number" name=" days" class="input-text" id="days" style="width: 60px">
                    </div>
                    <div style="display: flex; align-items: center; margin-top: 5px;">
                        <label style="width: 150px;font-size: 18px; ">Gender:</label>
                        <label><input type="radio" id="male" name="gender" value="1"> Male</label>
                        <label><input type="radio" id="female" name="gender" value="2"> Female</label>
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
                        <input type="text" name="refcode" class="input-text" id="refcode" style="width: 250px" oninput="searchRefferenceCode()">
                        <div id="refcode_suggestions" class="autocomplete-suggestions"></div>
                        <input type="hidden" id="hidden_idref" name="idref">
                        <input type="button" style="color:green" class="btn" id="resetbtn" value="Add New Reference" onclick="window.location.href='{{ url('/doc-reference') }}';">
                    </div>
                    <div style="display: flex; align-items: center; margin-top: 5px;">
                        <label style="width: 150px;font-size: 18px; ">Refered:</label>
                        <select name="ref" style="width: 450px; height: 30px" class="input-text" id="refDropdown" onchange="updateRefCode()">

                            <option value=""></option>
                            <?php
                            $Result = DB::select("select idref, name,code from refference where lid = '" . $_SESSION['lid'] . "' AND name IS NOT NULL ORDER BY name ASC");

                            foreach ($Result as $res) {
                                $refId = $res->idref;
                                $refName = $res->name;
                                $refCode = $res->code;
                            ?>
                             
                               <option value="<?= $refId ?>" data-code="<?= $refCode ?>"><?= $refName ?> </option>
                               


                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <hr style=" background-color: rgb(19, 153, 211); height: 5px; border: none; margin-top: 20px;">
                    <!-- <div style="display: flex; align-items: center; margin-top: 5px;">
                        <label style="width: 150px;font-size: 18px; "><b>Test Name</b>:</label>
                        <input type="text" name="testname" class="input-text" id="testname" list="testlist" oninput="setDataToTable(this.value)" style="width: 350px">
                        <datalist id="testlist"></datalist>
                        <input type="checkbox" name="byname" id="byname" class="ref_chkbox" value="1">
                        <label style="width: 70px;font-size: 16px;  "><b>By Name</b></label>
                    </div> -->

                    <div style="display: flex; align-items: center; margin-top: 5px;">
                        <label style="width: 150px;font-size: 18px; "><b>Test Name</b>:</label>
                        <!-- Using onchange event here to trigger the function when a valid value is selected -->
                        <input type="text" name="testname" class="input-text" id="testname" list="testlist" onchange="setDataToTable(this.value)" style="width: 220px">
                        <datalist id="testlist">

                        </datalist>
                        <label style="width: 40px;font-size: 18px;margin-left: 15px"><b>Type</b></label>
                        <select type="text" name="type" class="input-text" id="type" style="width: 70px; height: 30px">
                            <option value="In">In</option>
                            <option value="Out">Out</option>
                        </select>
                        <label style="width: 100px;font-size: 18px; margin-left: 15px "><b>Fast Time</b>:</label>
                        <input type="text" name=" fast_time" class="input-text" id="fast_time" value="0" style="width: 40px">
                        <input type="checkbox" name="fastcheck" id="fastcheck" class="ref_chkbox" value="1">
                        <!-- <input type="checkbox" name="byname" id="byname" class="ref_chkbox" value="1">
                        <label style="width: 70px;font-size: 16px;  "><b>By Name</b></label> -->
                    </div>
                    <div style="display: flex; align-items: center; margin-top: 5px;">
                        <label style="width: 150px;font-size: 18px; "><b>Package Name</b>:</label>
                        <input type="text" name="packageDropdown" class="input-text" id="packageDropdown"
                            list="package_test_list"
                            onchange="load_package_tests()"
                            style="width: 350px">

                        <datalist id="package_test_list">


                            <option value=""></option>
                            <?php

                            $Result = DB::select("select idlabpackages, name,price FROM labpackages WHERE Lab_lid = '" . $_SESSION['lid'] . "' ORDER BY name ASC");

                            foreach ($Result as $res) {
                                $packageId = $res->idlabpackages;
                                $packageName = $res->name;
                                $packagePrice = $res->price;
                            ?>
                                <option value="<?= $packageId . ":" . $packageName . ":" . $packagePrice ?>"><?= $packageName ?></option>
                            <?php
                            }
                            ?>

                        </datalist>

                        <!-- <label style="width: 120px;font-size: 18px; "><b>Fast Time</b>:</label>
                        <input type="text" name=" fast_time" class="input-text" id="fast_time" value="0" style="width: 40px">
                        <input type="checkbox" name="fastcheck" id="fastcheck" class="ref_chkbox" value="1"> -->
                    </div>

                        <hr style=" background-color: rgb(19, 153, 211); height: 5px; border: none; margin-top: 20px;">

                        <div style="display: flex; align-items: center; margin-top: 5px;">
                            <label style="width: 125px; font-size: 18px;">Total Amount:</label>
                            <label style="width: 30px; font-size: 18px;">Rs: </label>
                            <label id="total_amount" style=" padding-right: 50px; font-size: large; color: rgb(17, 17, 17); font-family: 'Times New Roman', Times, serif; font-weight: bolder;">000,000.00</label>

                            <br>

                            <label style="width: 80px; font-size: 18px; ">Discount:</label>
                            <input type="number" name="discount" class="input-text" id="discount" style="width: 80px; " oninput="applyDiscount()" readonly>

                            <select name="discount_percentage" class="input-text" id="discount_percentage" style="margin-left: 20px; width: 100px; height: 30px;" onchange="applyDiscount()">
                                <option value="">%</option>
                                <?php

                                $Result = DB::select("select did, name, value FROM Discount WHERE Lab_lid = '" . $_SESSION['lid'] . "' ORDER BY name ASC");

                                foreach ($Result as $res) {
                                    $discountId = $res->did;
                                    $discountName = $res->name;
                                    $discountValue = $res->value;
                                ?>
                                    <option value="<?= $discountId.":".$discountValue ?>"><?= $discountName ?> (<?= $discountValue ?>%)</option>
                                    <?php
                                }
                                ?>
                            </select>

                        </div>

                        <div style="display: flex; align-items: center; margin-top: 20px;">
                            <label style="width: 125px; font-size: 18px;"><b>Grand Total:</b></label>
                            <label style="width: 30px; font-size: 18px;">Rs: </label>
                            <label style=" padding-right: 45px; color: rgb(17, 17, 17); font-size: large; font-family: 'Times New Roman', Times, serif; font-weight: bolder;" id="grand_total">000,000.00</label>

                            <label><input type="radio" name="payment_method" id="cash" value="1" checked onchange="togglePaidField();"> Cash</label>
                            <label><input type="radio" name="payment_method" id="card" value="2"onchange="togglePaidField();"> Card</label>
                            <label><input type="radio" name="payment_method" id="credit" value="credit"onchange="togglePaidField();"> Credit</label>
                            <label><input type="radio" name="payment_method" id="cheque" value="3"onchange="togglePaidField();"> Cheque</label>
                            <label><input type="radio" name="payment_method" id="voucher" value="6" onchange="togglePaidField();"> Voucher</label>
                            <label><input type="radio" name="payment_method" id="split" value="5" onchange="togglePaidField();"> Split</label>
                        </div>

                        <div style="display: flex; align-items: center; margin-top: 20px;">
                            <label style="width: 125px; font-size: 18px;">Payment:</label>
                            <label style="width: 30px; font-size: 18px;">Rs: </label>
                            <input type="number" name="paid" class="input-text" id="paid" style="width: 97px;" oninput="calculateDue()">

                            <label style="width: 35px; font-size: 18px;"></label>
                            <label style="width: 60px; font-size: 18px;">Due:</label>
                            <label style="width: 30px; font-size: 18px;">Rs:</label>
                            <label style="color: rgb(17, 17, 17); font-size: large; font-family: 'Times New Roman', Times, serif; font-weight: bolder;" id="due">000,000.00</label>
                        </div>
                        <div style="display: flex; align-items: center;margin-top: 15px;" id="vaucher_div">
                            <label style="width: 125px; font-size: 18px;">Vaucher No:</label>
                            <label style="width: 30px; font-size: 18px;"> </label>
                            <input type="number" name="Voucher_No" class="input-text" id="Voucher_No" style="width: 97px;" oninput="">

                            <label style="width: 150px; font-size: 18px; margin-left:35px;">Vaucher Amount</label>
                            <label style="width: 30px; font-size: 18px;">Rs: </label>
                            <input type="number" name="vaucher_amount" class="input-text" id="vaucher_amount" style="width: 97px;" oninput="">

                        </div>
                        <div style="display: flex; align-items: center;margin-top: 15px;" id="split_div">

                            <label style="width: 125px; font-size: 18px;">Cash Amount</label>
                            <label style="width: 30px; font-size: 18px;">Rs: </label>
                            <input type="number" name="cash_amount" class="input-text" id="split_cash_amount" style="width: 97px;" oninput="">

                            <label style="width: 125px; font-size: 18px;margin-left:35px;">Card Amount</label>
                            <label style="width: 30px; font-size: 18px; margin-left: 25px;">Rs: </label>
                            <input type="number" name="card_amount" class="input-text" id="split_card_amount" style="width: 97px;" oninput="">

                        </div>
                        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 15px;">
                            <input type="button" style="color:black; width: 180px; height: 50px;" class="btn" id="cashDrower" value="Cash Drawer">
                            <input type="button" style="color:black; width: 210px; height: 50px;" onclick="viewSelectedInvoicePayments()" class="btn" id="update_payment" value="Update Payment">
                        </div>



                </div>


                <!-- Right Side -->

                <div style="flex: 1; padding: 10px; border: 2px #8e7ef7 solid; border-radius: 10px;">
                    <div class="pageTableScope" style="height: 250px; margin-top: 10px;">
                        <table style="font-family: Futura, 'Trebuchet MS', Arial, sans-serif; font-size: 13pt;" id="test_tbl" width="100%" border="0" cellspacing="2" cellpadding="0">
                            <thead>
                                <tr class="viewTHead">
                                    <td align="center" class="fieldText" style="width: 20px;">Id </td>
                                    <td align="center" class="fieldText" style="width: 250px; text-align:left">Test Name</td>
                                    <td align="center" class="fieldText" style="width: 20px;"> price</td>
                                    <td align="center" class="fieldText" style="width: 10px;">TAT</td>
                                    <td align="center" class="fieldText" style="width: 10px;">Fast. Time</td>
                                    <td align="center" class="fieldText" style="width: 10px;"> B.code</td>
                                    <td align="center" class="fieldText" style="width: 10px;">Priority</td>
                                    <td align="center" class="fieldText" style="width: 10px;">Type</td>
                                    <td align="center" class="fieldText" style="width: 10px;">Action</td>
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
                    </div>
                    <div style="display: flex; align-items: center;margin-top: 5px; ">
                        <label style="width: 230px;font-size: 18px; "><b>Report Collection Method</b></label>
                    </div>
                    <div style="display: flex; flex-wrap: wrap; gap: 30px; margin-top: 15px;">
                        <div style="display: flex; flex-direction: column; align-items: center;">
                            <label>Hard Copy</label>
                            <input type="checkbox" name="hard_copy" id="hard_copy" value="Hard Copy" checked style="margin-top: 5px;">
                        </div>
                        <div style="display: flex; flex-direction: column; align-items: center;">
                            <label>SMS</label>
                            <input type="checkbox" name="sms" id="sms" value="SMS" style="margin-top: 5px;">
                        </div>
                        <div style="display: flex; flex-direction: column; align-items: center;">
                            <label>Email</label>
                            <input type="checkbox" name="email" id="email" value="Email" style="margin-top: 5px;">
                        </div>
                        <div style="display: flex; flex-direction: column; align-items: center;">
                            <label>WhatsApp</label>
                            <input type="checkbox" name="whatsapp" id="whatsapp" value="WhatsApp" style="margin-top: 5px;">
                        </div>
                        <div style="display: flex; flex-direction: column; align-items: center;">
                            <label>Package Invoice</label>
                            <input type="checkbox" name="package_invoice" id="package_invoice" value="package_invoice" style="margin-top: 5px;">
                        </div>
                        <div style="display: flex; flex-direction: column; align-items: center;">
                            <label for="print_bill" style="font-size: 16px;">Print Bill</label>
                            <input type="checkbox" name="print_bill" id="print_bill" class="ref_chkbox" value="1" style="margin-top: 5px;">
                        </div>
                        <div style="display: flex; flex-direction: column; align-items: center;">
                            <label for="claim_bill" style="font-size: 16px;">Claim Bill</label>
                            <input type="checkbox" name="claim_bill" id="claim_bill" class="ref_chkbox" value="1" style="margin-top: 5px;">
                        </div>
                        <div style="display: flex; flex-direction: column; align-items: center;">
                            <label for="two_copies" style="font-size: 16px;">2 Copies</label>
                            <input type="checkbox" name="two_copies" id="two_copies" class="ref_chkbox" value="1" style="margin-top: 5px;">
                        </div>
                    </div>

              


                    <hr style=" background-color: rgb(19, 153, 211); height: 5px; border: none;">


                    <div style="display: flex; align-items: center;margin-top: 4px; ">
                        <label style="width: 50px;font-size: 16px;  "></label>
                        <input type="button" style="color:green; width: 210px; height: 50px" class="btn" id="savebtn" value="Save" onclick="getAllTableRecords();savePatientDetails()">
                        <input type="button" style="color:rgb(245, 168, 34); width: 210px; height: 50px" class="btn" id="updatebtn" value="Update Details " onclick="">
                        <input type="button" style="color:rgb(10, 113, 158); width: 210px; height: 50px" class="btn" id="getlastpatientbtn" value="Get Last patient" onclick="">
                    </div>
                    <div style="display: flex; align-items: center;margin-top: 5px; ">
                        <label style="width: 50px;font-size: 16px;  "></label>
                        <input type="button" style="color:gray; width: 210px; height: 50px" class="btn" id="resetbtn" value="Reset" onclick="resetPage()">
                        <input type="button" style="color:rgb(10, 113, 158); width: 210px; height: 50px" class="btn" id="print_invoicebtn" value="Print Invoice " onclick="">
                        <input type="button" style="color:rgb(10, 113, 158); width: 210px; height: 50px" class="btn" id="view_invoicebtn" value="View Invoice" onclick="goToViewInvoice()">
                    </div>

                </div>



                <!-- Button to open the modal -->
                    <input type="button" style="color:black; width: 210px; height: 50px;" 
                    onclick="openModal()" class="btn" id="update_payment" value="Open Modal">

                    <!-- The Modal -->
                    <div id="myModal" class="modal">
                    <!-- Modal content -->
                    <div class="modal-content">
                    <span class="close" onclick="closeModal()">&times;</span>
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
                        <!-- PHP loop to load payment methods -->
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
                    <input type="button" style="flex: 0 0 80px; margin-left: 10px; color:red" class="btn" id="ser_btn" value="Delete">
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
                    </div>
                    </div>
                
            </div>
        </div>
    </div>

</div>





@stop
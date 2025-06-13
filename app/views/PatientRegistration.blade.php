<?php ?>
@extends('Templates/WiTemplate')


@section('title')
Add New Patient
@stop

@section('head')

<script src="{{ asset('JS/ReportCalculations.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



<script>

    $(document).ready(function () {
        window.scrollTo(0, 85);
        
        const today = new Date().toISOString().split('T')[0];
        $('#ser_date').val(today);
        $('#patientDate').val(today);
        const params = new URLSearchParams(window.location.search);

        const sampleNo = params.get('sampleNo');
        const date = params.get('date');

        if (sampleNo) {
            document.getElementById('sampleNo').value = sampleNo;
            view_selected_patient(sampleNo, date);

              // Barcode Privilage checking
        $.ajax({
            url: '/barcode-feature-checking',
            type: 'GET',
            success: function (response) {
                console.log('AJAX response received:', response);

                if (response.hasFeature) {
                    $('#print_barcode').show();
                    $('#group_barcode').show();
                    $('#test_wise_bcode').show();
                    $('#remove_barcode').show();
                    $('#rep_chkboxlbl').show();
                    $('#rep_chkbox').show();
                   

                    // Log display after showing
                    console.log('Buttons shown based on feature present');
                    ['print_barcode', 'group_barcode', 'test_wise_bcode', 'remove_barcode','rep_chkbox','rep_chkboxlbl'].forEach(id => {
                        console.log(id + ':', $('#' + id).css('display'));
                    });
                } else {
                    console.log('Feature not present; buttons remain hidden');
                }
            },
            error: function (xhr) {
                console.error('AJAX error:', xhr.status, xhr.statusText);
                if (xhr.status === 401) {
                    alert('Session expired. Please log in again.');
                }
            }
        });

        //  reportnig method Privilage checking
             $.ajax({
            url: '/reportnig-feature-checking',
            type: 'GET',
            success: function (response) {
                console.log('AJAX response received:', response);

                if (response.hasFeature) {
                    $('#smstd').show();
                    $('#emailtd').show();
                    $('#whatsapptd').show();


                    // Log display after showing
                    console.log('Buttons shown based on feature present');
                    ['sms', 'email', 'whatsapp'].forEach(id => {
                        console.log(id + ':', $('#' + id).css('display'));
                    });
                } else {
                    console.log('Feature not present; buttons remain hidden');
                }
            },
            error: function (xhr) {
                console.error('AJAX error:', xhr.status, xhr.statusText);
                if (xhr.status === 401) {
                    alert('Session expired. Please log in again.');
                }
            }
        });
              //  patientData privilage checking
               $.ajax({
                url: '/patientDetailsEditing-feature-checking',
                type: 'GET',
                success: function (response) {
                    console.log('AJAX response received:', response);

                    if (response.hasPdetailsUpdateFeature) {
                        $('.updatebtntd').show(); 
                        console.log('Buttons and checkbox shown based on feature present');
                    } else {
                        $('.updatebtntd').hide(); 
                        console.log('Feature not present; buttons and checkbox remain hidden');
                    }

                    
                    console.log('updatebtntd:', $('.updatebtntd').css('display'));
                },
                error: function (xhr) {
                    console.error('AJAX error:', xhr.status, xhr.statusText);
                    if (xhr.status === 401) {
                        alert('Session expired. Please log in again.');
                    }
                }
            });
        } else {
            loadcurrentSampleNo();
            
        }

        if (date) {
            document.getElementById('selected_date').value = date;
        }

        load_test();

        // AJAX call to check barcode feature
    // Check if all buttons are initially hidden
        console.log('Initial display states:');
        ['print_barcode', 'group_barcode', 'test_wise_bcode', 'remove_barcode'].forEach(id => {
            console.log(id + ':', $('#' + id).css('display'));
        });

        // Barcode Privilage checking
        $.ajax({
            url: '/barcode-feature-checking',
            type: 'GET',
            success: function (response) {
                console.log('AJAX response received:', response);

                if (response.hasFeature) {
                    $('#print_barcode').show();
                    $('#group_barcode').show();
                    $('#test_wise_bcode').show();
                    $('#remove_barcode').show();
                    $('#rep_chkboxlbl').show();
                    $('#rep_chkbox').show();
                   

                    // Log display after showing
                    console.log('Buttons shown based on feature present');
                    ['print_barcode', 'group_barcode', 'test_wise_bcode', 'remove_barcode','rep_chkbox','rep_chkboxlbl'].forEach(id => {
                        console.log(id + ':', $('#' + id).css('display'));
                    });
                } else {
                    console.log('Feature not present; buttons remain hidden');
                }
            },
            error: function (xhr) {
                console.error('AJAX error:', xhr.status, xhr.statusText);
                if (xhr.status === 401) {
                    alert('Session expired. Please log in again.');
                }
            }
        });

        //  reportnig method Privilage checking
             $.ajax({
            url: '/reportnig-feature-checking',
            type: 'GET',
            success: function (response) {
                console.log('AJAX response received:', response);

                if (response.hasFeature) {
                    $('#smstd').show();
                    $('#emailtd').show();
                    $('#whatsapptd').show();


                    // Log display after showing
                    console.log('Buttons shown based on feature present');
                    ['sms', 'email', 'whatsapp'].forEach(id => {
                        console.log(id + ':', $('#' + id).css('display'));
                    });
                } else {
                    console.log('Feature not present; buttons remain hidden');
                }
            },
            error: function (xhr) {
                console.error('AJAX error:', xhr.status, xhr.statusText);
                if (xhr.status === 401) {
                    alert('Session expired. Please log in again.');
                }
            }
        });


        //  patientData privilage checking
            $.ajax({
                url: '/patientDetailsEditing-feature-checking',
                type: 'GET',
                success: function (response) {
                    console.log('AJAX response received:', response);

                    if (response.hasPdetailsUpdateFeature) {
                        $('.updatebtntd').show(); 
                        console.log('Buttons and checkbox shown based on feature present');
                    } else {
                        $('.updatebtntd').hide(); 
                        console.log('Feature not present; buttons and checkbox remain hidden');
                    }

                    
                    console.log('updatebtntd:', $('.updatebtntd').css('display'));
                },
                error: function (xhr) {
                    console.error('AJAX error:', xhr.status, xhr.statusText);
                    if (xhr.status === 401) {
                        alert('Session expired. Please log in again.');
                    }
                }
            });



        checkInvoiceField();
        $('#invoiceId').on('change keyup paste', checkInvoiceField);

        function checkInvoiceField() {
            const hasValue = $('#invoiceId').val().trim() !== '';
            $('#update_payment').prop('disabled', !hasValue);
            console.log('Invoice field check:', hasValue ? 'has value' : 'empty');
        }
    });






    function load_test() {
        var labBranchDropdown = $('#labBranchDropdown').val();

        $.ajax({
            type: "GET",
            url: "getTests",
            data: {
                'labBranchId': labBranchDropdown
            },
            dataType: "json",
            success: function (response) {
                $('#testlist').empty();

                if (response.options) {
                    $('#testlist').html(response.options);
                } else {
                    $('#testlist').append('<option value="">No Tests Available</option>');
                }

            },
            error: function (xhr) {
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
            success: function (response) {
                $('#sampleNo').val(response);
            },
            error: function (xhr) {
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
        if (!selectedValue)
            return;

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
                    <button type="button" class="delbtn" onclick="removeTestItemInTable('${tgid}', '${tstData}')">
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
        var testInput = document.getElementById('testname');

  
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
            success: function (response) {
                document.getElementById("packageDropdown").disabled = true;
                document.getElementById("testname").disabled = true;

                if (response.testData && response.testData.length > 0) {

                    $.each(response.testData, function (index, test) {
                        var tst_part = test.split("@");

                        var tstData = tst_part[0] + "@" + tst_part[1] + "@" + tst_part[2] + "@" + tst_part[3] + "@" + f_time;
                        var x = itemListTestData.indexOf(tstData);

                        if (x == -1) {
                            itemListTestData.push(tstData);

                            var tr = "<tr id='tblTesttr" + tst_part[0] + "'><td>" + tst_part[0] + "</td><td>" + tst_part[1] + "</td><td align='right'>" + tst_part[2] + "</td><td align='center'>" + tst_part[3] + "</td><td align='center'>" + f_time + "</td><td align='center'><input type='checkbox' id='chk_bcode" + tst_part[0] + "' checked></td>";
                            tr += "<td><center></center></td></tr>";

                            $('#Branch_record_tbl').append(tr);

                            // $('#packageDropdown').val("");
                            $('#fast_time').val("0");
                        } else {
                            alert("This test already exists in the table!");
                        }
                    });

                } else {
                    alert("No test data assigned to this package.");
                }
            },
            error: function (xhr) {
                console.log('Error:', xhr);
                var errorMsg = xhr.responseJSON ? xhr.responseJSON.error : 'An unexpected error occurred.';
                alert(errorMsg);
            }
        });
    }


    // **********************grand total genereting*******************
    function updateTotalAmount() {
        let total = 0;

        $('.price-column').each(function () {
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



    $('#discount, #discount_percentage').on('input change', function ()
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
    $(document).on('click', '#Branch_record_tbl tr', function (event) {
        event.stopPropagation();
        $('#Branch_record_tbl tr').removeClass('selected-row');
        $(this).addClass('selected-row');
    });

    $(document).on('click', function () {

        $('#Branch_record_tbl tr').removeClass('selected-row');
    });


    //*************************************************************************************************
    // Priority button pe=rocess function
    $(document).ready(function () {
        $('#make_priority').on('click', function () {
            let selectedRow = $('#Branch_record_tbl tr.selected-row');

            if (selectedRow.length === 0) {
                alert("Please select a test!");
                return;
            }

            let priorityCell = selectedRow.find('td:nth-child(7)');

            if (priorityCell.text().trim() === '***') {

                priorityCell.text('-');
                $(this).val('Make Priority').css('color', 'blue');
                selectedRow.css('background-color', '');
            } else {

                priorityCell.html('<span style="color: red;">***</span>');
                $(this).val('Remove Priority').css('color', 'red');
                selectedRow.css('background-color', '');
            }
        });


        $(document).on('click', '#Branch_record_tbl tr', function () {
            $('#Branch_record_tbl tr').removeClass('selected-row');
            $(this).addClass('selected-row');

            let priorityCell = $(this).find('td:nth-child(7)');

            if (priorityCell.text().trim() === '***') {
                $('#make_priority').val('Remove Priority').css('color', 'red');
                $(this).css('background-color', 'pink');
            } else {
                $('#make_priority').val('Make Priority').css('color', 'blue');
                $(this).css('background-color', '');
            }
        });
    });


    //*************************************************************************************************
    // When dob enter then calculate age function
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById("dob").addEventListener("change", function () {
            var dobInput = this.value;

            if (!dobInput)
                return;

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
    document.addEventListener("DOMContentLoaded", function () {
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

        $('#Branch_record_tbl tr').each(function () {
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
        if ($('#hard_copy').is(':checked'))
            deliveryMethods.push('Hard Copy');
        if ($('#sms').is(':checked'))
            deliveryMethods.push('SMS');
        if ($('#email').is(':checked'))
            deliveryMethods.push('Email');
        if ($('#whatsapp').is(':checked'))
            deliveryMethods.push('WhatsApp');

        // Join with comma if multiple methods selected
        var deliveryMethodsString = deliveryMethods.join(', ');

        // Validate essential fields
            if (!fname) {
            alert('First Name is required.');
            closepatientConfirmModal();
             $("#loaderOverlay").hide();
              $("#savebtn").prop("disabled", false);
            return;
        }
        if (!gender) {
            alert('Gender is required.');
            closepatientConfirmModal();
             $("#loaderOverlay").hide();
              $("#savebtn").prop("disabled", false);
            return;
        }

        // Build testData array
        $('#Branch_record_tbl tr').each(function (index) {
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
            closepatientConfirmModal();
             $("#loaderOverlay").hide();
              $("#savebtn").prop("disabled", false);
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
            discount: discount, 
            discountId: discountId, 
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
            data: postData, 
            beforeSend: function () {
                $("#loaderOverlay").show(); 
                console.log("Sending data to server...");
            },
            success: function (response) {

                console.log("Server Response:", response);
                alert(response.message || 'Patient saved successfully!');
                var sampleData = response.datainv.split('###');
                view_selected_patient(sampleData[0], sampleData[1]);
                printInvoice();


            },
            error: function (xhr) {
                console.error('Error:', xhr);
                var errorMsg = xhr.responseJSON ?
                        (xhr.responseJSON.error || xhr.responseJSON.message || 'Unknown error') :
                        'Server communication failed';
                alert('Error: ' + errorMsg);
            },
                complete: function () {
                $("#loaderOverlay").hide(); 
            }
        });
    }






    //view selected invoice in viewinvoice page proces related function
    function view_selected_patient(sampleNo, date)
    {
        $.ajax({
            async: true,
            type: "GET",
            url: "getSelectedInvoice",
            data: {
                'sampleNo': sampleNo,
                'date': date
            },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    const patientData = response.data.patient;
                    const testData = response.data.tests;
                    const invoiceData = response.data.invoice;
                    const invoicePayments = response.data.invoicePayments;
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
                    $('#patientDate').val(date || '');


                    invoicePayments.forEach(function (payment) {

                        document.getElementById("vaucher_div").style.display = "none";
                        document.getElementById("split_div").style.display = "none";


                        if (payment.paymethod == '1') {
                            $('#split_cash_amount').val(payment.amount || '');
                        } else if (payment.paymethod == '2') {
                            $('#split_card_amount').val(payment.amount || '');
                        }

                        if (payment.paymentmethod == 'split') {
                            document.getElementById("split_div").style.display = "flex";
                        } else if (payment.paymentmethod == 'voucher') {
                            document.getElementById("vaucher_div").style.display = "flex";
                        } else {
                            document.getElementById("vaucher_div").style.display = "none";
                            document.getElementById("split_div").style.display = "none";
                        }

                    });


                    // $('#split_cash_amount').val(invoicePayments.amount || '');
                    // $('#split_card_amount').val(invoicePayments.amount || '');




                    $('#refDropdown').val(firstRecord.ref_id || '');
                    $('#refDropdown option').filter(function () {
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
                    if (invoiceData.paymentmethod == 'cash') {
                        paymeth = "1";
                    } else if (invoiceData.paymentmethod == 'card') {
                        paymeth = "2";
                    } else if (invoiceData.paymentmethod == 'voucher') {
                        paymeth = "6";
                    } else if (invoiceData.paymentmethod == 'split') {
                        paymeth = "5";
                    } else if (invoiceData.paymentmethod == 'credit') {
                        paymeth = "credit";
                    } else if (invoiceData.paymentmethod == 'cheque') {
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

                    testData.forEach(test => {
                        let rowStyle = '';
                        let urgentDisplay = '';
                        let isChecked = true;

                        // Check if the test's lpsid is marked as urgent in lpsRecords
                        const matchingLps = lpsRecords.find(lps => lps.lpsid == test.lpsid && lps.urgent_sample == 1);
                        const barcodedcheck = lpsRecords.find(lps => lps.lpsid == test.lpsid && lps.status == 'barcorded');

                        // Set row style to pink if either condition is met
                        if (barcodedcheck) {
                            rowStyle = 'style="background-color: pink;"';
                            isChecked = false;
                        }

                        if (matchingLps) {
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
                                     <input type="checkbox" class="barcode-checkbox" ${isChecked ? 'checked' : ''}>
                                </td>
                                <td align="center">${urgentDisplay}</td>  
                                <td align="center">${test.type}</td>  
                            </tr>`;

                        $('#Branch_record_tbl').append(newRow);
                    });



                if (sampleNo == "") {
                        $("#savebtn").attr("disabled", false);
                        $("#fname").attr("disabled", false);
                        $("#lname").attr("disabled", false);
                        $("#initial").attr("disabled", false);
                        $("#years").attr("disabled", false);
                        $("#months").attr("disabled", false);
                        $("#days").attr("disabled", false);
                        $("#dob").attr("disabled", false);
                        $("#male").attr("disabled", false);
                        $("#female").attr("disabled", false);
                        $("#nic").attr("disabled", false);
                        $("#address").attr("disabled", false);
                        $("#refcode").attr("disabled", false);
                        $("#refDropdown").attr("disabled", false);
                        $("#testname").attr("disabled", false);
                        $("#packageDropdown").attr("disabled", false);
                    } else {
                        $("#savebtn").attr("disabled", true);
                        $("#print_invoicebtn").attr("disabled", false);
                        $("#fname").attr("readonly", true);
                        $("#lname").attr("readonly", true);
                        $("#initial").attr("readonly", true);
                        $("#years").attr("readonly", true);
                        $("#months").attr("readonly", true);
                        $("#days").attr("readonly", true);
                        $("#dob").attr("readonly", true);
                        $("#male").attr("disabled", true);
                        $("#female").attr("disabled", true);
                        $("#nic").attr("readonly", true);
                        $("#address").attr("readonly", true);
                        $("#refcode").attr("readonly", true);
                        $("#refDropdown").attr("disabled", true);
                        $("#testname").attr("readonly", true);
                        $("#packageDropdown").attr("readonly", true);

                    }




                } else {
                    alert(response.message || 'No data found.');
                }
            },
            error: function (xhr) {
                console.log('Error:', xhr);
                var errorMsg = xhr.responseJSON ? xhr.responseJSON.error : 'An unexpected error occurred.';
                alert(errorMsg);
            }
        });
    }

    $(document).on('change', '.patient_details_edit', function () {
        if ($(this).is(':checked')) {
            // Enable all readonly fields
            $("#fname").attr("readonly", false);
            $("#lname").attr("readonly", false);
            $("#initial").attr("readonly", false);
            $("#years").attr("readonly", false);
            $("#months").attr("readonly", false);
            $("#days").attr("readonly", false);
            $("#dob").attr("readonly", false);
            $("#male").attr("disabled", false);
            $("#female").attr("disabled", false);
            $("#nic").attr("readonly", false);
            $("#address").attr("readonly", false);
            $("#refcode").attr("readonly", false);
            $("#refDropdown").attr("disabled", false);
            $("#testname").attr("readonly", false);
            $("#packageDropdown").attr("readonly", false);
            $("#updatebtn").attr("disabled", false); 
            // $("#updatebtn").hide(); 
        } else {
            // Revert fields back to readonly
            $("#fname").attr("readonly", true);
            $("#lname").attr("readonly", true);
            $("#initial").attr("readonly", true);
            $("#years").attr("readonly", true);
            $("#months").attr("readonly", true);
            $("#days").attr("readonly", true);
            $("#dob").attr("readonly", true);
            $("#male").attr("disabled", true);
            $("#female").attr("disabled", true);
            $("#nic").attr("readonly", true);
            $("#address").attr("readonly", true);
            $("#refcode").attr("readonly", true);
            $("#refDropdown").attr("disabled", true);
            $("#testname").attr("readonly", true);
            $("#packageDropdown").attr("readonly", true);
             $("#updatebtn").attr("disabled", true); 
            //  $("#updatebtn").show();
        }
    });


    // ******************Bill search Function in billing UI************************************
    function view_search_patient()
    {
        var searchDate = $('#ser_date').val();
        var searchSampleNo = $('#ser_sampleno').val();
        if (!searchSampleNo || searchSampleNo.trim() === "") {
            alert('Please enter a sample number to search.');
            return;
        }
        $.ajax({
            type: "GET",
            url: "getSearchPatient",
            data: {
                'searchDate': searchDate,
                'searchSampleNo': searchSampleNo
            },
            dataType: "json",
            success: function (response) {
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
                    $('#sampleNo').val(searchSampleNo || '');


                    $('#refDropdown').val(firstRecord.ref_id || '');
                    $('#refDropdown option').filter(function () {
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
                    if (invoiceData.paymentmethod == 'cash') {
                        paymeth = "1";
                    } else if (invoiceData.paymentmethod == 'card') {
                        paymeth = "2";
                    } else if (invoiceData.paymentmethod == 'voucher') {
                        paymeth = "6";
                    } else if (invoiceData.paymentmethod == 'split') {
                        paymeth = "5";
                    } else if (invoiceData.paymentmethod == 'credit') {
                        paymeth = "credit";
                    } else if (invoiceData.paymentmethod == 'cheque') {
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

                    testData.forEach(test => {
                        let rowStyle = '';
                        let urgentDisplay = '';
                         let isChecked = true;

                        // Check if the test's lpsid is marked as urgent in lpsRecords
                        const matchingLps = lpsRecords.find(lps => lps.lpsid == test.lpsid && lps.urgent_sample == 1);
                        const barcodedcheck = lpsRecords.find(lps => lps.lpsid == test.lpsid && lps.status == 'barcorded');

                        // Set row style to pink if either condition is met
                        if (barcodedcheck) {
                            rowStyle = 'style="background-color: pink;"';
                            isChecked = false;
                        }

                        if (matchingLps) {
                          
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
                                          <input type="checkbox" class="barcode-checkbox" ${isChecked ? 'checked' : ''}>
                                    </td>
                                    <td align="center">${urgentDisplay}</td>  
                                    <td align="center">${test.type}</td>  
                                </tr>`;

                        $('#Branch_record_tbl').append(newRow);
                    });

                    if (sampleNo == "") {
                        $("#savebtn").attr("disabled", false);
                        $("#fname").attr("disabled", false);
                        $("#lname").attr("disabled", false);
                        $("#initial").attr("disabled", false);
                        $("#years").attr("disabled", false);
                        $("#months").attr("disabled", false);
                        $("#days").attr("disabled", false);
                        $("#dob").attr("disabled", false);
                        $("#male").attr("disabled", false);
                        $("#female").attr("disabled", false);
                        $("#nic").attr("disabled", false);
                        $("#address").attr("disabled", false);
                        $("#refcode").attr("disabled", false);
                        $("#refDropdown").attr("disabled", false);
                        $("#testname").attr("disabled", false);
                        $("#packageDropdown").attr("disabled", false);
                    } else {
                        $("#savebtn").attr("disabled", true);
                        $("#print_invoicebtn").attr("disabled", false);
                        $("#fname").attr("readonly", true);
                        $("#lname").attr("readonly", true);
                        $("#initial").attr("readonly", true);
                        $("#years").attr("readonly", true);
                        $("#months").attr("readonly", true);
                        $("#days").attr("readonly", true);
                        $("#dob").attr("readonly", true);
                        $("#male").attr("disabled", true);
                        $("#female").attr("disabled", true);
                        $("#nic").attr("readonly", true);
                        $("#address").attr("readonly", true);
                        $("#refcode").attr("readonly", true);
                        $("#refDropdown").attr("disabled", true);
                        $("#testname").attr("readonly", true);
                        $("#packageDropdown").attr("readonly", true);

                    }

                } else {
                    alert(response.message || 'No data found.');
                    loadcurrentSampleNo();
                    resetPage();
                }
            },
            error: function (xhr) {
                console.log('Error:', xhr);
                var errorMsg = xhr.responseJSON ? xhr.responseJSON.error : 'An unexpected error occurred.';
                alert(errorMsg);
            }
        });
    }




    //*************************************************************************************************

    //   ***********#######TP Search########*************
    document.addEventListener('click', function (event)
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
        var anyFilter = $('#any_filter').is(':checked') ? 1 : 0;

        if ((anyFilter && Usertpno.length < 3) || (!anyFilter && Usertpno.length < 10)) {
            $('#tpno_suggestions').hide();
            return;
        }

        $.ajax({
            type: "GET",
            url: "/getAllUsers",
            data: {
                Usertpno: Usertpno,
                any_filter: anyFilter
            },
            success: function (data) {
                var suggestionsHtml = '';
                if (data.length > 0) {

                    $.each(data, function (index, user) {
                        suggestionsHtml += '<div class="suggestion-item" onclick="selectTP(\'' + user.tpno + '\', \'' + user.uid + '\')">' +
                                user.tpno + ' - ' + user.fname + ' ' + user.lname + '</div>';
                    });

                    $('#tpno_suggestions').html(suggestionsHtml).show();
                } else {
                    $('#tpno_suggestions').hide();
                }
            },
            error: function (xhr) {
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
            success: function (data) {
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


        if (data.dob && data.dob !== '0000-00-00') {
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
            // '0000-00-00' / null / missing DOB - use age values from DB
            $('#years').val(data.age != null ? data.age : '');
            $('#months').val(data.months != null ? data.months : '');
            $('#days').val(data.days != null ? data.days : '');
        }


            },
            error: function (xhr) {
                alert('Error loading user data: ' + xhr.statusText);
            }
        });
    }

    // function searchRefferenceCode()
    // {
    //     var refCode = $('#refcode').val();

    //     if (refCode.length < 1) {
    //         $('#refcode_suggestions').hide();
    //         return;
    //     }

    //     $.ajax({
    //         type: "GET",
    //         url: "/getRefCode",
    //         data: {
    //             keyword: refCode
    //         },
    //         success: function (data) {
    //             var suggestionsHtml = '';
    //             if (data.length > 0) {
    //                 $.each(data, function (index, ref) {
    //                     suggestionsHtml += '<div class="suggestion-item" onclick="selectRef(\'' + ref.code + '\', \'' + ref.idref + '\')">' +
    //                             ref.code + ' - ' + ref.name + '</div>';
    //                 });
    //                 $('#refcode_suggestions').html(suggestionsHtml).show();
    //             } else {
    //                 $('#refcode_suggestions').hide();
    //             }
    //         },
    //         error: function (xhr) {
    //             console.error('Error:', xhr.statusText);
    //         }
    //     });
    // }

    function searchRefferenceCode() {
        var refCode = $('#refcode').val();

        if (refCode.length < 1) {
            $('#refcode_suggestions').hide();
            return;
        }

        $.ajax({
            type: "GET",
            url: "/getRefCode",
            data: { keyword: refCode },
            success: function (data) {
                var suggestionsHtml = '';
                if (data.length > 0) {
                    $.each(data, function (index, ref) {
                        suggestionsHtml += '<div class="suggestion-item" style="padding:5px; cursor:pointer;" onclick="selectRef(\'' +
                            ref.code + '\', \'' + ref.idref + '\', \'' + ref.name + '\')">' +
                            ref.code + ' - ' + ref.name + '</div>';
                    });
                    $('#refcode_suggestions').html(suggestionsHtml).show();
                } else {
                    $('#refcode_suggestions').hide();
                }
            },
            error: function (xhr) {
                console.error('Error:', xhr.statusText);
            }
        });
    }



    //-----------------------------------------------------------------------
    $('#refDropdown').on('change', function () {
        var selectedOption = $(this).find('option:selected');
        var selectedCode = selectedOption.data('code');

        $('#refcode').val(selectedCode || '');

        if ($('#hidden_idref').length) {
            $('#hidden_idref').val($(this).val());
        }
    });



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

    $(document).ready(function ()
    {
        $("#tpno").on("input", function () {
            let value = $(this).val();
            $(this).val(value.replace(/[^0-9]/g, ""));
        });
    });

    $(document).ready(function ()
    {
        $("#fname").on("input", function () {
            let value = $(this).val();
            $(this).val(value.replace(/[^a-zA-Z.\s]/g, ""));
        });
    });

    //when payment method split and voucher hided feild show and hide function
    document.addEventListener("DOMContentLoaded", function ()
    {

        document.getElementById("vaucher_div").style.display = "none";
        document.getElementById("split_div").style.display = "none";

        const paymentRadios = document.getElementsByName("payment_method");


        paymentRadios.forEach(radio => {
            radio.addEventListener("change", function () {
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


        //sample number edit true false
    document.addEventListener("DOMContentLoaded", function () {
        var checkbox = document.getElementById('edit');
        var sampleInput = document.getElementById('sampleNo');

        checkbox.addEventListener('change', function () {
            sampleInput.disabled = !checkbox.checked;
        });
    });

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
        } else if (cashSelected || chequeSelected || creditSelected || cardSelected) {
            paidField.readOnly = false;
        }
    }


    document.addEventListener('DOMContentLoaded', function () {
        togglePaidField();
    });


    function viewSelectedInvoicePayments() {
        var invoiceId = $('#invoiceId').val();
        var due = $('#due').val();


        window.open("invoicePayments?iid=" + invoiceId + "&due=" + $('#due').val(), "_blank");


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
    window.onclick = function (event) {
        if (event.target == document.getElementById("myModal")) {
            closeModal();
        }
    }


    // Save payment function 
    function savePayment() {
        alert('Payment saved!');
    }


    //Print Invoice Section
    function printInvoice() {
        var fname = $('#fname').val().trim();
        // var lname = $('#lname').val().trim();
        var years = $('#years').val().trim();
        var months = $('#months').val().trim();
        var days = $('#days').val().trim();
        var testRows = $('#test_tbl tbody tr').length;

        if (
            fname === "" || 
            (years === "" && months === "" && days === "") || 
            testRows === 0
            ) 
            {
                alert("Please fill all required fields and add at least one test before printing.");
                return; 
            }

            var date = $('#patientDate').val();
            var sno = $('#sampleNo').val();

            var win = window.open("printinvoice/" + sno + "&" + date, '_blank');

            setTimeout(function () {
                win.print();
            }, 5000);

            setTimeout(function () {
                win.close();
                resetPage();
            }, 8000);
    }

    //   document.getElementById("btnPrint").addEventListener("click", function () {
    //     var hardCopyChecked = document.getElementById("hard_copy").checked;

    //     if (hardCopyChecked) {
    //         printInvoice();
    //     } else {
    //         alert("Tick 'Hard Copy' checkbox to print invoice.");
    //     }
    // });


    // *-*-*-*-*-*-*-*-*-*-*most recent test buttons-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
    $(document).ready(function () {
        $.ajax({
            url: '/get-test-codes',
            method: 'GET',
            success: function (response) {
                if (response.success) {
                    const testCodes = response.data.testCodes;
                    const buttons = $('#testCodeButtons button');

                    buttons.each(function (index) {
                        if (index < testCodes.length) {
                            const test = testCodes[index];

                            // Store all needed data in a data attribute on button
                            $(this).text(test.testCode);
                            $(this).prop('disabled', false);

                            // Compose the string in the expected format: "tgid:group:price:time"
                            const valueString = `${test.tgid}:${test.group}:${parseFloat(test.price).toFixed(2)}:${test.testingtime}`;
                            $(this).data('testinfo', valueString);
                        } else {
                            $(this).text('');
                            $(this).prop('disabled', true);
                            $(this).removeData('testinfo');
                        }
                    });

                    // Attach click handlers
                    $('#testCodeButtons button').off('click').on('click', function () {
                        const testInfo = $(this).data('testinfo');
                        if (testInfo) {
                            setDataToTable(testInfo);
                        }
                    });
                }
            },
            error: function () {
                alert('Error loading test codes');
            }
        });
    });

    // *-*-*-*-*-*-*-*-*-*-*most recent test buttons-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*





    function barcodePrint(isGroup) {
        var date = $('#patientDate').val();
        var sno = $('#sampleNo').val();
        // var inv_balance = $('#due').val();
        var isReprint = $('#rep_chkbox').is(':checked');
        var selectedRow = "";
        var tgid = "";
        var testGroupName = "";
        
      
        if (isGroup == false) {

            selectedRow = $('#Branch_record_tbl').find('tr.selected-row');

            if (selectedRow.length === 0) {
                alert("Please select a test!");
                return;
            }

            tgid = selectedRow.find('td').eq(0).text();
            testGroupName = selectedRow.find('td').eq(1).text();
        } 

        


        var win = window.open("printBarcode/" + sno + "&" + date + "&" + isGroup + "&" + tgid + "&" + testGroupName + "&" + (isReprint ? "1" : "0"), '_blank');

        setTimeout(function () {
            win.print();
        }, 5000);


        setTimeout(function () {
            win.close();
            // resetPage();
        }, 8000);

    }



    // 1. SampleNo uniqueness check function
    function getUniqueSampleNo(initialSampleNo, patientDate, callback) {
        let attempt = 0;
        let sampleNo = initialSampleNo;

        function tryNext() {
            if (attempt >= 5) {
                alert("Didn't save patient - Sample No duplicates");
                loadcurrentSampleNo();
                callback(null);
                return;
            }

            $.ajax({
                url: '/checkSampleNo',
                method: 'POST',
                data: {
                    sample_no: sampleNo,
                    patientDate: patientDate
                },
                success: function (res) {
                    if (res.exists) {
                        sampleNo++;
                        attempt++;
                        tryNext();
                    } else {
                        callback(sampleNo);
                    }
                },
                error: function () {
                    alert('Error checking sample number');
                    callback(null);
                }
            });
        }

        tryNext();
    }



    $(document).ready(function () {
        $("#savebtn").click(function () {
            
            $("#savebtn").prop("disabled", true);
            $("#loaderOverlay").show();

            getAllTableRecords();

            setTimeout(function () {
                let initialSampleNo = $("#sampleNo").val();
                let patientDate = $("#patientDate").val();

                if (!initialSampleNo || !patientDate) {
                    alert("Sample Number or Patient Date is empty");
                    $("#loaderOverlay").hide(); 
                    $("#savebtn").prop("disabled", false); 
                    return;
                }

                getUniqueSampleNo(initialSampleNo, patientDate, function (uniqueSampleNo) {
                    if (uniqueSampleNo !== null) {
                        $("#sampleNo").val(uniqueSampleNo);
                        savePatientDetails(); 
                    } else {
                        $("#loaderOverlay").hide();
                        $("#savebtn").prop("disabled", false);
                    }
                });
            }, 300);
        });
    });





    //*************************************************************************************************
    // page reset function
    function resetPage()
    {
        window.location.href = '/patientRegistration';
    }



    //*************************************************************************************************
    // Branch lock checkbox process

    document.addEventListener('DOMContentLoaded', function () {
        const lockBranchCheckbox = document.getElementById('lock_branch');
        const labBranchDropdown = document.getElementById('labBranchDropdown');

        function toggleDropdownLock() {
            if (lockBranchCheckbox.checked) {
                labBranchDropdown.disabled = true;

                localStorage.setItem('lab_branch_value', labBranchDropdown.value);
            } else {
                labBranchDropdown.disabled = false;

                localStorage.removeItem('lab_branch_value');
            }
        }

        lockBranchCheckbox.addEventListener('change', toggleDropdownLock);
        lockBranchCheckbox.addEventListener('change', function () {
            localStorage.setItem('lock_branch_checked', lockBranchCheckbox.checked);
        });

        const savedCheck = localStorage.getItem('lock_branch_checked');
        const savedBranchValue = localStorage.getItem('lab_branch_value');

        if (savedCheck === 'true') {
            lockBranchCheckbox.checked = true;
            if (savedBranchValue) {
                labBranchDropdown.value = savedBranchValue;
            }
            toggleDropdownLock();
        }
    });


    //*************************************************************************************************
    // Get Last Patient button process

    function loadLastPatient() {
        var date = $('#patientDate').val();

        $.ajax({
            url: '/getLastPatientDetails',
            method: 'GET',
            data: {
                patientDate: date
            },
            success: function(response) {
                if (response.status === 'success') {
                    var data = response.data;
                    $('#refby').val(data.refby);
                    $('#initial').val(data.initials);
                    $('#fname').val(data.fname);
                    $('#lname').val(data.lname);
                    $('#years').val(data.age);
                    $('#months').val(data.months);
                    $('#days').val(data.days);
                    $('#Ser_tpno').val(data.tpno);
                    $('#address').val(data.address);
                    $('#sampleNO').val(data.sampleNO);

                    let femaleInitials = ['Mrs', 'Miss'];
                    let maleInitials = ['Mr', 'Dr', 'Hons'];

                    if (femaleInitials.includes(data.initials)) {
                        $('#female').prop('checked', true);
                    } else if (maleInitials.includes(data.initials)) {
                        $('#male').prop('checked', true);
                    } else {
                        $('input[name="gender"]').prop('checked', false);
                    }
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Error occurred while loading last patient.');
            }
        });
    }


    //*************************************************************************************************
    // Back Front buttons process

    function loadPatient(direction) {
        var currentSampleNo = $('#sampleNo').val(); // input field
        var lab_lid = $('#lab_lid').val(); // hidden or dropdown
        var date = $('#patientDate').val(); // input date field

        $.ajax({
            url: '/getPatientDetailsBySample',
            method: 'GET',
            data: {
                sampleNO: currentSampleNo,
                direction: direction, // 'back' or 'front'
                lab_lid: lab_lid,
                patientDate: date
            },
            success: function(response) {
                if (response.status === 'success') {
                    var data = response.data;
                    $('#refby').val(data.refby);
                    $('#initial').val(data.initials);
                    $('#fname').val(data.fname);
                    $('#lname').val(data.lname);
                    $('#years').val(data.years);
                    $('#months').val(data.months);
                    $('#days').val(data.days);
                    $('#Ser_tpno').val(data.tpno);
                    $('#address').val(data.address);
                    $('#sampleNO').val(data.sampleNO); // update to new sample


                    let femaleInitials = ['Mrs', 'Miss'];
                    let maleInitials = ['Mr', 'Dr', 'Hons'];

                    if (femaleInitials.includes(data.initials)) {
                        $('#female').prop('checked', true);
                    } else if (maleInitials.includes(data.initials)) {
                        $('#male').prop('checked', true);
                    } else {
                        // If unknown or not matched, uncheck both
                        $('input[name="gender"]').prop('checked', false);
                    }
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Error occurred while loading data.');
            }
        });
    }

    // Button click bindings
    $('#btnBack').on('click', function() {
        loadPatient('back');
    });

    $('#btnFront').on('click', function() {
        loadPatient('front');
    });


    //*************************************************************************************************
    // PatientDetails update process

    function updatePatientDetails() {
        let sampleNO = $('#sampleNo').val();
        // let lab_lid = $('#lab_lid').val();
        let patientDate = $('#patientDate').val();

        $.ajax({
            url: '/updatePatientDetails',
            method: 'POST',
            data: {
                sampleNO: sampleNO,
                // lab_lid: lab_lid,
                patientDate: patientDate,
                refby: $('#refby').val(),
                initials: $('#initial').val(),
                fname: $('#fname').val(),
                lname: $('#lname').val(),
                years: $('#years').val(),
                months: $('#months').val(),
                days: $('#days').val(),
                tpno: $('#Ser_tpno').val(),
                address: $('#address').val(),
                gender: $('input[name="gender"]:checked').val()
            },
            success: function (response) {
                if (response.status === 'success') {
                    alert('Patient details updated successfully!');
                } else {
                    alert(response.message);
                }
            },
            error: function () {
                alert('Error occurred while updating patient details.');
            }
        });
    }




    function searchRefName() {
        var keyword = $('#refDropdown').val();

        if (keyword.length < 1) {
            $('#refname_suggestions').hide();
            return;
        }

        $.ajax({
            type: "GET",
            url: "/getRefName", // you need to handle this route in backend
            data: {
                keyword: keyword
            },
            success: function (data) {
                var suggestionsHtml = '';
                if (data.length > 0) {
                    $.each(data, function (index, ref) {
                        suggestionsHtml += '<div class="suggestion-item" onclick="selectRef(\'' + ref.code + '\', \'' + ref.idref + '\', \'' + ref.name + '\')">' +
                            ref.name + ' (' + ref.code + ')</div>';
                    });
                    $('#refname_suggestions').html(suggestionsHtml).show();
                } else {
                    $('#refname_suggestions').hide();
                }
            },
            error: function (xhr) {
                console.error('Error:', xhr.statusText);
            }
        });
    }

    function selectRef(code, idref, name) {
        $('#refcode').val(code);
        $('#refDropdown').val(name); // Show ref name in text input
        $('#ref').val(idref);        // Store idref in hidden input

        $('#refcode_suggestions').hide();
        $('#refname_suggestions').hide();
    }

    // Function to open the barcode modal
    function openBcodeModal() {
        var selectedRow = $('#Branch_record_tbl').find('tr.selected-row');

        if (selectedRow.length === 0) {
            alert("Please select a test!");
            return; // Stop here if no test selected
        }

        
        testWiseBarcodeLoad();
    }


    // Function to close the barcode modal
    function closeBcodeModal() {
        document.getElementById("barCodeModal").style.display = "none";
    }



    function testWiseBarcodeLoad() {
        var date = $('#patientDate').val();
        var sno = $('#sampleNo').val();
        var selectedRow = $('#Branch_record_tbl').find('tr.selected-row');
        
        if (selectedRow.length === 0) {
            alert("Please select a test!");
            return;
        }

        var tgid = selectedRow.find('td').eq(0).text();
        var testGroupName = selectedRow.find('td').eq(1).text();

        $('#test_name').text(testGroupName); 
        $('#test_ID').val(tgid); 
        

        $('#test_para_record_tbl').empty();

        $.ajax({
            url: 'getTestParametersByTGID', // Route path
            type: 'GET',
            data: {
                tgid: tgid,
                testGroupName:testGroupName
            },
            success: function (data) {
                if (data.length > 0) {
                    $.each(data, function (index, item) {
                        var row = '<tr>' +
                            '<td>' + item.orderno + ' </td>' +
                            '<td>' + item.reportname + ' </td>' +
                            '</tr>';
                        $('#test_para_record_tbl').append(row);
                    });
                } else {
                    $('#test_para_record_tbl').append('<tr><td>No parameters found.</td></tr>');
                }

                $('#barCodeModal').css('display', 'block');
            },
            error: function () {
                alert("Error loading parameters.");
            }
        });
    }

    // Table Row selection function
    $(document).on('click', '#test_param_record_tbl tr', function (event) {
        event.stopPropagation();
        $('#test_param_record_tbl tr').removeClass('selected-row');
        $(this).addClass('selected-row');
    });


    // parameeter wise Barcode Print process
    function parameeterBarcodePrint() {
        var date = $('#patientDate').val();
        var sno = $('#sampleNo').val();
        // var inv_balance = $('#due').val();

        var selectedRow = "";
        var tgid = $('#test_ID').val();
        var testGroupName = "";
        var orderNo = "";
        

        

            selectedRow = $('#test_param_record_tbl').find('tr.selected-row');

            if (selectedRow.length === 0) {
                alert("Please select a test!");
                return;
            }

            orderNo = selectedRow.find('td').eq(0).text();
            testGroupName = selectedRow.find('td').eq(1).text();
        

        


        var win = window.open("printParameterBarcode/" + sno + "&" + date+ "&" + tgid + "&" + testGroupName + "&" + orderNo, '_blank');

        setTimeout(function () {
            win.print();
        }, 5000);


        setTimeout(function () {
            win.close();
            // resetPage();
        }, 8000);

    }


    // Remove Barcode status update prosess


    function removeBarcode() {
        var selectedRow = $('#Branch_record_tbl').find('tr.selected-row');

        if (selectedRow.length === 0) {
            alert("Please select a test!");
            return;
        }

        // Assuming sampleNo and date are in specific columns of the selected row
        var date = $('#patientDate').val();
        var sno = $('#sampleNo').val();

        if (!sno || !date) {
            alert("Sample number or date is missing!");
            return;
        }

        // AJAX request to update status
        $.ajax({
            url: '/remove-barcode', // Route to handle the update
            type: 'POST',
            data: {
                sampleNo: sno,
                date: date
            
            },
            success: function(response) {
                if (response.success) {
                    alert("Barcode status updated to 'pending' successfully!");
                    // Optionally, update the UI (e.g., change status column text)
                    // selectedRow.find('td.status-column').text('pending'); // Adjust if you have a status column
                } else {
                    alert("Failed to update status: " + response.message);
                }
            },
            error: function(xhr) {
                alert("An error occurred: " + xhr.responseText);
            }
        });
    }



function patientConfirmModal() {
    // Show the modal
    document.getElementById('patientConfirmModal').style.display = 'block';

    // Collect patient details
    const initial = document.getElementById('initial').value;
    const fname = document.getElementById('fname').value;
    const lname = document.getElementById('lname').value;
    const fullName = `${initial} ${fname} ${lname}`;
    const years = document.getElementById('years').value;
    const months = document.getElementById('months').value;
    const days = document.getElementById('days').value;
    const dob = document.getElementById('dob').value;
    const gender = document.getElementById('male').checked ? "Male" : document.getElementById('female').checked ? "Female" : "";
    const nic = document.getElementById('nic').value;
    const address = document.getElementById('address').value;
    const refName = document.getElementById('refDropdown').value;
    const refCode = document.getElementById('refcode').value;

    // Fill patient detail section
    const detailHTML = `
        <strong>Name:</strong> ${fullName}<br>
        <strong>Age:</strong> ${years} Years, ${months} Months, ${days} Days<br>
        <strong>DOB:</strong> ${dob}<br>
        <strong>Gender:</strong> ${gender}<br>
        <strong>NIC:</strong> ${nic}<br>
        <strong>Address:</strong> ${address}<br>
        <strong>Referred By:</strong> ${refCode} - ${refName}<br>
    `;
    document.getElementById('patientDetailsPreview').innerHTML = detailHTML;

    // Delay to ensure table is fully loaded
    setTimeout(() => {
        const testTableBody = document.getElementById('Branch_record_tbl');
        const confirmTableBody = document.getElementById('test_para_record_tbl');
        confirmTableBody.innerHTML = ''; // clear old data

        const rows = testTableBody.querySelectorAll('tr');
        console.log("Loaded rows:", rows.length); // Debug log

        rows.forEach(row => {
            const cols = row.querySelectorAll('td');
            if (cols.length >= 3) {
                const testId = cols[0].innerText.trim();
                const testName = cols[1].innerText.trim();
                const price = cols[2].innerText.trim();

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${testId}</td>
                    <td>${testName}</td>
                    <td>${price}</td>
                `;
                confirmTableBody.appendChild(tr);
            }
        });
    }, 100); // Wait 100ms to allow dynamic row population
}


function closepatientConfirmModal() {
    document.getElementById('patientConfirmModal').style.display = 'none';
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
        border: none;
        border-radius: 15px;
        background-color: white;
        max-height: 150px;
        overflow-y: auto;
        position: absolute;
        z-index: 1000;
        width: 300px;
        margin-top: 150px;
        margin-left: 95px;


    }
    .autocomplete-suggestions2
    {
         border: none;
        border-radius: 15px;
        background-color: white;
        max-height: 150px;
        overflow-y: auto;
        position: absolute;
        z-index: 1000;
        width: 300px;
        margin-top: 120px;
        margin-left: 185px;


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


    
    .bcodemodal {
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

    .bcodemodal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 20%;
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

    .barcode-label {
        width: 300px;
        font-family: Arial, sans-serif;
        font-size: 14px;
        text-align: center;
        margin: 20px auto;
        display: none;
    }

    .barcode-top {
        font-weight: bold;

        margin-bottom: 4px;
    }

    .barcode-info {
        margin-top: 4px;
        text-align: left;
        padding-left: 5px; /* slight padding for neat look */
    }

    .barcode-info div {
        margin: 2px 0;
    }

    .barcode-footer {
        display: flex;
        justify-content: space-between;
        margin-top: 2px;
        font-size: 13px;
        padding-right: 5px;
    }


    /*Added by @SAM**********************************/
    
    html, body {
        margin: 0;
        padding: 0;
        width: 100vw;
        height: 100vh;
        /*overflow: hidden;  Optional: prevent scrolling */
      }
      
      .mainScreen{
          width: 95%;
          vertical-align: top;
      }

    .pregHeaderraw{
        width: 100%;
        background-color: lightskyblue;
        padding-left: 5px;
        padding-right: 5px;
        border-radius: 5px;
    }

    .pregHeadercol{
        width: 50%
    }

    .pregHeadercol2{
        width: 50%
    }


    .pregHeadercol input[type=text], button{
        width: 110px;
    }

    .pregHeadercol select{
        width: 150px;
    }

    .pregHeadercol2 input[type=text], input[type=date] , button{
        width: 90px;
        margin: 0;
    }

    .pregHeadercol2 select{
        width: 90px;
        margin: 0;
    }

    .p_suggest{
        position: absolute;
        background: #fff;
        border: 1px solid #ccc;
        max-height: 150px;
        overflow-y: auto;
        width: 510px;
        z-index: 1100;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        display:none;
        top: 200px;
        left: 50px;
    }

    .form_label_sm{
        width: 100px;
    }
    
    .form_label_sm2{
        /*width: 60px;*/
    }
    
    .tblbdy td{
        border-bottom: 1px solid black;
    }

    .delbtn{
        width:30px;
    }
    
    .barcode_panal input{
        margin: 5px;
    }

    #savebtn:disabled {
        background-color: #ccc;
        color: #666;
        cursor: not-allowed;
        border: 1px solid #999;
    
    }

    #print_invoicebtn:disabled {
        background-color: #ccc;
        color: #666;
        cursor: not-allowed;
        border: 1px solid #999;
    
    }

    #print_barcode {
        color: rgb(45, 194, 181);
        width: 90%;
        background-color: transparent;
        border: 2px solid rgb(45, 194, 181);
        transition: all 0.3s ease;
    }

    #print_barcode:hover {
        background-color: rgb(45, 194, 181);
        color: white;
        cursor: pointer;
    }

    #group_barcode {
        color: rgb(45, 194, 181);
        width: 90%;
        background-color: transparent;
        border: 2px solid rgb(45, 194, 181);
        transition: all 0.3s ease;
    }

    #group_barcode:hover {
        background-color: rgb(45, 194, 181);
        color: white;
        cursor: pointer;
    }

       #test_wise_bcode {
        color: rgb(45, 194, 181);
        width: 90%;
        background-color: transparent;
        border: 2px solid rgb(45, 194, 181);
        transition: all 0.3s ease;
    }

    #test_wise_bcode:hover {
        background-color: rgb(45, 194, 181);
        color: white;
        cursor: pointer;
    }

    #remove_barcode {
        color: rgb(45, 194, 181);
        width: 90%;
        background-color: transparent;
        border: 2px solid rgb(45, 194, 181);
        transition: all 0.3s ease;
    }

    #remove_barcode:hover {
        background-color: rgb(45, 194, 181);
        color: white;
        cursor: pointer;
    }

     /* #make_priority {
        color: #7230ec;
        width: 90%;
        background-color: transparent;
        border: 2px solid #7230ec;
        transition: all 0.3s ease;
    } */

    #make_priority:hover {
        background-color: #b1f1ee;
        /* color: white; */
        cursor: pointer;
    }



    #test_param_record_tbl tr {
        cursor: pointer;
    }

    .patientConfirmModal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.4);
    }


    .patientConfirmModal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 20px;
        border-radius: 10px;
        width: 80%;
        max-width: 900px;
    }

     /* Initially hide all barcode buttons */
        #print_barcode,
        #group_barcode,
        #test_wise_bcode,
        #remove_barcode,
        #rep_chkboxlbl,
        #rep_chkbox {
            display: none;
        }

          /* Initially hide all reportnig check boxes */
        #smstd,
        #emailtd,
        #whatsapptd{
            display: none;
        }

 

      
          #print_barcode {
            padding: 8px 16px;
            margin: 4px;
            background-color: #af288e;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }
        #group_barcode {
            padding: 8px 16px;
            margin: 4px;
            background-color: #af288e;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }
          #test_wise_bcode {
            padding: 8px 16px;
            margin: 4px;
            background-color: #af288e;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }
          #remove_barcode {
            padding: 8px 16px;
            margin: 4px;
            background-color: #af288e;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

       

       
        
    
</style>

@stop

@section('body')



<div class='mainScreen'>
    <!--<div class="card" style="height: 1250px; margin-top: 20px; background-color:rgb(222, 222, 223);">-->
    <!--<div class="card-body">-->
        <div class="pregHeaderraw">

            <table width="100%" valign="top">
                <tr>
                        <td class="pregHeadercol" valign='top'>


                            <table width="100%" >
                                <tr>
                                    <td>
                                        <label class='form_label_sm'>Sample No</label>

                                    </td>

                                    <td>

                                        <input type="text" name="sampleNo" class="input-text" id="sampleNo" style="font-size: 18px; font-weight: bold; height: 30px; color: green;" disabled>

                                        <input type="checkbox" name="edit" id="edit" value="1" style="margin:0; padding: 0;"> Edit 

                                    </td>

                                    <td >
                                        <label style="width: 100px;  ">Center</label>
                                    </td>

                                    <td>

                                        <select name="labbranch" class="input-text" id="labBranchDropdown" onchange="loadcurrentSampleNo();
                                                    load_test();">
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
                                        <input type="checkbox" name="lock_branch" id="lock_branch" value="1" style="margin:0">
                                        Lock

                                    </td>
                                </tr>

                            

                                <tr>
                                    <td>

                                        <label class='form_label_sm2'>T.P.NO</label>

                                    </td>

                                    <td>
                                        <input type="hidden" id="user_Uid">
                                        <input type="text" id="Ser_tpno" class="input-text" oninput="searchUserRecords()" maxlength="20" autocomplete="off" placeholder="Enter TP Number" >
                                        
                                        <input type="checkbox" name="any_filter" id="any_filter" value="1" style="margin:0"> Any

                                        <div id="tpno_suggestions"
                                            class="p_suggest">

                                        </div>


                                    </td>

                                    <td>
                                        <label style="">Source</label>
                                    </td>

                                    <td>
                                        <select type="text" name="source" class="input-text" id="source" >
                                            <option value="1">Walking</option>
                                            <option value="2">Centers</option>
                                        </select>
                                    </td>
                                </tr>

                            </table>



                        </td>

                        <td class="pregHeadercol2">

                            <table>
                                    <tr>
                                        <td>

                                            Bill
                                        </td>

                                        <td>

                                            <input type="date" name="ser_date" class="input-text" id="ser_date" style="">



                                        </td>

                                        <td>
                                            <label class="form_label_sm">SampleNo</label>
                                            


                                        </td>
                                        
                                        <td>
                                            <input type="text" name="ser_sampleno" class="input-text" id="ser_sampleno" style="">
                                            <input type="checkbox" name="ignore_date" class="ignore_date" value="1">
                                            <label><b>Skip Date</b></label>
                                        </td>

                                        <td >
                                            <input type="button"  class="btn" id="ser_btn" value="Search" onclick="view_search_patient()" style="float:left; margin: 0; padding: 5; width: 80px">
                                        </td>

                                    </tr>

                                    <tr>
                                        <td>
                                            IID
                                        </td>
                                        <td>
                                            <input type="text" name="invoiceId" class="input-text" id="invoiceId" readonly>
                                        </td>

                                        <td>
                                            <label class="form_label_sm">Inv. Date</label> 
                                            
                                        </td>

                                        <td>
                                            <input type="text" name="patientDate" class="input-text" id="patientDate" readonly>
                                        </td>
                                        <td>
                                            <input type="button" class="btn" id="backBtn" value="Back" onclick="loadPatient('back')" style="float:left; margin: 0; padding: 0; width: 60px">
                                            <input type="button" class="btn" id="frontBtn" value="Front" onclick="loadPatient('front')" style="float:right; margin: 0; padding: 0; width: 60px">
                                        </td>
                                        
                                    </tr>


                                    <tr>
                                        <td></td>
                                        <td>


                                        </td>
                                        <td>
                                            
                                        </td>
                                        <td>
                                            

                                        </td>
                                    </tr>

                            </table>
                        </td>
                </tr>
            </table>
            
        </div>

        <!-- --------------*********************************************************************************-------------------->
        
        <table valign="top">
            <tr>
                                    <!--Left Side -->
                <td width='50%' valign = "top">
                    
                        
                    <div style="padding: 10px; border: 2px #8e7ef7 solid; margin-right: 5px; border-radius: 10px;  ">

                        <div style="display: flex; align-items: center;  ">
                            <label class='form_label_sm'>Patient Name</label>
                            <select type="text" name="initial" class="input-text" id="initial" style="width: 70px;" >
                                <option value="Mr">Mr</option>
                                <option value="Mrs">Mrs</option>
                                <option value="Miss">Miss</option>
                                <option value="Dr">Dr</option>
                                <option value="Hons">Hons</option>
                            </select>
                            <input type="text" name=" fname" class="input-text" id="fname" style="width: 180px" placeholder="First Name"autocomplete="off">
                            <input type="text" name=" lname" class="input-text" id="lname" style="width: 180px" placeholder="Last Name"autocomplete="off">
                            <input type="checkbox" name="patient_details_edit" id="patient_details_edit" class="patient_details_edit" value="1">
                        </div>

                        <div style="display: flex; align-items: center; margin-top: 10px; ">
                            <label class='form_label_sm'>Age</label>
                            
                            <input type="number" name=" years" class="input-text" id="years" style="width: 60px;margin-right:15px" placeholder="Years">
                            
                            <input type="number" name=" months" class="input-text" id="months" style="width: 60px;margin-right:15px" placeholder="Months">
                            
                            <input type="number" name=" days" class="input-text" id="days" style="width: 60px" placeholder="Days">
                            
                            <label style="width: 50px;  margin-left: 10px ">DOB</label>
                            <input type="date" name="dob" class="input-text" id="dob" style="width: 112px">
                        </div>
                        <div style="display: flex; align-items: center; margin-top: 10px;">
                            <label class='form_label_sm'>Gender</label>
                            <label><input type="radio" id="male" name="gender" value="1"> Male</label>
                            <label><input type="radio" id="female" name="gender" value="2"> Female</label>
                            
                            <label style="margin-left:20px; margin-right: 5px;">NIC NO</label>
                            <input type="text" name=" nic" class="input-text" id="nic" style="width: 230px">
                            
                            
                        </div>
                        <div style="display: flex; align-items: center; margin-top: 5px;">
                            
                        </div>
                        <div style="display: flex; align-items: center; margin-top: 5px;">
                            <label class='form_label_sm'>Address</label>
                            <input type="text" name="address" class="input-text" id="address" style="width: 450px"autocomplete="off">
                        </div>
                        <div style="display: flex; align-items: center; margin-top: 0px;">
                            <label class='form_label_sm'>Ref.By</label>

                            <input type="text" name="refcode" class="input-text" id="refcode" style="width: 60px" 
                                oninput="searchRefferenceCode()" placeholder="Ref. Code" autocomplete="off">
                            <div id="refcode_suggestions" class="autocomplete-suggestions"></div>

                            <input type="hidden" name="ref" id="ref">
                            
                           <input type="text" name="refDropdown" id="refDropdown" class="input-text" style="width: 325px; height: 27px;" 
                                placeholder="Referred By Name" oninput="searchRefName()" autocomplete="off">
                            <div id="refname_suggestions" class="autocomplete-suggestions2"></div>


                            
                            <input type="button" style="color:green" class="btn" id="addref" style='margin:0px;' value="+" onclick="window.location.href ='{{ url('/doc-reference') }}';">
                        </div>
                        
                        <hr style=" background-color: rgb(19, 153, 211); height: 2px; border: none; ">
                        <!-- <div style="display: flex; align-items: center; margin-top: 5px;">
                            <label style="width: 150px;  "><b>Test Name</b>:</label>
                            <input type="text" name="testname" class="input-text" id="testname" list="testlist" oninput="setDataToTable(this.value)" style="width: 350px">
                            <datalist id="testlist"></datalist>
                            <input type="checkbox" name="byname" id="byname" class="ref_chkbox" value="1">
                            <label style="width: 70px;   "><b>By Name</b></label>
                        </div> -->

                        <div style="display: flex; align-items: center; margin-top: 5px;">
                            <label class='form_label_sm'><b>Test Name</b></label>
                            <!-- Using onchange event here to trigger the function when a valid value is selected -->
                            <input type="text" name="testname" class="input-text" id="testname" list="testlist" onchange="setDataToTable(this.value)" style="width: 100px"autocomplete="off">
                            <datalist id="testlist">

                            </datalist>
                            <label style="width: 40px; margin-left: 15px"><b>Type</b></label>
                            <select type="text" name="type" class="input-text" id="type" style="width: 70px; height: 30px">
                                <option value="In">In</option>
                                <option value="Out">Out</option>
                            </select>
                            <label style="width: 100px;  margin-left: 15px "><b>Fast Time</b>:</label>
                            <input type="text" name=" fast_time" class="input-text" id="fast_time" value="0" style="width: 40px">
                            <input type="checkbox" name="fastcheck" id="fastcheck" class="ref_chkbox" value="1">
                            <!-- <input type="checkbox" name="byname" id="byname" class="ref_chkbox" value="1">
                            <label style="width: 70px;   "><b>By Name</b></label> -->
                        </div>

                        <div style="display: flex; align-items: center; margin-top: 5px;">
                            <label style="width: 100px;  "><b>Package</b></label>
                            <input type="text" name="packageDropdown" class="input-text" id="packageDropdown"
                                list="package_test_list"
                                onchange="load_package_tests()"
                                style="width: 400px" autocomplete="off">

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

                            <!-- <label style="width: 120px;  "><b>Fast Time</b>:</label>
                            <input type="text" name=" fast_time" class="input-text" id="fast_time" value="0" style="width: 40px">
                            <input type="checkbox" name="fastcheck" id="fastcheck" class="ref_chkbox" value="1"> -->
                        </div>
                    
                    </div>
                    
                    <div style="padding: 10px; border: 2px #8e7ef7 solid; margin-right: 5px; margin-top: 2px; border-radius: 10px; background-color: #dce9d8">


                        <div style="display: flex; align-items: center; margin-top: 0px;">
                            <label style="width: 125px;  ">Total Amount:</label>
                            <label style="width: 30px;  ">Rs: </label>
                            <label id="total_amount" style=" padding-right: 50px; font-size: large; color: rgb(17, 17, 17); font-family: 'Times New Roman', Times, serif; font-weight: bolder;">000,000.00</label>

                            <br>

                            <label style="width: 80px;   ">Discount:</label>
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
                                    <option value="<?= $discountId . ":" . $discountValue ?>"><?= $discountName ?> (<?= $discountValue ?>%)</option>
                                    <?php
                                }
                                ?>
                            </select>

                        </div>

                        <div style="display: flex; align-items: center; margin-top: 10px;">
                            <label style="width: 140px;  "><b>Grand Total:</b></label>
                            <label style="width: 20px;  margin-left: 20px;">Rs: </label>
                            <label style="padding-left: 10px; padding-right: 45px; color: rgb(17, 17, 17); font-size: large; font-family: 'Times New Roman', Times, serif; font-weight: bolder;" id="grand_total">000,000.00</label>

                            <label><input type="radio" name="payment_method" id="cash" value="1" checked onchange="togglePaidField();"> Cash</label>
                            <label><input type="radio" name="payment_method" id="card" value="2"onchange="togglePaidField();"> Card</label>
                            <label><input type="radio" name="payment_method" id="credit" value="credit"onchange="togglePaidField();"> Credit</label>
                            <label><input type="radio" name="payment_method" id="cheque" value="3"onchange="togglePaidField();"> Cheque</label>
                            <label><input type="radio" name="payment_method" id="voucher" value="6" onchange="togglePaidField();"> Voucher</label>
                            <label><input type="radio" name="payment_method" id="split" value="5" onchange="togglePaidField();"> Split</label>
                        </div>

                        <div style="display: flex; align-items: center; margin-top: 10px;">
                            <label style="width: 125px;  ">Payment:</label>
                            <label style="width: 30px;  ">Rs: </label>
                            <input type="number" name="paid" class="input-text" id="paid" style="width: 97px;" oninput="calculateDue()">

                            <label style="width: 20px;  "></label>
                            <label style="width: 60px;  ">Due:</label>
                            <label style="width: 40px;  ">Rs:</label>
                            <label style="color: rgb(17, 17, 17); font-size: large; font-family: 'Times New Roman', Times, serif; font-weight: bolder;" id="due">000,000.00</label>
                        </div>
                        <div style="display: flex; align-items: center;margin-top: 15px;" id="vaucher_div">
                            <label style="width: 125px;  ">Voucher No:</label>
                            <label style="width: 30px;  "> </label>
                            <input type="number" name="Voucher_No" class="input-text" id="Voucher_No" style="width: 100px;" oninput="">

                            <label style="width: 80px;   margin-left:20px;">Amount</label>
                            <label style="width: 30px;  ">Rs: </label>
                            <input type="number" name="vaucher_amount" class="input-text" id="vaucher_amount" style="width: 85px;" oninput="">

                        </div>
                        <div style="display: flex; align-items: center;margin-top: 15px;" id="split_div">

                            <label style="width: 125px;  ">Cash Amount</label>
                            <label style="width: 30px;  ">Rs: </label>
                            <input type="number" name="cash_amount" class="input-text" id="split_cash_amount" style="width: 97px;" oninput="">

                            <label style="width: 100px;  margin-left:20px;">Card Amount</label>
                            <label style="width: 30px;   margin-left: 5px;">Rs: </label>
                            <input type="number" name="card_amount" class="input-text" id="split_card_amount" style="width: 80px;" oninput="">

                        </div>
                        <div style="display: flex; margin-top: 5px;">
                            <input type="button" style="color:black; width: 180px; height: 30px; margin-left: 0px;" class="btn" id="cashDrower" value="Cash Drawer">
                            <input type="button" style="color:black; width: 210px; height: 30px; margin-left: 0px;" onclick="viewSelectedInvoicePayments()" class="btn" id="update_payment" value="Update Payment">
                        </div>


                    </div>
            
                    
                    
                </td>
                                    <!-- Right Side -->
                <td valign = "top">
                    
                    <div style="flex: 1; padding-left: 10px; padding-right: 10px; border: 2px #8e7ef7 solid; border-radius: 10px; width: 95%">
                        
                        <div id="testCodeButtons" style="display: flex; align-items: center; margin-top: 5px; overflow-x: scroll; width: 600px;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="padding: 5px;">
                                        <button style="height: 35px; padding: 10px; font-weight: bold; border-radius: 0%; background-color: #4b9bf0; color: #f8d7da"></button>
                                    </td>
                                    <td style="padding: 5px;">
                                        <button style="height: 35px; padding: 10px; font-weight: bold; border-radius: 0%; background-color: #4b9bf0; color: #f8d7da"></button>   
                                    </td>
                                    <td style="padding: 5px;">
                                        <button style="height: 35px; padding: 10px; font-weight: bold; border-radius: 0%; background-color: #4b9bf0; color: #f8d7da"></button>
                                    </td>
                                    <td style="padding: 5px;">
                                        <button style=" height: 35px; padding: 10px; font-weight: bold; border-radius: 0%; background-color: #4b9bf0; color: #f8d7da"></button>
                                    </td>
                                    <td style="padding: 5px;">
                                        <button style=" height: 35px; padding: 10px; font-weight: bold; border-radius: 0%; background-color: #4b9bf0; color: #f8d7da"></button>
                                    </td>
                                    <td style="padding: 5px;">
                                        <button style=" height: 35px; padding: 10px; font-weight: bold; border-radius: 0%; background-color: #4b9bf0; color: #f8d7da"></button>
                                    </td>
                                
                                    <td style="padding: 5px;">
                                        <button style=" height: 35px; padding: 10px; font-weight: bold; border-radius: 0%; background-color: #4b9bf0; color: #f8d7da"></button>
                                    </td>
                                    <td style="padding: 5px;">
                                        <button style=" height: 35px;padding: 10px; font-weight: bold; border-radius: 0%; background-color: #4b9bf0; color: #f8d7da"></button>
                                    </td>
                                    <td style="padding: 5px;">
                                        <button style="height: 35px; padding: 10px; font-weight: bold; border-radius: 0%; background-color: #4b9bf0; color: #f8d7da"></button>
                                    </td>
                                    <td style="padding: 5px;">
                                        <button style=" height: 35px; padding: 10px; font-weight: bold; border-radius: 0%; background-color: #4b9bf0; color: #f8d7da"></button>
                                    </td>
                                    <td style="padding: 5px;">
                                        <button style="height: 35px; padding: 10px; font-weight: bold; border-radius: 0%; background-color: #4b9bf0; color: #f8d7da"></button>
                                    </td>
                                    <td style="padding: 5px;">
                                        <button style=" height: 35px; padding: 10px; font-weight: bold; border-radius: 0%; background-color: #4b9bf0; color: #f8d7da"></button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <div style="height: 200px; overflow-y: scroll">
                            <table style="font-family: Futura, 'Trebuchet MS', Arial, sans-serif; font-size: 10pt;" id="test_tbl" width="100%" border="0" cellspacing="2" cellpadding="0">
                                <thead>
                                    <tr class="viewTHead" style="height:30px;">
                                        <td align="left" class="fieldText" style="width: 20px;">ID</td>
                                        <td align="left" class="fieldText" style="width: 250px; text-align:left">Test</td>
                                        <td align="left" class="fieldText" style="width: 20px;">Price</td>
                                        <td align="right" class="fieldText" style="width: 10px;">TAT</td>
                                        <td align="center" class="fieldText" style="width: 10px;">Fast.Time</td>
                                        <td align="center" class="fieldText" style="width: 10px;">B.Code</td>
                                        <td align="center" class="fieldText" style="width: 10px;">Priority</td>
                                        <td align="center" class="fieldText" style="width: 10px;">Type</td>
                                        <td align="center" class="fieldText" style="width: 10px;">Action</td>
                                    </tr>
                                </thead>
                                <tbody id="Branch_record_tbl" class="tblbdy">
                                    <!-- Dynamic rows will be inserted here via AJAX -->
                                </tbody>
                            </table>
                        </div>
                        
                        
                        <table width="100%">
                            
                            <tr>
                                <td width="50%" valign="top">
                                    
                                      <div style="display: flex; gap: 10px;">
                                        <input type="button" class="btn" id="make_priority" value="Make Priority" onclick="">
                                        
                                     </div>
                                                                    
                                    <input type="text" name=" inv_remark" class="input-text" id="inv_remark" style="width: 92%; margin-top: 5px;" placeholder="Invoice Remark">
                                    
                                    <div style="display: flex; align-items: center;margin-top: 10px; font-size: 12px; ">
                                        <label style="width: 230px;  "><i>Report Collection Method</i></label>
                                    </div>

                                    <table style="margin-top: 5px; font-size: 10pt;" cellspacing="0" width="100%">
                                    <tr>
                                        <td width="25%">
                                            <input type="checkbox" name="hard_copy" id="hard_copy" value="Hard Copy" checked ><br/>Hard Copy
                                        </td>

                                        <td width="25%" id="smstd">
                                            
                                            <input type="checkbox" name="sms" id="sms" value="SMS" style=""><br/>SMS
                                        </td>

                                        <td width="25%" id="emailtd">
                                            
                                        <input type="checkbox" name="email" id="email" value="Email" style=""><br/>Email
                                        </td>

                                        <td width="25%" id="whatsapptd"> 
                                            
                                        <input type="checkbox" name="whatsapp" id="whatsapp" value="WhatsApp" style=""><br/>WhatsApp
                                        </td>

                                    </tr>
                                    </table>
                                    
                                    <table style="margin-top: 10px; font-size: 10pt;" cellspacing="0" width="100%">
                                    
                                    <tr style="margin-top:10px;">
                                        <td width="25%">
                                            
                                        <input type="checkbox" name="package_invoice" id="package_invoice" value="package_invoice" style=""><br/>Package Invoice
                                        </td>

                                        <td width="25%">
                                            
                                        <input type="checkbox" name="print_bill" id="print_bill" class="ref_chkbox" value="1" style=""><br/>Print Bill
                                        </td>

                                        <td width="25%">
                                            
                                        <input type="checkbox" name="claim_bill" id="claim_bill" class="ref_chkbox" value="1" style=""><br/>Claim Bill
                                        </td>

                                        <td width="25%">
                                            
                                        <input type="checkbox" name="two_copies" id="two_copies" class="ref_chkbox" value="1" style=""><br/>2 Copies
                                        </td>

                                        
                                    </tr>

                                </table>
                                    
                                </td>
                                
                                <td valign="top" width="0.5%" ><div style="background-color: #4b9bf0; width: 3px; height: 180px; margin-left: 10px;"></div></td>
                                
                                
                            <td width="49%" valign="top" align="right"> 
                            <label style="width: 140px; font-size: 10pt;" id="rep_chkboxlbl"><b>Repeat Samples</b></label>
                            <input type="checkbox" name="rep_chkbox" id="rep_chkbox" class="ref_chkbox" value="1" />
                            <input type="button" class="btn" id="print_barcode" value="Print Barcode"  onclick="barcodePrint(false);" />
                            <input type="button" class="btn" id="group_barcode" value="Group Barcodes" onclick="barcodePrint(true);" />
                            <input type="button" class="btn" id="test_wise_bcode" value="Test Wise Bcode" onclick="openBcodeModal();" />
                            <input type="button" class="btn" id="remove_barcode" value="Remove Barcode" onclick="removeBarcode();" />
                            <input
                                type="button"
                                style="color:rgb(13, 134, 59); width: 190px; height: 40px; margin: 0px;"
                                class="btn"
                                id="save_Model"
                                value="Save & Confirm"
                                onclick="patientConfirmModal()"
                            />
                        </td>

                            
                        </table>

                        
                

                        <!-- Barcode div -->
                        <div class="barcode-label" id="print_bcode" >
                            <div class="barcode-top" id="bcode_sno"></div>
                            <canvas id="barcodeCanvas"></canvas>
                            <div class="barcode-info">
                                <div><strong id="bcode_p_name"></strong></div>
                                <div id="bcode_testname"></div>
                                <div class="barcode-footer">
                                    <span id="barcode_date"></span>
                                    <span id="barcode_time"></span>
                                    <span id="barcode_p_age"></span>
                                </div>
                            </div>
                        </div>

                        <iframe id="print-frame" style="display:none;"></iframe>


                        <hr style=" background-color: rgb(19, 153, 211); height: 3px; border: none;">
                        
                        <table width="100%">
                                
                                <tr>
                                    <td>
                                        <input type="button" style="color:rgb(10, 113, 158); width: 190px; height: 40px; margin: 0px;" class="btn" id="view_invoicebtn" value="View Invoice" onclick="goToViewInvoice()">
                                    </td>
                                    
                                   <td class="updatebtntd" style="display: none;"> <!-- Ensure this is initially hidden -->
                                         <input type="button" style="color:rgb(245, 168, 34); width: 190px; height: 40px; margin: 0px;" class="btn" id="updatebtn" value="Update Details" onclick="updatePatientDetails()">
                                  </td>

                                    
                                    <td>
                                    <input type="button" style="color:rgb(10, 113, 158); width: 190px; height: 40px; margin: 0px;" class="btn" id="getlastpatientbtn" value="Get Last patient" onclick="loadLastPatient()"> 
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td>
                                        <input type="button" style="color:gray; width: 190px; height: 40px; height: 40px; margin: 0px;" class="btn" id="resetbtn" value="Reset" onclick="resetPage()">
                                    </td>
                                    
                                    <td>
                                        <input type="button" style="color:rgb(10, 113, 158); width: 190px; height: 40px; margin: 0px;" class="btn" id="print_invoicebtn" value="Print Invoice " onclick="printInvoice()" disabled>
                                    </td>
                                    <td>
                                      


                                    </td>
                                    
                                    <td>
                                        
                                    </td>
                                </tr>
                                
                            </table>

                    </div>


                        <!-- Button to open the modal -->
                        <!--    <input type="button" style="color:black; width: 210px; height: 50px;" 
                            onclick="openModal()" class="btn" id="update_payment" value="Open Modal">-->
                        <!-- The Modal -->
                    <div id="myModal" class="modal">
                        <!-- Modal content -->
                        <div class="modal-content">
                            <span class="close" onclick="closeModal()">&times;</span>
                            <h1>Invoice Payments</h1>
                            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                <label for="inv_id" style="font-size: 14px; min-width: 90px;"><b>Inv ID:</b></label>
                                <label id="inv_id" style="  width: 190px;"></label>

                                <label for="inv_pname" style="font-size: 14px; min-width: 90px;">Name:</label>
                                <label id="pname" style=" width: 290px;"></label>

                                <label for="inv_total" style="font-size: 14px; min-width: 90px;"><b>Total Rs:</b></label>
                                <label id="inv_total" style=" width: 90px;"></label>

                                <label for="inv_paid" style="font-size: 14px; min-width: 160px;"><b>Total Paid Rs:</b></label>
                                <label id="inv_paid" style="  width: 90px;"></label>

                                <label for="inv_days" style="font-size: 14px; min-width: 90px;"><b>Days:</b></label>
                                <label id="inv_days" style=" width: 90px;"></label>
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
                                        <label id="due_amount" style=" color: red;"></label>
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
                                <div style="flex: 1; ">
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
                    

                    
                    
                           <!-- Test Wice barcode Modal -->
                    <div id="barCodeModal" class="bcodemodal">
                        <!-- Modal content -->
                        <div class="bcodemodal-content">
                            <span class="close" onclick="closeBcodeModal()">&times;</span>
                            <h1>Test Wise Barcode </h1>
                            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                <label for="test_name" style="font-size: 14px; min-width: 90px;"><b>Test Name:</b></label>
                                <label id="test_name" style="  width: 190px;"></label>
                                <input type="text" id="test_ID" hidden> 
                            </div>
                             <div class="pageTableScope" style="display: block; height: auto; margin-top: 10px; width: 100%;">
                                <!-- Left Side: Table -->
                                <div style="flex: 0 0 50%; padding-left: 10px;">
                                     <table style="font-family: Futura, 'Trebuchet MS', Arial, sans-serif; font-size: 13pt;" id="test_param_record_tbl" width="100%" border="0" cellspacing="2" cellpadding="0">
                                        <tbody>
                                            <tr>
                                                <td valign="top">
                                                    <table border="1" style="border-color: #ffffff;" cellpadding="0" cellspacing="0" class="TableWithBorder" width="100%">
                                                        <thead>
                                                            <tr class="viewTHead">
                                                                <td width="15%" class="fieldText" align="left">Order No</td>
                                                                <td width="15%" class="fieldText" align="left">Parameter List</td>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="test_para_record_tbl">
                                                            <!-- Dynamic content goes here -->
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                   
                                    <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                        <input type="button" style="flex: 0 0 80px; margin-left: 10px; color:rgb(23, 43, 179)" class="btn" id="ser_btn" value="Print Barcode" onclick = "parameeterBarcodePrint();" >
                                    </div>
                                </div>

                                
                            </div>
                        </div>
                    </div>

                    <!-- patient details confermation Modal -->

                   <div id="patientConfirmModal" class="patientConfirmModal">
                    <div class="patientConfirmModal-content">
                        <span class="close" onclick="closepatientConfirmModal()">&times;</span>
                        <h1>Patient Details Confirmation</h1>

                        <!-- Patient Details Section -->
                        <div id="patientDetailsPreview" style="margin-top: 10px; font-size: 14px;"></div>

                        <!-- Test Details Table -->
                        <h3 style="margin-top: 20px;">Selected Tests</h3>
                        <table style="font-family: Futura, 'Trebuchet MS', Arial, sans-serif; font-size: 13pt;" width="100%" border="1" cellspacing="0" cellpadding="5">
                        <thead>
                            <tr class="viewTHead">
                            <td width="15%" class="fieldText">Test ID</td>
                            <td width="50%" class="fieldText">Test Name</td>
                            <td width="20%" class="fieldText">Price</td>
                            </tr>
                        </thead>
                        <tbody id="test_para_record_tbl">
                            <!-- Test rows will be populated here -->
                        </tbody>
                        </table>

                        <div style="text-align: right; margin-top: 15px;">
                        <input type="button" style="color:green; " class="btn" id="savebtn" value="Save">
                        <input type="button" class="btn" value="Cancel" onclick="closepatientConfirmModal()" style="color:rgb(238, 49, 43)">
                        </div>
                    </div>
                    </div>


                </td>
                
            </tr>
            
        </table>
        
            
            
                            <!-- Loader  -->
            <div id="loaderOverlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(255,255,255,0.8); z-index:9999; text-align:center;">
                <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%);">
                    <img src="/images/load.gif" alt="Loading..." />
                    <p>Saving patient details...</p>
                </div>
            </div>



</div>





@stop
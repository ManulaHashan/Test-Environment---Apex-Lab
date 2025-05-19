<!-- **********~~~~~~~~~~~~~Manula Hashan's Devolopment~~~~~~~~~~~~~********** -->


<?php ?>
@extends('Templates/WiTemplate')


@section('title')
View Invices
@stop

@section('head')

<script src="{{ asset('JS/ReportCalculations.js') }}"></script>

<script>

    $(document).ready(function ()
{   
    const today = new Date().toISOString().split('T')[0];
    $('#idate').val(today);

    loadRecordToTable();

    $(document).on('click', '#inv_record_tbl tr', function () 
    {
        $('#inv_record_tbl tr').removeClass('selected');
        $(this).addClass('selected');

        var sampleNo = $(this).find('td:eq(0)').text().trim();
        var date = $(this).data('date');

        // $('#selected_sampleNo').val(sampleNo + " : " + date);

            if (sampleNo && date) {
                $.ajax({
                    type: "GET",
                    url: "getSampleTestData",
                    data: {
                        sampleNo: sampleNo,
                        date: date
                    },
                    success: function (sampleDataHtml) {

                        
                        $('#sample_record_tbl').html(sampleDataHtml);
                        
                    },
                    error: function (xhr) {
                        alert("Failed to load sample data.");
                        console.log(xhr.responseText);
                    }
                });
            }
    });
});






    // Function to load data into the table

    function formatCurrency(num) {
        num = parseFloat(num);
        if (isNaN(num)) return "0.00";
        return num.toLocaleString('en-LK', {
            style: 'currency',
            currency: 'LKR',
            minimumFractionDigits: 2
        }).replace("LKR", "").trim();
    }

    function loadRecordToTable() 
    {
        var center = $('#labbranch').val();
        var withOtherBranches = $('#with_other_branches').is(':checked') ? 1 : 0;
        var dueBillsOnly = $('#due_bills_only').is(':checked') ? 1 : 0;
        var byDate = $('#by_date').is(':checked') ? 1 : 0;
        var idate = $('#idate').val();
        var invoiceNo = $('#invoice_no').val();
        var firstName = $('#first_name').val();
        var lastName = $('#last_name').val();
        var contact = $('#contact').val();
        var patientType = $('#type').val();

        $.ajax({
            type: "GET",
            url: "getAllInvoices",
            data: {
                center: center,
                withOtherBranches: withOtherBranches,
                dueBillsOnly: dueBillsOnly,
                byDate: byDate,
                idate: idate, 
                invoiceNo: invoiceNo,
                firstName: firstName,
                lastName: lastName,
                contact: contact,
                patientType: patientType
            },
            success: function (tbl_records) {
                $('#inv_record_tbl').html(tbl_records);
              
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



    function sampleRecordToTableClear() {
    $('#sample_record_tbl').html(''); 
    }

    //*************************************************************************************************


    function viewSelectedInvoice() 
    {
        var selectedRow = $('#invdataTable tbody tr.selected');
        if (selectedRow.length === 0) {
            alert("Please select a row to view.");
            return;
        }

        var sampleNo = selectedRow.find('td:eq(0)').text(); 

        // Open the link in the same window
        window.open("patientRegistration?sampleNo=" + sampleNo + "&date=" + $('#idate').val(), "_self");
    }



    //*************************************************************************************************
    function cancelInvoice() {

        
        var selectedRow = $('#invdataTable tbody tr.selected');
        var sampleNo = selectedRow.find('td:eq(0)').text(); 
        var lpsId = selectedRow.data('lpsid'); 
        
        if (!confirm("Are you sure you want to cancel invoice for Sample No: " + sampleNo + "?")) {
            return;
        }

        $.ajax({
            type: "POST",
            url: "cancelInvoice",
            data: {
                sampleNo: sampleNo,
                lpsId: lpsId
            },
            success: function (response) {
                alert(response.message);
                loadRecordToTable(); 
                $('#sample_record_tbl').html(''); 
            },
            error: function (xhr) {
                alert("Error: " + xhr.status + " - " + xhr.statusText);
            }
        });
 
    }

    function selectToDelete(){

        var selectedRow = $('#invdataTable tbody tr.selected');
        if (selectedRow.length === 0) {
            alert("Please select a row to cancel.");
            return;
        }
        openModal();
    }

  
    $(document).ready(function() 
    {
   
        var today = new Date().toISOString().split('T')[0];
        $('#idate').val(today);
        fetchCashierBalanceData('%', today);

        $('#labuser, #idate').change(function() {
            var selectedCashier = $('#labuser').val();
            var selectedDate = $('#idate').val();
            fetchCashierBalanceData(selectedCashier, selectedDate);
        });
    });

// Fetch cashier balance data based on selected cashier and date
    function fetchCashierBalanceData() {
        var cashierId = $('#labuser').val();
        var date = $('#idate').val();

        $.ajax({
            url: 'getCashierInvoiceSummary',
            type: 'GET',
            data: { 
                cashier_id: cashierId,
                date: date
               
            },
            success: function(data) {

            
                $('#lblTotalBillCount').text(data.totalBillCount || 0);
                $('#lblTotalAmount').text(data.totalAmount || '0.00');
                $('#lblTotalExpenses').text(data.totalExpenses || '0.00');
                $('#lblTotalPaid').text(data.totalPaid || '0.00');
                $('#lblTotalDue').text(data.totalDue || '0.00');
                $('#lblCashierBalance').text(data.cashierBalance || '0.00');
            },

           
            error: function() {
                alert('Error fetching cashier balance data');
            }
        });
    }




    //*************************************************************************************************

    function openModal() {
        document.getElementById("cancelModal").style.display = "flex";
    }

    function closeModal() {
        document.getElementById("cancelModal").style.display = "none";
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

    #invdataTable table tr:hover {
    background-color: #28acbd; /* light cyan on hover */
    cursor: pointer;
    }

    #invdataTable tbody tr.selected {
        background-color: #4f8de5 !important; /* green for selected row */
    }

    .input-text::placeholder {
        color: #ffffff; 
        opacity: 2; 
    }
    

</style>
@stop

@section('body')



<div class="container">
    <div class="card" style="height: 850px; margin-top: 50px; background-color:rgb(222, 222, 223);">
        <div class="card-body" style="display: flex; max-width: 1350px; margin: auto; padding: 20px;">
            <!-- Main Content Area (70%) -->
            <div style="flex: 0 0 100%; padding-right: 20px;">
                <div style="flex: 0 0 70%; padding-right: 20px;">
                </div>
                <div style="display: flex; align-items: center; margin-top: 5px;">
                   
                    <label style="width: 70px;font-size: 18px;margin-left: 15px"><b>Center</b></label>
                    <select name="labbranch" style="width: 250px; height: 30px" class="input-text" id="labbranch" onchange="">
                        <option value="%:@" data-code="ML" data-maxno="0" data-mainlab="true">Main Lab</option>
                        <?php
                        $Result = DB::select("SELECT name, code, bid FROM labbranches WHERE Lab_lid = '" . $_SESSION['lid'] . "' ORDER BY name ASC");

                        foreach ($Result as $res) {
                            $branchName = $res->name;
                            $branchCode = $res->code;
                            $bid = $res->bid;


                            $displayText = $branchCode . " : " . $branchName;
                        ?>
                            <option value="<?= $bid . ":" . $branchCode ?>"><?= $displayText ?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <input type="checkbox" name="with_other_branches" id="with_other_branches" class="ref_chkbox" value="0">
                    <label style="font-size: 16px; margin-left: 5px;"><b>With Other Branches</b></label>
                    <label style="font-size: 16px; margin-left: 5px; width: 570px;"></label>
                    <input type="checkbox" name="due_bills_only" id="due_bills_only" class="ref_chkbox" value="0">
                    <label style="font-size: 16px; margin-left: 5px;"><b>Due Bills Only</b></label>
                </div>

                <div style="display: flex; align-items: center; margin-top: 5px;">
                   
                    <input type="checkbox" name="by_date" id="by_date" class="ref_chkbox" style="margin-bottom: 5px;" value="1" checked>
                    <label style="font-size: 14px; margin-left: 3px; width: 70px;"><b>By Date </b></label>
                    <input type="date" name="idate" class="input-text" id="idate" style="width: 100px">
                    <label style="font-size: 16px; margin-left: 5px;"><b>Invoice No </b></label>
                    <input type="text" name="invoice_no" class="input-text" id="invoice_no" style="width: 90px">
                    <label style="font-size: 16px; margin-left: 5px;"><b>First Name </b></label>
                    <input type="text" name="first_name" class="input-text" id="first_name" style="width: 90px">
                    <label style="font-size: 16px; margin-left: 5px;"><b>Last Name </b></label>
                    <input type="text" name="last_name" class="input-text" id="last_name" style="width: 90px">
                    <label style="font-size: 16px; margin-left: 5px;"><b>Contact </b></label>
                    <input type="text" name="contact" class="input-text" id="contact" style="width: 90px">
                    <label style="font-size: 16px; margin-left: 5px;"><b>Patient Type </b></label>
                    <select type="text" name="type" class="input-text" id="type" style="width: 70px; height: 30px">
                        <option value="In">In</option>
                        <option value="Out">Out</option>
                    </select>
                    <input type="button" style="flex: 0 0 80px; margin-left: 10px;" class="btn" id="ser_btn" value="Search" onclick="loadRecordToTable();sampleRecordToTableClear();">
                </div>
            </div>
        
        </div>

        <div class="pageTableScope" style="display: flex; height: 350px; margin-top: 10px; width: 100%;">
            <!-- Left Side: Table -->
          {{-- <input type="text" id="selected_sampleNo"> --}}
            <div style="flex: 1; padding-right: 10px;">
                <table style="font-family: Futura, 'Trebuchet MS', Arial, sans-serif; font-size: 13pt;" id="invdataTable" width="100%" border="0" cellspacing="2" cellpadding="0">
                    <tbody>
                        <tr>
                            <td valign="top">
                                <table border="1" style="border-color: #ffffff;" cellpadding="0" cellspacing="0" class="TableWithBorder" width="100%">
                                    <thead>
                                        <tr class="viewTHead">
                                            <td width="12%" class="fieldText" align="center">Sample No</td>
                                            <td width="18%" class="fieldText" align="center">First Name</td>
                                            <td width="18%" class="fieldText" align="center">Last Name</td>
                                            <td width="10%" class="fieldText" align="center">Status</td>
                                            <td width="8%" class="fieldText" align="center">Total Amount</td>
                                            <td width="10%" class="fieldText" align="center">Paid</td>
                                            <td width="10%" class="fieldText" align="center">Due</td>
                                            <td width="10%" class="fieldText" align="center">User</td>
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
        
            <!-- Right Side: Additional Content -->
            <div style="flex: 0 0 20%; padding-left: 10px;">
                <table style="font-family: Futura, 'Trebuchet MS', Arial, sans-serif; font-size: 13pt;" id="sampledataTable" width="100%" border="0" cellspacing="2" cellpadding="0">
                    <tbody>
                        <tr>
                            <td valign="top">
                                <table border="1" style="border-color: #ffffff;" cellpadding="0" cellspacing="0" class="TableWithBorder" width="100%">
                                    <thead>
                                        <tr class="viewTHead">
                                            <td width="12%" class="fieldText" align="center">Sample No</td>
                                            <td width="18%" class="fieldText" align="center">Test</td>
                                        </tr>
                                    </thead>
                                    <tbody id="sample_record_tbl">
                                        <!-- Dynamic content goes here -->
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div style="display: flex; gap: 10px; align-items: center;">
                   
                   <label style="font-size: 16px; margin-left: 5px; width: 90px; background-color: #f5ad39; color: #fff; text-align: center; border: none; border-radius: 5px; display: inline-block; padding: 5px 0;"><b>Pending</b></label>
                   <label style="font-size: 16px; margin-left: 5px; width: 90px; background-color: #3498db; color: white; text-align: center; border: none; border-radius: 5px; display: inline-block; padding: 5px 0;"><b>Accept</b></label>
                    <label style="font-size: 16px; margin-left: 5px; width: 90px; background-color: #27ae60; color: white; text-align: center; border: none; border-radius: 5px; display: inline-block; padding: 5px 0;"><b>Done</b></label>


                </div>
                
            </div>
        </div>


{{-- ############################################################################################################# --}}


        <div class="" style="display: flex; height: 350px; margin-top: 10px; width: 100%;">
            <!-- Left Side:  -->
            <div style="flex: 1; padding-right: 10px;">
                <div style="display: flex; align-items: center; flex-wrap: wrap; margin-bottom: 15px;">
                    <label style="width: auto; font-size: 18px; margin-left: 15px;"><b>Total Cashier Balance</b></label>
                    <label style="width: auto; font-size: 18px; margin-left: 15px;">Cashier</label>
                    <select name="labuser" style="width: 250px; height: 30px; margin-left: 15px;" class="input-text" id="labuser">
                        <option value="%">All</option>
                        <?php
                        $query = "SELECT a.uid, a.fname, a.lname 
                            FROM user a
                            INNER JOIN labUser b ON a.uid = b.user_uid
                            INNER JOIN Lab_labUser c ON b.luid = c.labUser_luid

                            WHERE c.lab_lid = '" . $_SESSION['lid'] . "' ORDER BY a.fname ASC";
                        
                        $Result = DB::select($query);
                        
                        foreach ($Result as $res) {
                            $uid = $res->uid;
                            $fullName = $res->fname . ' ' . $res->lname;
                            $displayText = $uid . " : " . $fullName;
                            echo "<option value='{$fullName}'>{$displayText}</option>";
                        }
                        ?>
                    </select>
                </div>
            
                {{-- justify-content: space-between; can add for more alignments --}}
                <div style="display: flex; width: 100%;"> 
                    <label style="font-size: 18px; margin-left: 15px; width:150px;">Total Bill Count:</label>
                    <label id="lblTotalBillCount" style="font-size: 18px; margin-left: 15px; width:150px;">0</label>
                
                    <label style="font-size: 18px; width:150px;">Total Amount Rs:</label>
                    <label id="lblTotalAmount" style="font-size: 18px; margin-left: 15px; width:150px;">00,000.00</label>
                
                    <label style="font-size: 18px; margin-right: 15px; width:160px;">Total Expences Rs:</label>
                    <label id="lblTotalExpenses" style="font-size: 18px; margin-left: 15px; width:150px;">00,000.00</label>
                </div>
                
                <div style="margin-top: 20px; display: flex; width: 100%;">
                    <label style="font-size: 18px; margin-left:15px; width:150px;">Total Paid Rs:</label>
                    <label id="lblTotalPaid" style="font-size: 18px; margin-left:15px; width:150px;">00,000.00</label>
                
                    <label style="font-size: 18px; width:150px;">Total Due Rs:</label>
                    <label id="lblTotalDue" style="font-size: 18px; margin-left: 15px; width:150px;">00,000.00</label>
                
                    <label style="font-size: 18px; margin-right: 15px; width:160px;">Cashier Balance Rs:</label>
                    <label id="lblCashierBalance" style="font-size: 18px; margin-left: 15px; width:150px;">00,000.00</label>
                </div>
                
            </div>
            
            
            
            <!-- Right Side:-->
            <div style="flex: 0 0 20%; padding-left: 10px;">
                <div id="button-container">
                    <input type="button" style="flex: 0 0 80px;width: 175px; height: 50px;" class="btn" id="ser_btn" value="View Selected Invoice" onclick="viewSelectedInvoice()">
                  </div>
                  <div id="button-container">
                    <input type="button" style="flex: 0 0 80px; width: 175px; height: 50px; color: red" class="btn" id="ser_btn" value="Cancel Invoice" onclick="selectToDelete()">
                  </div>
            </div>

            <div id="cancelModal" style="display: none; position: fixed; left: 0; top: 0; width: 100%; height: 100%; 
                        background-color: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center;">
                <div style="background-color: #fff; padding: 20px; border-radius: 12px; width: 350px; text-align: center; 
                            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);">
                    <h3 style="margin-bottom: 20px; color: #333;">Cancel Invoice</h3>

                    <label for="remark" style="font-weight: bold; color: #555;">Remark:</label><br>
                    <input type="text" id="delete_remark" 
                        style="width: 90%; padding: 8px; margin: 8px 0 15px 0; border-radius: 5px; 
                                border: 1px solid #ccc;"><br>

                    <label for="delete_password" style="font-weight: bold; color: #555;">Password:</label><br>
                    <input type="password" id="delete_password" autocomplete="new-password" 
                        style="width: 90%; padding: 8px; margin: 8px 0 15px 0; border-radius: 5px; 
                                border: 1px solid #ccc;"><br>

                    <!-- Button Container -->
                    <div style="display: flex; justify-content: space-around;">
                        <button onclick="cancelInvoice()" 
                                style="background-color: red; color: white; padding: 8px 20px; 
                                    border: none; border-radius: 5px; cursor: pointer;">Delete</button>
                        <button onclick="closeModal()" 
                                style="background-color: gray; color: white; padding: 8px 20px; 
                                    border: none; border-radius: 5px; cursor: pointer;">Close</button>
                    </div>
                </div>
            </div>
  
        </div>
    </div>

</div>




@stop
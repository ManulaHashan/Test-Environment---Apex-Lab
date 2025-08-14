<!-- **********~~~~~~~~~~~~~Manula Hashan's Devolopment~~~~~~~~~~~~~********** -->


<?php ?>
@extends('Templates/WiTemplate')


@section('title')
    Value Suggesions
@stop

@section('head')

    <script src="{{ asset('JS/ReportCalculations.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>





      


    function loadParameters(tgid) {
        if(tgid) {
            $.ajax({
                url: 'get-parameters',
                type: 'GET',
                data: { tgid: tgid },
                success: function(data) {
                    $('#test_parameeter').html(data);
                },
                error: function() {
                    alert('Error loading parameters.');
                }
            });
        } else {
            $('#test_parameeter').html('<option value="">-- Select Parameter --</option>');
        }
    }


    // Select All checkbox change event
    $(document).on('change', '#selectCh_box', function () {
        var isChecked = $(this).is(':checked');
        $('.value-checkbox').prop('checked', isChecked);
    });

    function searchValuesRecords() {
        var test_tid = $('#test_parameeter').val(); // Parameter dropdown එකෙන් value ගන්නවා
        
        if (!test_tid) {
            alert('Please select a parameter first.');
            return;
        }

        $.ajax({
            url: 'get_values_records',
            type: 'GET',
            data: { test_tid: test_tid },
            dataType: 'json',
            success: function(data) {
                var rows = '';

                if (data.length > 0) {
                    $.each(data, function(index, item) {
                        rows += `
                            <tr>
                                <td align="center">${item.test_tid}</td>
                                <td align="center">${item.value}</td>
                                <td align="center">
                                    <input type="checkbox" class="value-checkbox" 
                                        data-test-tid="${item.test_tid}" 
                                        data-value="${item.value}" checked>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    rows = '<tr><td colspan="3" align="center">No records found</td></tr>';
                }

                $('#value_record_tbl').html(rows);
                $('#selectCh_box').prop('checked', true);
            },
            error: function() {
                alert('Error loading value records.');
            }
        });
    }


    function addToSuggestions() {
        var selected = [];

      
        $('.value-checkbox:checked').each(function () {
            selected.push({
                test_tid: $(this).data('test-tid'),
                value: $(this).data('value')
            });
        });

        if (selected.length === 0) {
            alert('Please select at least one record.');
            return;
        }

        $.ajax({
            url: 'save_to_suggestions',
            type: 'POST',
            data: {
                selected: selected
                
            },
            success: function (response) {
                if (response.success) {
                    alert(response.message);
                     searchValuesRecords(); 
                } else {
                    alert(response.message);
                }
            },
            error: function () {
                alert('Error saving data.');
            }
        });
    }


    function deleteValues() {
        var selected = [];

      
        $('.value-checkbox:checked').each(function () {
            selected.push({
                test_tid: $(this).data('test-tid'),
                value: $(this).data('value')
            });
        });

        if (selected.length === 0) {
            alert('Please select at least one record to delete.');
            return;
        }

        if (!confirm('Are you sure you want to delete the selected records?')) {
            return;
        }

        $.ajax({
            url: 'delete_values',
            type: 'POST',
            data: {
                selected: selected
               
            },
            success: function (response) {
                if (response.success) {
                    alert(response.message);
                    searchValuesRecords(); 
                } else {
                    alert(response.message);
                }
            },
            error: function () {
                alert('Error deleting records.');
            }
        });
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
    </style>
@stop

@section('body')



    <div class="container">

        <div class="card" style="height: 850px; margin-top: 50px; background-color:rgb(222, 222, 223);">
            <h1> Value Suggestions Management</h1>
           
            <div style="display: flex; align-items: center; margin-bottom: 10px; gap: 20px;">
    
                    <!-- Test Group Dropdown -->
                    <div style="display: flex; align-items: center;">
                        <label for="test_group" style="font-size: 14px; min-width: 90px;"><b>Select Test Group:</b></label>
                        <select name="labbranch" style="width: 250px; height: 30px" class="input-text" id="lab_teats" onchange="loadParameters(this.value)">
                            <option value="">-- Select Test Group --</option>
                            <?php
                            $Result = DB::select("SELECT tgid, name FROM Testgroup WHERE Lab_lid = '" . $_SESSION['lid'] . "' ORDER BY name ASC");
                            foreach ($Result as $res) {
                                $tgid = $res->tgid;        
                                $name = $res->name;        
                                $displayText = $tgid . " : " . $name;
                            ?>
                            <option value="<?= $tgid ?>"><?= $displayText ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- Parameter Dropdown -->
                    <div style="display: flex; align-items: center;">
                        <label for="parameter" style="font-size: 14px; min-width: 90px;"><b>Select Parameter:</b></label>
                        <select name="test_parameeter" style="width: 250px; height: 30px" class="input-text" id="test_parameeter">
                            <option value="">-- Select Parameter --</option>
                        </select>
                    </div>




                    <div style="display: flex; align-items: center;">
                        <input type="button" style="flex: 0 0 80px; margin-left: 10px; color:green" class="btn"
                                id="ser_btn" value="Search" onclick="searchValuesRecords()">
                    
                    </div>

            </div>

          
            <div style="display: flex; width: 100%; height: 350px; margin-top: 10px;">

                <!-- Left Half -->
                <div class="pageTableScope" style="flex: 0 0 50%; padding: 0 10px; box-sizing: border-box;">
                     <table style="font-family: Futura, 'Trebuchet MS', Arial, sans-serif; font-size: 13pt;" id="valuedataTable" width="100%" border="0" cellspacing="2" cellpadding="0">
                        <tbody>
                            <tr>
                                <td valign="top">
                                    <table border="1" style="border-color: #ffffff;" cellpadding="0" cellspacing="0" class="TableWithBorder" width="100%">
                                        <thead>
                                            <tr class="viewTHead">
                                                <td width="12%" class="fieldText" align="center">LHT_ID</td>
                                                <td width="18%" class="fieldText" align="center">Value</td>
                                                <td width="18%" class="fieldText" align="center">Select</td>
                                            </tr>
                                        </thead>
                                        <tbody id="value_record_tbl">
                                            <!-- Dynamic content goes here -->
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                 
                     </table>
                 
                        <div style="display: flex; align-items: center; gap: 10px; position: sticky; bottom: 0; background: #bcb2b2; padding: 5px 0; border-top: 1px solid #ccc;">
                            <input 
                                type="button" 
                                class="btn" 
                                id="add_btn" 
                                value="Add to Value Suggestions" 
                                onclick="addToSuggestions()" 
                                style="color: green;"
                            >
                            <input 
                                type="button" 
                                class="btn" 
                                id="val_delete_btn" 
                                value="Delete" 
                                onclick="deleteValues()" 
                                style="color: red;"
                            >
                             <label for="selectCh_box" style="margin: 0;">Select All</label>
                            <input 
                                type="checkbox" 
                                id="selectCh_box" 
                                class="check" 
                                style="color: blue;"
                                checked
                            >
                           
                        </div>
                </div>  


                <!-- Right Half -->
                <div class="pageTableScope" style="flex: 0 0 50%; padding: 0 10px; box-sizing: border-box;">
                     <table style="font-family: Futura, 'Trebuchet MS', Arial, sans-serif; font-size: 13pt;" id="valueSuggesdataTable" width="100%" border="0" cellspacing="2" cellpadding="0">
                        <tbody>
                            <tr>
                                <td valign="top">
                                    <table border="1" style="border-color: #ffffff;" cellpadding="0" cellspacing="0" class="TableWithBorder" width="100%">
                                        <thead>
                                            <tr class="viewTHead">
                                                <td width="12%" class="fieldText" align="center">LHT_ID</td>
                                                <td width="18%" class="fieldText" align="center">Value</td>
                                                
                                            </tr>
                                        </thead>
                                        <tbody id="valueSugges_record_tbl">
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

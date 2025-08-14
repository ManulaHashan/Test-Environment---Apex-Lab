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
        var test_tid = $('#test_parameeter').val(); 
        
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
                     searchSuggestions();
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



    function searchSuggestions() {
        var test_tid = $('#test_parameeter').val();
        if(!test_tid){ alert('Select parameter'); return; }

        $.ajax({
            url: 'get-suggestions',
            type: 'GET',
            data: {test_tid:test_tid},
            dataType:'json',
            success:function(data){
                var rows='';
                if(data.length>0){
                    $.each(data,function(i,item){
                        rows+=`<tr data-id="${item.id}">
                            <td align="center">${item.lhtid}</td>
                            <td align="center">
                                <input type="text" class="edit-value" value="${item.value}" data-id="${item.id}">
                            </td>
                        </tr>`;
                    });
                } else {
                    rows='<tr><td colspan="2" align="center">No records</td></tr>';
                }
                $('#valueSugges_record_tbl').html(rows);
            },
            error:function(){ alert('Error loading data'); }
        });
    }


    // Insert new row and DB insert
    function insertSuggestions(){
        var test_tid = $('#test_parameeter').val();
        if(!test_tid){ alert('Select parameter'); return; }

        var new_value = prompt('Enter new value:');
        if(!new_value) return;

        $.ajax({
            url:'insert-suggestion',
            type:'POST',
            data:{
                lhtid:test_tid,
                value:new_value
            },
            success:function(res){
                alert(res.message);
                if(res.success) searchSuggestions();
            },
            error:function(){ alert('Error inserting'); }
        });
    }


    // Row click event to select row
    $(document).on('click', '#valueSugges_record_tbl tr', function() {
        $(this).addClass('selected').siblings().removeClass('selected');
    });

    // Update only selected row
    function updateValues(){
        var selectedRow = $('#valueSugges_record_tbl tr.selected');
        if(selectedRow.length == 0){
            alert('Select a row to update');
            return;
        }

        var input = selectedRow.find('.edit-value');
        var id = input.data('id'); 
        var new_value = input.val().trim();

        if(new_value === ''){
            alert('Value cannot be empty');
            return;
        }

        $.ajax({
            url:'update-suggestion',
            type:'POST',
            data:{
                id: id,
                value: new_value,
               
            },
            success:function(res){
                if(res.success){
                    alert('Value updated successfully');
                    searchSuggestions(); 
                } else {
                    alert(res.message);
                }
            },
            error:function(){
                alert('Error updating value');
            }
        });
    }


    // Delete selected row
    function deleteValues(){
        var selectedRow = $('#valueSugges_record_tbl tr.selected');
        if(selectedRow.length == 0){
            alert('Select a row to delete');
            return;
        }

        var id = selectedRow.data('id');

        if(!confirm('Delete selected value?')) return;

        $.ajax({
            url:'delete-suggestion',
            type:'POST',
            data:{
                id: id,
                
            },
            success:function(res){
                alert(res.message);
                searchSuggestions(); 
            },
            error:function(){ alert('Error deleting'); }
        });
    }


    // Select row on click
    $(document).on('click','#valueSugges_record_tbl tr',function(){
        $(this).addClass('selected').siblings().removeClass('selected');
    });


    
       
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

         #valueSugges_record_tbl tr.selected {
        background-color: #ffcccc; /* light red */
        }
        #valueSugges_record_tbl tr:hover {
            background-color: #f0f0f0; /* hover effect */
            cursor: pointer;
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
                                id="ser_btn" value="Search" onclick="searchValuesRecords(); searchSuggestions();">
                    
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

                     <div style="display: flex; align-items: center; gap: 10px; position: sticky; bottom: 0; background: #bcb2b2; padding: 5px 0; border-top: 1px solid #ccc;">
                            <input 
                                type="button" 
                                class="btn" 
                                id="insert_btn" 
                                value="Insert" 
                                onclick="insertSuggestions()" 
                                style="color: green;"
                            >
                            <input 
                                type="button" 
                                class="btn" 
                                id="update_btn" 
                                value="Update" 
                                onclick="updateValues()" 
                                style="color: yellow;"
                            >
                            <input 
                                type="button" 
                                class="btn" 
                                id="delete_val_btn" 
                                value="Delete" 
                                onclick="deleteValues()" 
                                style="color: red;"
                            >
                            
                           
                        </div>
                </div>


                      
            </div>




            {{-- ############################################################################################################# --}}



        </div>

    </div>




@stop

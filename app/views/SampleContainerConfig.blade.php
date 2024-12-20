<?php ?>
@extends('Templates/WiTemplate')


@section('title')
Sample Container Configuration
@stop

@section('head')

<script src="{{ asset('JS/ReportCalculations.js') }}"></script>


<script>
    $(document).ready(function() {

        loadRecordToTable();

    });



    // Function to load records to table

    function loadRecordToTable() {

    }

    // Function to Update Containers data
    function UpdateContainers() {

    }
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
</style>
@stop

@section('body')


<h2 class="pageheading" style="margin-top: 5px;"> Sample Container Configuration
</h2>
<div class="container" style="margin-top: 20px;">

    <div class="card" style="height: 700px;">

        <div class="card-body">

            <div style="width: 1000px; display: flex;">

                <div style="flex: 1; padding: 10px; margin-right: 5px;">

                    <div style="display: flex; align-items: center; margin-bottom: 10px; margin-top: 10px;">
                        <label style="width: 150px;font-size: 20px; margin-left: 15px;">Container Type &nbsp;:</label>
                        <select name="labbranch" style="width: 250px" class="input-text" id="containerDropdown">
                            <option value="%"> All</option>
                            <?php

                            $Result = DB::select("Select name, code FROM labbranches WHERE Lab_lid = '" . $_SESSION['lid'] . "' ORDER BY name ASC");

                            foreach ($Result as $res) {
                                $branchName = $res->name;
                                $code = $res->code;

                                if (isset($labbranch) && $labbranch == $bid) {
                            ?>
                                    <option value="{{ $code }}" selected="selected">{{ $branchName }}</option>
                                <?php
                                } else {
                                ?>
                                    <option value="{{ $code }}">{{ $branchName }}</option>
                            <?php
                                }
                            }

                            // If no branch is selected, set the default value as '%'
                            if (!isset($labbranch)) {
                                $labbranch = "%";
                            }
                            ?>

                        </select>
                    </div>
                    <div style="display: flex; align-items: center; margin-bottom: 10px; margin-top: 10px;">
                        <label style="width: 150px;font-size: 20px; margin-left: 15px; ">Search Test &nbsp;:</label>
                        <input type="text" name="searchTest" class="input-text" id="searchTest" style="width: 230px">
                    </div>
                </div>

            </div>

            <div style="flex: 1; padding: 10px; border: 2px #8e7ef7 solid; border-radius: 10px;">
                <label for="" style="font-size: 20px; "><b><i><u>Test Container Details</u></i></b></label>
                <div class="pageTableScope" style="height: 450px; margin-top: 10px;">
                    <table style="font-family: Futura, 'Trebuchet MS', Arial, 
                    sans-serif; font-size: 13pt;" id="containerdataTable" width="100%" border="0" cellspacing="2" cellpadding="0">
                        <thead>
                            <tr class="viewTHead">
                                <td align="center" class="fieldText" style="width: 15px;">TGID</td>
                                <td align="center" class="fieldText" style="width: 150px;">Name</td>
                                <td align="center" class="fieldText" style="width: 30px;">Container</td>
                                <td align="center" class="fieldText" style="width: 20px;">Select</td>
                            </tr>
                        </thead>
                        <tbody id="record_tbl">
                            <!-- Dynamic rows will be inserted here via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 10px; margin-top: 10px;">

            <input type="button" style="color:blue; font-size: 20px;" class="btn" id="configureBtn" value="Configure" onclick="UpdateContainers()">

        </div>




    </div>

</div>





@stop
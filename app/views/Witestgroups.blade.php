@extends('Templates/WiTemplate')
<?php
if (session_id() == '') {
    session_start();
}
?>
@section('title')
Testing Manage
@stop

@section('head')
<script type="text/javascript">
    window.onload = load;
    function load() {
        $('#settingstr').hide();
    }

    var del = false;
    function gettgID(id) {
        document.getElementById('tgid').value = id;
        del = true;
    }

    function getAnID(id) {
        document.getElementById('anid').value = id;
        del = true;
    }



    function viewTests(id) {
        tgid = id;
        document.getElementById('tgname').value = document.getElementById('name+' + id).value;
        document.getElementById('tgprice').value = document.getElementById('price+' + id).value;
        document.getElementById('time').value = document.getElementById('time+' + id).value;
        document.getElementById('tgcost').value = document.getElementById('cost+' + id).value;
        document.getElementById('testcode').value = document.getElementById('testCode+' + id).value;
    //get comment  
    $.ajax({
        url: "gettgcomment",
        type: 'POST',
        data: {'tgid': id, '_token': $('input[name=_token]').val()},
        success: function(result) {

            data = JSON.parse(result);
            
            for (var i = 0; i < data.length; i++) {
                document.getElementById('commentx').innerHTML = data[i].comment;
                
                //set report configs

                
                if(data[i].custom_configs === "1" || data[i].custom_configs === null){ 
                    $("#custom_configs").prop("checked", true);
                }else{
                    $("#custom_configs").prop("checked", false);
                }
                
                if(data[i].name_col === "1"){
                    $("#name_col").prop("checked", true);
                }else{
                    $("#name_col").prop("checked", false);
                }
                
                if(data[i].value_col === "1"){
                    $("#value_col").prop("checked", true);
                }else{
                    $("#value_col").prop("checked", false);
                }
                
                if(data[i].unit_col === "1"){
                    $("#unit_col").prop("checked", true);
                }else{
                    $("#unit_col").prop("checked", false);
                }
                
                if(data[i].flag_col === "1"){
                    $("#flag_col").prop("checked", true);
                }else{
                    $("#flag_col").prop("checked", false);
                }
                
                if(data[i].ref_col === "1"){
                    $("#ref_col").prop("checked", true);
                }else{
                    $("#ref_col").prop("checked", false);
                }
                
                $("#name_col_head").val(data[i].name_col_head);
                $("#value_col_head").val(data[i].value_col_head);
                $("#unit_col_head").val(data[i].unit_col_head);
                $("#flag_col_head").val(data[i].flag_col_head);
                $("#ref_col_head").val(data[i].ref_col_head);
                
                $("#name_col_width").val(data[i].name_col_width);
                $("#value_col_width").val(data[i].value_col_width);
                $("#unit_col_width").val(data[i].unit_col_width);
                $("#flag_col_width").val(data[i].flag_col_width);
                $("#ref_col_width").val(data[i].ref_col_width);
                
                $("#name_col_align").val(data[i].name_col_align);
                $("#result_col_align").val(data[i].result_col_align);
                $("#unit_col_align").val(data[i].unit_col_align);
                $("#flag_col_align").val(data[i].flag_col_align);
                $("#ref_col_align").val(data[i].ref_col_align);

                if(data[i].sample_containers_scid == "null"){
                    $("#sam_container").val(1); 
                }else{
                    $("#sam_container").val(data[i].sample_containers_scid); 
                }

                
                
                if(data[i].age_ref === "1"){
                    $("#age_ref").prop("checked", true);
                }else{
                    $("#age_ref").prop("checked", false);
                }

                if(data[i].rep_heading === 1){
                    $("#rep_heading").prop("checked", true);
                }else{
                    $("#rep_heading").prop("checked", false);
                }
                
            }


        }
    });
    //


    $.ajax({
        url: "testGroupsubmit",
        type: 'POST',
        data: {'submit': 'getTests', 'tgid': id, '_token': $('input[name=_token]').val()},
        success: function(result) {
            if (result !== "") {
                var div = document.getElementById('TestList');
                div.innerHTML = result;
            } else {
                var div = document.getElementById('TestList');
                div.innerHTML = "<p>Testings not found!</p>";
            }
        }
    });
}

function validate(){
    if (del){
        var x = confirm("Dou you want to delete this record?");
        if (x){
            return true;
        } else{
            del = false;
            return false;
        }
    }
}

function printGroupTable() {

    var body = $("#tg_table").html();
    var newWin = window.open('', 'Print-Window');
    newWin.document.open();
    newWin.document.write("<html><head><title>MLWS - Print</title><head><body onload='window.print()'><center><h2>MLWS Testing Details</h2><div style='width:800px'><hr/><br/>" + body + "</div><br/><hr/><p style='font-size:12px' align='right'>Generated By MLWS. Powered by Appex Solutions. www.appexsl.com</p></center></body></html>");
    newWin.document.close();
    setTimeout(function () {
        newWin.close();
    }, 2000);
}
var tgid;
function updateComment(){

    var form = $('#altform').serialize();

    $.ajax({
        url: "updatetgcomment",
        type: 'POST',
        data: $('#altform').serialize() + "&tgid=" + tgid + "&_token" + $('input[name=_token]').val(),
        success: function(result) {
            alert(result);
        }
    });
}

function openSettings(){

    if ($('#settingstr').is(":visible")){
        $('#settingstr').hide();
    } else{
        $('#settingstr').show();
    }
}

function getCost(){

    var actCost = parseFloat($("#tgprice").val()*((100-$("#testcostpr").val())/100));

    $("#tgcost").val(actCost);

}

var last_selected_tgid = "";
var last_selected_price = "";

var tltp_tests = "Click here to add this cost to all tests in the price list. (Not adds for center prices list costs)";
var tltp_centers = "Click here to add this cost to all centers. (Only for the selected test)";

function manageCosts(id){


    var tgid = id.split("+")[1];
    last_selected_price = id.split("+")[2];

    var branch = $("#branch").val();

    last_selected_tgid = tgid;

    // alert(branch);

    var tcost = 0;

    $.ajax({
        url: "getTGCosts",
        type: 'POST',
        data: "tgid=" + tgid + "&brid="+ branch + "&_token" + $('input[name=_token]').val(),
        success: function(result) {
            // alert(result);

            data = JSON.parse(result);

            var tr = "";
            
            for (var i = 0; i < data.length; i++) {

                tr += "<tr><td>"+data[i].name+"</td><td align='right'>"+data[i].amount+"</td><td>"+data[i].date+"</td><td><input type='button' value = 'Remove' onclick='removeCost("+data[i].id+")'></td><td><input type='button' value = 'Add to all Tests' title='"+tltp_tests+"' onclick='toAllTests("+data[i].id+")'></td><td><input type='button' value = 'Add to all Centers' title='"+tltp_centers+"' onclick='toAllCenters("+data[i].id+")'></td></tr>";

                tcost += data[i].amount;

            }

            $("#costtbody").html(tr);

            $("#t_cost").html(tcost);

        } 
    });

    var targeted_popup_class = jQuery($('[data-popup-open]')).attr('data-popup-open');
    $('[data-popup="' + targeted_popup_class + '"]').fadeIn(350);

}

function toAllTests(id){
    var x = confirm("Do you want to add this cost to all testings in the price list?");
    if (x){

        var branch = $("#branch").val();

        $.ajax({
            url: "toAllTests",
            type: 'POST',
            data: "recid=" + id + "&brid="+ branch + "&_token" + $('input[name=_token]').val(),
            success: function(result) {
                alert(result);
            } 

        });

    }

}

function toAllCenters(id){

    var x = confirm("Do you want to add this cost to all centers related to selected test?");
    if (x){

        var branch = $("#branch").val();

        $.ajax({
            url: "toAllCenters",
            type: 'POST',
            data: "recid=" + id + "&brid="+ branch + "&_token" + $('input[name=_token]').val(),
            success: function(result) {
                alert(result);
            } 

        });

    }

}

function addCost(){

    var costname = $("#cname").val();
    var cost = $("#camount").val();
    var branch = $("#branch").val();

    if(last_selected_tgid !== ""){

        $.ajax({
            url: "addTGCosts",
            type: 'POST',
            data: "costname=" + costname+ "&cost="+ cost+ "&brid="+ branch + "&tgid="+ last_selected_tgid + "&_token" + $('input[name=_token]').val(),
            success: function(result) {
                alert(result);

                manageCosts("tgid+"+last_selected_tgid+"+"+last_selected_price);
                
                $("#cname").val("");
                $("#camount").val("");
                $("#costper").val("");

            } 

        });

    }else{

        alert("Please select test again!");

    }
    
}

function removeCost(id) {

    var branch = $("#branch").val();

    var x = confirm("Dou you want to remove this cost from testing?");
    if (x){

        $.ajax({
            url: "removeTGCosts",
            type: 'POST',
            data: "id=" + id+ "&brid="+ branch+ "&tgid="+ last_selected_tgid+ "&_token" + $('input[name=_token]').val(),
            success: function(result) {
                alert(result);
                manageCosts("tgid+"+last_selected_tgid+"+"+last_selected_price); 
            } 

        });

    }

}

function closePrivs() {

    last_selected_tgid = "";
    last_selected_price = "";

    var targeted_popup_class = jQuery($('[data-popup-close]')).attr('data-popup-close');
    $('[data-popup="' + targeted_popup_class + '"]').fadeOut(350);

    $('#btnsearch').click(); 

}

function calcostper(){

    var costpr =  parseFloat($("#costper").val());
    var pricex = parseFloat(last_selected_price);

    var cost = pricex*(costpr/100);

    $("#camount").val(cost.toFixed(2));

}


</script>
@stop

@section('body')
<?php
if (isset($_SESSION['lid']) & isset($_SESSION['luid'])) {
    ?>
    <blockquote>
        <?php
        $cuSymble = $_SESSION['cuSymble'];
        ?>

        <h2 class="pageheading">Manage Testing Groups</h2>
        <p>&nbsp;</p>


        <table width="100%">
            <tr valign="top">
                <form action="testGroupsubmit" id="altform" method="post" onsubmit="return validate()">
                    <td width="25%">
                        <p class="tableHead">Add new Testing Group</p>

                        <table width="100%">
                            <tr>
                                <td>                                
                                    Name
                                </td>
                                <td>
                                    <input type="text" name="tgname" id="tgname" class="input-text" style="width: 150px;">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Price {{ $cuSymble }}
                                </td>
                                <td>
                                    <input type="text" name="tgprice" id="tgprice" class="input-text" style="width: 150px;" >
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Cost {{ $cuSymble }}
                                </td>
                                <td>
                                    <table border="0">
                                        <tbody>
                                            <tr>
                                                <td><input type="text" name="tgcost" id="tgcost" class="input-text" style="width: 70px;" value="0" ></td>
                                                <td><input type="text" id="testcostpr" class="input-text" style="width: 40px;" onkeyup="getCost()">%</td>
                                            </tr>
                                        </tbody>
                                    </table>



                                </td> 
                            </tr>
                            <tr valign="top">
                                <td>
                                    Time(Hrs)
                                </td>
                                <td>
                                    <input type="text" name="time" value="0" id="time" class="input-text" style="width: 150px;">
                                </td>
                            </tr>
                            <tr valign="top">
                                <td>
                                    Barcode Name
                                </td>
                                <td>
                                    <input type="text" name="testcode" id="testcode" class="input-text" style="width: 150px;">
                                </td>
                            </tr>
                            <tr valign="top">
                                <td>
                                    Sample Container
                                </td>
                                <td>

                                    <select class="select-basic" name="sam_container" id="sam_container" style="width: 170px;">
                                        <?php
                                        $rs5 = DB::select("select scid, name from sample_containers ");
                                        foreach ($rs5 as $rss) {


                                            ?>
                                            <option value="{{ $rss->scid }}">{{ $rss->name }}</option>
                                            <?php

                                        }
                                        ?>
                                    </select>

                                </td>
                            </tr>
                            <tr valign="top">
                                <td>
                                    Comment Section HTML
                                </td>
                                <td>
                                    <textarea id="commentx" name="comment" rows="10" class="text-area" style="width: 150px;"></textarea>
                                    <a href="https://html-online.com/editor" target="_blank" style="float: left;">HTML Editor</a> 
                                </td>
                            </tr>

                        </table>

                        <br/>

                        <div onclick="openSettings()" style="cursor: pointer; padding: 5px; background-color: cornflowerblue; color: white">More Settings</div>
                        <br/>
                        <div id="settingstr">
                            <table cellpadding  = "5">
                                <tr valign="top">
                                    <td>
                                        <label for="custom_configs">Default Format</label>
                                    </td>
                                    <td>
                                        <input type="checkbox" id="custom_configs" name="custom_configs" checked="checked">
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td >
                                        <label for="rep_heading">Report Column Headings</label>
                                    </td>
                                    <td>
                                        <input type="checkbox" id="rep_heading" name="rep_heading" checked="checked">
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td>
                                        <hr/>
                                    </td>
                                    <td>
                                        <hr/>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td>
                                        Name Column
                                    </td>
                                    <td>
                                        <input type="checkbox" name="name_col" id="name_col">
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td>
                                        Column Heading
                                    </td>
                                    <td>
                                        <input type="text" name="name_col_head" id="name_col_head" value="TESTING" class="input-text">
                                    </td>
                                </tr>

                                <tr valign="top">
                                    <td>
                                        Column Width
                                    </td>
                                    <td>
                                        <input type="number" name="name_col_width" id="name_col_width" class="input-text">
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td>
                                        Column Align
                                    </td>
                                    <td>
                                        <select name="name_col_align" id="name_col_align" class="select-basic"> 
                                            <option>left</option>
                                            <option>center</option>
                                            <option>right</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr valign="top">
                                    <td>
                                        <hr/>
                                    </td>
                                    <td>
                                        <hr/>
                                    </td>
                                </tr>


                                <tr valign="top">
                                    <td>
                                        Value Column
                                    </td>
                                    <td>
                                        <input type="checkbox" name="value_col" id="value_col">
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td>
                                        Column Heading
                                    </td>
                                    <td>
                                        <input type="text" name="value_col_head" id="value_col_head" value="RESULT" class="input-text">
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td>
                                        Column Width
                                    </td>
                                    <td>
                                        <input type="number" name="value_col_width" id="value_col_width" class="input-text">
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td>
                                        Column Align
                                    </td>
                                    <td>
                                        <select name="result_col_align" id="result_col_align" class="select-basic">
                                            <option>left</option>
                                            <option>center</option>
                                            <option>right</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr valign="top">
                                    <td>
                                        <hr/>
                                    </td>
                                    <td>
                                        <hr/>
                                    </td>
                                </tr>

                                <tr valign="top">
                                    <td>
                                        Unit Column
                                    </td>
                                    <td>
                                        <input type="checkbox" name="unit_col" id="unit_col">
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td>
                                        Column Heading
                                    </td>
                                    <td>
                                        <input type="text" name="unit_col_head" id="unit_col_head" value="UNIT" class="input-text">
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td>
                                        Column Width
                                    </td>
                                    <td>
                                        <input type="number" name="unit_col_width" id="unit_col_width" class="input-text">
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td>
                                        Column Align
                                    </td>
                                    <td>
                                        <select name="unit_col_align" id="unit_col_align" class="select-basic">
                                            <option>left</option>
                                            <option selected="selected">center</option>
                                            <option>right</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr valign="top">
                                    <td>
                                        <hr/>
                                    </td>
                                    <td>
                                        <hr/>
                                    </td>
                                </tr>

                                <tr valign="top">
                                    <td>
                                        Flag Column
                                    </td>
                                    <td>
                                        <input type="checkbox" name="flag_col" id="flag_col">
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td>
                                        Column Heading
                                    </td>
                                    <td>
                                        <input type="text" name="flag_col_head" id="flag_col_head" value="FLAG" class="input-text">
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td>
                                        Column Width
                                    </td>
                                    <td>
                                        <input type="number" name="flag_col_width" id="flag_col_width" class="input-text">
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td>
                                        Column Align
                                    </td>
                                    <td>
                                        <select name="flag_col_align" id="flag_col_align" class="select-basic">
                                            <option>left</option>
                                            <option selected="selected">center</option>
                                            <option>right</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr valign="top">
                                    <td>
                                        <hr/>
                                    </td>
                                    <td>
                                        <hr/>
                                    </td>
                                </tr>

                                <tr valign="top">
                                    <td>
                                        Reference Column
                                    </td>
                                    <td>
                                        <input type="checkbox" name="ref_col" id="ref_col">
                                    </td>
                                </tr>

                                <tr valign="top">
                                    <td>
                                        Column Heading
                                    </td>
                                    <td>
                                        <input type="text" name="ref_col_head" id="ref_col_head" value="REFERENCE RANGE" class="input-text">
                                    </td>
                                </tr>

                                <tr valign="top">
                                    <td>
                                        Column Width
                                    </td>
                                    <td>
                                        <input type="number" name="ref_col_width" id="ref_col_width" class="input-text">
                                    </td>
                                </tr>

                                <tr valign="top">
                                    <td>
                                        Column Align
                                    </td>
                                    <td>
                                        <select name="ref_col_align" class="select-basic" id="ref_col_align">
                                            <option>left</option>
                                            <option selected="selected">center</option>
                                            <option>right</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr valign="top">
                                    <td>
                                        <hr/>
                                    </td>
                                    <td>
                                        <hr/>
                                    </td>
                                </tr>

                                <tr valign="top">
                                    <td>
                                        Age Wise Reference
                                    </td>
                                    <td>
                                        <input type="checkbox" name="age_ref" id="age_ref">
                                    </td>
                                </tr>

                            </table>
                        </div>

                        <br/>



                        <input type="submit" class="btn" style="margin-left: 0px; width: 170px;" name="submit" value="Add Group">
                        <input type="button" class="btn" style="margin:0px; width: 170px;" value="Update" onclick="updateComment()">
                        <br/>
                        {{ $msg or '' }}

                    </form>
                </td>

                <td>
                    <form action="testGroupsubmit" method="POST" onsubmit="return validate()">
                        <div class="pageTableScope2" style="height: 500px; margin-left: 30px; border-left-color: #001092; border-left-style: solid; border-left-width: 1px; padding-left: 20px">
                            <p style="float: left" class="tableHead">View Testing Groups</p>
                            <br/>
                            <table style="float: left">
                                <tr>

                                    <?php
                                    $separate_prices = false;
                                    $rsx = DB::select("SELECT separate_prices_branch FROM `configs` where Lab_lid = '" . $_SESSION['lid'] . "'");
                                    foreach ($rsx as $rsbx) {
                                        $separate_prices = $rsbx->separate_prices_branch;
                                    }

                                    if ($separate_prices) {
                                        ?>

                                        <td>Select Branch</td>
                                        <td>

                                            <select class="select-basic" id="branch" name="branch">
                                                <option value="all">General</option> 

                                                <?php
                                                $rs = DB::select("select name, bid from labbranches where lab_lid = '" . $_SESSION['lid'] . "'");
                                                foreach ($rs as $rsb) {

                                                    if (isset($branchID) && $rsb->bid == $branchID) {
                                                        ?>
                                                        <option value="{{ $rsb->bid }}" selected="selected">{{ $rsb->name }}</option>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <option value="{{ $rsb->bid }}">{{ $rsb->name }}</option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>

                                        </td>
                                        <td width="50"><input type="submit" id="btnsearch" name="submit" value="Search" class="btn"/></td>

                                        <?php
                                    } else {
                                        ?>
                                        <td width="200"></td>
                                        <?php
                                    }
                                    ?>

                                    <td><input type="button" value="Print Table" class="btn" style="margin: 0px; width: 120px;" onclick="printGroupTable()"></td>
                                    <td><input type="submit" name="submit" value="Update Table" class="btn" style="margin: 0px;"></td>
                                </tr>
                            </table>                       

                            <table id="tg_table" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="482" height="70" valign="top">
                                        <table width="800" border="1" cellpadding="0" cellspacing="0" class="TableWithBorder">
                                            <tr>
                                                <th width="50%" class="viewTHead" scope="col">ID</th>
                                                <th width="50%" class="viewTHead" scope="col">Group Name</th>
                                                <th width="11%" class="viewTHead" scope="col">Price {{ $cuSymble }}</th>
                                                <th width="10%" class="viewTHead" scope="col">Cost {{ $cuSymble }}</th>
                                                <th width="21%" class="viewTHead" scope="col">Time Duration (Hours)</th>
                                                <th width="21%" class="viewTHead" scope="col">Test Code</th>
                                            </tr>

                                            <?php
                                            if (isset($branchID) && $branchID != 'all') {
                                                $Result = DB::select("select a.testCode,a.tgid,a.name,b.price,b.cost,b.testingtime from Testgroup a, labbranches_has_Testgroup b where a.tgid=b.tgid and a.Lab_lid = '" . $_SESSION['lid'] . "' and b.bid = '" . $branchID . "' order by name ASC");
                                            } else {
                                                $Result = DB::select("select * from Testgroup where Lab_lid = '" . $_SESSION['lid'] . "' order by name ASC");
                                            }
                                            foreach ($Result as $res) {
                                                ?>
                                                <tr>
                                                    <td>{{ $res->tgid }}</td>
                                                    <td height="26"><input type="text" name="name+{{ $res->tgid }}" id="name+{{ $res->tgid }}" value="{{ $res->name }}" size="50"></td>
                                                    <td><input type="text" name="price+{{ $res->tgid }}" id="price+{{ $res->tgid }}" value="{{ $res->price }}" size="5"></td>
                                                    <td><input type="text" name="cost+{{ $res->tgid }}" id="cost+{{ $res->tgid }}" value="{{ $res->cost }}" size="5"></td>
                                                    <td><input type="text" name="time+{{ $res->tgid }}" id="time+{{ $res->tgid }}" value="{{ $res->testingtime }}" size="10">
                                                    </td>
                                                    <td><input type="text" name="testCode+{{ $res->tgid }}" id="testCode+{{ $res->tgid }}" value="{{ $res->testCode }}" size="10"></td>

                                                    <td width="19%" ><input type="button" name="{{ $res->tgid }}" class="btn" style="margin:0px;" value="View Testings" onClick="viewTests(name)"></td>

                                                    <td width="23%" ><input id="{{ $res->tgid }}" type="submit" class="btn" style="margin:0px;" name="submit" value="Delete" onClick="gettgID(id)">

                                                        <td><input type="button" id="btncost+{{ $res->tgid }}+{{ $res->price }}" data-popup-open="popup-1" name="btncost" class="btn" style="margin:0px;" value="Costing" onclick="manageCosts(id)"></td>

                                                    </td> 
                                                </tr>
                                            <?php } ?>
                                        </table>
                                        <input type="hidden" id="tgid" name="tgid" value="">
                                    </td>
                                    <td width="400" valign="top">
                                        <div id="TestList">
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </form>
                </td>
            </tr>            
        </table> 
        <hr/>
        <h2 class="pageheading">Manage Analyzers</h2>
        <p>&nbsp;</p>

        <form action="analyzermanagesubmit" method="post" onsubmit="return validate()">
            <table width="100%">
                <tr valign="top">
                    <td width="25%">
                        <p class="tableHead">Add new Analyzer</p>

                        <table width="100%">
                            <tr>
                                <td width="150px">                                
                                    Name
                                </td>
                                <td>
                                    <input type="text" name="anname" class="input-text" />
                                </td>
                            </tr>                        
                            <tr>
                                <td>

                                </td>
                                <td>
                                    <input type="submit" class="btn" style="margin-left: 0px; width: 190px;" name="submit" value="Add Analyzer">
                                    {{ $msg or '' }}
                                </td>
                            </tr>

                        </table>
                    </td>

                    <td>
                        <div class="pageTableScope2" style="margin-left: 30px; border-left-color: #001092; border-left-style: solid; border-left-width: 1px; padding-left: 20px">
                            <p class="tableHead">View Analyzers</p>
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="482" height="70" valign="top">
                                        <table width="414" border="1" cellpadding="0" cellspacing="0" class="TableWithBorder">
                                            <tr>
                                                <th width="37%" class="viewTHead" scope="col">Analyzer Name</th>
                                            </tr>

                                            <?php
                                            $Result = DB::select("select * from analyzers where Lab_lid = '" . $_SESSION['lid'] . "' and status = '1'");
                                            foreach ($Result as $res) {
                                                ?>
                                                <tr>
                                                    <td height="26"><input type="text" name="name+{{ $res->anid }}" value="{{ $res->name }}" style="width: 95%"></td>
                                                    <td width="23%"><input type="submit" class="btn" style="margin:0px;" name="submit" value="Delete" onClick="getAnID('{{ $res->anid }}')"> 

                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </table>
                                        <input type="hidden" id="anid" name="anid" value="">
                                    </td>
                                    <td width="400" valign="top">
                                        <div id="TestList">
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>            
            </table>
        </form>



        <div class="popup" data-popup="popup-1">
            <div class="popup-inner">
                <h2>Testing Cost Management</h2>

                <br>
                <br>

                <table>
                    <tr>
                        <td>Cost Name</td>
                        <td>&nbsp; <input type="text" name="cname" id="cname" class="input-text" list="costlist">

                            <datalist id="costlist">
                                
                                <?php
                                        $rscc = DB::select("SELECT DISTINCT a.name FROM test_costs a, Testgroup b where a.Testgroup_tgid = b.tgid and b.Lab_lid = '".$_SESSION["lid"]."'");
                                        foreach ($rscc as $rscc) {


                                            ?>
                                            <option>{{ $rscc->name }}</option>
                                            <?php

                                        }
                                        ?>

                            </datalist>

                        </td>
                    </tr>
                    <tr>
                        <td>Cost Amount Rs. </td>
                        <td>&nbsp; <input type="number" name="camount" id="camount" class="input-text"> &nbsp; % <input type="number" id="costper" class="input-text" onkeyup="calcostper();" style="width: 50px;" /> ( % From test price )</td>
                    </tr>

                    <tr><td><input type="button" name="csave" id="csave" class="btn" value="Add Cost" style="margin-left: 0;" onclick="addCost();"></td>
                    </tr>
                    
                </table>

                <br><br>

                <table border="1" style="font-family: sans-serif; border-collapse: collapse;">

                    <tr>
                        <th>Cost</th>
                        <th>Amount Rs.</th>
                        <th>Added Date</th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>

                    <tbody id="costtbody" style="color: black; font-style: normal; text-decoration: none;">

                    </tbody>


                </table>

                <br><br>

                <h3 style="color: black; font-style: normal; font-family: sans-serif; color: blue;">Total Cost Rs. <span id="t_cost"></span></h3>

                <p><button class="btn btn-default submit" data-popup-close="popup-1" style="float: right;" onclick="closePrivs()">Close</button></p>

            </div>
        </div>

        <style type="text/css">

            .tooltip {
              position: relative;
              display: inline-block;
              border-bottom: 1px dotted black;
          }

          .tooltip .tooltiptext {
              visibility: hidden;
              width: 120px;
              background-color: #555;
              color: #fff;
              text-align: center;
              border-radius: 6px;
              padding: 5px 0;
              position: absolute;
              z-index: 1;
              bottom: 125%;
              left: 50%;
              margin-left: -60px;
              opacity: 0;
              transition: opacity 0.3s;
          }

          .tooltip .tooltiptext::after {
              content: "";
              position: absolute;
              top: 100%;
              left: 50%;
              margin-left: -5px;
              border-width: 5px;
              border-style: solid;
              border-color: #555 transparent transparent transparent;
          }

          .tooltip:hover .tooltiptext {
              visibility: visible;
              opacity: 1;
          }

      </style>




  </blockquote>
  <?php
}
?>
@stop
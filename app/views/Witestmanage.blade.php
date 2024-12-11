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
    window.onload = load();
    function load() {
        document.getElementById('vchcount').disabled = true;
        document.getElementById('vmin').disabled = true;
        document.getElementById('vmax').disabled = true;
        document.getElementById('vdecis').disabled = true;
    }

    function getLhtID(value) {
        document.getElementById('lhtid').value = value;
        $.ajax({
            url: "managetestings",
            type: 'POST',
            data: {'submit': 'Delete', 'lhtid': value, '_token': $('input[name=_token]').val()},
            success: function (result) {
                alert("Test Deleted!");
                window.location = "testmanage";
            }
        });
    }


    var stid;
    function viewMat(tid) {
        var MatTable = document.getElementById('matTable');
        MatTable.innerHTML = "";
        stid = tid;
        document.getElementById('tid').value = tid;
        var url = "managetestings";
        $.ajax({
            url: url,
            type: 'POST',
            data: {'submit': 'viewMat', 'tid': tid, '_token': $('input[name=_token]').val()},
            success: function (result) {
                
                $("#updatebtn").prop("disabled",false);
                
                if (result != "<tr><th>Material Name</th><th>Value</th><th>Unit</th></tr>") {
                    MatTable.innerHTML = result;
                } else {
                    result = "<tr><td>Materials Not found!</td></tr>";
                    MatTable.innerHTML = result;
                }

                viewTesting(tid);
            }
        });
    }

    function deleteMat(id) {
        $.ajax({
            url: "managetestings",
            type: 'POST',
            data: {'submit': 'deleteMat', 'lmid': id, 'tid': stid, '_token': $('input[name=_token]').val()},
            success: function (result) {
                if (result != "Error in Delete Material from Testing!") {
                    viewMat(stid);
                } else {
                    alert('Error deleting the material!');
                }
            }
        });
    }

    function getTGID(value) {

        document.getElementById('tgid').value = value;
    }

    function getMatID(value) {

        document.getElementById('matid').value = value;
        getUnits();
    }

    function getUnits() {
        var matID = document.getElementById('matid').value;
        createXMLHttpRequest();
        var url = "testmansubmit?submit=getUnits&matID=" + matID;
        xmlHttp.open("POST", url, true);
        xmlHttp.send();
        xmlHttp.onreadystatechange = function () {
            if (xmlHttp.readyState === 4 && xmlHttp.status === 200) {

                document.getElementById('units').innerHTML = xmlHttp.responseText;
            }
        }

    }

    function selectVType() {
        load();
        var vtype = document.getElementById('vtype').value;
        if (vtype === "Integer") {

            document.getElementById('vchcount').disabled = false;
            document.getElementById('vmin').disabled = false;
            document.getElementById('vmax').disabled = false;
        } else if (vtype === "Decimal") {
            document.getElementById('vchcount').disabled = false;
            document.getElementById('vmin').disabled = false;
            document.getElementById('vmax').disabled = false;
            document.getElementById('vdecis').disabled = false;
        } else if (vtype === "String") {
            document.getElementById('vchcount').disabled = false;
        }
    }

    function addMaterial() {
        if ($('#tid').val() == "") {
            alert('Select a Testing to add material!');
        } else {
            var tid = $('#tid').val();
            var mat = $('#matName').val();
            var val = $('#matval').val();
            var unit = $('#units').val();
            $.ajax({
                url: "managetestings",
                type: 'POST',
                data: {'submit': 'addMaterial', 'tid': tid, 'matid': mat, 'val': val, 'units': unit, '_token': $('input[name=_token]').val()},
                success: function (result) {
                    if (result != "0") {
                        viewMat(stid);
                    } else {
                        alert('Error adding the material!');
                    }
                }
            });
        }
    }

    function selectAgeRange(id) {
        alert(id);
    }

    function deleteAgeRange(id) {
        alert(id);
    }

    function adRefRange() {
        var tid = $('#tid').val();

        if (tid !== "") {

            var min = $('#arfmin').val();
            var max = $('#arfmax').val();
            var gen = $('#arfgen').val();
            var age = $('#arfage').val();
            $.ajax({
                url: "addreference",
                type: 'POST',
                data: {'submit': 'addRefRenge', 'tid': tid, 'min': min, 'max': max, 'gen': gen, 'age': age, '_token': $('input[name=_token]').val()},
                success: function (result) {
//                    alert(result);
                    loadRefRanges();
                }
            });
        } else {
            alert("Select test to continue!");
        }
    }
    
    function loadRefRanges() {
        var tid = $('#tid').val();

        if (tid !== "") {
            $.ajax({
                url: "loadreference",
                type: 'POST',
                data: {'submit': 'loadreference', 'tid': tid, '_token': $('input[name=_token]').val()},
                success: function (result) {
//                    alert(result);
                    $("#agereftable").html(result);
                }
            });
        } else {
            alert("Select test to continue!");
        }
    }

    var viewTestingData;
    function viewTesting(tid) {
        $.ajax({
        url: "managetestings",
                type: 'POST',
                data: {'submit': 'viewTesting', 'tid': tid, '_token': $('input[name=_token]').val()},
                success: function(result) {
                viewTestingData = JSON.parse(result);
                        var data = viewTestingData;
                        for (var i = 0; i < data.length; i++) {

                $('#defaultval').val("");
                        $('#lisis').val("");
                        $('#defaultval').val(data[i].defaultval);
                        $('#lisis').val(data[i].listestid);
                        $('#tid').val(data[i].test_tid);
                        $('#testname').val(data[i].name);
                        $('#repname').val(data[i].reportname);
                        $('#measurement').val(data[i].measurement);
                        $('#testprice').val(data[i].price);
                        $('#testGroup').val(data[i].Testgroup_tgid);
                        $('#tgid').val(data[i].Testgroup_tgid);
                        var arr = data[i].pattern.split("#");
                        $("#vtype").val(arr[0]);
                        $('#vchcount').val(arr[1]);
                        $('#vmin').val(arr[2]);
                        $('#vmax').val(arr[3]);
                        $('#refmin').val(data[i].refference_min);
                        $('#refmax').val(data[i].refference_max);
                        $('#vdecis').val(arr[4]);
                        $('#analyzer').val(data[i].analyzers_anid);
                        $('#tcat').val(data[i].testingcategory_tcid);
                        $('#tinput').val(data[i].testinginput_tiid);
                        $('#order').val(data[i].orderno);
                        if (data[i].viewnorvals == '1'){
                $('#viewnor').attr('checked', 'checked');
                } else{
                $('#viewnor').removeAttr('checked');
                }

                if (data[i].selactablevals == '1'){
                $('#selvals').attr('checked', 'checked');
                } else{
                $('#selvals').removeAttr('checked');
                }
                
                if (data[i].advance_ref == '1'){
                $('#awr').attr('checked', 'checked');
                } else{
                $('#awr').removeAttr('checked');
                }

                if (data[i].viewanalyzer == '1'){
                $('#viewana').attr('checked', 'checked');
                } else{
                $('#viewana').removeAttr('checked');
                }
                
                loadRefRanges();

                }



                }
        }
        );
    }


</script>
@stop

@section('body')
<?php
if (isset($_SESSION['lid']) & isset($_SESSION['luid'])) {
    ?>
    <blockquote>

        <table style="margin-left: -20px;" width="100%">
            <tr>
                <td>
                    <h2 class="pageheading" style="margin-top: -10px;"> Manage Testing and Materials
                    </h2> 
                </td>

                <td>
                    <form action="managetestings" method="POST">
                        Select Test Group 
                        <select name="tgroup" style="" class="input-text"> 
                            <option value="%">ALL</option>
                            <?php
                            $Result = DB::select("select c.name,c.tgid from test a, Lab_has_test b,Testgroup c where a.tid = b.test_tid and b.testgroup_tgid = c.tgid and b.lab_lid='" . $_SESSION['lid'] . "' group by c.name order by c.name");
                            foreach ($Result as $res) {
                                $tgid = $res->tgid;
                                $group = $res->name;

                                if (isset($tgroup) && $tgroup == $tgid) {
                                    ?>
                                    <option value="{{ $tgid }}" selected="selected">{{ $group }}</option>
                                    <?php
                                } else {
                                    ?>
                                    <option value="{{ $tgid }}">{{ $group }}</option>
                                    <?php
                                }
                            }

                            if (!isset($tgroup)) {
                                $tgroup = "%";
                            }
                            ?>

                            <input type="submit" name="submit" value="Search" class="btn" style="margin :0; float: right"/>
                    </form>
                </td>
            </tr>
        </table>

        <br/>

        <?php
        $cuSymble = $_SESSION['cuSymble'];
        ?>
        <!--<form action="managetestings" method="POST" onsubmit="return validate()">-->
        <div class="pageTableScope" style="height: 400px">
            <form action="managetestings" method="POST" onsubmit="return validate()">
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="60%"valign="top">
                            <table border="1px" style="border-color: #ffffff" cellpadding="0" cellspacing="0" class="TableWithBorder">
                                <tr class="viewTHead">
                                    <th>Test ID</th>                                
                                    <th>Testing Name</th>                                
                                    <th>Testing Group</th>
                                    <th>Min. Rate</th>
                                    <th>Max. Rate</th>
                                    <th>Normal Rate</th>
                                    <th>Unit</th>
                                    <th>Price {{ $cuSymble }}</th>
                                    <th></th>
                                    <th></th>
                                </tr>

                                <?php
                                $Result = DB::select("select a.tid,a.name as name,c.name as tgroup,a.minrate,a.maxrate,a.normalValue,a.tid,b.measurement,b.price,b.lhtid, d.refference_min, d.refference_max from test a, Lab_has_test b,Testgroup c,labtestingdetails d where c.tgid like '" . $tgroup . "' and d.Lab_lid = b.lab_lid and a.tid=d.test_tid and a.tid = b.test_tid and b.testgroup_tgid = c.tgid and b.lab_lid='" . $_SESSION['lid'] . "' GROUP BY a.tid order by c.name ASC, b.orderno ASC");
                                foreach ($Result as $res) {
                                    $tid = $res->tid;
                                    ?>

                                    <tr>
                                        <td>#{{ $res->tid }}</td>
                                        <td><input type="text" class="input-text" style="margin: opx;" name="name+{{ $res->tid }}" value="{{ $res->name }}"></td>                                    
                                        <td>{{ $res->tgroup }}</td>
                                        <td>{{ $res->refference_min }}</td>
                                        <td>{{ $res->refference_max }}</td>
                                        <td>{{ $res->normalValue }}</td>
                                        <td><input type="text" class="input-text" style="margin: opx; width: 50px;" name="mes+{{ $res->tid }}" value="{{ $res->measurement }}"></td>
                                        <td><input type="text" class="input-text" style="margin: opx; width: 50px;" name="price+{{ $res->tid }}" value="{{ $res->price }}" pattern="[0-9]{1,10}" title="Insert Valid Price!" required></td>
                                        <td><input type="button" class="btn" style="margin: opx;" name="ViewMats" value="More" onClick="viewMat({{ $tid }})"></td>
                                        <td>
                                            <!-- <input id="{{ $res->lhtid }}" type="button" name="submit" value="Delete" class="btn" style="margin: opx;" onclick="getLhtID(id)"> -->
                                            <input type="hidden" id="lhtid" name="lhtid" value=""></td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </td>
                        <td width="1%" valign="top">

                        </td>                            

                        <td width="39%" valign="top">






                        </td>
                    </tr>
                    <tr>
                        <td align="right"><input type="submit" class="btn" name="submit" value="Update Table">
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <hr/>



        <form action="managetestings" method="POST" onsubmit="return validate()">

            <table width="100%" border="0" cellpadding="0" cellspacing="0">

                <tr>
                    <td align="right"></td>
                    <td></td>                           
                </tr>
                <tr>
                    <td width="30%" valign="top"><p class="tableHead">Add new Testing</p>
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tbody>
                                <tr>
                                    <td valign="top">
                                        <table width="100%">
                                            <tr>
                                                <td width="140">Test Name </td>
                                                <td><input type="text" class="input-text" style="width: 200px;" id="testname" name="testname"/></td>
                                            </tr>
                                            <tr>
                                                <td width="140">Report Name </td>
                                                <td><input type="text" class="input-text" style="width: 200px;" id="repname" name="repname"/></td>
                                            </tr>
                                            <tr>
                                                <td>Measurement</td>
                                                <td><input type="text" class="input-text" style="width: 200px;" id="measurement" name="measurement"></td>
                                            </tr>
                                            <tr>
                                                <td>Price {{ $cuSymble }}</td>
                                                <td><input type="text" class="input-text" style="width: 200px;" id="testprice" name="testprice" pattern="[0-9]{1,20}" title="Enter valid price!" value="0"></td>
                                            </tr>
                                            <tr>
                                                <td>Testing Group </td>
                                                <td><select class="select-basic" name="testGroup" id="testGroup" onChange="getTGID(value)" style="width: 222px;">
                                                        <?php
                                                        $Result = DB::select("select * from Testgroup where Lab_lid = '" . $_SESSION['lid'] . "' order by tgid DESC");
                                                        foreach ($Result as $res) {
                                                            ?>

                                                            <option value="{{ $res->tgid }}"> {{ $res->name }}</option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                    <input type="hidden" id="tgid" name="tgid" value=""></td>
                                            </tr>
                                            <tr>
                                                <td>Reference Range</td>
                                                <td>
                                                    <input type="text" class="input-text" style="width: 80px;" id="refmin" name="refmin"> - 
                                                    <input type="text" class="input-text" style="width: 80px;" id="refmax" name="refmax"> 
                                                </td>
                                            </tr> 
                                            <tr>
                                                <td>Test Value Type </td>
                                                <td>
                                                    <select class="select-basic" id="vtype" name="vtype" onchange="selectVType()" style="width: 222px;">
                                                        <option value="0"></option> 
                                                        <option>Integer</option> 
                                                        <option>Decimal</option>
                                                        <option>String</option>
                                                        <option>Negative/Positive</option>
                                                        <option>Paragraph</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Character Count </td>
                                                <td>
                                                    <input id="vchcount" class="input-text" type="number" id="vchcount" name="vchcount" value="255" style="width: 200px;">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Value Range </td>
                                                <td>Min:<input id="vmin" class="input-text" type="number" name="vmin" size="3" step="any" style="width:48px" value="0"> 
                                                    Max : <input id="vmax" class="input-text" type="number" name="vmax" size="3" step="any" style="width:50px" value="255"></td>
                                            </tr>
                                            <tr>
                                                <td>Decimal Points </td>
                                                <td><input id="vdecis" class="input-text" type="number" id="vdecis" name="vdecis"  value="1" style="width: 200px;"></td>
                                            </tr>
                                            <tr>
                                                <td>Analyzer </td>
                                                <td>
                                                    <select class="select-basic" name="analyzer" id="analyzer" style="width: 222px;">
                                                        <option value="0"></option>
                                                        <?php
                                                        $Result = DB::select("select * from analyzers where Lab_lid = '" . $_SESSION['lid'] . "' and status='1'");
                                                        foreach ($Result as $res) {
                                                            ?>

                                                            <option value="{{ $res->anid }}"> {{ $res->name }}</option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Report Category </td>
                                                <td>
                                                    <select class="select-basic" name="tcat" id="tcat" style="width: 222px;">
                                                        <option value="0"></option>
                                                        <?php
                                                        $Result = DB::select("select * from testingcategory");
                                                        foreach ($Result as $res) {
                                                            ?>
                                                            <option value="{{ $res->tcid }}"> {{ $res->name }}</option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Specimen</td>
                                                <td>
                                                    <select class="select-basic" name="tinput" id="tinput" style="width: 222px;">
                                                        <option value="0"></option>
                                                        <?php
                                                        $Result = DB::select("select * from testinginput");
                                                        foreach ($Result as $res) {
                                                            ?>

                                                            <option value="{{ $res->tiid }}"> {{ $res->name }}</option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>View Normal Value</td>
                                                <td>
                                                    <input type="checkbox" id="viewnor" name="viewnor">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>View Analyzer</td>
                                                <td>
                                                    <input type="checkbox" id="viewana" name="viewana">
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Default Value</td>
                                                <td><input type="text" class="input-text" style="width: 200px;" id="defaultval" name="defaultval"/></td>
                                            </tr>

                                            <tr>
                                                <td>Test ID (LIS)</td>
                                                <td><input type="text" class="input-text" style="width: 200px;" id="lisis" name="lisis"/></td>
                                            </tr>

                                            <tr>
                                                <td>Order Number</td>
                                                <td><input type="number" class="input-text" style="width: 200px;" id="order" name="order"/></td>
                                            </tr>

                                            <tr>
                                                <td>Selectable Results</td>
                                                <td><input type="checkbox" id="selvals" name="selvals"></td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Age & Gender Reference</td>
                                                <td><input type="checkbox" id="awr" name="awr"></td>
                                            </tr>


                                            <tr>
                                                <td>&nbsp;</td>
                                                <td><input type="submit" class="btn" style="margin-left: 0; width: 220px;" name="submit" value="Add Testing">
                                                    <input type="submit" class="btn" style="margin-left: 0; width: 220px;" name="submit" id="updatebtn" value="Update Testing" disabled="disabled"></td>
                                            </tr>

                                        </table>

                                        <p style="color: #0015B0">{{ $msg or '' }}</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="tableHead">&nbsp;</p>
                    </td>                                       

                    <td style="border-left-style: solid; border-left-width: 1px; border-left-color: #0015B0; padding-left: 25px;" valign="top">
                        <p class="tableHead">Manage Material Consumption</p>
                        <table width="100%" border="0" cellpadding="2" cellspacing="0">                        
                            <tr>
                                <td>Material</td>
                                <td style="padding-left: 10px;">
                                    <select class="select-basic" id="matName" name="mat" id="matList" onChange="getMatID(value)" style="width: 220px;">
                                        <option></option>

                                        <?php
                                        $Result = DB::select("select * from Lab_has_materials a, materials b where a.materials_mid=b.mid and a.lab_lid='" . $_SESSION['lid'] . "' and a.status = '1' order by b.name ASC");
                                        foreach ($Result as $res) {
                                            ?>
                                            <option value="{{ $res->mid }}"> {{ $res->name }}</option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Value </td>
                                <td style="padding-left: 10px;"><input type="text" class="input-text" style="width: 198px;" id="matval" name="val" pattern="[0-9]{1,10}" title="Enter valid value!"></td>
                            </tr>
                            <tr>
                                <td>Unit </td>
                                <td style="padding-left: 10px;">
                                    <select class="select-basic" id="units" style="width: 220px;" name="units">
                                        <?php
                                        $Result = DB::select("select * from measurements");
                                        foreach ($Result as $res) {
                                            ?>
                                            <option value="{{ $res->msid }}"> {{ $res->name }}</option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>

                                </td>
                                <td style="padding-left: 10px;">
                                    <input type="button" class="btn" name="submit" onclick="addMaterial()" style="width: 220px; margin-left: 0px;" value="Add to Testing Consumption">
                                    <input type="hidden" name="matid" id="matid" value="">
                                    <input type="hidden" name="tid" id="tid" value="">
                                </td>
                            </tr>

                        </table>
                        <table>
                            <tr>
                                <td>
                                    <p style="color: #F00">NOTE: Please select unit which is have lower or Equals power to Stock value's Unit</p>
                                    <p style="color: #0015B0">{{ $msg or '' }}</p>
                                </td>
                            </tr>
                        </table>
                        <hr/>
                        <br/>
                        <p class="tableHead">Material Consumption For Selected Test</p>                             
                        <div id="testMats" class="testMats">

                            <table id="matTable" width="99%" border="1" cellspacing="0" cellpadding="0" class="TableWithBorder">


                            </table>

                        </div>

                        <br/>
                        <hr/>
                        <br/>

                        <p class="tableHead">Age and Gender wise Reference Ranges</p> 

                        <table border="0">                                
                            <tbody>
                                <tr>
                                    <td>Age Range</td>
                                    <td width='1'>:</td>
                                    <td>
                                        <select name="arfage" id="arfage" class="select-basic" style="width: 220px;">
                                            <?php
                                            $Result = DB::select("select * from age_range");
                                            foreach ($Result as $res) {


                                                if ($res->min / 365 >= 1) {
                                                    $agMinValue = number_format($res->min / 365);
                                                    $agMaxValue = number_format($res->max / 365);
                                                    $agUnit = "Years";
                                                } elseif ($res->min / 30 >= 1) {
                                                    $agMinValue = number_format($res->min / 30);
                                                    $agMaxValue = number_format($res->max / 30);
                                                    $agUnit = "Months";
                                                } else {
                                                    $agMinValue = $res->min;
                                                    $agMaxValue = $res->max;
                                                    $agUnit = "Days";
                                                }
                                                ?>
                                                <option value="{{ $res->id }}"> {{ $agMinValue or '' }} {{ $agUnit }} - {{ $agMaxValue or '' }} {{ $agUnit }}</option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Gender</td>
                                    <td width='1'>:</td>
                                    <td>
                                        <select name="arfgen" id="arfgen" class="select-basic" style="width: 220px;">
                                            <?php
                                            $Result = DB::select("select * from gender");
                                            foreach ($Result as $res) {
                                                ?>
                                                <option value="{{ $res->idgender }}">{{ $res->gender }}</option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Reference Range</td>
                                    <td width='1'>:</td>
                                    <td>
                                        <input type="text" id="arfmin" class="input-text" style="width: 79px;" /> - 
                                        <input type="text" id="arfmax" class="input-text" style="width: 79px;"/>
                                    </td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td width='1'></td>
                                    <td>
                                        <input type="button" id="btnarf" class="btn" style="width: 220px; margin-left: 0;" value="Add Reference" onclick="adRefRange()">
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <br/>

                        <u><i>Current References</i></u>

                        <br/>

                        <table border="1" width='100%' style="border-collapse: collapse;" id="agereftable">
                            
                        </table>

                        <br/>

                        <u><i>Edit Ranges</i></u>

                        <br/>

                        <table border="0">                                
                            <tbody>

                                <tr>
                                    <td>Age Range</td>
                                    <td width='1'>:</td>
                                    <td>
                                        <input type="text" id="arfagemin" class="input-text" style="width: 79px;" /> - 
                                        <input type="text" id="arfagemax" class="input-text" style="width: 79px;"/>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Age By</td>
                                    <td width='1'>:</td>
                                    <td>
                                        <select id="arfageby" class="select-basic" style="width: 220px;">
                                            <option>Years</option>
                                            <option>Months</option>
                                            <option>Days</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td width='1'></td>
                                    <td>
                                        <button id="btnarf" class="btn" style="width: 220px; margin: 0;">Add Age Range</button>
                                    </td>
                                </tr>
    <!--                                <tr>
                                    <td></td>
                                    <td width='1'></td>
                                    <td>
                                        <button id="btnarf" class="btn" style="width: 220px; margin: 0;">Update Age Range</button>
                                    </td>
                                </tr>-->
                            </tbody>
                        </table>

                        <br/>

                        <u><i>Current Age Ranges</i></u>

                        <br/>

                        <table border="1" width='100%' style="border-collapse: collapse; font-family: Arial;">
                            <tbody>
                                <tr>                                    
                                    <td>Range ID</td>                                      
                                    <td>Min. Value</td>                                      
                                    <td>Max. Value</td>                                     
                                    <!--<td></td>-->                                     
                                    <!--<td></td>-->                                     
                                </tr>

                                <?php
                                $Result = DB::select("select * from age_range");
                                foreach ($Result as $res) {

                                    $agValue = 0;
                                    if ($res->min / 365 >= 1) {
                                        $agMinValue = number_format($res->min / 365);
                                        $agMaxValue = number_format($res->max / 365);
                                        $agUnit = "Years";
                                    } elseif ($res->min / 30 >= 1) {
                                        $agMinValue = number_format($res->min / 30);
                                        $agMaxValue = number_format($res->max / 30);
                                        $agUnit = "Months";
                                    } else {
                                        $agMinValue = $res->min;
                                        $agMaxValue = $res->max;
                                        $agUnit = "Days";
                                    }
                                    ?>
                                    <tr>
                                        <td>{{ $res->id }}</td>
                                        <td>{{ $agMinValue }} {{ $agUnit }}</td>
                                        <td>{{ $agMaxValue }} {{ $agUnit }}</td>
                                        <!--<td width='70'><input type="button" class="btn" style="margin: 0;" value="Select" onclick="selectAgeRange('{{ $res->id }}')"></td>-->
                                        <!--<td width='70'><input type="button" class="btn" style="margin: 0;" value="Delete" onclick="deleteAgeRange('{{ $res->id }}')"></td>-->
                                    </tr>
                                    <?php
                                }
                                ?>

                            </tbody>
                        </table>


                    </td>
                </tr>
            </table>
        </form>

    </blockquote>
    <?php
}
?>
@stop
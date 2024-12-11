@extends('Templates/WiTemplate')
<?php
if (!isset($_SESSION)) {
    session_start();
}

date_default_timezone_set('Asia/Colombo');
?>
@section('title')
View Patients
@stop

@section('head')
<script type="text/javascript">
    window.onload = loadDate;



    function loadDate() {

        $("#othFilters").hide();

        var d = new Date();
        d.toLocaleString('en-US', {timeZone: 'Asia/Colombo'});

        document.getElementById('pdate').valueAsDate = d;
        document.getElementById('pdatex').valueAsDate = d;



        search();
    }

    function validate() {
        var sNo = document.getElementById('sNo').value;
        var fname = document.getElementById('fname').value;
        var lname = document.getElementById('lname').value;
        var check = true;
        var regex = /^[a-zA-Z]*$/;

        if (check) {
            search();
        }

    }



    var searchedDate;
    var DataOblect;


    var sno_forTests = "";
    var date_forTests = "";


    function search() {
        tableBody = "<tr class='viewTHead'>"
        + "<td width='100' height='28' class='fieldText'>Date</td>"
        + "<td width='200' height='28' class='fieldText'>Patient Name</td>"



        + "<td width='40' class='fieldText'>Gender</td>"
        + "<td width='80' class='fieldText'>Age</td>"
        + "<td width='100' class='fieldText'>Contact</td>"
        + "<td width='30' class='fieldText'>S.No</td>"
        + "<td width='50' class='fieldText' style='overflow: hidden;'>Test</td>"; 

        if ($('#more').is(":checked")) {
            tableBody += "<td width='100' class='fieldText'>Refby</td>";
        }


        tableBody += "<td width='30px' >Status</td>";

        if ($('#more').is(":checked")) {
            tableBody += "<td width='45px' >Price</td>";
        }


        tableBody += "<td width='30' ></td>"

        + "</tr>";
        var date = document.getElementById('pdate').value;
        var date2 = document.getElementById('pdatex').value;

        searchedDate = date;
        var sNo = document.getElementById('sNo').value;
        var fname = document.getElementById('fname').value;
        var lname = document.getElementById('lname').value;
        var type = document.getElementById('type').value;
        var refby = document.getElementById('refby').value;
        var tstate = document.getElementById('teststate').value;

        var more = "on";
        if (!$('#more').is(":checked")) {
            var more = "off";
        }


        var branchCode = "";
        if ($('#brcodex').length > 0) {
            branchCode = $('#brcodex').val();
        }

        var opt = $('option[value="' + $('#tgroup').val() + '"]');
        var selectedTest = opt.length ? opt.attr('id') : '%';

        if (document.getElementById('loadVal').value === "ok") {
            var url = "SearchPatientViewbulk?date=" + date + "&datex=" + date2 + "&sno=" + sNo + "&fname=" + fname + "&lname=" + lname + "&status=pending&type=" + type + "&refby=" + refby + "&testgroup=" + selectedTest + "&teststate=" + tstate + "&branchcode=" + branchCode + "&more=" + more;
        } else {
            var url = "SearchPatientViewbulk?date=" + date + "&datex=" + date2 + "&sno=" + sNo + "&fname=" + fname + "&lname=" + lname + "&type=" + type + "&refby=" + refby + "&testgroup=" + selectedTest + "&teststate=" + tstate + "&branchcode=" + branchCode + "&more=" + more;
        }

        $.ajax({
            type: 'POST',
            url: url,
            success: function (data) {
                data = JSON.parse(data);
                DataOblect = data;
                var xy = 0;
                for (var i = 0; i < data.length; i++) {
                    xy += 1;
                    var refbyID = data[i].refference_idref;
                    var refby = $("#refby option[value=" + refbyID + "]").text();

                    var x = data[i].status;

                    var statusx = x.substr(0, 1).toUpperCase() + x.substr(1);

                    var age_months = "";
                    if (data[i].months !== "0") {
                        age_months = "<td style='width: 30%'>" + data[i].months + "M</td>";
                    }

                    var age_days = "";
                    if (data[i].days !== "0") {
                        age_days = "<td style='width: 35%' bgcolor='#CCCCCC'>" + data[i].days + "D</td>";
                    }

                    var age_table = "<table style='width: 100%' class='ageTable'><tr><td style='width: 35%' bgcolor='#CCCCCC'>" + data[i].age + "Y</td>" + age_months + age_days + "</tr></table>"


                    var test_name = data[i].testname;

                    var test_code = data[i].testcode;



                    if (data[i].lab_lid === 19) {
                        if (test_name.includes("-")) {
                            test_name = test_name.split("-")[0];
                        }
                    }


                    if (!$('#more').is(":checked")) {

                        tableBody += "<tr class='phistr' style='cursor:pointer;'><td>" + data[i].date + "</td><td>" + data[i].initials + " " + data[i].fname + " " + data[i].lname + "</td><td>&nbsp;" + data[i].gender + "</td><td>" + age_table + "</td></td><td>&nbsp;" + data[i].tpno + "</td><td style='color:blue; font-weight:bold; size:14pt;' >&nbsp;" + data[i].sampleNo + "</td><td style='font-size:11pt;'>" + test_code + "</td><td>&nbsp;" + statusx + "</td>";
                    } else {

                        tableBody += "<tr class='phistr' style='cursor:pointer;'><td>" + data[i].date + "</td><td>" + data[i].initials + " " + data[i].fname + " " + data[i].lname + "</td><td>&nbsp;" + data[i].gender + "</td><td>" + age_table + "</td></td><td>&nbsp;" + data[i].tpno + "</td><td style='color:blue; font-weight:bold; size:14pt;'>&nbsp;" + data[i].sampleNo + "</td><td style='font-size:11pt;'>" + test_code + "</td><td style='font-size:11pt;'>&nbsp;" + refby + "</td><td>&nbsp;" + statusx + "</td><td align='right'>&nbsp;" + data[i].tgprice + "</td>";
                    } 

                    sno_forTests = data[i].sampleNo;
                    date_forTests = data[i].date;

                    if ($('#guestx').val()) {




                        if (data[i].status === "Accepted" || data[i].status === "Done") {

                        } else {

                        }


                    }



                    tableBody += "<td><input type='checkbox' style='margin:0px; background-color:#00cc00;' id=" + data[i].sampleNo + " name='sno' value=" + data[i].sampleNo + "#" + data[i].date + "></td></tr>"; 


                }
                document.getElementById('pdataTable').innerHTML = tableBody;
                $('#tablesummery').html("Sample Count : " + xy);

                $('#pdataTable input:checkbox').prop('checked', true);

                

                searchTests();

            } 
        });
    }

    function searchTests() {

        var tableBody = "";

        var date = date_forTests;
        var sNo = sno_forTests;

        var url = "SearchSampleByDtnSno?date=" + date + "&sno=" + sNo;

        $.ajax({
            type: 'POST',
            url: "SearchSampleByDtnSno",
            data: {'date': date, 'sno': sNo, '_token': $('input[name=_token]').val()},
            success: function (data) {

                $('#errormsg').html("");
                var res = data.split("/&&");

                document.getElementById('testD').innerHTML = res[1];

                $("input[name='printN']").hide();
                $("input[id='printListBtn']").hide();                
                $("tr[id='sptrrow']").remove();              
                $("input[id='btnsr']").remove();              
                $("input[id='btnao']").remove();              
                $("input[id='btnat']").remove();              
                $("input[name='reset']").remove();              

            }
        });

    }

    function WorkSheet(pid, id) {


        var arr = id.split("#");

        var sno = arr[0];
        var date = arr[1];

        var x = confirm("Do you want to accept the sample?");
        if (x) {
            // update sample status
            $.ajax({
                url: "acceptsample",
                type: 'POST',
                data: {'pid': pid, 'sno': sno, 'date': date, '_token': $('input[name=_token]').val()},
                success: function (result) {
                    var x = confirm("Do you want to print patient worksheet?");
                    if (x) {
                        //print worksheet

                        var win = window.open("patientworksheet/" + sno + "/" + date, '_blank');
                        win.print();
                        setTimeout(function () {
                            win.close();
                            search();
                        }, 5000);
                    }
                }
            });
        }
    }


    function addSample(pid) {
        window.location = "addpatientto?pid=" + pid;
    }

    function view(lpsid) {
        window.location = "viewOP?lpsid=" + lpsid;
    }

    function goto(sno) {
        window.location = "enterresults?pdate=" + searchedDate + "&psno=" + sno;
    }

    function selectAll() {
        $('#pdataTable input:checkbox').prop('checked', true);
    }

    function deselectAll() {
        $('#pdataTable input:checkbox').prop('checked', false);
    }

    function printPatientTable() {

        //hide buttons
        $('.btn').hide();

        var body = $("#pdataTable").html();
        var date = document.getElementById('pdate').value;
        var newWin = window.open('', 'Print-Window');
        newWin.document.open();
        newWin.document.write("<html><head><title>MLWS - Print</title><head><body onload='window.print()'><h2>MLWS Patient Details</h2><p>Date : " + date + "</p><div style='width:800px'><hr/><br/><table>" + body + "</table></div><br/><hr/><p style='font-size:12px' align='right'>Generated By MLWS. Powered by Appex Solutions. www.appexsl.com</p><style>table, td, th {border: 1px solid black;} table {border-collapse: collapse;}</style></body></html>");
        newWin.document.close();
        setTimeout(function () {
            newWin.close();
            $('.btn').show();
        }, 2000);

    }

    function setBranchCode() {
        var branchCode = "";
        if ($('#brcodex').val() === "ALL") {
            branchCode = "";
        } else {
            branchCode = $('#brcodex').val();
        }
        $('#sNo').val(branchCode);
    }

    function moreSettings() {
        if ($("#othFilters").is(":hidden")) {
            $("#othFilters").show();
        } else {
            $("#othFilters").hide();
        }

    }

    function enterResultBulk(){
        var selectedArray = [];

        $("input:checkbox[name=sno]:checked").each(function(){
            selectedArray.push($(this).val());
        });


        var x = confirm("Do you want to update results for selected samples?");

        if (x) {

            var resultVal = "";

            //get Test IDs
                var div = document.getElementById("testD");
                $(div).find('input:text, input:password, input:file, select, textarea')
                .each(function() {

                    resultVal += $(this).attr('name') + "###" + $(this).val() + "^^";
                    
                });

            resultVal = resultVal.slice(0, -2);


            // alert(selectedArray+" "+resultVal);

            $.ajax({
                url: "updateResultBulk",
                type: 'POST',
                data: {result:resultVal,samplenos: selectedArray},
                success: function (result) {
                    // alert(result);
                    alert("Results Updated!");
                    search();
                }
            });

        }


    }

    function enterDetailsBulk(){
        var selectedArray = [];

        $("input:checkbox[name=sno]:checked").each(function(){
            selectedArray.push($(this).val());
        });


        var x = confirm("Do you want to update date and time for selected samples?");

        if (x) {

            var dates_times = $("#regdate").val()+"#"+$("#regtime").val()+"#"+$("#fdate").val()+"#"+$("#ftime").val();

            // alert(dates_times);

            $.ajax({
                url: "updateDetailsBulk",
                type: 'POST',
                data: {datestimes:dates_times,samplenos: selectedArray},
                success: function (result) {
                    // alert(result);
                    alert("Details Updated!");
                    search();
                }
            });

        }


    }

    $(document).on('keydown keypress', '#sNo', function (e) {
        if (e.which === 13) {
            search();
        }
    });

    $(document).on('keydown keypress', '#fname', function (e) {
        if (e.which === 13) {
            search();
        }
    });

    $(document).on('keydown keypress', '#lname', function (e) {
        if (e.which === 13) {
            search();
        }
    });


</script>

<style>
#pdataTable tr:nth-child(even){background-color: white;}

#pdataTable tr:hover {background-color: lightgray;}

#pdataTable td{
    padding-left: 5px;
    padding-right: 5px;
}
</style>

@stop

@section('body')
<?php
if (isset($_SESSION['lid']) & isset($_SESSION['luid'])) {
    ?>
    <blockquote>
        <h3 class="pageheading">Bulk Enter Options</h3>
        <br/>

        <table border="0">

            <tbody>
                <tr>

                    <td>Date :<input type="date" name="date" id="pdate" class="input-text" style="width: 125px;"> <input type="date" name="datex" id="pdatex" class="input-text" style="width: 125px;"></td>


                    <td>Sample NO : 
                        <?php
                        if ($_SESSION["guest"] == null) {
                            $selectedBranchCode = "";
                        } else {
                            $selectedBranchCode = $_SESSION["userbranch"];
                        }
                        ?>

                        <?php
                        if ($_SESSION["guest"] == null) {
                            ?>
                            <input type="text" name="sampleNo"  class="input-text" id="sNo" style="width: 100px" pattern="[A-Za-z0-9]{1,10}" title="Minimum one charactor, Maximum 10 charactors and excluding symbols." value="<?php echo $selectedBranchCode; ?>">
                            <?php
                        } else {
                            ?>
                            <input type="text" name="sampleNo"  class="input-text" id="sNo" style="width: 100px" pattern="[A-Za-z0-9]{1,10}" title="Minimum one charactor, Maximum 10 charactors and excluding symbols." value="<?php echo $selectedBranchCode; ?>" disabled="disabled">
                            <?php
                        }
                        ?>
                    </td>


                    <td>
                        <?php
                        $result1x = DB::select("select * from Lab_features where lab_lid = '" . $_SESSION["lid"] . "' and features_idfeatures = (select idfeatures from features where name = 'Branch Handeling')");
                        if (!empty($result1x)) {
                            ?> 
                            Branch :  
                            <select class="select-basic" id="brcodex" style="width: 150px;" onchange="setBranchCode()"> 


                                <?php
                                if ($_SESSION["guest"] == null) {
                                    ?>
                                    <option value="ALL"></option> 
                                    <?php
                                    $rs = DB::select("select name, code from labbranches where lab_lid = '" . $_SESSION['lid'] . "'");
                                    foreach ($rs as $rsb) {
                                        ?>
                                        <option value="{{ $rsb->code }}">{{ $rsb->name }}</option>
                                        <?php
                                    }
                                } else {
                                    $rs = DB::select("select name, code from labbranches where lab_lid = '" . $_SESSION['lid'] . "' and code='" . $selectedBranchCode . "'");
                                    foreach ($rs as $rsb) {
                                        ?>
                                        <option value="{{ $rsb->code }}">{{ $rsb->name }}</option>
                                        <?php
                                    }
                                }
                                ?> 

                            </select>
                            <?php
                        }
                        ?>


                    </td>
                    <td> 
                        <input type="button" name="search" class="btn" id="search" value="Search" onclick="validate();" style="margin-right: 0px; margin-left: 0px; width: 100px; float: left;"> 
                        &nbsp;&nbsp;&nbsp; <div id="tablesummery" style="float: right; padding-top: 15px;"></div>
                    </td>

                    <td>
                        &nbsp;&nbsp;&nbsp; <input type="checkbox" id="more" name="more" onchange="moreSettings()"> More Options
                    </td>

                </tr>
            </tbody>
        </table>


        <table id="othFilters">
            <tr>


                <td>Patient Name :
                    <input type="text" name="searchfname" class="input-text" style="width: 100px" id="fname" pattern="[A-Za-z]{1,40}" title="Valid name excluding digits."></td>
                    <td>Contact : 
                        <input type="text" name="searchlname" class="input-text" style="width: 100px" id="lname" pattern="[A-Za-z]{1,40}" title="Valid name excluding digits."></td>
                        <td><?php if ($_SESSION["guest"] == null) {
                            ?>
                            Referred By : 
                            <select id="refby" class="select-basic" style="width: 150px;" class="select-basic">
                                <option value="0">All</option>
                                <?php
                                $refferenceResult = DB::select("Select * from refference where lid = '" . $_SESSION['lid'] . "' order by name");
                                foreach ($refferenceResult as $result) {
                                    ?>
                                    <option value="<?php echo $result->idref; ?>"><?php echo $result->name; ?></option>
                                    <?php
                                }
                                ?>
                            </select>

                        <?php } else { ?>
                            <input type="hidden" id="refby" value="0"/>
                        <?php } ?>    
                    </td>



                </tr>

                <tr>

                    <td>
                        Status : &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                        <select id="teststate" name="teststate" class="select-basic" style="width: 73px; margin-left: 5px;">
                            <option value="%">All</option>
                            <option value="pending">Pending</option>
                            <option value="Accepted">Accepted</option>
                            <option value="Done">Done</option>
                            <option value="Billed Only">Billed Only</option>
                            <option value="Not Collected">Not Collected</option>                        
                            <option value="Cancelled">Cancelled</option>

                        </select>
                    </td>
                    <td>
                        Testing : 
                        <input id="tgroup" list="testgroups" style="width: 100px; margin-left: 2px;" class="input-text"> 
                        <datalist id="testgroups">
                            <?php
                            $Result = DB::select("select c.name,c.tgid from test a, Lab_has_test b,Testgroup c where a.tid = b.test_tid and b.testgroup_tgid = c.tgid and b.lab_lid='" . $_SESSION['lid'] . "' group by c.name order by c.name");
                            foreach ($Result as $res) {
                                $tgid = $res->tgid;
                                $group = $res->name;
                                ?>
                                <option id="{{ $tgid }}" value="{{ $group }}"/>
                                <?php
                            }
                            ?>
                        </datalist>
                    </td>

                    <td>Type : &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp;
                        <select id="type" name="type" class="select-basic" style="width: 145px;">
                            <option></option>
                            <option>In</option>
                            <option>Out</option>
                        </select>
                    </td>


                </tr>
            </table>



            <div class="tableBody" style="height:400px; border-bottom: solid black 1px;">
                <form action="selectOP" method="POST">
                    <table style="font-family: Futura, 'Trebuchet MS', Arial, sans-serif; font-size: 13pt;" id="pdataTable" width="100%" border="0" cellspacing="2" cellpadding="0">
                        <tr class="viewTHead">
    <!--                        <td width="134" height="28" class="fieldText">First Name</td>
                        <td width="129" class="fieldText">Last Name</td>
                        <td width="65" class="fieldText">Gender</td>
                        <td width="154" class="fieldText">Age</td>
                        <td width="115" class="fieldText">Contact No</td>
                        <td width="229" class="fieldText">Address</td>
                        <td width="229" class="fieldText">Sample No</td>
                        <td width="229" class="fieldText">Ref.by</td>-->

                    </tr>
                </table> 
            </form> 
        </div>
        <br>
        <div style="border-bottom: solid black 1px;">

            <table>
                <tr>
                    <td>Update Values for selected Sample Numbers</td>
                    <td><input type="button" class="btn" value="Select All" onclick="selectAll()" /></td>
                    <td><input type="button" class="btn" value="Deselect All" onclick="deselectAll()" /></td>
                </tr>
            </table>

               <br>

            <table id="testD"> 

            </table> 

            
                    
            <input type="button" class="btn" value="Update Results" onclick="enterResultBulk()"> 
                
        </div>

        <br>


        <div style="border-bottom: solid black 1px;">
            
            Change Sample Dates and Times

            <br> 
            <br> 

            <table>
                <tr>
                    <td>Register Date / Time</td>
                    <td><input type="date" id="regdate" name="regdate" class="input-text" /></td>
                    <td><input type="time" id="regtime" name="regtime" class="input-text" /></td>

                </tr>

                <tr>
                    <td>Finished Date / Time</td>
                    <td><input type="date" id="fdate" name="fdate" class="input-text" /></td>
                    <td><input type="time" id="ftime" name="ftime" class="input-text" /></td>
                </tr>
            </table>

            <input type="button" class="btn" value="Update Sample Details" onclick="enterDetailsBulk()"> 

        <br>
        </div>

        <br>

        <input type="button" class="btn" id="printListBtn" value="Print Patient List" onclick="printPatientTable()">

        <?php
        if (isset($_GET['load'])) {
            ?>
            <input type="hidden" id="loadVal" name="loadVal" value="ok"/>
            <?php
        } else {
            ?>
            <input type="hidden" id="loadVal" name="loadVal" value=""/>
            <?php
        }
        ?>

        <?php if ($_SESSION["guest"] == null) { ?>
            <input type="hidden" id="guestx" value="false"/>
        <?php } else { ?>
            <input type="hidden" id="guestx" value="true"/>  
        <?php } ?>

    </blockquote> 
    <?php
}
?>


@stop



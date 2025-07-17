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


    window.onload = function () {
        const urlParams = new URLSearchParams(window.location.search);
        const sampleNo = urlParams.get('sampleNo');
        const idate = urlParams.get('idate');

        if (sampleNo) {
            document.getElementById('sNo').value = sampleNo;
        }
        if (idate) {
            document.getElementById('pdate').value = idate;
            document.getElementById('pdatex').value = idate;
        }

        if (sampleNo || idate) {
            validate(); // auto-trigger search if values exist
        }
    };




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
    function search() {
        tableBody = "<tr class='viewTHead'>"
                + "<td width='100' height='28' class='fieldText'>Date</td>"
                + "<td width='200' height='28' class='fieldText'>Patient Name</td>"


//                + "<td width='159' height='28' class='fieldText'>First Name</td>"
//                + "<td width='152' class='fieldText'>Last Name</td>"
                + "<td width='60' class='fieldText'>Gender</td>"
                + "<td width='130' class='fieldText'>Age</td>"
                + "<td width='130' class='fieldText'>Contact</td>"
                + "<td width='44' class='fieldText'>S.No</td>"
                + "<td width='44' class='fieldText'>Test</td>";

        if ($('#more').is(":checked")) {
            tableBody += "<td width='100' class='fieldText'>Refby</td>";
        }

//                + "<td width='45' class='fieldText'>Type</td>"
        tableBody += "<td width='45px' >Status</td>";

        if ($('#more').is(":checked")) {
            tableBody += "<td width='45px' >Price</td>";
        }


        tableBody += "<td width='30' ></td>"
                + "<td width='30' ></td>"
                + "<td width='30' ></td>"
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
            var url = "SearchPatientView?date=" + date + "&datex=" + date2 + "&sno=" + sNo + "&fname=" + fname + "&lname=" + lname + "&status=pending&type=" + type + "&refby=" + refby + "&testgroup=" + selectedTest + "&teststate=" + tstate + "&branchcode=" + branchCode + "&more=" + more;
        } else {
            var url = "SearchPatientView?date=" + date + "&datex=" + date2 + "&sno=" + sNo + "&fname=" + fname + "&lname=" + lname + "&type=" + type + "&refby=" + refby + "&testgroup=" + selectedTest + "&teststate=" + tstate + "&branchcode=" + branchCode + "&more=" + more;
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

//                    alert(data[i].lab_lid);

                    if (data[i].lab_lid === 19) {
                        if (test_name.includes("-")) {
                            test_name = test_name.split("-")[0];
                        }
                    }


                    if (!$('#more').is(":checked")) {
//                        tableBody += "<tr class='phistr' style='cursor:pointer;'><td>&nbsp;" + data[i].date + "</td><td>&nbsp;" + data[i].initials + " " + data[i].fname + " " + data[i].lname + "</td><td>&nbsp;" + data[i].gender + "</td><td><table style='width: 100%' class='ageTable'><tr><td style='width: 35%' bgcolor='#CCCCCC'>" + data[i].age + "Y</td><td style='width: 30%'>" + data[i].months + "M</td><td style='width: 35%' bgcolor='#CCCCCC'>" + data[i].days + "D</td></tr></table></td></td><td>&nbsp;" + data[i].tpno + "</td><td style='color:blue; font-weight:bold;'>&nbsp;" + data[i].sampleNo + "</td><td>&nbsp;" + data[i].testname + "</td><td>&nbsp;" + refby + "</td><td>&nbsp;" + data[i].type + "</td><td>&nbsp;" + statusx + "</td>";
                        tableBody += "<tr class='phistr' style='cursor:pointer;'><td>" + data[i].date + "</td><td>" + data[i].initials + " " + data[i].fname + " " + data[i].lname + "</td><td>&nbsp;" + data[i].gender + "</td><td>" + age_table + "</td></td><td>&nbsp;" + data[i].tpno + "</td><td style='color:blue; font-weight:bold; size:14pt;' >&nbsp;" + data[i].sampleNo + "</td><td style='font-size:11pt;'>" + test_name + "</td><td>&nbsp;" + statusx + "</td>";
                    } else {
//                        tableBody += "<tr class='phistr' style='cursor:pointer;'><td>&nbsp;" + data[i].date + "</td><td>&nbsp;" + data[i].initials + " " + data[i].fname + " " + data[i].lname + "</td><td>&nbsp;" + data[i].gender + "</td><td><table style='width: 100%' class='ageTable'><tr><td style='width: 35%' bgcolor='#CCCCCC'>" + data[i].age + "Y</td><td style='width: 30%'>" + data[i].months + "M</td><td style='width: 35%' bgcolor='#CCCCCC'>" + data[i].days + "D</td></tr></table></td></td><td>&nbsp;" + data[i].tpno + "</td><td style='color:blue; font-weight:bold;'>&nbsp;" + data[i].sampleNo + "</td><td>&nbsp;" + data[i].testname + "</td><td>&nbsp;" + refby + "</td><td>&nbsp;" + data[i].type + "</td><td>&nbsp;" + statusx + "</td><td align='right'>&nbsp;" + data[i].tgprice + "</td>";
                        tableBody += "<tr class='phistr' style='cursor:pointer;'><td>" + data[i].date + "</td><td>" + data[i].initials + " " + data[i].fname + " " + data[i].lname + "</td><td>&nbsp;" + data[i].gender + "</td><td>" + age_table + "</td></td><td>&nbsp;" + data[i].tpno + "</td><td style='color:blue; font-weight:bold; size:14pt;'>&nbsp;" + data[i].sampleNo + "</td><td style='font-size:11pt;'>" + test_name + "</td><td style='font-size:11pt;'>&nbsp;" + refby + "</td><td>&nbsp;" + statusx + "</td><td align='right'>&nbsp;" + data[i].tgprice + "</td>";
                    } 

                    if ($('#guestx').val()) {
                        tableBody += "<td width='30'><input type='button' class='btn' style='margin:0px;' name='submit' value='View' onclick='view(" + data[i].lpsid + ")'></td>";

                        if (data[i].lab_lid !== "6") {
//                            tableBody += "<td><input type='button' class='btn' style='margin:0px;' name='submit' value='Add Sample' onclick='addSample(" + data[i].pid + ")'></td>";

                            if (data[i].status === "Accepted" || data[i].status === "Done") {
                                tableBody += "<td><input type='button' class='btn' id='" + data[i].sampleNo + "#" + data[i].date + "' style='margin:0px; background-color:#6D99FE;' name='submit' value='ACCEPT' onclick='WorkSheet(" + data[i].pid + ",id)'></td>";
                            } else {
                                tableBody += "<td><input type='button' class='btn' id='" + data[i].sampleNo + "#" + data[i].date + "' style='margin:0px; background-color:#FEE87A;' name='submit' value='ACCEPT' onclick='WorkSheet(" + data[i].pid + ",id)'></td>";
                            }

                        } else {

                            if (data[i].status === "Accepted" || data[i].status === "Done") {
                                tableBody += "<td><input type='button' class='btn' id='" + data[i].sampleNo + "#" + data[i].date + "' style='margin:0px; background-color:#6D99FE;' name='submit' value='ACCEPT' onclick='WorkSheet(" + data[i].pid + ",id)'></td>";
                            } else {
                                tableBody += "<td><input type='button' class='btn' id='" + data[i].sampleNo + "#" + data[i].date + "' style='margin:0px; background-color:#FEE87A;' name='submit' value='ACCEPT' onclick='WorkSheet(" + data[i].pid + ",id)'></td>";
                            }
//                            tableBody += "<td></td>";
                        }
                    }

                    tableBody += "<td><input type='button' class='btn' style='margin:0px; background-color:#00cc00;' id=" + data[i].sampleNo +"#"+ data[i].date + " value='Results' onclick='goto(id)'></td></tr>";
//                alert(tableBody);
                }
                document.getElementById('pdataTable').innerHTML = tableBody;
                $('#tablesummery').html("Sample Count : " + xy);
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

        var snox = sno.split("#")[0];
        var date = sno.split("#")[1];

        window.location = "enterresults?pdate=" + date + "&psno=" + snox;
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
        <h3 class="pageheading">View Patients</h3>
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



        <div class="tableBody" style="height:500px">
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
        <input type="button" class="btn" value="Print Patient List" onclick="printPatientTable()">

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



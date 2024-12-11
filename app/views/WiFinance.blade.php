@extends('Templates/WiTemplate')
<?php
if (isset($_SESSION)) {
    
} else {
    session_start();
}
?>
@section('title')
Finance Management
@stop

@section('head')
<script src="{{ asset('JS/chart.js') }}"></script>
<script type="text/javascript">
window.onload = function () {
    document.getElementById('genrep').disabled = true;
};

var xmlHttp;
function createXMLHttpRequest() {
    if (window.XMLHttpRequest) {
        xmlHttp = new XMLHttpRequest();
    } else if (window.ActiveXObject) {
        xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
    } else {
        alert("Please update your Browser!!!");
    }

}

function finViewSub(Number) {
    var eleID = "finSubPanal" + Number;
    var height = document.getElementById(eleID).offsetHeight;

    if (height === 0) {
        document.getElementById(eleID).style.height = "110px";
    } else {
        document.getElementById(eleID).style.height = "0px";
    }

}

function finView(Number) { // numbers are sub buttons
    document.getElementById('finDetails').innerHTML = "";

    if (Number === 1 || Number === 2 || Number === 3) {

        var optionDiv = document.getElementById('optionsBar');
        optionDiv.innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;Select a Date : &nbsp;&nbsp;&nbsp;&nbsp;";

        var dateField = document.createElement("input");
        dateField.type = "date";
        dateField.id = "option";
        dateField.name = "date";
        dateField.className = "input-text";

        dateField.addEventListener('change',
                function () {
                    finViewDetail(Number);
                },
                false
                );
        
        var dateField2 = document.createElement("input");
        dateField2.type = "date";
        dateField2.id = "optiony";
        dateField2.name = "datey";
        dateField2.className = "input-text";

        dateField2.addEventListener('change',
                function () {
                    finViewDetail(Number);
                },
                false
                );
        var hidden = document.createElement("input");
        hidden.type = "hidden";
        hidden.id = "option2";
        hidden.value = "";

        optionDiv.appendChild(dateField);        
        optionDiv.appendChild(dateField2);

        optionDiv.appendChild(hidden);

        var lineBreak1 = document.createElement("br");
        optionDiv.appendChild(lineBreak1);

        document.getElementById("option").valueAsDate = new Date();        
        document.getElementById("optiony").valueAsDate = new Date();

        finViewDetail(Number);

    } else if (Number === 4 || Number === 5 || Number === 6) {

        var optionDiv = document.getElementById('optionsBar');
        optionDiv.innerHTML = "Select a Year and Month : ";

        var yearField = document.createElement("select");
        yearField.id = "option2";
        yearField.name = "year";
        yearField.className = "select-basic";

        yearField.options[0] = new Option("2017");
        yearField.options[1] = new Option("2018");
        yearField.options[2] = new Option("2019");        
        yearField.options[3] = new Option("2020");

        yearField.addEventListener('change',
                function () {
                    finViewDetail(Number);
                },
                false
                );

        var monthField = document.createElement("select");
        monthField.id = "option";
        monthField.name = "month";
        monthField.className = "select-basic";

        monthField.addEventListener('change',
                function () {
                    finViewDetail(Number);
                },
                false
                );

        monthField.options[0] = new Option("January", "1");
        monthField.options[1] = new Option("February", "2");
        monthField.options[2] = new Option("March", "3");
        monthField.options[3] = new Option("April", "4");
        monthField.options[4] = new Option("May", "5");
        monthField.options[5] = new Option("June", "6");
        monthField.options[6] = new Option("July", "7");
        monthField.options[7] = new Option("August", "8");
        monthField.options[8] = new Option("September", "9");
        monthField.options[9] = new Option("October", "10");
        monthField.options[10] = new Option("November", "11");
        monthField.options[11] = new Option("December", "12");

        optionDiv.appendChild(yearField);
        optionDiv.appendChild(monthField);
        finViewDetail(Number);

    } else if (Number === 7 || Number === 8 || Number === 9) {

        var optionDiv = document.getElementById('optionsBar');
        optionDiv.innerHTML = "Select a Year : ";


        var yearField = document.createElement("select");
        yearField.id = "option";
        yearField.name = "year";
        yearField.className = "select-basic";
        yearField.addEventListener('change',
                function () {
                    finViewDetail(Number);
                },
                false
                );

        loadYears();

        optionDiv.appendChild(yearField);

        var hidden = document.createElement("input");
        hidden.type = "hidden";
        hidden.id = "option2";
        hidden.value = "";
        optionDiv.appendChild(hidden);
        //finViewDetail(Number);

    } else if (Number === 11 || Number === 12) {
        //date one
        var optionDiv = document.getElementById('optionsBar');
        optionDiv.innerHTML = "Date Range : ";


        var dateField = document.createElement("input");
        dateField.type = "date";
        dateField.id = "option";
        dateField.name = "date";
        dateField.className = "input-text";

        dateField.addEventListener('change',
                function () {
                    finViewDetail(Number);
                },
                false
                );
        var hidden = document.createElement("input");
        hidden.type = "hidden";
        hidden.id = "option2";
        hidden.value = "";

        optionDiv.appendChild(dateField);
        optionDiv.appendChild(hidden);

        document.getElementById("option").valueAsDate = new Date();

        //date two

        var dateField2 = document.createElement("input");
        dateField2.type = "date";
        dateField2.id = "optiondd2";
        dateField2.name = "date2";
        dateField2.className = "input-text";

        dateField2.addEventListener('change',
                function () {
                    finViewDetail(Number);
                },
                false
                );
        var hiddend2 = document.createElement("input");
        hiddend2.type = "hidden";
        hiddend2.id = "optiondh2";
        hiddend2.value = "";

        optionDiv.appendChild(dateField);
        optionDiv.appendChild(dateField2);
        optionDiv.appendChild(hidden);
        optionDiv.appendChild(hiddend2);

        document.getElementById("option").valueAsDate = new Date();
        document.getElementById("optiondd2").valueAsDate = new Date();


        //test select field
        var tsSelect = document.createElement("select");
        tsSelect.id = "tscode";
        tsSelect.className = "select-basic";
        tsSelect.style = "width:200px";

        var option = document.createElement("option");
        option.value = "All";
        option.innerHTML = "All";
        tsSelect.appendChild(option);

        tsSelect.addEventListener('change',
                function () {
                    $('#tscode1').val(tsSelect.value);
                    $('#tscode2').val(tsSelect.value);
                    GenBrCode = $('#tscode1').val();
                    finViewDetail(Number);
                },
                false
                );

        $.ajax({
            type: 'POST',
            url: "getLabTests",
            data: {'_token': $('input[name=_token]').val()},
            success: function (data) {
                data = JSON.parse(data);

                for (var i = 0; i < data.length; ++i) {
                    var option = document.createElement("option");
                    option.value = data[i].tgid;
                    option.innerHTML = data[i].name;

                    tsSelect.appendChild(option);
                }

                var tsp = document.createElement("span");
                tsp.innerHTML = " Test Group :";
                tsp.style = "margin-left:20px;";

                optionDiv.appendChild(tsp);
                optionDiv.appendChild(tsSelect);
            }
        });
        //

        finViewDetail(Number);

    }
    // else if (Number === 12) {

    //     var options = "Date From <input type='date' id='csdateone' class='input-text' />";
    //     var options += " Date to <input type='date' id='csdatetwo' class='input-text' />";

    //     $("#optionsBar").html(options);

    // }

    //make referance select box
    if (Number === 1 || Number === 4 || Number === 7 || Number === 11 || Number === 12) {
        var refSelect = document.createElement("select");
        refSelect.id = "refid";
        refSelect.className = "select-basic";
        refSelect.style = "width:150px";


        refSelect.addEventListener('change',
                function () {
                    $('#refhid').val(refSelect.value);
                    finViewDetail(Number);
                },
                false
                );

        $.ajax({
            type: 'POST',
            url: "getLabRefferences",
            data: {'_token': $('input[name=_token]').val()},
            success: function (data) {
                data = JSON.parse(data);

                var option = document.createElement("option");
                option.value = "0";
                option.innerHTML = "All";
                refSelect.appendChild(option);

                var option = document.createElement("option");
                option.value = "null";
                option.innerHTML = "None";
                refSelect.appendChild(option);

                for (var i = 0; i < data.length; ++i) {
                    var option = document.createElement("option");
                    option.value = data[i].idref;
                    option.innerHTML = data[i].name;

                    refSelect.appendChild(option);
                }

                var refp = document.createElement("span");
                refp.innerHTML = " Reference : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='text' id='refnametext' onkeyup='loadrefs()'>"; 
                refp.style = "margin-left:20px;";

                optionDiv.appendChild(refp);
                optionDiv.appendChild(refSelect);

                var lineBreak2 = document.createElement("br");
                optionDiv.appendChild(lineBreak2);
            }
        });
    }

    var GenBrCode;
    //make branch code select box
    if ($('#bractive').length > 0) {
        //if(Number === 1 || Number === 4 || Number === 7){ For incomes only
        if (true) {
            var brSelect = document.createElement("select");
            brSelect.id = "brcode";
            brSelect.className = "select-basic";
            brSelect.style = "width:100px";




            brSelect.addEventListener('change',
                    function () {
                        $('#brcode1').val(brSelect.value);
                        $('#brcode2').val(brSelect.value);
                        GenBrCode = $('#brcode1').val();
                        finViewDetail(Number);
                    },
                    false
                    );

            $.ajax({
                type: 'POST',
                url: "getLabBranches",
                data: {'_token': $('input[name=_token]').val()},
                success: function (data) {
                    data = JSON.parse(data);

                    var option = document.createElement("option");
                    option.value = "All";
                    option.innerHTML = "All";
                    brSelect.appendChild(option);

                    var option = document.createElement("option");
                    option.value = "None";
                    option.innerHTML = "None";
                    brSelect.appendChild(option);

                    for (var i = 0; i < data.length; ++i) {
                        var option = document.createElement("option");
                        option.value = data[i].code;
                        option.innerHTML = data[i].name;

                        brSelect.appendChild(option);
                    }

                    brSelect.value = "None";
                    $('#brcode1').val(brSelect.value);
                    $('#brcode2').val(brSelect.value);
                    GenBrCode = $('#brcode1').val();
                    finViewDetail(Number);

                    var brp = document.createElement("span");
                    brp.innerHTML = " Branch : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                    brp.style = "margin-left:20px;";

                    optionDiv.appendChild(brp);
                    optionDiv.appendChild(brSelect);

                    var lineBreak3 = document.createElement("br");
                    optionDiv.appendChild(lineBreak3);

                    if ($('#empbranch').length > 0) {
                        if ($('#empbranch').val() === "Main") {
                            $('#brcode').val("None");
                        } else {
                            $('#brcode').val($('#empbranch').val());
                        }

                        brSelect.disabled = "disabled";

                        $('#brcode1').val(brSelect.value);
                        $('#brcode2').val(brSelect.value);
                        GenBrCode = $('#brcode1').val();
                        finViewDetail(Number);
                    }
                }
            });
        }
    }

    // Payment method
    var payMethodSelect = document.createElement("select");
        payMethodSelect.id = "payMethod";
        payMethodSelect.className = "select-basic";
        payMethodSelect.style = "width:150px";

        payMethodSelect.addEventListener('change',
        function () {
            $('#paymentMethod').val(payMethodSelect.value);
            
        },
        false
        );

        $.ajax({
        type: 'POST',
        url: "getPaymentMethods",
        data: {'_token': $('input[name=_token]').val()},
        success: function (data) {
            data = JSON.parse(data);

            var option = document.createElement("option");
            option.value = "All";
            option.innerHTML = "All";
            payMethodSelect.appendChild(option);

            for (var i = 0; i < data.length; ++i) {
                var option = document.createElement("option");
                    // option.value = data[i].uid;
                    option.value = data[i].name;
                    option.innerHTML = data[i].name;

                    payMethodSelect.appendChild(option);
                }

                var payMethodPin = document.createElement("span");
                payMethodPin.innerHTML = " Payment Method :";
                payMethodPin.style = "margin-left:20px;";

                optionDiv.appendChild(payMethodPin);
                optionDiv.appendChild(payMethodSelect);

                var lineBreak4 = document.createElement("br");
                optionDiv.appendChild(lineBreak4);
                
            }
        });

        // payment status

        var payStatusSelect = document.createElement("select");
        payStatusSelect.id = "payStatus";
        payStatusSelect.className = "select-basic";
        payStatusSelect.style = "width:150px";

        payStatusSelect.addEventListener('change',
        function () {
            $('#paymentStatus').val(payStatusSelect.value);
            
        },
        false
        );

        var option = document.createElement("option");
        option.value = "All";
        option.innerHTML = "All";
        payStatusSelect.appendChild(option);

        var option2 = document.createElement("option");
        option2.value = "fullPaid";
        option2.innerHTML = "Full Paid";
        payStatusSelect.appendChild(option2);

        var option3 = document.createElement("option");
        option3.value = "halfPaid";
        option3.innerHTML = "Half Paid";
        payStatusSelect.appendChild(option3);

        var option4 = document.createElement("option");
        option4.value = "notPaid";
        option4.innerHTML = "Not Paid";
        payStatusSelect.appendChild(option4);

        var payStatusPin = document.createElement("span");
        payStatusPin.innerHTML = " Payment Status : &nbsp;";
        payStatusPin.style = "margin-left:20px;";



        optionDiv.appendChild(payStatusPin);
        optionDiv.appendChild(payStatusSelect);
       
        var lineBreak5 = document.createElement("br");
        optionDiv.appendChild(lineBreak5);
        
}

var no;
function finViewDetail(Number) {

    createXMLHttpRequest();
    no = Number;
    var option = document.getElementById('option').value;    
    var optiony = document.getElementById('optiony').value;
    var option2 = document.getElementById('option2').value;


    var refID = 0;
    if ($('#refid').length) {
        refID = $('#refid').val();
    }

    if (refID === undefined) {
        refID = "All";
    }


    var branch = "";
    if ($('#brcode').val() !== "ALL") {
        branch = $('#brcode').val();
    }

    if (branch === undefined) {
        branch = "None";
    }

    var dated2 = 0;
    if ($('#optiondd2').length) {
        dated2 = $('#optiondd2').val();
    } else {
        dated2 = "None";
    }

    var tsvalue = "%";
    if ($('#tscode').length) {
        tsvalue = $('#tscode').val();
    } else {
        tsvalue = "";
    }


    var url = "financecontroller?RID=" + Number + "&option=" + option + "&optiony=" + optiony + "&option2=" + option2 + "&ref=" + refID + "&brcode=" + branch + "&dated2=" + dated2 + "&test=" + tsvalue;
    xmlHttp.open("POST", url, true);
    xmlHttp.send();

    xmlHttp.onreadystatechange = function () {
        if (xmlHttp.readyState === 3) {
            document.getElementById('finDetails').innerHTML = "<p style='color:red'>Loading...</p>";
        }
        if (xmlHttp.readyState === 4 && xmlHttp.status === 200) {
            if (JSON.stringify(xmlHttp.responseText).includes("#/#")) {
                var arr = JSON.stringify(xmlHttp.responseText).split("#/#");
                document.getElementById('finDetails').innerHTML = arr[0] + "</br></br>" + arr[1];
                loadChart();
            } else {
                document.getElementById('finDetails').innerHTML = xmlHttp.responseText;
            }



            if (Number === 3 | Number === 6 | Number === 9) {
                document.getElementById('genrep').disabled = true;
            } else {
                document.getElementById('genrep').disabled = false;
            }



        }
    };
}

function loadYears() {

    createXMLHttpRequest();
    var url = "financecontroller?RID=10";
    xmlHttp.open("POST", url, true);
    xmlHttp.send();

    xmlHttp.onreadystatechange = function () {
        if (xmlHttp.readyState === 4 && xmlHttp.status === 200) {
            document.getElementById('option').innerHTML = xmlHttp.responseText;

        }
    };
}

function genReport() {
    var option = document.getElementById('option').value;
    var option2 = document.getElementById('option2').value;    
    var optiony = document.getElementById('optiony').value;
    
//    alert(option +" "+ optiony); 
   
    document.getElementById('type').value = no;
    document.getElementById('optionx').value = option;
    document.getElementById('option2x').value = optiony;
    
    $("#optiony").val(optiony);
    
    document.getElementById('optiony').value = optiony;    
    document.getElementById('optiony2').value = optiony;


//    alert(document.getElementById('optiony').value); 

    document.getElementById('brcode1').value = GenBrCode;

}

function genDetailedReport() {
    var option = document.getElementById('option').value;
    var option2 = document.getElementById('option2').value;    
    var optiony = document.getElementById('optiony').value;
    
//    alert(option +" "+ optiony); 
   
    document.getElementById('type').value = no;
    document.getElementById('optionx').value = option;
    document.getElementById('option2x').value = optiony;
    
    $("#optiony").val(optiony);
    
    document.getElementById('optiony').value = optiony;    
    document.getElementById('optiony2').value = optiony;


//    alert(document.getElementById('optiony').value); 

    document.getElementById('brcode1').value = GenBrCode;

}

function genReportPaymentSummary() {
    var option = document.getElementById('option').value;
    var option2 = document.getElementById('option2').value;    
    var optiony = document.getElementById('optiony').value;
   

    document.getElementById('type2').value = no;
    document.getElementById('optionx2').value = option;
    document.getElementById('option2x2').value = option2;    
    document.getElementById('optiony2').value = optiony;

    document.getElementById('brcode2').value = GenBrCode;

}

function finViewDisabled() {
    alert("This feature is not available in Basic Package!");
}

function  loadChart() {
    var ctx = document.getElementById("finChart").getContext('2d');

    var dates = document.getElementById("finChartDetails").innerHTML.split("#-#")[0].split("#");
    var vals = document.getElementById("finChartDetails").innerHTML.split("#-#")[1].split("#");

    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                    label: '# Test Count',
                    data: vals,
                    backgroundColor: [
                        'rgba(51, 102, 255, 0.3)',
                        'rgba(51, 102, 255, 0.3)',
                        'rgba(51, 102, 255, 0.3)',
                        'rgba(51, 102, 255, 0.3)',
                        'rgba(51, 102, 255, 0.3)',
                        'rgba(51, 102, 255, 0.3)'
                    ],
                    borderColor: [
                        'rgba(0, 32, 128,1)',
                        'rgba(0, 32, 128, 1)',
                        'rgba(0, 32, 128, 1)',
                        'rgba(0, 32, 128, 1)',
                        'rgba(0, 32, 128, 1)',
                        'rgba(0, 32, 128, 1)'
                    ],
                    borderWidth: 1
                }]
        },
        options: {
            scales: {
                yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
            }
        }
    });
}

function loadrefs(){
    
}

</script>

@stop
@section('body')



<blockquote>
    <h2 class="pageheading">Financial Reports</h2>

    <?php
    $branchview = false;
    $result1x = DB::select("select * from privillages where user_uid = (select user_uid from labUser where luid = '" . $_SESSION["luid"] . "') and options_idoptions = (select idoptions from options where name = 'Branch Finance View')");
    if (!empty($result1x)) {
        $branchview = true;
        ?> 
        <input type="hidden" id="brview">
        <?php
    } else { 

        $result1x2 = DB::select("select branch from labUser where luid = '" . $_SESSION["luid"] . "'");
        foreach ($result1x2 as $item) {
            ?>    
            <input type="hidden" id="empbranch" value="<?php echo $item->branch; ?>">
            <?php
        }
    }
    ?>

    <table width="1200">
        <tr valign="top">
            <td height="21">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr valign="top">
            <td width="253" height="365">

                <div id="finPanalHead1" class="finPanalHead" onClick="finViewSub(1)">Summeries</div>
                <div class="finSubPanal" id="finSubPanal1">
                    <div class="finPanalSubHead" onClick="finView(1)">Income and Expenses Summery</div>
                    <!--<div class="finPanalSubHead" onClick="finView(2)">Day Outcome Summery</div>
                        <div class="finPanalSubHead" onClick="finView(3)">Day Balance</div>-->

                    <!--<div class="finPanalSubHead" onClick="finViewDisabled()">Day Outcome Summery</div>-->
                    <!--<div class="finPanalSubHead" onClick="finViewDisabled()">Day Balance</div>-->
                </div>

                <?php // if ($branchview) { ?>
<!--                    <div id="finPanalHead2" class="finPanalHead" onClick="finViewSub(2)">Month Summery</div>
                    <div class="finSubPanal" id="finSubPanal2">
                        <div class="finPanalSubHead" onClick="finView(4)">Month Income Summery</div>
                        <div class="finPanalSubHead" onClick="finView(5)">Month Outcome Summery</div>
                            <div class="finPanalSubHead" onClick="finView(6)">Month Balance</div>

                        <div class="finPanalSubHead" onClick="finViewDisabled()">Month Outcome Summery</div>
                        <div class="finPanalSubHead" onClick="finViewDisabled()">Month Balance</div>
                    </div>-->

<!--                    <div id="finPanalHead3" class="finPanalHead" onClick="finViewSub(3)">Year Summery</div>
                    <div class="finSubPanal" id="finSubPanal3">
                        <div class="finPanalSubHead" onClick="finView(7)">Year Income Summery</div>
                                            <div class="finPanalSubHead" onClick="finView(8)">Year Outcome Summery</div>
                                            <div class="finPanalSubHead" onClick="finView(9)">Year Balance</div>

                        <div class="finPanalSubHead" onClick="finViewDisabled()">Year Outcome Summery</div>
                        <div class="finPanalSubHead" onClick="finViewDisabled()">Year Balance</div>
                    </div>-->

                    <div id="finPanalHead4" class="finPanalHead" onClick="finView(11)">Test Counts</div>
                    <div id="finPanalHead5" class="finPanalHead" onClick="finView(12)">Costing Summery</div>

                <?php // } ?>

            </td>
            <td width="20">&nbsp;</td>
            <td width="662">
                <!-- <div class="optionsBar" id="optionsBar" style="display:flex;flex-direction: column;"></div> -->
                <div class="optionsBar" id="optionsBar"></div>
                <div style="font-family: Futura, 'Trebuchet MS', Arial, sans-serif; font-size: 13pt; overflow-y: scroll;" class="finDetails" id="finDetails">

                </div>


                <form action="financeReport" method="POST" target="_blank"> 
                    <input type="hidden" id="type" name="RID" value="">
                    <input type="hidden" id="optionx" name="option" value="">                    
                    <input type="hidden" id="optiony" name="optiony" value=""> 
                    <input type="hidden" id="option2x" name="option2" value=""> 
                    <input type="hidden" id="refhid" name="ref" value="0">
                    <input type="hidden" id="brcode1" name="brcode" value="0">
                    <input type="hidden" id="tscode1" name="tscode" value="0">
                    <input type="hidden" id="paymentMethod" name="paymentMethod" value="0">
                    <input type="hidden" id="paymentStatus" name="paymentStatus" value="0">
                    <input type="submit" id="genrep" name="submit" class="btn" style="margin-left: 0px; width: 220px; text-align: left;" value="Genarate Report" onclick="genReport()"/>
                    <input type="submit" id="genrepdl" name="submit" class="btn" style="margin-left: 0px; width: 220px; text-align: left;" value="Genarate Detailed Report" onclick="genDetailedReport()"/>
                    <input type="submit" id="genrepdl" name="submit" class="btn" style="margin-left: 0px; width: 220px; text-align: left;" value="Genarate Patient Details" onclick="genDetailedReport()"/>
                </form>
                <form action="ipsummaryReport" method="POST" target="_blank"> 
                    <input type="hidden" id="type2" name="RID" value="">
                    <input type="hidden" id="optionx2" name="option" value="">
                    <input type="hidden" id="optiony2" name="optiony2" value=""> 
                    <input type="hidden" id="option2x2" name="option2" value="">
                    <input type="hidden" id="refhid" name="ref" value="0">
                    <input type="hidden" id="brcode2" name="brcode" value="0">
                    <input type="hidden" id="tscode2" name="tscode" value="0">
                    <input type="submit" id="genrep2" name="submit2" class="btn" style="margin-left: 0px; width: 220px; text-align: left;" value="Invoice Payment Summary" onclick="genReportPaymentSummary()"/>
                    <input type="submit" id="genrep2" name="submit2" class="btn" style="margin-left: 0px; width: 220px; text-align: left;" value="Due Payments Report" onclick="genReportPaymentSummary()"/>
                </form>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
    </table>

    <?php
    $result1x = DB::select("select * from Lab_features where lab_lid = '" . $_SESSION["lid"] . "' and features_idfeatures = (select idfeatures from features where name = 'Branch Handeling')");
    if (!empty($result1x)) {
        ?> 
        <input type="hidden" id="bractive">
    <?php } ?>





    </p>
</blockquote>
@stop

@extends('Templates/WiTemplate')
<?php
if (session_id() == '') {
    session_start();
}
?>
@section('title')
Patient Details
@stop

@section('head')
<script src="{{ asset('JS/chart.js') }}"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>

<script type="text/javascript">
var xmlHttp;

function load() {
    document.getElementById('pdate').valueAsDate = new Date();
    dateChangeEvent();


}

$(document).ready(function () {
    $('#chart_window').hide();
    $('#image_for_crop').hide();

    $('#chart_window').center();
});


jQuery.fn.center = function () {
    this.css("position", "absolute");
    this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2) +
            $(window).scrollTop()) + "px");
    this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) +
            $(window).scrollLeft()) + "px");
    return this;
}

var chart_x;
var chart_y;

function dateChangeEvent(ele) {

    var date = document.getElementById('pdates').value;

    if (ele === 0) {
        document.getElementById('pdates2').value = date;
    }

    var date2 = document.getElementById('pdates2').value;

    var lid = document.getElementById('lid').value;
    var pid = document.getElementById('pid').value;
    var test_group = document.getElementById('pstest').value;

    var url = "selectPTestbyDate";

    $.ajax({
        url: url,
        type: 'POST',
        data: {'lid': lid, 'pid': pid, 'testg': test_group, 'date': date, 'date2': date2, '_token': $('input[name=_token]').val()},
        success: function (result) {

            var arr = result.split("##//##");
            document.getElementById('testFieldsTR').innerHTML = arr[0];

            chart_x = arr[1];
            chart_y = arr[2];
            ref_min = arr[3];
            ref_max = arr[4];
            test_mes = arr[5];
        }
    });
}

var clicked;
function selectBtn() {

    if (clicked === "remove") {
        var r = confirm("Dou you want to Delete this patient?");
        if (r == true) {
            return true;
        } else {
            return false;
        }
    }
}

function enterTotal(value) {
    var gtotele = document.getElementById('gtot');
    var paymentele = document.getElementById('payment');
    var dueele = document.getElementById('due');

    var dis = parseFloat(document.getElementById('disPre').value);
    var payment = parseFloat(document.getElementById('payment').value);
    if (document.getElementById('payment').value === "") {
        payment = 0;
    }

    var gtot = 0;

    if (dis === 0) {
        gtot = value;
        gtot = gtot.toFixed(2);
        gtotele.value = gtot;
    } else {
        gtot = value - ((value * dis) / 100);
        gtot = gtot.toFixed(2);
        gtotele.value = gtot;
    }

    var due = gtot - payment;
    dueele.innerHTML = due.toFixed(2);

}

function selectDis() {
    var pre = document.getElementById('discount').value;
    if (pre === "") {
        document.getElementById('disPre').value = "0";
    } else {
        document.getElementById('disPre').value = pre;
        var disName = document.getElementById('discount').options[document.getElementById('discount').selectedIndex].text;

        document.getElementById('dc').value = disName;
    }
    refreshInvoice();
}

function refreshInvoice() {
    var total = document.getElementById('tot').value;
    enterTotal(total);
}

function clickPaymentOut() {
    var val = document.getElementById('payment').value;
    if (val === "") {
        document.getElementById('payment').value = "0";
        enterPayment();
    }

}

function validate() {
    if (clicked === "DeleteSample") {
        var x = confirm("Are you sure you want to delete this Sample?");
        if (x) {
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }
}

function printSample(lpsid) {
    if ($('#repstatus').html() === "Status : Done") {
        var win = window.open("printreport/" + lpsid + "&" + "", '_blank');
        win.print();
        setTimeout(function () {
            win.close();
        }, 3000);
    } else {
        alert("Please enter test results to continue!");
    }
}

function getChart() {
    if ($("#pstest").val() === "%") {
        alert("Please select test to view graph!");
    } else {
        loadChart();

    }
}

var ref_min;
var ref_max;
var test_mes;

function  loadChart() {
    var ctx = document.getElementById("finChart").getContext('2d');

//    var dates = ['2020-02-01', '2020-02-02', '2020-02-03', '2020-02-04', '2020-02-05'];
//    var vals = ['120', '112', '115', '110', '128'];

    var dates = chart_x.substring(0, chart_x.length - 1).split(",");
    var vals = chart_y.substring(0, chart_y.length - 1).split(",");

    var x = 0;
    var len = vals.length;
    while (x < len) {
        vals[x] = parseFloat(vals[x]);
        x++;
    }

    var valueMin = Math.min.apply(null, vals);

    var y_min = 0;
    if (valueMin < ref_min) {
        y_min = valueMin - 5;
    } else {
        y_min = ref_min - 5;
    }


    var minArray = new Array(vals.length).fill(ref_min);
    var maxArray = new Array(vals.length).fill(ref_max);

    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                    label: 'Dates',
                    data: vals,

                    borderColor: [
                        'rgba(0, 32, 128, 1)'
                    ],
                    borderWidth: 2,
                    fill: false,
                    pointBackgroundColor: 'rgba(0, 32, 128, 1)'
                }, {
                    label: 'Reference Min',
                    data: minArray,

                    borderColor: [
                        'rgba(58, 235, 52, 1)'
                    ],
                    borderWidth: 2,
                    fill: false,
                    pointRadius: 0
                }, {
                    label: 'Reference Max',
                    data: maxArray,

                    borderColor: [
                        'rgba(58, 235, 52, 1)'
                    ],
                    borderWidth: 2,
                    fill: false,
                    pointRadius: 0
                }]
        },
        options: {

            legend: {
                position: 'top'
            },
            scales: {
                yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            min: y_min
                        },
                        scaleLabel: {
                            display: true,
                            labelString: test_mes
                        }
                    }],
                xAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'Dates'
                        }
                    }]
            }

        }
    });

    $('#chart_window').show(500);
}

function printChart() {
    var url = document.getElementById("finChart").toDataURL();
    var image = new Image();
    image.id = "pic";
    image.src = url;
    document.getElementById('image_for_crop').appendChild(image);

    var divToPrint = document.getElementById('image_for_crop').innerHTML;
    var divToPrint2 = document.getElementById('chart_footer').innerHTML;
    var pname = document.getElementById('fname').value + " " + document.getElementById('lname').value;
    var test = document.getElementById('pstest').options[document.getElementById('pstest').selectedIndex].text;


    var newWin = window.open('', 'Print-Window');
    newWin.document.open();
    newWin.document.write('<html><body onload="window.print()"><center><h3><br/>Test Value Graph - MLWS System</h3><h4>Patient Name : ' + pname + ' | Test : ' + test + '</h4>' + divToPrint + '</br><div>' + divToPrint2 + '</div><hr/><p>Generated By Apex Medical Laboratory Web System (www.appexsl.com)</p></center></body></html>');
    newWin.document.close();
    setTimeout(function () {
        newWin.close();
    }, 10);
}

</script>
@stop

@section('body')
<blockquote>

    <table width="100%">
        <tr valign="top">
            <td width="30%">
                <?php
                $lid = $_SESSION['lid'];
                $cusymbol = $_SESSION['cuSymble'];

                if (isset($_GET['lpsid']) | isset($lpsid)) {
                    if (isset($lpsid)) {
                        $result = DB::select("select b.dob,a.fastinghours,a.refference_idref,b.initials,a.lpsid,b.pid,a.date,c.status,c.fname,c.nic,c.mname,c.lname,e.gender,b.age,b.months,b.days,c.tpno,c.hpno,c.address , c.email,a.lpsid,round(f.total,2) as total,round(f.gtotal,2) as gtotal,round(f.paid,2) as paid,discount_did,paiddate,paymentmethod,f.cashier,f.iid, b.national as national from lps a,patient b, user c,usertype d,gender e,invoice f  where a.patient_pid = b.pid and b.user_uid = c.uid and c.usertype_idusertype=d.idusertype and e.idgender = c.gender_idgender and a.lpsid=f.lps_lpsid and a.lab_lid='" . $lid . "' and d.type='Patient' and a.lpsid='" . $lpsid . "'");
                        foreach ($result as $patientResult) {
                            
                        }
                    } else {
                        $result = DB::select("select b.dob,a.fastinghours,a.refference_idref,b.initials,a.lpsid,b.pid,a.date,c.status,c.fname,c.nic,c.mname,c.lname,e.gender,b.age,b.months,b.days,c.tpno,c.hpno,c.address , c.email,a.lpsid,round(f.total,2) as total,round(f.gtotal,2) as gtotal,round(f.paid,2) as paid,discount_did,paiddate,paymentmethod,f.cashier,f.iid, b.national as national from lps a,patient b, user c,usertype d,gender e,invoice f  where a.patient_pid = b.pid and b.user_uid = c.uid and c.usertype_idusertype=d.idusertype and e.idgender = c.gender_idgender and a.lpsid=f.lps_lpsid and a.lab_lid='" . $lid . "' and d.type='Patient' and a.lpsid='" . $_GET['lpsid'] . "'");
                        foreach ($result as $patientResult) {
                            $lpsid = $patientResult->lpsid;
                        }

                        if (isset($patientResult)) {
                            
                        } else {
                            $result = DB::select("select b.dob,a.fastinghours,a.refference_idref,b.initials,a.lpsid,b.pid,a.date,c.status,c.fname,c.nic,c.mname,c.lname,e.gender,b.age,b.months,b.days,c.tpno,c.hpno,c.address , c.email,a.lpsid,round(0,2) as total,round(0,2) as gtotal,round(0,2) as paid,b.national as national from lps a,patient b, user c,usertype d,gender e,invoice f  where a.patient_pid = b.pid and b.user_uid = c.uid and c.usertype_idusertype=d.idusertype and e.idgender = c.gender_idgender and a.lab_lid='" . $lid . "' and d.type='Patient' and a.lpsid='" . $_GET['lpsid'] . "'");
                            foreach ($result as $patientResult) {
                                $lpsid = $patientResult->lpsid;
                            }
                        }
                    }
                } elseif (isset($_GET['pid'])) {
                    $result = DB::select("select b.dob,a.fastinghours,a.refference_idref,b.initials,a.lpsid,b.pid,a.date,c.status,c.fname,c.nic,c.mname,c.lname,e.gender,b.age,b.months,b.days,c.tpno,c.hpno,c.address , c.email,a.lpsid,round(f.total,2) as total,round(f.gtotal,2) as gtotal,round(f.paid,2) as paid,discount_did,paiddate,paymentmethod,f.cashier,f.iid, b.national as national from lps a,patient b, user c,usertype d,gender e,invoice f  where a.patient_pid = b.pid and b.user_uid = c.uid and c.usertype_idusertype=d.idusertype and e.idgender = c.gender_idgender and a.lpsid=f.lps_lpsid and a.lab_lid='" . $lid . "' and d.type='Patient' and b.pid='" . $_GET['pid'] . "'");
                    foreach ($result as $patientResult) {
                        $lpsid = $patientResult->lpsid;
                    }
                }

                if (isset($patientResult)) {

                    if ($patientResult->status == '1') {
                        $patientStatus = "Active";
                    } else {
                        $patientStatus = "In-Active";
                    }
                    ?>

                    <table width="100%" style=" margin-top: -20px;">
                        <tr>
                            <td width='15%'><img src="images/man.png" width="75" height="75"></td>
                            <td>
                                <blockquote>
                                    <input type="hidden" name="pid" value="{{  $patientResult->pid  }}">

                                    <h3 class="OPName">{{ $patientResult->fname }} {{ $patientResult->lname }}</h3>
                                    <p class="fieldText">Last attended : {{ $patientResult->date }} </p>
                                    <p class="fieldText">
                                        Status :                                     
                                        @if($patientStatus=='Active')
                                        <span style="color: green; font-size: 16px;">{{ $patientStatus }}</span>
                                        @else
                                        <span style="color: red; font-size: 16px;">{{ $patientStatus }}</span>
                                        @endif  
                                    </p>


                                </blockquote>
                            </td>
                        </tr>
                    </table>

                    <form action="viewOPSubmit" method="post" onsubmit="return selectBtn()" id="OPForm">
                        <p class="tableHead2" style="font-size: 16px; margin-left: -10px">General Information</p>
                        <table width="100%" cellspacing='0' cellpadding='0'>
                            <tr>
                                <td width='50%'>Initials</td>
                                <td><input type="text" name="ini" id="ini" size="5" value="{{ $patientResult->initials }}" class="input-text" style="margin-bottom: 5px;"></td>
                            </tr>
                            <tr>
                                <td width='50%'>First Name</td>
                                <td><input type="text" name="fname" id="fname" size="30px" value="{{ $patientResult->fname }}" class="input-text" style="margin-bottom: 5px;"></td>
                            </tr>
                            <tr>
                                <td>Middle Name</td>
                                <td><input type="text" name="mname" id="mname" size="30px" value="{{ $patientResult->mname }}" class="input-text" style="margin-bottom: 5px;" disabled></td>
                            </tr>
                            <tr>
                                <td>Last Name</td>
                                <td><input type="text" name="lname" id="lname" size="30px" value="{{ $patientResult->lname }}" class="input-text" ></td>
                            </tr>                        
                            <tr>
                                <td >Age</td>
                                <td>
                                    Y:
                                    <input type="number" name="years" style="width:30px; margin-top: 10px; margin-bottom: 10px;" value="{{ $patientResult->age }}" class="input-text">
                                    &nbsp;
                                    M:
                                    <input type="number" name="months" style="width:30px" value="{{ $patientResult->months }}" class="input-text"> 
                                    &nbsp;
                                    D: 
                                    <input type="number" name="days" style="width:30px" value="{{ $patientResult->days }}" class="input-text"> 
                                </td>
                            </tr>

                            <tr>
                                <td >Birth Day</td>
                                <td><input type="date" name="udob" id="udob" style="margin-bottom: 5px;" size="30px" value="{{ $patientResult->dob }}" class="input-text"></td>
                            </tr>

                            <tr>
                                <td >Gender</td>
                                <td>
                                    <select name="gender" class="select-basic" style="width: 150px; margin-bottom: 5px;">
                                        @if($patientResult->gender == 'Male' )
                                        <option selected="selected">Male</option>
                                        <option>Female</option>
                                        @else
                                        <option>Male</option>
                                        <option selected="selected">Female</option>                                    
                                        @endif
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td >Telephone No</td>
                                <td><input type="tel" name="tpno" id="fname6" style="margin-bottom: 5px;" size="30px" value="{{ $patientResult->tpno }}" class="input-text"></td>
                            </tr>
                            <tr>
                                <td >Home Phone No</td>
                                <td><input type="tel" name="hpno" id="fname7" style="margin-bottom: 5px;" size="30px" value="{{ $patientResult->hpno }}" class="input-text"></td>
                            </tr>
                            <tr>
                                <td >Address</td>
                                <td><textarea style="width: 91%; margin-bottom: 2px;" name="address" id="address" cols="30" rows="3" class="text-area">{{ $patientResult->address }}</textarea></td>
                            </tr>
                            <tr>
                                <td >Email</td>
                                <td><input type="email" name="email" id="fname9" size="30px" value="{{ $patientResult->email }}" class="input-text"></td>
                            </tr>
                            
                            <tr>
                                <td >NIC NO</td>
                                <td><input type="text" name="nic" id="nic" size="30px" value="{{ $patientResult->nic }}" class="input-text"></td>
                            </tr>
                            <tr>
                                <td >Nationality</td>
                                <td><input type="text" name="national" id="national" size="30px" value="{{ $patientResult->national }}" class="input-text"></td>
                            </tr>
                        </table>
                        <br/>
                        <p><span class="tableHead" style="margin-left: -15px;">Invoice Details</span></p>

                        <table width="370" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="143" >Session Reference</td>
                                <td width="35" >:</td>
                                <td width="200" >

                                    <?php
                                    if ($patientResult->refference_idref !== null) {
                                        $result = DB::select("SELECT name FROM refference where lid = '" . $lid . "' and idref = '" . $patientResult->refference_idref . "'");
                                        foreach ($result as $RefResult) {
                                            $refName = $RefResult->name;
                                        }
                                    }
                                    ?>

                                    <input class="input-text" style="margin-bottom: 5px;" type="text" name="ref" id="ref" list="reflist" value="{{ $refName or '' }}">
                                    <datalist id="reflist">
                                        <?php
                                        $result = DB::select("SELECT * FROM refference where lid = '" . $lid . "'");
                                        foreach ($result as $RefResult) {
                                            ?>
                                            <option>{{ $RefResult->name }}</option>
                                            <?php
                                        }
                                        ?>
                                    </datalist>
                                </td>
                            </tr>
                            <tr>
                                <td width="143" >Fasting Hours</td> 
                                <td width="35" >:</td> 
                                <td width="200" ><input class="input-text" style="margin-bottom: 5px;" type="text" name="fhours" id="fhours" value="{{ $patientResult->fastinghours or '' }}"></td>
                            </tr>

                            @if(isset($patientResult->iid))
                            <tr><td><hr/></td><td><hr/></td><td><hr/>
                                    <input type="hidden" name="iid" value="{{ $patientResult->iid }}">
                                </td></tr>
                            <tr>
                                <td width="143" >Total Cost</td>
                                <td width="35" >: {{ $cusymbol }}</td>
                                <td width="200" ><input class="input-text" readonly="readonly" style="margin-bottom: 5px;" type="text" name="tot" id="tot" value="{{ $patientResult->total }}" onkeyup="enterTotal(value)"></td>
                            </tr>
                            <tr>
                                <td >Discount</td>
                                <td >:</td>
                                <td ><select id="discount" name="discount" onchange="selectDis()" class="select-basic" style="margin-bottom: 5px;">
                                        <option></option>
                                        <?php
                                        $resultdis = DB::select("select * from Discount where lab_lid = '" . $lid . "'");
                                        foreach ($resultdis as $Discount) {
                                            $selected = "";
                                            if ($Discount->did == $patientResult->discount_did) {
                                                $selected = "selected='selected'";
                                                $selectedValue = $Discount->value;
                                            }
                                            ?>
                                            <option value="{{ $Discount->value }}" {{ $selected or '' }} >{{ $Discount->name }}</option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                    <input type="text" id="disPre" name="disPre" size="10" value="{{ $selectedValue or '0' }}" class="input-text" style="width: 68px;" >
                                    %
                                    <input type="hidden" id="dc" name="dc" value=""></td>
                            </tr>
                            <tr>
                                <td >Grand Total</td>
                                <td >: {{ $cusymbol }}</td>
                                <td ><input type="text" name="gtot" readonly="readonly" id="gtot" value="{{ $patientResult->gtotal }}" onkeyup="enterPayment()" class="input-text" style="margin-bottom: 5px;"></td>
                            </tr>
                            <tr>
                                <td >Payment Method</td>
                                <td >:</td>
                                <td>
                                    @if($patientResult->paymentmethod == "Cash")
                                    <input type="radio" name="paym" value="Cash" checked="checked" style="margin-bottom: 10px;">
                                    Cash
                                    <input type="radio" name="paym" value="Card">
                                    Card
                                    <input type="radio" name="paym" value="Online">
                                    Online 

                                    @elseif($patientResult->paymentmethod == "Card")
                                    <input type="radio" name="paym" value="Cash">
                                    Cash
                                    <input type="radio" name="paym" value="Card" checked="checked" style="margin-bottom: 10px;">
                                    Card
                                    <input type="radio" name="paym" value="Online">
                                    Online                                 

                                    @else
                                    <input type="radio" name="paym" value="Cash">
                                    Cash
                                    <input type="radio" name="paym" value="Card">
                                    Card
                                    <input type="radio" name="paym" value="Online" checked="checked" style="margin-bottom: 10px;">
                                    Online                                 

                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td >Payment </td>
                                <td >: {{ $cusymbol }}</td>
                                <td ><input type="text" id="payment" name="payment" value="{{ $patientResult->paid }}" class="input-text" style="margin-bottom: 5px;" onkeyup="refreshInvoice()" onclick="clickPayment()" onfocusout="clickPaymentOut()"></td>
                            </tr>
                            <tr>
                                <td >Due Payment</td>
                                <td >: {{ $cusymbol }}</td>
                                <td id="due"> <p style="margin-bottom: 5px;">{{ $patientResult->gtotal - $patientResult->paid }}</p></td>
                            </tr>
                            <tr>
                                <td >Cashier</td>
                                <td >:</td>
                                <td>{{ $patientResult->cashier }}</td> 
                            </tr>

                            @endif
                        </table>
                        <p>

                            <?php
                            $editingPrivs = "disabled='disabled'";
                            $deletingPrivs = "disabled='disabled'";

                            $resultEP = DB::select("SELECT * FROM privillages p where user_uid = (select user_uid from labUser where luid = '" . $_SESSION["luid"] . "') and options_idoptions = '13'");
                            foreach ($resultEP as $resep) {
                                $editingPrivs = "";
                            }
                            $resultEP = DB::select("SELECT * FROM privillages p where user_uid = (select user_uid from labUser where luid = '" . $_SESSION["luid"] . "') and options_idoptions = '17'");
                            foreach ($resultEP as $resep) {
                                $deletingPrivs = "";
                            }
                            ?>

                            <input name="submit" type="submit" class="btn" value="Update Details" style="float: left; margin-left: 0;" {{ $editingPrivs or '' }} />

                                   <input name="submit" type="submit" value="Remove Patient" onClick="clicked = 'remove'" class="btn" style="float: left;" {{ $deletingPrivs or '' }}/>  

                                   <input type="hidden" name="pid" value="{{ $patientResult->pid }}">
                            <input type="hidden" name="lpsid" value="{{ $lpsid }}">                        
                        </p>
                        <br/>
                        <br/>
                        <p style="color: green;">{{ $msg or '' }}</p>
                    </form>
                </td>



                <td width="35" align="center">
                    <div style="width:2px; height:700px; background-color:#000; margin-left: 20px;">
                        <div align="center"></div>
                    </div>
                </td>

                <td>

                    <blockquote>
                        <table width="450" border="0" cellpadding="2" cellspacing="0">
                            <tr>
                                <td class="tableHead2">Testing Informations </td>
                            </tr>
                            <tr>

                                <td valign='top'>Date : 
                                    <select id="pdates" onchange="dateChangeEvent(0)" class="select-basic" style="margin-bottom: 10px; width: 180px ;">   
                                        <option value="0">ALL</option>
                                        <?php
                                        if (isset($_GET['lpsid']) | isset($lpsid)) {
                                            $resultTinfo = DB::select("select date,patient_pid as pid from lps where Lab_lid='" . $lid . "' and patient_pid = (select patient_pid from lps where lpsid='" . $lpsid . "') group by date");
                                            foreach ($resultTinfo as $Tinfo) {
                                                ?>       
                                                <option>{{ $Tinfo->date or '' }}</option>  
                                                <?php
                                            }
                                        }
                                        ?>

                                    </select>

                                    <br/>

                                    Test : 

                                    <select id="pstest" onchange="dateChangeEvent(1)" class="select-basic" style=" width: 180px ;">
                                        <option value="%">ALL</option>
                                        <?php
                                        $Result = DB::select("select c.name,c.tgid from test a, Lab_has_test b,Testgroup c where a.tid = b.test_tid and b.testgroup_tgid = c.tgid and b.lab_lid='" . $_SESSION['lid'] . "' group by c.name order by c.name ASC");
                                        foreach ($Result as $res) {
                                            $tgid = $res->tgid;
                                            $group = $res->name;
                                            ?>
                                            <option value="{{ $tgid }}"> {{ $group }} </option>
                                            <?php
                                        }
                                        ?>
                                    </select>

                                    <input type="hidden" id="lid" name="lid" value="{{ $lid }}"/>
                                    <input type="hidden" id="pid" name="pid" value="{{ $patientResult->pid }}"/>
                                </td> 

                                <td valign='top' width='30%'>
                                    TO : 
                                    <select id="pdates2" onchange="dateChangeEvent(1)" class="select-basic" style="margin-bottom: 10px; width: 120px ;">   
                                        <option value="0">ALL</option>
                                        <?php
                                        if (isset($_GET['lpsid']) | isset($lpsid)) {
                                            $resultTinfo = DB::select("select date,patient_pid as pid from lps where Lab_lid='" . $lid . "' and patient_pid = (select patient_pid from lps where lpsid='" . $lpsid . "') group by date");
                                            foreach ($resultTinfo as $Tinfo) {
                                                ?>       
                                                <option>{{ $Tinfo->date or '' }}</option>  
                                                <?php
                                            }
                                        }
                                        ?>

                                    </select>

                                    <br/>
                                    <button id="chart_btn" onclick="getChart()" class="btn" style="width: 150px;"> Get Chart </button> 
                                </td>
                            </tr>
                        </table>

                        <div style="height: 650px; width: 550px; overflow-y: scroll; border-top: 2px blue solid; margin-top: 10px;">
                            <table id="testFieldsTR">
                                <?php
                                $resultSampleinfo = DB::select("select lpsid,sampleNo,date,status from lps where patient_pid = '" . $Tinfo->pid . "' and Lab_lid ='" . $lid . "' order by date DESC");
                                foreach ($resultSampleinfo as $Sampleinfo) {
                                    $lpsID2 = $Sampleinfo->lpsid;
                                    $sampleNo = $Sampleinfo->sampleNo;
                                    $RepStatus = $Sampleinfo->status;
                                    ?>

                                    <form action="viewOPSampleSubmit" method="post" id="OPForm" onsubmit="return validate()">
                                        <tr>
                                            <td>Date : {{ $Sampleinfo->date }}</td>
                                            <td>Sample No : <input type="text" name="sno" value="{{ $Sampleinfo->sampleNo }}" style="width:82px" class="input-text"></td>
                                        </tr>

                                        <?php
                                        $resultSampleTinfo = DB::select("select a.tid,a.name,c.measurement,c.status,b.value from test a,lps_has_test b,Lab_has_test c where a.tid=b.test_tid and c.test_tid=a.tid and b.lps_lpsid='" . $lpsID2 . "' group by a.tid");
                                        foreach ($resultSampleTinfo as $SampleTestinfo) {
                                            $name = $SampleTestinfo->name;
                                            $tid = $SampleTestinfo->tid;
                                            $mes = $SampleTestinfo->measurement;
                                            $value = $SampleTestinfo->value;
                                            $vCode = $SampleTestinfo->status;

                                            $vCodeSp = explode("#", $vCode);
                                            ?>                                                               
                                            <tr> 
                                                <td>{{ $name }}</td>

                                                <td><input type="text" name="{{ $tid }}" value="{{ $value }}" class="input-text"></td> 

                                            </tr>
                                        <?php } ?>
                                        <tr>
                                            <td>
                                                <input type="hidden" name="lpsid" value="{{ $lpsID2 }}">
                                                <div id="repstatus">Status : {{ $RepStatus }}</div>
                                            </td>                                    
                                        </tr>  
                                        <tr>
                                            <td>
                                                <p>
                                                    <input name="submit" type="submit" class="btn" style="margin-left: 0" value="Update Values" onclick="clicked = 'UpdateTests'" {{ $editingPrivs or '' }} />
                                                </p>
                                                <hr/>
                                            </td> 
                                            <td>
                                                <p>
                                                    <input name="submit" type="submit" class="btn" style="margin-left: 0; width: 185px; " value="Delete Sample" onclick="clicked = 'DeleteSample'" {{ $deletingPrivs or '' }} /> 
                                                </p>
                                                <hr/>
                                            </td><td>
                                                <p>
                                                    <input name="print" type="button" class="btn" style="margin-left: 0; width: 150px; " value="Print Report" onclick="printSample({{$lpsID2}})" {{ $editingPrivs or '' }} />
                                                </p>
                                                <hr/>
                                            </td>
                                        </tr>
                                    </form>
                                <?php } ?>
                            </table>
                        </div>
                    </blockquote>
                    <?php
                } else {
                    echo 'There are no records releted to this patient!';
                }
                ?>
            </td>
        </tr>     
    </table>

    <div id="chart_window" style=" width: 750px; height: 600px; background-color: white; border: 2px #006cad solid; padding: 5px; border-radius: 5px;">

        <div id="chart_header"> <h3 style="float: left; padding-left: 5px;">Test Historical Graph</h3> <div style="float: right;"><img src="images/close.png" style="cursor: pointer" onclick="$('#chart_window').hide(500);" width="30" height="30"></div> </div>

        <div id="chart_box" style="width: 700px; height: 550px; margin-left: 20px; margin-top: 10px;">
            <canvas id='finChart' height="200" ></canvas>
        </div>

        <div id="chart_footer">



        </div>
        <button onclick="printChart()" class="btn">Print Graph</button>

        </canvas>

    </div> 

    <div id="image_for_crop"> </div>

</blockquote>
@stop
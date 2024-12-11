<?php
if (!isset($_SESSION)) {
    session_start();
}
date_default_timezone_set('Asia/Colombo');
?>
@extends('Templates/WiTemplate')

@section('title')
Add Patient
@stop

@section('head')
<script type="text/javascript">

    $(document).ready(function() {
        showNaviPane();
        $('#fname').focus();
        
        if($('#initial').length){
            $('#initial').focus();
        }
        
        onloadElements();

    });

    function loadTests(x) {
        var panel = document.getElementById(x);

        document.getElementById('allTlist').style.opacity = "0";
        document.getElementById('testgroups').style.opacity = "0";

        var maxOpasity = "1";
        if (panel.style.opacity === maxOpasity) {
            panel.style.opacity = "0px";

        } else {
            panel.style.opacity = maxOpasity;
        }

    }

    var PatientSugDataOblect;
    function loadPHistory() {
        var tableBody = "";

        var fname = document.getElementById('fname').value;
        var lname = document.getElementById('lname').value;
        var address = "";
        if ($('#address').is(':disabled')) {
            address = "%";
        } else {
            address = document.getElementById('address').value;
        }

        if (fname !== "") {
            $.ajax({
                type: 'POST',
                url: "searchPHistory",
                data: {'fname': fname, 'lname': lname, 'address': address, '_token': $('input[name=_token]').val()},
                success: function(data) {
                    data = JSON.parse(data);
                    PatientSugDataOblect = data;

                    for (var i = 0; i < data.length; i++) {
                        var gender = "Male";
                        if (data[i].gender_idgender === '2') {
                            var gender = "Female";
                        }

                        tableBody += "<tr class='phistr' style='cursor:pointer;' onclick='selectItem(" + i + ")'><td>&nbsp;" + data[i].fname + "</td><td>&nbsp;" + data[i].lname + "</td><td style='overflow:scroll;'>&nbsp;" + data[i].address + "</td><td>&nbsp;" + data[i].tpno + "</td><td>&nbsp;" + data[i].age + "</td><td>&nbsp;" + gender + "</td></tr>";
                    }

                    if (tableBody === "") {
                        closepHp();
                    } else {
                        var panel = document.getElementById('pHisPanel');
                        var phTable = document.getElementById('phTable');
                        panel.style.height = "300px";
                        panel.style.border = "2px solid #006";
                        phTable.innerHTML = "<table><tr style='background-color:blue; color:white'><th>F.Name</th><th>L.Name</th><th>Address</th><th>T.P.No</th><th>Age</th><th>Gender</th></tr>" + tableBody + "</table>";

                    }
                }
            });

        } else {
            closepHp();
        }
    }

    function selectItem(rid) {
        $('#fname').val(PatientSugDataOblect[rid].fname);
        $('#lname').val(PatientSugDataOblect[rid].lname);
        $('#years').val(PatientSugDataOblect[rid].age);
        $('#months').val(PatientSugDataOblect[rid].months);
        $('#dates').val(PatientSugDataOblect[rid].days);
        $('#pnno').val(PatientSugDataOblect[rid].tpno);
        $('#address').val(PatientSugDataOblect[rid].address);

        if (PatientSugDataOblect[rid].gender_idgender === '1') {
            $("#genfemale").prop("checked", false);
            $("#genmale").prop("checked", true);
        } else {
            $("#genmale").prop("checked", false);
            $("#genfemale").prop("checked", true);
        }

        $('#selectedPid').val(PatientSugDataOblect[rid].pid);

        closepHp();
        $('#years').focus();
    }

    function closepHp() {
        var panel = document.getElementById('pHisPanel');
        
        panel.style.height = "0px"        
        panel.style.border = "0px";
        
    }

    function resetFields() {
        window.location.href = "addpatient";

    }

    function onloadElements() {
        document.getElementById('testgroups').style.opacity = "1";        
        closepHp(); 
    }

    function clickTGroup(ele, classname, tgprice) { //element object //classname //price

        if (tgprice === null) {
            tgprice = "0";
        }

        var price = parseFloat(tgprice);
        var sp = classname.split("-");

        var checkboxes = document.getElementsByClassName(sp[1]);
        if (ele.checked) {
            total = total + price;
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type === 'checkbox') {
                    checkboxes[i].checked = true;
                    var id = checkboxes[i].id;
                    var name = checkboxes[i].name;


                }
            }
        } else {
            total = total - price;
            for (var i = 0; i < checkboxes.length; i++) {

                if (checkboxes[i].type === 'checkbox') {
                    checkboxes[i].checked = false;
                    var id = checkboxes[i].id;
                    var name = checkboxes[i].name;


                }
            }

        }

        refreshInvoice();

    }

    function selectDis() {
        var disName = document.getElementById('discount').options[document.getElementById('discount').selectedIndex].text;
        var arr = disName.split(' : ');
        var disName = arr[0];
        var arr2 = arr[1].split('%');

        var pre = arr2[0];

        //var pre = document.getElementById('discount').value;
        if (document.getElementById('discount').value === "0") {
            document.getElementById('disPre').value = "0";
            document.getElementById('gtot').value = total;
        } else {
            document.getElementById('disPre').value = pre;
            var gtot = total - (total * (pre / 100));
            gtot = gtot.toFixed(2);
            document.getElementById('gtot').value = gtot;


            //var disName = document.getElementById('discount').options[document.getElementById('discount').selectedIndex].text;

            document.getElementById('dc').value = disName;
        }
        enterPayment();

    }

    var total = 0;
    function cboxEvent(ele, name, id, classname) {//ele tname tprice checkboxgroupname
        groupDone = 0;
        //checkGroup(classname);

        if (ele.checked) {
            total = total + parseFloat(id);
            refreshInvoice();
        } else {
            total = total - parseFloat(id);
            refreshInvoice();
        }

    }

    var groupDone = 0;
    var checkedPrice = 0;

    function checkGroup(className) {

        var check = 0;
        var checkboxes = document.getElementsByClassName(className);
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {
                check += 1;
            }
        }

        var groupName = "group-" + className;
        var tgprice = parseFloat(document.getElementsByName(groupName)[0].id);

        if (check === 0) {
            document.getElementsByName(groupName)[0].checked = false;
            total = total - tgprice;

        } else if (check === 1) {
            document.getElementsByName(groupName)[0].checked = true;
            checkedPrice = 0;


            total = total + tgprice;
            groupDone = 1;

        }

        refreshInvoice();

    }

    function clickPayment() {
        var val = document.getElementById('payment').value;
        if (val === "0") {
            document.getElementById('payment').value = "";
        }

    }

    function clickPaymentOut() {
        var val = document.getElementById('payment').value;
        if (val === "") {
            document.getElementById('payment').value = "0";
            enterPayment();
        }

    }

    function enterPayment() {
        var gtot = document.getElementById('gtot').value;
        var cdue = document.getElementById('payment').value;
        var due = parseFloat(gtot) - parseFloat(cdue);
        due = due.toFixed(2);
        document.getElementById('due').innerHTML = due;
    }

    function refreshInvoice() {
        document.getElementById('tot').value = total + "";
        enterPayment();
        selectDis();

    }

    function validate() {
        var inputs = document.getElementsByTagName("input");
        var cbs = [];
        var checked = [];
        for (var i = 0; i < inputs.length; i++) {
            if (inputs[i].type === "checkbox") {
                cbs.push(inputs[i]);
                if (inputs[i].checked) {
                    checked.push(inputs[i])
                }
            }
        }
        if (checked.length === 0) {
            document.getElementById('errorp').innerHTML = "<p style='color:red'>Please select any test!</p>";
            return false;

        } else {
            if (document.getElementById('submited').value === "yes") {
                alert("Form Already Submitted!. Please reset or reload the page.");
                return false;
            } else {
                ChangeState();
                return true;
            }

        }
    }

    function selectRef() {
        if (document.getElementById('refby').value !== "") {
            document.getElementById('newref').value = "";
            document.getElementById('newref').disabled = true;
        } else {
            document.getElementById('newref').disabled = false;
        }
    }

    function ChangeState() {
        document.getElementById('submited').value = "yes";
    }

    function checkBoxEnb(id, ele) {
        if (ele.indexOf("+") >= 0) {
            var arr = ele.split('+');

            for (var i = 0; i < ele.length; ++i) {
                if ($('#' + id).is(':checked')) {
                    $('#' + arr[i]).removeAttr('disabled');
                } else {
                    $('#' + arr[i]).attr('disabled', 'disabled');
                }
            }

        } else {
            if ($('#' + id).is(':checked')) {
                $('#' + ele).removeAttr('disabled');
            } else {
                $('#' + ele).attr('disabled', 'disabled');
            }
        }
    }

    function disableInput() {
        event.preventDefault();
        return false;
    }

    function submitForm() {
        
        if (document.getElementById('discount').value === "0") {
            document.getElementById('gtot').value = total;
        }
        
        var selected = [];
        $('#testArea input:checked').each(function() {
            selected.push($(this).attr('name'));
        });
        
        if($("#fname").val() !== ""){

        if (selected.length === 0) {
            alert("Please select tests!");
        } else {
            var form = $('#form');
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(result) {
                    if ($('#directenter').val() === '1') {
                        if (result.includes("#@@#")) {
                            var arr = result.split("#@@#");
                            window.location = "enterresults?psno=" + arr[1];
                        } else {
                            var myWindow = window.open("", "MsgWindow", "width=500,height=700");
                            myWindow.document.write(result);
                            //myWindow.print();                    
                            resetFields();
                        }
                    } else {
                        if (result.includes("#@@#")) {
                            resetFields();
                        } else {
                            var myWindow = window.open("", "MsgWindow", "width=500,height=700");
                            myWindow.document.write(result);
                            //myWindow.print();                    
                            resetFields();
                        }
                    }
                }
            });
        }
    }else{
        alert("Please enter patient name!");
    }
    }

    function getLastPatient() {
        $.ajax({
            type: 'POST',
            url: "getlastpatient",
            data: {'_token': $('input[name=_token]').val()},
            success: function(data) {
                if (data === '0') {
                    alert("Last added patient not available!");
                } else {
                    data = JSON.parse(data);
                    $('#fname').val(data[0].fname);
                    $('#lname').val(data[0].lname);
                    $('#years').val(data[0].age);
                    $('#months').val(data[0].months);
                    $('#dates').val(data[0].days);
                    $('#pnno').val(data[0].tpno);
                    $('#address').val(data[0].address);

                    if (data[0].gender_idgender === '1') {
                        $("#genfemale").prop("checked", false);
                        $("#genmale").prop("checked", true);
                    } else {
                        $("#genmale").prop("checked", false);
                        $("#genfemale").prop("checked", true);
                    }

                    $('#selectedPid').val(data[0].pid);

                    $('#years').focus();
                }
            }
        });
    }
    
    var initialAuto = true;
    function initialClicked(){
        initialAuto = false;        
    }
    
    function setInitials(){
        if($('#initial').length){
        if(initialAuto){            
            if($('#years').val() != "" && (parseFloat($('#years').val()))< 4){
                $('#initial').val("Baby").change();       
            }else{
                if($('#genmale').is(':checked')){
                    $('#initial').val("Mr").change();
                }
                if($('#genfemale').is(':checked')){
                    $('#initial').val("Mrs").change();                
                }
            }
            
            if($('#years').val() === "" && $('#months').val() != ""){
                $('#initial').val("Baby").change();       
            }
            if($('#years').val() === "" && $('#dates').val() != ""){
                $('#initial').val("Baby").change();       
            }
        }
    }
    }
    
    

</script>
@stop
<?php
    if (isset($_SESSION['lid']) & isset($_SESSION['luid'])) {
?>
@section('body')
<h3 class="pageheading" id="fnamex">Register Patients</h3>

<form action="regPatient" id="form" method="POST" onsubmit="return validate()" target="_blank"> 
    <table border="0" cellpadding="0" cellspacing="0" style="margin-left: 20px; margin-top: 20px;">
        <tr>
            <td>
                <p id="ax" class="tableHead" style="margin-left: -12px; margin-top: 10px; float: left;">
                    Patient Details
                </p>

                <?php
                $cuSymble = $_SESSION['cuSymble'];

                if (isset($pid)) {
                    $patientResult = DB::select("select a.fname,a.lname,a.tpno,a.address,b.age,b.months,b.days,c.gender from user a,patient b,gender c where a.uid=b.user_uid and a.gender_idgender=c.idgender and b.pid = '" . $pid . "'");
                    foreach ($patientResult as $result) {
                        $fname = $result->fname;
                        $lname = $result->lname;
                        $gender = $result->gender;
                        $years = $result->age;
                        $months = $result->months;
                        $days = $result->days;
                        $tpno = $result->tpno;
                        $address = $result->address;
                    }
                }

                $fieldResult = DB::select("select * from addpatientconfigs where lab_lid = '" . $_SESSION['lid'] . "'");
                foreach ($fieldResult as $field) {
                    if (!$field->tpno) {
                        $tpnoenb = "disabled";
                    }
                    if (!$field->address) {
                        $addressenb = "disabled";
                    }
                    if (!$field->refby) {
                        $refbyenb = "disabled";
                    }

                    if ($field->typedv == '1') {
                        $typedvFRHidden = 'In';
                    } else {
                        $typedvFRHidden = 'Out';
                    }

                    if (!$field->type) {
                        $typeenb = "disabled";
                        $typehidden = "<input type='hidden' name='ptype' value='" . $typedvFRHidden . "'/>";
                    }

                    $directenter = $field->directresultenter;
                    $patientsuggestion = $field->patientsuggestion;

                    $viewinvoice = $field->viewinvoice;

                    if (!$field->tot) {
                        $totenb = "readonly='readonly'";
                    }
                    if (!$field->gtot) {
                        $gtotenb = "readonly='readonly'";
                    }
                    if (!$field->discount) {
                        $discountenb = "disabled";
                        $discountenb2 = "readonly='readonly'";
                    }
                    if (!$field->payment) {
                        $paymentenb = "readonly='readonly'";
                    }
                    if (!$field->paymeth) {
                        $paymethenb = "disabled";
                    }
                    if (!$field->focusonpayment) {
                        $focusonpaymentfocus = "tabindex='-1'";
                    }

                    if ($field->printinvoicedv) {
                        $printinvoicedv = "checked='checked'";
                    }

                    $refbydv = $field->refbydv;
                    $typedv = $field->typedv;
                    $genderdv = $field->genderdv;
                    if ($genderdv == '1') {
                        $genMale = "checked='checked'";
                        $genFemale = "";
                    } else {
                        $genMale = "";
                        $genFemale = "checked='checked'";
                    }
                    $discountdv = $field->discountdv;

                    if ($field->paymethdv == '2') {
                        $paymethCash = "checked='checked'";
                    } elseif ($field->paymethdv == '3') {
                        $paymethCard = "checked='checked'";
                    } elseif ($field->paymethdv == '4') {
                        $paymethOnline = "checked='checked'";
                    }
                    
                    $initialsEnable = $field->patientinitials;
                }
                ?>

                @if($initialsEnable)
                <table style="float: right;">
                    <tr><td height="32"><span class="fieldText">Initials </span></td>
                        <td>                                
                            <select name="initial" id="initial" class="select-basic" onclick="initialClicked()" tabindex="-1" >
                                    <option value="Mr">Mr</option>
                                    <option value="Mrs">Mrs</option>
                                    <option value="Miss">Miss</option>
                                    <option value="Ms">Ms</option>
                                    <option value="Master">Master</option>
                                    <option value="Baby">Baby</option>
                                    <option value="Ven">Ven</option>
                                    <option value="Rev">Rev</option>
                                    <option value="Hon">Hon</option>
                            </select>    
                        </td>
                    </tr>
                </table>
                @endif
                
                <table style="float: right;">
                    <tr><td height="32"><span class="fieldText">Type </span></td>
                        <td>                                
                            <select name="ptype" class="select-basic" tabindex="-1" {{ $typeenb or '' }}>
                            <?php if (isset($typedv) && $typedv == '1') { ?>
                                        <option value="In" selected="selected">In Patient</option>
                                    <option value="Out">Out Patient</option>
                                <?php } else { ?>
                                    <option value="In">In Patient</option>
                                    <option value="Out" selected="selected">Out Patient</option>
                                <?php } ?>

                            </select>    

                            {{ $typehidden or '' }}

                        </td>
                    </tr>
                </table>

            </td>
            <td><div style="width:1px; height:45px; background-color:#000; margin-left: 20px;"></div></td>
            <td><p style="margin-left: 20px;" class="tableHead">Test Details</p></td>
            <td><div style="width:1px; height:45px; background-color:#000; margin-left: 20px;"></div></td>

        </tr>
        <tr>
            <td valign="top">
                <table width="500" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="124" height="29"><span class="fieldText">First Name</span></td>
                        <td width="427"><input type="text" name="fname" id="fname" value="{{ $fname or '' }}" class="input-text" style="width:94%; margin-bottom: 5px;" onKeyUp="loadPHistory()" required></td>
                    </tr>
                    <tr>
                        <td height="29"><span class="fieldText">Last Name</span></td>
                        <td><input type="text" name="lname" id="lname" value="{{ $lname or '' }}" class="input-text" style="width:94%; margin-bottom: 10px;" onKeyUp="loadPHistory()" required></td>
                    </tr>
                    <tr>
                        <td height="30"><span class="fieldText">Age</span></td>
                        <td>Years:
                            <input type="number" name="years" id="years" value="{{ $years or '' }}" max="120" min="0" class="input-text" style="width:40px;">
                            &nbsp;&nbsp;
                            Months:
                            <input type="number" name="months" id="months" max="12" min="0" value="{{ $months or '' }}" class="input-text" style="width:45px">
                            &nbsp;&nbsp;
                            <label for="number3">Dates: </label>
                            <input type="number" name="dates" id="dates" max="31" min="0" value="{{ $days or '' }}" class="input-text" style="width:45px"></td>
                    </tr>
                    <tr>
                        <td height="47"><span class="fieldText">Gender</span></td>
                        <td>
                            @if(isset($gender) && $gender == "Male")
                            <input type="radio" name="gender" id="genmale" value="1" checked="checked" class="input-radio">
                            Male
                            <input type="radio" name="gender" id="genfemale" value="2" class="input-radio">
                            Female
                            @elseif(isset($gender) && $gender == "Female")
                            <input type="radio" name="gender" id="genmale" value="1" class="input-radio">
                            Male
                            <input type="radio" name="gender" id="genfemale" value="2" checked="checked" class="input-radio">
                            Female
                            @endif

                            @if(!isset($gender))
                                   <input type="radio" name="gender" id="genmale" value="1" {{ $genMale or '' }} class="input-radio"/>
                                   Male
                                   <input type="radio" name="gender" id="genfemale" value="2" {{ $genFemale or '' }} class="input-radio"/>
                                   Female
                            @endif                               
                        </td>
                    </tr>
                    <tr>
                        <td height="28"><span class="fieldText">Referred By</span></td>
                        <td>
                            <table width="100%">
                                <tr>
                                    <td>
                                        <select id="refby" name="refby" onchange="selectRef()" class="select-basic" style="width: 131px;" {{ $refbyenb or '' }}>
                                                <option value=""></option>
                                                    <?php
                                                    $refferenceResult = DB::select("Select * from refference where lid = '" . $_SESSION['lid'] . "'");
                                                    foreach ($refferenceResult as $result) {
                                                        if ($result->idref == $refbydv) {
                                                            $select = "selected='true'";
                                                        } else {
                                                            $select = "";
                                                        }
                                                        ?>
                                                <option value="<?php echo $result->idref; ?>" {{ $select or '' }}><?php echo $result->name; ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td>New: <input type="text" name="newref" id="newref" class="input-text" {{ $refbyenb or '' }} />

                                                    <input type="checkbox" id="refenbl" onchange="checkBoxEnb(id, 'newref+refby')" tabindex="-1">
                                    </td>
                                </tr>
                            </table>


                        </td>
                    </tr>

                    <tr>

                        <?php
                        $date = date('Y-m-d');
                        $sampleResult = DB::select("SELECT MAX(CONVERT(sampleNo, SIGNED INTEGER)) as csno FROM lps where Lab_lid = '" . $_SESSION['lid'] . "' and date='" . $date . "'");
                        foreach ($sampleResult as $result) {
                            $sampleNo = $result->csno;
                            if ($sampleNo == '0' | $sampleNo == '' | $sampleNo == 'null') {
                                $sampleNo = 1;
                            } else {
                                $currentNo = preg_replace("/[^0-9]/", "", $sampleNo);
                                $sampleNo = $currentNo + 1;
                            }
                        }
                        ?>
                        <td height="22"><span class="fieldText">Sample NO</span></td>
                        <td>
                            <input type="text" name="sampleno" id="sampleno" class="input-text" style="width:20%; margin-left: 4px; height: 35px; font-size: 16px; font-weight: bold; border-radius: 3; border-color: #2c418e; border-width: 2px; border-style: solid;" value="{{ $sampleNo }}" pattern="[A-Za-z0-9]{1,5}" title="Valid sample number including digit or letter,Maximum 5 charactors." required>
                        </td>
                    </tr>

                    <tr>
                        <td height="31"><span class="fieldText">Contact NO</span></td>
                        <td>
                            <table width="100%">
                                <tr>
                                    <td width="95%"><input type="text" name="pnno" id="pnno" value="{{ $tpno or '' }}" class="input-text" style="width:94%;" pattern="[0-9]{10}" title="Valid 10 digit phone number" {{ $tpnoenb or '' }} /></td>
                                    <td><input type="checkbox" id="pnnoenbl" onchange="checkBoxEnb(id, 'pnno')" tabindex="-1"></td>
                                </tr>
                            </table>                           

                        </td>
                    </tr>
                    <tr>                        
                        <td height="59"><span class="fieldText">Address</span></td>
                        <td>
                            <table width="100%">
                                <tr>
                                    <td width="95%"><textarea onKeyUp="loadPHistory()" cols="50" rows="3" name="address" id="address" class="text-area" style="width:94%" onKeyUp="resetAddress()" {{ $addressenb or '' }} />{{ $address or '' }}</textarea></td> 
                                    <td><input type="checkbox" id="addenbl" onchange="checkBoxEnb(id, 'address')" tabindex="-1"></td> 
                                </tr>
                            </table>
                        </td>
                    </tr>


                    <tr>
                        <td height="19">&nbsp;</td>
                        <td>                              
                            <input type="hidden" id="selectedPid" name="selectedpid" value="{{ $pid or '' }}">
                        </td>
                    </tr>
                </table>

                @if(isset($viewinvoice) && !$viewinvoice)
                <input id="tot" type="hidden" name="tot" value="0">
                <input id="discount" type="hidden" name="discount" value="0">
                <input id="disPre" type="hidden" name="disPre" value="0">
                <input id="dc" type="hidden" name="dc" value="0">
                <input id="gtot" type="hidden" name="gtot" value="0">
                <input type="hidden" name="paym" value="Cash">
                <input id="payment" type="hidden" name="payment" value="0">
                @endif

                @if(isset($viewinvoice) && $viewinvoice)
                <p><span class="tableHead" style="margin-left: -15px;">Invoice Details</span></p>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="130"  class="fieldText">Total Cost</td>
                        <td width="38"  class="fieldText">: {{ $cuSymble }}</td>
                        <td  class="fieldText">

                            <input type="text" name="tot" id="tot" class="input-text" tabindex="-1" style="width: 200px; margin-bottom: 5px; background-color: #f0f0f0;" value="0" {{ $totenb or '' }} required>

                        </td>
                    </tr>
                    <tr>
                        <td  class="fieldText">Discount</td>
                        <td class="fieldText">:</td> 
                        <td class="fieldText"> 
                            <select id="discount" name="discount" tabindex="-1" onchange="selectDis()" class="select-basic" style="width: 95px; margin-bottom: 5px;" {{ $discountenb or '' }}>
                                    <option value="0">None : 0%</option>  
                                        <?php
                                        $discountResult = DB::select("select * from Discount where lab_lid = '" . $_SESSION['lid'] . "' order by value ASC");
                                        foreach ($discountResult as $result) {
                                            $disName = $result->name;
                                            $disVal = $result->value;
                                            $did = $result->did;

                                            if ($discountdv == $did) {
                                                $select = "selected='selected'";
                                            } else {
                                                $select = "";
                                            }
                                            ?>
                                    <option value="{{ $did }}" {{ $select or '' }} >{{ $disName }} : {{ $disVal }}%</option>  
                                    <?php
                                }
                                ?>
                            </select>        

                            <input type="text" id="disPre" tabindex="-1" name="disPre" size="8" value="0" class="input-text" class="input-text" style="width: 100px;"  pattern="[0-9.]{1,20}" title="only digits" {{ $discountenb2 or '' }} required> %
                                   <input type="hidden" id="dc" name="dc" value="">
                        </td>
                    </tr>
                    <tr>
                        <td height="24" class="fieldText">Grand Total</td>
                        <td class="fieldText">: {{ $cuSymble }}</td>
                        <td class="fieldText"><input type="text" name="gtot" id="gtot" tabindex="-1" value="0" class="input-text" style="width: 200px; margin-bottom: 5px; background-color: #f0f0f0; font-weight: bold;" onkeyup="enterPayment()" {{ $gtotenb or '' }} required></td>
                    </tr>
                    <tr>
                        <td height="24" class="fieldText">Payment Method</td>
                        <td class="fieldText">:</td>
                        <td class="fieldText">
                            <input type="radio" name="paym" value="Cash" checked="checked" tabindex="-1" class="input-radio" style="margin-left: 0px; margin-bottom: 5px;" {{ $paymethenb or '' }} {{ $paymethCash or '' }}>Cash
                                   <input type="radio" name="paym" value="Card" class="input-radio" tabindex="-1" {{ $paymethenb or '' }} {{ $paymethCard or '' }}>Card
                                   <input type="radio" name="paym" value="Online" class="input-radio" tabindex="-1" {{ $paymethenb or '' }} {{ $paymethOnline or '' }}>Online
                        </td>
                    </tr>
                    <tr>
                        <td height="24" class="fieldText">Payment </td>
                        <td class="fieldText">: {{ $cuSymble }}</td>
                        <td class="fieldText">
                            <input type="text" id="payment" name="payment" value="0" {{ $focusonpaymentfocus or '' }} class="input-text" class="input-text" style="width: 200px; margin-bottom: 5px;" onkeyup="enterPayment()" {{ $paymentenb or '' }} onclick="clickPayment()" onfocusout="clickPaymentOut()" pattern="[0-9.]{1,20}" title="only digits" required>                                       
                        </td>
                    </tr>
                    <tr>
                        <td height="27" class="fieldText">Due Payment</td>
                        <td class="fieldText">: {{ $cuSymble }}</td>
                        <td id="due"></td>
                    </tr>
                </table>  
                @endif

                <table width="100%">
                    <tr>
                        <td valign='top'><input type="reset" class="btn" tabindex="-1" style="width: 140px; margin-left: -5px; margin-right: 65px; background-color: #cccccc;" value="Reset" class="formButton" onclick="resetFields()">
                            <input type="button" value="Get Last Patient" class="btn" onclick="getLastPatient()" style="width: 140px; margin-left: -5px; margin-right: 65px; background-color: #cccccc;">
                        </td>
                        <td align="right">                            
                            <input type="checkbox" name="invoice" style="margin-bottom: 40px;" {{ $printinvoicedv or '' }} tabindex="-1"/> Print Invoice
                        </td>
                        <td valign='top' align="right"><input type="button" onclick="submitForm()" class="btn" style="width: 150px;  background-color: #00cc00; border-color: #99ff99; color: #ffffff;" name="submit" value="Register" class="formButton">
                            <input type="hidden"  id="submited" name="submited" value="no">

                        </td>
                    </tr>
                </table>


            </td>

            <td valign="top">
                <div style="width:1px; height:550px; background-color:#006; margin-left: 20px; margin-right: 20px;"></div>                           
            </td>

            <td valign="top" id="testDetails">
                @if($patientsuggestion)
                <!--patient suggest-->                    
                <div id="pHisPanel" class="pHisPanel"> 
                    <table width="100%" border="0">
                        <tr>
                            <td align="center"><p class="Normaltext" style="color: #001092; line-height: 0px; margin: 0px;">Patient Suggestions</p></td>
                            <td align="right"><img src="images/remove.png" id="closePHP" style="cursor: pointer;" onclick="closepHp()"></td>
                        </tr>
                    </table>

                    <table id="phTable" class="normalTable" width="100%">

                    </table>
                </div>
                <!--suggest end-->
                @endif

                <table id="tabButtons">
                    <tr>
                        <td>
                            <div class="formTab" style="padding-left: 15px; padding-right: 15px" onclick="loadTests('testgroups')">
                                Testing Groups
                            </div>
                        </td>
                        <td>
                            <div id="allt" class="formTab" style="padding-left: 20px; padding-right: 20px" onclick="loadTests('allTlist')">
                                All Testings
                            </div>

                        </td>
                    </tr>
                </table>
                <div id="testArea" class="TestListDiv" style="margin-left: 10px;">
                    <table>
                        <tr>
                            <td valign="top"><!-- Test Group Div -->

                                <div id="testgroups" class="tDiv2">

                                    <?php
                                    $TestingsResult = DB::select("SELECT name,tgid,b.price FROM Lab_has_test a, Testgroup b where b.tgid = a.Testgroup_tgid and a.lab_lid='" . $_SESSION['lid'] . "' and b.Lab_lid='" . $_SESSION['lid'] . "' group by b.name");
                                    foreach ($TestingsResult as $result) {
                                        $testName = $result->name;
                                        $testPrice = $result->price;
                                        $TGID = $result->tgid;
                                        ?>

                                        <p style="font-weight: bold;"><label><input type="checkbox" id="{{ $testPrice }}" onclick="clickTGroup(this, 'group-{{ $testName }}', id)">{{ $testName }} : {{$cuSymble}} {{$testPrice}}</label></p>
                                        <ul class="testList" style="margin-left: -10px; margin-top: 0px;">                                          
                                            <?php
                                            $TestingsGroupResult = DB::select("select a.tid,a.name, b.price from Lab_has_test b,test a where a.tid=b.test_tid and b.lab_lid='" . $_SESSION['lid'] . "'and Testgroup_tgid='" . $TGID . "'");
                                            foreach ($TestingsGroupResult as $result) {
                                                $tID = $result->tid;
                                                $tName = $result->name;
                                                $tPrice = $result->price;
                                                ?>
                                                <li><label><input type="checkbox" id="{{ $tPrice }}" name="test-{{ $tID }}" class="{{ $testName }}" onchange="cboxEvent(this, name, id, '{{ $testName }}')">{{ $tName }}</label></li>  
                                                <?php
                                            }
                                            ?>
                                        </ul>
                                        <?php
                                    }
                                    ?>


                                </div>
                                <!--name="group-{{ $testName }}"-->
                                <!-- Test Group Div End -->
                            </td>
                            <td valign="top">                                                    
                                <!-- all tests Div -->
                                <div id="allTlist" class="tDiv">
                                    <ul>
                                        <?php
                                        $TestingsResult = DB::select("select a.name, b.price from Lab_has_test b,test a where a.tid=b.test_tid and b.lab_lid='" . $_SESSION['lid'] . "'");
                                        foreach ($TestingsResult as $result) {
                                            $testName = $result->name;
                                            $testPrice = $result->price;
                                            ?>

                                            <li class="testList" style="margin-bottom: 5px;">
                                                <input type="checkbox" id="{{ $testPrice }}" name="test-{{ $tID }}" onclick="cboxEvent(this, name, id, '')" tabindex="-1">{{ $testName }} - Price : {{ $testPrice }}
                                            </li>  

                                            <?php
                                        }
                                        ?>
                                    </ul>


                                </div>

                                <!-- all tests Div end -->
                            </td>

                        </tr>
                    </table>
                </div>
            </td>

            <td valign="top">
                <div style="width:1px; height:550px; background-color:#006; margin-left: 20px;"></div>                           
            </td>
        </tr>
        <tr>
            <td>

<!--                    <table width="100%">
                        <tr>
                            <td><input type="reset" class="btn" tabindex="-1" style="width: 100px; margin-left: -5px; margin-right: 65px; background-color: #cccccc;" value="Reset" class="formButton" onclick="resetFields()">
                            </td>
                            <td align="right">
                                <input type="checkbox" name="invoice" tabindex="-1"/> Print Invoice
                            </td>
                            <td align="right"><input type="button" onclick="submitForm()" class="btn" style="width: 150px;  background-color: #00cc00; border-color: #99ff99; color: #ffffff;" name="submit" value="Register" class="formButton">
                                <input type="hidden"  id="submited" name="submited" value="no">
                            </td>
                        </tr>
                    </table>-->

            </td>
            <td></td>
            <td></td>
            <td id="errorp" width="100px">
                <?php
                if (isset($_GET['status'])) {
                    ?>
                    <p style="color:blue"><?php echo $_GET['status']; ?></p>
                    <?php
                }
                ?>
            </td>
        </tr>

    </table>
</form>

<input type="hidden" id="directenter" value="{{ $directenter or '' }}">
<?php
    }
?>
@stop

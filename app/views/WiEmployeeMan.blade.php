<?php
if (!isset($_SESSION)) {
    session_start();
}
?>
@extends('Templates/WiTemplate')

@section('title')
Employee Management
@stop

@section('head')
<script type="text/javascript">

    var xmlHttp;
    function createXMLHttpRequest() {
        if (window.XMLHttpRequest) {
            xmlHttp = new XMLHttpRequest();
        } else if (window.ActiveXObject) {
            xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
        } else {
            alert("Please Update your web browser!");
        }
    }

    var x = 0;
    function selectEmp(id) {
        var fn = 'fn+' + id;
        var mn = 'mn+' + id;
        var ln = 'ln+' + id;
        var gen = 'gen+' + id;
        var co = 'co+' + id;
        var dob = 'dob+' + id;
        var nic = 'nic+' + id;
        var ut = 'ut+' + id;
        var posi = 'posi+' + id;
        var br = 'br+' + id;
        var add = 'add+' + id;
        var em = 'em+' + id;
        var tp = 'tp+' + id;
        var hp = 'hp+' + id;
        var st = 'st+' + id;

        var un = 'un+' + id;
        var pw = 'pw+' + id;
        var sec1 = 'sec1+' + id;
        var sec2 = 'sec2+' + id;
        var guest = 'guest+' + id;

        var gender = document.getElementById(gen).innerHTML;
        if (gender === "Male") {
            document.getElementById('female').checked = false;
            document.getElementById('male').checked = true;
        } else {
            document.getElementById('male').checked = false;
            document.getElementById('female').checked = true;
        }

        var status = document.getElementById(st).innerHTML;
        
//        alert(document.getElementById(guest).value);
        
        if(document.getElementById(guest).value === "1"){
             document.getElementById("guest").checked = true;
        }else{
            document.getElementById("guest").checked = false;
        }



        if (status === "1") {
            $('#status').val('Confirmed');
        } else if (status === "Deactivated") {
            $('#status').val('Deactivated');
        } else if (status === "Resigned") {
            $('#status').val('Resigned');
        }

        document.getElementById('fname').value = document.getElementById(fn).innerHTML;
        document.getElementById('mname').value = document.getElementById(mn).innerHTML;
        document.getElementById('lname').value = document.getElementById(ln).innerHTML;
        document.getElementById('country').value = document.getElementById(co).value;
        document.getElementById('dob').value = document.getElementById(dob).value;
        document.getElementById('nic').value = document.getElementById(nic).value;

        var ut = document.getElementById(ut).innerHTML;
        if (ut === "Admin") {
            document.getElementById('ut').options[1].selected = true;
        } else if (ut === "labuser") {
            document.getElementById('ut').options[2].selected = true;
        } else if (ut === "patient") {
            document.getElementById('ut').options[3].selected = true;
        }

        document.getElementById('position').value = document.getElementById(posi).innerHTML;
        document.getElementById('branch').value = document.getElementById(br).innerHTML;
        document.getElementById('address').innerHTML = document.getElementById(add).value;
        document.getElementById('email').value = document.getElementById(em).value;
        document.getElementById('tpno').value = document.getElementById(tp).innerHTML;
        document.getElementById('hpno').value = document.getElementById(hp).innerHTML;

        if ((document.getElementById(un).value) !== "") {
            document.getElementById('un').value = document.getElementById(un).value;
            document.getElementById('pw').value = document.getElementById(pw).value;
            document.getElementById('sec1').value = document.getElementById(sec1).value;
            document.getElementById('sec2').value = document.getElementById(sec2).value;
        } else {
            document.getElementById('notelog').innerHTML = "<p style='color:red'>You have no privilege to  view login details.</p>";

        }
        document.getElementById('luid').value = id;        
        document.getElementById('luidimg').value = id;

        loadPrivs(id);
    }

    function submitReq() {
        if (x === 1) {
            var c = confirm("Are you sure you want to delete this refference?");
            if (c) {
                return true;
            } else {
                return false;
            }
        } else {
            if (document.getElementById('name').value === "" && document.getElementById('company').value === "") {
                alert("Please fill Details!");
                return false;
            } else {
                return true;
            }
        }
    }

    var opArray = [];
    function selectOP() {
        var exsist = false;
        var option = document.getElementById('oplist').value;
        if (option !== "") {
            if (opArray.length === 0) {
                opArray.push(option);
            } else {
                for (var a = 0; a < opArray.length; ++a) {
                    if (opArray[a] === option) {
                        exsist = true;
                    }
                }
                if (!exsist) {
                    opArray.push(option);
                }
            }
            refreshOPS();
        }
    }

    function removeOp(name) {
        for (var x = 0; x < opArray.length; ++x) {
            if (opArray[x] === name) {
                opArray.splice(x, 1);
            }
        }
        refreshOPS();
    }

    function refreshOPS() {
        var tableEle = "<table class='table-basic'>";

        for (var i = 0; i < opArray.length; ++i) {
            tableEle += "<tr><td width='320px'>" + opArray[i] + "</td><td><input type='button' name='" + opArray[i] + "' onClick='removeOp(name)' value='Remove'></td></tr>";
        }
        tableEle += "</table>";

        document.getElementById('EMPoptions').innerHTML = tableEle;

    }

    function loadPrivs(luid) {
        document.getElementById('EMPoptions').innerHTML = "";
        createXMLHttpRequest();
        var url = "loadUserPrivillages?luid=" + luid;

        xmlHttp.open("GET", url, true);
        xmlHttp.send();
        xmlHttp.onreadystatechange = function () {
            if (xmlHttp.readyState === 4 && xmlHttp.status === 200) {
                if (xmlHttp.responseText === "You have no permision to View Privilleges of Users.") {
                    document.getElementById('EMPoptions').innerHTML = xmlHttp.responseText;
                } else {

                    var pArray = xmlHttp.responseText.split(",");

                    opArray = [];
                    for (var i = 0; i < pArray.length - 1; ++i) {
                        opArray.push(pArray[i]);
                    }

                    refreshOPS();
                }

            }
        };
    }

    function SaveEmp(submit) {
        createXMLHttpRequest();

        var inner = $('#EMPoptions')[0].childElementCount;

        if (inner === 0) {
            alert("Please select privileges for this user!");
        } else {

            var fname = document.getElementById('fname').value;
            var mname = document.getElementById('mname').value;
            var lname = document.getElementById('lname').value;
            var country = document.getElementById('country').value;
            var dob = document.getElementById('dob').value;
            var nic = document.getElementById('nic').value;
            var ut = document.getElementById('ut').value;
            var pos = document.getElementById('position').value;
            var branch = document.getElementById('branch').value;
            var address = document.getElementById('address').value;
            var email = document.getElementById('email').value;
            var tpno = document.getElementById('tpno').value;
            var hpno = document.getElementById('hpno').value;

            var gen = "";
            if (document.getElementById('female').checked) {
                gen = "female";
            } else {
                gen = "male";
            }

            var un = document.getElementById('un').value;
            var pw = document.getElementById('pw').value;
            var sec1 = document.getElementById('sec1').value;
            var sec2 = document.getElementById('sec2').value;

            var status = document.getElementById('status').value;            
            
            var guest = "";
            
            if (document.getElementById('guest').checked) {
                guest = "1";
            } else {
                guest = "0";
            }
            
            var luid = document.getElementById('luid').value;

            var privs = opArray.toString();

            var url = "EmpMan?fname=" + fname + "&lname=" + lname + "&mname=" + mname + "&gender=" + gen + "&country=" + country + "&nic=" + nic + "&dob=" + dob + "&ut=" + ut + "&position=" + pos + "&branch=" + branch + "&email=" + email + "&address=" + address + "&tpno=" + tpno + "&hpno=" + hpno + "&un=" + un + "&pw=" + pw + "&sec1=" + sec1 + "&sec2=" + sec2 + "&status=" + status + "&luid=" + luid + "&privs=" + privs + "&submit=" + submit+ "&guest=" + guest;

            xmlHttp.open("GET", url, true);
            xmlHttp.send();

            xmlHttp.onreadystatechange = function () {

                if (xmlHttp.readyState === 0 | xmlHttp.readyState === 1 | xmlHttp.readyState === 2 | xmlHttp.readyState === 3) {
                    document.getElementById('sv').disabled = true;
                    document.getElementById('up').disabled = true;
                    document.getElementById('dl').disabled = true;

                } else if (xmlHttp.readyState === 4 && xmlHttp.status === 200) {
                    if (xmlHttp.responseText === "User Already Exsist!") {
                        alert("User Already Exsist!");
                    } else {
                        if (submit === "Save") {
                            alert("Employee Saved!");
                            document.getElementById('sv').disabled = false;
                            document.getElementById('up').disabled = false;
                            document.getElementById('dl').disabled = false;
                        } else {
                            alert("Employee Updated!");
                            document.getElementById('sv').disabled = false;
                            document.getElementById('up').disabled = false;
                            document.getElementById('dl').disabled = false;
                        }
                    }
                    location.reload();
                }
            };
        }
    }

    function TRMEmp() {
        var luid = document.getElementById('luid').value;
        var url = "EmpMan?luid=" + luid + "&submit=Terminate";

        xmlHttp.open("POST", url, true);
        xmlHttp.send();

        xmlHttp.onreadystatechange = function () {
            if (xmlHttp.readyState === 4 && xmlHttp.status === 200) {
                if (xmlHttp.responseText === "Successfully Terminated!") {
                    alert("Successfully Terminated!");
                    location.reload();
                } else {
                    alert("Error Terminating Employee!");
                    document.getElementById('sv').disabled = false;
                    document.getElementById('up').disabled = false;
                    document.getElementById('dl').disabled = false;
                }

            } else {
                document.getElementById('sv').disabled = true;
                document.getElementById('up').disabled = true;
                document.getElementById('dl').disabled = true;
            }
        };
    }    

</script>
@stop

@section('body')
<?php
if (isset($_SESSION['lid']) & isset($_SESSION['luid'])) {
    ?>
    <blockquote>
        <h2 class="pageheading">Employee Management</h2>
        <p>&nbsp;</p>
        <form action="EmpMan" method="POST" onsubmit="return submitReq()" id="form">
            <table border="0" cellspacing="0" cellpadding="0">
                <tr valign="top">
                    <td height="260">
                        <div class="pageTableScope3">
                            <table border="0" cellspacing="0" cellpadding="0" class="table-basic">
                                <tr class="viewTHead">
                                    <th width="70" height="23" scope="col">UID</th>
                                    <th width="123" height="23" scope="col">First Name</th>
                                    <th width="123" height="23" scope="col">Middle Name</th>
                                    <th width="123" height="23" scope="col">Last Name</th>
                                    <th width="123" height="23" scope="col">Gender</th>

                                    <th width="123" height="23" scope="col">User Type</th>
                                    <th width="123" height="23" scope="col">Position</th>
                                    <th width="123" height="23" scope="col">Branch</th>
                                    <th width="110" scope="col">Mobile No</th>
                                    <th width="110" scope="col">Home No</th>
                                    <th width="105" scope="col">Status</th>
                                    <th width="105" scope="col"></th>
                                </tr>

                                <?php
                                $Result = DB::select("select * from user a,labUser b,Lab_labUser c,gender d,country e,loginDetails f where a.uid=b.user_uid and b.luid=c.labUser_luid and a.gender_idgender=d.idgender and b.country_idcountry = e.idcountry and a.loginDetails_idlogindetails=f.idlogindetails and c.lab_lid = '" . $_SESSION['lid'] . "'");
                                foreach ($Result as $result) {
                                    ?>

                                    <tr>
                                        <td id="<?php echo "fn+" . $result->luid; ?>">{{$result-> uid}}</td>
                                        <td id="<?php echo "fn+" . $result->luid; ?>">{{$result-> fname}}</td>
                                        <td id="<?php echo "mn+" . $result->luid; ?>">{{$result-> mname}}</td>
                                        <td id="<?php echo "ln+" . $result->luid; ?>">{{$result-> lname}}</td>
                                        <td id="<?php echo "gen+" . $result->luid; ?>">{{$result-> gender}}</td>

                                        <?php
                                        $ResultUtype = DB::select("select type from usertype where idusertype = '" . $result->usertype_idusertype . "'");
                                        if ($ResultUtype != null) {
                                            foreach ($ResultUtype as $utype) {
                                                ?>
                                                <td id="<?php echo "ut+" . $result->luid; ?>">{{$utype-> type}}</td>
                                                <?php
                                            }
                                        }
                                        ?>

                                        <td id="<?php echo "posi+" . $result->luid; ?>">{{$result->position}}</td>
                                        <td id="<?php echo "br+" . $result->luid; ?>">{{$result->branch}}</td>
                                        <td id="<?php echo "tp+" . $result->luid; ?>">{{$result->tpno}}</td>
                                        <td id="<?php echo "hp+" . $result->luid; ?>">{{$result->hpno}}</td>
                                        <td id="<?php echo "st+" . $result->luid; ?>">{{$result->status}}</td>
                                        <td>

                                            <input type="hidden" id="<?php echo "co+" . $result->luid; ?>" value="{{$result->country}}">
                                            <input type="hidden" id="<?php echo "dob+" . $result->luid; ?>" value="{{$result->dob}}">
                                            <input type="hidden" id="<?php echo "nic+" . $result->luid; ?>" value="{{$result->nic}}">
                                            <input type="hidden" id="<?php echo "add+" . $result->luid; ?>" value="{{$result->address}}">
                                            <input type="hidden" id="<?php echo "em+" . $result->luid; ?>" value="{{$result->email}}">
                                            <input type="hidden" id="<?php echo "guest+" . $result->luid; ?>" value="{{$result->guest}}">

                                            <?php
                                            $ResultUserid = DB::select("select uid from user a,labUser b,usertype c where a.uid = b.user_uid and a.usertype_idusertype=c.idusertype and c.type='admin' and b.luid='" . $result->luid . "'");
                                            if ($ResultUserid != null) {
                                                foreach ($ResultUserid as $userid) {
                                                    ?>
                                                    <input type="hidden" id="<?php echo "un+" . $result->luid; ?>" value="{{$result->username}}">
                                                    <input type="hidden" id="<?php echo "pw+" . $result->luid; ?>" value="{{$result->password}}">
                                                    <input type="hidden" id="<?php echo "sec1+" . $result->luid; ?>" value="{{$result->seq1}}">
                                                    <input type="hidden" id="<?php echo "sec2+" . $result->luid; ?>" value="{{$result->seq2}}">
                                                    
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                <input type="hidden" id="<?php echo "un+" . $result->luid; ?>" value="">
                                                <?php
                                            }
                                            ?>

                                            <input type="button" id="{{$result->luid}}" name="select" value="Select" class="btn" onclick="selectEmp(id)">

                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </div>

                    </td>
                </tr>
                <tr valign="top">
                    <td height="943"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="44%" height="943" valign="top">
                                    <div style="height: 20px;"></div>
                                    <p class="tableHead">Manage Employees</p>

                                    <table width="448" border="0" cellspacing="0" cellpadding="5">
                                        <tr>
                                            <td style="padding-left: 180px;"></td>
                                            <td></td>
                                        <tr>
                                        <tr>
                                            <td class="fieldText">First Name</td>
                                            <td width=""><input name="fname" id="fname" type="text" class="input-text"></td>
                                        </tr>
                                        <tr>
                                            <td class="fieldText">Middle Name</td>
                                            <td><input name="mname" id="mname" type="text" class="input-text"></td>
                                        </tr>
                                        <tr>
                                            <td class="fieldText">Last Name</td>
                                            <td><input type="text" id="lname" name="lname" class="input-text"></td>
                                        </tr>
                                        <tr>
                                            <td class="fieldText">Gender</td>
                                            <td><input type="radio" name="gender" id="male" value="Male" checked="checked" class="input-radio">
                                                Male
                                                <input type="radio" name="gender" id="female" value="Female">
                                                Female
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fieldText">Country</td>
                                            <td><input type="text" id="country" name="country" class="input-text"></td>
                                        </tr>
                                        <tr>
                                            <td class="fieldText">Date of Birth</td>
                                            <td><input name="dob" id="dob" type="date" class="input-text" style="width: 170px;"></td>
                                        </tr>
                                        <tr>
                                            <td class="fieldText">NIC</td>
                                            <td><input name="nic" id="nic" type="text" class="input-text"></td>
                                        </tr>
                                        <tr>
                                            <td class="fieldText">User Type</td>
                                            <td>
                                                <select  id="ut" name="ut" class="select-basic" style="width: 193px; height: 26px;">

                                                    <?php
                                                    $ResultUsertype = DB::select("select * from usertype");
                                                    foreach ($ResultUsertype as $usertype) {
                                                        ?>
                                                        <option>{{$usertype->type}}</option>
                                                        <?php
                                                    }
                                                    ?>

                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fieldText">Position</td>
                                            <td><input name="position" id="position" type="text" class="input-text"></td>
                                        </tr>
                                        <tr>
                                            <td class="fieldText">Branch</td>
                                            <td><input name="branch" id="branch" type="text" class="input-text"></td>
                                        </tr>
                                        <tr>
                                            <td class="fieldText">Address</td>
                                            <td><textarea id="address" name="address" rows="3" cols="22" class="text-area"></textarea></td>
                                        </tr>
                                        <tr>
                                            <td class="fieldText">Email</td>
                                            <td><input name="email" id="email" type="email" class="input-text"></td>
                                        </tr>
                                        <tr>
                                            <td class="fieldText">Mobile No</td>
                                            <td><input name="tpno" id="tpno" type="tel" class="input-text"></td>
                                        </tr>
                                        <tr>
                                            <td class="fieldText">Home T.P.No</td>
                                            <td><input name="hpno" id="hpno" type="tel" class="input-text"></td>
                                        </tr>
                                        <tr>
                                            <td class="fieldText">Status</td>
                                            <td><select id="status" name="status" class="select-basic" style="width: 193px; height: 26px;">
                                                    <option>Confirmed</option>
                                                    <option>Deactivated</option>
                                                    <option>Resigned</option>
                                                </select>
                                            </td>
                                        </tr>
                                            <td class="fieldText">Login As Guest</td>
                                            <td>
                                                <input type="checkbox" id="guest" name="guest" /> Guest 
                                            </td>
                                        <tr>
                                            
                                        </tr>
                                        
                                        <tr>
                                            <td class="fieldText">&nbsp;</td>
                                            <td id="notelog">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td class="fieldText">User Name</td>
                                            <td><input type="text" name="un" id="un" class="input-text"></td>
                                        </tr>
                                        <tr>
                                            <td class="fieldText">Password</td>
                                            <td><input type="password" name="pw" id="pw" class="input-text"></td>
                                        </tr>
                                        <tr>
                                            <td class="fieldText">Security Question One : What is your First Occupation?</td>
                                            <td><input type="text" name="sec1" id="sec1" class="input-text"></td>
                                        </tr>
                                        <tr>
                                            <td class="fieldText">Security Question Two : What is your Birth City?</td>
                                            <td><input type="text" name="sec2" id="sec2" class="input-text"></td>
                                        </tr>                                    
                                        <tr>

                                            <td style="padding: 5px;" ><input style="width: 100px;" name="submit" type="button" class="btn" value="Save" id="sv" onClick="SaveEmp('Save')"></td>
                                            <td style="padding : 5px;" ><input style="width: 100px;" name="submit" type="button" class="btn" value="Update" id="up" onClick="SaveEmp('Update')"></td>
                                            <td style="padding: 5px;" ><input style="width: 100px;" name="submit" type="button" class="btn" value="Tarminate" id="tr" onClick=" TRMEmp()"></td>
                                            <td style="padding: 5px;" ><input style="width: 100px;" type="button" name="submit" class="btn" value="Delete" id="dl" onClick="SaveEmp('Delete')"></td>

                                        </tr>


                                        <tr>
                                            <td><input type="hidden" id="luid" name="luid" value="{{$result->luid}}"></td>
                                        </tr>
                                        <tr>
                                            <td height="38">&nbsp;</td>
                                        </tr>
                                    </table></td>
                                <td width="1%">&nbsp;</td>
                            <div style="height: 30px;"></div>
                            <td style="height: 20px;" width="53%" valign="top"><p class="tableHead">Manage Privileges</p>
                                <div id="privList"></div>
                                Select Privileges : 
                                <select id="oplist" onchange ="selectOP()" class="select-basic">
                                    <option></option>
                                    <?php
                                    $ResultOptions = DB::select("select * from options where idoptions in (select options_idoptions from Lab_has_options where lab_lid = '" . $_SESSION['lid'] . "')");
                                    foreach ($ResultOptions as $options) {
                                        ?>
                                        <option>{{$options->name}}</option>
                                        <?php
                                    }
                                    ?>
                                </select> 

                                <div id="EMPoptions" class="EMPoptions">

                                </div>

                                <br/>                                
                                <br/>



                            </td>

                </tr>
            </table></td>
            </tr>
            </table>
            @unless(empty($msg))
            <p style="color:red">{{$msg}}</p>
            @endunless
        </form>
        <br/>
        <form action="updatesignimage" method="post" enctype="multipart/form-data">
        <div>
            <table border="0">

                <tbody>
                    <tr>
                        <td>Sign Image</td>
                        <td><input type="file" name="signurl" id="signurl" class="input-file"/></td>
                        <td><input type="submit" class="btn" style="width:120px" value="Update Sign" name="updatesign"/>  
                            <input type="submit" class="btn" style="width:120px" value="Delete Sign" name="updatesign"/> 
                            
                            {{$signmsg or ''}}
                            
                            <input type="hidden" name="signurllbl" id="signurllbl" value="{{ $signurl or '' }}" >
                            <input type="hidden" id="luidimg" name="luid" value="{{$result->luid}}">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        </form>

    </blockquote>
    <?php
}
?>
@stop

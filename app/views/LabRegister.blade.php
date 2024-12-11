<!DOCTYPE html>
<html>
    <head>
        <link href="CSS/outTMP1.css" rel="stylesheet" type="text/css">
        <link href="CSS/Stylie.css" rel="stylesheet" type="text/css">

        <title>Laboratory Registration</title>

        <script type="text/javascript">
            function validate() {

                var name = document.getElementById('name').value;
                var add1 = document.getElementById('add1').value;
                var add2 = document.getElementById('add2').value;
                var country = document.getElementById('country').value;
                var tpno = document.getElementById('tpno').value;
                var email = document.getElementById('email').value;
                var ownerno = document.getElementById('ownerno').value;
                var ownername = document.getElementById('ownername').value;

                if (name == "") {
                    var lblname = document.getElementById('lblname');
                    lblname.innerHTML = "Laboratory Name* <h5>(example : ABC medical laboratory, Abc(pvt)ltd.)</h5>";
                    lblname.style.color = "red";
                } else {
                    var lblname = document.getElementById('lblname');
                    lblname.innerHTML = "Laboratory Name*";
                    lblname.style.color = "black";
                }

                if (add1 == "") {
                    var lblname = document.getElementById('lbladd1');
                    lblname.innerHTML = "Line1* <h5>(example : NO:222/1,Park Street)";
                    lblname.style.color = "red";
                } else {
                    var lblname = document.getElementById('lbladd1');
                    lblname.innerHTML = "Line1*";
                    lblname.style.color = "black";
                }

                if (add2 == "") {
                    var lblname = document.getElementById('lbladd2');
                    lblname.innerHTML = "Line2* <h5>(example : Colombo 05)";
                    lblname.style.color = "red";
                } else {
                    var lblname = document.getElementById('lbladd2');
                    lblname.innerHTML = "Line2*";
                    lblname.style.color = "black";
                }

                if (country == "") {
                    var lblname = document.getElementById('lblcountry');
                    lblname.innerHTML = "country* <h5>(example : Srilanka)";
                    lblname.style.color = "red";
                } else {
                    var lblname = document.getElementById('lblcountry');
                    lblname.innerHTML = "country*";
                    lblname.style.color = "black";
                }

                if (tpno == "") {
                    var lblname = document.getElementById('lbltpno');
                    lblname.innerHTML = "T.P.Number* <h5>(example : 0770123456, 0094770123456)";
                    lblname.style.color = "red";
                } else {
                    var lblname = document.getElementById('lbltpno');
                    lblname.innerHTML = "T.P.Number*";
                    lblname.style.color = "black";
                }

                if (email == "") {
                    var lblname = document.getElementById('lblemail');
                    lblname.innerHTML = "Email Address * <h5>(example : user@abcd.com)";
                    lblname.style.color = "red";
                } else {
                    var lblname = document.getElementById('lblemail');
                    lblname.innerHTML = "Email Address*";
                    lblname.style.color = "black";
                }

                if (ownername == "") {
                    var lblname = document.getElementById('lblownername');
                    lblname.style.color = "red";
                } else {
                    var lblname = document.getElementById('lblownername');
                    lblname.style.color = "black";
                }

                if (ownerno == "") {
                    var lblname = document.getElementById('lblownerno');
                    lblname.innerHTML = "Owner's Contact Number* <h5>(example : 0112123456, 0094112123456)";
                    lblname.style.color = "red";
                } else {
                    var lblname = document.getElementById('lblownerno');
                    lblname.innerHTML = "Owner's Contact Number*";
                    lblname.style.color = "black";
                }

                if (name == "" | add1 == "" | add2 == "" | country == "" | tpno == "" | email == "" | ownerno == "") {
                    alert("Please Fill all fields!")
                    return false;
                } else {

                    //namecheck
                    var nameOnly = /\d/;
                    if (nameOnly.test(name)) {
                        alert("Laboratory Name must be a Name excluding Numbers!");
                        return false;
                    }

                    //country check
                    if (nameOnly.test(country)) {
                        alert("Country must be a Name excluding Numbers!");
                        return false;
                    }

                    //tpno
                    var tno = /^\d+$/;
                    if (!tno.test(tpno)) {
                        alert("T.P.Number must be a Number excluding Letters!");
                        return false;
                    } else if (tpno.length < 10) {
                        alert("T.P.Number must be 10 digits");
                        return false;
                    } else if (tpno.length > 13) {
                        alert("T.P.Number must be below 13 digits");
                        return false;
                    }

                    //email check
                    var atpos = email.indexOf("@");
                    var dot = email.lastIndexOf(".");

                    if (atpos < 1 || dot < 1 || atpos > dot) {
                        alert("Invalied Email address!. Check your email address and try again.");
                        return false;
                    }

                    //ownernamecheck
                    var nameOnly = /\d/;
                    if (nameOnly.test(ownername)) {
                        alert("Owner name must be a name excluding Numbers!");
                        return false;
                    }

                    //ownerstpno
                    if (!tno.test(ownerno)) {
                        alert("Owner's Contact Number must be a Number excluding Letters!");
                        return false;
                    } else if (ownerno.length < 10) {
                        alert("Owner's Contact Number must be 10 digits");
                        return false;
                    } else if (ownerno.length > 13) {
                        alert("Owner's Contact Number must be below 13 digits");
                        return false;
                    }

                }

            }

        </script>
    </head>

    <body background="images/labRegBackground.jpg" style="background-repeat: repeat-y; background-size: 100%;" >

        <h3 id="Heading">Medical Laboratory Registration - MLWS&trade;</h3>

        <h3 style="margin-left: 50px;" id="subheading">Please enter your laboratory details. <span style="color: #990033">(Note: All fields are required)</span></h3>

        <table width="1123" height="477" style="margin:0px">
            <tr>
                <td width="832" height="471" valign="top" class="subheading"><h3 id="subheading">
                        <form action="registerlab" method="post" onsubmit="return validate();">
                            <blockquote>
                                <blockquote>
                                    <blockquote>                        
                                        <blockquote>                        
                                            <h3 id="subheading">General Information</h3>
                                            

                                                <table style="margin:0px" > 
                                                    <tr>
                                                        <td width="200">
                                                            <p id="lblname" name="lblname" class="form-label">Laboratory Name  </p>
                                                        </td>
                                                        <td>
                                                            <input class="input-text" type="text" id="name" name="name" size="49">
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td>
                                                            <p id="lblname" name="lblname" class="form-label">Currency Symbol  </p>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="input-text" name="cus" size="49">
                                                        </td>
                                                    </tr>

                                                </table>

                                            
                                            <p id="subheading"> Contact Details</p>
                                            
                                                <table style="margin:0px" width="600">
                                                    <tr>
                                                        <td width="200">
                                                            <p id="lbladd1" class="form-label">Address Line 1</p>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="input-text" id="add1" name="add1" size="50">
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td >
                                                            <p id="lbladd2" class="form-label">Address Line 2</p>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="input-text" id="add2" name="add2" size="50">
                                                        </td>
                                                    </tr>
                                                </table>

                                                <table style="margin:0px">
                                                    <tr>
                                                        <td>
                                                            <p id="lblcountry" class="form-label"><span class="normaltext">Country  </span></p>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="input-text" id="country" name="country" size="50">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><p id="lbltpno" class="form-label"> T.P.Number  </p></td>
                                                        <td><input type="tel" class="input-text" name="tpno" size="50" id="tpno"></td>
                                                    </tr>
                                                    <tr>
                                                        <td><p id="lblemail"class="form-label"> Email Address  </p></td>
                                                        <td><input type="text" class="input-text" size="50" name="email" id="email"></td>
                                                    </tr>
                                                    <tr>
                                                        <td><p class="form-label" id="lblownername">Owner's Name </p></td>
                                                        <td><input type="text" class="input-text" size="50" id="ownername" name="ownername"></td>
                                                    </tr>
                                                    <tr>
                                                        <td><p id="lblownerno" class="form-label"> Owner's Contact Number  </p></td>
                                                        <td><input type="tel" class="input-text" size="50" id="ownerno" name="ownerno"></td>
                                                    </tr>
                                                </table>


                                                <p style="float: right; margin-top: 0; margin-right: 0">
                                                    <input type="submit" name="submit" value="Register" class="btn" style="width: 200px; margin-right: 187px;">
                                                </p>
                                                <br/>
                                                <p style="color: red;">{{{ $error or '' }}}</p>

                                        </blockquote>
                                    </blockquote>
                                </blockquote>
                            </blockquote>
                        </form>
                    </h3>
                </td>
            </tr>
        </table>

    </body>
</html>

<!DOCTYPE html>
<html>
    <head>
        <link href="CSS/outTMP1.css" rel="stylesheet" type="text/css">
        <link href="CSS/Stylie.css" rel="stylesheet" type="text/css">

        <title>Administrator Registration</title>
        <script type="text/javascript">
            window.onload = function() {
                document.getElementById("loginpanel").style.height = "0px";
                document.getElementById("infopanel").style.height = "1550px";
                document.getElementById('bdate').valueAsDate = new Date();
            }

            function naviDrop(x, y) {

                var panel = document.getElementById(x);
                var infopanel = document.getElementById("infopanel");

                var maxHeight = y;
                if (panel.style.height == maxHeight) {
                    panel.style.height = "0px";
                    infopanel.style.height = "1550px";


                } else {
                    panel.style.height = maxHeight;
                    infopanel.style.height = "0px";
                }

            }

            function validate() {
                var pw = document.getElementById('pw').value;
                var cpw = document.getElementById('cpw').value;

                if (pw === cpw) {
                    return true;
                } else {
                    alert('Password does not match!');
                    return false;
                }
            }

        </script>
    </head>
    <body background="images/labRegBackground.jpg" style="background-repeat: repeat-y; background-size: 100%;">
       
        <h3 id="Heading">Administrator Registration - MLWS&trade;</h3>
        <h3 style="margin-left: 50px;" id="subheading">Please enter your administrator details. <span style="color: #990033">(Note: All fields are required)</span></h3>


        <blockquote>
            <blockquote>
                <table width="497" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="20" height="32"><div onClick="naviDrop('loginpanel', '420px')"><input type="checkbox" name="alrdyreg" class="roundedOne"></div></td>
                        <td width="465"> <h3 id="subheading"><b>I am Already registered in MLWS</b></h3></td>
                    </tr>

                </table>
                <blockquote>
                    <blockquote>
                        <div id="loginpanel" style="margin: 0px; padding:0px">
                            <h3 style="margin-top: 0px;" id="subheading">Sign in here</h3>
                            <blockquote>
                                <blockquote>
                                    <form action="adminregister" method="post" onSubmit="return validate();">
                                        <table style="margin: 0px">
                                            <tr>
                                                <td class="form-label">Username  &nbsp;&nbsp;&nbsp;</td>
                                                <td><input type="text" name="username" size="35" class="input-text"></td>
                                            </tr>
                                            <tr>
                                                <td class="form-label">Password</td>
                                                <td><input type="password" name="password" size="35" class="input-text"></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td align="right"><input name="submit" type="submit" value="Sign In" id="submit" class="btn" style="width: 200px;"/>                                                   
                                            </tr>                                            
                                        </table>
                                        <p style="color: red; margin-top: 0px; margin-left: 30px;">{{{ $errorlog or '' }}}</p>
                                    </form>
                                </blockquote>
                            </blockquote>
                        </div>
                    </blockquote>
                </blockquote>
                <form action="adminregister" method="post" onSubmit="return validate();">
                    <div id="infopanel" style="margin-top: -40px;"> 
                        <blockquote>
                            <blockquote>
                                <table width="1021">
                                    <tr>
                                        <td valign="top" ><h3 id="subheading">General Information
                                            </h3></td>
                                        <td valign="top">&nbsp;</td> 

                                        <td height="22">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td valign="middle" class="subheading">
                                            <p class="form-label">First Name</p>
                                        </td>
                                        <td>
                                            <input name="fname" class="input-text" type="text" size="80" maxlength="30" required>
                                        </td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td valign="middle" class="subheading">
                                            <p class="form-label">Middle Name</p>
                                        </td>
                                        <td><input class="input-text" type="text" name="mname" size="80"></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td valign="middle" class="subheading">
                                            <p class="form-label">Last Name</p>
                                        </td>
                                        <td><input class="input-text" type="text" name="lname" size="80" required></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td valign="middle" class="subheading">
                                            <p class="form-label">Gender</p>
                                        </td>
                                        <td><input class="input-text" type="radio" name="gender" value="Male" checked>
                                            Male
                                            <input class="input-text" type="radio" name="gender" value="Female">
                                            Female</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td valign="middle" class="subheading">
                                            <p class="form-label">Birthday</p>
                                        </td>
                                        <td><input class="input-text" id="bdate" type="date" name="bdate" size="80" style="width: 588px;"></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td valign="middle" class="subheading">
                                            <p class="form-label">NIC Number or<br/>Passport Number</p>

                                        </td>
                                        <td><input class="input-text" name="nic" type="text" required size="80" size="20" pattern="[0-9]{10,20}" title="10-20 digit number"></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td valign="top" class="subheading">
                                            <h3 id="subheading" style="margin-top: 30px;">Carrier Information</h3></td> 
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td valign="middle" class="subheading">
                                            <p class="form-label">Occupation</p>
                                        </td>
                                        <td><input class="input-text" type="text" name="pos" size="80"></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td valign="top" class="subheading">
                                            <p class="form-label">Work Place</p>
                                        </td>
                                        <td><p>
                                                <input class="input-text" type="text" name="wrkplce" size="80"></p>
                                            <div style="width:480px">Note : Enter your work place if you work another place except this place which you are going to attach to this System</div></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td valign="top" class="subheading">
                                            <h3 id="subheading" style="margin-top: 20px;">Contact Informations</h3></td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td valign="middle" class="subheading">
                                            <p class="form-label">Address</p>
                                        </td>
                                        <td><textarea class="text-area" style="width: 588px;" cols="60" rows="3" name="address" required></textarea></td>
                                        <td>&nbsp;</td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <p class="form-label">Country</p>
                                        </td>
                                        <td>
                                            <input class="input-text" type="text" name="country" size="80" required>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td valign="middle" class="subheading">
                                            <p class="form-label">Mobile Number</p>
                                        </td>
                                        <td><input class="input-text" type="text" name="mobno" size="80" required size="10" pattern="[0-9]{10}" title="10 Digit Number"/></td>
                                        <td height="24">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td valign="middle" class="subheading">
                                            <p class="form-label">Offlice Number</p>
                                        </td>
                                        <td><input class="input-text" type="text" name="officeno" size="80" size="10" pattern="[0-9]{10}" title="10 Digit Number" /></td>
                                        <td height="24">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td valign="middle" class="subheading">
                                            <p class="form-label">Home Number</p>
                                        </td>
                                        <td><input class="input-text" type="text" name="hmno" size="80" size="10" pattern="[0-9]{10}" title="10 Digit Number" /></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td valign="middle" class="subheading">
                                            <p class="form-label">Email</p>
                                        </td>
                                        <td><input class="input-text" type="email" name="email" size="80" required></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td valign="top" class="subheading"><h3 style="margin-top: 40px;" id="subheading">Login Details</h3></td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td valign="top" class="subheading">
                                            <p class="form-label">Username</p>
                                        </td>
                                        <td><input class="input-text" type="text" name="un" id="un" size="80" required></td>
                                        <td height="26">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td valign="top" class="subheading">
                                            <p class="form-label">Password</p>
                                        </td>
                                        <td><input class="input-text" type="password" name="pw" id="pw" size="80" required></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td valign="top" class="subheading">
                                            <p class="form-label">Confirm Password</p>
                                        </td>
                                        <td><input class="input-text" type="password" name="pw2" id="cpw" size="80" required></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td valign="top" class="subheading">
                                            <p class="form-label">Security Question 1</p>
                                        </td>
                                        <td>What is your First Occupation?</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td valign="top" class="subheading">
                                            <p class="form-label">Answer 1</p>
                                        </td>
                                        <td><input class="input-text" type="text" name="seq1" id="seq1" size="80" required></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td valign="top" class="subheading">
                                            <p class="form-label">Security Question 1</p>
                                        </td>
                                        <td>Where is your born City?</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td valign="top" class="subheading">
                                            <p class="form-label">Answer 2</p>
                                        </td>
                                        <td><input class="input-text" type="text" name="seq2" id="seq2" size="80" required></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td valign="top" class="subheading"><p>&nbsp;</p></td>
                                        <td align="right"><input style="margin-right: 180px; width: 200px;" type="submit" name="submit" value="Register Admin" class="btn" id="submit"></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                </table>
                                <p style="color: red; margin-top: 0px; margin-left: 30px;">{{{ $error or '' }}}</p>
                            </blockquote>
                        </blockquote>
                    </div>    
                </form>
            </blockquote>
        </blockquote>

    </body>
</html>
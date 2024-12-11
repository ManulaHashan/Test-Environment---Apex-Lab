<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="_token" content="{!! csrf_token() !!}"/>
        <title>Members Area</title>
        <link href="CSS/outPage.css" rel="stylesheet" type="text/css"/>
        <link href="CSS/HomePage.css" rel="stylesheet" type="text/css" />

        <script type="text/javascript">

            function naviDrop(x) {
                var panel = document.getElementById(x);
                var maxHeight = "1";
                if (panel.style.opacity == maxHeight) {
                    panel.style.opacity = "0";

                } else {
                    document.getElementById('userLogindiv').style.opacity = "0";
                    document.getElementById('PatientLogindiv').style.opacity = "0";

                    panel.style.opacity = maxHeight;
                }

            }

            function validate() {

                var un = document.getElementById('un').value;
                var pw = document.getElementById('pw').value;
                var pun = document.getElementById('pun').value;
                var ppw = document.getElementById('ppw').value;

                var msg = document.getElementById('errorMsg');
                var pmsg = document.getElementById('perrorMsg');

                var udiv = document.getElementById('userLogindiv');
                var pdiv = document.getElementById('PatientLogindiv');

                if (udiv.style.opacity === "1") {
                    if (un === "") {
                        msg.innerHTML = "Please Enter Username!";
                        return false;
                    } else if (pw === "") {
                        msg.innerHTML = "Please Enter Password!";
                        return false;
                    } else {
                        return true;
                    }

                } else {
                    if (pun === "") {
                        pmsg.innerHTML = "Please Enter Username!";
                        return false;
                    } else if (ppw === "") {
                        pmsg.innerHTML = "Please Enter Password!";
                        return false;
                    } else {
                        return true;
                    }

                }


            }


        </script>      
    </head>
    <body style="border:0px; padding: 0px; margin: 0px">

<!--        <%
        boolean log = false;
        Cookie ck[] = request.getCookies();
        for(int c = 0; c<ck.length; ++c){
            if(ck[c].getName().equals("LoginP")){
            if(ck[c].getValue().equals("true")){
            response.sendRedirect("../PatientUI/Main.jsp");
            }else{
            log = false;
            }
            }else if(ck[c].getName().equals("LoginU")){
            if(ck[c].getValue().equals("true")){
            response.sendRedirect("../WiMain.jsp");
            }else{
            log = false;
            }
            }
            }
            %>-->
        <form action="userlogin" method="post" onsubmit="return validate();">

            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="outPageHead">&nbsp;&nbsp;&nbsp;Welcome To Members Area</td>
                    <td class="outPageHead"></td>
                </tr>
                <tr >
                    <td id="User">

                        <div class="ULoginBackHead">Laboratory Employee Login</div>

                        <img src="images/labUserLogin.jpg" width="660px" class="UMemLoginBack">

                            <div id="userLogindiv" class="userLogindiv">
                                @if(isset($error) && $error == "erroru")                                
                                <p style="color: red;">Wring Username or Password. Please try again!</p>
                                @endif
                                @if(isset($error) && $error == "disconnected") 
                                <p style="color: red;">Laboratory is not confirmed!</p>
                                @endif   
                                @if(isset($error) && $error == "notactive") 
                                <p style="color: red;">User not active!</p>
                                @endif   

                                <table id="userLoginTbl" width="100%" border="0" cellpadding="0" cellspacing="0" class="loginDivTable">
                                    <tr>
                                        <td width="145" height="31" class="tableHead1"> User Login</td>
                                        <td width="10"></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td height="30" align="right" class="fieldText">
                                            Username
                                        </td>
                                        <td width="10">&nbsp;</td>
                                        <td width="243"><input name="un" type="text" id="un" style="width:90%"></td>
                                    </tr>
                                    <tr>
                                        <td height="28" align="right" class="fieldText">
                                            Password
                                        </td>
                                        <td>&nbsp;</td>
                                        <td><input name="pw" type="password" id="pw" style="width:90%"> </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td id="errorMsg" class="ErrorMessage"><p></p></td>
                                    </tr>
                                    <tr>
                                        <td><p class="fieldText"><input type="checkbox" name="RememberUser" class="fieldText"/>Remember Me</p></td>
                                        <td>&nbsp;</td>
                                        <td><input type="submit" name="submit" Value="Login as User" class="LoginBtn"></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="userLogindivIco"><img src="images/loginBtn.png" class="loginBtnIcon" onclick="naviDrop('userLogindiv')"></div>

                    </td>

                    <td id="Patient">

                        <img src="images/PatientLogin.jpg" width="660px" class="PMemLoginBack">

                            <div class="PLoginBackHead">Patient Login</div>

                            <div id="PatientLogindiv" class="PatientLogindiv">

                                @if(isset($error) && $error == "errorp")
                                <p style="color: red;">Wring Username or Password. Please try again!</p>
                                @endif
                                <table id="PatientLoginTbl" width="100%" height="137" border="0" cellpadding="0" csellspacing="0" class="loginDivTable">
                                    <tr>
                                        <td width="145" height="31" class="tableHead1"> Patient Login</td>
                                        <td width="10"></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td height="30" align="right" class="fieldText">
                                            Username
                                        </td>
                                        <td width="10">&nbsp;</td>
                                        <td width="243"><input name="pun" type="text" id="pun" style="width:90%"></td>
                                    </tr>
                                    <tr>
                                        <td height="28" align="right" class="fieldText">
                                            Password
                                        </td>
                                        <td>&nbsp;</td>
                                        <td><input name="ppw" type="password" id="ppw" style="width:90%"> </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td id="perrorMsg" class="ErrorMessage"><p></p></td>
                                    </tr>
                                    <tr>
                                        <td><p class="fieldText"><input type="checkbox" name="RememberPatient" class="fieldText"/>Remember Me</p></td>
                                        <td>&nbsp;</td>
                                        <td><input type="submit" name="submit" Value="Login as Patient" class="LoginBtn"></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="PatientLogindivIco"><img src="images/loginBtn.png" class="loginBtnIcon" onclick="naviDrop('PatientLogindiv')"></div>

                    </td>
                </tr>
            </table>
            <input type="hidden" name="meth" value="Web">
        </form>
        <div style="height:73px">
            <a href="../ForgetPassword.jsp" class="recoverPass">Recover My Password</a>
        </div>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr valign="middle">
                <td class="menuBtn"><a class="menu_link" href="/about">About</a></td>
                <td class="menuBtn"><a class="menu_link" href="/products">Products</a></td>
                <td class="menuBtn"><a class="menu_link" href="/features">Features</a></td>
                <td class="menuBtn"><a class="menu_link" href="/contact">Contact Us</a></td>
                <td class="menuBtn"><a class="menu_link" href="/memberarea">Members Area</a></td>
            </tr>
        </table>
    </body>
</html>

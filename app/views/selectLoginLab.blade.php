<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="_token" content="{!! csrf_token() !!}"/>
        <title>Select Laboratory</title>
        <link href="CSS/workUI.css" rel="stylesheet" type="text/css">
        <link href="CSS/HomePage.css" rel="stylesheet" type="text/css" />
        <link href="{{ asset('CSS/Stylie.css') }}" rel='stylesheet' type='text/css' />
    </head>
    <body>
        <table width="100%">
            <tr>
                <td height="126" class="TopBanner"><h1 class="TopBannerHeading">Welcome To Medical Laboratory Web System</h1></td>
            </tr>
        </table>
    <center>
        <h1 class="pageheading">MLWS User Login</h1>
        <?php
            if(isset($_SESSION['luid'])){
//                echo $_SESSION['luid'];
            }
        ?>
        
        <br/>
        <br/>
        
        <h4 class="">You have linked with many Laboratory Systems. Please select Laboratory which you want to login.</h4>

        <form action="selectLabforLogin" method="post">
            
            <table width="500" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="150" height="42" class="Normaltext">Laboratory List &nbsp;</td>
                    <td width="275"><label for="lablist"></label>
                        <select name="lablist" id="lablist" class="select-basic" style="height: 50px;">

                            @foreach($labs as $lab)                                
                                <option value="{{ $lab->lid }}">{{ $lab->name }}</option>                                
                            @endforeach                          

                        </select>
                    </td>
                </tr>
                <tr>
                    <td height="19" class="Normaltext">&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td></td>
                    <td id="errorMsg" class="ErrorMessage"></td>
                </tr>
                <tr>
                    <td height="47"></td>
                    <td><input name="Login" value="Login" type="submit" id="Login" class="btn" style="margin-left: o; width: 200px;"></td>
                </tr>
            </table>
        </form>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
    </center>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr valign="middle">
            <td height="60" class="menuBtn"><a class="menu_link" href="index.jsp">Home</a></td>
            <td class="menuBtn"><a class="menu_link" href="WelcomePages/About.html">About</a></td>
            <td class="menuBtn"><a class="menu_link" href="WelcomePages/Products.html">Products</a></td>
            <td class="menuBtn"><a class="menu_link" href="WelcomePages/Features.html">Features</a></td>
            <td class="menuBtn"><a class="menu_link" href="WelcomePages/ContactUs.html">Contact Us</a></td>
            <td class="menuBtn"><a class="menu_link" href="WelcomePages/MembersArea.html">Members Area</a></td>
        </tr>
    </table>
</body> 
</html>

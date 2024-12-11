<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Contact Us</title>
        <link href="CSS/HomePage.css" rel="stylesheet" type="text/css" />

    </head>

    <body style="border:0px; padding: 0px; margin: 0px">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td height="126" class="TopBanner"><h1 class="TopBannerHeading">Welcome To Medical Laboratory Web System</h1></td>
            </tr>
        </table>

        <blockquote>
            <h3 class="PageHeading">Contact Us</h3>
            <table>
                <tr>
                    <td width="500px" valign="top">
                        <p class="head3">Site Admin</p>
                        <p class="paraText">K.A. Samadhi Gunaratna</p>
                        <p class="paraText">Contact No : +(94) 777 136 733</p>
                        <p class="paraText">Email Address : Admin@MLWS.com</p>
                        <p class="paraText">Admin Email : info@appexsl.com</p>
                        <p>&nbsp;</p>

                        <p class="head3">Appex Solutions.</p>
                        <p class="head3">Web Site : www.appexsl.com</p>
                        <p class="paraText">Contact No : +(94) 777 136 733</p>
                        <p class="paraText">Email Address : info@appexsl.com</p>
                    </td>
                    <td valign="top">
                        <form action="Contact.php" method="POST">
                            <table border="0" cellpadding="0" cellspacing="10">
                                <p class="head3">Leave a Message Comment or Complement</p>
                                <tr>
                                    <td><p class="paraText">Subject  :  </p></td>
                                    <td><input id="sub" type="text" name="subject" size="49" pattern="[A-Za-z]{1,100}" title="Enter Valid Subject!" required/></td>
                                </tr>
                                <tr>
                                    <td><p class="paraText">Message  : </p></td>
                                    <td><textarea id="message" rows="5" cols="51" name="msg" required></textarea></td>
                                </tr>
                                <tr>
                                    <td><p></p></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><p class="paraText">Your Name  :  </p></td>
                                    <td><input id="nm" type="text" name="name" size="50" pattern="[A-Za-z]{1,100}" title="Enter Valid Name!"></td>
                                </tr>
                                <tr>
                                    <td><p class="paraText">Your Email Address  :  </p></td>
                                    <td><input id="em" type="text" name="email" size="50"></td>
                                </tr>
                                <tr>
                                    <td><p class="paraText">Your Contact No  :  </p></td>
                                    <td><input id="tel" type="tel" name="tpno" size="50"></td>
                                </tr>
                                <tr>
                                    <td><p class="paraText"></p></td>
                                    <td><input type="submit" name="submit" value="Submit Message">

                                        <?php
                                        if (isset($_POST['submit'])) {

                                            $message = "Subject : " . $_POST['subject'] . " | From Name : " . $_POST['name'] . " | Message : " . $_POST['msg'] . " | Email : " . $_POST['email'] . " | Contact No : " . $_POST['tpno'];

                                            $headers = 'From: mlwssite@appexsl.com' . "\r\n" .
                                                    'X-Mailer: PHP/' . phpversion();

                                            mail('info@appexsl.com', 'Site Comment MLWS', $message, $headers);

                                            echo "<p style='color:blue;'>Your message has been sent! Thank you...</p>";
                                        }
                                        ?>

                                    </td>
                                </tr>
                                <tr>
                                    <td><p class="paraText"></p></td>
                                    <td></td>
                                </tr>

                            </table>
                        </form>

                    </td>
                </tr>
            </table>
        </blockquote>
        <div style="height:38px"></div>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr valign="middle">
                <td height="60" class="menuBtn"><a class="menu_link" href="/mlws/">Home</a></td>
                <td class="menuBtn"><a class="menu_link" href="/about">About</a></td>
                <td class="menuBtn"><a class="menu_link" href="/products">Products</a></td>
                <td class="menuBtn"><a class="menu_link" href="/features">Features</a></td>
                <td class="menuBtn"><a class="menu_link" href="/contact">Contact Us</a></td>
                <td class="menuBtn"><a class="menu_link" href="/memberarea">Members Area</a></td>
            </tr>
        </table>
    </body>
</html>

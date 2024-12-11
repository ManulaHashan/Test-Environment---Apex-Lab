<?php
date_default_timezone_set('Asia/Colombo');
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!--        <link href="CSS/Stylie.css" rel="stylesheet" type="text/css">
                <link href="CSS/ReportStyles.css" rel="stylesheet" type="text/css">-->

        <link href="{{ asset('CSS/Stylie.css') }}" rel='stylesheet' type='text/css' />
        <link href="{{ asset('CSS/ReportStyles.css') }}" rel='stylesheet' type='text/css' />


        <title>@yield('title')</title>        

    </head> 

    @yield('head')

    <body bgcolor="#EEEEEE" style="margin:0px" oncontextmenu="return false">      

        <?php
        $lab = Lab::find($_SESSION['lid']);
        $labName = $lab->name;
        $labAddress = $lab->Address;
        $labTpno = $lab->tpno;
        $labOTpno = $lab->ownertpno;
        $labEmail = $lab->email;
        $labLogo = $lab->logo;

        if ($labLogo == "" | $labLogo == "null") {
            $labLogo = "images/defaultlablogo.jpg";
        }

        $add = explode(',', $labAddress);
        $add1 = $add[0];
        $add2 = $add[1];

        $Result = DB::select("select * from reportconfigs where lab_lid = '" . $_SESSION['lid'] . "'");
        foreach ($Result as $res) {
            $header = $res->header;
            $headerURL = $res->headerurl;
            $footer = $res->footer;
            $footerURL = $res->footerurl;
            $pageheading = $res->pageheading;
            $date = $res->date;
            $sign = $res->sign;
            $fontitelic = $res->fontitelic;
            $confidential = $res->confidential;

            if ($res->headerdefault == null) {
                $headerdefault = 0;
            } else {
                $headerdefault = $res->headerdefault;
            }
        }
        ?>

        @if($confidential)
        <!--<p style="position: absolute; top: 5px; left: 5px; font-family: 'Times New Roman', Times, serif; font-size: 10px;">CONFIDENTIAL</p>-->
        @endif

        @if($fontitelic)
        <i>
            @endif    

            <table width="100%">

                <!--Letter Heading-->
                <tr>
                    <td>

                        @if($headerdefault == 1)

                        <table width="100%" style="font-style: italic;">
                            <tr>
                                <td align="center">
                                    <table>
                                        <tr>
                                            <td align="right"><img src="{{ asset($labLogo) }}" width="100%"/>    </td>
                                            <td><h1 class="Rep_labname">{{ $labName }}</h1></td>
                                        </tr>
                                    </table>                                
                                </td>
                            </tr>
                            <tr>
                                <td>                          
                                    <div style="float: left;">
                                        <p class="hedingText">{{ $add1 }}</p>
                                        <br/>
                                        <p class="hedingText">{{ $add2 }}</p>
                                    </div>
                                    <div style="float: right;">
                                        <p class="hedingTextright">{{ $labEmail }}</p>
                                        <br/>
                                        <p class="hedingTextright">{{ $labTpno }} </p>
                                        <br/>
                                        <p class="hedingTextright">{{ $labOTpno }} </p> 
                                    </div>                               
                                </td>                            
                            </tr>
                            <tr>
                                <td><hr/></td>
                            </tr>
                        </table>

                        @else

                        @if($header == 1)

                        <img src="{{ asset($headerURL) }}" width="100%"/>

                        @else 
                        
                        <div style="height: 180px"></div>

                        @endif

                        @endif


                    </td>
                </tr>                
                <!--end-->
                <tr>
                    <td>
                        @if($pageheading)
                <center>
                    <h2 class="reportHeading">@yield('heading')</h2>
                </center>
                @endif

                <div class="repContent">
                    @yield('content')
                </div>

                </td>
                </tr> 
                <!--Footer-->

                @if($fontitelic)
        </i>
        @endif 
    <tr>
        <td>
            <table>
                <tr>
                    <td>
 
                    </td>
                    <td>
                        <footer style="position: absolute; bottom: 0px; width: 100%;">
                            @if($footer)
                            <img src="{{ asset($footerURL) }}" width="95%"/>
                            
                            <hr/>
                            <p class="reportfooter">Generated By MLWS&trade; - Powered by Appex Solutions - www.appexsl.com</p>
                            @endif
                        </footer>
                    </td> 
                </tr>
            </table>
        </td>

    </tr>
    <!--end-->
</table>
</body>
</html>
<?php
//if (session_id() == '') {
//    session_start();
//}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link href="{{ asset('CSS/n_list.css') }}" rel='stylesheet' type='text/css' />
        <link href="{{ asset('CSS/workUI.css') }}" rel='stylesheet' type='text/css' />
        <link href="{{ asset('CSS/Stylie.css') }}" rel='stylesheet' type='text/css' />
        <script src="{{ asset('JS/jquery-3.1.0.js') }}"></script>

        <title>@yield('title')</title>        

        <script type="text/javascript">

var directLid = "%";

window.onload = function () {

    if ($('#directdate').val() === "") {
        document.getElementById('date').valueAsDate = new Date();
    } else {
        directLid = $('#directlid').val();
//        alert($('#ref').val());
        CheckReports();
    }

    $('#loading').hide();
    $('#framebody').hide();




    $("#tp").focus();

    setSpace();
}

$(document).ajaxStart(function () {
    $('#loading').show();
}).ajaxStop(function () {
    $('#loading').hide();
});

function CheckReports() {

    $('#manyrepviewer').html("");
    $('#bodybox').height(280);

    var pnno = $("#tp").val();
    var url = "";
    
    var pathPrefix = "";
    if($('#directdate').val() !== ""){
        pathPrefix = "../";  
    }

    var refference = "";
    if ($('#refx').length) {
        refference = $("#refx").html().trim();
        url = pathPrefix + "../getPRcheck";
    } else {
        refference = $("#ref").val();
        url = pathPrefix + "getPRcheck";
    }
    
    var date = $("#date").val();
    
//    alert(pnno+" "+refference);
    
//    alert(directLid);

    $.ajax({
        url: url,
        type: 'POST',
        data: {'tp': pnno, 'ref': refference, 'date': date, 'dlid': directLid, '_token': $('input[name=_token]').val()},
        success: function (result) {
//            alert(result);
            if (result !== "notfound") {
                var arr = result.split("&&#&&");
                if (arr[1] === "1") {
                    viewReport(arr[2]);
                } else {
                    $("#manyrepviewer").html(arr[0]);
                    $('#framebody').hide();
                }

                var height = $('#testings').height();

                $('#bodybox').height(350 + height);
            } else { 
                alert("Sorry! Sample number not found!");
            }
        }
    });
}

function getreport(id) {
    viewReport(id);
}

function viewReport(id) {
    
    var pathPrefix = ""; 
    if($('#directdate').val() !== ""){
        pathPrefix = "../";  
    }

    // alert("Your Reference Number is ");

    var pnno = $("#tp").val();
    var url = "";

    var refference = id; 

    if ($('#refx').length) {
        url = pathPrefix+"../getPRDetails";
    } else {
        url = pathPrefix+"getPRDetails";
    }

    var date = $("#date").val();
    
//    alert(directLid);

//    
    
//    alert(ltest);

    $.ajax({ 
        url: url,
        type: 'POST',
        data: {'tp': pnno, 'ref': refference, 'date': date,'dlid': directLid, '_token': $('input[name=_token]').val()}, 
        success: function (result) {

            var ltest = result.split("&")[2];  
//            alert(ltest);

            if (result === "NOTREADY") {
                alert("Sorry! The report is not ready.");
                $('#framebody').hide();
            } else if (result === "NOTEXISTS") {
                alert("Error! Report not found under given details.");
                $('#framebody').hide();
            } else if (result === "NOTPAID" | result === ("NOTPAID& &"+ltest)) {
                alert("Sorry! You have to complete the total payment for this report for enjoy online facility.");
                $('#framebody').hide();
            } else {
                if ($('#refx').length) {
                    $('#frame').attr('src', pathPrefix+'../printreportpvd/' + result + '&onlprep=true');
                } else {
                    $('#frame').attr('src', pathPrefix+'printreportpvd/' + result + '&onlprep=true');
                }
                $('#framebody').show();
            }
        }
    });
}
function disableContextMenu() {
    window.frames["frame"].document.oncontextmenu = function () {
        alert("No way!");
        return false;
    };
    // Or use this
    document.getElementById("frame").contentWindow.document.oncontextmenu = function () {
        alert("No way!");
        return false;
    };
    ;
}

function setSpace() {
    var wheight = $(window).height();
    var bheight = $('#bodyx').height();

    if (wheight > bheight) {
        var x = wheight - bheight - 32;
        $('#space').height(x);
    }
}

function printOnRep(){    
    $("#frame").get(0).contentWindow.print();
}
        </script>
    </head>  

    <style>
        #bodyx{
            /* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#4096ee+0,81bbf4+50,4096ee+100 */
            background: #4096ee; /* Old browsers */
            /* IE9 SVG, needs conditional override of 'filter' to 'none' */
            background: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIxMDAlIiB5Mj0iMCUiPgogICAgPHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iIzQwOTZlZSIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjUwJSIgc3RvcC1jb2xvcj0iIzgxYmJmNCIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiM0MDk2ZWUiIHN0b3Atb3BhY2l0eT0iMSIvPgogIDwvbGluZWFyR3JhZGllbnQ+CiAgPHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEiIGhlaWdodD0iMSIgZmlsbD0idXJsKCNncmFkLXVjZ2ctZ2VuZXJhdGVkKSIgLz4KPC9zdmc+);
            background: -moz-linear-gradient(left, #4096ee 0%, #81bbf4 50%, #4096ee 100%); /* FF3.6-15 */
            background: -webkit-gradient(linear, left top, right top, color-stop(0%,#4096ee), color-stop(50%,#81bbf4), color-stop(100%,#4096ee)); /* Chrome4-9,Safari4-5 */
            background: -webkit-linear-gradient(left, #4096ee 0%,#81bbf4 50%,#4096ee 100%); /* Chrome10-25,Safari5.1-6 */
            background: -o-linear-gradient(left, #4096ee 0%,#81bbf4 50%,#4096ee 100%); /* Opera 11.10-11.50 */
            background: -ms-linear-gradient(left, #4096ee 0%,#81bbf4 50%,#4096ee 100%); /* IE10 preview */
            background: linear-gradient(to right, #4096ee 0%,#81bbf4 50%,#4096ee 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#4096ee', endColorstr='#4096ee',GradientType=1 ); /* IE6-8 */
        }

        #bodybox{
            width: 700px; 
            height: 280px; 
            background-color: white; 
            border-radius: 20px; 
            opacity: 0.9; 
            margin-top: 20px;

            -webkit-box-shadow: 6px 7px 16px -4px rgba(0,0,0,0.75);
            -moz-box-shadow: 6px 7px 16px -4px rgba(0,0,0,0.75);
            box-shadow: 6px 7px 16px -4px rgba(0,0,0,0.75);
        }

        

    </style>
    <body id="bodyx" style="margin:0px; margin-top: 30px;" oncontextmenu="return false">
    <center>
        <h1 style="font-family: Lucida Sans Unicode, Lucida Grande, sans-serif; font-weight: bold; color: #ffffff">Welcome To MLWS Online Report Portal</h1>

        <div id="bodybox">
            <h3 style="padding-top: 20px; margin-bottom: 20px;">Please enter below requested details to view reports</h3>

            <table cellspacing="10">
                <?php
                if (!isset($sno)) {
                    ?>
                    <tr>
                        <td>Patient Mobile NO </td>
                        <td><input type="text" class="input-text" id="tp" name="tp" /></td>
                        <td style="color:slategrey; font-family: Arial;">Eg: 0770123123</td>
                    </tr>
                    <?php
                } 
                ?>
                <tr>
                    <td>Bill Reference NO </td>
                    <td>
                        <?php
                        if (isset($sno)) {
                            ?>
                            : <span id="refx"> {{$sno}} </span>
                            <?php
                        } else {
                            ?>
                            <input type="text" class="input-text" id="ref" name="ref" />
                            <?php
                        }
                        ?>
                    </td>
                    <td style="color:slategrey; font-family: Arial;"></td>
                </tr>
                <tr>
                    <td>Bill Date </td>
                    <td><input type="date" class="input-text" id="date" name="date" min="{{$mindate or '' }}" max="{{$maxdate or '' }}" value="{{$sdate or ''}}" style="width: 160px;" />
                        <input type="hidden" id="directdate" value="{{$sdate or ''}}">
                        <input type="hidden" id="directlid" value="{{$lid or ''}}">
                        
                    </td>
                    <td style="color:slategrey; font-family: Arial;">Month/Day/Year</td>
                </tr>
            </table>

            <table width="150">
                <tr>
                    <td width="75">
                        <input class="btn" type="button" value="View Report" onclick="CheckReports()" style="border-right: 5px solid #ADADAD; border-bottom-left-radius: 15px; background-color: #00cc00; padding-left: 50px; padding-right: 50px; padding-bottom: 20px; padding-top: 20px; color: white;" />
                    </td>
                    <td width="75">
                        <?php
                        if (isset($sno)) {
                            ?>
                            <div style="margin-left: 10px;" id="loading"><img src="../images/load.gif"> Loading...</div>
                            <?php
                        } else {
                            ?>
                            <div style="margin-left: 10px;" id="loading"><img src="images/load.gif"> Loading...</div>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
            </table>
            <div id="manyrepviewer">

            </div>

        </div>

        <br/>
        <div id="framebody">
            <h3 style="color: #ffffff; font-weight: bold; background-color: #666666; display: block; width: 800px; border-top-left-radius: 15px; border-top-right-radius: 15px;">Report View</h3>
            
            <iframe id="frame" src="" width="800" height="1500" ></iframe>
        </div>
        
        <button id="btn" class="btn" onclick="printOnRep()">Print Report</button>

        <br/>



    </center>
    <div id="space"></div>
    <h4 style="color: #ffffff; font-weight: bold; background-color: #666666; "> &nbsp;&nbsp; System MLWS By Appex Solutions - www.appexsl.com</h4>

</body>


</html>
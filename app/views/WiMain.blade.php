@extends('Templates/WiTemplate')

@section('title')
Home
@stop

@section('head')

<!--Calender-->
<link href="{{ asset('app/views/ClocknCalender/css/style.css') }}" rel='stylesheet' type='text/css' />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<link rel="stylesheet" href="{{ asset('app/views/ClocknCalender/css/clndr.css') }}" type="text/css" />
<script src="{{ asset('app/views/ClocknCalender/js/underscore-min.js') }}"></script>
<script src= "{{ asset('app/views/ClocknCalender/js/moment-2.2.1.js') }}"></script>
<script src="{{ asset('app/views/ClocknCalender/js/clndr.js') }}"></script>
<script src="{{ asset('app/views/ClocknCalender/js/site.js') }}"></script>
<!--End Calender-->
<script src="{{ asset('app/views/ClocknCalender/js/jClocksGMT.js') }}"></script>
<script src="{{ asset('app/views/ClocknCalender/js/jquery.rotate.js') }}"></script>
<link rel="stylesheet" href="{{ asset('app/views/ClocknCalender/css/jClocksGMT.css') }}">
<!-- -->

<script type="text/javascript">
$(document).ready(function () {
    $('#naviItemPane').show();
    loadPage();

});
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

function loadPage() {
    //loadWelcomeWindow();
    //window.setInterval('loadNotifications()', 2000);

    loadNotifications();
}

function loadWelcomeWindow() {

    createXMLHttpRequest();

    var url = "UserFirstTimeLogin?utype=labuser&function=check";

    xmlHttp.open("POST", url, true);
    xmlHttp.send();
    xmlHttp.onreadystatechange = function () {
        if (xmlHttp.readyState === 4 && xmlHttp.status === 200) {
            if (xmlHttp.responseText === "true") {

                document.getElementById('pageBlurDiv').className = "FullPageBlurDiv";
                document.getElementById('WelcomeDiv').style.display = 'block';
            } else {
                skipWelcomrTour();
            }
        }
    };
}
function skipWelcomrTour() {
    document.getElementById('pageBlurDiv').className = "";
    document.getElementById('WelcomeDiv').style.display = 'none';
    document.getElementById('DesTitlePanel').style.display = 'none';
    document.getElementById('desTitlepText').style.display = 'none';
    document.getElementById('nvpanel').style.display = 'none';
    document.getElementById('desnvpanel').style.display = 'none';
    document.getElementById('notPanel').style.display = 'none';
    document.getElementById('desnotpanel').style.display = 'none';
    EndWelcomeWindow();

}
function WelcomeTourNext() {
    document.getElementById('WelcomeDiv').style.display = 'none';
    document.getElementById('DesTitlePanel').style.display = 'block';
    document.getElementById('desTitlepText').style.display = 'block';
}
function WelcomeTourNext2() {
    document.getElementById('DesTitlePanel').style.display = 'none';
    document.getElementById('desTitlepText').style.display = 'none';
    document.getElementById('nvpanel').style.display = 'block';
    document.getElementById('desnvpanel').style.display = 'block';
}
function WelcomeTourNext3() {
    document.getElementById('nvpanel').style.display = 'none';
    document.getElementById('desnvpanel').style.display = 'none';
    document.getElementById('notPanel').style.display = 'block';
    document.getElementById('desnotpanel').style.display = 'block';
}
function EndWelcomeWindow() {

    var checkbox = document.getElementById('keeptuor').checked;
    if (!checkbox) {
        createXMLHttpRequest();

        var url = "UserFirstTimeLogin?utype=labuser&function=end";

        xmlHttp.open("POST", url, true);
        xmlHttp.send();
        xmlHttp.onreadystatechange = function () {
            if (xmlHttp.readyState === 4 && xmlHttp.status === 200) {
                if (xmlHttp.responseText === "done") {
                }
            }
        };
    }
}

function loadNotifications() {
    createXMLHttpRequest();
    var url = "loadNotifications";

    xmlHttp.open("POST", url, true);
    xmlHttp.send();
    xmlHttp.onreadystatechange = function () {
        if (xmlHttp.readyState === 4 && xmlHttp.status === 200) {

            if (xmlHttp.responseText !== "") {
                document.getElementById('notificationBody').innerHTML = xmlHttp.responseText;
            } else {
                document.getElementById('notificationBody').innerHTML = "You don't have Notifications";
            }

        } else {

        }
    };
}

function loadExps(){
    window.location = "stock/exp";
}

function loadLowStocks(){
    window.location = "stock/low";
}

</script>

<style type="text/css">
    .notificationBanner{
        height: 23px;
        text-align: center;
        margin-top: 10px;
        background-color: #4472c2;
        border-radius: 15px;
        width: 200px;
        padding: 10px;
        color: white;       
    }
    .notificationBody{
        height: 380px;
        width: 200px;
        background-color: #d7e4f0;
    }
</style>

@stop


@section('body')

<table>    
    <tr>
        <td valign="top">
            <div id="mainpagewelcome">
                @include('ClocknCalender.ClocknCalander') 
            </div>
        </td>

        <td valign="top">
            <table>
                <tr>
                    <td align="center">
                        <div class="notificationBanner">System Notifications</div>

                        <div style="padding:5px;" id="notificationBody" class="notificationBody">

                        </div>

                    </td>
                </tr>
            </table>
        </td>
    </tr>

</table>

<?php
    if($_SESSION['lid'] == "30"){
    ?>
      <p style="color: red; font-weight: bold; font-size: 16pt;">Important Notice: You have a pending payment for the MLWS package. Please settle the amount to continue with uninterrupted service. Thank you...</p>  
    <?php    
    }
?>

<style>
    table.blueTable {
        background-color: #EEEEEE;
        width: 100%;
        height: 200px;
        text-align: left;
        border-collapse: collapse;
        font-family: sans-serif;
    }
    table.blueTable td, table.blueTable th {
        padding: 3px 2px;
    }
    table.blueTable tbody td {
        font-size: 14px;
        font-weight: bold;
        color: #268723;
    }
    table.blueTable td:nth-child(even) {
        background: #BEF5CE;
    }
    table.blueTable tfoot td {
        font-size: 14px;
    }
    table.blueTable tfoot .links {
        text-align: right;
    }
    table.blueTable tfoot .links a{
        display: inline-block;
        background: #1C6EA4;
        color: #FFFFFF;
        padding: 2px 8px;
        border-radius: 5px;
    }
</style>

@stop
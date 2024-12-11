<?php
if (!isset($_SESSION)) {
    session_start();
}
?>
@extends('Templates/WiTemplate')

@section('title')
Add Patient Configurations
@stop

@section('head')
<script type="text/javascript">

</script>    
@stop

@section('body')
<?php
    if (isset($_SESSION['lid']) & isset($_SESSION['luid'])) {
?>
<style type="text/css">
    /* Base for label styling */
    [type="checkbox"]:not(:checked),
    [type="checkbox"]:checked {
        position: absolute;
        left: -9999px;
    }
    [type="checkbox"]:not(:checked) + label,
    [type="checkbox"]:checked + label {
        position: relative;
        padding-left: 1.95em;
        cursor: pointer;
    }

    /* checkbox aspect */
    [type="checkbox"]:not(:checked) + label:before,
    [type="checkbox"]:checked + label:before {
        content: '';
        position: absolute;
        left: 0; top: 0;
        width: 1.25em; height: 1.25em;
        border: 2px solid #ccc;
        background: #fff;
        border-radius: 4px;
        box-shadow: inset 0 1px 3px rgba(0,0,0,.1);
    }
    /* checked mark aspect */
    [type="checkbox"]:not(:checked) + label:after,
    [type="checkbox"]:checked + label:after {
        content: '✔';
        position: absolute;
        top: .1em; left: .3em;
        font-size: 1.3em;
        line-height: 0.8;
        color: #09ad7e;
        transition: all .2s;
    }
    /* checked mark aspect changes */
    [type="checkbox"]:not(:checked) + label:after {
        opacity: 0;
        transform: scale(0);
    }
    [type="checkbox"]:checked + label:after {
        opacity: 1;
        transform: scale(1);
    }
    /* disabled checkbox */
    [type="checkbox"]:disabled:not(:checked) + label:before,
    [type="checkbox"]:disabled:checked + label:before {
        box-shadow: none;
        border-color: #bbb;
        background-color: #ddd;
    }
    [type="checkbox"]:disabled:checked + label:after {
        color: #999;
    }
    [type="checkbox"]:disabled + label {
        color: #aaa;
    }
    /* accessibility */
    [type="checkbox"]:checked:focus + label:before,
    [type="checkbox"]:not(:checked):focus + label:before {
        border: 2px dotted blue;
    }

    /* hover style just for information */
    label:hover:before {
        border: 2px solid #4778d9!important;
    }

</style>

<h3 class="pageheading">Testing Report Configurations</h3>
<br/>
<blockquote>
    
    <?php 
    $headerURL = "";
    $footerURL = "";
    
    $count = 0;
    $Result = DB::select("select * from reportconfigs where lab_lid = '" . $_SESSION['lid'] . "'");
    foreach ($Result as $result) {
        $count += 1;
        
        if ($result->header) {
            $header = "checked='checked'";
        } else {
            $header = "";
        }
        if ($result->footer) {
            $footer = "checked='checked'";
        } else {
            $footer = "";
        }
        if ($result->pageheading) {
            $pageheading = "checked='checked'";
        } else {
            $pageheading = "";
        }
        if ($result->date) {
            $date = "checked='checked'";
        } else {
            $date = "";
        }
        if ($result->sign) {
            $sign = "checked='checked'";
        } else {
            $sign = "";
        }
        if ($result->confidential) {
            $confidential = "checked='checked'";
        } else {
            $confidential = "";
        }

        if ($result->fontitelic) {
            $fontitelic = "checked='checked'";
        } else {
            $fontitelic = "";
        }

        if ($result->agelabel) {
            $agelabel = "checked='checked'";
        } else {
            $agelabel = "";
        }
        
        if ($result->valuestate) {
            $statelabel = "checked='checked'";
        } else {
            $statelabel = "";
        }
        
        if ($result->viewsno) {
            $samplelabel = "checked='checked'";
        } else {
            $samplelabel = "";
        }
        
        if ($result->viewregdate) {
            $regdatelabel = "checked='checked'";
        } else {
            $regdatelabel = "";
        }
        
        if ($result->viewinitials) {
            $viewInitials = "checked='checked'";
        } else {
            $viewInitials = "";
        }
        
        if ($result->viewspecialnote) {
            $viewspecialnote = "checked='checked'";
        } else {
            $viewspecialnote = "";
        }
        
        if ($result->enableblooddrew) {
            $enableBloodDraw = "checked='checked'";
        } else {
            $enableBloodDraw = "";
        }
        if ($result->enablecollected) {
            $enableRepCollected = "checked='checked'";
        } else {
            $enableRepCollected = "";
        }
        
        
        
        $headerURL = $result->headerurl;
        $footerURL = $result->footerurl;
        
        if ($result->headerdefault) {
            $headerdefault = "checked='checked'";
        } else {
            $headerdefault = "";
        }
       
    }
    if ($count == 0) {
        echo "<p style='color:red;'>Please configure first!</p>";
    }
    ?>
    
    <form action="addreportconfig" method="post" enctype="multipart/form-data">
        <table>
            <tr valign="top">
                <td>
                    <p>
                        <input type="checkbox" id="test1" name="header" {{ $header or '' }}/>
                        <label for="test1"> &nbsp; Show report header</label>
                    </p>
                    <p>
                        Header Image : <input type="file" name="headerurl" class="input-file"/> {{ $headerURL or '' }}
                        <input type="hidden" name="headerurllbl" value="{{ $headerURL or '' }}">
                    </p>
                    <br/>
                    <p>
                        <input type="checkbox" id="test3" name="footer" {{ $footer or '' }}/>
                               <label for="test3"> &nbsp; Show report footer</label>
                    </p>
                    <p>
                        Footer Image : <input type="file" name="footerurl" class="input-file"/> {{ $footerURL or '' }}
                        <input type="hidden" name="footerurllbl" value="{{ $footerURL or '' }}">
                    </p> 
                    <br/>
                    <p>
                        <input type="checkbox" id="test17" name="headerdefault" {{ $headerdefault or '' }}/>
                               <label for="test17"> &nbsp; Show system generated heading in the report</label>
                    </p>
                    <br/>
                    <p>
                        <input type="checkbox" id="test11" name="pageheading" {{ $pageheading or '' }}/>
                               <label for="test11"> &nbsp; Show report heading in the report</label>
                    </p>
                    <p>
                        <input type="checkbox" id="test12" name="date" {{ $date or '' }}/>
                               <label for="test12"> &nbsp; Show date in the report</label>
                    </p>
                    <p>
                        <input type="checkbox" id="test13" name="sign" {{ $sign or '' }}/>
                               <label for="test13"> &nbsp; Show sign section in the report</label>
                    </p>
                    <p>
                        <input type="checkbox" id="test14" name="confidential" {{ $confidential or '' }}/>
                               <label for="test14"> &nbsp; Show confidential mark in the top of the report</label>
                    </p>
                    <p>
                        <input type="checkbox" id="test15" name="fontitelic" {{ $fontitelic or '' }}/>
                               <label for="test15"> &nbsp; Use italic font style for the report body</label>
                    </p>
                    <p>
                        <input type="checkbox" id="test16" name="agelabel" {{ $agelabel or '' }}/>
                               <label for="test16"> &nbsp; Use formal age format (Eg:- 5/365)</label>
                    </p>
                    
                    <p>
                        <input type="checkbox" id="test18" name="valuestate" {{ $statelabel or '' }}/>
                               <label for="test18"> &nbsp; View test value state for each test</label>
                    </p>
                    
                    <p>
                        <input type="checkbox" id="test19" name="viewsample" {{ $samplelabel or '' }}/>
                               <label for="test19"> &nbsp; View sample number in report</label>
                    </p>
                    
                    <p>
                        <input type="checkbox" id="test20" name="viewregdate" {{ $regdatelabel or '' }}/>
                               <label for="test20"> &nbsp; View registered date in report</label>
                    </p>
                    <p>
                        <input type="checkbox" id="test21" name="viewinitials" {{ $viewInitials or '' }}/>
                               <label for="test21"> &nbsp; View patient initials</label>
                    </p>
                    
                    <p>
                        <input type="checkbox" id="test22" name="viewspecialnote" {{ $viewspecialnote or '' }}/>
                               <label for="test22"> &nbsp; View Report Special Note</label>
                    </p>
                    
                    <p>
                        <input type="checkbox" id="test23" name="enableblooddrew" {{ $enableBloodDraw or '' }}/>
                               <label for="test23"> &nbsp; Enable Blood Draw Step</label>
                    </p>
                    
                    <p>
                        <input type="checkbox" id="test24" name="enablecollected" {{ $enableRepCollected or '' }}/>
                               <label for="test24"> &nbsp; Enable Report Collected Step</label>
                    </p>
                </td>
                
                <td style="padding-left: 100px;">
                    <h3>Header Image</h3>
                    @if($headerURL != null | $headerURL != '')
                    <img src="{{ asset($headerURL) }}" width="500" style="border-width: 2px; border-style: solid; border-color: #001092; margin-left: 20px;"/>
                    @else
                    <p><i>Please upload a header!</i></p>
                    @endif
                    
                    <br/>
                    <br/>
                    <br/>
                    
                    <h3>Footer Image</h3>  
                    @if($footerURL != null | $footerURL != '')
                    <img src="{{ asset($footerURL) }}" width="500" style="border-width: 2px; border-style: solid; border-color: #001092; margin-left: 20px;"/>
                    @else
                    <p><i>Please upload a footer!</i></p>
                    @endif
                    
                    
                </td>
                
            </tr>
        </table>
        <input type="submit" name="submit" value="Update Configurations" class="btn" style="margin-left: 0px;">
        &nbsp; 
        {{ $msg or '' }}
    
        </form>
    
    <p style="color: #cc0000">
        NOTE:<br/>
        The Header that you are going to upload is need to be .png image and size need to be 2480 x 380 with 300 dpi.<br/>
        The Footer that you are going to upload is need to be .png image and size need to be 2480 width and 300 dpi. you can decide the height of the image<br/>        
    </p>
    
    
    
    
</blockquote>
<?php
    }
?>
@stop
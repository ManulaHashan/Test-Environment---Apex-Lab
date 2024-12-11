<?php
if (!isset($_SESSION)) {
    session_start();
}
?>

@extends('Templates/ReportTemplate')

@section('title')
Laboratory Report
@stop

@section('head')
<script type="text/javascript">

</script>
@stop

@section('heading')
{{ $repHead or '' }}
@stop

@section('content')

<?php
if (isset($onlprep)) {
    //show rep heading
    ?>  
    <img src="{{ asset('images/LabHeadersFooters/LAB7.png') }}" style="position: absolute; top: 0; left: 0; z-index: -1; margin-top: -5px" width="100%">
    <?php
}
?>

<br/>
<div style="font-family:Arial; font-size: 11pt;">
    <?php
    $repResultxxxxx = DB::select("SELECT fname FROM user where uid = (select user_uid from labUser where luid = '" . $_SESSION['luid'] . "')");
    foreach ($repResultxxxxx as $userRes) {
        $userfname = $userRes->fname;
    }
    ?>

    <?php
    $repResult = DB::select("select a.entered_uid,a.specialnote,c.fname,c.lname,b.age,b.months,b.days,b.initials,c.gender_idgender,refference_idref,a.sampleNo,a.date as regdate,a.Lab_lid from lps a, patient b, user c where a.patient_pid = b.pid and b.user_uid = c.uid and a.lpsid = '" . $lpsid . "'");
    foreach ($repResult as $lpsItem) {
        $PName = ucwords($lpsItem->fname . " " . $lpsItem->lname);
        $age = $lpsItem->age;
        $months = $lpsItem->months;
        $days = $lpsItem->days;

        $sno = $lpsItem->sampleNo;
        $specialNote = $lpsItem->specialnote;
        $regDate = $lpsItem->regdate;
        $entered_uid = $lpsItem->entered_uid;

        if ($lpsItem->initials != "" && $lpsItem->initials != null) {
            $initials = $lpsItem->initials . ".";
        } else {
            //initian genaretion~~~~
            $initials = "";

            if ($age == "") {
                $initials = "Baby.";
            }

            if ($age != "") {
                if ($age < 7) {
                    $initials = "Baby.";
                } elseif ($age > 3 && $age < 18) {
                    $initials = "Mr.";
                } elseif ($age >= 18) {
                    $initials = "Mr.";
                    if ($lpsItem->gender_idgender != 1) {
                        $initials = "Ms.";
                    }
                }
            }
            //~~~~~~~~~~~~~~~~~~~~~~
        }

        if ($lpsItem->gender_idgender == 1) {
            $gender = "Male";
        } else {
            $gender = "Female";
        }

        if ($lpsItem->refference_idref == null) {
            $refby = "";
        } else {
            $repResultx = DB::select("SELECT name FROM refference where idref = '" . $lpsItem->refference_idref . "' and lid = '" . $_SESSION['lid'] . "'");
            foreach ($repResultx as $lpsItemx) {
                $refby = $lpsItemx->name;
            }
        }

        $Resultx = DB::select("select * from reportconfigs where lab_lid = '" . $_SESSION['lid'] . "'");
        foreach ($Resultx as $resx) {
            $agelabel = $resx->agelabel;
            $date = $resx->date;
            $sign = $resx->sign;
            $valueState = $resx->valuestate;

            $viewSno = $resx->viewsno;
            $viewRegDate = $resx->viewregdate;
            $viewinitials = $resx->viewinitials;
            $viewSpecialNote = $resx->viewspecialnote;

            if ($resx->fontitelic) {
                $fontitelic = "font-style: italic;";
            } else {
                $fontitelic = "";
            }
        }

        if ($agelabel) {
            $monthsLBL = "/12";
            $daysLBL = "/365";
        } else {
            $monthsLBL = "M";
            $daysLBL = "D";
        }
    }
    ?>

    <div>
        <table width="100%" cellspacing='5'>


            <tr>
                <td width="100px">Specimen No </td>
                <td width="300px">: &nbsp; 
                    {{ $sno }}
                    <!--                @if($viewinitials)
                                    {{ $initials }} 
                                    @endif
                    
                                    {{ $PName }}-->

                </td>
                <td width="30"></td>
                <td width="100">Gender</td>
                <td>: &nbsp; {{ $gender }}</td>

                <!--            @if($viewRegDate)
                            <td width="50px"></td>
                            <td align="left">Reg. Date  &nbsp; &nbsp; &nbsp; : </td>
                            <td align="right"> {{ $regDate }} </td>
                            @endif-->

            </tr>
            <tr>
                <td>Patient Name</td>
                <td> : &nbsp; 
                    @if($viewinitials)
                    {{ $initials }} 
                    @endif

                    {{ $PName }}
                </td>
                <td></td>
                <td>Age</td>
                <td>: &nbsp; 
                    @if($age != 0)
                    {{ $age }} Y &nbsp;
                    @endif

                    @if($months != 0)
                    {{ $months }} {{ $monthsLBL or '' }} &nbsp;
                    @endif

                    @if($days != 0)
                    {{ $days }} {{ $daysLBL or '' }} &nbsp;
                    @endif
                </td>


                <!--            @if($viewSno)
                            <td></td>
                            <td align="right">Specimen No : </td>
                            <td align="right"> {{ $sno }} </td>
                            @endif-->



            </tr>
            <tr>

                <td>Referred By</td>
                <td>: &nbsp; {{ $refby }}</td>

<!--            <td>Gender</td>
<td>: &nbsp; {{ $gender }}</td>-->

                @if($viewRegDate)
                <td ></td>
                <td >Date</td>
                <td >: &nbsp; {{$regDate}} </td>
                @endif
            </tr>
            <tr>
    <!--            <td>Referred By</td>
                <td>: &nbsp; {{ $refby }}</td>-->
            </tr>

        </table>
        <hr/>
    </div>
    <div style="font-size: 11pt; font-family: Arial;">
        <?php
        $specimenArr = array();
        $result0 = DB::select("select name from testinginput where tiid in (select testinginput_tiid from Lab_has_test where test_tid in (select test_tid from lps_has_test where lps_lpsid='" . $lpsid . "') and lab_lid = '" . $_SESSION['lid'] . "')");
        foreach ($result0 as $res0) {
            $Specimen = $res0->name;
        }

//    $Specimen = implode(", ", $specimenArr);
        ?>

        <p style="{{ $fontitelic or '' }}">Specimen :- {{ $Specimen or '' }}</p>    
        <!--<h4 class="repSubHeading" style="font-size: 11pt; {{ $fontitelic or '' }}">BIOCHEMISTRY</h4>-->  

        <br/>

        <!--<blockquote>--> 

        <?php
        //to show table heading
        $tableHead = true;
        $tableHead2 = false;


        //for enable disable value state for each test
        $tmpValState = true;

        $tData = "";
        $result0 = DB::select("select d.tgid,d.name,d.comment from lps_has_test b,Lab_has_test c, Testgroup d where c.Testgroup_tgid = d.tgid and c.test_tid = b.test_tid and b.lps_lpsid='" . $lpsid . "' and d.Lab_lid = '" . $_SESSION['lid'] . "' group by d.tgid order by c.orderno ASC, c.lhtid ASC");
        foreach ($result0 as $res0) {

            $TGComment = false;

            if ($res0->name == "Macroscopy") {
                $headingOK = true;

                //for disable value state Macroscopy
                $tmpValState = false;

                //disable table heads
                $tableHead = false;
            }

            if ($res0->name == "Combine Blood Tests") {
                //disable table heads
                $tableHead = false;
            }


            if ($res0->name == "V.D.R.L.") {
                //disable table heads
                $tableHead = false;
            }

            if ($res0->name == "Total Cholesterol") {
                $CholOK = true;
            }

//                if ($res0->name == "ESR") {
//                    $ESROK = true;
//                }

            if ($res0->name == "S. Ferritin") {
                $SFRI = true;
            }

            if ($res0->name == "FBC") {
                $FBCOK = true;
            }

            if ($res0->name == "Lipid Profile") {
                $Lipid = true;
            }

            if ($res0->name == "Lipid Profile with FBS") {
                $LipidNFBS = true;
            }

            if ($res0->name == "4PPBS") {
                $PPBS4 = true;
            }

            if ($res0->name == "PPBS") {
                $PPBS = true;
            }

            if ($res0->name == "FBS with PPBS") {
                $PPBS = true;
            }

            if ($res0->name == "Serum Creatinine") {
                $scret = true;
            }

            if ($res0->name == "Post Prandial Blood Sugar") {
                $ppbs = true;
            }

            if ($res0->name == "LDL Cholesterol") {
                $LDL = true;
            }

            if ($res0->name == "Anti Streptolysin ‘O’ Titer") {
                $ASOT = true;
            }

            if ($res0->name == "Troponin I (Cardiac Troponin I Assay)") {
                $TROPT = true;
            }

            if ($res0->name == "U Culture & ABST") {
                $UFC = true;
                $tableHead = false;
            }

            if ($res0->name == "Blood Grouping Test") {
                $tableHead = false;
            }

            if ($res0->name == "Dengue Antibody Test") {
                $tableHead = false;
            }

            if ($res0->name == "Erithrocytes Sedimentation Rate ( ESR )") {
                $tableHead = false;
            }

            if ($res0->name == "Haemoglobin(Hb)") {
                $tableHead = false;
                $Hb = true;
            }

            if ($res0->name == "GLYCOHEMOGLOBIN A1C & A1") {
                $tableHead = false;
                $gly = true;
            }

            if ($res0->name == "URINE FOR MICROALBUMIN") {
//                    $tableHead = false;
                $UMAL = true;
            }

//                if ($res0->name == "FBS") {
//                    $FBS = true;
//                }

            if ($res0->name == "HbA1C") {
                $HB1C = true;
            }

            if ($res0->name == "TSH (THYROID STIMULATING HORMONE)") {
                $tableHead = false;
                $TSH = true;
            }

            if ($res0->name == "Urine Full Report") {
                $tableHead = false;
            }

            if ($res0->name == "S.Creatinine with EGFR") {
                $tableHead = false;
                $tableHead2 = true;
            }

//                if ($res0->name == "C-Reactive Protien (CRP)") {
//                    $crp = true;
//                }

            if ($res0->name == "ORAL GLUCOSE TOLERANCE TEST(OGTT)") {
                $OGTT = true;
            }

            if ($res0->name == "HCG") {
                $HCG = true;
            }

            if ($res0->name == "S.Creatinine with EGFR") {
                $TGComment = true;
                $TGCommentText = $res0->comment;
            }
            ?>          

            &nbsp;<b><u>{{ $res0->name }}</u></b>
            <br/>
            <br/>

            @if(isset($UFC))
            <u>UNCENTRIFUGED DEPOSITS</u>
            @endif

            <table width="100%">

                <?php if ($tableHead) { ?>
                    <tr>
                        <th width="350">Test</th> 
                        <th></th>
                        <th align="right">Result</th>
                        <th></th>
                        <th align="center" width="70">Unit</th>
                        <th align="center" width="100">Flag</th>
                        <th align="center" width="200">Reference range</th>
                    </tr>
                <?php } ?>

                <?php if ($tableHead2) { ?>
                    <tr>
                        <th width="320">Test</th> 
                        <th></th>
                        <th align="right">Result</th>
                        <th></th>
                        <th align="center" width="120">Unit</th>
                        <th align="center" width="100">Flag</th>
                        <th align="center" width="200">Reference range</th>
                    </tr>
                <?php } ?>

                <?php if (isset($FBCOK)) { ?>
                <!--                        <tr>
                    <th width="250">Test</th> 
                    <th></th>
                    <th align="right">Result</th>
                    <th></th>
                    <th align="center" width="120">Unit</th>
                    <th align="center" width="100">Flag</th>
                    <th align="center" width="200">Reference range</th>
                </tr>-->
                <?php } ?>

                <?php
                $flag = true;
                $DCtestTr = "";
                $viewAna = false;
                $anaID = '';
                $result2 = DB::select("select a.tid,c.reportname,c.measurement,b.value,a.minrate,a.maxrate,c.viewnorvals,c.viewanalyzer,c.analyzers_anid as anid, d.refference_min, d.refference_max from test a,lps_has_test b,Lab_has_test c,labtestingdetails d where d.Lab_lid = c.Lab_lid and a.tid=d.test_tid and c.Testgroup_tgid = '" . $res0->tgid . "' and a.tid=b.test_tid and a.tid=c.test_tid and b.lps_lpsid='" . $lpsid . "' group by a.tid order by c.orderno");
                foreach ($result2 as $res) {
                    $name = $res->reportname;
                    $value = $res->value;
                    $mes = $res->measurement;

                    if ($res->viewanalyzer) {
                        $viewAna = true;
                        $anaID = $res->anid;
                    }

                    //enable flag according to view normal value boolean in DB
                    if ($res->viewnorvals) {
                        $valueState = true;
                    } else {
                        $valueState = false;
                    }
                    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//                        if ($name == "Neutrophils" | $name == "Lymphocytes" | $name == "Eosinophils" | $name == "Monocytes" | $name == "Basophils") {
                    if ($name == "Fasting Blood Sugar<br/>&nbsp;") {

                        if ($value < $res->refference_min) {
                            $DCtestTr .= "<tr><td width='140'>" . $name . "</td><td width='240'></td><td align='right'><b>" . $value . "</b></td><td width='30'>&nbsp;</td><td>" . $mes . "</td>";
                            if ($valueState && $tmpValState) {
                                $DCtestTr .= "<td align='center'>[LOW]</td>";
                            }
                            $DCtestTr .= "";
                        } elseif ($value > $res->refference_max) {
                            $DCtestTr .= "<tr><td width='140'>" . $name . "</td><td width='240'></td><td align='right'><b>" . $value . "</b></td><td width='30'>&nbsp;</td><td>" . $mes . "</td>";
                            if ($valueState && $tmpValState) {
                                $DCtestTr .= "<td align='center'>[HIGH]</td>";
                            }
                            $DCtestTr .= "";
                        } else {
                            $DCtestTr .= "<tr><td width='140'>" . $name . "</td><td width='240'></td><td align='right'>" . $value . "</td><td width='30'>&nbsp;</td><td >" . $mes . "</td>";
                            if ($valueState && $tmpValState) {
                                $DCtestTr .= "<td align='center'>[NORMAL]</td>";
                            }
                            $DCtestTr .= "";
                        }

                        if ($res->viewnorvals) {
                            $DCtestTr .= "<td align='center'>(" . $res->refference_min . " - " . $res->refference_max . " " . $res->measurement . ")</td>";
                        } else {
                            $DCtestTr .= "<td></td>";
                        }
                        echo $DCtestTr;
                    } else if ($name == "Colour") {
                        $flag = false;
                        $DCtestTr .= "<tr><td width='140'>" . $name . "</td><td width='240'></td><td align='right'><b>" . $value . "</b></td><td width='30'>&nbsp;</td><td>" . $mes . "</td>";
                        if ($valueState && $tmpValState) {
                            $DCtestTr .= "<td align='center'></td>";
                        }
                        $DCtestTr .= "";
                        echo $DCtestTr;
                    } else if ($name == "HUMAN CHORIONIC GONEDOTROPHINE HORMONE (HCG)") {
                        $flag = false;
                        $DCtestTr .= "<tr><td width='140'>" . $name . "</td><td width='240'></td><td align='right'><b>" . $value . "</b></td><td width='30'>&nbsp;</td><td>" . $mes . "</td>";
                        if ($valueState && $tmpValState) {
                            $DCtestTr .= "<td align='center'></td>";
                        }
                        $DCtestTr .= "";
                        echo $DCtestTr;
                    } else if ($name == "Fasting plasma glucose ") {
                        $flag = false;
                        $DCtestTr .= "<tr><td width='140'>" . $name . "</td><td width='240'></td><td align='right'><b>" . $value . "</b></td><td width='30'>&nbsp;</td><td>" . $mes . "</td>";
                        if ($valueState && $tmpValState) {
                            $DCtestTr .= "<td align='center'></td>";
                        }
                        $DCtestTr .= "";
                        echo $DCtestTr;
                    } else if ($name == "Comment") {
                        $testComment = "<br/><br/> Comments : <br/><br/>" . $value;
                    } elseif ($name == "Urine Culture") {
                        $UrineCulture = "<br/> <u>Urine Culture</u> <br/><br/>" . $value;
                    } elseif ($name == "Norfloxacine-1") {
                        $nor = "Norfloxacine";
                        $nor1 = $value;
                    } elseif ($name == "Norfloxacine-2") {
                        $nor = "Norfloxacine";
                        $nor2 = $value;
                    } elseif ($name == "Cefalexin-1") {
                        $cef = "Cefalexin";
                        $cef1 = $value;
                    } elseif ($name == "Cefalexin-2") {
                        $cef = "Cefalexin";
                        $cef2 = $value;
                    } elseif ($name == "Cotrimoxasole-1") {
                        $cot = "Cotrimoxasole";
                        $cot1 = $value;
                    } elseif ($name == "Cotrimoxasole-2") {
                        $cot = "Cotrimoxasole";
                        $cot2 = $value;
                    } elseif ($name == "Nitrofurantoin-1") {
                        $nit = "Nitrofurantoin";
                        $nit1 = $value;
                    } elseif ($name == "Nitrofurantoin-2") {
                        $nit = "Nitrofurantoin";
                        $nit2 = $value;
                    } elseif ($name == "Cefuroxime-1") {
                        $cefu = "Cefuroxime";
                        $cefu1 = $value;
                    } elseif ($name == "Cefuroxime-2") {
                        $cefu = "Cefuroxime";
                        $cefu2 = $value;
                    } elseif ($name == "Augmentin-1") {
                        $aug = "Augmentin";
                        $aug1 = $value;
                    } elseif ($name == "Augmentin-2") {
                        $aug = "Augmentin";
                        $aug2 = $value;
                    } elseif ($name == "Pus Cells") {

                        $UFRpusTr = "<br/><tr><td><b><u>Centrifuged Deposits</u></b></td></tr>";

                        $UFRpusTr .= "<tr><td width='140'>" . $name . "</td><td width='1'></td><td align='right'><br/><b>" . $value . "</b></td><td width='30'>&nbsp;</td><td><br/>" . $mes . "</td><td width='30'>";

                        $UFRpusTr .= "<td></td>";

                        $UFRpusTr .= "</td>";

                        echo $UFRpusTr;
                    } else {
                        ?>

                        <tr>

                            <?php
                            if ($value < $res->refference_min) {
                                ?>  

                                <td width='200'>{{ $name }}</td><td width="1"></td><td align="right" valign="bottom" width="200"><b>{{ $value }}</b></td><td width='30'>&nbsp;</td><td width="60" align="center" valign="bottom">{{ $mes }}</td><td align="center" width="30" valign="bottom">
                                    @if($valueState != 0 && $tmpValState && $flag)
                                    [LOW]
                                    @endif
                                </td>

                                <?php
                            } elseif ($value > $res->refference_max) {
                                ?>

                                <td width='200'>{{ $name }}</td><td width="1"></td><td align="right" valign="bottom" width="150"><b>{{ $value }}</b></td><td width='30' valign="bottom">&nbsp;</td><td width="60" align="center" valign="bottom">{{ $mes }}</td><td align="center" width="30" valign="bottom">
                                    @if($valueState != 0 && $tmpValState && $flag)
                                    [HIGH]
                                    @endif
                                </td>

                                <?php
                            } else {
                                ?>
                                <td width='200'>{{ $name }}</td><td width="1"></td><td align="right" valign="bottom" width="200">{{ $value }}</td><td width='30'>&nbsp;</td><td width="60" align="center" valign="bottom">{{ $mes }}</td><td align="center" width="30" valign="bottom">
                                    @if($valueState != 0 && $tmpValState && $flag)
                                    [NORMAL]
                                    @endif
                                </td>
                                <?php
                            }
                            ?>

                            <?php
                            if ($res->viewnorvals) {
                                if ($res->refference_min != "") {
                                    $max = $res->refference_max;
                                    if ($res->refference_max == 0) {
                                        $max = "<";
                                    }
                                    ?>

                                    <td align="center" valign="bottom">( <?php echo $res->refference_min . " - " . $max . " " . $res->measurement; ?>)</td>
                                    <?php
                                } else {
                                    ?>
                                    <td></td>    
                                    <?php
                                }
                                ?>

                                <?php
                            } else {
                                ?>
                                <td></td>
                                <?php
                            }
                            ?>   

                        </tr>


                        <?php
                    }
                }
                ?>

            </table>


            <div style="color: #9d9d9d; font-size: 11pt;">           <?php
                if (isset($testComment)) {
                    echo $testComment;
                }

                if (isset($UrineCulture)) {
                    echo $UrineCulture;
                }
                ?>

                <?php
                if (isset($cef)) {
                    ?>
                    <br/>
                    <hr/>
                    <i>Antibiotics Pattern</i>
                    <br/>
                    <table width="400">
                        <tr>
                            <th style="border-color: #000; border-width: 1px; border-style: solid;">Name</th>
                            <th style="border-color: #000; border-width: 1px; border-style: solid;">1st Line</th>
                            <th style="border-color: #000; border-width: 1px; border-style: solid;">2nd Line</th>
                        </tr>
                        <tr>
                            <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $nor; ?></td>
                            <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $nor1; ?></td>
                            <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $nor2; ?></td>
                        </tr>
                        <tr>
                            <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $cef; ?></td>
                            <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $cef1; ?></td>
                            <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $cef2; ?></td>
                        </tr>
                        <tr>
                            <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $cot; ?></td>
                            <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $cot1; ?></td>
                            <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $cot2; ?></td>
                        </tr>
                        <tr>
                            <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $nit; ?></td>
                            <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $nit1; ?></td>
                            <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $nit2; ?></td>
                        </tr>
                        <tr>
                            <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $cefu; ?></td>
                            <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $cefu1; ?></td>
                            <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $cefu2; ?></td>
                        </tr>
                        <tr>
                            <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $aug; ?></td>
                            <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $aug1; ?></td>
                            <td style="border-color: #000; border-width: 1px; border-style: solid;"><?php echo $aug2; ?></td>
                        </tr>
                    </table>
                    <?php
                }
                ?>


                <?php
                if ($viewAna) {
                    if ($anaID != '' && $anaID != 'null' && $anaID != null) {
                        $resulta = DB::select("select name from analyzers where anid = '" . $anaID . "'");
                        foreach ($resulta as $resa) {
                            $analyzerName = $resa->name;
                        }
                        ?>
                        <p>&nbsp;[ Performed by : {{ $analyzerName or '' }} ]</p>


                        <?php
                    }
                }
                ?>

                <br/>
                <br/>

                <?php
            }

            echo $tData;
            ?>




            <!-- Comment From Database -->    
            <?php
            if ($TGComment == true) {
                echo $TGCommentText;
            }
            ?>
            <!-- Comment From Database -->  


            @if(isset($OGTT))

            Reference Range :-74 - 106 mg/DL <br/>
            [10% less in pregnancy] 

            <img src="../images/comments/diagnostick_OGTTComment.png" width="80%">

            @endif

            @if(isset($CholOK))
            <br/>
            Total Cholesterol Normal Range
            <table width="300" border="1">
                <tr align="left">
                    <th>Desirable</th>
                    <th>Border Line</th>
                    <th>High</th>
                <tr>
                <tr>
                    <td> 140 - 200 </td>
                    <td> 200 - 239</td>
                    <td> > 245 </td>
                </tr>
            </table>
            @endif

            @if(isset($SFRI))
            <br/>
            <br/>
            <table width="300" >
                <tr>
                    <td>MEN</td>
                    <td>20 - 60 YRS</td>
                    <td>30 - 400</td>
                </tr>
                <tr>
                    <td>FEMALE</td>
                    <td>17 - 60 YRS</td>
                    <td>15 - 150</td>
                </tr>
                <tr>
                    <td>BOTH</td>
                    <td>60 - 90 YRS</td>
                    <td>15 - 650</td>
                </tr>
                <tr>
                    <td>CHILD</td>
                    <td>06 MONTHS - 15 YRS</td>
                    <td>7 - 140</td>
                </tr>
            </table>
            @endif

            @if(isset($HCG))
            Comment :<br/><br/>
            Human Chorionic Gonadotrophin (HCG) is a glycoprotein hormone secreted by the developing placenta <br/>
            beginning shortly after fertilization. The early appearance of HCG in urine following conception have <br/>
            been made the marker of choice in the early detection off pregnancy. 
            @endif

            @if(isset($TSH))

            Adult Reference Range      0.3 – 4.0 mIU/L
            <br/><br/>
            Pediatric Reference Range

            <table width="300">
                <tr>
                    <td>1  -  11 months</td>
                    <td>0.65 – 8.6</td>
                </tr>
                <tr>
                    <td>1  -  5  years</td>
                    <td>0.55 – 7.0</td>
                </tr>
                <tr>
                    <td>6  -  10 years</td>
                    <td>0.45 – 6.4</td>
                </tr>
                <tr>
                    <td>11 -   15 years</td>
                    <td>0.35 – 5.9</td>
                </tr>
            </table>

            <br/>

            Comments : 
            <br/>
            <br/>
            <br/>
            Method : ELISA

            @endif

            @if(isset($ESROK))
            <br/>
            Normal Values
            <table width="300" border="1">			
                <tr>
                    <td> Male </td>
                    <td> 2 - 15 mm</td>			   
                </tr>
                <tr>
                    <td> Female </td>
                    <td> 2 - 20 mm</td>			   
                </tr>
            </table>
            @endif

            @if(isset($FBCOK))
<!--                <p style="font-weight: bold; font-size: 13pt; margin-left: 0px; font-style: italic;">Manual Differential Count</p>
            <table width="650">
            <?php
//echo $DCtestTr;
            ?>
            </table>-->
            @endif

            @if(isset($FBS))
            <div>
                Comment:
                <br/>
                <br/>

                <table style="border-collapse: collapse; color: #868686;" border="1" >
                    <tr>
                        <td width="150">
                            In serum Or Plasma
                        </td>
                        <td width="60">
                            mg/dl
                        </td>
                        <td width="60">
                            mmol/l
                        </td>
                    </tr>
                    <tr>
                        <td>
                            new brn,1 day
                        </td>
                        <td>
                            40-60
                        </td>
                        <td>
                            2.2-3.3
                        </td>
                    </tr>
                    <tr>
                        <td>
                            New born>1 day
                        </td>
                        <td>
                            50-80
                        </td>
                        <td>
                            2.8-4.4
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Children
                        </td>
                        <td>
                            60-100
                        </td>
                        <td>
                            3.3-5.6
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Adult
                        </td>
                        <td>
                            74-106
                        </td>
                        <td>
                            4.1-5.9
                        </td>
                    </tr>
                    <tr>
                        <td>
                            60-90 Years
                        </td>
                        <td>
                            82-115
                        </td>
                        <td>
                            4.6-6.4
                        </td>
                    </tr>
                    <tr>
                        <td>
                            >90 Years
                        </td>
                        <td>
                            75-121
                        </td>
                        <td>
                            4.2-6.7
                        </td>
                    </tr>

                </table>
                <br/>
                10% less in pregnancy

                <br/>
                <br/>
                Hyperglycemia higher than 300mg/dl(16.5mmol/l)may induse keto-acidosis and hyperosmolar coma. 
                <br/>
                In prolonged hypoglycemia,lower than30/dl(1.7mmol/l),severe irrevercible encephalic damage may occur.
            </div>
            @endif

            @if(isset($Lipid))
            <!--                <br/>
                            <br/>
                            <div>
            
                                <p><b>Comment:-</b></p>
                                <table style="font-size: 12px;" width="85%">
                                    <tr>
                                        <td width="180">Analysis</td>
                                        <td width="20"></td>
                                        <td width="180" align="center">Prognositicaly Favoarable</td>
                                        <td width="40"></td>
                                        <td width="150" align="center">Risk Indicator</td>
                                        <td width="40"></td>
                                    </tr>
                                    <tr>
                                        <td>TOTAL CHOLESTEROL</td>
                                        <td></td>
                                        <td align="center">Less than 200</td>
                                        <td >mg/dl</td>
                                        <td align="center">More than 240 </td>
                                        <td >mg/dl</td>
                                    </tr>
                                    <tr>
                                        <td>HDL CHOLESTEROL</td>
                                        <td>Male</td>
                                        <td align="center">More than 55 </td>
                                        <td >mg/dl</td>
                                        <td align="center">Less than 35 </td>
                                        <td >mg/dl</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>Female</td>
                                        <td align="center">More than 65 </td>
                                        <td >mg/dl</td>
                                        <td align="center">Less than 45 </td>
                                        <td >mg/dl</td>
                                    </tr>
                                    <tr>
                                        <td>LDL CHOLESTEROL</td>
                                        <td></td>
                                        <td align="center">Less than 150 </td>
                                        <td >mg/dl</td>
                                        <td align="center">More than 190 </td>
                                        <td >mg/dl</td>
                                    </tr>
                                    <tr>
                                        <td>VLDL</td>
                                        <td></td>
                                        <td align="center">Less than 10 </td>
                                        <td >mg/dl</td>
                                        <td align="center">More than 41 </td>
                                        <td >mg/dl</td>
                                    </tr>
                                    <tr>
                                        <td>TRIGLYCERIDES</td>
                                        <td></td>
                                        <td align="center">Less than 160 </td>
                                        <td >mg/dl</td>
                                        <td align="center">More than 200 </td>
                                        <td >mg/dl</td>
                                    </tr>
                                    <tr>
                                        <td>RISK FACTOR</td>
                                        <td>Male</td>
                                        <td align="center">Less than 3.8</td>
                                        <td ></td>
                                        <td align="center">More than 5.9</td>
                                        <td ></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>Female</td>
                                        <td align="center">Less than 3.1</td>
                                        <td ></td>
                                        <td align="center">More than 4.6</td>
                                        <td ></td>
                                    </tr>
                                </table>
                                <br/>
            
            
                            </div>-->
            @endif

            @if(isset($crp))

            Method:- Rapid latex agglutination 
            <br/><br/><br/>
            <div>
                Comment:<br/>
                C - reactive protein (CRP), The Classic Acute Phase of human serum, is synthesized by hepatocyte.<br/>
                Normally it is present only in trace amount in serum, but it can increase as much as 1,000 fold in response toadinjury or infection.The clinical measurement of CRP in serum the therefore appears to be a voluble screening <br/>
                test fororganic disease and sensitive index of disease in inflammatory, infection and ischemic condition.<br/>
            </div>
            @endif

            @if(isset($LipidNFBS))
            <br/>
            <br/>
            <div>

                <p><b>Comment:-</b></p>
                <table style="font-size: 12px;" width="85%">
                    <tr>
                        <td width="180">Analysis</td>
                        <td width="20"></td>
                        <td width="180" align="center">Prognositicaly Favoarable</td>
                        <td width="40"></td>
                        <td width="150" align="center">Risk Indicator</td>
                        <td width="40"></td>
                    </tr>
                    <tr>
                        <td>TOTAL CHOLESTEROL</td>
                        <td></td>
                        <td align="center">Less than 200</td>
                        <td >mg/dl</td>
                        <td align="center">More than 240 </td>
                        <td >mg/dl</td>
                    </tr>
                    <tr>
                        <td>HDL CHOLESTEROL</td>
                        <td>Male</td>
                        <td align="center">More than 55 </td>
                        <td >mg/dl</td>
                        <td align="center">Less than 35 </td>
                        <td >mg/dl</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Female</td>
                        <td align="center">More than 65 </td>
                        <td >mg/dl</td>
                        <td align="center">Less than 45 </td>
                        <td >mg/dl</td>
                    </tr>
                    <tr>
                        <td>LDL CHOLESTEROL</td>
                        <td></td>
                        <td align="center">Less than 150 </td>
                        <td >mg/dl</td>
                        <td align="center">More than 190 </td>
                        <td >mg/dl</td>
                    </tr>
                    <tr>
                        <td>VLDL</td>
                        <td></td>
                        <td align="center">Less than 10 </td>
                        <td >mg/dl</td>
                        <td align="center">More than 41 </td>
                        <td >mg/dl</td>
                    </tr>
                    <tr>
                        <td>TRIGLYCERIDES</td>
                        <td></td>
                        <td align="center">Less than 160 </td>
                        <td >mg/dl</td>
                        <td align="center">More than 200 </td>
                        <td >mg/dl</td>
                    </tr>
                    <tr>
                        <td>RISK FACTOR</td>
                        <td>Male</td>
                        <td align="center">Less than 3.8</td>
                        <td ></td>
                        <td align="center">More than 5.9</td>
                        <td ></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Female</td>
                        <td align="center">Less than 3.1</td>
                        <td ></td>
                        <td align="center">More than 4.6</td>
                        <td ></td>
                    </tr>
                </table>
                <br/>


            </div>
            @endif

            @if(isset($PPBS4) | isset($PPBS))
            Comment:<br/>
            <table style="border-collapse: collapse;" border="1" width="200">
                <tr>
                    <td>< 140</td>
                    <td>Normal </td>
                </tr>
                <tr>
                    <td>140 - 200</td>
                    <td>Impaired</td>
                </tr>
                <tr>
                    <td>> 200</td>
                    <td>High</td>
                </tr>
            </table>
            @endif

            @if(isset($scret))
            <br/>
            <br/>
            Serum Creatinine Normal Values
            <br/>
            <table width="250px" cellpadding="5">
                <tr>
                    <td><b>Male</b></td>
                    <td><b>Female</b></td>
                </tr>
                <tr>
                    <td>0.7-1.2 mg%</td>
                    <td>0.5-1.0 mg%</td>
                </tr>
            </table>
            @endif

            @if(isset($ppbs))
            <br/>       
            2 Hours After Break Fast
            @endif

            @if(isset($ASOT))
            Titer Up To <br/>
            Interpretations : <br/><br/>
            <table width="300" style="margin-left: 30px;"> 
                <tr>
                    <td>Adults</td>
                    <td><200 IU /ml</td>
                </tr>
                <tr>
                    <td>Yong Adults</td>
                    <td>166-250 IU/ml</td>
                </tr>
                <tr>
                    <td>Children</td>
                    <td><100IU /ml</td>
                </tr>
            </table>
            @endif

            @if(isset($TROPT))
            <span>INTERPRITAION:</span><br/>
            Negative – No detectable cardiac Troponin I<br/>
            Positive – Level of cardiac Troponin I Equal or higher than 1ng/ml
            @endif

            @if(isset($Hb))
            <span><u>Haemoglobin Normal Range</u></span><br/>
            Male &nbsp; &nbsp; : 12.0 - 17.5<br/>
            Female : 11.0 - 15.0
            @endif

            @if(isset($UMAL))
            <u>EXPECTED VALUES</u> <br/><br/>
            <table>
                <tr>
                    <td>ALBUMIN / CREATININE</td> 
                    <td width="50"></td> 
                    <td>mg ALBUMIN/g CREATININE</td> 
                </tr>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; NORMAL</td>
                    <td></td>
                    <td><30</td>
                </tr>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; MICROALBUMINURIA</td>
                    <td></td>
                    <td>30 - 300</td>
                </tr>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; CLINICAL ALBUMINURIA</td>
                    <td></td>
                    <td>>300</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>MICROALBUMIN</td> 
                    <td></td> 
                    <td>mg/L</td> 
                </tr>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; NORMAL</td>
                    <td></td>
                    <td><2</td>
                </tr>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; MICROALBUMINURIA</td>
                    <td></td>
                    <td>2 - 20</td>
                </tr>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; MACROALBUMINURIA</td>
                    <td></td>
                    <td>>20</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>URINE CREATININE</td> 
                    <td></td> 
                    <td>10 - 300 mg/dl</td> 
                </tr>
            </table>
            @endif

            @if(isset($HB1C))
            Interpretation<br/><br/>

            <table width="60%">
                <tr>
                    <td>Below 6 %</td>
                    <td width="20"></td>
                    <td>Normal for non diabetics <br/> Excellent control for diabetics</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>6 - 7 %</td>
                    <td></td>
                    <td>Good control for diabetics</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>7 - 8 %</td>
                    <td></td>
                    <td>Unsatisfactory control for diabetics</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>10 % or above</td>
                    <td></td>
                    <td>Very poor control for diabetics</td>
                </tr>

            </table> 
            <br/>
            @endif


            @if(isset($gly))
            <p><u>xpected values</u></p>
            <table width="500">
                <tr>
                    <td>Glycohemoglobin A1C</td>
                    <td>Glycohemoglobin A1 </td>
                </tr>

                <tr>
                    <td><b> Normal Range     4.2 – 6.2 %.</b></td>
                    <td><b>Normal Range     6.0 – 8.0 %.</b></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>Diabetic</td>
                    <td>Diabetic</td>
                </tr>
                <tr>
                    <td>
                        <table>
                            <tr>
                                <td>Good control</td>
                                <td>5.5 – 6.8 %</td>
                            </tr>
                            <tr>
                                <td>Fair control</td>
                                <td>6.8 – 7.6 %</td>
                            </tr>
                            <tr>
                                <td>Poor control</td>
                                <td>Above 7.6%</td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table>
                            <tr>
                                <td>Good control</td>
                                <td>7.5 – 8.9   %</td>
                            </tr>
                            <tr>
                                <td>Fair control</td>
                                <td>9.0 – 10.0 %</td>
                            </tr>
                            <tr>
                                <td>Poor control</td>
                                <td>Above   10.0 %</td>
                            </tr>
                        </table>
                    </td>

                </tr>
            </table>
            <p>** Estimation of Mean Blood Glucose  =  33.3 (%HbA1c value) - 86</p>

            @endif

            @if(isset($LDL))
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <div>

                <p><b>CORONARY HEART DISEASE (CHD) RISK INDICATOR LEVEL</b></p>
                <table>
                    <tr>
                        <td width="180"></td>
                        <td width="150">DESIRABLE LEVELS(LOW RISK) CHD</td>
                        <td width="150">BORDERLINE LEVELS(AVERAGE RISK) CHD</td>
                        <td width="150">HIGH LEVELS(HIGH RISK) CHD</td>
                    </tr>
                    <tr>
                        <td>CHOLESTEROL - LDL</td>
                        <td>< 130 mg/dl</td>
                        <td>130 – 160 mg/dl</td>
                        <td> > 160  mg/dl</td>
                    </tr>
                </table>

                <p><b>LIPID LEVELS FOR INDIVIDUALS WITH PREMATURE CHD ( MALE < 55YRS, FEMALE < 65 YRS)</b></p>
                <table>
                    <tr>
                        <td width="180">CHOLESTEROL - LDL</td>
                        <td width="150">< 110 mg/dl</td>
                        <td width="150">110 – 130 mg/dl</td>
                        <td width="150">>= 130  mg/dl</td>
                    </tr>
                </table>

            </div>
            @endif
        </div>


        @if($viewSpecialNote)
        {{$specialNote}}
        @endif


        <center><p>** End of Report **</p></center>

        <table style="position: absolute; bottom: 100px; right: 80px; width: 100%;"> 
            <tr>

                @if($date)
                <td width='50%'>
                    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Report Date : <?php echo date('Y-m-d'); ?></p>
                    <!--<p>Issued By : {{ $userfname or '' }}</p>-->

                </td> 
                @endif

                <?php
                if (substr($sno, 0, 2) == "NI") {
                    $Resultx = DB::select("select sign_img from labUser where luid = (select luid from labUser where user_uid = '" . $entered_uid . "')");
                    foreach ($Resultx as $resx) {
                        if ($resx->sign_img != null && $resx->sign_img != '') {
                            $db = mysqli_connect("appexsl.com", "appexsl2_mlwsus", "mlws@avissawella", "appexsl2_mlws");
                            $sql = "select sign_img from labUser where luid = (select luid from labUser where user_uid = '" . $entered_uid . "')";
                            $sth = $db->query($sql);
                            $result = mysqli_fetch_array($sth);
                            echo '<img src="data:image/jpeg;base64,' . base64_encode($result['sign_img']) . '" style="position: absolute; bottom: 150px; right: 110px; width:120px;"/>';
//                                            echo $entered_uid;
                            ?>
                                                                            <!--<img src="data:image/jpeg;base64,'.base64_encode( $resx->sign_img ).'" style="position: absolute; bottom: 100px; right: 70px;"/>-->
                            <?php
                        }
                    }
                }
                ?>

                @if($sign)
                <td align='right' vlign='bottom'>
                    <br/><br/><br/>

                    <?php
                    if (substr($sno, 0, 2) == "NI") {
                        ?>
                        <p>...................................................................</p>
                        <p style="{{ $fontitelic or '' }}">H.T.S. Prasanga </br>Dip. In MLT(MRI.BL) </br>Medical Laboratory Technologist </br>SLMC Reg. No. 617</p>

                    <?php } else { ?>
                        <p>...................................................</p>
                        <p style="{{ $fontitelic or '' }}">Medical Laboratory Technologist</p>
                    <?php } ?>
                </td>
                @endif
            </tr>
        </table>



    </div>

</div>

@stop






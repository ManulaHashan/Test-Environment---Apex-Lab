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
$repResultxxxxx = DB::select("SELECT fname FROM user where uid = (select user_uid from labUser where luid = '" . $_SESSION['luid'] . "')");
foreach ($repResultxxxxx as $userRes) {
    $userfname = $userRes->fname;
}
?>

<?php
$repResult = DB::select("select c.fname,c.lname,b.age,b.months,b.days,b.initials,c.gender_idgender,refference_idref,a.sampleNo,a.date as regdate,a.Lab_lid from lps a, patient b, user c where a.patient_pid = b.pid and b.user_uid = c.uid and a.lpsid = '" . $lpsid . "'");
foreach ($repResult as $lpsItem) {
    $PName = ucwords($lpsItem->fname . " " . $lpsItem->lname);
    $age = $lpsItem->age;
    $months = $lpsItem->months;
    $days = $lpsItem->days;

    $sno = $lpsItem->sampleNo;
    $regDate = $lpsItem->regdate;

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
        $monthsLBL = "Months";
        $daysLBL = "Days";
    }
}
?>

<br/>
<div style="font-family:Arial, Helvetica, sans-serif; ">
    <table width="100%" cellspacing='8'>
        <tr>
            <td width="100px">Patient Name</td>
            <td width="300px">: &nbsp; 
                @if($viewinitials)
                {{ $initials }} 
                @endif

                {{ $PName }}

            </td>

            @if($viewRegDate)
            <td width="50px"></td>
            <td align="left">Reg. Date  &nbsp; &nbsp; &nbsp; : </td>
            <td align="right"> {{ $regDate }} </td>
            @endif

        </tr>
        <tr>
            <td>Age</td>
            <td>: &nbsp; 
                @if($age != 0)
                {{ $age }} Years &nbsp;
                @endif

                @if($months != 0)
                {{ $months }} {{ $monthsLBL or '' }} &nbsp;
                @endif

                @if($days != 0)
                {{ $days }} {{ $daysLBL or '' }} &nbsp;
                @endif
            </td>

            @if($viewSno)
            <td></td>
            <td align="left">Specimen No : </td>
            <td align="right"> {{ $sno }} </td>
            @endif

        </tr>
        <tr>
            <td>Gender</td>
            <td>: &nbsp; {{ $gender }}</td>
        </tr><tr>
            <td>Referred By</td>
            <td>: &nbsp; {{ $refby }}</td>
        </tr>

    </table>
    <hr/>

    <?php
    $specimenArr = array();
    $result0 = DB::select("select name from testinginput where tiid in (select testinginput_tiid from Lab_has_test where test_tid in (select test_tid from lps_has_test where lps_lpsid='" . $lpsid . "'))");
    foreach ($result0 as $res0) {
        array_push($specimenArr, $res0->name);
    }

    $Specimen = implode(", ", $specimenArr);
    ?>

    <br/>

    <p style="font-size: 11pt; font-weight: bold; {{ $fontitelic or '' }}">Specimen :- {{ $Specimen or '' }}</p>    
    <!--<h4 class="repSubHeading" style="font-size: 11pt; {{ $fontitelic or '' }}">BIOCHEMISTRY</h4>-->  

    <br/>
    
    <blockquote>

        <?php
        
        //to show table heading
        $tableHead = true;
                 
        
        //for enable disable value state for each test
        $tmpValState = true;

        $tData = "";
        $result0 = DB::select("select d.tgid,d.name from lps_has_test b,Lab_has_test c, Testgroup d where c.Testgroup_tgid = d.tgid and c.test_tid = b.test_tid and b.lps_lpsid='" . $lpsid . "' and d.Lab_lid = '" . $_SESSION['lid'] . "' group by d.tgid");
        foreach ($result0 as $res0) {

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

            if ($res0->name == "ESR") {
                $ESROK = true;
            }

            if ($res0->name == "Full Blood Count (FBC)") {
                $FBCOK = true;
            }

            if ($res0->name == "Lipid Profile") {
                $Lipid = true;
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
            
            if ($res0->name == "URINE FOR MICRO ALBUMIN") {
                $tableHead = false;
                $UMAL = true;
            }
            
            if ($res0->name == "TSH (THYROID STIMULATING HORMONE)") {
                $tableHead = false;
                $TSH = true;
            }
            
            if ($res0->name == "URINE FULL REPORT (UFR)") {
                $tableHead = false;
                
            }
            
            ?>          

        <b><u>{{ $res0->name }}</u></b>
            <br/>
            <br/>
            
            @if(isset($UFC))
            <u>UNCENTRIFUGED DEPOSITS</u>
            @endif

                <table width="100%" style="font-size: 11pt;">
                    
                    <?php if($tableHead){?>
                    <tr>
                        <th>TESTING</th>
                        <th></th>
                        <th align="right">RESULT</th>
                        <th></th>
                        <th align="center">UNIT</th>
                        <th></th>
                        <th align="center">NORMAL RANGE</th>
                    </tr>
                    <?php } ?>
                    
                    <?php
                    $DCtestTr = "";
                    $viewAna = false;
                    $anaID = '';
                    $result2 = DB::select("select a.tid,c.reportname,c.measurement,b.value,a.minrate,a.maxrate,c.viewnorvals,c.viewanalyzer,c.analyzers_anid as anid from test a,lps_has_test b,Lab_has_test c where c.Testgroup_tgid = '" . $res0->tgid . "' and a.tid=b.test_tid and a.tid=c.test_tid and b.lps_lpsid='" . $lpsid . "' group by a.tid");
                    foreach ($result2 as $res) {
                        $name = $res->reportname;
                        $value = $res->value;
                        $mes = $res->measurement;

                        if ($res->viewanalyzer) {
                            $viewAna = true;
                            $anaID = $res->anid;
                        }

                        if ($name == "Neutrophils" | $name == "Lymphocytes" | $name == "Eosinophils" | $name == "Monocytes" | $name == "Basophils") {

                            if ($value < $res->minrate) {
                                $DCtestTr .= "<tr><td width='140'><b>" . $name . "</b></td><td width='240'></td><td align='right'><b>" . $value . "</b></td><td width='30'>&nbsp;</td><td>" . $mes . "</td><td width='100'>";
                                if ($valueState && $tmpValState) {
                                    $DCtestTr .= "<td>[L]</td>";
                                }
                                $DCtestTr .= "</td>";
                            } elseif ($value > $res->maxrate) {
                                $DCtestTr .= "<tr><td width='140'><b>" . $name . "</b></td><td width='240'></td><td align='right'><b>" . $value . "</b></td><td width='30'>&nbsp;</td><td>" . $mes . "</td><td width='100'>";
                                if ($valueState && $tmpValState) {
                                    $DCtestTr .= "<td>[H]</td>";
                                }
                                $DCtestTr .= "</td>";
                            } else {
                                $DCtestTr .= "<tr><td width='140'>" . $name . "</td><td width='240'></td><td align='right'>" . $value . "</td><td width='30'>&nbsp;</td><td >" . $mes . "</td><td width='100'>";
                                if ($valueState && $tmpValState) {
                                    $DCtestTr .= "<td>[N]</td>";
                                }
                                $DCtestTr .= "</td>";
                            }

                            if ($res->viewnorvals) {
                                $DCtestTr .= "<td align='center'>(" . $res->minrate . " - " . $res->maxrate . " " . $res->measurement . ")</td>";
                            } else {
                                $DCtestTr .= "<td></td>";
                            }
                        }elseif ($name == "Comment") {
                                $testComment = "<br/><br/> Comments : <br/><br/>".$value;
                        }elseif($name == "Urine Culture"){
                                $UrineCulture = "<br/> <u>Urine Culture</u> <br/><br/>".$value;
                        }
                        
                        elseif($name == "Norfloxacine-1"){
                            $nor = "Norfloxacine";
                            $nor1 = $value;
                        }
                        elseif($name == "Norfloxacine-2"){
                            $nor = "Norfloxacine";
                            $nor2 = $value;
                        }
                        
                        elseif($name == "Cefalexin-1"){
                            $cef = "Cefalexin";
                            $cef1 = $value;
                        }
                        elseif($name == "Cefalexin-2"){
                            $cef = "Cefalexin";
                            $cef2 = $value;
                        }
                        
                        elseif($name == "Cotrimoxasole-1"){
                            $cot = "Cotrimoxasole";
                            $cot1 = $value;
                        }
                        elseif($name == "Cotrimoxasole-2"){
                            $cot = "Cotrimoxasole";
                            $cot2 = $value;
                        }
                        
                        elseif($name == "Nitrofurantoin-1"){
                            $nit = "Nitrofurantoin";
                            $nit1 = $value;
                        }
                        elseif($name == "Nitrofurantoin-2"){
                            $nit = "Nitrofurantoin";
                            $nit2 = $value;
                        }
                                                
                        elseif($name == "Cefuroxime-1"){
                            $cefu = "Cefuroxime";
                            $cefu1 = $value;
                        }
                        elseif($name == "Cefuroxime-2"){
                            $cefu = "Cefuroxime";
                            $cefu2 = $value;
                        }
                        
                        elseif($name == "Augmentin-1"){
                            $aug = "Augmentin";
                            $aug1 = $value;
                        }
                        elseif($name == "Augmentin-2"){
                            $aug = "Augmentin";
                            $aug2 = $value;
                        }
                        elseif($name == "Color"){
                            
                            $UFRcolorTr = "<tr><td><b><u>Macroscopy</u></b></td></tr>";
                            
                            $UFRcolorTr .= "<tr><td width='140'>" . $name . "</td><td width='1'></td><td align='right'><b>" . $value . "</b></td><td width='30'>&nbsp;</td><td>" . $mes . "</td><td width='30'>";
                                
                            $UFRcolorTr .= "<td></td>";
                                
                            $UFRcolorTr .= "</td>";
                            
                            echo $UFRcolorTr;
                        }
                        
                        elseif($name == "Pus Cells"){
                            
                            $UFRpusTr = "<br/><tr><td><b><u>Centrifuged Deposits</u></b></td></tr>";
                            
                            $UFRpusTr .= "<tr><td width='140'>" . $name . "</td><td width='1'></td><td align='right'><br/><b>" . $value . "</b></td><td width='30'>&nbsp;</td><td><br/>" . $mes . "</td><td width='30'>";
                                
                            $UFRpusTr .= "<td></td>";
                                
                            $UFRpusTr .= "</td>";
                            
                            echo $UFRpusTr;
                        }
                        
                        
                        else {
                            ?>

                            <tr>

                                <?php
                                if ($value < $res->minrate) {
                                    ?>  

                                    <td width='200'>{{ $name }}</td><td width="1"></td><td align="right" width="200"><b>{{ $value }}</b></td><td width='30'>&nbsp;</td><td width="60">{{ $mes }}</td><td width="30">
                                        @if($valueState != 0 && $tmpValState)
                                        [L]
                                        @endif
                                    </td>

                                    <?php
                                } elseif ($value > $res->maxrate) {
                                    ?>

                                    <td width='200'>{{ $name }}</td><td width="1"></td><td align="right" width="150"><b>{{ $value }}</b></td><td width='30'>&nbsp;</td><td width="60">{{ $mes }}</td><td width="30">
                                        @if($valueState != 0 && $tmpValState)
                                        [H]
                                        @endif
                                    </td>

                                    <?php
                                } else {
                                    ?>
                                    <td width='200'>{{ $name }}</td><td width="1"></td><td align="right" width="200">{{ $value }}</td><td width='30'>&nbsp;</td><td width="60">{{ $mes }}</td><td width="30">
                                        @if($valueState != 0 && $tmpValState)
                                        [N]
                                        @endif
                                    </td>
                                    <?php
                                }
                                ?>

                                <?php
                                if ($res->viewnorvals) {
                                if($res->minrate != ""){
                                    $max = $res->maxrate;
                                    if($res->maxrate == 0){
                                        $max = "<";
                                    }
                                    
                                ?>
                                    
                                    <td align="center">( <?php echo $res->minrate . " - " . $max . " " . $res->measurement; ?>)</td>
                                <?php
                                }else{
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


            <?php
                if(isset($testComment)){
                    echo $testComment;
                }
                
                if(isset($UrineCulture)){
                    echo $UrineCulture;
                }
            ?>
            
            <?php
                if(isset($cef)){
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

        <br/>

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
        <p style="font-weight: bold; font-size: 13pt; margin-left: 0px; font-style: italic;">Manual Differential Count</p>
        <table width="650">
            <?php
            echo $DCtestTr;
            ?>
        </table>
        @endif

        @if(isset($Lipid))
        <br/>
        <br/>
        <div>
         
            <b>CORONARY HEART DISEASE (CHD) RISK INDICATOR LEVEL</b>
            <table>
                <tr>
                    <td width="180"></td>
                    <td width="150">DESIRABLE LEVELS(LOW RISK) CHD</td>
                    <td width="150">BORDERLINE LEVELS(AVERAGE RISK) CHD</td>
                    <td width="150">HIGH LEVELS(HIGH RISK) CHD</td>
                </tr>
                <tr>
                    <td>CHOLESTEROL - Total</td>
                    <td>< 200 mg/dl</td>
                    <td>200 – 240 mg/dl</td>
                    <td> > 240 mg/dl</td>
                </tr>
                <tr>
                    <td>CHOLESTEROL - HDL</td>
                    <td>>= 45 mg/dl</td>
                    <td>35 – 45 mg/dl</td>
                    <td>< 35 mg/dl</td>
                </tr>
                <tr>
                    <td>CHOLESTEROL - LDL</td>
                    <td>< 130 mg/dl</td>
                    <td>130 – 160 mg/dl</td>
                    <td>> 160  mg/dl</td>
                </tr>
                <tr>
                    <td>TRIGLYCERIDES</td>
                    <td>< 100 mg/dl</td>
                    <td>< 150 mg/dl</td>
                    <td>> 150  mg/dl</td>
                </tr>
            </table>
            <br/>
            <b>LIPID LEVELS FOR INDIVIDUALS WITH PREMATURE CHD ( MALE < 55YRS, FEMALE < 65 YRS)</b>
            <table>
                <tr>
                    <td width="180">CHOLESTEROL - Total</td>
                    <td width="150">< 170 mg/dl</td>
                    <td width="150">170 – 200 mg/dl</td>
                    <td width="150">>= 200  mg/dl</td>
                </tr>
                <tr>
                    <td width="180">CHOLESTEROL - LDL</td>
                    <td width="150">< 110 mg/dl</td>
                    <td width="150">110 – 130 mg/dl</td>
                    <td width="150">>= 130  mg/dl</td>
                </tr>
            </table>

        </div>
        

<!--        <b><i>Expected values:-</i></b>

            <table cellpadding="5">
                <tr>
                    <td width="200px">Total Cholesterol</td>
                    <td width="120px">150.0 – 225.0</td>
                    <td>mg%</td>
                </tr>
                <tr>
                    <td>Triglycerides</td>
                    <td>10.0 – 190.0</td>
                    <td>mg%</td>
                </tr>
                <tr>
                    <td>HDL- Cholesterol</td>
                    <td>30.0 – 85.0</td>
                    <td>mg%</td>
                </tr>
                <tr>
                    <td>LDL Cholesterol</td>
                    <td>75.0 – 159.0</td>
                    <td>mg%</td>
                </tr>
                <tr>
                    <td>VLDL-Cholesterol</td>
                    <td>10.0 – 39.0</td>
                    <td>mg%</td>
                </tr>
                <tr>
                    <td>Total cholesterol / HDL</td>
                    <td>2.0 – 5.6</td>
                    <td>mg%</td>
                </tr>
            </table>
        </blockquote>-->

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
        <u>Reference Range</u>
        <table width="400">
            <TR>
                <td>Normal</td>
                <td>< 30 mg Alb/g Cre</td>
            </TR>
            <TR>
                <td>Microalbuminurea</td>
                <td>30 - 300 mg Alb/g Cre</td>
                <td></td>
            </TR>
            <TR>
                <td>Clinical Microalbuminurea</td>
                <td>>300 mg Alb/g Cre</td>
            </TR>
        </table>
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


    <br/>

    <table width='100%'>
        <tr>
            <td>
                <table style="position: absolute; bottom: 60px; width: 100%;">
                    <tr>
                        @if($date)
                        <td width='50%'>
                            <p>Report Date : <?php echo date('Y-m-d'); ?></p>
                            <!--<p>Issued By : {{ $userfname or '' }}</p>-->

                        </td> 
                        @endif

                        @if($sign)
                        <td align='center' vlign='bottom'>
                            <br/><br/><br/>
                            <p>...................................................................</p>
                            <p style="{{ $fontitelic or '' }}">Medical Laboratory Technologist</p>
                        </td>
                        @endif
                    </tr>
                </table>
            </td>
        </tr>
    </table>


</div>

@stop






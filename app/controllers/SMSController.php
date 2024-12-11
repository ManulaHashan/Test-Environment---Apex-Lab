<?php

//session_start();
if (!isset($_SESSION)) {
    session_start();
}
date_default_timezone_set('Asia/Colombo');

class SMSController extends Controller {

    function sendSMS() {

        if ($_POST["type"] == "ReportReady" || $_POST["type"] == "ReportEmergency") {

            $tpno = $_POST["tp"];
            $type = $_POST["type"];
            $date = $_POST["date"];

            //get sample no
            $sno = $_POST["sno"];
            $snoLast = substr($sno, -1);
            if (is_numeric($snoLast)) {
                //no need to remove last charactor
            } else {
                //need to remove last charactor
                $sno = substr($sno, 0, -1);
            }
            // 

            $delivery = "1";
            $lid = $_SESSION['lid']; 
//            $patientName = ; 
            $patientName = preg_replace('/\s+/', ' ', $_POST['name']);

            if ($lid == "9") {
                $msg = "Dear " . $patientName . ", Your lab report is ready which is tested on " . $date . ". Thank you\nFor Online Reports visit : mlwslab.com/reports/" . $lid . "/" . $sno . "/" . $date . " \nSuwarogya Medi Lab,Sooriyawewa";
            } else if ($lid == "8") {
                $msg = "Dear " . $patientName . ", Your lab report is ready. Thank you. For Online Reports visit : mlwslab.com/reports/" . $lid . "/" . $sno . "/" . $date . " \nAlpha Medi Lab,Dalugama,Kelaniya";
            } else if ($lid == "6") {
                $msg = "Dear " . $patientName . ", Your lab report is ready. Thank you. For Online Reports visit : mlwslab.com/reports/" . $lid . "/" . $sno . "/" . $date . " \nK LAB, Malwana.";
            } else if ($lid == "7") {
                $msg = "Dear " . $patientName . ", Your lab report is ready. Please collect the report. Online Reports : mlwslab.com/reports/" . $lid . "/" . $sno . "/" . $date . " \nBiotech Laboratory, Veyangoda";
            }else if ($lid == "10") {
//                $msg = "Dear " . $patientName . ", Your lab report is ready which is tested on " . date("d-m-Y") . ". Please visit to collect the report. Thank you.";
                $msg = "Dear " . $patientName . ", Your lab report is ready which is tested on " . $date . ". Please visit to collect the report. Thank you.\nFor Online Reports visit : mlwslab.com/reports/" . $lid . "/" . $sno . "/" . $date . " \nDigasiri Laboratory.";

            }else if ($lid == "12") {
                $msg = "Dear " . $patientName . ", Your lab report is ready which is tested on " . $date . ". Please visit to collect the report. Thank you.\nFor Online Reports visit : mlwslab.com/reports/" . $lid . "/" . $sno . "/" . $date . " \nLifeline Laboratory.";
            }
            
            else if ($lid == "14") {
                $msg = "Dear " . $patientName . ", Your lab report is ready which is tested on " . $date . ". Please visit to collect the report. Thank you.\nFor Online Reports visit : apexssl.com/mlws/reports/" . $lid . "/" . $sno . "/" . $date . " \nDerana Laboratory Services.";
            }else if ($lid == "15") {
                $msg = "Dear " . $patientName . ", Your lab report is ready which is tested on " . $date . ". Please visit to collect the report. Thank you.\nFor Online Reports visit : apexssl.com/mlws/reports/" . $lid . "/" . $sno . "/" . $date . " \nDerana Laboratory Services.";
            }else if ($lid == "16") {
                $msg = "Dear " . $patientName . ", Your lab report is ready which is tested on " . $date . ". Please visit to collect the report. Thank you.\nFor Online Reports visit : apexssl.com/mlws/reports/" . $lid . "/" . $sno . "/" . $date . " \nDerana Laboratory Services.";
            }else if ($lid == "17") {
                $msg = "Dear " . $patientName . ", Your lab report is ready which is tested on " . $date . ". Please visit to collect the report. Thank you.\nFor Online Reports visit : mlwss.appexsl.com/reports/" . $lid . "/" . $sno . "/" . $date . " \nIngiriya Medical Laboratory.";
            }else if ($lid == "18") {
                $msg = "Dear " . $patientName . ", Your lab report is ready which is tested on " . $date . ". Please visit to collect the report. Thank you.\nFor Online Reports visit : mlwss.appexsl.com/reports/" . $lid . "/" . $sno . "/" . $date . " \nNew Meditech Laboratory.";
            }else if ($lid == "19") {
                $msg = "Dear " . $patientName . ", Your lab report is ready which is tested on " . $date . ". Please visit to collect the report. Thank you.\nFor Online Reports visit : mlwss.appexsl.com/report/" . $lid . "/" . $sno . "/" . $date . " \nCharaka Medicare Hospital.";
            } else if ($lid == "24") {
                $msg = "Dear " . $patientName . ", Your lab report is ready which is tested on " . $date . ". Please visit to collect the report. Thank you.\nFor Online Reports visit : mlwss.appexsl.com/report/" . $lid . "/" . $sno . "/" . $date . " \nNawamini Channeling Centre, Digana.";
            } else if ($lid == "25") {
                $msg = "Dear " . $patientName . ", Your lab report is ready which is tested on " . $date . ". Please visit to collect the report. Thank you.\nFor Online Reports visit : mlwss.appexsl.com/report/" . $lid . "/" . $sno . "/" . $date . " \nMeditech Laboratory.";
            } else if ($lid == "28") {
                $msg = "Dear " . $patientName . ", Your lab report is ready which is tested on " . $date . ". Please visit to collect the report. Thank you.\nFor Online Reports visit : mlwss.appexsl.com/report/" . $lid . "/" . $sno . "/" . $date . " \nDanura Laboratory.";
            } else if ($lid == "29") {
                $msg = "Dear " . $patientName . ", Your lab report is ready which is tested on " . $date . ". Please visit to collect the report. Thank you.\nFor Online Reports visit : mlwss.appexsl.com/report/" . $lid . "/" . $sno . "/" . $date . " \nCRN Diagnostic Service";
            }  else if ($lid == "30") {
                $msg = "Dear " . $patientName . ", Your lab report is ready which is tested on " . $date . ". Please visit to collect the report. Thank you.\nFor Online Reports visit : mlwss.appexsl.com/report/" . $lid . "/" . $sno . "/" . $date . " \nVision Lab Kurunegala";
            } else if ($lid == "31") {
                $msg = "Dear " . $patientName . ", Your lab report is ready which is tested on " . $date . ". Please visit to collect the report. Thank you.\nFor Online Reports visit : mlwss.appexsl.com/report/" . $lid . "/" . $sno . "/" . $date . " \nUnique Lab - Matale";
            } else if ($lid == "32") {
                $msg = "Dear " . $patientName . ", Your lab report is ready which is tested on " . $date . ". Please visit to collect the report. Thank you.\nFor Online Reports visit : mlwss.appexsl.com/report/" . $lid . "/" . $sno . "/" . $date . " \nMeditech Lab - Rathnapura";
            } else if ($lid == "33") { 
                $msg = "Dear " . $patientName . ", Your lab report is ready which is tested on " . $date . ". Please visit to collect the report. Thank you.\nFor Online Reports visit : mlwss.appexsl.com/report/" . $lid . "/" . $sno . "/" . $date . " \nSiyasi Hospital - Kuliyapitiya";
            } else if ($lid == "34") { 
                $msg = "Dear " . $patientName . ", Your lab report is ready which is tested on " . $date . ". Please visit to collect the report. Thank you.\nFor Online Reports visit : https://synergy.appexsl.webs.lk/report/" . $lid . "/" . $sno . "/" . $date . " \nSynergy Bio.";
                
            } 

            if (isset($_GET['lpsid'])) {
                $msg .= "\nLab Results : \n";

                $result = DB::select("select a.tid,a.name,b.state,b.value,c.measurement,c.reportname from test a,lps_has_test b, Lab_has_test c where c.lab_lid = '" . $lid . "' and c.test_tid = a.tid and a.tid=b.test_tid and b.lps_lpsid='" . $_GET['lpsid'] . "'");
                foreach ($result as $res) {
                    $tid = $res->tid;
                    $tname = $res->reportname;
                    $tname = str_replace("<br/>", ", OR ", $tname);
                    $value = $_GET[$tid];
                    $uom = $res->measurement;

                    $msg .= $tname . " : " . $value . " " . $uom . "\n";
                }
            }

            if (isset($type) && $type == "ReportEmergency") { 
                if ($lid == "9") {
                    $msg = "Please show your blood report to the doctor immediately.\nThank you.\nSuwarogya Medi Lab";
                } else if ($lid == "8") {
                    $msg = "Please show your blood report to the doctor immediately.\nThank you.\nAlpha Medi Lab\nDlugama, Kelaniya.";
                } else if ($lid == "6") {
                    $msg = "Please collect the lab report and meet your doctor immediately.\nThank you.\nK LAB, Malwana.";
                } else if ($lid == "7") {
                    $msg = "Please show your blood report to the doctor immediately.\nThank you.\nBiotech Laboratory";
                }else if ($lid == "10") {
                    $msg = "Please show your blood report to the doctor immediately.\nThank you.\nDigasiri Laboratory";
                }else if ($lid == "12") {
                    $msg = "Please show your blood report to the doctor immediately.\nThank you.\nLifeline Laboratory";
                }else if ($lid == "11") {
                    $msg = "Please show your blood report to the doctor immediately.\nThank you.\nSuwasarana Laboratory";
                } 
                
                else if ($lid == "14") {
                    $msg = "Please show your blood report to the doctor immediately.\nThank you.\nDerana Laboratory Services";
                } else if ($lid == "15") {
                    $msg = "Please show your blood report to the doctor immediately.\nThank you.\nDerana Laboratory Services";
                } else if ($lid == "16") {
                    $msg = "Please show your blood report to the doctor immediately.\nThank you.\nDerana Laboratory Services";
                }  else if ($lid == "17") {
                    $msg = "Please show your blood report to the doctor immediately.\nThank you.\nIngiriya Medical Laboratory";
                }  else if ($lid == "18") {
                    $msg = "Please show your blood report to the doctor immediately.\nThank you.\nNew Meditech Laboratory";
                } else if ($lid == "19") {
                    $msg = "Please show your blood report to the doctor immediately.\nThank you.\nNew Meditech Laboratory";
                } else if ($lid == "24") {
                    $msg = "Please show your blood report to the doctor immediately.\nThank you.\nNawamini Channeling Centre, Digana";
                } else if ($lid == "25") {
                    $msg = "Please show your blood report to the doctor immediately.\nThank you.\nMeditech Laboratory";
                } else if ($lid == "28") {
                    $msg = "Please show your blood report to the doctor immediately.\nThank you.\Danura Laboratory";
                } else if ($lid == "29") {
                    $msg = "Please show your blood report to the doctor immediately.\nThank you.\CRN Diagnostic Service"; 
                } else if ($lid == "30") {
                    $msg = "Please show your blood report to the doctor immediately.\nThank you.\Vision Lab Kurunegala"; 
                } else if ($lid == "31") {
                    $msg = "Please show your blood report to the doctor immediately.\nThank you.\Unique Lab - Matale"; 
                } else if ($lid == "32") {
                    $msg = "Please show your blood report to the doctor immediately.\nThank you.\Meditech Lab - Rathnapura"; 
                } else if ($lid == "33") {
                    $msg = "Please show your blood report to the doctor immediately.\nThank you.\Siyasi Hospital - Kuliyapitiya"; 
                } else if ($lid == "33") {
                    $msg = "Please show your blood report to the doctor immediately.\nThank you.\Synergy Bio"; 
                } 
            }

            $drep = SMSManager::sendSMS($tpno, $msg, $delivery, $lid);
            SMSManager::saveLOG($tpno, $msg, $lid, $drep);
        }

        return $drep;
    }

}

?>
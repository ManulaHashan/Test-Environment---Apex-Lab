<?php

if (!isset($_SESSION)) {
    session_start();
}
date_default_timezone_set('Asia/Colombo');

class EmailController extends Controller {

    function sendEmail() {

        if ($_POST["type"] == "ReportReady") {
            
            $msg ="";

            $emailAdd = $_POST["email"];
            
            
            $sampleDate = $_POST["sdate"];


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
            $patientName = $_POST['name'];
            $patientName = preg_replace('/\s+/', ' ', $patientName); 

            //make report
            
            if ($lid == "9") {  
                $tpno = $_POST["tpno"];
                $msg = "Suwarogya Medi Lab Patient Report\nPatient Name : " . $patientName . ",\nMobile Number : " . $tpno . " \nReport Date : " . date("d-m-Y") . ". \nFor Online Reports visit : mlwslab.com/reports/".$lid."/".$sno."/".$sampleDate." \nThank you. \nSuwarogya Medi Lab,Sooriyawewa";
            } else if ($lid == "8") {
                $msg = "Dear " . $patientName . ", \nYour lab report is ready which is tested on " . date("d-m-Y") . ". \nFor Online Reports visit : mlwslab.com/reports/".$lid."/".$sno."/".$sampleDate." \nThank you. \nAlpha Medi Lab,Dalugama,Kelaniya";
            } else if ($lid == "6") {
                $msg = "Dear " . $patientName . ", \nYour lab report is ready which is tested on " . date("d-m-Y") . ". \nFor Online Reports visit : mlwslab.com/reports/".$lid."/".$sno."/".$sampleDate." \nThank you. \nDiagnosticK Laboratory, Malwana.";
            } else if ($lid == "7") {
                $msg = "Dear " . $patientName . ", \nYour lab report is ready which is tested on " . date("d-m-Y") . ". \nFor Online Reports visit : mlwslab.com/reports/".$lid."/".$sno."/".$sampleDate." \nThank you. \nBiotech Medical Laboratory, Veyangoda.";
            }else if ($lid == "12") {
                $msg = "Dear " . $patientName . ", \nYour lab report is ready which is tested on " . date("d-m-Y") . ". \nFor Online Reports visit : mlwslab.com/reports/".$lid."/".$sno."/".$sampleDate." \nThank you. \nLifeline Medical Laboratory, Kandy."; 
            }else if ($lid == "13") {
                $msg = "Dear " . $patientName . ", \nYour lab report is ready which is tested on " . date("d-m-Y") . ". \nFor Online Reports visit : mlwslab.com/reports/".$lid."/".$sno."/".$sampleDate." \nThank you. \nImperial Healthcare";
            }
            else if ($lid == "14") {
                $msg = "Dear " . $patientName . ", \nYour lab report is ready which is tested on " . date("d-m-Y") . ". \nFor Online Reports visit : mlwslab.com/reports/".$lid."/".$sno."/".$sampleDate." \nThank you. \nDerana Laboratory, Tangalle.";
            }
            else if ($lid == "17") {
                $msg = "Dear " . $patientName . ", \nYour lab report is ready which is tested on " . date("d-m-Y") . ". \nFor Online Reports visit : mlwslab.com/reports/".$lid."/".$sno."/".$sampleDate." \nThank you. \nIngiriya Medical Laboratory, Ingiriya."; 
            }
            else if ($lid == "18") {
                $msg = "Dear " . $patientName . ", \nYour lab report is ready which is tested on " . date("d-m-Y") . ". \nFor Online Reports visit : mlwslab.com/reports/".$lid."/".$sno."/".$sampleDate." \nThank you. \nNew Meditech Laboratory."; 
            }
            else if ($lid == "19") {
                $msg = "Dear " . $patientName . ", \nYour lab report is ready which is tested on " . date("d-m-Y") . ". \nFor Online Reports visit : mlwslab.com/reports/".$lid."/".$sno."/".$sampleDate." \nThank you. \nCharaka Medical Centre, Ethiliwewa."; 
            }else if ($lid == "24") {
                $msg = "Dear " . $patientName . ", \nYour lab report is ready which is tested on " . date("d-m-Y") . ". \nFor Online Reports visit : mlwss.appexsl.com/report/".$lid."/".$sno."/".$sampleDate." \nThank you. \nNawamini Channeling Centre, Digana"; 
            }else if ($lid == "25") {
                $msg = "Dear " . $patientName . ", \nYour lab report is ready which is tested on " . date("d-m-Y") . ". \nFor Online Reports visit : mlwss.appexsl.com/report/".$lid."/".$sno."/".$sampleDate." \nThank you. \nMeditech Laboratory - Balangoda."; 
            }else if ($lid == "26") {
                $msg = "Dear " . $patientName . ", \nYour lab report is ready which is tested on " . date("d-m-Y") . ". \nFor Online Reports visit : mlwss.appexsl.com/report/".$lid."/".$sno."/".$sampleDate." \nThank you. \nPrathyaksha Laboratories."; 
            }else if ($lid == "28") {
                $msg = "Dear " . $patientName . ", \nYour lab report is ready which is tested on " . date("d-m-Y") . ". \nFor Online Reports visit : mlwss.appexsl.com/report/".$lid."/".$sno."/".$sampleDate." \nThank you. \nDanura Laboratory."; 
            }else if ($lid == "29") {
                $msg = "Dear " . $patientName . ", \nYour lab report is ready which is tested on " . date("d-m-Y") . ". \nFor Online Reports visit : mlwss.appexsl.com/report/".$lid."/".$sno."/".$sampleDate." \nThank you. \nCRN Diagnostic Service"; 
            }else if ($lid == "30") {
                $msg = "Dear " . $patientName . ", \nYour lab report is ready which is tested on " . date("d-m-Y") . ". \nFor Online Reports visit : mlwss.appexsl.com/report/".$lid."/".$sno."/".$sampleDate." \nThank you. \nVision Lab Kurunegala"; 
            }else if ($lid == "31") {
                $msg = "Dear " . $patientName . ", \nYour lab report is ready which is tested on " . date("d-m-Y") . ". \nFor Online Reports visit : mlwss.appexsl.com/report/".$lid."/".$sno."/".$sampleDate." \nThank you. \nUnique Lab - Matale"; 
            }else if ($lid == "32") {
                $msg = "Dear " . $patientName . ", \nYour lab report is ready which is tested on " . date("d-m-Y") . ". \nFor Online Reports visit : mlwss.appexsl.com/report/".$lid."/".$sno."/".$sampleDate." \nThank you. \nMeditech Lab - Rathnapura"; 
            }else if ($lid == "33") {
                $msg = "Dear " . $patientName . ", \nYour lab report is ready which is tested on " . date("d-m-Y") . ". \nFor Online Reports visit : mlwss.appexsl.com/report/".$lid."/".$sno."/".$sampleDate." \nThank you. \nSiyasi Hospital - Kuliyapitiya"; 
            }else if ($lid == "34") {
                $msg = "Dear " . $patientName . ", \nYour lab report is ready which is tested on " . date("d-m-Y") . ". \nFor Online Reports visit : https://synergy.appexsl.webs.lk/report/".$lid."/".$sno."/".$sampleDate." \nThank you. \nSynergy Bio."; 
            }

            
            $return = EmailManager::sendEmail($emailAdd, $msg, $lid);
            if($return == "1"){
                $return = "Message Text\n------------------------------\n".$msg."\n------------------------------\nSent To : ".$emailAdd."\nEmail has been sent!";
            }else{
                $return = "Error in sending Email...!";
            }
        }

        return $return."";
    }

}

?>
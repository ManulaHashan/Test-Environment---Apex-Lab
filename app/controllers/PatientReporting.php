<?php

//session_start();
if (!isset($_SESSION)) {
    session_start();
}
date_default_timezone_set('Asia/Colombo');

class PatientReporting extends Controller {

    function getDetails() {
        $lps = "NOTEXISTS";
//        $Result = DB::select("SELECT lpsid FROM lps where date = '".$_POST['date']."' and sampleno = '".$_POST['ref']."' and patient_pid = (select pid from patient where user_uid = (select uid from user where tpno = '".$_POST['tp']."'))");
//        if ($_POST['dlid'] != "") {
//            $Result = DB::select("SELECT lpsid FROM lps where date = '" . $_POST['date'] . "' and sampleno = '" . $_POST['ref'] . "' and lab_lid like '".$_POST['dlid']."'");
//        } else {
        $Result = DB::select("SELECT lpsid FROM lps where date = '" . $_POST['date'] . "' and sampleno = '" . $_POST['ref'] . "' and lab_lid = '" . $_POST['dlid'] . "'");
//        }
        foreach ($Result as $res) {
            $lps = $res->lpsid;

            //check report status
            $Resultx = DB::select("SELECT status FROM lps where lpsid = '" . $lps . "'");
            foreach ($Resultx as $resx) {
                if ($resx->status == "pending") {
                    $lps = "NOTREADY";
                }
            }

            //check payment complete 
            if ($_POST['dlid'] != "8") {
                $Resultx = DB::select("SELECT (gtotal) - paid as due FROM invoice i where lps_lpsid = '" . $lps . "'");
                foreach ($Resultx as $resx) {
                    if ($resx->due > 0) {
                        $lps = "NOTPAID";
                    }
                }
            }
        }
        return $lps . "&" . " &" . $_POST['dlid'];
    }

    function checkDetails() {
        $lps = "";

        $Result = DB::select("SELECT patient_pid,lpsid FROM lps where date = '" . $_POST['date'] . "' and sampleno = '" . $_POST['ref'] . "' and lab_lid = '" . $_POST['dlid'] . "'");
        foreach ($Result as $res) {
            $pid = $res->patient_pid;
            $lpsid = $res->lpsid;
        }
        
        //Check payment complete
        if ($_POST['dlid'] != "8") {
                $Resultx = DB::select("SELECT (gtotal) - paid as due FROM invoice i where lps_lpsid = '" . $lpsid . "'");
                foreach ($Resultx as $resx) {
                    if ($resx->due > 0) {
                        $lps = "NOTPAID";
                    }
                }
            }

//            return "select d.tgid,d.name,b.lps_lpsid,e.sampleno,e.status from lps_has_test b,Lab_has_test c, Testgroup d, lps e where e.lpsid = b.lps_lpsid and c.lab_lid='".$_POST['dlid']."' and c.Testgroup_tgid = d.tgid and c.test_tid = b.test_tid and e.patient_pid = '" . $pid . "' and date = '" . $_POST['date'] . "' and d.Lab_lid = '".$_POST['dlid']."' group by d.tgid";


        if (isset($pid)) {
            
            if($lps != "NOTPAID"){
                $tgnames = "<br/><h3>Please select report you want to view...</h3><table width='80%' id='testings' class='table-basic'>";

            $reportCount = 0;
            $reportlps = 0;

            $results = DB::select("select d.tgid,d.name,b.lps_lpsid,e.sampleno,e.status from lps_has_test b,Lab_has_test c, Testgroup d, lps e where e.lpsid = b.lps_lpsid and c.lab_lid='" . $_POST['dlid'] . "' and c.Testgroup_tgid = d.tgid and c.test_tid = b.test_tid and e.patient_pid = '" . $pid . "' and date = '" . $_POST['date'] . "' and d.Lab_lid = '" . $_POST['dlid'] . "' group by d.tgid");
            foreach ($results as $res) {

                $status = "<div style='color:red;'>Report Pending</div>";
                if ($res->status == "Done") {
                    $status = "<div style='color:green;'>Report Ready</div>";
                }


                $tgnames .= "<tr>"
                        . "<td>" . $res->name . "</td><td style='text-transform: capitalize;' width='130'>" . $status . "</td><td><input type='button' value='View' id='" . $res->sampleno . "' class='btn' onclick='getreport(id)'></td>"
                        . "</tr>";

                $reportCount += 1;
                $reportlps = $res->sampleno;
            }

            return $tgnames . "</table>" . "&&#&&" . $reportCount . "&&#&&" . $reportlps;
            }else{
                return "<div style='color:red;'>Sorry! You have to complete the total payment for this invoice to enjoy online facility</div>";
            }

            
        } else {
            return "notfound";
        }
    }

}

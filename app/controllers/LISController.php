<?php

if (!isset($_SESSION)) {
    session_start();
}
date_default_timezone_set('Asia/Colombo');

class LISController extends Controller {

    function updateSample() {
        $feedBack = "";

        $lid = $_GET['lid'];
        $date = $_GET['date'];
        $time = $_GET['time'];
        $sampleNO = $_GET['sno'];
        $mlwstid = $_GET['tid'];
        $result = $_GET['res'];
        $anlyid = $_GET['anlyid'];
        $newState = "Done";

        //get lps id
        $query = DB::select("select lpsid from lps where lab_lid = '" . $lid . "' and sampleno = '" . $sampleNO . "' and date = '" . $date . "'");
        foreach ($query as $res) {
            $lpsID = $res->lpsid;
        }

        //get test id from lis test id
//        $queryx = DB::select("select test_tid from labtestingdetails where listestid = '" . $mlwstid . "' and lab_lid = '" . $lid . "'");
        $queryx = DB::select("select a.test_tid from labtestingdetails a, lps_has_test b, lps c where c.lpsid = b.lps_lpsid and a.test_tid = b.test_tid and a.listestid = '" . $mlwstid . "' and a.lab_lid = '" . $lid . "' and c.sampleno = '" . $sampleNO . "' group by a.test_tid");
        foreach ($queryx as $resx) {
            $tid = $resx->test_tid;
        }

        if (isset($tid)) {
            if (isset($lpsID)) {
                DB::statement("update lps_has_test set value = '" . $result . "', state = '" . $newState . "' where test_tid = '" . $tid . "' and lps_lpsid= '" . $lpsID . "'");

                DB::statement("insert into lisrecords(analyzers_anid,lab_lid,date,time,sno,listestid,result) values('" . $anlyid . "','" . $lid . "','" . $date . "','" . $time . "','" . $sampleNO . "','" . $tid . "','" . $result . "')");

                if ($lid != "12" && $lid != "8") {
                    DB::statement("update lps set status='Done', finishdate='" . date('Y-m-d') . "',finishtime='" . date('h:i') . "' where lpsID = '" . $lpsID . "'");
                }
                $feedBack = "Updated";
            } else {
                $feedBack = "Sample Missing";
            }
        } else {
            $feedBack = "Test ID Error";
        }

//        $feedBack = "select lpsid from lps where lab_lid = '" . $lid . "' and sampleno = '" . $sampleNO . "' and date = '" . $date . "'";

        echo $feedBack;
    }

    function updateSample8() {
        $feedBack = "";

        $lid = $_GET['lid'];
        $date = $_GET['date'];
        $time = $_GET['time'];
        $sampleNOX = $_GET['sno'];
        $mlwstid = $_GET['tid'];
        $result = $_GET['res'];
        $anlyid = $_GET['anlyid'];
        $newState = "Done";

        //remove Charactors from sample number
        $sNOFirstChars = substr($sampleNOX, 0, 2);
        $sNOLastChars = substr($sampleNOX, 2);
        $sampleNO = $sNOFirstChars . "" . preg_replace('/[^0-9]/', '', $sNOLastChars);

        //get lps id
//        $query = DB::select("select lpsid from lps where lab_lid = '" . $lid . "' and sampleno like '" . $sampleNO . "%' and date = '" . $date . "'");
//        foreach ($query as $res) {
//            $lpsID = $res->lpsid;
//        }
        //get test id from lis test id
//        $queryx = DB::select("select a.test_tid,c.lpsid from labtestingdetails a, lps_has_test b, lps c where c.lpsid = b.lps_lpsid and a.test_tid = b.test_tid and a.listestid = '".$mlwstid."' and a.lab_lid = '".$lid."' and c.sampleno like '".$sampleNO."%' group by a.test_tid");
//        $out = "select a.test_tid,c.lpsid from labtestingdetails a, lps_has_test b, lps c where c.lpsid = b.lps_lpsid and a.test_tid = b.test_tid and a.listestid = '" . $mlwstid . "' and a.lab_lid = '" . $lid . "' and c.lab_lid = '" . $lid . "' and c.sampleno like '" . $sampleNO . "%' and c.date = '" . $date . "' group by a.test_tid";
        $queryx = DB::select("select a.test_tid,c.lpsid from labtestingdetails a, lps_has_test b, lps c where c.lpsid = b.lps_lpsid and a.test_tid = b.test_tid and a.listestid = '" . $mlwstid . "' and a.lab_lid = '" . $lid . "' and c.lab_lid = '" . $lid . "' and c.sampleno like '" . $sampleNO . "%' and c.date = '" . $date . "' group by a.test_tid");
        foreach ($queryx as $resx) {
            $tid = $resx->test_tid;
            $lpsID = $resx->lpsid;
        }

        if (isset($tid)) {
            if (isset($lpsID)) {
                DB::statement("update lps_has_test set value = '" . $result . "', state = '" . $newState . "' where test_tid = '" . $tid . "' and lps_lpsid= '" . $lpsID . "'");

                DB::statement("insert into lisrecords(analyzers_anid,lab_lid,date,time,sno,listestid,result) values('" . $anlyid . "','" . $lid . "','" . $date . "','" . $time . "','" . $sampleNO . "','" . $tid . "','" . $result . "')");

                if ($lid != "12" && $lid != "8") {
                    DB::statement("update lps set status='Done', finishdate='" . date('Y-m-d') . "',finishtime='" . date('h:i') . "' where lpsID = '" . $lpsID . "'");
                }

                $feedBack = "Updated";
            } else {
                $feedBack = "Sample Missing";
            }
        } else {
            $feedBack = "Test ID Error";
        }


        echo $feedBack;
    }

    function updateSampleGraph() {
        $feedBack = "On Post";

        $lid = $_POST['lid'];
        $date = $_POST['date'];
        $time = $_POST['time']; 
        $sampleNOX = $_POST['sno'];
        $mlwstid = $_POST['tid'];
        $result = $_POST['result'];
        $anlyid = $_POST['anlyid'];
        $newState = "Done";
        
        $tid = $mlwstid;

        //remove Charactors from sample number
        $sNOFirstChars = substr($sampleNOX, 0, 2);
        $sNOLastChars = substr($sampleNOX, 2);
        $sampleNO = $sNOFirstChars . "" . preg_replace('/[^0-9]/', '', $sNOLastChars);

        // $queryx = DB::select("select a.test_tid,c.lpsid from labtestingdetails a, lps_has_test b, lps c where c.lpsid = b.lps_lpsid and a.test_tid = b.test_tid a.lab_lid = '" . $lid . "' and c.lab_lid = '" . $lid . "' and c.sampleno like '" . $sampleNO . "%' and c.date = '" . $date . "' group by a.test_tid");
        // foreach ($queryx as $resx) {
        //     $tid = $resx->test_tid;
        //     $lpsID = $resx->lpsid;
        // }
        
        // if (isset($tid)) {
        //     if (isset($lpsID)) {

        DB::statement("insert into lisimages(analyzers_anid,lab_lid,date,time,sno,listestid,result) values('" . $anlyid . "','" . $lid . "','" . $date . "','" . $time . "','" . $sampleNO . "','" . $tid . "','" . $result . "')");    



        $feedBack = "Updated";   
        //     } else {
        //         $feedBack = "Sample Missing";
        //     }
        // } else {
        //     $feedBack = "Test ID Error";
        // }


        echo $feedBack; 
    }

    function updateSampleSysmexKX21() {
        $feedBack = "";

        $lid = $_GET['lid'];
        $date = $_GET['date'];
        $time = $_GET['time'];
        $sampleNO = $_GET['sno'];
        $mlwstid = $_GET['tid'];
        $result = $_GET['res'];
        $anlyid = $_GET['anlyid'];
        $newState = "Done";

        $queryx = DB::select("select a.test_tid,c.lpsid from labtestingdetails a, lps_has_test b, lps c where c.lpsid = b.lps_lpsid and a.test_tid = b.test_tid and a.listestid = '" . $mlwstid . "' and a.lab_lid = '" . $lid . "' and c.lab_lid = '" . $lid . "' and c.sampleno like '" . $sampleNO . "%' and c.date = '" . $date . "' group by a.test_tid");
        foreach ($queryx as $resx) {
            $tid = $resx->test_tid;
            $lpsID = $resx->lpsid;
        }

        if (isset($tid)) {
            if (isset($lpsID)) {
                DB::statement("update lps_has_test set value = '" . $result . "', state = '" . $newState . "' where test_tid = '" . $tid . "' and lps_lpsid= '" . $lpsID . "'");

                DB::statement("insert into lisrecords(analyzers_anid,lab_lid,date,time,sno,listestid,result) values('" . $anlyid . "','" . $lid . "','" . $date . "','" . $time . "','" . $sampleNO . "','" . $tid . "','" . $result . "')");

                if ($lid != "12" && $lid != "8") {
                    DB::statement("update lps set status='Done', finishdate='" . date('Y-m-d') . "',finishtime='" . date('h:i') . "' where lpsID = '" . $lpsID . "'");
                } 

                $feedBack = "Updated";
            } else {
                $feedBack = "Sample Missing";
            }
        } else {
            $feedBack = "Test ID Error";
        }

//        $feedBack = "select lpsid from lps where lab_lid = '" . $lid . "' and sampleno = '" . $sampleNO . "' and date = '" . $date . "'";

        echo $feedBack;
    }

    function updateSampleTOSOHAIA360() {
        $feedBack = "";

        $lid = $_GET['lid'];
        $date = $_GET['date'];

        //modified for MLWS 02
        $drr = explode("/", $date);

        // if($_GET['lid'] == "31"){
        //     $date = "20".$drr[0]."-".$drr[1]."-".$drr[2]; 
        // }else{
            $date = "20".$drr[2]."-".$drr[1]."-".$drr[0];
        // }
        
        //

        $time = $_GET['time'];
        $sampleNO = $_GET['sno'];
        $mlwstid = $_GET['tid'];
        $result = $_GET['res'];
        $anlyid = $_GET['anlyid'];
        $newState = "Done";

        $queryx = DB::select("select a.test_tid,c.lpsid from labtestingdetails a, lps_has_test b, lps c where c.lpsid = b.lps_lpsid and a.test_tid = b.test_tid and a.listestid = '" . $mlwstid . "' and a.lab_lid = '" . $lid . "' and c.lab_lid = '" . $lid . "' and c.sampleno like '" . $sampleNO . "%' and c.date = '" . $date . "' group by a.test_tid");
        foreach ($queryx as $resx) {
            $tid = $resx->test_tid;
            $lpsID = $resx->lpsid;
        }

        if (isset($tid)) {
            if (isset($lpsID)) {
                DB::statement("update lps_has_test set value = '" . $result . "', state = '" . $newState . "' where test_tid = '" . $tid . "' and lps_lpsid= '" . $lpsID . "'");

                DB::statement("insert into lisrecords(analyzers_anid,lab_lid,date,time,sno,listestid,result) values('" . $anlyid . "','" . $lid . "','" . $date . "','" . $time . "','" . $sampleNO . "','" . $tid . "','" . $result . "')");

                if ($lid != "12" && $lid != "8") {
                    DB::statement("update lps set status='Done', finishdate='" . date('Y-m-d') . "',finishtime='" . date('h:i') . "' where lpsID = '" . $lpsID . "'");
                } 

                $feedBack = "Updated";
            } else {
                $feedBack = "Sample Missing";
            }
        } else {
            $feedBack = "Test ID Error";
        }

//        $feedBack = "select lpsid from lps where lab_lid = '" . $lid . "' and sampleno = '" . $sampleNO . "' and date = '" . $date . "'";

        echo $feedBack;
    }

    function searchSample() {

        $date = $_POST['date'];
        $sno = $_POST['sno'];

        $snox = substr($sno, strlen($sno)-1, strlen($sno));
        if (is_numeric($snox)) {

        } else {
            $sno = substr($sno, 0, strlen($sno)-1);
        }
        
        $count = 0;

        $out = "<tr><th>Time</th><th>Test Name</th><th>Result</th>";
        $queryx = DB::select("select a.*,b.name from lisrecords a, test b where a.listestid = b.tid and a.date = '" . $date . "' and sno like '" . $sno . "%' and lab_lid = '" . $_SESSION["lid"] . "' group by b.name");
        foreach ($queryx as $resx) {
            $time = $resx->time;
            $tName = $resx->name;
            $result = $resx->result;
            $tid = $resx->listestid;
//            
            $out .= "<tr><td>" . $time . "</td><td>" . $tName . "</td><td align='right'>" . $result . "</td><td><input type='button' id=tid_'" . $tid . "' name='" . $result . "' class='btn' onclick='setLISValue(id,name)' style='margin:0' value='Set'/></td></tr>";
            
            ++$count;
        }

        echo $out."###Sample NO : ".$sno."###".$count;
    }

}

?>

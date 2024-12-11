<?php

if (!isset($_SESSION)) {
    session_start();
}

class NotificationController extends Controller {

    function searchNotifications() {

        $result = "<table class='blueTable'>";

        $lid = $_SESSION['lid'];
        $luid = $_SESSION['luid'];

        //search for total patient count
        $patients = 0;
        $Result = DB::select("select count(patient_pid) as pcount from lps where Lab_lid = '" . $lid . "' and date = '" . date('Y-m-d') . "' group by patient_pid");
        foreach ($Result as $res) {
            $patients++;
        }

        $result .= "<tr><td width='70%'>Patient Count</td> <td align='center'>" . $patients . "</td></tr>";
//        
        //search for total test count
        $Result = DB::select("select count(b.pid) as pcount from lps a,patient b, user c where a.patient_pid = b.pid and b.user_uid = c.uid and a.lab_lid='" . $lid . "' and a.lpsid in (select lpsid from lps where Lab_lid = '" . $lid . "' and date = '" . date('Y-m-d') . "')");
        foreach ($Result as $res) {
            $result .= "<tr><td width='70%'>Test Count</td> <td align='center'>" . $res->pcount . "</td></tr>";
        }

        //search pending reports
        $Result = DB::select("select count(b.pid) as pcount from lps a,patient b, user c where a.patient_pid = b.pid and b.user_uid = c.uid and a.lab_lid='" . $lid . "' and a.lpsid in (select lpsid from lps where Lab_lid = '" . $lid . "' and date = '" . date('Y-m-d') . "') and a.status = 'pending'");
        foreach ($Result as $res) {
            $result .= "<tr><td width='70%'>Pending Reports</td> <td align='center'>" . $res->pcount . "</td></tr>";
        }

        //search pending reports
        $Result = DB::select("select count(b.pid) as pcount from lps a,patient b, user c, invoice d where a.lpsid = d.lps_lpsid and a.patient_pid = b.pid and b.user_uid = c.uid and a.lab_lid='" . $lid . "' and a.lpsid in (select lpsid from lps where Lab_lid = '" . $lid . "' and date = '" . date('Y-m-d') . "') and (d.status = 'Pending Due' or d.status = 'Not Paid')");
        foreach ($Result as $res) {
            $result .= "<tr><td width='70%'>Pending Payments </td> <td align='center'>" . $res->pcount . "</td></tr>";
        }

        $result .= "<tr><td><hr/></td><td><hr/></td></tr>";

        //calculate Speed per hour
        $Result = DB::select("select min(finishtime) as cycle_start, max(finishtime) as cycle_end from lps where Lab_lid = '" . $lid . "' and date = '" . date('Y-m-d') . "'");
        foreach ($Result as $res) {
            $result .= "<tr><td width='70%'>Work Start </td> <td align='center'>" . $res->cycle_start . "</td></tr>";
            $result .= "<tr><td width='70%'>Work Ends </td> <td align='center'>" . $res->cycle_end . "</td></tr>";

            $start = $res->cycle_start;
            $end = $res->cycle_end;
        }

        //search pending reports
        $Result = DB::select("select count(lpsid) as testgroups from lps where Lab_lid = '" . $lid . "' and date = '" . date('Y-m-d') . "' and finishtime is not null");
        foreach ($Result as $res) {

            if ($end - $start > 0) {
                $speed = $res->testgroups / ($end - $start);
            } else {
                $speed = 0;
            }

            $result .= "<tr><td width='70%'>Working Speed </td> <td align='center'>" . round($speed, 2) . " / hr</td></tr>";
        }

        //search for low stocks and expire materials
        $SM = false;
        $prevresults = DB::select("select name from options where idoptions in (select options_idoptions from privillages where user_uid = (select user_uid from labUser where luid = '" . $luid . "'))");
        foreach ($prevresults as $resultx) {
            if ($resultx->name == "Stock Management") {
                $SM = true;
            }
        }

        if ($SM) {
            $result .= "<tr><td><hr/></td><td><hr/></td></tr>";

            $slowCount = 0;
            $expCount = 0;
            $lstock = DB::select("select a.name,sum(c.qty-c.usedqty) as qty,b.rol, MIN(expDate) as exp from materials a, Lab_has_materials b, stock c where b.materials_mid = a.mid and b.lmid = Lab_has_materials_lmid and b.Lab_lid = '" . $lid . "' and b.status = '1' group by a.mid");
            foreach ($lstock as $resultx2) {
//                if ($resultx2->qty < $resultx2->rol) {
//                    $slowCount ++;
//                }

                $today = date("Y-m-d");
                if ($resultx2->exp < $today) {
                    $expCount++;
                }
            }

            $lstock = DB::select("select *, a.unit as unt, MIN(c.expDate) as expDate, SUM(c.qty) as qty from Lab_has_materials a,materials b, stock c where c.Lab_has_materials_lmid = a.lmid and  a.materials_mid=b.mid and a.lab_lid='" . $lid . "' and a.status='1' and (c.qty-c.usedqty) > 0 group by b.mid order by b.name ASC");
            foreach ($lstock as $resultx2) {

                $sumQty = $resultx2->qty;
                $rol = $resultx2->rol;

                if ($sumQty > $rol) {
                    continue;
                } else {
                    $slowCount ++;
                }
            }

            if ($slowCount == 0) {
                $result .= "<tr><td width='70%'>Low Stock Items</td> <td align='center'>NO</td></tr>";
            } else {
                $result .= "<tr><td width='70%' style='color : red; cursor:pointer;' onclick='loadLowStocks()'>Low Stock Items</td> <td align='center' style='color : red;'>" . $slowCount . "</td></tr>";
            }

            if ($expCount == 0) {
                $result .= "<tr><td width='70%'>Expired Items </td> <td align='center'>NO</td></tr>";
            } else {
                $result .= "<tr><td width='70%' style='color : red; cursor:pointer;' onclick='loadExps()'>Expired Items </td> <td align='center' style='color : red;'>" . $expCount . "</td></tr>";
            }
        }
        //


        $result .= "</table>";

        return $result;
    }

}

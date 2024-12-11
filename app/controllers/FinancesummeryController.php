<?php

if (!isset($_SESSION)) {
    session_start();
}
setlocale(LC_MONETARY, 'en_US');
//date_default_timezone_set('Asia/Colombo');

class FinancesummeryController extends Controller {

    function submit() {

        $lid = $_SESSION['lid'];
        $luid = $_SESSION['luid'];
        $cuSymble = $_SESSION['cuSymble'];

        if ($luid == null) {
            echo "Login Error!";
        } else {
            $RID = Input::get("RID");
            $option = Input::get("option");
            $optionx = Input::get("optiony");

            $ref = Input::get("ref");
            if ($ref == "0") {
                $ref = false;
            }

            //branch code filter
            $brcode = Input::get("brcode");
            if ($brcode == "0" || $brcode == "All" || $brcode == "" || $brcode == "undefined") {
                $brcode = "and sampleno like '%'";
            } else {
                $brcode = "and sampleno like '" . Input::get("brcode") . "%'";
            }

            if (Input::get("brcode") == "None") {
                $brcode = "and sampleno REGEXP '^[0-9]'";
            }

//day summery
            if ($RID == "1") {
                $result = $this->getDayIncomeSumery($lid, $option, $cuSymble, $ref, $brcode, $optionx);
            } else if ($RID == "2") {
                $result = $this->getDayOutcomeSumery($lid, $option, $cuSymble, $brcode);
            } else if ($RID == "3") {
                $result = $this->getDayProfitSumery($lid, $option, $cuSymble, $brcode);
            }
//month summery
            $option2 = Input::get("option2");

            if ($RID == "4") {
                $result = $this->getMonthIncomeSumery($lid, $option, $option2, $cuSymble, $ref, $brcode);
            } else if ($RID == "5") {
                $result = $this->getMonthOutcomeSumery($lid, $option, $option2, $cuSymble, $brcode);
            } else if ($RID == "6") {
                $result = $this->getMonthProfitSumery($lid, $option, $option2, $cuSymble, $brcode);
            }
//year Summery

            if ($RID == "7") {
                $result = $this->getYearIncomeSumery($lid, $option, $cuSymble, $ref, $brcode);
            } else if ($RID == "8") {
                $result = $this->getYearOutcomeSumery($lid, $option, $cuSymble, $brcode);
            } else if ($RID == "9") {
                $result = $this->getYearProfitSumery($lid, $option, $cuSymble, $brcode);
            }

//load years
            if ($RID == "10") {

                $result = "<option></option>";

                $rs = DB::select("select year(date) as yd from invoice group by year(date)");
                foreach ($rs as $years) {
                    $result .= "<option>" . $years->yd . "</option>";
//                   
                }
            } else if ($RID == "11") {
                $option2 = Input::get("dated2");
                $test = Input::get("test");
                if ($test == "All") {
                    $test = "%";
                }
                $result = $this->getcustomIncomeSumery($lid, $option, $option2, $cuSymble, $ref, $brcode, $test);
            }else if ($RID == "12") {
                $option2 = Input::get("dated2");
                $test = Input::get("test");
                if ($test == "All") {
                    $test = "%";
                }
                $result = $this->getCostingSummery($lid, $option, $option2, $cuSymble, $ref, $brcode, $test);
            }
        }
        return $result;
    }

//--------Functions------------------------------------------------------------
//DayIncomeSumery---------------------------------
    function getDayIncomeSumery($lid, $date, $cuSymble, $ref, $brcode, $date2) {

        $result = "<p class='finDetailField'>There is no any income details for this date and reference.</p>";

        if (!$ref) {
            $rs = DB::select("select count(patient_pid) as pcnt from lps where date between '" . $date . "' and '" . $date2 . "' and lab_lid = '" . $lid . "' " . $brcode . " group by patient_pid");
            $rs2 = DB::select("select count(sampleNo) as scnt from lps where date between '" . $date . "' and '" . $date2 . "' and lab_lid = '" . $lid . "' " . $brcode . "");
            $rs3 = DB::select("select count(iid) as icnt from invoice where date between '" . $date . "' and '" . $date2 . "' and lps_lpsid in (select lpsid from lps where lab_lid = '" . $lid . "' " . $brcode . ")");
            $rs4 = DB::select("select total,paid,gtotal,Discount_did,cost from invoice where date between '" . $date . "' and '" . $date2 . "' and lps_lpsid in (select lpsid from lps where lab_lid = '" . $lid . "' " . $brcode . ")");
            $rs5 = DB::select("select count(patient_pid) as lpsids from lps where date between '" . $date . "' and '" . $date2 . "' and status = 'Cancelled' and lab_lid = '" . $lid . "' " . $brcode . "");

            $rs6 = DB::select("select ROUND(SUM(a.amount),2) as amount from lps_costs a, lps b where a.lps_lpsid = b.lpsid and b.date between '" . $date . "' and '" . $date2 . "' and b.lab_lid = '" . $lid . "' " . $brcode . ""); 

        } elseif ($ref == "null") {
            $rs = DB::select("select count(patient_pid) as pcnt from lps where date between '" . $date . "' and '" . $date2 . "' and lab_lid = '" . $lid . "' " . $brcode . " and refference_idref is NULL group by patient_pid");
            $rs2 = DB::select("select count(sampleNo) as scnt from lps where date between '" . $date . "' and '" . $date2 . "' and lab_lid = '" . $lid . "' " . $brcode . " and refference_idref is NULL");
            $rs3 = DB::select("select count(iid) as icnt from invoice where date between '" . $date . "' and '" . $date2 . "' and lps_lpsid in (select lpsid from lps where lab_lid = '" . $lid . "' " . $brcode . " and refference_idref is NULL)");
            $rs4 = DB::select("select total,paid,gtotal,Discount_did,cost from invoice where date between '" . $date . "' and '" . $date2 . "' and lps_lpsid in (select lpsid from lps where lab_lid = '" . $lid . "' " . $brcode . " and refference_idref is NULL)");
            $rs5 = DB::select("select count(patient_pid) as lpsids from lps where date between '" . $date . "' and '" . $date2 . "' and status = 'Cancelled' and lab_lid = '" . $lid . "' " . $brcode . " and refference_idref is NULL");
            
            $rs6 = DB::select("select ROUND(SUM(a.amount),2) as amount from lps_costs a, lps b where a.lps_lpsid = b.lpsid and b.date between '" . $date . "' and '" . $date2 . "' and b.lab_lid = '" . $lid . "' " . $brcode . "  and refference_idref is NULL"); 

        } else {
            $rs = DB::select("select count(patient_pid) as pcnt from lps where date between '" . $date . "' and '" . $date2 . "' and lab_lid = '" . $lid . "' and refference_idref = '" . $ref . "' " . $brcode . " group by patient_pid");
            $rs2 = DB::select("select count(sampleNo) as scnt from lps where date between '" . $date . "' and '" . $date2 . "' and lab_lid = '" . $lid . "' and refference_idref = '" . $ref . "' " . $brcode . "");
            $rs3 = DB::select("select count(iid) as icnt from invoice where date between '" . $date . "' and '" . $date2 . "' and lps_lpsid in (select lpsid from lps where lab_lid = '" . $lid . "' " . $brcode . " and refference_idref = '" . $ref . "')");
            $rs4 = DB::select("select total,paid,gtotal,Discount_did,cost from invoice where date between '" . $date . "' and '" . $date2 . "' and lps_lpsid in (select lpsid from lps where lab_lid = '" . $lid . "' " . $brcode . " and refference_idref = '" . $ref . "')");
            $rs5 = DB::select("select count(patient_pid) as lpsids from lps where date between '" . $date . "' and '" . $date2 . "' and lab_lid = '" . $lid . "' and status = 'Cancelled' and refference_idref = '" . $ref . "' " . $brcode . "");

            $rs6 = DB::select("select ROUND(SUM(a.amount),2) as amount from lps_costs a, lps b where a.lps_lpsid = b.lpsid and b.date between '" . $date . "' and '" . $date2 . "' and b.lab_lid = '" . $lid . "' and refference_idref = '" . $ref . "' " . $brcode . ""); 
        }

        $patients = 0;
        foreach ($rs as $count) {
            $patients += 1;
        }

        foreach ($rs2 as $count2) {
            $samples = $count2->scnt;
        }

        foreach ($rs3 as $count3) {
            $invoices = $count3->icnt;
        }

        $tot_cost = 0;
        foreach ($rs6 as $count6) {
            $tot_cost += $count6->amount;
        }

        $canelled_invoices = "0";
        foreach ($rs5 as $count4) {
            $canelled_invoices = $count4->lpsids;
        }

        $Total_Income = 0;
        $Total_gtot = 0;
        $Total_dis = 0;
        $Total_tot = 0;
        $Total_cost = 0;
        foreach ($rs4 as $count4) {
            $Total_Income += $count4->paid;
            $Total_gtot += $count4->gtotal;
            $Total_tot += $count4->total;
            $Total_cost += $count4->cost;

            if ($count4->Discount_did != null) {
                $ResultD = DB::select("select value from Discount where did = '" . $count4->Discount_did . "'and Lab_lid = '" . $lid . "' ");
                foreach ($ResultD as $Ditem) {
                    $tot = $count4->total;
                    $disVal = $Ditem->value;

                    $disAmount = $tot * ($disVal / 100);

                    $Total_dis += $disAmount;
                }
            }
        }
        //day pay
        $today= date("Y-m-d"); 
        
        $paid_today = 0;
        $paid_earlier = 0;
        
        $paid_tot = 0;
        
        //total paid
        $rs = DB::select("select i.amount as paid, a.date as invdate from invoice a, lps b,invoice_payments i where a.iid=i.invoice_iid and a.lps_lpsid=b.lpsid and i.date between '" . $date . "' and '" . $date2 . "' and b.lpsid in (select lpsid from lps where lab_lid = '" . $lid . "' " . $brcode . ")");
        foreach ($rs as $count) {

            $paid_tot += $count->paid;
            
        } 
        
        //indetail
        $rs = DB::select("select i.amount as paid, a.date as invdate from invoice a, lps b,invoice_payments i where a.iid=i.invoice_iid and a.lps_lpsid=b.lpsid and i.date between '" . $date . "' and '" . $date2 . "' and b.lpsid in (select lpsid from lps where lab_lid = '" . $lid . "' " . $brcode . ")");
        foreach ($rs as $count) {
            if($count->invdate == $today){
                $paid_today += $count->paid;
            }else{
                $paid_earlier += $count->paid;
            }
        } 

        //DUE
        $tot_due = $Total_gtot - $paid_tot;
        
        if($tot_due < 0){
            $tot_due = 0;
        }
        
        //get daily Expenses
        $daily_expenses = 0;
//        $ResultEx = DB::select("select sum(price) as totexp from payments where date = '".$date."' and Lab_lid = '" . $lid . "' " . $brcode . "");
        $ResultEx = DB::select("select sum(price) as totexp from payments where date between '" . $date . "' and '" . $date2 . "' and Lab_lid = '" . $lid . "'");

        foreach ($ResultEx as $exp) {

            $daily_expenses = $exp->totexp;
        }
        
        //cashier total
        $cashier = ($paid_today+$paid_earlier) - $daily_expenses;

//        if ($patients != 0 && $samples != 0 && $invoices != 0) {
        if ($patients != 0) {
//            $Total_Income = number_format($Total_Income, 2);
            $result = "<p class='finDetailHead'>Income Summery</p>" . "<table class='finDetailField' width='500px'>" . "<tr>" . "<td>Total Patient Count  </td><td>:</td><td align='right'>" . $patients . "</td>" . "</tr>" . "<tr>" . "<td>Total Sample Count  </td><td>:</td><td align='right'>" . $samples . "</td>" . "</tr>" . "<tr>" . "<td>Total Invoice Count  </td><td>:</td><td align='right'>" . $invoices . "</td>" . "</tr><tr><td><hr></td><td><hr></td><td><hr></td></tr><tr>" . "<td>Total Sales Amount </td><td>: " . $cuSymble . " </td><td align='right'>" . number_format($Total_tot, 2) . "</td>" . "</tr><tr>" . "<td>Total Discount Amount </td><td>: " . $cuSymble . "</td><td align='right'> " . number_format($Total_dis, 2) . "</td>" . "</tr><tr>" . "<td>Total Income </td><td>: " . $cuSymble . " </td><td align='right'>" . number_format($Total_gtot, 2) . "</td>" . "</tr>  <tr><td><hr></td><td><hr></td><td><hr></td></tr>  <tr>" . "<td>Total Paid </td><td>: " . $cuSymble . "</td><td align='right'> " . number_format($paid_tot, 2) . "</td>" . "</tr>  <tr>" . "<td style='font-size: 12pt;'>Total Paid (Today) </td><td style='font-size: 12pt;'>: " . $cuSymble . "</td><td align='right' style='font-size: 12pt;'> " . number_format($paid_today, 2) . "</td>" . "</tr><tr>" . "<td style='font-size: 12pt;'>Total Paid (Past) </td><td style='font-size: 12pt;'>: " . $cuSymble . "</td><td align='right' style='font-size: 12pt;'> " . number_format($paid_earlier, 2) . "</td>" . "</tr><tr>" . "<td>Total Due </td><td>: " . $cuSymble . "</td><td align='right'> " . number_format($tot_due, 2) . "</td>" . "</tr> <tr><td><hr></td><td><hr></td><td><hr></td></tr> <tr>" . "<td>Total Testing Cost </td><td>: " . $cuSymble . "</td><td align='right'> " . number_format($tot_cost, 2) . "</td></tr>" . "<tr><td>Profit from Testing </td><td>: " . $cuSymble . "</td><td align='right'> " . number_format($Total_gtot - $tot_cost, 2) . "</td></tr>" . "<tr><td>Daily Expenses </td><td>: " . $cuSymble . "</td><td align='right'> " . number_format($daily_expenses, 2) . "</td></tr><tr><td>Cancelled Invoices</td><td>: </td><td align='right'> " . $canelled_invoices . "</td></tr><tr><td>Cashier Balance</td><td>: </td><td align='right'> " . number_format($cashier, 2) . "</td></tr>" . "</table>";
        }
        return $result;
//        return $brcode;
//        return "select count(patient_pid) as pcnt from lps where date = '" . $date . "' and lab_lid = '" . $lid . "' and refference_idref = '" . $ref . "'";
    }

    function getcustomIncomeSumery($lid, $date, $date2, $cuSymble, $ref, $brcode, $test) {

        $result = "<p class='finDetailField'>Records not found for selected test group.</p>";

        if (!$ref) {
            $rs4 = DB::select("select date,count(sampleno) as tcount from lps where date between '" . $date . "' and '" . $date2 . "' and lab_lid = '" . $lid . "' and lpsid in (select lps_lpsid from lps_has_test where test_tid in (select test_tid from Lab_has_test where lab_lid = '" . $lid . "' and Testgroup_tgid like '" . $test . "')) " . $brcode . " group by date");
            $rs5 = DB::select("SELECT price FROM Testgroup where tgid like '" . $test . "' and Lab_lid = '" . $lid . "'");
        } elseif ($ref == "null") {
            $rs4 = DB::select("select date,count(sampleno) as tcount from lps where date between '" . $date . "' and '" . $date2 . "' and lab_lid = '" . $lid . "' and lpsid in (select lps_lpsid from lps_has_test where test_tid in (select test_tid from Lab_has_test where lab_lid = '" . $lid . "' and Testgroup_tgid like '" . $test . "')) " . $brcode . " and refference_idref is NULL group by date");
            $rs5 = DB::select("SELECT price FROM Testgroup where tgid like '" . $test . "' and Lab_lid = '" . $lid . "'");
        } else {
            $rs4 = DB::select("select date,count(sampleno) as tcount from lps where date between '" . $date . "' and '" . $date2 . "' and lab_lid = '" . $lid . "' and lpsid in (select lps_lpsid from lps_has_test where test_tid in (select test_tid from Lab_has_test where lab_lid = '" . $lid . "' and Testgroup_tgid like '" . $test . "')) " . $brcode . " and refference_idref = '" . $ref . "' group by date");
            $rs5 = DB::select("SELECT price FROM Testgroup where tgid like '" . $test . "' and Lab_lid = '" . $lid . "'");
        }


        $patients = 0;
        $dates = 0;
        $counts = 0;
        foreach ($rs4 as $count) {
            $patients += $count->tcount;
            $dates .= "#" . $count->date;
            $counts .= "#" . $count->tcount;
        }

        $Total_Income = 0;
        $Total_gtot = 0;
        $Total_dis = 0;
        $Total_tot = 0;

        //get test price
        if ($test != "%") {
            foreach ($rs5 as $count5) {
                $Total_tot = number_format($count5->price * $patients, 2);
            }
        } else {
            $Total_tot = "~";
        }

        if ($patients != 0) {
            $result = "<p class='finDetailHead'>Income Summery</p>" . "<table class='finDetailField' width='500px'>" . "<tr>" . "<td>Total Test Count  </td><td>:</td><td align='right'>" . $patients . "</td>" . "</tr><tr><td><hr></td><td><hr></td><td><hr></td></tr><tr>" . "<td>Total Sales Amount </td><td>: " . $cuSymble . " </td><td align='right'>" . $Total_tot . "</td>" . "</tr></table>";
            $result .= "#/#<canvas id='finChart' width='380' height='110'></canvas><div style='display:none' id='finChartDetails'>" . $dates . "#-#" . $counts . "</div>";
        }
        return $result;
    }

    function getCostingSummery($lid, $date, $date2, $cuSymble, $ref, $brcode, $test) {

        //Get expenses from Testgroup Cost~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        $result = "<p class='finDetailField'>Records not found for selected test group or date period.</p>";

        if (!$ref) {
            $rs6 = DB::select("select a.name, ROUND(SUM(a.amount),2) as amount from lps_costs a, lps b, lps_has_test c where b.lpsid = c.lps_lpsid and a.lps_lpsid = b.lpsid and b.date between '" . $date . "' and '" . $date2 . "' and b.Testgroup_tgid like '".$test."' and b.lab_lid = '" . $lid . "' " . $brcode . " group by a.name"); 
 
            // $rs7 = DB::select("select b.unit,(g.cost/g.qty)*SUM(a.qty) as percost , f.name as tgname, a.test_tid, c.name, SUM(a.qty) as qty from test_Labmeterials a, Lab_has_materials b, materials c, lps d, lps_has_test e, Testgroup f, stock g where b.lmid = g.Lab_has_materials_lmid and a.Lab_has_materials_lmid = b.lmid and b.materials_mid = c.mid and a.test_tid = e.test_tid and e.lps_lpsid = d.lpsid and d.lab_lid = b.lab_lid and d.Testgroup_tgid = f.tgid and  b.Lab_lid = '" . $lid . "' and d.date between '" . $date . "' and '" . $date2 . "' and d.Testgroup_tgid like'".$test."' group by c.mid order by f.name"); 

            $rs7 = DB::select("select h.symble as unit,(g.cost/g.qty)*SUM(a.qty) as percost , f.name as tgname, c.name, SUM(a.qty) as qty from lps_has_stock a, Lab_has_materials b, materials c, lps d, Testgroup f, stock g, measurements h where h.msid = b.unit and b.lmid = g.Lab_has_materials_lmid and a.stock_idstock = g.idstock and b.materials_mid = c.mid and a.lps_lpsid = d.lpsid and d.lab_lid = b.lab_lid and d.Testgroup_tgid = f.tgid and  b.Lab_lid = '" . $lid . "' and d.date between '" . $date . "' and '" . $date2 . "' and d.Testgroup_tgid like'".$test."' group by c.mid order by f.name");

        } elseif ($ref == "null") {
            $rs6 = DB::select("select a.name, ROUND(SUM(a.amount),2) as amount from lps_costs a, lps b, lps_has_test c where b.lpsid = c.lps_lpsid and a.lps_lpsid = b.lpsid and b.date between '" . $date . "' and '" . $date2 . "' and b.Testgroup_tgid like '".$test."' and b.lab_lid = '" . $lid . "' " . $brcode . "  and refference_idref is NULL group by a.name"); 

            // $rs7 = DB::select("select b.unit,(g.cost/g.qty)*SUM(a.qty) as percost , f.name as tgname, a.test_tid, c.name, SUM(a.qty) as qty from test_Labmeterials a, Lab_has_materials b, materials c, lps d, lps_has_test e, Testgroup f, stock g where b.lmid = g.Lab_has_materials_lmid and a.Lab_has_materials_lmid = b.lmid and b.materials_mid = c.mid and a.test_tid = e.test_tid and e.lps_lpsid = d.lpsid and d.lab_lid = b.lab_lid and d.Testgroup_tgid = f.tgid and  b.Lab_lid = '" . $lid . "' and d.date between '" . $date . "' and '" . $date2 . "' and d.Testgroup_tgid like '".$test."' and d.refference_idref is NULL group by c.mid order by f.name"); 

            $rs7 = DB::select("select h.symble as unit,(g.cost/g.qty)*SUM(a.qty) as percost , f.name as tgname, c.name, SUM(a.qty) as qty from lps_has_stock a, Lab_has_materials b, materials c, lps d, Testgroup f, stock g, measurements h where h.msid = b.unit and b.lmid = g.Lab_has_materials_lmid and a.stock_idstock = g.idstock and b.materials_mid = c.mid and a.lps_lpsid = d.lpsid and d.lab_lid = b.lab_lid and d.Testgroup_tgid = f.tgid and  b.Lab_lid = '" . $lid . "' and d.date between '" . $date . "' and '" . $date2 . "' and d.Testgroup_tgid like'".$test."'  and refference_idref is NULL group by c.mid order by f.name");
        } else {
            $rs6 = DB::select("select a.name, ROUND(SUM(a.amount),2) as amount from lps_costs a, lps b, lps_has_test c where b.lpsid = c.lps_lpsid and a.lps_lpsid = b.lpsid and b.date between '" . $date . "' and '" . $date2 . "' and b.Testgroup_tgid like '".$test."' and b.lab_lid = '" . $lid . "' and refference_idref = '" . $ref . "' " . $brcode . " group by a.name"); 

            // $rs7 = DB::select("select b.unit,(g.cost/g.qty)*SUM(a.qty) as percost , f.name as tgname, a.test_tid, c.name, SUM(a.qty) as qty from test_Labmeterials a, Lab_has_materials b, materials c, lps d, lps_has_test e, Testgroup f, stock g where b.lmid = g.Lab_has_materials_lmid and a.Lab_has_materials_lmid = b.lmid and b.materials_mid = c.mid and a.test_tid = e.test_tid and e.lps_lpsid = d.lpsid and d.lab_lid = b.lab_lid and d.Testgroup_tgid = f.tgid and  b.Lab_lid = '" . $lid . "' and d.date between '" . $date . "' and '" . $date2 . "' and d.Testgroup_tgid like '".$test."' and d.refference_idref = '" . $ref . "' group by c.mid order by f.name");

            $rs7 = DB::select("select h.symble as unit,(g.cost/g.qty)*SUM(a.qty) as percost , f.name as tgname, c.name, SUM(a.qty) as qty from lps_has_stock a, Lab_has_materials b, materials c, lps d, Testgroup f, stock g, measurements h where h.msid = b.unit and b.lmid = g.Lab_has_materials_lmid and a.stock_idstock = g.idstock and b.materials_mid = c.mid and a.lps_lpsid = d.lpsid and d.lab_lid = b.lab_lid and d.Testgroup_tgid = f.tgid and  b.Lab_lid = '" . $lid . "' and d.date between '" . $date . "' and '" . $date2 . "' and d.Testgroup_tgid like'".$test."' and refference_idref = '" . $ref . "' group by c.mid order by f.name"); 
        }
 
        $tot_cost = 0;
        $cost_list = ""; 
        foreach ($rs6 as $count) { 
            $tot_cost += $count->amount;
            
            $cost_list .= "<tr><td>".$count->name."</td><td>: " . $cuSymble . "</td><td align='right'>".number_format($count->amount,2)."</td></tr>";
        }       
                
        $result = "<p class='finDetailHead'>Costing Summery</p><h3><u>Indirect Costs</u></h3>" . "<table class='finDetailField' width='500px'>".$cost_list."<tr><td><hr></td><td><hr></td><td><hr></td></tr><tr>" . "<td>Total Cost Amount </td><td>: " . $cuSymble . " </td><td align='right'>" . number_format($tot_cost,2) . "</td>" . "</tr></table>";

        //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ 


        //get testing materials and cost ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        $tot_material_cost = 0;

        $out2 = "</hr><br/><h3><u>Material Costs</u></h3><table width='100%' class='table-basic'> <tr><th>TEST</th><th>MATERIAL</th><th>QTY</th><th>UNIT</th><th>COST RS.</th></tr> ";

        foreach ($rs7 as $countx) {

            $tot_material_cost += $countx->percost;
            
            $out2 .= "<tr><td>".$countx->tgname."</td><td>".$countx->name."</td><td align='right'>".$countx->qty."</td><td align='center'>".$countx->unit."</td><td align='right'>".number_format($countx->percost,2)."</td></tr>";
        }  
        
        $out2 .= "<tr><td></td><td></td><td></td><td></td><td><hr/></td></tr>";
        $out2 .= "<tr><td><b>Total Material Cost</b></td><td></td><td></td><td><b>Rs.</b></td><td align='right'><b>".number_format($tot_material_cost,2)."</b></td></tr>";
        $out2 .= "<tr><td></td><td></td><td></td><td></td><td><hr/><hr/></td></tr>";
        $out2 .= "</table>";
 
        //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        
        return $result.$out2;
    }

//----------------------------------------------------------------------------
//DayExpences--------------------------------------
    function getDayOutcomeSumery($lid, $date, $cuSymble, $brcode) {
        $result = "<p class='finDetailField'>There is no any Outcome details for this date.</p>";

        if ($brcode == "") {
            $brcode = "%";
        }

        $rs = DB::select("select count(payid) as paid from payments where date = '" . $date . "' and lab_lid ='" . $lid . "' and branchcode like = '" . $brcode . "'");
        foreach ($rs as $count) {
            $confirm1 = 1;
        }
        if (isset($confirm1)) {
            $Payment = $count->paid;
        }
        $rs3 = DB::select("select count(grnid) as gcnt from grn where date = '" . $date . "' and grnid in (select grn_grnid from Labmaterials_has_grn where labmaterials_lmid in (select lmid from Lab_has_materials where lab_lid='" . $lid . "') )");
        foreach ($rs3 as $count1) {
            $confirm2 = 1;
        }
        if (isset($confirm2)) {
            $GRNs = $count1->gcnt;
        }
        $rs4 = DB::select("select round(SUM(paid),2) as paid from grn where date = '" . $date . "' and grnid in (select grn_grnid from Labmaterials_has_grn where labmaterials_lmid in (select lmid from Lab_has_materials where lab_lid='" . $lid . "') )");
        foreach ($rs4 as $count2) {
            $confirm3 = 1;
        }
        if (isset($confirm3)) {
            $Total_Outcome = $count2->paid;
        }



        if ($Total_Outcome != "0" && $GRNs != 0) {
            $Total_Outcome = number_format($Total_Outcome, 2);
            $result = "<p class='finDetailHead'>Outcome Summery</p>" . "<table class='finDetailField'>" . "<tr>" . "<td>Total GRN Count : </td><td>" . $GRNs . "</td>" . "</tr>" . "<tr>" . "<td>Total Payment Count : </td><td>" . $Payment . "</td>" . "</tr>" . "<tr>" . "<td>Total Outcome : " . $cuSymble . " </td><td>" . $Total_Outcome . "</td>" . "</tr>" . "</table>";
        }


        return $result;
    }

//--------------------------------------------------------------------------------
//DayProfitSumery-----------------------------------
    function getDayProfitSumery($lid, $date, $cuSymble, $brcode) {

        $result = "<p class='finDetailField'>There is no any Income and outcome details for this date.</p>";


        $rs = DB::select("select count(patient_pid) pcnt from lps where date = '" . $date . "' and lab_lid = '" . $lid . "' " . $brcode . "");
        foreach ($rs as $count) {
            $patients = $count->pcnt;
        }


        $rs2 = DB::select("select count(sampleNo) scnt from lps where date = '" . $date . "' and lab_lid = '" . $lid . "' " . $brcode . "");
        foreach ($rs2 as $count2) {
            $samples = $count2->scnt;
        }


        $rs3 = DB::select("select count(iid) icnt from invoice where date = '" . $date . "' and lps_lpsid in (select lpsid from lps where lab_lid = '" . $lid . "' " . $brcode . ")");
        foreach ($rs3 as $count3) {
            $invoices = $count3->icnt;
        }


        $rs4 = DB::select("select round(SUM(paid),2) as paid from invoice where date = '" . $date . "' and lps_lpsid in (select lpsid from lps where lab_lid = '" . $lid . "' " . $brcode . ")");
        foreach ($rs4 as $count4) {
            $Total_Income = $count4->paid;
        }



        $rs5 = DB::select("select count(payid) paycnt from payments where date = '" . $date . "' and lab_lid ='" . $lid . "' and branchcode like = '" . $brcode . "'");
        foreach ($rs5 as $count5) {
            $Payment = $count5->paycnt;
        }


        $rs6 = DB::select("select count(grnid) gcnt from grn where date = '" . $date . "' and grnid in (select grn_grnid from Labmaterials_has_grn where labmaterials_lmid in (select lmid from Lab_has_materials where lab_lid = '" . $lid . "'))");
        foreach ($rs6 as $count6) {
            $GRNs = $count6->gcnt;
        }


        $rs7 = DB::select("select round(SUM(paid),2) as paid from grn where date = '" . $date . "' and grnid in (select grn_grnid from Labmaterials_has_grn where labmaterials_lmid in (select lmid from Lab_has_materials where lab_lid = '" . $lid . "'))");
        foreach ($rs7 as $count7) {
            $Total_Outcome = $count7->paid;
            if ($Total_Outcome == null) {
                $Total_Outcome = "0";
            }
        }


        if ($patients != 0 && $samples != 0 && $invoices != 0 && $Total_Income != "0" || $Total_Outcome != "0" && $GRNs != 0) {
            $result = "<p class='finDetailHead'>Day Summery</p>" . "<table width=100%><tr><td>" . "<table class='finDetailField'>" . "<tr>" . "<td>Total Patient Count</td><td>: " . $patients . "</td>" . "</tr>" . "<tr>" . "<td>Total Sample Count</td><td>: " . $samples . "</td>" . "</tr>" . "<tr>" . "<td>Total Invoice Count</td><td>: " . $invoices . "</td>" . "</tr>" . "<tr>" . "<td>Total Income " . $cuSymble . "</td><td> : " . $Total_Income . "</td>" . "</tr>" . "</table>" . "</td>" . "<td>" . "<table class='finDetailField'>" . "<tr>" . "<td>Total GRN Count</td><td> : " . $GRNs . "</td>" . "</tr>" . "<tr>" . "<td>Total Payment Count</td><td> : " . $Payment . "</td>" . "</tr>" . "<tr>" . "<td>Total Outcome " . $cuSymble . "</td><td> : " . $Total_Outcome . "</td>" . "</tr>" . "</table>" . "</td></tr></table><p></p>" . "<table class='finDetailField2'><tr>" . "<td>Tatal Events</td> <td>: " . ($invoices + $GRNs) . "</td></tr>";

            $totIn = $Total_Income;
            $totOut = $Total_Outcome;

            $dProf = $totIn - $totOut;
            $prof = number_format($dProf, 2);


            if ($dProf <= 0) {
                $result .= "<tr><td>Tatal Profit</td> <td style='color:red'>: " . $cuSymble . " " . $prof . "</td>" . "</tr></table>";
            } else {
                $result .= "<tr><td>Tatal Profit</td> <td>: " . $cuSymble . " " . $prof . "</td>" . "</tr></table>";
            }
        }


        return $result;
    }

//---------------------------------------------------------------------------------
//MonthIncomeSumery----------------------------------
    function getMonthIncomeSumery($lid, $month, $year, $cuSymble, $ref, $brcode) {

        $result = "<p class='finDetailField'>There is no any income details for this date and reference.</p>";

        if (!$ref) {
            $rs = DB::select("select count(patient_pid) as pcnt from lps where year(date) = '" . $year . "' and month(date) = '" . $month . "' and lab_lid = '" . $lid . "' " . $brcode . "");
            $rs2 = DB::select("select count(sampleNo) as scnt from lps where year(date) = '" . $year . "' and month(date) = '" . $month . "' and lab_lid = '" . $lid . "' " . $brcode . "");
            $rs3 = DB::select("select count(iid) as icnt from invoice where year(date) = '" . $year . "' and month(date) = '" . $month . "' and lps_lpsid in (select lpsid from lps where lab_lid = '" . $lid . "' " . $brcode . ")");
            $rs4 = DB::select("select total,paid,gtotal,Discount_did from invoice where year(date) = '" . $year . "' and month(date) = '" . $month . "' and lps_lpsid in (select lpsid from lps where lab_lid = '" . $lid . "' " . $brcode . ")");
        } elseif ($ref == "null") {
            $rs = DB::select("select count(patient_pid) as pcnt from lps where year(date) = '" . $year . "' and month(date) = '" . $month . "' and lab_lid = '" . $lid . "' " . $brcode . " and refference_idref is NULL");
            $rs2 = DB::select("select count(sampleNo) as scnt from lps where year(date) = '" . $year . "' and month(date) = '" . $month . "' and lab_lid = '" . $lid . "' " . $brcode . " and refference_idref is NULL");
            $rs3 = DB::select("select count(iid) as icnt from invoice where year(date) = '" . $year . "' and month(date) = '" . $month . "' and lps_lpsid in (select lpsid from lps where lab_lid = '" . $lid . "' " . $brcode . " and refference_idref is NULL)");
            $rs4 = DB::select("select total,paid,gtotal,Discount_did from invoice where year(date) = '" . $year . "' and month(date) = '" . $month . "' and lps_lpsid in (select lpsid from lps where lab_lid = '" . $lid . "' " . $brcode . " and refference_idref is NULL)");
        } else {
            $rs = DB::select("select count(patient_pid) as pcnt from lps where year(date) = '" . $year . "' and month(date) = '" . $month . "' and lab_lid = '" . $lid . "' " . $brcode . " and refference_idref = '" . $ref . "'");
            $rs2 = DB::select("select count(sampleNo) as scnt from lps where year(date) = '" . $year . "' and month(date) = '" . $month . "' and lab_lid = '" . $lid . "' " . $brcode . " and refference_idref = '" . $ref . "'");
            $rs3 = DB::select("select count(iid) as icnt from invoice where year(date) = '" . $year . "' and month(date) = '" . $month . "' and lps_lpsid in (select lpsid from lps where lab_lid = '" . $lid . "' " . $brcode . " and refference_idref = '" . $ref . "')");
            $rs4 = DB::select("select total,paid,gtotal,Discount_did from invoice where year(date) = '" . $year . "' and month(date) = '" . $month . "' and lps_lpsid in (select lpsid from lps where lab_lid = '" . $lid . "' " . $brcode . " and refference_idref = '" . $ref . "')");
        }

        foreach ($rs as $count) {
            $patients = $count->pcnt;
        }

        foreach ($rs2 as $count2) {
            $samples = $count2->scnt;
        }

        foreach ($rs3 as $count3) {
            $invoices = $count3->icnt;
        }

        $Total_Income = 0;
        $Total_gtot = 0;
        $Total_dis = 0;
        $Total_tot = 0;
        foreach ($rs4 as $count4) {
            $Total_Income += $count4->paid;
            $Total_gtot += $count4->gtotal;
            $Total_tot += $count4->total;

            if ($count4->Discount_did != null) {
                $ResultD = DB::select("select value from Discount where did = '" . $count4->Discount_did . "' and Lab_lid = '" . $lid . "' ");
                foreach ($ResultD as $Ditem) {
                    $tot = $count4->total;
                    $disVal = $Ditem->value;

                    $disAmount = $tot * ($disVal / 100);

                    $Total_dis += $disAmount;
                }
            }
        }

        if ($patients != 0 && $samples != 0 && $invoices != 0) {
//            $Total_Income = number_format($Total_Income, 2);
            $result = "<p class='finDetailHead'>Income Summery</p>" . "<table class='finDetailField' width='500px'>" . "<tr>" . "<td>Total Patient Count  </td><td>:</td><td align='right'>" . $patients . "</td>" . "</tr>" . "<tr>" . "<td>Total Sample Count  </td><td>:</td><td align='right'>" . $samples . "</td>" . "</tr>" . "<tr>" . "<td>Total Invoice Count  </td><td>:</td><td align='right'>" . $invoices . "</td>" . "</tr><tr><td><hr></td><td><hr></td><td><hr></td></tr><tr>" . "<td>Total Sales Amount </td><td>: " . $cuSymble . " </td><td align='right'>" . number_format($Total_tot, 2) . "</td>" . "</tr><tr>" . "<td>Total Discount Amount </td><td>: " . $cuSymble . "</td><td align='right'> " . number_format($Total_dis, 2) . "</td>" . "</tr><tr>" . "<td>Total Income </td><td>: " . $cuSymble . " </td><td align='right'>" . number_format($Total_gtot, 2) . "</td>" . "</tr><tr>" . "<td>Total Paid </td><td>: " . $cuSymble . "</td><td align='right'> " . number_format($Total_Income, 2) . "</td>" . "</tr>" . "</table>";
        }
        return $result;
    }

    function getMonthIncomeSumeryOLD($lid, $month, $year, $cuSymble) {


        $result = "<p class='finDetailField'>There is no any income details for this date.</p>";


        $rs = DB::select("select count(patient_pid) as cpaid from lps where year(date) = '" . $year . "' and month(date) = '" . $month . "' and lab_lid = '" . $lid . "'");
        foreach ($rs as $count) {
            $confirm = 1;
        }
        if (isset($confirm)) {
            $patients = $count->cpaid;
        }

        $rs2 = DB::select("select count(sampleNo)scnt from lps where year(date) = '" . $year . "' and month(date) = '" . $month . "' and lab_lid = '" . $lid . "'");
        foreach ($rs2 as $count2) {
            $confirm2 = 1;
        }
        if (isset($confirm2)) {
            $samples = $count2->scnt;
        }

        $rs3 = DB::select("select count(iid)as icnt from invoice where year(date) = '" . $year . "' and month(date) = '" . $month . "' and lps_lpsid in (select lpsid from lps where lab_lid = '" . $lid . "')");
        foreach ($rs3 as $count3) {
            $confirm3 = 1;
        }
        if (isset($confirm3)) {
            $invoices = $count3->icnt;
        }

        $rs4 = DB::select("select round(SUM(paid),2) as paid from invoice where year(date) = '" . $year . "' and month(date) = '" . $month . "' and lps_lpsid in (select lpsid from lps where lab_lid = '" . $lid . "')");
        foreach ($rs4 as $count4) {
            $confirm4 = 1;
        }
        if (isset($confirm4)) {
            $Total_Income = $count4->paid;
        }


        if ($patients != 0 && $samples != 0 && $invoices != 0 && $Total_Income != "0") {
            $Total_Income = number_format($Total_Income, 2);
            $result = "<p class='finDetailHead'>Income Summery</p>" . "<table class='finDetailField'>" . "<tr>" . "<td>Total Patient Count : </td><td>" . $patients . "</td>" . "</tr>" . "<tr>" . "<td>Total Sample Count : </td><td>" . $samples . "</td>" . "</tr>" . "<tr>" . "<td>Total Invoice Count : </td><td>" . $invoices . "</td>" . "</tr>" . "<tr>" . "<td>Total Income : " . $cuSymble . "</td><td>" . $Total_Income . "</td>" . "</tr>" . "</table>";
        }

        return $result;
    }

//---------------------------------------------------------------------------------
//MonthOutcomeSumery----------------------------------
    function getMonthOutcomeSumery($lid, $month, $year, $cuSymble, $brcode) {

        if ($brcode == "") {
            $brcode = "%";
        }

        $result = "<p class='finDetailField'>There is no any Outcome details for this date.</p>";


        $rs = DB::select("select count(payid) as paid from payments where year(date) = '" . $year . "' and month(date) = '" . $month . "' and lab_lid ='" . $lid . "' and branchcode like = '" . $brcode . "'");
        foreach ($rs as $count) {
            $confirm = 1;
        }
        if (isset($confirm)) {
            $Payment = $count->paid;
        }


        $rs2 = DB::select("select count(grnid) as gcnt from grn where year(date) = '" . $year . "' and month(date) = '" . $month . "' and grnid in (select grn_grnid from Labmaterials_has_grn where labmaterials_lmid in (select lmid from Lab_has_materials where lab_lid='" . $lid . "') )");
        foreach ($rs2 as $count2) {
            $confirm2 = 1;
        }
        if (isset($confirm2)) {
            $GRNs = $count2->gcnt;
        }

        $rs4 = DB::select("select round(SUM(paid),2) as paid from grn where year(date) = '" . $year . "' and month(date) = '" . $month . "' and grnid in (select grn_grnid from Labmaterials_has_grn where labmaterials_lmid in (select lmid from Lab_has_materials where lab_lid='" . $lid . "') )");
        foreach ($rs4 as $count4) {
            $confirm4 = 1;
        }
        if (isset($confirm4)) {
            $Total_Outcome = $count4->paid;
            if ($Total_Outcome == null) {
                $Total_Outcome = "0";
            }
        }

        if ($Total_Outcome != "0" && $GRNs != 0) {
            $Total_Outcome = number_format($Total_Outcome, 2);
            $result = "<p class='finDetailHead'>Outcome Summery</p>" . "<table class='finDetailField'>" . "<tr>" . "<td>Total GRN Count : </td><td>" . $GRNs . "</td>" . "</tr>" . "<tr>" . "<td>Total Payment Count : </td><td>" . $Payment . "</td>" . "</tr>" . "<tr>" . "<td>Total Outcome : " . $cuSymble . "</td><td>" . $Total_Outcome . "</td>" . "</tr>" . "</table>";
        }


        return $result;
    }

//---------------------------------------------------------------------------------
//MonthProfitSumery----------------------------------
    function getMonthProfitSumery($lid, $month, $year, $cuSymble, $brcode) {

        $result = "<p class='finDetailField'>There is no any Income and outcome details for this date.</p>";

        $rs = DB::select("select count(patient_pid) as pcnt from lps where year(date) = '" . $year . "' and month(date) = '" . $month . "' and lab_lid = '" . $lid . "' " . $brcode . "");
        foreach ($rs as $count) {
            $confirm = 1;
        }
        if (isset($confirm)) {
            $patients = $count->pcnt;
        }

        $rs2 = DB::select("select count(sampleNo) as scnt from lps where year(date) = '" . $year . "' and month(date) = '" . $month . "' and lab_lid = '" . $lid . "' " . $brcode . "");
        foreach ($rs2 as $count2) {
            $confirm2 = 1;
        }
        if (isset($confirm2)) {
            $samples = $count2->scnt;
        }

        $rs3 = DB::select("select count(iid) as icnt from invoice where year(date) = '" . $year . "' and month(date) = '" . $month . "' and lps_lpsid in (select lpsid from lps where lab_lid = '" . $lid . "' " . $brcode . ")");
        foreach ($rs3 as $count3) {
            $confirm3 = 1;
        }
        if (isset($confirm3)) {
            $invoices = $count3->icnt;
        }


        $rs4 = DB::select("select round(SUM(paid),2) as paid from invoice where year(date) = '" . $year . "' and month(date) = '" . $month . "' and lps_lpsid in (select lpsid from lps where lab_lid = '" . $lid . "' " . $brcode . ")");
        foreach ($rs4 as $count4) {
            $confirm4 = 1;
        }
        if (isset($confirm4)) {
            $Total_Income = $count4->paid;
        }

        if ($brcode == "") {
            $brcode = "%";
        }

        $rs5 = DB::select("select count(payid) as pcnt from payments where year(date) = '" . $year . "' and month(date) = '" . $month . "' and lab_lid ='" . $lid . "' and branchcode like = '" . $brcode . "'");
        foreach ($rs5 as $count5) {
            $confirm5 = 1;
        }
        if (isset($confirm5)) {
            $Payment = $count5->pcnt;
        }

        $rs6 = DB::select("select count(grnid) as gcnt from grn where year(date) = '" . $year . "' and month(date) = '" . $month . "' and grnid in (select grn_grnid from Labmaterials_has_grn where labmaterials_lmid in (select lmid from Lab_has_materials where lab_lid='" . $lid . "') )");
        foreach ($rs6 as $count6) {
            $confirm6 = 1;
        }
        if (isset($confirm6)) {
            $GRNs = $count6->gcnt;
        }

        $rs7 = DB::select("select round(SUM(paid),2) as paid from grn where year(date) = '" . $year . "' and month(date) = '" . $month . "' and grnid in (select grn_grnid from Labmaterials_has_grn where labmaterials_lmid in (select lmid from Lab_has_materials where lab_lid='" . $lid . "') )");
        foreach ($rs7 as $count7) {
            $confirm7 = 1;
        }
        if (isset($confirm7)) {
            $Total_Outcome = $count7->paid;
        }


        if (($patients != 0 && $samples != 0 && $invoices != 0 && $Total_Income != "0") || ($Total_Outcome != "0" && $GRNs != 0)) {
            $result = "<p class='finDetailHead'>Month Summery</p>" . "<table width=100%><tr><td>" . "<table class='finDetailField'>" . "<tr>" . "<td>Total Patient Count</td><td>: " . $patients . "</td>" . "</tr>" . "<tr>" . "<td>Total Sample Count</td><td>: " . $samples . "</td>" . "</tr>" . "<tr>" . "<td>Total Invoice Count</td><td>: " . $invoices . "</td>" . "</tr>" . "<tr>" . "<td>Total Income " . $cuSymble . "</td><td> : " . $Total_Income . "</td>" . "</tr>" . "</table>" . "</td>" . "<td>" . "<table class='finDetailField'>" . "<tr>" . "<td>Total GRN Count</td><td> : " . $GRNs . "</td>" . "</tr>" . "<tr>" . "<td>Total Payment Count</td><td> : " . $Payment . "</td>" . "</tr>" . "<tr>" . "<td>Total Outcome " . $cuSymble . "</td><td> : " . $Total_Outcome . "</td>" . "</tr>" . "</table>" . "</td></tr></table><p></p>" . "<table class='finDetailField2'><tr>" . "<td>Tatal Deals made</td> <td>: " . ($invoices . $GRNs) . "</td></tr>";


            $totIn = $Total_Income;
            $totOut = $Total_Outcome;

            $dProf = $totIn - $totOut;
            $prof = number_format($dProf, 2);




            if ($dProf <= 0) {
                $result .= "<tr><td>Tatal Profit</td> <td style='color:red'>: " . $cuSymble . " " . $prof . "</td>" . "</tr></table>";
            } else {
                $result .= "<tr><td>Tatal Profit</td> <td>: " . $cuSymble . " " . $prof . "</td>" . "</tr></table>";
            }
        }


        return $result;
    }

//---------------------------------------------------------------------------------
//YearIncomeSumery----------------------------------
    function getYearIncomeSumery($lid, $year, $cuSymble, $ref, $brcode) {

        $result = "<p class='finDetailField'>There is no any income details for this year and reference.</p>";

        if (!$ref) {
            $rs = DB::select("select count(patient_pid) as pcnt from lps where year(date) = '" . $year . "' and lab_lid = '" . $lid . "' " . $brcode . "");
            $rs2 = DB::select("select count(sampleNo) as scnt from lps where year(date) = '" . $year . "' and lab_lid = '" . $lid . "' " . $brcode . "");
            $rs3 = DB::select("select count(iid) as icnt from invoice where year(date) = '" . $year . "' and lps_lpsid in (select lpsid from lps where lab_lid = '" . $lid . "' " . $brcode . ")");
            $rs4 = DB::select("select total,paid,gtotal,Discount_did from invoice where year(date) = '" . $year . "' and lps_lpsid in (select lpsid from lps where lab_lid = '" . $lid . "' " . $brcode . ")");
        } elseif ($ref == "null") {
            $rs = DB::select("select count(patient_pid) as pcnt from lps where year(date) = '" . $year . "' and lab_lid = '" . $lid . "' " . $brcode . " and refference_idref is NULL");
            $rs2 = DB::select("select count(sampleNo) as scnt from lps where year(date) = '" . $year . "' and lab_lid = '" . $lid . "' " . $brcode . " and refference_idref is NULL");
            $rs3 = DB::select("select count(iid) as icnt from invoice where year(date) = '" . $year . "' and lps_lpsid in (select lpsid from lps where lab_lid = '" . $lid . "' " . $brcode . " and refference_idref is NULL)");
            $rs4 = DB::select("select total,paid,gtotal,Discount_did from invoice where year(date) = '" . $year . "' and lps_lpsid in (select lpsid from lps where lab_lid = '" . $lid . "' " . $brcode . " and refference_idref is NULL)");
        } else {
            $rs = DB::select("select count(patient_pid) as pcnt from lps where year(date) = '" . $year . "' and lab_lid = '" . $lid . "' and refference_idref = '" . $ref . "' " . $brcode . "");
            $rs2 = DB::select("select count(sampleNo) as scnt from lps where year(date) = '" . $year . "' and lab_lid = '" . $lid . "' and refference_idref = '" . $ref . "' " . $brcode . "");
            $rs3 = DB::select("select count(iid) as icnt from invoice where year(date) = '" . $year . "' and lps_lpsid in (select lpsid from lps where lab_lid = '" . $lid . "' " . $brcode . " and refference_idref = '" . $ref . "')");
            $rs4 = DB::select("select total,paid,gtotal,Discount_did from invoice where year(date) = '" . $year . "' and lps_lpsid in (select lpsid from lps where lab_lid = '" . $lid . "' " . $brcode . " and refference_idref = '" . $ref . "')");
        }

        foreach ($rs as $count) {
            $patients = $count->pcnt;
        }

        foreach ($rs2 as $count2) {
            $samples = $count2->scnt;
        }

        foreach ($rs3 as $count3) {
            $invoices = $count3->icnt;
        }

        $Total_Income = 0;
        $Total_gtot = 0;
        $Total_tot = 0;
        $Total_dis = 0;

        foreach ($rs4 as $count4) {
            $Total_Income += $count4->paid;
            $Total_gtot += $count4->gtotal;
            $Total_tot += $count4->total;

            if ($count4->Discount_did != null) {
                $ResultD = DB::select("select value from Discount where did = '" . $count4->Discount_did . "'and Lab_lid = '" . $lid . "' ");
                foreach ($ResultD as $Ditem) {
                    $tot = $count4->total;
                    $disVal = $Ditem->value;

                    $disAmount = $tot * ($disVal / 100);

                    $Total_dis += $disAmount;
                }
            }
        }

        if ($patients != 0 && $samples != 0 && $invoices != 0) {
//            $Total_Income = number_format($Total_Income, 2);
            $result = "<p class='finDetailHead'>Income Summery</p>" . "<table class='finDetailField' width='500px'>" . "<tr>" . "<td>Total Patient Count  </td><td>:</td><td align='right'>" . $patients . "</td>" . "</tr>" . "<tr>" . "<td>Total Sample Count  </td><td>:</td><td align='right'>" . $samples . "</td>" . "</tr>" . "<tr>" . "<td>Total Invoice Count  </td><td>:</td><td align='right'>" . $invoices . "</td>" . "</tr><tr><td><hr></td><td><hr></td><td><hr></td></tr><tr>" . "<td>Total Sales Amount </td><td>: " . $cuSymble . " </td><td align='right'>" . number_format($Total_tot, 2) . "</td>" . "</tr><tr>" . "<td>Total Discount Amount </td><td>: " . $cuSymble . "</td><td align='right'> " . number_format($Total_dis, 2) . "</td>" . "</tr><tr>" . "<td>Total Income </td><td>: " . $cuSymble . " </td><td align='right'>" . number_format($Total_gtot, 2) . "</td>" . "</tr><tr>" . "<td>Total Paid </td><td>: " . $cuSymble . "</td><td align='right'> " . number_format($Total_Income, 2) . "</td>" . "</tr>" . "</table>";
        }
        return $result;
    }

//---------------------------------------------------------------------------------
//YearOutcomeSumery----------------------------------
    function getYearOutcomeSumery($lid, $year, $cuSymble, $brcode) {

        if ($brcode == "") {
            $brcode = "%";
        }

        $result = "<p class='finDetailField'>There is no any Outcome details for this year.</p>";

        $rs = DB::select("select count(payid) as paid from payments where year(date) = '" . $year . "' and lab_lid ='" . $lid . "' and branchcode like = '" . $brcode . "'");
        foreach ($rs as $count) {
            $Payment = $count->paid;
        }



        $rs3 = DB::select("select count(grnid) as gcnt from grn where year(date) = '" . $year . "' and grnid in (select grn_grnid from Labmaterials_has_grn where labmaterials_lmid in (select lmid from Lab_has_materials where lab_lid='" . $lid . "') )");
        foreach ($rs3 as $count3) {
            $Total_Outcome = $count3->gcnt;
        }


        $rs4 = DB::select("select round(SUM(paid),2) as paid from grn where year(date) = '" . $year . "' and grnid in (select grn_grnid from Labmaterials_has_grn where labmaterials_lmid in (select lmid from Lab_has_materials where lab_lid='" . $lid . "') )");
        foreach ($rs4 as $count4) {
            $GRNs = $count4->paid;
        }


        if ($Total_Outcome != "0" && $GRNs != 0) {
            $Total_Outcome = number_format($Total_Outcome, 2);
            $result = "<p class='finDetailHead'>Outcome Summery</p>" . "<table class='finDetailField'>" . "<tr>" . "<td>Total GRN Count : </td><td>" . $GRNs . "</td>" . "</tr>" . "<tr>" . "<td>Total Payment Count : </td><td>" . $Payment . "</td>" . "</tr>" . "<tr>" . "<td>Total Outcome " . $cuSymble . " : </td><td>" . $Total_Outcome . "</td>" . "</tr>" . "</table>";
        }


        return $result;
    }

//---------------------------------------------------------------------------------
//YearOutcomeSumery----------------------------------
    function getYearProfitSumery($lid, $year, $cuSymble, $brcode) {


        $result = "<p class='finDetailField'>There is no any Income and outcome details for this year.</p>";


        $rs = DB::select("select count(patient_pid) as paid from lps where year(date) = '" . $year . "' and lab_lid = '" . $lid . "' " . $brcode . "");
        foreach ($rs as $count) {
            $patients = $count->paid;
        }


        $rs2 = DB::select("select count(sampleNo) as scnt from lps where year(date) = '" . $year . "' and lab_lid = '" . $lid . "' " . $brcode . "");
        foreach ($rs2 as $count2) {
            $samples = $count2->scnt;
        }


        $rs3 = DB::select("select count(iid) as icnt from invoice where year(date) = '" . $year . "' and lps_lpsid in (select lpsid from lps where lab_lid = '" . $lid . "' " . $brcode . ")");
        foreach ($rs3 as $count3) {
            $invoices = $count3->icnt;
        }


        $rs4 = DB::select("select round(SUM(paid),2) as paid from invoice where year(date) = '" . $year . "' and lps_lpsid in (select lpsid from lps where lab_lid = '" . $lid . "' " . $brcode . ")");
        foreach ($rs4 as $count4) {
            $Total_Income = $count4->paid;
        }

        if ($brcode == "") {
            $brcode = "%";
        }

        $rs5 = DB::select("select count(payid) outcnt from payments where year(date) = '" . $year . "' and lab_lid ='" . $lid . "' and branchcode like = '" . $brcode . "'");
        foreach ($rs5 as $count5) {
            $Total_Payment = $count5->outcnt;
        }


        $rs6 = DB::select("select count(grnid) as gcnt from grn where year(date) = '" . $year . "' and grnid in (select grn_grnid from Labmaterials_has_grn where labmaterials_lmid in (select lmid from Lab_has_materials where lab_lid='" . $lid . "') )");
        foreach ($rs6 as $count6) {
            $GRNs = $count6->gcnt;
        }


        $rs7 = DB::select("select round(SUM(paid),2) as paid from grn where year(date) = '" . $year . "' and grnid in (select grn_grnid from Labmaterials_has_grn where labmaterials_lmid in (select lmid from Lab_has_materials where lab_lid='" . $lid . "') )");
        foreach ($rs7 as $count7) {
            $Total_Outcome = $count7->paid;
            if ($Total_Outcome == null) {
                $Total_Outcome = "0";
            }
        }



        if ($patients != 0 && $samples != 0 && $invoices != 0 && $Total_Income != "0" || $Total_Payment != "0" && $GRNs != 0) {
            $result = "<p class='finDetailHead'>Year Summery</p>" . "<table width=100%><tr><td>" . "<table class='finDetailField'>" . "<tr>" . "<td>Total Patient Count</td><td>: " . $patients . "</td>" . "</tr>" . "<tr>" . "<td>Total Sample Count</td><td>: " . $samples . "</td>" . "</tr>" . "<tr>" . "<td>Total Invoice Count</td><td>: " . $invoices . "</td>" . "</tr>" . "<tr>" . "<td>Total Income " . $cuSymble . "</td><td> : " . $Total_Income . "</td>" . "</tr>" . "</table>" . "</td>" . "<td>" . "<table class='finDetailField'>" . "<tr>" . "<td>Total GRN Count</td><td> : " . $GRNs . "</td>" . "</tr>" . "<tr>" . "<td>Total Payment Count</td><td> : " . $Total_Payment . "</td>" . "</tr>" . "<tr>" . "<td>Total Outcome " . $cuSymble . "</td><td> : " . $Total_Outcome . "</td>" . "</tr>" . "</table>" . "</td></tr></table><p></p>" . "<table class='finDetailField2'><tr>" . "<td>Tatal events</td> <td>: " . ($invoices + $GRNs) . "</td></tr>";

            $totIn = $Total_Income;
            $totOut = $Total_Outcome;

            $dProf = $totIn - $totOut;
            $prof = number_format($dProf, 2);

            if ($dProf <= 0) {
                $result .= "<tr><td>Tatal Profit</td> <td style='color:red'>: " . $cuSymble . " " . $prof . "</td>" . "</tr></table>";
            } else {
                $result .= "<tr><td>Tatal Profit</td> <td>: " . $cuSymble . " " . $prof . "</td>" . "</tr></table>";
            }
        }

        return $result;
    }

}

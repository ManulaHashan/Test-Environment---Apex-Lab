<?php

if (!isset($_SESSION)) {
    session_start();
}
date_default_timezone_set('Asia/Colombo');

class FinanceController extends Controller {

    function getDayFinanceSummeryClient() {

        if (Input::get('lid') == 24) {
            if (Input::get('date') != null) {
                $gtot = 0;
                $paid = 0;
                $due = 0;
                $countx = 0;
                $total_expenses = 0;

                $code = "%";
                if (Input::get('code') !== null) {
                    if (Input::get('code') != "") {
                        $code = Input::get('code') . "%";
                    }
                }


                $rs = DB::select("select a.iid, a.gtotal, a.paid from invoice a, lps b where a.lps_lpsid = b.lpsid and b.sampleno like '" . $code . "' and a.date = '" . Input::get('date') . "' and b.Lab_lid = '" . Input::get('lid') . "' and (a.status = 'Payment Done' or a.status = 'Pending Due' or a.status = 'Not Paid') and a.cashier like '%". Input::get('user') ."%' ");
                foreach ($rs as $count) {
                    $countx += 1;
                    $gtot += $count->gtotal;
//                $paid += $count->paid;

                    if ($count->gtotal > $count->paid) {
                        $due += $count->gtotal - $count->paid;
                    }
                }

                $rs = DB::select("select SUM(i.amount) as paid from invoice a, lps b,invoice_payments i where a.iid=i.invoice_iid and a.lps_lpsid=b.lpsid and b.sampleno like '" . $code . "' and i.date='" . Input::get('date') . "' and b.Lab_lid='" . Input::get('lid') . "' and a.cashier like '%". Input::get('user') ."%'");
                foreach ($rs as $count) {
                    $paid += $count->paid;
                }

                $rs = DB::select("SELECT SUM(price) as exps FROM payments where lab_lid = '" . Input::get('lid') . "' and date = '" . Input::get('date') . "'");
                foreach ($rs as $count) {
                    $total_expenses = $count->exps;
                }

                if ($total_expenses == "") {
                    $total_expenses = 0;
                }

                echo $gtot . "//" . $paid . "//" . $due . "//" . $countx . "//" . $total_expenses;
            }
        } else {
            if (Input::get('date') != null) {
                $gtot = 0;
                $paid = 0;
                $due = 0;
                $countx = 0;
                $total_expenses = 0;

                $code = "%";
                if (Input::get('code') !== null) {
                    if (Input::get('code') != "") {
                        $code = Input::get('code') . "%";
                    }
                }


                $rs = DB::select("select a.iid, a.gtotal, a.paid from invoice a, lps b where a.lps_lpsid = b.lpsid and b.sampleno like '" . $code . "' and a.date = '" . Input::get('date') . "' and b.Lab_lid = '" . Input::get('lid') . "' and (a.status = 'Payment Done' or a.status = 'Pending Due' or a.status = 'Not Paid')");
                foreach ($rs as $count) {
                    $countx += 1;
                    $gtot += $count->gtotal;
//                $paid += $count->paid;

                    if ($count->gtotal > $count->paid) {
                        $due += $count->gtotal - $count->paid;
                    }
                }

                $rs = DB::select("select SUM(i.amount) as paid from invoice a, lps b,invoice_payments i where a.iid=i.invoice_iid and a.lps_lpsid=b.lpsid and b.sampleno like '" . $code . "' and i.date='" . Input::get('date') . "' and b.Lab_lid='" . Input::get('lid') . "'");
                foreach ($rs as $count) {
                    $paid += $count->paid;
                }

                $rs = DB::select("SELECT SUM(price) as exps FROM payments where lab_lid = '" . Input::get('lid') . "' and date = '" . Input::get('date') . "'");
                foreach ($rs as $count) {
                    $total_expenses = $count->exps;
                }

                if ($total_expenses == "") {
                    $total_expenses = 0;
                }

                echo $gtot . "//" . $paid . "//" . $due . "//" . $countx . "//" . $total_expenses;
            }
        }
    }

    function getRefferences() {
        $rs = DB::select("select idref, name from refference where lid = '" . $_SESSION['lid'] . "' order by name ASC");
        return json_encode($rs);
    }

    function getBranches() {
        $rs = DB::select("select name, code from labbranches where lab_lid = '" . $_SESSION['lid'] . "'");
        return json_encode($rs);
    }

    function getTests() {
        $rs = DB::select("select name, tgid from Testgroup where lab_lid = '" . $_SESSION['lid'] . "' order by name ASC");
        return json_encode($rs);
    }

    function getPaymentMethodTypes(){
        $rs = DB::select("select idpaymethod,name from paymethod");
        return json_encode($rs);
    }

}

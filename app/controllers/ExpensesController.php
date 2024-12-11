<?php

if (!isset($_SESSION)) {
    session_start();
}
date_default_timezone_set('Asia/Colombo');

class ExpensesController extends Controller {

    function manageExpenses() {
        if (Input::get('submit') != null) {
            if (Input::get('submit') == "Add Expense") {
                if (Input::get('desc') != "") {
                    $desc = Input::get("desc");
                    $price = Input::get("tgprice");
                    $date = Input::get("date");
                    $brcode = Input::get("brcode");                    
                    
                    $vendor = Input::get("sup");
                    $cno = Input::get("cno");
                    $cdate = Input::get("cdt");


                    if (Input::get("scno") !== "") {
                        $scdate = Input::get("scdate");
                        $scno = Input::get("scno");
                    }


                    $Result = DB::select("select * from payments where date = '" . $date . "' and Lab_lid = '" . $_SESSION['lid'] . "' and reason = '" . $desc . "' and branchcode='" . $brcode . "'");
                    foreach ($Result as $res) {
                        $tgroup = $res;
                    }
                    if (isset($tgroup)) {
                        return View::make('WiExpenses')->with('msg', 'Expenses Exsist!');
                    } else { 
                        if (Input::get("scno") !== "") {
                            DB::statement("insert into payments(reason,price,Lab_lid,date,branchcode,user_uid,sdate,sno,vendor,chequeno,chequedate) values('" . $desc . "','" . $price . "','" . $_SESSION['lid'] . "','" . $date . "','" . $brcode . "',(select user_uid from labUser where luid = '" . $_SESSION['luid'] . "'),'".$scdate."','".$scno."','" . $vendor . "','" . $cno . "','" . $cdate . "')");
                            DB::statement("update lps set cancelled = '1', status='Cancelled' where date = '" . $scdate . "' and sampleno = '" . $scno . "' and lab_lid = '" . $_SESSION['lid'] . "'");
                        } else {
                            DB::statement("insert into payments(reason,price,Lab_lid,date,branchcode,user_uid,vendor,chequeno,chequedate) values('" . $desc . "','" . $price . "','" . $_SESSION['lid'] . "','" . $date . "','" . $brcode . "',(select user_uid from labUser where luid = '" . $_SESSION['luid'] . "'),'" . $vendor . "','" . $cno . "','" . $cdate . "')");
                        }
                        return View::make('WiExpenses')->with('msg', 'Expense Added!');
                    }
                } else {
                    return View::make('WiExpenses')->with('msg', 'Enter correct values!');
                }
            } else if (Input::get('submit') == "Delete") {
                $tgID = Input::get("tgid");
                DB::statement("delete from payments where payid = '" . $tgID . "'");
                return View::make('WiExpenses')->with('msg', 'Expense Deleted!');
            }
        } else {
            return View::make('Witestgroups')->with('msg', 'Enter correct values!');
        }
    }

    function searchExpenses() {

        $dec = '%';
        if (Input::get("dec") != "") {
            $dec = Input::get("dec");
        }

        $br = '%';
        if (Input::get("br") != "ALL") {
            $dec = Input::get("dec");
        }
        
        $cdate = "";
        if (Input::get("cdate") != "") {
            $cdate = "and a.chequedate = '" . Input::get("cdate") . "'";
        }
        
        $vendor = "";
        if (Input::get("sup") != "") {
            $vendor = "and a.vendor like '" . Input::get("sup") . "'";
        }
        

        $totAmount = 0;

        $out = "<tr>
                <th width='15%' class='viewTHead' scope='col'>Date</th>
                <th width='10%' class='viewTHead' scope='col'>Vendor</th>
                <th width='25%' class='viewTHead' scope='col'>Reason</th>
                <th width='10%' class='viewTHead' scope='col'>Price Rs.</th>
                <th width='5%' class='viewTHead' scope='col'>Cheque NO</th>
                <th width='5%' class='viewTHead' scope='col'>Cheque Date</th>
                <th width='10%' class='viewTHead' scope='col'>Branch</th>                
                <th width='10%' class='viewTHead' scope='col'>Sample</th>
                <th width='10%' class='viewTHead' scope='col'>Sample Date</th>


                <th width='20%' class='viewTHead' scope='col'>User</th>
                </tr>";

        $Result = DB::select("select a.*,b.fname from payments a, user b where a.user_uid = b.uid and a.date between '" . Input::get("do") . "' and '" . Input::get("dt") . "' and a.Lab_lid = '" . $_SESSION['lid'] . "' and a.reason like '" . $dec . "' and a.branchcode like '" . $br . "' ".$vendor." ".$cdate." ");
        foreach ($Result as $res) {
            $out .= "<tr><td>" . $res->date . "</td> <td>" . $res->vendor . "</td> <td>" . $res->reason . "</td> <td align='right'>" . number_format($res->price,2) . "</td> <td>" . $res->chequeno . "</td> <td>" . $res->chequedate . "</td> <td>" . $res->branchcode . "</td> <td>" . $res->sno . "</td> <td>" . $res->sdate . "</td> <td>" . $res->fname . "</td> "
                    . "<td><input id=" . $res->payid . " type='submit' class='btn' style='margin:0px;' name='submit' value='Delete' onClick='gettgID(id)'></td>";

            $totAmount += $res->price;
        }

        $totAmount = number_format($totAmount, 2);

//        $out = "select * from payments where date between '" . Input::get("do") . "' and '" . Input::get("dt") . "' and Lab_lid = '" . $_SESSION['lid'] . "' and reason = '" . Input::get("dec") . "' and branchcode='" . Input::get("br") . "'";

        return $out . "/#/#/#" . $totAmount;
    }

    function getBillAmunt() {
        $totAmount = 0;
        $Result = DB::select("select * from invoice where lps_lpsid = (select lpsid from lps where date = '" . Input::get("sdate") . "' and sampleno = '" . Input::get("sno") . "' and lab_lid = '" . $_SESSION['lid'] . "')");
        foreach ($Result as $res) {

            $totAmount = $res->gtotal;
        }

        if ($totAmount == 0) {
            return "0";
        } else {
            return $totAmount;
        }


//        return "select * from invoice where lps_lpsid = (select lpsid form lps where date = '".Input::get("sdate")."' and sampleno = '".Input::get("sno")."' and lab_lid = '".$_SESSION['lid']."')";
    }

}

?>
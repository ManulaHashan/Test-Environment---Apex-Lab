<?php

if (!isset($_SESSION)) {
    session_start();
}

class WasteController extends Controller {

    function submit() {
        $lid = $_SESSION['lid'];
        $luid = $_SESSION['luid'];
        $lmid = Input::get('lmid');

        if (isset($luid)) {
            $submit = Input::get('submit');

            if ($submit == "Add Wastage") {

                $today = date("Y-m-d");  
                $today_time = date("H:i:s");  

                $x = DB::insert("insert into wastage(date, time, qty, lot, waste_category_id, Lab_has_materials_lmid, amount, recdate, user_att, added_by, reason) values('" . Input::get('date') . "','" . $today_time . "','" . Input::get('qty') . "','" . Input::get('lotno') . "','" . Input::get('wtype') . "','" . Input::get('mat') . "','0','" . $today . "','" . Input::get('user') . "','" . $luid . "','" . Input::get('desc') . "')");


                //stock Manage
                WasteController::deductStock(Input::get('mat'), Input::get('qty'));


                if ($x == 1) {
                    return View::make('WiwastageManagement')->with('msg', "Wastage added");
                } else {
                    return View::make('WiwastageManagement')->with('msg', "Error in adding Wastage");
                }
            } 

            else if ($submit == "DeteleItem") {


                $results = DB::select("select Lab_has_materials_lmid,qty from wastage where id = '" . Input::get('id') . "'");

                $lmid = "";
                $qty = "";
                foreach ($results as $ress) {
                    $lmid = $ress->Lab_has_materials_lmid;
                    $qty = $ress->qty;
                }


                //stock Manage
                WasteController::addStock($lmid, $qty);

                $x = DB::delete("delete from wastage where id='" . Input::get('id') . "'");


                echo $x;
                
            } 

        } else {
            echo "please Login!";
        }
    }

    function searchWastages() {

        $dec = '%';
        if (Input::get("dec") != "") {
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
        <th width='10%' class='viewTHead' scope='col'>Date</th>
        <th width='10%' class='viewTHead' scope='col'>Type</th>
        <th width='20%' class='viewTHead' scope='col'>Reason</th>
        <th width='20%' class='viewTHead' scope='col'>Material</th>
        <th width='10%' class='viewTHead' scope='col'>Qty</th>
        <th width='10%' class='viewTHead' scope='col'>Lot NO</th>
        <th width='10%' class='viewTHead' scope='col'>Related User</th>
        <th width='10%' class='viewTHead' scope='col'>Added By</th>
        </tr>";

        $Result = DB::select("select a.*,b.fname, f.name as cat,d.name as mat from wastage a, user b, Lab_has_materials c, materials d, waste_category f where a.Lab_has_materials_lmid = c.lmid and c.materials_mid = d.mid and (SELECT user_uid from labUser where luid = a.added_by) = b.uid and a.waste_category_id=f.id and a.date between '" . Input::get("do") . "' and '" . Input::get("dt") . "' and c.Lab_lid = '" . $_SESSION['lid'] . "' and a.reason like '" . $dec . "' and waste_category_id like '".Input::get("sup")."'");
        foreach ($Result as $res) {
            $out .= "<tr><td>" . $res->date . "</td> <td>" . $res->cat . "</td> <td>" . $res->reason . "</td><td>" . $res->mat . "</td> <td>" . $res->qty . "</td> <td>" . $res->lot . "</td> <td>" . $res->user_att . "</td> <td>" . $res->fname . "</td>"
            . "<td><input id=" . $res->id . " type='button' class='btn' style='margin:0px;' value='Delete' onClick='gettgID(id)'></td>";

            $totAmount += $res->qty;
        }

        $totAmount = number_format($totAmount, 2);

        return $out . "/#/#/#" . $totAmount;
    }

    function deductStock($lmid, $qty) {
        //get oldest stock available from each material
        $results = DB::select("select idstock,qty,usedqty from stock where Lab_has_materials_lmid = '" . $lmid . "' and (qty-usedqty) > 0 order by expDate ASC");

        $deducting_qty = $qty;

        foreach ($results as $ress) {
            // deduct stock from orderd materials for most resent exp. 
            if ($ress->qty >= $deducting_qty) {
                //stock update for each test
                if ($ress->usedqty != null) {
                    DB::statement("update stock set usedqty=usedqty+'" . $deducting_qty . "' where idstock='" . $ress->idstock . "'");
                } else {
                    DB::statement("update stock set usedqty='" . $deducting_qty . "' where idstock='" . $ress->idstock . "'");
                }
                break;
            } else {
                //stock update for each test
                if ($ress->usedqty != null) {
                    DB::statement("update stock set usedqty=usedqty+'" . $ress->qty . "' where idstock='" . $ress->idstock . "'");
                } else {
                    DB::statement("update stock set usedqty='" . $ress->qty . "' where idstock='" . $ress->idstock . "'");
                }
                $deducting_qty = $deducting_qty - $ress->qty;
            }
        }
    }

    function addStock($lmid, $qty) {
        //get oldest stock available from each material
        $results = DB::select("select idstock,qty,usedqty from stock where Lab_has_materials_lmid = '" . $lmid . "' and (qty-usedqty) > 0 order by expDate ASC");

        $deducting_qty = $qty;

        foreach ($results as $ress) {
            // deduct stock from orderd materials for most resent exp. 
            if ($ress->qty >= $deducting_qty) {
                //stock update for each test
                if ($ress->usedqty != null) {
                    DB::statement("update stock set usedqty=usedqty-'" . $deducting_qty . "' where idstock='" . $ress->idstock . "'");
                } else {
                    DB::statement("update stock set usedqty='" . $deducting_qty . "' where idstock='" . $ress->idstock . "'");
                }
                break;
            } else {
                //stock update for each test
                if ($ress->usedqty != null) {
                    DB::statement("update stock set usedqty=usedqty-'" . $ress->qty . "' where idstock='" . $ress->idstock . "'");
                } else {
                    DB::statement("update stock set usedqty='" . $ress->qty . "' where idstock='" . $ress->idstock . "'");
                }
                $deducting_qty = $deducting_qty - $ress->qty;
            }
        }
    }



}

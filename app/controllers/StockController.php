<?php

if (!isset($_SESSION)) {
    session_start();
}

class StockController extends Controller {

    function submit() {
        $lid = $_SESSION['lid'];
        $luid = $_SESSION['luid'];
        $lmid = Input::get('lmid');

        if (isset($luid)) {
            $submit = Input::get('submit');

            if ($submit == "Add new Stock") {

                $qty = Input::get('qty');
                $unit = Input::get('unit');
                $exp = Input::get('exp');

                $dqty = $qty;
                //Check Unit and matching units

                $unitResult = DB::select("select unit from stock where Lab_has_materials_lmid = '" . $lmid . "'");
                foreach ($unitResult as $ures) {
                    $stockUnit = $ures->unit;
                }
                //errrorrr=======================================
//                $powerDef = matchUnits($unit, $stockUnit);
//                $powerDef = -$powerDef;
//                if ($powerDef != 0) {
//                    $xx = pow(10, $powerDef);
//                    $dqty = $dqty * $xx;
//                }

                $dqty = $qty;

                $x = $this->addStock($lmid, $dqty, $unit, $exp);

                if ($x == 1) {
                    return View::make('Wistock')->with('msg', "Stock added");
                } else {
                    return View::make('Wistock')->with('msg', "Error in adding Stock");
                }
            } else if ($submit == "Update Table") {
                $newPOST = $_POST;              
                unset($newPOST['submit']);
                unset($newPOST['mat']);
                unset($newPOST['qty']);
                unset($newPOST['exp']);
                unset($newPOST['lmid']);
                
                $result1 = DB::select("select * from Lab_has_materials a,materials b, stock c where c.Lab_has_materials_lmid = a.lmid and  a.materials_mid=b.mid and a.lab_lid='" . $lid . "' and a.status='1' order by c.idstock DESC");
                foreach ($result1 as $res1) {
                    $id = $res1->idstock;
                    $key = "qty+" . $id;
                    if (array_key_exists($key, $newPOST)) {
                        $qty = $newPOST["qty+" . $id]; 
                        $unit = $newPOST["unit+" . $id];
                        $exp = $newPOST["exp+" . $id];
                        $stid = $newPOST["stid+" . $id];
                        
                        echo $stid." ".$qty." ".$unit." ".$exp;

                        $this->updateStockTable($stid, $qty, $unit, $exp);
                    }
                }

                return View::make('Wistock')->with('msg', "Stock Table Updated!");
            } else if ($submit == "Delete") {
                $lmid = Input::get('lmidh');
                $x = $this->deleteStock($lmid);
                if ($x == 1) {
                    return View::make('Wistock')->with('msg', "  Stock Deleted!");
                } else {
                    return View::make('Wistock')->with('msg', "Error in Delete Stock!");
                }
            } else if ($submit == "getUnits") {
                $lmid = Input::get("lmid");

                $resultS123 = "<option val='0'></option>";
//              
                $results1 = DB::select("select symble,msid from measurements");
                foreach ($results1 as $res1sym) {
                    $symbole = $res1sym->symble;
                    $resultS123 .= "<option value='" . $res1sym->msid . "'>" . $symbole . "</option>";
                }


                return $resultS123;
            }
        } else {
            echo "please Login!";
        }
    }

//error function---------------------------------------------
    function matchUnits($unit, $stockUnit) {
        
    }

//--------------------------------------------------
//---------add stock function-----------------------
    function addStock($lmid, $dqty, $stockUnit, $exp) {
        $x = DB::insert("insert into stock(Lab_has_materials_lmid,qty,unit,expDate,usedqty) values('" . $lmid . "','" . $dqty . "','" . $stockUnit . "','" . $exp . "','0')");
        return $x;
    }

//--------------------------------------------------
//---------update stock function-----------------------
    function updateStockTable($stid, $qty, $unit, $exp) {
        DB::update("update stock set qty='" . $qty . "', unit='" . $unit . "', expDate='" . $exp . "' where idstock='" . $stid . "'");
    }

//---------delete stock function-----------------------
    function deleteStock($lmid) {
        $x = DB::delete("delete from stock where idstock='" . $lmid . "'");
        return $lmid;
    }

}

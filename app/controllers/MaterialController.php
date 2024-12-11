<?php

if (!isset($_SESSION)) {
    session_start();
}

class MaterialController extends Controller {

    function submit() {
        $lid = $_SESSION['lid'];
        $luid = $_SESSION['luid'];
        $lmid = Input::get('lmid');

        if (isset($luid)) {

            if (Input::get('submit') == "Add Material") {

                $matName = Input::get("matName");
                $mcid = Input::get("matCat");
                $matType = Input::get("mType");
                $curretnQty = Input::get("cstock");
                $unit = Input::get("units");
                $exp = Input::get("exp");
                $rol = Input::get("rol");
                $deductType = Input::get("ded");

                $x = $this->addMaterial($matName, $matType, $curretnQty, $unit, $exp, $rol, $mcid, $lid, $deductType);
                return View::make('WimaterialManagement')->with('msg', $x);
            } else if (Input::get('submit') == "Update Material") {

                $matName = Input::get("matName");
                $mcid = Input::get("matCat");
                $matType = Input::get("mType");
                $curretnQty = Input::get("cstock");
                $unit = Input::get("units");
                $exp = Input::get("exp");
                $rol = Input::get("rol");
                $deductType = Input::get("ded");


                $x = $this->addMaterial($matName, $matType, $curretnQty, $unit, $exp, $rol, $mcid, $lid, $deductType);
                return View::make('WimaterialManagement')->with('msg', $x);
            } else if (Input::get('submit') == ("Delete")) {
                $x = $this->DeleteMaterials($lmid);
                return View::make('WimaterialManagement')->with('msg', 'Material Deleted!');
            } else if (Input::get('submit') == "selectMat") {
                $id = Input::get("id");
                $result = DB::select("select * from materials a, mat_category b,Lab_has_materials c,stock d where c.lmid = d.Lab_has_materials_lmid and a.mid = c.materials_mid and b.mcid = c.mat_category_mcid and c.Lab_lid = '" . $_SESSION['lid'] . "' and c.lmid='" . $id . "'");
                return json_encode($result);
            }             
            else if (Input::get('submit') == "Add Material Category") { 

                $matCatName = Input::get("matcat");
                $x = $this->addMaterialCategory($matCatName);

                if ($x == 1) {
                    return View::make('WimaterialManagement')->with('msg', 'Material Category Added!');
                    ;
                } else {
                    return View::make('WimaterialManagement')->with('msg', 'Already saved Material Category');
                }
            } else if (Input::get('submit') == "Delete Catgegoty") {

                $mcid = Input::get("mcid");
                $x = $this->deleteCategory($mcid);
                if ($x == 1) {
                    return View::make('WimaterialManagement');
                } else {
                    return View::make('WimaterialManagement')->with('msg', 'Error Deleting');
                }
            } else if (Input::get("submit") == "getUnits") {
                $result = "";
                $type = Input::get("type");

                if ($type == "Other") {
                    $results = DB::select("select symble,msid from measurements");
                    foreach ($results as $res) {
                        $result .= "<option value='" . $res->msid . "'>" . $res->symble . "</option>";
                    }
                } else {
                    $results = DB::select("select symble,msid from measurements where msid in ( select measurements_msid from mattype_has_measurements where mattype_idmattype = '" . $type . "')");
                    foreach ($results as $res) {
                        $result .= "<option value='" . $res->msid . "'>" . $res->symble . "</option>";
                    }
                }
                return $result;
            }
        } else {
            return View::make('WimaterialManagement')->with('msg', 'Please Login!');
        }
    }

    function getMatTests() {
        $id = Input::get("id");
        $resultx = "";

        $result = DB::select("SELECT b.tid,b.name,a.qty,a.unit FROM test_Labmeterials a, test b where a.test_tid = b.tid and a.Lab_has_materials_lmid = '".$id."';");
        foreach ($result as $results) {
            $resultx .= "<tr><td>".$results->name."</td><td align='center'>".$results->qty."</td><td  align='center'>".$results->unit."</td></tr>";
        }

        return $resultx;
    }

//--------------------------------------functions------------------------------------------------------------------------
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~addmaterial function~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function addMaterial($matName, $mattID, $curretnQty, $unit, $exp, $rol, $mcid, $lid, $dedtype) {

        $msg = "";
        $result123 = DB::select("SELECT mid FROM materials where name = '" . $matName . "'");
        foreach ($result123 as $res) {
            $mid = $res->mid;
            $msg .= "Material Name Exists";
        }

        if (!isset($mid)) {
            $mid = DB::table('materials')->insertGetId(
                ['name' => $matName]
            );
        }


        $result = DB::select("SELECT lmid FROM Lab_has_materials where materials_mid = '" . $mid . "' and Lab_lid = '" . $lid . "'");
        foreach ($result as $res) {
            $lmid = $res->lmid;

            DB::statement("update Lab_has_materials set mattype_idmattype='" . $mattID . "',mat_category_mcid='" . $mcid . "',unit='" . $unit . "', rol='" . $rol . "', dedtype='" . $dedtype . "', status = '1' where lmid = '" . $lmid . "'");


            $msg .= " and exists in the Lab.";
        }

        if (!isset($lmid)) {
            $lmid = DB::table('Lab_has_materials')->insertGetId(
                ['Lab_lid' => $lid, 'materials_mid' => $mid, 'mattype_idmattype' => $mattID, 'mat_category_mcid' => $mcid, 'unit' => $unit, 'rol' => $rol, 'dedtype' => $dedtype, 'status' => '1']
            );
        } else {
            DB::statement("delete from stock where Lab_has_materials_lmid='" . $lmid . "'");
        }

        DB::insert("insert into stock(qty,unit,expDate,Lab_has_materials_lmid,usedqty) values('" . $curretnQty . "','" . $unit . "','" . $exp . "','" . $lmid . "','0')");
        $msg .= " Material Added!"; 

        return $msg;
    }

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~DElete Material Function~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function DeleteMaterials($lmid) {
        $x = DB::statement("update Lab_has_materials set status = '0' where Lab_lid='" . $_SESSION['lid'] . "' and lmid = '" . $lmid . "'");
    }

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~addMaterialCategoty~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function addMaterialCategory($matCatName) {

        $x = 0;
        $result = DB::select("select category from mat_category where category='" . $matCatName . "'");

        if (isset($result)) {
            $x = DB::insert("insert into mat_category(category) values('" . $matCatName . "')");
        }

        return $x;
    }

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~deleteCategory~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function deleteCategory($mcid) {

        $x = DB::delete("delete from mat_category where mcid = '" . $mcid . "'");

        return $x;
    }

}

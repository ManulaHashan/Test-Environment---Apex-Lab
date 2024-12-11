<?php

if (!isset($_SESSION)) {
    session_start();
}
date_default_timezone_set('Asia/Colombo');

class TestController extends Controller {

    function manageTest() {
        if ($_SESSION['luid'] != null) {

//            echo print_r($_REQUEST);


            if (Input::get('submit') != null) {
                if (Input::get('submit') === "Add Testing") {

//                    echo "OKOKKOK";

                    $lid = $_SESSION['lid'];

                    $testName = Input::get("testname");
                    $tgid = Input::get("testGroup");
                    $tPrice = Input::get("testprice");
                    $measurement = Input::get("measurement");

                    $vtype = Input::get("vtype");
                    $vchcount = Input::get("vchcount");
                    $vmin = Input::get("vmin");
                    $vmax = Input::get("vmax");
                    $vdecis = Input::get("vdecis");
                    $validateCode = $vtype . "#" . $vchcount . "#" . $vmin . "#" . $vmax . "#" . $vdecis;

                    $repname = Input::get("repname");
                    $analyzer = Input::get("analyzer");
                    $tcat = Input::get("tcat");
                    $tinput = Input::get("tinput");
                    $order = Input::get("order");

                    $refMin = Input::get("refmin");
                    $refMax = Input::get("refmax");

                    $defaultVal = Input::get("defaultval");
                    $LISID = Input::get("lisis");

                    if ($analyzer != "0") {
                        $analyzerCol = ',analyzers_anid';
                        $analyzerVal = ",'" . $analyzer . "'";
                    } else {
                        $analyzerCol = '';
                        $analyzerVal = '';
                    }

                    if ($tcat != "0") {
                        $tcatCol = ',testingcategory_tcid';
                        $tcatVal = ",'" . $tcat . "'";
                    } else {
                        $tcatCol = '';
                        $tcatVal = '';
                    }

                    if ($tinput != "0") {
                        $tinputCol = ',testinginput_tiid';
                        $tinputVal = ",'" . $tinput . "'";
                    } else {
                        $tinputCol = '';
                        $tinputVal = '';
                    }

                    $extraCols = $analyzerCol . "" . $tcatCol . "" . $tinputCol;
                    $extraVals = $analyzerVal . "" . $tcatVal . "" . $tinputVal;

                    if (Input::get("viewnor") != null) {
                        $viewNormalVals = '1';
                    } else {
                        $viewNormalVals = '0';
                    }

                    if (Input::get("viewana") != null) {
                        $viewANA = '1';
                    } else {
                        $viewANA = '0';
                    }

                    if (Input::get("selvals") != null) {
                        $selVal = '1';
                    } else {
                        $selVal = '0';
                    }
                    
                    if (Input::get("awr") != null) {
                        $awr = '1';
                    } else {
                        $awr = '0';
                    }

                    $Result = DB::select("select name,tid from test where name='" . $testName . "'");
                    foreach ($Result as $res) {
                        $exsistingTest = $res;
                    }

//                    echo "select name,tid from test where name='" . $testName . "'";



                    if (isset($exsistingTest)) {
                        $xx = $exsistingTest->tid;
                        $Result = DB::select("select * from Lab_has_test where lab_lid='" . $lid . "' and test_tid='" . $xx . "' ");
                        if (!empty($Result)) {
                            $x = 0;
                        } else {
                            DB::statement("insert into Lab_has_test(lab_lid,test_tid,measurement,price,Testgroup_tgid,status,reportname,viewnorvals,viewanalyzer,orderno,selactablevals" . $extraCols . ",advance_ref) "
                                    . "values('" . $lid . "','" . $xx . "','" . $measurement . "','" . $tPrice . "','" . $tgid . "','" . $validateCode . "','" . $repname . "','" . $viewNormalVals . "','" . $viewANA . "','" . $order . "','" . $selVal . "'" . $extraVals . ",'" . $awr . "')");
                        }
                    } else {
                        $xx = DB::table('test')->insertGetId(['name' => $testName, 'status' => '1']);
                        DB::statement("insert into Lab_has_test(lab_lid,test_tid,measurement,price,Testgroup_tgid,status,reportname,viewnorvals,viewanalyzer,orderno,selactablevals" . $extraCols . ",advance_ref) "
                                . "values('" . $lid . "','" . $xx . "','" . $measurement . "','" . $tPrice . "','" . $tgid . "','" . $validateCode . "','" . $repname . "','" . $viewNormalVals . "','" . $viewANA . "','" . $order . "','" . $selVal . "'" . $extraVals . ",'" . $awr . "')");
                    }

                    //set default val and LIS ID
                    DB::statement("insert into labtestingdetails(test_tid, Lab_lid, defaultval, listestid,refference_min,refference_max) values('" . $xx . "','" . $lid . "','" . $defaultVal . "','" . $LISID . "','" . $refMin . "','" . $refMax . "')");


                    $tgroup = $tgid;

                    if ($xx != 0) {

                        $log_descreption = "Parameter Added : TGID ".$tgid." Param ".$testName;

                        SystemLogs::saveChangeLog($_SESSION['lid'], $_SESSION['luid'], "ManageTestMaterial", "Add Testing", $log_descreption);

                        return View::make('Witestmanage')->with('msg', 'Testing Added!')->with('tgroup', $tgroup);
                    } else {
                        return View::make('Witestmanage')->with('msg', 'Added Error!')->with('tgroup', $tgroup);
                    }
                }if (Input::get('submit') === "Update Testing") {
                    $lid = $_SESSION['lid'];

                    $tid = Input::get("tid");
                    $testName = Input::get("testname");
                    $tgid = Input::get("tgid");
                    $tPrice = Input::get("testprice");
                    $measurement = Input::get("measurement");

                    $vtype = Input::get("vtype");
                    $vchcount = Input::get("vchcount");
                    $vmin = Input::get("vmin");
                    $vmax = Input::get("vmax");

                    $refMin = Input::get("refmin");
                    $refMax = Input::get("refmax");

                    $vdecis = Input::get("vdecis");
                    $validateCode = $vtype . "#" . $vchcount . "#" . $vmin . "#" . $vmax . "#" . $vdecis;

                    $repname = Input::get("repname");
                    $analyzer = Input::get("analyzer");
                    $tcat = Input::get("tcat");
                    $tinput = Input::get("tinput");
                    $order = Input::get("order");

                    $defaultVal = Input::get("defaultval");
                    $LISID = Input::get("lisis");

                    if ($analyzer == "0" | $analyzer == "") {
                        $analyzer = "analyzers_anid=NULL";
                    } else {
                        $analyzer = "analyzers_anid='" . $analyzer . "'";
                    }

                    if ($tcat == "0" | $tcat == "") {
                        $tcat = "testingcategory_tcid=NULL";
                    } else {
                        $tcat = "testingcategory_tcid='" . $tcat . "'";
                    }

                    if ($tinput == "0" | $tinput == "") {
                        $tinput = "testinginput_tiid=NULL";
                    } else {
                        $tinput = "testinginput_tiid='" . $tinput . "'";
                    }


                    if (Input::get("viewnor") != null) {
                        $viewNormalVals = '1';
                    } else {
                        $viewNormalVals = '0';
                    }

                    if (Input::get("viewana") != null) {
                        $viewANA = '1';
                    } else {
                        $viewANA = '0';
                    }

                    if (Input::get("selvals") != null) {
                        $selVal = '1';
                    } else {
                        $selVal = '0';
                    }
                    
                    if (Input::get("awr") != null) {
                        $awr = '1';
                    } else {
                        $awr = '0'; 
                    }

                    DB::statement("update Lab_has_test set measurement = '" . $measurement . "',price = '" . $tPrice . "',Testgroup_tgid = '" . $tgid . "',status = '" . $validateCode . "',reportname = '" . $repname . "',viewnorvals = '" . $viewNormalVals . "',orderno='" . $order . "',selactablevals='" . $selVal . "',advance_ref='" . $awr . "',viewanalyzer = '" . $viewANA . "'," . $analyzer . "," . $tcat . " ," . $tinput . " where test_tid='" . $tid . "' and Lab_lid='" . $_SESSION['lid'] . "'");

                    //set default val and LIS ID
                    $Resultx = DB::select("select * from labtestingdetails where lab_lid = '" . $lid . "'  and test_tid = '" . $tid . "'");

                    if (empty($Resultx)) {
                        DB::statement("insert into labtestingdetails(test_tid, Lab_lid, defaultval, listestid) values('" . $tid . "','" . $lid . "','" . $defaultVal . "','" . $LISID . "')");
                    } else {
                        DB::statement("update labtestingdetails set defaultval='" . $defaultVal . "', listestid ='" . $LISID . "',refference_min='" . $refMin . "',refference_max='" . $refMax . "' where test_tid = '" . $tid . "' and lab_lid = '" . $lid . "'");
                    }

//                    return View::make('Witestmanage')->with('msg', $ok);
                    $tgroup = $tgid;
                    
                    $log_descreption = "Parameter Updated : TGID ".$tgid." Param ".$testName;

                    SystemLogs::saveChangeLog($_SESSION['lid'], $_SESSION['luid'], "ManageTestMaterial", "Update Testing", $log_descreption);
                    
                    return View::make('Witestmanage')->with('msg', 'Testing Updated!')->with('tgroup', $tgroup);
                    
                } else if (Input::get('submit') === "Delete") {

                    $lhtID = Input::get("lhtid");
                    DB::statement("delete from Lab_has_test where lhtid = '" . $lhtID . "'");

                    $log_descreption = "Parameter Deleted : LHT_ID ".$lhtID;

                    SystemLogs::saveChangeLog($_SESSION['lid'], $_SESSION['luid'], "ManageTestMaterial", "Delete", $log_descreption);

                    echo "1";
                } else if (Input::get('submit') === "Update Table") {

                    $newPost = $_POST;
                    unset($newPost['lhtid']);
                    unset($newPost['submit']);
                    unset($newPost['testname']);
                    unset($newPost['repname']);
                    unset($newPost['measurement']);
                    unset($newPost['testprice']);
                    unset($newPost['testGroup']);
                    unset($newPost['tgid']);
                    unset($newPost['vtype']);
                    unset($newPost['vchcount']);
                    unset($newPost['vmin']);
                    unset($newPost['vmax']);
                    unset($newPost['vdecis']);
                    unset($newPost['analyzer']);
                    unset($newPost['tcat']);
                    unset($newPost['tinput']);
                    unset($newPost['mat']);
                    unset($newPost['val']);
                    unset($newPost['units']);
                    unset($newPost['matid']);
                    unset($newPost['tid']);

                    $result = DB::select("select test_tid as tid from Lab_has_test where Lab_lid = '" . $_SESSION['lid'] . "'");
                    foreach ($result as $res) {
                        $testID = $res->tid;
                        $key = "name+" . $testID;
                        if (array_key_exists($key, $newPost)) {
                            $testName = $newPost["name+" . $testID];
                            $testMes = $newPost["mes+" . $testID];
                            $testPrice = $newPost["price+" . $testID];

                            DB::statement("update Lab_has_test set measurement='" . $testMes . "', price = '" . $testPrice . "' where test_tid = '" . $testID . "' and lab_lid = '" . $_SESSION['lid'] . "'");
                        }
                    }

                    $log_descreption = "Update Parameter List";

                    SystemLogs::saveChangeLog($_SESSION['lid'], $_SESSION['luid'], "ManageTestMaterial", "Update Table", $log_descreption);

                    return View::make('Witestmanage')->with('msg', 'Table Updated!');
                } else if (Input::get('submit') == "viewMat") {
                    $tid = Input::get("tid");
                    $mid = "";
                    $Out = "<tr><th>Material Name</th><th>Value</th><th>Unit</th></tr>";

                    $result = DB::select("select a.Lab_has_materials_lmid,a.qty,b.symble as unit from test_Labmeterials a,measurements b where a.unit = b.msid and test_tid ='" . $tid . "'");
                    foreach ($result as $res) {
                        $lmid = $res->Lab_has_materials_lmid;
                        $qty = $res->qty;
                        $unit = $res->unit;

                        $result2 = DB::select("select * from materials where mid in (select materials_mid from Lab_has_materials where lab_lid = '" . $_SESSION['lid'] . "' and lmid = '" . $lmid . "')");
                        foreach ($result2 as $res2) {
                            $mid = $res2->mid;
                            $Out .= "<tr><td>" . $res2->name . "</td>"
                                    . "<td>" . $qty . "</td>"
                                    . "<td>" . $unit . "</td>";

                            $result3 = DB::select("select lmid from Lab_has_materials where lab_lid = '" . $_SESSION['lid'] . "' and materials_mid = '" . $mid . "'");
                            foreach ($result3 as $res3) {
                                $lmid2 = $res3->lmid;
                                $Out .= "<td><input type='button' class='btn' style='margin:0px;' id='" . $lmid2 . "' value = 'Delete Material' onclick='deleteMat(id)'></td>";
                            }
                            $Out .= "</tr>";
                        }
                    }
                    echo $Out;
                } else if (Input::get('submit') === "addMaterial") {
                    $matID = Input::get("matid");
                    $tid = Input::get("tid");
                    $val = Input::get("val");
                    $unit = Input::get("units");

                    $result = DB::select("select lmid from Lab_has_materials where lab_lid = '" . $_SESSION['lid'] . "' and materials_mid = '" . $matID . "'");
                    foreach ($result as $res) {
                        $lmid = $res->lmid;
                        DB::statement("insert into test_Labmeterials(Lab_has_materials_lmid,test_tid,qty,unit) values('" . $lmid . "','" . $tid . "','" . $val . "','" . $unit . "')");
                    }

                    $log_descreption = "Material Added To Test : MatID".$matID." Test_ID ".$tid;

                    SystemLogs::saveChangeLog($_SESSION['lid'], $_SESSION['luid'], "ManageTestMaterial", "Add to Testing Consumption", $log_descreption);

                    echo '1';
                } else if (Input::get('submit') === "deleteMat") {
                    $lmid = Input::get("lmid");
                    $tid = Input::get("tid");

                    $x = DB::statement("delete from test_Labmeterials where Lab_has_materials_lmid='" . $lmid . "' and test_tid='" . $tid . "'");

                    if ($x == 1) {

                        $log_descreption = "Material Removed from Test : LMID".$lmid." Test_ID ".$tid;

                        SystemLogs::saveChangeLog($_SESSION['lid'], $_SESSION['luid'], "ManageTestMaterial", "Remove", $log_descreption);

                        echo "Deleted!";
                    } else {
                        echo "Error in Delete Material from Testing!";
                    }
                } else if (Input::get('submit') === "viewTesting") {

                    $result = DB::select("select *,a.status as pattern,c.defaultval,c.refference_min,c.refference_max, c.listestid from Lab_has_test a, test b, labtestingdetails c where a.lab_lid = c.lab_lid and c.test_tid = b.tid and a.test_tid=b.tid and b.tid = '" . Input::get('tid') . "' and a.Lab_lid = '" . $_SESSION['lid'] . "'");
                    if (empty($result)) {
                        $result = DB::select("select *,a.status as pattern from Lab_has_test a, test b where a.test_tid=b.tid and tid = '" . Input::get('tid') . "' and Lab_lid = '" . $_SESSION['lid'] . "'");
                    }

                    return json_encode($result);
                } else if (Input::get('submit') === "Search") {
                    $tgroup = Input::get('tgroup');
                    return View::make('Witestmanage')->with('tgroup', $tgroup);
                }
            } else {
                return View::make('Witestmanage')->with('msg', 'Try Again!');
            }
        } else {
            return View::make('Witestmanage')->with('msg', 'Please Login!');
        }
    }

//Functions~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    function addReferance() {

        $result = DB::select("select a.lhtid from Lab_has_test a, test b where a.test_tid=b.tid and b.tid = '" . Input::get('tid') . "' and a.Lab_lid = '" . $_SESSION['lid'] . "'");
        foreach ($result as $res) {
            $lhtid = $res->lhtid;
            DB::statement("insert into reference_values(min, max, age_range_id, gender_idgender, Lab_has_test_lhtid) "
                    . "values('" . Input::get('min') . "','" . Input::get('max') . "','" . Input::get('age') . "','" . Input::get('gen') . "','" . $lhtid . "')");
        }

        $log_descreption = "Add Reference Range : LHT_ID".$lhtid;

        SystemLogs::saveChangeLog($_SESSION['lid'], $_SESSION['luid'], "ManageTestMaterial", "Add Reference", $log_descreption);

        return "Reference Added!";
    }

    function loadReferances() {

        $out = "<tr> <td>Age Range</td><td>Gender</td><td>Min. Value</td><td>Max. Value</td>  </tr>";

        $result = DB::select("select a.lhtid from Lab_has_test a, test b where a.test_tid=b.tid and b.tid = '" . Input::get('tid') . "' and a.Lab_lid = '" . $_SESSION['lid'] . "'");
        foreach ($result as $res) {
            $lhtid = $res->lhtid;
        }

        $result = DB::select("select a.*,b.min as refmin,b.max as refmax,c.gender from reference_values a, age_range b, gender c where a.age_range_id= b.id and a.gender_idgender=c.idgender and a.Lab_has_test_lhtid = '" . $lhtid . "' order by a.age_range_id,c.idgender");
        foreach ($result as $res) {

            $agValue = 0;
            if ($res->refmin / 365 >= 1) {
                $agMinValue = number_format($res->refmin / 365);
                $agMaxValue = number_format($res->refmax / 365);
                $agUnit = "Years";
            } elseif ($res->refmin / 30 >= 1) {
                $agMinValue = number_format($res->refmin / 30);
                $agMaxValue = number_format($res->refmax / 30);
                $agUnit = "Months";
            } else {
                $agMinValue = $res->refmin;
                $agMaxValue = $res->refmax;
                $agUnit = "Days";
            }

            $out .= "<tr> <td>" . $agMinValue . " - " . $agMaxValue . " " . $agUnit . "</td> <td>" . $res->gender . "</td> <td>" . $res->min . "</td> <td>" . $res->max . "</td> </tr>";
        }

        return $out;
    }

}

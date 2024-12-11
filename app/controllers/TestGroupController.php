<?php

if (!isset($_SESSION)) {
    session_start();
}
date_default_timezone_set('Asia/Colombo');

class TestGroupController extends Controller {

    function manageTestGroups() {
        if (Input::get('submit') != null) {
            if (Input::get('submit') == "Add Group") {
                if (Input::get('tgname') != "") {
                    $tgName = Input::get("tgname");
                    $tgPrice = Input::get("tgprice");
                    $tgCost = Input::get("tgcost");
                    $time = Input::get("time");
                    $comment = Input::get("comment");

                    $testcode = Input::get("testcode");
                    $sam_container = Input::get("sam_container");

                    //report configs
                    $rep_heading = "0";
                    if (Input::get("rep_heading") == "on") {
                        $rep_heading = "1";
                    }
                    $custom_configs = "0";
                    if (Input::get("custom_configs") == "on") {
                        $custom_configs = "1";
                    }

                    $name_col = "0";
                    if (Input::get("name_col") == "on") {
                        $name_col = "1";
                    }
                    $value_col = "0";
                    if (Input::get("value_col") == "on") {
                        $value_col = "1";
                    }
                    $unit_col = "0";
                    if (Input::get("unit_col") == "on") {
                        $unit_col = "1";
                    }
                    $flag_col = "0";
                    if (Input::get("flag_col") == "on") {
                        $flag_col = "1";
                    }
                    $ref_col = "0";
                    if (Input::get("ref_col") == "on") {
                        $ref_col = "1";
                    }

                    $name_col_head = Input::get("name_col_head");
                    $value_col_head = Input::get("value_col_head");
                    $unit_col_head = Input::get("unit_col_head");
                    $flag_col_head = Input::get("flag_col_head");
                    $ref_col_head = Input::get("ref_col_head");

                    $name_col_width = Input::get("name_col_width");
                    $value_col_width = Input::get("value_col_width");
                    $unit_col_width = Input::get("unit_col_width");
                    $flag_col_width = Input::get("flag_col_width");
                    $ref_col_width = Input::get("ref_col_width");

                    $name_col_align = Input::get("name_col_align");
                    $result_col_align = Input::get("result_col_align");
                    $unit_col_align = Input::get("unit_col_align");
                    $flag_col_align = Input::get("flag_col_align");
                    $ref_col_align = Input::get("ref_col_align");

                    $age_ref = "0";
                    if (Input::get("age_ref") == "on") {
                        $age_ref = "1";
                    }

                    //
                    $max_tgid = 0;
                    $ResultTmp = DB::select("select MAX(tgid) as tgid from Testgroup");
                    foreach ($ResultTmp as $restmp) {
                        $max_tgid = $restmp->tgid;
                    }

                    $max_tgid = $max_tgid + 1;

                    $Result = DB::select("select * from Testgroup where name = '" . $tgName . "' and price='" . $tgPrice . "' and Lab_lid = '" . $_SESSION['lid'] . "'");
                    foreach ($Result as $res) {
                        $tgroup = $res;
                    }
                    if (isset($tgroup)) {
                        return View::make('Witestgroups')->with('msg', 'Test Group Exsist!');
                    } else {
                        DB::statement("insert into Testgroup(tgid,name,price,Lab_lid,testingtime,comment,cost, rep_heading, name_col, value_col, unit_col, flag_col, ref_col, name_col_head, value_col_head, unit_col_head, flag_col_head, ref_col_head, name_col_width, value_col_width, unit_col_width, flag_col_width, ref_col_width, custom_configs, name_col_align, result_col_align, unit_col_align, flag_col_align, ref_col_align,age_ref, testCode, sample_containers_scid) "
                            . "values('" . $max_tgid . "','" . $tgName . "','" . $tgPrice . "','" . $_SESSION['lid'] . "','" . $time . "','" . $comment . "','" . $tgCost . "',"
                            . "'" . $rep_heading . "','" . $name_col . "','" . $value_col . "','" . $unit_col . "',"
                            . "'" . $flag_col . "','" . $ref_col . "','" . $name_col_head . "','" . $value_col_head . "',"
                            . "'" . $unit_col_head . "','" . $flag_col_head . "','" . $ref_col_head . "',"
                            . "'" . $name_col_width . "','" . $value_col_width . "','" . $unit_col_width . "',"
                            . "'" . $flag_col_width . "','" . $ref_col_width . "','" . $custom_configs . "',"
                            . "'" . $name_col_align . "','" . $result_col_align . "','" . $unit_col_align . "',"
                            . "'" . $flag_col_align . "','" . $ref_col_align . "','" . $age_ref . "','" . $testcode . "','" . $sam_container . "')");

                        $log_descreption = "Test Added : ".$tgName." : Price : ".$tgPrice;

                        SystemLogs::saveChangeLog($_SESSION['lid'], $_SESSION['luid'], "TestGroupManage", "Add Group", $log_descreption);

                        return View::make('Witestgroups')->with('msg', 'Test Group Added!');
                    }
                } else {
                    return View::make('Witestgroups')->with('msg', 'Enter correct values!');
                }
            } else if (Input::get('submit') == "Update Table") {

                $result = DB::select("select tgid from Testgroup where Lab_lid = '" . $_SESSION['lid'] . "' order by name");
                foreach ($result as $res) {
                    $groupID = $res->tgid;
                    $nameKey = "name+" . $groupID;
                    $priceKey = "price+" . $groupID;
                    $timeKey = "time+" . $groupID;
                    $costKey = "cost+" . $groupID;
                    $bCodeKey = "testCode+" . $groupID;
                    
                    $newPost = $_POST; 
                    
                    

                    
                    unset($newPost['submit']);
                    unset($newPost['branch']);                    
                    unset($newPost['tgid']);

                    unset($newPost['sam_container']);
                    unset($newPost['testcode']);

//                    echo "<br/>".$newPost[$nameKey]; 
                    

                    if (array_key_exists($nameKey, $newPost)) {



                        if (isset($_POST['branch'])) {
                            if ($_POST['branch'] == "all") {
//                                DB::statement("update Testgroup set name='" . $newPost[$nameKey] . "', price = '" . $newPost[$priceKey] . "', cost = '" . $newPost[$costKey] . "', testingtime = '" . $newPost[$timeKey] . "' where tgid = '" . $groupID . "' and lab_lid = '" . $_SESSION['lid'] . "'");
                                DB::statement("update Testgroup set name='" . $newPost[$nameKey] . "', price = '" . $newPost[$priceKey] . "', cost = '" . $newPost[$costKey] . "' , testCode = '" . $newPost[$bCodeKey] . "' where tgid = '" . $groupID . "' and lab_lid = '" . $_SESSION['lid'] . "'");
                            } else {
                                DB::statement("delete from labbranches_has_Testgroup where bid = '" . $_POST['branch'] . "' and tgid = '" . $groupID . "'");
//                                DB::statement("insert into labbranches_has_Testgroup(bid,tgid,price,cost,testingtime) values('" . $_POST['branch'] . "','" . $groupID . "','" . $newPost[$priceKey] . "','" . $newPost[$costKey] . "','" . $newPost[$timeKey] . "')");
                                DB::statement("insert into labbranches_has_Testgroup(bid,tgid,price,cost) values('" . $_POST['branch'] . "','" . $groupID . "','" . $newPost[$priceKey] . "','" . $newPost[$costKey] . "')");
                            }
                        } else {
//                            DB::statement("update Testgroup set name='" . $newPost[$nameKey] . "', price = '" . $newPost[$priceKey] . "', cost = '" . $newPost[$costKey] . "', testingtime = '" . $newPost[$timeKey] . "' where tgid = '" . $groupID . "' and lab_lid = '" . $_SESSION['lid'] . "'");
                            $x = DB::statement("update Testgroup set name='" . $newPost[$nameKey] . "', price = '" . $newPost[$priceKey] . "', cost = '" . $newPost[$costKey] . "', testCode = '" . $newPost[$bCodeKey] . "' where tgid = '" . $groupID . "' and lab_lid = '" . $_SESSION['lid'] . "'");
                            
//                            echo "<br/>".$newPost[$nameKey]." : ".$x;
                        }
                    }
                }
                
                $log_descreption = "Test List Updated : Center-".$_POST["branch"];

                SystemLogs::saveChangeLog($_SESSION['lid'], $_SESSION['luid'], "TestGroupManage", "Update Table", $log_descreption);
                
                return View::make('Witestgroups')->with('msg', 'Table Updated!');

            } else if (Input::get('submit') == "getTests") {
                $tgID = Input::get("tgid");
                $results = "<p class = 'tableHead'>Testings in this Test group</p><ol>";

                $Result = DB::select("select a.name from test a,Lab_has_test b where a.tid = b.test_tid and b.Testgroup_tgid = '" . $tgID . "' and b.lab_lid = '" . $_SESSION['lid'] . "'");
                foreach ($Result as $res) {
                    $results .= "<li>" . $res->name . "</li>";
                }

                echo $results;
            } else if (Input::get('submit') == "Delete") {
                $tgID = Input::get("tgid");
                DB::statement("delete from Lab_has_test where lab_lid = '" . $_SESSION['lid'] . "' and Testgroup_tgid = '" . $tgID . "'");
                DB::statement("delete from Testgroup where tgid = '" . $tgID . "'");

                $log_descreption = "Test Deleted : TGID ".$tgID;

                SystemLogs::saveChangeLog($_SESSION['lid'], $_SESSION['luid'], "TestGroupManage", "Delete", $log_descreption);

                return View::make('Witestgroups')->with('msg', 'Group Deleted!');
            } else if (Input::get('submit') == "Search") {
                $branchID = Input::get("branch");
                return View::make('Witestgroups')->with('branchID', $branchID);
            }
        } else {
            return View::make('Witestgroups')->with('msg', 'Enter correct values!');
        }
    }

    function getTGComment() {
        $tgID = Input::get("tgid");
        $Result = DB::select("select * from Testgroup where tgid = '" . $tgID . "' and Lab_lid = '" . $_SESSION['lid'] . "'");
        return json_encode($Result);
    }

    function getTGCosts() {
        $tgID = Input::get("tgid");
        $branch = Input::get("brid");

        if($branch == "all"){         
            $branchSearch = "and bid is null";
        }else{
            $branchSearch = "and bid = '".$branch."'";
        }

        $Result = DB::select("select * from test_costs where Testgroup_tgid = '" . $tgID . "' ".$branchSearch." ");
        return json_encode($Result);
    }

    function addTGCosts() {
        $tgID = Input::get("tgid");
        $name = Input::get("costname");
        $cost = Input::get("cost");
        $branch = Input::get("brid");

        if($branch == "all"){         
            $branchSearch = "and bid is null";
        }else{
            $branchSearch = "and bid = '".$branch."'";
        }

        //add cost to cost table
        if($branch == "all"){
            DB::statement("insert into test_costs(name,amount,Testgroup_tgid,date,isactive) values('" . $name . "','" . $cost . "','" . $tgID . "','" . date("Y-m-d") . "','1')");
        }else{
            DB::statement("insert into test_costs(name,amount,Testgroup_tgid,date,isactive,bid) values('" . $name . "','" . $cost . "','" . $tgID . "','" . date("Y-m-d") . "','1','".$branch."')");
        }

        //update cost in testgroup
        $Result = DB::select("select SUM(amount) as amount from test_costs where Testgroup_tgid = '".$tgID."' ".$branchSearch."");
        foreach ($Result as $res) {
            if($branch == "all"){
                DB::statement("UPDATE Testgroup set cost = '".$res->amount."' where tgid = '".$tgID."' and Lab_lid = '".$_SESSION['lid']."'");
            }else{
                DB::statement("UPDATE labbranches_has_Testgroup set cost = '".$res->amount."' where tgid = '".$tgID."' and bid = '".$branch."'");
            }
            
        }

        $log_descreption = "Added Test Cost : TGID ".$tgID." Center-".$branch;

        SystemLogs::saveChangeLog($_SESSION['lid'], $_SESSION['luid'], "TestGroupManage", "Add Cost", $log_descreption);

        return "Cost Added!";
        
    }

    function removeTGCosts() {
        $recid = Input::get("id");
        $branch = Input::get("brid");
        $tgid = Input::get("tgid");

        if($branch == "all"){         
            $branchSearch = "and bid is null";
        }else{
            $branchSearch = "and bid = '".$branch."'";
        }


        DB::statement("DELETE from test_costs where id='".$recid."'");  


        //update cost in testgroup
        $Result = DB::select("select SUM(amount) as amount, Testgroup_tgid from test_costs where Testgroup_tgid = ".$tgid." ".$branchSearch." ");
        foreach ($Result as $res) {
            if($branch == "all"){
                DB::statement("UPDATE Testgroup set cost = '".$res->amount."' where tgid = '".$res->Testgroup_tgid."' and Lab_lid = '".$_SESSION['lid']."'");
            }else{
                DB::statement("UPDATE labbranches_has_Testgroup set cost = '".$res->amount."' where tgid = '".$res->Testgroup_tgid."' and bid = '".$branch."'");
            }
            
        } 

        $log_descreption = "Removed Test Cost : TGID ".$tgid." Center-".$branch;

        SystemLogs::saveChangeLog($_SESSION['lid'], $_SESSION['luid'], "TestGroupManage", "Remove", $log_descreption);


        return "Cost Removed!";
        // return "select SUM(amount) as amount, Testgroup_tgid from test_costs where Testgroup_tgid = (select Testgroup_tgid from test_costs where id = '".$recid."') ".$branchSearch." ";
        
    }

    function toAllTests() {
        $recid = Input::get("recid");
        $branch = Input::get("brid");

        if($branch == "all"){         
            $branchSearch = "and bid is null";
        }else{
            $branchSearch = "and bid = '".$branch."'";
        }

        //get current recode details
        $Result = DB::select("select * from test_costs where id = '".$recid."'");
        foreach ($Result as $res) {
            $name = $res->name;
            $cost = $res->amount;
            $tgID = $res->Testgroup_tgid;
            $bid = $res->bid;
        }

        $Result = DB::select("select tgid from Testgroup where Lab_lid = '" . $_SESSION['lid'] . "' and tgid != '".$tgID."'");
        foreach ($Result as $res) {
            if($bid == null){
                DB::statement("insert into test_costs(name,amount,Testgroup_tgid,date,isactive) values('" . $name . "','" . $cost . "','" . $res->tgid . "','" . date("Y-m-d") . "','1')");
            }else{
                DB::statement("insert into test_costs(name,amount,Testgroup_tgid,date,isactive,bid) values('" . $name . "','" . $cost . "','" . $res->tgid . "','" . date("Y-m-d") . "','1','".$bid."')");
            }

            //update testgroup or labbranch_has_testgroup price
            $Resultx = DB::select("select SUM(amount) as amount, Testgroup_tgid from test_costs where Testgroup_tgid = ".$res->tgid." ".$branchSearch." ");
            foreach ($Resultx as $resx) {
                if($branch == "all"){
                    DB::statement("UPDATE Testgroup set cost = '".$resx->amount."' where tgid = '".$res->tgid."' and Lab_lid = '".$_SESSION['lid']."'");
                }else{
                    DB::statement("UPDATE labbranches_has_Testgroup set cost = '".$resx->amount."' where tgid = '".$res->tgid."' and bid = '".$branch."'");
                }
            
            } 
        }

        return "Cost added to all tests in the selected price list.";
    }

    function toAllCenters() {
        $recid = Input::get("recid");
        $branch = Input::get("brid");

        if($branch == "all"){         
            $branchSearch = "and bid is null";
        }else{
            $branchSearch = "and bid = '".$branch."'";
        }

        //get current recode details
        $Result = DB::select("select * from test_costs where id = '".$recid."'");
        foreach ($Result as $res) {
            $name = $res->name;
            $cost = $res->amount;
            $tgID = $res->Testgroup_tgid;
            $bid = $res->bid;
        }

        $Result = DB::select("select bid from labbranches where Lab_lid = '" . $_SESSION['lid'] . "' and bid != '".$bid."'");
        foreach ($Result as $res) {
            // if($bid == null){
            //     DB::statement("insert into test_costs(name,amount,Testgroup_tgid,date,isactive) values('" . $name . "','" . $cost . "','" . $res->tgid . "','" . date("Y-m-d") . "','1')");
            // }else{
                DB::statement("insert into test_costs(name,amount,Testgroup_tgid,date,isactive,bid) values('" . $name . "','" . $cost . "','" . $tgID . "','" . date("Y-m-d") . "','1','".$res->bid."')");
            // }

            //update testgroup or labbranch_has_testgroup price
            $Resultx = DB::select("select SUM(amount) as amount, Testgroup_tgid from test_costs where Testgroup_tgid = ".$tgID." and bid = '".$res->bid."' ");
            foreach ($Resultx as $resx) {
                // if($branch == "all"){
                //     DB::statement("UPDATE Testgroup set cost = '".$resx->amount."' where tgid = '".$res->tgid."' and Lab_lid = '".$_SESSION['lid']."'");
                // }else{
                    DB::statement("UPDATE labbranches_has_Testgroup set cost = '".$resx->amount."' where tgid = '".$tgID."' and bid = '".$res->bid."'");
                // }
            
            } 
        }

        return "Cost added to selected test in all centers.";
    }

    function updateComment() {
        $tgID = Input::get("tgid");
        $cmt = Input::get("comment");
        $tgPrice = Input::get("tgprice");
        $tgCost = Input::get("tgcost");
        $time = Input::get("time");

        $testcode = Input::get("testcode");
        $sam_container = Input::get("sam_container");

        //report configs
        //report configs
        $rep_heading = "0";
        if (Input::get("rep_heading") == "on") {
            $rep_heading = "1";
        }
        $custom_configs = "0";
        if (Input::get("custom_configs") == "on") {
            $custom_configs = "1";
        }

        $name_col = "0";
        if (Input::get("name_col") == "on") {
            $name_col = "1";
        }
        $value_col = "0";
        if (Input::get("value_col") == "on") {
            $value_col = "1";
        }
        $unit_col = "0";
        if (Input::get("unit_col") == "on") {
            $unit_col = "1";
        }
        $flag_col = "0";
        if (Input::get("flag_col") == "on") {
            $flag_col = "1";
        }
        $ref_col = "0";
        if (Input::get("ref_col") == "on") {
            $ref_col = "1";
        }

        $name_col_head = Input::get("name_col_head");
        $value_col_head = Input::get("value_col_head");
        $unit_col_head = Input::get("unit_col_head");
        $flag_col_head = Input::get("flag_col_head");
        $ref_col_head = Input::get("ref_col_head");

        $name_col_width = Input::get("name_col_width");
        $value_col_width = Input::get("value_col_width");
        $unit_col_width = Input::get("unit_col_width");
        $flag_col_width = Input::get("flag_col_width");
        $ref_col_width = Input::get("ref_col_width");

        $name_col_align = Input::get("name_col_align");
        $result_col_align = Input::get("result_col_align");
        $unit_col_align = Input::get("unit_col_align");
        $flag_col_align = Input::get("flag_col_align");
        $ref_col_align = Input::get("ref_col_align");

        $age_ref = "0";
        if (Input::get("age_ref") == "on") {
            $age_ref = "1";
        }

        $res = DB::statement("update Testgroup set comment='" . $cmt . "',cost='" . $tgCost . "',price='" . $tgPrice . "',testingtime = '" . $time . "' "
            . ", rep_heading='" . $rep_heading . "', name_col='" . $name_col . "', value_col='" . $value_col . "', unit_col='" . $unit_col . "', flag_col='" . $flag_col . "', ref_col='" . $ref_col . "'"
            . ", name_col_head='" . $name_col_head . "', value_col_head='" . $value_col_head . "', unit_col_head='" . $unit_col_head . "', flag_col_head='" . $flag_col_head . "', ref_col_head='" . $ref_col_head . "'"
            . ", name_col_width='" . $name_col_width . "', value_col_width='" . $value_col_width . "', unit_col_width='" . $unit_col_width . "', flag_col_width='" . $flag_col_width . "', ref_col_width='" . $ref_col_width . "'"
            . ", custom_configs='" . $custom_configs . "', name_col_align='" . $name_col_align . "', result_col_align='" . $result_col_align . "', unit_col_align='" . $unit_col_align . "', flag_col_align='" . $flag_col_align . "', age_ref='" . $age_ref . "', ref_col_align='" . $ref_col_align . "', testCode='" . $testcode . "', sample_containers_scid='" . $sam_container . "'"
            . " where tgid = '" . $tgID . "' and lab_lid = '" . $_SESSION['lid'] . "'");

//        echo $cmt;

        $log_descreption = "TestGroup Comment Updated : TGID ".$tgID;

        SystemLogs::saveChangeLog($_SESSION['lid'], $_SESSION['luid'], "TestGroupManage", "Update", $log_descreption);

        return "Comment Updated!";
    }

}

?>
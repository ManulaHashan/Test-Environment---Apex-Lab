<?php

//session_start();
if (!isset($_SESSION)) {
    session_start();
}
date_default_timezone_set('Asia/Colombo');

class AnalyzerController extends Controller {

    function manageAnalyzers() {
        if (Input::get('submit') != null) {
            if (Input::get('submit') == "Add Analyzer") {
                if (Input::get('anname') != "") {
                    $tgName = Input::get("anname");

                    $Result = DB::select("select anid from analyzers where name = '" . $tgName . "' and Lab_lid = '" . $_SESSION['lid'] . "'");
                    foreach ($Result as $res) {
                        $tgroup = $res;
                        DB::statement("update analyzers set status='1' where anid = '".$res->anid."' and Lab_lid = '" . $_SESSION['lid'] . "'");
                    }
                    if (isset($tgroup)) {
                        return View::make('Witestgroups')->with('msg', 'Analyzer Exsist!');
                    } else {
                        DB::statement("insert into analyzers(name,Lab_lid,status) values('" . $tgName . "','" . $_SESSION['lid'] . "','1')");
                        return View::make('Witestgroups')->with('msg', 'Analyzer Added!');
                    }
                } else {
                    return View::make('Witestgroups')->with('msg', 'Enter correct values!');
                }
            } else if (Input::get('submit') == "Delete") {
                $tgID = Input::get("anid");
                DB::statement("update analyzers set status='0' where anid = '".$tgID."' and Lab_lid = '" . $_SESSION['lid'] . "'");
                return View::make('Witestgroups')->with('msg', 'Group Deleted!');
            }
        } else {
            return View::make('Witestgroups')->with('msg', 'Enter correct values!');
        }
    }

}

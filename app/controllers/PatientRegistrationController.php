<?php

use Illuminate\Support\Facades\DB;

if (!isset($_SESSION)) {
    session_start();
}

//error_reporting(0);

class PatientRegistrationController extends Controller
{
public function loadSampleNumber()
{
        $date = date('Y-m-d');
        $sampleNo = '';
       
        $labBranchId = Input::get('labBranchId');
        if ($labBranchId=='%') {
            $fromat = date('ymd');

            $sampleResult = DB::select("SELECT MAX(CONVERT(REGEXP_REPLACE(SUBSTRING(sampleNo, 7), '[^0-9]', ''), SIGNED INTEGER)) AS max_sample_no FROM lps WHERE Lab_lid = '" . $_SESSION['lid'] . "' AND date = ?", [$date]);
            if (!empty($sampleResult)) {
                foreach ($sampleResult as $result) {
                    $currentNo = $result->max_sample_no;
                    if ($currentNo) {
                        $sampleNo = $fromat . str_pad($currentNo + 1, 2, '0', STR_PAD_LEFT);
                    }else{
                        $sampleNo = $fromat . "01";
                    }
                }
            }else{
                $sampleNo = $fromat . "01";
            }
        }else{
            
            $branchCode = DB::select("SELECT code FROM labbranches WHERE bid = ? and Lab_lid = ?" , [$labBranchId,$_SESSION['lid']]);
            foreach ($branchCode as $bcode) {
                $fromat = $bcode->code;
            }

            $sampleResult = DB::select("SELECT MAX(CONVERT(REGEXP_REPLACE(SUBSTRING(sampleNo, 7), '[^0-9]', ''), SIGNED INTEGER)) AS max_sample_no FROM lps WHERE Lab_lid = '" . $_SESSION['lid'] . "' AND date = ? and sampleNo like '". $fromat."%'", [$date]);
            if (!empty($sampleResult)) {
                foreach ($sampleResult as $result) {
                    $currentNo = $result->max_sample_no;
                    if ($currentNo) {
                        $sampleNo = $fromat . str_pad($currentNo + 1, 2, '0', STR_PAD_LEFT);
                    } else {
                        $sampleNo = $fromat . "01";
                    }
                }
            }else{
                $sampleNo = $fromat . "01";
            }
        }

        
       return $sampleNo;
        
        
}

public function loadBrachWiceTest(){
        $labBranchId = Input::get('labBranchId');
        $labLid = $_SESSION['lid']; // Secure session retrieval

        // Check if labLid is available
        if (!$labLid) {
            return response()->json(['error' => 'Session expired or invalid.'], 401);
        }

        // Build SQL query based on labBranchId
        if ($labBranchId == "%") {
            $query = "SELECT c.name, c.tgid, c.price, c.testingtime 
                      FROM test a 
                      JOIN Lab_has_test b ON a.tid = b.test_tid 
                      JOIN Testgroup c ON b.testgroup_tgid = c.tgid 
                      WHERE b.lab_lid = ?
                      GROUP BY c.name 
                      ORDER BY c.name";
            $bindings = [$labLid];
        } else {
            $query = "SELECT c.name, d.tgid, d.price, d.testingtime 
                      FROM test a 
                      JOIN Lab_has_test b ON a.tid = b.test_tid 
                      JOIN Testgroup c ON b.testgroup_tgid = c.tgid 
                      JOIN labbranches_has_Testgroup d ON d.tgid = b.testgroup_tgid 
                      WHERE b.lab_lid = ? AND d.bid LIKE ?
                      GROUP BY c.name 
                      ORDER BY c.name";
            $bindings = [$labLid, $labBranchId];
        }

        // Execute query
        $result = DB::select($query, $bindings);

        // Build options for dropdown
        $list_data = "<option value=''></option>";
        

        foreach ($result as $res) {
            $tgid = htmlspecialchars($res->tgid);
            $group = htmlspecialchars($res->name);
            $price = htmlspecialchars($res->price);
            $time = htmlspecialchars($res->testingtime);
            $list_data .= "<option value='$tgid:$group:$price:$time'>$group</option>";
        }

        return Response::json(['options' => $list_data]);
}

    // public function getPackageTests(Request $request)
    // {
    //     $packageId = $request->input('packageId');

    //     // Fetch tests related to the selected package
    //     $tests = DB::table('Testgroup_has_labpackages')
    //         ->join('tests', 'Testgroup_has_labpackages.idtest', '=', 'tests.id')
    //         ->where('Testgroup_has_labpackages.idlabpackages', $packageId)
    //         ->select('tests.id', 'tests.name', 'tests.amount')
    //         ->get();

    //     // Return the tests as a JSON response
    //     return response()->json(['tests' => $tests]);
    // }
}
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
        $labLid = $_SESSION['lid']; 

       
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

public function loadPackageTests(){
        $packageId = explode(':', Input::get('packageId', ''))[0];
        $labLid = $_SESSION['lid'];

        $query = "SELECT c.name, a.tgid,c.price as testprice,c.testingtime
          FROM Testgroup_has_labpackages a
          JOIN labpackages b ON a.idlabpackages = b.idlabpackages
          JOIN Testgroup c ON a.tgid = c.tgid
          WHERE b.Lab_lid = ? AND a.idlabpackages = ?
          GROUP BY c.name
          ORDER BY c.name";

        $bindings = [$labLid, $packageId];

        // Execute query
        $result = DB::select($query, $bindings);
        $testarry = array();
        foreach ($result as $res) {
            $tgid = htmlspecialchars($res->tgid);
            $testname = htmlspecialchars($res->name);
            $price = htmlspecialchars($res->testprice);
            $testtime = htmlspecialchars($res->testingtime);
            $test_data = $tgid."@".$testname."@". $price."@". $testtime;
            $testarry[] = $test_data;
        }

        return Response::json(['testData' => $testarry]);
    }


    public function savePatientDetails(Request $request)
    {
        $sampleSufArray = ["", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"];
        $labLid = $_SESSION['lid'];
        $query = "SELECT idref FROM refference WHERE lid = ? AND name = ref LIMIT 1";

        $bindings = [$labLid, $request->input('ref')];

        // Execute query
        $result = DB::select($query, $bindings);

        foreach ($result as $res) {
            $refId = htmlspecialchars($res->idref); 
        }


        if (explode(":", $request->input('pkgname'))[1] != "") 
        {
            $labpackId = "";
            $result_labpack = DB::select("SELECT idlabpackages FROM labpackages WHERE name = '" . explode(":", $request->input('pkgname'))[1] . "' AND Lab_lid = '" . $labLid. "'");
            foreach ($result_labpack as $restlpk) {
                $labpackId = htmlspecialchars($restlpk->idlabpackages);

                DB::table('invoice_has_labpackages')->insert([
                    'sno' => $request->input('sampleNo'),
                    'pcid' => $labpackId,
                    'lab_lid' => $labLid
                ]);
            }
        }

        $simpleDateFormat = date('Ymd');
       
        if ($request->input('ref') == "") {
            $result = DB::select("SELECT idref FROM refference WHERE lid = ? AND name = '' LIMIT 1", $labLid);

            if (!empty($result)) {
                $reffereceId = $result[0]->idref;
            } else {
                $reffereceId = 0;
            }
        } else {
            $reffereceId = $request->input('refcode');
        }

        
        

        $patientData = [
            'sample_no' => $request->input('sampleNo'),
            'lab_branch' => $request->input('labbranch'),
            'type' => $request->input('type'),
            'source' => $request->input('source'),
            'tpno' => $request->input('tpno'),
            'initial' => $request->input('initial'),
            'first_name' => $request->input('fname'),
            'last_name' => $request->input('lname'),
            'dob' => $request->input('dob'),
            'years' => $request->input('years'),
            'months' => $request->input('months'),
            'days' => $request->input('days'),
            'gender' => $request->input('gender'),
            'nic' => $request->input('nic'),
            'address' => $request->input('address'),
            'refcode' => $request->input('refcode'),
            'ref' => $request->input('ref'),
            'testname' => $request->input('testname'),
            'pkgname' => $request->input('pkgname'),
            'fast_time' => $request->input('fast_time'),
            'test_data' => json_encode($request->input('test_data')),
            'total_amount' => $request->input('total_amount'),
            'discount' => $request->input('discount'),
            'discount_percentage' => $request->input('discount_percentage'),
            'grand_total' => $request->input('grand_total'),
            'payment_method' => $request->input('payment_method'),
            'paid' => $request->input('paid'),
            'due' => $request->input('due')
        ];

        echo $request->input('sampleNo');

        // try {

        //     $patientData = [
        //         'sample_no' => $request->input('sampleNo'),
        //         'lab_branch' => $request->input('labbranch'),
        //         'type' => $request->input('type'),
        //         'source' => $request->input('source'),
        //         'tpno' => $request->input('tpno'),
        //         'initial' => $request->input('initial'),
        //         'first_name' => $request->input('fname'),
        //         'last_name' => $request->input('lname'),
        //         'dob' => $request->input('dob'),
        //         'years' => $request->input('years'),
        //         'months' => $request->input('months'),
        //         'days' => $request->input('days'),
        //         'gender' => $request->input('gender'),
        //         'nic' => $request->input('nic'),
        //         'address' => $request->input('address'),
        //         'refcode' => $request->input('refcode'),
        //         'ref' => $request->input('ref'),
        //         'testname' => $request->input('testname'),
        //         'pkgname' => $request->input('pkgname'),
        //         'fast_time' => $request->input('fast_time'),
        //         'test_data' => json_encode($request->input('test_data')), 
        //         'total_amount' => $request->input('total_amount'),
        //         'discount' => $request->input('discount'),
        //         'discount_percentage' => $request->input('discount_percentage'),
        //         'grand_total' => $request->input('grand_total'),
        //         'payment_method' => $request->input('payment_method'),
        //         'paid' => $request->input('paid'),
        //         'due' => $request->input('due')
        //     ];

        //         echo json_encode($patientData);

        //     // PatientRegistration::create($patientData);


        //     // return response()->json(['error' => 'saved']);
        // } catch (\Exception $e) {

        //     return response()->json(['error' => 'An unexpected error occurred.']);
        // }
    }


 

   
}
<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;


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
        if ($labBranchId == '%') {
            $fromat = date('ymd');

            $sampleResult = DB::select("SELECT MAX(CONVERT(REGEXP_REPLACE(SUBSTRING(sampleNo, 7), '[^0-9]', ''), SIGNED INTEGER)) AS max_sample_no FROM lps WHERE Lab_lid = '" . $_SESSION['lid'] . "' AND date = ?", [$date]);
            if (!empty($sampleResult)) {
                foreach ($sampleResult as $result) {
                    $currentNo = $result->max_sample_no;
                    if ($currentNo) {
                        $sampleNo = $fromat . str_pad($currentNo + 1, 2, '0', STR_PAD_LEFT);
                    } else {
                        $sampleNo = $fromat . "01";
                    }
                }
            } else {
                $sampleNo = $fromat . "01";
            }
        } else {

            $branchCode = DB::select("SELECT code FROM labbranches WHERE bid = ? and Lab_lid = ?", [$labBranchId, $_SESSION['lid']]);
            foreach ($branchCode as $bcode) {
                $fromat = $bcode->code;
            }

            $sampleResult = DB::select("SELECT MAX(CONVERT(REGEXP_REPLACE(SUBSTRING(sampleNo, 7), '[^0-9]', ''), SIGNED INTEGER)) AS max_sample_no FROM lps WHERE Lab_lid = '" . $_SESSION['lid'] . "' AND date = ? and sampleNo like '" . $fromat . "%'", [$date]);
            if (!empty($sampleResult)) {
                foreach ($sampleResult as $result) {
                    $currentNo = $result->max_sample_no;
                    if ($currentNo) {
                        $sampleNo = $fromat . str_pad($currentNo + 1, 2, '0', STR_PAD_LEFT);
                    } else {
                        $sampleNo = $fromat . "01";
                    }
                }
            } else {
                $sampleNo = $fromat . "01";
            }
        }


        return $sampleNo;
    }

    public function loadBrachWiceTest()
    {
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

    public function loadPackageTests()
    {
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
            $test_data = $tgid . "@" . $testname . "@" . $price . "@" . $testtime;
            $testarry[] = $test_data;
        }

        return Response::json(['testData' => $testarry]);
    }


    

    //*************************************

    // public function savePatientDetails()
    // {
    //     $userUid = $_SESSION['luid']; 
    //     $years = Input::get('years');
    //     $months = Input::get('months');
    //     $days = Input::get('days');
    //     $initial = Input::get('initial');
    //     $dob = Input::get('dob');

    //     $fname = Input::get('fname');
    //     $lname = Input::get('lname');
    //     $tpno = Input::get('tpno');
    //     $address = Input::get('address');
    //     $gender = Input::get('gender');
    //     $nic = Input::get('nic');

    //     $labLid = $_SESSION['lid'];
    //     $now = date('Y-m-d H:i:s');
    //     $currentTimestamp = Carbon::now();

    //     $id = DB::table('user')->insertGetId([
    //         'uid' => $userUid,
    //         'fname' => $fname,
    //         'lname' => $lname,
    //         'tpno' => $tpno,
    //         'address' => $address,
    //         'gender_idgender' => $gender,
    //         'nic' => $nic,
    //         'created_at' => $currentTimestamp,
    //         'updated_at' => $currentTimestamp
    //     ]);

    //     DB::insert("insert into patient (user_uid, age, months, days, initials, dob, created_at, updated_at) 
    //                  values (?, ?, ?, ?, ?, ?, ?, ?)", [
    //         $id,
    //         $years,
    //         $months,
    //         $days,
    //         $initial,
    //         $dob,
    //         $currentTimestamp,
    //         $currentTimestamp
    //     ]);


    //     DB::insert("INSERT INTO lps (patient_pid, Lab_lid, date, sampleNo, arivaltime, refby, type, refference_idref, fastingtime, entered_uid, price, Testgroup_tgid, urgent_sample, created_at, updated_at) 
    //                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
    //         $patientId, // Assuming patientId is the last inserted ID
    //         $labLid,
    //         $now,
    //         Input::get('sampleNo'),
    //         $now,
    //         Input::get('ref'),
    //         Input::get('type'),
    //         Input::get('ref'), // Assuming this is the correct reference ID
    //         Input::get('fast_time'),
    //         '',
    //         Input::get('total_amount'),
    //         Input::get('test_data')[0]['tgid'], // Assuming test_data is an array and you want the first element
    //         Input::get('test_data')[0]['priority'], // Assuming priority is a field in the first test_data element
    //         $now,
    //         $now
    //     ]);


    //     return Response::json(['success' => true, 'message' => 'Patient details saved successfully.']);
    // }


    public function savePatientDetails()
    {
        $userUid = $_SESSION['luid']; 
        $years = Input::get('years');
        $months = Input::get('months');
        $days = Input::get('days');
        $initial = Input::get('initial');
        $dob = Input::get('dob');

        $fname = Input::get('fname');
        $lname = Input::get('lname');
        $tpno = Input::get('tpno');
        $address = Input::get('address');
        $gender = Input::get('gender');
        $nic = Input::get('nic');

        $labLid = $_SESSION['lid'];
        $now = date('Y-m-d H:i:s');
        $currentTimestamp = Carbon::now();
        // $sampleSufArray = ["", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"];
        // $sampleNo = $this->loadSampleNumber();
        $user = DB::table('user')->where('tpno', $tpno)->first();

        if ($user) {
           
            $patientid = DB::table('patient')->insertGetId([
                'user_uid' => $user->uid,
                'age' => $years,
                'months' => $months,
                'days' => $days,
                'initials' => $initial,
                'dob' => $dob,
                'created_at' => $currentTimestamp,
                'updated_at' => $currentTimestamp
            ]);
        } else {
            $userid = DB::table('user')->insertGetId([
                'fname' => $fname,
                'lname' => $lname,
                'tpno' => $tpno,
                'address' => $address,
                'gender_idgender' => $gender,
                'nic' => $nic,
                'created_at' => $currentTimestamp,
                'updated_at' => $currentTimestamp
            ]);

            $patientid = DB::table('patient')->insertGetId([
                'user_uid' => $userid,
                'age' => $years,
                'months' => $months,
                'days' => $days,
                'initials' => $initial,
                'dob' => $dob,
                'created_at' => $currentTimestamp,
                'updated_at' => $currentTimestamp
            ]);

           
        }


        DB::insert("INSERT INTO lps (patient_pid, Lab_lid, date, sampleNo, arivaltime, refby, type, refference_idref, fastingtime, entered_uid, price, Testgroup_tgid, urgent_sample, created_at, updated_at) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
            $patientid, 
            $labLid,
            $now,
            Input::get('sampleNo'),
            $now,
            Input::get('ref'),
            Input::get('type'),
            Input::get('ref'), 
            Input::get('fast_time'),
            '',
            Input::get('total_amount'),
            Input::get('test_data')[0]['tgid'], 
            Input::get('test_data')[0]['priority'], 
            $now,
            $now
        ]);


        return Response::json(['success' => true, 'message' => 'Patient details saved successfully.']);
    }

}

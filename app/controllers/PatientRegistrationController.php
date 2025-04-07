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
    // public function loadSampleNumber()
    // {
    //     $date = date('Y-m-d');
    //     $sampleNo = '';
    //     $format = '';
    //     $labBranchId = Input::get('labBranchId');
    //     if ($labBranchId == '%') {
    //         $format = date('ymd');

    //         $sampleResult = DB::select("SELECT MAX(CONVERT(REGEXP_REPLACE(SUBSTRING(sampleNo, 7), '[^0-9]', ''), SIGNED INTEGER)) AS max_sample_no FROM lps WHERE Lab_lid = '" . $_SESSION['lid'] . "' AND date = ?", [$date]);
    //         if (!empty($sampleResult)) {
    //             foreach ($sampleResult as $result) {
    //                 $currentNo = $result->max_sample_no;
    //                 if ($currentNo) {
    //                     $sampleNo = $format . str_pad($currentNo + 1, 2, '0', STR_PAD_LEFT);
    //                 } else {
    //                     $sampleNo = $format . "01";
    //                 }
    //             }
    //         } else {
    //             $sampleNo = $format . "01";
    //         }
    //     } else {
    //         $branchCode = DB::select("SELECT code FROM labbranches WHERE bid = ? and Lab_lid = ?", [$labBranchId, $_SESSION['lid']]);
    //         foreach ($branchCode as $bcode) {
    //             $format = $bcode->code;
    //         }

    //         $sampleResult = DB::select("SELECT MAX(CONVERT(REGEXP_REPLACE(SUBSTRING(sampleNo, 7), '[^0-9]', ''), SIGNED INTEGER)) AS max_sample_no FROM lps WHERE Lab_lid = '" . $_SESSION['lid'] . "' AND date = ? and sampleNo like '" . $format . "%'", [$date]);
    //         if (!empty($sampleResult)) {
    //             foreach ($sampleResult as $result) {
    //                 $currentNo = $result->max_sample_no;
    //                 if ($currentNo) {
    //                     $sampleNo = $format . str_pad($currentNo + 1, 2, '0', STR_PAD_LEFT);
    //                 } else {
    //                     $sampleNo = $format . "01";
    //                 }
    //             }
    //         } else {
    //             $sampleNo = $format . "01";
    //         }
    //     }

    //     return $sampleNo;
    // }


    public function loadSampleNumber()
    {
        $date = date('Y-m-d');
        $sampleNo = '';
        $format = ''; 

        $labBranchId = Input::get('labBranchId');
        if ($labBranchId == '%') {

            $format = date('ymd');


            $sampleResult = DB::select("SELECT MAX(CONVERT(SUBSTRING(sampleNo, 7), UNSIGNED INTEGER)) AS max_sample_no FROM lps WHERE Lab_lid = ? AND DATE(date) = ?", [$_SESSION['lid'], $date]);


            if (!empty($sampleResult)) {
                $currentNo = $sampleResult[0]->max_sample_no;
                $nextNo = $currentNo ? $currentNo + 1 : 1; 
                $sampleNo = $format . str_pad($nextNo, 2, '0', STR_PAD_LEFT); 
            } else {
                $sampleNo = $format . "01"; 
            }
        } else {

            $branchCode = DB::select("SELECT code FROM labbranches WHERE bid = ? AND Lab_lid = ?", [$labBranchId, $_SESSION['lid']]);
            $format = !empty($branchCode) ? $branchCode[0]->code : '';


            $sampleResult = DB::select("SELECT MAX(CONVERT(SUBSTRING(sampleNo, LENGTH(?) + 1), UNSIGNED INTEGER)) AS max_sample_no FROM lps WHERE Lab_lid = ? AND DATE(date) = ? AND sampleNo LIKE ?", [$format, $_SESSION['lid'], $date, $format . '%']);


            if (!empty($sampleResult)) {
                $currentNo = $sampleResult[0]->max_sample_no;
                $nextNo = $currentNo ? $currentNo + 1 : 1; 
                $sampleNo = $format . str_pad($nextNo, 2, '0', STR_PAD_LEFT); 
            } else {
                $sampleNo = $format . "01"; 
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
        $sampleSufArray = ["", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"];
        //$sampleNo = $this->loadSampleNumber();
        $user = DB::table('user')->where('tpno', $tpno)->first();
        $testData = Input::get('test_data');

        // Patient and User Insertion Logic
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

        // Inserting into the invoice table
        $totalAmount = Input::get('total_amount');
        $grandTotal = Input::get('grand_total');
        $paid = Input::get('paid');
        $paymentMethod = Input::get('payment_method');
        $invoiceRemark = Input::get('inv_remark');
        $source = Input::get('source'); 


        if ($paid == 0.0) {
            $paymentStatus = "Not Paid";
        } elseif ($paid >= $grandTotal) {
            $paymentStatus = "Payment Done";
        } else {
            $paymentStatus = "Pending Due";
        }

        // Insert into invoice table
        // $invoiceId = DB::table('invoice')->insertGetId([
        //     'lps_lpsid' => $patientid, 
        //     'date' => $now,
        //     'total' => $totalAmount,
        //     'gtotal' => $grandTotal,
        //     'paid' => $paid,
        //     'status' => $paymentStatus,
        //     'paymentmethod' => $paymentMethod,
        //     'cashier' => $userUid, 
        //     'cost' => 0, 
        //     // 'remark' => $invoiceRemark,
        //     // 'source' => $source,
        //     // 'created_at' => $now,
        //     // 'updated_at' => $now
        // ]);

        // Inserting into invoice_payments table if paid amount is greater than 0
        // if ($paid > 0.0) {
        //     DB::table('invoice_payments')->insert([
        //         'date' => $now,
        //         'amount' => $paid,
        //         'user_uid' => $userUid,
        //         'paymethod' => $paymentMethod, 
        //         'invoice_iid' => $invoiceId,
        //         // 'created_at' => $now,
        //         // 'updated_at' => $now
        //     ]);
        // }

        // Inserting tests into lps and lps_has_test tables
        foreach ($testData as $index => $test) {
            // $fullSampleNo = $sampleNo . ($index < count($sampleSufArray) ? $sampleSufArray[$index] : '');

            $lpsId = DB::table('lps')->insertGetId([
                'patient_pid' => $patientid,
                'Lab_lid' => $labLid,
                'date' => $now,
                'sampleNo' => $test['sampleNo'],
                'arivaltime' => $now,
                'refby' => Input::get('ref'),
                'type' => Input::get('type'),
                'refference_idref' => Input::get('ref'),
                'fastingtime' => Input::get('fast_time'),
                'entered_uid' => '',
                'price' => $test['price'],
                'Testgroup_tgid' => $test['tgid'],
                'urgent_sample' => $test['priority'],
                'created_at' => $now,
                'updated_at' => $now
            ]);


            if ($index == '0') {
                $invoiceId = DB::table('invoice')->insertGetId([
                    'lps_lpsid' => $lpsId,
                    'date' => $now,
                    'total' => $totalAmount,
                    'gtotal' => $grandTotal,
                    'paid' => $paid,
                    'status' => $paymentStatus,
                    'paymentmethod' => $paymentMethod,
                    'cashier' => $userUid,
                    'cost' => 0,
                    // 'remark' => $invoiceRemark,
                    // 'source' => $source,
                    // 'created_at' => $now,
                    // 'updated_at' => $now
                ]);

                if ($paid > 0.0) {
                    DB::table('invoice_payments')->insert([
                        'date' => $now,
                        'amount' => $paid,
                        'user_uid' => $userUid,
                        'paymethod' => $paymentMethod,
                        'invoice_iid' => $invoiceId,
                        // 'created_at' => $now,
                        // 'updated_at' => $now
                    ]);
                }
            }

            $lpsId = DB::getPdo()->lastInsertId();
            $testRecords = DB::table('Lab_has_test')
                ->where('Lab_lid', $labLid)
                ->where('Testgroup_tgid', $test['tgid'])
                ->get();

            $lpsHasTestData = [];
            foreach ($testRecords as $testRecord) {
                $lpsHasTestData[] = [
                    'lps_lpsid' => $lpsId,
                    'test_tid' => $testRecord->test_tid,
                    'state' => 'pending',
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }

            if (!empty($lpsHasTestData)) {
                DB::table('lps_has_test')->insert($lpsHasTestData);
            }

            

           
        }

        return Response::json(['success' => true, 'message' => 'Patient details saved successfully.']);
    }


         
    

}
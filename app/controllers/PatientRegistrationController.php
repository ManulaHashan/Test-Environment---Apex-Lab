<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use Illuminate\Http\Request;


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


    // public function getAllUsers()
    // {
    //     $tpno = Input::get('Usertpno'); 

    //     $query = DB::table('user')->select('fname', 'lname', 'tpno');


    //     if (!empty($tpno)) {
    //         $query->where('tpno', 'LIKE', '%' . $tpno . '%');
    //     }

    //     $Result = $query->orderBy('fname', 'ASC')->get();

    //     return Response::json($Result);
    // }

    public function getAllUsers()
    {
        $tpno = Input::get('Usertpno');

        // $query = DB::table('user')
        //     ->where('tpno', 'LIKE', '%' . $tpno . '%')
        //     ->select(
        //         'uid',
        //         'fname',
        //         'lname',
        //         'tpno'
        //     )
        //     ->orderBy('fname', 'ASC')->get();


        $user = DB::table('user as a')
            ->select('a.uid','a.fname', 'a.lname', 'a.tpno', 'b.initials')
            ->from(DB::raw('user as a, patient as b, lps as c'))
            ->whereRaw('a.uid = b.user_uid')
            ->whereRaw('b.pid = c.patient_pid')
            ->where('c.Lab_lid', $_SESSION['lid'])
            ->where('a.tpno', 'like', $tpno . '%')
            ->groupBy('a.uid')
            ->get();

        return Response::json($user);
    }
    public function getUserDetailsByTP()
    {
        $user_ID = Input::get('useruid');

        $user = DB::table('user as u')
            ->join('patient as p', 'u.uid', '=', 'p.user_uid')
            ->where('u.uid', $user_ID)
            ->select(
                'u.fname',
                'u.lname',
                'u.gender_idgender',
                'u.address',
                'u.tpno',
                'u.nic',
                'p.age',
                'p.months',
                'p.days',
                'p.initials',
                'p.dob'
                
            )
            ->first();

        return Response::json($user);
    }

    public function getRefCode()
    {
        $keyword = Input::get('keyword');
       
    
        $references = DB::table('refference')
            ->where('lid', '=', $_SESSION['lid'])
            ->where('code', 'LIKE', '%' . $keyword . '%')
            ->select('code', 'name', 'idref')
            ->get();
    
        return Response::json($references);
    }
    


    //*************************************


    public function savePatientDetails()
    {
        $userUid = $_SESSION['uid'];
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
        // $refName = Input::get('refName');

        $discount = Input::get('discount'); 
        $discountId = Input::get('discountId'); 
        $cashAmount = Input::get('split_cash_amount');
        $cardAmount = Input::get('split_card_amount');
        $voucherAmount = Input::get('vaucher_amount');
        

        $labLid = $_SESSION['lid'];
        $now = date('Y-m-d H:i:s');
        $currentTimestamp = Carbon::now();
        $sampleSufArray = ["", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"];
        //*#*#*#*##**#$sampleNo = $this->loadSampleNumber();*#*#*#*##**#

        
        $useruid = Input::get('userUID');

        $user = DB::table('user')->where('uid', $useruid)->first();
         
        $testData = Input::get('test_data');
        $refId = Input::get('ref');


        $reference = DB::table('refference')->where('idref', $refId)->first();
        $refName = $reference ? $reference->name : null;
        $inv_remark = Input::get('inv_remark');

        // *#*#*#*##**# Patient and User Insertion Logic *#*#*#*##**#
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
                'usertype_idusertype' => '2',
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

        // Inserting tests into lps and lps_has_test tables
        foreach ($testData as $index => $test) {
            // $fullSampleNo = $sampleNo . ($index < count($sampleSufArray) ? $sampleSufArray[$index] : '');


            // Inserting lps tables
            $lpsId = DB::table('lps')->insertGetId([
                'patient_pid' => $patientid,
                'Lab_lid' => $labLid,
                'date' => $now,
                'sampleNo' => $test['sampleNo'],
                'arivaltime' => $now,
                'refby' => $refName, 
                'type' => Input::get('type'),
                'refference_idref' => Input::get('ref'),
                'fastingtime' => Input::get('fast_time'),
                'entered_uid' => '',
                'price' => $test['price'],
                'Testgroup_tgid' => $test['tgid'],
                'urgent_sample' => $test['priority'],
                'specialnote' =>Input::get('inv_remark'),
                'status' => 'pending',
                'created_at' => $now,
                'updated_at' => $now
            ]);

             // Inserting invoice tables
             $cashier = DB::table('user')->where('uid', '=', $userUid)->first();

             if ($cashier) {
                 $cashierName = $cashier->fname . ' ' . $cashier->lname;
             }else{
                $cashierName = '';
             } 
             

            if ($index == '0') {
                $paymentMethodRaw = Input::get('payment_method');
                $paymentMethodMap  = [
                    '1' => 'cash',
                    '2' => 'card',
                    'credit' => 'credit',
                    '3' => 'cheque',
                    '6' => 'voucher',
                    '5' => 'split'
                ];
                $paymentMethod = isset($paymentMethodMap[$paymentMethodRaw]) ? $paymentMethodMap[$paymentMethodRaw] : 'cash';
                $invoiceId = DB::table('invoice')->insertGetId([
                    'lps_lpsid' => $lpsId,
                    'date' => $now,
                    'total' => $totalAmount,
                    'gtotal' => $grandTotal,
                    'paid' => $paid,
                    'paiddate' => $now,
                    'status' => $paymentStatus,
                    'paymentmethod' => $paymentMethod,
                    'cashier' => $cashierName,
                    'cost' => 0,
                    'discount' => Input::get('discount'), 
                    'Discount_did' => Input::get('discountId'), 
                    'multiple_delivery_methods' => Input::get('delivery_methods'),
                    // 'remark' => $invoiceRemark,
                    // 'source' => $source,
                    // 'created_at' => $now,
                    // 'updated_at' => $now
                ]);



                // insert invoce_payments table
                if ($paid > 0.0) {
                    if ($paymentMethodRaw == '5') { // Split
                        $cashAmount = Input::get('split_cash_amount');
                        $cardAmount = Input::get('split_card_amount'); 
                
                        if ($cashAmount > 0) {
                            DB::table('invoice_payments')->insert([
                                'date' => $now,
                                'amount' => $cashAmount,
                                'user_uid' => $userUid,
                                'paymethod' => 1, // Cash
                                'invoice_iid' => $invoiceId,
                            ]);
                        }
                
                        if ($cardAmount > 0) {
                            DB::table('invoice_payments')->insert([
                                'date' => $now,
                                'amount' => $cardAmount,
                                'user_uid' => $userUid,
                                'paymethod' => 2, // Card
                                'invoice_iid' => $invoiceId,
                            ]);
                        }
                
                    } elseif ($paymentMethodRaw == '6') { // Voucher
                        $voucherAmount = Input::get('vaucher_amount');
                
                        if ($voucherAmount > 0) {
                            DB::table('invoice_payments')->insert([
                                'date' => $now,
                                'amount' => $voucherAmount,
                                'user_uid' => $userUid,
                                'paymethod' => 6, // Voucher
                                'invoice_iid' => $invoiceId,
                            ]);
                        }
                
                    } else {
                       
                        DB::table('invoice_payments')->insert([
                            'date' => $now,
                            'amount' => $paid,
                            'user_uid' => $userUid,
                            'paymethod' => $paymentMethodRaw, 
                            'invoice_iid' => $invoiceId,
                        ]);
                    }
                }
                
                
                
            }

            // $lpsId = DB::getPdo()->lastInsertId();
            $testRecords = DB::table('Lab_has_test')
                ->where('Lab_lid', $labLid)
                ->where('Testgroup_tgid', $test['tgid'])
                ->get();

            $lpsHasTestData = [];
            foreach ($testRecords as $testRecord) {

                DB::table('lps_has_test')->insert([
                    'lps_lpsid' => $lpsId,
                    'test_tid' => $testRecord->test_tid,
                    'state' => 'pending',
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
           
        }

        return Response::json(['success' => true, 'message' => 'Patient details saved successfully.']);
    }

    public function getSampleTestData()
    {
        $sampleNo = Input::get('sampleNo');
        $date = Input::get('date');
        
        
        // Retrieve all lps records matching the sampleNo, date, and Lab ID
        $lpsRecords = DB::table('lps as a')
        ->join('Testgroup as b', 'a.Testgroup_tgid', '=', 'b.tgid')
        ->join('refference as r', 'a.refference_idref', '=', 'r.idref')
        ->join('patient as p', 'a.patient_pid', '=', 'p.pid')
        ->where('a.sampleNo', 'like', $sampleNo . '%')
        ->where('a.date', $date)
        ->where('a.Lab_lid', $_SESSION['lid'])
        ->select(
            'a.lpsid',
            'a.patient_pid',
            'a.date',
            'a.sampleNo',
            'a.type',
            'a.urgent_sample',
            'a.Testgroup_tgid',
            'b.name',
            'a.refby',
            'r.code'
        )
        ->get();
    
        
        if (empty($lpsRecords)) {
            return Response::json(['success' => false, 'message' => 'Sample not found']);
        }
        
        // Use the first lps record to get patient info
        $firstLps = $lpsRecords[0];
        $patientId = $firstLps->patient_pid;
        
        $patientData = DB::table('patient as p')
            ->join('user as u', 'p.user_uid', '=', 'u.uid')
            ->where('p.pid', $patientId)
            ->select('u.fname', 'u.lname', 'u.nic', 'u.address', 'u.gender_idgender', 'u.tpno', 'p.age', 'p.months', 'p.days', 'p.initials', 'p.dob')
            ->first();

            $invoiceData = DB::table('invoice as i')
            ->join('lps as a', 'i.lps_lpsid', '=', 'a.lpsid')
            ->where('a.sampleNo', 'like', $sampleNo . '%')
            ->where('a.date', $date)
            ->where('a.Lab_lid', $_SESSION['lid'])
            ->select(
                    'i.iid', 
                    'i.total', 
                    'i.paid', 
                    'i.gtotal',
                    'i.discount',
                    'i.status', 
                    'i.paymentmethod',
                    'i.multiple_delivery_methods'
            )
            ->first();    
        
        // Collect all lps IDs that belong to this sampleNo, date, and patient
        $filteredLpsIds = array();
        foreach ($lpsRecords as $rec) {
            if ($rec->patient_pid == $patientId) {
                $filteredLpsIds[] = $rec->lpsid;
            }
        }
        
        if (empty($filteredLpsIds)) {
            return Response::json(['success' => false, 'message' => 'No test data found for this patient']);
        }
        
      
        $testDataRaw = DB::table('lps as a')
            ->join('Testgroup as b', 'a.Testgroup_tgid', '=', 'b.tgid')
            ->select(
                'a.lpsid',
                'a.type',
                'a.urgent_sample',
                'a.Testgroup_tgid as tgid',
                'b.name as group',
                'b.price',
                'b.testingtime as time'
            )
            ->whereIn('a.lpsid', $filteredLpsIds)
            ->get();
        
        $testData = [];
        foreach ($testDataRaw as $test) {
            $testData[] = [
                'lpsid'    => $test->lpsid,
                'tgid'     => $test->tgid,
                'group'    => $test->group,
                'price'    => floatval($test->price),
                'time'     => $test->time,
                'f_time'   => 0,
                'priority' => $test->urgent_sample,
                'type'     => $test->type,
            ];
        }
        
        return Response::json([
            'success' => true,
            'data' => [
                'patient' => $patientData,
                'tests'   => $testData,
                'invoice' => $invoiceData,
                'lpsRecords' => $lpsRecords
            ]
        ]);
    }
    
    
    
    
    
         
    

}
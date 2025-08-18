<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use Illuminate\Http\Request;

if (!isset($_SESSION)) {
    session_start();
}

//error_reporting(0);

class PatientRegistrationController extends Controller {

    public function loadSampleNumber()
    {
        $date = date('Y-m-d');
        $sampleNo = '';
        $format = ''; 

        $labBranchId = Input::get('labBranchId');
        if ($labBranchId == '%') {
            
            if($_SESSION['lid'] == "34"){
                
                $sampleResult = DB::select("SELECT MAX(CONVERT(sampleNo, UNSIGNED INTEGER)) AS max_sample_no FROM lps WHERE Lab_lid = ? and date > '2025-08-05'", [$_SESSION['lid']]);

                if (!empty($sampleResult)) {
                    $currentNo = $sampleResult[0]->max_sample_no;
                    $nextNo = $currentNo ? $currentNo + 1 : 1; 
                    $sampleNo = $nextNo; 
                } else {
                    $sampleNo = $format . "01"; 
                }
                
                
            }else{
                $format = date('ymd');
                $sampleResult = DB::select("SELECT MAX(CONVERT(SUBSTRING(sampleNo, 7), UNSIGNED INTEGER)) AS max_sample_no FROM lps WHERE Lab_lid = ? AND DATE(date) = ?", [$_SESSION['lid'], $date]);

                if (!empty($sampleResult)) {
                    $currentNo = $sampleResult[0]->max_sample_no;
                    $nextNo = $currentNo ? $currentNo + 1 : 1; 
                    $sampleNo = $format . str_pad($nextNo, 2, '0', STR_PAD_LEFT); 
                } else {
                    $sampleNo = $format . "01"; 
                }
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

    public function loadBrachWiceTest() {
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

    public function loadPackageTests() {
        $packageId = explode(':', Input::get('packageId', ''))[0];
        $labLid = $_SESSION['lid'];

        $query = "select c.name, a.tgid,c.price as testprice,c.testingtime
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

    public function getAllUsers() {
        $tpno = Input::get('Usertpno');
        $anyFilter = Input::get('any_filter');

        $query = DB::table('user as a')
                ->join('patient as b', 'a.uid', '=', 'b.user_uid')
                ->join('lps as c', 'b.pid', '=', 'c.patient_pid')
                ->select('a.uid', 'a.fname', 'a.lname', 'a.tpno', 'b.age', 'b.initials', 'a.address', 'a.nic')
                ->where('c.Lab_lid', $_SESSION['lid']);

        if ($anyFilter) {
            // Universal search - match tpno or fname or lname
            $query->where(function ($q) use ($tpno) {
                $q->where('a.tpno', 'like', '%' . $tpno . '%')
                        ->orWhere('a.fname', 'like', '%' . $tpno . '%')
                        ->orWhere('a.lname', 'like', '%' . $tpno . '%')
                        ->orWhere('a.address', 'like', '%' . $tpno . '%')
                        ->orWhere('a.nic', 'like', '%' . $tpno . '%');
            });
        } else {
            // Default tpno search
            $query->where('a.tpno', 'like', $tpno . '%');
        }

        $query->groupBy('a.uid');

        $user = $query->get();

        return Response::json($user);
    }

    public function getUserDetailsByTP() {
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

    // public function getRefCode()
    // {
    //     $keyword = Input::get('keyword');
    //     $references = DB::table('refference')
    //         ->where('lid', '=', $_SESSION['lid'])
    //         ->where('code', 'LIKE', '%' . $keyword . '%')
    //         ->select('code', 'name', 'idref')
    //         ->get();
    //     return Response::json($references);
    // }

    public function getRefCode() {
        $keyword = Input::get('keyword');
        $labLid = $_SESSION['lid'];

        $references = DB::table('refference')
                ->where('lid', '=', $labLid)
                ->where('code', 'LIKE', '%' . $keyword . '%')
                ->select('code', 'name', 'idref')
                ->get();

        return Response::json($references);
    }

    //*************************************


    public function loadSampleNumberUniqe($labBranchId) {
        $date = date('Y-m-d');
        $sampleNo = '';
        $format = '';

        if ($labBranchId == '%') {
            $format = date('ymd');

            $sampleResult = DB::select("select MAX(CONVERT(SUBSTRING(sampleNo, 7), UNSIGNED INTEGER)) AS max_sample_no FROM lps WHERE Lab_lid = ? AND DATE(date) = ?", [$_SESSION['lid'], $date]);

            if (!empty($sampleResult)) {
                $currentNo = $sampleResult[0]->max_sample_no;
                $nextNo = $currentNo ? $currentNo + 1 : 1;
                $sampleNo = $format . str_pad($nextNo, 2, '0', STR_PAD_LEFT);
            } else {
                $sampleNo = $format . "01";
            }
        } else {
            $branchCode = DB::select("select code FROM labbranches WHERE bid = ? AND Lab_lid = ?", [$labBranchId, $_SESSION['lid']]);
            $format = !empty($branchCode) ? $branchCode[0]->code : '';

            $sampleResult = DB::select("select MAX(CONVERT(SUBSTRING(sampleNo, LENGTH(?) + 1), UNSIGNED INTEGER)) AS max_sample_no FROM lps WHERE Lab_lid = ? AND DATE(date) = ? AND sampleNo LIKE ?", [$format, $_SESSION['lid'], $date, $format . '%']);

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

    function insertRecordWithTable2Data($mode, $table2Data, $customPrefix, $labLid, $patientid, $refName, $refId, $type, $fasting_time, $invRemark, $lpsStatus, $now) {
        DB::beginTransaction();

        try {
            $insertedSerials = [];
            $today = date('Y-m-d');
            $todayYmd = date('ymd');
            $nextSeq = 1;

            // === 1. Find Starting Sequence Number ===
            if ($mode == 'date') {
                $prefix = $todayYmd;
                $like = $prefix . '%';

                $latest = DB::table('lps')
                        ->where('date', $today)
                        ->where('Lab_lid', $labLid)
                        ->where('sampleNo', 'like', $like)
                        ->orderBy('sampleNo', 'desc')
                        ->first();

                if ($latest) {
                    $lastSeq = (int) substr($latest->sampleNo, 6, 2);
                    $nextSeq = $lastSeq + 1;
                }
            } elseif ($mode == 'prefix') {
                $prefix = $customPrefix; 
                $like = $prefix . '%';

                $latest = DB::table('lps')
                        ->where('date', $today)
                        ->where('Lab_lid', $labLid)
                        ->where('sampleNo', 'like', $like)
                        ->orderBy(DB::raw('LENGTH(sampleNo)'), 'desc')
                        ->orderBy('sampleNo', 'desc')
                        ->first();

                if ($latest && preg_match('/^' . preg_quote($prefix, '/') . '(\d+)/', $latest->sampleNo, $matches)) {
                    $nextSeq = (int) $matches[1] + 1;
                }
            } elseif ($mode == 'normal') {
                $latest = DB::table('lps')
                        ->where('date', $today)
                        ->where('Lab_lid', $labLid)
                        ->whereRaw("sampleNo REGEXP '^[0-9]+'")
                        ->orderBy(DB::raw('CAST(sampleNo AS UNSIGNED)'), 'desc')
                        ->first();

                if ($latest && preg_match('/^(\d+)/', $latest->sampleNo, $matches)) {
                    $nextSeq = (int) $matches[1] + 1;
                }
            }

            if ($mode == 'date') {
                    $baseSerial = $todayYmd . str_pad($nextSeq, 2, '0', STR_PAD_LEFT);
                } elseif ($mode == 'prefix') {
                    $baseSerial = $customPrefix . $nextSeq;
                } else {
                    $baseSerial = (string) $nextSeq;
                }
                
            $return_lpsid = 0;
            $return_date = 0;
            $return_sno = 0;
                
            // === 2. Insert Records ===
            foreach ($table2Data as $index => $dataRow) {
                $orderNumber = $index + 1; // 1-based
                if ($orderNumber > 26) {
                    throw new Exception("Too many records. Max supported is 26 due to A-Z suffix.");
                }

                $suffixLetter = chr(64 + $orderNumber); // A-Z
                // Serial number creation
                if($suffixLetter == "A"){
                    $suffixLetter = "";
                }
                

                $finalSerial = $baseSerial . $suffixLetter;

                // Inserting lps tables
                $lpsId = DB::table('lps')->insertGetId([
                    'patient_pid' => $patientid,
                    'Lab_lid' => $labLid,
                    'date' => $now,
                    'sampleNo' => $finalSerial,
                    'arivaltime' => $now,
                    'refby' => $refName,
                    'refference_idref' => $refId,
                    'type' => $type,
                    'fastingtime' => $fasting_time,
                    'price' => $dataRow['price'],
                    'Testgroup_tgid' => $dataRow['tgid'],
                    'urgent_sample' => $dataRow['priority'],
                    'status' => $lpsStatus,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
                
                if($index == 0){
                    $return_lpsid = $lpsId;
                    $return_date = $now;
                    $return_sno = $finalSerial;
                }

                // Insert into lps_has_test (with data for this index)

                $testRecords = DB::table('Lab_has_test')
                        ->where('Lab_lid', $labLid)
                        ->where('Testgroup_tgid', $dataRow['tgid'])
                        ->get();

                foreach ($testRecords as $testRecord) {

                    DB::table('lps_has_test')->insert([
                        'lps_lpsid' => $lpsId,
                        'test_tid' => $testRecord->test_tid,
                        'state' => 'pending',
                            // 'created_at' => $now,
                            // 'updated_at' => $now
                    ]);
                }

                $insertedSerials[] = $finalSerial;
                $nextSeq++;
            }

            DB::commit();
            return $return_lpsid."##".$return_date."##".$return_sno;
//            return $nextSeq;
        } catch (Exception $e) {
            DB::rollBack();
            return $e->getMessage()." Line - ".$e->getLine();
        }
    }

    function detectSerialMode($serial) {
        // Example: 25080501 → date mode (starts with yymmdd + sequence digits)
        if (preg_match('/^\d{6}\d{2,}$/', $serial)) {
            return 'date';
        }

        // Example: MC12 → prefix mode (starts with letters, followed by digits)
        if (preg_match('/^[A-Z]{2,}\d+$/i', $serial)) {
            return 'prefix';
        }

        // Example: 1, 2, 3 → normal mode (only digits)
        if (preg_match('/^\d+$/', $serial)) {
            return 'normal';
        }

        return 'unknown'; // fallback
    }

    public function savePatientDetails() {
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

//        $discount = Input::get('discount');
//        $discountId = Input::get('discountId');
        $cashAmount = Input::get('split_cash_amount');
        $cardAmount = Input::get('split_card_amount');
        $voucherAmount = Input::get('vaucher_amount');

        $labLid = $_SESSION['lid'];
        $now = date('Y-m-d H:i:s');

        $useruid = Input::get('userUID');

        $user = DB::table('user')->where('uid', $useruid)->first();
        $packageIdOnly = Input::get('packageIdOnly');

        $testData = Input::get('test_data');
        $refId = Input::get('ref');

        $reference = DB::table('refference')->where('idref', $refId)->first();
        $refName = $reference ? $reference->name : null;
        $inv_remark = Input::get('inv_remark');

        if ($user) {
            $patientid = DB::table('patient')->insertGetId([
                'user_uid' => $user->uid,
                'age' => $years,
                'months' => $months,
                'days' => $days,
                'initials' => $initial,
                'dob' => $dob
            ]);
        } else {
            $userid = DB::table('user')->insertGetId([
                'fname' => $fname,
                'lname' => $lname,
                'tpno' => $tpno,
                'address' => $address,
                'gender_idgender' => $gender,
                'usertype_idusertype' => '2',
                'nic' => $nic
            ]);

            $patientid = DB::table('patient')->insertGetId([
                'user_uid' => $userid,
                'age' => $years,
                'months' => $months,
                'days' => $days,
                'initials' => $initial,
                'dob' => $dob
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


        if (!empty($testData) && is_array($testData) && count($testData) > 0) {
            $firstSample = $testData[0];

            if (!empty($packageIdOnly) && !empty($firstSample['sampleNo'])) {
                DB::table('invoice_has_labpackages')->insert([
                    'pcid' => $packageIdOnly,
                    'sno' => $firstSample['sampleNo'],
                    'lab_lid' => $labLid
                ]);
            }
        }


        //~~ Code of SAM ~~ Insert LPS and lps_has_test data ~~~~~~~~~~~~~~~~~~
        //identify sample number pattern 
        $mode = PatientRegistrationController::detectSerialMode($firstSample['sampleNo']);
        if ($mode == "prefix") {
            $customPrefix = substr($firstSample['sampleNo'], 0, 2);
        } else {
            $customPrefix = "";
        }

        $lpsStatus = "pending";

        $lpsidArr = PatientRegistrationController::insertRecordWithTable2Data($mode, $testData, $customPrefix, $labLid, $patientid, $refName, $refId, Input::get('type'), Input::get('fast_time'), Input::get('inv_remark'), $lpsStatus, $now);

        $retArr = explode("##", $lpsidArr);
        
        $lpsid = $retArr[0];
        $sample_date = $retArr[1];
        $sample_sno = $retArr[2];
        
        //~~~~~~~~~~~END~~~~~~~~~~~~~~~~~~~~
        
        // Inserting invoice tables
        $cashier = DB::table('user')->where('uid', '=', $userUid)->first();

        if ($cashier) {
            $cashierName = $cashier->fname . ' ' . $cashier->lname;
        } else {
            $cashierName = '';
        }

            $paymentMethodRaw = Input::get('payment_method');
            $paymentMethodMap = [
                '1' => 'cash',
                '2' => 'card',
                'credit' => 'credit',
                '3' => 'cheque',
                '6' => 'voucher',
                '5' => 'split'
            ];
            
            $paymentMethod = isset($paymentMethodMap[$paymentMethodRaw]) ? $paymentMethodMap[$paymentMethodRaw] : 'cash';
            
            $invoiceId = DB::table('invoice')->insertGetId([
                'lps_lpsid' => $lpsid,
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
                     'remark' => $invoiceRemark
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
            
            $getSampleNo = DB::table('lps as a')
                    ->join('invoice as b', 'a.lpsid', '=', 'b.lps_lpsid')
                    ->where('a.Lab_lid', '=', $_SESSION['lid'])
                    ->where('b.iid', '=', $invoiceId)
                    ->select('a.date', 'a.sampleNo')
                    ->first(); // get single record

                $sample_date = $getSampleNo ? $getSampleNo->date : '';
                $sample_sno = $getSampleNo ? $getSampleNo->sampleNo : '';

        return Response::json([
                    'success' => true,
                    'message' => 'Patient details saved successfully',
                    'datainv' => $sample_sno . '###' . $sample_date
        ]);


    }


    public function checkSampleNo() {
        $sampleNo = Input::get('sample_no');
        $patientDate = Input::get('patientDate');
        $labid = $_SESSION['lid'];

        // Search in lps table for same sample number and date
        $exists = DB::table('lps')
                ->where('sampleNo', $sampleNo)
                ->where('Lab_lid', $labid)
                ->whereDate('date', '=', $patientDate)
                ->exists(); // true or false

        return Response::json(['exists' => $exists]);
    }

    public function getSampleTestData() {
        $sampleNo = Input::get('sampleNo');
        $date = Input::get('date');

        // Retrieve all lps records matching the sampleNo, date, and Lab ID
        $lpsRecords = DB::table('lps as a')
                ->join('Testgroup as b', 'a.Testgroup_tgid', '=', 'b.tgid')
                ->leftJoin('refference as r', 'a.refference_idref', '=', 'r.idref')
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
                        'r.name as ref_name',
                        'a.specialnote',
                        'r.code',
                        'a.status'
                )
                ->get();

        if (empty($lpsRecords)) {
            return Response::json(['success' => false, 'message' => 'Sample not found']);
        }


        // Collect invoice payments for all lps records
        $invoicePayments = [];
        foreach ($lpsRecords as $record) {
            $payments = DB::table('lps as a')
                    ->join('invoice as b', 'a.lpsid', '=', 'b.lps_lpsid')
                    ->join('invoice_payments as c', 'b.iid', '=', 'c.invoice_iid')
                    ->where('a.lpsid', $record->lpsid)
                    ->select(
                            'c.ipid',
                            'c.amount',
                            'c.paymethod',
                            'c.invoice_iid',
                            'b.paymentmethod'
                    )
                    ->get();

            foreach ($payments as $payment) {
                $invoicePayments[] = $payment;
            }
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
                ->leftJoin('Discount as d', 'i.Discount_did', '=', 'd.did')
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
                        'i.multiple_delivery_methods',
                        'd.value',
                        'd.did',
                        'i.remark'
                )
                ->first();

        $packageData = DB::table('lps as a')
                ->join('invoice_has_labpackages as c', 'a.sampleNo', '=', 'c.sno')
                ->join('labpackages as b', 'b.idlabpackages', '=', 'c.pcid')
                ->where('a.date', $date)
                ->where('a.Lab_lid', $_SESSION['lid'])
                ->where('a.sampleNo', '=', $sampleNo)
                ->select(
                        'b.name',
                        'a.date',
                        'a.sampleNo',
                        'b.idlabpackages'
                )
                ->first();

        if (!$packageData) {
            $packageData = (object) ['name' => ''];
        }

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
                'lpsid' => $test->lpsid,
                'tgid' => $test->tgid,
                'group' => $test->group,
                'price' => floatval($test->price),
                'time' => $test->time,
                'f_time' => 0,
                'priority' => $test->urgent_sample,
                'type' => $test->type,
            ];
        }

        return Response::json([
                    'success' => true,
                    'data' => [
                        'patient' => $patientData,
                        'tests' => $testData,
                        'invoice' => $invoiceData,
                        'lpsRecords' => $lpsRecords,
                        'invoicePayments' => $invoicePayments,
                        'package' => $packageData
                    ]
        ]);
    }

    public function getSearchSampleData() {
        $searchDate = Input::get('searchDate');
        $searchSampleNo = Input::get('searchSampleNo');
        $ignoredate = Input::get('ignoreDate');

        if ($ignoredate == "true") {
            $searchDateResult = DB::table('lps')
                    ->where('sampleNo', 'like', $searchSampleNo . '%')
                    ->where('Lab_lid', $_SESSION['lid'])
                    ->where('date', '>', '1990-01-01')
                    ->limit(1)
                    ->select('date')
                    ->first();  // execute the query

            if ($searchDateResult) {
                $searchDate = $searchDateResult->date;  // extract date value
            } else {
                $searchDate = null;  // or set a default if no record found
            }
        }
        // Retrieve all lps records matching the sampleNo, date, and Lab ID
        $lpsRecords = DB::table('lps as a')
                ->join('Testgroup as b', 'a.Testgroup_tgid', '=', 'b.tgid')
                ->leftJoin('refference as r', 'a.refference_idref', '=', 'r.idref')
                ->join('patient as p', 'a.patient_pid', '=', 'p.pid')
                ->where('a.sampleNo', 'like', $searchSampleNo . '%')
                ->where('a.date', $searchDate)
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
                        'r.name as ref_name',
                        'a.specialnote',
                        'r.code',
                        'a.status'
                )
                ->get();

        if (empty($lpsRecords)) {
            return Response::json(['success' => false, 'message' => 'Sample not Found']);
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
                ->leftJoin('Discount as d', 'i.Discount_did', '=', 'd.did')
                ->where('a.sampleNo', 'like', $searchSampleNo . '%')
                ->where('a.date', $searchDate)
                ->where('a.Lab_lid', $_SESSION['lid'])
                ->select(
                        'i.iid',
                        'i.total',
                        'i.paid',
                        'i.gtotal',
                        'i.discount',
                        'i.status',
                        'i.paymentmethod',
                        'i.multiple_delivery_methods',
                        'd.value',
                        'd.did',
                        'a.sampleNo',
                        'i.date'
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
                'lpsid' => $test->lpsid,
                'tgid' => $test->tgid,
                'group' => $test->group,
                'price' => floatval($test->price),
                'time' => $test->time,
                'f_time' => 0,
                'priority' => $test->urgent_sample,
                'type' => $test->type,
            ];
        }

        return Response::json([
                    'success' => true,
                    'data' => [
                        'patient' => $patientData,
                        'tests' => $testData,
                        'invoice' => $invoiceData,
                        'lpsRecords' => $lpsRecords
                    ]
        ]);
    }

    public function getTestCodes() {
        try {
            $labLid = $_SESSION['lid'];
            $testCodes = DB::table('Testgroup as t')
                    ->join('lps as l', 't.tgid', '=', 'l.Testgroup_tgid')
                    ->select('t.testCode', 't.tgid', 't.name as group', 't.price', 't.testingtime')
                    ->where('l.lab_lid', '=', $labLid)
                    ->groupBy('t.tgid', 't.testCode', 't.name', 't.price', 't.testingtime')
                    ->orderBy(DB::raw('COUNT(*)'), 'DESC')
                    ->limit(12)
                    ->get();

            return Response::json([
                        'success' => true,
                        'data' => [
                            'testCodes' => $testCodes
                        ]
            ]);
        } catch (Exception $e) {
            return Response::json([
                        'success' => false,
                        'message' => $e->getMessage()
            ]);
        }
    }

    public function getSingleBarcode() {
        $tgid = Input::get('tgid'); // test group id
        $sno = Input::get('sno');
        // $specialBarcodes = Input::get('specialBarcodes'); 
        $labLid = $_SESSION['lid']; // assuming you store it in session
        $containerId = '';
        $containerIdList = [];

        // Get container id
        $result = DB::select("SELECT sample_containers_scid FROM Testgroup WHERE tgid = ? AND Lab_lid = ? LIMIT 1", [$tgid, $labLid]);
        if (!empty($result)) {
            $containerId = $result[0]->sample_containers_scid;
        }

        $barcodeData = [];

        // if (!empty($specialBarcodes)) {
        //     $TGID = '';
        //     $barcodeSuffix = '';
        //     $barcodeSuffixN = '';
        //     $count = 0;
        //     // $specialBarcodeExists = false;
        //     foreach ($specialBarcodes as $entry) {
        //         list($id, $suffix) = explode(':', $entry);
        //         if ($tgid == $id) {
        //             // $specialBarcodeExists = true;
        //             $TGID = $id;
        //             $barcodeSuffix = $suffix;
        //             break;
        //         }
        //     }
        //     // if ($specialBarcodeExists) {
        //     //     $tests = DB::select("SELECT a.name FROM test a, Lab_has_test b WHERE Testgroup_tgid = ? AND a.tid = b.test_tid", [$TGID]);
        //     //     foreach ($tests as $test) {
        //     //         $barcodeSuffixN = $count == 0 ? $barcodeSuffix : $barcodeSuffix . $count;
        //     //         $barcodeData[] = [
        //     //             'test' => $test->name,
        //     //             'sample_no' => $sno . '-' . $barcodeSuffixN,
        //     //             'container_id' => $containerId
        //     //         ];
        //     //         $count++;
        //     //     }
        //     // } else {
        //     //     $barcodeData[] = [
        //     //         'test' => Input::get('testGroupName'),
        //     //         'sample_no' => $sno,
        //     //         'container_id' => $containerId
        //     //     ];
        //     // }
        // } else {
        //     $barcodeData[] = [
        //         'test' => Input::get('testGroupName'),
        //         'sample_no' => $sno,
        //         'container_id' => $containerId
        //     ];
        // }

        return Response::json([
                    'success' => true,
                    'barcodes' => $containerId
        ]);
    }

    public function getPatientDetailsBySample() {
        $sampleNO = Input::get('sampleNO');
        $direction = Input::get('direction');
        $labLid = $_SESSION['lid'];
        $date = Input::get('searachDate');

        $lpsQuery = DB::table('lps as a')
                ->join('Testgroup as b', 'a.Testgroup_tgid', '=', 'b.tgid')
                ->leftJoin('refference as r', 'a.refference_idref', '=', 'r.idref')
                ->join('patient as p', 'a.patient_pid', '=', 'p.pid')
                ->where('a.date', $date)
                ->where('a.Lab_lid', $labLid)
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
                'r.name as ref_name',
                'a.specialnote',
                'r.code',
                'a.status'
        );

        if ($direction == 'back') {
            $lpsQuery->where('a.sampleNo', '<', $sampleNO)
                    ->groupBy('a.patient_pid')
                    ->orderBy('a.sampleNo', 'DESC')
                    ->limit(1);
        } else if ($direction == 'front') {
            $sampleNOADD = $sampleNO + 1;
            $lpsQuery->where('a.sampleNo', '=', $sampleNOADD)
                    ->groupBy('a.patient_pid')
                    ->orderBy('a.sampleNo', 'ASC')
                    ->limit(1);
        } else {

            $lpsQuery->where('a.sampleNo', 'like', $sampleNO . '%');
        }

        $lpsRecords = $lpsQuery->get();

        if (empty($lpsRecords)) {
            return Response::json(['success' => false, 'message' => 'Sample not found']);
        }


        $firstLps = $lpsRecords[0];
        $patientId = $firstLps->patient_pid;

        $patientData = DB::table('patient as p')
                ->join('user as u', 'p.user_uid', '=', 'u.uid')
                ->where('p.pid', $patientId)
                ->select('u.fname', 'u.lname', 'u.nic', 'u.address', 'u.gender_idgender', 'u.tpno', 'p.age', 'p.months', 'p.days', 'p.initials', 'p.dob')
                ->first();

        $invoiceData = DB::table('invoice as i')
                ->join('lps as a', 'i.lps_lpsid', '=', 'a.lpsid')
                ->leftJoin('Discount as d', 'i.Discount_did', '=', 'd.did')
                ->where('a.sampleNo', 'like', $sampleNO . '%')
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
                        'i.multiple_delivery_methods',
                        'd.value',
                        'd.did',
                        'a.sampleNo',
                        'i.date'
                )
                ->first();

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
                'lpsid' => $test->lpsid,
                'tgid' => $test->tgid,
                'group' => $test->group,
                'price' => floatval($test->price),
                'time' => $test->time,
                'f_time' => 0,
                'priority' => $test->urgent_sample,
                'type' => $test->type,
            ];
        }

        return Response::json([
                    'success' => true,
                    'data' => [
                        'patient' => $patientData,
                        'tests' => $testData,
                        'invoice' => $invoiceData,
                        'lpsRecords' => $lpsRecords
                    ]
        ]);
    }

    public function getLastPatientDetails() {
        $labLid = $_SESSION['lid'];
        $date = Input::get('patientDate');

        $result = DB::table('lps as a')
                ->leftjoin('patient as b', 'a.patient_pid', '=', 'b.pid')
                ->leftjoin('user as c', 'b.user_uid', '=', 'c.uid')
                ->leftjoin('refference as d', 'a.refference_idref', '=', 'd.idref')
                ->select('a.refby', 'd.idref', 'd.code', 'b.initials', 'c.fname', 'c.lname', 'b.age', 'c.tpno', 'c.address', 'a.sampleNO', 'b.months', 'b.days')
                ->where('a.Lab_lid', '=', $labLid)
                ->where('a.date', '=', $date)
                ->orderBy('a.lpsid', 'DESC')
                ->first();

        if ($result) {
            return Response::json([
                        'status' => 'success',
                        'data' => $result
            ]);
        } else {
            return Response::json([
                        'status' => 'not_found',
                        'message' => 'No patient found for the selected date.'
            ]);
        }
    }

    public function updatePatientDetails() {
        $sampleNO = Input::get('sampleNO');
        $labLid = $_SESSION['lid'];
        $date = Input::get('patientDate');

        // Find patient via sample number
        $lps = DB::table('lps')
                ->where('sampleNO', '=', $sampleNO)
                ->where('Lab_lid', '=', $labLid)
                ->where('date', '=', $date)
                ->first();

        if (!$lps) {
            return Response::json([
                        'status' => 'error',
                        'message' => 'Sample not found.'
            ]);
        }

        $refcode = Input::get('refcode');
        $refId = Input::get('ref');
        $refName = Input::get('refDropdown');

        // Update lps
        DB::table('lps')
                ->where('lpsid', '=', $lps->lpsid)
                ->update([
                    'refby' => $refName,
                    'refference_idref' => $refId,
                        // 'refcode' => $refcode
        ]);

        // Update patient
        DB::table('patient')
                ->where('pid', '=', $lps->patient_pid)
                ->update([
                    'initials' => Input::get('initials'),
                    'age' => Input::get('years'),
                    'months' => Input::get('months'),
                    'days' => Input::get('days')
        ]);

        // Update user
        $patient = DB::table('patient')->where('pid', '=', $lps->patient_pid)->first();
        if ($patient) {
            DB::table('user')
                    ->where('uid', '=', $patient->user_uid)
                    ->update([
                        'fname' => Input::get('fname'),
                        'lname' => Input::get('lname'),
                        'tpno' => Input::get('tpno'),
                        'address' => Input::get('address'),
                        'nic' => Input::get('nic')
            ]);
        }

        return Response::json([
                    'status' => 'success',
                    'message' => 'Patient details updated successfully'
        ]);
    }

    public function getRefName() {
        $keyword = Input::get('keyword');
        $labLid = $_SESSION['lid'];

        $results = DB::table('refference')
                ->where('lid', '=', $labLid)
                ->whereNotNull('name')
                ->where('name', 'LIKE', '%' . $keyword . '%')
                ->orderBy('name', 'asc')
                ->get(['idref', 'name', 'code']);

        return Response::json($results);
    }

    public function getRefByCode() {
        $code = Input::get('code');
        $labLid = $_SESSION['lid'];

        $ref = DB::table('refference')
                ->where('lid', '=', $labLid)
                ->where('code', '=', $code)
                ->select('idref', 'name')
                ->first();

        return Response::json($ref);
    }

    public function getTestParametersByTGID() {
        $tgid = Input::get('tgid');
        $labLid = $_SESSION['lid'];

        $results = DB::table('Lab_has_test')
                ->select('reportname', 'orderno', 'Testgroup_tgid')
                ->where('Testgroup_tgid', '=', $tgid)
                ->where('Lab_lid', '=', $labLid)
                ->get();

        return Response::json($results);
    }

    public function removeBarcode() {
        // Get input data
        $sampleNo = Input::get('sampleNo');
        $date = Input::get('date');
        $tgid = Input::get('tgid');

        // Validate input
        if (empty($sampleNo) || empty($date)) {
            return Response::json([
                        'success' => false,
                        'message' => 'Sample number or date is missing'
                            ], 400);
        }


        try {
            // Update status to 'pending' in lps table
            $updated = DB::table('lps')
                    ->where('sampleNo', 'like', $sampleNo . '%')
                    ->where('Testgroup_tgid', $tgid)
                    ->where('date', $date)
                    ->update(['status' => 'pending']);

            if ($updated) {
                return Response::json([
                            'success' => true,
                            'message' => 'Status updated to pending'
                ]);
            } else {
                return Response::json([
                            'success' => false,
                            'message' => 'No matching record found'
                                ], 404);
            }
        } catch (Exception $e) {
            return Response::json([
                        'success' => false,
                        'message' => 'Database error: ' . $e->getMessage()
                            ], 500);
        }
    }

    public function barcodeFeatureChecking() {
        if (!isset($_SESSION['lid'])) {
            return Response::json(['error' => 'Session expired.'], 401);
        }

        $labLid = $_SESSION['lid'];

        $barcodeFeature = DB::table('Lab_features')
                ->where('Lab_lid', '=', $labLid)
                ->where('features_idfeatures', '=', 20)
                ->exists();

        return Response::json(['hasFeature' => $barcodeFeature]);
    }

    public function reportingFeatureChecking() {
        if (!isset($_SESSION['lid'])) {
            return Response::json(['error' => 'Session expired.'], 401);
        }

        $labLid = $_SESSION['lid'];

        $reportFeature = DB::table('Lab_features')
                ->where('Lab_lid', '=', $labLid)
                ->where('features_idfeatures', '=', 9)
                ->exists();

        return Response::json(['hasFeature' => $reportFeature]);
    }

    public function emailFeatureChecking() {
        if (!isset($_SESSION['lid'])) {
            return Response::json(['error' => 'Session expired.'], 401);
        }

        $labLid = $_SESSION['lid'];

        $emailFeature = DB::table('Lab_features')
                ->where('Lab_lid', '=', $labLid)
                ->where('features_idfeatures', '=', 8)
                ->exists();

        return Response::json(['hasEmailFeature' => $emailFeature]);
    }

    public function whatsappFeatureChecking() {
        if (!isset($_SESSION['lid'])) {
            return Response::json(['error' => 'Session expired.'], 401);
        }

        $labLid = $_SESSION['lid'];

        $whatsappFeature = DB::table('Lab_features')
                ->where('Lab_lid', '=', $labLid)
                ->where('features_idfeatures', '=', 21)
                ->exists();

        return Response::json(['hasWhatsappFeature' => $whatsappFeature]);
    }

    public function TokenFeatureChecking() {
        if (!isset($_SESSION['lid'])) {
            return Response::json(['error' => 'Session expired.'], 401);
        }

        $labLid = $_SESSION['lid'];

        $TokenFeature = DB::table('addpatientconfigs')
                ->where('lab_lid', '=', $labLid)
                ->where('registerbytoken', '=', 1)
                ->exists();

        return Response::json(['hasTokenFeature' => $TokenFeature]);
    }

//    public function reportingFeatureChecking()
//     {
//         if (!isset($_SESSION['lid'])) {
//             return Response::json(['error' => 'Session expired.'], 401);
//         }
//         $labLid = $_SESSION['lid']; 
//         $hasEmail = DB::table('Lab_features')
//             ->where('Lab_lid', $labLid)
//             ->where('features_idfeatures', 8)
//             ->exists();
//         $hasSMS = DB::table('Lab_features')
//             ->where('Lab_lid', $labLid)
//             ->where('features_idfeatures', 9)
//             ->exists();
//         $hasWhatsapp = DB::table('Lab_features')
//             ->where('Lab_lid', $labLid)
//             ->where('features_idfeatures', 21)
//             ->exists();
//         return Response::json([
//             'hasEmail' => $hasEmail,
//             'hasSMS' => $hasSMS,
//             'hasWhatsapp' => $hasWhatsapp
//         ]);
//     }


    public function patientDetailsEditingFeatureChecking() {

        $userUid = $_SESSION['uid'];

        $patientDetailsEditingFeature = DB::table('privillages')
                ->where('user_uid', '=', $userUid)
                ->where('options_idoptions', '=', 13)
                ->exists();

        return Response::json(['hasPdetailsUpdateFeature' => $patientDetailsEditingFeature]);
    }



    public function getReferenceDetails()
    {
        try {
            $referenceId = Input::get('reference_id'); 

            if (!$referenceId) {
                return Response::json([], 200);
            }

        
            $result = DB::table('refference')
                ->where('idref', $referenceId)
                ->select('idref', 'code', 'name') 
                ->first();

            if ($result) {
                return Response::json($result, 200);
            } else {
                return Response::json([], 200);
            }

        } catch (Exception $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
    }

}





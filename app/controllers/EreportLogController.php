<?php

use Illuminate\Support\Facades\DB;

if (!isset($_SESSION)) {
    session_start();
}

//error_reporting(0);

class EreportLogController extends Controller{
    // Function to get all details 
//    public function getEreportLogData()
//     {
//         $lid = $_SESSION['lid']; 
//         $today = date('Y-m-d');

//         $data = DB::table('ereport_log as a')
//             ->join('user as b', 'a.User_uid', '=', 'b.uid')
//             ->join('lps as c', 'a.Lps_lpsid', '=', 'c.lpsid')
//             ->join('Testgroup as d', 'c.Testgroup_tgid', '=', 'd.tgid')
//             ->where('a.lid', '=', $lid)
//             ->where('a.date', '=', $today)
//             ->select('a.date', 'a.time', 'b.fname', 'b.mname', 'c.sampleNo', 'd.name as test_name', 'a.status', 'a.source')
//             ->orderBy('a.time', 'desc')
//             ->get();

//         return Response::json($data);
//     }


    public function getEreportLogData()
    {
        $lid = $_SESSION['lid'];

        $date       = Input::get('date');
        $user       = Input::get('user');
        $sampleNo   = Input::get('sampleNo');
        $testName   = Input::get('testName');
        $status     = Input::get('status');

        $query = DB::table('ereport_log as a')
            ->join('user as b', 'a.User_uid', '=', 'b.uid')
            ->join('lps as c', 'a.Lps_lpsid', '=', 'c.lpsid')
            ->join('Testgroup as d', 'c.Testgroup_tgid', '=', 'd.tgid')
            ->where('a.lid', '=', $lid);

        if ($date !== null && $date !== '') {
            $query->where('a.date', '=', $date);
        }

        if ($user !== '%' && $user !== null && $user !== '') {
            $query->where(DB::raw("CONCAT(b.fname, ' ', b.lname)"), '=', $user);
        }

        if ($sampleNo !== null && $sampleNo !== '') {
            $query->where('c.sampleNo', 'like', "%$sampleNo%");
        }

        if ($testName !== '%' && $testName !== null && $testName !== '') {
            $query->where('d.tgid', '=', $testName);
        }

        if ($status !== '%' && $status !== null && $status !== '') {
            $query->where('a.status', '=', $status);
        }

        $data = $query->select(
            'a.date', 'a.time', 'b.fname', 'b.mname',
            'c.sampleNo', 'd.name as test_name', 'a.status', 'a.source'
        )->orderBy('a.time', 'desc')->get();

        return Response::json($data);
    }



        

   public function save_EreportLog()
    {
    
        $date       = Input::get('date');
        $time       = Input::get('time');
        $method     = Input::get('method');
        $lps_lpsid  = Input::get('Lps_lpsid');
        $source     = Input::get('source');
        $status     = Input::get('status');


        $user_uid   = $_SESSION['uid'];
        $lid        = $_SESSION['lid'];

    
        if (!$date || !$time || !$method || !$lps_lpsid || !$source || !$status || !$user_uid || !$lid) {
             return Response::json([
                'success' => false,
                'message' => 'Invalid input'
            ]);
        }


        DB::statement("
            INSERT INTO ereport_log (`Date`, `time`, `method`, `Lps_lpsid`, `User_uid`, `source`, `status`, `lid`)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)", 
            [$date, $time, $method, $lps_lpsid, $user_uid, $source, $status, $lid]
        );

        return Response::json([
            'success' => true,
            'message' => 'eReport Log saved successfully!'
        ]);

    }



   





}

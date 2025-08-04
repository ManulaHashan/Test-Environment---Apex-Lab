<?php

use Illuminate\Support\Facades\DB;

if (!isset($_SESSION)) {
    session_start();
}

//error_reporting(0);

class SystemChangeLogController extends Controller{
    // Function to get all details 
    // public function getChangeLogs()
    // {
    //     $lid = $_SESSION['lid']; 

    //     $logs = DB::select("
    //         SELECT a.`date`, a.`time`, a.`page`, a.`button`, a.`user_luid`, b.`fname`, c.`position`
    //         FROM change_log a
    //         JOIN labUser c ON a.`user_luid` = c.`luid`
    //         JOIN user b ON c.`user_uid` = b.`uid`
    //         WHERE a.lid = ?
    //         ORDER BY a.date DESC, a.time DESC
    //     ", [$lid]);

    //     return Response::json($logs);
    // }


        
        
    public function getChangeLogs()
    {
        $lid = $_SESSION['lid'];
        $date = Input::get('date');
        $page = Input::get('page');
        $button = Input::get('button');
        $uid = Input::get('uid');
        $fname = Input::get('fname');

        $query = DB::table('change_log as a')
            ->join('labUser as c', 'a.user_luid', '=', 'c.luid')
            ->join('user as b', 'c.user_uid', '=', 'b.uid')
            ->select('a.date','a.descreption' ,'a.time', 'a.page', 'a.button', 'a.user_luid', 'b.fname', 'b.lname', 'c.position')
            ->where('a.lid', $lid);

        if (!empty($date)) {
            $query->where('a.date', '=', $date);
        }

        if (!empty($page)) {
            $query->where('a.page', 'LIKE', '%' . $page . '%');
        }

        if (!empty($button)) {
            $query->where('a.button', 'LIKE', '%' . $button . '%');
        }

        if (!empty($uid) && $uid !== '%') {
            $query->where('b.uid', '=', $uid);
        }

        if (!empty($fname) && $fname !== '%') {
            $query->where('b.fname', 'LIKE', '%' . $fname . '%');
        }

        $results = $query->orderBy('a.date', 'desc')->orderBy('a.time', 'desc')->get();

        return Response::json($results);
    }





}

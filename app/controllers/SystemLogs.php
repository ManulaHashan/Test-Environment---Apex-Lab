<?php

//session_start();
if (!isset($_SESSION)) {
    session_start();
}
date_default_timezone_set('Asia/Colombo');

class SystemLogs extends Controller {

    public function saveChangeLog($lid, $user_uid, $page, $button, $descreption) {

        $date = date("Y-m-d");
        $time = date("H:i:s");

        DB::table('change_log')->insert(['lid' => $lid, 'date' => $date, 'time' => $time, 'page' => $page, 'button' => $button, 'descreption' => $descreption, 'user_luid' => $user_uid]);

    }

}


?>

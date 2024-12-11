<?php

use Illuminate\Database\Eloquent\Model;

require 'vendor/autoload.php'; 

class EmailManager extends Model {

    static function sendEmail($emailAdd, $msg, $lid) {
        $Resultx = DB::select("SELECT name FROM Lab where lid = '".$lid."'");
        foreach ($Resultx as $resx) {
            $labName = $resx->name;
        }

        $to      = $emailAdd;
        $subject = $labName.' - Report';
        $message = $msg;

        $headers = 'From: reports@mlwslab.com' . "\r\n" .
        'Reply-To: reports@mlwslab.com' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

        $return = mail($to, $subject, $message, $headers);

        return $return;
    }

    static function saveLOG($tpno, $msg, $lid) {
        DB::statement("insert into smslog(tpno,msg,drep,lab_lid) values('" . $tpno . "','" . $msg . "','" . $tpno . "','" . $lid . "')");
    }

}

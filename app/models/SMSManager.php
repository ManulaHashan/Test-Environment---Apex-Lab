<?php

use Illuminate\Database\Eloquent\Model;

require 'vendor/autoload.php';

class SMSManager extends Model {

    static function sendSMS($tpno, $msg, $delivery, $lid) {
        $Resultx = DB::select("SELECT * FROM sms_profile where lab_lid = '" . $lid . "'");
        foreach ($Resultx as $resx) {
            $username = $resx->username;
            $password = $resx->password;
            $src = $resx->src;
        }

        $url = "http://sms.textware.lk:5000/sms/send_sms.php?username=" . $username . "&password=" . $password . "&src=" . $src . "&dst=" . $tpno . "&msg=" . $msg . "&dr=" . $delivery;

        $url = str_replace(" ", "+", $url);
        $url = str_replace("\n", "%0A", $url);

//        $client = new \GuzzleHttp\Client();
//        $response = $client->request('POST', $url);
//
//        $x = $response->getStatusCode(); # 200
//        echo $response->getHeaderLine('content-type'); # 'application/json; charset=utf8'
//        echo $response->getBody(); # '{"id": 1420053, "name": "guzzle", ...}'

        return $url;
    }

    static function saveLOG($tpno, $msg, $lid, $drep) {
        DB::statement("insert into smslog(tpno,msg,drep,lab_lid) values('" . $tpno . "','" . $msg . "','" . $tpno . "','" . $lid . "')");
    }

}

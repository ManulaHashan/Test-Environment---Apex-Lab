<?php

if (!isset($_SESSION)) {
    session_start();
}

class LabController extends Controller {

    function RegisterLab() {
        $name = Input::get('name');
        $cus = Input::get('cus');
        $country = Input::get('country');
        $tpno = Input::get('tpno');
        $ownerName = Input::get('ownername');
        $ownerNo = Input::get('ownerno');
        $email = Input::get('email');

        $add1 = Input::get('add1');
        $add2 = Input::get('add2');
        $address = $add1 . "," . $add2;

        $status = "pending";

//echo $name." ".$cus." ".$country." ".$tpno." ".$ownerName." ".$ownerNo." ".$email." ".$add1." ".$add2." ".$address." ".$status;
//check lab exsist!
        if (Lab::where('name', $name)->where('tpno', $tpno)->exists()) {
            $error = "Laboratory already exsists in our system.Please contact site administrator!";
            return View::make('LabRegister', compact("error"));
        } else {
//get currency or save it     
            if (Currency::where('symble', $cus)->exists()) {
                $currencyID = Currency::where('symble', $cus)->first()->idCurrency;
            } else {
                $Cur = new Currency;
                $Cur->symble = $cus;
                $Cur->type = $cus;
                $Cur->status = 1;
                $Cur->save();

                $currencyID = $Cur->idCurrency;
            }

            $Lab = new Lab;

            $Lab->name = $name;
            $Lab->Address = $address;
            $Lab->country = $country;
            $Lab->email = $email;
            $Lab->tpno = $tpno;
            $Lab->ownername = $ownerName;
            $Lab->ownertpno = $ownerNo;
            $Lab->status = $status;
            $Lab->rdate = date('Y-m-d');
            $Lab->rtime = date('h:i');
            $Lab->Currency_idCurrency = $currencyID;
            $Lab->logo = "none";

            $Lab->save();
            $LabID = $Lab->lid;

            $_SESSION['lid'] = $LabID;

            return View::make('AdminReg');
        }
    }

    function SelectPackege() {
        $package = Input::get('packege');
        $webpage = Input::get('webpage');

        if ($webpage == "on") {
            $this->addFeatureToLab(11);
        }

        //to payment gatway use
        $_SESSION['packegeID'] = $package;

        //add selected package features to lab //1 basic, 2 medium, 3ultimate, 4 trail for basic
        $query = "select features_idfeatures from features_packeges where packeges_idpackeges = '" . $package . "'";
        $result = DB::select($query);
        //$result = DB::table('features_packeges')->where('packeges_idpackeges', $package);
        foreach ($result as $res) {
            $this->addFeatureToLab($res->features_idfeatures);
        }
        return View::make('mlwsAgreement');
    }

    function addFeatureToLab($fid) {
        DB::table('Lab_features')->where(['Lab_lid' => $_SESSION['lid'], 'features_idfeatures' => $fid])->delete();
        DB::table('Lab_features')->insert(['Lab_lid' => $_SESSION['lid'], 'features_idfeatures' => $fid]);

        $opResult = DB::select("select idoptions from options");
        foreach ($opResult as $option) {
            DB::table('Lab_has_options')->insert(['Lab_lid' => $_SESSION['lid'], 'options_idoptions' => $option->idoptions]);
        }
    }

}

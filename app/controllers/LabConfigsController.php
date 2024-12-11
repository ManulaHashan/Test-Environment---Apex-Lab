<?php

if (!isset($_SESSION)) {
    session_start();
}

class LabConfigsController extends Controller {

    function updatepatientaddformconfigs() {

        if (Input::get('submit') !== null) {

            $result = DB::select("select id from addpatientconfigs where lab_lid = '" . $_SESSION['lid'] . "'");
            foreach ($result as $res) {
                DB::delete("delete from addpatientconfigs where id = '" . $res->id . "'");
            }

            if (Input::get('tpno') != null) {
                $tpno = "1";
            } else {
                $tpno = "0";
            }
            if (Input::get('address') != null) {
                $address = "1'";
            } else {
                $address = "0";
            }
            if (Input::get('refby') != null) {
                $refby = "1";
            } else {
                $refby = "0";
            }
            if (Input::get('type') != null) {
                $type = "1";
            } else {
                $type = "0";
            }
            if (Input::get('viewinvoice') != null) {
                $viewinvoice = "1";
            } else {
                $viewinvoice = "0";
            }
            if (Input::get('tot') != null) {
                $tot = "1";
            } else {
                $tot = "0";
            }

            if (Input::get('discount') != null) {
                $discount = "1";
            } else {
                $discount = "0";
            }

            if (Input::get('gtot') != null) {
                $gtot = "1";
            } else {
                $gtot = "0";
            }

            if (Input::get('paymeth') != null) {
                $paymeth = "1";
            } else {
                $paymeth = "0";
            }

            if (Input::get('payment') != null) {
                $payment = "1";
            } else {
                $payment = "0";
            }

            if (Input::get('directresultenter') != null) {
                $directresultenter = "1";
            } else {
                $directresultenter = "0";
            }

            if (Input::get('patientsuggestion') != null) {
                $patientsuggestion = "1";
            } else {
                $patientsuggestion = "0";
            }

            if (Input::get('focusonpayment') != null) {
                $focusonpayment = "1";
            } else {
                $focusonpayment = "0";
            }
            
            if (Input::get('patientinitials') != null) {
                $patientinitials = "1";
            } else {
                $patientinitials = "0";
            }

            $refbydv = Input::get('refbydv');
            $typedv = Input::get('typedv');
            $genderdv = Input::get('genderdv');
            $discountdv = Input::get('discountdv');
            $paymethdv = Input::get('paymethdv');
            $printinvoicedv = Input::get('printinvoicedv');

            DB::table('addpatientconfigs')->insertGetId(['lab_lid' => $_SESSION['lid'], 'tpno' => $tpno, 'address' => $address,
                'refby' => $refby, 'type' => $type, 'viewinvoice' => $viewinvoice, 'tot' => $tot, 'discount' => $discount, 'gtot' => $gtot,
                'paymeth' => $paymeth, 'payment' => $payment, 'directresultenter' => $directresultenter, 'patientsuggestion' => $patientsuggestion,
                'refbydv' => $refbydv, 'typedv' => $typedv, 'genderdv' => $genderdv, 'discountdv' => $discountdv, 'paymethdv' => $paymethdv,
                'printinvoicedv' => $printinvoicedv, 'focusonpayment' => $focusonpayment, 'patientinitials' => $patientinitials]);

            return View::make('WiaddPFormConfigs')->with('msg', 'Configurations Updated!');
        } else {
            return View::make('WiaddPFormConfigs');
        }
    }

    function updatereportconfigs() {
        if (Input::get('submit') !== null) {
            $result = DB::select("select id from reportconfigs where lab_lid = '" . $_SESSION['lid'] . "'");
            foreach ($result as $res) {
                DB::delete("delete from reportconfigs where id = '" . $res->id . "'");
            }

            if (Input::get('header') != null) {
                $header = "1";
            } else {
                $header = "0";
            }
            if (Input::get('footer') != null) {
                $footer = "1'";
            } else {
                $footer = "0";
            }
            if (Input::get('pageheading') != null) {
                $pageheading = "1";
            } else {
                $pageheading = "0";
            }
            if (Input::get('date') != null) {
                $date = "1";
            } else {
                $date = "0";
            }
            if (Input::get('sign') != null) {
                $sign = "1";
            } else {
                $sign = "0";
            }
            if (Input::get('confidential') != null) {
                $confidential = "1";
            } else {
                $confidential = "0";
            }

            if (Input::get('fontitelic') != null) {
                $fontitelic = "1";
            } else {
                $fontitelic = "0";
            }

            if (Input::get('agelabel') != null) {
                $agelabel = "1";
            } else {
                $agelabel = "0";
            }

            if (Input::get('headerdefault') != null) {
                $headerdefault = "1";
            } else {
                $headerdefault = "0";
            }
            
            if (Input::get('valuestate') != null) {
                $valuestate = "1";
            } else {
                $valuestate = "0";
            }
            
            if (Input::get('viewsample') != null) {
                $viewsample = "1";
            } else {
                $viewsample = "0";
            }
            
            if (Input::get('viewregdate') != null) {
                $viewregdate = "1";
            } else {
                $viewregdate = "0";
            }
            
            if (Input::get('viewinitials') != null) {
                $viewInitials = "1";
            } else {
                $viewInitials = "0";
            }
            
            if (Input::get('viewspecialnote') != null) {
                $viewspecialnote = "1";
            } else {
                $viewspecialnote = "0";
            }
            
            if (Input::get('enableblooddrew') != null) {
                $enableblooddrew = "1";
            } else {
                $enableblooddrew = "0";
            }
            
            if (Input::get('enablecollected') != null) {
                $enablecollected = "1";
            } else {
                $enablecollected = "0";
            }
            
            $HeaderUrl = Input::get('headerurllbl');
            $FooterUrl = Input::get('footerurllbl');

            //uploadImage
            //header
            $target_dir = "images/LabHeadersFooters/";
            $target_file = $target_dir . "header" . $_SESSION['lid'];

            $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
            if (move_uploaded_file($_FILES["headerurl"]["tmp_name"], $target_file)) {
                $uploadOk = 1;
                $HeaderUrl = $target_file;
            }
            
            //footer
            $target_dir = "images/LabHeadersFooters/";
            $target_file = $target_dir . "footer" . $_SESSION['lid'];

            $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
            if (move_uploaded_file($_FILES["footerurl"]["tmp_name"], $target_file)) {
                $uploadOk = 1;
                $FooterUrl = $target_file;
            }            
            //
            
            
            DB::table('reportconfigs')->insertGetId(['lab_lid' => $_SESSION['lid'], 'header' => $header, 'footer' => $footer,
                'pageheading' => $pageheading, 'date' => $date, 'sign' => $sign, 'confidential' => $confidential, 'fontitelic' => $fontitelic, 
                'agelabel' => $agelabel,'headerdefault' => $headerdefault, 'headerurl' => $HeaderUrl, 'footerurl' => $FooterUrl,
                'valuestate' => $valuestate,'viewsno' => $viewsample,'viewregdate' => $viewregdate,'viewinitials' => $viewInitials,'viewspecialnote' => $viewspecialnote,'enableblooddrew' => $enableblooddrew,'enablecollected' => $enablecollected]);

            return View::make('WireportConfigs')->with('msg', 'Configurations Updated!');
        } else {
            return View::make('WireportConfigs');
        }
    }

}

?>
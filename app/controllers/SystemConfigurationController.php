<?php

if (!isset($_SESSION)) {
    session_start();
}
date_default_timezone_set('Asia/Colombo');

class SystemConfigurationController extends Controller {

    public function getAllAddPatientConfigs()
    {
        $labLid = isset($_SESSION['lid']) ? $_SESSION['lid'] : null;

        if (!$labLid) {
            return Response::json([
                'status' => 'error',
                'message' => 'Lab LID not found in session'
            ], 400);
        }

        try {
            $configs = DB::table('addpatientconfigs')
                ->where('lab_lid', $labLid)
                ->get();

            return Response::json([
                'status' => 'success',
                'data' => $configs
            ]);
        } catch (Exception $e) {
            return Response::json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function updateAddPatientConfig()
    {
        try {
            $id = Input::get('id');
            $lab_lid = Input::get('lab_lid');

            DB::table('addpatientconfigs')
                ->where('id', $id)
                ->where('lab_lid', $lab_lid)
                ->update([
                    'tpno' => Input::get('tpno'),
                    'address' => Input::get('address'),
                    'refby' => Input::get('refby'),
                    'refbydv' => Input::get('refbydv'),
                    'type' => Input::get('type'),
                    'typedv' => Input::get('typedv'),
                    'genderdv' => Input::get('genderdv'),
                    'viewinvoice' => Input::get('viewinvoice'),
                    'tot' => Input::get('tot'),
                    'discount' => Input::get('discount'),
                    'discountdv' => Input::get('discountdv'),
                    'gtot' => Input::get('gtot'),
                    'paymeth' => Input::get('paymeth'),
                    'paymethdv' => Input::get('paymethdv'),
                    'payment' => Input::get('payment'),
                    'printinvoicedv' => Input::get('printinvoicedv'),
                    'directresultenter' => Input::get('directresultenter'),
                    'patientsuggestion' => Input::get('patientsuggestion'),
                    'focusonpayment' => Input::get('focusonpayment'),
                    'patientinitials' => Input::get('patientinitials'),
                    'invoice_copy' => Input::get('invoice_copy'),
                    'duplicate_barcodes' => Input::get('duplicate_barcodes'),
                    'duplicate_barset' => Input::get('duplicate_barset'),
                    'bill_allowed_amount_limit' => Input::get('bill_allowed_amount_limit'),
                    'bill_duplicate_count' => Input::get('bill_duplicate_count'),
                    'print_center_receipt' => Input::get('print_center_receipt'),
                    'autoadd_center_discount' => Input::get('autoadd_center_discount'),
                    'print_bill_barcode' => Input::get('print_bill_barcode'),
                    'additional_test_barcode_name' => Input::get('additional_test_barcode_name'),
                    'invoice_sms' => Input::get('invoice_sms'),
                    'disable_branch_bill_print' => Input::get('disable_branch_bill_print'),
                    'bulk_special_barcode_skip' => Input::get('bulk_special_barcode_skip'),
                    'inward_priceincrease' => Input::get('inward_priceincrease'),
                    'grandtotal_roundup' => Input::get('grandtotal_roundup'),
                    'registerbytoken' => Input::get('registerbytoken')
                ]);

            return Response::json([
                'status' => 'success',
                'message' => 'Configuration updated successfully.'
            ]);

        } catch (Exception $e) {
            return Response::json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }


    public function getAllReportConfigs()
    {
        $labLid = isset($_SESSION['lid']) ? $_SESSION['lid'] : null;

        if (!$labLid) {
            return Response::json([
                'status' => 'error',
                'message' => 'Lab LID not found in session'
            ], 400);
        }

        try {
            $configs = DB::table('reportconfigs')
                ->where('lab_lid', $labLid)
                ->get();

            return Response::json([
                'status' => 'success',
                'data' => $configs
            ]);
        } catch (Exception $e) {
            return Response::json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function updateReportConfig()
    {
        $input = Input::all();

        try {
            // Optional: validate input
            $rcid = $input['rcid'];

            // Map values if they are 'Active'/'Inactive'
            $valuestate = ($input['valuestate'] === 'Active') ? 1 : 0;
            $viewsno = ($input['viewsno'] === 'Active') ? 1 : 0;
            $viewregdate = ($input['viewregdate'] === 'Active') ? 1 : 0;
            $viewinitials = ($input['viewinitials'] === 'Active') ? 1 : 0;
            $viewspecialnote = ($input['viewspecialnote'] === 'Active') ? 1 : 0;
            $enableblooddrew = ($input['enableblooddrew'] === 'Active') ? 1 : 0;
            $enablecollected = ($input['enablecollected'] === 'Active') ? 1 : 0;
            $reference_in_invoice = ($input['reference_in_invoice'] === 'Active') ? 1 : 0;
            $rcdob = ($input['rcdob'] === 'Active') ? 1 : 0;

            // id, lab_lid, header, headerurl, footer, footerurl, pageheading, date, sign, confidential, fontitelic, agelabel,
            //  headerdefault, valuestate, viewsno, viewregdate, viewinitials, viewspecialnote, enableblooddrew, enablecollected, 
            //  reference_in_invoice, dob
            DB::table('reportconfigs')
                ->where('id', $rcid)
                ->update([
                    'header' => $input['header'],
                    'headerurl' => $input['headerurl'],
                    'footer' => $input['footer'],
                    'footerurl' => $input['footerurl'],   // correct column name
                    'pageheading' => $input['pageheading'],
                    'date' => $input['rcdate'],           // correct column name is 'date'
                    'sign' => $input['sign'],
                    'confidential' => $input['confidential'],
                    'fontitelic' => $input['fontitelic'],
                    'agelabel' => $input['agelabel'],
                    'headerdefault' => $input['headerdefault'],
                    'valuestate' => $valuestate,
                    'viewsno' => $viewsno,
                    'viewregdate' => $viewregdate,
                    'viewinitials' => $viewinitials,
                    'viewspecialnote' => $viewspecialnote,
                    'enableblooddrew' => $enableblooddrew,
                    'enablecollected' => $enablecollected,
                    'reference_in_invoice' => $reference_in_invoice,
                    'dob' => $rcdob,                      // correct column name is 'dob'
                ]);

            return Response::json(['status' => 'success']);
        } catch (Exception $e) {
            return Response::json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }


     public function getAllConfigConfigs()
    {
        $labLid = isset($_SESSION['lid']) ? $_SESSION['lid'] : null;

        if (!$labLid) {
            return Response::json([
                'status' => 'error',
                'message' => 'Lab LID not found in session'
            ], 400);
        }

        try {
            $configConfigs = DB::table('configs')
                ->where('lab_lid', $labLid)
                ->get();

            return Response::json([
                'status' => 'success',
                'data' => $configConfigs
            ]);
        } catch (Exception $e) {
            return Response::json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateConfigConfigurations()
    {
        $input = Input::all();

        try {
            $id = $input['config_id'];

            DB::table('configs')
                ->where('idconfigs', $id)
                ->update([
                    
                    'separate_prices_branch' => $input['separate_prices_branch'],
                    'worksheet_pertest' => $input['worksheet_pertest'],
                    'worksheet_perdept' => $input['worksheet_perdept'],
                    'report_auth_1' => $input['report_auth_1'],
                    'report_auth_2' => $input['report_auth_2']
                ]);

            return Response::json([
                'status' => 'success',
                'message' => 'Config updated successfully'
            ]);
        } catch (Exception $e) {
            return Response::json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }


     public function getAllSMSConfigs()
    {
        $labLid = isset($_SESSION['lid']) ? $_SESSION['lid'] : null;

        if (!$labLid) {
            return Response::json([
                'status' => 'error',
                'message' => 'Lab LID not found in session'
            ], 400);
        }

        try {
            $SMSConfigs = DB::table('sms_profile')
                ->where('lab_lid', $labLid)
                ->get();

            return Response::json([
                'status' => 'success',
                'data' => $SMSConfigs
            ]);
        } catch (Exception $e) {
            return Response::json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function updateSMSConfig()
    {
        $input = Input::all();

        try {
            $id = $input['sms_id'];

            DB::table('sms_profile')
                ->where('id', $id)
                ->update([
                    'username' => $input['smsusername'],
                    'password' => $input['password'],
                    'src' => $input['src'],
                    'isactiveauto' => $input['isactiveauto']
                ]);

            return Response::json([
                'status' => 'success',
                'message' => 'SMS config updated successfully'
            ]);
        } catch (Exception $e) {
            return Response::json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }



}

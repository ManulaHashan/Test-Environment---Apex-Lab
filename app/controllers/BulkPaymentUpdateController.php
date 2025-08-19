<?php


use Illuminate\Support\Facades\DB;


if (!isset($_SESSION)) {
    session_start();
}



class BulkPaymentUpdateController extends Controller
{

    // **********************Function to get all details*************************************** 
    public function getAllSample_Details()
    {
        try {
            // Get input values
            $dateFrom = Input::get('date_from'); 
            $dateTo = Input::get('date_to');     
            $branch = Input::get('branch');                
            $reference = Input::get('reference');          
            $labId = $_SESSION['lid'];
            

            $samples = DB::table('lps as a')
                ->join('invoice as b', 'a.lpsid', '=', 'b.lps_lpsid')
                ->select(
                    'a.lpsid',
                    'a.date',
                    'b.iid',
                    'a.sampleNo',
                    'b.total',
                    'b.gtotal',
                    'b.paid',
                    DB::raw('(b.gtotal - b.paid) AS due')
                )
                ->where('a.Lab_lid', $labId)
                ->whereRaw('(b.gtotal - b.paid) > 0')
                ->whereBetween('a.date', [$dateFrom, $dateTo])
                ->where('a.sampleNo', 'like', $branch . '%');
            if ($reference != '%') {
                $samples->where('a.refference_idref', '=', $reference);
            }

            $samples = $samples->get();
            
            $output = '';
            if (count($samples) == 0) {
                $output .= '<tr><td colspan="8">No records found</td></tr>';
            } else {
                foreach ($samples as $record) {
                    $output .= '<tr align="center">';
                    $output .= '<td align="center">' . $record->date . '</td>';
                    $output .= '<td align="center">' . $record->iid . '</td>';
                    $output .= '<td align="center">' . $record->sampleNo . '</td>';
                    $output .= '<td align="center">' . $record->total . '</td>';
                    $output .= '<td align="center">' . $record->gtotal . '</td>';
                    $output .= '<td align="center">' . $record->paid . '</td>';
                    $output .= '<td align="center">' . $record->due . '</td>';
                    $output .= '<td align="center"><input type="checkbox" class="select-due" 
                    value="' . $record->lpsid.':' . $record->due . ':'.$record->iid . '" data-due="' . $record->due . '"></td>';
                    $output .= '</tr>';
                }
            }

         
            return Response::json(['html' => $output]);
        } catch (Exception $e) {
            
            return Response::json(['error' => $e->getMessage()], 500);
        }
    }

    //Update Payments

    public function update_Payments()
    {
        $selectedTests = Input::get('selectedInvoice');
        $payment_method = Input::get('payment_method');
        $paymentDate = Input::get('paymentDate');
        


        if (!$selectedTests) {
            return Response::json(['success' => false, 'error' => 'Invalid input']);
        }

        foreach ($selectedTests as $dueinvoice) {
            $testData = explode(':', $dueinvoice);
            $lpsid = $testData[0];
            $due = $testData[1];
            $invoice_id = $testData[2];

            $get_user_id = DB::select("select uid from user where uid=(select user_uid from labUser where luid='" . $_SESSION['luid'] . "')");

             //echo  $get_user_id[0]->uid;




           //echo $lpsid." ".$due." ".$invoice_id." ". $payment_method ." ". $paymentDate;  
            $saveData = DB::table('invoice_payments')->insert([
                'date' => $paymentDate, 
                'amount' => $due , 
                'user_uid' => $get_user_id[0]->uid, 
                'paymethod' => $payment_method, 
                'invoice_iid' => $invoice_id 
            ]);

            if ($saveData == "1") {
                DB::table('invoice')->where('iid', $invoice_id)->update([
                    'paid' => DB::raw('paid + ' . $due),
                    'paiddate' => $paymentDate,
                    'status' => 'Payment Done'
                ]);
            }

           
                
        }
         return Response::json(['success' => true, 'error' => 'updated']);
    }
}

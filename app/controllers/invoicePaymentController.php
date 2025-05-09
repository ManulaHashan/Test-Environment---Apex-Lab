<?php

use Illuminate\Support\Facades\DB;

if (!isset($_SESSION)) {
    session_start();
}

//error_reporting(0);

class invoicePaymentController extends Controller
{

   
    // public function getAllPayments()
    // {
    //     // $invoiceId = Input::get('invoice_iid'); // Pass this from frontend
    //     $invoiceId = Input::get('429836');
    
    //     $payments = DB::table('invoice_payments as a')
    //         ->join('paymethod as b', 'a.paymethod', '=', 'b.idpaymethod')
    //         ->join('user as c', 'a.user_uid', '=', 'c.uid')
    //         // ->where('a.invoice_iid', '=', $invoiceId)
    //         ->where('a.invoice_iid', '=', $invoiceId)
    //         ->select('a.idinvoice_payments', 'a.pay_date', 'b.name as method', 'a.amount', 'a.cheque_no', 'c.fname as user')
    //         ->orderBy('a.pay_date', 'desc')
    //         ->get();
    
    //     $output = '';
    //     if (count($payments) > 0) {
    //         foreach ($payments as $p) {
    //             $output .= '<tr>
    //                 <td align="center">' . htmlspecialchars($p->idinvoice_payments) . '</td>
    //                 <td align="center">' . htmlspecialchars($p->pay_date) . '</td>
    //                 <td align="center">' . htmlspecialchars($p->method) . '</td>
    //                 <td align="right">' . number_format($p->amount, 2) . '</td>
    //                 <td align="center">' . htmlspecialchars($p->cheque_no) . '</td>
    //                 <td align="center">' . htmlspecialchars($p->user) . '</td>
    //             </tr>';
    //         }
    //     } else {
    //         $output = '<tr><td colspan="6" align="center">No payment records found</td></tr>';
    //     }
    
    //     return Response::make($output);
    // }
    
    
    public function getAllPayments()
    {
        // $invoiceId = 429836; // Hardcoded invoice ID
        $invoiceId = Input::get('invoice_iid'); 
    
        // Using the updated SQL logic
        $payments = DB::table('user as a')
        ->join('patient as b', 'a.uid', '=', 'b.user_uid')
        ->join('lps as c', 'c.patient_pid', '=', 'b.pid')
        ->join('invoice as d', 'd.lps_lpsid', '=', 'c.lpsid')
        ->join('invoice_payments as e', 'd.iid', '=', 'e.invoice_iid')
        ->join('paymethod as f', 'e.paymethod', '=', 'f.idpaymethod')
        ->where('d.iid', '=', $invoiceId)
        ->select('a.fname', 'a.lname', 'e.amount', 'd.paid','e.ipid','e.date','f.name','e.chno','d.cashier')
        ->get();
    
        $output = '';
        if (count($payments) > 0) {
            foreach ($payments as $p) {
                $output .= '<tr>
                    <td align="center">' . htmlspecialchars($p->ipid) . '</td>
                    <td align="center">' . htmlspecialchars($p->date) . '</td>
                    <td align="center">' . htmlspecialchars($p->name) . '</td>
                    <td align="right">' . number_format($p->amount, 2) . '</td>
                    <td align="center">' . htmlspecialchars($p->chno) . '</td>
                    <td align="center">' . htmlspecialchars($p->cashier) . '</td>
                    <td align="center"> <button class="delete-btn"
                    style=" border-radius: 5px; background-color:rgb(242, 67, 67); color: #fff; border: none; padding: 5px 10px; cursor: pointer;" 
                    data-ipid="' . $p->ipid . '" >Delete</button></td>
                </tr>';
            }
        } else {
            $output = '<tr><td colspan="6" align="center">No payment records found</td></tr>';
        }
    
        return Response::make($output);
    }
    
    public function loadInvoicePatientDetails()
    {
        $invoiceId = Input::get('invoice_iid'); // Get the invoice ID from the request
    
        $payments = DB::table('user as a')
            ->join('patient as b', 'a.uid', '=', 'b.user_uid')
            ->join('lps as c', 'c.patient_pid', '=', 'b.pid')
            ->join('invoice as d', 'd.lps_lpsid', '=', 'c.lpsid')
            ->leftJoin('invoice_payments as e', 'd.iid', '=', 'e.invoice_iid')
            ->where('d.iid', '=', $invoiceId)
            ->select('a.fname', 'a.lname', 'd.iid', 'd.gtotal', 'd.paid', 'e.ipid', 'e.date', 'd.paymentmethod', 'e.chno', 'd.cashier')
            ->get();
    
        if (count($payments) > 0) {
            $data = [];
            foreach ($payments as $p) {
                $data[] = [
                    'invoice_id' => $p->iid,
                    'patient_name' => $p->fname . ' ' . $p->lname,
                    'total_amount' => $p->gtotal,
                    'paid_amount' => $p->paid,
                    'due_amount' => $p->gtotal - $p->paid
                ];
            }
            return Response::json(['success' => true, 'data' => $data]);
        } else {
            return Response::json(['success' => false, 'message' => 'No data found']);
        }
    }

    
    public function deletePayment()
    {
        try {
            $ipid = Input::get('ipid'); 
    
            if (!$ipid) {
                return Response::json(['status' => 'error', 'message' => 'Invalid payment ID']);
            }
    
            // Get payment details including the invoice ID and amounts
            $paymentDetails = DB::table('invoice_payments as a')
                ->join('invoice as b', 'a.invoice_iid', '=', 'b.iid')
                ->where('a.ipid', '=', $ipid)
                ->select('a.amount', 'b.paid', 'a.invoice_iid as invoice_id')
                ->first(); // Use first() since we're dealing with a single record
    
            if (!$paymentDetails) {
                return Response::json(['status' => 'error', 'message' => 'Payment not found']);
            }
    
            // Calculate new paid amount
            $newPaidAmount = $paymentDetails->paid - $paymentDetails->amount;
    
            // Start transaction to ensure both operations succeed or fail together
            DB::beginTransaction();
    
            try {
                // Delete the payment record
                DB::table('invoice_payments')->where('ipid', $ipid)->delete();
    
                // Update the invoice's paid amount
                DB::table('invoice')
                    ->where('iid', $paymentDetails->invoice_id)
                    ->update(['paid' => $newPaidAmount]);
    
                DB::commit();
    
                return Response::json([
                    'status' => 'success', 
                    'message' => 'Payment deleted successfully',
                    'new_paid_amount' => $newPaidAmount
                ]);
            } catch (Exception $e) {
                DB::rollBack();
                return Response::json(['status' => 'error', 'message' => $e->getMessage()]);
            }
    
        } catch (Exception $e) {
            return Response::json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    

    public function savePayment()
{
    try {
        // Get data from the request
        $invoiceId = Input::get('INVID');
        $date = Input::get('date');
        $amount = Input::get('amount');
        $cno = Input::get('cno');
        $type = explode(':', Input::get('type'))[0];
        $totAmount = Input::get('totAmount');
        $totp = Input::get('totp');
        $userUid = $_SESSION['uid'];  // Get the user UID from session

        // Insert new payment record
        DB::table('invoice_payments')->insert([
            'invoice_iid' => $invoiceId,
            'date' => $date,
            'amount' => $amount,
            'chno' => $cno,
            'user_uid' => $userUid,
            'paymethod' => $type
        ]);

        // Calculate the new payment status
        $status = 'Pending Due';
        if (doubleval($totAmount) == doubleval($totp) + doubleval($amount)) {
            $status = 'Payment Done';
        }

        // Update the invoice status and paid amount, even if invoice doesn't exist
        DB::table('invoice')->where('iid', $invoiceId)->update([
            'paid' => DB::raw("paid + $amount"),
            'status' => $status
        ]);

        return Response::json(['status' => 'success', 'message' => 'Payment saved successfully.']);
    } catch (Exception $e) {
        // Handle any errors
        return Response::json(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

    


}
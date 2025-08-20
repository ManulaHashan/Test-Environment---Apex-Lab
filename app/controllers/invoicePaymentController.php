<?php

use Illuminate\Support\Facades\DB;

if (!isset($_SESSION)) {
    session_start();
}

//error_reporting(0);

class invoicePaymentController extends Controller
{

   
 
    
    
    public function getAllPayments()
    {
        
        $invoiceId = Input::get('invoice_iid'); 
    
      

        $payments = DB::table('invoice_payments as e')
            ->join('user as a', 'a.uid', '=', 'e.user_uid')
            ->join('invoice as d', 'd.iid', '=', 'e.invoice_iid')
            ->join('paymethod as f', 'e.paymethod', '=', 'f.idpaymethod')
            ->where('d.iid', '=', $invoiceId)
            ->select(
                'a.fname',
                'a.lname',
                'e.amount',
                'd.paid',
                'e.ipid',
                'e.date',
                'f.name as paymethod',
                'e.chno'
            )
            ->get();
    
        $output = '';
        if (count($payments) > 0) {
            foreach ($payments as $p) {
                $output .= '<tr>
                <td align="center">' . htmlspecialchars($p->ipid) . '</td>
                <td align="center">' . htmlspecialchars($p->date) . '</td>
                <td align="center">' . htmlspecialchars($p->paymethod) . '</td>
                <td align="right">' . number_format($p->amount, 2) . '</td>
                <td align="center">' . htmlspecialchars($p->chno) . '</td>
                <td align="center">' . htmlspecialchars($p->fname . ' ' . $p->lname) . '</td>
                <td align="center">
                    <button class="delete-btn"
                        style="border-radius:5px; background-color:rgb(242,67,67); color:#fff; border:none; padding:5px 10px; cursor:pointer;" 
                        data-ipid="' . $p->ipid . '">Delete</button>
                </td>
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
                $status = 'Pending Due';
                // Update the invoice's paid amount
                DB::table('invoice')
                    ->where('iid', $paymentDetails->invoice_id)
                    ->update(['paid' => $newPaidAmount,
                              'status' => $status]);
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
        

        $userUid = $_SESSION['uid'];  

        //   if (session_status() === PHP_SESSION_NONE) {
        //         session_start();
        //     }

            
        //     $userUid = isset($_SESSION['uid']) ? $_SESSION['uid'] : null;

        //     if (!$userUid) {
        //         return Response::json([
        //             'status' => 'error',
        //             'message' => 'Session expired. Please login again.'
        //         ]);
        //     }

        if (empty($invoiceId) || empty($date) || empty($amount) || empty($type) ||$amount <= 0) {
            return Response::json(['status' => 'error', 'message' => 'All fields are required and tender amount must be greater than 0.']);
        }
        // Insert new payment record
        DB::table('invoice_payments')->insert([
            'invoice_iid' => $invoiceId,
            'date' => $date,
            'amount' => $amount,
            'chno' => $cno,
            'user_uid' => $userUid,
            'paymethod' => $type
        ]);

        $totAmount = 0;
        $totp = 0;
        $paymentDetails_invoice_Table = DB::table('invoice')
            ->where('iid', $invoiceId)
            ->select('gtotal', 'paid')
            ->first();

        $totAmount = $paymentDetails_invoice_Table->gtotal;
        $totp = $paymentDetails_invoice_Table->paid;

            
        // Calculate the new payment status
        $status = 'Pending Due';
        $calamt = floatval($totp) + floatval($amount); // Convert string to float and add $amount;
        if ($calamt >= floatval($totAmount)) {
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
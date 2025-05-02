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
        $invoiceId = 429836; // Hardcoded invoice ID
    
        // Using the updated SQL logic
        $payments = DB::table('invoice_payments as a')
            ->join('paymethod as b', 'a.paymethod', '=', 'b.idpaymethod')
            ->join('user as c', 'a.user_uid', '=', 'c.uid')
            ->where('a.invoice_iid', '=', $invoiceId)
            ->select('a.ipid', 'a.date', 'b.name as method', 'a.amount', 'a.chno', 'c.fname as user')
            ->orderBy('a.date', 'desc')
            ->get();
    
        $output = '';
        if (count($payments) > 0) {
            foreach ($payments as $p) {
                $output .= '<tr>
                    <td align="center">' . htmlspecialchars($p->ipid) . '</td>
                    <td align="center">' . htmlspecialchars($p->date) . '</td>
                    <td align="center">' . htmlspecialchars($p->method) . '</td>
                    <td align="right">' . number_format($p->amount, 2) . '</td>
                    <td align="center">' . htmlspecialchars($p->chno) . '</td>
                    <td align="center">' . htmlspecialchars($p->user) . '</td>
                </tr>';
            }
        } else {
            $output = '<tr><td colspan="6" align="center">No payment records found</td></tr>';
        }
    
        return Response::make($output);
    }
    
    








}

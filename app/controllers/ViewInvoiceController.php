<?php

use Illuminate\Support\Facades\DB;

if (!isset($_SESSION)) {
    session_start();
}

//error_reporting(0);

class ViewInvoiceController extends Controller{
    // Function to get all details 
    public function getAllInvoices()
    {
        $labId = $_SESSION['lid'];
        $uId = $_SESSION['uid'];
    
        // Get inputs

        $center = explode(':', Input::get('center'));

        $withOtherBranches = Input::get('withOtherBranches');
        $dueBillsOnly = Input::get('dueBillsOnly');
        $byDate = Input::get('byDate');
        $idate = Input::get('idate');
        $invoiceNo = Input::get('invoiceNo');
        $firstName = Input::get('firstName');
        $lastName = Input::get('lastName');
        $contact = Input::get('contact');
        $patientType = Input::get('patientType');

        $query = DB::table('user as u')
        ->join('patient as p', 'u.uid', '=', 'p.user_uid')
        ->join('lps as l', 'l.patient_pid', '=', 'p.pid')
        ->join('invoice as i', 'l.lpsid', '=', 'i.lps_lpsid')
        ->where('l.Lab_lid', '=', $labId);

            if($withOtherBranches == "0"){
            if ($center[0] == '%') {
                $query->whereRaw("l.sampleno REGEXP '^[0-9]'");
            } else {
                $query->where('l.sampleno', 'like', $center[1] . '%');
            }
            } 
            if( $dueBillsOnly == "1"){
                $query->whereRaw('i.total - i.paid > 0');
            }

            
            
            
            if (!empty($idate)) {
                $query->where('l.date', '=', $idate);
            }
        
        
            if (!empty($invoiceNo)) {
                $query->where('l.sampleNo', '=', $invoiceNo);
            }
        
            if (!empty($firstName)) {
                $query->where('u.fname', 'LIKE', '%' . $firstName . '%');
            }
        
            if (!empty($lastName)) {
                $query->where('u.lname', 'LIKE', '%' . $lastName . '%');
            }
        
            if (!empty($contact)) {
                $query->where('u.tpno', 'LIKE', '%' . $contact . '%');
            }
            if (!empty($patientType)) {
                $query->where('l.type', '=', $patientType);
            }
        
            // Select relevant fields
            $records = $query->select(
                'l.lpsid',
                'l.date',
                'l.sampleNo',
                'l.type',
                'u.fname',
                'u.lname',
                'i.status',
                'i.total',
                'i.gtotal',
                'i.paid',
                'i.cashier'
            )->get();
        
            
            if (count($records) > 0) {
                $output = '';
                foreach ($records as $row) {
                    $due = $row->gtotal - $row->paid;
                    $output .= '<tr class="invoiceRow" data-lpsid="' . $row->lpsid . '" data-date="' . htmlspecialchars($row->date) . '">
                                    <td align="left">' . htmlspecialchars($row->sampleNo) . '</td>
                                    <td align="left">' . htmlspecialchars($row->fname) . '</td>
                                    <td align="left">' . htmlspecialchars($row->lname) . '</td>
                                    <td align="left">' . htmlspecialchars($row->status) . '</td>
                                    <td align="right">' . number_format($row->gtotal, 2) . '</td>
                                    <td align="right">' . number_format($row->paid, 2) . '</td>
                                    <td align="right">' . number_format($due, 2) . '</td>
                                    <td align="left">' . htmlspecialchars($row->cashier) . '</td> 
                                </tr>';
                }
                echo $output;
            } else {
                echo '<tr><td colspan="8" style="text-align: center;">No Invoice Records Found</td></tr>';
            }
    }
        
        
    public function getSampleTestData()
    {
        $sampleNo = Input::get('sampleNo');
        $date = Input::get('date');

        $data = DB::table('lps as l')
            ->join('Testgroup as t', 'l.Testgroup_tgid', '=', 't.tgid')
            ->select('l.sampleNo', 't.name','l.status')
            ->where('l.date', '=', $date)
            ->where('l.sampleNo', 'like', $sampleNo . '%')
            ->get();

        if (count($data) > 0) {
            $html = '';
            foreach ($data as $item) {
                if($item->status=='pending'){
                    $html .= '<tr style="background-color:rgb(245, 201, 40);">
                            <td align="center">' . htmlspecialchars($item->sampleNo) . '</td>
                            <td align="left">' . htmlspecialchars($item->name) . '</td>
                        </tr>';
                }else if($item->status=='Accepted'){
                    $html .= '<tr style="background-color:rgb(55, 105, 241);">
                    <td align="center">' . htmlspecialchars($item->sampleNo) . '</td>
                    <td align="left">' . htmlspecialchars($item->name) . '</td>
                </tr>';
                }else if($item->status=='barcorded'){
                    $html .= '<tr style="background-color:rgb(182, 55, 241);">
                    <td align="center">' . htmlspecialchars($item->sampleNo) . '</td>
                    <td align="left">' . htmlspecialchars($item->name) . '</td>
                </tr>';
                }else{
                    $html .= '<tr style="background-color:rgb(82, 241, 74);">
                    <td align="center">' . htmlspecialchars($item->sampleNo) . '</td>
                    <td align="left">' . htmlspecialchars($item->name) . '</td>
                </tr>';
                }

                

                
            }
            echo $html;
        } else {
            echo '<tr><td colspan="2" style="text-align:center;">No Related Test Found</td></tr>';
        }
    }

    // public function cancelInvoice()
    // {
    //     $sampleNo = Input::get('sampleNo'); 
    //     $lpsId = Input::get('lpsId');
    //      $invoiceDate = Input::get('invoiceDate');
    
    //     try {
    //         DB::transaction(function () use ($sampleNo, $lpsId,$invoiceDate) {
    //             // Update the lps table
    //             DB::table('lps')
    //                 ->where('sampleNo', 'like', $sampleNo . '%')
    //                 ->where('date', '=', $invoiceDate )
    //                 ->update(['date' => '0000-00-00']);
    
    //             // Retrieve invoice IDs
    //             $invoiceIds = DB::table('invoice')
    //                 ->where('lps_lpsid', $lpsId)
    //                 ->pluck('iid');
    //             $invoiceIds = (array) $invoiceIds;
    
    //             // Update the invoice table
    //             DB::table('invoice')
    //                 ->where('lps_lpsid', $lpsId)
    //                 ->update(['date' => '0000-00-00']);
    
    //             // Update the invoice_payments table
    //             DB::table('invoice_payments')
    //                 ->whereIn('invoice_iid', $invoiceIds)
    //                 ->update(['date' => '0000-00-00']);
    //         });
            
    //         // Insert into canceled_invoices table after successful cancellation
    //         $lps_id = $lpsId;
    //         $invoiceDate = date('Y-m-d');
    //         $today_date = date('Y-m-d');
    //         $today_time = date('H:i:s');
    //         $labId = $_SESSION['lid'];
    //         $uId = $_SESSION['uid'];
    
    //         DB::table('canceled_invoices')->insert([
    //             'lps_lpsid' => $lps_id,
    //             'invoiced_date' => $invoiceDate,
    //             'canceled_date' => $today_date,
    //             'canceled_time' => $today_time,
    //             'user' => $uId,
    //             'approved' => $uId, 
    //             'note' => 'Invoice canceled by user.', 
    //             'Lab_lid' =>  $labId,
    //         ]);
    
    //         return Response::json(['message' => 'Invoice successfully cancelled.']);
    //     } catch (Exception $e) {
    //         return Response::json(['message' => 'Error cancelling invoice: ' . $e->getMessage()], 500);
    //     }
    // }
    

    public function cancelInvoice()
{
    $sampleNo = Input::get('sampleNo'); 
    $lpsId = Input::get('lpsId');
    $invoiceDate = Input::get('invoiceDate');
    $inputPassword = Input::get('password');

    $uId = $_SESSION['uid'];

    // Privilege check - options_idoptions = 18
    $hasPrivilege = DB::table('privillages')
                    ->where('user_uid', $uId)
                    ->where('options_idoptions', 18)
                    ->exists();

    if (!$hasPrivilege) {
        return Response::json(['message' => 'Permission denied. You do not have cancel privileges.'], 403);
    }

    // Password check 
    $storedPassword = DB::table('loginDetails as a')
                        ->join('user as b', 'b.loginDetails_idloginDetails', '=', 'a.idloginDetails')
                        ->where('b.uid', $uId)
                        ->pluck('a.password');

    if ($storedPassword != $inputPassword) {
        return Response::json(['message' => 'Invalid password.'], 403);
    }

    try {
        DB::transaction(function () use ($sampleNo, $lpsId, $invoiceDate) {
            // Update lps table
            DB::table('lps')
                ->where('sampleNo', 'like', $sampleNo . '%')
                ->where('date', '=', $invoiceDate)
                ->update(['date' => '0000-00-00']);

            // Get related invoice IDs
            $invoiceIds = DB::table('invoice')
                ->where('lps_lpsid', $lpsId)
                ->pluck('iid');
            $invoiceIds = (array) $invoiceIds;

            // Update invoice table
            DB::table('invoice')
                ->where('lps_lpsid', $lpsId)
                ->update(['date' => '0000-00-00']);

            // Update invoice_payments table
            DB::table('invoice_payments')
                ->whereIn('invoice_iid', $invoiceIds)
                ->update(['date' => '0000-00-00']);
        });

        // Insert record into canceled_invoices
        $today_date = date('Y-m-d');
        $today_time = date('H:i:s');
        $labId = $_SESSION['lid'];

        DB::table('canceled_invoices')->insert([
            'lps_lpsid' => $lpsId,
            'invoiced_date' => $invoiceDate,
            'canceled_date' => $today_date,
            'canceled_time' => $today_time,
            'user' => $uId,
            'approved' => $uId, 
            'note' => 'Invoice canceled by user.', 
            'Lab_lid' =>  $labId,
        ]);

        return Response::json(['message' => 'Invoice successfully cancelled.']);
    } catch (Exception $e) {
        return Response::json(['message' => 'Error cancelling invoice: ' . $e->getMessage()], 500);
    }
}


    
    public function getCashierBalanceData()
    {
        $cashierId = Input::get('cashier_id');
        $date = Input::get('date');
    
        $query = DB::table('invoice as a')
            ->join('lps as b', 'a.lps_lpsid', '=', 'b.lpsid')
            ->select(
                DB::raw('COALESCE(count(a.lps_lpsid), 0) as total_bill_count'),
                'a.date',
                DB::raw('COALESCE(sum(a.gtotal), 0) as gtotal'),
                DB::raw('COALESCE(sum(a.paid), 0) as paid'),
                DB::raw('COALESCE(sum(a.gtotal - a.paid), 0) as total_due')
            )
            ->where('a.date', '=', $date)
            ->where('a.cashier', 'like', $cashierId . '%')
            ->where('b.Lab_lid', '=', $_SESSION['lid'])
            ->groupBy('a.date');
    
        $results = $query->first();
    
        // Initialize values to zero if the query result is null
        $totalBillCount = $results->total_bill_count ?? 0;
        $totalAmount = number_format($results->gtotal ?? 0, 2);
        $totalExpenses = '00.00';
        $totalPaid = number_format($results->paid ?? 0, 2);
        $totalDue = number_format($results->total_due ?? 0, 2);
        $cashierBalance = number_format($results->paid ?? 0, 2);
    
        return Response::json([
            'totalBillCount' => $totalBillCount,
            'totalAmount' => $totalAmount,
            'totalExpenses' => $totalExpenses,
            'totalPaid' => $totalPaid,
            'totalDue' => $totalDue,
            'cashierBalance' => $cashierBalance
        ]);
    }
    


    public function getInvoiceArray()
    {
        $labId = $_SESSION['lid'];
        $sampleNo = Input::get('sampleNo');
        $date = Input::get('date');

        if (!$sampleNo || !$date) {
            return Response::json(['0', '0']);
        }

        $result = DB::select("
            select a.iid, IFNULL(a.gtotal - a.paid, 0) AS due 
            FROM invoice a, lps b 
            WHERE a.lps_lpsid = b.lpsid 
            and b.Lab_lid = $labId
            AND b.date = ? 
            AND b.sampleNo = ?
            LIMIT 1
        ", [$date, $sampleNo]);

        if (count($result) > 0) {
            return Response::json([$result[0]->iid, $result[0]->due]);
        } else {
            return Response::json(['0', '0']);
        }
    }




}

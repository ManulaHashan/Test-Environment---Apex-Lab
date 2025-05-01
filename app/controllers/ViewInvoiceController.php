<?php

use Illuminate\Support\Facades\DB;

if (!isset($_SESSION)) {
    session_start();
}

//error_reporting(0);

class ViewInvoiceController extends Controller
{
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
            'i.paid',
            'i.cashier'
        )->get();
    
        
        if (count($records) > 0) {
            $output = '';
            foreach ($records as $row) {
                $due = $row->total - $row->paid;
                $output .= '<tr class="invoiceRow" data-lpsid="' . $row->lpsid . '" data-date="' . htmlspecialchars($row->date) . '">
                                <td align="center">' . htmlspecialchars($row->sampleNo) . '</td>
                                <td align="left">' . htmlspecialchars($row->fname) . '</td>
                                <td align="left">' . htmlspecialchars($row->lname) . '</td>
                                <td align="left">' . htmlspecialchars($row->status) . '</td>
                                <td align="right">' . number_format($row->total, 2) . '</td>
                                <td align="right">' . number_format($row->paid, 2) . '</td>
                                <td align="right">' . number_format($due, 2) . '</td>
                                <td align="center">' . htmlspecialchars($row->cashier) . '</td> 
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
        ->select('l.sampleNo', 't.name')
        ->where('l.date', '=', $date)
        ->where('l.sampleNo', 'like', $sampleNo . '%')
        ->get();

    if (count($data) > 0) {
        $html = '';
        foreach ($data as $item) {
            $html .= '<tr>
                        <td align="center">' . htmlspecialchars($item->sampleNo) . '</td>
                        <td align="left">' . htmlspecialchars($item->name) . '</td>
                      </tr>';
        }
        echo $html;
    } else {
        echo '<tr><td colspan="2" style="text-align:center;">No Related Test Found</td></tr>';
    }
}

public function cancelInvoice()
{
    $sampleNo = Input::get('sampleNo'); 
    $lpsId = Input::get('lpsId');

    try {
        DB::transaction(function () use ($sampleNo, $lpsId) {
            DB::table('lps')
                ->where('sampleNo', 'like', $sampleNo . '%')
                ->update(['date' => '0000-00-00']);
    
    
            $invoiceIds = DB::table('invoice')
                ->where('lps_lpsid', $lpsId)
                ->pluck('iid');
    
           
            $invoiceIds = (array) $invoiceIds;
    
          
            DB::table('invoice')
                ->where('lps_lpsid', $lpsId)
                ->update(['date' => '0000-00-00']);
    
           
            DB::table('invoice_payments')
                ->whereIn('invoice_iid', $invoiceIds)  
                ->update(['date' => '0000-00-00']);
        });

        return Response::json(['message' => 'Invoice successfully cancelled.']);
    } catch (Exception $e) {
        return Response::json(['message' => 'Error cancelling invoice: ' . $e->getMessage()], 500);
    }
}





}

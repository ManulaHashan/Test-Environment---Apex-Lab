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

        // Optional filters
        
        
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
            'l.sampleNo',
            'l.type',
            'u.fname',
            'u.lname',
            'i.status',
            'i.total',
            'i.paid',
            'i.cashier'
        )->get();
    
        // Output HTML
        if (count($records) > 0) {
            $output = '';
            foreach ($records as $row) {
                $due = $row->total - $row->paid;
                $output .= '<tr>
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
    
    
    




}

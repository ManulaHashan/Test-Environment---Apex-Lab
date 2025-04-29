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
        $labId = $_SESSION['lid']; // Lab ID from session
        $date = '2025-04-29'; // You can make this dynamic if needed
    
        $records = DB::table('user as u')
            ->join('patient as p', 'u.uid', '=', 'p.user_uid')
            ->join('lps as l', 'l.patient_pid', '=', 'p.pid')
            ->join('invoice as i', 'l.lpsid', '=', 'i.lps_lpsid')
            ->where('l.Lab_lid', '=', $labId)
            ->where('l.date', '=', $date)
            ->select(
                'l.sampleNo',
                'u.fname',
                'u.lname',
                'i.status',
                'i.total',
                'i.paid',
                'u.uid'
            )
            ->get();
    
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
                                <td align="center">' . htmlspecialchars($row->uid) . '</td>
                            </tr>';
            }
            echo $output;
        } else {
            echo '<tr><td colspan="8" style="text-align: center;">No Invoice Records Found</td></tr>';
        }
    }
    




}

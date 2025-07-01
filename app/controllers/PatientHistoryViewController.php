<?php

use Illuminate\Support\Facades\DB;

if (!isset($_SESSION)) {
    session_start();
}

//error_reporting(0);

class PatientHistoryViewController extends Controller
{

    public function getAllPatientHistoryRecords()
    {
        $iid = Input::get('iid');

        // Validate input
        if (empty($iid)) {
            return '<tr><td colspan="6" style="text-align: center;">Invalid invoice ID</td></tr>';
        }

        // Main query
        $records = DB::table('invoice')
            ->join('lps', 'lps.lpsid', '=', 'invoice.lps_lpsid')
            ->join('patient', 'patient.pid', '=', 'lps.patient_pid')
            ->join('user', 'user.uid', '=', 'patient.user_uid')
            ->select(
                'lps.date',
                'lps.arivaltime',
                'user.fname',
                'user.mname',
                'invoice.status AS invoice_status',
                'lps.status AS lps_status',
                'invoice.multiple_delivery_methods'
            )
            ->where('user.uid', function($query) use ($iid) {
                $query->select('u.uid')
                    ->from('invoice as i')
                    ->join('lps as l', 'l.lpsid', '=', 'i.lps_lpsid')
                    ->join('patient as p', 'p.pid', '=', 'l.patient_pid')
                    ->join('user as u', 'u.uid', '=', 'p.user_uid')
                    ->where('i.iid', '=', $iid);
                    
            })
            ->orderBy('lps.date', 'DESC')
            ->get();

        // Build HTML rows
        if (count($records) > 0) {
            $output = '';
            foreach ($records as $row) {
                $fullName = htmlspecialchars($row->fname . ' ' . $row->mname);
                $output .= '<tr class="phistr" style="cursor:pointer;">';
                $output .= '<td align="center">' . htmlspecialchars($row->date) . '</td>';
                $output .= '<td align="center">' . htmlspecialchars($row->arivaltime) . '</td>';
                $output .= '<td align="center">' . $fullName . '</td>';
                $output .= '<td align="center">' . htmlspecialchars($row->invoice_status) . '</td>';
                $output .= '<td align="center">' . htmlspecialchars($row->lps_status) . '</td>';
                $output .= '<td align="center">' . htmlspecialchars($row->multiple_delivery_methods) . '</td>';
                $output .= '</tr>';
            }
            echo $output;
        } else {
            echo '<tr><td colspan="6" style="text-align: center;">No records found</td></tr>';
        }
    }

}



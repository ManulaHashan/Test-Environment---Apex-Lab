<?php


use Illuminate\Support\Facades\DB;



if (!isset($_SESSION)) {
    session_start();
}



class TestParameterMappingController extends Controller{

    // **********************Function to get all details*************************************** 
    public function getAllSampleDetails()
    {
        try {
            $date = Input::get('date');
            $sample_no = Input::get('sample_no');

            $query = DB::table('lps as a')
                ->join('patient as b', 'a.patient_pid', '=', 'b.pid')
                ->join('user as c', 'b.user_uid', '=', 'c.uid')
                ->join('Testgroup as d', 'a.Testgroup_tgid', '=', 'd.tgid')
                ->select('a.lpsid', 'a.date', 'a.sampleNo', 'c.fname', 'c.lname', 'a.Testgroup_tgid', 'd.name')
                ->where('a.lab_lid', '=', $_SESSION['lid']);

            if (!empty($date)) {
                $query->whereDate('a.date', '=', $date); 
            }

            if (!empty($sample_no)) {
                $query->where('a.sampleNo', 'like', '' . $sample_no . '%'); 
            }

            $records = $query->get();

            $output = '';
            if (count($records) == 0) {
                $output .= '<tr><td colspan="6">No records found</td></tr>';
            } else {
                foreach ($records as $record) {
                    $output .= '<tr>';
                    $output .= '<td>' . $record->lpsid . '</td>';
                    $output .= '<td>' . $record->sampleNo . '</td>';
                    $output .= '<td>' . $record->fname . ' ' . $record->lname . '</td>';
                    $output .= '<td>' . $record->Testgroup_tgid . '</td>';
                    $output .= '<td>' . $record->name . '</td>';
                    $output .= '<td><input type="checkbox" value="' . $record->lpsid . '"></td>';
                    $output .= '</tr>';
                }
            }

            return Response::json(['html' => $output]);
        } catch (Exception $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
    }





   
}

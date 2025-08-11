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

            // Building the query
            $query = DB::table('lps as a')
            ->join('patient as b', 'a.patient_pid', '=', 'b.pid')
            ->join('user as c', 'b.user_uid', '=', 'c.uid')
            ->join('Testgroup as d', 'a.Testgroup_tgid', '=', 'd.tgid')
            ->select('a.lpsid', 'a.sampleNo', 'c.fname', 'c.lname', 'a.Testgroup_tgid', 'd.name')
            ->where('a.lab_lid', '=', $_SESSION['lid']);

            // Apply filters if any
            if (!empty($date)) {
                $query->whereDate('a.created_at', '=', $date); // Ensure the field you're filtering on is correct
            }

            if (!empty($sample_no)) {
                $query->where('a.sampleNo', '=', $sample_no); // Ensure this column is correct
            }

            $records = $query->get();

            // Generate the HTML output for the table rows
            $output = '';
            if ($records->isEmpty()) {
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

            return response()->json(['html' => $output]);  // Return HTML as a JSON response
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }




   
}

<?php

use Illuminate\Support\Facades\DB;


if (!isset($_SESSION)) {
    session_start();
}

//error_reporting(0);

class SampleDataEditController extends Controller{
    // Function to get all details 
  
        
    public function getSampleDataEditRecords()
    {
        $labId = $_SESSION['lid'];
        $sample_date = Input::get('sample_date');
        $sample_no = Input::get('sample_no');

        $query = DB::table('lps')
            ->where('Lab_lid', '=', $labId)
            ->where('date', '=', $sample_date);

        // Sample No එකක් enter කරලා තිබ්බොත් filter එකට add කරන්න
        if (!empty($sample_no)) {
            $query->where('sampleNo', 'like','%'.$sample_no.'%');
        }

        $records = $query->select(
            'lpsid', 'date', 'sampleNo', 'patient_pid', 'arivaltime', 'finishtime', 'finishdate',
            'collecteddate', 'status', 'refference_idref', 'blooddraw', 'repcollected',
            'fastinghours', 'fastingtime', 'entered_uid', 'reference_in_invoice',
            'Testgroup_tgid', 'urgent_sample'
        )->get();

        if (count($records) > 0) {
                    $output = '';
                foreach ($records as $row) {
                $output .= '<tr>
                    <td>' . htmlspecialchars($row->lpsid) . '</td>
                    <td><input type="text" value="' . htmlspecialchars($row->sampleNo) . '" name="sampleNo"></td>
                    <td><input type="text" value="' . htmlspecialchars($row->date) . '" name="date" class="datepicker" readonly></td>
                    <td><input type="number" value="' . htmlspecialchars($row->patient_pid) . '" name="patient_pid"></td>
                    <td><input type="text" value="' . htmlspecialchars($row->arivaltime) . '" name="arivaltime" class="timepicker"readonly></td>
                    <td><input type="text" value="' . htmlspecialchars($row->finishtime) . '" name="finishtime" class="timepicker"readonly></td>
                    <td><input type="text" value="' . htmlspecialchars($row->finishdate) . '" name="finishdate" class="datepicker"readonly></td>
                    <td><input type="text" value="' . htmlspecialchars($row->collecteddate) . '" name="collecteddate" class="datepicker"readonly></td>
                    <td><input type="text" value="' . htmlspecialchars($row->status) . '" name="status"></td>
                    <td><input type="number" value="' . htmlspecialchars($row->refference_idref) . '" name="refference_idref"></td>
                    <td><input type="text" value="' . htmlspecialchars($row->blooddraw) . '" name="blooddraw" class="datetimepicker"readonly></td>
                    <td><input type="text" value="' . htmlspecialchars($row->repcollected) . '" name="repcollected"class="datetimepicker"readonly></td>
                    <td><input type="number" value="' . htmlspecialchars($row->fastinghours) . '" name="fastinghours"></td>
                    <td><input type="text" value="' . htmlspecialchars($row->fastingtime) . '" name="fastingtime" class="timepicker"readonly></td>
                    <td><input type="number" value="' . htmlspecialchars($row->entered_uid) . '" name="entered_uid"></td>
                    <td><input type="number" value="' . htmlspecialchars($row->reference_in_invoice) . '" name="reference_in_invoice"></td>
                    <td><input type="number" value="' . htmlspecialchars($row->Testgroup_tgid) . '" name="Testgroup_tgid"></td>
                    <td><input type="number" value="' . htmlspecialchars($row->urgent_sample) . '" name="urgent_sample"></td>
                    <td><button class="updateRowBtn" data-lpsid="' . htmlspecialchars($row->lpsid) . '">Update</button></td>
                </tr>';
            }
            echo $output;
        } else {
            echo '<tr><td colspan="17" style="text-align: center;">No Records Found</td></tr>';
        }
    }

    public function updateSampleRecord()
{
    $lpsid = Input::get('lpsid');
    $sampleNo = Input::get('sampleNo');
    $date = Input::get('date');

        $exists = DB::table('lps')
        ->where('sampleNo', $sampleNo)
        ->where('date', $date)
        ->where('lpsid', '!=', $lpsid)
        ->exists();

    if ($exists) {
        
        return Response::json([
            'success' => false,
            'message' => 'Sample No / Sample Date already exists'
        ]);
    }
    
    $updateData = array(
        'sampleNo' => Input::get('sampleNo'),
        'date' => Input::get('date'),
        'patient_pid' => Input::get('patient_pid'),
        'arivaltime' => Input::get('arivaltime'),
        'finishtime' => Input::get('finishtime'),
        'finishdate' => Input::get('finishdate'),
        'collecteddate' => Input::get('collecteddate'),
        'status' => Input::get('status'),
        'refference_idref' => Input::get('refference_idref'),
        'blooddraw' => Input::get('blooddraw'),
        'repcollected' => Input::get('repcollected'),
        'fastinghours' => Input::get('fastinghours'),
        'fastingtime' => Input::get('fastingtime'),
        'entered_uid' => Input::get('entered_uid'),
        'reference_in_invoice' => Input::get('reference_in_invoice'),
        'Testgroup_tgid' => Input::get('Testgroup_tgid'),
        'urgent_sample' => Input::get('urgent_sample')
    );

    DB::table('lps')->where('lpsid', $lpsid)->update($updateData);

    return Response::json(['success' => true]);
}


}

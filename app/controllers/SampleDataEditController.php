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

    if (!empty($sample_no)) {
        $query->where('sampleNo', 'like','%'.$sample_no.'%');
    }

    $records = $query->select(
        'lpsid', 'date', 'sampleNo', 'patient_pid', 'arivaltime', 'finishtime', 'finishdate',
        'collecteddate', 'status', 'refference_idref', 'blooddraw', 'repcollected',
        'fastinghours', 'fastingtime', 'entered_uid', 'reference_in_invoice',
        'Testgroup_tgid', 'urgent_sample'
    )->get();

    // Get all refference options ONCE
    $refferenceOptions = DB::table('refference')
        ->where('lid', $labId)
        ->orderBy('name', 'asc')
        ->select('idref', 'name')
        ->get();

        $testGroupOptions = DB::table('Testgroup')
        ->where('Lab_lid', $labId)
        ->orderBy('name', 'asc')
        ->select('tgid', 'name')
        ->get();

        $labUserOptionss = DB::table('user as a')
        ->join('labUser as b', 'a.uid', '=', 'b.user_uid')
        ->join('Lab_labUser as c', 'b.luid', '=', 'c.labUser_luid')
        ->where('c.lab_lid', $labId)
        ->orderBy('a.fname', 'asc')
        ->select('a.uid', 'a.fname', 'a.lname')
        ->get();



    if (count($records) > 0) {
        $output = '';
        foreach ($records as $row) {
            
            $refferenceDropdown = '<select name="refference_idref">';
                // Add empty option if refference_idref is null or empty
                if (empty($row->refference_idref)) {
                    $refferenceDropdown .= '<option value="" selected></option>';
                } else {
                    $refferenceDropdown .= '<option value=""></option>';
                }
            foreach ($refferenceOptions as $ref) {
                $selected = ($row->refference_idref == $ref->idref) ? 'selected' : '';
                $refferenceDropdown .= '<option value="' . htmlspecialchars($ref->idref) . '" ' . $selected . '>' . htmlspecialchars($ref->name) . '</option>';
            }
            $refferenceDropdown .= '</select>';

            // *************************************
            $testGroupDropdown = '<select name="Testgroup_tgid">';
                // Add empty option if refference_idref is null or empty
                if (empty($row->Testgroup_tgid)) {
                    $testGroupDropdown .= '<option value="" selected></option>';
                } else {
                    $testGroupDropdown .= '<option value=""></option>';
                }
            foreach ($testGroupOptions as $testgroup) {
                $selected = ($row->Testgroup_tgid == $testgroup->tgid) ? 'selected' : '';
                $testGroupDropdown .= '<option value="' . htmlspecialchars($testgroup->tgid) . '" ' . $selected . '>' . htmlspecialchars($testgroup->name) . '</option>';
            }
            $testGroupDropdown .= '</select>';

             // *************************************


             $labUserOptions = '<select name="entered_uid">';
                // Add empty option if refference_idref is null or empty
                if (empty($row->entered_uid)) {
                    $labUserOptions .= '<option value="" selected></option>';
                } else {
                    $labUserOptions .= '<option value=""></option>';
                }
                foreach ($labUserOptionss as $labuser) {
                    $selected = ($row->entered_uid == $labuser->uid) ? 'selected' : '';
                    $fullName = $labuser->fname . ' ' . $labuser->lname;
                    $displayText = $labuser->uid . " : " . $fullName;
                    $labUserOptions .= '<option value="' . htmlspecialchars($labuser->uid) . '" ' . $selected . '>' . htmlspecialchars($displayText) . '</option>';
                }
                $labUserOptions .= '</select>';

            $output .= '<tr>
                <td>' . htmlspecialchars($row->lpsid) . '</td>
                <td><input type="text" value="' . htmlspecialchars($row->sampleNo) . '" name="sampleNo"></td>
                <td><input type="text" value="' . htmlspecialchars($row->date) . '" name="date" class="datepicker"></td>
                <td><input type="number" value="' . htmlspecialchars($row->patient_pid) . '" name="patient_pid"></td>
                <td><input type="text" value="' . htmlspecialchars($row->arivaltime) . '" name="arivaltime" class="timepicker"></td>
                <td><input type="text" value="' . htmlspecialchars($row->finishtime) . '" name="finishtime" class="timepicker"></td>
                <td><input type="text" value="' . htmlspecialchars($row->finishdate) . '" name="finishdate" class="datepicker"></td>
                <td><input type="text" value="' . htmlspecialchars($row->collecteddate) . '" name="collecteddate" class="datepicker"></td>
                <td>
                    <select name="status">
                        <option value="Accepted" ' . ($row->status == "Accepted" ? "selected" : "") . '>Accepted</option>
                        <option value="barcorded" ' . ($row->status == "barcorded" ? "selected" : "") . '>barcorded</option>
                        <option value="Done" ' . ($row->status == "Done" ? "selected" : "") . '>Done</option>
                        <option value="pending" ' . ($row->status == "pending" ? "selected" : "") . '>pending</option>
                    </select>
                </td>
                <td>
                        ' . $refferenceDropdown . '
                </td>
                <td><input type="text" value="' . htmlspecialchars($row->blooddraw) . '" name="blooddraw" class="datetimepicker"></td>
                <td><input type="text" value="' . htmlspecialchars($row->repcollected) . '" name="repcollected" class="datetimepicker"></td>
                <td><input type="number" value="' . htmlspecialchars($row->fastinghours) . '" name="fastinghours"></td>
                <td><input type="text" value="' . htmlspecialchars($row->fastingtime) . '" name="fastingtime" class="timepicker"></td>
                
               <td>
                        ' . $labUserOptions . '
                </td>
                <td><input type="number" value="' . htmlspecialchars($row->reference_in_invoice) . '" name="reference_in_invoice"></td>
                <td>
                        ' . $testGroupDropdown . '
                </td>
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
    
    function nullIfEmpty($val) {
        return (is_null($val) || trim($val) === '') ? null : $val;
    }
    $updateData = array(
        'sampleNo' => Input::get('sampleNo'),
        'date' => Input::get('date'),
        'patient_pid' => Input::get('patient_pid'),
        'arivaltime' => Input::get('arivaltime'),
        'finishtime' => nullIfEmpty(Input::get('finishtime')),
        'finishdate' => nullIfEmpty(Input::get('finishdate')),
        'collecteddate' => nullIfEmpty(Input::get('collecteddate')),
        'status' => Input::get('status'),
        'refference_idref' => Input::get('refference_idref') ?: null,
        'blooddraw' => nullIfEmpty(Input::get('blooddraw')),
        'repcollected' => nullIfEmpty(Input::get('repcollected')),
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

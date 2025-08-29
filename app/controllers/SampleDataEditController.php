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
        $branchCode = Input::get('branch_code');

        $query = DB::table('lps')
            ->where('Lab_lid', '=', $labId)
            ->where('date', '=', $sample_date);

    $center = explode(':', $branchCode); 
    $mainLabValues = array('%:@', '%');
    if (!empty($sample_no)) {
        
        $query->where('sampleNo', 'like', $sample_no.'%');
    } else {
       
        if (!empty($branchCode) && !in_array($branchCode, $mainLabValues) && isset($center[1]) && $center[1] != '') {
           
            $query->whereRaw("sampleNo REGEXP '^" . $center[1] . "'");
        } else {
            
            $query->whereRaw("sampleNo REGEXP '^[0-9]'");
        }
    }
    $records = $query->select(
        'lpsid', 'date', 'sampleNo', 'patient_pid', 'arivaltime', 'finishtime', 'finishdate',
        'collecteddate', 'status', 'refference_idref', 'blooddraw', 'repcollected',
        'fastinghours', 'fastingtime', 'entered_uid', 'reference_in_invoice',
        'Testgroup_tgid', 'urgent_sample'
    )->get();

    
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
                <td><input type="text" value="' . htmlspecialchars($row->sampleNo) . '" name="sampleNo" style="border:none;"></td>
                <td><input type="text" value="' . htmlspecialchars($row->date) . '" name="date" class="datepicker" style="border:none;"></td>
                <td><input type="number" value="' . htmlspecialchars($row->patient_pid) . '" name="patient_pid" style="border:none;"></td>
                <td><input type="text" value="' . htmlspecialchars($row->arivaltime) . '" name="arivaltime" class="timepicker" style="border:none;"></td>
                <td><input type="text" value="' . htmlspecialchars($row->finishtime) . '" name="finishtime" class="timepicker" style="border:none;"></td>
                <td><input type="text" value="' . htmlspecialchars($row->finishdate) . '" name="finishdate" class="datepicker" style="border:none;"></td>
                <td><input type="text" value="' . htmlspecialchars($row->collecteddate) . '" name="collecteddate" class="datepicker" style="border:none;"></td>
                <td>
                    <select name="status" style="border:none;">
                        <option value="Accepted" ' . ($row->status == "Accepted" ? "selected" : "") . '>Accepted</option>
                        <option value="barcorded" ' . ($row->status == "barcorded" ? "selected" : "") . '>barcorded</option>
                        <option value="Done" ' . ($row->status == "Done" ? "selected" : "") . '>Done</option>
                        <option value="pending" ' . ($row->status == "pending" ? "selected" : "") . '>pending</option>
                    </select>
                </td>
                <td>
                        ' . str_replace('<select ', '<select style="border:none;" ', $refferenceDropdown) . '
                </td>
                <td><input type="text" value="' . htmlspecialchars($row->blooddraw) . '" name="blooddraw" class="datetimepicker" style="border:none;"></td>
                <td><input type="text" value="' . htmlspecialchars($row->repcollected) . '" name="repcollected" class="datetimepicker" style="border:none;"></td>
                <td><input type="number" value="' . htmlspecialchars($row->fastinghours) . '" name="fastinghours" style="border:none;"></td>
                <td><input type="text" value="' . htmlspecialchars($row->fastingtime) . '" name="fastingtime" class="timepicker" style="border:none;"></td>
                
               <td>
                        ' . str_replace('<select ', '<select style="border:none;" ', $labUserOptions) . '
                </td>
                <td><input type="number" value="' . htmlspecialchars($row->reference_in_invoice) . '" name="reference_in_invoice" style="border:none;"></td>
                <td>
                        ' . str_replace('<select ', '<select style="border:none;" ', $testGroupDropdown) . '
                </td>
                <td>
                    <select name="urgent_sample" style="border:none;">
                        <option value="0" ' . ($row->urgent_sample == "0" ? "selected" : "") . '>0 (Normal)</option>
                        <option value="1" ' . ($row->urgent_sample == "1" ? "selected" : "") . '>1 (Urgent)</option>
                    </select>
                </td>
                <td><input type="checkbox" name="select_row" class="updateRowBtn" data-lpsid="' . htmlspecialchars($row->lpsid) . '" value="' . htmlspecialchars($row->lpsid) . '"></td>
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


    if (empty($lpsid)) {
        return Response::json([
            'success' => false,
            'message' => 'lpsid Not Found. Can\'t Update '
        ]);
    }

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
        'fastinghours' => nullIfEmpty(Input::get('fastinghours')),
        'fastingtime' => nullIfEmpty(Input::get('fastingtime')),
        'entered_uid' => Input::get('entered_uid'),
        'reference_in_invoice' => nullIfEmpty(Input::get('reference_in_invoice')),
        'Testgroup_tgid' => Input::get('Testgroup_tgid'),
        'urgent_sample' => Input::get('urgent_sample')
    );

    DB::table('lps')->where('lpsid', $lpsid)->update($updateData);

    return Response::json(['success' => true]);
}


}

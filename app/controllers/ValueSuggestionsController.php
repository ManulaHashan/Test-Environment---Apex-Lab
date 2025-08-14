<?php

use Illuminate\Support\Facades\DB;

if (!isset($_SESSION)) {
    session_start();
}

//error_reporting(0);

class ValueSuggestionsController extends Controller
{



     public function getParameters()
    {
        $tgid = Input::get('tgid');
        $lid = $_SESSION['lid'];

        $results = DB::select(
            "SELECT test_tid, reportname 
             FROM Lab_has_test 
             WHERE Lab_lid = ? AND Testgroup_tgid = ? 
             ORDER BY orderno ASC", 
             [$lid, $tgid]
        );

        $options = '<option value="">-- Select Parameter --</option>';
        foreach ($results as $res) {
            $options .= '<option value="'.$res->test_tid.'">'.$res->test_tid.' : '.$res->reportname.'</option>';
        }

        return $options;
    }

    public function getValuesRecords()
    {

        $lid = $_SESSION['lid'];
        $test_tid = Input::get('test_tid');

        $results = DB::select("
            SELECT DISTINCT
                a.test_tid,
                a.value
            FROM
                lps_has_test a
            JOIN
                lps b ON b.lpsid = a.lps_lpsid
            WHERE
                b.Lab_lid = ?
                AND a.value IS NOT NULL
                AND a.test_tid = ?
            ORDER BY
                a.value
        ", [$lid, $test_tid]);

        
        return Response::json($results);
    }

    public function saveToSuggestions() {
        $selected = Input::get('selected'); 

        if(empty($selected) || !is_array($selected)) {
            return Response::json(['success' => false, 'message' => 'No records selected.']);
        }

        foreach($selected as $item) {
            $test_tid = $item['test_tid'];
            $value = $item['value'];

            // Check if record already exists
            $exists = DB::table('value_suggests')
                        ->where('lhtid', $test_tid)
                        ->where('value', $value)
                        ->first();

            if(!$exists) {
                DB::table('value_suggests')->insert([
                    'lhtid' => $test_tid,
                    'value' => $value

                ]);
            }
        }

        return Response::json(['success' => true, 'message' => 'Selected records saved successfully.']);
    }

    public function deleteValues()
    {
        $selected = Input::get('selected'); 

        if (empty($selected) || !is_array($selected)) {
            return Response::json(['success' => false, 'message' => 'No records selected.']);
        }

        foreach ($selected as $item) {
            $test_tid = $item['test_tid'];
            $value = $item['value'];

            DB::table('lps_has_test')
                ->where('test_tid', $test_tid)
                ->where('value', $value)
                ->delete();
        }

        return Response::json(['success' => true, 'message' => 'Selected records deleted successfully.']);
    }

    // Load suggestions
    public function getSuggestions()
    {
        $test_tid = Input::get('test_tid');

        if (!$test_tid) {
            return Response::json([]);
        }

        $results = DB::select("
            SELECT id,lhtid, value
            FROM value_suggests
            WHERE lhtid = ?
            ORDER BY value
        ", [$test_tid]);

        return Response::json($results);
    }

    // Insert new value
    public function insertSuggestion()
    {
        $lhtid = Input::get('lhtid');
        $value = Input::get('value');

        if (!$lhtid || !$value) {
            return Response::json(['success'=>false,'message'=>'Invalid data']);
        }

        // Duplicate check
        $exists = DB::table('value_suggests')->where('lhtid',$lhtid)->where('value',$value)->first();
        if($exists){
            return Response::json(['success'=>false,'message'=>'Value already exists']);
        }

        DB::table('value_suggests')->insert([
            'lhtid'=>$lhtid,
            'value'=>$value
        ]);

        return Response::json(['success'=>true,'message'=>'Value inserted']);
    }

    // Update value
    public function updateSuggestion()
    {
        $id = Input::get('id');
        $new_value = trim(Input::get('value'));

        if(!$id || $new_value === ''){
            return Response::json(['success'=>false,'message'=>'Invalid data']);
        }

        $affected = DB::table('value_suggests')
                    ->where('id', $id) 
                    ->update(['value' => $new_value]);

        if($affected){
            return Response::json(['success'=>true,'message'=>'Value updated']);
        } else {
            return Response::json(['success'=>false,'message'=>'No record updated']);
        }
    }



    // Delete value
   public function deleteSuggestion()
    {
        $id = Input::get('id');
        if(!$id){
            return Response::json(['success'=>false,'message'=>'Invalid id']);
        }

        $deleted = DB::table('value_suggests')->where('id',$id)->delete();

        if($deleted){
            return Response::json(['success'=>true,'message'=>'Value deleted']);
        } else {
            return Response::json(['success'=>false,'message'=>'No record deleted']);
        }
    }


}
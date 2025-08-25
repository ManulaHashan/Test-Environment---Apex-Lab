<?php

use Illuminate\Support\Facades\DB;

if (!isset($_SESSION)) {
    session_start();
}

//error_reporting(0);

class SuggestionButtonsController extends Controller
{
   

    // **********************Function to Save Button*************************************** 
  public function save_Button()
    {
        // Fetch values sent in POST request
        $testGroupId = Input::get('testGroupId');
        $colorValue = Input::get('colorValue');
        $orderNo = Input::get('orderNo');
        $lid = $_SESSION['lid'];

        if (!$testGroupId || !$colorValue || !$orderNo) {
            return Response::json(['error' => 'Invalid input']);
        }

        
        $existingCode = DB::table('reg_test_suggestions')
            ->where('tgid', '=', $testGroupId)
            ->where('lab_lid', '=', $_SESSION['lid'])
            ->exists();

        if ($existingCode) {
            return Response::json(['error' => 'exist']);
        }

        
        DB::statement("
            INSERT INTO reg_test_suggestions (lab_lid, tgid, color, orderno) 
            VALUES (?, ?, ?, ?)", [$lid, $testGroupId, $colorValue, $orderNo]);

        return Response::json(['error' => 'saved']);
    }

    public function getButtonStyles()
    {
        $lid = $_SESSION['lid'];

        
        $records = DB::select("
            SELECT rts.tgid, rts.color, rts.orderno, tg.name as testGroupName
            FROM reg_test_suggestions rts
            JOIN Testgroup tg ON rts.tgid = tg.tgid
            WHERE rts.lab_lid = ?
            ORDER BY rts.orderno ASC", [$lid]);

        return Response::json(['error' => 'success', 'data' => $records]);
    }

   public function update_Button()
    {
        
        $testGroupId = Input::get('testGroupId');
        $colorValue = Input::get('colorValue');
        $orderNo = Input::get('orderNo');
        $lid = $_SESSION['lid'];

        
        if (!$testGroupId || !$colorValue || !$orderNo) {
            return Response::json(['error' => 'Invalid input']);
        }

       
        $existingCode = DB::table('reg_test_suggestions')
            ->where('tgid', '=', $testGroupId)
            ->where('lab_lid', '=', $_SESSION['lid'])
            ->exists();

        if (!$existingCode) {
            return Response::json(['error' => 'not_found']);
        }

        // Check if orderno already exists for another record
        $orderNoExists = DB::table('reg_test_suggestions')
            ->where('orderno', '=', $orderNo)
            ->where('tgid', '!=', $testGroupId) // Exclude current record
            ->where('lab_lid', '=', $_SESSION['lid'])
            ->exists();

        if ($orderNoExists) {
            return Response::json(['error' => 'order_exists']);
        }

        // Update the record
        DB::statement("
            UPDATE reg_test_suggestions 
            SET color = ?, orderno = ? 
            WHERE tgid = ? AND lab_lid = ?", 
            [$colorValue, $orderNo, $testGroupId, $lid]);

        return Response::json(['error' => 'updated']);
    }

    public function delete_Button()
    {
        
        $testGroupId = Input::get('testGroupId');
        $lid = $_SESSION['lid'];

       
        if (!$testGroupId) {
            return Response::json(['error' => 'Invalid input']);
        }

       
        $existingCode = DB::table('reg_test_suggestions')
            ->where('tgid', '=', $testGroupId)
            ->where('lab_lid', '=', $_SESSION['lid'])
            ->exists();

        if (!$existingCode) {
            return Response::json(['error' => 'not_found']);
        }

        
        DB::statement("
            DELETE FROM reg_test_suggestions 
            WHERE tgid = ? AND lab_lid = ?", 
            [$testGroupId, $lid]);

        return Response::json(['error' => 'deleted']);
    }
  
}

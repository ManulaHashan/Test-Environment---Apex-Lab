<?php

use Illuminate\Support\Facades\DB;

if (!isset($_SESSION)) {
    session_start();
}

//error_reporting(0);

class DocRefferenceController extends Controller
{
    // Function to get all details 
    public function getAllDetails()
    {
        $name = Input::get('name');  
        $code = Input::get('code');  

        // Base query to fetch data
        $query = DB::table('refference')
            ->select('idref', 'code', 'name', 'address', 'tpno', 'degree', 'join_date')
            ->where('lid', '=', $_SESSION['lid']);

        // Apply filters if they exist
        if (!empty($name)) {
            $query->where('name', 'LIKE', "%{$name}%");
        }
        if (!empty($code)) {
            $query->where('code', 'LIKE', "%{$code}%");
        }

        // Get results
        $Result = $query->orderBy('name', 'ASC')->get();

        // Prepare the output
        if (count($Result) > 0) {
            $output = '';
            foreach ($Result as $res) {
                $idref = $res->idref;
                $code = $res->code;
                $name = $res->name;
                $address = $res->address;
                $tp = $res->tpno;
                $degree = $res->degree;
                $joindate = $res->join_date;

                $output .= '<tr class="phistr" style="cursor: pointer;">
                <td>' . $code . '</td>
                <td   onclick="selectRecord(' .
                    $idref . ', \'' . htmlspecialchars($code) . '\', \'' . htmlspecialchars($name) . '\', \'' .
                    htmlspecialchars($address) . '\', \'' . htmlspecialchars($tp) . '\', \'' . htmlspecialchars($degree) . '\', \'' .
                    htmlspecialchars($joindate) . '\')">' . htmlspecialchars($name) . '</td>
                <td>' . htmlspecialchars($address) . '</td>
                <td >' . htmlspecialchars($tp) . '</td>
                <td >' . htmlspecialchars($degree) . '</td>
                <td >' . htmlspecialchars($joindate) . '</td>
                <td >' . $idref . '</td>
                <td align="center">
                    <input type="checkbox" value="' . $idref . '" class="ref_chkbox">
                </td>
            </tr>';
            }
            echo $output; // Output the filtered results
        } else {
            // If no records found, show a message
            echo '<tr><td colspan="8" style="text-align: center;">No records found</td></tr>';
        }
    }



    public function save_Reference()
    {
        // alert('save reference');
        // Get data from the request
        $refID = Input::get('refID');
        $refName = Input::get('refName');
        $refAddress = Input::get('refAddress');
        $refContact = Input::get('refContact');
        $refDegree = Input::get('refDegree');
        $refJoinedDate = Input::get('refJoinedDate');


        $existingCode = DB::table('refference') 
            ->where('code', '=', $refID)
            ->where('lid', '=', $_SESSION['lid']) 
            ->exists();

        if ($existingCode) {
            // If code exists, return error message

            // return Response::json(['error' => 'exist']);

            return Response::json(['success' => true, 'error' => 'exist']);
        }


        DB::statement("
    INSERT INTO refference (code, name, address, tpno, degree, join_date, lid) 
    VALUES (
        '". $refID."', 
        '". $refName."', 
        '". $refAddress."', 
        '". $refContact."', 
        '". $refDegree."', 
        '". $refJoinedDate."', 
        '". $_SESSION['lid'] . "'
    )
");
        return Response::json(['error' => 'saved']);

      
    }

    // *****************Function to delete reference*****************
    public function delete_Reference()
    {
        // Get the reference ID from the request
        $refID = Input::get('refID');

        if (empty($refID)) {
            return "error"; 
        }

        // Check if the reference ID is being used in the lps table
        $isUsed = DB::table('lps')
        ->where('refference_idref', '=', $refID)
        ->exists();

        if ($isUsed) {
            return "cannot_delete"; 
        }

        // Delete the reference from the database
        DB::table('refference')
        ->where('idref', '=', $refID)
        ->where('lid', '=', $_SESSION['lid'])
        ->delete();

        return "deleted"; // Return success message
    }


    //***************** */ Function to update reference*****************
    public function update_Reference()
    {
        // Get data from the request
        $refID = Input::get('refID');
        $refCode = Input::get('refcode');
        $refName = Input::get('refName');
        $refAddress = Input::get('refAddress');
        $refContact = Input::get('refContact');
        $refDegree = Input::get('refDegree');
        $refJoinedDate = Input::get('refJoinedDate');

        // Check if the code already exists for another reference
        $existingCode = DB::table('refference')
        ->where('code', '=', $refCode)
            ->where('lid', '=', $_SESSION['lid'])
            ->where('idref', '!=', $refID) // Exclude the current record
            ->exists();

        if ($existingCode) {
            // If the code exists, return error response
            return Response::json(['success' => false, 'error' => 'exist']);
        }

        // Update the reference in the database
        $updated = DB::table('refference')
        ->where('idref', $refID)
            ->where('lid', $_SESSION['lid'])
            ->update([
                'code' => $refCode,
                'name' => $refName,
                'address' => $refAddress,
                'tpno' => $refContact,
                'degree' => $refDegree,
                'join_date' => $refJoinedDate,
            ]);

        if ($updated) {
            // If update is successful, return success response
            return Response::json(['success' => true, 'error' => 'updated']);
        } else {
            // If no rows were affected, return an error
            return Response::json(['success' => false, 'error' => 'not_updated']);
        }
    }

    //***********Function for view invoive count********************* */
    // public function getInvoiceCountForReference()
    // {
    //     $refID = Input::get('refID'); // Get the reference ID from the request

    //     if (empty($refID)) {
    //         return Response::json(['success' => false, 'error' => 'Reference ID is required.']);
    //     }

    //     // Fetch the count of records for the given reference ID
    //     $count = DB::table('lps')
    //     ->where('refference_idref', '=', $refID)
    //         ->count();

    //     // Return the count as a JSON response
    //     return Response::json(['success' => true, 'count' => $count]);
    // }

    //***********Function for Merge Refference********************* */
    public function merge_Reference()
    {
        $refID = Input::get('Main_refID');
        $effected_refIds = Input::get('effected_refIds');
        $lpsArray = [];

        if (is_array($effected_refIds) && count($effected_refIds) >= 1) {
            // Collect all lpsids that are associated with the effected reference ids
            foreach ($effected_refIds as $check) {
                $lpsResults = DB::table('lps')
                    ->where('refference_idref', '=', $check)
                    ->lists('lpsid');
                $lpsArray = array_merge($lpsArray, (array)$lpsResults);
            }

            // Update lps records with the new reference ID
            foreach ($lpsArray as $lpsid) {
                DB::table('lps')
                    ->where('lpsid', $lpsid)
                    ->update(['refference_idref' => $refID]);
            }

            // Delete the effected references
            foreach ($effected_refIds as $check) {
                DB::table('refference')
                    ->where('idref', $check)
                    ->delete();
            }

            return Response::json(['success' => true, 'message' => 'Refference merged successfully']);
        } else {
            return Response::json(['success' => false, 'message' => 'Refference not selected']);
        }
    }


public function getInvoiceCountFor_Reference(){
    $refID = Input::get('refID'); // Get the reference ID from the request

    $count = DB:: table('lps')->where('refference_idref','=',$refID)->count();
   return Response::json(['success' => true, 'count' => $count]);
}

}

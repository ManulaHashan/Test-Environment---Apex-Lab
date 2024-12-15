<?php

use Illuminate\Support\Facades\DB;

if (!isset($_SESSION)) {
    session_start();
}

//error_reporting(0);

class BranchWiseTestMappingController extends Controller
{
    // **********************Function to get all details*************************************** 
    public function getAllBranchWiseTests()
    {
        $name = Input::get('name');
        $labBranch = Input::get('labBranchDropdown');
        $labId = $_SESSION['lid'];

        // Base query
        $query = DB::table('Testgroup as c')
        ->where('c.lab_lid', '=', $labId)
            ->select('c.tgid', 'c.name as test_name', 'c.price')
            ->orderBy('c.name', 'asc');

        // Add search condition if name is provided
        if (!empty($name)) {
            $query->where('c.name', 'LIKE', $name . '%');
        }

        // If labBranch is not "%" and is set, modify the query for specific branch
        if ($labBranch !== "%" && !empty($labBranch)) {
            $query = DB::table('labbranches_has_Testgroup as b')
            ->join('labbranches as a', 'a.bid', '=', 'b.bid')
            ->join('Testgroup as c', 'b.tgid', '=', 'c.tgid')
            ->where('b.bid', '=', $labBranch)
                ->where('c.lab_lid', '=', $labId)
                ->select('b.tgid', 'c.name as test_name', 'b.price')
                ->orderBy('c.name', 'asc');

            // Add search condition to the branch-specific query if name is provided
            if (!empty($name)) {
                $query->where('c.name', 'LIKE', $name . '%');
            }
        }

        // Fetch the results
        $Result = $query->get();

        // Return results
        if (count($Result) > 0) {
            $output = '';
            foreach ($Result as $res) {
                $tgid = $res->tgid;
                $testName = $res->test_name;
                $price = $res->price;
                $output .= '<tr>
                <td>' . htmlspecialchars($tgid) . '</td>
                <td>' . htmlspecialchars($testName) . '</td>
                <td>' . htmlspecialchars($price) . '</td>
                <td><input type="checkbox" class="select-test" value="' . $tgid . '"></td>
            </tr>';
            }
            echo $output;
        } else {
            echo '<tr><td colspan="4" style="text-align: center;">No tests found for this branch.</td></tr>';
        }
    }

    public function getBranch_Details()
    {
        $branchData = DB::table('labbranches')
            ->where('Lab_lid', '=', $_SESSION['lid'])
            ->select('bid', 'name', 'code')
            ->orderBy('name', 'asc')
            ->get();

        if (count($branchData) > 0) {
            $output = '';
            foreach ($branchData as $branch) {
                $brnID = $branch->bid;
                $brnName = $branch->name;
                $brnCode = $branch->code;

                $output .= '<tr style="cursor: pointer;">
                    <td onclick="selectBranch(' . $brnID . ', \'' . htmlspecialchars($brnName) . '\', \'' . htmlspecialchars($brnCode) . '\')">' . htmlspecialchars($brnID) . '</td>
                    <td onclick="selectBranch(' . $brnID . ', \'' . htmlspecialchars($brnName) . '\', \'' . htmlspecialchars($brnCode) . '\')">' . htmlspecialchars($brnName) . '</td>
                    <td onclick="selectBranch(' . $brnID . ', \'' . htmlspecialchars($brnName) . '\', \'' . htmlspecialchars($brnCode) . '\')">' . htmlspecialchars($brnCode) . '</td>
                    <td><input type="checkbox" class="select-test" value="' . htmlspecialchars($brnID) . '"></td>
                </tr>';
            }
            echo $output;
        } else {
            echo '<tr><td colspan="2" style="text-align: center;">No Branches Found</td></tr>';
        }
    }


    // **********************Function to Save Branch*************************************** 
    public function save_Branch()
    {
        // Fetch values sent in POST request
        $branchName = Input::get('branchName');
        $branchCode = Input::get('branchCode');

        if (!$branchName || !$branchCode) {
            return Response::json(['error' => 'Invalid input']);
        }

        $nameExists  = DB::table('labbranches')
            ->where('code', '=', $branchCode)
            ->where('name', '=', $branchName)
            ->where('Lab_lid', '=', $_SESSION['lid'])
            ->exists();

        if ($nameExists) {
            return Response::json(['success' => true, 'error' => 'name_exist']);
        }
        $codeExists = DB::table('labbranches')
        ->where('code', '=', $branchCode)
        ->where('Lab_lid', '=', $_SESSION['lid'])
        ->exists();
        if ($codeExists) {
            return Response::json(['success' => true, 'error' => 'code_exist']);
        }

        DB::statement("
        INSERT INTO labbranches ( Lab_lid,name, code) 
        VALUES (?, ?, ?)", [$_SESSION['lid'], $branchName, $branchCode, ]);

        return Response::json(['error' => 'saved']);
    }


    //***************** */ Function to update Branch*****************
    public function update_Branch()
    {
        $brnID = Input::get('Branch_id');
        $brnName = Input::get('Branch_name');
        $brnCode = Input::get('Branch_code'); // This will not be updated

        if (!$brnID || !$brnName || !$brnCode) {
            return Response::json(['success' => false, 'error' => 'Invalid input']);
        }

        // Check if the branch name already exists, excluding the current branch being updated
        $existingBranchName = DB::table('labbranches')
        ->where('name', '=', $brnName)
            ->where('Lab_lid', '=', $_SESSION['lid'])
            ->where('bid', '<>', $brnID)
            ->exists();

        if ($existingBranchName) {
            return Response::json([
                'success' => false,
                'error' => 'Branch_name_exist'
            ]);
        }

        // Perform the actual update (only updating the branch name)
        $updated = DB::table('labbranches')
        ->where('bid', '=', $brnID)
            ->update([
                'name' => $brnName, // Only update the name, not the code
                // 'code' remains unchanged
            ]);

        if ($updated) {
            return Response::json(['success' => true, 'error' => 'updated']);
        }

        return Response::json(['success' => false, 'error' => 'not_updated']);
    }


}
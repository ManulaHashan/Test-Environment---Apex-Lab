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




}

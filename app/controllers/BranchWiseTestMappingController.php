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

        $labBranch = Input::get('labBranchDropdown');

        $Result = DB::table('labbranches_has_Testgroup as b')
        ->join('labbranches as a', 'a.bid', '=', 'b.bid')
        ->join('Testgroup as c', 'b.tgid', '=', 'c.tgid')
        ->where('b.bid', '=', $labBranch) 
        ->select('b.tgid', 'c.name as test_name', 'b.price')
        ->get();


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

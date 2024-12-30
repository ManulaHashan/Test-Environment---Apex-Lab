<?php


use Illuminate\Support\Facades\DB;


if (!isset($_SESSION)) {
    session_start();
}



class SampleContainerConfigController extends Controller
{

    // **********************Function to get all details*************************************** 
    public function getAllDetails()
    {
        $name = Input::get('name');

        $query = DB::table('Testgroup as a')
        ->join('sample_containers as b', 'a.sample_containers_scid', '=', 'b.scid')
        ->select('a.tgid', 'a.name as test_name', 'b.name as container_name')
        ->where('a.Lab_lid', '=', $_SESSION['lid']);

        if (!empty($name)) {
            $query->where('a.name', 'LIKE', "{$name}%"); 
        }


        $Result = $query->orderBy('a.name', 'ASC')->get(); 


        if (count($Result) > 0) {
            $output = '';
            foreach ($Result as $res) {
                $tgid = $res->tgid;
                $aname = $res->test_name; 
                $bname = $res->container_name; 

                $output .= '<tr class="phistr" style="cursor: pointer;">
                <td>' . htmlspecialchars($tgid) . '</td>
                <td>' . htmlspecialchars($aname) . '</td>
                <td align="center">' . htmlspecialchars($bname) . '</td>
                <td align="center">
                    <input type="checkbox" value="' . htmlspecialchars($tgid) . '" class="select-test">
                </td>
            </tr>';
            }
            echo $output; 
        } else {
           
            echo '<tr><td colspan="8" style="text-align: center;">No records found</td></tr>';
        }
    }

    public function updateContainers()
    {
        // Fetch input data
        $selectedTests = Input::get('selectedTests'); // This will be an array
        $containerId = Input::get('containerId');

        // Validate the input
        if (empty($selectedTests) || empty($containerId)) {
            return Response::json([
                'success' => false,
                'message' => 'Invalid input data.',
            ]);
        }

        try {
            // Begin database transaction
            DB::transaction(function () use ($selectedTests, $containerId) {
                foreach ($selectedTests as $testId) {
                    DB::table('Testgroup')
                    ->where('tgid', $testId)
                        ->where('Lab_lid', $_SESSION['lid'])
                        ->update(['sample_containers_scid' => $containerId]);
                }
            });

            return Response::json([
                'success' => true,
                'message' => 'Container updated successfully.',
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'success' => false,
                'message' => 'Error updating container: ' . $e->getMessage(),
            ]);
        }
    }



}

<?php

use Illuminate\Support\Facades\DB;

if (!isset($_SESSION)) {
    session_start();
}



class PackageCreateController extends Controller
{
    // Function to get all details to table from database
    public function getAllTestPackages()
    {
        // Base query to fetch data
        $query = DB::table('labpackages')
            ->select('idlabpackages', 'name', 'price')
            ->where('Lab_lid', '=', $_SESSION['lid'])
            ->where('isactive', '=', 1);  

        // Get results
        $Result = $query->orderBy('idlabpackages', 'ASC')->get();

        // Prepare the output
        if (count($Result) > 0) {
            $output = '';
            foreach ($Result as $res) {
                $idlabpackages = $res->idlabpackages;
                $pkgName = $res->name;
                $pkgPrice = $res->price;

                $output .= '<tr style="cursor: pointer;" onclick="selectRecord(' . $idlabpackages . ', \'' . htmlspecialchars($pkgName) . '\', \'' . htmlspecialchars($pkgPrice) . '\')">';
                $output .= '<td>' . htmlspecialchars($idlabpackages) . '</td>';
                $output .= '<td>' . htmlspecialchars($pkgName) . '</td>';
                $output .= '<td>' . htmlspecialchars($pkgPrice) . '</td>';
                $output .= '</tr>';
            }
            echo $output;  // Output the filtered results
        } else {
            // If no records found, show a message
            echo '<tr><td colspan="3" style="text-align: center;">No records found</td></tr>';
        }
    }

    // *****************Function to Save Package*****************
    public function save_Package()
    {
        $pkgName = Input::get('pkgName');
        $pkgPrice = Input::get('pkgPrice');
        $pkgTests = Input::get('pkgTests');

        // Check if package name already exists
        $existingPackage = DB::table('labpackages')
        ->where('name', $pkgName)
            ->where('Lab_lid', $_SESSION['lid'])
            ->exists();

        if ($existingPackage) {
            return Response::json(['error' => 'exist']);
        }

        DB::beginTransaction();
        try {
            // Insert into 'labpackages'
            $pkgID = DB::table('labpackages')->insertGetId([
                'name' => $pkgName,
                'price' => $pkgPrice,
                'Lab_lid' => $_SESSION['lid'],
                'isactive' => '1'
                
            ]);
           

            // Insert into 'Testgroup_has_labpackages'
            foreach ($pkgTests as $testID) {
                DB::table('Testgroup_has_labpackages')->insert([
                    'tgid' => $testID,
                    'idlabpackages' => $pkgID
                ]);
            }
            
            DB::commit();
            return Response::json(['error' => 'saved']);
            
            
        } catch (Exception $e) {
            DB::rollBack();
            return Response::json(['error' => 'saveerror','message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }
    }

    // Function for load package tests for table
    public function loadPackageTests()
    {
        $pkgID = Input::get('packageID');

        $query = DB::table('Testgroup_has_labpackages as b')
        ->join('labpackages as a', 'a.idlabpackages', '=', 'b.idlabpackages')
        ->join('Testgroup as c', 'c.tgid', '=', 'b.tgid')
        ->where('a.Lab_lid', '=', $_SESSION['lid'])
        ->where('b.idlabpackages', '=', $pkgID)
        ->select('a.idlabpackages', 'c.name', 'b.tgid')
        ->orderBy('b.tgid', 'ASC')  // Adjust the column for ordering if needed
        ->get();


        $tgarray = array();
        // Prepare the output
        if (count($query) > 0) {
            $output = '';
            $tgarray = []; // Initialize the array

            foreach ($query as $res) {
                $pkgName = $res->name;
                $tgid = $res->tgid;
                $tgarray[] = $tgid;

                $output .= '<tr>';
                $output .= '<td>' . htmlspecialchars($tgid) . '</td>';
                $output .= '<td>' . htmlspecialchars($pkgName) . '</td>';
                $output .= '<td> <button style ="background-color: #ff4d4d; 
                                color: white; 
                                padding: 5px 10px;
                                    border-radius: 5px;
                                    border: none;
                                    cursor: pointer;" 
                                    onclick="removeTest(this,' . $tgid . ')">
                                    Remove
                                    </button> </td>';
                $output .= '</tr>';
            }

            // Create the response array
            $tgData = [
                'tbldata' => $output,
                'testgrouparray' => $tgarray
            ];

            // Send the response as JSON
            echo json_encode($tgData); // Convert PHP array to JSON string
        } else {
            // No records found case
            $tgData = [
                'tbldata' => '<tr><td colspan="3" style="text-align: center;">No records found</td></tr>',
                'testgrouparray' => []
            ];

            echo json_encode($tgData); // Convert PHP array to JSON string
        }

    }

    // Function for Update Data
    public function update_Package()
    {
        try {
            $pkgID = Input::get('pkgID');
            $pkgName = Input::get('pkgName');
            $pkgPrice = Input::get('pkgPrice');
            $pkgTests = Input::get('pkgTests');

            if (empty($pkgID) || empty($pkgName) || empty($pkgPrice) || empty($pkgTests)) {
                return json_encode(['error' => 'empty']);
            }

            DB::beginTransaction();

            // Check if the package exists
            $package = DB::table('labpackages')->where('idlabpackages', $pkgID)->first();
            if (!$package) {
                DB::rollBack();
                return json_encode(['error' => 'notfound']);
            }

            // Update the package details
            DB::table('labpackages')
            ->where('idlabpackages', $pkgID)
                ->update([
                    'name' => $pkgName,
                    'price' => $pkgPrice
                ]);

            //Remove old tests associated with this package
            DB::table('Testgroup_has_labpackages')->where('idlabpackages', $pkgID)->delete();

            // Insert updated tests
            foreach ($pkgTests as $testID) {
                DB::table('Testgroup_has_labpackages')->insert([
                    'idlabpackages' => $pkgID,
                    'tgid' => $testID
                ]);
            }

            DB::commit();

            return json_encode(['error' => 'updated']);
        } catch (\Exception $e) {
            DB::rollBack();
            return json_encode(['error' => 'updateerror', 'message' => $e->getMessage()]);
        }
    }

    //Function for Delete Data

    public function delete_Package()
    {
        try {
            $pkgID = Input::get('pkgID');

            if (!$pkgID) {
                return json_encode(['error' => 'notfound']);
            }
            DB::beginTransaction();


            $updated = DB::table('labpackages')
            ->where('idlabpackages', '=', $pkgID)
                ->update(['isactive' => 0]);

            if ($updated) {
                DB::commit();
                return json_encode(['error' => 'deleted']);
            } else {
                DB::rollBack();
                return json_encode(['error' => 'notfound']);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return json_encode(['error' => 'deleteerror', 'message' => $e->getMessage()]);
        }
    }



}
?>
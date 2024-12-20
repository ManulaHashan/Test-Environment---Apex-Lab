<?php

use Illuminate\Support\Facades\DB;

if (!isset($_SESSION)) {
    session_start();
}

//error_reporting(0);

class DiscountCreateController extends Controller
{
    // **********************Function to get all details*************************************** 
    public function getAllDetails()
    {
        $discounts = DB::table('Discount')
            ->where('Lab_lid', '=', $_SESSION['lid']) // Filter by user session
            ->select('did', 'name', 'value')
            ->orderByRaw("
        CASE 
            WHEN name REGEXP '^[0-9]+$' THEN CAST(name AS UNSIGNED) 
            ELSE 999999999 
        END ASC, 
        name ASC
    ")
            ->get();


        // Check if records exist and dynamically return table rows
        if (count($discounts) > 0) {
            $output = '';
            foreach ($discounts as $discount) {
                $disID = $discount->did; // Assuming ID is in the 'did' field
                $disName = $discount->name;
                $disValue = $discount->value;

                $output .= '<tr style="cursor: pointer;" onclick="selectRecord(' . $disID . ', \'' . htmlspecialchars($disName) . '\', \'' . htmlspecialchars($disValue) . '\')">
                        <td align="center">' . htmlspecialchars($disName) . '</td>
                        <td align="center">' . htmlspecialchars($disValue) . '</td>
                    </tr>';
            }
            echo $output;
        } else {
            echo '<tr><td colspan="2" style="text-align: center;">No Discounts Found</td></tr>';
        }
    }


    // **********************Function to Savd Discount*************************************** 
    public function save_Discount()
    {
        // Fetch values sent in POST request
        $discountName = Input::get('discountName');
        $discountValue = Input::get('discountValue');

        if (!$discountName || !$discountValue) {
            return Response::json(['error' => 'Invalid input']);
        }

        $existingCode = DB::table('Discount')
            ->where('name', '=', $discountName)
            ->where('Lab_lid', '=', $_SESSION['lid'])
            ->exists();

        if ($existingCode) {
            return Response::json(['success' => true, 'error' => 'exist']);
        }

        DB::statement("
        INSERT INTO Discount (name, value, Lab_lid) 
        VALUES (?, ?, ?)", [$discountName, $discountValue, $_SESSION['lid']]);

        return Response::json(['error' => 'saved']);
    }

    //***************** */ Function to update reference*****************
    public function update_Discount()
    {
        $discountID = Input::get('Discount_id');
        $discountName = Input::get('Discount_name');
        $discountValue = Input::get('Discount_value');

        if (!$discountID || !$discountName || !$discountValue) {
            return Response::json(['success' => false, 'error' => 'Invalid input']);
        }

        // Check if the name-value combination already exists
        $existingDiscount = DB::table('Discount')
            ->where('name', '=', $discountName)
            ->where('Lab_lid', '=', $_SESSION['lid'])
            ->where('did', '<>', $discountID) // Exclude the current discount ID
            ->exists();

        if ($existingDiscount) {
            return Response::json([
                'success' => false,
                'error' => 'exist'
            ]);
        }

        // Perform the actual update
        $updated = DB::table('Discount')
            ->where(
                'did',
                '=',
                $discountID
            )
            ->update([
                'name' => $discountName,
                'value' => $discountValue
            ]);

        if ($updated) {
            return Response::json(['success' => true, 'error' => 'updated']);
        }

        return Response::json(['success' => false, 'error' => 'not_updated']);
    }


    // *****************Function to delete reference*****************
    public function delete_Discount()
    {
        // Get the reference ID from the request
        $Discount_id = Input::get('Discount_id');

        if (empty($Discount_id)) {
            return "error";
        }



        // Delete the Discount from the database
        DB::table('Discount')
            ->where('did', '=', $Discount_id)
            ->where('Lab_lid', '=', $_SESSION['lid'])
            ->delete();

        return "deleted"; // Return success message
    }
}

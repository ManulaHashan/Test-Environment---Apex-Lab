<?php

//session_start();
if (!isset($_SESSION)) {
    session_start();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 

date_default_timezone_set('Asia/Colombo');

class GRNController extends Controller {

    function manageGRN() {
        if (Input::get('submit') != null) {
            if (Input::get('submit') == "Save GRN") {
                if (Input::get('supplier') != "") {   

                    $spid = "";
                    $Result = DB::select("select spid from supplier where company = '" . Input::get('supplier') . "' and Lab_lid = '" . $_SESSION['lid'] . "'");
                    foreach ($Result as $res) {                     
                        $spid = $res->spid;
                    }

                    if($spid == ""){
                        $spid = DB::table('supplier')->insertGetId(['company' => Input::get('supplier'), 'Lab_lid' => $_SESSION['lid']]);
                    } 

                    $today = date("Y-m-d");  
                    $today_time = date("H:i:s"); 

                    $grnID = DB::table('grn')->insertGetId(['supplier_spid' => $spid, 'date' => $today, 'total' => Input::get('tot'), 'dis' => Input::get('gdis'), 'gtotal' => Input::get('gtot'), 'paid' => '0', 'status' => Input::get('remark'), 'user_uid' => $_SESSION['luid'], 'time' => $today_time, 'refno' => Input::get('refno'), 'isactive' => '1', 'Lab_lid' => $_SESSION['lid']]);


                    $newPOST = Input::get(); 

                    unset($newPOST["supplier"]); 
                    unset($newPOST["refno"]); 
                    unset($newPOST["tot"]); 
                    unset($newPOST["gdis"]); 
                    unset($newPOST["remark"]); 
                    unset($newPOST["gtot"]); 
                    unset($newPOST["return"]); 
                    unset($newPOST["submit"]); 


                    $keys = array_keys ($newPOST); 

                    for ($i=0; $i < count($keys); $i++) { 

                        $arr = explode("#@",$newPOST[$keys[$i]]); 

                        $mat = $arr[0];
                        $qty = $arr[1];
                        $free = $arr[2];
                        $price = $arr[3];
                        $amount = $arr[4];
                        $lot = $arr[5];
                        $exp = $arr[6];

                        $effect_qty = $qty+$free;

                        $grnlmid = DB::table('labmaterials_has_grn')->insertGetId(['grn_id' => $grnID, 'Lab_has_materials_lmid' => $mat, 'qty' => $qty, 'free' => $free, 'lotno' => $lot, 'price' => $price, 'exp_date' => $exp, 'return' => '0']);  

                        //stock Manage
                        GRNController::addStock($mat, $effect_qty, '1', $exp, $amount, $grnlmid);  

                    }



                    return View::make('WiGRN')->with('msg', 'GRN Saved!');

                } else {
                    // return View::make('Witestgroups')->with('msg', 'Enter correct values!');
                }

            } else if (Input::get('submit') == "Delete GRN") {

                //update GRN material stock if all not used
                $mat_used = 0;
                $Result = DB::select("SELECT id FROM labmaterials_has_grn where grn_id = '".Input::get('selectedID')."'");
                foreach ($Result as $res) {
                    $Resultx = DB::select("SELECT idstock FROM stock where usedqty != '0' and labmaterials_has_grn_id = '".$res->id."'");
                    foreach ($Resultx as $resx) {
                        $mat_used = '1';
                    }
                }
                // 

                if($mat_used){
                    return View::make('WiGRN')->with('msg', 'GRN materials are used. GRN cant be removed!');
                }else{
                    DB::statement("update grn set isactive='0' where id = '".Input::get('selectedID')."'");   
                    return View::make('WiGRN')->with('msg', 'GRN Deleted!');  
                }

                
                

            } else if(Input::get('submit') == "View"){
             return View::make('WiGRN')->with('id', Input::get('grnid'));     
         }
     } else {
            // return View::make('Witestgroups')->with('msg', 'Enter correct values!');
     }
 }

 function ViewGRNs(){

    $status = Input::get("status");

    $status_phase = "";
    if($status == "Paid"){
        $status_phase = " and a.gtotal <= a.paid";
    }else if($status == "Due"){
        $status_phase = " and a.gtotal > a.paid";
    }
    
    $sup = Input::get("sup");



    $totAmount = 0;
    $totPaid = 0;

    $out = "<tr>
    <th width='5%' class='viewTHead' scope='col'>ID</th>
    <th width='5%' class='viewTHead' scope='col'>Date</th>
    <th width='30%' class='viewTHead' scope='col'>Supplier</th>
    <th width='15%' class='viewTHead' scope='col'>Ref. NO</th>
    <th width='10%' class='viewTHead' scope='col'>Grand Total</th>
    <th width='10%' class='viewTHead' scope='col'>Paid Rs.</th>
    <th width='10%' class='viewTHead' scope='col'>Due Rs.</th>
    <th width='15%' class='viewTHead' scope='col'>Added By</th> 
    </tr>";

    $Result = DB::select("SELECT a.*,b.company, c.fname from grn a, supplier b, user c where a.supplier_spid = b.spid and (SELECT user_uid from labUser where luid = a.user_uid) = c.uid and a.date between '".Input::get("d")."' and '".Input::get("dd")."' and b.company like '".$sup."' and isactive = '1' ".$status_phase);
    foreach ($Result as $res) {

        $due = $res->gtotal - $res->paid;

        $out .= "<tr><td>" . $res->id . "</td><td>" . $res->date . "</td> <td>" . $res->company . "</td> <td>" . $res->refno . "</td><td align='right'>" . $res->gtotal . "</td> <td align='right'>" . $res->paid . "</td> <td align='right'>".number_format($due, 2)."</td> <td  align='center'>" . $res->fname . "</td>"
        . "<td><form action='grnmaintain' method='POST'><input type='submit' name='submit' class='btn' style='margin:0px;' value='View'/> <input type='hidden' name='grnid' value='" . $res->id . "' /></form></td> </tr>";

        $totAmount += $res->gtotal;
        $totPaid += $res->paid;

    }

    $due = $totAmount - $totPaid;
    $due = number_format($due,2);

    $totAmount = number_format($totAmount, 2);
    $totPaid = number_format($totPaid, 2);    

    return $out . "/#/" . $totAmount. "/#/" . $totPaid. "/#/" . $due;
}


function addStock($lmid, $dqty, $stockUnit, $exp, $cost, $grnlmid) {
    $x = DB::insert("insert into stock(Lab_has_materials_lmid,qty,unit,expDate,usedqty,cost,labmaterials_has_grn_id) values('" . $lmid . "','" . $dqty . "','" . $stockUnit . "','" . $exp . "','0','" . $cost . "','" . $grnlmid . "')");
}

}

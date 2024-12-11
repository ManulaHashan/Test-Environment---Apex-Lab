<?php

if (!isset($_SESSION)) {
    session_start();
}
date_default_timezone_set('Asia/Colombo');

class EmployeeController extends Controller {

    function manageEmployee() {

        $lid = $_SESSION['lid'];
        $luid = $_SESSION['luid'];

        if (isset($_SESSION['luid']) && isset($_SESSION['lid'])) {

            $submit = Input::get("submit");

            $fname = Input::get("fname");
            $mname = Input::get("mname");
            $lname = Input::get("lname");
            $gender = Input::get("gender");
            $country = Input::get("country");
            $dob = Input::get("dob");
            $ut = Input::get("ut");
            $posi = Input::get("position");
            $branch = Input::get("branch");
            $hpno = Input::get("hpno");
            $status = Input::get("status");
            $nic = Input::get("nic");
            $address = Input::get("address");
            $email = Input::get("email");
            $tpno = Input::get("tpno");            
            
            $guest = Input::get("guest");


            $un = Input::get("un");
            $pw = Input::get("pw");
            $sec1 = Input::get("sec1");
            $sec2 = Input::get("sec2");

            if ($submit == "Save") {

                $privs = Input::get("privs");
                $priv = explode(",", $privs);

                $res = $this->checkExsists($fname, $mname, $lname, $gender, $dob, $nic, $email);

                if ($res == 0) {
                    $x = $this->addLabUser($fname, $mname, $lname, $gender, $tpno, $hpno, $address, $country, $email, $status, $branch, $ut, $dob, $nic, $posi, $un, $pw, $sec1, $sec2, $guest);
                    $y = $this->addUserToLab($lid, $x);
                    $z = $this->addPrivillegesToLabUser($x, $priv);
                    if ($x != 0 && $y != 0) {

                        $log_descreption = "User Added : Fname ".$fname." Privs ".$privs;

                        SystemLogs::saveChangeLog($_SESSION['lid'], $_SESSION['luid'], "Employee Management", "Save", $log_descreption);

                        return View::make('WiEmployeeMan');
                    } else {
                        echo "Error Adding Employee!";
                    }
                } else {
                    echo "User Already Exsist!";
                }
            } else if ($submit == "Update") {

                $privs = Input::get("privs");
                $priv = explode(",", $privs);

                $xluid = Input::get("luid");
                $x = $this->UpdateLabUser($xluid, $fname, $mname, $lname, $gender, $tpno, $hpno, $address, $country, $email, $status, $branch, $ut, $dob, $nic, $posi, $un, $pw, $sec1, $sec2, $guest);
                $z = $this->addPrivillegesToLabUser($xluid, $priv);
                if ($x != 0 && $z != 0) {

                    $log_descreption = "User Updated : Fname ".$fname." Privs ".$privs;

                    SystemLogs::saveChangeLog($_SESSION['lid'], $_SESSION['luid'], "Employee Management", "Update", $log_descreption);

                    return View::make('WiEmployeeMan');
                } else {
                    echo "Error Updating Employee!";
                }
            } else if ($submit == "Delete") {

                $xluid = Input::get("luid");
                $x = $this->deleteLabUser($xluid);
                if ($x != 0) {

                    $log_descreption = "User Deleted : LUID ".$xluid; 

                    SystemLogs::saveChangeLog($_SESSION['lid'], $_SESSION['luid'], "Employee Management", "Delete", $log_descreption);

                    return View::make('WiEmployeeMan');
                } else {
                    echo "Error Deleting Employee!";
                }
            } else if ($submit == "Terminate") {

                $xluid = Input::get("luid");
                $x = $this->terminateEmployee($lid, $xluid);
                if ($x != 0) {

                    $log_descreption = "User Terminated : Fname ".$fname;

                    SystemLogs::saveChangeLog($_SESSION['lid'], $_SESSION['luid'], "Employee Management", "Terminate", $log_descreption);

                    echo "Successfully Terminated!";
                } else {
                    echo "Error Terminating Employee!";
                }
            }
        } else {
            return View::make('WiEmployeeMan')->with('msg', 'operation error!');
        }
    }

    function getPrivilleges() {
        $luid = Input::get("luid");

        $result = "";
        $User = null;
        if (Labuser::where('luid', $luid)->exists()) {

            $User = Labuser::where('luid', $luid)->first();
            //echo $User->user_uid;
        }

        $quary = "select name from options where idoptions in (select options_idoptions from privillages where user_uid = '" . $User->user_uid . "')";
        try {
            $Result = DB::select($quary);
            if ($Result != null) {
                foreach ($Result as $rs) {
                    $result .= $rs->name . ",";
                }
            }
        } catch (Exception $e) {
            
        }
        return $result;
    }

    function checkExsists($fname, $mname, $lname, $gender, $dob, $nic, $email) {

        $exists = 0;
        $Gender = null;

        if (Gender::where('gender', $gender)->exists()) {
            $Gender = Gender::where('gender', $gender)->first();
        }

        $matchTheseUser = ['fname' => $fname, 'mname' => $mname, 'lname' => $lname, 'email' => $email, 'gender_idgender' => $Gender->idgender];

        if (User::where($matchTheseUser)->exists()) {
            $User = User::where($matchTheseUser)->first();
            $matchTheseLUser = ['user_uid' => $User->uid, 'nic' => $nic];

            if (Labuser::where($matchTheseLUser)->exists()) {
                $exists = 1;
            }
        }
        return $exists;
    }

    function addLabUser($fname, $mname, $lname, $gender, $tpno, $hpno, $address, $country, $email, $status, $branch, $ut, $dob, $nic, $posi, $un, $pw, $sec1, $sec2, $guest) {

        try {
            //get the gender object
            $Gender;
            if (Gender::where('gender', $gender)->exists()) {
                $Gender = Gender::where('gender', $gender)->first();
            } else {
                $Gender = new Gender();
                $Gender->gender = $gender;
                $Gender->save();
            }

            //get the usertype object
            $UserType;
            if (UserType::where('type', $ut)->exists()) {
                $UserType = UserType::where('type', $ut)->first();
            } else {
                $UserType = new UserType();
                $UserType->type = $ut;
                $UserType->save();
            }

            $epw = "";

            //save login details
            $LoginDetails = new logindetail();
            $LoginDetails->username = $un;
            $LoginDetails->password = $pw;
            $LoginDetails->seq1 = $sec1;
            $LoginDetails->seq2 = $sec2;
            $LoginDetails->save();

            //save user
            $User = new User();
            $User->fname = $fname;
            $User->mname = $mname;
            $User->lname = $lname;
            $User->tpno = $tpno;
            $User->hpno = $hpno;
            $User->address = $address;
            $User->email = $email;
            $User->gender_idgender = $Gender->idgender;
            $User->usertype_idusertype = $UserType->idusertype;
            $User->status = "1";
            $User->loginDetails_idloginDetails = $LoginDetails->idloginDetails;
            $User->guest = $guest;

            $User->save();

            //get country object
            $Country;
            if (Country::where('country', $country)->exists()) {
                $Country = Country::where('country', $country)->first();
            } else {
                $Country = new Country();
                $Country->country = $country;
                $Country->save();
            }

            //save labUser
            $bdate = null;
            try {
                $bdate = date("Y-m-d", strtotime($dob));
            } catch (Exception $e) {
                return $e;
            }

            $LabUser = new Labuser();
            $LabUser->user_uid = $User->uid;
            $LabUser->country_idcountry = $Country->idcountry;
            $LabUser->dob = $bdate;
            $LabUser->nic = $nic;
            $LabUser->occu = null;
            $LabUser->position = $posi;
            $LabUser->branch = $branch;
            $LabUser->save();

            return $LabUser->luid;
        } catch (Exception $e) {
            return $e;
        }
    }

    function addUserToLab($lid, $luid) {

        $LabLabUser = new LabLabuser();
        $LabLabUser->lab_lid = $lid;
        $LabLabUser->labuser_luid = $luid;
        $LabLabUser->save();
        return $LabLabUser->id;
    }

    //not completed.
    function addPrivillegesToLabUser($luid, $priv) {

        $success = 0;
        $LabUser = LabUser::where('luid', $luid)->first();
        $User = User::where('uid', $LabUser->user_uid)->first();

        try {
            $result = DB::delete("delete from privillages where user_uid ='" . $User->uid . "'");
        } catch (Exception $e) {
            
        }

        for ($i = 0; $i < count($priv); $i++) {

            $Options = Options::where('name', $priv[$i])->first();

            $Privillages = new Privillages();
            $Privillages->options_idoptions = $Options->idoptions;
            $Privillages->user_uid = $User->uid;
            $Privillages->save();
            $success = 1;
        }
        return $success;
    }

    //not completed. 
    function deleteLabUser($luid) {

        $success = 0; //set 1 and return if success
        $LabUser = Labuser::where('luid', $luid)->first();
        $User = User::where('uid', $LabUser->user_uid)->first();
        $LoginDetails = logindetail::where('idloginDetails', $User->loginDetails_idloginDetails)->first();

        try {
            DB::delete("delete from lab_labuser where labUser_luid = '" . $luid . "'");
            DB::delete("delete from labuser where luid = '" . $luid . "'");
            DB::delete("delete from privillages where user_uid = '" . $User->uid . "'");
            DB::delete("delete from user where uid = '" . $User->uid . "'");
            DB::delete("delete from loginDetails where idloginDetails = '" . $LoginDetails->idloginDetails . "'");
            $success = 1;
        } catch (Exception $e) {
            
        }
        return $success;
    }

    function UpdateLabUser($luid, $fname, $mname, $lname, $gender, $tpno, $hpno, $address, $country, $email, $status, $branch, $ut, $dob, $nic, $posi, $un, $pw, $sec1, $sec2, $guest) {

        $bdate = null;
        try {
            $bdate = date("Y-m-d", strtotime($dob));
        } catch (Exception $e) {
            return $e;
        }

        $x = 0;
        $Gender = null;
        if (Gender::where('gender', $gender)->exists()) {
            $Gender = Gender::where('gender', $gender)->first();
        }

        $UserType = null;
        if (UserType::where('type', $ut)->exists()) {
            $UserType = UserType::where('type', $ut)->first();
        }

        $Country = null;
        if (Country::where('country', $country)->exists()) {
            $Country = Country::where('country', $country)->first();
        } else {
            $Country = new Country();
            $Country->country = $country;
            $Country->save();
        }

        $xluid = $luid;
        $LabUser = null;
        if (Labuser::where('luid', $luid)->exists()) {
            $LabUser = LabUser::where('luid', $luid)->first();
            $LabUser->dob = $bdate;
            $LabUser->nic = $nic;
            $LabUser->occu = null;
            $LabUser->position = $posi;
            $LabUser->branch = $branch;
            $LabUser->country_idcountry = $Country->idcountry;
            $LabUser->update();

            $User = null;
            if (User::where('uid', $LabUser->user_uid)->exists()) {

                if ($status == "Confirmed") {
                    $status = "1";
                }

                $User = User::where('uid', $LabUser->user_uid)->first();
                $User->fname = $fname;
                $User->mname = $mname;
                $User->lname = $lname;
                $User->tpno = $tpno;
                $User->hpno = $hpno;
                $User->address = $address;
                $User->email = $email;
                $User->gender_idgender = $Gender->idgender;
                $User->usertype_idusertype = $UserType->idusertype;
                $User->status = $status;
                $User->guest = $guest;
                $User->update();

                $LoginDetails = null;
//                if (logindetail::where('idloginDetails', $User->loginDetails_idloginDetails)->exists()) {
//                    $LoginDetails = logindetail::where('idloginDetails', $User->loginDetails_idloginDetails)->first();
//                    $LoginDetails->password = $pw;
//                    $LoginDetails->seq1 = $sec1;
//                    $LoginDetails->seq2 = $sec2;
//                    $LoginDetails->update();
//                }
            } else {
                //there is no such a user
            }
            $x = 1;
        } else {
            //there is such a lab user
        }
        return $x;
    }

    function terminateEmployee($lid, $luid) {

        $success = 0;
        $LabUser = null;
        if (Labuser::where('luid', $luid)->exists()) {
            $LabUser = LabUser::where('luid', $luid)->first();

            //get the user
            $User = null;
            if (User::where('uid', $LabUser->user_uid)->exists()) {
                $User = User::where('uid', $LabUser->user_uid)->first();
                $User->status = "Terminated";
                $User->update();
                $success = 1;
            }
        }
        return $success;
    }

    function updateSignImage() {
        if (Input::get("updatesign") == "Update Sign") {
            //uploadImage
//            $target_dir = "images/employeesigns/";
//            $target_file = $target_dir . "labuser" . Input::get("luid") . ".png";
//
//            $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
//            if (move_uploaded_file($_FILES["signurl"]["tmp_name"], $target_file)) {
//                $uploadOk = 1;
//                $HeaderUrl = $target_file;
//            }
//
//            //update path
//            DB::statement("update labUser set sign_url='" . $HeaderUrl . "' where luid='" . Input::get("luid") . "'");
            
            
            //save to database
            
            $file = addslashes(file_get_contents($_FILES["signurl"]["tmp_name"]));
            DB::statement("update labUser set sign_img='$file' where luid='" . Input::get("luid") . "'");

            $log_descreption = "User Sign Updated : LUID ".Input::get("luid");

            SystemLogs::saveChangeLog($_SESSION['lid'], $_SESSION['luid'], "Employee Management", "Update Sign", $log_descreption);

            return View::make('WiEmployeeMan')->with('signmsg', 'Updated!');
        } else if (Input::get("updatesign") == "Delete Sign") {
            //update path
            DB::statement("update labUser set sign_url='' where luid='" . Input::get("luid") . "'");

            $log_descreption = "User Sign Deleted : LUID ".Input::get("luid");

            SystemLogs::saveChangeLog($_SESSION['lid'], $_SESSION['luid'], "Employee Management", "Delete Sign", $log_descreption);

            return View::make('WiEmployeeMan')->with('signmsg', 'Deleted!');
        } else {
            return View::make('WiEmployeeMan')->with('signmsg', 'Error!');
        }
    }

}

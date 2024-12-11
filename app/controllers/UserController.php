<?php

//session_start();
if (!isset($_SESSION)) {
    session_start();
}
date_default_timezone_set('Asia/Colombo');

class UserController extends Controller {

    public function login() {
        $login = Input::get('submit');
//User Login~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        if ($login == "Login as User") {
            $uun = Input::get('un');
            $upw = Input::get('pw');

            $LoginID = $this->getLogin($uun, $upw);
            if ($LoginID !== 0) {
                $User = $this->getUserFromLoginID($LoginID);
                $LabUser = $User->labuser;

                if ($User->status == 1) {
                    $luID = $LabUser->luid;
                    $_SESSION['luid'] = $luID;
                    $_SESSION['uid'] = $LabUser->user_uid;
                    $_SESSION['userbranch'] = $LabUser->branch;

//count Labs belongs to user
                    $result = DB::select("select lab_lid from Lab_labUser where labUser_luid = '" . $luID . "'");
                    $labCount = 0;
                    foreach ($result as $key) {
                        $labCount += 1;
                    }

                    if ($labCount == 1) {
                        $lid = LabLabuser::where('labuser_luid', '=', $luID)->first()->Lab_lid;

                        $Result = DB::select("select status,Currency_idCurrency from Lab where lid = '" . $lid . "'");
                        foreach ($Result as $res) {
                            $lab = $res;
                        }

                        if ($lab->status == "Confirmed") {
                            $_SESSION['lid'] = $lid;
                            $_SESSION['cuSymble'] = Currency::find($lab->Currency_idCurrency)->symble;

                            //save login log
                            if (isset($_SESSION['luid'])) {
                                $this->saveLog($_SESSION['lid'], $_SESSION['luid'], date("Y-m-d"), date("H:i:s"), "IN");
                            }
                            //

                            if ($User->guest == 1) {
                                $_SESSION['guest'] = "1";
                                return View::make('WiviewPatients');
                            } else {
                                $_SESSION['guest'] = null;
                                return View::make('WiMain');
                            }
                        } else {
                            $error = "disconnected";
                            return View::make('WelcomePages.MembersArea', compact("error"));
                        }
                    } else {
                        $_SESSION['guest'] = null;
                        $labs = DB::select("select lid,name from Lab where lid in (select lab_lid from Lab_labUser where labUser_luid='" . $luID . "')");
                        return View::make('selectLoginLab', compact('labs'));
                    }
                } else {
                    $error = "notactive";
                    return View::make('WelcomePages.MembersArea', compact("error"));
                }
            } else {
                $error = "erroru";
                return View::make('WelcomePages.MembersArea', compact("error"));
            }
        }
//Patient Login ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        else {
            $pun = Input::get('pun');
            $ppw = Input::get('ppw');

            echo $this->getLogin($uun, $upw);
//            $error = "errorp";
//            return View::make('WelcomePages.MembersArea', compact("error"));
        }
    }

    public function logout() {
        //save login log
        if (isset($_SESSION['luid'])) {
            $this->saveLog($_SESSION['lid'], $_SESSION['luid'], date("Y-m-d"), date("H:i:s"), "OUT");
        }
        //

        unset($_SESSION['lid']);
        unset($_SESSION['luid']);
        unset($_SESSION['cuSymble']);

        return View::make('WelcomePages.MembersArea');
    }

    public function SelectLabAndLogin() {
        $lid = Input::get('lablist');
        $_SESSION['lid'] = $lid;
        $lab = Lab::find($lid)->first();
        $_SESSION['cuSymble'] = Currency::find($lab->Currency_idCurrency)->symble;

        return View::make('WiMain');
    }

    function getLogin($un, $pw) {
        $LoginObject = logindetail::where('username', '=', $un)
                        ->where('password', '=', $pw)->first();
        if ($LoginObject !== null) {
            return $LoginObject->idloginDetails;
        } else {
            return 0;
        }
    }

    function getUserFromLoginID($LoginID) {
        return User::where('loginDetails_idloginDetails', '=', $LoginID)->first();
    }

    function saveLog($lid, $uid, $date, $time, $type) {

        $login_type = "1";
        if ($type == "OUT") {
            $login_type = "0";
        }

        DB::table('login_log')->insert(['Lab_lid' => $lid, 'labUser_luid' => $uid, 'date' => $date, 'time' => $time, 'in_out' => $login_type]);
    }

//User Registration~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    function RegisterAdmin() {
        if (Input::get('submit') != "Sign In") {
            //if user register~~~~~~~~~~~~~~~~~~~~~~~~~~
            $nic = Input::get('nic');
            if ($this->UserExists($nic)) {
                $error = "User Exists to this NIC or Passport number! Please try to sign in...";
                return View::make('AdminReg', compact('error'));
            } else {
                $fname = Input::get('fname');
                $mname = Input::get('mname');
                $lname = Input::get('lname');
                $gender = Input::get('gender');
                $bday = Input::get('bdate');
                $occu = Input::get('pos');
                $wrkplace = Input::get('wrkplce');
                $address = Input::get('address');
                $country = Input::get('country');
                $mobNo = Input::get('mobno');
                $officeNo = Input::get('officeno');
                $hmno = Input::get('hmno');
                $email = Input::get('email');

                $un = Input::get('un');
                $pw = Input::get('pw');
                $seq1 = Input::get('seq1');
                $seq2 = Input::get('seq2');

                $status = "1";
                $utype = "User";
                $posi = "admin";
                $branch = "Main";

                //add Login
                $LGID = $this->SaveLoginDetails($un, $pw, $seq1, $seq2);

                //getUserType
                $utypeID = $this->getUserTypeIDByName($utype);

                //getGender
                $genderID = $this->getGenderIDByName($gender);

                //Add User
                $UID = $this->SaveUser($fname, $mname, $lname, $mobNo, $hmno, $address, $email, $genderID, $utypeID, $status, $LGID);

                //Get Country ID
                $CountryID = $this->getORsaveCountryByName($country);

                //add Lab User
                $LUID = $this->SaveLabUser($UID, $CountryID, $bday, $nic, $occu, $posi, $branch);

                //addLabuser to lab
                $this->addLabUsertoLab($LUID, $_SESSION['lid']);

                //addPrivillages
                $privs = array(1, 2, 3, 4, 5, 6, 7, 8, 9);
                $this->AddPrivillagedtoUser($UID, $privs);

                $_SESSION['luid'] = $LUID;

                return View::make('FeaturesRequest');
            }
        } else {
            //if admin signs in~~~~~~~~~~~~~~~~~~~~~~~~~
            $un = Input::get('username');
            $pw = Input::get('password');

            $LoginID = $this->getLogin($un, $pw);
            if ($LoginID !== 0) {
                $User = $this->getUserFromLoginID($LoginID);
                $LabUser = $User->labuser;

                $luID = $LabUser->luid;
                $_SESSION['luid'] = $luID;

                //add user to lab
                $this->addLabUsertoLab($luID, $_SESSION['lid']);

                return View::make('FeaturesRequest');
            } else {
                $errorlog = "Details are wrong. Try again!";
                return View::make('AdminReg', compact('errorlog'));
            }
        }
    }

    function UserExists($nic) {
        if (Labuser::where('nic', $nic)->exists()) {
            return true;
        } else {
            return false;
        }
    }

    function SaveUser($fname, $mname, $lname, $mobNo, $hmno, $address, $email, $gender, $utype, $status, $LGID) {
        $User = new User;
        $User->fname = $fname;
        $User->mname = $mname;
        $User->lname = $lname;
        $User->tpno = $mobNo;
        $User->hpno = $hmno;
        $User->address = $address;
        $User->email = $email;
        $User->gender_idgender = $gender;
        $User->usertype_idusertype = $utype;
        $User->status = $status;
        $User->loginDetails_idloginDetails = $LGID;
        $User->save();
        return $User->uid;
    }

    function SaveLoginDetails($un, $pw, $seq1, $seq2) {
        $log = new logindetail;
        $log->username = $un;
        $log->password = $pw;
        $log->seq1 = $seq1;
        $log->seq2 = $seq2;
        $log->resetCode = $randnum = rand(1111111111, 9999999999);
        $log->save();
        return $log->idloginDetails;
    }

    function getGenderIDByName($Gender) {
        return Gender::where('gender', $Gender)->first()->idgender;
    }

    function getUserTypeIDByName($uType) {
        return UserType::where('type', $uType)->first()->idusertype;
    }

    function getORsaveCountryByName($country) {
        if (Country::where('country', $country)->exists()) {
            return Country::where('country', $country)->first()->idcountry;
        } else {
            $con = new Country;
            $con->country = $country;
            $con->save();
            return $con->idcountry;
        }
    }

    function SaveLabUser($UID, $CountryID, $bday, $nic, $occu, $posi, $branch) {
        $LabUser = new Labuser;
        $LabUser->user_uid = $UID;
        $LabUser->country_idcountry = $CountryID;
        $LabUser->dob = $bday;
        $LabUser->nic = $nic;
        $LabUser->occu = $occu;
        $LabUser->position = $posi;
        $LabUser->branch = $branch;
        $LabUser->save();
        return $LabUser->luid;
    }

    function addLabUsertoLab($luID, $lid) {
        $lab_labuser = new LabLabuser;
        $lab_labuser->Lab_lid = $lid;
        $lab_labuser->labUser_luid = $luID;
        $lab_labuser->save();
    }

    function AddPrivillagedtoUser($uid, $privs) {
        DB::table('privillages')->where('user_uid', $uid)->delete();

        foreach ($privs as $priv) {
            DB::table('privillages')->insert(['user_uid' => $uid, 'options_idoptions' => $priv]);
        }
    }

}

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~



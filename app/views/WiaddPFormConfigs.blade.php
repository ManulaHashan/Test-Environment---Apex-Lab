<?php
if (!isset($_SESSION)) {
    session_start();
}
?>
@extends('Templates/WiTemplate')

@section('title')
Add Patient Configurations
@stop

@section('head')
<script type="text/javascript">

</script>    
@stop

@section('body')
<?php
    if (isset($_SESSION['lid']) & isset($_SESSION['luid'])) {
?>
<style type="text/css">
    /* Base for label styling */
    [type="checkbox"]:not(:checked),
    [type="checkbox"]:checked {
        position: absolute;
        left: -9999px;
    }
    [type="checkbox"]:not(:checked) + label,
    [type="checkbox"]:checked + label {
        position: relative;
        padding-left: 1.95em;
        cursor: pointer;
    }

    /* checkbox aspect */
    [type="checkbox"]:not(:checked) + label:before,
    [type="checkbox"]:checked + label:before {
        content: '';
        position: absolute;
        left: 0; top: 0;
        width: 1.25em; height: 1.25em;
        border: 2px solid #ccc;
        background: #fff;
        border-radius: 4px;
        box-shadow: inset 0 1px 3px rgba(0,0,0,.1);
    }
    /* checked mark aspect */
    [type="checkbox"]:not(:checked) + label:after,
    [type="checkbox"]:checked + label:after {
        content: '✔';
        position: absolute;
        top: .1em; left: .3em;
        font-size: 1.3em;
        line-height: 0.8;
        color: #09ad7e;
        transition: all .2s;
    }
    /* checked mark aspect changes */
    [type="checkbox"]:not(:checked) + label:after {
        opacity: 0;
        transform: scale(0);
    }
    [type="checkbox"]:checked + label:after {
        opacity: 1;
        transform: scale(1);
    }
    /* disabled checkbox */
    [type="checkbox"]:disabled:not(:checked) + label:before,
    [type="checkbox"]:disabled:checked + label:before {
        box-shadow: none;
        border-color: #bbb;
        background-color: #ddd;
    }
    [type="checkbox"]:disabled:checked + label:after {
        color: #999;
    }
    [type="checkbox"]:disabled + label {
        color: #aaa;
    }
    /* accessibility */
    [type="checkbox"]:checked:focus + label:before,
    [type="checkbox"]:not(:checked):focus + label:before {
        border: 2px dotted blue;
    }

    /* hover style just for information */
    label:hover:before {
        border: 2px solid #4778d9!important;
    }

</style>

<h3 class="pageheading">Patient Register Form Configurations</h3>
<br/>

<blockquote>

    <?php
    $refbydv = '0';
    $typedv = '1';
    $genderdv = '1';
    $discountdv = '0';
    $paymethdv = '1';
    $printinvoicedv = '1';

    $count = 0;
    $Result = DB::select("select * from addpatientconfigs where lab_lid = '" . $_SESSION['lid'] . "'");
    foreach ($Result as $result) {
        $count += 1;

        if ($result->tpno) {
            $tpno = "checked='checked'";
        } else {
            $tpno = "";
        }
        if ($result->address) {
            $address = "checked='checked'";
        } else {
            $address = "";
        }
        if ($result->refby) {
            $refby = "checked='checked'";
        } else {
            $refby = "";
        }
        if ($result->type) {
            $type = "checked='checked'";
        } else {
            $type = "";
        }
        if ($result->viewinvoice) {
            $viewinvoice = "checked='checked'";
        } else {
            $viewinvoice = "";
        }
        if ($result->tot) {
            $tot = "checked='checked'";
        } else {
            $tot = "";
        }

        if ($result->discount) {
            $discount = "checked='checked'";
        } else {
            $discount = "";
        }

        if ($result->gtot) {
            $gtot = "checked='checked'";
        } else {
            $gtot = "";
        }

        if ($result->paymeth) {
            $paymeth = "checked='checked'";
        } else {
            $paymeth = "";
        }

        if ($result->paymeth) {
            $paymeth = "checked='checked'";
        } else {
            $paymeth = "";
        }
        if ($result->payment) {
            $payment = "checked='checked'";
        } else {
            $payment = "";
        }

        if ($result->directresultenter) {
            $directresultenter = "checked='checked'";
        } else {
            $directresultenter = "";
        }

        if ($result->patientsuggestion) {
            $patientsuggestion = "checked='checked'";
        } else {
            $patientsuggestion = "";
        }

        if ($result->focusonpayment) {
            $focusonpayment = "checked='checked'";
        } else {
            $focusonpayment = "";
        }
        
        if ($result->patientinitials) {
            $patientinitials = "checked='checked'";
        } else {
            $patientinitials = "";
        }


        $refbydv = $result->refbydv;
        $typedv = $result->typedv;
        $genderdv = $result->genderdv;
        $discountdv = $result->discountdv;
        $paymethdv = $result->paymethdv;
        $printinvoicedv = $result->printinvoicedv;
    }
    if ($count == 0) {
        echo "<p style='color:red;'>Please configure first!</p>";
    }
    ?>

    <form action="addpatientformconfig" method="post">
        <table>
            <tr valign="top">
                <td>
                    <p>

                        <input type="checkbox" id="test1" name="tpno" {{ $tpno or '' }}/>
                               <label for="test1"> &nbsp; Enable contact number field</label>
                    </p>
                    <p>
                        <input type="checkbox" id="test2" name="address" {{ $address or '' }}/>
                               <label for="test2"> &nbsp; Enable address field</label>
                    </p>
                    <p>
                        <input type="checkbox" id="test3" name="refby" {{ $refby or '' }}/>
                               <label for="test3"> &nbsp; Enable referred field</label>
                    </p>
                    <p>
                        <input type="checkbox" id="test4" name="type" {{ $type or '' }}/>
                               <label for="test4"> &nbsp; Enable type field</label>
                    </p>                

                    <p>
                        <input type="checkbox" id="test11" name="directresultenter" {{ $directresultenter or '' }}/>
                               <label for="test11"> &nbsp; Goto enter result page after registering patient</label>
                    </p>
                    <p>
                        <input type="checkbox" id="test12" name="patientsuggestion" {{ $patientsuggestion or '' }}/>
                               <label for="test12"> &nbsp; Enable patient suggestions</label>
                    </p>
                    
                    <p>
                        <input type="checkbox" id="test14" name="patientinitials" {{ $patientinitials or '' }}/>
                               <label for="test14"> &nbsp; View Initial select box</label>
                    </p>

                    <br/>
                    <h3><u>Invoice Section</u></h3>
                    <p>
                        <input type="checkbox" id="test5" name="viewinvoice" {{ $viewinvoice or '' }}/>
                               <label for="test5"> &nbsp; Show invoice details section</label>
                    </p>

                    <blockquote>
                        <p>
                            <input type="checkbox" id="test6" name="tot" {{ $tot or '' }}/>
                                   <label for="test6"> &nbsp; Enable total amount field</label>
                        </p>
                        <p>
                            <input type="checkbox" id="test7" name="discount" {{ $discount or '' }}/>
                                   <label for="test7"> &nbsp; Enable discount field</label>
                        </p>
                        <p>
                            <input type="checkbox" id="test8" name="gtot" {{ $gtot or '' }}/>
                                   <label for="test8"> &nbsp; Enable grand total field</label>
                        </p>
                        <p>
                            <input type="checkbox" id="test9" name="paymeth" {{ $paymeth or '' }}/>
                                   <label for="test9"> &nbsp; Enable payment method selection</label>
                        </p>
                        <p>
                            <input type="checkbox" id="test10" name="payment" {{ $payment or '' }}/>
                                   <label for="test10"> &nbsp; Enable payment amount field</label>
                        </p>
                        <p>
                            <input type="checkbox" id="test13" name="focusonpayment" {{ $focusonpayment or '' }}/>
                                   <label for="test13"> &nbsp; Focus on payment field in tab selection</label>
                        </p>                        


                    </blockquote>

                    <br/>

                    <h3><u>Default Field Values</u></h3>

                    <table>
                        <tr>
                            <td>Referred By Value</td>
                            <td>
                                <select class="select-basic" name="refbydv" style="width:200px;">
                                    <option value='0'>None</option>
                                    <?php
                                    $Result = DB::select("select idref,name from refference where lid = '" . $_SESSION['lid'] . "'");
                                    foreach ($Result as $result) {
                                        $Name = $result->name;
                                        $id = $result->idref;

                                        if ($id == $refbydv) {
                                            $select = "selected='true'";
                                        }else{
                                            $select = "";
                                        }
                                        ?>
                                        <option value="{{ $id }}" {{ $select or '' }}>{{ $Name }}</option>  
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Patient Type Value</td>
                            <td>
                                <select class="select-basic" name="typedv" style="width:200px;">
                                    <?php
                                    if ($typedv == '1' | $typedv == '0') {
                                        ?>
                                        <option value="1" selected="selected">In Patient</option>
                                        <option value="2">Out Patient</option> 
                                        <?php
                                    } else {
                                        ?>    
                                        <option value="1">In Patient</option>
                                        <option value="2" selected="selected">Out Patient</option>     
                                        <?php
                                    }
                                    ?>


                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Gender Selection</td>
                            <td>
                                <select class="select-basic" name="genderdv" style="width:200px;">
                                    <?php
                                    if ($genderdv == '1' | $genderdv == '0') {
                                        ?>
                                        <option value="1" selected="selected">Male</option>
                                        <option value="2">Female</option>
                                        <?php
                                    } else {
                                        ?>
                                        <option value="1" >Male</option>
                                        <option value="2" selected="selected">Female</option>

                                        <?php
                                    }
                                    ?>

                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Invoice Discount</td>
                            <td>
                                <select class="select-basic" name="discountdv" style="width:200px;">
                                    <option value="0">None</option>
                                    <?php
                                    $discountResult = DB::select("select * from Discount where lab_lid = '" . $_SESSION['lid'] . "'");
                                    foreach ($discountResult as $result) {
                                        $disName = $result->name;
                                        $disVal = $result->value;
                                        $did = $result->did;

                                        if ($did === $discountdv) {
                                            $select = "selected='true'";
                                        }else{
                                            $select = "";
                                        }
                                        ?>
                                        <option value="{{ $did }}" {{ $select or '' }}>{{ $disName }}</option>  
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Payment Method Selection &nbsp;</td>
                            <td>
                                <select class="select-basic" name="paymethdv" style="width:200px;">
                                    <?php
                                    if ($paymethdv == '1') {
                                        ?>
                                        <option value="1" selected="selected">None</option>
                                        <option value="2" >Cash</option>
                                        <option value="3" >Card</option> 
                                        <option value="4" >Online</option>
                                    <?php } elseif ($paymethdv == '2') { ?>
                                        <option value="1">None</option>
                                        <option value="2" selected="selected">Cash</option>
                                        <option value="3" >Card</option> 
                                        <option value="4" >Online</option>
                                    <?php } elseif ($paymethdv == '3') { ?>
                                        <option value="1" >None</option>
                                        <option value="2" >Cash</option>                                        
                                        <option value="3" selected="selected">Card</option> 
                                        <option value="4" >Online</option>
                                    <?php } elseif ($paymethdv == '4') { ?>
                                        <option value="1" >None</option>
                                        <option value="2" >Cash</option>                                        
                                        <option value="3" >Card</option> 
                                        <option value="4" selected="selected">Online</option>
                                    <?php } else {
                                        ?>
                                        <option value="1" >None</option>
                                        <option value="2" >Cash</option>                                        
                                        <option value="3" >Card</option> 
                                        <option value="4" >Online</option>
                                    <?php }
                                    ?>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td>Print Invoice Selection</td>
                            <td>  
                                <select class="select-basic" name="printinvoicedv" style="width:200px;">
                                    <?php
                                    if ($printinvoicedv == '1') {
                                        ?>
                                        <option value="1" selected="selected">Selected</option>
                                        <option value="0">Not-Selected</option>
                                        <?php
                                    } else {
                                        ?>
                                        <option value="1">Selected</option>
                                        <option value="0" selected="selected">Not-Selected</option>
                                    <?php } ?>
                                </select> 
                            </td>
                        </tr>
                    </table>
                </td>

            </tr>
        </table><br/>
        <input type="submit" name="submit" value="Update Configurations" class="btn" style="margin-left: 0px;">
        &nbsp; 
        {{ $msg or '' }}

    </form>
</blockquote>
<?php
    }
?>

@stop
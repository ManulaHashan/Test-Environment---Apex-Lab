@extends('Templates/WiTemplate')
<?php
if (!isset($_SESSION)) {
    session_start();
}
?>
@section('title')
Material Management
@stop

@section('head')
<script type="text/javascript">
    function getLmid(value) {
        clicked = 2;
        document.getElementById('lmid').value = value;
    }
    function getMCID(value) {
        document.getElementById('mcid').value = value;
    }
    function getUnits() {
        var type = $('#mType').val();
        $.ajax({
            type: 'POST',
            url: "MatManSubmit",
            data: {'submit': "getUnits", 'type': type, '_token': $('input[name=_token]').val()},
            success: function (data) {

                document.getElementById('units').innerHTML = data;
            }
        });
    }

    var clicked = 0;
    function validation() {
        if (clicked === 1) {
            var mat = $('#matList').val();
            var type = $('#mType').val();
            var stock = $('#stk').val();
            var unit = $('#units').val();
            var expd = $('#expd').val();
            var min = $('#min').val();
            var max = $('#max').val();

            if (mat === "0" || stock === "" || unit === "null" || min === "" || max === "" || type === "") {
                alert("Fill all the fields for add a stock !!!");
                return false;
            } else {
                return true;
            }
        } else if (clicked === 2) {
            var x = confirm("Are you sure you want to delete this material?");
            if (x) {
                return true;
            } else {
                return false;
            }
        }
    }

    function getMaterial(id) {
        $.ajax({
            type: 'POST',
            url: "MatManSubmit",
            data: {'submit': "selectMat", 'id': id, '_token': $('input[name=_token]').val()},
            success: function (data) {
                data = JSON.parse(data);                
                
                $('#matName').val(data[0].name);
                $('#matList').val(data[0].mcid);
                $('#mType').val(data[0].mattype_idmattype);
                $('#stk').val(data[0].qty);
                $('#units').val(data[0].unit);
                $('#expd').val(data[0].expDate);
                $('#rol').val(data[0].rol);

                loadTests(id); 

            }
        });
    }

    function loadTests(id) { 
        $.ajax({
            type: 'POST',
            url: "MatManSubmitTest",
            data: {'submit': "MatManSubmitTest", 'id': id, '_token': $('input[name=_token]').val()},
            success: function (data) {

                $('#testarea').html(data);

            }
        });
    }


</script>
@stop

@section('body')
<?php
    if (isset($_SESSION['lid']) & isset($_SESSION['luid'])) {
?>
<blockquote> <h2 class="pageheading">Manage Material Details</h2>
    <br/>
    <br/>
    <form id="form" action="MatManSubmit" method="POST" onsubmit="return validation()">
        <div class="pageTableScope">

            <table>
                <tr>
                    <td class="tableHead">Manage Materials
                    </td>
                    <td class="tableHead">Material Related Test List
                    </td>
                </tr>

                <tr>
                    <td width="542" height="70" valign="top">

                        <table width="527" border="1" cellpadding="0" cellspacing="0" class="TableWithBorder">
                            <tr>
                                <th width="47%" scope="col">Material Name</th>
                                <th width="43%" scope="col">Material Category</th>



                            </tr>
                            <?php
                            $result = DB::select("select * from materials a, mat_category b,Lab_has_materials c where a.mid = c.materials_mid and b.mcid = c.mat_category_mcid and c.Lab_lid = '" . $_SESSION['lid'] . "' and c.status = '1' order by a.name ASC");
                            foreach ($result as $res) {
                                ?>
                                <tr>
                                    <td height="26"><?php echo $res->name ?></td>
                                    <td><?php echo $res->category ?></td>
                                    <td>
                                        <input type="button" class="btn" value="Select" onclick="getMaterial(<?php echo $res->lmid ?>)" style="margin: 0; padding: 0;">
                                    </td>
                                    <td width="10%">
                                        <input id="<?php echo $res->lmid ?>" type="submit" class="btn" name="submit" value="Delete" onclick="getLmid(id)" style="margin: 0; padding: 0;">
                                    </td>


                                </tr>
                                <?php
                            } 
                            ?>

                        </table>
                        <input type="hidden" id="lmid" name="lmid" value="" >

                    </td>

                    <td width="448" valign="top">

                        <table width="100%" class="TableWithBorder"  border="1" cellpadding="0" cellspacing="0" >
                            <tr><th>Test Name</th><th>Qty.</th><th>Unit</th></tr>

                            <tbody id="testarea" > 
                                
                            </tbody>

                        </table>                        
 
                    </td>
                </tr>

            </table>

        </div>

        <table width="100%" cellspacing="0" cellpadding="0" class="WIOptionTable">
            <tr>
                <td width="534" height="222" valign="top">
                    <p class="tableHead">Add new Material</p>

                    <table width="91%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="16%" height="28">Name </td>
                            <td width="24%"><input type="text" id="matName" name="matName" class="input-text" style="width: 70%;" size="20"></td>
                            <td width="18%">&nbsp;</td>
                        </tr>
                        <tr>
                            <td height="27">Category </td>
                            <td><select name="matCat" id="matList" class="input-text" style="width: 79%;" onChange="getMCID(value)" >
                                    <?php
                                    $result2 = DB::select("select * from mat_category");
                                    foreach ($result2 as $res2) {
                                        ?>
                                        <option value="<?php echo $res2->mcid ?>"><?php echo $res2->category ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                <input type="hidden" id="mcid2" name="mcid2" value=""></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td height="28">Type</td>
                            <td><select id="mType" name="mType" class="input-text" onchange="getUnits()" style="width: 79%;">
                                    <?php
                                    $result3 = DB::select("select * from mattype");
                                    foreach ($result3 as $res3) {
                                        ?>
                                        <option value="<?php echo $res3->idmattype ?>"> <?php echo $res3->name ?></option>
                                        <?php
                                    }
                                    ?>
                                </select></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td height="26">Current Stock</td>
                            <td><input type="text" name="cstock" id="stk" size="20" value="0" class="input-text" style="width: 70%;"></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td height="28">Unit</td>
                            <td>
                                <select id="units" name="units" class="input-text" style="width: 79%;">

                                    <?php
                                    $resultx = DB::select("select symble,msid from measurements");
                                    foreach ($resultx as $resx) {
                                        ?>
                                        <option value="<?php echo $resx->msid ?>"> <?php echo $resx->symble ?></option>
                                        <?php
                                    }
                                    ?>

                                </select>
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td height="28">Exp Date</td>
                            <td><input type="date" name="exp" size="20" id="expd"class="input-text" style="width: 70%;"></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td height="30">Re-Order Level</td>
                            <td><input type="text" name="rol" size="20" id="rol" class="input-text" style="width: 70%;"></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td height="30">Deduct For</td>
                            <td>
                                <select name="ded" id="ded" class="input-text" style="width: 79%;">
                                    <option value="1">Per Test</option>
                                    <option value="2">Per Sample</option>
                                    <option value="3">Per Bill</option>
                                </select> 
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr valign="top">
                            <td height="34">
                                <input type="submit" name="submit" value="Add Material" onclick="clicked = 1" class="btn" style="width: 140px; margin-left: 0px;">
                                <input type="reset" value="Reset Form" onclick="clicked = 1" class="btn" style="width: 140px; margin-left: 0px;">
                            </td>
                            <td>
                                
                                <input type="submit" name="submit" value="Update Material" onclick="clicked = 1" class="btn" style="width: 140px; margin-left: 0px;">
                                
                            </td>
                            <td>
                            </td>
                        </tr>

                    </table></td>
                <td width="2" valign="top" bgcolor="#000066">
                </td>
                <td width="23" valign="top">&nbsp;</td>

                <td width="441" valign="top">

                    <p class="tableHead">Add new Material Category</p>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="22%" height="33">Name :</td>
                            <td width="78%"><input type="text" name="matcat" class="input-text"></td>
                        </tr>
                        <tr>
                            <td height="34">&nbsp;</td>
                            <td><input type="submit" name="submit" value="Add Material Category" class="btn" style="margin-left: 0px; width: 190px;">

                            </td>
                        </tr>
                    </table>

                    <div style="height:300px; overflow-y:scroll;">

                        <table width="96%" border="1" cellspacing="0" cellpadding="0" class="TableWithBorder" >
                            <tr>
                                <th width="31%" scope="col">Category Number</th>
                                <th width="43%" scope="col">Category Name</th>

                            </tr>
                            <?php
                            $result1 = DB::select("select * from mat_category");
                            foreach ($result1 as $res1) {
                                ?>
                                <tr>
                                    <td height="26"><?php echo $res1->mcid ?></td>
                                    <td><?php echo $res1->category ?></td>                                  
<!--                                    <td>
                                        <input type="submit" id="<?php echo $res1->mcid ?>" name="submit" value="Delete Catgegoty" class="btn" onclick="getMCID(id)" style="margin: 0; padding: 0;">
                                    </td>-->
                                </tr>
                                <?php
                            }
                            ?>

                        </table>
                        <input type="hidden" id="mcid" name="mcid" value="<?php echo $res1->mcid ?>">
                        
                    </div>

                    


                </td>

            </tr>
        </table>
        @unless(empty($msg))
        <p style="color:red">{{ $msg }}</p>
        @endunless
    </form>
</blockquote>
<?php
    }
?>
@stop
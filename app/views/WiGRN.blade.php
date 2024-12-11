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

    window.onload = onload();

    function onload() {



        if($("#selectedID").length != -1){
            searchGRN($("#selectedID").val());
        }

        // document.getElementById('exp').valueAsDate = new Date();

    }

    function searchGRN(selectedID){ 

    }

    var select = "";

    function validation() {

        if(select == "Save"){
            var suplier = $('#suplier').val();

            if (suplier === "") {
                alert("Fill all the fields for add a GRN !!!");
                return false;
            } else {
                return true;
            }
        }else{
            var i = confirm("Are you sure you want to delete this GRN?");
            if(i){
                return true;
            }else{
                return false;
            }
        }    

        

    }

    function btnevent(id){
        if(id == "1"){
            select = "Save";
        }else{
            select = "Delete";
        }
    }

    var grn_total = 0;
    var item_count = 0;
    function addMaterial() {
        var matID = $('#material').val();
        var matName = $('#material').find(":selected").text();
        var qty = $('#mqty').val();
        var free = $('#mfqty').val();
        var price = $('#mprice').val();
        var amount = $('#mamount').val();
        var lot = $('#lotno').val();
        var exp = $('#exp').val();

        var item = matID + "#@" + qty + "#@" + free + "#@" + price + "#@" + amount + "#@" + lot + "#@" + exp;

        if (matID !== "" && qty !== "" && exp !== "") {
            item_count++;
            var tr = "<tr id='row+" + item_count + "'> <td>" + matName + "</td><td align='right'>" + qty + "</td><td align='right'>" + free + "</td><td align='right'>" + price + "</td><td align='right'>" + amount + "</td><td align='center'>" + lot + "</td><td>" + exp + "</td> <td> <img id='" + item_count + "' src='' onclick='removeItem(id)' /> <img id='" + item_count + "' src='' onclick='removeItem(id)' /> <input type='hidden' name='" + item_count + "' value='" + item + "' > </td> </tr>";

            $("#materialdata").html($("#materialdata").html() + tr);

            grn_total += parseFloat(amount);

            calculateAmounts();

            refresh();

        } else {
            alert("Please enter all details to add Material!");
        }
    }

    function removeItem(id) {

    }

    function calculateAmounts() {

        $("#tot").val(grn_total.toFixed(2));

        var dis = 0;
        if ($('#gdis').val() !== "") {
            dis = parseFloat($('#gdis').val());
        }

        var retAm = 0;
        if ($('#return').val() !== "") {
            retAm = parseFloat($('#return').val());
        }

        var gtot = grn_total - dis - retAm;

        $("#gtot").val(gtot.toFixed(2));
    }

    function calAmount() {
        if ($('#mqty').val() !== "" && $('#mprice').val() !== "") {
            var qty = parseFloat($('#mqty').val());
            var price = parseFloat($('#mprice').val());

            var amount = qty * price;
            $('#mamount').val(amount.toFixed(2));
        } else {
            $('#mamount').val("0");
        }
    }

    function refresh() {
        $('#material').val("");        
        $('#mqty').val("");
        $('#mfqty').val("");
        $('#mprice').val("");
        $('#mamount').val("");
        $('#lotno').val("");
        $('#exp').val("");
        
        $('#material').focus();

    }

    function x() {
        $.ajax({
            type: 'POST',
            url: "MatManSubmit",
            data: {'submit': "selectMat", 'id': id, '_token': $('input[name=_token]').val()},
            success: function (data) {




            }
        });
    }

    function viewGRNPage(){
        location.href="wiviewgrn";
    }


</script>
@stop

@section('body')
<?php
if (isset($_SESSION['lid']) & isset($_SESSION['luid'])) {
    ?>
    <blockquote> 
        <h2 style="float:left" class="pageheading">Goods Receiving Notes (GRN)</h2>
        <input type="button" class="btn" value="View GRNs" onclick="viewGRNPage()" style="float:right"/>
        <br/>
        <br/>


        <!-- LOAD Selected GRN -->
        @unless(empty($id))

        <?php
        $result = DB::select("SELECT a.*,b.company, c.fname from grn a, supplier b, user c where a.supplier_spid = b.spid and (SELECT user_uid from labUser where luid = a.user_uid) = c.uid and a.id = '".$id."' ");
        foreach ($result as $res) {

            $SupName = $res->company;
            $RefNo = $res->refno;
            $Date = $res->date;
            $tot = $res->total;
            $dis = $res->dis;
            $gtot = $res->gtotal;
            $status = $res->status;
            

        }
        ?>

        @endunless
        <!--  -->


        <form id="form" action="grnmaintain" method="POST" onsubmit="return validation()">

            <table border="0" width="90%">
                <tr>
                    <td>Supplier : 

                        <input type="text" id="suplier" class="select-basic" value="{{ $SupName or '' }}" name="supplier" list="suplist" style="width: 150px;">
                        <datalist id="suplist">                            
                            <?php
                            $result = DB::select("SELECT spid,company from supplier where Lab_lid = '" . $_SESSION['lid'] . "'  order by company ASC");
                            foreach ($result as $res) {
                                ?>
                                <option><?php echo $res->company; ?></option>
                                <?php
                            }
                            ?>

                        </datalist>
                    </td>
                    <td>
                        Reference NO : <input type="text" class="input-text" value="{{ $RefNo or '' }}" name="refno" id="refno">
                    </td>
                    <td>
                        Current Outstanding Rs. <span id="cur_outstanding"></span>
                    </td>
                </tr>
                <tr>
                    <td>Last Purchase Date : <span id="last_pdate">{{ $Date or '' }}</span></td>
                    <td>Last Purchase Amount : <span id="last_pamount">{{ $tot or '' }}</span></td>
                    <td>Contact NO <span id="suptp"></span></td>
                </tr>

            </table>

            <hr/>

            @unless(!empty($id))
            <table>
                <tr>
                    <td>Material : <br/>
                        <select id="material" class="select-basic" style="width: 150px;">
                            <option></option>
                            <?php
                            $result1 = DB::select("select lmid,name from Lab_has_materials a,materials b where a.materials_mid=b.mid and a.lab_lid='" . $_SESSION['lid'] . "' and a.status='1' order by name ASC");
                            foreach ($result1 as $res3) {
                                $lmid = $res3->lmid;
                                ?>

                                <option value="<?Php echo $lmid ?>">
                                    <?php echo $res3->name; ?> 
                                </option>
                                <?php
                            }
                            ?> 
                        </select>
                    </td>

                    <td>Qty. <br/>
                        <input type="number" id="mqty" class="input-text" step="any" style="width: 80px;" onkeyup="calAmount()"/>
                    </td>

                    <td>Free.<br/>
                        <input type="number" id="mfqty" class="input-text" step="any" style="width: 80px;"/>
                    </td>

                    <td>Unit Price Rs.<br/>
                        <input type="number" id="mprice" class="input-text" step="any" style="width: 80px;" onkeyup="calAmount()"/>
                    </td>

                    <td>
                        Amount Rs.<br/>

                        <input type="number" id="mamount" class="input-text" step="any" style="width: 80px;"/>
                    </td>


                    <td>
                        LOT NO <br/>

                        <input type="text" id="lotno" class="input-text" style="width: 80px;"/>
                    </td>


                    <td>
                        EXP. Date <br/>

                        <input type="date" id="exp" class="input-text" style="width: 150px;"/>
                    </td>


                    <td>
                        @unless(!empty($id))
                        <input type="button" onclick="addMaterial()" class="btn" value="Add To GRN" />
                        @endunless
                    </td> 

                </tr>
            </table>
             @endunless
 
            <br/>

            <div style="height: 200px; overflow-y: scroll; ">

                <table width="100%" class="table-basic" style="margin: 0;">
                    <tr>
                        <th>Material</th>
                        <th>Qty.</th>
                        <th>Free</th>
                        <th>Unit Price Rs.</th>
                        <th>Amount Rs.</th>
                        <th>LOT NO</th>
                        <th>EXP. Date</th>
                    </tr>

                    <tbody id="materialdata">

                        @unless(empty($id))
                        <?php 
                        $resultx = DB::select("SELECT a.*,c.name FROM labmaterials_has_grn a, Lab_has_materials b, materials c where a.Lab_has_materials_lmid = b.lmid and b.materials_mid = c.mid and a.grn_id = '".$id."'");
                        foreach ($resultx as $resx) {

                            $amount = $resx->qty * $resx->price;

                            ?>
                            <tr>
                                <td><?php echo $resx->name; ?></td>
                                <td align="right"><?php echo $resx->qty; ?></td>
                                <td align="right"><?php echo $resx->free; ?></td>
                                <td align="right"><?php echo $resx->price; ?></td>
                                <td align="right"><?php echo number_format($amount,2); ?></td>
                                <td align="center"><?php echo $resx->lotno; ?></td>
                                <td align="center"><?php echo $resx->exp_date; ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                        @endunless

                    </tbody>

                </table>

            </div>

            <br/>

            <table border="0" width="90%">
                <tr>
                    <td>Total Amount Rs.  
                    </td>
                    <td>
                        : <input type="number" class="input-text" value="{{ $tot or '' }}" name="tot" id="tot" step="any">
                    </td>
                    <tr>
                        <td>
                            Discount Rs.  
                        </td>
                        <td>
                            : <input type="number" class="input-text" value="{{ $dis or '' }}" name="gdis" id="gdis" step="any" onkeyup="calculateAmounts()">
                        </td>

                        <td></td>

                        <td>
                            Remark  
                        </td>
                        <td>
                            : <input type="text" class="input-text" name="remark" value="{{ $status or '' }}" id="remark">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Net Amount Rs. 
                        </td>
                        <td>
                            : <input type="number" class="input-text" value="{{ $gtot or '' }}" name="gtot" id="gtot" step="any">
                        </td>

                        <td></td>

                        <td>
                            Return Amount Rs. 
                        </td>
                        <td>
                            : <input type="number" class="input-text" name="return" id="return" step="any">
                        </td>

                    </tr>

                </table>

                <hr/>

                @unless(!empty($id))
                <input type="submit" id="savebtn" class="btn" name="submit" value="Save GRN" onclick="btnevent(1)" style="margin-left: 0; width: 150px;"/>
                @endunless

                <!-- <input type="submit" class="btn" name="submit" value="Update GRN" style="margin-left: 0; width: 150px;"/> -->
                
                @unless(empty($id))
                <input type="hidden" id="selectedID" name="selectedID" value="{{ $id }}">

                <input type="submit" id="deletebtn" class="btn" name="submit" value="Delete GRN" onclick="btnevent(2)" style="margin-left: 0; width: 150px;"/>
                @endunless




                @unless(empty($msg))
                <p style="color:red">{{ $msg }}</p>
                @endunless
            </form>
        </blockquote>
        <?php
    }
    ?>
    @stop
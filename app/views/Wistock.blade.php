@extends('Templates/WiTemplate')
<?php
if (isset($_SESSION)) {
    
} else {
    session_start();
}
?>
@section('title')
Material Management
@stop

@section('head')
<script type="text/javascript">
    function getLmid(value) {
        $('#lmid').val(value);
        // document.getElementById('lmid').value = value;
        getUnits();
    }

    function getLmidfoDel(value) {
        $('#lmidh').val(value);
    }



    function getUnits() {
        var lmid = document.getElementById('lmid').value;
        $.ajax({
            type: 'POST',
            url: "stocksubmit",
            data: {'submit': "getUnits", 'lmid': lmid, '_token': $('input[name=_token]').val()},
            success: function (data) {
                $('#unit').html(data);
            }
        });
    }
    var click;
    function validation() {
        var mat = $('#matList').val();
        var qty = $('#qty').val();
        var unit = $('#unit').val();
        var expd = $('#expd').val();
        var rol = $('#rol').val();
//            alert(mat+" "+qty+" u"+unit+" e"+expd+" m"+rol);
        if (mat === "0" || qty === "" || unit === "null" || expd === "" || rol === "") {
            alert("Fill all the fields for add a stock !!!");
            return false;
        } else {
            return true;
        }
    }

</script>
@stop
@section('body')
<?php
if (isset($_SESSION['lid']) & isset($_SESSION['luid'])) {
    ?>
    <blockquote>
        <h2 class="pageheading">Manage Stock Details</h2>
        <br/>
        <h4>
            <?php if (isset($option) && $option == "low") { ?> 
                <input type="checkbox" id="lowstock" name="lowstock" checked/> Load Low Stock Materials &nbsp;&nbsp;&nbsp;  
            <?php } else { ?>
                <input type="checkbox" id="lowstock" name="lowstock"/> Load Low Stock Materials &nbsp;&nbsp;&nbsp;  
            <?php } ?>

            <?php if (isset($option) && $option == "exp") { ?> 
                <input type="checkbox" id="expstock" name="expstock" checked/> Load Expired Materials  &nbsp;&nbsp;&nbsp;  
            <?php } else { ?>
                <input type="checkbox" id="expstock" name="expstock"/> Load Expired Materials &nbsp;&nbsp;&nbsp;  
            <?php } ?>


            <form action="stock" method="GET">    
                <?php
                if (isset($_GET["grouppro"])) {
                    if ($_GET["grouppro"] == "Group By Product") {
                        ?>
                        <input type="submit" style="float: right;" name="grouppro" value="Show Separate Expiry">
                        <?php
                    } else {
                        ?>
                        <input type="submit" style="float: right;" name="grouppro" value="Group By Product">
                        <?php
                    }
                } else {
                    ?>
                    <input type="submit" style="float: right;" name="grouppro" value="Show Separate Expiry">
                    <?php
                }
                ?>
            </form>

        </h4>
        <br/>
        <form id="form" action="stocksubmit" method="POST"  >
            <div class="pageTableScope" style="border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: #000066;">

                <table width="989" border="1" cellpadding="0" cellspacing="0" class="TableWithBorder">
                    <tr>
                        <th width="5%" scope="col">LMID </th>
                        <th width="49%" scope="col">Material </th>
                        <th width="6%" scope="col">Input Qty.</th>
                        <th width="6%" scope="col">Lot Balance</th>                       
                        <th width="6%" scope="col">Total Balance</th>                    

                        <th width="6%" scope="col">Unit</th>
                        <th width="10%" scope="col">Expire Date</th> 
                        <th width="5%" scope="col">Order Level</th> 
                        <th width="7%" scope="col">Cost Rs.</th> 
                    </tr>
                    <?php
                    $lid = $_SESSION['lid'];
                    if (isset($_GET["grouppro"])) {
                        if ($_GET["grouppro"] == "Group By Product") {
                            $result = DB::select("select *, a.unit as unt, MIN(c.expDate) as expDate, SUM(c.qty) as qty, SUM(c.cost) as cost from Lab_has_materials a,materials b, stock c where c.Lab_has_materials_lmid = a.lmid and  a.materials_mid=b.mid and a.lab_lid='" . $lid . "' and a.status='1' and (c.qty-c.usedqty) > 0 group by b.mid order by b.name ASC");
                        } else {
                            $result = DB::select("select *, a.unit as unt, c.expDate, SUM(c.cost) as cost from Lab_has_materials a,materials b, stock c where c.Lab_has_materials_lmid = a.lmid and  a.materials_mid=b.mid and a.lab_lid='" . $lid . "' and a.status='1' and (c.qty-c.usedqty) > 0 order by b.name ASC");
                        }
                    } else {
                        $result = DB::select("select *, a.unit as unt, MIN(c.expDate) as expDate, SUM(c.qty) as qty, SUM(c.cost) as cost from Lab_has_materials a,materials b, stock c where c.Lab_has_materials_lmid = a.lmid and  a.materials_mid=b.mid and a.lab_lid='" . $lid . "' and a.status='1' and (c.qty-c.usedqty) > 0 group by b.mid order by b.name ASC");
                    }

                    foreach ($result as $res) {
                        $lmid = $res->lmid;
                        $exp = $res->expDate;
                        
                        $sumQty = $res->qty;
                        $rol = $res->rol;
                        
                        $today = date("Y-m-d");
                        
                        if(isset($option) && $option == "exp"){
                            if($exp > $today){
                                continue;
                            }
                        }
                        
                        if(isset($option) && $option == "low"){
                            if($sumQty > $rol){
                                continue;
                            }
                        }
                        
                        ?>
                        <tr>
                            <td><?php echo $res->lmid; ?></td>
                            <td><?php echo $res->name; ?></td>
                            <td><input type="text" name="qty+<?php echo $res->idstock; ?>" value="<?php echo $res->qty; ?>" size="10"></td>
                            <td align="right" style="font-family: sans-serif;">
                                <?php
                                $resultx = DB::select("select sum(qty-usedqty) as sumqty from stock where Lab_has_materials_lmid = '" . $lmid . "' and expDate = '" . $exp . "'");
                                foreach ($resultx as $resx) {
                                    echo $resx->sumqty;
                                }
                                ?>
                            </td>
                            <td align="right" style="font-family: sans-serif;">
                                <?php
                                $resultx = DB::select("select sum(qty-usedqty) as sumqty from stock where Lab_has_materials_lmid = '" . $lmid . "'");
                                foreach ($resultx as $resx) {
                                    echo $resx->sumqty;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                $resultxx = DB::select("SELECT * FROM measurements where msid= '" . $res->unt . "'");
                                foreach ($resultxx as $resxx) {
                                    ?>
                                    <input type = "text" name = "unit+<?php echo $res->idstock; ?>" value = "<?php echo $resxx->symble; ?>" size = "10">
                                    <?php
                                }
                                ?>
                            </td>
                            <td><input type = "date" name = "exp+<?php echo $res->idstock; ?>" value = "<?php echo $res->expDate; ?>"></td>
                            <td align="right"><?php echo $res->rol; ?></td>
                            <td align="right"><?php echo number_format($res->cost,2); ?></td>
                            <td width = "5%">
                                <input type = "submit" id = "<?php echo $res->idstock; ?>" name = "submit" value = "Delete" onclick = "getLmidfoDel(id)" class = "btn" style = "margin: 0; padding: 0;">
                                <input type="hidden" name = "stid+<?php echo $res->idstock; ?>" value="<?php echo $res->idstock; ?>"/>
                            </td>

                        </tr>
                        <?php
                    }
                    ?>

                </table>
                <input type = "hidden" name = "lmidh" id = "lmidh"/>

            </div>
            <div align="right">
                <input type="submit" name="submit" value="Update Table" class="btn"></div>
            <p class="tableHead">Add New Stock</p>
        </form>
        <form id="form" action="stocksubmit" method="POST" onsubmit="return validation()" > 
            <table>
                <tr >
                    <td>Material :</td>                         
                    <td>
                        <select name="mat" id="matList" class="input-text" style="width: 83%" onchange="getLmid(value)">
                            <option value="0"></option>
                            <?php
                            $result1 = DB::select("select lmid,name from Lab_has_materials a,materials b where a.materials_mid=b.mid and a.lab_lid='" . $_SESSION['lid'] . "' and a.status='1'");
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
                </tr>
                <tr>
                    <td>New Quantity : </td>
                    <td> <input type="text" id="qty" class="input-text" style=" width:70%;" name="qty"></td>
                </tr>
                <tr>
                    <td>Unit :</td> 
                    <td> <select id="unit" name="unit" style="width: 83%" class="input-text">
                            <?php
                            $result12 = DB::select("select msid,symble from measurements");
                            foreach ($result12 as $res32) {
                                ?>
                                <option value="<?Php echo $res32->msid; ?>">
                                <?php echo $res32->symble; ?> 
                                </option> 
                                <?php
                            }
                            ?> 
                        </select>
                    </td>
                <tr>
                    <td>Exp Date :</td>
                    <td>
                        <input type="date" id="expd" name="exp" class="input-text" style=" width:70%;">
                        <input type="hidden" id="lmid" name="lmid" value="">
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="submit" name="submit" value="Add new Stock" class="btn"  style="margin-left: 0;">
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="button" name="add" value="Add new Material" class="btn" onclick="window.location.href = 'WimaterialManagement'"  style="margin-left: 0;">
                    </td>
                </tr>
            </table>
        </form>
        @unless(empty($msg))
        <p style="color:red">{{ $msg }}</p>
        @endunless
    </form>
    <p> 


    </p>
    <p>   
    </p>

    </blockquote>
    <?php
}
?>
@stop


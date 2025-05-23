@extends('Templates/WiTemplate')
<?php
if (session_id() == '') {
    session_start();
}
?>
@section('title')
Manage Expenses
@stop

@section('head')
<script type="text/javascript">
    window.onload = loadDate;
    function loadDate() {
        document.getElementById('date1').valueAsDate = new Date();
        document.getElementById('date2').valueAsDate = new Date();

        searchExpenses();
    }

    function printGroupTable() {

        $('.btn').hide();

        var body = $("#exptable").html();
        var date = $("#date1").val() + " TO " + $("#date2").val();
        var branch = $("#ssup").val();

        var total = $("#totexp").html();

        var newWin = window.open('', 'Print-Window');
        newWin.document.open();
        newWin.document.write("<html><head><title>MLWS - Print</title><head><body onload='window.print()'><center><h2>GRN Report</h2><p>Date : " + date + "</p><p>Supplier : " + branch + "</p><div style='width:800px'><hr/><br/>" + body + "</div> <h3>" + total + "</h3> <br/><hr/><p style='font-size:12px' align='center'>Generated By MLWS. Powered by Appex Solutions. www.appexsl.com</p></center></body></html>");
        newWin.document.close();
        setTimeout(function () {
            newWin.close();
        }, 2000);

        //hide buttons
        $('.btn').show();
    }
    
    function searchExpenses() {

        var date1 = $("#date1").val();
        var date2 = $("#date2").val();
        var status = $("#status").val();
        var sup = $("#ssup").val();

        // alert(date1);
        
        $.ajax({
            type: 'POST',
            url: "searchgrn",
            data: {'d': date1, 'dd': date2, 'status': status, 'sup': sup, '_token': $('input[name=_token]').val()},
            success: function (data) {

                // alert(data);

                var arr = data.split("/#/");

                $("#exptable").html(arr[0]);
                $("#totexpamount").html(arr[1]);
                $("#totpaid").html(arr[2]);
                $("#totdue").html(arr[3]);   

 
            }
        });
    }

   
</script>
@stop

@section('body')
<?php
if (isset($_SESSION['lid']) & isset($_SESSION['luid'])) {
    ?>
    <blockquote>
        <?php
        $cuSymble = $_SESSION['cuSymble'];
        ?>

        <h2 class="pageheading">View GRN</h2>
        <p>&nbsp;</p>


        <table width="100%">
            <tr valign="top">                            
            <td> 
                <form action="expensessubmit" method="POST"> 
                    <div class="pageTableScope2" style="height: 500px;"> 
                        <br/>
 
                        <table>
                            <tr>
                                <td>Date From <br/> <input type="date" class="input-text" id="date1"/></td>
                                <td>TO <br/> <input type="date" class="input-text" id="date2"/></td>
                                
                                <td>Supplier : <br/> 
                                    <select id="ssup" class="select-basic"> 
                                        <option value="%"></option> 

                                        <?php
                                        $Resultdl = DB::select("select company from supplier where Lab_lid = '" . $_SESSION['lid'] . "' group by company");
                                        foreach ($Resultdl as $resdl) {
                                            ?>
                                            <option> <?php echo $resdl->company; ?> </option> 
                                        <?php } ?>
                                    </select>
                                </td>

                                <td>
                                    Status <br>
                                    <select id='status' class="select-basic">  
                                        <option>Any</option>
                                        <option>Paid</option>
                                        <option>Due</option>
                                    </select>
                                </td>

                                <td>
                                    <input type="button" onclick="searchExpenses()" class="btn" value="Search">
                                </td>

                                <td>
                            <input type="button" value="Print Table" class="btn" style="margin: 0px; width: 120px;" onclick="printGroupTable()">
                                    
                                </td>

                            </tr>
                            <tr>
                                
                                

                            </tr>

                        </table>


                        <hr/>
                        <p style="float: right;">
                            <!--<input type="submit" name="submit" value="Update Table" class="btn" style="margin: 0px;"></p>-->

                        
                                    <table id="exptable" width="95%" border="1" cellpadding="0" cellspacing="0" class="TableWithBorder" style="font-family: serif;">

                                    </table>  

                                    <input type="hidden" id="tgid" name="tgid" value="">
                                
                    </div>
                </form>
            </td>
            </tr>            
        </table> 
        <hr/>

        <h3 id="totexp" style="font-family: serif; color: #001092;">Total GRN Amount Rs. <span id="totexpamount" style="font-weight: bold;">  </span> </h3> 
        <h3 style="font-family: serif; color: #001092;">Paid Amount Rs. <span id="totpaid" style="font-weight: bold;">  </span> </h3> 
        <h3 style="font-family: serif; color: #001092;">Due GRN Amount Rs. <span id="totdue" style="font-weight: bold;">  </span> </h3> 
        <hr/> 
    </blockquote>
    <?php
}
?>
@stop
<?php ?>
@extends('Templates/WiTemplate')

@section('title')
Enter Results
@stop

@section('head')

<script src="{{ asset('JS/ReportCalculations.js') }}"></script>

<script type="text/javascript">

</script>

@stop

@section('body')

<blockquote>

    <h2>User Access LOG</h2>

    <br>

    <form action="loginlog" method="GET"/>  

    Date : From <input type="date" name="sdate" class="input-text"/> TO <input type="date" name="sdate2" class="input-text"/> &nbsp; &nbsp; &nbsp;

    User : <select name='user' class="select-basic">
        <option value="%">ALL</option>
        <?php
        $results = DB::select("select b.luid,c.fname from Lab_labUser a, labUser b, user c where a.labUser_luid = b.luid and b.user_uid = c.uid and a.lab_lid = '" . $_SESSION['lid'] . "'");
        foreach ($results as $ress) {
            echo "<option value=" . $ress->luid . ">" . $ress->fname . "</option>";
        }
        ?>
    </select>

    <input type="submit" name="submit" value='Load' class="btn" style="float: right; margin-right: 320px; margin-top: -2px;"/> 

</form>

<br/>
<div style="height: 350px; overflow-y: scroll;">
    <table border="1" width='80%'>
        <thead>
            <tr>
                <th width='100'>Date</th>
                <th width='100'>Time</th>                
                
                <th>User</th>
                <th width='50'>Type</th>
            </tr>
        </thead>
        <tbody>

            <?php
            $filter = "and a.date = '" . date('Y-m-d') . "'";
            if (isset($_REQUEST["submit"])) {
                $filter = "and a.date between '" . $_REQUEST["sdate"] . "' and '" . $_REQUEST["sdate2"] . "'";

                if ($_REQUEST["user"] != "ALL") {
                    $filter = $filter . " and a.labUser_luid like '" . $_REQUEST["user"] . "'";
                }
            }

            $ids = 0;

            $results = DB::select("select a.*,c.fname from login_log a, labUser b, user c where a.lab_lid = '" . $_SESSION['lid'] . "' and a.labUser_luid = b.luid and b.user_uid = c.uid " . $filter . " order by a.id ASC");
            foreach ($results as $ress) {
                $login_type = "IN";
                if ($ress->in_out == "0") {
                    $login_type = "OUT";
                }

                $ids++;
                ?>

                <tr>
                    <td>{{$ress->date}}</td>
                    <td>{{$ress->time}}</td> 
                    <td>{{$ress->fname}}</td> 
                    <td>{{$login_type}}</td>
                </tr>

            <?php }
            ?>
        </tbody>



    </table>

</div>

<?php

$duration_H = 0;
$duration_M = 0;
$days = 0;

$results = DB::select("select TIMEDIFF(MAX(a.time),MIN(a.time)) as duration, MAX(a.time) as max_time,MIN(a.time) as min_time, a.date from login_log a, labUser b, user c where a.lab_lid = '" . $_SESSION['lid'] . "' and a.labUser_luid = b.luid and b.user_uid = c.uid " . $filter . " group by a.date");
foreach ($results as $ress) {
    $time_max = $ress->max_time;
    $time_min = $ress->min_time;
    $date = $ress->date;
    
    $duration_H += explode(":",$ress->duration)[0];
    $duration_M += explode(":",$ress->duration)[1];
    
    $days += 1;
}

$duration_frm_M = $duration_M/60;
$duration_H += $duration_frm_M;
$duration_H = number_format($duration_H,0); 


$duration_M = 0;

$start = date_create($date . " " . $time_min);
$end = date_create($date . " " . $time_max);

$diff = date_diff($end, $start);

$rouded = $diff->format('%H : %I');
?>

<br/>

<!--<p style="font-size: 14pt; font-family: sans-serif;">Total Time Duration : &nbsp; {{$rouded}} &nbsp;&nbsp; (Hours : Minutes)</p>-->    
<p style="font-size: 14pt; font-family: sans-serif;">Total Time Duration : &nbsp; {{ $duration_H }} : {{ $duration_M }} &nbsp;&nbsp; (Hours : Minutes)</p>    


<p style="font-size: 14pt; font-family: sans-serif;">Total Days Count : &nbsp; {{ $days }}</p>
<p style="font-size: 14pt; font-family: sans-serif;">Total Row Count : &nbsp; {{ $ids }}</p>




</blockquote>

@stop
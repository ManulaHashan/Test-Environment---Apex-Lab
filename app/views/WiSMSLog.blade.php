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
    
    <h2>SMS LOG</h2>

    <table border="1">
        <thead>
            <tr>
                <th>Number</th>
                <th>Message</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>

            <?php
                $results = DB::select("select * from smslog where lab_lid = '".$_SESSION['lid']."' order by id DESC limit 50");
                    foreach ($results as $ress) {
            ?>

            <tr>
                <td>{{$ress->tpno}}</td>
                <td>{{$ress->msg}}</td> 
                <td>{{$ress->drep}}</td>
            </tr>

                    <?php } ?>
        </tbody>
    </table>


</blockquote>

@stop
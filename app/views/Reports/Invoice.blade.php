@extends('Templates/ReportTemplate')

@section('title')
Invoice
@stop

@section('head')
<script type="text/javascript">

</script>
@stop

@section('heading')
Invoice
@stop

@section('content')

<?php
$repResult = DB::select("select * from lps where lpsid = '" . $lpsID . "'");
foreach ($repResult as $lpsItem) {
    $date = $lpsItem->date;
    $sampleNo = $lpsItem->sampleNo;
    $time = $lpsItem->arivaltime;
    $Type = $lpsItem->type;
}
?>

<p>Invoice NO:{{ $IID }} &nbsp;&nbsp;|&nbsp;&nbsp; Date : {{ $date }} &nbsp;&nbsp;|&nbsp;&nbsp; Time : {{ $time }}  &nbsp;&nbsp;|&nbsp;&nbsp; Sample NO: {{ $sampleNo }} &nbsp;&nbsp;|&nbsp;&nbsp; Patient Type: {{ $Type }} </p>
<br/>
<h4 class="repSubHeading">Test Details</h4>
<table width="400" style="margin-left: 20px;">
    <tr>
        <th>Test Name <hr/></th>
        <th>Test Price <hr/></th>
    </tr>
    <?php
    $repResult2 = DB::select("select b.name,c.price from lps_has_test a,test b,Lab_has_test c where a.test_tid = b.tid and b.tid = c.test_tid and a.lps_lpsid = '" . $lpsID . "' group by b.tid");
    foreach ($repResult2 as $testItem) {
        ?>
        <tr>
            <td>
                &nbsp; <?php echo $testItem->name; ?>
            </td>
            <td>
                &nbsp; <?php echo $_SESSION['cuSymble']." ". $testItem->price; ?>
            </td>
        </tr>
        <?php
    }
    ?>
        <tr>
            <td><hr/></td>
            <td><hr/></td>
        </tr>
</table>
<br/>
<h4 class="repSubHeading">Payment Details</h4>
<?php
$cuSymbol = $_SESSION['cuSymble'];
$invResult = DB::select("select * from invoice where iid = '" . $IID . "'");
foreach ($invResult as $invItem) {
    $tot = number_format($invItem->total, 2, '.', ',');
    $dis = number_format($invItem->total - $invItem->gtotal, 2, '.', ',');
    $gtot = number_format($invItem->gtotal, 2, '.', ','); 
    $paid = number_format($invItem->paid, 2, '.', ','); 
    $due = number_format($invItem->gtotal - $invItem->paid, 2, '.', ',');     
}
?>

<table width="345" style="margin-left: 25px;">
    <tr>
        <td>Total Amount</td>
        <td> {{$cuSymbol}}</td>
        <td align="right">{{$tot}}</td>
    </tr>
    <tr>
        <td>Discount Amount</td>
        <td> {{$cuSymbol}}</td>
        <td align="right">{{$dis}}</td>
    </tr>
    <tr>
        <td>Grand Amount</td>
        <td> {{$cuSymbol}}</td>
        <td align="right">{{$gtot}}</td>
    </tr>
    <tr>
        <td>Paid Amount</td>
        <td> {{$cuSymbol}}</td>
        <td align="right">{{$paid}}</td>
    </tr>
    <tr>
        <td>Due Amount</td>
        <td> {{$cuSymbol}}</td>
        <td align="right">{{$due}}</td>
    </tr>
</table>
<br/>
<p>NOTE: Please bring this invoice to collect your report</p>
@stop




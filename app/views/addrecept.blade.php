@extends('template')

@section('title')
	Add Recepts
@stop

@section('head')
<meta name="_token" content="{!! csrf_token() !!}"/>

<script type="text/javascript" src="JS/jquery-3.1.0.js"></script>

<script type="text/javascript">	

	window.onload = function(){
		selectCus();
	}

	function selectItem(rid){
    	for (var i = tableDataOblect.length - 1; i >= 0; i--) {
    	 	if(tableDataOblect[i].rid===rid){

    	 		$('#id').val(tableDataOblect[i].rid);
    	 		$('#from').val(tableDataOblect[i].customer);
    	 		$('#amount').val(tableDataOblect[i].amount);
    	 		$('#reason').val(tableDataOblect[i].reason);
    	 		$('#date').val(tableDataOblect[i].date);
    	 		$('#method').val(tableDataOblect[i].method);

    	 	}
    	}

    }

	var tableDataOblect;
	function selectCus(){
		var tableHeaders = "<tr><th>Customer</th><th>Amount</th><th>Reason</th><th>Date</th><th>Method</th></tr>";
		var tableBody = "";

		var customer = $('#cus').val();
		var amount = $('#amo').val();

		$.ajax({
                    type: 'POST',
                    url: "SearchRecept",
                    data:{'customer':customer,'amount':amount, '_token': $('input[name=_token]').val()} ,
                    success: function(data) {
                    	data = JSON.parse(data);
                    	tableDataOblect =data;
                        console.log(data);

                        for (var i = data.length - 1; i >= 0; i--) {
                        	tableBody += "<tr style='cursor:pointer;' onclick='selectItem("+data[i].rid+")'><td>"+data[i].customer+"</td><td>"+data[i].amount+"</td><td>"+data[i].reason+"</td><td>"+data[i].date+"</td><td>"+data[i].method+"</td> </tr>";
                        }

                        $('#table').html(tableHeaders+tableBody);
                    }
                });
	}

	$(document).keypress(function(e) {
    if(e.which == 13) {
        selectCus();
    }  


});
</script>
@stop
	
@section('content')

	<h2>Please fill the form...</h2>
	<blockquote>
	<form action="manageRecept" method="post" cellpadding="10" >
		<table>
		<tr>
				<td width="100px">ID</td>
				<td><input type="text" name="id" id="id" class="form-control"></td>
			</tr>
			<tr>
				<td>Customer</td>
				<td><input type="text" name="from" id="from" class="form-control"></td>
			</tr>
			<tr>
				<td>Amount</td>
				<td><input type="text" name="amount" id="amount" class="form-control"></td>
			</tr>
			<tr>
				<td>Reason</td>
				<td><input type="text" name="reason" id="reason" class="form-control"></td>
			</tr>
			<tr>
				<td>date</td>
				<td><input type="text" name="date" id="date" class="form-control"></td>
			</tr>			
			<tr>
				<td>method</td>
				<td><input type="text" name="method" id="method" class="form-control"></td>
			</tr>
			<tr>
				<td></td>
				<td><br/><input type="submit" name="submit" value="Save Recept" class="btn btn-default" style="width: 100%;">
				<input type="submit" name="submit" value="Update Recept" class="btn btn-default" style="width: 100%;">
				<input type="submit" name="submit" value="Delete Recept" class="btn btn-default" style="width: 100%;">
				</td>
			</tr>
		</table>
	</form>

	@unless(empty($message))
	<div class="alert alert-success">
	<strong>Message!</strong>
	
		{{ $message; }}
	
	</div>
	@endunless

	</blockquote>

	<h3>View Recepts</h3>

	<h4>Advance Search</h4>
	<p>
		Customer : 
		<select name="cus" id="cus" onchange="selectCus()">
			<option value="%">All</option>			
			<?php 
				$customers = DB::select('select customer from recepts group by customer');
			 ?>
			@foreach($customers as $recept)
				<option>{{ $recept->customer }}</option>
			@endforeach
		</select>

		|

		Amount <input type="text" id="amo" style="width: 50px;"/>

	</p>

	<table id="table" class="table">		
		
	

	</table>

@stop


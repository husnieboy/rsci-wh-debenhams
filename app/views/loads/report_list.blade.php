<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes">
{{ HTML::style('resources/css/bootstrap.min.css') }}
{{ HTML::style('resources/css/bootstrap-responsive.min.css') }}
{{ HTML::style('resources/css/style.css') }}
</head>
<body>
<!-- <div class="table-responsive">
 -->		<div style="text-align: center">
				<a class="font-size-02"> RSCI- eWMS<br/>Shipping Reports<br/></a>
				Printed By: {{Auth::user()->username}} <br>
				Print Date: {{ date('m/d/y h:i A')}}
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>{{ $col_load_no }}</th>  
				<th> TL no.</th>  
				<th>Store</th>  
				<th>Box no.</th>  
				<th> Total Qty</th>  
				<th> Ship Date</th>  
				<th> Piler name</th>  
			</tr>
		</thead>
		@if( !CommonHelper::arrayHasValue($results) )
			<tr class="font-size-08">
				<td colspan="3" class="align-center">{{ $text_empty_results }}</td>
			</tr>
		@else
			@foreach($results as $load)
			<tr class="font-size-01"> 
			 	<td></td>
			</tr>
			@endforeach
		@endif
	</table>
<!-- </div> -->

</body>
</html>
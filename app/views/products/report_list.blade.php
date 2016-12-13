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
<div class="table-responsive">
			<div style="text-align: center">
				<h1>Casual Clothing Retailers Inc.<br/>PRODUCTS REPORT</h1>
				Print Date: {{ date('m/d/y h:i A')}}
			</div>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>{{ $col_prod_sku }}</th>
				<th>{{ $col_prod_upc }}</th>
				<th>{{ $col_prod_full_name }}</th>
				<th>{{ $col_prod_short_name }}</th>
				<th>{{ $col_department }}</th>
				<th>{{ $col_sub_department }}</th>
			</tr>
		</thead>
		@if( !CommonHelper::arrayHasValue($results) )
			<tr class="font-size-13">
				<td colspan="6" class="align-center">{{ $text_empty_results }}</td>
			</tr>
		@else
			@foreach( $results as $product )
			<tr class="font-size-13">
				<td>{{ $product->sku }}</td>
				<td>{{ $product->upc }}</td>
				<td>{{ $product->description }}</td>
				<td>{{ $product->short_description }}</td>
				
			</tr>
			@endforeach
		@endif
			<tr>
			<td>Total item:{{count($results) }} </td>
		</tr>
	</table>
</div>

</body>
</html>
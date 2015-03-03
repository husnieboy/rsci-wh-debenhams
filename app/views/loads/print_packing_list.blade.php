<style>
@media print {
    .soContainer {page-break-after: always; page-break-inside: avoid;}
    #actionButtons {display: none;}

}
@media screen {
    #mainContainer {width: 750px; }

}
body { font: normal 12px arial; margin: 0; counter-reset:pageNumber;}
table {padding: 0; border-collapse: collapse;}
h1 {margin-bottom: 5px;}
header {margin-bottom: 20px;}

.soContainer { border: solid 1px #000; padding: 10px;}
.soContainer:after { content:""; display:block; clear:both; }

.doctitle {text-align: center;}
.commonInfo {width: 100%; border: none;}
.commonInfo th, .commonInfo td  {text-align: left;}
.commonInfo th {width: 150px;}

.contents {margin-top: 10px; width: 100%;}
.contents th, .contents td { padding: 2px;  border: solid 1px #F0F0F0; margin: 0; }
.contents th {text-align: left; padding: 5px;}
.contents th {background-color: #F0F0F0}

.comments {width: 100%; margin-top: 15px;}
.comments hr{ margin-top:25px;}

.signatories {width: 100%; margin-top: 25px; line-height: 20px;  }
.signatories div {float: left; width: 30%; margin-right: 3%;}
.signatories hr{margin-top:25px;}
td.underline hr{ margin-top: 20px; border: none; border-bottom: solid 1px #000;}
td.underline {padding-bottom: 0; }

#actionButtons { top:0; left: 0; background-color: #DFF1F7; padding: 5px;}
#actionButtons a {display: inline-block; padding: 1em; background-color: #3199BE; text-decoration: none; font: bold 1em Verdana ; color: #FFF;}
#actionButtons a:hover {background-color: #1F6077 ;}

</style>

<div id="actionButtons">
	<a href="#" onclick="window.print();">PRINT THIS</a>
	<a href="{{url('load/list')}}">BACK TO LOAD LIST</a>

</div>
	<section class="soContainer">
		<header>
			<div class="doctitle">
				<h1>Casual Clothing Retailers Inc.<br/>PACKING LIST</h1>
				Print Date: {{ date('m/d/y h:i A')}}
			</div>
		</header>
		<table class="commonInfo">
			<tr>
				<td>
					<table><tr>
							<th>From Location:</th>
							<td>Warehouse</td>
						</tr><tr>
							<th>To Location:</th>
							<td>{{ $records['store_code'] .' - ' . $records['store_name']}}</td>
						</tr>
					</table>
				</td>
				<td>
					<table><tr>
							<th>Username:</th>
							<td>{{Auth::user()->username;}}</td>
						</tr><tr>
							<th>PL Number:</th>
							<td>_____________</td>
						</tr>
					</table>
				</td>
			<tr>
		</table>
		<table class="contents">
			<tr>
				<th rowspan="2" style="text-align: center">Brand</th>
				<th rowspan="2" style="text-align: center">Ref MTS No.</th>
				<th rowspan="2" style="text-align: center">Boxes</th>
				<th colspan="2" style="text-align: center">Quantity</th>
				<th rowspan="2" style="text-align: center">Received By/ Date</th>
			</tr>
            <tr>
				<th style="text-align: center">Issued</th>
				<th style="text-align: center">Received</th>
			</tr>
			<?php 
				$boxarray=[];
				$grandTotal = 0;
			?>
			@foreach($records['StoreOrder'] as $soNo => $val)
				<tr>
					<td align="center" rowspan={{count($val['items'])+1}} >{{$val['brand']}}</td>
					<td align="center"><strong>{{$soNo}}</strong></td>
					<td align="center" colspan="3"></td>
				</tr>
			    @foreach($val['items'] as $boxNo => $items)
				<tr>
					<td></td>
					<?php 
						if(!in_array($boxNo, $boxarray))
							array_push($boxarray, $boxNo);
	    				$boxTotal = 0;
					?>
			    
			    	<td align="center">{{$boxNo}}</td>
					@foreach($items as $item)
						<?php
							if($item->so_no == $soNo){
								$boxTotal += $item->moved_qty;
								$grandTotal += $item->moved_qty;
							}
						?>
					@endforeach
				    <td align="center">{{$boxTotal}}</td>
					<td class="underline"><hr/></td>
				</tr>
			    @endforeach
			@endforeach
			<tr>
				<?php 
					$numOfBoxTotal=count($boxarray);
				?>
					<td></td>
			    <td style="text-align: center"><strong>Box Total:</strong></td>
			    <td align="center"> {{ $numOfBoxTotal }} </td>
			    <td align="center">{{$grandTotal}}</td>
				<td class="underline"><hr/></td>
				<td class="underline"><hr/></td>
			</tr>
			<tr>
				<td>.</td>
			</tr>
			<tr>
					<td></td>
				<th style="text-align: center">Grand Total: </th>
			    <td align="center">{{$numOfBoxTotal}}</td>
			    <td align="center">{{$grandTotal}}</td>
				<td class="underline"><hr/></td>
			</tr>
			<tr>
				<td>.</td>
			</tr>
			<tr><th colspan="6">Inter-Transfers</th></tr>
			<tr>
					<td></td>
				<th style="text-align: center">Grand Total: </th>
				<td class="underline"><hr/></td>
				<td class="underline"><hr/></td>
				<td></td>
			</tr>
		</table>
		<table>
			<tr>
				<th style="text-align: right">Seal #1:</th>
				<td>_____________</td>
					<td>&nbsp&nbsp&nbsp</td>
				<th style="text-align: right">Seal #2:</th>
				<td>_____________</td>
			</tr>
		</table>

		<div class="signatories">
			<div>
				Prepared By / Date: <hr/><br/>
				Issued By / Date: <hr/><br/>
				Plate No.: <hr/><br/>
			</div>
			<div>
				Other Remarks: <hr/><br/>
				Issuance/Validated By / Date:<hr/><br/>
				Deliveryman:<hr/><br/>
			</div>
			<div>
				Delivery Van Opened By / Date:<hr/><br/>
				Updated By / Date:<hr/><br/>
				Driver:<hr/><br/>
			</div>
		</div>
	</section>
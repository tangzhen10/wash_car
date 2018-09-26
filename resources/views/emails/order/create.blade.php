<style>
	table {
		background: #eee;
		border-collapse: collapse;
		width: 800px;
	}
	td {
		background: #fff;
		border: #ccc solid 1px;
		padding: 5px;
	}
	tr td:first-child {
		font-weight: bold;
		text-align: center;
	}
	caption {
		font-weight: bold;
		padding: 10px;
	}
</style>
<table>
	<caption>有新的洗车订单 - <a href="{{$orderLink}}" target="_blank">{{$orderData['order_id']}}</a></caption>
	<tbody>
	<tr>
		<td>{{trans('common.order_id')}}</td>
		<td>{{$orderData['order_id']}}</td>
	</tr>
	<tr>
		<td>{{trans('common.wash_product')}}</td>
		<td>{{$orderData['wash_product']}}</td>
	</tr>
	<tr>
		<td>{{trans('common.wash_time')}}</td>
		<td>{{$orderData['wash_time']}}</td>
	</tr>
	<tr>
		<td>{{trans('common.plate_number')}}</td>
		<td>{{$orderData['plate_number']}}</td>
	</tr>
	<tr>
		<td>{{trans('common.serve_address')}}</td>
		<td>{{$orderData['address']}}</td>
	</tr>
	<tr>
		<td>{{trans('common.order_amount')}}</td>
		<td>{{$orderData['total']}}</td>
	</tr>
	<tr>
		<td>{{trans('common.contact_user')}}</td>
		<td>{{$orderData['contact_user']}}</td>
	</tr>
	<tr>
		<td>{{trans('common.contact_phone')}}</td>
		<td>{{$orderData['contact_phone']}}</td>
	</tr>
	<tr>
		<td>{{trans('common.create_at')}}</td>
		<td>{{$orderData['create_at']}}</td>
	</tr>
	</tbody>
</table>
@extends('admin.structure_form')
@section('js')
	<script>
		function validate_form() {
			
			var name = $('input[name="name"]');
			if (!name.val().trim()) {
				layer.tips('{{trans('validation.required', ['attr' => trans('common.name')])}}', name);
				name.focus();
				return false;
			}
			
			var wash_product = $('select[name="wash_product_id"]');
			if (!wash_product.val()) {
				layer.tips('{{trans('validation.required', ['attr' => trans('common.wash_product')])}}', wash_product);
				return false;
			}
			
			var price = $('input[name="price"]');
			if (!price.val().trim()) {
				layer.tips('{{trans('validation.required', ['attr' => trans('common.price')])}}', price);
				price.focus();
				return false;
			} else {
				if (parseFloat(price.val().trim()) <= 0) {
					layer.tips('{{trans('validation.more_than.number', ['attr' => trans('common.price'), 'min' => 0])}}', price);
					price.focus();
					return false;
				}
			}
			
			var price_ori = $('input[name="price_ori"]');
			if (!price_ori.val().trim()) {
				layer.tips('{{trans('validation.required', ['attr' => trans('common.price_ori')])}}', price_ori);
				price_ori.focus();
				return false;
			} else {
				if (parseFloat(price_ori.val().trim()) < parseFloat(price.val().trim())) {
					layer.tips('{{trans('validation.min.number', ['attr' => trans('common.price_ori'), 'min' => trans('common.price')])}}', price_ori);
					price_ori.focus();
					return false;
				}
			}
			
			var expire_date = $('input[name="expire_date"]');
			if (!expire_date.val().trim()) {
				layer.tips('{{trans('validation.required', ['attr' => trans('common.expire_date')])}}', expire_date);
				expire_date.focus();
				return false;
			} else {
				if (parseFloat(expire_date.val().trim()) <= 0 || parseFloat(expire_date.val().trim()) != parseInt(expire_date.val().trim())) {
					layer.tips('{{trans('validation.must_be_positive_int', ['attr' => trans('common.expire_date')])}}', expire_date);
					expire_date.focus();
					return false;
				}
			}
			
			var use_times = $('input[name="use_times"]');
			if (!use_times.val().trim()) {
				layer.tips('{{trans('validation.required', ['attr' => trans('common.use_times')])}}', use_times);
				use_times.focus();
				return false;
			} else {
				if (parseFloat(use_times.val().trim()) <= 0 || parseFloat(use_times.val().trim()) != parseInt(use_times.val().trim())) {
					layer.tips('{{trans('validation.must_be_positive_int', ['attr' => trans('common.use_times')])}}', use_times);
					use_times.focus();
					return false;
				}
			}
			
			return true
		}
	</script>
@endsection
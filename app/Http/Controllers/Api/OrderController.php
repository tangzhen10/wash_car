<?php

namespace App\Http\Controllers\Api;

class OrderController extends BaseController {
	
	public function checkout() {
		
		\OrderService::createOrder();
	}
}

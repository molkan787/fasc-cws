<?php
include_once 'dep.php';

class ControllerApiPos extends Controller{

	public function listProducts(){

		checkAccess(0);

		$this->load->model('catalog/prt');

		$store_id = $this->config->get('config_store_id');

		$products = $this->model_catalog_prt->getProductsPos($store_id);

		$this->respond_json(array('items' => $products));
	}

	public function addOrder(){

		checkAccess(0); // Check POS access


		$this->load->model('checkout/order');
		$this->load->model('admin/order');
		$this->load->model('admin/product');
		$this->load->model('admin/customer');

		$store_id = $this->config->get('config_store_id');
		$prts = $this->getInput('products');
		$customerName = $this->getInput('customerName');
		$customerPhone = $this->getInput('customerPhone');
		$other_val = floatval($this->getInput('other_val'));

		$customer = null;
		if(strlen($customerPhone) == 10){
			$customer = $this->model_admin_customer->getOrCreateCustomer($customerPhone, $customerName);
		}

		if(empty($prts)){
			$this->respond_fail('argument_mssing');
			return;
		}
		$prts = json_decode($prts, true)['items'];

		$products = array();
		$total = 0;
		$saved = 0;
		foreach ($prts as $p) {
			$real_ltotal = floatval($p['real_price']) * intval($p['q']);
			$ltotal = floatval($p['price']) * intval($p['q']);
			$total += $ltotal;
			$saved += $real_ltotal - $ltotal;
			$products[] = $this->getProductArray($p['id'], $p['name'], $p['q'], $p['price']);
		}

		if($other_val > 0){
			$total += $other_val;
			$products[] = $this->getProductArray(0, 'Other', 1, $other_val);
		}

		$data = $this->getOrderArray($store_id, $products, $total, $customer);
		$data['saved_amount'] = $saved;

		$order_id = $this->model_checkout_order->addOrder($data);
		$this->model_checkout_order->addOrderHistory($order_id, 5);

		$this->model_admin_order->markAsPaid($order_id);

		$this->respond_json(array(
			'order_id' => $order_id,
			'customer' => $customer['firstname'] . ' ' . $customer['lastname'],
			'saved' => $saved,
		));

	}

	public function addOrderCS(){

		if(!$this->customer->getId()){
			$this->respond_fail('NO_CUSTOMER');
			return;
		}

		$address_1 = $this->getInput('address_1');
		$address_2 = $this->getInput('address_2');
		$city = $this->getInput('city');

		$pay_method = $this->getInput('pay_method');


		$del_date = $this->getInput('del_date');
		$del_timing = $this->getInput('del_timing');
		$del_date = $this->db->escape($del_date);
		$del_timing = $this->db->escape($del_timing);

		$fast_del = (int)$this->getInput('fast_del', 0);
		// ^^ TODO ^^

		$customer = array(
			'customer_id' => $this->customer->getId(),
			'firstname' => $this->customer->getFirstname(),
			'lastname' => $this->customer->getLastname(),
			'telephone' => $this->customer->getTelephone(),
			'address_1' => $address_1,
			'address_2' => $address_2,
			'city' => $city
		);


		$this->load->model('checkout/order');
		$this->load->model('admin/product');
		$this->load->model('admin/store');

		$store_id = $this->config->get('config_store_id');
		$prts = $this->getInput('products');
		if(empty($prts)){
			$this->respond_fail('argument_mssing');
			return;
		}
		$prts = json_decode($prts, true)['items'];

		$products = array();
		$ids = '';
		$total = 0;
		$saved = 0;
		foreach ($prts as $pid => $q) {
			if($ids != '') $ids .= ',';
			$ids .= (int)$pid;
		}

		$p_list = $this->model_admin_product->getProductsByIds($ids);

		foreach ($p_list as $p) {
			$id = $p['product_id'];
			$real_price = floatval($p['price']);
			$price = $real_price;
			$discount = (int)$p['discount_amt'];
			if($discount > 0){
				if($p['discount_type'] == '1') $price -= ($price * $discount  / 100);
				else $price -= $discount;
			}
			$ltotal = $price * intval($prts[$id]);
			$real_ltotal = $real_ltotal * intval($prts[$id]);
			$total += $ltotal;
			$saved += $real_ltotal - $ltotal;
			$products[] = $this->getProductArray($id, $p['name'], (int)$prts[$id], $price);
		}

		if($fast_del){
			$bsd = $this->model_admin_store->getStoreBSD($store_id);
			$total += (int)$bsd['fast_del_cost'];
		}


		$data = $this->getOrderArray($store_id, $products, $total, $customer, $pay_method);
		$data['saved_amount'] = $saved;

		$order_id = $this->model_checkout_order->addOrder($data);
		if($pay_method != 'razor'){
			$this->model_checkout_order->addOrderHistory($order_id, 1);
		}

		$this->db->query("UPDATE ".DB_PREFIX."order SET del_date = '".$del_date."', del_timing = '".$del_timing."', fast_del = '".$fast_del."' WHERE order_id = '".$order_id."'");

		$response = array('order_id' => $order_id, 'total' => $total);

		if($pay_method == 'razor'){
			$this->load->model('admin/payments');
			$response['api_key'] = $this->model_admin_payments->getRazorKey();
		}else{
			$this->load->model('admin/notifier');
			$this->model_admin_notifier->notify_order($total, $del_timing);
		}

		$this->respond_json($response);

	}

	private function getOrderArray($store_id, $products, $total, $customer = null, $pay_method = 'cod'){
		$payment_name = $pay_method == 'cod' ? 'Cash On Delivery' : 'Credit Card/NetBanking';
		$payment_code = $pay_method;
		if($customer == null){
			$payment_name = 'Cash';
			$payment_code = 'cash';
			$customer = array(
				'customer_id' => '0',
				'firstname' => 'Walk on',
				'lastname' => 'Customer',
				'telephone' => '',
				'address_1' => '',
				'address_2' => '',
				'city' => ''
			);
		}
		return array (
				  'totals' => 
					  array (
					    0 => 
						    array (
						      'code' => 'sub_total',
						      'title' => 'Sub-Total',
						      'value' => $total,
						      'sort_order' => '1',
						    ),
					    1 => 
						    array (
						      'code' => 'total',
						      'title' => 'Total',
						      'value' => $total,
						      'sort_order' => '9',
						    ),
					  ),
				  'invoice_prefix' => 'INV-2019-00',
				  'store_id' => $store_id,
				  'store_name' => 'WalkOnRetail',
				  'store_url' => 'https://www.walkonretail.com/',
				  'customer_id' => $customer['customer_id'],
				  'customer_group_id' => '1',
				  'firstname' => $customer['firstname'],
				  'lastname' => $customer['lastname'],
				  'email' => '',
				  'telephone' => $customer['telephone'],
				  'custom_field' => array ( 1 => '1'),
				  'payment_firstname' => '',
				  'payment_lastname' => '',
				  'payment_company' => '',
				  'payment_address_1' => '',
				  'payment_address_2' => '',
				  'payment_city' => '',
				  'payment_postcode' => '',
				  'payment_zone' => '',
				  'payment_zone_id' => '',
				  'payment_country' => '',
				  'payment_country_id' => '',
				  'payment_address_format' => '',
				  'payment_custom_field' => array (),
				  'payment_method' => $payment_name,
				  'payment_code' => $payment_code,
				  'shipping_firstname' => '',
				  'shipping_lastname' => '',
				  'shipping_company' => '',
				  'shipping_address_1' => $customer['address_1'],
				  'shipping_address_2' => $customer['address_2'],
				  'shipping_city' => $customer['city'],
				  'shipping_postcode' => '',
				  'shipping_zone' => '',
				  'shipping_zone_id' => '1492',
				  'shipping_country' => 'India',
				  'shipping_country_id' => '99',
				  'shipping_address_format' => '',
				  'shipping_custom_field' => array (),
				  'shipping_method' => '',
				  'shipping_code' => '',
				  'products' => $products,
				  'vouchers' => array (),
				  'comment' => '',
				  'total' => $total,
				  'affiliate_id' => 0,
				  'commission' => 0,
				  'marketing_id' => 0,
				  'tracking' => '',
				  'language_id' => '1',
				  'currency_id' => '4',
				  'currency_code' => 'INR',
				  'currency_value' => '1.00000000',
				  'ip' => '127.0.0.1',
				  'forwarded_ip' => '',
				  'user_agent' => 'CUSTOMER APP',
				  'accept_language' => 'en-US;hi-IN',
				);
	}

	public function getProductArray($id, $name, $q, $price){
		return array (
		      'product_id' => $id,
		      'name' => $name,
		      'model' => '---',
		      'option' => array (),
		      'download' => array (),
		      'quantity' => $q,
		      'subtract' => $q,
		      'price' => $price,
		      'total' => $price * $q,
		      'tax' => 0,
		      'reward' => 0,
		    );
	}

}
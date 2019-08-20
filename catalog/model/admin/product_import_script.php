$pid = $this->model_admin_product->addProduct($_data);

			$data = array();
	        $data['store_id'] = 1;
	        $data['price'] = floatval($row['prize']);
	        $data['quantity'] = $row['stock'];
	        $data['barcode'] = '';//$row['attr1'];
	        $data['spf'] = $row['spf'];
	        $data['spf_unit'] = $row['spf_unit'];


	        $discount = (int)$row['discount'];
	        $isd = $discount > 1000;

	        $data['discount_amt'] = $isd ? ($discount - 1000) : $discount;
	        $data['discount_type'] = $isd ? 2 : 1;
	        $data['gst'] = '';//$row['gst'];
	        $data['hsn'] = '';//$row['hsn'];
	        $data['status'] = 1;
	        $data['cat'] = $this->getCatId($row['cat']);
	        $data['subcat'] = $this->getSubCatId($row['cat'], $row['subcat']);
	        $data['child_subcat'] = 0;
	        $data['brand'] = 0;
	        $data['sub_brand'] = 0;

	        $fprice = floatval($data['price']);
	        $discount_type = intval($data['discount_type']);
	        $discount_amt = intval($data['discount_amt']);

	        if($discount_amt > 0){
	            if($discount_type == 1){
	                $fprice -= ($fprice * $discount_amt / 100);
	            }else{
	                $fprice -= $discount_amt;
	            }
	        }
	        $data['fprice'] = $fprice;

 			$data['image'] = 'catalog/Products/pi-'.$pid.'.jpg';

	        $this->model_admin_product->setProduct($pid, $data);

	        $cats = array($data['cat'], $data['subcat']);
	        $this->model_admin_product->setProductCategories($pid, $cats);


        	$desc = array('name' => $row['display_name'], 'description' => $row['adt_info']);

	        $this->model_admin_product->setProductDescription($pid, 1, $desc);
	        $this->model_admin_product->setProductDescription($pid, 2, $desc);

	        $da = (float)$data['discount_amt'];
	        if($da > 0){
	            $dt = (int)$data['discount_type'];
	            $discount = floatval($data['price']);
	            if($dt == 1) $discount = ($discount / 100) * (100 - $da);
	            else if($dt == 2) $discount -= $da;
	            $this->model_admin_product->setProductDiscount($pid, $discount);
	        }else{
	            $this->model_admin_product->deleteProductDiscount($pid);
	        }

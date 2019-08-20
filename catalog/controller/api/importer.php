<?php
include_once 'dep.php';
die('ACCESS_DENIED');
class ControllerApiImporter extends Controller{

	private $catIds = array(
		'fruits_vegetables' => 5,
		'dairy_break' => 3,
		'household' => 4,
		'kirana_goods' => 2,
		'personal_care' => 6,
		'ayurvedic' => 8,
		'foods_snacks' => 7,
		'offers' => 1,
		'other' => 12,
		'electronic_accessories' => 9,
		'poojan_saamagri' => 10,
		'clothes_apparel' => 11
	);

	public function adjust(){

	}

	public function index(){
		return;
		$this->load->model('admin/banner');

		/*$servername = "139.59.8.143";
		$username = "phpmyadmin";
		$password = "h46rstj4gzq6f";

		// Create connection
		$conn = new mysqli($servername, $username, $password);
		$conn->select_db("surjan");

		$query = $conn->query("SELECT * FROM banners");*/

		$products = $this->db->query("SELECT product_id, image FROM oc_product")->rows;

		$ids = explode(',', '1217,1216,1201,1169,1168,1139,1002,1001,473,189,188,187,179,178,140,136');
		$new_ids = array();

		foreach ($ids as $id) {
			$query = $this->db->query("SELECT product_id FROM oc_product WHERE brand = ".(int)$id);
			$new_ids[] = $query->row['product_id'];
		}

		echo 'Result: '.join(',', $new_ids);
	}

    private function getCatId($slug){
    	return $this->catIds[$slug];
    }
    private function getSubCatId($parentSlug, $slug){
    	$cat_id = $this->getCatId($parentSlug);
    	return $this->subs_ids[$cat_id][$slug];
    }

    private $subs_ids = array (
				  1 => 
				  array (
				    'Offer' => 73,
				  ),
				  2 => 
				  array (
				    'oil_ghee' => 62,
				    'tea_coffee' => 63,
				    'detergent_cake' => 64,
				    'daal_chawal' => 65,
				    'atta_maida' => 66,
				    'poha_' => 67,
				    'masale_spices' => 68,
				    'Others' => 69,
				  ),
				  3 => 
				  array (
				    'cake_bakery' => 76,
				    'milk_products' => 77,
				    'toste_biscuits' => 78,
				  ),
				  4 => 
				  array (
				    'spray_coil' => 70,
				    'film_foil' => 71,
				    'Harpic' => 72,
				  ),
				  5 => 
				  array (
				    'Fruits' => 74,
				    'Vegitables' => 75,
				  ),
				  6 => 
				  array (
				    'handwash_shampoo' => 79,
				    'toothpaste_brush' => 80,
				    'cream_powder' => 81,
				    'lotion_gel' => 82,
				    'soap_bodywash' => 83,
				    'perfume_deo' => 84,
				    'hair_oil' => 85,
				    'others_' => 86,
				    'Face Wash' => 87,
				    'Hair Colour' => 88,
				  ),
				  7 => 
				  array (
				    'chips_papad' => 89,
				    'namkeen_bhujiya' => 90,
				    'snaks_' => 91,
				    'chocolates_sweet' => 92,
				    'dry_fruits' => 93,
				    'Honey' => 94,
				    'Instant Mix' => 95,
				    'Aachar(pickle)' => 96,
				  ),
				  8 => 
				  array (
				    'patanjali_' => 97,
				    'sri_sri_tattva' => 98,
				    'oil_powder' => 99,
				  ),
				  9 => 
				  array (
				    '' => 101,
				  ),
				  10 => 
				  array (
				    'Dhoop' => 102,
				    'Agarbatti' => 103,
				  ),
				  11 => 
				  array (
				    'coming_soon' => 104,
				  ),
				  12 => 
				  array (
				    'other' => 100,
				  ),
				);

     private function getDataArray()
    {
        return array(
            'product_description' =>
                array(
                1 =>
                    array(
                    'name' => '',
                    'description' => '',
                    'meta_title' => '',
                    'meta_description' => '',
                    'meta_keyword' => '',
                    'tag' => ''
                ),
                2 =>
                    array(
                    'name' => '',
                    'description' => '',
                    'meta_title' => '',
                    'meta_description' => '',
                    'meta_keyword' => '',
                    'tag' => ''
                )
            ),
            'model' => 'model',
            'sku' => '',
            'upc' => '',
            'ean' => '',
            'jan' => '',
            'isbn' => '',
            'mpn' => '',
            'location' => '',
            'price' => '0.0000',
            'tax_class_id' => '9',
            'quantity' => '1',
            'minimum' => '1',
            'subtract' => '1',
            'stock_status_id' => '5',
            'shipping' => '1',
            'date_available' => '2018-12-11',
            'length' => '0.00000000',
            'width' => '0.00000000',
            'height' => '0.00000000',
            'length_class_id' => '1',
            'weight' => '0.00000000',
            'weight_class_id' => '1',
            'status' => '1',
            'sort_order' => '1',
            'manufacturer' => '',
            'manufacturer_id' => '0',
            'filter' => '',
            'product_store' =>
                array(
                    0 => $this->config->get('config_store_id')
                ),
            'download' => '',
            'related' => '',
            'option' => '',
            'image' => '',
            'points' => '0'
        );
    }

}
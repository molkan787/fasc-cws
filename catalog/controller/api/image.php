<?php
include_once 'dep.php';

class ControllerApiImage extends Controller
{
	public function index(){

		die('');

	}

	public function up(){
		checkAccess(AG_ADMIN);

		if(!empty($this->request->files['image']['name'])){
			$image_size = getimagesize($this->request->files['image']['tmp_name']);
			if ($image_size==FALSE){
				$this->respond_fail('Invalid_file_type');
			}else{

				$image = $this->request->files['image']['name'];
				$expimage = explode('.', $image);
				$imageexptype = $expimage[1];
				$date = date('m/d/Yh:i:sa', time());
				$rand = rand(10000,99999);
				$encname = $date.$rand;
				$imagename = md5($encname).'.'.$imageexptype;
				$imagepath = "image/catalog/Products/".$imagename;
				move_uploaded_file($this->request->files["image"]["tmp_name"], $imagepath);
				$this->respond_json(array('filename' => $imagename));
			}
		}else{
			$this->respond_fail('file_missing');
		}
	}

	public function upBase64(){
		checkAccess(AG_ADMIN);
		$folder = $this->getInput('folder');

		$folderPath;
		if($folder == 'products') $folderPath = "catalog/Products/";
		else if($folder == 'banners') $folderPath = "catalog/Banners/";
		else if($folder == 'categories') $folderPath = 'catalog/Categories/';
		else if($folder == 'logos') $folderPath = 'catalog/Logos/';
		else if($folder == 'ls_ads') $folderPath = 'catalog/LsAds/';
		else if($folder == 'ads') $folderPath = 'catalog/Ads/';
		else{
			$this->respond_fail('unknown_folder');
			return;
		}

		$imageData = file_get_contents('php://input');


		if($folder == 'logos'){
			$this->db->query("UPDATE fasc_imgs SET base64 = '".$this->db->escape($imageData)."', date_modified = '".time()."' WHERE `name` = 'logo' LIMIT 1");
		}else if($folder == 'ls_ads'){
			$this->db->query("UPDATE fasc_imgs SET base64 = '".$this->db->escape($imageData)."', date_modified = '".time()."' WHERE `name` = 'ls_ad' LIMIT 1");
		}


		$date = date('m/d/Yh:i:sa', time());
		$rand = rand(10000, 99999);
		$encname = $date.$rand;
		$imagename = md5($encname). ($folder == 'categories' ? '.png' : '.jpg');

		$result = $this->generateImage($imageData, "image/".$folderPath, $imagename);

		if(!$result){
			$this->respond_fail('invalid_input');
			return;
		}

		$this->respond_json(array('filename' => $folderPath.$imagename));
	}

	private function generateImage($img, $folderPath, $name)
    {

        $image_parts = explode(";base64,", $img);

        if(count($image_parts) < 2){
        	return false;
        }

        $image_type_aux = explode("image/", $image_parts[0]);

        $image_type = $image_type_aux[1];

        $image_base64 = base64_decode($image_parts[1]);

        $file = $folderPath . $name;

        file_put_contents($file, $image_base64);

        return true;
    }

}
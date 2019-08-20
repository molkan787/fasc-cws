<?php
include_once 'dep.php';

class ControllerApiCategory extends Controller{

	public function info(){

        $cat_id = $this->getInput('cat_id');
        if(empty($cat_id)){
            $this->respond_fail('argument_missing');
            return;
        }
		
        $this->load->model('admin/category');
        $this->load->model('tool/image');

        $data = $this->model_admin_category->getCategory($cat_id);

        $data['image'] = url($this->model_tool_image->resize($data['image'], 120, 120));

        $this->respond_json($data);


	}

    public function delete(){
        checkAccess(AG_ADMIN);
        
        $cat_id = $this->getInput('cat_id');
        if(empty($cat_id)){
            $this->respond_fail('argument_missing');
            return;
        }
        
        $this->load->model('admin/category');

        $data = $this->model_admin_category->getCategory($cat_id);
        if($data){
            $this->model_admin_category->deleteCategory($cat_id);
            $this->respond_json(array('cat_id' => $cat_id));
        }else{
            $this->respond_fail('not_found');
        }

    }

    public function save(){

        checkAccess(AG_ADMIN);

        $this->load->model('admin/category');

        $store_id = $this->config->get('config_store_id');

        $gtype = (int)$this->getInput('gtype');
        $parent = (int)$this->getInput('parent');
        $cat_id = $this->getInput('cat_id');
        $image = $this->getInput('image');
        $name1 = $this->getInput('name1');
        $name2 = $this->getInput('name2');

        $childs = $this->getInput('childs_order');
        $childs = explode(',', $childs);

        if($cat_id == 'new'){
            $data = $this->getArrayData($parent, $gtype);
            $cat_id = $this->model_admin_category->addCategory($data);
        }
        if(count($childs)){
            $this->model_admin_category->setSortOrder($childs, $store_id);
        }

        $this->model_admin_category->setCategoryName($cat_id, 1, $name1);
        $this->model_admin_category->setCategoryName($cat_id, 2, $name2);
        if(!empty($image)) $this->model_admin_category->setCategoryImage($cat_id, $image);

        $this->respond_json(array('cat_id' => $cat_id));

    }

    public function list_cats(){
        
		$this->load->model('catalog/category');
        $this->load->model('tool/image');
        
        $gtype = (int)$this->getInput('gtype', 0);

        $categories = $this->model_catalog_category->getCategories(0, $gtype);

        foreach($categories as &$category){
            $category['image'] = url($this->model_tool_image->resize($category['image'], 120, 120));
        }

        $this->respond_json($categories);
    }

    public function sort_order(){

        $this->load->model('admin/category');

        $cats = $this->getInput('ids');
        $store_id = $this->config->get('config_store_id');

        $cats = explode(',', $cats);

        $this->model_admin_category->setSortOrder($cats, $store_id);

        $this->respond_json('');
    }


    private function getArrayData($parent_id = 0, $gtype = 0, $name1 = '', $name2 = ''){
        return array(
            "category_description" => array(
                "1" => array(
                    "name" => $name1,
                    "description" => "",
                    "meta_title" => $name1,
                    "meta_description" => "",
                    "meta_keyword" => "",
                ) ,
                "2" => array(
                    "name" => $name2,
                    "description" => "",
                    "meta_title" => $name2,
                    "meta_description" => "",
                    "meta_keyword" => ""
                ) ,
            ) ,
            "path" => "",
            "parent_id" => $parent_id,
            "filter" => "",
            "category_store" => array(
               $this->config->get('config_store_id')
            ) ,
            "image" => "",
            "column" => "1",
            "sort_order" => "0",
            "status" => "1",
            "gtype" => $gtype
        );
    }

}
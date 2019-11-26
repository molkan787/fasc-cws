<?php
include_once 'dep.php';

define('USERFILES_DIR', 'user_files/');

class ControllerApiFiles extends Controller{

    public function upload(){
        $file = $_FILES["file"];
        $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

        $filename = 'FILE_' . time() . '.' . $ext;
        move_uploaded_file($file["tmp_name"], USERFILES_DIR . $filename);

        $this->respond_json(array(
            'filename' => $filename
        ));
    }

}
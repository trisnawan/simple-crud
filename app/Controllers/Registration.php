<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use Trisnawan\ImageResizer\ImageResizer;
use Ramsey\Uuid\Uuid;

class Registration extends ResourceController
{
    protected $modelName = 'App\Models\UserModel';
    protected $format    = 'json';

    public function postIndex()
    {
        $post = $this->request->getPost();
        if(!($post['name'] ?? false) || !($post['phone'] ?? false) || !($post['email'] ?? false) || !($post['password'] ?? false)){
            return $this->respond([
                "status" => false,
                "message" => "Silahkan masukan email dan password terlebih dahulu",
            ], 200);
        }

        $saved = $this->model->insert($post);
        if(!$saved){
            $error = getOneError($this->model->errors());
            return $this->respond([
                "status" => false,
                "message" => $error ?? 'User failed to save, please try again.',
            ], 400);
        }
        return $this->respond([
            "status" => true,
            "message" => "Success",
            "data" => $this->model->find($saved)
        ], 200);
    }

}
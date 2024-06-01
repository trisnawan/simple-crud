<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use Trisnawan\ImageResizer\ImageResizer;
use Ramsey\Uuid\Uuid;

class Login extends ResourceController
{
    protected $modelName = 'App\Models\UserModel';
    protected $format    = 'json';

    public function postIndex()
    {
        $post = $this->request->getPost();
        $user = $this->model->where('email', $post['email'])->first();
        if(password_verify($post['password'], $user['password'])){
            unset($user['password']);
            return $this->respond([
                "status" => true,
                "message" => "Success",
                "data" => $user
            ], 200);
        }
        return $this->respond([
            "status" => false,
            "message" => "Email atau kata sandi tidak cocok",
        ], 200);
    }

}
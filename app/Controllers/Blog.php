<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use Trisnawan\ImageResizer\ImageResizer;
use Ramsey\Uuid\Uuid;

class Blog extends ResourceController
{
    protected $modelName = 'App\Models\BlogModel';
    protected $format    = 'json';

    public function getIndex()
    {
        $limit = $this->request->getGet('limit') ?? 20;
        $offset = $this->request->getGet('offset') ?? 0;
        $search = $this->request->getGet('search') ?? null;
        if($search){
            $this->model->where("(title LIKE '%$search%' OR content LIKE '%$search%')");
        }
        $data = $this->model->findAll($limit,$offset);
        return $this->respond($data, 200);
    }

    public function getRead()
    {
        $id = $this->request->getGet('id') ?? null;
        if(!$id){
            return $this->failValidationError('Field id is required');
        }

        $data = $this->model->find($id);
        if(!$data){
            return $this->failValidationError('Blog not found');
        }
        return $this->respond($data, 200);
    }

    public function postCreate(){
        $post = $this->request->getPost();
        if(!($_FILES['image']['tmp_name'] ?? false)){
            return $this->failValidationError('File image is required');
        }

        $extension = explode('.', $_FILES['image']['name']);
        $extension = end($extension);
        if(!in_array(strtolower($extension), ['jpg', 'jpeg', 'png'])){
            return $this->failValidationError('Images must be uploaded in jpg, jpeg, or png format');
        }

        $filePath = $_FILES["image"]["tmp_name"];
        $fileName = 'img-'.Uuid::uuid4()->toString() . '.' . $extension;
        $resizer = new ImageResizer($filePath);
        $resizer->maxWidth(600);
        $resizer->writeImage($this->model->filePath() . $fileName);

        $post['image'] = $fileName;
        if(!file_exists($this->model->filePath() . $fileName)){
            return $this->failValidationError('Image failed to upload!');
        }

        $saved = $this->model->insert($post);
        if(!$saved){
            $error = getOneError($this->model->errors());
            return $this->failValidationError($error ?? 'Blog failed to save, please try again.');
        }

        return $this->respond($this->model->find($saved), 200);
    }

    public function postUpdate(){
        $post = $this->request->getPostGet();
        if(!isset($post['id'])){
            return $this->failValidationError("Field id is required");
        }

        $this->model->select('image as image_file');
        $old = $this->model->find($post['id']);

        if($_FILES['image']['tmp_name'] ?? false){
            $extension = explode('.', $_FILES['image']['name']);
            $extension = end($extension);
            if(!in_array(strtolower($extension), ['jpg', 'jpeg', 'png'])){
                return $this->failValidationError('Images must be uploaded in jpg, jpeg, or png format');
            }
    
            $filePath = $_FILES["image"]["tmp_name"];
            $fileName = 'img-'.Uuid::uuid4()->toString() . '.' . $extension;
            $resizer = new ImageResizer($filePath);
            $resizer->maxWidth(600);
            $resizer->writeImage($this->model->filePath() . $fileName);
    
            $post['image'] = $fileName;
            if(!file_exists($this->model->filePath() . $fileName)){
                return $this->failValidationError('Image failed to upload!');
            }
        }

        $saved = $this->model->update($post['id'], $post);
        if(!$saved){
            if($post['image'] ?? false){
                @unlink($this->model->filePath() . $post['image']);
            }
            $error = getOneError($this->model->errors());
            return $this->failValidationError($error ?? 'Blog failed to save, please try again.');
        }else{
            if(($old['image_file'] ?? false) && ($post['image'] ?? false)){
                @unlink($this->model->filePath() . $old['image_file']);
            }
        }

        return $this->respond($this->model->find($saved), 200);
    }

    public function deleteDelete(){
        $id = $this->request->getGet('id');
        if(!$id){
            return $this->failValidationError("Field id is required");
        }

        $deleted = $this->model->delete($id);
        if(!$deleted){
            return $this->failValidationError("Delete failed!");
        }
        return $this->respond(['status' => 'success'], 200);
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;
use Ausi\SlugGenerator\SlugGenerator;

class BlogModel extends Model
{
    protected $table            = 'blogs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id', 'title', 'image', 'content', 'created_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'title' => 'required|max_length[120]',
        'image' => 'required',
        'content' => 'required',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert   = ['insert_id'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = ['selection_field'];
    protected $afterFind      = [];
    protected $beforeDelete   = ['delete_file'];
    protected $afterDelete    = [];

    public function filePath(){
        if(!is_dir('contents')) mkdir('contents');
        if(!is_dir('contents/blog')) mkdir('contents/blog');
        if(!is_dir('contents/blog/image')) mkdir('contents/blog/image');
        return 'contents/blog/image/';
    }

    protected function insert_id(array $data){
        $generator = new SlugGenerator;
        $data['data']['id'] = Uuid::uuid4()->toString();
        $data['data']['slug'] = $generator->generate($data['data']['title'].'-'.uniqid());
        return $data;
    }

    protected function selection_field(array $data){
        $this->select('id, slug, title, content');
        $this->select("CONCAT('".base_url($this->filePath())."', image) as image");
        $this->select('created_at');
        return $data;
    }

    protected function delete_file(array $data){
        $item = $this->select('image as image_file')->find($data['id']);
        if($item['image_file'] ?? false){
            if(file_exists($this->filePath() . $item['image_file']))
            @unlink($this->filePath() . $item['image_file']);
        }
        return $data;
    }
}

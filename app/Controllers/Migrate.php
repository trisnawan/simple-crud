<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Database\MigrationRunner;
use Config\Migrations;

class Migrate extends ResourceController
{
    protected $modelName = null;
    protected $format    = 'json';

    public function getIndex(){
        $config = new Migrations();
        $migration = new MigrationRunner($config);
        $migration->latest();
        return $this->respond(["status" => "success"], 200);
    }
}

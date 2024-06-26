<?php

namespace App\Controllers;

use CodeIgniter\Database\MigrationRunner;
use Config\Migrations;

class Home extends BaseController
{
    public function index(): string
    {
        return view('welcome_message');
    }

    public function getMigrate(){
        $config = new Migrations();
        $migration = new MigrationRunner($config);
        $migration->latest();
        return $this->respond(["status" => "success"], 200);
    }
}

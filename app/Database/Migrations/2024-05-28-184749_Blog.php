<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class Blog extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 40,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 120,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 160,
            ],
            'image' => [
                'type' => 'VARCHAR',
                'constraint' => 120,
            ],
            'content' => [
                'type' => 'TEXT',
                'null' => true,
                'default' => null,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('blogs', true, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('blogs');
    }
}

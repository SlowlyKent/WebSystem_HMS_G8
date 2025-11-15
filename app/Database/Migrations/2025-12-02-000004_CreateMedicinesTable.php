<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMedicinesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'stock' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'Current stock quantity',
            ],
            'price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'comment' => 'Price per unit',
            ],
            'unit' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Unit of measurement (tablet, bottle, etc.)',
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'comment' => '1 = active, 0 = inactive',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('is_active');
        $this->forge->addKey('name');
        $this->forge->createTable('medicines');
    }

    public function down()
    {
        $this->forge->dropTable('medicines');
    }
}


<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLabRequestItemsTable extends Migration
{
    public function up()
    {
        // This table links lab_requests to lab_test_types (many-to-many)
        // One request can have multiple test types
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'request_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'test_type_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
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
        $this->forge->addKey('request_id');
        $this->forge->addKey('test_type_id');
        $this->forge->addForeignKey('request_id', 'lab_requests', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('test_type_id', 'lab_test_types', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('lab_request_items');
    }

    public function down()
    {
        $this->forge->dropTable('lab_request_items');
    }
}


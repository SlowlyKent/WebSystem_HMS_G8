<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLabSamplesTable extends Migration
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
            'request_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'sample_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
                'comment' => 'Blood, Urine, Stool, Tissue, etc.',
            ],
            'collection_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'collected_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'User ID of lab staff who collected',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addForeignKey('request_id', 'lab_requests', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('collected_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('lab_samples');
    }

    public function down()
    {
        $this->forge->dropTable('lab_samples');
    }
}


<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePrescriptionItemsTable extends Migration
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
            'prescription_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'medicine_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'quantity' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'comment' => 'Number of units',
            ],
            'unit_price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'comment' => 'Price per unit at time of prescription',
            ],
            'total_price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'comment' => 'quantity × unit_price',
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
        $this->forge->addKey('prescription_id');
        $this->forge->addKey('medicine_id');
        $this->forge->addForeignKey('prescription_id', 'prescriptions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('medicine_id', 'medicines', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('prescription_items');
    }

    public function down()
    {
        $this->forge->dropTable('prescription_items');
    }
}


<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRoomRatesTable extends Migration
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
            'room_type' => [
                'type' => 'ENUM',
                'constraint' => ['ICU', 'NICU', 'WARD'],
                'null' => false,
            ],
            'daily_rate' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'comment' => 'Price per day',
            ],
            'effective_date' => [
                'type' => 'DATE',
                'null' => false,
                'comment' => 'When this rate becomes effective',
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
        $this->forge->addKey('room_type');
        $this->forge->addKey('is_active');
        $this->forge->addKey('effective_date');
        $this->forge->createTable('room_rates');

        // Insert default room rates
        $defaultRates = [
            ['room_type' => 'ICU', 'daily_rate' => 5000.00, 'effective_date' => date('Y-m-d'), 'is_active' => 1],
            ['room_type' => 'NICU', 'daily_rate' => 6000.00, 'effective_date' => date('Y-m-d'), 'is_active' => 1],
            ['room_type' => 'WARD', 'daily_rate' => 2000.00, 'effective_date' => date('Y-m-d'), 'is_active' => 1],
        ];

        $this->db->table('room_rates')->insertBatch($defaultRates);
    }

    public function down()
    {
        $this->forge->dropTable('room_rates');
    }
}


<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBillsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'patient_name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'bill_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'due_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => false,
                'default' => '0.00',
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
                'default' => 'unpaid',
                'comment' => 'unpaid|paid|overdue',
            ],
            'invoice_no' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
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
        $this->forge->addKey('status');
        $this->forge->addKey('bill_date');
        $this->forge->addKey('invoice_no');

        $this->forge->createTable('bills', true);
    }

    public function down()
    {
        $this->forge->dropTable('bills', true);
    }
}

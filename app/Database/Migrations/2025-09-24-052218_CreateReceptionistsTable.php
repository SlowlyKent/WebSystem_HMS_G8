<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReceptionistsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'=>[
                'type'=>'INT',
                'constraint'=>11,
                'unsigned'=>true,
                'auto_increment'=>true,
            ],
            'username'=>[
                'type'=>'VARCHAR',
                'constraint'=>100,
            ],
            'email'=>[
                'type'=>'VARCHAR',
                'constraint'=>255,
            ],
            'password'=>[
                'type'=>'VARCHAR',
                'constraint'=>255,
            ],
            'first_name'=>[
                'type'=>'VARCHAR',
                'constraint'=>100,
            ],
            'last_name'=>[
                'type'=>'VARCHAR',
                'constraint'=>100,
            ],
            'role_id'=>[
                'type'=>'INT',
                'constraint'=>11,
                'unsigned'=>true,
                'null'=>true,
            ],
            'status'=>[
                'type'=>'ENUM',
                'constraint'=>['active','inactive','suspended'],
                'default'=>'active',
            ],
            'created_at'=>[
                'type'=>'DATETIME',
            ],
            'updated_at'=>[
                'type'=>'DATETIME',
            ],
        ]);

        // Primary key
        $this->forge->addKey('id', true);

        // Unique keys
        $this->forge->addUniqueKey('username');
        $this->forge->addUniqueKey('email');

        // Foreign key
        $this->forge->addForeignKey('role_id', 'roles', 'id', 'SET NULL', 'CASCADE');

        // Create table
        $this->forge->createTable('receptionists');
    }

    public function down()
    {
        $this->forge->dropTable('receptionists');
    }
}
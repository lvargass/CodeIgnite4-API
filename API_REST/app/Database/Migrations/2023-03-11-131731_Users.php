<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Users extends Migration
{
    public function up()
    {
        //
        $this->forge->addField([
            'id' => [
                'type'  =>  'INT',
                'constraint'    =>  5,
                'unsigned'  =>  true,
                'auto_increment'    =>  true
            ],
            'custumerName' => [
                'type'  =>  'VARCHAR',
                'constraint'    =>  '150',
                'null'  =>  false
            ],
            'userEmail' => [
                'type'  =>  'VARCHAR',
                'constraint'    =>  '100',
                'null'  =>  false,
                'unique'  =>  true,
            ],
            'userPassword' => [
                'type'  =>  'VARCHAR',
                'constraint'    =>  '255',
                'null'  =>  false,
            ],
            'phoneNumber' => [
                'type'  =>  'VARCHAR',
                'constraint'    =>  '18',
                'null'  =>  false
            ],
            'profilePic' => [
                'type'  =>  'VARCHAR',
                'constraint'    =>  '25',
                'null'  =>  false
            ],
            'status' => [
                'type'  =>  'VARCHAR',
                'constraint'    =>  '25',
                'default'  =>  'Pendiente'
            ],
            'updated_at' => [
                'type'  =>  'datetime',
                'null'  =>  true
            ],
            'created_at datetime default current_timestamp'
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('users');
    }

    public function down()
    {
        //
        $this->forge->dropTable('users');
    }
}

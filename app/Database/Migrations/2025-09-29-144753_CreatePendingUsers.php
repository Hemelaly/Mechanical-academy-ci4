<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePendingUsers extends Migration
{
public function up()
{
    $this->forge->addField([
        'id'          => ['type' => 'INT','unsigned' => true,'auto_increment' => true],
        'username'    => ['type' => 'VARCHAR','constraint' => 100],
        'email'       => ['type' => 'VARCHAR','constraint' => 150],
        'course_id'   => ['type' => 'INT','unsigned' => true],
        'status'      => ['type' => 'ENUM','constraint' => ['waiting_payment','paid'],'default' => 'waiting_payment'],
        'created_at'  => ['type' => 'DATETIME','null' => true],
        'updated_at'  => ['type' => 'DATETIME','null' => true],
    ]);
    $this->forge->addKey('id', true);
    $this->forge->createTable('pending_users');
}


    public function down()
    {
        //
    }
}
